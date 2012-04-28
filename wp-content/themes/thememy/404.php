<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package _s
 * @since _s 1.0
 */

get_header(); ?>

	<article id="post-0" class="post error404 not-found">
		<header class="page-header">
			<h1 class="entry-title"><?php _e( 'Oops! That page can&rsquo;t be found.', '_s' ); ?></h1>
		</header>

		<div class="entry-content">
			<p><?php _e( 'It looks like nothing was found at this location.', '_s' ); ?></p>
		</div><!-- .entry-content -->
	</article><!-- #post-0 -->

<?php get_footer(); ?>
