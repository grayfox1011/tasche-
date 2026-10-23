(function () {
	'use strict';

	var gsap = window.gsap;
	var section = document.querySelector('.tp-materie-section');

	if (!section || !gsap) {
		return;
	}

	var cards = gsap.utils.toArray(section.querySelectorAll('.tp-materie-card'));
	var marquee = section.querySelector('.tp-materie-marquee');
	var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	if (!cards.length) {
		return;
	}

	if (window.matchMedia && window.matchMedia('(max-width: 767px)').matches) {
		section.classList.add('is-mobile-static');
		gsap.set(cards, { autoAlpha: 1, y: 0, rotate: 0 });
		gsap.set(cards, { clearProps: 'opacity,visibility,transform' });
		return;
	}

	var syncStickyMarquee = function () {
		if (!marquee || !window.matchMedia || !window.matchMedia('(min-width: 981px)').matches) {
			if (marquee) {
				marquee.style.setProperty('--tp-materie-marquee-sticky-y', '0px');
			}

			return;
		}

		var viewportHeight = window.innerHeight || document.documentElement.clientHeight || 0;
		var sectionTop = section.getBoundingClientRect().top + window.pageYOffset;
		var sectionBottom = sectionTop + section.offsetHeight;
		var offsetParent = marquee.offsetParent || section;
		var parentTop = offsetParent.getBoundingClientRect().top + window.pageYOffset;
		var start = parentTop + marquee.offsetTop;
		var end = sectionBottom - viewportHeight;
		var stickyY = Math.min(Math.max(window.pageYOffset - start, 0), Math.max(end - start, 0));

		marquee.style.setProperty('--tp-materie-marquee-sticky-y', stickyY.toFixed(2) + 'px');
	};

	var stickyRaf = 0;
	var requestStickyMarquee = function () {
		if (stickyRaf) {
			return;
		}

		stickyRaf = window.requestAnimationFrame(function () {
			stickyRaf = 0;
			syncStickyMarquee();
		});
	};

	syncStickyMarquee();
	window.addEventListener('scroll', requestStickyMarquee, { passive: true });
	window.addEventListener('resize', requestStickyMarquee, { passive: true });

	if (reduceMotion) {
		section.classList.add('is-reduced-motion');
		gsap.set(cards, { autoAlpha: 1, y: 0, rotate: 0 });
		return;
	}

	gsap.set(cards, {
		autoAlpha: 0,
		y: 96,
		rotate: function (index) {
			return index % 2 === 0 ? -2.8 : 2.8;
		}
	});

	var reveal = function () {
		gsap.to(cards, {
			autoAlpha: 1,
			y: 0,
			rotate: 0,
			duration: 1.08,
			stagger: 0.11,
			ease: 'power3.out',
			overwrite: 'auto'
		});
	};

	if ('IntersectionObserver' in window) {
		var observer = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (!entry.isIntersecting) {
					return;
				}

				reveal();
				observer.disconnect();
			});
		}, { threshold: 0.14 });

		observer.observe(section.querySelector('.tp-materie-cards') || section);
	} else {
		reveal();
	}

	gsap.matchMedia().add('(min-width: 981px)', function () {
		var withImage = cards.filter(function (card) {
			return card.querySelector('.tp-materie-card-image');
		});

		return window.tpBindActiveState(withImage, {
			onChange: function (card) {
				gsap.set(card.querySelector('.tp-materie-card-image'), { clearProps: 'transform' });
			}
		});
	});
})();
