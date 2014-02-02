<?php
/**
 * The WordPress Plugin Boilerplate.
 *
 * A foundation off of which to build well-documented WordPress plugins that
 * also follow WordPress Coding Standards and PHP best practices.
 *
 * @package   Google_Mapper
 * @author    Gabe Shackle <gabe@hereswhatidid.com>
 * @license   GPL-2.0+
 * @link      http://hereswhatidid.com
 * @copyright 2014 Gabe Shackle
 *
 * @wordpress-plugin
 * Plugin Name:       Google Mapper
 * Plugin URI:        http://hereswhatidid.com/google-mapper/
 * Description:       Create custom Google Maps locations and search by proximity
 * Version:           1.0.0
 * Author:            Gabe Shackle
 * Author URI:        http://hereswhatidid.com
 * Text Domain:       google-mapper-locale
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/hereswhatidid/google-mapper
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

/*
 * @TODO:
 *
 * - replace `class-google-mapper.php` with the name of the plugin's class file
 *
 */
require_once( plugin_dir_path( __FILE__ ) . 'public/class-google-mapper.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 *
 * @TODO:
 *
 * - replace Google_Mapper with the name of the class defined in
 *   `class-google-mapper.php`
 */
register_activation_hook( __FILE__, array( 'Google_Mapper', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Google_Mapper', 'deactivate' ) );

/*
 * @TODO:
 *
 * - replace Google_Mapper with the name of the class defined in
 *   `class-google-mapper.php`
 */
add_action( 'plugins_loaded', array( 'Google_Mapper', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 * @TODO:
 *
 * - replace `class-google-mapper-admin.php` with the name of the plugin's admin file
 * - replace Google_Mapper_Admin with the name of the class defined in
 *   `class-google-mapper-admin.php`
 *
 * If you want to include Ajax within the dashboard, change the following
 * conditional to:
 *
 * if ( is_admin() ) {
 *   ...
 * }
 *
 * The code below is intended to to give the lightest footprint possible.
 */
if ( is_admin() ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-google-mapper-admin.php' );
	add_action( 'plugins_loaded', array( 'Google_Mapper_Admin', 'get_instance' ) );

}
