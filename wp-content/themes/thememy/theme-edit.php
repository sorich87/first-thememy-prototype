<?php
/**
 * The Template for displaying all single posts.
 *
 * @package ThemeMY_
 * @since ThemeMY! 0.1
 */

$post = get_queried_object();

get_header(); ?>

	<?php while ( have_posts() ) : the_post(); ?>

		<div class="page-header">
			<h1>
				<?php echo get_the_title(); ?>
				<a href="<?php echo site_url( 'themes/' ); ?>" class="btn btn-mini"><?php _e( 'Back to theme list' ); ?></a>
				<a href="<?php the_permalink(); ?>" class="btn btn-mini"><?php _e( 'View landing page' ); ?></a>
			</h1>
		</div>

		<form class="well form-horizontal">
			<input type="hidden" id="theme-id" name="theme-id" value="<?php the_ID(); ?>" />
			<fieldset>
				<legend><?php _e( 'Edit landing page details' ); ?></legend>
				<div class="control-group">
					<label class="control-label" for="theme-slug"><?php _e( 'Landing page URL' ); ?></label>
					<div class="controls">
						<div class="input-prepend input-append">
							<?php
							$button_states = array(
								'loading'     => __( 'Verifying...' ),
								'error'       => __( 'Access denied.' ),
								'unavailable' => __( 'Already used. Try another.' ),
								'available'   => __( 'Available!' )
							);
							$button_states_attrs = '';
							foreach ( $button_states as $key => $value ) {
								$button_states_attrs .= " data-{$key}-text='" . esc_attr( $value ) . "'";
							}

							// Using PHP here because there should be no space between the HTML elements
							// and doing that with HTML only would give a very lengthy line
							echo '<span class="add-on">' . site_url( 'theme/' ) . '</span>';
							echo "<input type='text' class='input-medium' id='theme-slug' name='theme-slug' value='$post->post_name' />";
							echo "<button class='btn btn-info'$button_states_attrs autocomplete='off' id='verify-theme-slug'>";
							echo __( 'Verify Availability' );
							echo '</button>';
							?>
						</div>
						<p class="help-block"><?php _e( 'Choose the URL for the theme landing page' ); ?></p>
					</div>
				</div>
			</fieldset>
		</form>

		<h3><?php _e( 'Slideshow Images' ); ?></h3>

		<?php
		$button_states = array(
			'loading'  => __( 'Uploading...' ),
			'error'    => __( 'Access denied.' ),
			'complete' => __( 'Success!' )
		);
		$button_states_attrs = '';
		foreach ( $button_states as $key => $value ) {
			$button_states_attrs .= " data-{$key}-text='" . esc_attr( $value ) . "'";
		}
		?>
		<p>
			<button id="plupload-browse-button" class="btn"<?php echo $button_states_attrs; ?>><?php _e( 'Add Images' ); ?></button>

			<div id="errorlist" class="hide alert alert-block alert-error">
				<h4 class="alert-heading"><?php _e( 'Errors occured' ); ?></h4>
				<ul class="unstyled"></ul>
			</div>
		</p>

		<?php
		$args = array(
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'nopaging'       => true,
			'post_parent'    => get_the_ID(),
			'exclude'        => get_post_thumbnail_id()
		);
		$attachments = get_posts( $args );
		?>

		<?php if ( $attachments ) : ?>
			<ul id="slideshow-images" class="thumbnails">
			<?php foreach ( $attachments as $attachment ) : ?>
				<li class="span3">
					<span class="thumbnail">
						<a class="close delete-image" href="<?php echo $attachment->ID; ?>">&times;</a>
						<?php echo wp_get_attachment_image( $attachment->ID, 'post-thumbnail' ); ?>
					</span>
				</li>
			<?php endforeach; ?>
			</ul>
		<?php else : ?>
			<p id="no-image"><?php _e( 'No image uploaded. Upload one above if you want the slideshow to be displayed on the landing page.' ); ?></p>
			<ul id="slideshow-images" class="thumbnails hide"></ul>
		<?php endif; ?>

	<?php endwhile; // end of the loop. ?>

<?php get_footer(); ?>
