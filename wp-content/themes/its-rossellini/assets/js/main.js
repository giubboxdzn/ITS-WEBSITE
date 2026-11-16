/* ITS Rossellini — progressive enhancement only. Every feature works as plain links without it. */
(() => {
	'use strict';

	/* Mobile navigation */
	const masthead = document.querySelector('.masthead');
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

	/* Course wall: filter by area in place, keep the URL shareable. */
	const filter = document.querySelector('.area-filter');
	const wall = document.querySelector('[data-wall]');
	if (filter && wall) {
		const items = [...wall.querySelectorAll('.wall__item')];
		const status = document.querySelector('[data-wall-status]');
		const links = [...filter.querySelectorAll('a[data-area]')];

		const apply = (area) => {
			let visible = 0;
			items.forEach((item) => {
				const show = !area || item.dataset.area === area;
				item.hidden = !show;
				item.classList.toggle('is-feature', show && visible < 2);
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
