<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package thememy
 * @since ThemeMY! 0.1
 */
?>

			<hr>

      <footer>
        <?php do_action( 'thememy_credits' ); ?>
				<p>
					<a href="http://wordpress.org/" title="<?php esc_attr_e( 'A Semantic Personal Publishing Platform', 'thememy' ); ?>" rel="generator"><?php printf( __( 'Proudly powered by %s', 'thememy' ), 'WordPress' ); ?></a>
					<span class="sep"> | </span>
					&copy; ThemeMY! 2012
				</p>
      </footer>

    </div> <!-- /container -->	

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="<?php echo get_template_directory_uri(); ?>/assets/js/jquery.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/assets/js/bootstrap-transition.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/assets/js/bootstrap-alert.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/assets/js/bootstrap-modal.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/assets/js/bootstrap-dropdown.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/assets/js/bootstrap-scrollspy.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/assets/js/bootstrap-tab.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/assets/js/bootstrap-tooltip.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/assets/js/bootstrap-popover.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/assets/js/bootstrap-button.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/assets/js/bootstrap-collapse.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/assets/js/bootstrap-carousel.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/assets/js/bootstrap-typeahead.js"></script>

	<?php wp_footer(); ?>

  </body>
</html>
