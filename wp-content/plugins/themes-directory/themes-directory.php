<?php
/**
 * @package Themes_Directory
 * @version 0.1
 */
/*
Plugin Name: Themes Directory
Plugin URI: http://wordpress.org/extend/plugins/themes-directory/
Description: A quick way to showcase your WordPress themes on your site
Author: Ulrich Sossou
Version: 0.1
Author URI: http://ulrichsossou.com/
*/
/*  Copyright 2011  Ulrich Sossou  (http://github.com/sorich87)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

if ( ! class_exists( 'Themes_Directory' ) ) :
/**
 * Main plugin class
 *
 * @package Themes_Directory
 * @since 0.1
 */
class Themes_Directory {

	/**
	 * Class contructor
	 *
	 * @package Themes_Directory
	 * @since 0.1
	 */
	public function __construct() {
		$this->setup_globals();
		$this->includes();
		$this->setup_hooks();
	}

	/**
	 * Global variables
	 *
	 * @package Themes_Directory
	 * @since 0.1
	 */
	private function setup_globals() {
		$this->file       = __FILE__;
		$this->basename   = plugin_basename( $this->file );
		$this->plugin_dir = plugin_dir_path( $this->file );
		$this->plugin_url = plugin_dir_url( $this->file );
	}

	/**
	 * Required files
	 *
	 * @package Themes_Directory
	 * @since 0.1
	 */
	private function includes() {
		if ( is_admin() )
			include( $this->plugin_dir . 'td-admin.php' );
	}

	/**
	 * Setup the plugin main functions
	 *
	 * @package Themes_Directory
	 * @since 0.1
	 */
	private function setup_hooks() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'init', array( $this, 'register_theme_directory' ) );

		register_activation_hook( __FILE__, array( $this, 'activation' ) );
	}

	/**
	 * Register loop post type
	 *
	 * @package Themes_Directory
	 * @since 0.1
	 */
	public function register_post_type() {
		$labels = array(
			'name'               => _x( 'Themes', 'post type general name' ),
			'singular_name'      => _x( 'Theme', 'post type singular name' ),
			'add_new'            => _x( 'Add New', 'theme' ),
			'add_new_item'       => __( 'Add New Theme' ),
			'edit_item'          => __( 'Edit Theme' ),
			'new_item'           => __( 'New Theme' ),
			'all_items'          => __( 'All Themes' ),
			'view_item'          => __( 'View Theme' ),
			'search_items'       => __( 'Search Themes' ),
			'not_found'          => __( 'No themes found' ),
			'not_found_in_trash' => __( 'No themes found in Trash' ),
			'parent_item_colon'  => '',
			'menu_name'          => __( 'Themes' )
		);
		$args = array(
			'description'  => __( 'Themes directory pages' ),
			'labels'       => $labels,
			'public'       => true,
			'map_meta_cap' => true,
			'rewrite'      => array( 'slug' => 'theme' ),
			'show_in_menu' => false,
			'supports'     => array(
				'author', 'comments', 'custom-fields',
				'editor', 'page-attributes', 'revisions'
			)
		);
		register_post_type( 'td_theme', $args );
	}

	/**
	 * Register directory for the default theme
	 *
	 * @package Themes_Directory
	 * @since 0.1
	 */
	public function register_theme_directory() {
		register_theme_directory( $this->plugin_dir . 'themes' );
	}

	/**
	 * Flush rewrite rules on plugin activation
	 *
	 * @package Themes_Directory
	 * @since 0.1
	 */
	public function activation() {
		$this->register_post_type();

		flush_rewrite_rules();
	}
}

$GLOBALS['themes_directory'] = new Themes_Directory;

endif;

