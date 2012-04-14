<?php
/**
 * Template Name: Reports
 *
 * @package thememy
 * @since ThemeMY! 0.1
 */

get_header(); ?>

	<div class="page-header">
		<h1><?php _e( 'Sales Reports' ); ?></h1>
	</div>

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

<?php get_footer(); ?>
