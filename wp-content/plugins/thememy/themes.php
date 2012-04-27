<?php

/**
 * Register theme post type
 *
 * @since ThemeMY! 0.1
 */
function register_theme_post_type() {
	$labels = array(
		'name'               => _x( 'Themes', 'post type general name' ),
		'singular_name'      => _x( 'Theme', 'post type singular name' ),
		'add_new'            => _x( 'Add New', 'theme' ),
		'add_new_item'       => __( 'Add New Theme' ),
		'edit_item'          => __( 'Edit Theme' ),
		'new_item'           => __( 'New Theme' ),
		'all_items'          => __( 'All Themes' ),
		'view_item'          => __( 'View Theme' ),
		'search_items'       => __( 'Search Themes' ),
		'not_found'          => __( 'No themes found' ),
		'not_found_in_trash' => __( 'No themes found in Trash' ),
		'parent_item_colon'  => '',
		'menu_name'          => __( 'Themes' )
	);
	$args = array(
		'description'  => __( 'Themes directory pages' ),
		'labels'       => $labels,
		'public'       => true,
		'map_meta_cap' => true,
		'rewrite'      => array( 'slug' => 'theme' ),
		'supports'     => array(
			'author', 'comments', 'custom-fields',
			'editor', 'page-attributes', 'revisions'
		)
	);
	register_post_type( 'theme', $args );
}
add_action( 'init', 'register_theme_post_type' );

/**
 * Handle file upload
 *
 * @since ThemeMY! 0.1
 */
function thememy_theme_upload_handler() {
	global $plugin_page, $wp_filesystem;

	if ( ! is_page( 'themes' ) || empty( $_FILES['themezip'] ) )
		return;

	$referer = remove_query_arg( 'message', wp_get_referer() );

	if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'thememy-theme-upload' ) ) {
		wp_redirect( add_query_arg( 'message', '1', $referer ) );
		exit;
	}

	include( ABSPATH . 'wp-admin/includes/file.php' );

	// Only zip archives please
	add_filter( 'wp_check_filetype_and_ext', 'thememy_restrict_filetype' );
	$file = wp_handle_upload( $_FILES['themezip'], array( 'test_form' => false ) );
	remove_filter( 'wp_check_filetype_and_ext', 'thememy_restrict_filetype', 10 );

	if ( isset( $file['error'] ) ) {
		wp_redirect( add_query_arg( 'message', '2', $referer ) );
		exit;
	}

	// Extract archive in temporary directory to read style.css content
	$temp_dir = get_temp_dir() . 'thememy-' . microtime() . '/';

	WP_Filesystem();

	$unzip_result = unzip_file( $file['file'], $temp_dir );

	if ( is_wp_error( $unzip_result ) ) {
		wp_redirect( add_query_arg( 'message', '3', $referer ) );
		exit;
	}

	$dirs = array_keys( $wp_filesystem->dirlist( $temp_dir ) );

	// Find style.css and read theme data
	foreach ( $dirs as $dir ) {
		$dir_path = $temp_dir . $dir . '/';

		if ( ! $wp_filesystem->is_dir( $dir_path ) )
			continue;

		$style_path = "{$dir_path}style.css";

		if ( ! $wp_filesystem->is_file( $style_path ) )
			continue;

		$theme_data = get_theme_data( $style_path );

		$screenshot = false;
		foreach ( array( 'png', 'gif', 'jpg', 'jpeg' ) as $ext ) {
			$screenshot = "{$dir_path}screenshot.$ext";

			if ( file_exists( $screenshot ) )
				break;
		}
		break;
	}

	if ( empty( $theme_data ) ) {
		$wp_filesystem->rmdir( $temp_dir, true );
		wp_redirect( add_query_arg( 'message', '4', $referer ) );
		exit;
	}

	// Save theme and attachment
	if ( ! $theme_id = thememy_save_theme_data( $theme_data ) ) {
		$wp_filesystem->rmdir( $temp_dir, true );
		wp_redirect( add_query_arg( 'message', '5', $referer ) );
		exit;
	}

	if ( ! thememy_save_file( $theme_id, $theme_data, $file ) ) {
		$wp_filesystem->rmdir( $temp_dir, true );
		wp_redirect( add_query_arg( 'message', '6', $referer ) );
		exit;
	}

	thememy_save_screenshot( $theme_id, $screenshot );

	$wp_filesystem->rmdir( $temp_dir, true );

	wp_redirect( add_query_arg( 'success', 'true', thememy_get_edit_link( $theme_id ) ) );
	exit;
}
add_action( 'template_redirect', 'thememy_theme_upload_handler' );

/**
 * Update or create a new theme page
 *
 * @since ThemeMY! 0.1
 *
 * @param array $theme_data Theme data from style.css
 * @return int Theme page ID
 */
function thememy_save_theme_data( $theme_data, $author_id = null ) {
	global $wpdb;

	if ( ! $author_id )
		$author_id = get_current_user_id();

	$theme_id = $wpdb->get_var( $wpdb->prepare(
		"SELECT ID FROM $wpdb->posts WHERE post_author = %d AND post_type= 'theme' AND post_title = %s",
		$author_id,
		$theme_data['Name']
	) );

	if ( $theme_id && $page = get_post( $theme_id ) ) {
		$args = array(
			'ID'           => $page->ID,
			'post_excerpt' => $theme_data['Description']
		);
		$theme_id = wp_update_post( $args );
	} else {
		$args = array(
			'post_excerpt' => $theme_data['Description'],
			'post_status'  => 'publish',
			'post_title'   => $theme_data['Name'],
			'post_type'    => 'theme'
		);
		$theme_id = wp_insert_post( $args );
	}

	update_post_meta( $theme_id, '_theme_current_version', $theme_data['Version'] );
	update_post_meta( $theme_id, '_theme_data', $theme_data );

	return $theme_id;
}

/**
 * Add file as attachment to a theme page or update existing attachment
 *
 * @since ThemeMY! 0.1
 *
 * @param int $theme_id Theme ID
 * @param array $theme_data Theme data from style.css
 * @param string $file Theme file path
 * @return int Attachment ID
 */
function thememy_save_file( $theme_id, $theme_data, $file ) {
	$attachment = get_posts( array(
		'fields'      => 'ids',
		'meta_key'    => '_theme_version',
		'meta_value'  => $theme_data['Version'],
		'nopaging'    => true,
		'post_parent' => $theme_id,
		'post_status' => 'inherit',
		'post_type'   => 'attachment'
	)	);

	$attachment_id = $attachment ? $attachment[0] : 0;

	$args = array(
		'guid'           => $file['url'],
		'ID'             => $attachment_id,
		'post_mime_type' => $file['type'],
		'post_parent'    => $theme_id,
		'post_title'     => $theme_data['Version']
	);
	$attachment_id = wp_insert_attachment( $args, $file['file'], $theme_id );

	if ( is_wp_error( $attachment_id ) )
		return;

	include( ABSPATH . 'wp-admin/includes/image.php' );

	wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $file ) );

	update_post_meta( $attachment_id, '_theme_version', $theme_data['Version'] );
	update_post_meta( $attachment_id, '_theme_data', $theme_data );

	// Make file private on s3
	if ( $amazon = get_post_meta( $attachment_id, 'amazonS3_info', true ) ) {
		$s3_config = get_option( 'tantan_wordpress_s3' );
		$s3 = new TanTanS3( $s3_config['key'], $s3_config['secret'] );
		$s3->setObjectACL( $amazon['bucket'], $amazon['key'], 'authenticated-read' );

		update_post_meta( $attachment_id, '_s3_acl', 'authenticated-read' );
	}

	return $attachment_id;
}

/**
 * Add screenshot as featured image to a theme page and delete existing featured image
 *
 * @since ThemeMY! 0.1
 *
 * @param int $theme_id Theme ID
 * @param string $path Path to the featured image
 * @return int Image attachment ID
 */
function thememy_save_screenshot( $theme_id,  $path ) {
	$args = array(
		'name'     => wp_basename( $path ),
		'tmp_name' => $path
	);

	include( ABSPATH . 'wp-admin/includes/media.php' );

	$attachment_id = media_handle_sideload( $args, $theme_id, __( 'Theme Screenshot' ) );

	if ( is_wp_error( $attachment_id ) )
		return;

	$old_thumbnail_id = get_post_thumbnail_id( $theme_id );

	if ( ! set_post_thumbnail( $theme_id, $attachment_id ) )
		return;

	if ( $old_thumbnail_id )
		wp_delete_attachment( $old_thumbnail_id, true );

	return $attachment_id;
}

/**
 * Restrict file type to zip only
 *
 * @since ThemeMY! 0.1
 *
 * @param array $filetype File type and extension
 * @return array Original array for zip file ; array of empty values for the other types
 */
function thememy_restrict_filetype( $filetype ) {
	if ( 'application/zip' != $filetype['type'] ) {
		return array(
			'ext'             => false,
			'type'            => false,
			'proper_filename' => false
		);
	}

	return $filetype;
}

/**
 * Process theme deletion request
 *
 * @since ThemeMY! 0.1
 */
function thememy_theme_delete() {
	if ( ! is_singular() || get_post_type() != 'theme' || ! get_query_var( 'delete' ) )
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
	if ( get_post_type() != 'theme' || ! get_query_var( 'edit' ) || ! current_user_can( 'edit_post', get_the_ID() ) )
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
			case '1' :
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
 * Verify theme slug availability
 *
 * @since ThemeMY! 0.1
 */
function thememy_theme_slug_verify() {
	check_ajax_referer( 'thememy-edit-theme' );

	$data = stripslashes_deep( $_POST );
	$theme_id = $data['theme_id'];
	$theme_slug = $data['theme_slug'];

	if ( ! current_user_can( 'edit_post', $theme_id ) || empty( $theme_slug ) )
		exit( 'error' );

	if ( $theme_slug == wp_unique_post_slug( $theme_slug, $theme_id, 'publish', 'theme', 0 ) )
		exit( 'available' );

	exit( 'unavailable' );
}
add_action( 'wp_ajax_thememy-verify-theme-slug', 'thememy_theme_slug_verify' );

/**
 * Delete a theme image
 *
 * @since ThemeMY! 0.1
 */
function thememy_image_delete() {
	check_ajax_referer( 'thememy-edit-theme' );

	$data = stripslashes_deep( $_POST );
	$theme_id = $data['theme_id'];
	$image_id = $data['image_id'];

	if ( ! current_user_can( 'edit_post', $theme_id ) )
		exit( 'error' );

	$image = get_post( $image_id );

	if ( $image->post_parent != $theme_id )
		exit( 'error' );

	if ( ! wp_delete_attachment( $image_id, true ) )
		exit( 'error' );

	exit( 'success' );
}
add_action( 'wp_ajax_thememy-delete-image', 'thememy_image_delete' );

/**
 * Enqueue theme upload script
 *
 * @since ThemeMY! 0.1
 */
function thememy_enqueue_plupload() {
	if ( ! is_singular() || get_post_type() != 'theme' || ! get_query_var( 'edit' ) )
		return;

	wp_enqueue_script( 'plupload-all' );

	wp_enqueue_script( 'thememy_plupload', THEMEMY_PLUGIN_URL . 'js/thememy-plupload.js', array( 'jquery' ), 201204254 );

	$plupload_init = array(
		'runtimes' => 'html5,silverlight,flash,html4',
		'browse_button' => 'plupload-browse-button',
		'file_data_name' => 'async-upload',
		'multiple_queues' => true,
		'max_file_size' => '2mb',
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

/**
 * Save theme edit form
 *
 * @since ThemeMY! 0.1
 */
function thememy_theme_edit() {
	if ( ! is_singular() || get_post_type() != 'theme' || ! get_query_var( 'edit' ) || empty( $_POST ) )
		return;

	$data = stripslashes_deep( $_POST );
	$theme_id   = $data['theme-id'];
	$theme_slug = $data['theme-slug'];
	$theme_demo = $data['theme-demo'];

	if ( ! current_user_can( 'edit_post', $theme_id ) ) {
		wp_redirect( get_permalink( $theme_id ) );
		exit;
	}

	$referer = remove_query_arg( array( 'message', 'success' ), wp_get_referer() );

	if ( ! wp_verify_nonce( $_REQUEST['thememy_nonce'], 'edit-theme' ) ) {
		wp_redirect( add_query_arg( 'message', '1', $referer ) );
		exit;
	}

	$theme = get_post( $theme_id );

	update_post_meta( $theme->ID, '_theme_demo', $theme_demo );

	if ( $theme_slug ) {
		if ( $theme_slug != wp_unique_post_slug( $theme_slug, $theme_id, 'publish', 'theme', 0 ) ) {
			wp_redirect( add_query_arg( array(
				'message'    => '2',
				'theme_slug' => $theme_slug
			) , $referer ) );
			exit;
		}

		$theme->post_name = $theme_slug;

		if ( ! wp_update_post( $theme ) ) {
			wp_redirect( add_query_arg( 'message', '1', $referer ) );
			exit;
		}
	}

	wp_redirect( add_query_arg( 'success', 'true', $referer ) );
	exit;
}
add_action( 'template_redirect', 'thememy_theme_edit');

