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
