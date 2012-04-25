<?php

/**
 * Process theme deletion request
 *
 * @since ThemeMY! 0.1
 */
function thememy_theme_delete() {
	if ( ! is_singular() || get_post_type() != 'td_theme' || ! get_query_var( 'delete' ) )
		return;

	$theme_id = get_the_ID();

	if ( ! current_user_can( 'edit_post', $theme_id ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], "delete-theme-$theme_id" ) ) {
		wp_redirect( get_permalink() );
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
add_action( 'template_redirect', 'thememy_theme_delete' );

/**
 * Display theme edit page
 *
 * @since ThemeMY! 0.1
 */
function thememy_theme_edit_template( $template ) {
	if ( get_post_type() != 'td_theme' || ! get_query_var( 'edit' ) || ! current_user_can( 'edit_post', get_the_ID() ) )
		return $template;

	return locate_template( 'theme-edit.php' );
}
add_filter( 'single_template', 'thememy_theme_edit_template' );

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

/**
 * Display theme upload error message
 *
 * @since ThemeMY! 0.1
 */
function thememy_upload_error_message() {
	if ( isset( $_GET['message'] ) ) {
		switch ( $_GET['message'] ) {
			case '2' :
				_e( 'File upload error' );
				break;

			case '3' :
				_e( 'Error extracting the archive content' );
				break;

			case '4' :
				_e( 'Error reading the theme data' );
				break;

			case '5' :
				_e( 'Error updating the theme data' );
				break;

			case '6' :
				_e( 'Error saving the file' );
				break;
		}
	}
}

