<?php
/**
 * Team portraits for royalvisionlaw.com.
 * Sideloads the advocate studio portraits in team-photos/ (960x1200, one shared ivory
 * backdrop made by team-photos/compose.py), creates or updates their Team members
 * (position "Advocate"), replaces any earlier portrait, lets the homepage show
 * up to 8 members, and sets the team grid to three cards per row with aligned buttons.
 * Run after elementor-law-premium.php (needs sandbox/rvl-team.php active). Safe to re-run.
 */

require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$base    = 'https://raw.githubusercontent.com/royalvisioneducationchannel-jpg/blog-/claude/magical-turing-m9b0sf/royal-vision-law/team-photos/';
$members = array(
	array( 'Ch. Abdul Nabi Qamar', 'ch-abdul-nabi-qamar', 1 ),
	array( 'Imran Ali', 'imran-ali', 2 ),
	array( 'Ch. Arshad Mehmood Warraich', 'arshad-mehmood-warraich', 3 ),
	array( 'Younis Amin', 'younis-amin', 4 ),
	array( 'Shahid Amin', 'shahid-amin', 5 ),
	array( 'Rana Tahir Mehmood', 'rana-tahir-mehmood', 6 ),
	array( 'Ch. Umar Shahzad Ashraf', 'ch-umar-shahzad-ashraf', 7 ),
);
$out = array();
foreach ( $members as $m ) {
	list( $name, $slug, $order ) = $m;
	$status = $m[3] ?? 'publish';
	$ex     = get_posts( array( 'post_type' => 'rvl_team', 'title' => $name, 'post_status' => 'any', 'numberposts' => 1, 'fields' => 'ids' ) );
	$id     = $ex ? $ex[0] : wp_insert_post( array( 'post_type' => 'rvl_team', 'post_status' => $status, 'post_title' => $name, 'menu_order' => $order ) );
	update_post_meta( $id, 'rvl_position', 'Advocate' );
	$src = $base . $slug . '.jpg?v=studio-3'; // Bump the version when a portrait file changes.
	$old = (int) get_post_thumbnail_id( $id );
	$att = get_posts( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'meta_key' => '_rvl_source', 'meta_value' => $src, 'numberposts' => 1, 'fields' => 'ids' ) );
	if ( $att ) {
		$aid = $att[0];
	} else {
		$tmp = download_url( $src, 60 );
		if ( is_wp_error( $tmp ) ) { $out[ $name ] = 'download failed: ' . $tmp->get_error_message(); continue; }
		$aid = media_handle_sideload( array( 'name' => 'team-' . $slug . '-studio.jpg', 'tmp_name' => $tmp ), $id, $name );
		if ( is_wp_error( $aid ) ) { @unlink( $tmp ); $out[ $name ] = 'sideload failed: ' . $aid->get_error_message(); continue; }
		update_post_meta( $aid, '_rvl_source', $src );
		update_post_meta( $aid, '_wp_attachment_image_alt', $name . ', Advocate, Royal Vision Law Associate' );
	}
	set_post_thumbnail( $id, $aid );
	if ( $old && $old !== $aid && get_post_meta( $old, '_rvl_source', true ) ) { wp_delete_attachment( $old, true ); } // Only portraits this script added.
	$out[ $name ] = array( 'member' => $id, 'photo' => $aid );
}

// Homepage: show up to 8 members.
$doc = \Elementor\Plugin::$instance->documents->get( 22, false );
$fix = function ( array $els ) use ( &$fix ) {
	foreach ( $els as &$el ) {
		if ( 'shortcode' === ( $el['widgetType'] ?? '' ) && false !== strpos( $el['settings']['shortcode'] ?? '', 'rvl_team' ) ) { $el['settings']['shortcode'] = '[rvl_team limit="8"]'; }
		$el['elements'] = $fix( $el['elements'] );
	}
	return $els;
};
$doc->save( array( 'elements' => $fix( $doc->get_elements_data() ), 'settings' => array( 'template' => 'elementor_canvas', 'hide_title' => 'yes' ) ) );

$kit_doc = \Elementor\Plugin::$instance->documents->get( (int) get_option( 'elementor_active_kit' ), false );
$kit_set = array_filter( (array) $kit_doc->get_settings(), function ( $k ) { return ! is_int( $k ); }, ARRAY_FILTER_USE_KEY );
$add = <<<'CSS'

/* Team grid v2: three per row, aligned buttons */
selector .rvl-team{grid-template-columns:repeat(auto-fill,minmax(min(100%,300px),1fr));max-width:1120px}
selector .rvl-team[data-count="4"]{max-width:760px}
selector .rvl-tm{display:flex;flex-direction:column}
selector .rvl-tm-body{flex:1 1 auto;display:flex;flex-direction:column;align-items:center}
selector .rvl-tm-links{margin-top:auto;padding-top:20px;flex-wrap:nowrap}
selector .rvl-tm-links a{white-space:nowrap;padding:10px 12px}
@media (max-width:360px){selector .rvl-tm-links{flex-wrap:wrap}}
CSS;
if ( false === strpos( $kit_set['custom_css'], '/* Team grid v2:' ) ) { $kit_set['custom_css'] .= $add; }
$kit_doc->save( array( 'settings' => $kit_set ) );

\Elementor\Plugin::$instance->files_manager->clear_cache();
do_action( 'litespeed_purge_all' );

$all = get_posts( array( 'post_type' => 'rvl_team', 'post_status' => array( 'publish', 'draft' ), 'numberposts' => -1, 'orderby' => array( 'menu_order' => 'ASC' ) ) );
$out['team_order'] = array_map( function ( $p ) { return $p->menu_order . ' ' . $p->post_title . ( has_post_thumbnail( $p ) ? ' [photo]' : ' [initials]' ); }, $all );
return $out;
