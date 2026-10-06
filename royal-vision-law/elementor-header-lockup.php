<?php
/**
 * Professional header for royalvisionlaw.com: logo lockup and right-aligned menu.
 * - Header template text becomes a two-line lockup: "Royal Vision" / "Law Associate"
 * - Scales mark in a gold-ringed circle, uppercase serif name, letter-spaced gold sub-line
 * - Menu toggle pinned to the right edge on tablet and mobile
 * Run after elementor-mobile-polish.php.
 */
$tpl_id = (int) get_posts( array( 'post_type' => 'elementor_library', 'meta_key' => '_rvl_key', 'meta_value' => 'tpl-header', 'numberposts' => 1, 'fields' => 'ids' ) )[0];
$tdoc   = \Elementor\Plugin::$instance->documents->get( $tpl_id, false );
$has    = function ( $el, $class ) { $c = $el['settings']['css_classes'] ?? ( $el['settings']['_css_classes'] ?? '' ); return in_array( $class, preg_split( '/\s+/', $c ), true ); };
$home   = array( 'url' => home_url( '/' ), 'is_external' => '', 'nofollow' => '', 'custom_attributes' => '' );
$walk   = function ( array $els ) use ( &$walk, $has, $home ) {
	foreach ( $els as &$el ) {
		if ( 'heading' === ( $el['widgetType'] ?? '' ) && $has( $el, 'rvl-brand-name' ) ) { $el['settings']['title'] = 'Royal Vision'; }
		if ( 'heading' === ( $el['widgetType'] ?? '' ) && $has( $el, 'rvl-brand-sub' ) ) { $el['settings']['title'] = 'Law Associate'; $el['settings']['link'] = $home; }
		if ( 'icon' === ( $el['widgetType'] ?? '' ) && $has( $el, 'rvl-brand-icon' ) ) { $el['settings']['link'] = $home; }
		$el['elements'] = $walk( $el['elements'] );
	}
	return $els;
};
$tdoc->save( array( 'elements' => $walk( $tdoc->get_elements_data() ) ) );

$kit_doc = \Elementor\Plugin::$instance->documents->get( (int) get_option( 'elementor_active_kit' ), false );
$kit_set = array_filter( (array) $kit_doc->get_settings(), function ( $k ) { return ! is_int( $k ); }, ARRAY_FILTER_USE_KEY );
$add = <<<'CSS'

/* Professional header: logo lockup + right-aligned menu */
selector .rvl-header{box-shadow:0 8px 24px -20px rgba(15,38,33,.55)}
selector .rvl-header .rvl-brand{gap:14px;--gap:14px;align-items:center}
selector .rvl-header .rvl-brand-icon .elementor-icon{width:48px;height:48px;border:1px solid var(--rvl-gold);border-radius:50%;display:flex;align-items:center;justify-content:center;background:#FFFFFF}
selector .rvl-header .rvl-brand-icon .elementor-icon svg{width:22px;height:22px;fill:var(--rvl-gold-ink)}
selector .rvl-header .rvl-brand-text{gap:5px;--gap:5px}
selector .rvl-header .rvl-brand-name .elementor-heading-title,selector .rvl-header .rvl-brand-name .elementor-heading-title a{font-family:var(--rvl-serif);font-size:21px;line-height:1;font-weight:400;letter-spacing:.08em;text-transform:uppercase;color:var(--rvl-ink);white-space:nowrap}
selector .rvl-header .rvl-brand-sub{display:block}
selector .rvl-header .rvl-brand-sub .elementor-heading-title,selector .rvl-header .rvl-brand-sub .elementor-heading-title a{font-family:var(--rvl-sans);font-size:11px;line-height:1;font-weight:600;letter-spacing:.38em;text-transform:uppercase;color:var(--rvl-gold-ink);white-space:nowrap}
selector .rvl-header .rvl-brand-sub .elementor-heading-title::before{content:none}
selector .rvl-header .rvl-navwrap{margin-left:auto;justify-content:flex-end}
selector .rvl-header .rvl-nav{margin-left:auto}
selector .rvl-nav .elementor-menu-toggle{margin:0 0 0 auto;width:46px;height:46px;border:1px solid var(--rvl-line);border-radius:2px;background:#FFFFFF;color:var(--rvl-ink);transition:border-color .15s ease,background-color .15s ease}
selector .rvl-nav .elementor-menu-toggle:hover,selector .rvl-nav .elementor-menu-toggle.elementor-active{border-color:var(--rvl-gold);background:var(--rvl-stone)}
selector .rvl-nav .elementor-menu-toggle svg{fill:var(--rvl-ink);width:22px;height:22px}
@media (max-width:767px){
selector .rvl-header .rvl-brand-sub{display:block}
selector .rvl-header .rvl-brand{gap:11px;--gap:11px}
selector .rvl-header .rvl-brand-icon .elementor-icon{width:40px;height:40px}
selector .rvl-header .rvl-brand-icon .elementor-icon svg{width:19px;height:19px}
selector .rvl-header .rvl-brand-name .elementor-heading-title,selector .rvl-header .rvl-brand-name .elementor-heading-title a{font-size:17px}
selector .rvl-header .rvl-brand-sub .elementor-heading-title,selector .rvl-header .rvl-brand-sub .elementor-heading-title a{font-size:9.5px;letter-spacing:.34em}
selector .rvl-nav .elementor-menu-toggle{width:42px;height:42px}
}
CSS;
if ( false === strpos( $kit_set['custom_css'], '/* Professional header:' ) ) { $kit_set['custom_css'] .= $add; }
$kit_doc->save( array( 'settings' => $kit_set ) );
\Elementor\Plugin::$instance->files_manager->clear_cache();
do_action( 'litespeed_purge_all' );
return 'ok';
