(function () {
	'use strict';

	var root = document.getElementById('nanocat-player');
	if (!root) {
		return;
	}

	var overlay = document.getElementById('nanocat-netease');
	var url = root.getAttribute('data-netease-url') || '';
	var frame = overlay ? overlay.querySelector('[data-netease-frame]') : null;

	if (!overlay || !url) {
		return;
	}

	function open() {
		if (window.matchMedia('(max-width: 720px)').matches) {
			window.open(url, '_blank', 'noopener');
			return;
		}

		if (frame && !frame.getAttribute('src')) {
			frame.src = frame.getAttribute('data-src');
		}

		overlay.classList.add('is-open');
		overlay.setAttribute('aria-hidden', 'false');
		document.body.classList.add('music-netease-open');
	}

	function close() {
		overlay.classList.remove('is-open');
		overlay.setAttribute('aria-hidden', 'true');
		document.body.classList.remove('music-netease-open');
	}

	root.querySelectorAll('[data-player-open]').forEach(function (button) {
		button.addEventListener('click', open);
	});

	overlay.querySelectorAll('[data-netease-close]').forEach(function (button) {
		button.addEventListener('click', close);
	});

	document.addEventListener('keydown', function (event) {
		if (event.key === 'Escape') {
			close();
		}
	});
}());
