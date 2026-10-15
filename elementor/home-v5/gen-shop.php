<?php
// Royal Vision fashion shop page (design "Shop v2"): builds Elementor data (containers + native widgets, no HTML widget).
// Needs gen5.php loaded first. Run server-side as: $data = $rvShop( $cfg );
// $cfg = gen5 cfg + 'home_url' (fashion home page URL). Filtering, sorting and "Load more" come from rvt-shop-filter.php.
$rvShop = function ( $cfg ) use ( $rv5 ) {
	extract( $rv5( array_merge( $cfg, [ 'kit' => 1 ] ) ) );
	$HOME = $cfg['home_url'];

	// Header, trust row, bundle tray and footer: the same elements as the fashion home page.
	$pick = function ( $cls ) use ( $out ) { foreach ( $out as $el ) if ( ( $el['settings']['css_classes'] ?? '' ) === $cls ) return $el; return null; };
	// Home-page anchors become shop filters here; the logo goes to the fashion home page.
	$relink = function ( $el ) use ( &$relink, $HOME ) {
		$map = [ '/' => $HOME, '#new-arrivals' => '#cat-new', '#khaddar' => '#cat-khaddar', '#karandi' => '#cat-karandi', '#best-sellers' => '#cat-best', '#sale' => $HOME . '#sale' ];
		foreach ( $el as $k => $v ) {
			if ( $k === 'url' && is_string( $v ) && isset( $map[ $v ] ) ) $el[ $k ] = $map[ $v ];
			elseif ( is_array( $v ) ) $el[ $k ] = $relink( $v );
		}
		return $el;
	};

	$COLS = [ 'Black', 'Aqua', 'Navy', 'Mauve', 'Navy', 'Pink', 'Mustard', 'Grey', 'Brown', 'Blue', 'Green', 'Ivory', 'Maroon', 'Green', 'Rust', 'Blue', 'Peach', 'Mauve', 'Maroon', 'Mustard', 'Brown', 'Grey', 'Green', 'Green' ];
	$BEST = [ 13, 21, 7, 23, 10, 18, 5, 24 ];
	$CATS = [ [ 'all', 'Shop All', 1, '#EBDDD2' ], [ 'khaddar', 'Khaddar', 3, '#E6DCF3' ], [ 'karandi', 'Karandi', 19, '#F1E3DA' ], [ 'new', 'New In', 5, '#DDE5D9' ], [ 'best', 'Best Sellers', 13, '#E9E2D6' ], [ 'u4', 'Under Rs 4,000', 7, '#E6DCF3' ] ];
	$grid = function ( $cols, $gaps, $cls ) use ( $gap ) {
		return [ 'cls' => $cls, 'container_type' => 'grid', 'grid_columns_grid' => [ 'unit' => 'fr', 'size' => $cols[0] ], 'grid_columns_grid_tablet' => [ 'unit' => 'fr', 'size' => $cols[1] ], 'grid_columns_grid_mobile' => [ 'unit' => 'fr', 'size' => $cols[2] ],
			'grid_rows_grid' => [ 'unit' => 'fr', 'size' => 1 ], 'grid_auto_flow' => 'row', 'grid_gaps' => $gap( $gaps[0], $gaps[1] ), 'grid_gaps_mobile' => $gap( $gaps[2], $gaps[1] ) ];
	};
	// Filter options as a plain icon list; rvt-shop-filter.php adds ticks and live counts.
	$opts = function ( $list, $cls ) use ( $w, $typo, $FB, $link, $C, $px, $fa ) {
		return $w( 'icon-list', array_merge( [ 'icon_list' => array_map( function ( $o ) use ( $link, $fa ) {
			return [ 'text' => $o[0], 'selected_icon' => ! empty( $o[2] ) ? $fa( 'fa-circle' ) : [ 'value' => '', 'library' => '' ], 'link' => $link( $o[1] ), '_id' => 'sh' . substr( md5( $o[1] ), 0, 6 ) ];
		}, $list ), 'space_between' => $px( 2 ), 'text_color' => $C['ink'], 'text_color_hover' => $C['purple'], 'icon_size' => $px( 30 ), 'text_indent' => $px( 0 ), 'cls' => 'sh-opts ' . $cls ],
			$typo( 'icon_typography_', $FB, 16, [ 'lh' => 1.3, 'm' => 15.5 ] ) ) );
	};
	$group = function ( $title, $list, $cls ) use ( $con, $h, $opts ) {
		return $con( [ 'cls' => 'sh-group ' . $cls ], [
			$h( $title, [ 'size' => 14, 'w' => '600', 'ls' => 0.14, 'up' => true, 'lh' => 1.2, 'cls' => 'sh-gtitle' ] ),
			$opts( $list, $cls . '-opts' ),
		] );
	};
	$pill = function ( $t, $cls = '' ) use ( $h ) { return $h( $t, [ 'size' => 14, 'w' => '500', 'lh' => 1, 'cls' => trim( 'sh-pill ' . $cls ) ] ); };
	$ib = function ( $icon, $url, $cls ) use ( $w, $px, $C, $link ) {
		return $w( 'icon', [ 'selected_icon' => $icon, 'link' => $link( $url ), 'primary_color' => $C['ink'], 'size' => $px( 17 ), 'cls' => $cls ] );
	};

	$data = [];
	$data[] = $pick( 'v5-ann' );
	$data[] = $relink( $pick( 'v5-head' ) );

	// ---------- 1. shop header band: title left, three suits right ----------
	$data[] = $con( [ 'cls' => 'sh-hero', 'content_width' => 'boxed', 'boxed_width' => [ 'unit' => 'px', 'size' => 1520, 'sizes' => [] ], 'background_background' => 'classic', 'background_color' => '#F3EDE7' ], [
		$row( [ 'cls' => 'sh-hero-in', 'flex_wrap' => 'nowrap' ], [
			$con( [ 'cls' => 'sh-hero-txt', 'flex_justify_content' => 'center', 'flex_gap' => $gap( 14 ) ], [
				$h( '<a href="' . esc_url( $HOME ) . '">Home</a><span>/</span><a href="#cat-all">Shop</a><span>/</span><span class="sh-crumb-cur">Shop All</span>', [ 'size' => 14, 'color' => $C['mute'], 'cls' => 'sh-crumb' ] ),
				$eyebrow( "Winter '26 Collection", [ 'size' => 13.5 ] ),
				$h( 'Shop All', [ 'tag' => 'h1', 'font' => $FH, 'w' => '600', 'size' => 88, 't' => 64, 'm' => 50, 'lh' => 0.95, 'cls' => 'sh-title' ] ),
				$h( '24 unstitched 3-piece winter suits · Free delivery all over Pakistan', [ 'size' => 17, 'm' => 15.5, 'color' => $C['body'], 'lh' => 1.5, 'cls' => 'sh-sub v5-num' ] ),
				$row( [ 'cls' => 'sh-pills', 'flex_gap' => $gap( 8 ) ], [ $pill( 'Upto 19% off', 'sh-pill-on' ), $pill( 'Any 2 for Rs 6,990', 'v5-num' ), $pill( 'Free delivery' ), $pill( 'Cash on Delivery' ) ] ),
			] ),
			$row( [ 'cls' => 'sh-hero-img', 'flex_wrap' => 'nowrap' ], array_map( function ( $id ) use ( $img, $P ) { return $img( $P[ $id ]['front'], $P[ $id ]['name'] . ' suit', 'sh-himg' ); }, [ 1, 2, 3 ] ) ),
		] ),
	], false );

	// ---------- 2. category circles ----------
	$data[] = $section( [ 'cls' => 'sh-cats-sec', 'flex_gap' => $gap( 0 ) ], [
		$con( $grid( [ 6, 6, 3 ], [ 26, 18, 12 ], 'sh-cats' ), array_map( function ( $c ) use ( $a, $con, $img, $h, $P, $gap ) {
			return $a( '#cat-' . $c[0], [ 'cls' => 'sh-cat sh-cat-' . $c[0], 'flex_align_items' => 'center', 'flex_gap' => $gap( 10 ) ], [
				$con( [ 'cls' => 'sh-cat-ring' ], [ $con( [ 'cls' => 'sh-cat-disc', 'background_background' => 'classic', 'background_color' => $c[3] ], [ $img( $P[ $c[2] ]['front'], $c[1], 'sh-cat-img' ) ] ) ] ),
				$h( $c[1], [ 'size' => 16, 'm' => 14, 'w' => '500', 'lh' => 1.3, 'align' => 'center', 'cls' => 'sh-cat-label' ] ),
				$h( '&nbsp;', [ 'size' => 13.5, 'm' => 12.5, 'color' => '#77707D', 'lh' => 1.3, 'align' => 'center', 'cls' => 'sh-cat-count v5-num' ] ),
			] );
		}, $CATS ) ),
	] );

	// ---------- 3. filters + product grid ----------
	$colourList = []; foreach ( array_unique( $COLS ) as $c ) $colourList[] = [ $c, '#col-' . strtolower( $c ), true ];
	$aside = $con( [ 'cls' => 'sh-aside', 'anchor' => 'filters', 'flex_gap' => $gap( 0 ) ], [
		$row( [ 'cls' => 'sh-aside-head', 'flex_wrap' => 'nowrap', 'flex_justify_content' => 'space-between', 'flex_align_items' => 'center', 'flex_gap' => $gap( 6 ) ], [
			$h( 'Filters', [ 'font' => $FH, 'w' => '600', 'size' => 28, 'lh' => 1, 'cls' => 'sh-aside-title' ] ),
			$h( 'Clear all', [ 'size' => 14.5, 'w' => '500', 'color' => $C['purple'], 'url' => '#clear', 'cls' => 'sh-clear' ] ),
			$ib( $fa( 'fa-times' ), '#close', 'sh-close' ),
		] ),
		$group( 'Category', array_map( function ( $c ) { return [ $c[0] === 'all' ? 'All suits' : $c[1], '#cat-' . $c[0] ]; }, $CATS ), 'sh-g-cat' ),
		$group( 'Fabric', [ [ 'Khaddar', '#fab-khaddar' ], [ 'Karandi', '#fab-karandi' ] ], 'sh-g-fab' ),
		$group( 'Price', [ [ 'Any price', '#price-any' ], [ 'Under Rs 4,000', '#price-u4' ], [ 'Rs 4,000 – 5,000', '#price-b45' ] ], 'sh-g-price' ),
		$group( 'Colour', $colourList, 'sh-g-col' ),
		$con( [ 'cls' => 'sh-help', 'flex_gap' => $gap( 6 ), 'background_background' => 'classic', 'background_color' => $C['lilac'] ], [
			$h( 'Need help choosing?', [ 'font' => $FH, 'w' => '600', 'size' => 24, 'lh' => 1.1 ] ),
			$h( "Send us a screenshot and we'll help with colour and fabric.", [ 'size' => 14.5, 'color' => $C['body'], 'lh' => 1.5 ] ),
			$btn( '0322 778 2932', $wa( 'Assalam o Alaikum, mujhe suit choose karne mein madad chahiye.' ), [ 'size' => 14.5, 'py' => 13, 'px' => 18, 'ls' => 0, 'up' => false, 'cls' => 'sh-help-btn', 'icon' => $fa( 'fa-whatsapp', 'fa-brands' ) ] ),
		] ),
		$btn( 'Show 24 suits', '#close', [ 'size' => 15, 'py' => 19, 'px' => 20, 'ls' => 0.1, 'cls' => 'sh-show', 'align' => 'justify' ] ),
	] );

	// Shop card: same card as home, with the details re-ordered into a store layout
	// (fabric + colour on top, then name, price and a quiet sale timer).
	$shopCard = function ( $id ) use ( $card, $row, $h, $gap, $COLS, $C ) {
		$c = $card( $id );
		foreach ( $c['elements'] as $k => $part ) {
			if ( ( $part['settings']['css_classes'] ?? '' ) !== 'v5-meta' ) continue;
			$by = [];
			foreach ( $part['elements'] as $el ) {
				$cls = $el['settings']['_css_classes'] ?? '';
				$key = ( $el['widgetType'] ?? '' ) === 'rv-sale-timer' ? 'timer' : ( strpos( $cls, 'v5-name' ) !== false ? 'name' : ( strpos( $cls, 'v5-price' ) !== false ? 'price' : ( strpos( $cls, 'v5-swatch' ) !== false ? 'swatch' : 'fabric' ) ) );
				$by[ $key ] = $el;
			}
			$by['fabric']['settings']['_css_classes'] = 'sh-fabric';
			$part['settings']['css_classes'] = $part['settings']['_css_classes'] = 'v5-meta sh-meta';
			$part['elements'] = array_values( array_filter( [
				$row( [ 'cls' => 'sh-meta-top', 'flex_wrap' => 'nowrap', 'flex_justify_content' => 'space-between', 'flex_align_items' => 'center', 'flex_gap' => $gap( 8 ) ], [
					$by['fabric'],
					$row( [ 'cls' => 'sh-colour', 'flex_wrap' => 'nowrap', 'flex_align_items' => 'center', 'flex_gap' => $gap( 6 ) ], [ $by['swatch'], $h( $COLS[ $id - 1 ], [ 'size' => 13, 'color' => $C['mute'], 'lh' => 1.2, 'cls' => 'sh-colour-name' ] ) ] ),
				] ),
				$by['name'], $by['price'], $by['timer'] ?? null,
			] ) );
			$c['elements'][ $k ] = $part;
		}
		return $c;
	};

	$cells = [];
	foreach ( $P as $id => $pp ) {
		$cls = 'sh-cell sh-fab-' . strtolower( $pp['fabric'] ) . ' sh-col-' . strtolower( $COLS[ $id - 1 ] ) . ( $pp['new'] ? ' sh-new' : '' ) . ( in_array( $id, $BEST, true ) ? ' sh-best' : '' );
		$cells[] = $con( [ 'cls' => $cls ], [ $shopCard( $id ) ] );
		if ( $id === 6 ) {
			$cells[] = $a( $HOME . '#bundle', [ 'cls' => 'sh-promo', 'flex_direction' => 'row', 'flex_wrap' => 'nowrap', 'background_background' => 'classic', 'background_color' => $C['plum'] ], [
				$con( [ 'cls' => 'sh-promo-txt', 'flex_justify_content' => 'center', 'flex_gap' => $gap( 14 ) ], [
					$eyebrow( 'Bundle offer', [ 'size' => 13, 'color' => '#C9B4FF' ] ),
					$h( 'Any 2 suits for Rs 6,990', [ 'font' => $FH, 'w' => '600', 'size' => 50, 't' => 40, 'm' => 34, 'lh' => 1, 'color' => '#FFFFFF', 'cls' => 'v5-num' ] ),
					$h( 'Mix khaddar and karandi. Tap + Bundle on any two suits.', [ 'size' => 15.5, 'color' => '#E6DDF0', 'lh' => 1.5 ] ),
					$h( 'Pick any 2 →', [ 'size' => 14, 'w' => '600', 'ls' => 0.08, 'up' => true, 'cls' => 'sh-promo-cta' ] ),
				] ),
				$row( [ 'cls' => 'sh-promo-img', 'flex_wrap' => 'nowrap' ], [ $img( $P[7]['front'], 'Maya suit', 'sh-pimg' ), $img( $P[17]['front'], 'Peachy suit', 'sh-pimg' ) ] ),
			] );
		}
	}

	$main = $con( [ 'cls' => 'sh-results', 'flex_gap' => $gap( 18 ) ], [
		$row( [ 'cls' => 'sh-bar', 'flex_justify_content' => 'space-between', 'flex_align_items' => 'center', 'flex_gap' => $gap( 12 ) ], [
			$row( [ 'cls' => 'sh-bar-l', 'flex_align_items' => 'center', 'flex_gap' => $gap( 12 ) ], [
				$btn( 'Filters', '#open-filters', [ 'style' => 'light', 'size' => 15, 'py' => 14, 'px' => 20, 'ls' => 0, 'up' => false, 'cls' => 'sh-fbtn', 'icon' => $fa( 'fa-sliders-h' ) ] ),
				$h( '<b>24</b> suits found', [ 'size' => 16, 'color' => $C['body'], 'cls' => 'sh-count v5-num' ] ),
			] ),
			$row( [ 'cls' => 'sh-bar-r', 'flex_wrap' => 'nowrap', 'flex_align_items' => 'center', 'flex_gap' => $gap( 10 ) ], [
				$w( 'form', array_merge( [ 'form_name' => 'Sort suits', 'form_fields' => [ [ 'custom_id' => 'sort', 'field_type' => 'select', 'field_label' => 'Sort by', 'field_options' => "Featured|featured\nNewest first|new\nPrice: low to high|low\nPrice: high to low|high\nName: A to Z|az", 'width' => '100', '_id' => 'shsort' ] ],
					'show_labels' => 'yes', 'input_size' => 'sm', 'button_text' => 'Sort', 'submit_actions' => [], 'label_color' => $C['mute'], 'field_text_color' => $C['ink'], 'field_background_color' => '#FFFFFF', 'field_border_color' => '#DDD5CE', 'field_border_radius' => $box( 999 ), 'cls' => 'sh-sort' ],
					$typo( 'label_typography_', $FB, 15 ), $typo( 'field_typography_', $FB, 15.5 ) ) ),
				$row( [ 'cls' => 'sh-cols', 'flex_wrap' => 'nowrap' ], [ $ib( $fa( 'fa-grip-vertical' ), '#cols-3', 'sh-col3' ), $ib( $fa( 'fa-th' ), '#cols-4', 'sh-col4' ) ] ),
			] ),
		] ),
		$row( [ 'cls' => 'sh-chips', 'flex_align_items' => 'center', 'flex_gap' => $gap( 8 ) ] ),
		$con( array_merge( $grid( [ 4, 3, 2 ], [ 22, 40, 12 ], 'sh-grid' ), [ 'anchor' => 'shop-grid' ] ), $cells ),
		$con( [ 'cls' => 'sh-empty', 'flex_align_items' => 'center', 'flex_gap' => $gap( 12 ), 'background_background' => 'classic', 'background_color' => '#F6EFE9' ], [
			$h( 'No suits match these filters', [ 'font' => $FH, 'w' => '600', 'size' => 34, 'm' => 28, 'lh' => 1.1, 'align' => 'center' ] ),
			$h( 'Try removing a colour or price filter.', [ 'size' => 16, 'color' => $C['body'], 'align' => 'center' ] ),
			$btn( 'Clear all filters', '#clear', [ 'size' => 15, 'py' => 17, 'px' => 28, 'ls' => 0.04, 'up' => false, 'align' => 'center' ] ),
		] ),
		$con( [ 'cls' => 'sh-more-wrap', 'flex_align_items' => 'center', 'flex_gap' => $gap( 14 ) ], [
			$h( "You've viewed 12 of 24 suits", [ 'size' => 15, 'color' => $C['body'], 'align' => 'center', 'cls' => 'sh-prog-txt v5-num' ] ),
			$con( [ 'cls' => 'sh-prog', 'background_background' => 'classic', 'background_color' => $C['line'] ], [ $con( [ 'cls' => 'sh-prog-fill', 'background_background' => 'classic', 'background_color' => $C['ink'] ] ) ] ),
			$btn( 'Load more', '#more', [ 'style' => 'light', 'size' => 15, 'py' => 19, 'px' => 40, 'ls' => 0.1, 'align' => 'center', 'cls' => 'sh-more' ] ),
		] ),
	] );

	$data[] = $section( [ 'cls' => 'sh-main', 'anchor' => 'shop', 'flex_direction' => 'row', 'flex_wrap' => 'nowrap', 'flex_align_items' => 'flex-start', 'flex_gap' => $gap( 44 ) ], [
		$a( '#close', [ 'cls' => 'sh-backdrop' ], [] ),
		$aside, $main,
	] );

	// ---------- 4. trust row (same as home) ----------
	$data[] = $pick( 'v5-trust-row' );

	// ---------- 5. about these suits ----------
	$data[] = $section( [ 'cls' => 'sh-seo', 'flex_direction' => 'row', 'flex_wrap' => 'nowrap', 'flex_gap' => $gap( 64 ) ], [
		$h( 'Winter suits for women, delivered free across Pakistan', [ 'tag' => 'h2', 'font' => $FH, 'w' => '600', 'size' => 44, 't' => 38, 'm' => 32, 'lh' => 1.1, 'cls' => 'sh-seo-h' ] ),
		$p( "Shop original 3-piece unstitched winter suits in light khaddar and soft karandi. Each suit includes a printed shirt, dupatta and trouser in the brand's sealed packet. Pay with Cash on Delivery and enjoy free delivery to every city.", [ 'size' => 16.5, 'lh' => 1.75, 'cls' => 'sh-seo-p' ] ),
	] );

	$data[] = $pick( 'v5-tray' );
	$data[] = $relink( $pick( 'v5-foot v9-foot' ) );
	return array_values( array_filter( $data ) );
};
