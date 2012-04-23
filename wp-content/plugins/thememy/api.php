<?php

/**
 * Get theme details in an array for API usage
 *
 * @since ThemeMY! 0.1
 */
function thememy_api_theme_details( $theme_id ) {
	$theme = get_post( $theme_id );

	if ( ! $theme )
		return;

	$theme_data = td_get_theme_data( $theme_id );

	$versions = td_get_all_versions( $theme_id );
	foreach ( $versions as $version => $version_data ) {
		$versions[$version] = array(
			'version'  => $version,
			'ThemeURI' => $version_data['URI']
		);
	}

	$details = array(
		'package'     => td_get_download_link( $theme_id ),
		'new_version' => td_get_current_version( $theme_id ),
		'url'         => $theme_data['URI'],
		'versions'    => $versions
	);

	return $details;
}

/**
 * Process API requests
 *
 * @since ThemeMY! 0.1
 */
function thememy_process_api_requests() {
	if ( ! is_page( 'api' ) || empty( $_REQUEST['action'] ) )
		return;

	$request = stripslashes_deep( $_REQUEST );

	if ( 'theme_update' != $request['action'] || wp_hash( $request['email'] ) != $request['api_key'] )
		exit;

	// get details of themes purchased by the user
	$themes = thememy_get_themes( $request['email'] );
	if ( ! $themes )
		exit;

	$themes = get_posts( array( 'post__in' => $themes, 'post_type' => 'td_theme' ) );
	if ( ! $themes )
		exit;

	foreach ( $themes as $theme ) {
		$available[$theme->post_title] = thememy_api_theme_details( $theme->ID );
	}

	// loop through the user's installed themes and match themes names and URIs
	$installed = json_decode( $request['themes'], true );
	foreach ( $installed as $slug => $theme ) {
		$theme_name = $theme['Name'];
		if ( ! isset( $available[$theme_name] ) )
			continue;

		$match = $available[$theme_name];
		$theme_version = $theme['Version'];

		if ( ! isset( $match['versions'][$theme_version] ) || $match['versions'][$theme_version]['ThemeURI'] != $theme['ThemeURI'] )
			continue;

		if ( $theme_version >= $match['new_version'] )
			continue;

		$new[$slug] = array(
			'package'     => $match['package'],
			'new_version' => $match['new_version'],
			'url'         => $match['url']
		);
	}

	if ( $new )
		echo json_encode( $new );	

	exit;
}
add_action( 'template_redirect', 'thememy_process_api_requests' );

