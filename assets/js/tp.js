/*
 * Tasche Piene: the effects Divi has no option for. Each one is tied to a class written in a module's
 * Advanced > Attributes; without this file the page is the same, only still.
 * Not loaded in the Visual Builder, where one writes.
 *
 *   tp-scorre            the words of a Text module run across the screen
 *   tp-video-telefono    the background video of a section also runs on phones and tablets
 *   tp-testata           the header: its height is told to the page, its menu closes after a tap on a voice
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
		var speed = parseFloat(module.getAttribute('data-velocita')) || 70;
		module.style.setProperty('--tp-scorre-passo', step + 'px');
		module.style.setProperty('--tp-scorre-tempo', Math.max(4, step / speed) + 's');
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

	/* Start --------------------------------------------------------------------------------------- */

	onReady(function () {
		videos();
		running();
		if (document.fonts && document.fonts.ready) { document.fonts.ready.then(running); }
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
