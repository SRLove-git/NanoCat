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
	var vipEl = root.querySelector('.music-player__vip');
	var playBtn = root.querySelector('[data-player-play]');
	var prevBtn = root.querySelector('[data-player-prev]');
	var nextBtn = root.querySelector('[data-player-next]');
	var listBtn = root.querySelector('[data-player-list]');
	var panel = root.querySelector('.music-player__playlist');
	var progress = root.querySelector('[data-player-progress]');
	var bar = progress ? progress.querySelector('span') : null;

	if (!audio || !trackEls.length) {
		return;
	}

	var index = parseInt(root.getAttribute('data-start') || '0', 10) || 0;
	var loaded = false;

	audio.volume = 0.65;

	function track(i) {
		var el = trackEls[i];
		return {
			src: el.getAttribute('data-src') || '',
			external: el.getAttribute('data-external') || '',
			title: el.getAttribute('data-title') || '',
			artist: el.getAttribute('data-artist') || '',
			vip: el.getAttribute('data-vip') === '1'
		};
	}

	function setMeta(data) {
		titleEl.textContent = data.title;
		artistEl.textContent = data.artist;
		if (vipEl) {
			vipEl.hidden = !data.vip;
		}
		root.classList.toggle('is-vip', data.vip);
	}

	function setActive(i) {
		trackEls.forEach(function (el, k) {
			el.classList.toggle('is-active', k === i);
		});
	}

	function resetProgress() {
		if (bar) {
			bar.style.width = '0%';
		}
		if (progress) {
			progress.setAttribute('aria-valuenow', '0');
		}
	}

	function openExternal(data) {
		if (data.external) {
			window.open(data.external, '_blank', 'noopener');
		}
	}

	function setTrack(i, autoplay) {
		index = (i + trackEls.length) % trackEls.length;
		var data = track(index);

		audio.pause();
		resetProgress();

		if (data.src) {
			audio.src = data.src;
			loaded = true;
		} else {
			audio.removeAttribute('src');
			audio.load();
			loaded = false;
		}

		setMeta(data);
		setActive(index);

		if ('mediaSession' in navigator && 'MediaMetadata' in window) {
			navigator.mediaSession.metadata = new window.MediaMetadata({
				title: data.title,
				artist: data.artist,
				album: '网易云音乐 · 慢品人间烟火色'
			});
		}

		if (autoplay) {
			if (data.src) {
				audio.play().catch(function () {
					setPlaying(false);
				});
			} else {
				openExternal(data);
			}
		}
	}

	function setPlaying(playing) {
		root.classList.toggle('is-playing', playing);
		playBtn.setAttribute('aria-label', playing ? '暂停' : '播放');
	}

	function toggle() {
		var data = track(index);

		if (data.vip) {
			openExternal(data);
			return;
		}

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

	function nextPlayable(step) {
		for (var offset = 1; offset <= trackEls.length; offset++) {
			var candidate = (index + step * offset + trackEls.length * offset) % trackEls.length;
			if (track(candidate).src) {
				setTrack(candidate, !audio.paused || step !== 0);
				return;
			}
		}
	}

	playBtn.addEventListener('click', toggle);
	prevBtn.addEventListener('click', function () {
		nextPlayable(-1);
	});
	nextBtn.addEventListener('click', function () {
		nextPlayable(1);
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
			if (el.getAttribute('data-vip') === '1') {
				openExternal(track(i));
				return;
			}
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
		nextPlayable(1);
	});
	audio.addEventListener('timeupdate', function () {
		if (!progress || !bar || !audio.duration) {
			return;
		}
		var percent = Math.min(100, Math.max(0, (audio.currentTime / audio.duration) * 100));
		bar.style.width = percent.toFixed(2) + '%';
		progress.setAttribute('aria-valuenow', String(Math.round(percent)));
	});
	audio.addEventListener('error', function () {
		setPlaying(false);
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

	// Initial metadata without fetching audio until the user presses play.
	var first = track(index);
	setMeta(first);
	setActive(index);
}());
