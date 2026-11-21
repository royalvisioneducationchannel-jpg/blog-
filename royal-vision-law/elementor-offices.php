<?php
/**
 * Three offices for royalvisionlaw.com, and "Ch. Imran Ali".
 *
 * Replaces the old single address (45 District Courts, Faisalabad) across the homepage, header,
 * footer, Our Team, Privacy Policy and Legal Notice with the firm's three offices (Lahore, High
 * Court Lahore, Faisalabad); adds an "Our Offices" section with a card and Google Maps link for
 * each office before Contact; renames Imran Ali to Ch. Imran Ali; and hides the "Advocate" label
 * at the top of the team cards. The Punjab Bar Council registration paragraph is left as it is,
 * because it describes what the 2023 registration letter says. Safe to re-run.
 */

$offices = array( // Exactly as the firm gave them (name, full address, address shown in compact places).
	'lahore'     => array( 'Royal Vision Law Associates (Lahore Office)', '1st Floor, 52 CCA DD Block, DHA Phase 4, Lahore, 54000 Pakistan' ),
	'high-court' => array( 'Royal Vision Law Associates (High Court Office)', 'G-13, Anexy Aiwan-e-Auqaf, The Mall, Near Shah Chiragh Masjid, Mozang Chungi, Lahore, 54000 Pakistan' ),
	'faisalabad' => array( 'Royal Vision Law Associates (Faisalabad Office)', '1395 - B, Pilot Ground, People\'s Colony No 1, Faisalabad, 38000 Pakistan' ),
);
foreach ( $offices as &$o ) { $o[2] = $o[1]; } // Full address everywhere, never shortened.
unset( $o );
$map   = function ( $address ) { return 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( $address ); };
$phone = '<a href="tel:+923345298703">+92 334 5298703</a>';
$out   = array();

/* ---------- Text that changes wherever it appears ---------- */
$text = array(
	'Advocates &amp; Legal Consultants · Faisalabad'                                     => 'Advocates &amp; Legal Consultants · Lahore · Faisalabad',
	'registered with the Punjab Bar Council, based at the District Courts, Faisalabad. We help' => 'registered with the Punjab Bar Council, with offices in Lahore and Faisalabad. We help',
	'A registered firm of advocates at the District Courts, Faisalabad'                  => 'A registered firm of advocates in Lahore and Faisalabad',
	'founded by advocates Ch. Abdul Nabi Qamar and Imran Ali and registered as a law firm by the Punjab Bar Council in 2023. We work from chambers at the District Courts, where we advise and represent clients in matters before the local courts.'
		=> 'founded by advocates Ch. Abdul Nabi Qamar and Ch. Imran Ali and registered as a law firm by the Punjab Bar Council in 2023. We work from three offices, in DHA Lahore, at our High Court office on The Mall, Lahore, and in Faisalabad, advising and representing clients before the courts.',
	'Ch. Abdul Nabi Qamar · Imran Ali'                                                   => 'Ch. Abdul Nabi Qamar · Ch. Imran Ali',
	'At the District Courts'                                                             => 'Three offices',
	'Our chambers are at 45 District Courts, Faisalabad, where many matters are heard.'  => 'Offices in DHA Lahore, at the High Court on The Mall, Lahore, and in Faisalabad.',
	'Chambers at the District Courts, Faisalabad'                                        => 'Offices in Lahore & Faisalabad',
	'A law firm registered with the Punjab Bar Council (Reg. No. 13294/23), at the District Courts, Faisalabad.' => 'A law firm registered with the Punjab Bar Council (Reg. No. 13294/23), with offices in Lahore and Faisalabad.',
	'Registered with the Punjab Bar Council and based at the District Courts, Faisalabad.' => 'Registered with the Punjab Bar Council, with offices in Lahore and Faisalabad.',
);

/* ---------- Whole widgets that are rewritten ---------- */
$rows = '';
foreach ( $offices as $o ) { $rows .= '<div class="rvl-detail"><span class="k">' . esc_html( $o[0] ) . '</span><span class="v">' . esc_html( $o[2] ) . '</span></div>'; }
$foot = '';
foreach ( $offices as $o ) { $foot .= '<p><strong>' . esc_html( $o[0] ) . '</strong><br>' . esc_html( $o[2] ) . '</p>'; }
$full = array();
foreach ( $offices as $o ) { $full[] = esc_html( $o[0] ) . ': ' . esc_html( $o[1] ); }

$by_id = array(
	22 => array(
		'5020675' => array( 'title' => 'Offices' ),
		'd12099d' => array( 'title' => 'Lahore &amp; Faisalabad' ),
		'e2236f4' => array( 'editor' => '<div class="rvl-details">' . $rows . '<div class="rvl-detail"><span class="k">Telephone</span><a class="v" href="tel:+923345298703">+92 334 5298703</a></div></div>' ),
	),
	55 => array(
		'b1f9490' => array( 'editor' => $foot . '<p>' . $phone . '</p>' ),
	),
);
$prose = array(
	3  => array(
		'with chambers at 45 District Courts, Faisalabad, Punjab, Pakistan.' => 'with offices in Lahore and Faisalabad, Punjab, Pakistan.',
		'<p>45 District Courts, Faisalabad, Punjab, Pakistan. Telephone: '     => '<p>' . implode( '<br>', $full ) . '<br>Telephone: ',
	),
	45 => array(
		'Advocates: Ch. Abdul Nabi Qamar and Imran Ali. Chambers: 45 District Courts, Faisalabad, Punjab, Pakistan.' => 'Advocates: Ch. Abdul Nabi Qamar and Ch. Imran Ali. Offices: ' . implode( '; ', $full ) . '.',
	),
);

$swap = function ( $v ) use ( &$swap, $text ) {
	if ( is_array( $v ) ) { foreach ( $v as $k => $x ) { $v[ $k ] = $swap( $x ); } return $v; }
	return is_string( $v ) ? strtr( $v, $text ) : $v;
};
$walk = function ( array $els, $doc_id ) use ( &$walk, $swap, $by_id, $prose, $full, &$out ) {
	foreach ( $els as &$el ) {
		$el['settings'] = $swap( $el['settings'] );
		foreach ( $by_id[ $doc_id ][ $el['id'] ] ?? array() as $k => $v ) { $el['settings'][ $k ] = $v; $out['set'][] = "$doc_id:{$el['id']}:$k"; }
		if ( isset( $prose[ $doc_id ] ) && isset( $el['settings']['editor'] ) ) {
			$before = $el['settings']['editor'];
			$el['settings']['editor'] = strtr( $before, $prose[ $doc_id ] );
			$el['settings']['editor'] = preg_replace( '#(<h2>Contact</h2>\s*<p>).*?(Telephone: )#s', '$1' . implode( '<br>', $full ) . '<br>$2', $el['settings']['editor'] );
			$el['settings']['editor'] = preg_replace( '#Offices: .*?\. (Telephone: )#s', 'Offices: ' . implode( '; ', $full ) . '. $1', $el['settings']['editor'] );
			if ( $before !== $el['settings']['editor'] ) { $out['set'][] = "$doc_id:{$el['id']}:prose"; }
		}
		if ( 53 === $doc_id && isset( $el['settings']['icon_list'] ) ) {
			foreach ( $el['settings']['icon_list'] as &$item ) {
				if ( '45 District Courts, Faisalabad' === $item['text'] ) { $item['text'] = 'Lahore · Faisalabad'; $item['link'] = array( 'url' => home_url( '/#offices' ), 'is_external' => '', 'nofollow' => '' ); $out['set'][] = '53:top-bar'; }
			}
			unset( $item );
		}
		$el['elements'] = $walk( $el['elements'], $doc_id );
	}
	return $els;
};

/* ---------- "Our Offices" section, cloned from existing blocks so it matches the design ---------- */
$eid   = function () { return substr( md5( uniqid( '', true ) ), 0, 7 ); };
$reid  = function ( array $el ) use ( &$reid, $eid ) { $el['id'] = $eid(); $el['elements'] = array_map( $reid, $el['elements'] ); return $el; };
$find  = function ( array $els, $id ) use ( &$find ) { foreach ( $els as $el ) { if ( $el['id'] === $id ) { return $el; } $r = $find( $el['elements'], $id ); if ( $r ) { return $r; } } return null; };

foreach ( array( 22, 53, 55, 105, 3, 45 ) as $doc_id ) {
	$doc = \Elementor\Plugin::$instance->documents->get( $doc_id, false );
	if ( ! $doc ) { continue; }
	$els = $walk( $doc->get_elements_data(), $doc_id );

	if ( 22 === $doc_id && ! array_filter( $els, function ( $e ) { return 'offices' === ( $e['settings']['_element_id'] ?? '' ); } ) ) {
		$section = $reid( $find( $els, '1280e55' ) );          // Our Approach section: spacing and stone background
		$section['settings']['css_classes'] = 'rvl-section rvl-alt rvl-offices';
		$section['settings']['_element_id'] = 'offices';
		$head = $reid( $find( $els, 'e105b3a' ) );             // Why Choose Us heading block
		$head['elements'][0]['settings']['title'] = 'Our Offices';
		$head['elements'][1]['settings']['title'] = 'Visit us in Lahore or Faisalabad';
		$grid = $find( $els, '4e1ed62' );                       // Practice Areas grid
		$grid['settings']['css_classes'] = 'rvl-grid rvl-office-grid';
		$card_tpl = $find( $els, '8e72418' );
		$grid['elements'] = array();
		foreach ( $offices as $o ) {
			$card = $reid( $card_tpl );
			$card['settings']['css_classes'] = 'rvl-card rvl-office';
			$card['settings']['link'] = array( 'url' => $map( $o[1] ), 'is_external' => 'on', 'nofollow' => '', 'custom_attributes' => '' );
			foreach ( $card['elements'] as &$w ) {
				$cls = $w['settings']['_css_classes'] ?? '';
				if ( false !== strpos( $cls, 'rvl-card-icon' ) ) { $w['settings']['selected_icon'] = array( 'value' => 'fas fa-map-marker-alt', 'library' => 'fa-solid' ); }
				if ( false !== strpos( $cls, 'rvl-h3' ) ) { $w['settings']['title'] = $o[0]; }
				if ( false !== strpos( $cls, 'rvl-body' ) ) { $w['settings']['editor'] = '<p>' . esc_html( $o[1] ) . '</p>'; }
				if ( false !== strpos( $cls, 'rvl-card-cta' ) ) { $w['settings']['title'] = 'Get directions →'; }
			}
			unset( $w );
			$grid['elements'][] = $card;
		}
		$grid = $reid( $grid );
		$section['elements'] = array( $head, $grid );
		$at = array_search( '69e952f', array_column( $els, 'id' ), true ); // before Contact
		array_splice( $els, false === $at ? count( $els ) - 1 : $at, 0, array( $section ) );
		$out['set'][] = '22:offices-section';
	}

	if ( 22 === $doc_id ) { // keep existing office cards in step with $offices
		foreach ( $els as &$sec ) {
			if ( 'offices' !== ( $sec['settings']['_element_id'] ?? '' ) ) { continue; }
			foreach ( $sec['elements'] as &$grid ) {
				if ( false === strpos( $grid['settings']['css_classes'] ?? '', 'rvl-office-grid' ) ) { continue; }
				foreach ( array_values( $offices ) as $i => $o ) {
					if ( ! isset( $grid['elements'][ $i ] ) ) { continue; }
					$grid['elements'][ $i ]['settings']['link']['url'] = $map( $o[1] );
					foreach ( $grid['elements'][ $i ]['elements'] as &$w ) {
						$cls = $w['settings']['_css_classes'] ?? '';
						if ( false !== strpos( $cls, 'rvl-h3' ) ) { $w['settings']['title'] = $o[0]; }
						if ( false !== strpos( $cls, 'rvl-body' ) ) { $w['settings']['editor'] = '<p>' . esc_html( $o[1] ) . '</p>'; }
					}
					unset( $w );
				}
				$out['set'][] = '22:offices-cards';
			}
			unset( $grid );
		}
		unset( $sec );
	}

	$save = array( 'elements' => $els );
	if ( in_array( $doc_id, array( 22, 105, 3, 45 ), true ) ) { $save['settings'] = array( 'template' => 'elementor_canvas', 'hide_title' => 'yes' ); }
	$doc->save( $save );
}

/* ---------- Team: Ch. Imran Ali, and intros without the old address ---------- */
foreach ( get_posts( array( 'post_type' => 'rvl_team', 'post_status' => 'any', 'numberposts' => -1 ) ) as $m ) {
	$upd = array( 'ID' => $m->ID );
	if ( 'Imran Ali' === $m->post_title ) { $upd['post_title'] = 'Ch. Imran Ali'; }
	$ex = str_replace( 'before the courts at the District Courts, Faisalabad.', 'before the courts in Lahore and Faisalabad.', $m->post_excerpt );
	if ( $ex !== $m->post_excerpt ) { $upd['post_excerpt'] = $ex; }
	if ( count( $upd ) > 1 ) { wp_update_post( $upd ); $out['team'][] = $m->ID . ' ' . ( $upd['post_title'] ?? $m->post_title ); }
}

/* ---------- CSS: hide the role label on team cards; office cards ---------- */
$kit_doc = \Elementor\Plugin::$instance->documents->get( (int) get_option( 'elementor_active_kit' ), false );
$kit_set = array_filter( (array) $kit_doc->get_settings(), function ( $k ) { return ! is_int( $k ); }, ARRAY_FILTER_USE_KEY );
$add = <<<'CSS'

/* Offices: team cards without the "Advocate" label on top; office cards */
selector .rvl-tm-role{display:none}
selector .rvl-office .rvl-body p{font-size:15.5px;line-height:1.7}
selector .rvl-office .rvl-card-icon .elementor-icon{background:var(--rvl-night)}
selector .rvl-office .rvl-card-icon .elementor-icon svg{fill:var(--rvl-gold-light)}
CSS;
if ( false === strpos( $kit_set['custom_css'], '/* Offices: team cards' ) ) { $kit_set['custom_css'] .= $add; $kit_doc->save( array( 'settings' => $kit_set ) ); }

\Elementor\Plugin::$instance->files_manager->clear_cache();
do_action( 'litespeed_purge_all' );
return $out;
