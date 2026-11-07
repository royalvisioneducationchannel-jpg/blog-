<?php
/**
 * Royal Vision - small front-end helpers for the fashion home page.
 *
 * - The hero (.v6-hero) changes its background tint to match the active slide
 *   of its Elementor carousel.
 * - The promise seals (.v7-ring, Elementor Text Path widgets) get a letter
 *   spacing measured in the browser so each ring of text closes exactly.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wp_footer', function () {
	$page_id = (int) get_option( 'rv_fashion_home_id' );
	if ( ! $page_id || get_queried_object_id() !== $page_id ) {
		return;
	}
	?>
	<script>
	(function () {
		var hero = document.querySelector('.v6-hero');
		if (!hero) return;
		var tints = ['#EBDDD2', '#E6DCF3', '#DDE5D9'], last = -1;
		function sync() {
			var a = hero.querySelector('.swiper-slide-active');
			if (!a) return;
			var i = parseInt(a.getAttribute('data-swiper-slide-index'), 10);
			if (isNaN(i)) i = Array.prototype.indexOf.call(a.parentNode.children, a);
			if (i === last) return;
			last = i;
			hero.style.backgroundColor = tints[i % tints.length];
		}
		new MutationObserver(sync).observe(hero, { subtree: true, attributes: true, attributeFilter: ['class'] });
		sync();
	})();
	(function () {
		var NS = 'http://www.w3.org/2000/svg', tries = 0;
		function fit() {
			var left = 0;
			document.querySelectorAll('.v7-ring').forEach(function (w) {
				var svg = w.querySelector('svg'), t = svg && svg.querySelector('text'), tp = t && t.querySelector('textPath'), p = svg && svg.querySelector('path');
				if (!tp || !p) { left++; return; }
				if (svg.getAttribute('data-fit')) return;
				var s = tp.textContent, tmp = document.createElementNS(NS, 'text'), cs = getComputedStyle(t);
				tmp.style.fontFamily = cs.fontFamily; tmp.style.fontWeight = cs.fontWeight; tmp.style.fontSize = cs.fontSize; tmp.style.letterSpacing = '0px';
				tmp.textContent = s; svg.appendChild(tmp);
				var len = tmp.getComputedTextLength(); svg.removeChild(tmp);
				var n = Array.from(s).length, ls = (p.getTotalLength() - len) / n;
				if (n && ls > -2) { t.style.letterSpacing = ls.toFixed(2) + 'px'; svg.setAttribute('data-fit', '1'); }
			});
			if (left && ++tries < 40) setTimeout(fit, 250);
		}
		(document.fonts && document.fonts.ready ? document.fonts.ready : Promise.resolve()).then(fit);
	})();
	</script>
	<?php
}, 30 );
