<?php
/**
 * Template Name: Store Page
 *
 * @package thememy
 * @since ThemeMY! 0.1
 */

get_header(); ?>

	<?php while ( have_posts() ) : the_post(); ?>

		<?php
		/**
		 * Process settings shortcode
		 *
		 * @since ThemeMY! 0.1
		 */
		function thememy_settings_shortcode( $atts ) {
			if ( empty( $_GET['store'] ) )
				return;

			extract( shortcode_atts( array(
				'name' => ''
			), $atts ) );

			$settings = get_user_meta( $_GET['store'], 'thememy_settings', true );

			if ( isset( $settings[$name] ) )
				return $settings[$name];
		}

		add_shortcode( 'thememy-setting', 'thememy_settings_shortcode' );
		?>

		<?php get_template_part( 'content', 'page' ); ?>

		<?php comments_template( '', true ); ?>

	<?php endwhile; // end of the loop. ?>

<?php get_footer(); ?>
