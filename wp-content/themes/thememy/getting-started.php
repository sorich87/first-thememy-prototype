<div class="modal hide" id="getting-started">
  <div class="modal-header">
    <a class="close" data-dismiss="modal" href="#getting-started">×</a>
		<h3><?php _e( 'Getting Started' ); ?></h3>
  </div>
  <div class="modal-body">
		<p><?php _e( 'Start selling your themes in three (3) easy steps.' ); ?></p>

		<h4>
			<?php _e( 'Step 1' ); ?>
			<?php if ( thememy_get_settings() ) : ?>
				<span class="label label-success"><?php _e( 'done' ); ?></span>
			<?php endif; ?>
		</h4>
		<p><?php printf(
			__( 'Go to <a href="%s">the settings page</a> and fill in your business, pricing and payment details.' ),
			add_query_arg( 'getting_started', 'true', site_url( 'settings/' ) )
		); ?></p>

		<h4>
			<?php _e( 'Step 2' ); ?>
			<?php if ( get_posts( array( 'post_type' => 'theme', 'author' => get_current_user_id(), 'fields' => 'ids' ) ) ) : ?>
				<span class="label label-success"><?php _e( 'done' ); ?></span>
			<?php endif; ?>
		</h4>
		<p><?php printf(
			__( 'Go to <a href="%s">the themes page</a> and upload the themes you want to sell.' ),
			add_query_arg( 'getting_started', 'true', site_url( 'themes/' ) )
		); ?></p>

		<h4><?php _e( 'Step 3' ); ?></h4>
		<p><?php _e( 'Share the theme landing pages with your website visitors or on social medias.' ); ?></p>
  </div>
  <div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal"><?php _e( 'Close' ); ?></a>
		<a href="#" class="btn btn-warning" id="no-getting-started"><?php _e( "Don't show this again" ); ?></a>
  </div>
</div>
<?php wp_print_scripts( 'bootstrap-modal' ); ?>
<script type="text/javascript">
jQuery(function ($) {
	$("#getting-started").modal();

	$("#no-getting-started").click(function (e) {
		e.preventDefault();

		var data = {
			_ajax_nonce: "<?php echo wp_create_nonce( 'thememy-no-getting-started' ); ?>",
			action: "thememy-no-getting-started"
		};
		$.post( "<?php echo admin_url( 'admin-ajax.php' ); ?>", data );

		$("#getting-started").modal( "hide" );
	});
});
</script>
