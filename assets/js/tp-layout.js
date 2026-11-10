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

	// Tasca o piatto (0.6.8): each card gets the height of its texts (--tp-scelta-text-h: texts longer than the
	// Figma's rise until they fit when the card opens), and with more than two cards the board and the section
	// get the height that reaches the last card (--tp-scelta-board, --tp-scelta-height). The builder does the
	// same (tp_child_vb_choice_scale_script()).
	function fitChoiceCards(phone, tablet, scale, width) {
		var rows = document.querySelector('.tp-scelta-schede');
		var root = document.documentElement;
		if (!rows) {
			return;
		}
		var section = rows.closest('.tp-scelta');
		var cards = Array.prototype.filter.call(rows.children, function (child) {
			return child.classList.contains('tp-scelta-scheda');
		});
		cards.forEach(function (card) {
			var text = card.querySelector('.tp-scelta-testo');
			if (text) {
				card.style.setProperty('--tp-scelta-text-h', text.offsetHeight + 'px');
			}
		});
		// 0.6.9: the cards start at least the title's bottom margin under its last line (--tp-scelta-push).
		var push = choiceTitlePush(section, cards, tablet ? 1 : scale);
		if (push > 0.5) {
			root.style.setProperty('--tp-scelta-push', push.toFixed(2) + 'px');
		} else {
			push = 0;
			root.style.removeProperty('--tp-scelta-push');
		}
		var extra = 0;
		if (cards.length > 2) {
			var last = 0;
			cards.forEach(function (card) {
				last = Math.max(last, card.offsetTop + card.offsetHeight);
			});
			// The Figma's two cards end with the first line of cards (on phones, one under the other, with the second).
			var ref = cards[phone ? 1 : 0];
			extra = last - (ref.offsetTop + ref.offsetHeight);
		}
		var grow = push + Math.max(0, extra);
		if (grow > 0.5) {
			var board = (phone ? 963 : (tablet ? width * 0.90531746 : 1263)) + grow;
			root.style.setProperty('--tp-scelta-board', board.toFixed(2) + 'px');
			root.style.setProperty('--tp-scelta-height', (tablet ? board : board * scale).toFixed(2) + 'px');
		} else {
			root.style.removeProperty('--tp-scelta-board');
		}
		if (section) {
			if (grow > 0.5) {
				section.setAttribute('data-tp-grown', '');
			} else {
				section.removeAttribute('data-tp-grown');
			}
		}
	}

	// How far the cards of Tasca o piatto go down so that they start the title's bottom margin under its last line,
	// in the pixels of the board (the computers and the phones draw it scaled by boardScale). The title's band and
	// the cards' band are two boards at the top of the section: the last line is measured in the first, the cards
	// in the second (their place in the layout, whatever moves them on the screen).
	function choiceTitlePush(section, cards, boardScale) {
		var title = section ? section.querySelector('.tp-scelta-titolo') : null;
		var band = title ? title.closest('.tp-scelta-testata') : null;
		if (!title || !band || !cards.length || !boardScale) {
			return 0;
		}
		var heading = title.querySelector('h1, h2, h3, h4, h5, h6, p') || title;
		var range = document.createRange();
		range.selectNodeContents(heading);
		var rects = range.getClientRects();
		var bottom = -Infinity;
		for (var i = 0; i < rects.length; i++) {
			if (rects[i].width > 0 && rects[i].height > 0) {
				bottom = Math.max(bottom, rects[i].bottom);
			}
		}
		if (!isFinite(bottom)) {
			return 0;
		}
		var y = 0;
		var el = heading;
		while (el && el !== band) {
			y += el.offsetTop;
			el = el.offsetParent;
		}
		if (el !== band) {
			return 0;
		}
		var textBottom = y + (bottom - heading.getBoundingClientRect().top) / boardScale;
		var first = Infinity;
		cards.forEach(function (card) {
			first = Math.min(first, card.offsetTop);
		});
		var current = parseFloat(document.documentElement.style.getPropertyValue('--tp-scelta-push')) || 0;
		var gap = parseFloat(window.getComputedStyle(title).marginBottom) || 0;
		return textBottom + gap - (first - current);
	}

	// Tasca/Piatto stage: 1512px wide on desktop, 390px reference width on phones. The section made of
	// Divi modules (.tp-scelta) reads the same scale and height from CSS variables on the page.
	function updateChoiceStage() {
		if (document.querySelector('.tp-scelta')) {
			var root = document.documentElement;
			var width = window.innerWidth || root.clientWidth;
			var phone = window.matchMedia('(max-width: 767px)').matches;
			var tablet = !phone && window.matchMedia('(max-width: 980px)').matches;
			var stageScale = width / (phone ? 390 : 1512);
			root.style.removeProperty(phone ? '--tp-scelta-scale' : '--tp-scelta-scale-phone');
			if (tablet) {
				root.style.removeProperty('--tp-scelta-scale');
				root.style.removeProperty('--tp-scelta-height');
			} else {
				root.style.setProperty(phone ? '--tp-scelta-scale-phone' : '--tp-scelta-scale', stageScale.toFixed(6));
				root.style.setProperty('--tp-scelta-height', ((phone ? 963 : 1263) * stageScale).toFixed(2) + 'px');
			}
			fitChoiceCards(phone, tablet, stageScale, width);
		}
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
	// Figma's width (357.7px, frame "mobilw") and zoomed to narrower phones so the prices keep their layout.
	function updateFooterScale() {
		var w = window.innerWidth || document.documentElement.clientWidth || 0;
		var r = document.documentElement;
		if (w >= 981 && w <= 1511) {
			r.style.setProperty('--tp-fit-scale', (w / 1512).toFixed(6));
		} else {
			r.style.removeProperty('--tp-fit-scale');
		}
		if (w <= 767) {
			r.style.setProperty('--tp-menu-zoom', Math.min(1, (w - 32) / 357.7).toFixed(4));
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

	// The texts of the Tasca o piatto cards take their size with the fonts and the photos: measured again then.
	onReady(function () {
		var rows = document.querySelector('.tp-scelta-schede');
		if (!rows) {
			return;
		}
		if (document.fonts && document.fonts.ready) {
			document.fonts.ready.then(updateChoiceStage);
		}
		if (typeof ResizeObserver !== 'undefined') {
			var pending = false;
			var observer = new ResizeObserver(function () {
				if (pending) {
					return;
				}
				pending = true;
				window.requestAnimationFrame(function () {
					pending = false;
					updateChoiceStage();
				});
			});
			rows.querySelectorAll('.tp-scelta-testo').forEach(function (text) {
				observer.observe(text);
			});
		}
	});

	/* Hero ------------------------------------------------------------------ */

	// Divi's background video uses MediaElement.js, which on iOS Safari resizes the
	// <video> and breaks inline playback: below 981px a plain muted, looping,
	// inline video is injected instead. The poster stays as fallback when autoplay
	// is blocked. Desktop keeps Divi's video.
	//
	// 0.8.3, after a test on an iPhone where the video did not start by itself:
	// - A phone can refuse to start a video by itself (an iPhone in Low Power Mode is the common case: WebKit
	//   rejects play() with NotAllowedError there, bug 216887): the video starts only inside a gesture of the
	//   person. The retry was on `touchstart`, which is not a gesture those browsers accept, and happened once:
	//   the video never started, not even after a tap. It is now retried on every touchend, click and key until
	//   the video runs.
	// - The video is started again when the page comes back (another app, the lock screen, the back button) and
	//   when the hero comes back into view, if the phone stopped it.
	// - On Apple's browsers the MP4 comes first: every iPhone decodes H.264 in hardware, while WebM (VP9) reached
	//   iOS only in its recent versions. Elsewhere WebM stays first, as before. A file the phone cannot decode:
	//   the other one.
	// - ?tp-video=1 in the address shows the state of the video in a corner of the page, to read a phone one
	//   cannot hold from a screenshot.
	(function () {
		var MQ = '(max-width: 980px)';
		var GESTURES = ['touchend', 'click', 'keydown'];
		var injected = false;

		// Starts the video and keeps it running. `state` is what the corner note shows.
		function keepPlaying(v, media, order, state) {
			var listening = false;
			var inView = true;

			function attempt() {
				if (!v.paused || document.hidden || !inView) { return; }
				var started;
				try {
					started = v.play();
				} catch (error) {
					state.refused = (error && error.name) || 'error';
				}
				if (started && started.then) {
					started.then(function () { state.refused = ''; }, function (error) { state.refused = (error && error.name) || 'refused'; });
				}
			}
			function listen() {
				if (listening) { return; }
				listening = true;
				for (var i = 0; i < GESTURES.length; i++) { document.addEventListener(GESTURES[i], attempt, { capture: true, passive: true }); }
			}
			function unlisten() {
				if (!listening) { return; }
				listening = false;
				for (var i = 0; i < GESTURES.length; i++) { document.removeEventListener(GESTURES[i], attempt, { capture: true }); }
			}
			// The file in use cannot be played (a decoder the phone lacks, a broken download): the other format, once.
			function otherFormat() {
				if (state.switched) { return; }
				var current = v.currentSrc || v.getAttribute('src') || '';
				for (var i = 0; i < order.length; i++) {
					var url = media[order[i]];
					if (url && current.indexOf(url) === -1) {
						state.switched = order[i];
						while (v.firstChild) { v.removeChild(v.firstChild); }
						v.setAttribute('src', url);
						v.load();
						attempt();
						return;
					}
				}
			}

			v.addEventListener('playing', unlisten);
			v.addEventListener('pause', listen);
			v.addEventListener('loadeddata', attempt);
			v.addEventListener('canplay', attempt);
			v.addEventListener('error', otherFormat);
			document.addEventListener('visibilitychange', attempt);
			window.addEventListener('pageshow', attempt);
			if (window.IntersectionObserver) {
				new window.IntersectionObserver(function (entries) {
					inView = entries[entries.length - 1].isIntersecting;
					attempt();
				}).observe(v);
			}
			listen();
			attempt();
		}

		// ?tp-video=1: the state of the video, twice a second, in the bottom left corner.
		function showState(v, state) {
			var box = document.createElement('div');
			box.setAttribute('style', 'position:fixed;left:8px;bottom:8px;z-index:2147483647;max-width:calc(100vw - 16px);padding:8px 10px;border-radius:6px;background:rgba(0,0,0,.82);color:#fff;font:12px/1.45 Menlo,Consolas,monospace;white-space:pre-wrap;word-break:break-word;pointer-events:none');
			document.body.appendChild(box);
			var nav = window.navigator || {};
			var system = (/(?:iPhone OS|CPU OS|Android) [\d_.]+/.exec(nav.userAgent || '') || ['?'])[0].replace(/_/g, '.') + ((/Version\/[\d.]+/.exec(nav.userAgent || '') || [''])[0] ? ' ' + /Version\/[\d.]+/.exec(nav.userAgent)[0] : '');
			var can = function (type) { return v.canPlayType(type) || 'no'; };
			window.setInterval(function () {
				box.textContent = [
					'tp-video ' + system,
					'file: ' + ((v.currentSrc || '').split('/').pop() || '-') + (state.switched ? ' (cambiato in ' + state.switched + ')' : ''),
					'stato: ' + (v.paused ? 'fermo' : 'in riproduzione') + ' ' + v.currentTime.toFixed(1) + 's, dati ' + v.readyState + '/4, rete ' + v.networkState,
					'avvio: ' + (!v.paused ? 'ok' : (state.refused ? 'rifiutato dal telefono (' + state.refused + ')' : 'in attesa')),
					'errore: ' + (v.error ? v.error.code + ' ' + (v.error.message || '') : 'nessuno'),
					'mp4 ' + can('video/mp4; codecs="avc1.640032"') + ', webm ' + can('video/webm; codecs="vp9"') + ', ordine ' + state.order
				].join('\n');
			}, 500);
		}

		function inject() {
			if (injected) { return; }
			if (!window.matchMedia || !window.matchMedia(MQ).matches) { return; }
			var hero = document.querySelector('.tp-hero-section');
			if (!hero) { return; }
			injected = true;
			// The video chosen in Divi for this breakpoint: Divi prints one span per
			// breakpoint with its own value (an empty one when the video is removed
			// there), and none at all when the hero has no video. The theme data only
			// supplies the poster.
			var media = window.tpHeroVideo || {};
			var own = function (suffix) {
				for (var i = 0; i < hero.children.length; i++) {
					var el = hero.children[i];
					if (!el.classList || !el.classList.contains('et-pb-background-video')) { continue; }
					var variant = /(?:^|\s)et-pb-background-video_/.test(el.className);
					if (suffix ? el.classList.contains('et-pb-background-video' + suffix) && !/__/.test(el.className) : !variant) { return el; }
				}
				return null;
			};
			var span = (window.matchMedia('(max-width: 767px)').matches && own('_phone')) || own('_tablet') || own('');
			if (!span) { return; }
			var pick = function (type) {
				var s = span.querySelector('source[type="video/' + type + '"]');
				return s ? (s.getAttribute('data-src') || s.getAttribute('src') || '') : '';
			};
			media = { mp4: pick('mp4'), webm: pick('webm'), poster: media.poster };
			if (!media.mp4 && !media.webm) { return; }
			var apple = /Apple/.test((window.navigator && window.navigator.vendor) || '');
			var order = apple ? ['mp4', 'webm'] : ['webm', 'mp4'];
			var state = { refused: '', switched: '', order: order.join('>') };
			var v = document.createElement('video');
			v.className = 'tp-hero-native-video';
			v.muted = true; v.defaultMuted = true;
			v.autoplay = true; v.loop = true;
			v.setAttribute('muted', '');
			v.setAttribute('autoplay', '');
			v.setAttribute('loop', '');
			v.setAttribute('playsinline', '');
			v.setAttribute('webkit-playsinline', '');
			v.setAttribute('disablepictureinpicture', '');
			v.setAttribute('disableremoteplayback', '');
			v.setAttribute('x-webkit-airplay', 'deny');
			v.setAttribute('aria-hidden', 'true');
			v.setAttribute('preload', 'auto');
			if (media.poster) { v.setAttribute('poster', media.poster); }
			for (var i = 0; i < order.length; i++) {
				if (!media[order[i]]) { continue; }
				var source = document.createElement('source');
				source.src = media[order[i]];
				source.type = 'video/' + order[i];
				v.appendChild(source);
			}
			hero.insertBefore(v, hero.firstChild);
			keepPlaying(v, media, order, state);
			if (/[?&]tp-video=1(?:&|$)/.test(window.location.search)) { showState(v, state); }
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

	// Allergens: since 0.5.3 each dish has a Text module of its own with the class
	// tp-menu-allergeni next to its name, set in Divi as any text: it goes at the end of the
	// name, where the superscript was. Without it, the numbers typed in the dish title between
	// square brackets, e.g. "MELANZANA ALLA PARMIGIANA [7,8]", are removed from the title and
	// shown as a superscript; a dish without either shows no numbers (0.7.0: the dishes of
	// taschepiene.it have none, and the map of the Figma's numbers this script used is gone).
	// The Visual Builder shows the titles as typed.
	(function () {
		// The dish's Allergeni module: already at the end of the name, or among the modules next to it.
		function allergensModule(h) {
			var own = h.querySelector('.tp-menu-allergeni');
			var name = h.closest('.tp-menu-name');
			for (var c = name && !own ? name.parentNode.firstElementChild : null; c; c = c.nextElementSibling) {
				if (c.classList.contains('tp-menu-allergeni')) { own = c; break; }
			}
			return own;
		}
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
				var own = allergensModule(h);
				var sup = h.querySelector('.tp-allergeni');
				if (own) {
					if (own.parentNode !== h) { h.appendChild(own); }
					if (sup) { sup.parentNode.removeChild(sup); }
				} else if (nums) {
					if (!sup) {
						sup = document.createElement('sup');
						sup.className = 'tp-allergeni';
						h.appendChild(sup);
					}
					sup.textContent = nums;
				} else if (sup) {
					sup.parentNode.removeChild(sup);
				}
			}
		}
		window.tpMenuAllergeni = apply; // callable for tests and refreshes
		onReady(apply);
	})();
})();
