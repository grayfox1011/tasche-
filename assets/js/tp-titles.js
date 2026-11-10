/*
 * Titles "Siamo nei caruggi..." and "Il nostro menù" on computers (from 981 px up): their size follows the
 * text written in Divi. On the site, printed before tp-layout.js; in the Visual Builder too (0.8.3), where
 * it measures again after every change of the text, so the builder shows what the site will.
 *
 * - The longest word never breaks in two: the title gets smaller with its column instead (CSS:
 *   --tp-title-fit, 1 / (longest word in font sizes x 1.033); the CSS default suits today's words).
 * - "Il nostro menù" on more than three lines gets smaller until they are three, not under 58% of its size
 *   nor under 40 px: a long title stays readable and the lists of the menu start a little lower, not a
 *   page lower (CSS: --tp-title-max).
 * - A title that wraps gets 10% more room between its lines when a later line has an accent above a
 *   capital, which would touch the line above (tp-title-accent-room; site only).
 *
 * The two values are set on the module, never on the heading: in the builder the heading is the text
 * being written, and what is added to it could be saved with it.
 */
(function () {
	'use strict';

	var TITLES = '.tp-location-title h2, .tp-menu-section .tp-menu-title h2';
	var MODULES = '.tp-location-title, .tp-menu-title';
	var WATCHED = '.tp-location-title, .tp-menu-header';
	var MARGIN = 1.033; // other browsers may draw the font a little wider
	var ACCENT_ABOVE = /[̀-̛̕̚]/;
	var MAX_LINES = 3;
	var SMALLEST = 0.58;
	var SMALLEST_PX = 40;
	// What makes a word as wide as it is.
	var FONT = ['fontFamily', 'fontSize', 'fontStyle', 'fontWeight', 'fontStretch', 'fontVariant', 'fontFeatureSettings', 'fontVariationSettings', 'fontKerning', 'letterSpacing', 'wordSpacing', 'textTransform', 'textRendering'];
	var desktop = window.matchMedia ? window.matchMedia('(min-width: 981px)') : null;
	var builder = Boolean(document.body && document.body.classList.contains('et-fb'));
	var probe = null;
	var frame = 0;

	function onReady(callback) {
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', callback);
		} else {
			callback();
		}
	}

	function moduleOf(title) {
		return title.closest(MODULES) || title.parentNode;
	}

	// The title is making its entrance (tp-split-text.js): its letters are rising into place.
	function entering(title) {
		return !title.classList.contains('is-revealed') && Boolean(title.querySelector('.tp-split-line'));
	}

	// The words as written in Divi, read also while the title waits for its font or makes its entrance
	// (innerText leaves out what is hidden): lines, <br> and blocks part them.
	function titleWords(title) {
		var walker = document.createTreeWalker(title, NodeFilter.SHOW_ELEMENT | NodeFilter.SHOW_TEXT);
		var text = '';
		var node;
		while ((node = walker.nextNode())) {
			if (node.nodeType === 3) {
				text += node.nodeValue;
			} else if (node.nodeName === 'BR' || window.getComputedStyle(node).display.indexOf('inline') !== 0) {
				text += ' ';
			}
		}
		return text.split(/\s+/).filter(Boolean);
	}

	// Measured beside the page, in the title's font, and not inside the title.
	function longestWordEm(title) {
		var words = titleWords(title);
		var style = window.getComputedStyle(title);
		var size = parseFloat(style.fontSize);
		var widest = 0;
		if (!(size > 0) || !words.length || !document.body) {
			return 0;
		}
		if (!probe) {
			probe = document.createElement('span');
			probe.setAttribute('aria-hidden', 'true');
			probe.style.cssText = 'position:absolute;left:-9999px;top:0;visibility:hidden;white-space:nowrap;pointer-events:none';
		}
		FONT.forEach(function (name) {
			probe.style[name] = style[name];
		});
		document.body.appendChild(probe);
		words.forEach(function (word) {
			probe.textContent = word;
			widest = Math.max(widest, probe.getBoundingClientRect().width);
		});
		document.body.removeChild(probe);
		return widest / size;
	}

	function accentOnLaterLine(title) {
		var style = window.getComputedStyle(title);
		var size = parseFloat(style.fontSize);
		var upper = style.textTransform === 'uppercase';
		// While the title makes its entrance its letters rise into place: the line they are in says
		// where they stand.
		var rising = entering(title);
		var walker = document.createTreeWalker(title, NodeFilter.SHOW_TEXT);
		var range = document.createRange();
		var firstTop = null;
		var node;
		while ((node = walker.nextNode())) {
			var line = rising ? node.parentNode.closest('.tp-split-line') : null;
			for (var i = 0; i < node.textContent.length; i++) {
				var ch = node.textContent.charAt(i);
				if (!/\S/.test(ch)) { continue; }
				range.setStart(node, i);
				range.setEnd(node, i + 1);
				var top = (line || range).getBoundingClientRect().top;
				if (firstTop === null) { firstTop = top; }
				if (top > firstTop + size / 2 && (upper || ch !== ch.toLowerCase()) && ACCENT_ABOVE.test(ch.normalize ? ch.normalize('NFD') : ch)) {
					return true;
				}
			}
		}
		return false;
	}

	function lineCount(title) {
		var range = document.createRange();
		range.selectNodeContents(title);
		var rects = range.getClientRects();
		var tops = [];
		for (var i = 0; i < rects.length; i++) {
			if (rects[i].width < 1 || rects[i].height < 1) { continue; }
			var known = false;
			for (var j = 0; j < tops.length; j++) {
				if (Math.abs(tops[j] - rects[i].top) < rects[i].height / 2) {
					known = true;
					break;
				}
			}
			if (!known) { tops.push(rects[i].top); }
		}
		return tops.length;
	}

	// More than three lines: the largest size that gives three, or the smallest one allowed.
	function fitLines(module, title) {
		function fontSize() {
			return parseFloat(window.getComputedStyle(title).fontSize);
		}
		function limit(size) {
			module.style.setProperty('--tp-title-max', size.toFixed(2) + 'px');
		}

		module.style.removeProperty('--tp-title-max');
		if (lineCount(title) <= MAX_LINES) {
			return;
		}
		var high = fontSize();
		var low = Math.min(high, Math.max(SMALLEST_PX, high * SMALLEST));
		if (!(high - low > 0.5)) {
			return;
		}
		limit(low);
		if (Math.abs(fontSize() - low) > 0.5) {
			// The size comes from somewhere else (set in Divi on the module): it is not ours to change.
			module.style.removeProperty('--tp-title-max');
			return;
		}
		if (lineCount(title) <= MAX_LINES) {
			for (var i = 0; i < 7; i++) {
				var middle = (low + high) / 2;
				limit(middle);
				if (lineCount(title) <= MAX_LINES) {
					low = middle;
				} else {
					high = middle;
				}
			}
		}
		limit(low);
	}

	function update() {
		frame = 0;
		if (!desktop || !desktop.matches) { return; }
		var titles = document.querySelectorAll(TITLES);
		var i;
		for (i = 0; i < titles.length; i++) {
			var em = longestWordEm(titles[i]);
			if (em > 0) {
				moduleOf(titles[i]).style.setProperty('--tp-title-fit', (1 / (em * MARGIN)).toFixed(3));
			}
		}
		// After the sizes: the lines depend on them. Not while the letters are rising: they are not on their lines.
		for (i = 0; i < titles.length; i++) {
			var module = moduleOf(titles[i]);
			if (module.classList.contains('tp-menu-title') && !entering(titles[i])) {
				fitLines(module, titles[i]);
			}
		}
		if (builder) { return; }
		for (i = 0; i < titles.length; i++) {
			titles[i].classList.toggle('tp-title-accent-room', accentOnLaterLine(titles[i]));
		}
	}

	function schedule() {
		if (!frame) { frame = window.requestAnimationFrame(update); }
	}

	// Visual Builder: the titles, the legend beside the menu's one and the styles of the page change while one
	// works. What the builder draws elsewhere (toolbars, panels) is left alone.
	function concerns(mutation) {
		var target = mutation.target;
		var el = target.nodeType === 1 ? target : target.parentElement;
		if (!el || !el.closest) { return false; }
		if (el.closest(WATCHED) || el.closest('head') || 'STYLE' === el.tagName) { return true; }
		for (var i = 0; i < mutation.addedNodes.length; i++) {
			var node = mutation.addedNodes[i];
			if (node.nodeType === 1 && node !== probe && (node.matches(WATCHED) || node.querySelector(WATCHED))) { return true; }
		}
		return false;
	}

	// tp-split-text.js is about to cut a title into lines, now that its font is loaded: the sizes first.
	document.addEventListener('tp:split-text:lines', update);

	onReady(function () {
		update();
		if (document.fonts && document.fonts.ready) { document.fonts.ready.then(update); }
		window.addEventListener('resize', schedule, { passive: true });
		if (builder) {
			if (document.fonts && document.fonts.addEventListener) { document.fonts.addEventListener('loadingdone', schedule); }
			if (typeof MutationObserver !== 'undefined') {
				new MutationObserver(function (mutations) {
					for (var i = 0; i < mutations.length; i++) {
						if (concerns(mutations[i])) {
							schedule();
							return;
						}
					}
				}).observe(document.documentElement, { childList: true, subtree: true, characterData: true });
			}
		}
	});
})();
