<?php
/**
 * Builds the Royal Vision Law homepage as a native Elementor page.
 * Run through Novamira execute-php (WordPress environment loaded).
 * Idempotent: re-running updates the same "Home" page, menu and kit globals.
 */

$rv_id = function () { return substr( md5( uniqid( '', true ) . mt_rand() ), 0, 7 ); };

$con = function ( $settings, $elements = array() ) use ( $rv_id ) {
	return array( 'id' => $rv_id(), 'elType' => 'container', 'isInner' => false, 'settings' => $settings, 'elements' => $elements );
};
$wid = function ( $type, $settings ) use ( $rv_id ) {
	return array( 'id' => $rv_id(), 'elType' => 'widget', 'widgetType' => $type, 'isInner' => false, 'settings' => $settings, 'elements' => array() );
};
$px = function ( $n ) { return array( 'unit' => 'px', 'size' => $n, 'sizes' => array() ); };
$pad = function ( $t, $r, $b, $l ) { return array( 'unit' => 'px', 'top' => (string) $t, 'right' => (string) $r, 'bottom' => (string) $b, 'left' => (string) $l, 'isLinked' => false ); };
$gap = function ( $n ) { return array( 'unit' => 'px', 'size' => $n, 'column' => (string) $n, 'row' => (string) $n, 'isLinked' => true ); };
$link = function ( $url ) { return array( 'url' => $url, 'is_external' => '', 'nofollow' => '', 'custom_attributes' => '' ); };

// Text widgets
$h = function ( $text, $tag, $class, $url = '' ) use ( $wid, $link ) {
	$s = array( 'title' => $text, 'header_size' => $tag, '_css_classes' => $class );
	if ( $url ) { $s['link'] = $link( $url ); }
	return $wid( 'heading', $s );
};
$txt = function ( $html, $class = 'rvl-body' ) use ( $wid ) {
	return $wid( 'text-editor', array( 'editor' => $html, '_css_classes' => $class ) );
};
$eyebrow = function ( $text ) use ( $h ) { return $h( $text, 'span', 'rvl-eyebrow' ); };
$more = function ( $text, $url ) use ( $h ) { return $h( $text . ' <span aria-hidden="true">→</span>', 'div', 'rvl-more', $url ); };
$btn = function ( $text, $url, $class = 'rvl-btn' ) use ( $wid, $link ) {
	return $wid( 'button', array( 'text' => $text, 'link' => $link( $url ), '_css_classes' => $class ) );
};

// Section shell: full-width band with a 1200px boxed inner
$section = function ( $id, $class, $children, $extra = array() ) use ( $con, $pad, $px ) {
	$s = array_merge( array(
		'content_width'  => 'boxed',
		'boxed_width'    => $px( 1200 ),
		'flex_direction' => 'column',
		'flex_gap'       => array( 'unit' => 'px', 'size' => 44, 'column' => '44', 'row' => '44', 'isLinked' => true ),
		'flex_gap_mobile'=> array( 'unit' => 'px', 'size' => 28, 'column' => '28', 'row' => '28', 'isLinked' => true ),
		'padding'        => $pad( 92, 40, 92, 40 ),
		'padding_tablet' => $pad( 72, 32, 72, 32 ),
		'padding_mobile' => $pad( 48, 20, 48, 20 ),
		'css_classes'    => 'rvl-section ' . $class,
	), $extra );
	if ( $id ) { $s['_element_id'] = $id; }
	return $con( $s, $children );
};
// Plain inner flex container
$box = function ( $class, $children, $dir = 'column', $extra = array() ) use ( $con, $px ) {
	return $con( array_merge( array(
		'content_width'  => 'full',
		'flex_direction' => $dir,
		'padding'        => array( 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => true ),
		'css_classes'    => $class,
	), $extra ), $children );
};
// Responsive grid container
$grid = function ( $class, $cols, $cols_t, $cols_m, $g, $children ) use ( $con, $px ) {
	$fr = function ( $n ) { return array( 'unit' => 'fr', 'size' => $n, 'sizes' => array() ); };
	return $con( array(
		'container_type'          => 'grid',
		'content_width'           => 'full',
		'grid_columns_grid'       => $fr( $cols ),
		'grid_columns_grid_tablet'=> $fr( $cols_t ),
		'grid_columns_grid_mobile'=> $fr( $cols_m ),
		'grid_rows_grid'          => array( 'unit' => 'custom', 'size' => 'auto', 'sizes' => array() ),
		'grid_gaps'               => array( 'unit' => 'px', 'column' => (string) $g, 'row' => (string) $g, 'isLinked' => true ),
		'padding'                 => array( 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => true ),
		'css_classes'             => 'rvl-grid ' . $class,
	), $children );
};
// Section heading row (eyebrow + h2, optional link on the right)
$head = function ( $eb, $title, $more_text = '', $more_url = '' ) use ( $box, $eyebrow, $h, $more ) {
	$left = $box( 'rvl-head', array( $eyebrow( $eb ), $h( $title, 'h2', 'rvl-h2' ) ), 'column', array( 'width' => array( 'unit' => 'custom', 'size' => 'auto' ) ) );
	if ( ! $more_text ) { return $left; }
	return $box( 'rvl-headrow', array( $left, $more( $more_text, $more_url ) ), 'row', array(
		'flex_wrap' => 'wrap', 'flex_justify_content' => 'space-between', 'flex_align_items' => 'flex-end',
	) );
};

/* ---------- Primary menu (anchor links on the homepage) ---------- */
$menu_name = 'Royal Vision Primary';
$menu      = wp_get_nav_menu_object( $menu_name );
$menu_id   = $menu ? $menu->term_id : wp_create_nav_menu( $menu_name );
foreach ( (array) wp_get_nav_menu_items( $menu_id ) as $item ) { wp_delete_post( $item->ID, true ); }
$nav = array( 'Home' => '#top', 'The Firm' => '#firm', 'Practice Areas' => '#practice', 'Our Team' => '#team', 'Insights' => '#insights', 'Contact' => '#contact' );
$pos = 1;
foreach ( $nav as $label => $url ) {
	wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => $label, 'menu-item-url' => $url, 'menu-item-status' => 'publish', 'menu-item-type' => 'custom', 'menu-item-position' => $pos++ ) );
}
$menu_slug = get_term( $menu_id )->slug;

/* ---------- Kit globals (colours + fonts) ---------- */
$kit_id   = (int) get_option( 'elementor_active_kit' );
$kit_doc  = \Elementor\Plugin::$instance->documents->get( $kit_id );
$kit_set  = $kit_doc ? (array) $kit_doc->get_settings() : array();
$kit_set  = array_filter( $kit_set, function ( $k ) { return ! is_int( $k ); }, ARRAY_FILTER_USE_KEY );
$kit_set['system_colors'] = array(
	array( '_id' => 'primary', 'title' => 'Ink', 'color' => '#14302A' ),
	array( '_id' => 'secondary', 'title' => 'Forest', 'color' => '#1D5A42' ),
	array( '_id' => 'text', 'title' => 'Text', 'color' => '#343B42' ),
	array( '_id' => 'accent', 'title' => 'Accent', 'color' => '#1D5A42' ),
);
$kit_set['custom_colors'] = array(
	array( '_id' => 'rv_label', 'title' => 'Label green', 'color' => '#1D6A4B' ),
	array( '_id' => 'rv_muted', 'title' => 'Muted', 'color' => '#5E646A' ),
	array( '_id' => 'rv_line', 'title' => 'Hairline', 'color' => '#DDDCD8' ),
	array( '_id' => 'rv_stone', 'title' => 'Stone', 'color' => '#F7F5F1' ),
	array( '_id' => 'rv_tile', 'title' => 'Tile', 'color' => '#ECE9E3' ),
	array( '_id' => 'rv_gold', 'title' => 'Gold', 'color' => '#7A5A1E' ),
	array( '_id' => 'rv_gold_bg', 'title' => 'Gold tint', 'color' => '#F6EEDC' ),
	array( '_id' => 'rv_footer', 'title' => 'Footer', 'color' => '#12302A' ),
);
$font = function ( $id, $title, $family, $weight ) {
	return array( '_id' => $id, 'title' => $title, 'typography_typography' => 'custom', 'typography_font_family' => $family, 'typography_font_weight' => $weight );
};
$kit_set['system_typography'] = array(
	$font( 'primary', 'Headings', 'Libre Baskerville', '400' ),
	$font( 'secondary', 'Subheadings', 'Libre Baskerville', '400' ),
	$font( 'text', 'Body', 'Source Sans 3', '400' ),
	$font( 'accent', 'Buttons', 'Source Sans 3', '500' ),
);
$kit_set['body_typography_typography']  = 'custom';
$kit_set['body_typography_font_family'] = 'Source Sans 3';
$kit_set['body_color']                  = '#343B42';
$kit_set['container_width']             = $px( 1200 );
$kit_doc->save( array( 'settings' => $kit_set ) );

/* ---------- Page content ---------- */
$slot = function ( $class ) use ( $box ) { return $box( 'rvl-slot ' . $class, array() ); };

// Header
$header = $con( array(
	'content_width' => 'boxed', 'boxed_width' => $px( 1200 ), 'flex_direction' => 'row',
	'flex_justify_content' => 'space-between', 'flex_align_items' => 'center', 'flex_wrap' => 'nowrap',
	'flex_gap' => $gap( 24 ), 'min_height' => $px( 76 ),
	'padding' => $pad( 0, 40, 0, 40 ), 'padding_tablet' => $pad( 0, 32, 0, 32 ), 'padding_mobile' => $pad( 0, 20, 0, 20 ),
	'css_classes' => 'rvl-header', '_element_id' => 'top',
), array(
	$box( 'rvl-brand', array(
		$h( 'Royal Vision Law Associate', 'div', 'rvl-brand-name', '#top' ),
		$h( 'Advocates &amp; Legal Consultants', 'div', 'rvl-brand-sub' ),
	), 'column', array( 'width' => array( 'unit' => 'custom', 'size' => 'auto' ), 'flex_gap' => $gap( 2 ) ) ),
	$box( 'rvl-navwrap', array(
		$wid( 'nav-menu', array(
			'menu' => $menu_slug, 'layout' => 'horizontal', 'align_items' => 'right', 'pointer' => 'underline',
			'animation_line' => 'fade', 'dropdown' => 'tablet', 'toggle' => 'burger', 'full_width' => 'stretch',
			'text_align' => 'aside', 'toggle_align' => 'right', '_css_classes' => 'rvl-nav',
			'menu_typography_typography' => 'custom', 'menu_typography_font_family' => 'Source Sans 3',
			'menu_typography_font_size' => $px( 15 ), 'menu_typography_font_weight' => '500',
			'color_menu_item' => '#343B42', 'color_menu_item_hover' => '#1D5A42', 'pointer_color_menu_item_hover' => '#1D6A4B',
			'color_menu_item_active' => '#14302A', 'pointer_color_menu_item_active' => '#1D6A4B',
			'padding_horizontal_menu_item' => $px( 13 ), 'pointer_width' => $px( 1 ),
			'color_dropdown_item' => '#14302A', 'background_color_dropdown_item' => '#FFFFFF',
			'color_dropdown_item_hover' => '#1D5A42', 'background_color_dropdown_item_hover' => '#F7F5F1',
			'dropdown_typography_typography' => 'custom', 'dropdown_typography_font_family' => 'Source Sans 3',
			'dropdown_typography_font_size' => $px( 17 ), 'dropdown_typography_font_weight' => '500',
			'toggle_color' => '#14302A', 'toggle_background_color' => '#FFFFFF',
		) ),
		$btn( 'Contact Us', '#contact', 'rvl-btn rvl-btn-sm rvl-hide-mobile' ),
	), 'row', array( 'width' => array( 'unit' => 'custom', 'size' => 'auto' ), 'flex_align_items' => 'center', 'flex_gap' => $gap( 20 ), 'flex_wrap' => 'nowrap' ) ),
) );

// Hero
$hero = $section( '', 'rvl-hero rvl-alt rvl-bb', array(
	$box( 'rvl-hero-row', array(
		$box( 'rvl-hero-copy', array(
			$eyebrow( 'Advocates &amp; Legal Consultants · Faisalabad' ),
			$wid( 'heading', array( 'title' => 'Clear advice. Considered action.', 'header_size' => 'h1', '_css_classes' => 'rvl-h1',
				'typography_typography' => 'custom', 'typography_font_family' => 'Libre Baskerville', 'typography_font_weight' => '400' ) ),
			$wid( 'text-editor', array( 'editor' => '<p>Royal Vision Law Associate is a firm of advocates registered with the Punjab Bar Council, based at the District Courts, Faisalabad. We help individuals and businesses understand their legal position and decide, with care, how to move forward.</p>',
				'_css_classes' => 'rvl-body rvl-lead', 'typography_typography' => 'custom', 'typography_font_family' => 'Source Sans 3', 'typography_font_weight' => '400' ) ),
			$box( 'rvl-actions', array(
				$btn( 'Discuss Your Matter', '#contact' ),
				$btn( 'Explore Our Practice Areas', '#practice', 'rvl-btn rvl-btn-ghost' ),
			), 'row', array( 'flex_wrap' => 'wrap', 'flex_gap' => $gap( 12 ) ) ),
		), 'column', array( 'width' => array( 'unit' => '%', 'size' => 55 ), 'width_mobile' => array( 'unit' => '%', 'size' => 100 ), 'flex_justify_content' => 'center', 'flex_gap' => $gap( 24 ) ) ),
		$box( 'rvl-hero-media', array( $slot( 'rvl-slot-hero' ) ), 'column', array(
			'width' => array( 'unit' => '%', 'size' => 45 ), 'width_mobile' => array( 'unit' => '%', 'size' => 100 ),
		) ),
	), 'row', array( 'flex_direction_mobile' => 'column', 'flex_gap' => $gap( 56 ), 'flex_gap_mobile' => $gap( 32 ), 'flex_align_items' => 'stretch', 'flex_wrap' => 'nowrap' ) ),
), array( 'padding' => $pad( 84, 40, 84, 40 ), 'padding_mobile' => $pad( 40, 20, 48, 20 ) ) );

// The Firm
$fact = function ( $k, $v ) use ( $box, $h ) {
	return $box( 'rvl-fact', array( $h( $k, 'span', 'rvl-fact-k' ), $h( $v, 'div', 'rvl-fact-v' ) ), 'column', array( 'flex_gap' => array( 'unit' => 'px', 'size' => 6, 'column' => '6', 'row' => '6', 'isLinked' => true ) ) );
};
$firm = $section( 'firm', 'rvl-white', array(
	$box( 'rvl-split', array(
		$box( 'rvl-split-a', array( $eyebrow( 'The Firm' ), $h( 'A registered firm of advocates at the District Courts, Faisalabad', 'h2', 'rvl-h2' ) ), 'column',
			array( 'width' => array( 'unit' => '%', 'size' => 42 ), 'width_mobile' => array( 'unit' => '%', 'size' => 100 ), 'flex_gap' => $gap( 16 ) ) ),
		$box( 'rvl-split-b', array(
			$txt( '<p>Royal Vision Law Associate was founded by advocates Ch. Abdul Nabi Qamar and Imran Ali and registered as a law firm by the Punjab Bar Council in 2023. We work from chambers at the District Courts, where we advise and represent clients in matters before the local courts.</p>' ),
			$more( 'About the Firm', '#registration' ),
		), 'column', array( 'width' => array( 'unit' => '%', 'size' => 58 ), 'width_mobile' => array( 'unit' => '%', 'size' => 100 ), 'flex_gap' => $gap( 18 ) ) ),
	), 'row', array( 'flex_direction_mobile' => 'column', 'flex_gap' => $gap( 48 ), 'flex_gap_mobile' => $gap( 24 ), 'flex_wrap' => 'nowrap' ) ),
	$grid( 'rvl-facts', 4, 2, 1, 0, array(
		$fact( 'Registration', 'Punjab Bar Council, Reg. No. 13294/23' ),
		$fact( 'Registered', 'August 2023' ),
		$fact( 'Chambers', '45 District Courts, Faisalabad' ),
		$fact( 'Regulated under', 'Punjab Legal Practitioners &amp; Bar Council Rules, 1974' ),
	) ),
) );

// Registration
$row = function ( $k, $v ) { return '<div class="rvl-dl-row"><span>' . $k . '</span><strong>' . $v . '</strong></div>'; };
$registration = $section( 'registration', 'rvl-white rvl-bt', array(
	$box( 'rvl-split rvl-reg', array(
		$box( 'rvl-reg-figure', array(
			$box( 'rvl-doc', array( $wid( 'icon', array( 'selected_icon' => array( 'value' => 'far fa-file-alt', 'library' => 'fa-regular' ), '_css_classes' => 'rvl-doc-icon' ) ) ), 'column',
				array( 'flex_justify_content' => 'center', 'flex_align_items' => 'center' ) ),
			$txt( '<p>Registration letter, Punjab Bar Council, Lahore. 31 August 2023</p>', 'rvl-caption' ),
		), 'column', array( 'width' => array( 'unit' => '%', 'size' => 40 ), 'width_mobile' => array( 'unit' => '%', 'size' => 100 ), 'flex_gap' => $gap( 12 ) ) ),
		$box( 'rvl-reg-copy', array(
			$eyebrow( 'Registration' ),
			$h( 'Registered with the Punjab Bar Council', 'h2', 'rvl-h2' ),
			$txt( '<p>The Punjab Bar Council, at its 275th meeting held on 5 August 2023, granted registration to Royal Vision Law Associate, situated at 45 District Courts, Faisalabad, under Chapter VII of the Punjab Legal Practitioners &amp; Bar Council Rules, 1974.</p>' ),
			$txt( '<div class="rvl-dl">' . $row( 'Reference', '13294' ) . $row( 'Date of letter', '31 August 2023' ) . $row( 'Advocates', 'Ch. Abdul Nabi Qamar · Imran Ali' ) . '</div>', 'rvl-dl-wrap' ),
		), 'column', array( 'width' => array( 'unit' => '%', 'size' => 60 ), 'width_mobile' => array( 'unit' => '%', 'size' => 100 ), 'flex_gap' => $gap( 18 ) ) ),
	), 'row', array( 'flex_direction_mobile' => 'column', 'flex_gap' => $gap( 64 ), 'flex_gap_mobile' => $gap( 32 ), 'flex_align_items' => 'center', 'flex_wrap' => 'nowrap' ) ),
) );

// Practice areas
$practices = array(
	array( 'Civil litigation', 'Representation in civil suits, recovery claims and appeals before the civil courts.' ),
	array( 'Criminal defence', 'Advice and representation from bail applications through to trial and appeal.' ),
	array( 'Family law', 'Guidance on divorce, custody, maintenance and guardianship matters.' ),
	array( 'Property &amp; land', 'Title verification, transfers, tenancy and property dispute resolution.' ),
	array( 'Corporate &amp; commercial', 'Contracts, company matters and advice for businesses of every size.' ),
	array( 'Banking &amp; recovery', 'Recovery suits, cheque dishonour matters and banking disputes.' ),
);
$cards = array();
foreach ( $practices as $p ) {
	$cards[] = $con( array(
		'content_width' => 'full', 'flex_direction' => 'column', 'flex_gap' => $gap( 12 ),
		'html_tag' => 'a', 'link' => $link( '#contact' ),
		'padding' => $pad( 28, 28, 24, 28 ), 'css_classes' => 'rvl-card',
	), array(
		$h( 'Placeholder', 'span', 'rvl-tag' ),
		$h( $p[0], 'h3', 'rvl-h3' ),
		$txt( '<p>' . $p[1] . '</p>', 'rvl-body rvl-small rvl-grow' ),
		$h( 'Learn more →', 'span', 'rvl-card-cta' ),
	) );
}
$practice = $section( 'practice', 'rvl-alt rvl-bt rvl-bb', array(
	$head( 'Practice Areas', 'How we can help', 'View All Practice Areas', '#contact' ),
	$grid( 'rvl-practice-grid', 3, 2, 1, 20, $cards ),
) );

// Approach
$step = function ( $t, $d ) use ( $box, $h, $txt, $gap ) {
	return $box( 'rvl-step', array( $h( $t, 'h3', 'rvl-h3 rvl-h3-sm' ), $txt( '<p>' . $d . '</p>', 'rvl-body rvl-small' ) ), 'column', array( 'flex_gap' => $gap( 10 ) ) );
};
$approach = $section( 'approach', 'rvl-white', array(
	$head( 'Our Approach', 'How we work with you' ),
	$grid( 'rvl-steps', 3, 3, 1, 40, array(
		$step( 'Understanding the matter', 'We start by listening, reviewing your documents and establishing the facts, so that our advice rests on a clear picture of your situation.' ),
		$step( 'Explaining your options', 'We set out the routes available to you in plain language, including the likely steps, costs and risks of each.' ),
		$step( 'Agreeing the next steps', 'Once you have decided how to proceed, we agree a course of action and keep you informed as the matter moves forward.' ),
	) ),
) );

// People
$people = array(
	array( 'Ch. Abdul Nabi Qamar', 'Advocate', 'Practice focus to be supplied' ),
	array( 'Imran Ali', 'Advocate', 'Practice focus to be supplied' ),
	array( '[Team member]', '[Role]', 'Placeholder, details to be supplied' ),
	array( '[Team member]', '[Role]', 'Placeholder, details to be supplied' ),
);
$people_els = array();
foreach ( $people as $m ) {
	$people_els[] = $box( 'rvl-person', array(
		$slot( 'rvl-slot-portrait' ),
		$box( 'rvl-person-info', array( $h( $m[0], 'h3', 'rvl-h3 rvl-h3-person' ), $h( $m[1], 'span', 'rvl-role' ), $h( $m[2], 'span', 'rvl-focus' ) ), 'column', array( 'flex_gap' => $gap( 4 ) ) ),
		$h( 'View Profile →', 'div', 'rvl-inline-link', '#contact' ),
	), 'column', array( 'flex_gap' => $gap( 14 ) ) );
}
$team = $section( 'team', 'rvl-alt rvl-bt rvl-bb', array(
	$head( 'Our People', 'The advocates behind the firm', 'Meet Our Team', '#team' ),
	$grid( 'rvl-people', 4, 2, 1, 32, $people_els ),
) );

// Insights
$articles = array(
	array( 'Property', 'What to check before buying land in Punjab', 'A short guide to the documents and records worth reviewing before a property transaction.' ),
	array( 'Family', 'Understanding the guardianship process', 'An overview of how guardianship applications are made and what the court considers.' ),
	array( 'Civil', 'Preparing for your first hearing', 'Practical points on documents, timing and what to expect when you attend court.' ),
);
$art_els = array();
foreach ( $articles as $a ) {
	$art_els[] = $box( 'rvl-article', array(
		$slot( 'rvl-slot-thumb' ),
		$txt( '<p class="rvl-meta"><span class="rvl-cat">' . $a[0] . '</span><span class="rvl-date">[Date]</span><span class="rvl-tag-inline">Sample</span></p>', 'rvl-meta-wrap' ),
		$h( $a[1], 'h3', 'rvl-h3 rvl-h3-sm' ),
		$txt( '<p>' . $a[2] . '</p>', 'rvl-body rvl-small' ),
		$h( 'Read article →', 'div', 'rvl-inline-link', '#insights' ),
	), 'column', array( 'flex_gap' => $gap( 14 ) ) );
}
$insights = $section( 'insights', 'rvl-white', array(
	$head( 'Insights', 'Updates and guidance', 'All Insights', '#insights' ),
	$grid( 'rvl-articles', 3, 2, 1, 40, $art_els ),
) );

// Contact
$detail = function ( $k, $v, $tbc = false ) {
	return '<div class="rvl-detail"><span class="k">' . $k . '</span><span class="' . ( $tbc ? 'tbc' : 'v' ) . '">' . $v . '</span></div>';
};
$field = function ( $cid, $type, $label, $width, $req = false, $extra = array() ) use ( $rv_id ) {
	return array_merge( array( '_id' => $rv_id(), 'custom_id' => $cid, 'field_type' => $type, 'field_label' => $label, 'placeholder' => '', 'required' => $req ? 'true' : '', 'width' => $width, 'width_mobile' => '100' ), $extra );
};
$options = "Select an area|\nCivil litigation\nCriminal defence\nFamily law\nProperty & land\nCorporate & commercial\nBanking & recovery\nNot sure";
$form = $wid( 'form', array(
	'form_name' => 'Website enquiry',
	'form_fields' => array(
		$field( 'name', 'text', 'Full name', '50', true ),
		$field( 'email', 'email', 'Email', '50', true ),
		$field( 'phone', 'tel', 'Phone (optional)', '50' ),
		$field( 'area', 'select', 'Practice area (optional)', '50', false, array( 'field_options' => $options ) ),
		$field( 'message', 'textarea', 'Brief message', '100', true, array( 'rows' => 5 ) ),
		$field( 'note', 'html', '', '100', false, array( 'field_html' => '<p class="rvl-note">Please avoid including confidential details in your initial enquiry.</p>' ) ),
	),
	'show_labels' => 'true', 'mark_required' => '', 'label_position' => 'above',
	'button_text' => 'Send Enquiry', 'button_size' => 'md', 'button_align' => 'start',
	'submit_actions' => array( 'email' ),
	'email_to' => get_option( 'admin_email' ),
	'email_subject' => 'New enquiry from the Royal Vision Law website',
	'email_content' => '[all-fields]',
	'email_from' => 'noreply@royalvisionlaw.com',
	'email_from_name' => 'Royal Vision Law website',
	'email_reply_to' => 'email',
	'success_message' => 'Thank you. Your enquiry has been sent. An advocate will review it and contact you to discuss it.',
	'error_message' => 'Sorry, your enquiry could not be sent. Please try again.',
	'required_field_message' => 'This field is required.',
	'invalid_message' => 'Please check this field and try again.',
	'column_gap' => $px( 20 ), 'row_gap' => $px( 20 ),
	'label_spacing' => $px( 6 ), 'label_color' => '#14302A',
	'label_typography_typography' => 'custom', 'label_typography_font_family' => 'Source Sans 3', 'label_typography_font_size' => $px( 15 ), 'label_typography_font_weight' => '600',
	'field_text_color' => '#14302A', 'field_background_color' => '#FFFFFF', 'field_border_color' => '#C9C7C2',
	'field_border_width' => array( 'unit' => 'px', 'top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'isLinked' => true ),
	'field_border_radius' => array( 'unit' => 'px', 'top' => '2', 'right' => '2', 'bottom' => '2', 'left' => '2', 'isLinked' => true ),
	'field_typography_typography' => 'custom', 'field_typography_font_family' => 'Source Sans 3', 'field_typography_font_size' => $px( 16 ),
	'button_background_color' => '#1D5A42', 'button_background_hover_color' => '#14302A', 'button_text_color' => '#FFFFFF', 'button_hover_color' => '#FFFFFF',
	'button_border_radius' => array( 'unit' => 'px', 'top' => '2', 'right' => '2', 'bottom' => '2', 'left' => '2', 'isLinked' => true ),
	'button_typography_typography' => 'custom', 'button_typography_font_family' => 'Source Sans 3', 'button_typography_font_size' => $px( 16 ), 'button_typography_font_weight' => '500',
	'_css_classes' => 'rvl-form',
) );
$contact = $section( 'contact', 'rvl-alt rvl-bt', array(
	$box( 'rvl-split', array(
		$box( 'rvl-contact-info', array(
			$eyebrow( 'Contact' ),
			$h( 'Speak with our team.', 'h2', 'rvl-h2' ),
			$txt( '<p>Tell us briefly what your matter concerns and how to reach you. An advocate will review your enquiry and contact you to discuss it.</p>' ),
			$txt( '<div class="rvl-details">' . $detail( 'Chambers', '45 District Courts, Faisalabad, Punjab, Pakistan' ) . $detail( 'Telephone', '[To be supplied]', true ) . $detail( 'Email', '[To be supplied]', true ) . $detail( 'Office hours', '[To be confirmed]', true ) . '</div>', 'rvl-details-wrap' ),
		), 'column', array( 'width' => array( 'unit' => '%', 'size' => 42 ), 'width_mobile' => array( 'unit' => '%', 'size' => 100 ), 'flex_gap' => $gap( 18 ) ) ),
		$box( 'rvl-form-card', array( $form ), 'column', array(
			'width' => array( 'unit' => '%', 'size' => 58 ), 'width_mobile' => array( 'unit' => '%', 'size' => 100 ),
			'padding' => $pad( 36, 36, 36, 36 ), 'padding_mobile' => $pad( 22, 22, 22, 22 ),
		) ),
	), 'row', array( 'flex_direction_mobile' => 'column', 'flex_gap' => $gap( 64 ), 'flex_gap_mobile' => $gap( 32 ), 'flex_align_items' => 'flex-start', 'flex_wrap' => 'nowrap' ) ),
) );

// Footer
$flinks = function ( $title, $items ) use ( $box, $h ) {
	$els = array( $h( $title, 'span', 'rvl-foot-h' ) );
	foreach ( $items as $label => $url ) { $els[] = $h( $label, 'div', 'rvl-foot-link', $url ); }
	return $box( 'rvl-foot-col', $els, 'column', array( 'flex_gap' => array( 'unit' => 'px', 'size' => 10, 'column' => '10', 'row' => '10', 'isLinked' => true ) ) );
};
$footer = $section( '', 'rvl-footer', array(
	$grid( 'rvl-foot-grid', 4, 2, 1, 36, array(
		$box( 'rvl-foot-col rvl-foot-brand', array(
			$h( 'Royal Vision Law Associate', 'div', 'rvl-foot-name' ),
			$h( 'Advocates &amp; Legal Consultants', 'div', 'rvl-foot-sub' ),
			$txt( '<p>A law firm registered with the Punjab Bar Council (Reg. No. 13294/23), at the District Courts, Faisalabad.</p>', 'rvl-foot-text' ),
		), 'column', array( 'flex_gap' => $gap( 14 ) ) ),
		$flinks( 'Firm', array( 'The Firm' => '#firm', 'Our Team' => '#team', 'Insights' => '#insights', 'Careers' => '#contact', 'Contact' => '#contact' ) ),
		$flinks( 'Practice areas', array( 'Civil litigation' => '#practice', 'Family law' => '#practice', 'Property &amp; land' => '#practice', 'All practice areas' => '#practice' ) ),
		$box( 'rvl-foot-col', array(
			$h( 'Contact', 'span', 'rvl-foot-h' ),
			$txt( '<p>45 District Courts<br>Faisalabad, Pakistan</p><p>Tel: [to be supplied]</p><p>Email: [to be supplied]</p><p>Social: [links if applicable]</p>', 'rvl-foot-text rvl-foot-contact' ),
		), 'column', array( 'flex_gap' => array( 'unit' => 'px', 'size' => 10, 'column' => '10', 'row' => '10', 'isLinked' => true ) ) ),
	) ),
	$box( 'rvl-foot-bottom', array(
		$txt( '<p>© 2026 Royal Vision Law Associate. All rights reserved.</p>', 'rvl-foot-copy' ),
		$box( 'rvl-foot-legal', array( $h( 'Privacy Policy', 'div', 'rvl-foot-small', '#top' ), $h( 'Legal Notice', 'div', 'rvl-foot-small', '#top' ) ), 'row', array( 'width' => array( 'unit' => 'custom', 'size' => 'auto' ), 'flex_gap' => $gap( 22 ) ) ),
	), 'row', array( 'flex_wrap' => 'wrap', 'flex_justify_content' => 'space-between', 'flex_align_items' => 'center', 'flex_gap' => $gap( 12 ) ) ),
), array( 'padding' => $pad( 72, 40, 32, 40 ), 'padding_mobile' => $pad( 44, 20, 32, 20 ), 'flex_gap' => $gap( 36 ) ) );

$data = array( $header, $hero, $firm, $registration, $practice, $approach, $team, $insights, $contact, $footer );

// Top-level containers are parents; everything nested is inner.
$mark_inner = function ( array $els ) use ( &$mark_inner ) {
	foreach ( $els as &$el ) {
		if ( 'container' === $el['elType'] ) { $el['isInner'] = true; }
		$el['elements'] = $mark_inner( $el['elements'] );
	}
	return $els;
};
foreach ( $data as &$top ) { $top['elements'] = $mark_inner( $top['elements'] ); }
unset( $top );

/* ---------- Scoped custom CSS ---------- */
$css = <<<'CSS'
/* Royal Vision Law homepage. "selector" = this page only. */
selector{--rvl-ink:#14302A;--rvl-green:#1D5A42;--rvl-label:#1D6A4B;--rvl-text:#343B42;--rvl-muted:#5E646A;--rvl-line:#DDDCD8;--rvl-field:#C9C7C2;--rvl-stone:#F7F5F1;--rvl-tile:#ECE9E3;--rvl-gold:#7A5A1E;--rvl-gold-bg:#F6EEDC;--rvl-serif:'Libre Baskerville',Georgia,serif;--rvl-sans:'Source Sans 3',system-ui,sans-serif;background:#FFFFFF;color:var(--rvl-text);font-family:var(--rvl-sans);-webkit-font-smoothing:antialiased}
selector .elementor-widget:not(:last-child){margin-block-end:0}
selector a:focus-visible,selector button:focus-visible,selector input:focus-visible,selector select:focus-visible,selector textarea:focus-visible{outline:2px solid var(--rvl-label);outline-offset:2px}
html{scroll-behavior:smooth;scroll-padding-top:90px}
@media (prefers-reduced-motion:reduce){html{scroll-behavior:auto}}

selector .rvl-head{max-width:640px}

/* Bands */
selector .rvl-white{background:#FFFFFF}
selector .rvl-alt{background:var(--rvl-stone)}
selector .rvl-bt{border-top:1px solid var(--rvl-line)}
selector .rvl-bb{border-bottom:1px solid var(--rvl-line)}

/* Header */
selector .rvl-header{position:sticky;top:0;z-index:50;background:#FFFFFF;border-bottom:1px solid var(--rvl-line)}
body.admin-bar selector .rvl-header,.admin-bar .rvl-header{top:32px}
selector .rvl-brand .elementor-heading-title{line-height:1.2}
selector .rvl-brand-name .elementor-heading-title,selector .rvl-brand-name .elementor-heading-title a{font-family:var(--rvl-serif);font-size:18px;font-weight:400;letter-spacing:.01em;color:var(--rvl-ink)}
selector .rvl-brand-sub .elementor-heading-title{font-family:var(--rvl-sans);font-size:11px;letter-spacing:.14em;text-transform:uppercase;font-weight:600;color:var(--rvl-label)}
selector .rvl-nav .elementor-nav-menu--main .elementor-item{padding-top:6px;padding-bottom:6px}
selector .rvl-nav .elementor-nav-menu--dropdown{border-top:1px solid var(--rvl-line);box-shadow:0 12px 24px rgba(20,48,42,.08)}
selector .rvl-nav .elementor-nav-menu--dropdown a{border-bottom:1px solid var(--rvl-line);padding:14px 20px!important}
selector .rvl-nav .elementor-menu-toggle{border:1px solid var(--rvl-line);border-radius:2px;width:44px;height:44px;justify-content:center}

/* Type */
selector .rvl-eyebrow .elementor-heading-title{font-family:var(--rvl-sans);font-size:12.5px;line-height:1.4;letter-spacing:.14em;text-transform:uppercase;font-weight:600;color:var(--rvl-label)}
selector .rvl-h1 .elementor-heading-title{font-family:var(--rvl-serif);font-weight:400;font-size:clamp(34px,4.3vw,54px);line-height:1.15;letter-spacing:-.015em;color:var(--rvl-ink);text-wrap:balance}
selector .rvl-h2 .elementor-heading-title{font-family:var(--rvl-serif);font-weight:400;font-size:clamp(26px,2.9vw,36px);line-height:1.25;color:var(--rvl-ink);text-wrap:balance}
selector .rvl-h3 .elementor-heading-title{font-family:var(--rvl-serif);font-weight:400;font-size:21px;line-height:1.3;color:var(--rvl-ink)}
selector .rvl-h3-sm .elementor-heading-title{font-size:20px;line-height:1.35;text-wrap:balance}
selector .rvl-h3-person .elementor-heading-title{font-size:clamp(17px,1.6vw,20px)}
selector .rvl-body,selector .rvl-body p{font-family:var(--rvl-sans);font-size:17px;line-height:1.65;color:var(--rvl-text);text-wrap:pretty}
selector .rvl-body p{margin:0;max-width:36em}
selector .rvl-lead p{font-size:clamp(17px,1.5vw,19px);max-width:34em}
selector .rvl-small,selector .rvl-small p{font-size:16px;line-height:1.6}
selector .rvl-more .elementor-heading-title,selector .rvl-inline-link .elementor-heading-title{font-family:var(--rvl-sans);font-size:16px;font-weight:600;line-height:1.4}
selector .rvl-inline-link .elementor-heading-title{font-size:15px;padding-top:10px;border-top:1px solid var(--rvl-line)}
selector .rvl-more a,selector .rvl-inline-link a,selector .rvl-body a{color:var(--rvl-green);transition:color .15s ease}
selector .rvl-more a:hover,selector .rvl-inline-link a:hover{color:var(--rvl-ink)}
selector .rvl-more a span{display:inline-block;transition:transform .15s ease}
selector .rvl-more a:hover span{transform:translateX(3px)}

/* Buttons */
selector .rvl-btn .elementor-button{font-family:var(--rvl-sans);font-size:16px;font-weight:500;line-height:1.4;padding:14px 22px;border-radius:2px;border:1px solid var(--rvl-green);background:var(--rvl-green);color:#FFFFFF;transition:background-color .15s ease,border-color .15s ease}
selector .rvl-btn .elementor-button:hover,selector .rvl-btn .elementor-button:focus-visible{background:var(--rvl-ink);border-color:var(--rvl-ink);color:#FFFFFF}
selector .rvl-btn-ghost .elementor-button{background:transparent;color:var(--rvl-ink);border-color:var(--rvl-ink)}
selector .rvl-btn-ghost .elementor-button:hover,selector .rvl-btn-ghost .elementor-button:focus-visible{background:#FFFFFF;color:var(--rvl-ink);border-color:var(--rvl-ink)}
selector .rvl-btn-sm .elementor-button{font-size:15px;padding:10px 18px}
@media (max-width:767px){selector .rvl-hide-mobile{display:none}}

/* Placeholders for photography (swap for real images) */
selector .rvl-slot{position:relative;border-radius:2px;overflow:hidden;background:linear-gradient(160deg,#ECE9E3 0%,#DCE3DE 55%,#C9D6CF 100%)}
selector .rvl-slot::after{content:"";position:absolute;inset:0;background:repeating-linear-gradient(135deg,rgba(20,48,42,.035) 0 1px,transparent 1px 14px)}
selector .rvl-slot-hero{flex:1 1 auto;min-height:clamp(280px,42vw,540px);height:100%}
selector .rvl-hero-media{align-self:stretch}
selector .rvl-slot-portrait{aspect-ratio:4/5;width:100%}
selector .rvl-slot-thumb{aspect-ratio:16/10;width:100%}

/* Firm facts */
selector .rvl-split-b{padding-top:clamp(0px,2.6vw,34px)}
selector .rvl-facts{border-top:1px solid var(--rvl-ink)}
selector .rvl-fact{padding:20px 24px 4px 0}
selector .rvl-fact-k .elementor-heading-title{font-family:var(--rvl-sans);font-size:13px;line-height:1.4;font-weight:400;color:var(--rvl-muted)}
selector .rvl-fact-v .elementor-heading-title{font-family:var(--rvl-serif);font-size:17px;line-height:1.4;font-weight:400;color:var(--rvl-ink)}

/* Registration */
selector .rvl-reg-figure{max-width:380px}
selector .rvl-doc{aspect-ratio:3/4;width:100%;background:#FFFFFF;border:1px solid var(--rvl-line);box-shadow:0 18px 40px -24px rgba(20,48,42,.35)}
selector .rvl-doc-icon .elementor-icon{font-size:44px;color:var(--rvl-line)}
selector .rvl-caption p{margin:0;font-size:14px;line-height:1.5;color:var(--rvl-muted)}
selector .rvl-dl{border-top:1px solid var(--rvl-line);font-size:16px}
selector .rvl-dl-row{display:flex;justify-content:space-between;gap:16px;padding:12px 0;border-bottom:1px solid var(--rvl-line)}
selector .rvl-dl-row span{color:var(--rvl-muted)}
selector .rvl-dl-row strong{color:var(--rvl-ink);font-weight:500;text-align:right}

/* Practice cards */
selector .rvl-card{background:#FFFFFF;border:1px solid var(--rvl-line);border-radius:2px;color:var(--rvl-text);transition:border-color .15s ease,transform .15s ease,box-shadow .15s ease}
selector .rvl-card:hover{border-color:var(--rvl-green);transform:translateY(-2px);box-shadow:0 14px 30px -22px rgba(20,48,42,.45)}
selector .rvl-grow{flex-grow:1}
selector .rvl-tag .elementor-heading-title{display:inline-block;font-family:var(--rvl-sans);font-size:11.5px;line-height:1.5;letter-spacing:.08em;text-transform:uppercase;font-weight:600;color:var(--rvl-gold);background:var(--rvl-gold-bg);padding:2px 8px;border-radius:2px}
selector .rvl-card-cta .elementor-heading-title{display:block;font-family:var(--rvl-sans);font-size:15px;line-height:1.4;font-weight:600;color:var(--rvl-green);padding-top:12px;border-top:1px solid var(--rvl-line)}
@media (prefers-reduced-motion:reduce){selector .rvl-card,selector .rvl-card:hover{transition:none;transform:none}}

/* Approach */
selector .rvl-step{border-top:1px solid var(--rvl-ink);padding:24px 0}

/* People */
selector .rvl-role .elementor-heading-title{font-family:var(--rvl-sans);font-size:15px;line-height:1.4;font-weight:500;color:var(--rvl-text)}
selector .rvl-focus .elementor-heading-title{font-family:var(--rvl-sans);font-size:14px;line-height:1.4;font-weight:400;color:var(--rvl-muted)}

/* Insights */
selector .rvl-meta{display:flex;flex-wrap:wrap;gap:10px;align-items:center;margin:0;font-size:13.5px;line-height:1.4}
selector .rvl-cat{font-weight:600;color:var(--rvl-label);letter-spacing:.06em;text-transform:uppercase}
selector .rvl-date{color:var(--rvl-muted)}
selector .rvl-tag-inline{font-size:11.5px;letter-spacing:.08em;text-transform:uppercase;font-weight:600;color:var(--rvl-gold);background:var(--rvl-gold-bg);padding:1px 7px;border-radius:2px}

/* Contact */
selector .rvl-details{margin-top:12px;border-top:1px solid var(--rvl-line)}
selector .rvl-detail{padding:16px 0;border-bottom:1px solid var(--rvl-line);display:flex;flex-direction:column;gap:2px;line-height:1.5}
selector .rvl-detail .k{font-size:13px;color:var(--rvl-muted)}
selector .rvl-detail .v{color:var(--rvl-ink);font-weight:500}
selector .rvl-detail .tbc{color:var(--rvl-gold);font-weight:500}
selector .rvl-form-card{background:#FFFFFF;border:1px solid var(--rvl-line);border-radius:2px}
selector .rvl-form .elementor-field-textual,selector .rvl-form select{min-height:48px;padding:0 14px}
selector .rvl-form textarea.elementor-field-textual{padding:12px 14px;line-height:1.55;resize:vertical}
selector .rvl-form .elementor-field-textual:focus{border-color:var(--rvl-label)!important;box-shadow:0 0 0 3px rgba(29,106,75,.15)}
selector .rvl-form .rvl-note{margin:0;font-size:14.5px;line-height:1.5;color:var(--rvl-muted);padding:12px 14px;background:var(--rvl-stone);border-left:2px solid var(--rvl-line)}
selector .rvl-form .elementor-button{min-height:48px;padding:14px 26px;transition:background-color .15s ease}
selector .rvl-form .elementor-message-success{background:#E8F1EC;border:1px solid var(--rvl-label);padding:14px 16px;color:var(--rvl-ink)}
selector .rvl-form .elementor-message-danger,selector .rvl-form .elementor-error .elementor-field-textual{border-color:#9B2C2C}
selector .rvl-form .elementor-message-danger,selector .rvl-form .elementor-message.elementor-help-inline{color:#9B2C2C}

/* Footer */
selector .rvl-footer{background:#12302A;color:#D3DDD8}
selector .rvl-foot-name .elementor-heading-title{font-family:var(--rvl-serif);font-size:19px;line-height:1.3;font-weight:400;color:#FFFFFF}
selector .rvl-foot-sub .elementor-heading-title,selector .rvl-foot-h .elementor-heading-title{font-family:var(--rvl-sans);font-size:11px;line-height:1.4;letter-spacing:.14em;text-transform:uppercase;font-weight:600;color:#A9C4B6}
selector .rvl-foot-h .elementor-heading-title{font-size:12px;margin-bottom:4px}
selector .rvl-foot-link .elementor-heading-title,selector .rvl-foot-link a{font-family:var(--rvl-sans);font-size:15.5px;line-height:1.5;font-weight:400;color:#FFFFFF}
selector .rvl-foot-link a:hover{color:#A9C4B6}
selector .rvl-foot-text,selector .rvl-foot-text p{font-size:15.5px;line-height:1.6;color:#D3DDD8;margin:0}
selector .rvl-foot-contact p+p{margin-top:10px}
selector .rvl-foot-bottom{border-top:1px solid #2F4E45;padding-top:22px}
selector .rvl-foot-copy p{margin:0;font-size:14px;color:#A9C4B6}
selector .rvl-foot-small .elementor-heading-title,selector .rvl-foot-small a{font-family:var(--rvl-sans);font-size:14px;font-weight:400;color:#D3DDD8}
selector .rvl-foot-small a:hover{color:#FFFFFF}

/* Mobile header */
@media (max-width:767px){selector .rvl-brand-name .elementor-heading-title,selector .rvl-brand-name .elementor-heading-title a{font-size:16px}selector .rvl-brand-sub .elementor-heading-title{font-size:9.5px;letter-spacing:.1em}}
CSS;

/* ---------- Create / update the page ---------- */
$existing = get_posts( array( 'post_type' => 'page', 'post_status' => 'any', 'meta_key' => '_rvl_homepage', 'meta_value' => '1', 'numberposts' => 1 ) );
$page_id  = $existing ? $existing[0]->ID : wp_insert_post( array( 'post_type' => 'page', 'post_title' => 'Home', 'post_status' => 'publish', 'post_name' => 'home' ) );
update_post_meta( $page_id, '_rvl_homepage', '1' );
update_post_meta( $page_id, '_wp_page_template', 'elementor_canvas' );
update_post_meta( $page_id, '_elementor_edit_mode', 'builder' );
update_post_meta( $page_id, '_elementor_template_type', 'wp-page' );

$doc = \Elementor\Plugin::$instance->documents->get( $page_id, false );
$doc->save( array(
	'elements' => $data,
	'settings' => array( 'template' => 'elementor_canvas', 'custom_css' => $css, 'hide_title' => 'yes', 'post_status' => 'publish' ),
) );

update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $page_id );

\Elementor\Plugin::$instance->files_manager->clear_cache();
do_action( 'litespeed_purge_all' );

$saved = json_decode( get_post_meta( $page_id, '_elementor_data', true ), true );
$ps    = get_post_meta( $page_id, '_elementor_page_settings', true );
return array(
	'page_id'       => $page_id,
	'url'           => get_permalink( $page_id ),
	'edit'          => admin_url( 'post.php?post=' . $page_id . '&action=elementor' ),
	'sections'      => is_array( $saved ) ? count( $saved ) : 'not saved',
	'custom_css'    => isset( $ps['custom_css'] ) ? strlen( $ps['custom_css'] ) : 0,
	'template'      => get_post_meta( $page_id, '_wp_page_template', true ),
	'front'         => get_option( 'page_on_front' ),
	'menu'          => $menu_slug,
	'kit_colors'    => count( $kit_set['custom_colors'] ),
);
