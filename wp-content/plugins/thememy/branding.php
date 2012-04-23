<?php

/**
 * Add favicon and touch icons
 *
 * @since ThemeMY! 0.1
 */
function thememy_favicon() {
?>
<link rel="shortcut icon" href="<?php echo THEMEMY_PLUGIN_URL; ?>/assets/ico/favicon.ico" />
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo THEMEMY_PLUGIN_URL; ?>assets/ico/apple-touch-icon-114-precomposed.png" />
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo THEMEMY_PLUGIN_URL; ?>assets/ico/apple-touch-icon-72-precomposed.png" />
<link rel="apple-touch-icon-precomposed" href="<?php echo THEMEMY_PLUGIN_URL; ?>assets/ico/apple-touch-icon-57-precomposed.png" />
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

