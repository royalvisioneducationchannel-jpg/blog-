<?php
/**
 * Calm the motion on royalvisionlaw.com (replaces the scroll-entrance animations).
 * Elementor entrance animations hide content until JavaScript reveals it; on the live
 * site some blocks (e.g. the legal-maxim band) stayed hidden. This removes every
 * entrance animation, shows counters at their final value, stops the WhatsApp pulse,
 * and keeps one CSS-only hero entrance that never hides content if it fails.
 * Run after elementor-motion.php.
 */

$home_id = 22;
$doc     = \Elementor\Plugin::$instance->documents->get( $home_id, false );
$removed = 0;
$strip   = function ( array $els ) use ( &$strip, &$removed ) {
	foreach ( $els as &$el ) {
		foreach ( array( 'animation', 'animation_delay', '_animation', '_animation_delay', 'animation_duration' ) as $k ) {
			if ( isset( $el['settings'][ $k ] ) ) { unset( $el['settings'][ $k ] ); $removed++; }
		}
		if ( 'counter' === ( $el['widgetType'] ?? '' ) ) { $el['settings']['starting_number'] = $el['settings']['ending_number']; }
		$el['elements'] = $strip( $el['elements'] );
	}
	return $els;
};
$doc->save( array( 'elements' => $strip( $doc->get_elements_data() ), 'settings' => array( 'template' => 'elementor_canvas', 'hide_title' => 'yes' ) ) );

$kit_doc = \Elementor\Plugin::$instance->documents->get( (int) get_option( 'elementor_active_kit' ), false );
$kit_set = array_filter( (array) $kit_doc->get_settings(), function ( $k ) { return ! is_int( $k ); }, ARRAY_FILTER_USE_KEY );
$add = <<<'CSS'

/* Calm motion: CSS-only hero entrance, no hidden content, no constant pulse */
@keyframes rvl-up{from{opacity:0;transform:translateY(18px)}to{opacity:1;transform:none}}
selector .rvl-hero-dark .rvl-eyebrow,selector .rvl-hero-dark .rvl-h1,selector .rvl-hero-dark .rvl-lead,selector .rvl-hero-dark .rvl-actions{animation:rvl-up .8s cubic-bezier(.2,.7,.2,1) both}
selector .rvl-hero-dark .rvl-h1{animation-delay:.12s}
selector .rvl-hero-dark .rvl-lead{animation-delay:.24s}
selector .rvl-hero-dark .rvl-actions{animation-delay:.36s}
selector .rvl-wa-icon .elementor-icon{animation:none}
selector .rvl-btn .elementor-button:hover::before{animation:none}
@media (prefers-reduced-motion:reduce){selector .rvl-hero-dark .rvl-eyebrow,selector .rvl-hero-dark .rvl-h1,selector .rvl-hero-dark .rvl-lead,selector .rvl-hero-dark .rvl-actions{animation:none}}
CSS;
if ( false === strpos( $kit_set['custom_css'], '/* Calm motion:' ) ) { $kit_set['custom_css'] .= $add; }
$kit_doc->save( array( 'settings' => $kit_set ) );

\Elementor\Plugin::$instance->files_manager->clear_cache();
do_action( 'litespeed_purge_all' );

$h = wp_remote_retrieve_body( wp_remote_get( add_query_arg( 'nc', time(), home_url( '/' ) ), array( 'timeout' => 30, 'sslverify' => false ) ) );
preg_match_all( '#elementor-counter-number" data-duration="\d+" data-to-value="(\d+)" data-from-value="(\d+)"#', $h, $c );
return array(
	'settings_removed' => $removed,
	'invisible_left'   => substr_count( $h, 'elementor-invisible' ),
	'quote_visible'    => false !== strpos( $h, 'Fiat justitia ruat caelum' ),
	'counters'         => array_map( null, $c[1], $c[2] ),
);
