<?php
/**
 * Royal Vision - filters, sorting and "Load more" for the fashion shop page.
 *
 * Adds behaviour to markup built in Elementor (gen-shop.php) on the page saved
 * in the rv_fashion_shop_id option. Every filter, category circle and menu item
 * is a plain link such as #cat-khaddar, #fab-karandi, #price-u4 or #col-navy,
 * so the page still reads well without JavaScript. Counts come from the cards
 * on the page; prices are read from each card's live WooCommerce price.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wp_footer', function () {
	$page_id = (int) get_option( 'rv_fashion_shop_id' );
	if ( ! $page_id || get_queried_object_id() !== $page_id ) {
		return;
	}
	?>
	<script>
	(function () {
		var main = document.querySelector('.sh-main');
		if (!main) return;
		var grid = main.querySelector('.sh-grid'), results = main.querySelector('.sh-results'), promo = grid.querySelector('.sh-promo'), body = document.body;
		var PAGE = 12;
		var CAT = { all: 'Shop All', khaddar: 'Khaddar', karandi: 'Karandi', 'new': 'New In', best: 'Best Sellers', u4: 'Under Rs 4,000' };
		var PRICE = { any: 'Any price', u4: 'Under Rs 4,000', b45: 'Rs 4,000 – 5,000' };
		var s = { cat: 'all', fab: [], price: 'any', col: [], sort: 'featured', shown: PAGE, cols: 4 };

		function num(t) { return parseInt(String(t).replace(/[^0-9]/g, ''), 10) || 0; }
		function cap(t) { return t.charAt(0).toUpperCase() + t.slice(1); }
		function q(sel, el) { return (el || document).querySelector(sel); }
		function qa(sel, el) { return [].slice.call((el || document).querySelectorAll(sel)); }
		function text(sel, t, html) { qa(sel + ' .elementor-heading-title').forEach(function (e) { if (html) e.innerHTML = t; else e.textContent = t; }); }

		var items = qa('.sh-cell', grid).map(function (el, i) {
			var c = el.className, m = function (re) { var r = c.match(re); return r ? r[1] : ''; },
				pr = q('.v5-price ins .amount', el) || q('.v5-price .amount', el), nm = q('.v5-name .elementor-heading-title', el);
			return { el: el, i: i, fab: m(/sh-fab-(\w+)/), col: m(/sh-col-(\w+)/), isNew: /\bsh-new\b/.test(c), best: /\bsh-best\b/.test(c),
				price: pr ? num(pr.textContent) : (m(/sh-fab-(\w+)/) === 'khaddar' ? 3990 : 4290), name: nm ? nm.textContent.trim() : '' };
		});

		function catOk(k, p) { return k === 'all' || (k === 'khaddar' || k === 'karandi' ? p.fab === k : k === 'new' ? p.isNew : k === 'best' ? p.best : p.price < 4000); }
		function priceOk(k, p) { return k === 'any' || (k === 'u4' ? p.price < 4000 : p.price >= 4000 && p.price <= 5000); }
		function match(p, skip) {
			return (skip === 'cat' || catOk(s.cat, p)) && (skip === 'fab' || !s.fab.length || s.fab.indexOf(p.fab) > -1) &&
				(skip === 'price' || priceOk(s.price, p)) && (skip === 'col' || !s.col.length || s.col.indexOf(p.col) > -1);
		}
		function count(skip, fn) { return items.filter(function (p) { return match(p, skip) && fn(p); }).length; }

		// Ticks, counts and disabled states on every filter option.
		function renderOptions() {
			qa('.sh-opts a').forEach(function (a) {
				var m = (a.getAttribute('href') || '').match(/^#(cat|fab|price|col)-(\w+)$/);
				if (!m) return;
				var k = m[1], v = m[2], li = a.parentNode, on, n;
				if (k === 'cat') { on = s.cat === v; n = count('cat', function (p) { return catOk(v, p); }); }
				if (k === 'price') { on = s.price === v; n = count('price', function (p) { return priceOk(v, p); }); }
				if (k === 'fab') { on = s.fab.indexOf(v) > -1; n = count('fab', function (p) { return p.fab === v; }); }
				if (k === 'col') { on = s.col.indexOf(v) > -1; n = count('col', function (p) { return p.col === v; }); }
				li.classList.toggle('is-on', on);
				li.classList.toggle('is-off', !on && !n && (k === 'fab' || k === 'col'));
				a.setAttribute('role', k === 'cat' || k === 'price' ? 'radio' : 'checkbox');
				a.setAttribute('aria-checked', on ? 'true' : 'false');
				var c = q('.sh-n', a);
				if (!c) { c = document.createElement('span'); c.className = 'sh-n'; (k === 'col' ? q('.elementor-icon-list-text', a) : a).appendChild(c); }
				c.textContent = (k === 'col' ? ' ' : '') + n;
			});
			[['sh-g-fab', s.fab.length], ['sh-g-col', s.col.length]].forEach(function (g) {
				var t = q('.' + g[0] + ' .sh-gtitle .elementor-heading-title'), b = t && q('.sh-badge', t);
				if (!t) return;
				if (g[1] && !b) { b = document.createElement('span'); b.className = 'sh-badge'; t.appendChild(b); }
				if (b) { if (g[1]) b.textContent = g[1]; else b.remove(); }
			});
		}

		function chips() {
			var list = [];
			if (s.cat !== 'all') list.push([CAT[s.cat], function () { s.cat = 'all'; }]);
			s.fab.forEach(function (f) { list.push([cap(f), function () { s.fab = s.fab.filter(function (x) { return x !== f; }); }]); });
			if (s.price !== 'any') list.push([PRICE[s.price], function () { s.price = 'any'; }]);
			s.col.forEach(function (c) { list.push([cap(c), function () { s.col = s.col.filter(function (x) { return x !== c; }); }]); });
			return list;
		}

		function render() {
			var res = items.filter(function (p) { return match(p); });
			var by = { low: function (a, b) { return a.price - b.price || a.i - b.i; }, high: function (a, b) { return b.price - a.price || a.i - b.i; },
				az: function (a, b) { return a.name.localeCompare(b.name); }, 'new': function (a, b) { return (b.isNew - a.isNew) || b.i - a.i; } }[s.sort];
			if (by) res = res.slice().sort(by);
			var shown = Math.min(s.shown, res.length);

			// Matching cards first (in sort order), the rest hidden at the end; the bundle tile sits after the 6th card.
			res.concat(items.filter(function (p) { return res.indexOf(p) < 0; })).forEach(function (p, k) {
				grid.appendChild(p.el);
				p.el.classList.toggle('is-hidden', k >= shown);
				if (k === 5 && promo) grid.appendChild(promo);
			});
			if (promo) promo.classList.toggle('is-hidden', shown < 7);

			renderOptions();
			qa('.sh-cat').forEach(function (a) {
				var k = (a.getAttribute('href') || '').replace('#cat-', '');
				a.classList.toggle('is-on', k === s.cat);
				a.setAttribute('aria-current', k === s.cat ? 'true' : 'false');
				var c = q('.sh-cat-count .elementor-heading-title', a);
				if (c) c.textContent = items.filter(function (p) { return catOk(k, p); }).length + ' styles';
			});
			text('.sh-title', CAT[s.cat]);
			qa('.sh-crumb-cur').forEach(function (e) { e.textContent = CAT[s.cat]; });
			text('.sh-sub', items.filter(function (p) { return catOk(s.cat, p); }).length + ' unstitched 3-piece winter suits · Free delivery all over Pakistan');
			text('.sh-count', '<b>' + res.length + '</b> ' + (res.length === 1 ? 'suit' : 'suits') + ' found', true);
			qa('.sh-show .elementor-button-text').forEach(function (e) { e.textContent = 'Show ' + res.length + (res.length === 1 ? ' suit' : ' suits'); });

			var list = chips(), box = q('.sh-chips');
			body.classList.toggle('sh-has-filters', list.length > 0);
			qa('.sh-fbtn .elementor-button-text').forEach(function (e) { e.textContent = 'Filters' + (list.length ? ' (' + list.length + ')' : ''); });
			if (box) {
				box.innerHTML = '';
				list.forEach(function (c) {
					var b = document.createElement('button');
					b.type = 'button'; b.className = 'sh-chip'; b.setAttribute('aria-label', 'Remove ' + c[0]);
					b.innerHTML = '<span></span><i aria-hidden="true">×</i>'; b.firstChild.textContent = c[0];
					b.addEventListener('click', function () { c[1](); s.shown = PAGE; render(); });
					box.appendChild(b);
				});
				if (list.length) {
					var x = document.createElement('button');
					x.type = 'button'; x.className = 'sh-chip-clear'; x.textContent = 'Clear all';
					x.addEventListener('click', clearAll);
					box.appendChild(x);
				}
			}

			results.classList.toggle('is-empty', !res.length);
			text('.sh-prog-txt', "You've viewed " + shown + ' of ' + res.length + ' suits');
			qa('.sh-prog-fill').forEach(function (e) { e.style.setProperty('width', (res.length ? Math.round(shown / res.length * 100) : 0) + '%', 'important'); });
			qa('.sh-more').forEach(function (e) { e.classList.toggle('is-done', shown >= res.length); });

			grid.classList.toggle('is-3', s.cols === 3);
			qa('.sh-col3, .sh-col4').forEach(function (e) {
				var on = e.classList.contains('sh-col' + s.cols), a = q('a', e);
				e.classList.toggle('is-on', on);
				if (a) { a.setAttribute('aria-pressed', on ? 'true' : 'false'); a.setAttribute('aria-label', (e.classList.contains('sh-col3') ? 3 : 4) + ' per row'); }
			});
		}

		function clearAll() { s.cat = 'all'; s.fab = []; s.price = 'any'; s.col = []; s.shown = PAGE; render(); }
		function toResults() {
			var bar = q('.sh-bar') || results, head = q('.v5-head'), off = (head ? Math.min(head.offsetHeight, 90) : 70) + 8;
			window.scrollTo({ top: bar.getBoundingClientRect().top + window.pageYOffset - off, behavior: 'smooth' });
		}
		function drawer(open) {
			body.classList.toggle('sh-open', open);
			if (open) { var c = q('.sh-close a'); if (c) c.focus(); }
		}
		function apply(k, v, fromPage) {
			if (k === 'cols') { s.cols = v === '3' ? 3 : 4; render(); return; }
			if (k === 'cat') s.cat = CAT[v] ? v : 'all';
			if (k === 'price') s.price = PRICE[v] ? v : 'any';
			if (k === 'fab' || k === 'col') { var arr = s[k], i = arr.indexOf(v); if (i > -1) arr.splice(i, 1); else arr.push(v); }
			s.shown = PAGE;
			render();
			if (fromPage) { drawer(false); toResults(); }
		}

		document.addEventListener('click', function (e) {
			var t = e.target.closest('.sh-gtitle .elementor-heading-title');
			if (t) { var g = t.closest('.sh-group'); g.classList.toggle('is-closed'); t.setAttribute('aria-expanded', g.classList.contains('is-closed') ? 'false' : 'true'); return; }
			var a = e.target.closest('a');
			if (!a) return;
			var h = a.getAttribute('href') || '', m = h.match(/^#(cat|fab|price|col|cols)-(\w+)$/);
			if (m) { e.preventDefault(); apply(m[1], m[2], !a.closest('.sh-aside') && m[1] !== 'cols'); return; }
			if (h === '#clear') { e.preventDefault(); clearAll(); return; }
			if (h === '#more') { e.preventDefault(); s.shown += PAGE; render(); return; }
			if (h === '#open-filters') { e.preventDefault(); drawer(true); return; }
			if (h === '#close') { e.preventDefault(); drawer(false); }
		});
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && body.classList.contains('sh-open')) drawer(false);
			var t = e.target.closest && e.target.closest('.sh-gtitle .elementor-heading-title');
			if (t && (e.key === 'Enter' || e.key === ' ')) { e.preventDefault(); t.click(); }
		});
		// The sort control is an Elementor form: never submit it, just re-sort.
		document.addEventListener('submit', function (e) { if (e.target.closest('.sh-sort')) { e.preventDefault(); e.stopImmediatePropagation(); } }, true);
		qa('.sh-sort select').forEach(function (sel) { sel.addEventListener('change', function () { s.sort = sel.value; s.shown = PAGE; render(); }); });

		qa('.sh-gtitle .elementor-heading-title').forEach(function (t) { t.setAttribute('role', 'button'); t.setAttribute('tabindex', '0'); t.setAttribute('aria-expanded', 'true'); });
		qa('.sh-close a, .sh-backdrop').forEach(function (a) { a.setAttribute('aria-label', 'Close filters'); });
		qa('.sh-fbtn a').forEach(function (a) { a.setAttribute('href', '#open-filters'); a.setAttribute('aria-controls', 'filters'); });

		var start = (location.hash || '').match(/^#(cat|fab|price|col)-(\w+)$/);
		if (start) { apply(start[1], start[2], false); setTimeout(toResults, 300); } else render();
	})();
	</script>
	<?php
}, 30 );
