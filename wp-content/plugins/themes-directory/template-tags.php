<?php

/**
 * Display theme screenshot.
 *
 * @package Themes_Directory
 * @since 0.1
 */
function td_screenshot() {
	echo td_get_screenshot();
}

/**
 * Retrieve theme screenshot.
 *
 * @package Themes_Directory
 * @since 0.1
 *
 * @param int $theme_id Theme ID
 * @return string theme screenshot image tag
 */
function td_get_screenshot( $theme_id = null ) {
	return get_the_post_thumbnail( $theme_id, 'full' );
}

/**
 * Display theme current version number.
 *
 * @package Themes_Directory
 * @since 0.1
 */
function td_current_version() {
	echo td_get_current_version();
}

/**
 * Retrieve theme current version number.
 *
 * @package Themes_Directory
 * @since 0.1
 *
 * @param int $theme_id Theme ID
 * @return string Current version number for the theme
 */
function td_get_current_version( $theme_id = null ) {
	$theme_id = ( null === $theme_id ) ? get_the_ID() : $theme_id;
	return get_post_meta( $theme_id, '_td_current_version', true );
}

/**
 * Retrieve theme header data.
 *
 * @package Themes_Directory
 * @since 0.1
 *
 * @param int $theme_id Theme ID
 * @return array Theme data
 */
function td_get_theme_data( $theme_id = null ) {
	$theme_id = ( null === $theme_id ) ? get_the_ID() : $theme_id;
	return get_post_meta( $theme_id, '_td_theme_data', true );
}

/**
 * Display theme download link.
 *
 * @package Themes_Directory
 * @since 0.1
 */
function td_download_link() {
	echo td_get_download_link();
}

/**
 * Retrieve theme download link.
 *
 * @package Themes_Directory
 * @since 0.1
 *
 * @param int $theme_id Theme ID
 * @param string $version Version number for which the download link should be retrieved
 * @return string Download link for the specified version
 */
function td_get_download_link( $theme_id = null, $version = null ) {
	$theme_id = ( null === $theme_id ) ? get_the_ID() : $theme_id;
	$version = ( null === $version ) ? td_get_current_version( $theme_id ) : $version;

	$attachment = get_posts( array(
		'fields'      => 'ids',
		'meta_key'    => '_td_version',
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
 * @package Theme_Directory
 * @since 0.1
 */
function td_purchase_link() {
	echo td_get_purchase_link();
}

/**
 * Retrieve theme purchase link
 *
 * @package Theme_Directory
 * @since 0.1
 *
 * @param int $theme_id Theme ID
 * @return string Purchase link for the theme
 */
function td_get_purchase_link( $theme_id = null ) {
	$theme_id = ( null === $theme_id ) ? get_the_ID() : $theme_id;

	return add_query_arg( 'buy', $theme_id, site_url( '/' ) );
}

/**
 * Display link to purchase all themes at once
 *
 * @package Theme_Directory
 * @since 0.1
 */
function td_global_purchase_link() {
	echo td_get_global_purchase_link();
}

/**
 * Retrieve purchase link for all themes at once
 *
 * @package Theme_Directory
 * @since 0.1
 */
function td_get_global_purchase_link( $user_id = null ) {
	$user_id = ( null === $user_id ) ? get_current_user_id() : $user_id;

	return add_query_arg( 'buy_all', $user_id, site_url( '/' ) );
}

/**
 * Retrieve all the theme versions and download links.
 *
 * @package Themes_Directory
 * @since 0.1
 *
 * @param int $theme_id Theme ID
 * @return array Theme versions as keys and download links as values
 */
function td_get_all_versions( $theme_id = null ) {
	$versions = array();

	$theme_id = ( null === $theme_id ) ? get_the_ID() : $theme_id;

	$attachments = get_posts( array(
		'meta_key'    => '_td_version',
		'nopaging'    => true,
		'post_parent' => $theme_id,
		'post_status' => 'inherit',
		'post_type'   => 'attachment'
	)	);

	if ( $attachments ) {
		foreach ( $attachments as $attachment ) {
			$version = get_post_meta( $attachment->ID, '_td_version', true );

			$versions[$version] = get_post_meta( $attachment->ID, '_td_data', true );
		}
	}

	return $versions;
}

