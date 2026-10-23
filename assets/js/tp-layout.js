/*
 * Page layout helpers. Front end only, printed by tp_child_print_layout_script()
 * where the former inline scripts were (before jQuery and Divi's scripts), so
 * each part still runs on DOMContentLoaded ahead of them.
 * - Scales: the fixed 1512px Tasca/Piatto stage and footer scene, the mobile menu card.
 * - Hero: native inline video on tablets and phones.
 * - Alberto section: mobile order of photos and text.
 * - Menu: allergen superscripts and short mobile names.
 */
(function () {
	'use strict';

	function onReady(callback) {
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', callback);
		} else {
			callback();
		}
	}

	/* Scales ---------------------------------------------------------------- */

	// Tasca/Piatto stage: 1512px wide on desktop, 390px reference width on phones.
	function updateChoiceStage() {
		document.querySelectorAll('.tp-choice-standalone').forEach(function (section) {
			var viewportWidth = window.innerWidth || document.documentElement.clientWidth || section.clientWidth;
			if (window.matchMedia('(max-width: 767px)').matches) {
				var mobileScale = viewportWidth / 390;
				section.style.removeProperty('--choice-scale');
				section.style.setProperty('--choice-mobile-scale', mobileScale.toFixed(6));
				var mobileHeight = (963 * mobileScale).toFixed(2) + 'px';
				section.style.setProperty('height', mobileHeight, 'important');
				section.style.setProperty('min-height', mobileHeight, 'important');
				return;
			}
			if (window.matchMedia('(max-width: 980px)').matches) {
				section.style.removeProperty('--choice-scale');
				section.style.removeProperty('--choice-mobile-scale');
				section.style.removeProperty('height');
				section.style.removeProperty('min-height');
				return;
			}
			section.style.removeProperty('--choice-mobile-scale');
			var scale = viewportWidth / 1512;
			section.style.setProperty('--choice-scale', scale.toFixed(6));
			var scaledHeight = (1263 * scale).toFixed(2) + 'px';
			section.style.setProperty('height', scaledHeight, 'important');
			section.style.setProperty('min-height', scaledHeight, 'important');
		});
	}

	// Footer scene (1512x982) between 981 and 1511px; menu main card drawn at the
	// iPhone 16 width (346px) and zoomed to every phone so the prices keep their layout.
	function updateFooterScale() {
		var w = window.innerWidth || document.documentElement.clientWidth || 0;
		var r = document.documentElement;
		if (w >= 981 && w <= 1511) {
			r.style.setProperty('--tp-fit-scale', (w / 1512).toFixed(6));
		} else {
			r.style.removeProperty('--tp-fit-scale');
		}
		if (w <= 767) {
			r.style.setProperty('--tp-menu-zoom', Math.min(1, (w - 32) / 346).toFixed(4));
		} else {
			r.style.removeProperty('--tp-menu-zoom');
		}
	}

	function updateScales() {
		updateChoiceStage();
		updateFooterScale();
	}

	onReady(updateScales);
	window.addEventListener('resize', updateScales, { passive: true });

	/* Hero ------------------------------------------------------------------ */

	// Divi's background video uses MediaElement.js, which on iOS Safari resizes the
	// <video> and breaks inline playback: below 981px a plain muted, looping,
	// inline video is injected instead. The poster stays as fallback when autoplay
	// is blocked. Desktop keeps Divi's video.
	(function () {
		var MQ = '(max-width: 980px)';
		var injected = false;
		function inject() {
			if (injected) { return; }
			if (!window.matchMedia || !window.matchMedia(MQ).matches) { return; }
			var hero = document.querySelector('.tp-hero-section');
			if (!hero) { return; }
			injected = true;
			var media = window.tpHeroVideo || {};
			if (!media.mp4 && !media.webm) { return; }
			var v = document.createElement('video');
			v.className = 'tp-hero-native-video';
			v.muted = true; v.defaultMuted = true;
			v.autoplay = true; v.loop = true;
			v.setAttribute('muted', '');
			v.setAttribute('autoplay', '');
			v.setAttribute('loop', '');
			v.setAttribute('playsinline', '');
			v.setAttribute('webkit-playsinline', '');
			v.setAttribute('preload', 'auto');
			v.setAttribute('poster', media.poster);
			var sWebm = document.createElement('source');
			sWebm.src = media.webm; sWebm.type = 'video/webm';
			var sMp4 = document.createElement('source');
			sMp4.src = media.mp4; sMp4.type = 'video/mp4';
			if (media.webm) { v.appendChild(sWebm); }
			if (media.mp4) { v.appendChild(sMp4); }
			hero.insertBefore(v, hero.firstChild);
			var tryPlay = function () { var p = v.play(); if (p && p.catch) { p.catch(function () {}); } };
			tryPlay();
			v.addEventListener('canplay', tryPlay, { once: true });
			document.addEventListener('touchstart', tryPlay, { once: true, passive: true });
		}
		onReady(inject);
	})();

	/* Alberto section ------------------------------------------------------- */

	// Desktop: text column next to a two-photo collage. Mobile (Figma): title, then
	// each text block followed by one photo, remaining photos at the end. CSS cannot
	// put a photo between two paragraphs of the same module, so the mobile flow is
	// built from CLONES: the originals stay untouched and editable in Divi. The CSS
	// shows the flow only up to 767px.
	(function () {
		function stripIds(node) {
			if (node.removeAttribute) { node.removeAttribute('id'); }
			var withId = node.querySelectorAll ? node.querySelectorAll('[id]') : [];
			for (var i = 0; i < withId.length; i++) { withId[i].removeAttribute('id'); }
			return node;
		}
		// Clones added at runtime are not seen by lazy loaders: load their images eagerly.
		function clonePhoto(node) {
			var c = stripIds(node.cloneNode(true));
			var imgs = c.tagName === 'IMG' ? [c] : (c.querySelectorAll ? c.querySelectorAll('img') : []);
			for (var i = 0; i < imgs.length; i++) {
				var im = imgs[i];
				im.setAttribute('loading', 'eager');
				im.removeAttribute('data-lazy-src');
				if (im.getAttribute('data-src')) { im.setAttribute('src', im.getAttribute('data-src')); }
				if (im.getAttribute('data-srcset')) { im.setAttribute('srcset', im.getAttribute('data-srcset')); }
				im.classList && im.classList.remove('lazyload', 'lazy', 'et_pb_lazyload');
			}
			return c;
		}
		function build() {
			var sec = document.querySelector('.tp-alberto-section');
			if (!sec || sec.getAttribute('data-tp-alberto-flow') === '1') { return; }
			var row = sec.querySelector('.tp-alberto-row');
			var title = sec.querySelector('.tp-alberto-title');
			var copy = sec.querySelector('.tp-alberto-copy');
			var photoCol = sec.querySelector('.tp-alberto-photos-column');
			if (!row || !copy || !photoCol) { return; }
			var inner = copy.querySelector('.et_pb_text_inner') || copy;
			var paras = [].slice.call(inner.children).filter(function (n) { return n.tagName === 'P'; });
			var photos = [].slice.call(photoCol.children).filter(function (n) { return n.nodeType === 1; });
			if (!paras.length || !photos.length) { return; }
			var copyCls = copy.getAttribute('class') || 'tp-alberto-copy';
			function makeCopy(list) {
				if (!list.length) { return null; }
				var d = document.createElement('div'); d.setAttribute('class', copyCls);
				var di = document.createElement('div'); di.className = 'et_pb_text_inner';
				list.forEach(function (p) { di.appendChild(stripIds(p.cloneNode(true))); });
				d.appendChild(di);
				return d;
			}
			var g1 = makeCopy(paras.slice(0, 1));        // first text block: first paragraph
			var g2 = makeCopy(paras.slice(1));           // second text block: the rest
			// Title: a plain H2 with the text only. Cloning the SplitText markup would bring
			// absolutely positioned pieces that overlap the photo without their animation.
			var titleText = title ? (title.textContent || '').replace(/\s+/g, ' ').trim() : '';
			var titleEl = null;
			if (titleText) {
				titleEl = document.createElement('div');
				titleEl.className = 'tp-alberto-title tp-alberto-title--mobile';
				var h2 = document.createElement('h2');
				h2.textContent = titleText;
				titleEl.appendChild(h2);
			}
			var wrap = document.createElement('div');
			wrap.className = 'tp-alberto-mobile-flow';
			if (titleEl) { wrap.appendChild(titleEl); }
			var groups = [g1, g2].filter(Boolean);
			var pi = 0;
			groups.forEach(function (g) {
				wrap.appendChild(g);
				if (pi < photos.length) {
					wrap.appendChild(clonePhoto(photos[pi++]));
				}
			});
			while (pi < photos.length) {
				wrap.appendChild(clonePhoto(photos[pi++]));
			}
			row.appendChild(wrap);
			sec.setAttribute('data-tp-alberto-flow', '1');
		}
		onReady(build);
	})();

	/* Menu ------------------------------------------------------------------ */

	// Allergens are typed in the Divi dish title between square brackets, e.g.
	// "MELANZANA ALLA PARMIGIANA [7,8]": the brackets are removed and the numbers
	// shown as a superscript. Titles without brackets use the map below (Figma 10:314).
	// The short mobile name is the part of the title before " - " (data-tp-short,
	// read by the CSS). The Visual Builder shows the titles as typed.
	(function () {
		var MAP = [
			[/MELANZAN/i, '7,8'],
			[/RIPIENI LIGURI/i, '1,3,7,12'],
			[/POLPETTE/i, '1,3,7'],
			[/POLLO ALLA LIGURE/i, '8,12'],
			[/TRIPPA/i, '8,9,12'],
			[/STOCCAFISSO/i, '4,8,9,12'],
			[/CAPRESE/i, '7,8'],
			[/ACCIUGHE/i, '4,7']
		];
		function apply() {
			var heads = document.querySelectorAll('.tp-menu-section .tp-menu-name h4, .tp-menu-section h4.et_pb_module_header');
			for (var i = 0; i < heads.length; i++) {
				var h = heads[i];
				var nums = null;
				// Numbers from the Divi title: a "[7,8]" suffix wins over the map.
				for (var n = 0; n < h.childNodes.length; n++) {
					var node = h.childNodes[n];
					if (node.nodeType === 3 && /\[([0-9,\s]+)\]/.test(node.textContent)) {
						nums = node.textContent.match(/\[([0-9,\s]+)\]/)[1].replace(/\s+/g, '');
						node.textContent = node.textContent.replace(/\s*\[[0-9,\s]+\]\s*/, ' ').replace(/\s+$/, '');
					}
				}
				var t = h.textContent || '';
				if (nums === null) {
					for (var m = 0; m < MAP.length; m++) {
						if (MAP[m][0].test(t)) { nums = MAP[m][1]; break; }
					}
				}
				var sup = h.querySelector('.tp-allergeni');
				if (nums) {
					if (!sup) {
						sup = document.createElement('sup');
						sup.className = 'tp-allergeni';
						h.appendChild(sup);
					}
					sup.textContent = nums;
				} else if (sup) {
					sup.parentNode.removeChild(sup);
				}
				if (t.indexOf(' - ') > 0) {
					h.setAttribute('data-tp-short', t.split(' - ')[0].replace(/\s+$/, ''));
				} else {
					h.removeAttribute('data-tp-short');
				}
			}
		}
		window.tpMenuAllergeni = apply; // callable for tests and refreshes
		onReady(apply);
	})();
})();
