<?php
/**
 * Content pass on the Royal Vision Law homepage (page 22), run after elementor-build.php.
 * - Site title and tagline
 * - Imports CC0 photos (Openverse / rawpixel, Flickr) into the Media Library
 * - Registration letter image (attachment 27), phone number
 * - Removes placeholder tags, sample markers and unfilled contact rows
 * Safe to re-run: images are matched by source URL, edits are idempotent.
 */
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$page_id   = 22;
$letter_id = 27;
$phone     = '+92 334 5298703';
$tel       = 'tel:+923345298703';

update_option( 'blogname', 'Royal Vision Law Associate' );
update_option( 'blogdescription', 'Advocates & Legal Consultants, Faisalabad' );

/* ---------- Photos (CC0) ---------- */
$photos = array(
	'hero'     => array( 'https://images.rawpixel.com/editor_1024/cHJpdmF0ZS9zdGF0aWMvaW1hZ2Uvd2Vic2l0ZS8yMDIyLTA0L2xyL2ZybGVnYWw3LWltYWdlLWt5YmI1bnJsLmpwZw.jpg', 'Judge\'s gavel resting on an open law book' ),
	'property' => array( 'https://images.rawpixel.com/editor_1024/czNmcy1wcml2YXRlL3Jhd3BpeGVsX2ltYWdlcy93ZWJzaXRlX2NvbnRlbnQvbHIvaXMxNjQ0NC1pbWFnZS1rd3lzYmJzNS5qcGc.jpg', 'House keys held in an open hand' ),
	'family'   => array( 'https://images.rawpixel.com/editor_1024/czNmcy1wcml2YXRlL3Jhd3BpeGVsX2ltYWdlcy93ZWJzaXRlX2NvbnRlbnQvbHIvcHgxMDUzNDY4LWltYWdlLWt3dnk3NjZxLmpwZw.jpg', 'A parent\'s hand with a child\'s hand' ),
	'civil'    => array( 'https://live.staticflickr.com/54/117048243_7cc6bb0b87_b.jpg', 'Gavel on a courtroom bench' ),
);
$media = array();
foreach ( $photos as $key => $p ) {
	$found = get_posts( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'meta_key' => '_rvl_source', 'meta_value' => $p[0], 'numberposts' => 1, 'fields' => 'ids' ) );
	if ( $found ) {
		$id = $found[0];
	} else {
		$tmp = download_url( $p[0], 60 );
		if ( is_wp_error( $tmp ) ) { $media[ $key ] = $tmp->get_error_message(); continue; }
		$id = media_handle_sideload( array( 'name' => 'rvl-' . $key . '.jpg', 'tmp_name' => $tmp ), 0, $p[1] );
		if ( is_wp_error( $id ) ) { @unlink( $tmp ); $media[ $key ] = $id->get_error_message(); continue; }
		update_post_meta( $id, '_rvl_source', $p[0] );
		update_post_meta( $id, '_wp_attachment_image_alt', $p[1] );
	}
	$media[ $key ] = array( 'id' => $id, 'url' => wp_get_attachment_image_url( $id, 'large' ) );
}
foreach ( array( 'hero', 'property', 'family', 'civil' ) as $k ) {
	if ( ! is_array( $media[ $k ] ) ) { return array( 'error' => 'image import failed', 'media' => $media ); }
}

/* ---------- Element edits ---------- */
$doc  = \Elementor\Plugin::$instance->documents->get( $page_id, false );
$data = $doc->get_elements_data();
$log  = array();
$rv_id = function () { return substr( md5( uniqid( '', true ) . mt_rand() ), 0, 7 ); };
$has = function ( $el, $class ) {
	$c = isset( $el['settings']['css_classes'] ) ? $el['settings']['css_classes'] : ( isset( $el['settings']['_css_classes'] ) ? $el['settings']['_css_classes'] : '' );
	return in_array( $class, preg_split( '/\s+/', $c ), true );
};
$bg = function ( $m ) {
	return array(
		'background_background' => 'classic',
		'background_image'      => array( 'id' => $m['id'], 'url' => $m['url'], 'size' => '' ),
		'background_position'   => 'center center',
		'background_size'       => 'cover',
		'background_repeat'     => 'no-repeat',
	);
};
$thumbs = array( 'Property' => 'property', 'Family' => 'family', 'Civil' => 'civil' );
$initials = array( 'Ch. Abdul Nabi Qamar' => 'AQ', 'Imran Ali' => 'IA' );

$walk = function ( array $els ) use ( &$walk, &$log, $has, $bg, $media, $thumbs, $initials, $letter_id, $phone, $tel, $rv_id ) {
	$out = array();
	foreach ( $els as $el ) {
		// Drop "Placeholder" tags on practice cards
		if ( 'widget' === $el['elType'] && $has( $el, 'rvl-tag' ) ) { $log['tags_removed'] = ( $log['tags_removed'] ?? 0 ) + 1; continue; }
		// Drop "Meet Our Team" (links to itself) and "View Profile" (no profile pages)
		if ( 'widget' === $el['elType'] && 'heading' === $el['widgetType'] && in_array( $el['settings']['title'] ?? '', array( 'Meet Our Team <span aria-hidden="true">→</span>', 'View Profile →' ), true ) ) { $log['dead_links_removed'] = ( $log['dead_links_removed'] ?? 0 ) + 1; continue; }

		if ( 'container' === $el['elType'] ) {
			// Hero photo
			if ( $has( $el, 'rvl-slot-hero' ) ) { $el['settings'] = array_merge( $el['settings'], $bg( $media['hero'] ) ); $el['settings']['css_classes'] .= ' rvl-has-img'; $log['hero'] = true; }
			// Registration letter replaces the document placeholder
			if ( $has( $el, 'rvl-doc' ) ) {
				$out[] = array( 'id' => $rv_id(), 'elType' => 'widget', 'widgetType' => 'image', 'isInner' => false, 'elements' => array(), 'settings' => array(
					'image' => array( 'id' => $letter_id, 'url' => wp_get_attachment_image_url( $letter_id, 'full' ), 'alt' => 'Punjab Bar Council letter granting registration of Royal Vision Law Associate, Ref. 13294, dated 31 August 2023' ),
					'image_size' => 'large', '_css_classes' => 'rvl-letter',
				) );
				$log['letter'] = true;
				continue;
			}
			// People: drop unfilled team cards, turn portraits into monograms
			if ( $has( $el, 'rvl-person' ) ) {
				$name = '';
				foreach ( $el['elements'] as $c ) { if ( $has( $c, 'rvl-person-info' ) ) { $name = $c['elements'][0]['settings']['title'] ?? ''; } }
				if ( ! isset( $initials[ $name ] ) ) { $log['team_placeholders_removed'] = ( $log['team_placeholders_removed'] ?? 0 ) + 1; continue; }
				$el['settings']['flex_direction'] = 'row';
				$el['settings']['flex_align_items'] = 'center';
				$el['settings']['flex_gap'] = array( 'unit' => 'px', 'size' => 24, 'column' => '24', 'row' => '24', 'isLinked' => true );
				foreach ( $el['elements'] as &$c ) {
					if ( $has( $c, 'rvl-slot-portrait' ) ) {
						$c['settings']['css_classes'] = 'rvl-monogram';
						$c['settings']['flex_justify_content'] = 'center';
						$c['settings']['flex_align_items'] = 'center';
						$c['elements'] = array( array( 'id' => $rv_id(), 'elType' => 'widget', 'widgetType' => 'heading', 'isInner' => false, 'elements' => array(),
							'settings' => array( 'title' => $initials[ $name ], 'header_size' => 'span', '_css_classes' => 'rvl-monogram-text' ) ) );
					}
					if ( $has( $c, 'rvl-person-info' ) ) {
						foreach ( $c['elements'] as &$w ) { if ( $has( $w, 'rvl-focus' ) ) { $w['settings']['title'] = 'Founding advocate'; } }
						unset( $w );
					}
				}
				unset( $c );
				$log['monograms'] = ( $log['monograms'] ?? 0 ) + 1;
			}
			if ( $has( $el, 'rvl-people' ) ) {
				foreach ( array( 'grid_columns_grid' => 2, 'grid_columns_grid_tablet' => 2, 'grid_columns_grid_mobile' => 1 ) as $k => $v ) { $el['settings'][ $k ] = array( 'unit' => 'fr', 'size' => $v, 'sizes' => array() ); }
			}
			// Article thumbnails
			if ( $has( $el, 'rvl-article' ) ) {
				$cat = '';
				foreach ( $el['elements'] as $c ) { if ( $has( $c, 'rvl-meta-wrap' ) && preg_match( '#rvl-cat">([^<]+)#', $c['settings']['editor'], $mm ) ) { $cat = $mm[1]; } }
				foreach ( $el['elements'] as &$c ) {
					if ( isset( $thumbs[ $cat ] ) && $has( $c, 'rvl-slot-thumb' ) ) { $c['settings'] = array_merge( $c['settings'], $bg( $media[ $thumbs[ $cat ] ] ) ); $c['settings']['css_classes'] .= ' rvl-has-img'; $log['thumbs'] = ( $log['thumbs'] ?? 0 ) + 1; }
					if ( $has( $c, 'rvl-meta-wrap' ) ) { $c['settings']['editor'] = '<p class="rvl-meta"><span class="rvl-cat">' . $cat . '</span></p>'; }
				}
				unset( $c );
			}
		}

		if ( 'widget' === $el['elType'] && 'text-editor' === $el['widgetType'] ) {
			$e = $el['settings']['editor'];
			if ( $has( $el, 'rvl-details-wrap' ) ) {
				$el['settings']['editor'] = '<div class="rvl-details">'
					. '<div class="rvl-detail"><span class="k">Chambers</span><span class="v">45 District Courts, Faisalabad, Punjab, Pakistan</span></div>'
					. '<div class="rvl-detail"><span class="k">Telephone</span><a class="v" href="' . $tel . '">' . $phone . '</a></div>'
					. '</div>';
				$log['contact_details'] = true;
			}
			if ( $has( $el, 'rvl-foot-contact' ) ) {
				$el['settings']['editor'] = '<p>45 District Courts<br>Faisalabad, Pakistan</p><p><a href="' . $tel . '">' . $phone . '</a></p>';
				$log['footer_contact'] = true;
			}
		}

		$el['elements'] = $walk( $el['elements'] );
		$out[] = $el;
	}
	return $out;
};
$data = $walk( $data );

/* ---------- CSS additions ---------- */
$settings = $doc->get_settings();
$css      = $settings['custom_css'];
$add = <<<'CSS'

/* Content pass: photos, letter, monograms, phone */
selector .rvl-has-img::after{display:none}
selector .rvl-letter img{width:100%;height:auto;display:block;box-shadow:0 22px 44px -26px rgba(20,48,42,.45)}
selector .rvl-person .rvl-monogram{--width:132px;flex:0 0 auto;width:132px;aspect-ratio:4/5;background:#14302A;border-radius:2px}
selector .rvl-monogram-text .elementor-heading-title{font-family:var(--rvl-serif);font-size:38px;line-height:1;font-weight:400;letter-spacing:.04em;color:#F7F5F1}
selector .rvl-person{padding:20px;background:#FFFFFF;border:1px solid var(--rvl-line);border-radius:2px}
selector .rvl-person-info{flex:1 1 auto;min-width:0}
selector .rvl-detail a.v{color:var(--rvl-ink);font-weight:500;text-decoration:none}
selector .rvl-detail a.v:hover{color:var(--rvl-green)}
selector .rvl-foot-contact a{color:#FFFFFF;text-decoration:none}
selector .rvl-foot-contact a:hover{color:#A9C4B6}
@media (max-width:767px){selector .rvl-person .rvl-monogram{--width:96px;width:96px}selector .rvl-monogram-text .elementor-heading-title{font-size:28px}}
CSS;
if ( false === strpos( $css, '/* Content pass:' ) ) { $css .= $add; }

$doc->save( array( 'elements' => $data, 'settings' => array( 'custom_css' => $css, 'template' => 'elementor_canvas', 'hide_title' => 'yes' ) ) );
\Elementor\Plugin::$instance->files_manager->clear_cache();
do_action( 'litespeed_purge_all' );

return array( 'title' => get_bloginfo( 'name' ) . ' | ' . get_bloginfo( 'description' ), 'media' => $media, 'log' => $log );
