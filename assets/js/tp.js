/*
 * Tasche Piene: the effects Divi has no option for. Each one is tied to a class written in a module's
 * Advanced > Attributes; without this file the page is the same, only still.
 * Not loaded in the Visual Builder, where one writes.
 *
 *   tp-scorre            the words of a Text module run across the screen
 *   tp-lettere           a title comes in letter by letter (GSAP, assets/vendor)
 *   tp-video-telefono    the background video of a section also runs on phones and tablets
 *   tp-testata           the header: its height is told to the page, its menu closes after a tap on a voice,
 *                        a page opened at an anchor (…/#menu) shows the piece under the header
 *   tp-apertura          the section that opens the page: the logo on a full screen, then the page (GSAP)
 *   tp-scheda            a card whose photo fills it with the mouse over, while its hidden text comes up
 *   tp-resta             a piece that stays in view on computers while its section goes by
 *   tp-carte             a group whose cards come in one by one, a little turned (GSAP)
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

	function each(selector, callback) {
		Array.prototype.forEach.call(document.querySelectorAll(selector), callback);
	}

	/* Words that run ---------------------------------------------------------------------------- */

	// The text is written once in Divi. Here it is repeated as many times as the screen needs and moved
	// left by the width of one copy, so the loop has no seam. Between the copies goes the mark that
	// already parts the phrases in the text (· • | / – —), if there is one.
	function startRunning(module) {
		var inner = module.querySelector('.et_pb_text_inner') || module;
		var first = inner.querySelector('.tp-scorre__pezzo');
		var ribbon;

		if (!first) {
			first = document.createElement('div');
			first.className = 'tp-scorre__pezzo';
			while (inner.firstChild) { first.appendChild(inner.firstChild); }
			var mark = (first.textContent.match(/\s([·•|\/–—])\s/) || [])[1];
			var gap = document.createElement('span');
			gap.setAttribute('aria-hidden', 'true');
			gap.textContent = mark ? ' ' + mark + ' ' : '   ';
			first.appendChild(gap);
			ribbon = document.createElement('div');
			ribbon.className = 'tp-scorre__nastro';
			ribbon.appendChild(first);
			inner.appendChild(ribbon);
		} else {
			ribbon = first.parentNode;
			while (ribbon.children.length > 1) { ribbon.removeChild(ribbon.lastChild); }
		}

		module.classList.add('tp-scorre--attiva');
		var step = first.getBoundingClientRect().width;
		if (!(step > 0)) { return; }
		var copies = Math.ceil(module.getBoundingClientRect().width / step) + 1;
		for (var i = 0; i < copies; i++) {
			var copy = first.cloneNode(true);
			copy.setAttribute('aria-hidden', 'true');
			ribbon.appendChild(copy);
		}
		// How fast: data-durata="10" is the seconds the whole text takes to go by once, whatever its length (so
		// it is slower where the letters are smaller, as in version 0.8.3); or data-velocita="70", pixels a
		// second; 70 pixels a second when neither is there.
		var seconds = parseFloat(module.getAttribute('data-durata'));
		var speed = parseFloat(module.getAttribute('data-velocita')) || 70;
		module.style.setProperty('--tp-scorre-passo', step + 'px');
		module.style.setProperty('--tp-scorre-tempo', (seconds > 0 ? Math.max(1, seconds) : Math.max(4, step / speed)) + 's');
	}

	function running() {
		each('.tp-scorre', startRunning);
	}

	/* Background video on phones and tablets ------------------------------------------------------ */

	// Divi plays a section's background video through MediaElement.js, which on iPhones resizes the
	// <video> and stops it from playing in the page. Up to 980 px a plain muted, looping, inline video
	// takes its place, with the files chosen in Divi. A phone may still refuse to start it by itself
	// (an iPhone in Low Power Mode): it is tried again at every tap until it runs. The section's
	// background image stays under it.
	function nativeVideo(section) {
		if (!window.matchMedia || !window.matchMedia('(max-width: 980px)').matches) { return; }
		if (section.querySelector(':scope > .tp-video-nativo')) { return; }

		var own = function (suffix) {
			for (var i = 0; i < section.children.length; i++) {
				var el = section.children[i];
				if (!el.classList || !el.classList.contains('et-pb-background-video')) { continue; }
				var variant = /(?:^|\s)et-pb-background-video_/.test(el.className);
				if (suffix ? el.classList.contains('et-pb-background-video' + suffix) && !/__/.test(el.className) : !variant) { return el; }
			}
			return null;
		};
		var span = (window.matchMedia('(max-width: 767px)').matches && own('_phone')) || own('_tablet') || own('');
		if (!span) { return; }
		var pick = function (type) {
			var source = span.querySelector('source[type="video/' + type + '"]');
			return source ? (source.getAttribute('data-src') || source.getAttribute('src') || '') : '';
		};
		var media = { mp4: pick('mp4'), webm: pick('webm') };
		if (!media.mp4 && !media.webm) { return; }

		// Every iPhone decodes MP4 (H.264) in hardware; WebM reached iOS only lately.
		var apple = /Apple/.test((window.navigator && window.navigator.vendor) || '');
		var order = apple ? ['mp4', 'webm'] : ['webm', 'mp4'];
		var video = document.createElement('video');
		video.className = 'tp-video-nativo';
		video.muted = true;
		video.defaultMuted = true;
		video.autoplay = true;
		video.loop = true;
		['muted', 'autoplay', 'loop', 'playsinline', 'webkit-playsinline', 'disablepictureinpicture', 'disableremoteplayback'].forEach(function (name) {
			video.setAttribute(name, '');
		});
		video.setAttribute('x-webkit-airplay', 'deny');
		video.setAttribute('aria-hidden', 'true');
		video.setAttribute('preload', 'auto');
		order.forEach(function (type) {
			if (!media[type]) { return; }
			var source = document.createElement('source');
			source.src = media[type];
			source.type = 'video/' + type;
			video.appendChild(source);
		});
		section.insertBefore(video, section.firstChild);
		section.classList.add('tp-video-telefono--attivo');
		keepPlaying(video, media, order);
	}

	function keepPlaying(video, media, order) {
		var gestures = ['touchend', 'click', 'keydown'];
		var listening = false;
		var inView = true;
		var switched = false;

		function attempt() {
			if (!video.paused || document.hidden || !inView) { return; }
			try {
				var started = video.play();
				if (started && started.catch) { started.catch(function () {}); }
			} catch (error) { /* tried again at the next gesture */ }
		}
		function listen() {
			if (listening) { return; }
			listening = true;
			gestures.forEach(function (name) { document.addEventListener(name, attempt, { capture: true, passive: true }); });
		}
		function unlisten() {
			if (!listening) { return; }
			listening = false;
			gestures.forEach(function (name) { document.removeEventListener(name, attempt, { capture: true }); });
		}
		// The file in use cannot be played: the other format, once.
		function otherFormat() {
			if (switched) { return; }
			var current = video.currentSrc || video.getAttribute('src') || '';
			for (var i = 0; i < order.length; i++) {
				var url = media[order[i]];
				if (url && current.indexOf(url) === -1) {
					switched = true;
					while (video.firstChild) { video.removeChild(video.firstChild); }
					video.setAttribute('src', url);
					video.load();
					attempt();
					return;
				}
			}
		}

		video.addEventListener('playing', unlisten);
		video.addEventListener('pause', listen);
		video.addEventListener('loadeddata', attempt);
		video.addEventListener('canplay', attempt);
		video.addEventListener('error', otherFormat);
		document.addEventListener('visibilitychange', attempt);
		window.addEventListener('pageshow', attempt);
		if (window.IntersectionObserver) {
			new window.IntersectionObserver(function (entries) {
				inView = entries[entries.length - 1].isIntersecting;
				attempt();
			}).observe(video);
		}
		listen();
		attempt();
	}

	function videos() {
		each('.tp-video-telefono', nativeVideo);
	}

	/* Titles that come in letter by letter ---------------------------------------------------------- */

	// A title with the class tp-lettere (a Heading, or a Text with a title in it) rises into place one
	// letter after the other, the first time it is seen. GSAP's SplitText cuts it into lines and letters
	// for the time of the movement, then the title is put back exactly as Divi printed it.
	// Until it starts the title is hidden by assets/css/tp.css, which shows it anyway after four seconds.
	function letters() {
		var modules = document.querySelectorAll('.tp-lettere');
		var still = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
		if (!modules.length) { return; }
		if (still || !window.gsap || !window.SplitText) {
			each('.tp-lettere', function (module) { module.style.visibility = 'visible'; });
			return;
		}
		window.gsap.registerPlugin(window.SplitText);

		function play(module) {
			if (module.tpLettere) { return; }
			module.tpLettere = true;
			var title = module.querySelector('h1, h2, h3, h4, h5, h6') || module.querySelector('.et_pb_text_inner') || module;
			var split;
			try {
				split = window.SplitText.create(title, { type: 'lines,words,chars', mask: 'lines' });
			} catch (error) {
				module.style.visibility = 'visible';
				return;
			}
			module.style.visibility = 'visible';
			window.gsap.from(split.chars, {
				yPercent: 110,
				duration: 0.58,
				stagger: 0.014,
				delay: 0.12,
				ease: 'power3.out',
				onComplete: function () { split.revert(); }
			});
		}

		if (!window.IntersectionObserver) {
			each('.tp-lettere', play);
			return;
		}
		var observer = new window.IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (entry.isIntersecting) {
					observer.unobserve(entry.target);
					play(entry.target);
				}
			});
		}, { threshold: 0.2 });
		each('.tp-lettere', function (module) { observer.observe(module); });
	}

	/* Header ------------------------------------------------------------------------------------------ */

	// The header (class tp-testata) lies over the top of the page. Its height is told to the page, so the
	// pages that do not start with a picture under it begin below it (assets/css/tp.css). On tablets and
	// phones its voices open with the sandwich, by Divi's own Interactions: a tap on a voice closes them.
	function header() {
		var bar = document.querySelector('.tp-testata');
		if (!bar) { return; }
		document.documentElement.classList.add('tp-ha-testata');
		var measure = function () {
			var row = bar.querySelector('.et_pb_row') || bar;
			var burger = bar.querySelector('.tp-panino');
			var open = burger && burger.offsetParent !== null && row.querySelector('.et_pb_group') && row.querySelector('.et_pb_group').offsetParent !== null;
			if (open) { return; } // the open menu is not the height of the header
			document.documentElement.style.setProperty('--tp-testata', Math.round(bar.getBoundingClientRect().height) + 'px');
		};
		measure();
		window.addEventListener('resize', measure, { passive: true });
		window.addEventListener('load', measure);

		// A page opened at one of its pieces (…/#menu, from a link on another page): Divi brings the piece to
		// the very top of the screen, behind the header, because it measures the header only a moment later.
		// Put it under the header, where the page's scroll-padding says (assets/css/tp.css). With Divi's
		// "smooth scrolling" on, Divi scrolls later, when it knows the header's height: nothing to do then.
		var underHeader = function () {
			if (!window.location.hash || document.body.classList.contains('et_smooth_scroll')) { return; }
			var target = null;
			try { target = document.getElementById(decodeURIComponent(window.location.hash.slice(1))); } catch (error) { target = null; }
			if (target && Math.abs(target.getBoundingClientRect().top) < 2) { target.scrollIntoView(); }
		};
		if (document.readyState === 'complete') {
			window.setTimeout(underHeader, 60);
		} else {
			window.addEventListener('load', function () { window.setTimeout(underHeader, 60); });
		}

		bar.addEventListener('click', function (event) {
			var voice = event.target.closest ? event.target.closest('.et_pb_group a, .et_pb_group .et_pb_link') : null;
			var burger = bar.querySelector('.tp-panino');
			if (!voice || !burger || burger.offsetParent === null) { return; }
			var target = burger.querySelector('.et-pb-icon') || burger;
			window.setTimeout(function () { target.click(); }, 60);
		}, true); // before Divi's own handling of the link, which keeps the click to itself
		// The sandwich is a button for the keyboard too.
		bar.addEventListener('keydown', function (event) {
			var burger = event.target.closest ? event.target.closest('.tp-panino') : null;
			if (burger && (event.key === 'Enter' || event.key === ' ')) {
				event.preventDefault();
				(burger.querySelector('.et-pb-icon') || burger).click();
			}
		});
	}

	/* Opening ------------------------------------------------------------------------------------- */

	// The section with the class tp-apertura covers the page when it loads (assets/css/tp.css): its logo comes
	// up in two lines, as the two words of the name, then the section lifts and the page is there. The times
	// are those of version 0.8.3. What must wait for the page to be seen (the title that comes in letter by
	// letter) asks afterOpening().
	var lifted = true;
	var waiting = [];

	function afterOpening(callback) {
		if (lifted) { callback(); } else { waiting.push(callback); }
	}

	function lift() {
		if (lifted) { return; }
		lifted = true;
		waiting.splice(0).forEach(function (callback) { callback(); });
	}

	function opening() {
		var root = document.documentElement;
		var box = document.querySelector('.tp-apertura');
		var gsap = window.gsap;
		var hold = function (event) { event.preventDefault(); };
		var finished = false;
		var finish = function () {
			if (finished) { return; }
			finished = true;
			window.removeEventListener('wheel', hold);
			window.removeEventListener('touchmove', hold);
			root.classList.remove('tp-apre');
			root.classList.add('tp-scopre', 'tp-aperto');
			lift();
		};
		if (!box || !gsap || !root.classList.contains('tp-anima')) { finish(); return; }

		lifted = false;
		root.classList.add('tp-apre');
		box.setAttribute('aria-hidden', 'true');
		window.addEventListener('wheel', hold, { passive: false });
		window.addEventListener('touchmove', hold, { passive: false });
		window.setTimeout(finish, 5200); // whatever happens, the page shows

		var image = box.querySelector('.et_pb_image img');
		var started = false;
		var play = function () {
			if (started || finished) { return; }
			started = true;
			var logo = null;
			var pieces = [];
			var rect = image ? image.getBoundingClientRect() : null;
			if (rect && rect.width > 0 && rect.height > 0) {
				logo = document.createElement('div');
				logo.className = 'tp-apertura__logo';
				logo.style.width = rect.width + 'px';
				logo.style.height = rect.height + 'px';
				logo.style.left = ((window.innerWidth - rect.width) / 2) + 'px';
				logo.style.top = ((window.innerHeight - rect.height) / 2) + 'px';
				['su', 'giu'].forEach(function (name) {
					var line = document.createElement('div');
					var piece = document.createElement('div');
					line.className = 'tp-apertura__riga tp-apertura__riga--' + name;
					piece.className = 'tp-apertura__pezzo';
					piece.style.backgroundImage = 'url("' + (image.currentSrc || image.src) + '")';
					line.appendChild(piece);
					logo.appendChild(line);
					pieces.push(piece);
				});
				box.appendChild(logo);
				gsap.set(pieces, { yPercent: 112 });
			}
			var timeline = gsap.timeline({ defaults: { ease: 'power2.out' }, onComplete: finish });
			if (logo) {
				timeline.to(pieces[0], { yPercent: 0, duration: 0.62 }, 0.14).to(pieces[1], { yPercent: 0, duration: 0.62 }, 0.20);
			}
			timeline.to({}, { duration: 1.18 }, 0.82)
				.add(function () { root.classList.add('tp-scopre'); }, 2.0) // the header comes back under the lifting screen
				.to(box, { yPercent: -112, duration: 1, ease: 'power2.inOut' }, 2.0);
			if (logo) {
				timeline.to(logo, { yPercent: -70, autoAlpha: 0, duration: 0.9, ease: 'power2.inOut' }, 2.0);
			}
			timeline.add(lift, 2.42);
		};
		if (image && !image.complete) {
			image.addEventListener('load', play);
			image.addEventListener('error', play);
			window.setTimeout(play, 1500);
		} else {
			play();
		}
	}

	/* Cards whose photo fills them ------------------------------------------------------------------ */

	// A card with the class tp-scheda: its photo (tp-scheda-foto) and its hidden text (tp-scheda-nascosto) keep
	// the sizes Divi gives them. Here they are only measured, to know how far the photo must grow to cover the
	// card and how far the words must go up to make room for the hidden text.
	function cards() {
		var root = document.documentElement;
		if (!root.classList.contains('tp-anima')) { return; }
		var hover = window.matchMedia && window.matchMedia('(hover: hover)').matches;
		var all = [];

		each('.tp-scheda', function (card) {
			var photo = card.querySelector('.tp-scheda-foto');
			if (!photo || card.tpScheda) { return; }
			card.tpScheda = true;
			all.push(card);

			var measure = function () {
				var open = card.classList.contains('tp-scheda--aperta');
				card.classList.add('tp-scheda--misura', 'tp-scheda--pronta');
				card.classList.remove('tp-scheda--aperta');
				var style = window.getComputedStyle(card);
				var gap = parseFloat(style.rowGap) || 0;
				var up = 0;
				card.style.setProperty('--tp-scheda-sinistra', (parseFloat(style.paddingLeft) || 0) + 'px');
				card.style.setProperty('--tp-scheda-destra', (parseFloat(style.paddingRight) || 0) + 'px');
				Array.prototype.forEach.call(card.querySelectorAll('.tp-scheda-nascosto'), function (piece) {
					piece.style.setProperty('--tp-scheda-giu', up + 'px');
					up += piece.offsetHeight + gap;
				});
				var outer = card.getBoundingClientRect();
				var inner = photo.getBoundingClientRect();
				if (!(inner.width > 0 && inner.height > 0)) { card.classList.remove('tp-scheda--misura'); return; }
				var grow = Math.max(outer.width / inner.width, outer.height / inner.height);
				card.style.setProperty('--tp-scheda-s', grow.toFixed(4));
				card.style.setProperty('--tp-scheda-x', ((outer.width - inner.width * grow) / 2 - (inner.left - outer.left)).toFixed(1) + 'px');
				card.style.setProperty('--tp-scheda-y', ((outer.height - inner.height * grow) / 2 - (inner.top - outer.top)).toFixed(1) + 'px');
				card.style.setProperty('--tp-scheda-su', up + 'px');
				card.style.setProperty('--tp-scheda-fondo', (up ? up - gap + (parseFloat(style.paddingBottom) || 0) : 0) + 'px');
				void card.offsetWidth;
				card.classList.remove('tp-scheda--misura');
				if (open) { card.classList.add('tp-scheda--aperta'); }
			};
			var frame = 0;
			var later = function () {
				window.cancelAnimationFrame(frame);
				frame = window.requestAnimationFrame(measure);
			};

			measure();
			if (window.ResizeObserver) { new window.ResizeObserver(later).observe(card); }
			window.addEventListener('load', later);
			if (document.fonts && document.fonts.ready) { document.fonts.ready.then(later); }
			each('img', function (picture) { if (card.contains(picture) && !picture.complete) { picture.addEventListener('load', later); } });

			if (!card.hasAttribute('tabindex')) { card.setAttribute('tabindex', '0'); }
			var open = function () { card.classList.add('tp-scheda--aperta'); };
			var close = function () { card.classList.remove('tp-scheda--aperta'); };
			if (hover) {
				// With a mouse: the card under it, or the one reached with the Tab key.
				card.addEventListener('mouseenter', open);
				card.addEventListener('mouseleave', close);
				card.addEventListener('focus', open);
				card.addEventListener('blur', close);
			} else {
				// With a finger: a tap opens the card, a second tap (or a tap elsewhere) closes it.
				var toggle = function () {
					var was = card.classList.contains('tp-scheda--aperta');
					all.forEach(function (other) { other.classList.remove('tp-scheda--aperta'); });
					if (!was) { open(); }
				};
				card.addEventListener('click', toggle);
				card.addEventListener('keydown', function (event) {
					if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); toggle(); }
				});
			}
		});

		if (!hover && all.length) {
			document.addEventListener('click', function (event) {
				if (event.target.closest && event.target.closest('.tp-scheda')) { return; }
				all.forEach(function (card) { card.classList.remove('tp-scheda--aperta'); });
			});
		}
	}

	/* What stays in view ---------------------------------------------------------------------------- */

	// A piece with the class tp-resta (the diagonal words of the ingredients): on computers, once its middle
	// has come to 39% of the screen's height it stays there while the rest of its section goes by, until the
	// section's end reaches the bottom of the screen. data-posizione="50" changes the 39. The piece is moved
	// with "translate", which adds itself to the rotation set in Divi.
	function stay() {
		var pieces = Array.prototype.slice.call(document.querySelectorAll('.tp-resta'));
		if (!pieces.length || !('translate' in document.documentElement.style) || !window.matchMedia) { return; }
		var wide = window.matchMedia('(min-width: 981px)');
		var places = [];
		var frame = 0;

		var place = function () {
			frame = 0;
			var screen = window.innerHeight;
			var scrolled = window.pageYOffset;
			places.forEach(function (item) {
				if (!wide.matches) { item.piece.style.translate = ''; return; }
				var pin = screen * item.share;
				var most = Math.max(0, item.end - screen + pin - item.middle);
				var move = Math.min(Math.max(scrolled + pin - item.middle, 0), most);
				item.piece.style.translate = move > 0.5 ? '0 ' + move.toFixed(1) + 'px' : '';
			});
		};
		var measure = function () {
			places = pieces.map(function (piece) {
				piece.style.translate = '';
				var section = (piece.closest && piece.closest('.et_pb_section')) || piece.parentNode;
				var box = piece.getBoundingClientRect();
				var share = parseFloat(piece.getAttribute('data-posizione'));
				return {
					piece: piece,
					middle: box.top + box.height / 2 + window.pageYOffset,
					end: section.getBoundingClientRect().bottom + window.pageYOffset,
					share: (share > 0 && share < 100 ? share : 39) / 100
				};
			});
			place();
		};
		var ask = function () { if (!frame) { frame = window.requestAnimationFrame(place); } };
		var timer = 0;
		var again = function () { window.clearTimeout(timer); timer = window.setTimeout(measure, 120); };

		measure();
		window.addEventListener('scroll', ask, { passive: true });
		window.addEventListener('resize', again, { passive: true });
		window.addEventListener('load', again);
		if (window.ResizeObserver) { new window.ResizeObserver(again).observe(document.body); }
	}

	/* Cards that come in ---------------------------------------------------------------------------- */

	// The cards of a group with the class tp-carte: each one comes up from below, a little turned (one to the
	// left, the next to the right), when it enters the screen. As in version 0.8.3; still on phones.
	function arrive() {
		var gsap = window.gsap;
		var root = document.documentElement;
		if (!gsap || !window.IntersectionObserver || !root.classList.contains('tp-anima')) { return; }
		if (window.matchMedia && window.matchMedia('(max-width: 767px)').matches) { return; }

		each('.tp-carte', function (group) {
			if (group.tpCarte) { return; }
			group.tpCarte = true;
			var list = Array.prototype.filter.call(group.children, function (card) { return card.getBoundingClientRect().height > 0; });
			var watch = new window.IntersectionObserver(function (entries) {
				entries.forEach(function (entry) {
					if (!entry.isIntersecting) { return; }
					watch.unobserve(entry.target);
					gsap.to(entry.target, {
						autoAlpha: 1, y: 0, rotate: 0, duration: 1.08, ease: 'power3.out', overwrite: 'auto',
						onComplete: function () { gsap.set(entry.target, { clearProps: 'transform,opacity,visibility' }); }
					});
				});
			}, { threshold: 0.14 });
			list.forEach(function (card, index) {
				gsap.set(card, { autoAlpha: 0, y: 96, rotate: index % 2 === 0 ? -2.8 : 2.8 });
				watch.observe(card);
			});
		});
	}

	/* Start --------------------------------------------------------------------------------------- */

	onReady(function () {
		opening();
		header();
		videos();
		running();
		cards();
		arrive();
		stay();
		var fonts = document.fonts && document.fonts.ready ? document.fonts.ready : null;
		if (fonts) {
			// The lines of a title are cut with its own font: wait for it, but not for long.
			var started = false;
			var go = function () { if (!started) { started = true; running(); afterOpening(letters); } };
			fonts.then(go);
			window.setTimeout(go, 1500);
		} else {
			afterOpening(letters);
		}
		var timer = 0;
		var width = window.innerWidth;
		window.addEventListener('resize', function () {
			if (window.innerWidth === width) { return; }
			width = window.innerWidth;
			window.clearTimeout(timer);
			timer = window.setTimeout(function () { running(); videos(); }, 200);
		}, { passive: true });
	});
})();
