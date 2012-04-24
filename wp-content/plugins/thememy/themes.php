<?php

/**
 * Process theme deletion request
 *
 * @since ThemeMY! 0.1
 */
function thememy_delete_theme() {
	if ( ! is_singular() || get_post_type() != 'td_theme' || ! get_query_var( 'delete' ) )
		return;

	$theme_id = get_the_ID();

	if ( ! current_user_can( 'edit_post', $theme_id ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], "delete-theme-$theme_id" ) ) {
		wp_redirect( site_url( 'themes/' ) );
		exit;
	}

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

/**
 * Add delete and edit endpoints
 *
 * @since ThemeMY! 0.1
 */
function thememy_add_rewrite_endpoints() {
	add_rewrite_endpoint( 'delete', EP_PERMALINK );
	add_rewrite_endpoint( 'edit', EP_PERMALINK );
}
add_action( 'init', 'thememy_add_rewrite_endpoints' );

/**
 * Set delete and edit query vars
 *
 * @since ThemeMY! 0.1
 */
function thememy_theme_query_vars( $vars ) {
	if ( isset( $vars['delete'] ) )
		$vars['delete'] = true;

	if ( isset( $vars['edit'] ) )
		$vars['edit'] = true;

	return $vars;
}
add_filter( 'request', 'thememy_theme_query_vars' );

