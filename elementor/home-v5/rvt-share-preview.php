<?php
/**
 * Secret share link for the draft fashion pages (rv_fashion_home_id, rv_fashion_shop_id).
 * Anyone with ?rv_share=<token> can view these drafts without logging in, and
 * links between the two drafts keep the token so the visitor can move between them.
 * Not indexed, not cached. Delete this file (or the rv_share_token option) to revoke.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Draft page IDs that the share link opens.
 *
 * @return int[]
 */
function rvt_share_pages() {
	return array_values( array_filter( array( (int) get_option( 'rv_fashion_home_id' ), (int) get_option( 'rv_fashion_shop_id' ) ) ) );
}

add_filter( 'posts_results', function ( $posts, $q ) {
	if ( is_admin() || ! $q->is_main_query() || count( (array) $posts ) !== 1 || empty( $_GET['rv_share'] ) ) {
		return $posts;
	}
	$token = (string) get_option( 'rv_share_token' );
	$key   = sanitize_text_field( wp_unslash( $_GET['rv_share'] ) );
	if ( $token === '' || ! in_array( (int) $posts[0]->ID, rvt_share_pages(), true ) || ! hash_equals( $token, $key ) ) {
		return $posts;
	}
	if ( $posts[0]->post_status !== 'publish' ) {
		$posts[0]->post_status = 'publish';
	}
	add_filter( 'wp_robots', 'wp_robots_no_robots' );
	if ( ! headers_sent() ) {
		header( 'X-Robots-Tag: noindex, nofollow', true );
		nocache_headers();
	}
	do_action( 'litespeed_control_set_nocache', 'rv share preview' );

	// Keep the token on links to the other draft page (e.g. ?page_id=123 or ?page_id=123#cat-khaddar).
	ob_start( function ( $html ) use ( $token ) {
		$ids = implode( '|', rvt_share_pages() );
		return preg_replace( '/(\?page_id=(?:' . $ids . '))(?=["\'#])/', '$1&#038;rv_share=' . rawurlencode( $token ), $html );
	} );
	return $posts;
}, 10, 2 );
