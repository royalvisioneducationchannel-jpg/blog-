<?php
/**
 * Royal Vision Law: Team members.
 *
 * Adds a "Team" section in wp-admin. Each member has a name (title), photo (featured
 * image), position, practice focus, a short introduction (excerpt) and a display order.
 * Members are shown anywhere with the shortcode [rvl_team] (optional: limit="4").
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', function () {
	register_post_type( 'rvl_team', array(
		'labels'          => array(
			'name'               => 'Team',
			'singular_name'      => 'Team member',
			'menu_name'          => 'Team',
			'add_new'            => 'Add team member',
			'add_new_item'       => 'Add team member',
			'edit_item'          => 'Edit team member',
			'new_item'           => 'New team member',
			'view_item'          => 'View team member',
			'all_items'          => 'All team members',
			'search_items'       => 'Search team',
			'not_found'          => 'No team members yet.',
			'featured_image'     => 'Photo',
			'set_featured_image' => 'Set photo (portrait, 4:5)',
			'remove_featured_image' => 'Remove photo',
			'use_featured_image' => 'Use as photo',
		),
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_rest'        => true,
		'menu_position'       => 21,
		'menu_icon'           => 'dashicons-groups',
		'supports'            => array( 'title', 'thumbnail', 'excerpt', 'page-attributes' ),
		'capability_type'     => 'post',
		'map_meta_cap'        => true,
	) );
	add_theme_support( 'post-thumbnails', array( 'rvl_team' ) );
} );

add_filter( 'enter_title_here', function ( $text, $post ) {
	return 'rvl_team' === $post->post_type ? 'Full name, e.g. Ch. Abdul Nabi Qamar' : $text;
}, 10, 2 );

/* ---------- Position / focus fields ---------- */
add_action( 'add_meta_boxes_rvl_team', function () {
	add_meta_box( 'rvl_team_details', 'Member details', function ( $post ) {
		wp_nonce_field( 'rvl_team_save', 'rvl_team_nonce' );
		$position = get_post_meta( $post->ID, 'rvl_position', true );
		$focus    = get_post_meta( $post->ID, 'rvl_focus', true );
		echo '<p><label for="rvl_position"><strong>Position</strong></label><br>';
		echo '<input type="text" id="rvl_position" name="rvl_position" class="widefat" placeholder="e.g. Advocate High Court" value="' . esc_attr( $position ) . '"></p>';
		echo '<p><label for="rvl_focus"><strong>Practice focus</strong> (optional)</label><br>';
		echo '<input type="text" id="rvl_focus" name="rvl_focus" class="widefat" placeholder="e.g. Civil and property litigation" value="' . esc_attr( $focus ) . '"></p>';
		echo '<p class="description">Write a short introduction in the <em>Excerpt</em> box, set a portrait in <em>Photo</em>, and use <em>Order</em> (Page Attributes) to sort: lower numbers show first.</p>';
	}, 'rvl_team', 'normal', 'high' );
} );

add_action( 'save_post_rvl_team', function ( $post_id ) {
	if ( ! isset( $_POST['rvl_team_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['rvl_team_nonce'] ) ), 'rvl_team_save' ) ) { return; }
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
	if ( ! current_user_can( 'edit_post', $post_id ) ) { return; }
	foreach ( array( 'rvl_position', 'rvl_focus' ) as $key ) {
		if ( isset( $_POST[ $key ] ) ) {
			update_post_meta( $post_id, $key, sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) );
		}
	}
} );

add_action( 'init', function () {
	foreach ( array( 'rvl_position', 'rvl_focus' ) as $key ) {
		register_post_meta( 'rvl_team', $key, array( 'type' => 'string', 'single' => true, 'show_in_rest' => true, 'sanitize_callback' => 'sanitize_text_field', 'auth_callback' => function () { return current_user_can( 'edit_posts' ); } ) );
	}
} );

/* Admin list: photo + position columns */
add_filter( 'manage_rvl_team_posts_columns', function ( $cols ) {
	return array( 'cb' => $cols['cb'], 'rvl_photo' => 'Photo', 'title' => 'Name', 'rvl_position' => 'Position', 'menu_order' => 'Order', 'date' => $cols['date'] );
} );
add_action( 'manage_rvl_team_posts_custom_column', function ( $col, $id ) {
	if ( 'rvl_photo' === $col ) { echo has_post_thumbnail( $id ) ? get_the_post_thumbnail( $id, array( 48, 48 ) ) : '—'; }
	if ( 'rvl_position' === $col ) { echo esc_html( get_post_meta( $id, 'rvl_position', true ) ); }
	if ( 'menu_order' === $col ) { echo (int) get_post_field( 'menu_order', $id ); }
}, 10, 2 );

/* ---------- Shortcode ---------- */
function rvl_team_initials( $name ) {
	$name  = preg_replace( '/^(ch|chaudhry|mr|mrs|ms|dr|mian|rana|malik|syed|sheikh)\.?\s+/i', '', trim( $name ) );
	$parts = preg_split( '/\s+/', $name );
	$first = mb_substr( $parts[0], 0, 1 );
	$last  = count( $parts ) > 1 ? mb_substr( end( $parts ), 0, 1 ) : '';
	return mb_strtoupper( $first . $last );
}

add_shortcode( 'rvl_team', function ( $atts ) {
	$atts    = shortcode_atts( array( 'limit' => -1 ), $atts, 'rvl_team' );
	$members = get_posts( array(
		'post_type'      => 'rvl_team',
		'post_status'    => 'publish',
		'posts_per_page' => (int) $atts['limit'],
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'ASC' ),
	) );
	if ( ! $members ) { return ''; }

	$phone = '+923345298703';
	ob_start();
	echo '<div class="rvl-team" data-count="' . count( $members ) . '">';
	foreach ( $members as $m ) {
		$name     = get_the_title( $m );
		$position = get_post_meta( $m->ID, 'rvl_position', true );
		$focus    = get_post_meta( $m->ID, 'rvl_focus', true );
		$intro    = has_excerpt( $m ) ? get_the_excerpt( $m ) : '';
		echo '<article class="rvl-tm">';
		echo '<div class="rvl-tm-media">';
		if ( has_post_thumbnail( $m ) ) {
			echo get_the_post_thumbnail( $m, 'medium_large', array( 'class' => 'rvl-tm-photo', 'alt' => esc_attr( $name ), 'loading' => 'lazy' ) );
		} else {
			echo '<div class="rvl-tm-mono" aria-hidden="true"><span>' . esc_html( rvl_team_initials( $name ) ) . '</span></div>';
		}
		if ( $intro ) { echo '<div class="rvl-tm-intro"><p>' . esc_html( $intro ) . '</p></div>'; }
		echo '</div>';
		echo '<div class="rvl-tm-body">';
		if ( $position ) { echo '<p class="rvl-tm-role">' . esc_html( $position ) . '</p>'; }
		echo '<h3 class="rvl-tm-name">' . esc_html( $name ) . '</h3>';
		if ( $focus ) { echo '<p class="rvl-tm-focus">' . esc_html( $focus ) . '</p>'; }
		echo '<div class="rvl-tm-links">';
		echo '<a href="tel:' . esc_attr( $phone ) . '" aria-label="' . esc_attr( 'Call the chambers of ' . $name ) . '">Call chambers</a>';
		echo '<a href="' . esc_url( home_url( '/#contact' ) ) . '">Book a consultation</a>';
		echo '</div></div></article>';
	}
	echo '</div>';
	return ob_get_clean();
} );
