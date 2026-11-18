(function () {
	'use strict';

	var root = document.getElementById('nanocat-player');
	if (!root) {
		return;
	}

	var audio = root.querySelector('audio');
	var trackEls = Array.prototype.slice.call(root.querySelectorAll('.music-player__track'));
	var titleEl = root.querySelector('.music-player__title');
	var artistEl = root.querySelector('.music-player__artist');
	var playBtn = root.querySelector('[data-player-play]');
	var prevBtn = root.querySelector('[data-player-prev]');
	var nextBtn = root.querySelector('[data-player-next]');
	var listBtn = root.querySelector('[data-player-list]');
	var panel = root.querySelector('.music-player__playlist');
	var qqBtn = root.querySelector('[data-player-qq]');
	var qq = document.getElementById('nanocat-qq');
	var progress = root.querySelector('[data-player-progress]');
	var bar = progress ? progress.querySelector('span') : null;

	if (!audio || !trackEls.length) {
		return;
	}

	var index = 0;
	var loaded = false;

	audio.volume = 0.65;

	function track(i) {
		var el = trackEls[i];
		return {
			src: el.getAttribute('data-src'),
			title: el.getAttribute('data-title'),
			artist: el.getAttribute('data-artist')
		};
	}

	function setTrack(i, autoplay) {
		index = (i + trackEls.length) % trackEls.length;
		var data = track(index);

		audio.src = data.src;
		loaded = true;
		titleEl.textContent = data.title;
		artistEl.textContent = data.artist;

		trackEls.forEach(function (el, k) {
			el.classList.toggle('is-active', k === index);
		});

		if (bar) {
			bar.style.width = '0%';
		}
		if (progress) {
			progress.setAttribute('aria-valuenow', '0');
		}

		if ('mediaSession' in navigator && 'MediaMetadata' in window) {
			navigator.mediaSession.metadata = new window.MediaMetadata({
				title: data.title,
				artist: data.artist,
				album: 'NanoCat Ambient'
			});
		}

		if (autoplay) {
			audio.play().catch(function () {
				root.classList.remove('is-playing');
			});
		}
	}

	function setPlaying(playing) {
		root.classList.toggle('is-playing', playing);
		playBtn.setAttribute('aria-label', playing ? '暂停' : '播放');
	}

	function toggle() {
		if (!loaded) {
			setTrack(index, true);
			return;
		}
		if (audio.paused) {
			audio.play().catch(function () {
				setPlaying(false);
			});
		} else {
			audio.pause();
		}
	}

	playBtn.addEventListener('click', toggle);
	prevBtn.addEventListener('click', function () {
		setTrack(index - 1, !audio.paused);
	});
	nextBtn.addEventListener('click', function () {
		setTrack(index + 1, !audio.paused);
	});

	listBtn.addEventListener('click', function () {
		var open = panel.hasAttribute('hidden');
		if (open) {
			panel.removeAttribute('hidden');
		} else {
			panel.setAttribute('hidden', '');
		}
		listBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
	});

	trackEls.forEach(function (el, i) {
		el.addEventListener('click', function () {
			setTrack(i, true);
			panel.setAttribute('hidden', '');
			listBtn.setAttribute('aria-expanded', 'false');
		});
	});

	audio.addEventListener('play', function () {
		setPlaying(true);
	});
	audio.addEventListener('pause', function () {
		setPlaying(false);
	});
	audio.addEventListener('ended', function () {
		setTrack(index + 1, true);
	});
	audio.addEventListener('timeupdate', function () {
		if (!progress || !bar || !audio.duration) {
			return;
		}
		var percent = Math.min(100, Math.max(0, (audio.currentTime / audio.duration) * 100));
		bar.style.width = percent.toFixed(2) + '%';
		progress.setAttribute('aria-valuenow', String(Math.round(percent)));
	});

	function seek(event) {
		if (!audio.duration) {
			return;
		}
		var rect = progress.getBoundingClientRect();
		var ratio = Math.min(1, Math.max(0, (event.clientX - rect.left) / rect.width));
		audio.currentTime = ratio * audio.duration;
	}

	progress.addEventListener('click', seek);
	progress.addEventListener('keydown', function (event) {
		if (!audio.duration) {
			return;
		}
		if (event.key === 'ArrowRight') {
			audio.currentTime = Math.min(audio.duration, audio.currentTime + 5);
		} else if (event.key === 'ArrowLeft') {
			audio.currentTime = Math.max(0, audio.currentTime - 5);
		}
	});

	if (qqBtn && qq) {
		var qqFrame = qq.querySelector('[data-qq-frame]');
		var qqUrl = qqBtn.getAttribute('data-qq-url');

		var closeQQ = function () {
			qq.classList.remove('is-open');
			qq.setAttribute('aria-hidden', 'true');
			document.body.classList.remove('music-qq-open');
		};

		qqBtn.addEventListener('click', function () {
			if (window.matchMedia('(max-width: 720px)').matches) {
				window.open(qqUrl, '_blank', 'noopener');
				return;
			}
			if (qqFrame && !qqFrame.getAttribute('src')) {
				qqFrame.src = qqFrame.getAttribute('data-src');
			}
			qq.classList.add('is-open');
			qq.setAttribute('aria-hidden', 'false');
			document.body.classList.add('music-qq-open');
			audio.pause();
		});

		qq.querySelectorAll('[data-qq-close]').forEach(function (el) {
			el.addEventListener('click', closeQQ);
		});

		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape') {
				closeQQ();
			}
		});
	}

	// Set the first title without fetching the audio file yet.
	var first = track(0);
	titleEl.textContent = first.title;
	artistEl.textContent = first.artist;
}());
