<?php
/**
 * The Template for displaying all single posts.
 *
 * @package ThemeMY_
 * @since ThemeMY! 0.1
 */

get_header(); ?>

	<?php while ( have_posts() ) : the_post(); ?>

	<?php endwhile; // end of the loop. ?>

<?php get_footer(); ?>
