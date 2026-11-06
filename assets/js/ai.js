/* global NanoCatAI */
/**
 * NanoCat AI client.
 *
 * A small, dependency-free console for the site's /api/ai endpoints:
 * streaming answers, cited sources, page summaries and a selection action.
 */
(function () {
	'use strict';

	var CFG = window.NanoCatAI || {};
	var API = String(CFG.api || '/api/ai').replace(/\/+$/, '');
	var STR = Object.assign(
		{
			searching: '正在检索站内笔记…',
			grounded: '基于站内文章生成',
			error: 'AI 暂时没有接上。稍后再问一次，或者换个更具体的词。',
			stopped: '已停止生成。',
			sources: '参考来源',
			empty: '问一个具体问题。',
		},
		CFG.strings || {}
	);
	var HISTORY_KEY = 'nanocat-ai-history';
	var MAX_HISTORY = 8;
	var currentDialog = null;
	var consoles = [];
	var selectionButton = null;

	function escapeHTML(value) {
		return String(value == null ? '' : value)
			.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;')
			.replace(/'/g, '&#039;');
	}

	function safeURL(value) {
		try {
			var url = new URL(String(value), window.location.href);
			if (url.protocol === 'http:' || url.protocol === 'https:') {
				return url.href;
			}
		} catch (error) {
			/* Ignore malformed URLs. */
		}
		return '';
	}

	/**
	 * Tiny safe markdown renderer. Raw HTML is always escaped first.
	 *
	 * @param {string} value Markdown-ish model output.
	 * @return {string} Sanitized HTML.
	 */
	function renderMarkdown(value) {
		var codeBlocks = [];
		var text = String(value == null ? '' : value).replace(/\r\n/g, '\n');

		text = text.replace(/```[a-zA-Z0-9_-]*\n?([\s\S]*?)```/g, function (match, code) {
			codeBlocks.push('<pre><code>' + escapeHTML(code.replace(/\s+$/, '')) + '</code></pre>');
			return '\u0000CODE' + (codeBlocks.length - 1) + '\u0000';
		});

		text = escapeHTML(text);
		text = text.replace(/`([^`\n]+)`/g, '<code>$1</code>');
		text = text.replace(/\*\*([^*\n]+)\*\*/g, '<strong>$1</strong>');
		text = text.replace(/\*([^*\n]+)\*/g, '<em>$1</em>');
		text = text.replace(/\[([^\]\n]+)\]\((https?:\/\/[^\s)]+)\)/g, function (match, label, url) {
			var safe = safeURL(url);
			if (!safe) {
				return match;
			}
			return '<a href="' + escapeHTML(safe) + '" target="_blank" rel="noopener noreferrer">' + label + '</a>';
		});

		var blocks = text.split(/\n{2,}/);
		var html = '';

		// Models often separate list items with blank lines. Join matching
		// numbered/bulleted blocks so the renderer can emit one real list.
		blocks = blocks.reduce(function (merged, block) {
			var trimmed = block.trim();
			var previous = merged.length ? merged[merged.length - 1].trim() : '';
			var numbered = /^\d+[.)]\s+/.test(trimmed);
			var previousNumbered = /^\d+[.)]\s+/.test(previous);
			var bulleted = /^\s*[-*]\s+/.test(trimmed);
			var previousBulleted = /^\s*[-*]\s+/.test(previous);

			if ((numbered && previousNumbered) || (bulleted && previousBulleted)) {
				merged[merged.length - 1] += '\n' + trimmed;
				return merged;
			}

			merged.push(block);
			return merged;
		}, []);

		blocks.forEach(function (block) {
			var trimmed = block.trim();
			if (!trimmed) {
				return;
			}

			var codeMatch = trimmed.match(/^\u0000CODE(\d+)\u0000$/);
			if (codeMatch) {
				html += codeBlocks[Number(codeMatch[1])] || '';
				return;
			}

			var heading = trimmed.match(/^(#{1,3})\s+([\s\S]+)$/);
			if (heading) {
				var level = Math.min(3, heading[1].length) + 2;
				html += '<h' + level + '>' + heading[2] + '</h' + level + '>';
				return;
			}

			var lines = trimmed.split('\n');
			var bullets = lines.every(function (line) {
				return /^\s*[-*]\s+/.test(line);
			});
			var numbered = lines.every(function (line) {
				return /^\s*\d+[.)]\s+/.test(line);
			});

			if (bullets) {
				html += '<ul>' + lines.map(function (line) {
					return '<li>' + line.replace(/^\s*[-*]\s+/, '') + '</li>';
				}).join('') + '</ul>';
				return;
			}

			if (numbered) {
				html += '<ol>' + lines.map(function (line) {
					return '<li>' + line.replace(/^\s*\d+[.)]\s+/, '') + '</li>';
				}).join('') + '</ol>';
				return;
			}

			html += '<p>' + lines.join('<br>') + '</p>';
		});

		html = html.replace(/\u0000CODE(\d+)\u0000/g, function (match, index) {
			return codeBlocks[Number(index)] || '';
		});

		return html;
	}

	function readHistory() {
		try {
			var raw = window.localStorage.getItem(HISTORY_KEY);
			var list = raw ? JSON.parse(raw) : [];
			return Array.isArray(list) ? list.filter(function (item) {
				return item && typeof item.q === 'string';
			}) : [];
		} catch (error) {
			return [];
		}
	}

	function saveHistory(question) {
		var list = readHistory().filter(function (item) {
			return item.q !== question;
		});
		list.unshift({ q: question, at: Date.now() });
		list = list.slice(0, MAX_HISTORY);
		try {
			window.localStorage.setItem(HISTORY_KEY, JSON.stringify(list));
		} catch (error) {
			/* Private mode or storage disabled. */
		}
		renderHistory();
	}

	function clearHistory() {
		try {
			window.localStorage.removeItem(HISTORY_KEY);
		} catch (error) {
			/* Ignore. */
		}
		renderHistory();
	}

	function renderHistory() {
		var roots = document.querySelectorAll('[data-ai-history]');
		if (!roots.length) {
			return;
		}

		var list = readHistory();
		roots.forEach(function (root) {
			root.innerHTML = '';
			if (!list.length) {
				var empty = document.createElement('p');
				empty.className = 'ai-history__empty';
				empty.textContent = '还没有提问。上面的问题会保存在这台设备上。';
				root.appendChild(empty);
				return;
			}

			list.forEach(function (item) {
				var button = document.createElement('button');
				button.type = 'button';
				button.className = 'ai-history__item';
				button.textContent = item.q;
				button.addEventListener('click', function () {
					var pageConsole = consoles.find(function (consoleInstance) {
						return consoleInstance.root.getAttribute('data-ai-context') === 'page';
					});
					if (pageConsole) {
						pageConsole.ask(item.q);
						pageConsole.root.scrollIntoView({ block: 'center', behavior: 'smooth' });
						return;
					}
					openDialog(item.q, true);
				});
				root.appendChild(button);
			});
		});
	}

	function scrollThread(consoleInstance) {
		window.requestAnimationFrame(function () {
			consoleInstance.stream.scrollTop = consoleInstance.stream.scrollHeight;
		});
	}

	function createMessage(role) {
		var message = document.createElement('div');
		message.className = 'ai-msg ai-msg--' + role;
		return message;
	}

	function createAssistantMessage(consoleInstance) {
		var message = createMessage('assistant');
		var head = document.createElement('div');
		head.className = 'ai-msg__head';

		var avatar = document.createElement('span');
		avatar.className = 'ai-msg__avatar';
		avatar.setAttribute('aria-hidden', 'true');
		avatar.textContent = '✦';

		var label = document.createElement('span');
		label.textContent = 'NanoCat AI';

		var state = document.createElement('span');
		state.className = 'ai-msg__state is-streaming';
		state.textContent = STR.searching;

		head.appendChild(avatar);
		head.appendChild(label);
		head.appendChild(state);

		var body = document.createElement('div');
		body.className = 'ai-msg__body';

		var sources = document.createElement('div');
		sources.className = 'ai-sources';
		sources.hidden = true;

		message.appendChild(head);
		message.appendChild(body);
		message.appendChild(sources);
		consoleInstance.thread.appendChild(message);

		return {
			message: message,
			state: state,
			body: body,
			sources: sources,
		};
	}

	function renderSources(container, sources) {
		if (!sources || !sources.length) {
			container.hidden = true;
			return;
		}

		container.innerHTML = '';
		var label = document.createElement('span');
		label.className = 'ai-sources__label';
		label.textContent = STR.sources;
		container.appendChild(label);

		var list = document.createElement('div');
		list.className = 'ai-sources__list';

		sources.forEach(function (source) {
			var url = safeURL(source && source.url);
			if (!url) {
				return;
			}

			var link = document.createElement('a');
			link.className = 'ai-source';
			link.href = url;
			link.target = '_blank';
			link.rel = 'noopener noreferrer';

			var spark = document.createElement('span');
			spark.setAttribute('aria-hidden', 'true');
			spark.textContent = '↗';

			var title = document.createElement('span');
			title.textContent = source.title || url;

			link.appendChild(spark);
			link.appendChild(title);
			list.appendChild(link);
		});

		if (!list.children.length) {
			container.hidden = true;
			return;
		}

		container.appendChild(list);
		container.hidden = false;
	}

	function streamAsk(question, handlers, signal) {
		if (!API) {
			return Promise.reject(new Error('API unavailable'));
		}

		return fetch(API + '/ask', {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({ q: question }),
			signal: signal,
		}).then(function (response) {
			if (!response.ok) {
				return response.json().catch(function () {
					return {};
				}).then(function (payload) {
					throw new Error(payload.error || 'HTTP ' + response.status);
				});
			}

			if (!response.body) {
				throw new Error('Streaming unavailable');
			}

			var reader = response.body.getReader();
			var decoder = new TextDecoder('utf-8');
			var buffer = '';
			var finished = false;

			function parseChunk(chunk) {
				chunk.split('\n').forEach(function (line) {
					var trimmed = line.trim();
					if (trimmed.indexOf('data:') !== 0) {
						return;
					}

					var payload;
					try {
						payload = JSON.parse(trimmed.slice(5).trim());
					} catch (error) {
						return;
					}

					if (payload.sources && handlers.onSources) {
						handlers.onSources(payload.sources);
					}
					if (payload.delta && handlers.onDelta) {
						handlers.onDelta(payload.delta);
					}
					if (payload.error && handlers.onDelta) {
						handlers.onDelta('\n\n' + payload.error);
					}
					if (payload.done && !finished) {
						finished = true;
						if (handlers.onDone) {
							handlers.onDone();
						}
					}
				});
			}

			function pump() {
				return reader.read().then(function (result) {
					if (result.done) {
						if (!finished && handlers.onDone) {
							handlers.onDone();
						}
						return;
					}

					buffer += decoder.decode(result.value, { stream: true });
					var parts = buffer.split(/\n\n/);
					buffer = parts.pop();
					parts.forEach(parseChunk);
					return pump();
				});
			}

			return pump();
		});
	}

	function AIConsole(root) {
		this.root = root;
		this.stream = root.querySelector('.ai-console__stream');
		this.thread = root.querySelector('[data-ai-thread]');
		this.intro = root.querySelector('[data-ai-intro]');
		this.form = root.querySelector('[data-ai-form]');
		this.input = root.querySelector('[data-ai-input]');
		this.send = root.querySelector('[data-ai-send]');
		this.stop = root.querySelector('[data-ai-stop]');
		this.clear = root.querySelector('[data-ai-clear]');
		this.busy = false;
		this.abort = null;
		this.answer = '';
		this.sources = [];
		this.lastQuestion = '';
		this.bind();
	}

	AIConsole.prototype.bind = function () {
		var self = this;

		if (!this.form || !this.input || !this.thread) {
			return;
		}

		this.form.addEventListener('submit', function (event) {
			event.preventDefault();
			self.submit();
		});

		this.input.addEventListener('keydown', function (event) {
			if (event.key === 'Enter' && !event.shiftKey) {
				event.preventDefault();
				self.submit();
			}
		});

		this.input.addEventListener('input', function () {
			self.input.style.height = 'auto';
			self.input.style.height = Math.min(this.scrollHeight, 150) + 'px';
		});

		this.root.querySelectorAll('[data-ai-suggest]').forEach(function (button) {
			button.addEventListener('click', function () {
				self.ask(button.getAttribute('data-ai-suggest') || '');
			});
		});

		if (this.stop) {
			this.stop.addEventListener('click', function () {
				self.abortRequest(true);
			});
		}

		if (this.clear) {
			this.clear.addEventListener('click', function () {
				self.clearConversation();
			});
		}
	};

	AIConsole.prototype.focus = function () {
		var self = this;
		window.setTimeout(function () {
			if (self.input) {
				self.input.focus();
			}
		}, 80);
	};

	AIConsole.prototype.submit = function () {
		if (!this.input) {
			return;
		}
		var question = this.input.value.trim();
		if (!question || this.busy) {
			return;
		}
		this.input.value = '';
		this.input.style.height = 'auto';
		this.ask(question);
	};

	AIConsole.prototype.setBusy = function (busy) {
		this.busy = busy;
		if (this.send) {
			this.send.disabled = busy;
		}
		if (this.stop) {
			this.stop.hidden = !busy;
		}
	};

	AIConsole.prototype.clearConversation = function () {
		if (this.thread) {
			this.thread.innerHTML = '';
		}
		if (this.intro) {
			this.intro.classList.remove('is-hidden');
		}
		this.answer = '';
		clearHistory();
		this.focus();
	};

	AIConsole.prototype.abortRequest = function (notify) {
		if (this.abort) {
			this.abort.abort();
			this.abort = null;
		}

		if (this.busy) {
			this.setBusy(false);
			if (notify && this.currentState) {
				this.currentState.classList.remove('is-streaming');
				this.currentState.textContent = STR.stopped;
			}
		}
	};

	AIConsole.prototype.ask = function (question) {
		var self = this;
		question = String(question || '').trim();

		if (!question) {
			return;
		}

		this.abortRequest(false);
		this.setBusy(true);
		this.lastQuestion = question;
		this.answer = '';
		this.sources = [];

		if (this.intro) {
			this.intro.classList.add('is-hidden');
		}

		var user = createMessage('user');
		user.textContent = question;
		this.thread.appendChild(user);

		var assistant = createAssistantMessage(this);
		this.currentState = assistant.state;
		this.currentBody = assistant.body;
		this.currentSources = assistant.sources;
		this.currentMessage = assistant.message;

		scrollThread(this);

		this.abort = new AbortController();

		streamAsk(
			question,
			{
				onSources: function (sources) {
					self.sources = Array.isArray(sources) ? sources : [];
					renderSources(self.currentSources, self.sources);
					if (self.sources.length && self.currentState) {
						self.currentState.textContent = STR.grounded;
					}
					scrollThread(self);
				},
				onDelta: function (delta) {
					self.answer += delta;
					if (self.currentBody) {
						self.currentBody.innerHTML = renderMarkdown(self.answer);
					}
					if (self.currentState) {
						self.currentState.textContent = self.sources.length ? STR.grounded : STR.searching;
					}
					scrollThread(self);
				},
				onDone: function () {
					self.finishAnswer();
				},
			},
			this.abort.signal
		).catch(function (error) {
			if (error && error.name === 'AbortError') {
				return;
			}
			self.failAnswer(error);
		});
	};

	AIConsole.prototype.finishAnswer = function () {
		if (!this.busy) {
			return;
		}

		this.setBusy(false);
		this.abort = null;

		if (this.currentState) {
			this.currentState.classList.remove('is-streaming');
			this.currentState.textContent = this.sources.length ? STR.grounded : '来自站内知识库';
		}

		if (!this.answer) {
			this.currentBody.innerHTML = '<p class="ai-msg__error">' + escapeHTML(STR.error) + '</p>';
		}

		saveHistory(this.lastQuestion);
	};

	AIConsole.prototype.failAnswer = function (error) {
		this.setBusy(false);
		this.abort = null;

		if (this.currentState) {
			this.currentState.classList.remove('is-streaming');
			this.currentState.textContent = '连接中断';
		}

		if (this.currentBody) {
			this.currentBody.innerHTML = '<p class="ai-msg__error">' + escapeHTML(STR.error + (error && error.message ? ' (' + error.message + ')' : '')) + '</p>';
		}

		scrollThread(this);
	};

	function initConsoles() {
		document.querySelectorAll('[data-ai-console]').forEach(function (root) {
			if (root.dataset.aiReady === '1') {
				return;
			}
			root.dataset.aiReady = '1';
			consoles.push(new AIConsole(root));
		});
	}

	function getDialogConsole() {
		var dialog = document.getElementById('nanocat-ai-dialog');
		if (!dialog) {
			return null;
		}
		var root = dialog.querySelector('[data-ai-console]');
		if (!root) {
			return null;
		}
		return consoles.find(function (consoleInstance) {
			return consoleInstance.root === root;
		}) || null;
	}

	function openDialog(prompt, autoAsk) {
		var dialog = document.getElementById('nanocat-ai-dialog');
		if (!dialog) {
			return;
		}

		currentDialog = dialog;
		var consoleInstance = getDialogConsole();

		if (!dialog.open) {
			try {
				dialog.showModal();
			} catch (error) {
				dialog.setAttribute('open', '');
			}
		}

		document.body.classList.add('is-ai-open');
		document.querySelectorAll('[data-ai-open]').forEach(function (button) {
			button.setAttribute('aria-expanded', 'true');
		});

		if (!consoleInstance) {
			return;
		}

		if (prompt) {
			consoleInstance.input.value = prompt;
			if (autoAsk) {
				window.setTimeout(function () {
					consoleInstance.ask(prompt);
				}, 140);
			} else {
				consoleInstance.focus();
			}
		} else {
			consoleInstance.focus();
		}
	}

	function closeDialog() {
		var dialog = document.getElementById('nanocat-ai-dialog');
		if (!dialog) {
			return;
		}

		var consoleInstance = getDialogConsole();
		if (consoleInstance) {
			consoleInstance.abortRequest(false);
		}

		if (dialog.open) {
			dialog.close();
		}
		document.body.classList.remove('is-ai-open');
		document.querySelectorAll('[data-ai-open]').forEach(function (button) {
			button.setAttribute('aria-expanded', 'false');
		});
	}

	function initDialog() {
		var dialog = document.getElementById('nanocat-ai-dialog');
		if (!dialog) {
			return;
		}

		var close = dialog.querySelector('[data-ai-close]');
		if (close) {
			close.addEventListener('click', closeDialog);
		}

		dialog.addEventListener('cancel', function (event) {
			event.preventDefault();
			closeDialog();
		});

		dialog.addEventListener('click', function (event) {
			if (event.target === dialog) {
				closeDialog();
			}
		});

		dialog.addEventListener('close', function () {
			document.body.classList.remove('is-ai-open');
			document.querySelectorAll('[data-ai-open]').forEach(function (button) {
				button.setAttribute('aria-expanded', 'false');
			});
		});

		document.addEventListener('click', function (event) {
			var trigger = event.target.closest('[data-ai-open]');
			if (trigger) {
				event.preventDefault();
				openDialog(trigger.getAttribute('data-ai-prompt') || '', Boolean(trigger.getAttribute('data-ai-prompt')));
				return;
			}

			var promptTrigger = event.target.closest('[data-ai-prompt]');
			if (promptTrigger) {
				event.preventDefault();
				openDialog(promptTrigger.getAttribute('data-ai-prompt') || '', true);
			}
		});

		document.addEventListener('keydown', function (event) {
			if ((event.metaKey || event.ctrlKey) && String(event.key).toLowerCase() === 'k') {
				event.preventDefault();
				if (dialog.open) {
					closeDialog();
				} else {
					openDialog('', false);
				}
			}
		});
	}

	function initSummary() {
		var companion = document.querySelector('[data-ai-companion]');
		if (!companion) {
			return;
		}

		var postId = Number(companion.getAttribute('data-post-id') || 0);
		var summary = companion.querySelector('[data-ai-summary]');
		if (!postId || !summary) {
			companion.hidden = true;
			return;
		}

		var cacheKey = 'nanocat-ai-summary-' + postId;
		var cached = '';
		try {
			cached = window.sessionStorage.getItem(cacheKey) || '';
		} catch (error) {
			/* Ignore. */
		}

		function paint(text) {
			summary.innerHTML = '';
			String(text).split(/\n{2,}/).forEach(function (paragraph) {
				if (!paragraph.trim()) {
					return;
				}
				var p = document.createElement('p');
				p.textContent = paragraph.trim();
				summary.appendChild(p);
			});
			companion.classList.add('is-loaded');
		}

		if (cached) {
			paint(cached);
			return;
		}

		if (!API) {
			companion.hidden = true;
			return;
		}

		fetch(API + '/summary?post=' + encodeURIComponent(postId) + '&t=' + Date.now())
			.then(function (response) {
				if (!response.ok) {
					throw new Error('HTTP ' + response.status);
				}
				return response.json();
			})
			.then(function (payload) {
				if (!payload || !payload.summary) {
					throw new Error('empty');
				}
				try {
					window.sessionStorage.setItem(cacheKey, payload.summary);
				} catch (error) {
					/* Ignore. */
				}
				paint(payload.summary);
			})
			.catch(function () {
				companion.hidden = true;
			});
	}

	function initSelection() {
		var content = document.querySelector('[data-entry-content]');
		if (!content) {
			return;
		}

		selectionButton = document.createElement('button');
		selectionButton.type = 'button';
		selectionButton.className = 'ai-selection';
		selectionButton.hidden = true;
		selectionButton.innerHTML = '<span aria-hidden="true">✦</span> 问 AI 这段';
		document.body.appendChild(selectionButton);

		var selectedText = '';
		var picking = false;

		function hide() {
			selectionButton.hidden = true;
			selectedText = '';
		}

		function place() {
			if (picking) {
				return;
			}
			picking = true;

			window.setTimeout(function () {
				picking = false;
				var selection = window.getSelection();
				if (!selection || selection.isCollapsed) {
					hide();
					return;
				}

				var text = selection.toString().trim();
				if (text.length < 24 || text.length > 2400) {
					hide();
					return;
				}

				var anchor = selection.anchorNode;
				var element = anchor && anchor.nodeType === 1 ? anchor : anchor && anchor.parentElement;
				if (!element || !content.contains(element)) {
					hide();
					return;
				}

				var range = selection.getRangeAt(0);
				var rect = range.getBoundingClientRect();
				if (!rect.width && !rect.height) {
					hide();
					return;
				}

				selectedText = text;
				var x = rect.left + rect.width / 2;
				var y = rect.top - 42;
				if (y < 72) {
					y = rect.bottom + 9;
				}
				selectionButton.style.left = Math.max(16, Math.min(window.innerWidth - 16, x)) + 'px';
				selectionButton.style.top = y + 'px';
				selectionButton.hidden = false;
			}, 90);
		}

		content.addEventListener('mouseup', place);
		content.addEventListener('touchend', place);

		selectionButton.addEventListener('mousedown', function (event) {
			event.preventDefault();
		});

		selectionButton.addEventListener('click', function () {
			if (!selectedText) {
				return;
			}
			var text = selectedText;
			hide();
			openDialog(
				'请结合站内文章解释下面这段内容，并说明它和上下文的关系：\n\n“' + text.slice(0, 1600) + '”',
				true
			);
		});

		document.addEventListener('mousedown', function (event) {
			if (event.target !== selectionButton && !selectionButton.contains(event.target)) {
				hide();
			}
		});

		window.addEventListener('scroll', hide, { passive: true });
		window.addEventListener('resize', hide);
	}

	function init() {
		initConsoles();
		initDialog();
		initSummary();
		initSelection();
		renderHistory();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
