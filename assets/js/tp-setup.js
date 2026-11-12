/*
 * Aspetto > Tasche Piene: creates the site one step after the other (the pictures a few at a time, then
 * the pages) and shows how far it is. A step that does not answer is sent again three times; then
 * "Riprova" goes on from the same step. Without this file the button sends the whole work at once.
 * It also asks for a yes before the photograph of the site is saved over, restored or a restore undone.
 */
(function () {
	'use strict';

	var box = document.querySelector('.tp-setup');
	if (!box || !window.fetch || !window.URLSearchParams) { return; }

	var ajax = box.getAttribute('data-ajax');
	var nonce = box.getAttribute('data-nonce');
	var part = {};
	['ready', 'ask', 'run', 'error', 'again', 'photo'].forEach(function (name) { part[name] = box.querySelector('.tp-setup__' + name); });
	var bar = box.querySelector('.tp-setup__bar');
	var fill = box.querySelector('.tp-setup__fill');
	var status = box.querySelector('.tp-setup__status');
	var state = { step: 'start', offset: 0, total: 0, tries: 0 };

	function show(names) {
		Object.keys(part).forEach(function (name) { if (part[name]) { part[name].hidden = names.indexOf(name) === -1; } });
	}

	function progress() {
		var total = state.total || 1;
		var percent = state.step === 'start' ? 2 : state.step === 'media' ? 4 + 86 * (state.offset / total) : 94;
		fill.style.width = percent + '%';
		bar.setAttribute('aria-valuenow', String(Math.round(percent)));
		status.textContent = state.step === 'media'
			? 'Foto e video nella libreria Media: ' + Math.min(state.offset, total) + ' di ' + total
			: state.step === 'finish' ? 'Creo le pagine, la testata e il piè di pagina…' : 'Preparo…';
	}

	function failed() {
		state.tries += 1;
		if (state.tries <= 3) {
			window.setTimeout(send, 1500 * state.tries);
		} else {
			show(['error']);
		}
	}

	function send() {
		progress();
		var body = new URLSearchParams({ action: 'tp_setup', nonce: nonce, step: state.step, offset: String(state.offset) });
		window.fetch(ajax, { method: 'POST', credentials: 'same-origin', body: body })
			.then(function (response) { return response.ok ? response.json() : Promise.reject(new Error(String(response.status))); })
			.then(function (json) {
				var data = json && json.success ? json.data : null;
				if (!data || data.error) { return failed(); }
				if (data.busy) { return window.setTimeout(send, 2500); }
				state.tries = 0;
				if (data.done) {
					fill.style.width = '100%';
					var edit = box.querySelector('.tp-setup__edit');
					var view = box.querySelector('.tp-setup__view');
					if (edit && data.edit_url) { edit.href = data.edit_url; }
					if (view && data.home_url) { view.href = data.home_url; }
					show(['ready', 'again', 'photo']);
					return;
				}
				if (!data.next) { return failed(); }
				state.step = data.next;
				state.offset = data.offset || 0;
				state.total = data.total || state.total;
				send();
			})
			.catch(failed);
	}

	function start() {
		state = { step: 'start', offset: 0, total: 0, tries: 0 };
		show(['run']);
		send();
	}

	var startButton = box.querySelector('.tp-setup__start');
	if (startButton) {
		startButton.addEventListener('click', function (event) { event.preventDefault(); start(); });
	}
	var retry = box.querySelector('.tp-setup__retry');
	if (retry) {
		retry.addEventListener('click', function () { state.tries = 0; show(['run']); send(); });
	}
	var redo = box.querySelector('.tp-setup__redo');
	if (redo) {
		redo.addEventListener('click', function () { if (window.confirm(redo.getAttribute('data-confirm'))) { start(); } });
	}
	// The photograph of the site: each of its buttons asks before it writes over something.
	Array.prototype.forEach.call(box.querySelectorAll('.tp-setup__photo button[data-confirm]'), function (button) {
		button.addEventListener('click', function (event) {
			if (!window.confirm(button.getAttribute('data-confirm'))) { event.preventDefault(); }
		});
	});
	if (box.getAttribute('data-autostart') === '1') { start(); }
})();
