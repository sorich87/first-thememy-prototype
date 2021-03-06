<?php
/**
 * The template for displaying all pages.
 *
 * @package ThemeMY_
 * @since ThemeMY! 0.1
 */

get_header(); ?>

	<?php while ( have_posts() ) : the_post(); ?>

		<?php get_template_part( 'content', 'page' ); ?>

		<?php comments_template( '', true ); ?>

	<?php endwhile; // end of the loop. ?>

<?php get_footer(); ?>
