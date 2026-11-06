/**
 * Progress bar of the home of Tasche Piene in the admin (inc/seed-progress.php): sends the steps of the seed one
 * after the other (menus, photos three at a time, pages) and shows what is being done and about how long is left.
 * A step that does not answer is sent again three times; then "Riprova" goes on from the same step.
 */
(function () {
	'use strict';

	var box = document.querySelector('.tp-seed');

	if (!box || !window.fetch || !window.URLSearchParams) {
		return;
	}

	var ajax = box.getAttribute('data-ajax');
	var nonce = box.getAttribute('data-nonce');
	var part = {
		ask: box.querySelector('.tp-seed__ask'),
		run: box.querySelector('.tp-seed__run'),
		done: box.querySelector('.tp-seed__done'),
		error: box.querySelector('.tp-seed__error')
	};
	var tone = { ask: 'warning', run: 'info', done: 'success', error: 'error' };
	var bar = box.querySelector('.tp-seed__bar');
	var fill = box.querySelector('.tp-seed__fill');
	var status = box.querySelector('.tp-seed__status');
	var FINISH_SECONDS = 6;
	var state = null;
	var creep = 0;

	function show(name) {
		Object.keys(part).forEach(function (key) {
			if (part[key]) {
				part[key].hidden = key !== name;
			}
		});
		box.className = box.className.replace(/\bnotice-(warning|info|success|error)\b/, 'notice-' + tone[name]);
	}

	function setBar(percent) {
		var value = Math.max(0, Math.min(100, percent));

		fill.style.width = value + '%';
		bar.setAttribute('aria-valuenow', String(Math.round(value)));
	}

	function left(seconds) {
		if (seconds < 5) {
			return 'quasi fatto';
		}

		return 'mancano circa ' + Math.ceil(seconds / 5) * 5 + ' secondi';
	}

	function render() {
		var total = state.total || 1;

		window.clearInterval(creep);

		if ('media' === state.step) {
			var done = state.offset;
			var elapsed = (Date.now() - state.mediaStart) / 1000;
			var each = done ? elapsed / done : 1;

			setBar(8 + 77 * done / total);
			status.textContent = 'Carico le foto nella Libreria media: ' + done + ' di ' + total + ' · ' + left(each * (total - done) + FINISH_SECONDS);
		} else if ('finish' === state.step) {
			var value = 88;

			setBar(value);
			status.textContent = 'Creo la pagina Home, l’header, il footer e le pagine Privacy e Cookie · ' + left(FINISH_SECONDS);
			// The last step is one request: the bar goes on slowly while it works.
			creep = window.setInterval(function () {
				value = Math.min(97, value + 1);
				setBar(value);
			}, 600);
		}
	}

	function post(data) {
		var body = new window.URLSearchParams();

		body.append('action', 'tp_child_seed_step');
		body.append('nonce', nonce);
		Object.keys(data).forEach(function (key) {
			body.append(key, data[key]);
		});

		return window.fetch(ajax, { method: 'POST', credentials: 'same-origin', body: body })
			.then(function (response) {
				if (!response.ok) {
					throw new Error('HTTP ' + response.status);
				}

				return response.json();
			})
			.then(function (json) {
				if (!json || !json.success || !json.data || json.data.error) {
					throw new Error('risposta');
				}

				return json.data;
			});
	}

	function finished(data) {
		var view = box.querySelector('.tp-seed__view');
		var edit = box.querySelector('.tp-seed__edit');

		window.clearInterval(creep);
		state.running = false;
		setBar(100);
		status.textContent = 'Fatto.';

		if (view && data.home_url) {
			view.href = data.home_url;
		}

		if (edit) {
			if (data.edit_url) {
				edit.href = data.edit_url;
			} else {
				edit.hidden = true;
			}
		}

		window.setTimeout(function () {
			show('done');
		}, 500);
	}

	function next() {
		post({ step: state.step, offset: String(state.offset), force: state.force ? '1' : '' })
			.then(function (data) {
				state.tries = 0;

				// The time spent waiting for an answer that did not come does not count in the estimate.
				if (state.failedAt) {
					state.mediaStart += Date.now() - state.failedAt;
					state.failedAt = 0;
				}

				if (data.busy) {
					status.textContent = 'Un’altra pagina sta già creando la home: aspetto che finisca…';
					window.setTimeout(next, 2000);
					return;
				}

				if (data.done) {
					finished(data);
					return;
				}

				if ('start' === state.step) {
					state.mediaStart = Date.now();
				}

				state.total = data.total || state.total;
				state.step = data.next;
				state.offset = data.offset || 0;
				render();
				next();
			})
			.catch(function () {
				state.tries += 1;

				if (!state.failedAt) {
					state.failedAt = Date.now();
				}

				if (state.tries <= 3) {
					window.setTimeout(next, 1500 * state.tries);
					return;
				}

				window.clearInterval(creep);
				state.running = false;
				show('error');
			});
	}

	function start(force) {
		if (state && state.running) {
			return;
		}

		state = { step: 'start', offset: 0, total: 0, force: force, tries: 0, running: true, mediaStart: Date.now(), failedAt: 0 };
		show('run');
		setBar(3);
		status.textContent = 'Preparo i menu…';
		next();
	}

	var button = box.querySelector('.tp-seed__start');
	var retry = box.querySelector('.tp-seed__retry');

	if (button) {
		button.addEventListener('click', function (event) {
			event.preventDefault();
			start(true);
		});
	}

	if (retry) {
		retry.addEventListener('click', function (event) {
			event.preventDefault();

			if (!state || state.running) {
				return;
			}

			state.running = true;
			state.tries = 0;

			if (state.failedAt) {
				state.mediaStart += Date.now() - state.failedAt;
				state.failedAt = 0;
			}

			show('run');
			render();
			next();
		});
	}

	if ('1' === box.getAttribute('data-autostart')) {
		start(false);
	}
})();
