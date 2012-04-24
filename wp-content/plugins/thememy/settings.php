<?php

/**
 * Add Delete Account Confirmation modal
 *
 * @since ThemeMY! 0.1
 */
function thememy_delete_account_modal() {
	if ( ! is_user_logged_in() || ! is_page_template( 'settings.php' ) )
		return;
?>
<div class="modal hide alert-error" id="delete-account">
  <div class="modal-header">
    <a class="close" data-dismiss="modal" href="#getting-started">×</a>
		<h3><?php _e( 'Make sure you want to do this' ); ?></h3>
  </div>
  <div class="modal-body">
		<p><?php _e( 'Deleting your account will delete all the data in your account including all the themes you uploaded.' ); ?><br />
		<?php _e( 'If you want to use ThemeMY! again in the future, you will need to start over from zero! Are you sure you want to delete your account?' ); ?></p>

		<form id="delete-account-form" action="" method="post">
			<?php wp_nonce_field( 'delete-account', 'thememy_nonce' ); ?>
			<input type="hidden" name="action" value="delete-account" />

			<label for="delete-user-email"><?php _e( 'Please enter your email below to confirm:' ); ?></label>
			<input class="input-xlarge" type="text" name="user-email" id="delete-user-email" />
		</form>
  </div>
  <div class="modal-footer">
		<a href="#" class="btn btn-danger" id="delete-account-confirmed"><?php _e( 'Delete this account and all my themes' ); ?></a>
  </div>
</div>
<script type="text/javascript">
jQuery(function ($) {
	$("#delete-account-confirmed").click(function (e) {
		e.preventDefault();

		$("#delete-account-form").submit();
	});
});
</script>
<?php
}
add_action( 'wp_footer', 'thememy_delete_account_modal', 20 );

/**
 * Save user settings or delete account
 *
 * @since ThemeMY! 0.1
 */
function thememy_save_settings() {
	if ( ! is_page_template( 'settings.php' ) || empty( $_POST ) )
		return;

	$data = stripslashes_deep( $_POST );

	switch ( $data['action'] ) {
		case 'delete-account' :
			if ( ! wp_verify_nonce( $_POST['thememy_nonce'], 'delete-account' ) || current_user_can( 'edit_others_posts' ) ) {
				wp_redirect( site_url( 'settings' ) );
				exit;
			}

			include( ABSPATH . 'wp-admin/includes/user.php' );

			$user = get_user_by( 'email', $data['user-email'] );

			if ( ! $user || get_current_user_id() != $user->ID ) {
				wp_redirect( add_query_arg( 'message', 2 ) );
				exit;
			}

			wp_delete_user( $user->ID );

			wp_redirect( site_url( 'account-deleted' ) );
			exit;

		case 'save-settings' :
			if ( ! wp_verify_nonce( $_POST['thememy_nonce'], 'save-settings' ) ) {
				wp_redirect( site_url( 'settings' ) );
				exit;
			}

			if ( empty( $data['business-name'] ) || empty( $data['business-email'] )
				|| empty( $data['price-one'] ) || empty( $data['price-all'] )
				|| empty( $data['paypal-email'] ) ) {
				wp_redirect( site_url( 'settings/?message=1' ) );
				exit;
			}

			$user_id = get_current_user_id();

			update_user_meta( $user_id, '_thememy_settings', $data );

			wp_redirect( add_query_arg( 'success', 'true' ) );
			exit;
	}
}
add_action( 'template_redirect', 'thememy_save_settings' );
