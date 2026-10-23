<?php
/**
 * Royal Vision - "Any 2 for Rs 6,990" bundle builder on the fashion home page.
 *
 * Runs on the fashion home page and the fashion shop page (same card markup).
 * Adds behaviour to markup built in Elementor: the "+ Bundle" button on each
 * product card (.v5-pick) and the bottom tray container (.v5-tray). A shopper
 * picks two suits, sees the real saving against the two current prices, and
 * sends both names in one WhatsApp message. Picks survive a page reload for
 * the browser session only.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wp_footer', function () {
	$pages = array_filter( array( (int) get_option( 'rv_fashion_home_id' ), (int) get_option( 'rv_fashion_shop_id' ) ) );
	if ( ! in_array( get_queried_object_id(), $pages, true ) ) {
		return;
	}
	?>
	<script>
	(function () {
		var WA = '923227782932', BUNDLE = 6990, KEY = 'rvBundle';
		var tray = document.querySelector('.v5-tray');
		if (!tray) return;
		var slots = tray.querySelector('.v5-tray-slots'),
			msg = tray.querySelector('.v5-tray-msg .elementor-heading-title'),
			tot = tray.querySelector('.v5-tray-total .elementor-heading-title'),
			go = tray.querySelector('.v5-tray-go a'),
			picks = [];
		try { picks = JSON.parse(sessionStorage.getItem(KEY) || '[]').slice(0, 2); } catch (e) {}

		function num(s) { return parseInt(String(s).replace(/[^0-9]/g, ''), 10) || 0; }
		function fmt(n) { return 'Rs ' + n.toLocaleString('en-US'); }
		function data(card) {
			var n = card.querySelector('.v5-name .elementor-heading-title'),
				p = card.querySelector('.v5-price ins .amount') || card.querySelector('.v5-price .amount'),
				i = card.querySelector('.v5-front img');
			return { name: n ? n.textContent.trim() : '', price: p ? num(p.textContent) : 0, img: i ? (i.currentSrc || i.src) : '' };
		}
		function has(name) { return picks.some(function (p) { return p.name === name; }); }

		function render() {
			document.querySelectorAll('.v5-card').forEach(function (c) {
				var on = has(data(c).name), b = c.querySelector('.v5-pick .elementor-button-text'), a = c.querySelector('.v5-pick a');
				c.classList.toggle('is-picked', on);
				if (b) b.textContent = on ? '✓ In bundle' : '+ Bundle';
				if (a) { a.setAttribute('role', 'button'); a.setAttribute('aria-pressed', on ? 'true' : 'false'); }
			});
			tray.classList.toggle('is-open', picks.length > 0);
			slots.innerHTML = '';
			for (var k = 0; k < 2; k++) {
				var s = document.createElement('span');
				s.className = 'v5-slot' + (picks[k] ? ' is-full' : '');
				if (picks[k]) { s.style.backgroundImage = 'url("' + picks[k].img.replace(/"/g, '') + '")'; s.title = picks[k].name; }
				else { s.textContent = '+'; }
				slots.appendChild(s);
			}
			if (picks.length < 2) {
				msg.textContent = picks.length ? picks[0].name + ' + pick 1 more' : 'Pick any 2 suits';
				tot.innerHTML = 'Any 2 for <b>' + fmt(BUNDLE) + '</b>';
				go.classList.add('is-disabled');
				go.setAttribute('aria-disabled', 'true');
			} else {
				var sum = picks[0].price + picks[1].price, save = sum - BUNDLE;
				msg.textContent = picks[0].name + ' + ' + picks[1].name;
				tot.innerHTML = (save > 0 ? '<s>' + fmt(sum) + '</s> ' : '') + '<b>' + fmt(BUNDLE) + '</b>' + (save > 0 ? ' · You save ' + fmt(save) : '');
				go.classList.remove('is-disabled');
				go.removeAttribute('aria-disabled');
			}
			go.href = 'https://wa.me/' + WA + '?text=' + encodeURIComponent('Assalam o Alaikum, mujhe 2 suits ka bundle (Rs 6,990) chahiye: ' + picks.map(function (p) { return p.name; }).join(' + '));
			try { sessionStorage.setItem(KEY, JSON.stringify(picks)); } catch (e) {}
		}

		document.addEventListener('click', function (e) {
			var a = e.target.closest('.v5-pick a');
			if (a) {
				e.preventDefault();
				var d = data(a.closest('.v5-card')), i = -1;
				picks.forEach(function (p, k) { if (p.name === d.name) i = k; });
				if (i > -1) picks.splice(i, 1); else { if (picks.length >= 2) picks.shift(); picks.push(d); }
				render();
				tray.classList.remove('pulse'); void tray.offsetWidth; tray.classList.add('pulse');
				return;
			}
			if (e.target.closest('.v5-tray-clear a')) { e.preventDefault(); picks = []; render(); return; }
			if (e.target.closest('.v5-tray-go a') && picks.length < 2) e.preventDefault();
		});
		render();
	})();
	</script>
	<?php
}, 30 );
