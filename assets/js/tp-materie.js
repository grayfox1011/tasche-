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
		var cleanups = [];
		var activeCard = null;

		var setActiveCard = function (card) {
			if (activeCard && activeCard !== card) {
				activeCard.classList.remove('is-active');
			}

			activeCard = card;
			card.classList.add('is-active');
		};

		var clearActiveCard = function (card) {
			if (!card || activeCard === card) {
				activeCard = null;
			}

			if (card) {
				card.classList.remove('is-active');
			}
		};

		cards.forEach(function (card) {
			var image = card.querySelector('.tp-materie-card-image');

			if (!image) {
				return;
			}

			if (!card.hasAttribute('tabindex')) {
				card.setAttribute('tabindex', '0');
			}

			var show = function () {
				setActiveCard(card);
				gsap.set(image, { clearProps: 'transform' });
			};

			var hide = function () {
				clearActiveCard(card);
				gsap.set(image, { clearProps: 'transform' });
			};

			var toggleFromPointer = function (event) {
				if (!event.pointerType || event.pointerType === 'mouse') {
					return;
				}

				if (card.classList.contains('is-active')) {
					hide();
					return;
				}

				show();
			};

			var handleKeydown = function (event) {
				if (event.key === 'Escape') {
					hide();
				}
			};

			card.addEventListener('pointerenter', show);
			card.addEventListener('pointerleave', hide);
			card.addEventListener('pointerdown', toggleFromPointer);
			card.addEventListener('focusin', show);
			card.addEventListener('focusout', hide);
			card.addEventListener('keydown', handleKeydown);

			cleanups.push(function () {
				card.removeEventListener('pointerenter', show);
				card.removeEventListener('pointerleave', hide);
				card.removeEventListener('pointerdown', toggleFromPointer);
				card.removeEventListener('focusin', show);
				card.removeEventListener('focusout', hide);
				card.removeEventListener('keydown', handleKeydown);
				card.classList.remove('is-active');
				gsap.set(image, { clearProps: 'transform' });
			});
		});

		return function () {
			cleanups.forEach(function (cleanup) {
				cleanup();
			});
		};
	});
})();
