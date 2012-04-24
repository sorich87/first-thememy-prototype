<?php
/**
 * Template Name: Download Page
 *
 * @package thememy
 * @since ThemeMY! 0.1
 */

$order = get_post( $_GET['order'] );
$access_key = $_GET['key'];

if ( ! $order )
	wp_die( __( 'Order not found' ) );

$buyer_email = get_post_meta( $order->ID, '_thememy_buyer', true );

if ( $access_key != wp_hash( $buyer_email ) )
	wp_die( __( "You don't have the rights to access this resource." ) );

get_header(); ?>

	<div class="page-header">
		<h1><?php printf( __( 'Order %s' ), $order->ID ); ?></h1>
	</div>

	<?php if ( isset( $_GET['message'] ) ) : ?>
		<?php if ( '1' == $_GET['message'] ) : ?>
			<div class="alert alert-success">
				<?php _e( 'Your installation request has been sent.' ); ?>
			</div>
		<?php endif; ?>
	<?php endif; ?>

	<p><?php _e( 'Here is the theme you purchased:' ); ?></p>
	<div class="well">
		<?php $theme = get_post( get_post_meta( $order->ID, '_thememy_item', true ) ); ?>
		<dl>
			<dt><?php echo $theme->post_title; ?></dt>
			<dd><a href="<?php echo thememy_get_download_link( $theme->ID ); ?>"><?php _e( 'download' ); ?></a></dd>
		</dl>
	</div>

	<p><?php _e( 'To receive automatic updates, please install the following plugin as well:' ); ?></p>
	<div class="well">
		<dl>
			<dt><?php _e( 'ThemeMY! Automatic Updates' ); ?></dt>
			<dd><a href="<?php thememy_plugin_download_link(); ?>"><?php _e( 'download' ); ?></a></dd>
		</dl>
	</div>

	<p><?php _e( 'Your API credentials are:' ); ?></p>
	<div class="well">
		<dl class="dl-horizontal">
			<dt><?php _e( 'Email:' ); ?></dt>
			<dd><?php echo $buyer_email; ?></dd>
			<dt><?php _e( 'API Key:' ); ?></dt>
			<dd><?php echo $access_key; ?></dd>
		</dl>
	</div>

	<p><?php _e( "If you would like the theme to be installed for you on your server, fill in the form below. It's free and the installation will be done within 12 hours." ); ?></p>

	<form class="form-horizontal" action="" method="post">
		<input type="hidden" name="theme_id" value="<?php echo $theme->ID; ?>" />

		<fieldset>
			<legend><?php _e( 'Installation Request' ); ?></legend>
			<div class="control-group">
				<label class="control-label" for="wp-siteurl"><?php _e( 'Website URL' ); ?></label>
				<div class="controls">
					<input type="text" class="input-xlarge" id="wp-siteurl" name="wp[siteurl]">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="wp-username"><?php _e( 'Admin Username' ); ?></label>
				<div class="controls">
					<input type="text" class="input-xlarge" id="wp-username" name="wp[username]">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="wp-password"><?php _e( 'Admin Password' ); ?></label>
				<div class="controls">
					<input type="password" class="input-xlarge" id="wp-password" name="wp[password]">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="ftp-host"><?php _e( 'FTP Host' ); ?></label>
				<div class="controls">
					<input type="text" class="input-xlarge" id="ftp-host" name="ftp[host]">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="ftp-username"><?php _e( 'FTP Username' ); ?></label>
				<div class="controls">
					<input type="text" class="input-xlarge" id="ftp-username" name="ftp[username]">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="ftp-password"><?php _e( 'FTP Password' ); ?></label>
				<div class="controls">
					<input type="password" class="input-xlarge" id="ftp-password" name="ftp[password]">
				</div>
			</div>
		</fieldset>
		<div class="form-actions">
			<button type="submit" class="btn btn-primary">Send</button>
		</div>
	</form>

<?php get_footer(); ?>
