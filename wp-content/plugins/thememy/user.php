<?php

/**
 * Add Getting Started modal
 *
 * @since ThemeMY! 0.1
 */
function thememy_getting_started() {
	if ( ! is_user_logged_in() || ! empty( $_GET ) || get_user_option( 'thememy_show_getting_started', get_current_user_id() ) == 'no' )
		return;

	get_template_part( 'getting-started' );
}
add_action( 'wp_footer', 'thememy_getting_started', 20 );

/**
 * Save option to never show getting started modal again
 *
 * @since ThemeMY! 0.1
 */
function thememy_no_getting_started() {
	check_ajax_referer( 'thememy-no-getting-started' );

	update_user_option( get_current_user_id(), 'thememy_show_getting_started', 'no' );

	exit;
}
add_action( 'wp_ajax_thememy-no-getting-started', 'thememy_no_getting_started' );

/**
 * Hide admin bar from authors and contributors
 *
 * @since ThemeMY! 0.1
 */
function thememy_admin_bar( $show ) {
	global $user_switching;

	if ( ! current_user_can( 'edit_others_posts' ) && ! $user_switching->get_old_user() )
		$show = false;

	return $show;
}
add_filter( 'show_admin_bar', 'thememy_admin_bar' );

