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

	<?php if ( isset( $_GET['message'] ) && '1' == $_GET['message'] ) : ?>
		<div class="alert alert-success">
			<?php _e( 'Theme successfully uploaded.' ); ?>
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

	<form class="well form-horizontal" enctype="multipart/form-data" method="post" action="<?php echo admin_url( '?page=td-admin' ); ?>">
		<?php wp_nonce_field( 'td-theme-upload' ); ?>
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
		'post_type' => 'td_theme',
		'author'    => get_current_user_id(),
		'nopaging'  => true
	);
	$query = new WP_Query( $args );
	?>

	<?php if ( $query->have_posts() ) : ?>

		<ul class="thumbnails">

		<?php /* Start the Loop */ ?>
		<?php while ( $query->have_posts() ) : $query->the_post(); ?>

		<li class="span3">
			<div class="thumbnail">
				<?php thememy_screenshot(); ?>
				<div class="caption">
					<h4>
						<?php the_title(); ?>
						<?php thememy_current_version(); ?>
						<a class="small" data-toggle="collapse" data-target="#theme-details-<?php the_ID(); ?>"><?php _e( '(details)' ); ?></a>
					</h4>
					<div class="collapse" id="theme-details-<?php the_ID(); ?>">
						<p>
							<a href="<?php thememy_edit_link(); ?>"><?php _e( 'edit' ); ?></a>
							<a href="<?php thememy_delete_link(); ?>" class="alert-danger"><?php _e( 'delete' ); ?></a>
						</p>
						<p><b><?php _e( 'Landing page' ); ?></b></p>
						<pre><?php the_permalink(); ?></pre>
						<p><b><?php _e( 'Buy now button' ); ?></b></p>
						<pre>buy now</pre>
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

	<?php endif; ?>

<?php get_footer(); ?>
