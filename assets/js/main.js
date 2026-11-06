/**
 * NanoCat front-end behaviour.
 *
 * No dependencies. Every enhancement is progressive: if a hook is absent the
 * rest of the script keeps working.
 */
(function () {
	'use strict';

	var doc = document;
	var root = doc.documentElement;
	var body = doc.body;
	var reducedMotion = window.matchMedia
		? window.matchMedia('(prefers-reduced-motion: reduce)').matches
		: false;

	function $(selector, context) {
		return (context || doc).querySelector(selector);
	}

	function $all(selector, context) {
		return Array.prototype.slice.call((context || doc).querySelectorAll(selector));
	}

	function scrollBehaviour() {
		return reducedMotion ? 'auto' : 'smooth';
	}

	function storageGet(key) {
		try {
			return window.localStorage.getItem(key);
		} catch (error) {
			return null;
		}
	}

	function storageSet(key, value) {
		try {
			window.localStorage.setItem(key, value);
		} catch (error) {
			/* Private mode or a blocked storage API: the toggle still works. */
		}
	}

	/* ---------------------------------------------------------------------
	 * Color scheme
	 * ------------------------------------------------------------------ */

	var themeToggle = $('[data-theme-toggle]');
	var themeMeta = doc.getElementById('nanocat-theme-color');
	var colorQuery = window.matchMedia ? window.matchMedia('(prefers-color-scheme: dark)') : null;

	function currentMode() {
		return root.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
	}

	function syncThemeLabel(mode) {
		if (themeToggle) {
			themeToggle.setAttribute('aria-pressed', mode === 'dark' ? 'true' : 'false');
			themeToggle.setAttribute('aria-label', mode === 'dark' ? '切换到浅色模式' : '切换到深色模式');
		}

		if (themeMeta) {
			themeMeta.setAttribute('content', mode === 'dark' ? '#071512' : '#f4fbf7');
		}
	}

	function applyTheme(mode, persist) {
		root.setAttribute('data-theme', mode === 'dark' ? 'dark' : 'light');
		syncThemeLabel(mode);
		if (persist) {
			storageSet('nanocat-theme', mode);
		}
	}

	if (themeToggle) {
		syncThemeLabel(currentMode());

		themeToggle.addEventListener('click', function () {
			var next = currentMode() === 'dark' ? 'light' : 'dark';
			applyTheme(next, true);
		});
	}

	if (colorQuery) {
		var onSystemThemeChange = function (event) {
			if (!storageGet('nanocat-theme')) {
				applyTheme(event.matches ? 'dark' : 'light', false);
			}
		};

		if (typeof colorQuery.addEventListener === 'function') {
			colorQuery.addEventListener('change', onSystemThemeChange);
		} else if (typeof colorQuery.addListener === 'function') {
			colorQuery.addListener(onSystemThemeChange);
		}
	}

	/* ---------------------------------------------------------------------
	 * Mobile navigation
	 * ------------------------------------------------------------------ */

	var header = $('[data-site-header]');
	var menuToggle = $('[data-menu-toggle]');
	var navigation = doc.getElementById('site-navigation');

	function setMenu(open, focusToggle) {
		if (!header || !menuToggle) {
			return;
		}

		header.classList.toggle('is-menu-open', open);
		doc.body.classList.toggle('is-nav-open', open);
		menuToggle.setAttribute('aria-expanded', open ? 'true' : 'false');

		if (open) {
			var firstLink = navigation ? navigation.querySelector('a') : null;
			if (firstLink) {
				window.setTimeout(function () {
					firstLink.focus();
				}, 80);
			}
		} else if (focusToggle) {
			menuToggle.focus();
		}
	}

	function closeMenu(focusToggle) {
		if (header && header.classList.contains('is-menu-open')) {
			setMenu(false, focusToggle);
		}
	}

	if (menuToggle) {
		menuToggle.addEventListener('click', function () {
			setMenu(!header.classList.contains('is-menu-open'), false);
		});
	}

	if (navigation) {
		navigation.addEventListener('click', function (event) {
			if (event.target.closest('a')) {
				closeMenu(false);
			}
		});
	}

	doc.addEventListener('click', function (event) {
		if (!header || !header.classList.contains('is-menu-open')) {
			return;
		}

		if (!header.contains(event.target)) {
			closeMenu(false);
		}
	});

	doc.addEventListener('keydown', function (event) {
		if (event.key === 'Escape') {
			closeMenu(true);
		}
	});

	window.addEventListener('resize', function () {
		if (window.innerWidth > 980) {
			closeMenu(false);
		}
	});

	/* ---------------------------------------------------------------------
	 * Search dialog
	 * ------------------------------------------------------------------ */

	var searchDialog = doc.getElementById('nanocat-search-dialog');
	var searchOpeners = $all('[data-search-open]');
	var lastSearchTrigger = null;

	function setSearchExpanded(expanded) {
		searchOpeners.forEach(function (trigger) {
			trigger.setAttribute('aria-expanded', expanded ? 'true' : 'false');
		});
	}

	function openSearch(trigger) {
		if (!searchDialog) {
			return;
		}

		closeMenu(false);
		lastSearchTrigger = trigger || doc.activeElement;

		if (typeof searchDialog.showModal === 'function' && !searchDialog.open) {
			searchDialog.showModal();
		} else {
			searchDialog.setAttribute('open', '');
		}

		body.classList.add('is-locked');
		setSearchExpanded(true);

		var field = searchDialog.querySelector('input[type="search"], input[name="s"]');
		if (field) {
			window.setTimeout(function () {
				field.focus();
				field.select();
			}, 70);
		}
	}

	function closeSearch() {
		if (!searchDialog) {
			return;
		}

		if (searchDialog.open && typeof searchDialog.close === 'function') {
			searchDialog.close();
		} else {
			searchDialog.removeAttribute('open');
			body.classList.remove('is-locked');
			setSearchExpanded(false);

			if (lastSearchTrigger && doc.contains(lastSearchTrigger)) {
				lastSearchTrigger.focus();
			}
		}
	}

	function resetSearch() {
		body.classList.remove('is-locked');
		setSearchExpanded(false);

		if (lastSearchTrigger && doc.contains(lastSearchTrigger)) {
			lastSearchTrigger.focus();
		}
	}

	if (searchDialog) {
		searchOpeners.forEach(function (trigger) {
			trigger.addEventListener('click', function (event) {
				event.preventDefault();
				openSearch(trigger);
			});
		});

		$all('[data-search-close]', searchDialog).forEach(function (button) {
			button.addEventListener('click', closeSearch);
		});

		searchDialog.addEventListener('click', function (event) {
			if (event.target === searchDialog) {
				closeSearch();
			}
		});

		searchDialog.addEventListener('close', resetSearch);
		searchDialog.addEventListener('cancel', function () {
			body.classList.remove('is-locked');
		});
	}

	/* ---------------------------------------------------------------------
	 * Reading progress, sticky header and back-to-top
	 * ------------------------------------------------------------------ */

	var readingBar = $('[data-reading-progress]');
	var readingContent = $('[data-entry-content]');
	var backToTop = $('[data-back-to-top]');

	if (backToTop) {
		backToTop.addEventListener('click', function () {
			window.scrollTo({ top: 0, behavior: scrollBehaviour() });
		});
	}

	function updateReadingProgress() {
		if (!readingBar || !readingContent) {
			return;
		}

		var contentTop = readingContent.getBoundingClientRect().top + window.pageYOffset;
		var headerHeight = header ? header.offsetHeight : 0;
		var start = contentTop - headerHeight - 24;
		var end = contentTop + readingContent.offsetHeight - window.innerHeight + 140;
		var progress = (window.pageYOffset - start) / Math.max(1, end - start);

		progress = Math.max(0, Math.min(1, progress));
		readingBar.style.width = (progress * 100).toFixed(2) + '%';
	}

	var scrollQueued = false;

	function onScroll() {
		if (scrollQueued) {
			return;
		}

		scrollQueued = true;
		window.requestAnimationFrame(function () {
			scrollQueued = false;
			updateScroll();
		});
	}

	function updateScroll() {
		var offset = window.pageYOffset || root.scrollTop;

		if (header) {
			header.classList.toggle('is-scrolled', offset > 10);
		}

		if (backToTop) {
			if (offset > 560) {
				if (backToTop.hidden) {
					backToTop.hidden = false;
					window.requestAnimationFrame(function () {
						backToTop.classList.add('is-visible');
					});
				}
			} else if (!backToTop.hidden) {
				backToTop.classList.remove('is-visible');
				backToTop.hidden = true;
			}
		}

		updateReadingProgress();
		updateTocActive();
	}

	window.addEventListener('scroll', onScroll, { passive: true });
	window.addEventListener('resize', onScroll);

	/* ---------------------------------------------------------------------
	 * Table of contents
	 * ------------------------------------------------------------------ */

	var tocDesktop = $('[data-toc]');
	var tocDesktopNav = $('[data-toc-nav]');
	var tocMobile = $('[data-toc-mobile]');
	var tocMobileNav = $('[data-toc-nav-mobile]');
	var tocCount = $('[data-toc-count]');
	var tocHeadings = [];
	var activeTocId = null;

	function slugify(text) {
		return text
			.trim()
			.toLowerCase()
			.replace(/[\s\u3000]+/g, '-')
			.replace(/[^\w\u4e00-\u9fa5-]+/g, '')
			.replace(/-+/g, '-')
			.replace(/^-|-$/g, '')
			.slice(0, 64);
	}

	function setTocActive(id) {
		if (!id || id === activeTocId) {
			return;
		}

		activeTocId = id;

		[tocDesktopNav, tocMobileNav].forEach(function (nav) {
			if (!nav) {
				return;
			}

			$all('a', nav).forEach(function (link) {
				var isActive = link.getAttribute('data-toc-target') === id;
				link.classList.toggle('is-active', isActive);

				if (isActive) {
					link.setAttribute('aria-current', 'location');
				} else {
					link.removeAttribute('aria-current');
				}
			});
		});
	}

	function updateTocActive() {
		if (!tocHeadings.length) {
			return;
		}

		var headerHeight = header ? header.offsetHeight : 0;
		var threshold = headerHeight + 70;
		var current = tocHeadings[0].id;

		for (var i = 0; i < tocHeadings.length; i += 1) {
			if (tocHeadings[i].element.getBoundingClientRect().top <= threshold) {
				current = tocHeadings[i].id;
			} else {
				break;
			}
		}

		var documentHeight = Math.max(body.scrollHeight, root.scrollHeight);
		if (window.innerHeight + window.pageYOffset >= documentHeight - 4) {
			current = tocHeadings[tocHeadings.length - 1].id;
		}

		setTocActive(current);
	}

	function goToHeading(id, details) {
		var target = doc.getElementById(id);
		if (!target) {
			return;
		}

		var headerHeight = header ? header.offsetHeight : 0;
		var top = target.getBoundingClientRect().top + window.pageYOffset - headerHeight - 22;

		window.scrollTo({ top: top, behavior: scrollBehaviour() });
		setTocActive(id);

		if (window.history && window.history.pushState) {
			window.history.pushState(null, '', '#' + id);
		}

		if (details) {
			details.open = false;
		}
	}

	function bindTocNav(nav, details) {
		if (!nav) {
			return;
		}

		nav.addEventListener('click', function (event) {
			var link = event.target.closest('a[data-toc-target]');
			if (!link) {
				return;
			}

			event.preventDefault();
			goToHeading(link.getAttribute('data-toc-target'), details);
		});
	}

	function initToc() {
		var content = $('[data-entry-content]');
		if (!content || (!tocDesktopNav && !tocMobileNav)) {
			return;
		}

		var headings = $all('h2, h3', content).filter(function (heading) {
			return heading.textContent.trim().length > 0;
		});

		if (headings.length < 2) {
			return;
		}

		var usedIds = {};

		headings.forEach(function (heading, index) {
			var id = heading.id;

			if (!id) {
				var base = slugify(heading.textContent) || 'section-' + (index + 1);
				id = base;
				var suffix = 2;

				while (doc.getElementById(id) || usedIds[id]) {
					id = base + '-' + suffix;
					suffix += 1;
				}

				heading.id = id;
			}

			usedIds[id] = true;
			tocHeadings.push({ id: id, element: heading });
		});

		var list = doc.createElement('ol');

		tocHeadings.forEach(function (item) {
			var listItem = doc.createElement('li');
			var link = doc.createElement('a');

			listItem.className = 'toc-level-' + (item.element.tagName === 'H3' ? '3' : '2');
			link.href = '#' + item.id;
			link.setAttribute('data-toc-target', item.id);
			link.textContent = item.element.textContent.trim();
			listItem.appendChild(link);
			list.appendChild(listItem);
		});

		[tocDesktopNav, tocMobileNav].forEach(function (nav) {
			if (!nav) {
				return;
			}

			nav.appendChild(list.cloneNode(true));
		});

		if (tocDesktop) {
			tocDesktop.hidden = false;
		}

		if (tocMobile) {
			tocMobile.hidden = false;
		}

		if (tocCount) {
			tocCount.textContent = '共 ' + tocHeadings.length + ' 节';
		}

		bindTocNav(tocDesktopNav, null);
		bindTocNav(tocMobileNav, tocMobile);
		updateTocActive();
	}

	/* ---------------------------------------------------------------------
	 * Clipboard helpers
	 * ------------------------------------------------------------------ */

	function copyText(text) {
		if (navigator.clipboard && window.isSecureContext) {
			return navigator.clipboard.writeText(text).then(
				function () {
					return true;
				},
				function () {
					return legacyCopy(text);
				}
			);
		}

		return Promise.resolve(legacyCopy(text));
	}

	function legacyCopy(text) {
		var field = doc.createElement('textarea');
		field.value = text;
		field.setAttribute('readonly', '');
		field.style.position = 'fixed';
		field.style.top = '-1000px';
		field.style.opacity = '0';
		doc.body.appendChild(field);

		var activeElement = doc.activeElement;
		var copied = false;
		field.select();

		try {
			copied = doc.execCommand('copy');
		} catch (error) {
			copied = false;
		}

		doc.body.removeChild(field);

		if (activeElement && typeof activeElement.focus === 'function') {
			activeElement.focus({ preventScroll: true });
		}

		return copied;
	}

	/* ---------------------------------------------------------------------
	 * Code block copy buttons
	 * ------------------------------------------------------------------ */

	var copyIcon =
		'<svg class="icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="9" y="9" width="11" height="11" rx="2"></rect><path d="M5 15V5a2 2 0 0 1 2-2h10"></path></svg>';
	var checkIcon =
		'<svg class="icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 5 5L20 7"></path></svg>';

	function initCodeCopy() {
		var content = $('[data-entry-content]');
		if (!content) {
			return;
		}

		$all('pre', content).forEach(function (block) {
			if ($('.code-copy', block)) {
				return;
			}

			var button = doc.createElement('button');
			button.type = 'button';
			button.className = 'code-copy';
			button.setAttribute('aria-label', '复制代码');
			button.setAttribute('aria-live', 'polite');
			button.innerHTML = copyIcon + '<span>复制</span>';
			block.appendChild(button);

			var resetTimer = null;

			button.addEventListener('click', function () {
				var code = block.querySelector('code');
				var text = code ? code.textContent : block.textContent;

				copyText(text).then(function (copied) {
					window.clearTimeout(resetTimer);
					button.classList.toggle('is-copied', copied);
					button.innerHTML =
						(copied ? checkIcon : copyIcon) +
						'<span>' + (copied ? '已复制' : '复制失败') + '</span>';

					resetTimer = window.setTimeout(function () {
						button.classList.remove('is-copied');
						button.innerHTML = copyIcon + '<span>复制</span>';
					}, 1800);
				});
			});
		});
	}

	/* ---------------------------------------------------------------------
	 * Share buttons
	 * ------------------------------------------------------------------ */

	function initShare() {
		var nativeButton = $('[data-share-native]');
		var copyButtons = $all('[data-share-copy]');
		var labels = $all('[data-share-label]');
		var canonical = $('link[rel="canonical"]');
		var shareUrl = canonical ? canonical.href : window.location.href;
		var defaultLabel = labels.length ? labels[0].textContent : '复制链接';
		var shareResetTimer = null;

		if (nativeButton && navigator.share) {
			nativeButton.hidden = false;
			nativeButton.addEventListener('click', function () {
				navigator
					.share({
						title: doc.title,
						text: doc.title,
						url: shareUrl
					})
					.catch(function () {
						/* Cancelled shares are not errors worth interrupting the reader. */
					});
			});
		}

		function setShareState(message, copied) {
			labels.forEach(function (label) {
				label.textContent = message;
			});

			copyButtons.forEach(function (button) {
				button.classList.toggle('is-copied', copied);
			});

			window.clearTimeout(shareResetTimer);
			shareResetTimer = window.setTimeout(function () {
				labels.forEach(function (label) {
					label.textContent = defaultLabel;
				});
				copyButtons.forEach(function (button) {
					button.classList.remove('is-copied');
				});
			}, 2200);
		}

		copyButtons.forEach(function (button) {
			button.addEventListener('click', function () {
				copyText(shareUrl).then(function (copied) {
					setShareState(copied ? '已复制链接' : '复制失败', copied);
				});
			});
		});
	}

	/* ---------------------------------------------------------------------
	 * Boot
	 * ------------------------------------------------------------------ */

	function init() {
		initToc();
		initCodeCopy();
		initShare();

		if (searchDialog && searchDialog.open) {
			body.classList.add('is-locked');
			setSearchExpanded(true);
		}

		updateScroll();
	}

	if (doc.readyState === 'loading') {
		doc.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
}());
