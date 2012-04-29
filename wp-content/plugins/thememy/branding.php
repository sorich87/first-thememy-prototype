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
	if ( ! is_page_template( 'store-page.php' ) )
		return;
?>
<meta name="robots" content="noindex, nofollow" />
<?php
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
function thememy_features() {
	ob_start();
?>
<div class="row">
	<div class="span4">
		<hr />
		<h2>10 Minutes / 3 Steps Setup</h2>
		<p class="thumbnail"><img src="<?php echo get_template_directory_uri(); ?>/images/feature-setup.png" /></p>
		<p>
			All the time consuming tasks associated with selling themes,
			like building a store from scratch, keeping the software up to date,
			marketing, monitoring social medias, customer support, etc. are made easier for you.
		</p>
	</div>
	<div class="span4">
		<hr />
		<h2>Custom Themes Landing Pages</h2>
		<p class="thumbnail"><img src="<?php echo get_template_directory_uri(); ?>/images/feature-page.png" /></p>
		<p>
			You don't need to build a website from scratch to start selling your themes.
			Focus on what matter (designing your themes) by using the
			pre-built responsive landing pages with beautiful slideshows and social media integration.
		</p>
	</div>
	<div class="span4">
		<hr />
		<h2>"Buy Now" Buttons</h2>
		<p class="thumbnail"><img src="<?php echo get_template_directory_uri(); ?>/images/feature-button.png" /></p>
		<p>
			Paste the "Buy Now" buttons in your existing store or site
			to allow your visitors to buy your themes via Paypal and benefit from
			all the ThemeMY! features.
		</p>
	</div>
</div>
<div class="row">
	<div class="span4">
		<hr />
		<h2>Detailed Analytics</h2>
		<p class="thumbnail"><img src="<?php echo get_template_directory_uri(); ?>/images/feature-report.png" /></p>
		<p>
			Track visits, clicks, purchases, downloads,&hellip; Have a better insight on
			how visitors are actually interacting with your store and use it to
			improve your services.
		</p>
	</div>
	<div class="span4">
		<hr />
		<h2>On Demand Installation</h2>
		<p class="thumbnail"><img src="<?php echo get_template_directory_uri(); ?>/images/feature-installation.png" /></p>
		<p>
			When someone buy a theme from you via ThemeMY!, we offer them the possibility
			to request the theme to be installed on their website and that's a plus for you.
		</p>
	</div>
	<div class="span4">
		<hr />
		<h2>Automatic Updates</h2>
		<p class="thumbnail"><img src="<?php echo get_template_directory_uri(); ?>/images/feature-update.png" /></p>
		<p>
			Give your clients the convenience of getting your themes updates
			through their WordPress admin dashboard, the same way they get updates for
			themes downloaded from wordpress.org.
		</p>
	</div>
</div>
<div class="row">
	<div class="span10">
		<hr />
		<h2>Coming Soon...</h2>
		<p>
			Custom domains, Google Analytics integration, affiliate tracking, Facebook tabs, themes directory, and many more...
		</p>
	</div>
	<div class="span2">
		<p>
			<a class="btn btn-primary btn-large" href="<?php echo home_url( '/' ); ?>">Sign up Now</a>
		</p>
	</div>
</div>
<?php
	$content = ob_get_contents();
	ob_end_clean();

	return $content;
}
add_shortcode( 'features', 'thememy_features' );

/**
 * Display feedback form shortcode content
 *
 * @since ThemeMY! 0.1
 */
function thememy_feedback_form() {
	ob_start();
?>
<?php $state_class = ''; ?>

<?php if ( isset( $_GET['message'] ) ) : ?>
	<?php if ( '1' == $_GET['message'] ) : ?>
		<div class="alert alert-success">
			<?php _e( 'Your feedback was sent. Thanks! We will get in touch soon.' ); ?>
		</div>
	<?php elseif( '2' == $_GET['message'] ) : ?>
		<?php $state_class = ' error'; ?>

		<div class="alert alert-error">
			<?php _e( 'Please enter your message in the field below.' ); ?>
		</div>
	<?php endif; ?>
<?php endif; ?>

<p><?php _e( 'If you have questions, suggestions, bug reports, or simply want to get in touch, please send us a message. We value your feedback.' ); ?>

<form class="form-horizontal" method="post">
	<fieldset>
		<legend>Your Message</legend>
		<div class="control-group<?php echo $state_class; ?>">
		<label class="control-label" for="message"><?php _e( 'Enter your message here and click send' ); ?></label>
			<div class="controls">
				<textarea class="span9" id="message" name="message" rows="9"></textarea>
			</div>
		</div>
		<div class="form-actions">
			<button type="submit" class="btn btn-primary">
				<i class="icon-white icon-envelope"></i>
				<?php _e( 'Send message' ); ?>
			</button>
		</div>
	</fieldset>
</form>
<?php
	$content = ob_get_contents();
	ob_end_clean();

	return $content;
}
add_shortcode( 'feedback-form', 'thememy_feedback_form' );
