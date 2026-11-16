/* ITS Rossellini — progressive enhancement only. Every feature works without it. */
(() => {
	'use strict';

	const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
	const saveData = Boolean(navigator.connection && navigator.connection.saveData);

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
		if (!reduceMotion.matches && !saveData) play();
		else setState(true);
		reduceMotion.addEventListener('change', (mq) => {
			if (mq.matches) {
				video.pause();
				setState(true);
			}
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
