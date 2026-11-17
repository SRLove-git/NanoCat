/**
 * Swup client-side navigation.
 *
 * The header and footer dialogs stay put; only #swup (main + footer) is
 * replaced. Page-level enhancements are re-initialised through the hooks
 * exposed by main.js and ai.js.
 */
(function () {
	'use strict';

	if (typeof Swup === 'undefined' || !document.getElementById('swup')) {
		return;
	}

	function normalize(path) {
		return (path || '/').replace(/\/+$/, '') || '/';
	}

	function isHome() {
		var path = normalize(window.location.pathname);
		return path === '/' || /^\/page\/\d+$/.test(path);
	}

	function closeOverlays() {
		document.querySelectorAll('dialog[open]').forEach(function (dialog) {
			try {
				dialog.close();
			} catch (error) {
				/* Ignore browsers that refuse to close a dialog. */
			}
		});

		document.body.classList.remove('is-nav-open', 'is-locked');

		var menuToggle = document.querySelector('[data-menu-toggle]');
		if (menuToggle) {
			menuToggle.setAttribute('aria-expanded', 'false');
		}
	}

	function syncHeader() {
		var header = document.querySelector('.site-header');
		if (!header) {
			return;
		}

		header.classList.toggle('flec-header--hero', isHome());

		var current = normalize(window.location.pathname);
		document.querySelectorAll('.site-navigation .primary-menu a').forEach(function (link) {
			var url;
			try {
				url = new URL(link.href, window.location.origin);
			} catch (error) {
				return;
			}

			var path = normalize(url.pathname);
			var active = path === current || (path !== '/' && current.indexOf(path + '/') === 0);
			var item = link.closest('li');

			if (item) {
				item.classList.toggle('current-menu-item', active);
				item.classList.toggle('current_page_item', active);
			}

			if (active) {
				link.setAttribute('aria-current', 'page');
			} else {
				link.removeAttribute('aria-current');
			}
		});
	}

	function syncReadingBar() {
		var existing = document.querySelector('.reading-progress');
		var isSingle = document.body.classList.contains('single');

		if (isSingle && !existing) {
			var bar = document.createElement('div');
			bar.className = 'reading-progress';
			bar.setAttribute('aria-hidden', 'true');
			bar.innerHTML = '<span class="reading-progress__bar" data-reading-progress></span>';

			var header = document.querySelector('.site-header');
			if (header && header.parentNode) {
				header.parentNode.insertBefore(bar, header);
			}
		} else if (!isSingle && existing) {
			existing.remove();
		}
	}

	function onContentReplaced() {
		syncHeader();
		syncReadingBar();
		closeOverlays();

		if (typeof window.NanoCatRefresh === 'function') {
			window.NanoCatRefresh();
		}

		if (typeof window.NanoCatAIRefresh === 'function') {
			window.NanoCatAIRefresh();
		}

		window.scrollTo(0, 0);
	}

	var swup = new Swup({
		containers: ['#swup'],
		animateHistoryBrowsing: true,
		plugins: [
			new SwupHeadPlugin(),
			new SwupBodyClassPlugin(),
			new SwupPreloadPlugin()
		],
		ignoreVisit: function (url, options) {
			var link = options && options.el;

			if (link && (link.target === '_blank' || link.hasAttribute('download') || link.hasAttribute('data-no-swup'))) {
				return true;
			}

			var target;
			try {
				target = url instanceof URL ? url : new URL(url, window.location.href);
			} catch (error) {
				return true;
			}

			if (target.origin !== window.location.origin) {
				return true;
			}

			var path = target.pathname;

			if (/\/(wp-admin|wp-login|wp-json|xmlrpc|feed)(\/|$)/.test(path)) {
				return true;
			}

			if (/\.(pdf|zip|rar|7z|docx?|xlsx?|pptx?|jpe?g|png|gif|webp|svg|mp4|mp3|css|js)$/i.test(path)) {
				return true;
			}

			if (target.hash && path === window.location.pathname) {
				return true;
			}

			return false;
		}
	});

	swup.hooks.on('visit:start', closeOverlays);
	swup.hooks.on('page:view', onContentReplaced);

	syncHeader();
})();
