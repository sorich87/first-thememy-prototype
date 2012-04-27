<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <title><?php wp_title( '|', true, 'right' ); bloginfo( 'name' ); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <?php wp_head(); ?>

		<style type="text/css">
			article {
				min-height: 600px;
			}
			.page-header h1 {
				font-size: 40px;
			}
			.entry-excerpt p {
				font-size: 20px;
				line-height: 26px;
			}
			.row-social {
				margin: 20px 0 0;
			}
			.span-social {
				margin: 0 10px 0 0;
				width: 80px;
			}
			.carousel .item {
				-webkit-transition: opacity 3s; 
				-moz-transition: opacity 3s;
				-ms-transition: opacity 3s;
				-o-transition: opacity 3s;
				transition: opacity 3s;
			}
			.carousel .active.left {
				left:0;
				opacity:0;
				z-index:2;
			}
			.carousel .next {
				left:0;
				opacity:1;
				z-index:1;
			}
			.btn-xxlarge {
				margin: 60px 0 30px 30px;
				padding: 9px 14px;
				font-size: 30px;
				line-height: normal;
				-webkit-border-radius: 10px;
				-moz-border-radius: 10px;
				border-radius: 10px;
			}
		</style>

		<script type="text/javascript">
			jQuery(function ($) {
				$('#carousel').carousel();
			});
		</script>
  </head>

  <body <?php body_class(); ?>>

		<?php do_action( 'before' ); ?>

    <div class="container">

      <?php while ( have_posts() ) : the_post(); ?>
				<?php $settings = thememy_get_settings( get_the_author_meta( 'ID' ) ); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<div class="row">
						<div class="span6">
							<header class="page-header">
								<h1 class="entry-title">
									<?php the_title(); ?>
									<small><?php printf( __( 'by %s' ), "<a href='{$settings['home-page']}'>{$settings['business-name']}</a>" ); ?></small>
								</h1>
							</header>

							<div class="entry-excerpt">
								<?php the_excerpt(); ?>
							</div>

							<div class="row row-social">
								<div class="span-social">
									<div id="fb-root"></div>
									<script>(function(d, s, id) {
										var js, fjs = d.getElementsByTagName(s)[0];
										if (d.getElementById(id)) return;
										js = d.createElement(s); js.id = id;
										js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
										fjs.parentNode.insertBefore(js, fjs);
									}(document, 'script', 'facebook-jssdk'));</script>
									<div class="fb-like" data-send="false" data-layout="button_count" data-width="450" data-show-faces="false"></div>
								</div>

								<div class="span-social">
									<a href="https://twitter.com/share" class="twitter-share-button" data-via="thememywp">Tweet</a>
									<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
								</div>

								<div class="span-social">
									<script src="//platform.linkedin.com/in.js" type="text/javascript"></script>
									<script type="IN/Share" data-counter="right"></script>
								</div>

								<div class="span-social">
									<div class="g-plusone" data-size="medium"></div>
									<script type="text/javascript">
										(function() {
											var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
											po.src = 'https://apis.google.com/js/plusone.js';
											var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
										})();
									</script>
								</div>

								<div class="span-social">
									<a href="http://pinterest.com/pin/create/button/?url=<?php echo urlencode( get_permalink() ); ?>&media=<?php echo urlencode( wp_get_attachment_url( get_post_thumbnail_id() ) ); ?>&description=<?php echo urlencode( wp_strip_all_tags( get_the_excerpt() ) ); ?>"
										class="pin-it-button" count-layout="horizontal">
										<img border="0" src="//assets.pinterest.com/images/PinExt.png" title="Pin It" />
									</a>
									<script type="text/javascript" src="//assets.pinterest.com/js/pinit.js"></script>
								</div>
							</div>

							<a class="btn btn-success btn-xxlarge span2" href="<?php thememy_purchase_link(); ?>">
								<?php _e( 'Buy Now' ); ?>
							</a>
							<a class="btn btn-primary btn-xxlarge span2" href="<?php thememy_demo_link(); ?>">
								<?php _e( 'Demo' ); ?>
							</a>
						</div>

						<div class="span6">
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

								<?php if ( count( $attachments ) > 1 ) : ?>
									<div id="carousel" class="carousel">
										<div class="carousel-inner">
											<?php foreach ( $attachments as $key => $attachment ) : ?>
												<div class="item<?php echo 0 == $key ? ' active' : ''; ?>">
													<?php echo wp_get_attachment_image( $attachment->ID, 'span6-span6' ); ?>
												</div>
											<?php endforeach; ?>
										</div>
									</div>

								<?php else : ?>
									<span class="thumbnail">
										<?php echo wp_get_attachment_image( $attachments[0]->ID, 'span6-span6' ); ?>
									</span>
								<?php endif; ?>

							<?php else : ?>
								<span class="thumbnail">
									<?php echo wp_get_attachment_image( get_post_thumbnail_id(), 'full' ); ?>
								</span>
							<?php endif; ?>
						</div>
					</div><!-- .row -->
				</article>
			<?php endwhile; ?>

			<hr />

      <footer>
        <?php do_action( 'thememy_credits' ); ?>
        <p>
          Powered by
          <a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php esc_attr_e( 'The easiest way to sell WordPress themes' ); ?>">ThemeMY!</a>
        </p>
      </footer>

    </div> <!-- /container -->

		<?php wp_footer(); ?>

  </body>
</html>
