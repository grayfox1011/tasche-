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

	// The iPhone header fix below writes inline styles. An iPad at 981 px or more (landscape,
	// iPad Pro) shows the desktop layout, so it drops them; they come back below 981 px.
	var desktopMq = window.matchMedia ? window.matchMedia('(min-width: 981px)') : null;

	function isDesktopWidth() {
		return !!(desktopMq && desktopMq.matches);
	}

	function setTracked(list, element, property, value) {
		if (!element) {
			return;
		}

		setImportant(element, property, value);

		for (var i = 0; i < list.length; i++) {
			if (list[i][0] === element && list[i][1] === property) {
				return;
			}
		}

		list.push([element, property]);
	}

	function clearTracked(list) {
		list.forEach(function (entry) {
			entry[0].style.removeProperty(entry[1]);
		});
		list.length = 0;
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

	/**
	 * Active state shared by the hours, the Tasca/Piatto cards and the Materie cards:
	 * one element at a time gets .is-active on mouse over and keyboard focus; a touch
	 * tap toggles it and Escape clears it. Options: `outside` (selector) clears it on
	 * a tap or click elsewhere, `onChange(element)` runs after every change.
	 * Returns a function that removes the listeners and the class.
	 */
	function bindActiveState(elements, options) {
		var settings = options || {};
		var activeItem = null;
		var cleanups = [];

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

		Array.prototype.forEach.call(elements, function (item) {
			var changed = function () {
				if (settings.onChange) {
					settings.onChange(item);
				}
			};
			var show = function () {
				setActive(item);
				changed();
			};
			var hide = function () {
				clearActive(item);
				changed();
			};
			var toggleFromPointer = function (event) {
				if (!event.pointerType || event.pointerType === 'mouse') {
					return;
				}

				if (item.classList.contains('is-active')) {
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

			if (!item.hasAttribute('tabindex')) {
				item.setAttribute('tabindex', '0');
			}

			item.addEventListener('pointerenter', show);
			item.addEventListener('pointerleave', hide);
			item.addEventListener('pointerdown', toggleFromPointer);
			item.addEventListener('focusin', show);
			item.addEventListener('focusout', hide);
			item.addEventListener('keydown', handleKeydown);

			cleanups.push(function () {
				item.removeEventListener('pointerenter', show);
				item.removeEventListener('pointerleave', hide);
				item.removeEventListener('pointerdown', toggleFromPointer);
				item.removeEventListener('focusin', show);
				item.removeEventListener('focusout', hide);
				item.removeEventListener('keydown', handleKeydown);
				item.classList.remove('is-active');
				changed();
			});
		});

		if (settings.outside) {
			var handleOutside = function (event) {
				if (event.target.closest(settings.outside)) {
					return;
				}

				if (activeItem) {
					clearActive(activeItem);
				}
			};

			document.addEventListener('pointerdown', handleOutside);
			cleanups.push(function () {
				document.removeEventListener('pointerdown', handleOutside);
			});
		}

		return function () {
			cleanups.forEach(function (cleanup) {
				cleanup();
			});
		};
	}

	window.tpBindActiveState = bindActiveState;

	function initIOSHeaderFallback() {
		if (!isIOSWebKit) {
			return;
		}

		var styled = [];
		var revealed = false;

		function setHeaderStyle(element, property, value) {
			setTracked(styled, element, property, value);
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

			revealed = true;

			if (isDesktopWidth()) {
				return;
			}

			[headerShell, header, nav, toggle].forEach(function (element) {
				if (!element) {
					return;
				}

				setHeaderStyle(element, "display", element === toggle ? "flex" : "block");
				setHeaderStyle(element, "visibility", "visible");
				setHeaderStyle(element, "opacity", "1");
				setHeaderStyle(element, "pointer-events", element === headerShell ? "none" : "auto");
			});

			if (header) {
				setHeaderStyle(header, "position", "absolute");
				setHeaderStyle(header, "top", "32.7px");
				setHeaderStyle(header, "left", "0");
				setHeaderStyle(header, "width", "100%");
				setHeaderStyle(header, "z-index", "3000");
				setHeaderStyle(header, "transform", "none");
				setHeaderStyle(header, "-webkit-transform", "none");
			}

			if (toggle) {
				setHeaderStyle(toggle, "position", "absolute");
				setHeaderStyle(toggle, "top", "0");
				setHeaderStyle(toggle, "right", "10.8px");
			}
		}

		if (desktopMq) {
			var onWidthChange = function () {
				if (isDesktopWidth()) {
					clearTracked(styled);
				} else if (revealed) {
					forceHeaderVisible();
				}
			};

			if (desktopMq.addEventListener) {
				desktopMq.addEventListener('change', onWidthChange);
			} else if (desktopMq.addListener) {
				desktopMq.addListener(onWidthChange);
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
		var items = document.querySelectorAll('.tp-hours-item');

		if (!items.length) {
			return;
		}

		bindActiveState(items, { outside: '.tp-hours-item' });
	}

	initIOSHeaderFallback();
	initThemeNav();
	initIntroButtons();
	initHoursItems();
})();
