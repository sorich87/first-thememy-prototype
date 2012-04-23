<?php

/**
 * Add the options page for sending invitations
 *
 * @since ThemeMY! 0.1
 */
function thememy_admin_menu() {
	add_options_page( __( 'Invitations' ), __( 'Invitations' ), 'manage_options', 'thememy-invitations', 'thememy_invitations_page' );
}
add_action( 'admin_menu', 'thememy_admin_menu' );

/**
 * Invitations page content
 *
 * @since ThemeMY! 0.1
 */
function thememy_invitations_page() {
?>
	<div class="wrap">
		<h2><?php _e( 'Send Invitations' ); ?></h2>

		<?php if ( ! empty( $_GET['success'] ) ) : ?>
		<div class="updated"><p><strong><?php _e( 'Invitation sent.' ); ?></strong></p></div>
		<?php endif; ?>

		<form method="post" action="">
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e( 'Email' ); ?></th>
					<td>
						<input type="text" name="user_email" value="" />
					</td>
				</tr>
			</table>
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e( 'Send' ); ?>" />
			</p>
		</form>
	</div>
<?php	
}

/**
 * Send invitation
 *
 * @since ThemeMY! 0.1
 */
function thememy_send_invitation() {
	global $plugin_page;

	if ( 'thememy-invitations' != $plugin_page || ! isset( $_POST['user_email'] ) )
		return;

	$to = $_POST['user_email'];

	$signup_page = add_query_arg( array(
		'user_email' => urlencode( $to ),
		'key' => wp_hash( $to )
	), site_url( 'signup/' ) );

	$subject = __( 'The wait is over! You can build your theme store now.' );

	$message = sprintf(
		__( "Hi! I have great news for you.

Since you signed up a couple of weeks ago, we have been hard at work to get ThemeMY! ready for public beta.
There are so much features we want to add in, but for now we just built the basics.
We received several emails from some of you asking for the day we will start sending invitations. That day is today.

Click on the link below to setup your account right away.
%s

We are very grateful for your patience and hope we built something that you will like using.

As ThemeMY! is currently in beta, if you find any bug or a must-have feature you would like incorporated,
please forgive our oversight and send us a message to get it fixed.

I hope to read your feedback soon!

Ulrich Sossou
Lead Developer,
ThemeMY!
http://thememy.com/

P.S.: If you are not interested in building your theme store anymore, just ignore this email and you will never receive another one.
" ),
		$signup_page
	);

	wp_mail( $to, $subject, $message );

	wp_redirect( add_query_arg( 'success', 'true' ) );
	exit;
}
add_action( 'admin_init', 'thememy_send_invitation' );

