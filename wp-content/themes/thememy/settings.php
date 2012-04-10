<?php
/**
 * Template Name: Settings
 *
 * @package thememy
 * @since ThemeMY! 0.1
 */

get_header(); ?>

	<?php
	$current_user_id = get_current_user_id();
	$settings = get_user_meta( $current_user_id, 'thememy_settings', true );

	if ( empty( $settings['return-page'] ) )
		$settings['return-page'] = site_url( "order-confirmation/?store=$current_user_id" );

	if ( empty( $settings['cancel-page'] ) )
		$settings['cancel-page'] = site_url( "order-cancellation/?store=$current_user_id" );
	?>

	<div class="page-header">
		<h1><?php _e( 'Your Store Settings' ); ?></h1>
	</div>

	<?php if ( isset( $_GET['message'] ) ) : ?>
		<?php if ( '1' == $_GET['message'] ) : ?>
			<div class="alert alert-success">
				<?php _e( 'Settings saved.' ); ?>
			</div>
		<?php elseif( '2' == $_GET['message'] ) : ?>
			<div class="alert alert-error">
				<?php _e( 'Your changes were not saved. Please fill in all the required fields.' ); ?>
			</div>
		<?php endif; ?>
	<?php endif; ?>

	<form class="form-horizontal" method="post">
		<div class="tabbable">
			<ul class="nav nav-tabs">
			<li class="active"><a href="#tab-1" data-toggle="tab"><?php _e( 'General' ); ?></a></li>
				<li><a href="#tab-2" data-toggle="tab"><?php _e( 'Prices' ); ?></a></li>
				<li><a href="#tab-3" data-toggle="tab"><?php _e( 'Payments' ); ?></a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="tab-1">
					<fieldset>
						<legend><?php _e( 'General' ); ?></legend>
						<div class="control-group">
							<label class="control-label" for="business-name"><?php _e( 'Business name' ); ?></label>
							<div class="controls">
								<input value="<?php echo isset( $settings['business-name'] ) ? $settings['business-name'] : ''; ?>" type="text" class="input-xlarge" id="business-name" name="business-name">
								<p class="help-block"><?php _e( 'Your business name.' ); ?></p>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="business-email"><?php _e( 'Business email address' ); ?></label>
							<div class="controls">
								<input value="<?php echo isset( $settings['business-email'] ) ? $settings['business-email'] : ''; ?>" type="text" class="input-xlarge" id="business-email" name="business-email">
								<p class="help-block"><?php _e( 'Email address to show as sender of all communications with your customers.' ); ?></p>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="home-page"><?php _e( 'Home page' ); ?></label>
							<div class="controls">
								<input value="<?php echo isset( $settings['home-page'] ) ? $settings['home-page'] : ''; ?>" type="text" class="input-xlarge" id="home-page" name="home-page">
								<p class="help-block"><?php _e( 'Your website home page. Optional.' ); ?></p>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="return-page"><?php _e( 'Return Page' ); ?></label>
							<div class="controls">
								<input value="<?php echo isset( $settings['return-page'] ) ? $settings['return-page'] : ''; ?>" type="text" class="input-xlarge" id="return-page" name="return-page">
								<p class="help-block">
									<?php _e( 'Page to redirect the customers to after they have approved the payment.' ); ?>
								</p>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="cancel-page"><?php _e( 'Cancel Page' ); ?></label>
							<div class="controls">
								<input value="<?php echo isset( $settings['cancel-page'] ) ? $settings['cancel-page'] : ''; ?>" type="text" class="input-xlarge" id="cancel-page" name="cancel-page">
								<p class="help-block">
									<?php _e( 'Page to redirect the customers to if they cancel the payment.' ); ?>
								</p>
							</div>
						</div>
					</fieldset>
				</div>

				<div class="tab-pane" id="tab-2">
					<fieldset>
						<legend><?php _e( 'Prices' ); ?></legend>
						<div class="control-group">
							<label class="control-label" for="price-one"><?php _e( 'For one theme' ); ?></label>
							<div class="controls">
								<div class="input-prepend">
								<span class="add-on">$</span><input value="<?php echo isset( $settings['price-one'] ) ? $settings['price-one'] : ''; ?>" type="text" class="input-mini" id="price-one" name="price-one">
								</div>
								<p class="help-block"><?php _e( 'Price for when a user buy only one theme.' ); ?></p>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="price-all"><?php _e( 'For all themes' ); ?></label>
							<div class="controls">
								<div class="input-prepend">
									<span class="add-on">$</span><input value="<?php echo isset( $settings['price-all'] ) ? $settings['price-all'] : ''; ?>" type="text" class="input-mini" id="price-all" name="price-all">
								</div>
								<p class="help-block"><?php _e( 'Price for when a user buy all your themes in one bundle.' ); ?></p>
							</div>
						</div>
					</fieldset>
				</div>

				<div class="tab-pane" id="tab-3">
					<fieldset>
						<legend><?php _e( 'Payments' ); ?></legend>
						<div class="control-group">
							<label class="control-label" for="test-mode"><?php _e( 'Test Mode' ); ?></label>
							<div class="controls">
								<label class="checkbox">
									<input<?php if ( isset( $settings['test-mode'] ) ) checked( $settings['test-mode'], 'yes' ); ?> type="checkbox" id="test-mode" name="test-mode" value="yes">
									<?php _e( 'Use this when testing the purchase links. If checked, real payments will not be processed.' ); ?>
								</label>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="paypal-email"><?php _e( 'Paypal Email' ); ?></label>
							<div class="controls">
								<input value="<?php echo isset( $settings['paypal-email'] ) ? $settings['paypal-email'] : ''; ?>" type="text" class="input-xlarge" id="paypal-email" name="paypal-email">
								<p class="help-block"><?php _e( 'Your Paypal email address were you will receive the payments.' ); ?></p>
							</div>
						</div>
					</fieldset>
				</div>
			</div>
		</div>

		<div class="form-actions">
			<button type="submit" class="btn btn-primary">
				<i class="icon-white icon-ok"></i>
				Save changes
			</button>
		</div>
	</form>

<?php get_footer(); ?>
