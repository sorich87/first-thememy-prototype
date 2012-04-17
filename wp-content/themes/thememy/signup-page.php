<?php
/**
 * Template Name: Signup Page
 *
 * @package thememy
 * @since ThemeMY! 0.1
 */

$request = stripslashes_deep( $_REQUEST );

$access_key = isset( $request['key'] ) ? $request['key'] : '';
$user_email = isset( $request['user_email'] ) ? $request['user_email'] : '';
$first_name = isset( $request['first_name'] ) ? $request['first_name'] : '';
$last_name  = isset( $request['last_name'] ) ? $request['last_name'] : '';
$message    = isset( $request['message'] ) ? $request['message'] : '';

if ( get_user_by( 'email', $user_email ) )
	wp_die( __( 'You, or someone else, already registered with this email.' ) );

if ( ! $user_email || ! $access_key || $access_key != wp_hash( $user_email ) )
	wp_die( __( "You don't have the rights to access this resource." ) );

get_header(); ?>

	<div class="page-header">
		<h1><?php _e( 'Signup' ); ?></h1>
	</div>

	<?php if ( $message ) : ?>
		<div class="alert alert-error">
			<?php
			switch( $message ) :
				case '1' :
					_e( 'Please fill in all the fields.' );
					break;

				case '2' :
					_e( 'Please enter a valid email.' );
					break;

				case '3' :
					_e( 'The passwords you entered do not match.' );
					break;

				default :
					break;
			endswitch;
			?>
		</div>
	<?php endif; ?>

	<p><?php _e( 'In a few minutes, you will be selling your themes. Please fill in the form below to setup your account.' ); ?></p>

	<form class="form-horizontal" action="" method="post">
		<?php wp_referer_field(); ?>
		<fieldset>
			<legend><?php _e( 'Account Details' ); ?></legend>
			<div class="control-group<?php if ( $message && ! $first_name ) echo ' error'; ?>">
				<label class="control-label" for="first_name"><?php _e( 'First Name' ); ?></label>
				<div class="controls">
					<input type="text" class="input-xlarge" id="first_name" name="first_name" value="<?php echo $first_name; ?>">
				</div>
			</div>
			<div class="control-group<?php if ( $message && ! $last_name ) echo ' error'; ?>">
				<label class="control-label" for="last_name"><?php _e( 'Last Name' ); ?></label>
				<div class="controls">
					<input type="text" class="input-xlarge" id="last_name" name="last_name" value="<?php echo $last_name; ?>">
				</div>
			</div>
			<div class="control-group<?php if ( $message && ! $user_email ) echo ' error'; ?>">
				<label class="control-label" for="user_email"><?php _e( 'Email' ); ?></label>
				<div class="controls">
					<input type="text" class="input-xlarge" id="user_email" name="user_email" value="<?php echo $user_email; ?>">
				</div>
			</div>
			<div class="control-group<?php if ( $message ) echo ' error'; ?>">
				<label class="control-label" for="user_pass"><?php _e( 'Password' ); ?></label>
				<div class="controls">
					<input type="password" class="input-xlarge" id="user_pass" name="user_pass">
				</div>
			</div>
			<div class="control-group<?php if ( $message ) echo ' error'; ?>">
				<label class="control-label" for="user_pass_2"><?php _e( 'Re-type Password' ); ?></label>
				<div class="controls">
					<input type="password" class="input-xlarge" id="user_pass_2" name="user_pass_2">
				</div>
			</div>
			<div class="form-actions">
				<button type="submit" class="btn btn-primary"><?php _e( 'Get Started' ); ?></button>
			</div>
		</fieldset>
	</form>

<?php get_footer(); ?>
