/*
 * 0.8.0: the buttons with the arrow ("Guarda il menù", "Le materie prime", "Mappe": classes tp-hero-button,
 * tp-intro-button, tp-location-button) as wide as their words, and with the border chosen in Divi.
 *
 * The stylesheet gives each button the measures of the Figma: the width of the label, the place of the arrow and
 * the width of the link are fixed numbers, right for the words the theme writes. With other words (Pulsante >
 * Testo pulsante) the longer ones came out of the button or went under the arrow and the shorter ones left it
 * wide and half empty. Here the words are measured: when they are as wide as the theme's (--tp-intro-button-text,
 * in the stylesheet) nothing is touched; otherwise label, arrow and link are moved by the difference, on the site
 * (the label and the arrow tasche-piene.js makes) and in the Visual Builder (the link drawn as the button). On
 * the phones, where "Le materie prime" and "Mappe" are a box with the words in the middle, the box only grows.
 *
 * The border (Pulsante > Design > Bordo) and the shadow (Design > Ombra riquadro) are the link's, and on the site
 * the button one sees is label and arrow: the four sides of the border and the shadow are read here and given to
 * the stylesheet, which draws them with the two pieces ("0.8.0: the values of Pulsante > Design").
 */
(function () {
	'use strict';

	var PROPS = ['--tp-intro-button-label-width', '--tp-intro-button-arrow-left', '--tp-intro-button-arrow-hover-x', 'width'];
	var SIDES = ['top', 'right', 'bottom', 'left'];
	var SELECTOR = 'a.tp-intro-button, a.tp-location-button, a.tp-hero-button';
	var canvas = null;

	// The words of the button as typed (on the site inside the label, in the builder inside the link).
	function words(button, label) {
		var text = '';
		var nodes = (label || button).childNodes;
		var i;

		for (i = 0; i < nodes.length; i++) {
			if (nodes[i].nodeType === 3) {
				text += nodes[i].nodeValue;
			} else if (
				nodes[i].nodeType === 1 &&
				!/^(style|script|svg)$/i.test(nodes[i].tagName) &&
				// Not the arrow, not what the Visual Builder keeps inside the link (its styles, its buttons).
				!/tp-intro-button__(arrow|glyph)|(^|\s)et-(vb|fb)-/.test(typeof nodes[i].className === 'string' ? nodes[i].className : '')
			) {
				text += nodes[i].textContent;
			}
		}

		return text.replace(/\s+/g, ' ').trim();
	}

	// The width of the words on one line in the font of the element, whatever room they have now.
	function wordsWidth(text, style) {
		var context;
		var spacing = parseFloat(style.letterSpacing) || 0;
		var transform = style.textTransform;

		if (transform === 'uppercase') {
			text = text.toUpperCase();
		} else if (transform === 'lowercase') {
			text = text.toLowerCase();
		} else if (transform === 'capitalize') {
			text = text.replace(/(^|\s)(\S)/g, function (match, space, letter) {
				return space + letter.toUpperCase();
			});
		}

		canvas = canvas || document.createElement('canvas');
		context = canvas.getContext('2d');

		if (!context) {
			return NaN;
		}

		context.font = [style.fontStyle, style.fontWeight, style.fontSize, style.fontFamily].join(' ');

		return context.measureText(text).width + spacing * text.length;
	}

	function fontReady(style) {
		if (!document.fonts || typeof document.fonts.check !== 'function') {
			return true;
		}

		try {
			return document.fonts.check([style.fontStyle, style.fontWeight, style.fontSize, style.fontFamily].join(' '));
		} catch (error) {
			return true;
		}
	}

	// The measures the stylesheet gives the button at this width of the window, read on an empty copy beside it:
	// the button itself may carry the measures set here and be in the middle of a transition.
	function stylesheetMeasures(button) {
		var probe = button.cloneNode(false);
		var style;
		var width;
		var measures;

		probe.removeAttribute('style');
		probe.removeAttribute('id');
		probe.removeAttribute('href');
		probe.removeAttribute('data-tp-button-fit');
		probe.setAttribute('aria-hidden', 'true');
		probe.style.cssText = 'visibility:hidden!important;transition:none!important;pointer-events:none!important;';
		button.parentNode.insertBefore(probe, button.nextSibling);
		style = window.getComputedStyle(probe);
		// The width with its decimals (277.52 px), which offsetWidth rounds.
		width = style.boxSizing === 'border-box' ? parseFloat(style.width) : NaN;
		measures = {
			text: parseFloat(style.getPropertyValue('--tp-intro-button-text')),
			label: parseFloat(style.getPropertyValue('--tp-intro-button-label-width')),
			arrow: parseFloat(style.getPropertyValue('--tp-intro-button-arrow-left')),
			hover: style.getPropertyValue('--tp-intro-button-arrow-hover-x').trim(),
			width: isFinite(width) && width > 0 ? width : probe.offsetWidth,
			// In the builder the stylesheet says whether the link is drawn as label and arrow or as one box.
			pieces: style.getPropertyValue('--tp-intro-button-arrow').trim() === '1',
			// The four sides of the link: its border (none without one chosen in Divi) and its padding.
			sides: SIDES.map(function (side) {
				var name = side.charAt(0).toUpperCase() + side.slice(1);

				return {
					width: parseFloat(style['border' + name + 'Width']) || 0,
					style: style['border' + name + 'Style'],
					color: style['border' + name + 'Color'],
					padding: parseFloat(style['padding' + name]) || 0
				};
			}),
			// The shadow Divi writes on the link (Design > Ombra riquadro).
			shadow: style.boxShadow
		};
		probe.parentNode.removeChild(probe);

		return measures;
	}

	// How much wider than the stylesheet's width the button can get inside its parent, in the pixels of its layout.
	function room(button, width) {
		var parent = button.parentElement;
		var box = button.getBoundingClientRect();
		var scale = button.offsetWidth ? box.width / button.offsetWidth : 1;
		var limit = parent ? parent.getBoundingClientRect().right : window.innerWidth;

		limit = Math.min(limit, document.documentElement.clientWidth - 16);

		return scale > 0 ? Math.max(0, (limit - box.left) / scale - width) : 0;
	}

	// Write a value on the link (or take it away), only when it changes: the button animates its changes.
	function set(button, prop, value, important) {
		if (button.style.getPropertyValue(prop) === value && (!value || !important || button.style.getPropertyPriority(prop) === 'important')) {
			return;
		}

		if (value) {
			button.style.setProperty(prop, value, important ? 'important' : '');
		} else {
			button.style.removeProperty(prop);
		}
	}

	// Write the measures (or take them away).
	function apply(button, values) {
		PROPS.forEach(function (prop) {
			set(button, prop, values && values[prop] ? values[prop] : '');
		});

		if (values) {
			if (button.getAttribute('data-tp-button-fit') !== values.kind) {
				button.setAttribute('data-tp-button-fit', values.kind);
			}
		} else if (button.hasAttribute('data-tp-button-fit')) {
			button.removeAttribute('data-tp-button-fit');
		}
	}

	// The border chosen in Pulsante > Design > Bordo.
	// On the site the link has a class tp-own-border-* then (from PHP) and does not paint its border: label and
	// arrow draw it inside themselves with the four sides read here (--tp-own-border-top ...), and go back by the
	// room the link's border takes at its top and left (--tp-own-border-y, --tp-own-border-x).
	// In the builder the link is the button and paints its border itself: its paddings give the border its room,
	// so that the words stay where they are, and the arrow goes back as on the site.
	function border(button, measures, builder) {
		var own = false;

		if (builder || /(^|\s)tp-own-border-[dtp](\s|$)/.test(button.className)) {
			measures.sides.forEach(function (side) {
				own = own || side.width > 0;
			});
		}

		SIDES.forEach(function (name, index) {
			var side = measures.sides[index];

			set(button, '--tp-own-border-' + name, own && !builder ? side.width + 'px ' + side.style + ' ' + side.color : '');

			if (builder) {
				set(button, 'padding-' + name, own && side.width > 0 ? Math.max(0, side.padding - side.width).toFixed(2) + 'px' : '', true);
			}
		});

		set(button, '--tp-own-border-x', own ? measures.sides[3].width + 'px' : '');
		set(button, '--tp-own-border-y', own ? measures.sides[0].width + 'px' : '');
	}

	// The shadow chosen in Design > Ombra riquadro is the link's, whose box is wider than the button one sees: on
	// the site the link (class tp-own-shadow-*, from PHP) does not paint it and the stylesheet puts it behind label
	// and arrow, with the value read here on the copy (an inner shadow, which they cannot share, is left out).
	function shadow(button, measures, builder) {
		var value = '';

		if (!builder && measures.shadow && measures.shadow !== 'none' && /(^|\s)tp-own-shadow-[dtp](\s|$)/.test(button.className)) {
			value = measures.shadow.split(/,(?![^(]*\))/).filter(function (part) {
				return !/(^|\s)inset(\s|$)/.test(part);
			}).join(',').trim();
		}

		set(button, '--tp-own-shadow', value);
	}

	function fit(button) {
		var label = button.querySelector('.tp-intro-button__label');
		var builder = !label;
		var style;
		var text;
		var measures;
		var width;
		var delta;
		var hover;
		var padding;
		var needed;
		var values;

		// On the site only once tasche-piene.js has made the label; in the builder the link holds the words.
		if ((builder && !document.body.classList.contains('et-fb')) || !button.parentNode) {
			return;
		}

		measures = stylesheetMeasures(button);
		border(button, measures, builder);
		shadow(button, measures, builder);
		style = window.getComputedStyle(label || button);
		text = words(button, label);

		if (!text || !fontReady(style)) {
			return;
		}

		width = wordsWidth(text, style);

		if (!isFinite(measures.text) || measures.text <= 0 || !isFinite(width) || width <= 0) {
			apply(button, null);

			return;
		}

		// Label and arrow side by side (computers, tablets, the hero's button on phones), or one box with the
		// words in the middle (the other two on phones).
		if (label ? style.position === 'absolute' : measures.pieces) {
			delta = width - measures.text;

			if (Math.abs(delta) < 2 || !isFinite(measures.label) || !isFinite(measures.arrow)) {
				apply(button, null);

				return;
			}

			delta = Math.min(delta, room(button, measures.width));

			if (measures.label + delta < 60) {
				apply(button, null);

				return;
			}

			hover = parseFloat(measures.hover);
			values = { kind: delta > 0 ? 'wider' : 'narrower' };
			values['--tp-intro-button-label-width'] = (measures.label + delta).toFixed(2) + 'px';
			values['--tp-intro-button-arrow-left'] = (measures.arrow + delta).toFixed(2) + 'px';
			values.width = (measures.width + delta).toFixed(2) + 'px';

			if (isFinite(hover)) {
				values['--tp-intro-button-arrow-hover-x'] = (hover + delta).toFixed(4) + (/px$/.test(measures.hover) ? 'px' : '');
			}

			apply(button, values);

			return;
		}

		padding = (parseFloat(style.paddingLeft) || 0) + (parseFloat(style.paddingRight) || 0);

		if (label) {
			style = window.getComputedStyle(button);
			padding += (parseFloat(style.paddingLeft) || 0) + (parseFloat(style.paddingRight) || 0);
		}

		// At least 14 px beside the words, as "Le materie prime" has, inside the border.
		needed = width + Math.max(padding, 28) + measures.sides[1].width + measures.sides[3].width + 2;

		if (needed > measures.width + 1) {
			apply(button, { kind: 'wider', width: Math.min(needed, measures.width + room(button, measures.width)).toFixed(2) + 'px' });
		} else {
			apply(button, null);
		}
	}

	function fitAll() {
		Array.prototype.forEach.call(document.querySelectorAll(SELECTOR), fit);
	}

	var pending = false;

	function schedule() {
		if (pending) {
			return;
		}

		pending = true;
		window.requestAnimationFrame(function () {
			pending = false;
			fitAll();
		});
	}

	window.tpFitIntroButtons = fitAll;

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', fitAll);
	} else {
		fitAll();
	}

	window.addEventListener('load', schedule);
	window.addEventListener('resize', schedule, { passive: true });

	if (document.fonts && document.fonts.ready && typeof document.fonts.ready.then === 'function') {
		document.fonts.ready.then(schedule);
	}

	if (document.fonts && typeof document.fonts.addEventListener === 'function') {
		document.fonts.addEventListener('loadingdone', schedule);
	}

	// In the Visual Builder the words and the styles change while one works.
	if (typeof MutationObserver !== 'undefined' && document.body && document.body.classList.contains('et-fb')) {
		new MutationObserver(function (mutations) {
			var i;
			var target;
			var element;

			for (i = 0; i < mutations.length; i++) {
				target = mutations[i].target;
				element = target.nodeType === 1 ? target : target.parentElement;

				if (element && element.closest && (element.closest(SELECTOR) || element.closest('head') || element.tagName === 'STYLE' || element.querySelector && element.querySelector(SELECTOR))) {
					schedule();

					return;
				}
			}
		}).observe(document.documentElement, { childList: true, subtree: true, characterData: true });
	}
})();
