<?php
// Royal Vision fashion home page, design v5: builds Elementor data (containers + native widgets, no HTML widget).
// Loaded server-side and run as: $data = $rv5( $cfg );
$rv5 = function ( $cfg ) {
	// Royal Vision home, design v5. Native Elementor containers + widgets only (no HTML widget).
	$ID = 0;
	$nid = function () use ( &$ID ) { $ID++; return substr( md5( 'rv5home' . $ID ), 0, 7 ); };
	$C = [ 'ink' => '#1A1A1A', 'plum' => '#2A1838', 'purple' => '#7B3FF2', 'line' => '#ECE6E1', 'mute' => '#77707D', 'body' => '#4A4350', 'lilac' => '#F2ECFE', 'sand' => '#EBDDD2', 'wine' => '#7A2230' ];
	$FH = 'Cormorant'; $FB = 'Jost';
	$WA = 'https://wa.me/923227782932';
	$wa = function ( $t ) use ( $WA ) { return $WA . '?text=' . rawurlencode( $t ); };

	$px = function ( $n ) { return [ 'unit' => 'px', 'size' => $n, 'sizes' => [] ]; };
	$box = function ( $t, $r = null, $b = null, $l = null ) { $r = $r ?? $t; $b = $b ?? $t; $l = $l ?? $r; return [ 'unit' => 'px', 'top' => (string) $t, 'right' => (string) $r, 'bottom' => (string) $b, 'left' => (string) $l, 'isLinked' => ( $t === $r && $r === $b && $b === $l ) ]; };
	$gap = function ( $c, $r = null ) { $r = $r ?? $c; return [ 'column' => (string) $c, 'row' => (string) $r, 'isLinked' => $c === $r, 'unit' => 'px', 'size' => $c ]; };
	$link = function ( $url ) { return [ 'url' => $url, 'is_external' => 0 === strpos( $url, 'http' ) ? 'on' : '', 'nofollow' => '' ]; };

	$con = function ( $s, $kids = [], $inner = true ) use ( $nid, $box, $gap ) {
		$base = [ 'content_width' => 'full', 'flex_direction' => 'column', 'flex_gap' => $gap( 0 ), 'padding' => $box( 0 ), 'padding_tablet' => $box( 0 ), 'padding_mobile' => $box( 0 ) ];
		if ( isset( $s['cls'] ) ) { $s['css_classes'] = $s['cls']; $s['_css_classes'] = $s['cls']; unset( $s['cls'] ); }
		if ( isset( $s['anchor'] ) ) { $s['_element_id'] = $s['anchor']; unset( $s['anchor'] ); }
		return [ 'id' => $nid(), 'elType' => 'container', 'isInner' => $inner, 'settings' => array_merge( $base, $s ), 'elements' => $kids ];
	};
	$row = function ( $s, $kids = [] ) use ( $con ) { return $con( array_merge( [ 'flex_direction' => 'row', 'flex_wrap' => 'wrap' ], $s ), $kids ); };
	$a = function ( $url, $s, $kids ) use ( $con, $link ) { return $con( array_merge( [ 'html_tag' => 'a', 'link' => $link( $url ) ], $s ), $kids ); };
	$w = function ( $type, $s ) use ( $nid ) {
		if ( isset( $s['cls'] ) ) { $s['_css_classes'] = $s['cls']; unset( $s['cls'] ); }
		return [ 'id' => $nid(), 'elType' => 'widget', 'widgetType' => $type, 'settings' => $s, 'elements' => [] ];
	};
	$typo = function ( $p, $fam, $size, $o = [] ) use ( $px ) {
		$t = [ $p . 'typography_typography' => 'custom', $p . 'typography_font_family' => $fam, $p . 'typography_font_size' => $px( $size ), $p . 'typography_font_weight' => (string) ( $o['w'] ?? '400' ) ];
		if ( isset( $o['t'] ) ) $t[ $p . 'typography_font_size_tablet' ] = $px( $o['t'] );
		if ( isset( $o['m'] ) ) $t[ $p . 'typography_font_size_mobile' ] = $px( $o['m'] );
		if ( isset( $o['lh'] ) ) $t[ $p . 'typography_line_height' ] = [ 'unit' => 'em', 'size' => $o['lh'], 'sizes' => [] ];
		if ( isset( $o['ls'] ) ) $t[ $p . 'typography_letter_spacing' ] = [ 'unit' => 'em', 'size' => $o['ls'], 'sizes' => [] ];
		if ( ! empty( $o['up'] ) ) $t[ $p . 'typography_text_transform' ] = 'uppercase';
		return $t;
	};
	$h = function ( $text, $o = [] ) use ( $w, $typo, $C, $FB ) {
		$s = array_merge( [ 'title' => $text, 'header_size' => $o['tag'] ?? 'div', 'title_color' => $o['color'] ?? $C['ink'] ], $typo( '', $o['font'] ?? $FB, $o['size'] ?? 16, $o ) );
		if ( isset( $o['align'] ) ) $s['align'] = $o['align'];
		if ( isset( $o['url'] ) ) $s['link'] = [ 'url' => $o['url'], 'is_external' => '', 'nofollow' => '' ];
		if ( isset( $o['cls'] ) ) $s['cls'] = $o['cls'];
		return $w( 'heading', $s );
	};
	$eyebrow = function ( $t, $o = [] ) use ( $h, $C ) { return $h( $t, array_merge( [ 'size' => 14.5, 'w' => '500', 'ls' => 0.28, 'up' => true, 'lh' => 1.4, 'color' => $C['purple'], 'm' => 12.5 ], $o ) ); };
	$display = function ( $t, $size, $o = [] ) use ( $h, $FH ) { return $h( $t, array_merge( [ 'tag' => 'h2', 'font' => $FH, 'w' => '600', 'size' => $size, 'lh' => 1 ], $o ) ); };
	$p = function ( $text, $o = [] ) use ( $w, $typo, $C, $FB ) {
		$s = array_merge( [ 'editor' => '<p>' . $text . '</p>', 'text_color' => $o['color'] ?? $C['body'] ], $typo( '', $FB, $o['size'] ?? 17, array_merge( [ 'lh' => 1.6 ], $o ) ) );
		if ( isset( $o['align'] ) ) $s['align'] = $o['align'];
		if ( isset( $o['cls'] ) ) $s['cls'] = $o['cls'];
		return $w( 'text-editor', $s );
	};
	$btn = function ( $text, $url, $o = [] ) use ( $w, $typo, $box, $C, $FB, $link ) {
		$light = ( $o['style'] ?? 'dark' ) === 'light';
		$s = array_merge( [ 'text' => $text, 'link' => $link( $url ),
			'button_text_color' => $light ? $C['ink'] : '#FFFFFF', 'background_color' => $light ? '#FFFFFF' : $C['ink'],
			'hover_color' => $light ? $C['ink'] : '#FFFFFF', 'button_background_hover_color' => $light ? '#C9B4FF' : $C['purple'],
			'border_radius' => $box( 999 ), 'text_padding' => $box( $o['py'] ?? 21, $o['px'] ?? 38 ) ],
			$typo( '', $FB, $o['size'] ?? 15.5, [ 'w' => '500', 'ls' => $o['ls'] ?? 0.18, 'up' => $o['up'] ?? true, 'lh' => 1.2 ] ) );
		if ( isset( $o['icon'] ) ) { $s['selected_icon'] = $o['icon']; $s['icon_align'] = 'left'; $s['icon_indent'] = [ 'unit' => 'px', 'size' => 10, 'sizes' => [] ]; }
		if ( isset( $o['align'] ) ) $s['align'] = $o['align'];
		$s['cls'] = trim( 'v5-btn ' . ( $o['cls'] ?? '' ) );
		return $w( 'button', $s );
	};
	$img = function ( $key, $alt, $cls = '' ) use ( $w, $cfg ) {
		$src = $cfg['img']( $key );
		return $w( 'image', [ 'image' => [ 'id' => $src['id'], 'url' => $src['url'], 'alt' => $alt ], 'image_size' => 'full', 'cls' => trim( 'v5-img ' . $cls ) ] );
	};
	$fa = function ( $name, $lib = 'fa-solid' ) { return [ 'value' => ( $lib === 'fa-brands' ? 'fab ' : 'fas ' ) . $name, 'library' => $lib ]; };
	$section = function ( $s, $kids ) use ( $con, $box, $gap ) {
		return $con( array_merge( [ 'content_width' => 'boxed', 'boxed_width' => [ 'unit' => 'px', 'size' => 1520, 'sizes' => [] ], 'flex_gap' => $gap( 26 ) ], $s ), $kids, false );
	};

	// ---------- data ----------
	$RAW = [ [ 'Tropical Bloom', 'Khaddar', '#1A1A1A' ], [ 'Ice Slit', 'Khaddar', '#8FD3D0' ], [ 'Janan', 'Khaddar', '#1F2C4F' ], [ 'Terilis', 'Khaddar', '#B48BA8' ], [ 'Vela', 'Khaddar', '#1F2C4F' ], [ 'Limelight', 'Khaddar', '#F1C6CF' ], [ 'Maya', 'Khaddar', '#D49A2C' ], [ 'Serena', 'Khaddar', '#8C8F96' ], [ 'Solid', 'Khaddar', '#6B4A35' ], [ 'Gulaal', 'Khaddar', '#3B4FA0' ], [ 'Greenry', 'Khaddar', '#3E8E6A' ], [ 'Verdant', 'Khaddar', '#EFE8D8' ], [ 'Resham', 'Khaddar', '#7A2230' ], [ 'Floral', 'Khaddar', '#3E8E6A' ], [ 'Hermony', 'Khaddar', '#B5532E' ], [ 'Gold', 'Karandi', '#3B4FA0' ], [ 'Peachy', 'Karandi', '#F2A98E' ], [ 'Texture', 'Karandi', '#B48BA8' ], [ 'Berry', 'Karandi', '#7A2230' ], [ 'Zareen', 'Karandi', '#D49A2C' ], [ 'Miraaz', 'Karandi', '#6B4A35' ], [ 'Aura', 'Karandi', '#8C8F96' ], [ 'Twilight', 'Karandi', '#3E8E6A' ], [ 'Dhani', 'Karandi', '#3E8E6A' ] ];
	$P = [];
	foreach ( $RAW as $i => $r ) {
		$id = $i + 1; $n = str_pad( (string) $id, 2, '0', STR_PAD_LEFT ); $kh = $r[1] === 'Khaddar';
		$compare = $kh ? 4890 : 5290; $price = $kh ? 3990 : 4290;
		$P[ $id ] = [ 'name' => $r[0], 'fabric' => $r[1], 'swatch' => $r[2], 'pid' => (int) ( $cfg['pid'][ $id ] ?? 0 ), 'price' => 'Rs ' . number_format( $price ), 'compare' => 'Rs ' . number_format( $compare ),
			'off' => '-' . round( ( 1 - $price / $compare ) * 100 ) . '%', 'new' => $id % 4 === 1, 'front' => "p$n-front", 'detail' => "p$n-detail" ];
	}
	// Elementor dynamic tag string, e.g. a product's live title or price.
	$tag = function ( $name, $settings ) { return '[elementor-tag id="' . substr( md5( $name . wp_json_encode( $settings ) ), 0, 7 ) . '" name="' . $name . '" settings="' . rawurlencode( wp_json_encode( $settings ) ) . '"]'; };

	$out = [];

	// ---------- 1. utility bar: contact left, rotating messages centre, help right ----------
	$ann = [ 'Free delivery all over Pakistan', 'Winter Sale is live · upto 19% off', 'Any 2 suits for Rs 6,990', 'Cash on Delivery · 100% original brands' ];
	$util = function ( $items, $cls ) use ( $w, $typo, $px, $FB, $link ) {
		return $w( 'icon-list', array_merge( [ 'view' => 'inline', 'icon_list' => $items, 'icon_color' => '#C9B4FF', 'text_color' => 'rgba(255,255,255,0.82)', 'text_color_hover' => '#FFFFFF',
			'icon_size' => $px( 13 ), 'text_indent' => $px( 6 ), 'space_between' => $px( 22 ), 'cls' => 'v5-util ' . $cls ], $typo( 'icon_typography_', $FB, 12.5, [ 'w' => '500', 'ls' => 0.04 ] ) ) );
	};
	$out[] = $con( [ 'cls' => 'v5-ann', 'flex_direction' => 'row', 'flex_wrap' => 'nowrap', 'flex_justify_content' => 'space-between', 'flex_align_items' => 'center', 'min_height' => $px( 38 ), 'background_background' => 'classic', 'background_color' => $C['ink'] ], [
		$util( [ [ 'text' => 'WhatsApp 0322 778 2932', 'selected_icon' => $fa( 'fa-whatsapp', 'fa-brands' ), 'link' => $link( $WA ), '_id' => 'v5u1' ] ], 'v5-util-l' ),
		$h( implode( '', array_map( function ( $t ) { return '<span>' . $t . '</span>'; }, $ann ) ), [ 'size' => 12.5, 'm' => 11.5, 'w' => '600', 'ls' => 0.14, 'up' => true, 'color' => '#FFFFFF', 'align' => 'center', 'cls' => 'v5-ann-rot' ] ),
		$util( [
			[ 'text' => 'Track order', 'selected_icon' => $fa( 'fa-map-marker-alt' ), 'link' => $link( $wa( 'Assalam o Alaikum, mujhe apna order track karna hai. Order number: ' ) ), '_id' => 'v5u2' ],
			[ 'text' => 'Help', 'selected_icon' => $fa( 'fa-question-circle' ), 'link' => $link( '/contact-us/' ), '_id' => 'v5u3' ],
		], 'v5-util-r' ),
	], false );

	// ---------- 2. header: menu left, logo centre, actions right; shrinks once scrolled ----------
	$hicon = function ( $value, $url, $label ) use ( $w, $px, $C, $link ) {
		return $w( 'icon', [ 'selected_icon' => [ 'value' => $value, 'library' => 'fa-regular' ], 'link' => $link( $url ), 'primary_color' => $C['ink'], 'hover_primary_color' => $C['purple'], 'size' => $px( 19 ), 'cls' => 'v5-hicon' ] );
	};
	$out[] = $con( [ 'cls' => 'v5-head', 'sticky' => 'top', 'sticky_on' => [ 'desktop', 'tablet', 'mobile' ], 'sticky_effects_offset' => 40, 'z_index' => 40, 'background_background' => 'classic', 'background_color' => '#FFFFFF',
		'border_border' => 'solid', 'border_width' => [ 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '1', 'left' => '0', 'isLinked' => false ], 'border_color' => $C['line'] ], [
		$con( [ 'cls' => 'v5-head-bar', 'flex_direction' => 'row', 'flex_wrap' => 'nowrap', 'flex_align_items' => 'center' ], [
			$w( 'nav-menu', array_merge( [ 'menu' => $cfg['menu'], 'layout' => 'horizontal', 'align_items' => 'left', 'pointer' => 'underline', 'animation_line' => 'grow', 'dropdown' => 'tablet', 'toggle' => 'burger', 'full_width' => 'stretch', 'text_align' => 'aside',
				'color_menu_item' => $C['ink'], 'color_menu_item_hover' => $C['purple'], 'pointer_color_menu_item_hover' => $C['purple'], 'color_menu_item_active' => $C['ink'], 'pointer_color_menu_item_active' => $C['ink'],
				'padding_horizontal_menu_item' => $px( 0 ), 'padding_vertical_menu_item' => $px( 8 ), 'menu_space_between' => $px( 30 ), 'pointer_width' => $px( 1.5 ),
				'color_dropdown_item' => $C['ink'], 'background_color_dropdown_item' => '#FFFFFF', 'color_dropdown_item_hover' => $C['purple'], 'background_color_dropdown_item_hover' => $C['lilac'],
				'padding_horizontal_dropdown_item' => $px( 24 ), 'padding_vertical_dropdown_item' => $px( 16 ), 'dropdown_divider_border' => 'solid', 'dropdown_divider_width' => $px( 1 ), 'dropdown_divider_color' => $C['line'],
				'toggle_color' => $C['ink'], 'toggle_background_color' => 'rgba(0,0,0,0)', 'toggle_size' => $px( 22 ), 'cls' => 'v5-nav' ],
				$typo( 'menu_', $FB, 12.5, [ 'w' => '500', 'ls' => 0.14, 'up' => true ] ), $typo( 'dropdown_', $FB, 14, [ 'w' => '500', 'ls' => 0.1, 'up' => true ] ) ) ),
			$w( 'image', [ 'image' => $cfg['img']( 'logo' ) + [ 'alt' => 'Royal Vision Store' ], 'image_size' => 'full', 'link_to' => 'custom', 'link' => $link( '/' ), 'cls' => 'v5-logo' ] ),
			$con( [ 'cls' => 'v5-head-r', 'flex_direction' => 'row', 'flex_wrap' => 'nowrap', 'flex_justify_content' => 'flex-end', 'flex_align_items' => 'center', 'flex_gap' => $gap( 4 ) ], [
				$w( 'search-form', [ 'skin' => 'full_screen', 'placeholder' => 'Search suits, colours, fabrics…', 'toggle_color' => $C['ink'], 'toggle_size' => $px( 40 ), 'cls' => 'v5-search' ] ),
				$hicon( 'far fa-user', '/my-account/', 'Account' ),
				$hicon( 'far fa-heart', '/wishlist/', 'Wishlist' ),
				$w( 'woocommerce-menu-cart', [ 'icon' => 'bag-light', 'items_indicator' => 'bubble', 'hide_empty_indicator' => '', 'show_subtotal' => '', 'toggle_button_text_color' => $C['ink'], 'toggle_button_icon_color' => $C['ink'], 'toggle_button_background_color' => 'rgba(0,0,0,0)', 'toggle_button_border_width' => $px( 0 ), 'items_indicator_text_color' => '#FFFFFF', 'items_indicator_background_color' => $C['purple'], 'cls' => 'v5-cart' ] ),
			] ),
		] ),
	], false );

	// ---------- 3. hero: text slider + moving "runway wall" of suits ----------
	$slides = [
		[ 'Winter Sale', 'Upto 19% Off', 'Light khaddar and karandi, 3-piece unstitched. Free delivery all over Pakistan.', 'Shop the Sale', '#new-arrivals', 'LIVE NOW' ],
		[ 'New Collection', "Karandi '26", 'Soft woven karandi in deep berry, earth and gold tones.', 'Shop Karandi', '#karandi', 'JUST IN' ],
		[ 'Bundle Offer', 'Any 2 for Rs 6,990', 'Mix and match any two winter suits. Tap + Bundle on the ones you like.', 'Pick Any 2', '#new-arrivals', 'LIMITED OFFER' ],
	];
	$slideEls = []; $items = [];
	foreach ( $slides as $k => $s ) {
		$items[] = [ 'slide_title' => 'Slide ' . ( $k + 1 ), '_id' => 'v6s' . $k ];
		$slideEls[] = $con( [ 'cls' => 'v6-slide', 'flex_justify_content' => 'center', 'flex_gap' => $gap( 16 ) ], [
			$eyebrow( $s[0] ),
			$h( $s[1], [ 'tag' => $k === 0 ? 'h1' : 'h2', 'font' => $FH, 'w' => '600', 'size' => 104, 't' => 68, 'm' => 50, 'lh' => 0.95, 'cls' => 'v6-title' ] ),
			$p( $s[2], [ 'size' => 18, 'm' => 16, 'cls' => 'v6-line' ] ),
			$row( [ 'cls' => 'v5-hero-ctas', 'flex_align_items' => 'center', 'flex_gap' => $gap( 14 ) ], [
				$btn( $s[3], $s[4] ),
				$h( $s[5], [ 'size' => 14.5, 'm' => 12.5, 'w' => '700', 'ls' => 0.16, 'color' => $C['purple'], 'cls' => 'v5-live' ] ),
			] ),
		] );
	}
	$tile = function ( $id ) use ( $a, $img, $h, $wa, $P ) {
		$p = $P[ $id ];
		return $a( $wa( "Assalam o Alaikum, mujhe {$p['name']} ({$p['fabric']}, {$p['price']}) chahiye." ), [ 'cls' => 'v6-tile' ], [
			$img( $p['front'], $p['name'] . ', ' . $p['fabric'] . ' suit' ),
			$h( $p['name'] . ' <span>· ' . $p['fabric'] . '</span>', [ 'size' => 13, 'w' => '600', 'lh' => 1.2, 'cls' => 'v6-chip' ] ),
		] );
	};
	// Each column lists its suits twice so the CSS loop (translateY -50%) is seamless.
	$col = function ( $ids ) use ( $con, $tile ) { return $con( [ 'cls' => 'v6-col' ], array_map( $tile, array_merge( $ids, $ids ) ) ); };
	$out[] = $con( [ 'cls' => 'v6-hero', 'flex_direction' => 'row', 'flex_wrap' => 'nowrap', 'background_background' => 'classic', 'background_color' => '#EBDDD2' ], [
		$con( [ 'cls' => 'v6-hero-left', 'flex_justify_content' => 'center' ], [
			array_merge( $w( 'nested-carousel', [ 'carousel_items' => $items, 'slides_to_show' => '1', 'slides_to_show_tablet' => '1', 'slides_to_show_mobile' => '1', 'slides_to_scroll' => '1',
				'autoplay' => 'yes', 'autoplay_speed' => 6000, 'pause_on_hover' => 'yes', 'pause_on_interaction' => '', 'infinite' => 'yes', 'speed' => 700,
				'arrows' => 'yes', 'pagination' => 'bullets', 'image_spacing_custom' => $px( 40 ),
				'navigation_previous_icon' => $fa( 'fa-arrow-left' ), 'navigation_next_icon' => $fa( 'fa-arrow-right' ), 'cls' => 'v6-text-car' ] ), [ 'elements' => $slideEls ] ),
		] ),
		$con( [ 'cls' => 'v6-wall', 'flex_direction' => 'row', 'flex_wrap' => 'nowrap' ], [
			$col( [ 13, 1, 16, 7, 19, 3 ] ),
			$col( [ 21, 10, 17, 22, 5, 24 ] ),
			$col( [ 14, 18, 20, 9, 11, 6 ] ),
		] ),
	], false );

	// ---------- 4. sale marquee ----------
	$marqItems = [ 'Winter Sale is live', 'Upto 19% off', 'Any 2 suits for Rs 6,990', 'Free delivery all over Pakistan', 'Cash on Delivery', '100% original brands' ];
	$t = ''; for ( $k = 0; $k < 4; $k++ ) foreach ( $marqItems as $it ) $t .= '<span>' . $it . '</span><i>✦</i>';
	$out[] = $con( [ 'cls' => 'v5-marq', 'flex_justify_content' => 'center', 'min_height' => $px( 58 ), 'background_background' => 'classic', 'background_color' => $C['purple'] ], [
		$h( $t, [ 'size' => 16, 'm' => 14, 'w' => '500', 'ls' => 0.2, 'up' => true, 'color' => '#FFFFFF', 'cls' => 'v5-marq-track' ] ),
	], false );

	// ---------- 5. trust row ----------
	$trust = function ( $t, $sub, $icon ) use ( $w, $typo, $px, $C, $FB ) {
		return $w( 'icon-box', array_merge( [ 'selected_icon' => $icon, 'view' => 'stacked', 'shape' => 'circle', 'position' => 'left', 'title_text' => $t, 'description_text' => $sub, 'title_size' => 'div',
			'primary_color' => $C['lilac'], 'secondary_color' => $C['purple'], 'icon_size' => $px( 20 ), 'icon_padding' => $px( 14 ), 'icon_space' => $px( 14 ), 'title_bottom_space' => $px( 2 ),
			'title_color' => $C['ink'], 'description_color' => $C['mute'], 'content_vertical_alignment' => 'middle', 'cls' => 'v5-trust' ],
			$typo( 'title_typography_', $FB, 16, [ 'w' => '700', 'lh' => 1.35 ] ), $typo( 'description_typography_', $FB, 15, [ 'lh' => 1.35, 'm' => 13 ] ) ) );
	};
	$out[] = $con( [ 'cls' => 'v5-trust-row', 'content_width' => 'boxed', 'boxed_width' => [ 'unit' => 'px', 'size' => 1520, 'sizes' => [] ],
		'border_border' => 'solid', 'border_width' => [ 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '1', 'left' => '0', 'isLinked' => false ], 'border_color' => $C['line'] ], [
		$con( [ 'cls' => 'v5-trust-grid', 'container_type' => 'grid', 'grid_columns_grid' => [ 'unit' => 'fr', 'size' => 4 ], 'grid_columns_grid_tablet' => [ 'unit' => 'fr', 'size' => 2 ], 'grid_columns_grid_mobile' => [ 'unit' => 'fr', 'size' => 2 ], 'grid_rows_grid' => [ 'unit' => 'fr', 'size' => 1 ], 'grid_auto_flow' => 'row', 'grid_gaps' => $gap( 0 ) ], [
			$trust( 'Free Delivery', 'All over Pakistan', $fa( 'fa-truck' ) ),
			$trust( 'Cash on Delivery', 'Pay when it arrives', $fa( 'fa-money-bill-wave' ) ),
			$trust( '100% Original', 'Sealed brand packet', $fa( 'fa-shield-alt' ) ),
			$trust( '7-Day Exchange', 'Easy and hassle-free', $fa( 'fa-sync-alt' ) ),
		] ),
	], false );

	// ---------- 6. collections ----------
	$col = function ( $label, $id, $bg, $url ) use ( $a, $img, $h, $P, $FH ) {
		return $a( $url, [ 'cls' => 'v5-col', 'background_background' => 'classic', 'background_color' => $bg ], [
			$img( $P[ $id ]['front'], $label ),
			$h( $label, [ 'font' => $FH, 'w' => '600', 'size' => 36, 't' => 30, 'm' => 22, 'lh' => 1.1, 'color' => '#FFFFFF', 'align' => 'center', 'cls' => 'v5-col-label' ] ),
		] );
	};
	$out[] = $section( [ 'cls' => 'v5-sec v5-cols', 'anchor' => 'collections' ], [
		$display( 'Our Collections', 54, [ 't' => 44, 'm' => 34, 'ls' => 0.04, 'up' => true, 'align' => 'center' ] ),
		$con( [ 'cls' => 'v5-col-grid', 'container_type' => 'grid', 'grid_columns_grid' => [ 'unit' => 'fr', 'size' => 4 ], 'grid_columns_grid_tablet' => [ 'unit' => 'fr', 'size' => 4 ], 'grid_columns_grid_mobile' => [ 'unit' => 'fr', 'size' => 2 ], 'grid_rows_grid' => [ 'unit' => 'fr', 'size' => 1 ], 'grid_auto_flow' => 'row', 'grid_gaps' => $gap( 28 ), 'grid_gaps_tablet' => $gap( 18 ), 'grid_gaps_mobile' => $gap( 14 ) ], [
			$col( 'Sale', 21, '#EBDDD2', '#sale' ), $col( 'Khaddar', 3, '#E6DCF3', '#khaddar' ), $col( 'Karandi', 19, '#F1E3DA', '#karandi' ), $col( "Winter '26", 13, '#DDE5D9', '#khaddar' ),
			$col( 'New In', 1, '#E9E2D6', '#new-arrivals' ), $col( '3-Piece', 14, '#E6DCF3', '#new-arrivals' ), $col( 'Best Sellers', 11, '#F1E3DA', '#best-sellers' ), $col( 'Bundle Offer', 7, '#EBDDD2', '#bundle' ),
		] ),
	] );

	// ---------- product card + shop section ----------
	$card = function ( $id ) use ( $con, $row, $h, $w, $img, $btn, $wa, $tag, $P, $C, $gap ) {
		$p = $P[ $id ];
		$dyn = function ( $el, $name, $settings ) use ( $tag ) { $el['settings']['__dynamic__'] = [ 'title' => $tag( $name, $settings ) ]; return $el; };
		$off = $h( $p['off'], [ 'size' => 13, 'w' => '700', 'color' => '#FFFFFF', 'cls' => 'v5-badge v5-badge-off' ] );
		$badges = [ $p['pid'] ? $dyn( $off, 'rv-product-discount', [ 'product_id' => (string) $p['pid'] ] ) : $off ];
		if ( $p['new'] ) $badges[] = $h( 'NEW', [ 'size' => 13, 'w' => '700', 'color' => '#FFFFFF', 'cls' => 'v5-badge v5-badge-new' ] );
		$name = $h( $p['name'], [ 'size' => 15.5, 'm' => 14, 'w' => '700', 'ls' => 0.08, 'up' => true, 'lh' => 1.3, 'align' => 'center', 'cls' => 'v5-name' ] );
		$price = $h( '<del>' . $p['compare'] . '</del> <ins>' . $p['price'] . '</ins>', [ 'size' => 16.5, 'm' => 15, 'w' => '600', 'align' => 'center', 'cls' => 'v5-price v5-num' ] );
		$meta = [
			$p['pid'] ? $dyn( $name, 'rv-product-title', [ 'product_id' => (string) $p['pid'] ] ) : $name,
			$h( '3 PC · Light ' . $p['fabric'], [ 'size' => 14.5, 'm' => 13, 'color' => $C['mute'], 'align' => 'center' ] ),
			$p['pid'] ? $dyn( $price, 'rv-product-price', [ 'product_id' => (string) $p['pid'] ] ) : $price,
		];
		if ( $p['pid'] ) $meta[] = $w( 'rv-sale-timer', [ 'product_id' => (string) $p['pid'], 'label' => 'Sale ends in', 'cls' => 'v5-timer' ] );
		$meta[] = $con( [ 'cls' => 'v5-swatch', 'background_background' => 'classic', 'background_color' => $p['swatch'] ] );
		return $con( [ 'cls' => 'v5-card', 'flex_gap' => $gap( 12 ) ], [
			$con( [ 'cls' => 'v5-media' ], [
				$img( $p['front'], $p['name'] . ', ' . $p['fabric'] . ' suit', 'v5-front' ),
				$img( $p['detail'], $p['name'] . ' print close-up', 'v5-detail' ),
				$row( [ 'cls' => 'v5-badges', 'flex_gap' => $gap( 6 ) ], $badges ),
				$btn( '+ Bundle', '#bundle-tray', [ 'size' => 12.5, 'py' => 8, 'px' => 13, 'ls' => 0.04, 'up' => false, 'cls' => 'v5-pick' ] ),
				$btn( 'Order Now', $wa( "Assalam o Alaikum, mujhe {$p['name']} ({$p['fabric']}, {$p['price']}) chahiye." ), [ 'size' => 13.5, 'py' => 14, 'px' => 16, 'ls' => 0.14, 'cls' => 'v5-add', 'align' => 'justify' ] ),
			] ),
			$con( [ 'cls' => 'v5-meta', 'flex_gap' => $gap( 3 ), 'flex_align_items' => 'center' ], $meta ),
		] );
	};
	$shop = function ( $anchor, $title, $script, $ids ) use ( $section, $con, $w, $eyebrow, $display, $card, $px, $fa, $gap ) {
		$items = []; foreach ( $ids as $k => $id ) $items[] = [ 'slide_title' => 'Item ' . ( $k + 1 ), '_id' => 'v5' . $anchor . $k ];
		return $section( [ 'cls' => 'v5-sec v5-shop', 'anchor' => $anchor ], [
			$con( [ 'flex_align_items' => 'center', 'flex_gap' => $gap( 6 ) ], [ $eyebrow( $script, [ 'align' => 'center' ] ), $display( $title, 54, [ 't' => 44, 'm' => 34, 'ls' => 0.04, 'up' => true, 'align' => 'center' ] ) ] ),
			array_merge( $w( 'nested-carousel', [ 'carousel_items' => $items, 'slides_to_show' => '5', 'slides_to_show_tablet' => '3', 'slides_to_show_mobile' => '2', 'slides_to_scroll' => '1',
				'autoplay' => '', 'infinite' => '', 'speed' => 500, 'arrows' => 'yes', 'pagination' => '', 'image_spacing_custom' => $px( 22 ), 'image_spacing_custom_mobile' => $px( 12 ),
				'navigation_previous_icon' => $fa( 'fa-chevron-left' ), 'navigation_next_icon' => $fa( 'fa-chevron-right' ), 'cls' => 'v5-shop-car' ] ),
				[ 'elements' => array_map( function ( $id ) use ( $con, $card ) { return $con( [ 'cls' => 'v5-cell' ], [ $card( $id ) ] ); }, $ids ) ] ),
		] );
	};

	// ---------- 7. new arrivals ----------
	$out[] = $shop( 'new-arrivals', 'New Arrivals', 'just landed', [ 1, 16, 6, 19, 11, 22, 4, 17, 2, 20 ] );

	// ---------- 8. sale countdown ----------
	$imgStrip = function ( $ids, $cls ) use ( $con, $img, $P ) { return $con( [ 'cls' => $cls, 'flex_direction' => 'row', 'flex_wrap' => 'nowrap' ], array_map( function ( $id ) use ( $img, $P ) { return $img( $P[ $id ]['front'], $P[ $id ]['name'] . ' suit' ); }, $ids ) ); };
	$priceCard = function ( $fabric, $now, $was ) use ( $con, $row, $h, $FH, $gap ) {
		return $con( [ 'cls' => 'v5-pc', 'flex_gap' => $gap( 2 ) ], [
			$h( $fabric, [ 'size' => 12, 'w' => '600', 'ls' => 0.16, 'up' => true, 'color' => '#C9B4FF' ] ),
			$row( [ 'flex_wrap' => 'nowrap', 'flex_align_items' => 'baseline', 'flex_gap' => $gap( 10 ) ], [
				$h( $now, [ 'font' => $FH, 'w' => '600', 'size' => 32, 'm' => 26, 'lh' => 1.05, 'color' => '#FFFFFF', 'cls' => 'v5-num' ] ),
				$h( $was, [ 'size' => 14.5, 'm' => 13, 'color' => 'rgba(255,255,255,0.55)', 'cls' => 'v5-num v5-strike' ] ),
			] ),
		] );
	};
	$saleEnds = date( 'j M', strtotime( $cfg['sale_end'] ) );
	$out[] = $section( [ 'cls' => 'v5-sec v5-sale-sec', 'anchor' => 'sale' ], [
		$con( [ 'cls' => 'v5-sale', 'animation' => 'fadeInUp', 'animation_duration' => 'slow', 'flex_direction' => 'row', 'flex_wrap' => 'nowrap', 'background_background' => 'classic', 'background_color' => $C['plum'] ], [
			$con( [ 'cls' => 'v5-sale-text', 'flex_gap' => $gap( 18 ) ], [
				$row( [ 'cls' => 'v5-sale-top', 'flex_align_items' => 'center', 'flex_gap' => $gap( 12 ) ], [
					$eyebrow( 'Winter Sale', [ 'color' => '#C9B4FF', 'cls' => 'v5-sale-live' ] ),
					$h( 'Ends ' . $saleEnds, [ 'size' => 12.5, 'w' => '600', 'ls' => 0.12, 'up' => true, 'color' => '#FFFFFF', 'cls' => 'v5-sale-ends' ] ),
				] ),
				$display( 'Upto <em>19%</em> Off', 84, [ 't' => 64, 'm' => 48, 'lh' => 0.95, 'color' => '#FFFFFF', 'cls' => 'v5-sale-title' ] ),
				$row( [ 'cls' => 'v5-sale-prices', 'flex_wrap' => 'nowrap', 'flex_gap' => $gap( 12 ) ], [
					$priceCard( 'Light Khaddar', 'Rs 3,990', 'Rs 4,890' ),
					$priceCard( 'Light Karandi', 'Rs 4,290', 'Rs 5,290' ),
				] ),
				$con( [ 'cls' => 'v5-count-wrap', 'flex_gap' => $gap( 8 ) ], [
					$h( 'Sale ends in', [ 'size' => 12, 'w' => '600', 'ls' => 0.16, 'up' => true, 'color' => 'rgba(255,255,255,0.6)' ] ),
					$w( 'countdown', array_merge( [ 'countdown_type' => 'due_date', 'due_date' => $cfg['sale_end'], 'label_display' => 'block', 'show_days' => 'yes', 'show_hours' => 'yes', 'show_minutes' => 'yes', 'show_seconds' => 'yes', 'show_labels' => 'yes',
						'custom_labels' => 'yes', 'label_days' => 'Days', 'label_hours' => 'Hours', 'label_minutes' => 'Mins', 'label_seconds' => 'Secs',
						'box_background_color' => 'rgba(255,255,255,0.07)', 'box_border_border' => 'solid', 'box_border_width' => $box( 1 ), 'box_border_color' => 'rgba(255,255,255,0.16)',
						'box_border_radius' => $box( 12 ), 'box_spacing' => $px( 10 ), 'box_padding' => $box( 12, 0 ), 'digits_color' => '#FFFFFF', 'label_color' => '#C9B4FF', 'cls' => 'v5-count' ],
						$typo( 'digits_typography_', $FH, 44, [ 'w' => '600', 'lh' => 1, 'm' => 32 ] ), $typo( 'label_typography_', $FB, 11, [ 'w' => '600', 'ls' => 0.14, 'up' => true, 'm' => 10 ] ) ) ),
				] ),
				$row( [ 'cls' => 'v5-sale-ctas', 'flex_align_items' => 'center', 'flex_gap' => $gap( 12 ) ], [
					$btn( 'Shop the Sale', '#new-arrivals', [ 'style' => 'light', 'py' => 20, 'px' => 34 ] ),
					$btn( 'Order on WhatsApp', $WA, [ 'py' => 20, 'px' => 28, 'cls' => 'v5-btn-ghost', 'icon' => $fa( 'fa-whatsapp', 'fa-brands' ) ] ),
				] ),
			] ),
			$con( [ 'cls' => 'v5-sale-media' ], [
				$imgStrip( [ 5, 20, 14 ], 'v5-sale-imgs' ),
				$con( [ 'cls' => 'v5-stamp', 'flex_justify_content' => 'center', 'flex_align_items' => 'center' ], [
					$h( 'Upto', [ 'size' => 11.5, 'w' => '600', 'ls' => 0.2, 'up' => true, 'color' => '#FFFFFF', 'align' => 'center' ] ),
					$h( '19%', [ 'font' => $FH, 'w' => '600', 'size' => 40, 'm' => 30, 'lh' => 1, 'color' => '#FFFFFF', 'align' => 'center' ] ),
					$h( 'Off', [ 'size' => 11.5, 'w' => '600', 'ls' => 0.2, 'up' => true, 'color' => '#FFFFFF', 'align' => 'center' ] ),
				] ),
			] ),
		] ),
	] );

	// ---------- 9. khaddar ----------
	$out[] = $shop( 'khaddar', "Winter '26 · Khaddar", 'garam aur halka', range( 1, 15 ) );

	// ---------- 10. karandi promo ----------
	$out[] = $section( [ 'cls' => 'v5-sec v5-promo-sec', 'anchor' => 'karandi' ], [
		$a( $wa( 'Assalam o Alaikum, mujhe Light Karandi collection dekhni hai.' ), [ 'cls' => 'v5-promo', 'animation' => 'fadeInUp', 'animation_duration' => 'slow', 'flex_direction' => 'row', 'flex_wrap' => 'nowrap', 'background_background' => 'classic', 'background_color' => '#EFE5DC' ], [
			$con( [ 'cls' => 'v5-promo-text', 'flex_justify_content' => 'center', 'flex_gap' => $gap( 4 ) ], [
				$h( 'NEW', [ 'font' => $FH, 'w' => '600', 'size' => 30, 'ls' => 0.04, 'color' => $C['wine'], 'lh' => 1.1 ] ),
				$eyebrow( 'Collection', [ 'color' => $C['wine'] ] ),
				$h( 'Light Karandi', [ 'font' => $FH, 'w' => '600', 'size' => 32, 'm' => 26, 'ls' => 0.08, 'up' => true, 'lh' => 1.15, 'cls' => 'v5-mt6' ] ),
				$h( '9 styles · Bonanza Satrangi LIA', [ 'size' => 15.5, 'color' => $C['body'], 'cls' => 'v5-mt8' ] ),
			] ),
			$imgStrip( [ 16, 18, 19, 22, 24 ], 'v5-promo-imgs' ),
		] ),
	] );

	// ---------- 11. best sellers ----------
	$out[] = $shop( 'best-sellers', 'Best Sellers', 'sab ki pasand', [ 13, 21, 7, 23, 10, 18, 5, 24 ] );

	// ---------- 12. bundle ----------
	$out[] = $section( [ 'cls' => 'v5-sec v5-bundle-sec', 'anchor' => 'bundle' ], [
		$con( [ 'cls' => 'v5-bundle', 'animation' => 'fadeInUp', 'animation_duration' => 'slow', 'flex_direction' => 'row', 'flex_wrap' => 'nowrap' ], [
			$con( [ 'cls' => 'v5-bundle-img' ], [ $img( 'p07-front', 'Maya, light khaddar' ) ] ),
			$con( [ 'cls' => 'v5-bundle-img' ], [ $img( 'p17-front', 'Peachy, light karandi' ) ] ),
			$con( [ 'cls' => 'v5-bundle-panel', 'flex_justify_content' => 'center', 'flex_gap' => $gap( 12 ), 'background_background' => 'classic', 'background_color' => $C['lilac'] ], [
				$h( 'BUNDLE OFFER', [ 'size' => 14, 'w' => '700', 'ls' => 0.12, 'color' => '#FFFFFF', 'cls' => 'v5-chip' ] ),
				$display( 'Any 2 Suits<br>for Rs 6,990', 66, [ 't' => 52, 'm' => 42 ] ),
				$p( 'Mix khaddar and karandi. Tap “+ Bundle” on any two suits and order both in one WhatsApp message.', [ 'size' => 17, 'm' => 16 ] ),
				$row( [ 'flex_gap' => $gap( 10 ), 'flex_align_items' => 'baseline' ], [
					$h( 'Rs 10,580', [ 'size' => 16, 'color' => '#9A939F', 'cls' => 'v5-num v5-strike' ] ),
					$h( 'Save up to Rs 3,590', [ 'size' => 16, 'w' => '700', 'color' => $C['purple'], 'cls' => 'v5-num' ] ),
				] ),
				$btn( 'Pick any 2', '#new-arrivals', [ 'py' => 20, 'px' => 34, 'cls' => 'v5-mt4' ] ),
			] ),
		] ),
	] );

	// ---------- 13. the Royal Vision promise: royal seals ----------
	$seal = function ( $n, $ring, $icon, $big ) use ( $con, $w, $h, $px, $FH ) {
		return $con( [ 'cls' => 'v7-seal v7-seal-' . $n, 'flex_justify_content' => 'center', 'flex_align_items' => 'center' ], [
			$w( 'text-path', [ 'text' => $ring, 'path' => 'circle', 'align' => 'center', 'size' => $px( 150 ), 'size_mobile' => $px( 104 ), 'text_color_normal' => '#E2C285', 'cls' => 'v7-ring' ] ),
			$con( [ 'cls' => 'v7-core', 'flex_justify_content' => 'center', 'flex_align_items' => 'center' ], [
				$w( 'icon', [ 'selected_icon' => $icon, 'primary_color' => '#E2C285', 'size' => $px( 18 ), 'size_mobile' => $px( 14 ), 'cls' => 'v7-icon' ] ),
				$h( $big, [ 'font' => $FH, 'w' => '600', 'size' => 21, 'm' => 15, 'lh' => 1, 'color' => '#FFF6E6', 'align' => 'center', 'cls' => 'v7-big' ] ),
			] ),
		] );
	};
	$out[] = $section( [ 'cls' => 'v5-sec v5-promise v7-promise' ], [
		$con( [ 'cls' => 'v7-panel', 'flex_align_items' => 'center', 'flex_gap' => $gap( 30 ), 'flex_gap_mobile' => $gap( 20 ), 'background_background' => 'classic', 'background_color' => '#F6F1EC' ], [
			$con( [ 'flex_align_items' => 'center', 'flex_gap' => $gap( 6 ) ], [
				$eyebrow( 'hamara wada', [ 'align' => 'center' ] ),
				$display( 'The Royal Vision Promise', 48, [ 't' => 40, 'm' => 32, 'align' => 'center' ] ),
			] ),
			$row( [ 'cls' => 'v7-seals', 'flex_justify_content' => 'center', 'flex_gap' => $gap( 34, 22 ), 'flex_gap_mobile' => $gap( 12, 14 ) ], [
				$seal( 1, 'FREE DELIVERY • ALL PAKISTAN • NO MINIMUM • ', $fa( 'fa-truck' ), 'FREE' ),
				$seal( 2, 'CASH ON DELIVERY • NO ADVANCE NEEDED • ', $fa( 'fa-money-bill-wave' ), 'COD' ),
				$seal( 3, '100% ORIGINAL • SEALED BRAND PACKET • ', $fa( 'fa-certificate' ), '100%' ),
				$seal( 4, '7-DAY EXCHANGE • EASY AND HASSLE-FREE • ', $fa( 'fa-sync-alt' ), '7 DAY' ),
				$seal( 5, 'TRACKED PARCEL • UPDATES ON WHATSAPP • ', $fa( 'fa-map-marker-alt' ), 'TRACKED' ),
			] ),
			$row( [ 'cls' => 'v5-promise-foot', 'flex_justify_content' => 'center', 'flex_align_items' => 'center', 'flex_gap' => $gap( 12 ) ], [
				$h( 'We accept', [ 'size' => 13, 'w' => '700', 'ls' => 0.14, 'up' => true, 'color' => $C['mute'] ] ),
				$h( 'Cash on Delivery', [ 'size' => 13.5, 'w' => '700', 'ls' => 0.06, 'up' => true, 'color' => '#FFFFFF', 'cls' => 'v5-pay' ] ),
				$h( 'Questions? <a href="' . $WA . '" target="_blank" rel="noopener">WhatsApp 0322 778 2932</a>', [ 'size' => 14.5, 'color' => $C['body'], 'cls' => 'v5-promise-q v5-num' ] ),
			] ),
		] ),
	] );

	// ---------- bundle tray (hidden until a suit is picked) ----------
	$out[] = $con( [ 'cls' => 'v5-tray', 'anchor' => 'bundle-tray', 'flex_direction' => 'row', 'flex_wrap' => 'nowrap', 'flex_align_items' => 'center', 'flex_gap' => $gap( 14 ), 'background_background' => 'classic', 'background_color' => '#FFFFFF' ], [
		$con( [ 'cls' => 'v5-tray-slots', 'flex_direction' => 'row', 'flex_wrap' => 'nowrap', 'flex_gap' => $gap( 8 ) ] ),
		$con( [ 'cls' => 'v5-tray-info', 'flex_gap' => $gap( 1 ) ], [
			$eyebrow( 'Your bundle', [ 'size' => 12, 'm' => 11, 'cls' => 'v5-tray-eyebrow' ] ),
			$h( 'Pick any 2 suits', [ 'size' => 16, 'm' => 14.5, 'w' => '600', 'lh' => 1.3, 'cls' => 'v5-tray-msg' ] ),
			$h( 'Any 2 for <b>Rs 6,990</b>', [ 'size' => 14.5, 'm' => 13, 'color' => $C['body'], 'lh' => 1.3, 'cls' => 'v5-tray-total v5-num' ] ),
		] ),
		$btn( 'Order bundle', $WA, [ 'size' => 13.5, 'py' => 15, 'px' => 22, 'ls' => 0.12, 'cls' => 'v5-tray-go', 'icon' => $fa( 'fa-whatsapp', 'fa-brands' ) ] ),
		$btn( 'Clear', '#bundle-tray', [ 'style' => 'light', 'size' => 13.5, 'py' => 12, 'px' => 12, 'ls' => 0, 'up' => false, 'cls' => 'v5-tray-clear' ] ),
	], false );

	// ---------- 14. footer: newsletter band, five columns, bottom bar, wordmark ----------
	$links = function ( $list ) use ( $w, $typo, $FB, $link ) {
		return $w( 'icon-list', array_merge( [ 'icon_list' => array_map( function ( $l ) use ( $link ) { return [ 'text' => $l[0], 'selected_icon' => [ 'value' => '', 'library' => '' ], 'link' => $link( $l[1] ), '_id' => 'v8f' . substr( md5( $l[0] ), 0, 6 ) ]; }, $list ),
			'space_between' => [ 'unit' => 'px', 'size' => 12, 'sizes' => [] ], 'text_color' => '#CFC9D4', 'text_color_hover' => '#FFFFFF', 'text_indent' => [ 'unit' => 'px', 'size' => 0, 'sizes' => [] ], 'cls' => 'v5-flinks' ], $typo( 'icon_typography_', $FB, 15, [ 'lh' => 1.4 ] ) ) );
	};
	$ftitle = function ( $t ) use ( $h ) { return $h( $t, [ 'size' => 12.5, 'w' => '600', 'ls' => 0.16, 'up' => true, 'color' => '#FFFFFF', 'cls' => 'v8-ftitle' ] ); };
	$fp = function ( $t, $cls = '' ) use ( $h ) { return $h( $t, [ 'size' => 15, 'color' => '#ADA6B6', 'lh' => 1.65, 'cls' => trim( 'v5-num ' . $cls ) ] ); };
	$out[] = $con( [ 'cls' => 'v5-foot v8-foot', 'content_width' => 'boxed', 'boxed_width' => [ 'unit' => 'px', 'size' => 1520, 'sizes' => [] ], 'background_background' => 'classic', 'background_color' => '#17111C', 'flex_gap' => $gap( 0 ) ], [
		$row( [ 'cls' => 'v8-foot-top', 'flex_wrap' => 'nowrap', 'flex_direction_mobile' => 'column', 'flex_align_items' => 'flex-end', 'flex_justify_content' => 'space-between', 'flex_gap' => $gap( 40 ), 'flex_gap_mobile' => $gap( 24 ) ], [
			$con( [ 'cls' => 'v8-foot-intro', 'flex_gap' => $gap( 10 ) ], [
				$eyebrow( 'Stay in the loop', [ 'color' => '#C9B4FF' ] ),
				$display( 'Winter, delivered<br>to your door.', 52, [ 't' => 42, 'm' => 34, 'lh' => 1.02, 'color' => '#FFFFFF' ] ),
			] ),
			$con( [ 'cls' => 'v8-foot-news', 'flex_gap' => $gap( 12 ) ], [
				$fp( 'New arrivals and sale alerts, first. No spam.' ),
				$w( 'form', array_merge( [ 'form_name' => 'Newsletter', 'form_fields' => [ [ 'custom_id' => 'email', 'field_type' => 'email', 'field_label' => 'Email', 'placeholder' => 'Your email address', 'required' => 'true', 'width' => '100', '_id' => 'v5nl1' ] ],
					'show_labels' => '', 'input_size' => 'md', 'button_text' => 'Subscribe', 'button_size' => 'md', 'button_width' => '100', 'submit_actions' => [ 'email' ],
					'email_to' => $cfg['admin_email'], 'email_subject' => 'New newsletter signup – Royal Vision', 'email_content' => '[all-fields]', 'success_message' => 'Thank you. You are subscribed.',
					'field_text_color' => $C['ink'], 'field_background_color' => '#FFFFFF', 'field_border_radius' => $box( 999 ), 'field_border_width' => $box( 0 ),
					'button_background_color' => $C['purple'], 'button_text_color' => '#FFFFFF', 'button_background_hover_color' => '#5B24D6', 'button_border_radius' => $box( 999 ), 'cls' => 'v5-news' ],
					$typo( 'field_typography_', $FB, 15.5 ), $typo( 'button_typography_', $FB, 14, [ 'w' => '600', 'ls' => 0.06 ] ) ) ),
				$fp( 'Prefer WhatsApp? <a href="' . $wa( 'Assalam o Alaikum, mujhe new arrivals aur sale ki updates WhatsApp par chahiye.' ) . '" target="_blank" rel="noopener">Get updates on WhatsApp</a>', 'v8-foot-wa' ),
			] ),
		] ),
		$con( [ 'cls' => 'v8-foot-grid', 'container_type' => 'grid', 'grid_columns_grid' => [ 'unit' => 'custom', 'size' => '1.6fr 1fr 1fr 1fr 1.3fr' ], 'grid_columns_grid_tablet' => [ 'unit' => 'fr', 'size' => 3 ], 'grid_columns_grid_mobile' => [ 'unit' => 'fr', 'size' => 2 ], 'grid_rows_grid' => [ 'unit' => 'fr', 'size' => 1 ], 'grid_auto_flow' => 'row', 'grid_gaps' => $gap( 40, 40 ), 'grid_gaps_mobile' => $gap( 24, 34 ) ], [
			$con( [ 'cls' => 'v8-fcol v8-fbrand', 'flex_gap' => $gap( 16 ) ], [
				$w( 'image', [ 'image' => $cfg['img']( 'logo' ) + [ 'alt' => 'Royal Vision Store' ], 'image_size' => 'full', 'link_to' => 'custom', 'link' => $link( '/' ), 'cls' => 'v8-flogo' ] ),
				$fp( 'Original branded ladies suits in light khaddar and karandi, delivered free all over Pakistan with Cash on Delivery.' ),
				$w( 'social-icons', [ 'social_icon_list' => [
					[ 'social_icon' => $fa( 'fa-instagram', 'fa-brands' ), 'link' => $link( 'https://www.instagram.com/royal_vision_traders' ), '_id' => 'v5si1' ],
					[ 'social_icon' => $fa( 'fa-facebook-f', 'fa-brands' ), 'link' => $link( 'https://www.facebook.com/royalvisiontraders' ), '_id' => 'v5si2' ],
					[ 'social_icon' => $fa( 'fa-tiktok', 'fa-brands' ), 'link' => $link( 'https://www.tiktok.com/@royalvisiontraders' ), '_id' => 'v5si3' ],
					[ 'social_icon' => $fa( 'fa-whatsapp', 'fa-brands' ), 'link' => $link( $WA ), '_id' => 'v5si4' ] ],
					'shape' => 'circle', 'align' => 'left', 'icon_color' => 'custom', 'icon_primary_color' => 'rgba(255,255,255,0.06)', 'icon_secondary_color' => '#FFFFFF', 'icon_size' => $px( 15 ), 'icon_padding' => [ 'unit' => 'em', 'size' => 0.8, 'sizes' => [] ], 'icon_spacing' => $px( 10 ),
					'hover_primary_color' => $C['purple'], 'hover_secondary_color' => '#FFFFFF', 'cls' => 'v5-social v8-social' ] ),
			] ),
			$con( [ 'cls' => 'v8-fcol', 'flex_gap' => $gap( 16 ) ], [ $ftitle( 'Shop' ), $links( [ [ 'New In', '#new-arrivals' ], [ 'Winter Sale', '#sale' ], [ 'Khaddar', '#khaddar' ], [ 'Karandi', '#karandi' ], [ 'Best Sellers', '#best-sellers' ], [ 'Any 2 for Rs 6,990', '#bundle' ] ] ) ] ),
			$con( [ 'cls' => 'v8-fcol', 'flex_gap' => $gap( 16 ) ], [ $ftitle( 'Help' ), $links( [ [ 'Track your order', $wa( 'Assalam o Alaikum, mujhe apna order track karna hai. Order number: ' ) ], [ 'Shipping & Delivery', '/shipping-and-delivery/' ], [ 'Returns & Exchange', '/returns-and-refunds/' ], [ 'Contact us', '/contact-us/' ] ] ) ] ),
			$con( [ 'cls' => 'v8-fcol', 'flex_gap' => $gap( 16 ) ], [ $ftitle( 'Company' ), $links( [ [ 'About us', '/about-us/' ], [ 'Privacy Policy', '/privacy-policy/' ], [ 'Terms of Service', '/terms-and-conditions/' ] ] ) ] ),
			$con( [ 'cls' => 'v8-fcol v8-fcontact', 'flex_gap' => $gap( 16 ) ], [
				$ftitle( 'Contact' ),
				$w( 'icon-list', array_merge( [ 'icon_list' => [
					[ 'text' => 'WhatsApp 0322 778 2932', 'selected_icon' => $fa( 'fa-whatsapp', 'fa-brands' ), 'link' => $link( $WA ), '_id' => 'v8c1' ],
					[ 'text' => 'Call 0334 529 8703', 'selected_icon' => $fa( 'fa-phone-alt' ), 'link' => $link( 'tel:03345298703' ), '_id' => 'v8c2' ],
					[ 'text' => 'Faisalabad, Pakistan', 'selected_icon' => $fa( 'fa-map-marker-alt' ), 'link' => [ 'url' => '' ], '_id' => 'v8c3' ] ],
					'space_between' => $px( 12 ), 'icon_color' => '#C9B4FF', 'icon_size' => $px( 14 ), 'text_indent' => $px( 10 ), 'text_color' => '#CFC9D4', 'text_color_hover' => '#FFFFFF', 'cls' => 'v5-flinks v8-contact' ], $typo( 'icon_typography_', $FB, 15, [ 'lh' => 1.4 ] ) ) ),
				$row( [ 'cls' => 'v8-qr-row', 'flex_wrap' => 'nowrap', 'flex_align_items' => 'center', 'flex_gap' => $gap( 12 ) ], [
					$w( 'image', [ 'image' => $cfg['img']( 'qr' ) + [ 'alt' => 'QR code for royalvision.online' ], 'image_size' => 'full', 'cls' => 'v8-qr' ] ),
					$fp( 'Scan to shop<br>on your phone', 'v8-qr-text' ),
				] ),
			] ),
		] ),
		$row( [ 'cls' => 'v8-foot-bar', 'flex_justify_content' => 'space-between', 'flex_align_items' => 'center', 'flex_gap' => $gap( 14 ) ], [
			$h( '© 2026 Royal Vision Store. All rights reserved.', [ 'size' => 13.5, 'color' => '#8E879A' ] ),
			$row( [ 'cls' => 'v8-pay', 'flex_wrap' => 'nowrap', 'flex_align_items' => 'center', 'flex_gap' => $gap( 10 ) ], [
				$h( 'We accept', [ 'size' => 12, 'w' => '600', 'ls' => 0.14, 'up' => true, 'color' => '#8E879A' ] ),
				$h( 'Cash on Delivery', [ 'size' => 12, 'w' => '700', 'ls' => 0.08, 'up' => true, 'color' => '#FFFFFF', 'cls' => 'v8-chip' ] ),
			] ),
			$h( 'Back to top ↑', [ 'size' => 13.5, 'w' => '500', 'color' => '#CFC9D4', 'url' => '#', 'cls' => 'v8-top' ] ),
		] ),
		$h( 'Royal Vision', [ 'font' => $FH, 'w' => '600', 'size' => 200, 'lh' => 0.8, 'color' => 'rgba(255,255,255,0.055)', 'align' => 'center', 'cls' => 'v8-wordmark' ] ),
	], false );

	return $out;
};
