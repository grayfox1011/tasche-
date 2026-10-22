(function () {
	'use strict';

	var root = document.documentElement;
	var body = document.body;

	if (!body || body.classList.contains('et-fb')) {
		return;
	}

	function qs(selector) {
		return document.querySelector(selector);
	}

	function dispatch(name) {
		if (typeof window.CustomEvent === 'function') {
			document.dispatchEvent(new window.CustomEvent(name));
		} else {
			var event = document.createEvent('CustomEvent');
			event.initCustomEvent(name, false, false, null);
			document.dispatchEvent(event);
		}
	}

	function init() {
		var preloader = qs('.tp-preloader');
		var panel = qs('.tp-preloader-panel');
		var logoWrap = qs('.tp-preloader-logo-wrap');
		var topPiece = qs('.tp-preloader-logo-piece--top');
		var bottomPiece = qs('.tp-preloader-logo-piece--bottom');
		var header = qs('.tp-theme-header, .tp-header');
		var navLogo = qs('.tp-theme-nav-logo img, .tp-nav-logo img');
		var heroTitle = qs('.tp-hero-title');
		var heroSubline = qs('.tp-hero-subline');
		var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
		var didHeroReveal = false;
		var didNavReveal = false;
		var didFinish = false;
		var failSafeTimer;

		function revealHero() {
			if (didHeroReveal) {
				return;
			}

			didHeroReveal = true;
			body.classList.add('tp-preloader-revealing');

			if (window.gsap && heroTitle) {
				window.gsap.set(heroTitle, { y: 0, autoAlpha: 1 });
			}

			dispatch('tp:preloader:reveal-hero');
			dispatch('tp:preloader:reveal-load-splits');

			if (window.gsap && heroSubline) {
				window.gsap.to(heroSubline, {
					y: 0,
					autoAlpha: 1,
					duration: 0.42,
					delay: 0.92,
					ease: 'power2.out',
					onComplete: function () {
						window.gsap.set(heroSubline, { clearProps: 'transform,opacity,visibility,will-change' });
					}
				});
			}
		}

		function revealNav() {
			if (didNavReveal) {
				return;
			}

			didNavReveal = true;
			body.classList.add('tp-preloader-nav-ready');
			dispatch('tp:preloader:reveal-nav');
		}

		function finish(forceReveal) {
			if (didFinish) {
				return;
			}

			didFinish = true;
			window.clearTimeout(failSafeTimer);

			if (forceReveal) {
				revealHero();
				revealNav();
			}

			body.classList.remove('is-loading');
			root.classList.remove('tp-preloader-enabled');

			if (window.gsap) {
				window.gsap.set(preloader, { display: 'none' });
				window.gsap.set(
					[panel, logoWrap, topPiece, bottomPiece, header, navLogo, heroTitle].filter(Boolean),
					{ clearProps: 'all' }
				);
			} else if (preloader) {
				preloader.style.display = 'none';
			}

			dispatch('tp:preloader:finished');
		}

		if (!preloader || !panel || !logoWrap || !topPiece || !bottomPiece) {
			root.classList.remove('tp-preloader-enabled');
			revealHero();
			revealNav();
			return;
		}

		body.classList.add('tp-preloader-page', 'is-loading');

		if (reduceMotion || !window.gsap) {
			finish(true);
			return;
		}

		window.gsap.set(logoWrap, { autoAlpha: 1 });
		window.gsap.set(topPiece, { yPercent: 112 });
		window.gsap.set(bottomPiece, { yPercent: 112 });
		window.gsap.set([heroTitle, heroSubline].filter(Boolean), { autoAlpha: 0 });

		if (heroTitle) {
			window.gsap.set(heroTitle, { y: 28 });
		}

		if (heroSubline) {
			window.gsap.set(heroSubline, { y: 18, willChange: 'transform, opacity' });
		}

		failSafeTimer = window.setTimeout(function () {
			finish(true);
		}, 4500);

		window.gsap.timeline({
			defaults: { ease: 'power2.out' },
			onComplete: function () {
				finish(false);
			}
		})
			.to(topPiece, {
				yPercent: 0,
				duration: 0.62
			}, 0.14)
			.to(bottomPiece, {
				yPercent: 0,
				duration: 0.62
			}, 0.20)
			.to({}, { duration: 1.18 }, 0.82)
			.add(function () {
				body.classList.add('tp-preloader-revealing');
			}, 2.00)
			.set(header, {
				opacity: 1,
				visibility: 'visible',
				pointerEvents: 'auto'
			}, 2.00)
			.fromTo(navLogo, {
				y: 18,
				autoAlpha: 0
			}, {
				y: 0,
				autoAlpha: 1,
				duration: 0.55,
				ease: 'power2.out'
			}, 2.10)
			.add(revealHero, 2.42)
			.to(panel, {
				y: '-120%',
				duration: 1,
				ease: 'power2.inOut'
			}, 2.00)
			.to(logoWrap, {
				y: '-140%',
				autoAlpha: 0,
				duration: 0.9,
				ease: 'power2.inOut'
			}, 2.00)
			.to(preloader, {
				autoAlpha: 0,
				duration: 0.18,
				ease: 'power2.out'
			}, 2.92)
			.add(revealNav, 2.98);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
