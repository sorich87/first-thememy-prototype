<?php
/**
 * Template Name: Reports
 *
 * @package thememy
 * @since ThemeMY! 0.1
 */

get_header(); ?>

	<div class="page-header">
		<h1><?php _e( 'Your Sales Reports' ); ?></h1>
	</div>

	<?php $settings = thememy_get_settings(); ?>

	<?php if ( ! empty( $settings['test-mode'] ) ) : ?>
		<div class="alert alert-error">
			<?php _e( 'Your store is in test mode. The orders you made for testing will be displayed here.' ); ?>
		</div>
	<?php endif; ?>

	<div class="alert alert-info">
		<?php printf( __(
			'We are working on more detailed reports and would like to hear about what you want to see on this page. Please <a href="%s">send us your feedback</a>.' ),
			site_url( 'feedback/' )
		); ?>
	</div>

	<?php if ( $count = thememy_count_orders() ) : ?>

		<div id="chart"></div>

		<script type="text/javascript">

		// Load the Visualization API and the piechart package.
		google.load('visualization', '1.0', {'packages':['corechart']});

		// Set a callback to run when the Google Visualization API is loaded.
		google.setOnLoadCallback(drawChart);

		// Callback that creates and populates a data table,
		// instantiates the pie chart, passes in the data and
		// draws it.
		function drawChart() {

			// Create the data table.
			var data = new google.visualization.DataTable();
			data.addColumn('string', 'Day');
			data.addColumn('number', 'Sales');
			data.addRows(<?php echo json_encode( $count ); ?>);

			// Set chart options
			var options = {
				title : "Sales for the last 30 days"
			};

			// Instantiate and draw our chart, passing in some options.
			var chart = new google.visualization.LineChart(document.getElementById('chart'));
			chart.draw(data, options);
		}
		</script>

	<?php else : ?>

		<p><?php _e( 'No sales yet. Have you already published the purchase links on your website? If yes, check back later.' ); ?></p>

	<?php endif; ?>

	<?php
	$args = array(
		'post_type' => 'thememy_order',
		'author' => get_current_user_id(),
		'post_status' => empty( $settings['test-mode'] ) ? 'publish' : array( 'publish', 'private' )
	);
	$orders = get_posts( $args );
	?>
	<?php if ( $orders ) : ?>
	<table class="table table-striped table-condensed">
		<thead>
			<tr>
				<th>#</th>
				<th><?php _e( 'Date' ); ?></th>
				<th><?php _e( 'Buyer Email' ); ?></th>
				<th><?php _e( 'Theme' ); ?></th>
				<th><?php _e( 'Download Page' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $orders as $order ) : ?>
			<tr>
				<td>
					<?php echo $order->ID; ?>
					<?php if ( ! empty( $settings['test-mode'] ) && 'private' == $order->post_status ) : ?>
						<?php echo '<span class="badge badge-info">' . __( 'test' ) . '</span>'; ?>
					<?php endif; ?>
				</td>
				<td><?php echo mysql2date( 'd-m-Y', $order->post_date ); ?></td>
				<td><?php echo get_post_meta( $order->ID, '_thememy_buyer', true ); ?></td>
				<?php
				$theme_id = get_post_meta( $order->ID, '_thememy_item', true );
				$theme = get_post( $theme_id );
				?>
				<td><?php echo $theme ? $theme->post_title : ''; ?></td>
				<td><?php echo thememy_theme_download_page( $order->ID ); ?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php endif; ?>

<?php get_footer(); ?>
