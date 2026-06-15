/* IphoneBayKE — theme interactions */
(function () {
	'use strict';

	document.addEventListener('DOMContentLoaded', function () {

		/* ---- HERO SLIDER ---- */
		var hero = document.getElementById('hero');
		if (hero) {
			var slides = hero.querySelectorAll('.hero-slide');
			var dots   = hero.querySelectorAll('.hero-dot');
			if (slides.length > 1) {
				var cur = 0, timer;
				var go = function (idx) {
					slides[cur].classList.remove('active');
					if (dots[cur]) { dots[cur].classList.remove('active'); dots[cur].setAttribute('aria-selected', 'false'); }
					cur = ((idx % slides.length) + slides.length) % slides.length;
					slides[cur].classList.add('active');
					if (dots[cur]) { dots[cur].classList.add('active'); dots[cur].setAttribute('aria-selected', 'true'); }
					reset();
				};
				var reset = function () { clearInterval(timer); timer = setInterval(function () { go(cur + 1); }, 5200); };
				dots.forEach(function (dot, i) { dot.addEventListener('click', function () { go(i); }); });
				var sx = 0;
				hero.addEventListener('touchstart', function (e) { sx = e.touches[0].clientX; }, { passive: true });
				hero.addEventListener('touchend', function (e) {
					var dx = e.changedTouches[0].clientX - sx;
					if (Math.abs(dx) > 40) { go(cur + (dx < 0 ? 1 : -1)); }
				});
				hero.addEventListener('mouseenter', function () { clearInterval(timer); });
				hero.addEventListener('mouseleave', reset);
				reset();
			}
		}

		/* ---- NAV SCROLL ---- */
		var nav = document.getElementById('nav');
		if (nav) {
			var onScroll = function () { nav.classList.toggle('scrolled', window.scrollY > 20); };
			window.addEventListener('scroll', onScroll, { passive: true });
			onScroll();
		}

		/* ---- OFF-CANVAS MENU ---- */
		var hamburger = document.getElementById('hamburger');
		var offcanvas = document.getElementById('offcanvas');
		var backdrop  = document.getElementById('backdrop');
		var openMenu = function () {
			if (!offcanvas) return;
			if (window.iphonebayCloseSearch) window.iphonebayCloseSearch();
			offcanvas.classList.add('open');
			if (backdrop) backdrop.classList.add('open');
			if (hamburger) { hamburger.classList.add('open'); hamburger.setAttribute('aria-expanded', 'true'); }
			document.body.style.overflow = 'hidden';
		};
		var closeMenu = function () {
			if (!offcanvas) return;
			offcanvas.classList.remove('open');
			if (backdrop) backdrop.classList.remove('open');
			if (hamburger) { hamburger.classList.remove('open'); hamburger.setAttribute('aria-expanded', 'false'); }
			document.body.style.overflow = '';
		};
		if (hamburger) hamburger.addEventListener('click', openMenu);
		if (backdrop) backdrop.addEventListener('click', closeMenu);
		document.querySelectorAll('[data-close-menu]').forEach(function (el) { el.addEventListener('click', closeMenu); });
		document.querySelectorAll('.offcanvas-body a').forEach(function (a) { a.addEventListener('click', closeMenu); });

		/* ---- MOBILE SUBMENU ACCORDION ---- */
		document.querySelectorAll('.offcanvas-expand').forEach(function (btn) {
			btn.addEventListener('click', function (e) {
				e.preventDefault();
				e.stopPropagation();
				var group = btn.closest('.offcanvas-group');
				if (!group) return;
				var sub = group.querySelector('.offcanvas-sub');
				var open = btn.classList.toggle('open');
				btn.setAttribute('aria-expanded', open ? 'true' : 'false');
				if (sub) sub.classList.toggle('open', open);
			});
		});

		/* ---- SEARCH OVERLAY ---- */
		var searchOverlay = document.getElementById('siteSearch');
		var searchToggle = document.getElementById('searchToggle');
		var searchInput = document.getElementById('siteSearchInput');
		var searchResults = document.getElementById('siteSearchResults');
		var searchStatus = document.getElementById('siteSearchStatus');
		var searchBrowse = document.getElementById('siteSearchBrowse');
		var searchForm = document.getElementById('siteSearchForm');
		var searchTimer;
		var searchController;
		var searchIsOpen = false;
		var searchStrings = (window.IphoneBayData && window.IphoneBayData.strings) || {};
		var searchEndpoint = window.IphoneBayData && window.IphoneBayData.searchEndpoint;
		var searchUrl = (window.IphoneBayData && window.IphoneBayData.searchUrl) || '';

		function escapeHtml(str) {
			return String(str || '').replace(/[&<>"']/g, function (ch) {
				return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[ch];
			});
		}

		function updateSearchBrowseLink(query) {
			if (!searchBrowse || !searchUrl) return;
			var nextUrl = searchUrl;
			if (query) {
				nextUrl += (searchUrl.indexOf('?') === -1 ? '?' : '&') + 's=' + encodeURIComponent(query);
			}
			searchBrowse.href = nextUrl;
		}

		function renderSearchResults(payload) {
			if (!searchResults || !searchStatus) return;
			var results = (payload && payload.results) || [];
			var query = (payload && payload.query) || '';
			var label = payload && payload.isDefault ? (searchStrings.searchPopular || 'Popular right now') : (query ? 'Results for "' + query + '"' : (searchStrings.searchPopular || 'Popular right now'));
			searchStatus.textContent = label;
			updateSearchBrowseLink(query);

			if (!results.length) {
				searchResults.innerHTML = '<div class="search-empty">' + escapeHtml(searchStrings.searchNoMatch || 'No matching products found.') + '</div>';
				return;
			}

			searchResults.innerHTML = results.map(function (item) {
				var badge = item.badge ? '<span class="search-result-badge ' + escapeHtml(item.badgeClass || '') + '">' + escapeHtml(item.badge) + '</span>' : '';
				var meta = item.meta ? '<p class="search-result-meta">' + escapeHtml(item.meta) + '</p>' : '';
				return '' +
					'<a class="search-result-card" href="' + escapeHtml(item.url) + '">' +
						'<div class="search-result-media">' +
							badge +
							'<img src="' + escapeHtml(item.image) + '" alt="' + escapeHtml(item.name) + '" loading="lazy">' +
						'</div>' +
						'<div class="search-result-body">' +
							'<h3 class="search-result-name">' + escapeHtml(item.name) + '</h3>' +
							meta +
							'<div class="search-result-price">' + (item.price_html || '') + '</div>' +
						'</div>' +
					'</a>';
			}).join('');
		}

		function fetchSearchResults(query) {
			if (!searchEndpoint || !searchResults || !searchStatus) return;
			if (searchController) searchController.abort();
			searchController = window.AbortController ? new AbortController() : null;
			searchStatus.textContent = searchStrings.searchLoading || 'Searching products…';
			searchResults.innerHTML = '<div class="search-empty">' + escapeHtml(searchStrings.searchLoading || 'Searching products…') + '</div>';
			updateSearchBrowseLink(query);

			fetch(searchEndpoint + '?q=' + encodeURIComponent(query || ''), {
				signal: searchController ? searchController.signal : undefined,
				credentials: 'same-origin'
			})
				.then(function (response) { return response.ok ? response.json() : Promise.reject(new Error('search_failed')); })
				.then(function (payload) {
					renderSearchResults(payload);
				})
				.catch(function (error) {
					if (error && error.name === 'AbortError') return;
					searchStatus.textContent = searchStrings.searchNoMatch || 'No matching products found.';
					searchResults.innerHTML = '<div class="search-empty">' + escapeHtml(searchStrings.searchNoMatch || 'No matching products found.') + '</div>';
				});
		}

		function openSearch() {
			if (!searchOverlay) return;
			closeMenu();
			if (window.iphonebayCloseFilters) window.iphonebayCloseFilters();
			searchIsOpen = true;
			searchOverlay.hidden = false;
			searchOverlay.setAttribute('aria-hidden', 'false');
			document.body.style.overflow = 'hidden';
			if (searchToggle) searchToggle.setAttribute('aria-expanded', 'true');
			window.requestAnimationFrame(function () {
				searchOverlay.classList.add('is-open');
			});
			window.setTimeout(function () {
				if (searchInput) searchInput.focus();
			}, 80);
			fetchSearchResults(searchInput ? searchInput.value.trim() : '');
		}

		function closeSearch() {
			if (!searchOverlay || !searchIsOpen) return;
			searchIsOpen = false;
			searchOverlay.classList.remove('is-open');
			searchOverlay.setAttribute('aria-hidden', 'true');
			if (searchToggle) searchToggle.setAttribute('aria-expanded', 'false');
			if (searchController) searchController.abort();
			window.setTimeout(function () {
				if (!searchIsOpen) searchOverlay.hidden = true;
			}, 280);
			document.body.style.overflow = '';
		}

		window.iphonebayCloseSearch = closeSearch;

		if (searchToggle) {
			searchToggle.addEventListener('click', function () {
				if (searchIsOpen) closeSearch();
				else openSearch();
			});
		}
		if (searchOverlay) {
			searchOverlay.querySelectorAll('[data-close-search]').forEach(function (btn) {
				btn.addEventListener('click', closeSearch);
			});
		}
		if (searchInput) {
			searchInput.addEventListener('input', function () {
				var query = searchInput.value.trim();
				window.clearTimeout(searchTimer);
				searchTimer = window.setTimeout(function () {
					fetchSearchResults(query);
				}, query ? 140 : 0);
			});
		}
		if (searchForm) {
			searchForm.addEventListener('submit', function (event) {
				var query = searchInput ? searchInput.value.trim() : '';
				if (!query || !searchUrl) return;
				event.preventDefault();
				window.location.href = searchUrl + '&s=' + encodeURIComponent(query);
			});
		}

		/* ---- PRODUCT CAROUSELS ---- */
		document.querySelectorAll('.row-carousel').forEach(function (rc) {
			var vp = rc.querySelector('.carousel-viewport');
			var controls = rc.querySelector('.carousel-controls');
			if (!vp || !controls) return;
			var prev = controls.querySelector('[data-dir="-1"]');
			var next = controls.querySelector('[data-dir="1"]');
			var update = function () {
				var max = vp.scrollWidth - vp.clientWidth - 2;
				if (prev) prev.disabled = vp.scrollLeft <= 2;
				if (next) next.disabled = vp.scrollLeft >= max;
			};
			controls.querySelectorAll('.carousel-arrow').forEach(function (btn) {
				btn.addEventListener('click', function () {
					var dir = parseInt(btn.dataset.dir, 10);
					vp.scrollBy({ left: dir * vp.clientWidth * 0.85, behavior: 'smooth' });
				});
			});
			vp.addEventListener('scroll', update, { passive: true });
			window.addEventListener('resize', update);
			update();
		});

		/* ---- SHOP FILTER DRAWER (mobile) ---- */
		var sidebar = document.getElementById('shopSidebar');
		var filterBackdrop = document.getElementById('filterBackdrop');
		var filterToggle = document.getElementById('filterToggle');
		window.iphonebayOpenFilters = function () {
			if (!sidebar) return;
			sidebar.classList.add('open');
			if (filterBackdrop) filterBackdrop.classList.add('open');
			document.body.style.overflow = 'hidden';
		};
		window.iphonebayCloseFilters = function () {
			if (!sidebar) return;
			sidebar.classList.remove('open');
			if (filterBackdrop) filterBackdrop.classList.remove('open');
			document.body.style.overflow = '';
		};
		if (filterToggle) filterToggle.addEventListener('click', window.iphonebayOpenFilters);
		if (filterBackdrop) filterBackdrop.addEventListener('click', window.iphonebayCloseFilters);

		/* Convert filter_*[] checkboxes into WooCommerce comma-separated params on submit */
		var filterForm = document.querySelector('.shop-sidebar form');
		if (filterForm) {
			filterForm.addEventListener('submit', function () {
				var groups = {};
				filterForm.querySelectorAll('input[type="checkbox"][name$="[]"]').forEach(function (cb) {
					var base = cb.name.slice(0, -2);
					if (cb.checked) { (groups[base] = groups[base] || []).push(cb.value); }
					cb.disabled = true; // keep array names out of the query string
				});
				Object.keys(groups).forEach(function (base) {
					var h = document.createElement('input');
					h.type = 'hidden';
					h.name = base;
					h.value = groups[base].join(',');
					filterForm.appendChild(h);
				});
				// Drop empty price fields so they don't clutter the URL.
				filterForm.querySelectorAll('input[name="min_price"], input[name="max_price"]').forEach(function (inp) {
					if (!inp.value) { inp.disabled = true; }
				});
			});
		}

		/* ---- ESC closes overlays ---- */
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape') {
				closeMenu();
				window.iphonebayCloseFilters();
				closeSearch();
			}
		});

		/* ---- QUANTITY STEPPERS (single product + cart) ---- */
		document.querySelectorAll('.woocommerce .quantity').forEach(function (q) {
			var input = q.querySelector('input.qty');
			if (!input || q.querySelector('.qty-btn')) return;
			var mk = function (cls, txt, label) {
				var b = document.createElement('button');
				b.type = 'button'; b.className = 'qty-btn ' + cls; b.textContent = txt;
				b.setAttribute('aria-label', label); b.setAttribute('tabindex', '-1');
				return b;
			};
			var minus = mk('qty-minus', '−', 'Decrease quantity');
			var plus  = mk('qty-plus', '+', 'Increase quantity');
			q.insertBefore(minus, input);
			q.appendChild(plus);
			var step = function (d) {
				var v = parseFloat(input.value) || 0;
				var min = parseFloat(input.min) || 1;
				var max = input.max ? parseFloat(input.max) : Infinity;
				v = Math.min(max, Math.max(min, v + d));
				input.value = v;
				input.dispatchEvent(new Event('change', { bubbles: true }));
			};
			minus.addEventListener('click', function () { step(-1); });
			plus.addEventListener('click', function () { step(1); });
		});
	});
})();

/* ============================================================
   VARIATION SWATCHES — progressively enhance Woo's <select>s into
   pills / colour dots while keeping the variations_form functional.
   ============================================================ */
(function () {
	'use strict';
	if (!window.jQuery) return;
	var $ = window.jQuery;
	var colours = (window.IphoneBayData && window.IphoneBayData.colours) || {};

	$(function () {
		$('.variations_form').each(function () {
			var $form = $(this);
			var $selects = $form.find('.variations select');
			if (!$selects.length) return;
			$form.addClass('swatches-active');

			/* Wrapper keeps the picker grid off form.cart itself so form.cart
			   can stay flex for the qty + ATC button row below it. */
			var $picksWrapper = $('<div class="sp-picks-wrapper"></div>');
			$form.find('.single_variation_wrap').before($picksWrapper);

			var groups = [];
			$selects.each(function () {
				var $select = $(this);
				var $row = $select.closest('tr');
				var label = ($row.find('.label label').text() || $select.data('attribute_name') || '').trim();
				var name = ($select.attr('name') || '').toLowerCase();
				var isColour = /colou?r/.test(name) || /colou?r/.test(label.toLowerCase());

				var $pick = $('<div class="sp-pick"></div>');
				var $head = $('<div class="sp-pick-head"><span class="sp-pick-label"></span><span class="sp-pick-value"></span></div>');
				$head.find('.sp-pick-label').text(label);
				var $sw = $('<div class="sp-swatches"></div>');
				$pick.append($head).append($sw);
				$picksWrapper.append($pick);

				groups.push({ $select: $select, $sw: $sw, $value: $head.find('.sp-pick-value'), isColour: isColour });
			});

			function optionText(g, val) {
				var t = '';
				g.$select.find('option').each(function () { if (this.value === val) { t = this.textContent; } });
				return t;
			}

			function build() {
				groups.forEach(function (g) {
					g.$sw.empty();
					g.$select.find('option').each(function () {
						var val = this.value;
						if (!val) return;
						var text = this.textContent;
						var $btn = $('<button type="button" class="sp-swatch"></button>').attr('data-value', val);
						if (g.isColour) {
							var hex = colours[text.toLowerCase()] || colours[val.toLowerCase()] || '#cccccc';
							$btn.addClass('sp-swatch-dot').attr('title', text).attr('aria-label', text).css('--c', hex);
						} else {
							$btn.addClass('sp-swatch-pill').text(text);
						}
						$btn.on('click', function () {
							if ($btn.hasClass('disabled')) return;
							g.$select.val(val).trigger('change');
						});
						g.$sw.append($btn);
					});
				});
				sync();
			}

			function sync() {
				groups.forEach(function (g) {
					var current = g.$select.val();
					var available = {};
					g.$select.find('option').each(function () {
						if (this.value && !this.disabled) available[this.value] = true;
					});
					g.$sw.find('.sp-swatch').each(function () {
						var v = this.getAttribute('data-value');
						$(this).toggleClass('active', !!current && v === current);
						$(this).toggleClass('disabled', !available[v]);
					});
					g.$value.text(current ? optionText(g, current) : '');
				});
			}

			build();
			$form.on('woocommerce_update_variation_values check_variations found_variation', sync);
			$form.on('reset_data', function () { setTimeout(sync, 10); });
		});
	});
})();
