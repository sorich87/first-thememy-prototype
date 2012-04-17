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

	wp_enqueue_script( 'bootstrap-modal', get_template_directory_uri() . '/assets/js/bootstrap-modal.js', 'jquery', '20120416', true );
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
 * Add Getting Started modal
 *
 * @since ThemeMY! 0.1
 */
function thememy_getting_started() {
	if ( ! is_user_logged_in() || isset( $_GET['getting_started'] ) || get_user_option( 'thememy_show_getting_started', get_current_user_id() ) == 'no'
		|| get_posts( array( 'post_type' => 'thememy_order', 'author' => get_current_user_id(), 'fields' => 'ids' ) ) )
		return;
?>
<div class="modal hide" id="getting-started">
  <div class="modal-header">
    <a class="close" data-dismiss="modal" href="#getting-started">×</a>
		<h3><?php _e( 'Getting Started' ); ?></h3>
  </div>
  <div class="modal-body">
		<p><?php _e( 'Start selling your themes in three (3) easy steps.' ); ?></p>

		<h4>
			<?php _e( 'Step 1' ); ?>
			<?php if ( thememy_get_settings() ) : ?>
				<span class="label label-success"><?php _e( 'done' ); ?></span>
			<?php endif; ?>
		</h4>
		<p><?php printf(
			__( 'Go to <a href="%s">the settings page</a> and fill in your business, pricing and payment details.' ),
			add_query_arg( 'getting_started', 'true', site_url( 'settings/' ) )
		); ?></p>

		<h4>
			<?php _e( 'Step 2' ); ?>
			<?php if ( get_posts( array( 'post_type' => 'td_theme', 'author' => get_current_user_id(), 'fields' => 'ids' ) ) ) : ?>
				<span class="label label-success"><?php _e( 'done' ); ?></span>
			<?php endif; ?>
		</h4>
		<p><?php printf(
			__( 'Go to <a href="%s">the themes page</a> and upload the themes you want to sell.' ),
			add_query_arg( 'getting_started', 'true', site_url( 'themes/' ) )
		); ?></p>

		<h4><?php _e( 'Step 3' ); ?></h4>
		<p><?php _e( 'Copy the themes purchase links and paste them in your website.' ); ?></p>
  </div>
  <div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal"><?php _e( 'Close' ); ?></a>
		<a href="#" class="btn btn-warning" id="no-getting-started"><?php _e( "Don't show this again" ); ?></a>
  </div>
</div>
<script type="text/javascript">
jQuery(function ($) {
	$("#getting-started").modal();

	$("#no-getting-started").click(function (e) {
		e.preventDefault();

		var data = {
			_ajax_nonce: "<?php echo wp_create_nonce( 'thememy-no-getting-started' ); ?>",
			action: "thememy-no-getting-started"
		};
		$.post( "<?php echo admin_url( 'admin-ajax.php' ); ?>", data );

		$("#getting-started").modal( "hide" );
	});
});
</script>
<?php
}
add_action( 'wp_footer', 'thememy_getting_started', 20 );

function thememy_no_getting_started() {
	check_ajax_referer( 'thememy-no-getting-started' );

	update_user_option( get_current_user_id(), 'thememy_show_getting_started', 'no' );

	exit;
}
add_action( 'wp_ajax_thememy-no-getting-started', 'thememy_no_getting_started' );

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
 * Show front page only to non logged-in users
 *
 * @since ThemeMY! 0.1
 */
function thememy_restrict_pages() {
	if ( is_user_logged_in() && current_user_can( 'edit_posts' ) ) {
		if ( ( is_front_page() && empty( $_GET['buy'] ) && empty( $_GET['buy-all'] ) )
	 		|| is_page_template( 'signup-page.php' )	) {
			wp_redirect( site_url( 'themes/' ) );
			exit;
		}

	} else {
		if ( ! is_front_page() && ! is_page_template( 'store-page.php' ) && ! is_page_template( 'survey-page.php' )
			&& ! is_page_template( 'download-page.php' ) && ! is_page_template( 'signup-page.php' )
			&& ! is_page( 'api' ) && ! is_page( 'ipn' ) ) {
			wp_redirect( home_url( '/' ) );
			exit;
		}
	}
}
add_action( 'template_redirect', 'thememy_restrict_pages' );

/**
 * Restrict admin pages from authors, contributors and subscribers
 *
 * @since ThemeMY! 0.1
 */
function thememy_restrict_admin() {
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX )
		return;

	if ( current_user_can( 'edit_others_posts' ) )
		return;

	global $plugin_page;

	if ( 'td-admin' != $plugin_page || empty( $_FILES['themezip'] ) ) {
		wp_redirect( site_url( 'themes/' ) );
		exit;
	}
}
add_action( 'admin_init', 'thememy_restrict_admin' );

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
 * Send survey answers to site admin
 *
 * @since ThemeMY! 0.1
 */
function thememy_send_survey() {
	if ( ! is_page_template( 'survey-page.php' ) || ! isset( $_POST['email'] ) )
		return;

	$post = stripslashes_deep( $_POST );

	$to = get_option( 'admin_email' );
	$subject = sprintf( __( 'Survey answers from %s' ), $post['email'] );
	$message = json_encode( $post );

	wp_mail( $to, $subject, $message );

	wp_redirect( add_query_arg( 'success', 'true' ) );
	exit;
}
add_action( 'template_redirect', 'thememy_send_survey' );

/**
 * Create a new user account
 *
 * @since ThemeMY! 0.1
 */
function thememy_user_signup() {
	if ( ! is_page_template( 'signup-page.php' ) || empty( $_POST ) )
		return;

	$data = stripslashes_deep( $_POST );

	$first_name  = isset( $_POST['first_name'] ) ? $_POST['first_name'] : '';
	$last_name   = isset( $_POST['last_name'] ) ? $_POST['last_name'] : '';
	$user_email  = isset( $_POST['user_email'] ) ? $_POST['user_email'] : '';
	$user_pass   = isset( $_POST['user_pass'] ) ? $_POST['user_pass'] : '';
	$user_pass_2 = isset( $_POST['user_pass_2'] ) ? $_POST['user_pass_2'] : '';

	$redirect_to = add_query_arg( array(
		'first_name' => $first_name,
		'last_name' => $last_name,
		'user_email' => $user_email
	) );

	if ( ! $first_name || ! $last_name || ! $user_email || ! $user_pass || ! $user_pass_2 ) {
		wp_redirect( add_query_arg( 'message', '1', $redirect_to ) );
		exit;
	}

	if ( ! is_email( $user_email ) ) {
		wp_redirect( add_query_arg( 'message', '2', remove_query_arg( 'user_email', $redirect_to ) ) );
		exit;
	}

	if ( $user_pass != $user_pass_2 ) {
		wp_redirect( add_query_arg( 'message', '3', $redirect_to ) );
		exit;
	}

	$user_login = wp_hash( $user_email );
	$display_name = "$first_name $last_name";
	$role = 'author';

	$args = compact( 'first_name', 'last_name', 'user_email', 'user_pass', 'user_login', 'display_name', 'role' );

	$user_id = wp_insert_user( $args );

	if ( is_wp_error( $user_id ) )
		thememy_error( $user_id );

	wp_redirect( add_query_arg( 'email', urlencode( $user_email ), site_url( "survey/" ) ) );
	exit;
}
add_action( 'template_redirect', 'thememy_user_signup' );

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

	wp_redirect( add_query_arg( 'message', '1' ) );
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
function thememy_register_post_type() {
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
			thememy_send_download_email( $order->ID );

			exit;
		}
	}

	thememy_error( $data, false );
	exit;
}
add_action( 'template_redirect', 'thememy_process_order' );

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

		$expires = strtotime( '+1 day' );
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

	wp_mail( $email, $subject, $message, $headers );

	if ( ! empty( $settings['test-mode'] ) )
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
			'version'  => $version,
			'ThemeURI' => $version_data['URI']
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
		exit;

	// get details of themes purchased by the user
	$themes = thememy_get_themes( $request['email'] );
	if ( ! $themes )
		exit;

	$themes = get_posts( array( 'post__in' => $themes, 'post_type' => 'td_theme' ) );
	if ( ! $themes )
		exit;

	foreach ( $themes as $theme ) {
		$available[$theme->post_title] = thememy_api_theme_details( $theme->ID );
	}

	// loop through the user's installed themes and match themes names and URIs
	$installed = json_decode( $request['themes'], true );
	foreach ( $installed as $slug => $theme ) {
		$theme_name = $theme['Name'];
		if ( ! isset( $available[$theme_name] ) )
			continue;

		$match = $available[$theme_name];
		$theme_version = $theme['Version'];

		if ( ! isset( $match['versions'][$theme_version] ) || $match['versions'][$theme_version]['ThemeURI'] != $theme['ThemeURI'] )
			continue;

		if ( $theme_version >= $match['new_version'] )
			continue;

		$new[$slug] = array(
			'package'     => $match['package'],
			'new_version' => $match['new_version'],
			'url'         => $match['url']
		);
	}

	if ( $new )
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

	wp_mail( $to, $subject, $message );

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
	echo site_url( 'thememy.zip' );
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

/**
 * Change default email sender
 */
function thememy_mail_from( $from_email ) {
	if ( strpos( $from_email, 'wordpress' ) === 0 ) {
		$sitename = strtolower( $_SERVER['SERVER_NAME'] );

		if ( substr( $sitename, 0, 4 ) == 'www.' )
			$sitename = substr( $sitename, 4 );

		$from_email = 'no-reply@' . $sitename;
	}

	return $from_email;
}
add_filter( 'wp_mail_from', 'thememy_mail_from' );

/**
 * Change default email sender name
 */
function thememy_mail_from_name( $from_name ){
	if ( 'WordPress' == $from_name )
		$from_name = get_option( 'blogname' );

	return $from_name;
}
add_filter( 'wp_mail_from_name', 'thememy_mail_from_name' );

/**
 * Use SMTP to send emails
 *
 * @since ThemeMY! 0.1
 */
function thememy_phpmailer_init( $phpmailer ) {
	if ( ! defined( 'SMTP_HOST' ) )
		return;

	$phpmailer->IsSMTP();
	$phpmailer->Host = SMTP_HOST;
	$phpmailer->Port = defined( 'SMTP_PORT' ) ? SMTP_PORT : 25;
	if ( defined( 'SMTP_USER' ) ) {
		$phpmailer->SMTPAuth = true;
		$phpmailer->Username = SMTP_USER;
		$phpmailer->Password = defined( 'SMTP_PASSWORD' ) ? SMTP_PASSWORD : '';
	}
	if ( defined( 'SMTP_SECURE' ) )
		$phpmailer->SMTPSecure = SMTP_SECURE;
	if ( defined( 'SMTP_DEBUG' ) && SMTP_DEBUG )
		$phpmailer->SMTPDebug = true;
}
add_action( 'phpmailer_init', 'thememy_phpmailer_init' );

/**
 * Alter themes archive page query var by restricting to current user themes
 *
 * @since ThemeMY! 0.1
 */
function thememy_pre_get_posts( $query ) {
	if ( ! is_post_type_archive( 'td_theme' ) )
		return;

	$query->query_vars['author'] = get_current_user_id();
}
add_action( 'pre_get_posts', 'thememy_pre_get_posts' );

/**
 * Don't allow direct access to upload directory
 *
 * @since ThemeMY! 0.1
 */
function thememy_mod_rewrite_rules( $rules ) {
	$rules = str_replace(
		"\nRewriteRule ^index\.php$ - [L]\n",
		"\nRewriteRule ^wp-content/uploads/ - [R=404,L,NC]\nRewriteRule ^index\.php$ - [L]\n",
		$rules
	);

	return $rules;
}
add_action( 'mod_rewrite_rules', 'thememy_mod_rewrite_rules' );

/**
 * Use email to authenticate users
 *
 * @since ThemeMY! 0.1
 */
function thememy_authenticate( $user, $username, $password ) {
	if ( ! empty( $username ) )
		$user = get_user_by( 'email', $username );
	if ( isset( $user->user_login, $user ) )
		$username = $user->user_login;

	return wp_authenticate_username_password( null, $username, $password );
}
remove_filter( 'authenticate', 'wp_authenticate_username_password', 20, 3 );
add_filter( 'authenticate', 'thememy_authenticate', 20, 3 );

