<?php

/**
 * Add favicon and touch icons
 *
 * @since ThemeMY! 0.1
 */
function thememy_favicon() {
?>
<link rel="shortcut icon" href="<?php echo THEMEMY_PLUGIN_URL; ?>bootstrap/ico/favicon.ico" />
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo THEMEMY_PLUGIN_URL; ?>bootstrap/ico/apple-touch-icon-114-precomposed.png" />
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo THEMEMY_PLUGIN_URL; ?>bootstrap/ico/apple-touch-icon-72-precomposed.png" />
<link rel="apple-touch-icon-precomposed" href="<?php echo THEMEMY_PLUGIN_URL; ?>bootstrap/ico/apple-touch-icon-57-precomposed.png" />
<?php
}
add_action( 'wp_head', 'thememy_favicon' );

/**
 * Add Facebook meta
 *
 * @since ThemeMY! 0.1
 */
function thememy_facebook_meta() {
?>
<meta property="og:title" content="<?php _e( 'ThemeMY! - Setup your WordPress theme store in 10 minutes.' ); ?>" />
<meta property="og:type" content="company" />
<meta property="og:site_name" content="ThemeMY!" />
<meta property="og:url" content="<?php echo site_url(); ?>" />
<meta property="og:image" content="null" />
<meta property="og:description" content="<?php _e( 'I just signed up to ThemeMY! and will be able to setup my own WordPress theme store in minutes. Check it out now! (Seats are limited)' ); ?>" />
<?php
}
add_action( 'wp_head', 'thememy_facebook_meta' );

/**
 * Add robots meta tags
 *
 * @since ThemeMY! 0.1
 */
function thememy_robots_meta() {
	if ( is_page() && ! is_page( 'features' ) )
		echo "<meta name='robots' content='noindex, nofollow' />\n";
}
add_action( 'wp_head', 'thememy_robots_meta' );

/**
 * Add Google Analytics script
 *
 * @since ThemeMY! 0.1
 */
function thememy_analytics() {
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG )
		return;
?>
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-30564629-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
<?php
}
add_action( 'wp_head', 'thememy_analytics' );

/**
 * Change default email sender
 *
 * @since ThemeMY! 0.1
 */
function thememy_mail_from( $from_email ) {
	if ( strpos( $from_email, 'wordpress' ) === 0 ) {
		$sitename = strtolower( $_SERVER['SERVER_NAME'] );

		if ( substr( $sitename, 0, 4 ) == 'www.' )
			$sitename = substr( $sitename, 4 );

		$from_email = 'no-reply@' . $sitename;
	}

	return $from_email;
}
add_filter( 'wp_mail_from', 'thememy_mail_from' );

/**
 * Change default email sender name
 *
 * @since ThemeMY! 0.1
 */
function thememy_mail_from_name( $from_name ){
	if ( 'WordPress' == $from_name )
		$from_name = get_option( 'blogname' );

	return $from_name;
}
add_filter( 'wp_mail_from_name', 'thememy_mail_from_name' );

/**
 * Use SMTP to send emails
 *
 * @since ThemeMY! 0.1
 */
function thememy_phpmailer_init( $phpmailer ) {
	if ( ! defined( 'SMTP_HOST' ) )
		return;

	$phpmailer->IsSMTP();
	$phpmailer->Host = SMTP_HOST;
	$phpmailer->Port = defined( 'SMTP_PORT' ) ? SMTP_PORT : 25;
	if ( defined( 'SMTP_USER' ) ) {
		$phpmailer->SMTPAuth = true;
		$phpmailer->Username = SMTP_USER;
		$phpmailer->Password = defined( 'SMTP_PASSWORD' ) ? SMTP_PASSWORD : '';
	}
	if ( defined( 'SMTP_SECURE' ) )
		$phpmailer->SMTPSecure = SMTP_SECURE;
	if ( defined( 'SMTP_DEBUG' ) && SMTP_DEBUG )
		$phpmailer->SMTPDebug = true;
}
add_action( 'phpmailer_init', 'thememy_phpmailer_init' );

/**
 * Enqueue bootstrap scripts and styles
 *
 * @since ThemeMY! 0.1
 */
function thememy_enqueue_bootstrap() {
	if ( is_admin() )
		return;

	$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;
	$bootstrap_dir = THEMEMY_PLUGIN_URL . 'bootstrap/';

	wp_enqueue_script( 'jquery' );

	if ( $debug ) {
		wp_enqueue_style( 'bootstrap', $bootstrap_dir . 'less/bootstrap.less' );
		wp_enqueue_style( 'bootstrap-reponsive', $bootstrap_dir . 'less/responsive.less' );
		wp_enqueue_script( 'less', $bootstrap_dir . 'less/less-1.3.0.min.js' );
	} else {
		wp_enqueue_style( 'bootstrap', $bootstrap_dir . 'css/bootstrap.min.css', false, 201204241 );
		wp_enqueue_style( 'bootstrap-reponsive', $bootstrap_dir . 'css/bootstrap-responsive.min.css', false, 201204241 );
	}

	wp_register_script( 'bootstrap-alert', $bootstrap_dir . 'js/bootstrap-alert.js', 'jquery', 201204261, true );
	wp_register_script( 'bootstrap-button', $bootstrap_dir . 'js/bootstrap-button.js', 'jquery', 201204261, true );
	wp_register_script( 'bootstrap-carousel', $bootstrap_dir . 'js/bootstrap-carousel.js', 'jquery', 201204261, true );
	wp_register_script( 'bootstrap-collapse', $bootstrap_dir . 'js/bootstrap-collapse.js', 'jquery', 201204261, true );
	wp_register_script( 'bootstrap-dropdown', $bootstrap_dir . 'js/bootstrap-dropdown.js', 'jquery', 201204261, true );
	wp_register_script( 'bootstrap-modal', $bootstrap_dir . 'js/bootstrap-modal.js', 'jquery', 201204261, true );
	wp_register_script( 'bootstrap-popover', $bootstrap_dir . 'js/bootstrap-popover.js', 'jquery', 201204261, true );
	wp_register_script( 'bootstrap-scrollspy', $bootstrap_dir . 'js/bootstrap-scrollspy.js', 'jquery', 201204261, true );
	wp_register_script( 'bootstrap-tab', $bootstrap_dir . 'js/bootstrap-tab.js', 'jquery', 201204261, true );
	wp_register_script( 'bootstrap-tooltip', $bootstrap_dir . 'js/bootstrap-tooltip.js', 'jquery', 201204261, true );
	wp_register_script( 'bootstrap-transition', $bootstrap_dir . 'js/bootstrap-transition.js', 'jquery', 201204261, true );
	wp_register_script( 'bootstrap-typeahead', $bootstrap_dir . 'js/bootstrap-typeahead.js', 'jquery', 201204261, true );
}
add_action( 'wp_enqueue_scripts', 'thememy_enqueue_bootstrap' );

/**
 * Set features shortcodes content
 *
 * @since ThemeMY! 0.1
 */
function thememy_content_shortcode( $atts ) {
	extract( shortcode_atts( array(
		'file' => '',
	), $atts ) );

	if ( ! $file )
		return;

	ob_start();
	get_template_part( "content/$file" );
	$content = ob_get_contents();
	ob_end_clean();

	return $content;
}
add_shortcode( 'content', 'thememy_content_shortcode' );

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
	if ( ! is_page( 'survey' ) )
		return;

	$email = isset( $_GET['email'] ) ? stripslashes( $_GET['email'] ) : '';

	if ( ! isset( $_GET['success'] ) && ! get_user_by( 'email', $email ) )
		wp_die( __( "You don't have the rights to access this resource." ) );

	if ( ! isset( $_POST['email'] ) )
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

