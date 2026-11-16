/* ITS Rossellini — progressive enhancement only. Every feature works without it. */
(() => {
	'use strict';

	const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
	const saveData = Boolean(navigator.connection && navigator.connection.saveData);

	/* Header over the home video: solid once the page scrolls. */
	const masthead = document.querySelector('[data-masthead]');
	if (masthead && masthead.classList.contains('masthead--over')) {
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
		if (!reduceMotion.matches && !saveData) play();
		else setState(true);
		reduceMotion.addEventListener('change', (mq) => {
			if (mq.matches) {
				video.pause();
				setState(true);
			}
		});
	}

	/* Course index: the hovered course's photo follows the pointer (fine pointers only). */
	const index = document.querySelector('[data-course-index]');
	const preview = document.querySelector('[data-course-preview]');
	const finePointer = window.matchMedia('(hover: hover) and (pointer: fine) and (min-width: 761px)');
	if (index && preview && finePointer.matches && !reduceMotion.matches) {
		const img = preview.querySelector('img');
		const section = index.closest('section') || index;
		let x = 0, y = 0, cx = 0, cy = 0, raf = 0;
		const loop = () => {
			cx += (x - cx) * 0.18;
			cy += (y - cy) * 0.18;
			preview.style.transform = `translate3d(${cx}px, ${cy}px, 0) translate(1.75rem, -60%) rotate(${Math.max(-6, Math.min(6, (x - cx) * 0.05))}deg)`;
			raf = Math.abs(x - cx) + Math.abs(y - cy) > 0.3 ? requestAnimationFrame(loop) : 0;
		};
		const move = (event) => {
			x = event.clientX;
			y = event.clientY;
			if (!raf) raf = requestAnimationFrame(loop);
		};
		document.body.classList.add('is-following');
		index.addEventListener('pointerover', (event) => {
			const link = event.target.closest('.course-index__link');
			const source = link && link.querySelector('img');
			if (!source) return;
			img.src = source.currentSrc || source.src;
			if (!preview.classList.contains('is-visible')) {
				cx = x = event.clientX;
				cy = y = event.clientY;
				preview.style.transform = `translate3d(${cx}px, ${cy}px, 0) translate(1.75rem, -60%)`;
			}
			preview.classList.add('is-visible');
		});
		index.addEventListener('pointermove', move, { passive: true });
		index.addEventListener('pointerleave', () => preview.classList.remove('is-visible'));
		section.addEventListener('scroll', () => preview.classList.remove('is-visible'), { passive: true });
		window.addEventListener('scroll', () => preview.classList.remove('is-visible'), { passive: true });
		// Preload the photos so the preview never flashes empty.
		index.querySelectorAll('img[loading="lazy"]').forEach((image) => { image.loading = 'eager'; });
	}

	/* Course grid: filter by area in place, keep the URL shareable. */
	const filter = document.querySelector('.area-filter');
	const grid = document.querySelector('[data-wall]');
	if (filter && grid) {
		const items = [...grid.querySelectorAll('.grid__item')];
		const status = document.querySelector('[data-wall-status]');
		const links = [...filter.querySelectorAll('a[data-area]')];

		const apply = (area) => {
			let visible = 0;
			items.forEach((item) => {
				const show = !area || item.dataset.area === area;
				item.hidden = !show;
				if (show) visible += 1;
			});
			links.forEach((link) => {
				if (link.dataset.area === area) link.setAttribute('aria-current', 'true');
				else link.removeAttribute('aria-current');
			});
			if (status) status.textContent = visible === 1 ? '1 corso' : `${visible} corsi`;
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
