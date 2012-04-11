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

	if ( is_page_template( 'reports.php' ) )
		wp_enqueue_script( 'google-jsapi', 'https://www.google.com/jsapi' );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	if ( is_singular() && wp_attachment_is_image( $post->ID ) ) {
		wp_enqueue_script( 'keyboard-image-navigation', get_template_directory_uri() . '/js/keyboard-image-navigation.js', array( 'jquery' ), '20120202' );
	}
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
	if ( is_user_logged_in() ) {
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
		if ( ! is_front_page() && ! is_page_template( 'store-page.php' ) ) {
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

	update_user_meta( $user_id, 'thememy_settings', $data );

	wp_redirect( site_url( 'settings/?message=1' ) );
	exit;
}
add_action( 'template_redirect', 'thememy_save_settings' );

/**
 * Redirect to paypal for purchase
 *
 * @since ThemeMY! 0.1
 */
function thememy_redirect_to_paypal() {
	if ( ! is_front_page() || empty( $_GET['buy'] ) )
		return;

	$theme = get_post( $_GET['buy'] );
	$settings = get_user_meta( $theme->post_author, 'thememy_settings', true );
	
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
			'ipnNotificationUrl' => site_url(),
			'memo' => sprintf( __( 'Payment for %s' ), $theme->post_title )
		) )
	);

	$response = wp_remote_post( $api_endpoint, $args );

	if ( is_wp_error( $response ) )
		thememy_error( $response );

	$result = json_decode( wp_remote_retrieve_body( $response ) );

	if ( empty( $result->payKey ) )
		thememy_error( $response );

	thememy_create_order( $result->payKey, $theme->ID );

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
		'map_meta_cap' => true
	);
	register_post_type( 'thememy_order', $args );

	$args = array(
		'labels' => array(
			'name' => __( 'Logs' ),
			'singular_name' => __( 'Log' )
		),
		'public' => false
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
function thememy_create_order( $paykey, $item_id, $type = 'theme' ) {
	$theme = get_post( $item_id );
	$settings = get_user_meta( $theme->post_author, 'thememy_settings', true );

	$args = array(
		'post_type'   => 'thememy_order',
		'post_author' => $theme->post_author,
		'post_title'  => sprintf( __( 'Purchase %s' ), wp_hash( $paykey ) )
	);
	$order_id = wp_insert_post( $args );

	if ( ! $order_id )
		return 0;

	update_post_meta( $order_id, '_thememy_item', $item_id );
	update_post_meta( $order_id, '_thememy_amount', $settings['price-one'] );
	update_post_meta( $order_id, '_thememy_email', $settings['paypal-email'] );
	update_post_meta( $order_id, '_thememy_paykey', $paykey );
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
function thememy_get_order( $paykey, $status = array( 'publish', 'draft' ) ) {
	$args = array(
		'post_type'   => 'thememy_order',
		'post_status' => $status,
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
	$order = thememy_get_order( $data['paykey'], 'draft' );
	if ( ! $order )
		return;

	// Add 'cmd' and post back to PayPal to validate

	$data['cmd'] = '_notify-validate';

	if ( empty( $settings['test-mode'] ) )
		$paypal_host = 'paypal.com';
	else
		$paypal_host = 'sandbox.paypal.com';

	$response = wp_remote_post( "ssl://www.{$paypal_host}", array( 'body' => $data ) );

	if ( is_wp_error( $response ) )
		thememy_error( $response, false );

	$result = wp_remote_retrieve_body( $response );

	// Process result

	if ( strcmp( $result, 'VERIFIED' ) == 0 ) {
		$amount = get_post_meta( $order->ID, '_thememy_amount', true );
		$email = get_post_meta( $order->ID, '_thememy_email', true );
		$transaction = $data['transaction'][0];

		// Check that receiver email is the author PayPal email and payment amount is correct
		if ( $email != $transaction.receiver || $amount != $transaction.amount )
			thememy_error( $response, false );

		$order->post_status = 'publish';
		wp_update_post( $order );
		update_post_meta( $order->ID, '_thememy_transaction', $transaction );

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

	$themes = get_user_meta( $buyer_id, '_thememy_themes' );

	if ( ! in_array( $theme_id, $themes ) )
		update_user_meta( $buyer_id, '_thememy_themes', $theme_id );
}

/**
 * Send theme download email to a buyer
 *
 * @since ThemeMY! 0.1
 *
 * @param string $email Buyer email
 * @param int $order_id Order ID
 */
function thememy_send_download_email( $email, $order_id ) {
	$theme = get_post( $theme_id );
	$settings = get_user_meta( $theme->post_author, 'thememy_settings', true );

	$args = array(
		'order' => $order_id,
		'key'   => wp_hash( $email )
	);
	$download_link = add_query_arg( $args, td_get_download_link( $theme->ID ) );
	$install_link = add_query_arg( $args, site_url( 'install' ) );

	$headers = array(
		"From: {$settings['business-email']}"
	);

	$subject = __( 'Here is your new theme' );

	$message = sprintf( __( 'Thanks for your purchase.

You have ordered the theme %1$s and your payment has been received.

You can now download your theme from:
%2$s

Alternatively you can request it to be installed for you on your website:
%3$s

If you need assistance, please feel free to email %4$s

Sincerely,
%5$s
%6$s' ),
		$theme->post_title,
		$download_link,
		$install_link,
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
	if ( is_array( $data ) )
		json_encode( $data );

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

	$current_version = td_get_current_version( $theme_id );
	$theme_data = td_get_theme_data( $theme_id );

	$details = array(
		'versions' => array(
			$current_version => array(
				'version' => $current_version,
				'date' => mysql2date( 'Y-m-d', $theme->post_date ),
				'package' => td_get_download_link( $theme_id )
			)
		),
		'info' => array(
			'url' => $theme_data['URI']
		)
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
	var_dump( thememy_api_theme_details( 1022 ) );
	die;

	extract( stripslashes_deep( $_POST ) );

	if ( is_array( $request ) )
		$request = (object) $request;

	$theme_details = thememy_api_theme_details();

	$latest_package = array_shift( $packages[$args->slug]['versions'] );

	if ( $action == 'basic_check' ) {	
		$update_info = (object) $latest_package;
		$update_info->slug = $args->slug;
		
		if ( version_compare( $args->version, $latest_package['version'], '<' ) )
			$update_info->new_version = $update_info->version;
		
		echo serialize( $update_info );
	}

	if ( $action == 'theme_update' ) {
		$update_info = (object) $latest_package;
		
		//$update_data = new stdClass;
		$update_data = array();
		$update_data['package'] = $update_info->package;	
		$update_data['new_version'] = $update_info->version;
		$update_data['url'] = $packages[$args->slug]['info']['url'];
			
		if ( version_compare( $args->version, $latest_package['version'], '<' ) )
			echo serialize( $update_data );	
	}

	exit;
}
add_action( 'template_redirect', 'thememy_process_api_requests' );

