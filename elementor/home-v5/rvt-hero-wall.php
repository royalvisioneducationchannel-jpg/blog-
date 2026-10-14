<?php
/**
 * Royal Vision - hero colour sync for the fashion home page.
 *
 * The hero (.v6-hero) changes its background tint to match the active slide
 * of its Elementor carousel. Everything else in the hero is CSS.
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
	</script>
	<?php
}, 30 );
