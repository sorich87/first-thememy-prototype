<?php
/**
 * Template Name: Themes Archive
 *
 * @package thememy
 * @since ThemeMY! 0.1
 */

get_header(); ?>

	<?php $current_user_id = get_current_user_id(); ?>

	<div class="page-header">
		<h1><?php _e( 'Your Themes' ); ?></h1>
	</div>

	<?php if ( isset( $_GET['message'] ) && '1' == $_GET['message'] ) : ?>
		<div class="alert alert-success">
			<?php _e( 'Theme successfully uploaded.' ); ?>
		</div>
	<?php endif; ?>

	<?php if ( get_user_meta( $current_user_id, 'thememy_settings' ) == false ) : ?>
		<div class="alert alert-error">
			<?php printf( __( 'Please fill in your <a href="%s">store settings</a> or the purchase links will not work.' ), site_url( 'settings/' ) ); ?>
		</div>
	<?php endif; ?>

	<form class="well form-horizontal" enctype="multipart/form-data" method="post" action="<?php echo admin_url( '?page=td-admin' ); ?>">
		<?php wp_nonce_field( 'td-theme-upload' ); ?>
		<fieldset>
			<legend><?php _e( 'Upload a new theme' ); ?></legend>
			<div class="control-group">
				<label class="control-label" for="input01">Upload zip archive</label>
				<div class="controls">
					<input type="file" class="input-file" id="theme-upload" name="themezip">
					<p class="help-block"><?php _e( 'If you previouly uploaded a theme with the same name, it will be replaced.' ); ?></p>
				</div>
			</div>
			<div class="control-group">
				<div class="controls">
					<button type="submit" class="btn btn-primary">
						<i class="icon-white icon-upload"></i>
						Upload
					</button>
				</div>
			</div>
		</fieldset>
	</form>

	<?php if ( have_posts() ) : ?>

		<ul class="thumbnails">

		<?php /* Start the Loop */ ?>
		<?php while ( have_posts() ) : the_post(); ?>

		<li class="span3">
			<div class="thumbnail">
				<?php td_screenshot(); ?>
				<div class="caption">
					<h4><?php the_title(); ?></h4>
					<pre><?php td_purchase_link(); ?></pre>
				</div>
			</div>
		</li>

		<?php endwhile; ?>

		</ul>

		<div>
			<h3><?php _e( 'Global Purchase Link' ); ?></h3>
			<pre><?php td_global_purchase_link(); ?></pre>
		</div>

	<?php elseif ( current_user_can( 'edit_posts' ) ) : ?>

		<div class="alert alert-error">
			<?php _e( 'Dude, everybody is waiting in line to buy your themes. <strong>Please, upload your first theme now and start selling it.</strong>' ); ?>
		</div>

	<?php endif; ?>

<?php get_footer(); ?>
