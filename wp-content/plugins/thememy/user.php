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

/**
 * Create a new user account
 *
 * @since ThemeMY! 0.1
 */
function thememy_user_signup() {
	if ( ! is_page_template( 'signup-page.php' ) || empty( $_POST ) )
		return;

	$data = stripslashes_deep( $_POST );

	$first_name  = isset( $_POST['first_name'] ) ? $_POST['first_name'] : '';
	$last_name   = isset( $_POST['last_name'] ) ? $_POST['last_name'] : '';
	$user_email  = isset( $_POST['user_email'] ) ? $_POST['user_email'] : '';
	$user_pass   = isset( $_POST['user_pass'] ) ? $_POST['user_pass'] : '';
	$user_pass_2 = isset( $_POST['user_pass_2'] ) ? $_POST['user_pass_2'] : '';

	$redirect_to = add_query_arg( array(
		'first_name' => $first_name,
		'last_name' => $last_name,
		'user_email' => $user_email
	) );

	if ( ! $first_name || ! $last_name || ! $user_email || ! $user_pass || ! $user_pass_2 ) {
		wp_redirect( add_query_arg( 'message', '1', $redirect_to ) );
		exit;
	}

	if ( ! is_email( $user_email ) ) {
		wp_redirect( add_query_arg( 'message', '2', remove_query_arg( 'user_email', $redirect_to ) ) );
		exit;
	}

	if ( $user_pass != $user_pass_2 ) {
		wp_redirect( add_query_arg( 'message', '3', $redirect_to ) );
		exit;
	}

	$user_login = wp_hash( $user_email );
	$display_name = "$first_name $last_name";
	$role = 'author';

	$args = compact( 'first_name', 'last_name', 'user_email', 'user_pass', 'user_login', 'display_name', 'role' );

	$user_id = wp_insert_user( $args );

	if ( is_wp_error( $user_id ) )
		thememy_error( $user_id );

	wp_redirect( add_query_arg( 'email', urlencode( $user_email ), site_url( "survey/" ) ) );
	exit;
}
add_action( 'template_redirect', 'thememy_user_signup' );

/**
 * Use email to authenticate users
 *
 * @since ThemeMY! 0.1
 */
function thememy_authenticate( $user, $username, $password ) {
	if ( ! empty( $username ) )
		$user = get_user_by( 'email', $username );
	if ( isset( $user->user_login, $user ) )
		$username = $user->user_login;

	return wp_authenticate_username_password( null, $username, $password );
}
remove_filter( 'authenticate', 'wp_authenticate_username_password', 20, 3 );
add_filter( 'authenticate', 'thememy_authenticate', 20, 3 );

/**
 * Don't show front page to logged-in users
 * Show front page only to non logged-in users
 *
 * @since ThemeMY! 0.1
 */
function thememy_restrict_pages() {
	if ( is_user_logged_in() && current_user_can( 'edit_posts' ) ) {
		if ( ( is_front_page() && empty( $_GET['buy'] ) && empty( $_GET['buy-all'] ) )
	 		|| is_page_template( 'signup-page.php' )	) {
			wp_redirect( site_url( 'themes/' ) );
			exit;
		}

	} else {
		if ( ! is_front_page() && ! is_page_template( 'store-page.php' ) && ! is_page_template( 'survey-page.php' )
			&& ! is_page_template( 'download-page.php' ) && ! is_page_template( 'signup-page.php' )
			&& ! is_page( 'api' ) && ! is_page( 'ipn' ) ) {
			wp_redirect( home_url( '/' ) );
			exit;
		}
	}
}
add_action( 'template_redirect', 'thememy_restrict_pages' );

/**
 * Restrict admin pages from authors, contributors and subscribers
 *
 * @since ThemeMY! 0.1
 */
function thememy_restrict_admin() {
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX )
		return;

	if ( current_user_can( 'edit_others_posts' ) )
		return;

	global $plugin_page;

	if ( 'td-admin' != $plugin_page || empty( $_FILES['themezip'] ) ) {
		wp_redirect( site_url( 'themes/' ) );
		exit;
	}
}
add_action( 'admin_init', 'thememy_restrict_admin' );
