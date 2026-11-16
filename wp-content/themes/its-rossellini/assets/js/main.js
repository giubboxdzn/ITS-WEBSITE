/* ITS Rossellini — progressive enhancement only. Every feature works without it. */
(() => {
	'use strict';

	const root = document.documentElement;
	const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
	const saveData = Boolean(navigator.connection && navigator.connection.saveData);
	const EASE = 'cubic-bezier(0.16, 1, 0.3, 1)';

	/* Motion: the footer switch decides; with no choice made, the system setting does.
	   The <head> script already set .motion-off from the same rule before first paint. */
	const readChoice = () => {
		try {
			return localStorage.getItem('its-motion');
		} catch (e) {
			return null;
		}
	};
	let choice = readChoice();
	const motionOn = () => !root.classList.contains('motion-off');
	const motionToggle = document.querySelector('[data-motion-toggle]');

	const setMotion = (on) => {
		root.classList.toggle('motion-off', !on);
		if (motionToggle) motionToggle.setAttribute('aria-checked', String(on));
		document.dispatchEvent(new CustomEvent('its:motion', { detail: { off: !on } }));
	};
	const store = (on) => {
		const value = on === !reduceMotion.matches ? null : (on ? 'on' : 'off');
		try {
			if (value) localStorage.setItem('its-motion', value);
			else localStorage.removeItem('its-motion');
			choice = value;
			return true;
		} catch (e) {
			return false;
		}
	};

	/* The switch saves the choice and reloads: the page starts again, from the top. */
	if (motionToggle) {
		motionToggle.hidden = false;
		motionToggle.setAttribute('aria-checked', String(motionOn()));
		motionToggle.addEventListener('click', () => {
			const on = !motionOn();
			motionToggle.setAttribute('aria-checked', String(on));
			if (store(on)) window.location.reload();
			else setMotion(on); /* storage blocked: switch in place, for this page only */
		});
	}
	reduceMotion.addEventListener('change', (mq) => {
		if (!choice) setMotion(!mq.matches);
	});

	/* Scroll: a reload always starts at the top (restoration is manual, set in <head>).
	   Back/forward put you back where you were: the page is usually restored whole from the
	   back-forward cache; when it is loaded again, the position saved on leaving is used. */
	const scrollKey = `its-scroll:${window.location.pathname}${window.location.search}`;
	window.addEventListener('pagehide', () => {
		try {
			sessionStorage.setItem(scrollKey, String(Math.round(window.scrollY)));
		} catch (e) { /* storage unavailable */ }
	});
	const navigation = performance.getEntriesByType?.('navigation')[0];
	if (navigation && navigation.type === 'back_forward') {
		let saved = 0;
		try {
			saved = Number(sessionStorage.getItem(scrollKey)) || 0;
		} catch (e) { /* storage unavailable */ }
		if (saved) window.addEventListener('load', () => window.scrollTo(0, saved), { once: true });
	}

	/* Back to top: smooth while motion is on; keyboard focus goes back to the top too. */
	document.querySelectorAll('[data-to-top]').forEach((link) => {
		link.addEventListener('click', (event) => {
			event.preventDefault();
			window.scrollTo({ top: 0, behavior: motionOn() ? 'smooth' : 'auto' });
			document.querySelector('.masthead__logo')?.focus({ preventScroll: true });
		});
	});

	/* Floating header: turns into a glass pill once the page scrolls. */
	const masthead = document.querySelector('[data-masthead]');
	if (masthead) {
		let ticking = false;
		const update = () => {
			masthead.classList.toggle('is-solid', window.scrollY > 24);
			ticking = false;
		};
		window.addEventListener('scroll', () => {
			if (!ticking) {
				ticking = true;
				window.requestAnimationFrame(update);
			}
		}, { passive: true });
		update();
	}

	/* Mobile navigation */
	const toggle = masthead?.querySelector('.masthead__toggle');
	if (masthead && toggle) {
		const setOpen = (open) => {
			toggle.setAttribute('aria-expanded', String(open));
			masthead.toggleAttribute('data-open', open);
		};
		toggle.addEventListener('click', () => setOpen(toggle.getAttribute('aria-expanded') !== 'true'));
		document.addEventListener('keydown', (event) => {
			if (event.key === 'Escape' && masthead.hasAttribute('data-open')) {
				setOpen(false);
				toggle.focus();
			}
		});
		window.matchMedia('(min-width: 1081px)').addEventListener('change', (mq) => mq.matches && setOpen(false));
	}

	/* Hero video: plays only when motion and data allow; always pausable. */
	const video = document.querySelector('[data-hero-video]');
	const videoToggle = document.querySelector('[data-video-toggle]');
	if (video && videoToggle) {
		const setState = (paused) => {
			videoToggle.classList.toggle('is-paused', paused);
			videoToggle.setAttribute('aria-label', paused ? videoToggle.dataset.labelPlay : videoToggle.dataset.labelPause);
		};
		const play = () => video.play().then(() => setState(false)).catch(() => setState(true));
		videoToggle.hidden = false;
		videoToggle.addEventListener('click', () => {
			if (video.paused) play();
			else {
				video.pause();
				setState(true);
			}
		});
		if (motionOn() && !saveData) play();
		else setState(true);
		document.addEventListener('its:motion', (event) => {
			if (event.detail.off) {
				video.pause();
				setState(true);
			} else if (!saveData) play();
		});
	}

	/* ---------- Animations (only while the switch is on) ---------- */

	const Motion = (() => {
		if (!('IntersectionObserver' in window)) return null;

		const TARGETS = {
			words: '.display, .hero__title, .page-head__title',
			up: [
				'.lead:not(.hero__lead)', '.panel__head > p', '.split-head > p', '.panel__more',
				'.bento > *', '.grid__item', '.news-grid > *', '.rows > *',
				'.compare', '.compare__row', '.compare__actions',
				'.overlap__media', '.overlap__card', '.steps > li',
				'.place__chips > li', '.place__globe', '.faq__item', '.faq-section__more',
				'.cta-card', '.prose-card', '.index-list > li', '.editions__group',
				'.psec__head > *', '.figures > *', '.pcards > *', '.psteps > *',
				'.area-filter', '.doc-filter', '.doc-table', '.article__photo', '.article__nav',
				'.colophon__grid > *', '.colophon__funding', '.colophon__base',
			].join(', '),
			draw: '.colophon__mark',
		};
		const FIRST_VIEW = '.hero, .page-head';
		const STEP = 80;

		/* Headings become words that rise from a mask; screen readers keep the plain text. */
		const split = (el) => {
			if (el.dataset.split) return;
			el.dataset.split = '1';
			const label = document.createElement('span');
			label.className = 'screen-reader-text';
			label.textContent = el.textContent.replace(/\s+/g, ' ').trim();
			const visual = document.createElement('span');
			visual.setAttribute('aria-hidden', 'true');
			let n = 0;
			const walk = (from, into) => {
				from.childNodes.forEach((node) => {
					if (node.nodeType === Node.TEXT_NODE) {
						node.textContent.split(/(\s+)/).forEach((part) => {
							if (!part) return;
							if (!part.trim()) {
								into.append(' ');
								return;
							}
							const word = document.createElement('span');
							const inner = document.createElement('span');
							word.className = 'word';
							inner.className = 'word__in';
							inner.style.setProperty('--w', String(n++));
							inner.textContent = part;
							word.append(inner);
							into.append(word);
						});
					} else if (node.nodeType === Node.ELEMENT_NODE) {
						const copy = node.cloneNode(false);
						copy.removeAttribute('id');
						into.append(copy);
						walk(node, copy);
					}
				});
			};
			walk(el, visual);
			el.replaceChildren(label, visual);
		};

		const finish = (el) => {
			el.dataset.reveal = 'done';
			el.style.removeProperty('--d');
		};

		/* The footer logo: its single compound path becomes one path per shape, each drawn 0 → 100%. */
		const prepareDraw = (mark) => {
			const source = mark.querySelector('path');
			if (!source || mark.dataset.drawn) return;
			mark.dataset.drawn = '1';
			const tokens = source.getAttribute('d').match(/[a-z]|[+-]?(?:\d+\.?\d*|\.\d+)(?:e[+-]?\d+)?/gi) || [];
			const ARGS = { m: 2, l: 2, t: 2, h: 1, v: 1, c: 6, s: 4, q: 4, a: 7, z: 0 };
			const parts = [];
			let cur = '';
			let cmd = '';
			let x = 0;
			let y = 0;
			let sx = 0;
			let sy = 0;
			let i = 0;
			while (i < tokens.length) {
				if (/[a-z]/i.test(tokens[i])) cmd = tokens[i++];
				const lower = cmd.toLowerCase();
				const rel = cmd === lower;
				const n = ARGS[lower];
				if (n === undefined) break;
				if (lower === 'z') {
					parts.push(`${cur}Z`);
					cur = '';
					x = sx;
					y = sy;
					if (i < tokens.length && !/[a-z]/i.test(tokens[i])) break;
					continue;
				}
				const a = tokens.slice(i, i + n).map(Number);
				i += n;
				if (a.length < n) break;
				if (lower === 'm') {
					if (cur) parts.push(cur);
					x = sx = rel ? x + a[0] : a[0];
					y = sy = rel ? y + a[1] : a[1];
					cur = `M${x},${y}`;
					cmd = rel ? 'l' : 'L';
					continue;
				}
				if (!cur) cur = `M${x},${y}`;
				cur += cmd + a.join(',');
				if (lower === 'h') x = rel ? x + a[0] : a[0];
				else if (lower === 'v') y = rel ? y + a[0] : a[0];
				else {
					x = rel ? x + a[n - 2] : a[n - 2];
					y = rel ? y + a[n - 1] : a[n - 1];
				}
			}
			if (cur) parts.push(cur);
			if (parts.length < 2) return;
			const paths = parts.map((d) => {
				const path = source.cloneNode(false);
				path.removeAttribute('id');
				path.setAttribute('d', d);
				path.setAttribute('pathLength', '1');
				return path;
			});
			source.replaceWith(...paths);
			/* Dashes need user units, so the 1px hairline is set in them too (no non-scaling stroke). */
			const svg = mark.querySelector('svg');
			const viewWidth = svg?.viewBox.baseVal.width || 0;
			if (svg && viewWidth) {
				const hairline = () => mark.style.setProperty('--hairline', String(viewWidth / Math.max(1, svg.getBoundingClientRect().width)));
				hairline();
				window.addEventListener('resize', hairline, { passive: true });
			}
			/* Left to right: the mark first, then the wordmark. */
			paths
				.map((path) => ({ path, left: path.getBBox().x }))
				.sort((a, b) => a.left - b.left)
				.forEach(({ path }, rank) => path.style.setProperty('--i', String(rank)));
		};

		const reveal = (els) => {
			els.sort((a, b) => {
				const ra = a.getBoundingClientRect();
				const rb = b.getBoundingClientRect();
				return ra.top - rb.top || ra.left - rb.left;
			});
			els.forEach((el, i) => {
				const delay = Math.min(i, 8) * STEP;
				const words = el.dataset.reveal === 'words' ? el.querySelectorAll('.word').length : 0;
				const strokes = el.dataset.reveal === 'draw' ? el.querySelectorAll('path').length : 0;
				el.style.setProperty('--d', `${delay}ms`);
				el.classList.add('is-in');
				window.setTimeout(() => finish(el), delay + 1900 + words * 55 + strokes * 40 + (strokes ? 1200 : 0));
			});
		};

		const pending = new Set();
		const release = (els) => {
			els.forEach((el) => {
				revealer.unobserve(el);
				drawWatch.unobserve(el);
				pending.delete(el);
			});
			if (els.length) reveal(els);
		};
		const revealer = new IntersectionObserver((entries) => {
			release(entries.filter((entry) => entry.isIntersecting).map((entry) => entry.target));
		}, { rootMargin: '0px 0px -8% 0px' });
		/* The footer logo waits until it is well in view: the end of the page. */
		const drawWatch = new IntersectionObserver((entries) => {
			entries.forEach((entry) => {
				if (!entry.isIntersecting) return;
				drawWatch.unobserve(entry.target);
				pending.delete(entry.target);
				reveal([entry.target]);
			});
		}, { threshold: 0.6 });
		const watch = (el) => {
			pending.add(el);
			if (el.dataset.reveal === 'draw') drawWatch.observe(el);
			else revealer.observe(el);
		};

		/* Figures count up from zero; the final value stays in the page for assistive tech. */
		const counters = [];
		const prepareCounter = (el) => {
			if (el.dataset.count) return null;
			const text = el.textContent.trim();
			const m = text.match(/^(\D*)(\d{1,3}(?:[.,]\d{3})+|\d+)(.*)$/s);
			if (!m) return null;
			el.dataset.count = '1';
			const label = document.createElement('span');
			label.className = 'screen-reader-text';
			label.textContent = text;
			const visual = document.createElement('span');
			visual.className = 'count';
			visual.setAttribute('aria-hidden', 'true');
			visual.textContent = text;
			el.replaceChildren(label, visual);
			const target = parseInt(m[2].replace(/[.,]/g, ''), 10);
			const sep = (m[2].match(/[.,]/) || [''])[0];
			const format = (v) => m[1] + (sep ? String(v).replace(/\B(?=(\d{3})+(?!\d))/g, sep) : String(v)) + m[3];
			const counter = { el, visual, target, format, frame: 0, done: false };
			counters.push(counter);
			return counter;
		};
		const settle = (c) => {
			window.cancelAnimationFrame(c.frame);
			c.visual.textContent = c.format(c.target);
			c.done = true;
		};
		const count = (c) => {
			const start = performance.now() + (c.el.closest('.hero') ? 700 : 0);
			const duration = 1500;
			const tick = (now) => {
				const t = Math.min(1, Math.max(0, (now - start) / duration));
				c.visual.textContent = c.format(Math.round(c.target * (1 - (1 - t) ** 3)));
				if (t < 1) c.frame = window.requestAnimationFrame(tick);
				else c.done = true;
			};
			c.frame = window.requestAnimationFrame(tick);
		};
		const counterWatch = new IntersectionObserver((entries) => {
			entries.forEach((entry) => {
				if (!entry.isIntersecting) return;
				counterWatch.unobserve(entry.target);
				const c = counters.find((item) => item.el === entry.target);
				if (c && !c.done) {
					if (motionOn()) count(c);
					else settle(c);
				}
			});
		}, { threshold: 0.6 });

		/* Parallax: the hero footage and the path photo drift against the scroll. */
		const heroMedia = document.querySelector('.hero__media');
		const pathPhoto = document.querySelector('.overlap__media img');
		let parallax = false;
		let queued = false;
		const drift = () => {
			queued = false;
			if (!parallax) return;
			const vh = window.innerHeight;
			const y = window.scrollY;
			/* At the very bottom, whatever is left (the footer mark) comes in too. */
			if (pending.size && y + vh >= document.documentElement.scrollHeight - 4) {
				release([...pending].filter((el) => el.getBoundingClientRect().top < vh));
			}
			if (heroMedia && y < vh * 1.5) heroMedia.style.translate = `0 ${(y * 0.3).toFixed(1)}px`;
			if (pathPhoto) {
				const r = pathPhoto.parentElement.getBoundingClientRect();
				if (r.bottom > 0 && r.top < vh) {
					const p = Math.max(-1, Math.min(1, (r.top + r.height / 2 - vh / 2) / vh));
					pathPhoto.style.translate = `0 ${(p * -5).toFixed(2)}%`;
				}
			}
		};
		const onScroll = () => {
			if (!queued) {
				queued = true;
				window.requestAnimationFrame(drift);
			}
		};

		const below = (el) => el.getBoundingClientRect().top > window.innerHeight;

		const enable = (initial) => {
			root.classList.add('anim');
			document.querySelectorAll(TARGETS.words).forEach(split);
			document.querySelectorAll(TARGETS.draw).forEach(prepareDraw);
			Object.entries(TARGETS).forEach(([type, selector]) => {
				document.querySelectorAll(selector).forEach((el) => {
					if (type !== 'words' && el.closest(FIRST_VIEW)) return;
					/* Switched on mid-page: what is already on screen stays put. */
					if (!initial && !below(el)) return;
					el.classList.remove('is-in');
					el.dataset.reveal = type;
					if (initial && el.closest(FIRST_VIEW)) {
						window.requestAnimationFrame(() => window.requestAnimationFrame(() => reveal([el])));
					} else {
						watch(el);
					}
				});
			});
			document.querySelectorAll('.tile__num, .glass-card__num').forEach((el) => {
				const c = prepareCounter(el) || counters.find((item) => item.el === el);
				if (!c || (!initial && !below(el))) return;
				c.done = false;
				c.visual.textContent = c.format(0);
				counterWatch.observe(el);
			});
			parallax = true;
			window.addEventListener('scroll', onScroll, { passive: true });
			drift();
		};

		const disable = () => {
			root.classList.remove('anim');
			pending.clear();
			document.querySelectorAll('[data-reveal]').forEach((el) => {
				revealer.unobserve(el);
				drawWatch.unobserve(el);
				el.classList.add('is-in');
				finish(el);
			});
			counters.forEach((c) => {
				counterWatch.unobserve(c.el);
				settle(c);
			});
			parallax = false;
			window.removeEventListener('scroll', onScroll);
			if (heroMedia) heroMedia.style.translate = '';
			if (pathPhoto) pathPhoto.style.translate = '';
		};

		return { enable, disable };
	})();

	if (Motion) {
		if (motionOn()) Motion.enable(true);
		document.addEventListener('its:motion', (event) => {
			if (event.detail.off) Motion.disable();
			else Motion.enable(false);
		});
	}

	/* Accordions (FAQ): open and close with a height animation; native otherwise. */
	document.querySelectorAll('details.faq__item, .prose details').forEach((details) => {
		const summary = details.querySelector('summary');
		if (!summary || !details.animate) return;
		let running = null;
		summary.addEventListener('click', (event) => {
			if (!motionOn()) return;
			event.preventDefault();
			const from = details.offsetHeight;
			const closing = details.open && !details.classList.contains('is-closing');
			if (running) running.cancel();
			let to;
			if (closing) {
				const cs = getComputedStyle(details);
				to = summary.offsetHeight + parseFloat(cs.borderTopWidth) + parseFloat(cs.borderBottomWidth) + parseFloat(cs.paddingTop) + parseFloat(cs.paddingBottom);
				details.classList.add('is-closing');
			} else {
				details.classList.remove('is-closing');
				details.open = true;
				to = details.offsetHeight;
			}
			details.style.overflow = 'clip';
			running = details.animate({ height: [`${from}px`, `${to}px`] }, { duration: 450, easing: EASE });
			running.onfinish = () => {
				if (closing) details.open = false;
				details.classList.remove('is-closing');
				details.style.overflow = '';
				running = null;
			};
		});
	});

	/* Language menus: one open at a time; a click elsewhere or Escape closes them. */
	const langMenus = [...document.querySelectorAll('details.lang')];
	if (langMenus.length) {
		langMenus.forEach((menu) => menu.addEventListener('toggle', () => {
			if (menu.open) langMenus.forEach((other) => { if (other !== menu) other.open = false; });
		}));
		document.addEventListener('click', (event) => {
			langMenus.forEach((menu) => { if (menu.open && !menu.contains(event.target)) menu.open = false; });
		});
		document.addEventListener('keydown', (event) => {
			if (event.key !== 'Escape') return;
			langMenus.forEach((menu) => {
				if (menu.open) {
					menu.open = false;
					menu.querySelector('summary')?.focus();
				}
			});
		});
	}

	/* Course grid: filter by area in place, keep the URL shareable. */
	const filter = document.querySelector('.area-filter');
	const grid = document.querySelector('[data-wall]');
	if (filter && grid) {
		const items = [...grid.querySelectorAll('.grid__item')];
		const status = document.querySelector('[data-wall-status]');
		const links = [...filter.querySelectorAll('a[data-area]')];

		const apply = (area) => {
			const shown = [];
			items.forEach((item) => {
				const show = !area || item.dataset.area === area;
				item.hidden = !show;
				if (show) shown.push(item);
				/* A card brought back by the filter is shown now, not on the next scroll. */
				if (show && item.dataset.reveal && item.dataset.reveal !== 'done') {
					item.classList.add('is-in');
					item.dataset.reveal = 'done';
				}
			});
			links.forEach((link) => {
				if (link.dataset.area === area) link.setAttribute('aria-current', 'true');
				else link.removeAttribute('aria-current');
			});
			if (status) status.textContent = shown.length === 1 ? status.dataset.one : (status.dataset.many || '%d').replace('%d', shown.length);
			if (motionOn() && grid.animate) {
				shown.forEach((item, i) => item.animate(
					[{ opacity: 0, translate: '0 1.25rem' }, { opacity: 1, translate: '0 0' }],
					{ duration: 550, delay: Math.min(i, 8) * 50, easing: EASE, fill: 'backwards' }
				));
			}
		};

		filter.addEventListener('click', (event) => {
			const link = event.target.closest('a[data-area]');
			if (!link) return;
			event.preventDefault();
			const area = link.dataset.area;
			apply(area);
			const url = new URL(window.location.href);
			if (area) url.searchParams.set('area', area);
			else url.searchParams.delete('area');
			history.replaceState(null, '', url);
		});
	}
})();
