<?php
/**
 * The Header for our theme.
 *
 * @package thememy
 * @since ThemeMY! 0.1
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title><?php
	/*
	 * Print the <title> tag based on what is being viewed.
	 */
	global $page, $paged;

	wp_title( '|', true, 'right' );

	// Add the blog name.
	bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		echo " | $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		echo ' | ' . sprintf( __( 'Page %s', 'thememy' ), max( $paged, $page ) );

	?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
  <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

<?php wp_head(); ?>
</head>

	<body <?php body_class(); ?>>

		<?php do_action( 'before' ); ?>

		<?php if ( is_front_page() || is_user_logged_in() ) : ?>

		<div class="navbar navbar-fixed-top">
			<div class="navbar-inner">
				<div class="container">
					<a class="brand" href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
						<?php bloginfo( 'name' ); ?>
						<sup><?php _e( 'beta' ); ?></sup>
					</a>
					<?php if ( is_user_logged_in() ) : ?>
					<ul class="nav">
						<li<?php if ( is_post_type_archive( 'td_theme' ) ) echo ' class="active"'; ?>><a href="<?php echo site_url( 'themes/' ); ?>"><?php _e( 'Themes' ); ?></a></li>
						<li<?php if ( is_page( 'reports' ) ) echo ' class="active"'; ?>><a href="<?php echo site_url( 'reports/' ); ?>"><?php _e( 'Reports' ); ?></a></li>
						<li<?php if ( is_page( 'settings' ) ) echo ' class="active"'; ?>><a href="<?php echo site_url( 'settings/' ); ?>"><?php _e( 'Settings' ); ?></a></li>
						<li<?php if ( is_page( 'feedback' ) ) echo ' class="active"'; ?>><a href="<?php echo site_url( 'feedback/' ); ?>"><?php _e( 'Feedback' ); ?></a></li>
					</ul>
					<p class="nav pull-right">
						<a class="btn" href="<?php echo esc_url( wp_logout_url( site_url( '/' ) ) ); ?>">Logout</a>
					</p>
				
					<p class="navbar-text pull-right"><?php printf( __( 'Welcome %s' ), wp_get_current_user()->first_name ); ?></p>
	
					<?php else : ?>
					<form class="navbar-form form-inline pull-right" method="post" action="<?php echo esc_url( site_url( 'wp-login.php', 'login_post' ) ); ?>">
						<input type="text" class="input-small" placeholder="Email" name="log">
						<input type="password" class="input-small" placeholder="Password" name="pwd">
						<button type="submit" class="btn" name="wp-submit">Sign in</button>
						<input type="hidden" name="redirect_to" value="<?php echo esc_url( site_url( 'themes/' ) ); ?>" />
					</form>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<?php endif; ?>

		<?php if ( ! is_front_page() ) : ?>
		<div class="container">
		<?php endif; ?>
