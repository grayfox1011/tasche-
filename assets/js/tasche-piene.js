(function () {
	'use strict';

	var doc = document.documentElement;
	var ua = window.navigator ? window.navigator.userAgent || '' : '';
	var isIOSWebKit = /iP(hone|ad|od)/.test(ua) || (window.navigator && window.navigator.platform === 'MacIntel' && window.navigator.maxTouchPoints > 1);

	function setImportant(element, property, value) {
		if (element) {
			element.style.setProperty(property, value, 'important');
		}
	}

	if (document.body && document.body.classList.contains('et-fb')) {
		return;
	}

	doc.classList.add('tp-js');

	if (isIOSWebKit) {
		doc.classList.add('tp-ios-webkit');
	}

	if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
		doc.classList.add('tp-reduced-motion');
	}



	function initIOSMenuPriceLayout() {
		if (!isIOSWebKit) {
			return;
		}

		var selectors = {
			tascaLabel: '.tp-menu-label-tasca p, .tp-menu-label-tasca .et_pb_text_inner, .tp-menu-price-label-tasca .tp-menu-price-label-text',
			sLabel: '.tp-menu-label-piatto p, .tp-menu-label-piatto .et_pb_text_inner, .tp-menu-price-label-s .tp-menu-price-label-text',
			mLabel: '.tp-menu-price-label-m .tp-menu-price-label-text',
			lLabel: '.tp-menu-price-label-l .tp-menu-price-label-text',
			tascaValue: '.tp-menu-price-value-tasca p, .tp-menu-price-value-tasca .et_pb_text_inner, .tp-menu-price-label-tasca .tp-menu-price-value',
			mValue: '.tp-menu-price-label-m .tp-menu-price-value',
			lValue: '.tp-menu-price-label-l .tp-menu-price-value',
			sValue: '.tp-menu-price-label-s .tp-menu-price-value'
		};

		function placeNode(row, selector, x, y, width, display) {
			var node = row.querySelector(selector);

			if (!node) {
				return;
			}

			setImportant(node, 'position', 'absolute');
			setImportant(node, 'left', x);
			setImportant(node, 'top', y);
			setImportant(node, 'width', width || 'auto');
			setImportant(node, 'min-width', '0');
			setImportant(node, 'max-width', 'none');
			setImportant(node, 'height', 'auto');
			setImportant(node, 'min-height', '0');
			setImportant(node, 'margin', '0');
			setImportant(node, 'padding', '0');
			setImportant(node, 'display', display || 'block');
			setImportant(node, 'visibility', 'visible');
			setImportant(node, 'opacity', '1');
			setImportant(node, 'transform', 'none');
			setImportant(node, 'z-index', '6');
			setImportant(node, 'text-align', 'center');
			setImportant(node, 'white-space', 'nowrap');
		}

		function layout() {
			var rows = document.querySelectorAll('.tp-menu-table--main .tp-menu-row');

			rows.forEach(function (row) {
				var prices = row.querySelector('.tp-menu-prices');
				var wrappers = row.querySelectorAll('.tp-menu-price, .tp-menu-price p, .tp-menu-price .et_pb_text_inner, .tp-menu-label, .tp-menu-label p, .tp-menu-label .et_pb_text_inner');

				if (!prices || row.classList.contains('tp-menu-row--proposal')) {
					return;
				}

				setImportant(prices, 'position', 'absolute');
				setImportant(prices, 'inset', '0');
				setImportant(prices, 'display', 'block');
				setImportant(prices, 'width', '100%');
				setImportant(prices, 'height', '100%');
				setImportant(prices, 'margin', '0');
				setImportant(prices, 'padding', '0');
				setImportant(prices, 'pointer-events', 'none');
				setImportant(prices, 'background-position', '9.5% 76%, 49.6% 77%, 86.5% 76%, 22% 65%, 46% 69%');
				setImportant(prices, 'background-size', '14.37% auto, 11.33% auto, 12.52% auto, 3.86% 10.2%, 3.52% 10.8%');

				wrappers.forEach(function (wrapper) {
					setImportant(wrapper, 'position', 'static');
					setImportant(wrapper, 'display', 'block');
					setImportant(wrapper, 'width', '0');
					setImportant(wrapper, 'min-width', '0');
					setImportant(wrapper, 'height', '0');
					setImportant(wrapper, 'min-height', '0');
					setImportant(wrapper, 'margin', '0');
					setImportant(wrapper, 'padding', '0');
				});

				placeNode(row, selectors.tascaLabel, '11.66%', '55%', '72px');
				placeNode(row, selectors.sLabel, '37.64%', 'calc(60.5% - 14px)', '82px');
				placeNode(row, selectors.tascaValue, '7.51%', '88%', '15.64%');
				placeNode(row, selectors.mValue, '41.86%', '88%', '15.64%');
				placeNode(row, selectors.lValue, '72.67%', '88%', '18.43%');

				placeNode(row, selectors.mLabel, '52.16%', '67.3%', '24px', 'none');
				placeNode(row, selectors.lLabel, '72.2%', '67.3%', '24px', 'none');

				var sValue = row.querySelector(selectors.sValue);
				setImportant(sValue, 'display', 'none');
			});
		}

		layout();
		window.addEventListener('load', layout, { once: true });
		window.addEventListener('resize', layout);
		window.setTimeout(layout, 600);
	}

	function initIOSHeaderFallback() {
		if (!isIOSWebKit) {
			return;
		}

		function forceHeaderVisible() {
			var header = document.querySelector(".site-header.tp-theme-header, .tp-theme-header");
			var headerShell = document.querySelector(".et-l--header");
			var nav = document.querySelector(".tp-theme-nav");
			var toggle = document.querySelector(".tp-theme-nav-toggle");

			doc.classList.remove("tp-preloader-enabled");

			if (document.body) {
				document.body.classList.add("tp-preloader-revealing", "tp-preloader-nav-ready");
			}

			[headerShell, header, nav, toggle].forEach(function (element) {
				if (!element) {
					return;
				}

				setImportant(element, "display", element === toggle ? "flex" : "block");
				setImportant(element, "visibility", "visible");
				setImportant(element, "opacity", "1");
				setImportant(element, "pointer-events", element === headerShell ? "none" : "auto");
			});

			if (header) {
				setImportant(header, "position", "absolute");
				setImportant(header, "top", "32.7px");
				setImportant(header, "left", "0");
				setImportant(header, "width", "100%");
				setImportant(header, "z-index", "3000");
				setImportant(header, "transform", "none");
				setImportant(header, "-webkit-transform", "none");
			}

			if (toggle) {
				setImportant(toggle, "position", "absolute");
				setImportant(toggle, "top", "0");
				setImportant(toggle, "right", "10.8px");
			}
		}

		window.setTimeout(forceHeaderVisible, 5200);
		window.addEventListener("load", function () {
			window.setTimeout(forceHeaderVisible, 1200);
		}, { once: true });
	}

	function initThemeNav() {
		var nav = document.querySelector('.tp-theme-nav');
		var toggle = document.querySelector('.tp-theme-nav-toggle');
		var links = document.querySelectorAll('.tp-theme-nav-links a');
		var desktopQuery = window.matchMedia ? window.matchMedia('(min-width: 981px)') : null;

		if (!nav || !toggle) {
			return;
		}

		var openLabel = toggle.getAttribute('data-open-label') || 'Apri menu';
		var closeLabel = toggle.getAttribute('data-close-label') || 'Chiudi menu';

		function setOpen(isOpen) {
			nav.classList.toggle('is-nav-open', isOpen);
			document.body.classList.toggle('is-nav-open', isOpen);
			toggle.setAttribute('aria-expanded', String(isOpen));
			toggle.setAttribute('aria-label', isOpen ? closeLabel : openLabel);
		}

		toggle.addEventListener('click', function () {
			setOpen(!nav.classList.contains('is-nav-open'));
		});

		links.forEach(function (link) {
			link.addEventListener('click', function () {
				setOpen(false);
			});
		});

		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape') {
				setOpen(false);
			}
		});

		if (!desktopQuery) {
			return;
		}

		function onDesktopChange(event) {
			if (event.matches) {
				setOpen(false);
			}
		}

		if (typeof desktopQuery.addEventListener === 'function') {
			desktopQuery.addEventListener('change', onDesktopChange);
		} else if (typeof desktopQuery.addListener === 'function') {
			desktopQuery.addListener(onDesktopChange);
		}
	}

	function initIntroButtons() {
		var buttons = document.querySelectorAll('a.tp-intro-button, a.tp-location-button');

		buttons.forEach(function (button) {
			var text;
			var label;
			var arrow;
			var glyph;

			if (button.classList.contains('tp-figma-button--enhanced')) {
				return;
			}

			text = Array.prototype.reduce.call(button.childNodes, function (value, node) {
				if (node.nodeType === Node.TEXT_NODE) {
					return value + node.nodeValue;
				}

				if (
					node.nodeType === Node.ELEMENT_NODE &&
					!node.classList.contains('tp-intro-button__arrow') &&
					!node.classList.contains('tp-intro-button__label')
				) {
					return value + node.textContent;
				}

				if (node.nodeType === Node.ELEMENT_NODE && node.classList.contains('tp-intro-button__label')) {
					return value + node.textContent;
				}

				return value;
			}, '').trim();

			arrow = document.createElement('span');
			arrow.className = 'tp-intro-button__arrow';
			arrow.setAttribute('aria-hidden', 'true');

			glyph = document.createElement('span');
			glyph.className = 'tp-intro-button__glyph';
			glyph.textContent = '\u2192';
			arrow.appendChild(glyph);

			if (!text) {
				return;
			}

			button.textContent = '';

			label = document.createElement('span');
			label.className = 'tp-intro-button__label';
			label.textContent = text;

			if (!button.getAttribute('aria-label')) {
				button.setAttribute('aria-label', text);
			}

			button.classList.add('tp-figma-button--enhanced');
			button.appendChild(label);
			button.appendChild(arrow);
		});
	}

	function initHoursItems() {
		var items = Array.prototype.slice.call(document.querySelectorAll('.tp-hours-item'));
		var activeItem = null;

		if (!items.length) {
			return;
		}

		function setActive(item) {
			if (activeItem && activeItem !== item) {
				activeItem.classList.remove('is-active');
			}

			activeItem = item;
			item.classList.add('is-active');
		}

		function clearActive(item) {
			if (!item || activeItem === item) {
				activeItem = null;
			}

			if (item) {
				item.classList.remove('is-active');
			}
		}

		items.forEach(function (item) {
			if (!item.hasAttribute('tabindex')) {
				item.setAttribute('tabindex', '0');
			}

			item.addEventListener('pointerenter', function () {
				setActive(item);
			});

			item.addEventListener('pointerleave', function () {
				clearActive(item);
			});

			item.addEventListener('pointerdown', function (event) {
				if (!event.pointerType || event.pointerType === 'mouse') {
					return;
				}

				if (item.classList.contains('is-active')) {
					clearActive(item);
					return;
				}

				setActive(item);
			});

			item.addEventListener('focusin', function () {
				setActive(item);
			});

			item.addEventListener('focusout', function () {
				clearActive(item);
			});

			item.addEventListener('keydown', function (event) {
				if (event.key === 'Escape') {
					clearActive(item);
				}
			});
		});

		document.addEventListener('pointerdown', function (event) {
			if (event.target.closest('.tp-hours-item')) {
				return;
			}

			if (activeItem) {
				clearActive(activeItem);
			}
		});
	}

	initIOSHeaderFallback();
	initIOSMenuPriceLayout();
	initThemeNav();
	initIntroButtons();
	initHoursItems();
})();
