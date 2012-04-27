<?php
/**
 * ThemeMY! functions and definitions
 *
 * @package thememy
 * @since ThemeMY! 0.1
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 *
 * @since ThemeMY! 0.1
 */
if ( ! isset( $content_width ) )
	$content_width = 640; /* pixels */

if ( ! function_exists( 'thememy_setup' ) ):
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 *
 * @since ThemeMY! 0.1
 */
function thememy_setup() {

	/**
	 * Custom template tags for this theme.
	 */
	require( get_template_directory() . '/inc/template-tags.php' );

	/**
	 * Custom functions that act independently of the theme templates
	 */
	//require( get_template_directory() . '/inc/tweaks.php' );

	/**
	 * Custom Theme Options
	 */
	//require( get_template_directory() . '/inc/theme-options/theme-options.php' );

	/**
	 * WordPress.com-specific functions and definitions
	 */
	//require( get_template_directory() . '/inc/wpcom.php' );

	/**
	 * Make theme available for translation
	 * Translations can be filed in the /languages/ directory
	 * If you're building a theme based on ThemeMY!, use a find and replace
	 * to change 'thememy' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'thememy', get_template_directory() . '/languages' );

	$locale = get_locale();
	$locale_file = get_template_directory() . "/languages/$locale.php";
	if ( is_readable( $locale_file ) )
		require_once( $locale_file );

	/**
	 * Add default posts and comments RSS feed links to head
	 */
	add_theme_support( 'automatic-feed-links' );

	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 300, 225 );

	add_image_size( 'span6-span6', 570, 570, true );

	/**
	 * This theme uses wp_nav_menu() in one location.
	 */
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'thememy' ),
	) );

	/**
	 * Add support for the Aside and Gallery Post Formats
	 */
	add_theme_support( 'post-formats', array( 'aside', ) );
}
endif; // thememy_setup
add_action( 'after_setup_theme', 'thememy_setup' );

/**
 * Register widgetized area and update sidebar with default widgets
 *
 * @since ThemeMY! 0.1
 */
function thememy_widgets_init() {
	register_sidebar( array(
		'name' => __( 'Sidebar', 'thememy' ),
		'id' => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h1 class="widget-title">',
		'after_title' => '</h1>',
	) );
}
add_action( 'widgets_init', 'thememy_widgets_init' );

/**
 * Enqueue scripts and styles
 *
 * @since ThemeMY! 0.1
 */
function thememy_scripts() {
	if ( is_admin() )
		return;

	global $post;

	$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;

	wp_enqueue_script( 'bootstrap-button' );
	wp_enqueue_script( 'bootstrap-collapse' );

	if ( is_page_template( 'settings.php' ) ) {
		wp_enqueue_script( 'bootstrap-modal' );
		wp_enqueue_script( 'bootstrap-tab' );
	}

	if ( get_post_type() == 'theme' ) {
		wp_enqueue_script( 'bootstrap-carousel' );
		wp_enqueue_script( 'bootstrap-transition' );
	}

	wp_enqueue_style( 'style', get_stylesheet_uri(), false, 201204242 );

	wp_enqueue_script( 'script', get_template_directory_uri() . '/script.js', 'jquery', 201204271, true );
	wp_localize_script( 'script', 'thememy', array(
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		'nonce'   => wp_create_nonce( 'thememy-edit-theme' )
	) );

	if ( is_page_template( 'reports.php' ) )
		wp_enqueue_script( 'google-jsapi', 'https://www.google.com/jsapi' );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );
}
add_action( 'wp_enqueue_scripts', 'thememy_scripts' );

/**
 * Set less scripts rel tag
 *
 * @since ThemeMY! 0.1
 */
function thememy_less_loader_tag( $tag, $handle ) {
	if ( ! defined( 'SCRIPT_DEBUG' ) || ! SCRIPT_DEBUG )
		return $tag;

	global $wp_styles;

	if ( ! preg_match( '/\.less$/U', $wp_styles->registered[$handle]->src ) )
		return $tag;

	$handle = $wp_styles->registered[$handle]->handle;
	$media = $wp_styles->registered[$handle]->args;
	$href = $wp_styles->registered[$handle]->src;
	if ( $wp_styles->registered[$handle]->ver )
		$href .= '?ver=' . $wp_styles->registered[$handle]->ver;
	$rel = isset($wp_styles->registered[$handle]->extra['alt']) && $wp_styles->registered[$handle]->extra['alt'] ? 'alternate stylesheet/less' : 'stylesheet/less';
	$title = isset($wp_styles->registered[$handle]->extra['title']) ? "title='" . esc_attr( $wp_styles->registered[$handle]->extra['title'] ) . "'" : '';

	return "<link rel='$rel' id='$handle-less' $title href='$href' type='text/css' media='$media' />";
}
add_filter( 'style_loader_tag', 'thememy_less_loader_tag', 10, 2 );

/**
 * Implement the Custom Header feature
 */
//require( get_template_directory() . '/inc/custom-header.php' );

/**
 * Send feedback to site admin
 *
 * @since ThemeMY! 0.1
 */
function thememy_send_feedback() {
	if ( ! is_page( 'feedback' ) || ! isset( $_POST['message'] ) )
		return;

	if ( empty( $_POST['message'] ) ) {
		wp_redirect( site_url( 'feedback/?message=2' ) );
		exit;
	}

	$current_user = wp_get_current_user();

	$to = get_option( 'admin_email' );
	$subject = sprintf( __( 'Feedback from %s' ), $current_user->display_name );
	$message = $_POST['message'];

	wp_mail( $to, $subject, $message );

	wp_redirect( site_url( 'feedback/?message=1' ) );
	exit;
}
add_action( 'template_redirect', 'thememy_send_feedback' );

/**
 * Send survey answers to site admin
 *
 * @since ThemeMY! 0.1
 */
function thememy_send_survey() {
	if ( ! is_page_template( 'survey-page.php' ) || ! isset( $_POST['email'] ) )
		return;

	$post = stripslashes_deep( $_POST );

	$to = get_option( 'admin_email' );
	$subject = sprintf( __( 'Survey answers from %s' ), $post['email'] );
	$message = json_encode( $post );

	wp_mail( $to, $subject, $message );

	wp_redirect( add_query_arg( 'success', 'true' ) );
	exit;
}
add_action( 'template_redirect', 'thememy_send_survey' );

/**
 * Register log post type
 *
 * @since ThemeMY! 0.1
 */
function thememy_log_post_type() {
	$args = array(
		'labels' => array(
			'name' => __( 'Logs' ),
			'singular_name' => __( 'Log' )
		),
		'public' => false,
    'show_ui' => true
	);
	register_post_type( 'thememy_log', $args );
}
add_action( 'init', 'thememy_log_post_type' );

/**
 * Display error message and log error
 *
 * @since ThemeMY! 0.1
 *
 * @param mixed $data Data to log
 * @param bool $die Whether to display error message or not
 */
function thememy_error( $data, $die = true ) {
  $data = json_encode( $data );

	$args = array(
		'post_type'    => 'thememy_log',
		'post_author'  => 1,
		'post_title'   => wp_hash( $data ),
		'post_content' => $data
	);
	wp_insert_post( $args );

	if ( $die )
		wp_die( __( '<h1>OMG! We broke something!</h1> <p>We have been notified and will fix it ASAP. We apologize for the inconvenience.<br /> <b>Please try again later.</b></p>' ) );
}

/**
 * Send installation request email to ThemeMY! admin
 *
 * @since ThemeMY! 0.1
 */
function thememy_install_request() {
	if ( ! is_page_template( 'download-page.php' ) || empty( $_POST ) )
		return;

	$to = get_option( 'admin_email' );
	$subject = __( 'Theme Installation Request' );
	$message = json_encode( stripslashes_deep( $_POST ) );

	wp_mail( $to, $subject, $message );

	wp_redirect( add_query_arg( 'message', '1' ) );
	die;
}
add_action( 'template_redirect', 'thememy_install_request' );

/**
 * Echo ThemeMY! plugin download link
 *
 * @since ThemeMY! 0.1
 */
function thememy_plugin_download_link() {
	echo site_url( 'thememy.zip' );
}

