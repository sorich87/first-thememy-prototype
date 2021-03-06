<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

if ( file_exists( dirname( __FILE__ ) . '/local-config.php' ) )
	include( dirname( __FILE__ ) . '/local-config.php' );

// ** Paypal API settings ** //
define( 'X_PAYPAL_SECURITY_USERID', 'ulrich.sossou_api1.takitiz.com' );
define( 'X_PAYPAL_SECURITY_PASSWORD', 'EYKWUTQ7K83B8JA9' );
define( 'X_PAYPAL_SECURITY_SIGNATURE', 'A8Cdu8CvZd7Iex6FUIl8z6Jelvw1Au.hOAwUwFAugChJvtRqUn8Y-FQj' );
define( 'X_PAYPAL_APPLICATION_ID', 'APP-1JF21546D8116283X' );

// ** SMTP settings ** //
/** SMTP server */
define( 'SMTP_HOST', 'smtp.sendgrid.net' );

/** SMTP username */
define( 'SMTP_USER', 'thememy' );

/** SMTP password */
define( 'SMTP_PASSWORD', 'Theme12MY!' );

/** SMTP port */
define( 'SMTP_PORT', 587 );

/** SMTP secure connections protocol */
define( 'SMTP_SECURE', 'TLS' );

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'thememy_com');

/** MySQL database username */
define('DB_USER', 'sorich87-35962');

/** MySQL database password */
define('DB_PASSWORD', 'Theme12MY');

/** MySQL hostname */
define('DB_HOST', 'mysql-shared-02.phpfog.com');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/** --- PHP Fog --- Set WordPress to cache requests. For use with Varnish. */
define('WP_CACHE', true);

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '0.0187627108869763');
define('SECURE_AUTH_KEY',  '0.0187627108869763');
define('LOGGED_IN_KEY',    '0.0187627108869763');
define('NONCE_KEY',        '0.0187627108869763');
define('AUTH_SALT',        '0.0187627108869763');
define('SECURE_AUTH_SALT', '0.0187627108869763');
define('LOGGED_IN_SALT',   '0.0187627108869763');
define('NONCE_SALT',       '0.0187627108869763');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/* PHPFOG edit to patch a few issues of file saves, plugins, etc. */
define('FS_METHOD', 'direct');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
