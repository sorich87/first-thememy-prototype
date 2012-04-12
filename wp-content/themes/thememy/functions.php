<?php
/**
 * ThemeMY! functions and definitions
 *
 * @package thememy
 * @since ThemeMY! 0.1
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 *
 * @since ThemeMY! 0.1
 */
if ( ! isset( $content_width ) )
	$content_width = 640; /* pixels */

if ( ! function_exists( 'thememy_setup' ) ):
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 *
 * @since ThemeMY! 0.1
 */
function thememy_setup() {

	/**
	 * Custom template tags for this theme.
	 */
	require( get_template_directory() . '/inc/template-tags.php' );

	/**
	 * Custom functions that act independently of the theme templates
	 */
	//require( get_template_directory() . '/inc/tweaks.php' );

	/**
	 * Custom Theme Options
	 */
	//require( get_template_directory() . '/inc/theme-options/theme-options.php' );

	/**
	 * WordPress.com-specific functions and definitions
	 */
	//require( get_template_directory() . '/inc/wpcom.php' );

	/**
	 * Make theme available for translation
	 * Translations can be filed in the /languages/ directory
	 * If you're building a theme based on ThemeMY!, use a find and replace
	 * to change 'thememy' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'thememy', get_template_directory() . '/languages' );

	$locale = get_locale();
	$locale_file = get_template_directory() . "/languages/$locale.php";
	if ( is_readable( $locale_file ) )
		require_once( $locale_file );

	/**
	 * Add default posts and comments RSS feed links to head
	 */
	add_theme_support( 'automatic-feed-links' );

	add_theme_support( 'post-thumbnails' );

	/**
	 * This theme uses wp_nav_menu() in one location.
	 */
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'thememy' ),
	) );

	/**
	 * Add support for the Aside and Gallery Post Formats
	 */
	add_theme_support( 'post-formats', array( 'aside', ) );
}
endif; // thememy_setup
add_action( 'after_setup_theme', 'thememy_setup' );

/**
 * Register widgetized area and update sidebar with default widgets
 *
 * @since ThemeMY! 0.1
 */
function thememy_widgets_init() {
	register_sidebar( array(
		'name' => __( 'Sidebar', 'thememy' ),
		'id' => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h1 class="widget-title">',
		'after_title' => '</h1>',
	) );
}
add_action( 'widgets_init', 'thememy_widgets_init' );

/**
 * Enqueue scripts and styles
 *
 * @since ThemeMY! 0.1
 */
function thememy_scripts() {
	global $post;

	wp_enqueue_style( 'style', get_stylesheet_uri() );

	wp_enqueue_script( 'jquery' );

	wp_enqueue_script( 'small-menu', get_template_directory_uri() . '/js/small-menu.js', 'jquery', '20120206', true );

	wp_enqueue_script( 'bootstrap-tab', get_template_directory_uri() . '/assets/js/bootstrap-tab.js', 'jquery', '20120412', true );

	if ( is_page_template( 'reports.php' ) )
		wp_enqueue_script( 'google-jsapi', 'https://www.google.com/jsapi' );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

	if ( is_singular() && wp_attachment_is_image( $post->ID ) )
		wp_enqueue_script( 'keyboard-image-navigation', get_template_directory_uri() . '/js/keyboard-image-navigation.js', array( 'jquery' ), '20120202' );
}
add_action( 'wp_enqueue_scripts', 'thememy_scripts' );

/**
 * Implement the Custom Header feature
 */
//require( get_template_directory() . '/inc/custom-header.php' );

/**
 * Admin bar hides the top navbar. This temporary solution hides the admin bar.
 * The permanent solution would be to fix with css
 *
 * @since ThemeMY! 0.1
 */
add_filter( 'show_admin_bar', '__return_false' );

/**
 * Don't show front page to logged-in users
 * Don't show admin to authors and subscribers
 * Show front page only to non logged-in users
 *
 * @since ThemeMY! 0.1
 */
function thememy_restrict_pages() {
	if ( is_user_logged_in() && current_user_can( 'edit_posts' ) ) {
		if ( is_front_page() && empty( $_GET['buy'] ) && empty( $_GET['buy-all'] ) ) {
			wp_redirect( site_url( 'themes/' ) );
			exit;

		} elseif ( is_admin() && ! current_user_can( 'edit_other_posts' ) ) {
			global $plugin_page;

			if ( 'td-admin' != $plugin_page || empty( $_FILES['themezip'] ) ) {
				wp_redirect( site_url( 'themes/' ) );
				exit;
			}
		}

	} else {
		if ( ! is_front_page() && ! is_page_template( 'store-page.php' ) && ! is_page_template( 'download-page.php' ) && ! is_page( 'api' ) ) {
			wp_redirect( home_url( '/' ) );
			exit;
		}
	}
}
add_action( 'template_redirect', 'thememy_restrict_pages' );

/**
 * Send feedback to site admin
 *
 * @since ThemeMY! 0.1
 */
function thememy_send_feedback() {
	if ( ! is_page( 'feedback' ) || ! isset( $_POST['message'] ) )
		return;

	if ( empty( $_POST['message'] ) ) {
		wp_redirect( site_url( 'feedback/?message=2' ) );
		exit;
	}

	$current_user = wp_get_current_user();

	$to = get_option( 'admin_email' );
	$subject = sprintf( __( 'Feedback from %s' ), $current_user->display_name );
	$message = $_POST['message'];

	wp_mail( $to, $subject, $message );

	wp_redirect( site_url( 'feedback/?message=1' ) );
	exit;
}
add_action( 'template_redirect', 'thememy_send_feedback' );

/**
 * Save user settings
 *
 * @since ThemeMY! 0.1
 */
function thememy_save_settings() {
	if ( ! is_page( 'settings' ) || empty( $_POST ) )
		return;

	$data = stripslashes_deep( $_POST );

	if ( empty( $data['business-name'] ) || empty( $data['business-email'] )
		|| empty( $data['price-one'] ) || empty( $data['price-all'] )
		|| empty( $data['paypal-email'] ) ) {
		wp_redirect( site_url( 'settings/?message=2' ) );
		exit;
	}

	$user_id = get_current_user_id();

	update_user_meta( $user_id, '_thememy_settings', $data );

	wp_redirect( site_url( 'settings/?message=1' ) );
	exit;
}
add_action( 'template_redirect', 'thememy_save_settings' );

/**
 * Get settings array
 *
 * @since ThemeMY! 0.1
 *
 * @param int $author_id Author ID
 */
function thememy_get_settings( $author_id = null ) {
	if ( ! $author_id )
		$author_id = get_current_user_id();

	return get_user_meta( $author_id, '_thememy_settings', true );
}

/**
 * Redirect to paypal for purchase
 *
 * @since ThemeMY! 0.1
 */
function thememy_redirect_to_paypal() {
	if ( ! is_front_page() || empty( $_GET['buy'] ) )
		return;

	$theme = get_post( $_GET['buy'] );
	$settings = thememy_get_settings( $theme->post_author );
	
	if ( empty( $settings ) )
		thememy_error( json_encode( array( 'seller' => $theme->post_author, 'error' => 10001 ) ) );

	if ( empty( $settings['test-mode'] ) )
		$paypal_host = 'paypal.com';
	else
		$paypal_host = 'sandbox.paypal.com';

	$api_endpoint = "https://svcs.{$paypal_host}/AdaptivePayments/Pay";

	$args = array(
		'headers' => array(
			'X-PAYPAL-SECURITY-USERID'      => 'sorich_1322676683_biz_api1.gmail.com',
			'X-PAYPAL-SECURITY-PASSWORD'    => '1322676719',
			'X-PAYPAL-SECURITY-SIGNATURE'   => 'AFFkJs.IxAtULlzaWU9t3xWHoNVwANpHE1q68lz-vmgRJKoN6THqZ7sY',
			'X-PAYPAL-REQUEST-DATA-FORMAT'  => 'JSON',
			'X-PAYPAL-RESPONSE-DATA-FORMAT' => 'JSON',
			'X-PAYPAL-APPLICATION-ID'       => 'APP-80W284485P519543T',
			'X-PAYPAL-DEVICE-IPADDRESS'     => '127.0.0.1'
		),
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
			'ipnNotificationUrl' => add_query_arg( 'item', $theme->ID, site_url() ),
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
function thememy_register_post_type() {
	$args = array(
		'labels' => array(
			'name' => __( 'Orders' ),
			'singular_name' => __( 'Order' )
		),
		'public' => false,
    'show_ui' => true,
		'map_meta_cap' => true
	);
	register_post_type( 'thememy_order', $args );

	$args = array(
		'labels' => array(
			'name' => __( 'Logs' ),
			'singular_name' => __( 'Log' )
		),
		'public' => false,
    'show_ui' => true
	);
	register_post_type( 'thememy_log', $args );
}
add_action( 'init', 'thememy_register_post_type' );

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

	$args = array(
		'post_type'   => 'thememy_order',
		'post_author' => $theme->post_author,
		'post_title'  => sprintf( __( 'Purchase %s' ), wp_hash( $data['paykey'] ) ),
		'post_status' => 'publish'
	);
	$order_id = wp_insert_post( $args );

	if ( ! $order_id )
		return 0;

	update_post_meta( $order_id, '_thememy_transaction', $data['transaction'][0] );
	update_post_meta( $order_id, '_thememy_buyer', $data['sender_email'] );
	update_post_meta( $order_id, '_thememy_item', $item_id );
	update_post_meta( $order_id, '_thememy_amount', $settings['price-one'] );
	update_post_meta( $order_id, '_thememy_email', $settings['paypal-email'] );
	update_post_meta( $order_id, '_thememy_paykey', $data['paykey'] );
	update_post_meta( $order_id, '_thememy_type', $type );

	return $order_id;
}

/**
 * Get order by paykey
 *
 * @since ThemeMY! 0.1
 *
 * @param string $paykey Paypal payKey
 * @param string Order status
 */
function thememy_get_order( $paykey ) {
	$args = array(
		'post_type'   => 'thememy_order',
		'meta_key'    => '_thememy_paykey',
		'meta_value'  => $paykey
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

	$counts = $wpdb->get_results( $wpdb->prepare(
		"SELECT DATE(post_date) AS date, COUNT(ID) AS count FROM $wpdb->posts WHERE post_author = %d AND post_type = 'thememy_order' and post_status = 'publish' GROUP BY date",
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
 * Process order
 *
 * @since ThemeMY! 0.1
 */
function thememy_process_order() {
	if ( ! isset( $_POST['transaction_type'] ) )
		return;

	$data = stripslashes_deep( $_POST );

	if ( 'Adaptive Payment PAY' != $data['transaction_type'] || 'COMPLETED' != $data['status'] )
		return;

	// Check that the order has not been previously processed
	$order = thememy_get_order( $data['paykey'] );
	if ( $order )
		return;

	// Add 'cmd' and post back to PayPal to validate

	$data['cmd'] = '_notify-validate';

	$theme = get_post( $_GET['item'] );
	$settings = get_settings( $theme->post_author );

	if ( empty( $settings['test-mode'] ) )
		$paypal_host = 'paypal.com';
	else
		$paypal_host = 'sandbox.paypal.com';

	$response = wp_remote_post( "https://www.{$paypal_host}", array( 'body' => $data ) );

	if ( is_wp_error( $response ) )
		thememy_error( $response, false );

	$result = wp_remote_retrieve_body( $response );

	// Process result

	if ( strcmp( $result, 'VERIFIED' ) == 0 ) {
		$transaction = $data['transaction'][0];

		// Check that receiver email is the author PayPal email and payment amount is correct
		if ( $settings['paypal-email'] != $transaction.receiver || $settings['price-one'] != $transaction.amount )
			thememy_error( $response, false );

		$order_id = thememy_create_order( $data, $theme->ID );

		thememy_assign_theme( $data['sender_email'], $theme->ID );
		thememy_send_download_email( $data['sender_email'], $order->ID );

	} elseif ( strcmp( $results, 'INVALID' ) == 0 ) {
		thememy_error( $response, false );
	}
}
add_action( 'init', 'thememy_process_order' );

/**
 * Assign theme to a buyer profile
 *
 * @since ThemeMY! 0.1
 *
 * @param string $email Buyer email
 * @param int $theme_id Theme ID
 */
function thememy_assign_theme( $email, $theme_id ) {
	$buyer_id = get_user_by( 'email', $email )->ID;

	if ( ! $buyer_id )
		$buyer_id = wp_create_user( wp_hash( $email ), wp_generate_password(), $email );

	$themes = thememy_get_themes( $buyer_id );

	if ( ! in_array( $theme_id, $themes ) )
		add_user_meta( $buyer_id, '_thememy_themes', $theme_id );
}

/**
 * Get all the themes assigned to a buyer
 *
 * @since ThemeMY! 0.1
 *
 * @param int $buyer Buyer email or ID
 */
function thememy_get_themes( $buyer ) {
	if ( is_int( $buyer ) )
		$buyer_id = $buyer;
	elseif ( is_email( $buyer ) )
		$buyer_id = get_user_by( 'email', $buyer )->ID;
	else
		return;

	return get_user_meta( $buyer_id, '_thememy_themes' );
}

/**
 * Serve private files for download from S3
 *
 * @since ThemeMY! 0.1
 */
function thememy_get_attachment_url( $url, $post_id ) {
	if ( get_post_meta( $post_id, '_s3_acl', true ) != 'authenticated-read' )
		return $url;

	require_once( WP_PLUGIN_DIR . '/tantan-s3/wordpress-s3/lib.s3.php' );

	$s3_config = get_option('tantan_wordpress_s3');

	if ( $s3_config['wp-uploads'] && ( $amazon = get_post_meta( $post_id, 'amazonS3_info', true ) ) ) {
		$domain = ! empty( $s3_config['virtual-host'] ) ? $amazon['bucket'] : "{$amazon['bucket']}.s3.amazonaws.com";

		$s3 = new TanTanS3( $s3_config['key'], $s3_config['secret'] );

		$expires = strtotime( '+1 hour' );
		$string_to_sign = "GET\n\n\n$expires\n/{$amazon['bucket']}/{$amazon['key']}";
		$signature = $s3->constructSig( $string_to_sign );

		$url = add_query_arg( array(
			'AWSAccessKeyId' => $s3_config['key'],
			'Expires'        => $expires,
			'Signature'      => urlencode( $signature )
		), "http://{$domain}/{$amazon['key']}" );

		return $url;
	}

	return $url;
}
add_action( 'wp_get_attachment_url', 'thememy_get_attachment_url', 10, 2 );

/**
 * Send theme download email to a buyer
 *
 * @since ThemeMY! 0.1
 *
 * @param string $email Buyer email
 * @param int $order_id Order ID
 */
function thememy_send_download_email( $email, $order_id ) {
	$order = get_post( $order_id );
	$settings = thememy_get_settings( $order->post_author );

	$args = array(
		'order' => $order->ID,
		'key'   => wp_hash( $email )
	);
	$download_page = add_query_arg( $args, site_url( 'download/' ) );

	$headers = array(
		"From: {$settings['business-email']}"
	);

	$subject = __( 'Download your new theme' );

	$message = sprintf( __( 'Thanks for your purchase. Your payment has been received.

To download your theme, go to:
%1$s

If you need assistance, please feel free to email %2$s.

Sincerely,
%3$s
%4$s' ),
		$download_page,
		$settings['business-email'],
		$settings['business-name'],
		$settings['home-page']
	);

	wp_mail( $email, $subject, $message, $headers );
}

/**
 * Display error message and log error
 *
 * @since ThemeMY! 0.1
 *
 * @param mixed $data Data to log
 * @param bool $die Whether to display error message or not
 */
function thememy_error( $data, $die = true ) {
  $data = json_encode( $data );

	$args = array(
		'post_type'    => 'thememy_log',
		'post_author'  => 1,
		'post_title'   => wp_hash( $data ),
		'post_content' => $data
	);
	wp_insert_post( $args );

	if ( $die )
		wp_die( __( '<h1>OMG! We broke something!</h1> <p>We have been notified and will fix it ASAP. We apologize for the inconvenience.<br /> <b>Please try again later.</b></p>' ) );
}

/**
 * Get theme details in an array for API usage
 *
 * @since ThemeMY! 0.1
 */
function thememy_api_theme_details( $theme_id ) {
	$theme = get_post( $theme_id );

	if ( ! $theme )
		return;

	$theme_data = td_get_theme_data( $theme_id );

	$versions = td_get_all_versions( $theme_id );
	foreach ( $versions as $version => $version_data ) {
		$versions[$version] = array(
			'version' => $version,
			'URI'     => $version_data['URI']
		);
	}

	$details = array(
		'package'     => td_get_download_link( $theme_id ),
		'new_version' => td_get_current_version( $theme_id ),
		'url'         => $theme_data['URI'],
		'versions'    => $versions
	);

	return $details;
}

/**
 * Process API requests
 *
 * @since ThemeMY! 0.1
 */
function thememy_process_api_requests() {
	if ( ! is_page( 'api' ) || empty( $_REQUEST['action'] ) )
		return;

	$request = stripslashes_deep( $_REQUEST );

	if ( 'theme_update' != $request['action'] || wp_hash( $request['email'] ) != $request['api_key'] )
		return;

	// get details of themes purchased by the user
	$themes = thememy_get_themes( $request['email'] );
	if ( ! $themes )
		return;

	$themes = get_posts( array( 'post__in' => $themes, 'post_type' => 'td_theme' ) );
	if ( ! $themes )
		return;

	foreach ( $themes as $theme ) {
		$available[$theme->post_title] = thememy_api_theme_details( $theme->ID );
	}

	// loop through the user's installed themes and match themes names and URIs
	$installed = json_decode( $request['themes'] );
	foreach ( $installed as $slug => $theme ) {
		$theme_name = $theme['Name'];
		if ( ! isset( $available[$name] ) )
			continue;

		$match = $available[$name];
		$theme_version = $theme['Version'];

		if ( ! isset( $match['versions'][$theme_version] ) || $match['versions'][$theme_version]['URI'] != $theme['URI'] )
			continue;

		$new[$slug] = array(
			'package'     => $match['package'],
			'new_version' => $match['new_version'],
			'url'         => $match['url']
		);
	}

	echo json_encode( $new );	

	exit;
}
add_action( 'template_redirect', 'thememy_process_api_requests' );

/**
 * Send installation request email to ThemeMY! admin
 *
 * @since ThemeMY! 0.1
 */
function thememy_install_request() {
	if ( ! is_page_template( 'download-page.php' ) || empty( $_POST ) )
		return;

	$to = get_option( 'admin_email' );
	$subject = __( 'Theme Installation Request' );
	$message = json_encode( stripslashes_deep( $_POST ) );

	wp_email( $to, $subject, $message );

	wp_redirect( add_query_arg( 'message', '1' ) );
	die;
}
add_action( 'template_redirect', 'thememy_install_request' );

/**
 * Echo ThemeMY! plugin download link
 *
 * @since ThemeMY! 0.1
 */
function thememy_plugin_download_link() {
	echo '';
}

