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
	add_rewrite_endpoint( 'buy', EP_PERMALINK );
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
	if ( isset( $vars['buy'] ) )
		$vars['buy'] = true;

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

/**
 * Assign theme to a buyer profile
 *
 * @since ThemeMY! 0.1
 *
 * @param string $email Buyer email
 * @param int $theme_id Theme ID
 */
function thememy_assign_theme( $email, $theme_id ) {
	$buyer_id = get_user_by( 'email', $email )->ID;

	if ( ! $buyer_id )
		$buyer_id = wp_create_user( wp_hash( $email ), wp_generate_password(), $email );

	$themes = thememy_get_themes( $buyer_id );

	if ( ! in_array( $theme_id, $themes ) )
		add_user_meta( $buyer_id, '_thememy_themes', $theme_id );
}

/**
 * Get all the themes assigned to a buyer
 *
 * @since ThemeMY! 0.1
 *
 * @param int $buyer Buyer email or ID
 */
function thememy_get_themes( $buyer ) {
	if ( is_int( $buyer ) )
		$buyer_id = $buyer;
	elseif ( is_email( $buyer ) )
		$buyer_id = get_user_by( 'email', $buyer )->ID;
	else
		return;

	return get_user_meta( $buyer_id, '_thememy_themes' );
}

/**
 * Serve private files for download from S3
 *
 * @since ThemeMY! 0.1
 */
function thememy_get_attachment_url( $url, $post_id ) {
	if ( get_post_meta( $post_id, '_s3_acl', true ) != 'authenticated-read' )
		return $url;

	require_once( WP_PLUGIN_DIR . '/tantan-s3/wordpress-s3/lib.s3.php' );

	$s3_config = get_option('tantan_wordpress_s3');

	if ( $s3_config['wp-uploads'] && ( $amazon = get_post_meta( $post_id, 'amazonS3_info', true ) ) ) {
		$domain = ! empty( $s3_config['virtual-host'] ) ? $amazon['bucket'] : "{$amazon['bucket']}.s3.amazonaws.com";

		$s3 = new TanTanS3( $s3_config['key'], $s3_config['secret'] );

		$expires = strtotime( '+1 day' );
		$string_to_sign = "GET\n\n\n$expires\n/{$amazon['bucket']}/{$amazon['key']}";
		$signature = $s3->constructSig( $string_to_sign );

		$url = add_query_arg( array(
			'AWSAccessKeyId' => $s3_config['key'],
			'Expires'        => $expires,
			'Signature'      => urlencode( $signature )
		), "http://{$domain}/{$amazon['key']}" );

		return $url;
	}

	return $url;
}
add_action( 'wp_get_attachment_url', 'thememy_get_attachment_url', 10, 2 );

/**
 * Don't allow direct access to upload directory
 *
 * @since ThemeMY! 0.1
 */
function thememy_mod_rewrite_rules( $rules ) {
	$rules = str_replace(
		"\nRewriteRule ^index\.php$ - [L]\n",
		"\nRewriteRule ^wp-content/uploads/ - [R=404,L,NC]\nRewriteRule ^index\.php$ - [L]\n",
		$rules
	);

	return $rules;
}
add_action( 'mod_rewrite_rules', 'thememy_mod_rewrite_rules' );

/**
 * Save theme slug
 *
 * @since ThemeMY! 0.1
 */
function thememy_theme_slug_save() {
	check_ajax_referer( 'thememy-save-theme-slug' );

	$data = stripslashes_deep( $_POST );
	$theme_id = $data['theme_id'];
	$theme_slug = $data['theme_slug'];

	if ( $theme_slug != wp_unique_post_slug( $theme_slug, $theme_id, 'publish', 'td_theme', 0 ) )
		exit('exists');

	$theme = get_post( $theme_id );
	$theme->post_name = $theme_slug;

	if ( ! wp_update_post( $theme ) )
		exit('error');

	exit('complete');
}
add_action( 'wp_ajax_thememy-save-theme-slug', 'thememy_theme_slug_save' );

/**
 * Enqueue theme upload script
 *
 * @since ThemeMY! 0.1
 */
function thememy_enqueue_plupload() {
	if ( ! is_singular() || get_post_type() != 'td_theme' || ! get_query_var( 'edit' ) )
		return;

	wp_enqueue_script( 'plupload-all' );

	wp_enqueue_script( 'thememy_plupload', THEMEMY_PLUGIN_URL . 'js/thememy-plupload.js', array( 'jquery' ), 201204254 );

	$plupload_init = array(
		'runtimes' => 'html5,silverlight,flash,html4',
		'browse_button' => 'plupload-browse-button',
		'file_data_name' => 'async-upload',
		'multiple_queues' => true,
		'max_file_size' => '1mb',
		'url' => admin_url( 'admin-ajax.php' ),
		'flash_swf_url' => includes_url( 'js/plupload/plupload.flash.swf' ),
		'silverlight_xap_url' => includes_url( 'js/plupload/plupload.silverlight.xap' ),
		'filters' => array(
			array(
				'title' => __( 'Allowed Files' ),
				'extensions' => 'gif,jpg,jpeg,png'
			)
		),
		'multipart' => true,
		'urlstream_upload' => true,
		'multipart_params' => array(
			'_ajax_nonce' => wp_create_nonce( 'theme-image-upload' ),
			'action' => 'theme_image_upload',
			'theme_id' => get_the_ID()
		)
	);
	wp_localize_script( 'thememy_plupload', 'thememyPlupload', $plupload_init );
}
add_action( 'wp_enqueue_scripts', 'thememy_enqueue_plupload' );

/**
 * Handle theme image upload
 *
 * @since ThemeMY! 0.1
 */
function thememy_image_upload() {
	check_ajax_referer( 'theme-image-upload' );

	$image_id = media_handle_upload( 'async-upload', $_POST['theme_id'], array(), array( 'test_form' => false, 'action' => 'theme_image_upload' ) );

	if ( ! is_wp_error( $image_id ) )
		echo wp_get_attachment_image( $image_id, 'post-thumbnail' );
	exit;
}
add_action('wp_ajax_theme_image_upload', "thememy_image_upload");

