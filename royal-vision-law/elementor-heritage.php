<?php
/**
 * "Heritage Law" design pass: gives the site a classic law-firm feel.
 * Dark law-library hero with gold accents, a top info bar, scales mark in the header,
 * a trust strip, practice icons, a Lady Justice split for "Our Approach" with
 * roman-numeral steps, and a dark registration band. Content is unchanged.
 * Photos use rawpixel's editor_1024 size: the larger image_1300 size is watermarked.
 * Run after elementor-connect-site.php. Guarded so re-running does not duplicate.
 */
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$home_id = 22;
$rv_id = function () { return substr( md5( uniqid( '', true ) . mt_rand() ), 0, 7 ); };
$px    = function ( $n ) { return array( 'unit' => 'px', 'size' => $n, 'sizes' => array() ); };
$pad   = function ( $t, $r, $b, $l ) { return array( 'unit' => 'px', 'top' => (string) $t, 'right' => (string) $r, 'bottom' => (string) $b, 'left' => (string) $l, 'isLinked' => false ); };
$gap   = function ( $n ) { return array( 'unit' => 'px', 'size' => $n, 'column' => (string) $n, 'row' => (string) $n, 'isLinked' => true ); };
$zero  = array( 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => true );
$con   = function ( $s, $els = array(), $inner = true ) use ( $rv_id ) { return array( 'id' => $rv_id(), 'elType' => 'container', 'isInner' => $inner, 'settings' => $s, 'elements' => $els ); };
$wid   = function ( $type, $s ) use ( $rv_id ) { return array( 'id' => $rv_id(), 'elType' => 'widget', 'widgetType' => $type, 'isInner' => false, 'settings' => $s, 'elements' => array() ); };
$has   = function ( $el, $class ) { $c = $el['settings']['css_classes'] ?? ( $el['settings']['_css_classes'] ?? '' ); return in_array( $class, preg_split( '/\s+/', $c ), true ); };
$icon  = function ( $fa ) { return array( 'value' => 'fas fa-' . $fa, 'library' => 'fa-solid' ); };
$ilist = function ( $items, $class, $space = 28 ) use ( $wid, $rv_id, $icon, $px ) {
	$list = array();
	foreach ( $items as $it ) {
		$row = array( '_id' => $rv_id(), 'text' => $it[0], 'selected_icon' => $icon( $it[1] ) );
		if ( ! empty( $it[2] ) ) { $row['link'] = array( 'url' => $it[2], 'is_external' => '', 'nofollow' => '' ); }
		$list[] = $row;
	}
	return $wid( 'icon-list', array( 'view' => 'inline', 'icon_list' => $list, 'space_between' => $px( $space ), '_css_classes' => $class ) );
};
$log = array();

/* ---------- Photos ---------- */
$photos = array(
	'library' => array( 'https://images.rawpixel.com/editor_1024/czNmcy1wcml2YXRlL3Jhd3BpeGVsX2ltYWdlcy93ZWJzaXRlX2NvbnRlbnQvbHIvcHg1ODcxMjItaW1hZ2Uta3d5cDRjdWUuanBn.jpg', 'Leather-bound law reports on a library shelf' ),
	'justice' => array( 'https://images.rawpixel.com/editor_1024/cHJpdmF0ZS9sci9pbWFnZXMvd2Vic2l0ZS8yMDIyLTA1L3B4MTE4NjU4Mi1pbWFnZS1rd3Z3N2puZC5qcGc.jpg', 'Statue of Lady Justice holding the scales and sword' ),
);
$media = array();
foreach ( $photos as $key => $p ) {
	$found = get_posts( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'meta_key' => '_rvl_source', 'meta_value' => $p[0], 'numberposts' => 1, 'fields' => 'ids' ) );
	if ( $found ) { $id = $found[0]; } else {
		$tmp = download_url( $p[0], 60 );
		if ( is_wp_error( $tmp ) ) { return array( 'error' => $key . ': ' . $tmp->get_error_message() ); }
		$id = media_handle_sideload( array( 'name' => 'rvl-' . $key . '-hq.jpg', 'tmp_name' => $tmp ), 0, $p[1] );
		if ( is_wp_error( $id ) ) { @unlink( $tmp ); return array( 'error' => $key . ': ' . $id->get_error_message() ); }
		update_post_meta( $id, '_rvl_source', $p[0] );
		update_post_meta( $id, '_wp_attachment_image_alt', $p[1] );
	}
	$media[ $key ] = array( 'id' => $id, 'url' => wp_get_attachment_image_url( $id, 'full' ) );
}
$bg = function ( $m ) {
	return array( 'background_background' => 'classic', 'background_image' => array( 'id' => $m['id'], 'url' => $m['url'], 'size' => '' ), 'background_position' => 'center center', 'background_size' => 'cover', 'background_repeat' => 'no-repeat' );
};

/* ---------- Header template: top bar, scales mark, gold CTA ---------- */
$tpl_id = (int) get_posts( array( 'post_type' => 'elementor_library', 'meta_key' => '_rvl_key', 'meta_value' => 'tpl-header', 'numberposts' => 1, 'fields' => 'ids' ) )[0];
$tdoc   = \Elementor\Plugin::$instance->documents->get( $tpl_id, false );
$tdata  = $tdoc->get_elements_data();
if ( ! $has( $tdata[0], 'rvl-topbar' ) ) {
	$topbar = $con( array(
		'content_width' => 'boxed', 'boxed_width' => $px( 1200 ), 'flex_direction' => 'row', 'flex_justify_content' => 'space-between',
		'flex_align_items' => 'center', 'flex_wrap' => 'wrap', 'flex_gap' => $gap( 12 ),
		'padding' => $pad( 9, 40, 9, 40 ), 'padding_tablet' => $pad( 9, 32, 9, 32 ), 'hide_mobile' => 'hidden-mobile', 'css_classes' => 'rvl-topbar',
	), array(
		$ilist( array( array( 'Registered with the Punjab Bar Council · Reg. No. 13294/23', 'balance-scale' ) ), 'rvl-topbar-list' ),
		$ilist( array( array( '45 District Courts, Faisalabad', 'map-marker-alt' ), array( '+92 334 5298703', 'phone-alt', 'tel:+923345298703' ) ), 'rvl-topbar-list' ),
	), false );
	foreach ( $tdata as &$el ) {
		if ( ! $has( $el, 'rvl-header' ) ) { continue; }
		foreach ( $el['elements'] as &$c ) {
			if ( $has( $c, 'rvl-brand' ) ) {
				$text = $c['elements'];
				$c['settings']['flex_direction'] = 'row';
				$c['settings']['flex_align_items'] = 'center';
				$c['settings']['flex_gap'] = $gap( 12 );
				$c['elements'] = array(
					$wid( 'icon', array( 'selected_icon' => $icon( 'balance-scale' ), '_css_classes' => 'rvl-brand-icon' ) ),
					$con( array( 'content_width' => 'full', 'flex_direction' => 'column', 'flex_gap' => $gap( 2 ), 'padding' => $zero, 'width' => array( 'unit' => 'custom', 'size' => 'auto', 'sizes' => array() ), 'css_classes' => 'rvl-brand-text' ), $text ),
				);
			}
			if ( $has( $c, 'rvl-navwrap' ) ) {
				foreach ( $c['elements'] as &$w ) { if ( 'button' === $w['widgetType'] ) { $w['settings']['_css_classes'] = 'rvl-btn rvl-btn-sm rvl-btn-gold rvl-hide-mobile'; } }
				unset( $w );
			}
		}
		unset( $c );
	}
	unset( $el );
	array_unshift( $tdata, $topbar );
	$tdoc->save( array( 'elements' => $tdata ) );
	$log['header'] = 'top bar, scales mark, gold CTA';
}

/* ---------- Homepage ---------- */
$doc  = \Elementor\Plugin::$instance->documents->get( $home_id, false );
$data = $doc->get_elements_data();
$card_icons = array( 'Civil litigation' => 'balance-scale', 'Criminal defence' => 'gavel', 'Family law' => 'users', 'Property &amp; land' => 'home', 'Corporate &amp; commercial' => 'briefcase', 'Banking &amp; recovery' => 'university' );
$out  = array();
foreach ( $data as $el ) {
	// Hero: full-bleed law library with dark overlay, gold primary button
	if ( $has( $el, 'rvl-hero' ) && ! $has( $el, 'rvl-hero-dark' ) ) {
		$el['settings'] = array_merge( $el['settings'], $bg( $media['library'] ), array(
			'background_overlay_background' => 'gradient', 'background_overlay_gradient_type' => 'linear',
			'background_overlay_color' => 'rgba(11,31,27,0.96)', 'background_overlay_color_stop' => array( 'unit' => '%', 'size' => 0, 'sizes' => array() ),
			'background_overlay_color_b' => 'rgba(11,31,27,0.72)', 'background_overlay_color_b_stop' => array( 'unit' => '%', 'size' => 100, 'sizes' => array() ),
			'background_overlay_gradient_angle' => array( 'unit' => 'deg', 'size' => 90, 'sizes' => array() ),
			'padding' => $pad( 150, 40, 150, 40 ), 'padding_tablet' => $pad( 120, 32, 120, 32 ), 'padding_mobile' => $pad( 88, 20, 88, 20 ),
			'css_classes' => 'rvl-section rvl-hero rvl-hero-dark',
		) );
		foreach ( $el['elements'] as &$row ) {
			$row['elements'] = array_values( array_filter( $row['elements'], function ( $c ) use ( $has ) { return ! $has( $c, 'rvl-hero-media' ); } ) );
			foreach ( $row['elements'] as &$copy ) {
				$copy['settings']['width'] = array( 'unit' => '%', 'size' => 58, 'sizes' => array() );
				foreach ( $copy['elements'] as &$w ) {
					if ( $has( $w, 'rvl-eyebrow' ) || $has( $w, 'rvl-h1' ) ) { $w['settings']['_css_classes'] .= ' rvl-on-dark'; }
					if ( $has( $w, 'rvl-lead' ) ) { $w['settings']['_css_classes'] .= ' rvl-on-dark'; }
					if ( $has( $w, 'rvl-actions' ) ) {
						foreach ( $w['elements'] as $i => &$b ) { $b['settings']['_css_classes'] = 0 === $i ? 'rvl-btn rvl-btn-gold' : 'rvl-btn rvl-btn-ghost-light'; }
						unset( $b );
					}
				}
				unset( $w );
			}
			unset( $copy );
		}
		unset( $row );
		$out[] = $el;
		// Trust strip under the hero
		$out[] = $con( array(
			'content_width' => 'boxed', 'boxed_width' => $px( 1200 ), 'flex_direction' => 'row', 'flex_justify_content' => 'center', 'flex_align_items' => 'center',
			'padding' => $pad( 22, 40, 22, 40 ), 'padding_mobile' => $pad( 22, 20, 22, 20 ), 'css_classes' => 'rvl-trust',
		), array( $ilist( array(
			array( 'Registered with the Punjab Bar Council', 'award' ),
			array( 'Reg. No. 13294/23', 'certificate' ),
			array( 'Chambers at the District Courts, Faisalabad', 'landmark' ),
			array( 'Civil · Criminal · Family · Property', 'balance-scale' ),
		), 'rvl-trust-list', 40 ) ), false );
		$log['hero'] = 'dark library hero + trust strip';
		continue;
	}
	// Registration: dark band
	if ( 'registration' === ( $el['settings']['_element_id'] ?? '' ) ) {
		$el['settings']['css_classes'] = 'rvl-section rvl-dark';
		$log['registration'] = 'dark band';
	}
	// Practice cards: gold icons
	if ( 'practice' === ( $el['settings']['_element_id'] ?? '' ) ) {
		$walk = function ( array $els ) use ( &$walk, $has, $wid, $icon, $card_icons, &$log ) {
			foreach ( $els as &$c ) {
				if ( 'container' === $c['elType'] && $has( $c, 'rvl-card' ) && ! $has( $c['elements'][0], 'rvl-card-icon' ) ) {
					$title = '';
					foreach ( $c['elements'] as $w ) { if ( $has( $w, 'rvl-h3' ) ) { $title = $w['settings']['title']; } }
					if ( isset( $card_icons[ $title ] ) ) {
						array_unshift( $c['elements'], $wid( 'icon', array( 'selected_icon' => $icon( $card_icons[ $title ] ), '_css_classes' => 'rvl-card-icon', 'align' => 'left' ) ) );
						$log['card_icons'] = ( $log['card_icons'] ?? 0 ) + 1;
					}
				}
				$c['elements'] = $walk( $c['elements'] );
			}
			return $els;
		};
		$el['elements'] = $walk( $el['elements'] );
	}
	// Approach: Lady Justice split with roman-numeral steps
	if ( 'approach' === ( $el['settings']['_element_id'] ?? '' ) && ! $has( $el['elements'][0], 'rvl-approach-row' ) ) {
		$head  = $el['elements'][0];
		$steps = $el['elements'][1];
		foreach ( array( 'grid_columns_grid' => 1, 'grid_columns_grid_tablet' => 1, 'grid_columns_grid_mobile' => 1 ) as $k => $v ) { $steps['settings'][ $k ] = array( 'unit' => 'fr', 'size' => $v, 'sizes' => array() ); }
		$steps['settings']['grid_gaps'] = array( 'unit' => 'px', 'column' => '0', 'row' => '0', 'isLinked' => true );
		$el['settings']['css_classes'] = 'rvl-section rvl-alt';
		$el['elements'] = array( $con( array(
			'content_width' => 'full', 'flex_direction' => 'row', 'flex_direction_mobile' => 'column', 'flex_wrap' => 'nowrap',
			'flex_gap' => $gap( 64 ), 'flex_gap_mobile' => $gap( 32 ), 'flex_align_items' => 'stretch', 'padding' => $zero, 'css_classes' => 'rvl-approach-row',
		), array(
			$con( array_merge( $bg( $media['justice'] ), array(
				'content_width' => 'full', 'flex_direction' => 'column', 'padding' => $zero, 'css_classes' => 'rvl-approach-img rvl-has-img',
				'width' => array( 'unit' => '%', 'size' => 42, 'sizes' => array() ), 'width_mobile' => array( 'unit' => '%', 'size' => 100, 'sizes' => array() ),
				'background_position' => 'center center',
			) ) ),
			$con( array(
				'content_width' => 'full', 'flex_direction' => 'column', 'flex_justify_content' => 'center', 'flex_gap' => $gap( 28 ), 'padding' => $zero, 'css_classes' => 'rvl-approach-copy',
				'width' => array( 'unit' => '%', 'size' => 58, 'sizes' => array() ), 'width_mobile' => array( 'unit' => '%', 'size' => 100, 'sizes' => array() ),
			), array( $head, $steps ) ),
		) ) );
		$log['approach'] = 'lady justice split';
	}
	$out[] = $el;
}
$doc->save( array( 'elements' => $out, 'settings' => array( 'template' => 'elementor_canvas', 'hide_title' => 'yes' ) ) );

/* ---------- Site-wide CSS layer ---------- */
$kit_id  = (int) get_option( 'elementor_active_kit' );
$kit_doc = \Elementor\Plugin::$instance->documents->get( $kit_id, false );
$kit_set = array_filter( (array) $kit_doc->get_settings(), function ( $k ) { return ! is_int( $k ); }, ARRAY_FILTER_USE_KEY );
$css = $kit_set['custom_css'];
$lib = esc_url_raw( wp_get_attachment_image_url( $media['library']['id'], 'full' ) );
$add = <<<CSS

/* Heritage Law layer: gold accents, dark bands, law motifs */
selector{--rvl-gold:#B08D57;--rvl-gold-ink:#7E5F26;--rvl-gold-light:#D4B77E;--rvl-night:#0F2621}
selector .rvl-eyebrow .elementor-heading-title{color:var(--rvl-gold-ink);display:inline-flex;align-items:center;gap:12px}
selector .rvl-eyebrow .elementor-heading-title::before{content:"";width:28px;height:1px;background:var(--rvl-gold);flex:0 0 auto}
selector .rvl-nav .elementor-nav-menu--main .elementor-item:after{background-color:var(--rvl-gold)!important}
html{scroll-padding-top:130px}
@media (max-width:767px){html{scroll-padding-top:90px}}

/* Top bar + brand mark */
selector .rvl-topbar{background:var(--rvl-night)}
selector .rvl-topbar .elementor-icon-list-text{font-family:var(--rvl-sans);font-size:13px;line-height:1.4;color:#D3DDD8;letter-spacing:.02em}
selector .rvl-topbar .elementor-icon-list-icon svg{fill:var(--rvl-gold-light);width:13px;height:13px}
selector .rvl-topbar .elementor-icon-list-icon i{color:var(--rvl-gold-light);font-size:13px}
selector .rvl-topbar a:hover .elementor-icon-list-text{color:#FFFFFF}
selector .rvl-brand-icon .elementor-icon{font-size:30px;color:var(--rvl-gold);display:flex}
selector .rvl-brand-icon .elementor-icon svg{width:30px;height:30px;fill:var(--rvl-gold)}

/* Buttons */
selector .rvl-btn-gold .elementor-button{background:var(--rvl-gold);border-color:var(--rvl-gold);color:var(--rvl-night);font-weight:600}
selector .rvl-btn-gold .elementor-button:hover,selector .rvl-btn-gold .elementor-button:focus-visible{background:var(--rvl-gold-light);border-color:var(--rvl-gold-light);color:var(--rvl-night)}
selector .rvl-btn-ghost-light .elementor-button{background:transparent;color:#FFFFFF;border-color:rgba(255,255,255,.65)}
selector .rvl-btn-ghost-light .elementor-button:hover,selector .rvl-btn-ghost-light .elementor-button:focus-visible{background:#FFFFFF;border-color:#FFFFFF;color:var(--rvl-night)}

/* Hero */
selector .rvl-hero-dark{border-bottom:0}
selector .rvl-on-dark .elementor-heading-title{color:#FFFFFF}
selector .rvl-eyebrow.rvl-on-dark .elementor-heading-title{color:var(--rvl-gold-light)}
selector .rvl-h1.rvl-on-dark .elementor-heading-title{font-size:clamp(38px,5vw,64px);line-height:1.1}
selector .rvl-lead.rvl-on-dark p{color:#DCE5E0}

/* Trust strip */
selector .rvl-trust{background:var(--rvl-night);border-top:1px solid rgba(212,183,126,.35);border-bottom:3px solid var(--rvl-gold)}
selector .rvl-trust .elementor-icon-list-items{justify-content:center;row-gap:10px}
selector .rvl-trust .elementor-icon-list-text{font-family:var(--rvl-sans);font-size:15px;line-height:1.4;color:#E6ECE9}
selector .rvl-trust .elementor-icon-list-icon svg{fill:var(--rvl-gold-light);width:16px;height:16px}
selector .rvl-trust .elementor-icon-list-icon i{color:var(--rvl-gold-light);font-size:16px}

/* Dark band (registration) */
selector .rvl-dark{background:#14302A;border-top:0}
selector .rvl-dark .rvl-h2 .elementor-heading-title{color:#FFFFFF}
selector .rvl-dark .rvl-eyebrow .elementor-heading-title{color:var(--rvl-gold-light)}
selector .rvl-dark .rvl-body p{color:#DCE5E0}
selector .rvl-dark .rvl-dl{border-top-color:rgba(255,255,255,.18)}
selector .rvl-dark .rvl-dl-row{border-bottom-color:rgba(255,255,255,.18)}
selector .rvl-dark .rvl-dl-row span{color:#A9C4B6}
selector .rvl-dark .rvl-dl-row strong{color:#FFFFFF}
selector .rvl-dark .rvl-caption p{color:#A9C4B6}
selector .rvl-dark .rvl-letter img{box-shadow:0 0 0 1px rgba(212,183,126,.55),0 30px 60px -30px rgba(0,0,0,.65)}

/* Practice cards */
selector .rvl-card{border-top:3px solid var(--rvl-line)}
selector .rvl-card:hover{border-color:var(--rvl-line);border-top-color:var(--rvl-gold)}
selector .rvl-card-icon .elementor-icon{width:52px;height:52px;display:flex;align-items:center;justify-content:center;border:1px solid var(--rvl-gold);border-radius:2px;color:var(--rvl-gold-ink);font-size:22px;transition:background-color .15s ease}
selector .rvl-card-icon .elementor-icon svg{width:22px;height:22px;fill:var(--rvl-gold-ink)}
selector .rvl-card:hover .rvl-card-icon .elementor-icon{background:var(--rvl-gold)}
selector .rvl-card:hover .rvl-card-icon .elementor-icon svg{fill:var(--rvl-night)}

/* Approach: Lady Justice + roman numerals */
selector .rvl-approach-img{min-height:520px;border-radius:2px;box-shadow:14px 14px 0 0 rgba(176,141,87,.35)}
selector .rvl-steps{counter-reset:rvl-step}
selector .rvl-step{counter-increment:rvl-step;position:relative;padding:24px 0 24px 64px;border-top:1px solid rgba(20,48,42,.25)}
selector .rvl-step::after{content:counter(rvl-step,upper-roman);position:absolute;left:0;top:22px;font-family:var(--rvl-serif);font-size:28px;line-height:1;color:var(--rvl-gold)}
@media (max-width:767px){selector .rvl-approach-img{min-height:320px;box-shadow:8px 8px 0 0 rgba(176,141,87,.35)}}

/* People, contact, footer */
selector .rvl-person .rvl-monogram{background:var(--rvl-night);box-shadow:inset 0 0 0 1px var(--rvl-gold)}
selector .rvl-monogram-text .elementor-heading-title{color:var(--rvl-gold-light)}
selector .rvl-form-card{border-top:3px solid var(--rvl-gold)}
selector .rvl-footer{border-top:3px solid var(--rvl-gold)}
selector .rvl-foot-h .elementor-heading-title{color:var(--rvl-gold-light)}

/* Article and page headers: dark library band */
selector .rvl-page-head{background:linear-gradient(90deg,rgba(11,31,27,.95),rgba(11,31,27,.78)),url("{$lib}") center/cover no-repeat;border-bottom:3px solid var(--rvl-gold)}
selector .rvl-page-head .rvl-eyebrow .elementor-heading-title{color:var(--rvl-gold-light)}
selector .rvl-page-head .rvl-h1 .elementor-heading-title{color:#FFFFFF}
selector .rvl-page-head .rvl-lead p{color:#DCE5E0}

/* Keep brand mark and team cards on one row on mobile */
selector .rvl-brand,selector .rvl-person{--flex-wrap:nowrap;flex-wrap:nowrap}
selector .rvl-brand .rvl-brand-icon{flex:0 0 auto;width:auto}
selector .rvl-brand .rvl-brand-text{--width:auto;width:auto;flex:1 1 auto;min-width:0}
selector .rvl-person .rvl-person-info{--width:auto;width:auto}
@media (max-width:767px){selector .rvl-brand-icon .elementor-icon svg{width:24px;height:24px}selector .rvl-brand{--gap:10px;gap:10px}}
CSS;
if ( false === strpos( $css, '/* Heritage Law layer' ) ) { $css .= $add; }
$kit_set['custom_css'] = $css;
$kit_doc->save( array( 'settings' => $kit_set ) );

\Elementor\Plugin::$instance->files_manager->clear_cache();
do_action( 'litespeed_purge_all' );
return array( 'media' => $media, 'log' => $log, 'top_level' => count( $out ), 'css' => strlen( $css ) );
