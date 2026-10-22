(function () {
	'use strict';

	if (document.body && document.body.classList.contains('et-fb')) {
		return;
	}

	var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var desktopQuery = window.matchMedia ? window.matchMedia('(min-width: 981px)') : null;

	var nativeGroups = [
		{
			selector: '.tp-hero-title h1, .tp-hero-title .et_pb_module_header',
			trigger: 'load',
			splitType: 'lines,chars',
			duration: 0.52,
			stagger: 0.018,
			delay: 0.14,
			yPercent: 110,
			ease: 'power2.out',
			responsive: 'desktop',
			preloaderEvent: 'tp:preloader:reveal-hero'
		},
		{
			selector: [
				'.tp-intro-title h2',
				'.tp-intro-title .et_pb_module_header',
				'.tp-alberto-title h2',
				'.tp-alberto-title .et_pb_module_header',
				'.tp-choice-title h2',
				'.tp-menu-title h2',
				'.tp-materie-title h2',
				'.tp-location-title h2'
			].join(','),
			trigger: 'viewport',
			splitType: 'lines,chars',
			duration: 0.66,
			stagger: 0.014,
			delay: 0,
			yPercent: 115,
			ease: 'power3.out',
			responsive: 'desktop'
		},
		{
			selector: '.tp-theme-nav-links a',
			trigger: 'load',
			splitType: 'chars',
			duration: 0.45,
			stagger: 0.018,
			delay: 0.35,
			groupDelay: 0.12,
			yPercent: 110,
			ease: 'power2.out',
			responsive: 'desktop',
			preloaderEvent: 'tp:preloader:reveal-nav'
		}
	];

	function reveal(element) {
		element.classList.add('is-revealed');
	}

	function getTargets(split, type) {
		if (type.indexOf('chars') !== -1) {
			return split.chars;
		}

		if (type.indexOf('words') !== -1) {
			return split.words;
		}

		return split.lines;
	}

	function toArray(list) {
		return Array.prototype.slice.call(list || []);
	}

	function getDataValue(element, name, fallback) {
		var value = element.dataset ? element.dataset[name] : undefined;

		return typeof value === 'undefined' || value === '' ? fallback : value;
	}

	function getNumber(element, name, fallback) {
		var value = parseFloat(getDataValue(element, name, fallback));

		return Number.isFinite(value) ? value : fallback;
	}

	function getInteger(element, name, fallback) {
		var value = parseInt(getDataValue(element, name, fallback), 10);

		return Number.isFinite(value) ? value : fallback;
	}

	function shouldSkipNative(element) {
		return (
			element.classList.contains('tp-split-text') ||
			Boolean(element.closest('[data-tp-split-text="1"]'))
		);
	}

	function collectDeclarativeItems(seen) {
		return toArray(document.querySelectorAll('[data-tp-split-text="1"]')).map(function (element) {
			seen.push(element);

			return {
				element: element,
				settings: {
					lineSelector: '.tp-split-text__line-inner',
					trigger: element.dataset.tpSplitTrigger || 'load',
					splitType: element.dataset.tpSplitType || 'chars',
					duration: 0.52,
					stagger: 0.018,
					delay: 0.14,
					yPercent: 110,
					ease: 'power2.out',
					responsive: element.dataset.tpSplitResponsive || 'desktop',
					preloaderEvent: element.dataset.tpSplitPreloaderEvent || 'tp:preloader:reveal-load-splits'
				}
			};
		});
	}

	function collectNativeItems(seen) {
		var items = [];

		nativeGroups.forEach(function (group) {
			toArray(document.querySelectorAll(group.selector)).forEach(function (element, index) {
				if (seen.indexOf(element) !== -1 || shouldSkipNative(element)) {
					return;
				}

				seen.push(element);

				items.push({
					element: element,
					settings: {
						trigger: group.trigger,
						splitType: group.splitType,
						duration: group.duration,
						stagger: group.stagger,
						delay: group.delay + (group.groupDelay || 0) * index,
						yPercent: group.yPercent,
						ease: group.ease,
						responsive: group.responsive,
						preloaderEvent: group.preloaderEvent
					}
				});
			});
		});

		return items;
	}

	function collectItems() {
		var seen = [];

		return collectDeclarativeItems(seen).concat(collectNativeItems(seen));
	}

	function splitDeclarativeLines(element, splitType) {
		var splits = [];
		var targets = [];

		toArray(element.querySelectorAll('.tp-split-text__line-inner')).forEach(function (line) {
			var split = new window.SplitText(line, { type: splitType });

			splits.push(split);
			targets = targets.concat(getTargets(split, splitType));
		});

		return { splits: splits, targets: targets };
	}

	function splitNativeElement(element, splitType) {
		var options = { type: splitType };

		if (splitType.indexOf('lines') !== -1) {
			options.linesClass = 'tp-split-line';
		}

		var split = new window.SplitText(element, options);

		if (split.lines && split.lines.length) {
			window.gsap.set(split.lines, { overflow: 'hidden' });
		}

		return {
			splits: [split],
			targets: getTargets(split, splitType)
		};
	}

	function animate(item) {
		var element = item.element;
		var settings = item.settings;

		if (element.dataset.tpSplitReady === '1') {
			return;
		}

		element.dataset.tpSplitReady = '1';

		var responsive = getDataValue(element, 'tpSplitResponsive', settings.responsive || 'desktop');

		if (reduceMotion || (responsive !== 'all' && desktopQuery && !desktopQuery.matches)) {
			reveal(element);
			return;
		}

		if (typeof window.gsap === 'undefined' || typeof window.SplitText === 'undefined') {
			reveal(element);
			return;
		}

		window.gsap.registerPlugin(window.SplitText);

		var splitType = getDataValue(element, 'tpSplitType', settings.splitType || 'chars');
		var duration = getNumber(element, 'tpSplitDuration', settings.duration || 0.52);
		var stagger = getNumber(element, 'tpSplitStagger', settings.stagger || 0.018);
		var delay = getNumber(element, 'tpSplitDelay', settings.delay || 0);
		var yPercent = getInteger(element, 'tpSplitY', settings.yPercent || 110);
		var ease = getDataValue(element, 'tpSplitEase', settings.ease || 'power2.out');
		var result;

		try {
			result = settings.lineSelector
				? splitDeclarativeLines(element, splitType)
				: splitNativeElement(element, splitType);
		} catch (error) {
			reveal(element);
			return;
		}

		if (!result.targets.length) {
			reveal(element);
			return;
		}

		window.gsap.set(result.targets, {
			yPercent: yPercent,
			autoAlpha: 0,
			willChange: 'transform, opacity'
		});

		var completed = false;
		var cleanup = function () {
			if (completed) {
				return;
			}

			completed = true;
			element.classList.remove('is-animating');
			reveal(element);
			window.gsap.set(result.targets, { clearProps: 'transform,opacity,visibility,will-change' });
		};

		window.gsap.to(result.targets, {
			yPercent: 0,
			autoAlpha: 1,
			duration: duration,
			stagger: stagger,
			delay: delay,
			ease: ease,
			onStart: function () {
				element.classList.add('is-animating');
			},
			onComplete: cleanup
		});

		window.setTimeout(cleanup, Math.ceil((delay + duration + stagger * result.targets.length + 0.5) * 1000));
	}

	function init() {
		var items = collectItems();
		var deferredItems = {};
		var hasPreloader = document.documentElement.classList.contains('tp-preloader-enabled');

		if (!items.length) {
			return;
		}

		function deferUntilPreloader(item) {
			var eventName = item.settings.preloaderEvent || 'tp:preloader:reveal-load-splits';

			if (!deferredItems[eventName]) {
				deferredItems[eventName] = [];

				document.addEventListener(eventName, function () {
					deferredItems[eventName].forEach(animate);
					deferredItems[eventName] = [];
				});
			}

			deferredItems[eventName].push(item);
		}

		if (!('IntersectionObserver' in window)) {
			items.forEach(function (item) {
				if (hasPreloader && item.settings.trigger !== 'viewport') {
					deferUntilPreloader(item);
				} else {
					animate(item);
				}
			});
			return;
		}

		var observer = new IntersectionObserver(
			function (entries) {
				entries.forEach(function (entry) {
					if (!entry.isIntersecting) {
						return;
					}

					animate(entry.target.__tpSplitItem);
					observer.unobserve(entry.target);
				});
			},
			{ threshold: 0.2 }
		);

		items.forEach(function (item) {
			item.element.__tpSplitItem = item;

			if (item.settings.trigger === 'viewport' || item.element.dataset.tpSplitTrigger === 'viewport') {
				observer.observe(item.element);
			} else if (hasPreloader) {
				deferUntilPreloader(item);
			} else {
				animate(item);
			}
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
