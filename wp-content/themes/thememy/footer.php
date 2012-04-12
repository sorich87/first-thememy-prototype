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
          Copyright &copy; 2012
          <a href="<?php esc_url( home_url( '/' ) ); ?>" title="<?php esc_attr_e( 'The easiest way to sell WordPress themes' ); ?>">ThemeMY!</a>
        </p>
      </footer>

    </div> <!-- /container -->	

	<?php wp_footer(); ?>

  </body>
</html>
