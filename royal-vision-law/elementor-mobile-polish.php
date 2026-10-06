<?php
/**
 * Mobile polish for royalvisionlaw.com (Site Settings custom CSS).
 * Full-width dropdown menu on tablet/mobile even before Elementor's JS stretches it,
 * a single-line brand in the mobile header, and a smaller WhatsApp button.
 * Run after elementor-calm-motion.php.
 */
$kit_doc = \Elementor\Plugin::$instance->documents->get( (int) get_option( 'elementor_active_kit' ), false );
$kit_set = array_filter( (array) $kit_doc->get_settings(), function ( $k ) { return ! is_int( $k ); }, ARRAY_FILTER_USE_KEY );
$add = <<<'CSS'

/* Mobile polish */
@media (max-width:1024px){
selector .rvl-header{position:relative}
selector .rvl-header .rvl-navwrap,selector .rvl-header .rvl-nav,selector .rvl-header .rvl-nav .elementor-widget-container{position:static}
selector .rvl-nav .elementor-nav-menu--dropdown.elementor-nav-menu__container{position:absolute;left:0!important;right:0;top:100%;width:auto!important;margin-top:0;background:#FFFFFF;z-index:60}
selector .rvl-nav .elementor-nav-menu--dropdown a{font-size:17px}
}
@media (max-width:767px){
selector .rvl-header .rvl-brand-sub{display:none}
selector .rvl-brand-name .elementor-heading-title,selector .rvl-brand-name .elementor-heading-title a{font-size:17px;line-height:1.25}
selector .rvl-header{min-height:64px}
selector .rvl-wa{right:14px;bottom:14px}
selector .rvl-wa-icon .elementor-icon{width:48px;height:48px}
selector .rvl-wa-icon .elementor-icon svg{width:26px;height:26px}
selector .rvl-foot-sub .elementor-heading-title{font-size:12px}
}
CSS;
if ( false === strpos( $kit_set['custom_css'], '/* Mobile polish */' ) ) { $kit_set['custom_css'] .= $add; }
$kit_doc->save( array( 'settings' => $kit_set ) );
\Elementor\Plugin::$instance->files_manager->clear_cache();
do_action( 'litespeed_purge_all' );
return 'ok';
