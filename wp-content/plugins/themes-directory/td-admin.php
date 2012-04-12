<?php
/**
 * Admin class
 *
 * @package Themes_Directory
 * @since 0.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

if ( ! class_exists( 'TD_Admin' ) ) :
class TD_Admin {

	/**
	 * Admin loader
	 *
	 * @package Themes_Directory
	 * @since 0.1
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'add_menu_page' ) );
		add_action( 'admin_init', array( __CLASS__, 'upload_handler' ) );

		// Hacky way to add the theme name to the theme edit screen by closing the form tag early
		add_action( 'post_edit_form_tag', array( __CLASS__, 'theme_name_tag' ) );
	}

	/**
	 * Add admin page
	 *
	 * @package Themes_Directory
	 * @since 0.1
	 */
	public static function add_menu_page() {
		add_menu_page( __( 'Themes Directory' ), __( 'Themes Directory' ), 'edit_posts', 'td-admin', array( __CLASS__, 'admin_page' ), null, 25 );
	}

	/**
	 * Add theme title to the post edit screen
	 *
	 * @package Themes_Directory
	 * @since 0.1
	 */
	public static function theme_name_tag() {
		global $post_ID;

		if ( 'td_theme' != get_current_screen()->id )
			return;

		$post = get_post( $post_ID );

		// Close form tag
		echo '><div>';

		echo '<h3>' . get_the_title( $post_ID ) . '</h3>';
		echo apply_filters( 'get_the_excerpt', $post->post_excerpt );

		// Hidden input so that the title and excerpt are not overwritten by empty values on autosave
		echo '<input type="hidden" name="post_title" value="' . format_to_edit( $post->post_title ) . '" id="title" />';
		echo '<textarea class="hidden" name="excerpt" id="excerpt">' . format_to_edit( $post->post_excerpt ) . '</textarea>';

		// Leave the last sign so that the WordPress one is used
		echo '</div';
	}

	/**
	 * Display admin page content
	 *
	 * @package Themes_Directory
	 * @since 0.1
	 */
	public static function admin_page() {
		if ( ! current_user_can( 'edit_posts' ) )
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );

		if ( isset( $_GET['message'] ) ) {
			$message = $_GET['message'];

			if ( '1' == $message )
				$message = __( 'Theme uploaded' );

			elseif ( '2' == $message )
				$message = __( 'File upload error' );

			elseif ( '3' == $message )
				$message = __( 'Error extracting the archive content' );

			elseif ( '4' == $message )
				$message = __( 'Error reading the theme data' );

			elseif ( '5' == $message )
				$message = __( 'Error updating the theme data' );

			elseif ( '6' == $message )
				$message = __( 'Error saving the file' );

			if ( isset( $message ) )
				echo "<div id='message' class='updated'><p><strong>$message</strong></p></div>";
		}
?>
<div class="wrap">
	<h2><?php _e( 'Themes Directory' ); ?></h2>
	<?php self::upload_form(); ?>
	<br class="clear" />
	<?php self::themes_list(); ?>
</div>
<?php
	}

	/**
	 * Display upload form
	 *
	 * @todo: Create functions to actually handle the upload via both html uploader and pupload.
	 *
	 * @package Themes_Directory
	 * @since 0.1
	 */
	public static function upload_form() {
		global $is_iphone;

		if ( $is_iphone )
			return;
?>
<h3><?php _e( 'Upload a theme in .zip format' ); ?></h3>
<p><?php _e( 'If the file matches an existing theme, it will be added as a new version of that theme. If not, a new theme will be created.' ); ?></p>
<form enctype="multipart/form-data" method="post" action="">
	<?php wp_nonce_field( 'td-theme-upload' ); ?>
	<label class="screen-reader-text" for="themezip"><?php _e( 'Theme zip file' ); ?></label>
	<input type="file" id="td-themezip" name="themezip" />
	<input type="submit" class="button" value="<?php esc_attr_e( 'Upload Now' ); ?>" />
</form>
<?php
	}

	/**
	 * Handle file upload
	 *
	 * @package Themes_Directory
	 * @since 0.1
	 */
	public static function upload_handler() {
		global $plugin_page, $wp_filesystem;

		if ( 'td-admin' != $plugin_page || empty( $_FILES['themezip'] ) )
			return;

		check_admin_referer( 'td-theme-upload' );

		$referer = wp_get_referer();

		// Only zip archives please
		add_filter( 'wp_check_filetype_and_ext', array( __CLASS__, 'restrict_filetype' ) );
		$file = wp_handle_upload( $_FILES['themezip'], array( 'test_form' => false ) );
		remove_filter( 'wp_check_filetype_and_ext', array( __CLASS__, 'restrict_filetype' ), 10 );

		if ( isset( $file['error'] ) ) {
			wp_redirect( add_query_arg( 'message', '2', $referer ) );
			exit;
		}

		// Extract archive in temporary directory to read style.css content
		$temp_dir = get_temp_dir() . 'td-' . time() . '/';

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
		if ( ! $theme_id = self::save_theme_data( $theme_data ) ) {
			$wp_filesystem->rmdir( $temp_dir, true );
			wp_redirect( add_query_arg( 'message', '5', $referer ) );
			exit;
		}

		if ( ! self::save_file( $theme_id, $theme_data, $file ) ) {
			$wp_filesystem->rmdir( $temp_dir, true );
			wp_redirect( add_query_arg( 'message', '6', $referer ) );
			exit;
		}

		self::save_screenshot( $theme_id, $screenshot );

		$wp_filesystem->rmdir( $temp_dir, true );

		wp_redirect( add_query_arg( 'message', '1', $referer ) );
		exit;
	}

	/**
	 * Update or create a new theme page
	 *
	 * @package Themes_Directory
	 * @since 0.1
	 *
	 * @param array $theme_data Theme data from style.css
	 * @return int Theme page ID
	 */
	public static function save_theme_data( $theme_data, $author_id = null ) {
		global $wpdb;

		if ( ! $author_id )
			$author_id = get_current_user_id();

		$theme_id = $wpdb->get_var( $wpdb->prepare(
			"SELECT ID FROM $wpdb->posts WHERE post_author = %d AND post_type= 'td_theme' AND post_title = %s",
			$author_id,
			$theme_data['Name']
		) );

		if ( $page = get_page( $theme_id ) ) {
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
				'post_type'    => 'td_theme'
			);
			$theme_id = wp_insert_post( $args );
		}

		update_post_meta( $theme_id, '_td_current_version', $theme_data['Version'] );
		update_post_meta( $theme_id, '_td_theme_data', $theme_data );

		return $theme_id;
	}

	/**
	 * Add file as attachment to a theme page or update existing attachment
	 *
	 * @package Themes_Directory
	 * @since 0.1
	 *
	 * @param int $theme_id Theme ID
	 * @param array $theme_data Theme data from style.css
	 * @param string $file Theme file path
	 * @return int Attachment ID
	 */
	public static function save_file( $theme_id, $theme_data, $file ) {
		$attachment = get_posts( array(
			'fields'      => 'ids',
			'meta_key'    => '_td_version',
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

		wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $file ) );

		update_post_meta( $attachment_id, '_td_version', $theme_data['Version'] );
		update_post_meta( $attachment_id, '_td_data', $theme_data );

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
	 * @package Themes_Directory
	 * @since 0.1
	 *
	 * @param int $theme_id Theme ID
	 * @param string $path Path to the featured image
	 * @return int Image attachment ID
	 */
	public static function save_screenshot( $theme_id,  $path ) {
		$args = array(
			'name'     => wp_basename( $path ),
			'tmp_name' => $path
		);

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
	 * @package Themes_Directory
	 * @since 0.1
	 *
	 * @param array $filetype File type and extension
	 * @return array Original array for zip file ; array of empty values for the other types
	 */
	public static function restrict_filetype( $filetype ) {
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
	 * List uploaded themes
	 *
	 * @package Themes_Directory
	 * @since 0.1
	 */
	public static function themes_list() {
		global $themes_directory;

		include( $themes_directory->plugin_dir . 'class-td-themes-list-table.php' );

		$list_table = new TD_Themes_List_Table();
		$list_table->prepare_items();
?>
<form id="td-themes-filter" method="get" action="<?php echo admin_url( 'admin.php' ); ?>">
	<input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>"/>
	<h3><?php _e( 'All Themes' ); ?></h3>
	<?php $list_table->views(); ?>
	<?php $list_table->display(); ?>
</form>
<?php
	}

}

TD_Admin::init();
endif;

