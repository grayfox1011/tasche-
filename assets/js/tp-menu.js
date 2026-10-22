(function () {
	'use strict';

	var gsap = window.gsap;
	var rows = gsap && gsap.utils ? gsap.utils.toArray('.tp-menu-section .tp-menu-row') : [];

	if (!gsap || !rows.length) {
		return;
	}

	var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	rows.forEach(function (row) {
		var rotation = 2;

		row.dataset.tpMenuRotation = rotation;
		row.style.setProperty('--tp-menu-row-rotation', rotation + 'deg');
	});

	if (reduceMotion) {
		toArray(document.querySelectorAll('.tp-menu-section .tp-menu-legend')).forEach(function (legend) {
			legend.classList.add('is-revealed');
		});
		return;
	}

	function markLegendRevealed(element) {
		if (element && element.classList && element.classList.contains('tp-menu-legend')) {
			element.classList.add('is-revealed');
		}
	}

	function revealRows(table) {
		if (!table || table.dataset.tpMenuRowsRevealed === '1') {
			return;
		}

		table.dataset.tpMenuRowsRevealed = '1';

		gsap.fromTo(
			toArray(table.querySelectorAll('.tp-menu-row')),
			{ y: 34, autoAlpha: 0 },
			{
				y: 0,
				autoAlpha: 1,
				duration: 0.95,
				stagger: 0.045,
				ease: 'power3.out',
				clearProps: 'transform,opacity,visibility'
			}
		);
	}

	function revealBlock(element) {
		if (!element || element.dataset.tpMenuBlockRevealed === '1') {
			return;
		}

		element.dataset.tpMenuBlockRevealed = '1';
		markLegendRevealed(element);

		gsap.fromTo(
			element,
			{ y: 52, autoAlpha: 0 },
			{
				y: 0,
				autoAlpha: 1,
				duration: 1.15,
				ease: 'power3.out',
				clearProps: 'transform,opacity,visibility'
			}
		);
	}

	function toArray(list) {
		return Array.prototype.slice.call(list || []);
	}

	function initScrollReveals() {
		var tables = toArray(document.querySelectorAll('.tp-menu-section .tp-menu-table'));
		var blocks = toArray(document.querySelectorAll('.tp-menu-section .tp-menu-legend, .tp-menu-section .tp-menu-sectionhead'));

		if (!('IntersectionObserver' in window)) {
			tables.forEach(revealRows);
			blocks.forEach(revealBlock);
			return;
		}

		var rowObserver = new IntersectionObserver(
			function (entries) {
				entries.forEach(function (entry) {
					if (!entry.isIntersecting) {
						return;
					}

					revealRows(entry.target);
					rowObserver.unobserve(entry.target);
				});
			},
			{ threshold: 0.15 }
		);

		var blockObserver = new IntersectionObserver(
			function (entries) {
				entries.forEach(function (entry) {
					if (!entry.isIntersecting) {
						return;
					}

					revealBlock(entry.target);
					blockObserver.unobserve(entry.target);
				});
			},
			{ threshold: 0.2 }
		);

		tables.forEach(function (table) {
			rowObserver.observe(table);
		});

		blocks.forEach(function (block) {
			blockObserver.observe(block);
		});
	}

	initScrollReveals();

	gsap.matchMedia().add('(min-width: 981px)', function () {
		var cleanups = [];

		rows.forEach(function (row) {
			var image = row.querySelector('.tp-menu-row-image');

			if (!image) {
				return;
			}

			gsap.set(image, {
				autoAlpha: 0,
				scale: 0.04,
				rotation: 0,
				xPercent: -50,
				yPercent: -50
			});

			var show = function () {
				row.classList.add('is-active');

				gsap.to(image, {
					autoAlpha: 1,
					scale: 1,
					rotation: parseFloat(row.dataset.tpMenuRotation || '-3'),
					xPercent: -50,
					yPercent: -50,
					duration: 1.05,
					ease: 'power4.out',
					overwrite: 'auto'
				});
			};

			var hide = function () {
				row.classList.remove('is-active');

				gsap.to(image, {
					autoAlpha: 0,
					scale: 0.04,
					rotation: 0,
					xPercent: -50,
					yPercent: -50,
					duration: 0.75,
					ease: 'power3.out',
					overwrite: 'auto'
				});
			};

			row.addEventListener('mouseenter', show);
			row.addEventListener('focusin', show);
			row.addEventListener('mouseleave', hide);
			row.addEventListener('focusout', hide);

			cleanups.push(function () {
				row.removeEventListener('mouseenter', show);
				row.removeEventListener('focusin', show);
				row.removeEventListener('mouseleave', hide);
				row.removeEventListener('focusout', hide);
				row.classList.remove('is-active');
				gsap.set(image, { clearProps: 'all' });
			});
		});

		return function () {
			cleanups.forEach(function (cleanup) {
				cleanup();
			});
		};
	});
})();
