<?php

function thememy_delete_theme() {
	if ( ! is_post_type_archive( 'td_theme' ) || empty( $_GET['delete'] ) )
		return;

	$theme_id = $_GET['delete'];

	$args = array(
		'post_type'   => 'attachment',
		'nopaging'    => true,
		'post_status' => 'any',
		'post_parent' => $theme_id,
		'fields'      => 'ids'
	);
	$attachment_ids = get_posts( $args );

	if ( $attachment_ids ) {
		foreach ( $attachment_ids as $attachment_id ) {
			wp_delete_post( $attachment_id, true );
		}
	}

	wp_delete_post( $theme_id, true );

	wp_redirect( add_query_arg( 'deleted', $theme_id, site_url( 'themes/' ) ) );
	exit;
}
add_action( 'template_redirect', 'thememy_delete_theme' );
