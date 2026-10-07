<?php
/**
 * Premium law-firm pass for royalvisionlaw.com.
 * - Hero: near full-screen, with a slowly turning gold seal (CSS only)
 * - "Why choose us" section (four factual points)
 * - Team section driven by the Team post type ([rvl_team]) + an "Our Team" page
 * - Dark contact section, ornamental heading dividers, premium team cards
 * Requires wp-content/novamira-sandbox/rvl-team.php. Guarded so re-running does not duplicate.
 */

$home_id = 22;
$rv_id = function () { return substr( md5( uniqid( '', true ) . mt_rand() ), 0, 7 ); };
$px    = function ( $n ) { return array( 'unit' => 'px', 'size' => $n, 'sizes' => array() ); };
$pct   = function ( $n ) { return array( 'unit' => '%', 'size' => $n, 'sizes' => array() ); };
$pad   = function ( $t, $r, $b, $l ) { return array( 'unit' => 'px', 'top' => (string) $t, 'right' => (string) $r, 'bottom' => (string) $b, 'left' => (string) $l, 'isLinked' => false ); };
$gap   = function ( $n ) { return array( 'unit' => 'px', 'size' => $n, 'column' => (string) $n, 'row' => (string) $n, 'isLinked' => true ); };
$zero  = array( 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => true );
$link  = function ( $url ) { return array( 'url' => $url, 'is_external' => '', 'nofollow' => '', 'custom_attributes' => '' ); };
$con   = function ( $s, $els = array(), $inner = true ) use ( $rv_id ) { return array( 'id' => $rv_id(), 'elType' => 'container', 'isInner' => $inner, 'settings' => $s, 'elements' => $els ); };
$wid   = function ( $type, $s ) use ( $rv_id ) { return array( 'id' => $rv_id(), 'elType' => 'widget', 'widgetType' => $type, 'isInner' => false, 'settings' => $s, 'elements' => array() ); };
$h     = function ( $text, $tag, $class, $url = '' ) use ( $wid, $link ) { $s = array( 'title' => $text, 'header_size' => $tag, '_css_classes' => $class ); if ( $url ) { $s['link'] = $link( $url ); } return $wid( 'heading', $s ); };
$txt   = function ( $html, $class ) use ( $wid ) { return $wid( 'text-editor', array( 'editor' => $html, '_css_classes' => $class ) ); };
$icon  = function ( $fa, $class ) use ( $wid ) { return $wid( 'icon', array( 'selected_icon' => array( 'value' => 'fas fa-' . $fa, 'library' => 'fa-solid' ), '_css_classes' => $class ) ); };
$box   = function ( $class, $children, $extra = array() ) use ( $con, $zero ) { return $con( array_merge( array( 'content_width' => 'full', 'flex_direction' => 'column', 'padding' => $zero, 'css_classes' => $class ), $extra ), $children ); };
$has   = function ( $el, $class ) { $c = $el['settings']['css_classes'] ?? ( $el['settings']['_css_classes'] ?? '' ); return in_array( $class, preg_split( '/\s+/', $c ), true ); };
$section = function ( $id, $class, $children, $extra = array() ) use ( $con, $px, $pad, $gap ) {
	$s = array_merge( array(
		'content_width' => 'boxed', 'boxed_width' => $px( 1200 ), 'flex_direction' => 'column', 'flex_gap' => $gap( 44 ),
		'padding' => $pad( 96, 40, 96, 40 ), 'padding_tablet' => $pad( 76, 32, 76, 32 ), 'padding_mobile' => $pad( 60, 20, 60, 20 ),
		'css_classes' => 'rvl-section ' . $class,
	), $extra );
	if ( $id ) { $s['_element_id'] = $id; }
	return $con( $s, $children, false );
};
$head = function ( $eyebrow, $title, $more = '', $more_url = '' ) use ( $box, $h ) {
	$left = $box( 'rvl-head', array( $h( $eyebrow, 'span', 'rvl-eyebrow' ), $h( $title, 'h2', 'rvl-h2' ) ), array( 'flex_gap' => array( 'unit' => 'px', 'size' => 14, 'column' => '14', 'row' => '14', 'isLinked' => true ), 'width' => array( 'unit' => 'custom', 'size' => 'auto', 'sizes' => array() ) ) );
	if ( ! $more ) { return $left; }
	return $box( 'rvl-headrow', array( $left, $h( $more . ' <span aria-hidden="true">→</span>', 'div', 'rvl-more', $more_url ) ), array( 'flex_direction' => 'row', 'flex_wrap' => 'wrap', 'flex_justify_content' => 'space-between', 'flex_align_items' => 'flex-end' ) );
};
$log = array();

/* ---------- Our Team page ---------- */
$tpl = function ( $key ) { return (int) get_posts( array( 'post_type' => 'elementor_library', 'meta_key' => '_rvl_key', 'meta_value' => $key, 'numberposts' => 1, 'fields' => 'ids' ) )[0]; };
$header_wrap = function () use ( $con, $wid, $zero, $tpl ) { return $con( array( 'content_width' => 'full', 'flex_direction' => 'column', 'padding' => $zero, 'css_classes' => 'rvl-sticky' ), array( $wid( 'template', array( 'template_id' => (string) $tpl( 'tpl-header' ) ) ) ), false ); };
$footer_wrap = function () use ( $con, $wid, $zero, $tpl ) { return $con( array( 'content_width' => 'full', 'flex_direction' => 'column', 'padding' => $zero, 'css_classes' => 'rvl-footer-wrap' ), array( $wid( 'template', array( 'template_id' => (string) $tpl( 'tpl-footer' ) ) ) ), false ); };

$team_page = get_posts( array( 'post_type' => 'page', 'post_status' => 'any', 'meta_key' => '_rvl_key', 'meta_value' => 'page-team', 'numberposts' => 1, 'fields' => 'ids' ) );
$team_id   = $team_page ? $team_page[0] : wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'Our Team', 'post_name' => 'our-team' ) );
update_post_meta( $team_id, '_rvl_key', 'page-team' );
update_post_meta( $team_id, '_wp_page_template', 'elementor_canvas' );
update_post_meta( $team_id, '_elementor_edit_mode', 'builder' );
$team_url = get_permalink( $team_id );
\Elementor\Plugin::$instance->documents->get( $team_id, false )->save( array(
	'elements' => array(
		$header_wrap(),
		$section( '', 'rvl-alt rvl-bb rvl-page-head', array( $box( 'rvl-measure', array(
			$h( 'Our People', 'div', 'rvl-eyebrow' ),
			$h( 'The advocates behind the firm', 'h1', 'rvl-h1 rvl-h1-page' ),
			$txt( '<p>Registered with the Punjab Bar Council and based at the District Courts, Faisalabad. Speak to our team about your matter.</p>', 'rvl-body rvl-lead' ),
		), array( 'flex_gap' => $gap( 18 ) ) ) ), array( 'padding' => $pad( 72, 40, 72, 40 ), 'padding_mobile' => $pad( 44, 20, 44, 20 ) ) ),
		$section( 'team', 'rvl-night rvl-team-sec', array( $wid( 'shortcode', array( 'shortcode' => '[rvl_team]', '_css_classes' => 'rvl-team-widget' ) ) ) ),
		$footer_wrap(),
	),
	'settings' => array( 'template' => 'elementor_canvas', 'hide_title' => 'yes', 'post_status' => 'publish' ),
) );
$log['team_page'] = $team_url;

/* Menu + footer links to the team page */
$menu_id = wp_get_nav_menu_object( 'Royal Vision Primary' )->term_id;
foreach ( (array) wp_get_nav_menu_items( $menu_id ) as $item ) {
	if ( 'Our Team' === $item->title ) {
		wp_update_nav_menu_item( $menu_id, $item->ID, array( 'menu-item-title' => 'Our Team', 'menu-item-url' => $team_url, 'menu-item-status' => 'publish', 'menu-item-type' => 'custom', 'menu-item-position' => $item->menu_order ) );
	}
}
$fdoc = \Elementor\Plugin::$instance->documents->get( $tpl( 'tpl-footer' ), false );
$relink = function ( array $els ) use ( &$relink, $team_url, $link ) {
	foreach ( $els as &$el ) {
		if ( 'heading' === ( $el['widgetType'] ?? '' ) && 'Our Team' === ( $el['settings']['title'] ?? '' ) ) { $el['settings']['link'] = $link( $team_url ); }
		$el['elements'] = $relink( $el['elements'] );
	}
	return $els;
};
$fdoc->save( array( 'elements' => $relink( $fdoc->get_elements_data() ) ) );

/* ---------- Homepage ---------- */
$doc  = \Elementor\Plugin::$instance->documents->get( $home_id, false );
$data = $doc->get_elements_data();
$has_why = false;
foreach ( $data as $el ) { if ( $has( $el, 'rvl-why' ) ) { $has_why = true; } }

$seal_svg = '<div class="rvl-seal-ring" aria-hidden="true"><svg viewBox="0 0 300 300" xmlns="http://www.w3.org/2000/svg"><defs><path id="rvl-seal-path" d="M150,150 m-118,0 a118,118 0 1,1 236,0 a118,118 0 1,1 -236,0"/></defs><circle cx="150" cy="150" r="146" fill="none" stroke="#B08D57" stroke-width="1.5"/><circle cx="150" cy="150" r="138" fill="none" stroke="#B08D57" stroke-width="0.75" stroke-dasharray="2 5"/><circle cx="150" cy="150" r="98" fill="rgba(11,31,27,0.6)" stroke="#B08D57" stroke-width="1"/><text font-family="Libre Baskerville, Georgia, serif" font-size="13" fill="#D4B77E"><textPath href="#rvl-seal-path" textLength="735" lengthAdjust="spacing">ROYAL VISION LAW ASSOCIATE • PUNJAB BAR COUNCIL • REG. 13294/23 •</textPath></text></svg></div>';

$out = array();
foreach ( $data as $el ) {
	$id = $el['settings']['_element_id'] ?? '';

	// Hero: taller, with the seal on the right
	if ( $has( $el, 'rvl-hero-dark' ) ) {
		$el['settings']['min_height']        = array( 'unit' => 'vh', 'size' => 86, 'sizes' => array() );
		$el['settings']['min_height_mobile'] = array( 'unit' => 'px', 'size' => 0, 'sizes' => array() );
		$el['settings']['flex_justify_content'] = 'center';
		foreach ( $el['elements'] as &$row ) {
			$has_seal = false;
			foreach ( $row['elements'] as $c ) { if ( $has( $c, 'rvl-hero-seal-col' ) ) { $has_seal = true; } }
			if ( ! $has_seal && $has( $row, 'rvl-hero-row' ) ) {
				$row['settings']['flex_align_items'] = 'center';
				$row['elements'][] = $box( 'rvl-hero-seal-col', array(
					$box( 'rvl-seal', array(
						$wid( 'html', array( 'html' => $seal_svg, '_css_classes' => 'rvl-seal-svg' ) ),
						$icon( 'balance-scale', 'rvl-seal-icon' ),
						$h( 'Est. 2023', 'div', 'rvl-seal-est' ),
					) ),
				), array( 'width' => $pct( 42 ), 'hide_mobile' => 'hidden-mobile', 'flex_align_items' => 'center', 'flex_justify_content' => 'center' ) );
				$log['seal'] = 'added';
			}
		}
		unset( $row );
		$out[] = $el;
		continue;
	}

	// Team: driven by the Team post type
	if ( 'team' === $id ) {
		$el['settings']['css_classes'] = 'rvl-section rvl-night rvl-team-sec';
		$el['elements'] = array(
			$head( 'Our People', 'The advocates behind the firm', 'Meet Our Team', $team_url ),
			$wid( 'shortcode', array( 'shortcode' => '[rvl_team limit="4"]', '_css_classes' => 'rvl-team-widget' ) ),
		);
		$log['team'] = 'shortcode';
	}

	// Contact: dark band
	if ( 'contact' === $id ) {
		$el['settings']['css_classes'] = 'rvl-section rvl-dark rvl-contact-dark';
		$log['contact'] = 'dark';
	}

	$out[] = $el;

	// Why choose us, right after Practice Areas
	if ( 'practice' === $id && ! $has_why ) {
		$items = array(
			array( 'award', 'Registered law firm', 'Registered with the Punjab Bar Council under Reg. No. 13294/23.' ),
			array( 'landmark', 'At the District Courts', 'Our chambers are at 45 District Courts, Faisalabad, where many matters are heard.' ),
			array( 'comments', 'Plain-language advice', 'We explain your position and your options clearly before you decide.' ),
			array( 'user-shield', 'Discretion', 'Your matter is handled in confidence from the first conversation.' ),
		);
		$cells = array();
		foreach ( $items as $it ) {
			$cells[] = $box( 'rvl-why-item', array( $icon( $it[0], 'rvl-why-icon' ), $h( $it[1], 'h3', 'rvl-h3 rvl-why-title' ), $txt( '<p>' . $it[2] . '</p>', 'rvl-body rvl-small' ) ), array(
				'flex_gap' => $gap( 14 ), 'padding' => array( 'unit' => 'px', 'top' => '34', 'right' => '28', 'bottom' => '30', 'left' => '28', 'isLinked' => false ),
			) );
		}
		$out[] = $section( 'why', 'rvl-white rvl-why', array(
			$head( 'Why Choose Us', 'A firm you can rely on' ),
			$con( array(
				'container_type' => 'grid', 'content_width' => 'full', 'padding' => $zero, 'css_classes' => 'rvl-grid rvl-why-grid',
				'grid_columns_grid' => array( 'unit' => 'fr', 'size' => 4, 'sizes' => array() ),
				'grid_columns_grid_tablet' => array( 'unit' => 'fr', 'size' => 2, 'sizes' => array() ),
				'grid_columns_grid_mobile' => array( 'unit' => 'fr', 'size' => 1, 'sizes' => array() ),
				'grid_rows_grid' => array( 'unit' => 'custom', 'size' => 'auto', 'sizes' => array() ),
				'grid_gaps' => array( 'unit' => 'px', 'column' => '24', 'row' => '24', 'isLinked' => true ),
			), $cells ),
		) );
		$log['why'] = 'added';
	}
}
$doc->save( array( 'elements' => $out, 'settings' => array( 'template' => 'elementor_canvas', 'hide_title' => 'yes' ) ) );

/* ---------- CSS ---------- */
$kit_doc = \Elementor\Plugin::$instance->documents->get( (int) get_option( 'elementor_active_kit' ), false );
$kit_set = array_filter( (array) $kit_doc->get_settings(), function ( $k ) { return ! is_int( $k ); }, ARRAY_FILTER_USE_KEY );
$add = <<<'CSS'

/* Premium law layer */
@keyframes rvl-spin{to{transform:rotate(360deg)}}

/* Ornamental divider under section headings (line, diamond, line) */
selector .rvl-head .rvl-h2 .elementor-heading-title::after{width:96px;height:9px;margin-top:20px;background:linear-gradient(var(--rvl-gold),var(--rvl-gold)) 0 50%/40px 1px no-repeat,linear-gradient(var(--rvl-gold),var(--rvl-gold)) 100% 50%/40px 1px no-repeat,linear-gradient(45deg,transparent 35%,var(--rvl-gold) 35%,var(--rvl-gold) 65%,transparent 65%) 50% 50%/9px 9px no-repeat}

/* Hero seal */
selector .rvl-hero-dark>.e-con-inner{justify-content:center}
selector .rvl-hero-seal-col{align-self:center}
selector .rvl-seal{position:relative;width:310px!important;--width:310px;height:310px;margin:0 auto}
selector .rvl-seal>.elementor-element{position:absolute!important}
selector .rvl-seal .rvl-seal-svg{inset:0;width:100%!important}
selector .rvl-seal-ring svg{display:block;width:100%;height:auto;animation:rvl-spin 60s linear infinite;filter:drop-shadow(0 18px 40px rgba(0,0,0,.45))}
selector .rvl-seal .rvl-seal-icon{top:50%;left:50%;transform:translate(-50%,-64%);width:auto!important}
selector .rvl-seal-icon .elementor-icon svg{width:64px;height:64px;fill:var(--rvl-gold-light)}
selector .rvl-seal .rvl-seal-est{top:58%;left:0;right:0;width:100%!important}
selector .rvl-seal-est .elementor-heading-title{font-family:var(--rvl-serif);font-size:13px;letter-spacing:.32em;text-transform:uppercase;color:var(--rvl-gold-light);text-align:center}
@media (max-width:1024px){selector .rvl-seal{width:240px!important;--width:240px;height:240px}selector .rvl-seal-icon .elementor-icon svg{width:50px;height:50px}}

/* Why choose us */
selector .rvl-why-item{position:relative;background:#FFFFFF;border:1px solid var(--rvl-line);border-radius:2px;transition:transform .25s ease,box-shadow .25s ease,border-color .25s ease;overflow:hidden}
selector .rvl-why-item::after{content:"";position:absolute;left:0;right:0;top:0;height:3px;background:var(--rvl-gold);transform:scaleX(0);transform-origin:left;transition:transform .35s ease}
selector .rvl-why-item:hover{transform:translateY(-4px);border-color:rgba(176,141,87,.5);box-shadow:0 22px 44px -28px rgba(15,38,33,.5)}
selector .rvl-why-item:hover::after{transform:scaleX(1)}
selector .rvl-why-icon .elementor-icon{width:64px;height:64px;border-radius:50%;background:var(--rvl-night);display:flex;align-items:center;justify-content:center;box-shadow:inset 0 0 0 1px var(--rvl-gold)}
selector .rvl-why-icon .elementor-icon svg{width:26px;height:26px;fill:var(--rvl-gold-light)}
selector .rvl-why-title .elementor-heading-title{font-size:20px}

/* Team cards ([rvl_team]) */
selector .rvl-team{display:grid;grid-template-columns:repeat(auto-fit,minmax(min(100%,260px),1fr));gap:32px;margin:0 auto}
selector .rvl-team[data-count="1"]{max-width:360px}
selector .rvl-team[data-count="2"]{max-width:760px}
selector .rvl-team[data-count="3"]{max-width:1120px}
selector .rvl-tm{position:relative;background:rgba(255,255,255,.035);border:1px solid rgba(212,183,126,.28);border-radius:2px;overflow:hidden;transition:transform .3s ease,border-color .3s ease,box-shadow .3s ease}
selector .rvl-tm:hover,selector .rvl-tm:focus-within{transform:translateY(-6px);border-color:var(--rvl-gold);box-shadow:0 30px 60px -32px rgba(0,0,0,.85)}
selector .rvl-tm-media{position:relative;aspect-ratio:4/5;overflow:hidden;background:radial-gradient(circle at 50% 32%,#1E4A3E 0%,#0B1F1B 72%)}
selector .rvl-tm-media::after{content:"";position:absolute;inset:14px;border:1px solid rgba(212,183,126,.45);pointer-events:none;z-index:2}
selector .rvl-tm-photo{display:block;width:100%;height:100%;object-fit:cover;transition:transform .7s ease}
selector .rvl-tm:hover .rvl-tm-photo{transform:scale(1.06)}
selector .rvl-tm-mono{position:absolute;inset:0;display:flex;align-items:center;justify-content:center}
selector .rvl-tm-mono span{display:flex;align-items:center;justify-content:center;width:168px;height:168px;border-radius:50%;border:1px solid rgba(212,183,126,.6);box-shadow:inset 0 0 0 8px rgba(212,183,126,.08);font-family:var(--rvl-serif);font-size:64px;letter-spacing:.06em;color:var(--rvl-gold-light)}
selector .rvl-tm-intro{position:absolute;left:0;right:0;bottom:0;z-index:1;padding:60px 26px 26px;background:linear-gradient(to top,rgba(11,31,27,.97) 45%,rgba(11,31,27,0));transform:translateY(100%);transition:transform .4s ease}
selector .rvl-tm-intro p{margin:0;font-family:var(--rvl-sans);font-size:15px;line-height:1.6;color:#E6ECE9}
selector .rvl-tm:hover .rvl-tm-intro,selector .rvl-tm:focus-within .rvl-tm-intro{transform:none}
@media (hover:none){selector .rvl-tm-intro{transform:none}}
selector .rvl-tm-body{padding:24px 24px 28px;text-align:center}
selector .rvl-tm-role{margin:0 0 10px;font-family:var(--rvl-sans);font-size:12px;font-weight:600;letter-spacing:.24em;text-transform:uppercase;color:var(--rvl-gold-light)}
selector .rvl-tm-name{margin:0;font-family:var(--rvl-serif);font-weight:400;font-size:24px;line-height:1.25;color:#FFFFFF}
selector .rvl-tm-focus{margin:8px 0 0;font-family:var(--rvl-sans);font-size:14px;color:#A9C4B6}
selector .rvl-tm-links{display:flex;flex-wrap:wrap;justify-content:center;gap:10px;margin-top:20px}
selector .rvl-tm-links a{font-family:var(--rvl-sans);font-size:13px;font-weight:600;letter-spacing:.04em;padding:10px 14px;border:1px solid rgba(212,183,126,.55);border-radius:2px;color:var(--rvl-gold-light);transition:background-color .2s ease,color .2s ease}
selector .rvl-tm-links a:hover,selector .rvl-tm-links a:focus-visible{background:var(--rvl-gold);border-color:var(--rvl-gold);color:var(--rvl-night)}
selector .rvl-team-sec .rvl-h2 .elementor-heading-title{color:#FFFFFF}
selector .rvl-team-sec .rvl-eyebrow .elementor-heading-title{color:var(--rvl-gold-light)}
selector .rvl-team-sec .rvl-more .elementor-heading-title,selector .rvl-team-sec .rvl-more .elementor-heading-title a{color:var(--rvl-gold-light)!important}
@media (prefers-reduced-motion:reduce){selector .rvl-seal-ring svg{animation:none}selector .rvl-tm,selector .rvl-tm-photo,selector .rvl-tm-intro,selector .rvl-why-item{transition:none}}

/* Dark contact band */
selector .rvl-contact-dark{background:radial-gradient(ellipse at 85% 0%,#1A3F35 0%,var(--rvl-night) 60%)}
selector .rvl-contact-dark .rvl-h2 .elementor-heading-title{color:#FFFFFF}
selector .rvl-contact-dark .rvl-eyebrow .elementor-heading-title{color:var(--rvl-gold-light)}
selector .rvl-contact-dark .rvl-body p{color:#DCE5E0}
selector .rvl-contact-dark .rvl-details{border-top-color:rgba(255,255,255,.18)}
selector .rvl-contact-dark .rvl-detail{border-bottom-color:rgba(255,255,255,.18)}
selector .rvl-contact-dark .rvl-detail .k{color:#A9C4B6}
selector .rvl-contact-dark .rvl-detail .v,selector .rvl-contact-dark .rvl-detail a.v{color:#FFFFFF}
selector .rvl-contact-dark .rvl-detail a.v:hover{color:var(--rvl-gold-light)}
selector .rvl-contact-dark .rvl-form-card{box-shadow:0 34px 70px -36px rgba(0,0,0,.8)}

/* Premium fixes: icon colours and divider diamond */
selector .rvl-seal-icon .elementor-icon,selector .rvl-why-icon .elementor-icon{color:var(--rvl-gold-light)!important}
selector .rvl-seal-icon .elementor-icon svg,selector .rvl-why-icon .elementor-icon svg{fill:var(--rvl-gold-light)!important}
selector .rvl-head .rvl-h2 .elementor-heading-title::after{content:"\25C6";font-family:Arial,sans-serif;font-size:9px;line-height:9px;color:var(--rvl-gold);text-align:center;width:96px;height:9px;background:linear-gradient(var(--rvl-gold),var(--rvl-gold)) 0 50%/38px 1px no-repeat,linear-gradient(var(--rvl-gold),var(--rvl-gold)) 100% 50%/38px 1px no-repeat}
CSS;
if ( false === strpos( $kit_set['custom_css'], '/* Premium law layer */' ) ) { $kit_set['custom_css'] .= $add; }
$kit_doc->save( array( 'settings' => $kit_set ) );

\Elementor\Plugin::$instance->files_manager->clear_cache();
do_action( 'litespeed_purge_all' );
return array( 'log' => $log, 'top_level' => count( $out ) );
