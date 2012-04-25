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

	wp_enqueue_style( 'style', get_stylesheet_uri(), false, 201204242 );

	if ( $debug ) {
		wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/assets/less/bootstrap.less' );
		wp_enqueue_style( 'bootstrap-reponsive', get_template_directory_uri() . '/assets/less/responsive.less' );
		wp_enqueue_script( 'less', get_template_directory_uri() . '/assets/less/less-1.3.0.min.js' );
	} else {
		wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/assets/css/bootstrap.min.css', false, 201204241 );
		wp_enqueue_style( 'bootstrap-reponsive', get_template_directory_uri() . '/assets/css/bootstrap-responsive.min.css', false, 201204241 );
	}

	wp_enqueue_script( 'jquery' );

	wp_enqueue_script( 'script', get_template_directory_uri() . '/script.js', 'jquery', 201204241, true );

	wp_enqueue_script( 'bootstrap-collapse', get_template_directory_uri() . '/assets/js/bootstrap-collapse.js', 'jquery', 201204241, true );
	wp_enqueue_script( 'bootstrap-modal', get_template_directory_uri() . '/assets/js/bootstrap-modal.js', 'jquery', '20120416', true );
	wp_enqueue_script( 'bootstrap-tab', get_template_directory_uri() . '/assets/js/bootstrap-tab.js', 'jquery', '20120412', true );

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

