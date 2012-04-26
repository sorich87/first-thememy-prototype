<?php
/**
 * Template Name: Themes Archive
 *
 * @package thememy
 * @since ThemeMY! 0.1
 */

get_header(); ?>

	<div class="page-header">
		<h1><?php _e( 'Your Themes' ); ?></h1>
	</div>

	<?php if ( isset( $_GET['message'] ) ) : ?>
		<div class="alert alert-error">
			<?php thememy_upload_error_message(); ?>
		</div>
	<?php endif; ?>

	<?php if ( isset( $_GET['deleted'] ) ) : ?>
		<div class="alert alert-success">
			<?php _e( 'Theme successfully deleted.' ); ?>
		</div>
	<?php endif; ?>

	<?php $settings = thememy_get_settings(); ?>

	<?php if ( ! $settings ) : ?>
		<div class="alert alert-error">
			<?php printf( __( 'Please fill in your <a href="%s">store settings</a> or the purchase links will not work.' ), site_url( 'settings/' ) ); ?>
		</div>
	<?php elseif ( ! empty( $settings['test-mode'] ) ) : ?>
		<div class="alert alert-error">
			<?php printf( __( 'Your store is in test mode. Real payments will not be processed. Please uncheck the test mode field on your <a href="%s">store settings page</a> if you want to start receiving payments from your customers.' ), site_url( 'settings/' ) ); ?>
		</div>
	<?php endif; ?>

	<form class="well form-horizontal" enctype="multipart/form-data" method="post" action="">
		<?php wp_nonce_field( 'thememy-theme-upload' ); ?>
		<fieldset>
			<legend><?php _e( 'Upload a new theme' ); ?></legend>
			<div class="control-group">
			<label class="control-label" for="input01"><?php _e( 'Upload zip archive' ); ?></label>
				<div class="controls">
					<input type="file" class="input-file" id="theme-upload" name="themezip">
					<p class="help-block"><?php _e( 'If you previouly uploaded a theme with the same name, it will be replaced.' ); ?></p>
				</div>
			</div>
			<div class="control-group">
				<div class="controls">
					<button type="submit" class="btn btn-primary">
						<i class="icon-white icon-upload"></i>
						<?php _e( 'Upload' ); ?>
					</button>
				</div>
			</div>
		</fieldset>
	</form>

	<?php
	$args = array(
		'post_type' => 'theme',
		'author'    => get_current_user_id(),
		'nopaging'  => true
	);
	$themes_query = new WP_Query( $args );
	?>

	<?php if ( $themes_query->have_posts() ) : ?>

		<ul class="thumbnails">

		<?php /* Start the Loop */ ?>
		<?php while ( $themes_query->have_posts() ) : $themes_query->the_post(); ?>

		<li class="span3">
			<div class="thumbnail">
				<?php thememy_screenshot(); ?>
				<div class="caption">
					<h4>
						<?php the_title(); ?>
						<?php thememy_current_version(); ?>
					</h4>
					<hr />
					<button class="btn btn-mini" data-toggle="collapse" data-target="#theme-details-<?php the_ID(); ?>">
						<?php _e( 'theme details' ); ?>
					</button>
					<a class="btn btn-mini btn-danger delete-theme" href="<?php thememy_delete_link(); ?>"><?php _e( 'delete theme' ); ?></a>
					<div class="collapse" id="theme-details-<?php the_ID(); ?>">
						<hr />
						<a class="close pull-right" data-toggle="collapse" data-target="#theme-details-<?php the_ID(); ?>">&times;</a>
						<p>
							<b><?php _e( 'Landing Page' ); ?></b>
							<a href="<?php thememy_edit_link(); ?>">(<?php _e( 'edit' ); ?>)</a>
						</p>
						<pre><a href="<?php the_permalink(); ?>"><?php the_permalink(); ?></a></pre>
					</div>
				</div>
			</div>
		</li>

		<?php endwhile; ?>

		</ul>

	<?php elseif ( current_user_can( 'edit_posts' ) ) : ?>

		<div class="alert alert-error">
			<?php _e( 'Dude, everybody is waiting in line to buy your themes. <strong>Please, upload your first theme now and start selling it.</strong>' ); ?>
		</div>

	<?php endif; wp_reset_postdata(); ?>

<?php get_footer(); ?>
