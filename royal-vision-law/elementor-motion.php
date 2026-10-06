<?php
/**
 * Motion + stronger law-firm character for royalvisionlaw.com.
 * - Hero: gold italic second line, staged entrance, animated gold rule, background parallax
 * - Scroll entrance animations across sections (Elementor native)
 * - Counter band (real figures only), dark Practice Areas, legal-maxim band
 * - Gold underline under section headings, floating WhatsApp button (footer template)
 * Run after elementor-heritage.php. Guarded so re-running does not duplicate.
 */

$home_id = 22;
$rv_id = function () { return substr( md5( uniqid( '', true ) . mt_rand() ), 0, 7 ); };
$px    = function ( $n ) { return array( 'unit' => 'px', 'size' => $n, 'sizes' => array() ); };
$pad   = function ( $t, $r, $b, $l ) { return array( 'unit' => 'px', 'top' => (string) $t, 'right' => (string) $r, 'bottom' => (string) $b, 'left' => (string) $l, 'isLinked' => false ); };
$gap   = function ( $n ) { return array( 'unit' => 'px', 'size' => $n, 'column' => (string) $n, 'row' => (string) $n, 'isLinked' => true ); };
$zero  = array( 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => true );
$con   = function ( $s, $els = array(), $inner = true ) use ( $rv_id ) { return array( 'id' => $rv_id(), 'elType' => 'container', 'isInner' => $inner, 'settings' => $s, 'elements' => $els ); };
$wid   = function ( $type, $s ) use ( $rv_id ) { return array( 'id' => $rv_id(), 'elType' => 'widget', 'widgetType' => $type, 'isInner' => false, 'settings' => $s, 'elements' => array() ); };
$link  = function ( $url, $ext = false ) { return array( 'url' => $url, 'is_external' => $ext ? 'on' : '', 'nofollow' => '', 'custom_attributes' => '' ); };
$has   = function ( $el, $class ) { $c = $el['settings']['css_classes'] ?? ( $el['settings']['_css_classes'] ?? '' ); return in_array( $class, preg_split( '/\s+/', $c ), true ); };
$anim  = function ( array &$el, $name, $delay = 0 ) {
	$p = 'container' === $el['elType'] ? '' : '_';
	$el['settings'][ $p . 'animation' ]       = $name;
	$el['settings'][ $p . 'animation_delay' ] = $delay;
};
$section = function ( $id, $class, $children, $extra = array() ) use ( $con, $px, $pad, $gap ) {
	$s = array_merge( array(
		'content_width' => 'boxed', 'boxed_width' => $px( 1200 ), 'flex_direction' => 'column', 'flex_gap' => $gap( 32 ),
		'padding' => $pad( 92, 40, 92, 40 ), 'padding_tablet' => $pad( 72, 32, 72, 32 ), 'padding_mobile' => $pad( 56, 20, 56, 20 ),
		'css_classes' => 'rvl-section ' . $class,
	), $extra );
	if ( $id ) { $s['_element_id'] = $id; }
	return $con( $s, $children, false );
};
$log = array();

/* ---------- Homepage ---------- */
$doc  = \Elementor\Plugin::$instance->documents->get( $home_id, false );
$data = $doc->get_elements_data();

// Animate a list of matching descendants with a stagger
$stagger = function ( array &$els, $class, $name, $step, $mod = 0 ) use ( &$stagger, $has, $anim, &$log ) {
	static $counts = array();
	foreach ( $els as &$c ) {
		if ( $has( $c, $class ) ) {
			$i = $counts[ $class ] ?? 0;
			$anim( $c, $name, ( $mod ? $i % $mod : $i ) * $step );
			$counts[ $class ] = $i + 1;
			$log['animated'][ $class ] = $counts[ $class ];
		}
		if ( ! empty( $c['elements'] ) ) { $stagger( $c['elements'], $class, $name, $step, $mod ); }
	}
};

$out = array();
$has_stats = $has_quote = false;
foreach ( $data as $el ) { if ( $has( $el, 'rvl-stats' ) ) { $has_stats = true; } if ( $has( $el, 'rvl-quote' ) ) { $has_quote = true; } }

foreach ( $data as $el ) {
	$id = $el['settings']['_element_id'] ?? '';

	if ( $has( $el, 'rvl-hero-dark' ) ) {
		$el['settings']['background_motion_fx_motion_fx_scrolling'] = 'yes';
		$el['settings']['background_motion_fx_translateY_effect']   = 'yes';
		$el['settings']['background_motion_fx_translateY_speed']    = array( 'unit' => 'px', 'size' => 3, 'sizes' => array() );
		$el['settings']['background_motion_fx_devices']             = array( 'desktop', 'tablet' );
		$seq = 0;
		$walk = function ( array &$els ) use ( &$walk, $has, $anim, &$seq ) {
			foreach ( $els as &$w ) {
				if ( 'widget' === $w['elType'] && $has( $w, 'rvl-h1' ) ) {
					$w['settings']['title'] = 'Clear advice.<br><em>Considered action.</em>';
				}
				if ( 'widget' === $w['elType'] && ( $has( $w, 'rvl-eyebrow' ) || $has( $w, 'rvl-h1' ) || $has( $w, 'rvl-lead' ) ) ) {
					$anim( $w, $has( $w, 'rvl-eyebrow' ) ? 'fadeInDown' : 'fadeInUp', 150 + 200 * $seq++ );
				}
				if ( 'container' === $w['elType'] && $has( $w, 'rvl-actions' ) ) { $anim( $w, 'fadeInUp', 800 ); continue; }
				if ( ! empty( $w['elements'] ) ) { $walk( $w['elements'] ); }
			}
		};
		$walk( $el['elements'] );
		$log['hero'] = 'parallax + staged entrance';
		$out[] = $el;
		continue;
	}

	if ( $has( $el, 'rvl-trust' ) ) {
		$anim( $el['elements'][0], 'fadeIn', 900 );
		$out[] = $el;
		// Counter band right after the trust strip
		if ( ! $has_stats ) {
			$counter = function ( $end, $title, $sep = false ) use ( $wid ) {
				return $wid( 'counter', array( 'starting_number' => 0, 'ending_number' => $end, 'title' => $title, 'thousand_separator' => $sep ? 'yes' : '', 'duration' => 2200, '_css_classes' => 'rvl-counter' ) );
			};
			$items = array( $counter( 2023, 'Year registered with the Punjab Bar Council' ), $counter( 6, 'Practice areas we advise on' ), $counter( 13294, 'Punjab Bar Council reference number' ) );
			$boxes = array();
			foreach ( $items as $i => $w ) {
				$b = $con( array( 'content_width' => 'full', 'flex_direction' => 'column', 'padding' => $pad( 8, 24, 8, 24 ), 'css_classes' => 'rvl-stat' ), array( $w ) );
				$anim( $b, 'fadeInUp', $i * 150 );
				$boxes[] = $b;
			}
			$out[] = $section( '', 'rvl-white rvl-stats', array( $con( array(
				'container_type' => 'grid', 'content_width' => 'full',
				'grid_columns_grid' => array( 'unit' => 'fr', 'size' => 3, 'sizes' => array() ),
				'grid_columns_grid_tablet' => array( 'unit' => 'fr', 'size' => 3, 'sizes' => array() ),
				'grid_columns_grid_mobile' => array( 'unit' => 'fr', 'size' => 1, 'sizes' => array() ),
				'grid_rows_grid' => array( 'unit' => 'custom', 'size' => 'auto', 'sizes' => array() ),
				'grid_gaps' => array( 'unit' => 'px', 'column' => '0', 'row' => '28', 'isLinked' => false ),
				'padding' => $zero, 'css_classes' => 'rvl-stats-grid',
			), $boxes ) ), array( 'padding' => $pad( 64, 40, 64, 40 ), 'padding_mobile' => $pad( 44, 20, 44, 20 ) ) );
			$log['stats'] = 'added';
		}
		continue;
	}

	// Section heads and content blocks
	$stagger( $el['elements'], 'rvl-head', 'fadeInUp', 0 );
	if ( 'firm' === $id ) {
		$stagger( $el['elements'], 'rvl-split-a', 'fadeInUp', 0 );
		$stagger( $el['elements'], 'rvl-split-b', 'fadeInUp', 0 );
		$stagger( $el['elements'], 'rvl-fact', 'fadeInUp', 120 );
	}
	if ( 'registration' === $id ) {
		$stagger( $el['elements'], 'rvl-reg-figure', 'fadeInLeft', 0 );
		$stagger( $el['elements'], 'rvl-reg-copy', 'fadeInRight', 0 );
	}
	if ( 'practice' === $id ) {
		$el['settings']['css_classes'] = 'rvl-section rvl-night';
		$stagger( $el['elements'], 'rvl-card', 'fadeInUp', 120, 3 );
	}
	if ( 'approach' === $id ) {
		$stagger( $el['elements'], 'rvl-approach-img', 'fadeInLeft', 0 );
		$stagger( $el['elements'], 'rvl-step', 'fadeInUp', 150 );
	}
	if ( 'team' === $id ) { $stagger( $el['elements'], 'rvl-person', 'fadeInUp', 150 ); }
	if ( 'insights' === $id ) { $stagger( $el['elements'], 'rvl-article', 'fadeInUp', 150 ); }
	if ( 'contact' === $id ) {
		$stagger( $el['elements'], 'rvl-contact-info', 'fadeInLeft', 0 );
		$stagger( $el['elements'], 'rvl-form-card', 'fadeInRight', 0 );
	}
	$out[] = $el;

	// Legal maxim band after Our Approach
	if ( 'approach' === $id && ! $has_quote ) {
		$q = $con( array( 'content_width' => 'full', 'flex_direction' => 'column', 'flex_align_items' => 'center', 'flex_gap' => $gap( 18 ), 'padding' => $zero, 'css_classes' => 'rvl-quote-inner' ), array(
			$wid( 'heading', array( 'title' => '“', 'header_size' => 'div', '_css_classes' => 'rvl-quote-mark', 'align' => 'center' ) ),
			$wid( 'heading', array( 'title' => '<em>Fiat justitia ruat caelum.</em>', 'header_size' => 'p', '_css_classes' => 'rvl-quote-text', 'align' => 'center' ) ),
			$wid( 'heading', array( 'title' => 'Let justice be done, though the heavens fall.', 'header_size' => 'div', '_css_classes' => 'rvl-quote-cite', 'align' => 'center' ) ),
			$wid( 'button', array( 'text' => 'Discuss Your Matter', 'link' => $link( '#contact' ), 'align' => 'center', '_css_classes' => 'rvl-btn rvl-btn-gold rvl-quote-btn' ) ),
		) );
		$anim( $q, 'zoomIn', 0 );
		$out[] = $section( '', 'rvl-quote', array( $q ), array( 'padding' => $pad( 110, 40, 110, 40 ), 'padding_mobile' => $pad( 72, 20, 72, 20 ), 'flex_align_items' => 'center' ) );
		$log['quote'] = 'added';
	}
}
$doc->save( array( 'elements' => $out, 'settings' => array( 'template' => 'elementor_canvas', 'hide_title' => 'yes' ) ) );
update_post_meta( 27, '_wp_attachment_image_alt', 'Punjab Bar Council letter granting registration of Royal Vision Law Associate, Ref. 13294, dated 31 August 2023' );

/* ---------- Floating WhatsApp button (footer template = every page) ---------- */
$ftpl = (int) get_posts( array( 'post_type' => 'elementor_library', 'meta_key' => '_rvl_key', 'meta_value' => 'tpl-footer', 'numberposts' => 1, 'fields' => 'ids' ) )[0];
$fdoc  = \Elementor\Plugin::$instance->documents->get( $ftpl, false );
$fdata = $fdoc->get_elements_data();
$has_wa = false;
foreach ( $fdata as $el ) { if ( $has( $el, 'rvl-wa' ) ) { $has_wa = true; } }
if ( ! $has_wa ) {
	$fdata[] = $con( array( 'content_width' => 'full', 'flex_direction' => 'column', 'padding' => $zero, 'css_classes' => 'rvl-wa' ), array(
		$wid( 'icon', array(
			'selected_icon' => array( 'value' => 'fab fa-whatsapp', 'library' => 'fa-brands' ),
			'link' => $link( 'https://wa.me/923345298703', true ), '_css_classes' => 'rvl-wa-icon',
		) ),
	), false );
	$fdoc->save( array( 'elements' => $fdata ) );
	$log['whatsapp'] = 'added';
}

/* ---------- CSS ---------- */
$kit_doc = \Elementor\Plugin::$instance->documents->get( (int) get_option( 'elementor_active_kit' ), false );
$kit_set = array_filter( (array) $kit_doc->get_settings(), function ( $k ) { return ! is_int( $k ); }, ARRAY_FILTER_USE_KEY );
$add = <<<'CSS'

/* Motion layer */
@keyframes rvl-rule{from{transform:scaleX(0)}to{transform:scaleX(1)}}
@keyframes rvl-pulse{0%{box-shadow:0 0 0 0 rgba(37,211,102,.55)}70%{box-shadow:0 0 0 16px rgba(37,211,102,0)}100%{box-shadow:0 0 0 0 rgba(37,211,102,0)}}
@keyframes rvl-shine{from{left:-60%}to{left:130%}}

/* Hero: gold italic line + drawn rule */
selector .rvl-h1.rvl-on-dark .elementor-heading-title em{font-style:italic;color:var(--rvl-gold-light)}
selector .rvl-h1.rvl-on-dark .elementor-heading-title::after{content:"";display:block;width:96px;height:2px;margin-top:26px;background:var(--rvl-gold);transform-origin:left;animation:rvl-rule 1.2s .9s cubic-bezier(.2,.7,.2,1) both}

/* Gold underline under every section heading */
selector .rvl-head .rvl-h2 .elementor-heading-title::after{content:"";display:block;width:56px;height:2px;margin-top:18px;background:var(--rvl-gold)}

/* Buttons: light sweep on hover */
selector .rvl-btn .elementor-button{position:relative;overflow:hidden}
selector .rvl-btn .elementor-button::before{content:"";position:absolute;top:0;left:-60%;width:40%;height:100%;background:linear-gradient(110deg,transparent,rgba(255,255,255,.35),transparent);pointer-events:none}
selector .rvl-btn .elementor-button:hover::before{animation:rvl-shine .7s ease}

/* Counter band */
selector .rvl-stats{border-bottom:1px solid var(--rvl-line)}
selector .rvl-stat{text-align:center;border-left:1px solid var(--rvl-line)}
selector .rvl-stat:first-child{border-left:0}
selector .rvl-counter .elementor-counter-number-wrapper{font-family:var(--rvl-serif);font-size:clamp(40px,4.4vw,56px);line-height:1;color:var(--rvl-gold-ink);justify-content:center}
selector .rvl-counter .elementor-counter-title{font-family:var(--rvl-sans);font-size:14px;line-height:1.45;letter-spacing:.08em;text-transform:uppercase;font-weight:600;color:var(--rvl-muted);margin-top:12px;text-align:center}
@media (max-width:767px){selector .rvl-stat{border-left:0;border-top:1px solid var(--rvl-line);padding-top:24px}selector .rvl-stat:first-child{border-top:0;padding-top:0}}

/* Dark Practice Areas */
selector .rvl-night{background:radial-gradient(ellipse at 20% 0%,#1A3F35 0%,var(--rvl-night) 60%);border-top:0;border-bottom:0}
selector .rvl-night .rvl-h2 .elementor-heading-title{color:#FFFFFF}
selector .rvl-night .rvl-eyebrow .elementor-heading-title{color:var(--rvl-gold-light)}
selector .rvl-night .rvl-more a{color:var(--rvl-gold-light)}
selector .rvl-night .rvl-more a:hover{color:#FFFFFF}
selector .rvl-night .rvl-card{background:rgba(255,255,255,.035);border:1px solid rgba(212,183,126,.22);border-top:3px solid rgba(212,183,126,.35);color:#DCE5E0}
selector .rvl-night .rvl-card:hover{background:rgba(255,255,255,.06);border-color:rgba(212,183,126,.5);border-top-color:var(--rvl-gold);box-shadow:0 24px 50px -28px rgba(0,0,0,.8),0 0 0 1px rgba(212,183,126,.25)}
selector .rvl-night .rvl-card .rvl-h3 .elementor-heading-title{color:#FFFFFF}
selector .rvl-night .rvl-card .rvl-body p{color:#C9D5CF}
selector .rvl-night .rvl-card-cta .elementor-heading-title{color:var(--rvl-gold-light);border-top-color:rgba(212,183,126,.22)}
selector .rvl-night .rvl-card-icon .elementor-icon{border-color:var(--rvl-gold-light)}
selector .rvl-night .rvl-card-icon .elementor-icon svg{fill:var(--rvl-gold-light)}
selector .rvl-night .rvl-card:hover .rvl-card-icon .elementor-icon svg{fill:var(--rvl-night)}

/* Legal maxim band */
selector .rvl-quote{background:linear-gradient(rgba(11,31,27,.9),rgba(11,31,27,.9)),var(--rvl-night);border-top:3px solid var(--rvl-gold);border-bottom:3px solid var(--rvl-gold);text-align:center}
selector .rvl-quote-mark .elementor-heading-title{font-family:var(--rvl-serif);font-size:96px;line-height:.6;color:var(--rvl-gold);height:44px}
selector .rvl-quote-text .elementor-heading-title{font-family:var(--rvl-serif);font-size:clamp(30px,4vw,52px);line-height:1.2;color:#FFFFFF}
selector .rvl-quote-text .elementor-heading-title em{font-style:italic}
selector .rvl-quote-cite .elementor-heading-title{font-family:var(--rvl-sans);font-size:15px;letter-spacing:.14em;text-transform:uppercase;font-weight:600;color:var(--rvl-gold-light)}
selector .rvl-quote-btn{margin-top:10px}

/* Card and image hover */
selector .rvl-article{transition:transform .25s ease}
selector .rvl-article:hover{transform:translateY(-4px)}
selector .rvl-person{transition:transform .25s ease,box-shadow .25s ease}
selector .rvl-person:hover{transform:translateY(-3px);box-shadow:0 18px 36px -24px rgba(20,48,42,.45)}

/* Floating WhatsApp */
selector .rvl-wa{position:fixed;right:20px;bottom:20px;z-index:99;width:auto;--width:auto;padding:0}
selector .rvl-wa-icon .elementor-icon{width:58px;height:58px;border-radius:50%;background:#25D366;display:flex;align-items:center;justify-content:center;animation:rvl-pulse 2.4s infinite;box-shadow:0 10px 24px -8px rgba(0,0,0,.45)}
selector .rvl-wa-icon .elementor-icon svg{width:30px;height:30px;fill:#FFFFFF}
selector .rvl-wa-icon .elementor-icon:hover{transform:scale(1.06)}

/* Dark sections: keep the section link legible */
selector .rvl-night .rvl-more .elementor-heading-title,selector .rvl-night .rvl-more .elementor-heading-title a{color:var(--rvl-gold-light)!important}
selector .rvl-night .rvl-more .elementor-heading-title a:hover{color:#FFFFFF!important}

/* Respect reduced motion */
@media (prefers-reduced-motion:reduce){
selector .elementor-invisible{visibility:visible!important}
selector .animated{animation:none!important}
selector .rvl-h1.rvl-on-dark .elementor-heading-title::after,selector .rvl-wa-icon .elementor-icon,selector .rvl-btn .elementor-button:hover::before{animation:none}
selector .rvl-article:hover,selector .rvl-person:hover{transform:none}
}
CSS;
if ( false === strpos( $kit_set['custom_css'], '/* Motion layer */' ) ) { $kit_set['custom_css'] .= $add; }
$kit_doc->save( array( 'settings' => $kit_set ) );

\Elementor\Plugin::$instance->files_manager->clear_cache();
do_action( 'litespeed_purge_all' );
return array( 'log' => $log, 'top_level' => count( $out ) );
