<?php

/**
 * Redirect to paypal for purchase
 *
 * @since ThemeMY! 0.1
 */
function thememy_redirect_to_paypal() {
	if ( ! is_singular() || get_post_type() != 'td_theme' || ! get_query_var( 'buy' ) )
		return;

	$theme = get_queried_object();
	$settings = thememy_get_settings( $theme->post_author );
	
	if ( empty( $settings ) )
		thememy_error( json_encode( array( 'seller' => $theme->post_author, 'error' => 10001 ) ) );

	if ( empty( $settings['test-mode'] ) ) {
		$paypal_host = 'paypal.com';

		$headers = array(
			'X-PAYPAL-SECURITY-USERID'      => X_PAYPAL_SECURITY_USERID,
			'X-PAYPAL-SECURITY-PASSWORD'    => X_PAYPAL_SECURITY_PASSWORD,
			'X-PAYPAL-SECURITY-SIGNATURE'   => X_PAYPAL_SECURITY_SIGNATURE,
			'X-PAYPAL-APPLICATION-ID'       => X_PAYPAL_APPLICATION_ID,
			'X-PAYPAL-DEVICE-IPADDRESS'     => $_SERVER['REMOTE_ADDR'],
			'X-PAYPAL-REQUEST-DATA-FORMAT'  => 'JSON',
			'X-PAYPAL-RESPONSE-DATA-FORMAT' => 'JSON'
		);
	} else {
		$paypal_host = 'sandbox.paypal.com';

		$headers = array(
			'X-PAYPAL-SECURITY-USERID'      => 'sorich_1322676683_biz_api1.gmail.com',
			'X-PAYPAL-SECURITY-PASSWORD'    => '1322676719',
			'X-PAYPAL-SECURITY-SIGNATURE'   => 'AFFkJs.IxAtULlzaWU9t3xWHoNVwANpHE1q68lz-vmgRJKoN6THqZ7sY',
			'X-PAYPAL-APPLICATION-ID'       => 'APP-80W284485P519543T',
			'X-PAYPAL-DEVICE-IPADDRESS'     => '127.0.0.1',
			'X-PAYPAL-REQUEST-DATA-FORMAT'  => 'JSON',
			'X-PAYPAL-RESPONSE-DATA-FORMAT' => 'JSON'
		);
	}

	$api_endpoint = "https://svcs.{$paypal_host}/AdaptivePayments/Pay";

	$args = array(
		'headers' => $headers,
		'body' => json_encode( array(
			'returnUrl' => $settings['return-page'],
			'cancelUrl' => $settings['cancel-page'],
			'requestEnvelope' => array(
				'errorLanguage' => 'en_US'
			),
			'currencyCode' => 'USD',
			'receiverList' => array(
				'receiver' => array(
					array(
						'email'       => $settings['paypal-email'],
						'amount'      => number_format( $settings['price-one'], 2 ),
						'paymentType' => 'DIGITALGOODS'
					)
				)
			),
			'actionType' => 'PAY',
			'trackingId' => microtime(),
			'ipnNotificationUrl' => add_query_arg( 'item', $theme->ID, site_url( 'ipn/' ) ),
			'memo' => sprintf( __( 'Payment for %s' ), $theme->post_title )
		) )
	);

	$response = wp_remote_post( $api_endpoint, $args );

	if ( is_wp_error( $response ) )
		thememy_error( $response );

	$result = json_decode( wp_remote_retrieve_body( $response ) );

	if ( empty( $result->payKey ) )
		thememy_error( $response );

	wp_redirect( add_query_arg( 'paykey', $result->payKey, "https://www.{$paypal_host}/webapps/adaptivepayment/flow/pay" ) );
	exit;
}
add_action( 'template_redirect', 'thememy_redirect_to_paypal' );

/**
 * Register order post type
 *
 * @since ThemeMY! 0.1
 */
function thememy_order_post_type() {
	$args = array(
		'labels' => array(
			'name' => __( 'Orders' ),
			'singular_name' => __( 'Order' )
		),
		'public' => false,
    'show_ui' => true,
		'map_meta_cap' => true,
		'supports' => array( 'title', 'editor', 'author' )
	);
	register_post_type( 'thememy_order', $args );
}
add_action( 'init', 'thememy_order_post_type' );

/**
 * Create a new order
 *
 * @since ThemeMY! 0.1
 *
 * @param int|string $item_id Theme ID or package ID
 * @param string $paykey Paypal payKey
 * @param string $type theme or package
 */
function thememy_create_order( $data, $item_id, $type = 'theme' ) {
	$theme = get_post( $item_id );
	$settings = thememy_get_settings( $theme->post_author );

	if ( empty( $settings['test-mode'] ) )
		$status = 'publish';
	else
		$status = 'private';

	$args = array(
		'post_type'   => 'thememy_order',
		'post_author' => $theme->post_author,
		'post_title'  => sprintf( __( 'Purchase %s' ), wp_hash( $data['paykey'] ) ),
		'post_status' => $status
	);
	$order_id = wp_insert_post( $args );

	if ( ! $order_id )
		return 0;

	update_post_meta( $order_id, '_thememy_buyer', $data['sender_email'] );
	update_post_meta( $order_id, '_thememy_item', $item_id );
	update_post_meta( $order_id, '_thememy_amount', $settings['price-one'] );
	update_post_meta( $order_id, '_thememy_email', $settings['paypal-email'] );
	update_post_meta( $order_id, '_thememy_trackingid', $data['trackingId'] );
	update_post_meta( $order_id, '_thememy_type', $type );

	return $order_id;
}

/**
 * Get order by trackingId
 *
 * @since ThemeMY! 0.1
 *
 * @param string $trackingid Paypal trackingId
 * @param string Order status
 */
function thememy_get_order( $trackingid ) {
	$args = array(
		'post_type'   => 'thememy_order',
		'meta_key'    => '_thememy_trackingid',
		'meta_value'  => $trackingid
	);
	$result = get_posts( $args );

	if ( $result )
		return $result[0];
}

/**
 * Get orders grouped by date
 *
 * @since ThemeMY! 0.1
 *
 * @param int $author_id Theme author ID
 */
function thememy_count_orders( $author_id = null ) {
	global $wpdb;

	if ( empty( $author_id ) )
		$author_id = get_current_user_id();

	$settings = thememy_get_settings( $author_id );

	$status = array( "post_status = 'publish'" );
	if ( ! empty( $settings['test-mode'] ) )
		$status[] = "post_status = 'private'";

	$counts = $wpdb->get_results( $wpdb->prepare(
		"SELECT DATE(post_date) AS date, COUNT(ID) AS count FROM $wpdb->posts WHERE post_author = %d AND post_type = 'thememy_order' AND (" . join( ' OR ', $status ) . ") GROUP BY date",
		$author_id
	), OBJECT_K );

	if ( ! $counts )
		return;

	$start = strtotime( '-30 days' );
	$today = time();
	$days = array();
	for ( $i = 0; $i <= 30; $i++ ) {
		$timestamp = $start + 3600 * 24 * $i;
		$day = date( 'Y-m-d', $timestamp );
		$count = isset( $counts[$day] ) ? (int) $counts[$day]->count : 0;
		$days[] = array( date( 'd', $timestamp ), $count );
	}

	return $days;
}

/**
 * Decode Paypal IPN request into an associative multi-dimensional array
 *
 * @since ThemeMY! 0.1
 *
 * @param string $raw_post IPN request string
 */
function thememy_decode_ipn( $raw_post ) {
	if ( empty( $raw_post ) )
		return array();

	$post = array();
	$pairs = explode( '&', $raw_post );
	foreach ( $pairs as $pair ) {
		list( $key, $value ) = explode( '=', $pair, 2 );
		$key = urldecode( $key );
		$value = urldecode( $value );

		// This is look for a key as simple as 'return_url' or as complex as 'somekey[x].property'
		preg_match( '/(\w+)(?:\[(\d+)\])?(?:\.(\w+))?/', $key, $key_parts );

		switch ( count( $key_parts ) ) {
			case 4:
				// Original key format: somekey[x].property
				// Converting to $post[somekey][x][property]
				if ( ! isset( $post[$key_parts[1]] ) ) {
					$post[$key_parts[1]] = array(
						$key_parts[2] => array(
							$key_parts[3] => $value
						)
					);
				} else if ( ! isset( $post[$key_parts[1]][$key_parts[2]] ) ) {
					$post[$key_parts[1]][$key_parts[2]] = array( $key_parts[3] => $value );
				} else {
					$post[$key_parts[1]][$key_parts[2]][$key_parts[3]] = $value;
				}
				break;

			case 3:
				// Original key format: somekey[x]
				// Converting to $post[somkey][x] 
				if ( ! isset( $post[$key_parts[1]] ) )
					$post[$key_parts[1]] = array();

				$post[$key_parts[1]][$key_parts[2]] = $value;
				break;

			default:
				// No special format
				$post[$key] = $value;
				break;
		}
	}

	return $post;
}

/**
 * Process order
 *
 * @since ThemeMY! 0.1
 */
function thememy_process_order() {
	if ( ! is_page( 'ipn' ) || empty( $_POST ) )
		return;

	$raw_post = file_get_contents( 'php://input' );
	$data = thememy_decode_ipn( $raw_post );

	if ( 'Adaptive Payment PAY' != $data['transaction_type'] || 'COMPLETED' != $data['status'] )
		return;

	// Check that the order has not yet been processed
	$order = thememy_get_order( $data['trackingId'] );
	if ( $order )
		return;

	// Add 'cmd' and post back to PayPal to validate

	$theme = get_post( $_GET['item'] );
	$settings = thememy_get_settings( $theme->post_author );

	if ( empty( $settings['test-mode'] ) )
		$paypal_host = 'paypal.com';
	else
		$paypal_host = 'sandbox.paypal.com';

	$req = 'cmd=_notify-validate&' . $raw_post;

	$response = wp_remote_post( "https://www.{$paypal_host}/cgi-bin/webscr", array( 'body' => $req ) );

	if ( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) ) {
		thememy_error( $response, false );
		return;
	}

	$result = wp_remote_retrieve_body( $response );

	if ( strcmp( $result, 'VERIFIED' ) == 0 ) {
		// Check that payment amount and receiver email are correct
		$transaction = $data['transaction'][0];
		$amount = 'USD ' . number_format( $settings['price-one'], 2 );

		if ( $settings['paypal-email'] == $transaction['receiver'] && $amount == $transaction['amount'] ) {
			$order_id = thememy_create_order( $data, $theme->ID );

			thememy_assign_theme( $data['sender_email'], $theme->ID );
			thememy_send_download_email( $order_id );

			exit;
		}
	}

	thememy_error( $data, false );
	exit;
}
add_action( 'template_redirect', 'thememy_process_order' );

/**
 * Send theme download email to a buyer
 *
 * @since ThemeMY! 0.1
 *
 * @param string $email Buyer email
 * @param int $order_id Order ID
 */
function thememy_send_download_email( $order_id ) {
	$order = get_post( $order_id );
	$settings = thememy_get_settings( $order->post_author );
	$email = get_post_meta( $order->ID, '_thememy_buyer', true );

	$subject = __( 'Download your new theme' );

	$message = sprintf( __( 'Thanks for your purchase. Your payment has been received.

To download your theme, go to:
%1$s

If you need assistance, please feel free to email %2$s.

Sincerely,
%3$s
%4$s' ),
		thememy_theme_download_page( $order_id ),
		$settings['business-email'],
		$settings['business-name'],
		$settings['home-page']
	);

	if ( empty( $settings['test-mode'] ) )
		wp_mail( $email, $subject, $message, $headers );
	else
		wp_mail( $settings['business-email'], $subject, $message, $headers );
}

/**
 * Get theme download page URL
 *
 * @since ThemeMY! 0.1
 *
 * @param int $order_id Order ID
 */
function thememy_theme_download_page( $order_id ) {
	$order = get_post( $order_id );
	$email = get_post_meta( $order->ID, '_thememy_buyer', true );

	$args = array(
		'order' => $order->ID,
		'key'   => wp_hash( $email )
	);
	return add_query_arg( $args, site_url( 'download/' ) );
}

/**
 * Display dump of post meta on order edit screen
 *
 * @since ThemeMY! 0.1
 */
function thememy_display_meta_dump() {
	global $post;

	if ( 'thememy_order' != $post->post_type )
		return;

	$custom_fields = get_post_custom( $post->ID );
?>

	<h3><?php _e( 'Metadata' ); ?></h3>
	<ul>
	<?php foreach ( $custom_fields as $key => $values ) : ?>
		<?php if ( strpos( $key, 'thememy' ) === false ) continue; ?>
		<li>
			<b><?php echo $key; ?></b><br />
			<?php
			foreach ( $values as $i => $value ) {
				if ( is_string( $value ) ) {
					$values[$i] = $value;
				} else {
					$values[$i] = json_encode( $value );
				}
			}
			echo implode( '<br />', $values );
			?>
		</li>
	<?php endforeach; ?>
	</ul>

	<h3><?php _e( 'Download Page' ); ?></h3>
	<?php echo thememy_theme_download_page( $post->ID ); ?>
<?php
}
add_action( 'dbx_post_sidebar', 'thememy_display_meta_dump' );

