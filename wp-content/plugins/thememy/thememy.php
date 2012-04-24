<?php
/**
 * @package ThemeMY_Website
 * @version 0.1
 */
/*
Plugin Name: ThemeMY! Website Features
Plugin URI: http://thememy.com/
Description: The easiest way to sell WordPress themes
Author: ThemeMY!
Version: 0.1
Author URI: http://thememy.com/
*/
/*  Copyright 2011 ThemeMY! All rights reserved

    This program is property of ThemeMY!. You don't have the right
		to modify or redistribute it unless explicitly granted
		permission to do so.
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

define( 'THEMEMY_PLUGIN_FILE', __FILE__ );
define( 'THEMEMY_PLUGIN_BASENAME', plugin_basename( THEMEMY_PLUGIN_FILE ) );
define( 'THEMEMY_PLUGIN_DIR', plugin_dir_path( THEMEMY_PLUGIN_FILE ) );
define( 'THEMEMY_PLUGIN_URL', plugin_dir_url( THEMEMY_PLUGIN_FILE ) );

include( THEMEMY_PLUGIN_DIR . 'api.php' );
include( THEMEMY_PLUGIN_DIR . 'branding.php' );
include( THEMEMY_PLUGIN_DIR . 'invitations.php' );
include( THEMEMY_PLUGIN_DIR . 'orders.php' );
include( THEMEMY_PLUGIN_DIR . 'settings.php' );
include( THEMEMY_PLUGIN_DIR . 'template-tags.php' );
include( THEMEMY_PLUGIN_DIR . 'themes.php' );

