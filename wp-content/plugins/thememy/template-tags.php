<?php

/**
 * Display theme screenshot.
 *
 * @since ThemeMY! 0.1
 */
function thememy_screenshot() {
	echo thememy_get_screenshot();
}

/**
 * Retrieve theme screenshot.
 *
 * @since ThemeMY! 0.1
 *
 * @param int $theme_id Theme ID
 * @return string theme screenshot image tag
 */
function thememy_get_screenshot( $theme_id = null ) {
	return get_the_post_thumbnail( $theme_id, 'post-thumbnail' );
}

/**
 * Display theme current version number.
 *
 * @since ThemeMY! 0.1
 */
function thememy_current_version() {
	echo thememy_get_current_version();
}

/**
 * Retrieve theme current version number.
 *
 * @since ThemeMY! 0.1
 *
 * @param int $theme_id Theme ID
 * @return string Current version number for the theme
 */
function thememy_get_current_version( $theme_id = null ) {
	$theme_id = ( null === $theme_id ) ? get_the_ID() : $theme_id;
	return get_post_meta( $theme_id, '_theme_current_version', true );
}

/**
 * Retrieve theme header data.
 *
 * @since ThemeMY! 0.1
 *
 * @param int $theme_id Theme ID
 * @return array Theme data
 */
function thememy_get_theme_data( $theme_id = null ) {
	$theme_id = ( null === $theme_id ) ? get_the_ID() : $theme_id;
	return get_post_meta( $theme_id, '_theme_data', true );
}

/**
 * Display theme download link.
 *
 * @since ThemeMY! 0.1
 */
function thememy_download_link() {
	echo thememy_get_download_link();
}

/**
 * Retrieve theme download link.
 *
 * @since ThemeMY! 0.1
 *
 * @param int $theme_id Theme ID
 * @param string $version Version number for which the download link should be retrieved
 * @return string Download link for the specified version
 */
function thememy_get_download_link( $theme_id = null, $version = null ) {
	$theme_id = ( null === $theme_id ) ? get_the_ID() : $theme_id;
	$version = ( null === $version ) ? thememy_get_current_version( $theme_id ) : $version;

	$attachment = get_posts( array(
		'fields'      => 'ids',
		'meta_key'    => '_theme_version',
		'meta_value'  => $version,
		'nopaging'    => true,
		'post_parent' => $theme_id,
		'post_status' => 'inherit',
		'post_type'   => 'attachment'
	)	);

	if ( $attachment )
		return wp_get_attachment_url( $attachment[0] );
}

/**
 * Display theme purchase link
 *
 * @since ThemeMY! 0.1
 */
function thememy_purchase_link() {
	echo thememy_get_purchase_link();
}

/**
 * Retrieve theme purchase link
 *
 * @since ThemeMY! 0.1
 *
 * @param int $theme_id Theme ID
 * @return string Purchase link for the theme
 */
function thememy_get_purchase_link( $theme_id = null ) {
	$theme_id = ( null === $theme_id ) ? get_the_ID() : $theme_id;

	return trailingslashit( get_permalink( $theme_id ) ) . 'buy/';
}

/**
 * Display link to purchase all themes at once
 *
 * @since ThemeMY! 0.1
 */
function thememy_global_purchase_link() {
	echo thememy_get_global_purchase_link();
}

/**
 * Retrieve purchase link for all themes at once
 *
 * @since ThemeMY! 0.1
 */
function thememy_get_global_purchase_link( $user_id = null ) {
	$user_id = ( null === $user_id ) ? get_current_user_id() : $user_id;

	return add_query_arg( 'buy_all', $user_id, site_url( '/' ) );
}

/**
 * Retrieve all the theme versions and download links.
 *
 * @since ThemeMY! 0.1
 *
 * @param int $theme_id Theme ID
 * @return array Theme versions as keys and download links as values
 */
function thememy_get_all_versions( $theme_id = null ) {
	$versions = array();

	$theme_id = ( null === $theme_id ) ? get_the_ID() : $theme_id;

	$attachments = get_posts( array(
		'meta_key'    => '_theme_version',
		'nopaging'    => true,
		'post_parent' => $theme_id,
		'post_status' => 'inherit',
		'post_type'   => 'attachment'
	)	);

	if ( $attachments ) {
		foreach ( $attachments as $attachment ) {
			$version = get_post_meta( $attachment->ID, '_theme_version', true );

			$versions[$version] = get_post_meta( $attachment->ID, '_theme_data', true );
		}
	}

	return $versions;
}

/**
 * Display theme delete link
 *
 * @since ThemeMY! 0.1
 */
function thememy_delete_link() {
	echo thememy_get_delete_link();
}

/**
 * Return delete link for one theme
 *
 * @since ThemeMY! 0.1
 *
 * @param int $theme_id Theme ID
 * @return string Theme delete link
 */
function thememy_get_delete_link( $theme_id = null ) {
	$theme_id = ( null === $theme_id ) ? get_the_ID() : $theme_id;

	$link = trailingslashit( get_permalink( $theme_id ) ) . 'delete/';

	return wp_nonce_url( $link, "delete-theme-$theme_id" );
}

/**
 * Display theme edit link
 *
 * @since ThemeMY! 0.1
 */
function thememy_edit_link() {
	echo thememy_get_edit_link();
}

/**
 * Return edit link for one theme
 *
 * @since ThemeMY! 0.1
 *
 * @param int $theme_id Theme ID
 * @return string Theme edit link
 */
function thememy_get_edit_link( $theme_id = null ) {
	$theme_id = ( null === $theme_id ) ? get_the_ID() : $theme_id;

	return trailingslashit( get_permalink( $theme_id ) ) . 'edit/';
}

/**
 * Display theme demo link
 *
 * @since ThemeMY! 0.1
 */
function thememy_demo_link() {
	echo thememy_get_demo_link();
}

/**
 * Return demo link for one theme
 *
 * @since ThemeMY! 0.1
 *
 * @param int $theme_id Theme ID
 * @return string Theme demo link
 */
function thememy_get_demo_link( $theme_id = null ) {
	$theme_id = ( null === $theme_id ) ? get_the_ID() : $theme_id;

	return get_post_meta( $theme_id, '_theme_demo', true );
}

