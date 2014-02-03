<?php
/**
 * Plugin Name.
 *
 * @package   Google_Mapper_Admin
 * @author    Gabe Shackle <gabe@hereswhatidid.com>
 * @license   GPL-2.0+
 * @link      http://hereswhatidid.com
 * @copyright 2014 Gabe Shackle
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * administrative side of the WordPress site.
 *
 * If you're interested in introducing public-facing
 * functionality, then refer to `class-google-mapper.php`
 *
 * @TODO: Rename this class to a proper name for your plugin.
 *
 * @package Google_Mapper_Admin
 * @author  Gabe Shackle <gabe@hereswhatidid.com>
 */
class Google_Mapper_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	protected $script_mode = 'min';

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		/*
		 * @TODO :
		 *
		 * - Uncomment following lines if the admin class should only be available for super admins
		 */
		/* if( ! is_super_admin() ) {
			return;
		} */

		/*
		 * Call $plugin_slug from public plugin class.
		 *
		 * @TODO:
		 *
		 * - Rename "Google_Mapper" to the name of your initial plugin class
		 *
		 */
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			$this->script_mode = 'dev';
		}

		$plugin = Google_Mapper::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		/*
		 * Define custom functionality.
		 *
		 * Read more about actions and filters:
		 * http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */
		add_action( 'init', array( $this, 'initialize_post_types' ) );

		add_action( 'wp_ajax_google_mapper_get_locations', array( $this, 'ajax_get_locations' ) );
		// add_filter( '@TODO', array( $this, 'filter_method_name' ) );

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		/*
		 * @TODO :
		 *
		 * - Uncomment following lines if the admin class should only be available for super admins
		 */
		/* if( ! is_super_admin() ) {
			return;
		} */

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @TODO:
	 *
	 * - Rename "Google_Mapper" to the name your plugin
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		$screen = get_current_screen();
		if ( false !== strpos( $screen->id, 'google-mapper' ) ) {
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/styles.' . $this->script_mode . '.css', __FILE__ ), array(), Google_Mapper::VERSION );
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @TODO:
	 *
	 * - Rename "Google_Mapper" to the name your plugin
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		$screen = get_current_screen();

		if ( false !== strpos( $screen->id, 'google-mapper' ) ) {
			wp_enqueue_script( $this->plugin_slug . '-google-maps', 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false', array(), '3' );
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/scripts.' . $this->script_mode . '.js', __FILE__ ), array( 'jquery', $this->plugin_slug . '-google-maps' ), Google_Mapper::VERSION );
			$mapper_localize = array(
				'get_locations_nonce' => wp_create_nonce( $this->plugin_slug . '_get_locations' ),
				'slug' => $this->plugin_slug
			);
			wp_localize_script( $this->plugin_slug . '-admin-script', 'GoogleMapper', $mapper_localize );
		}

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		/*
		 * Add a settings page for this plugin to the Settings menu.
		 *
		 * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
		 *
		 *        Administration Menus: http://codex.wordpress.org/Administration_Menus
		 *
		 * @TODO:
		 *
		 * - Change 'Page Title' to the title of your plugin admin page
		 * - Change 'Menu Text' to the text for menu item for the plugin settings page
		 * - Change 'manage_options' to the capability you see fit
		 *   For reference: http://codex.wordpress.org/Roles_and_Capabilities
		 */
		add_menu_page(
			__( 'Google Mapper', $this->plugin_slug ),
			__( 'Google Mapper', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'locations_admin' ),
			'dashicons-location-alt',
			26
		);
		add_submenu_page(
			$this->plugin_slug,
			__( 'Locations', $this->plugin_slug ),
			__( 'Locations', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'locations_admin' )
		);
		add_submenu_page(
			$this->plugin_slug,
			__( 'Maps', $this->plugin_slug ),
			__( 'Maps', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug . '-maps',
			array( $this, 'maps_admin' )
		);
		add_submenu_page(
			$this->plugin_slug,
			__( 'Google Mapper Settings', $this->plugin_slug ),
			__( 'Settings', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug . '-settings',
			array( $this, 'maps_settings' )
		);


		// $this->plugin_screen_hook_suffix = add_options_page(
		// 	__( 'Locations', $this->plugin_slug ),
		// 	__( 'Menu Text', $this->plugin_slug ),
		// 	'manage_options',
		// 	$this->plugin_slug,
		// 	array( $this, 'display_plugin_admin_page' )
		// );

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function plugin_settings() {
		include_once( 'views/admin.php' );
	}
	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function locations_admin() {
		include_once( 'views/admin.php' );
	}
	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function maps_admin() {
		include_once( 'views/admin.php' );
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
			),
			$links
		);

	}

	public function initialize_post_types() {
		$locations_labels = array(
			'name'                => _x( 'Locations', 'Post Type General Name', $this->plugin_slug ),
			'singular_name'       => _x( 'Location', 'Post Type Singular Name', $this->plugin_slug ),
			'menu_name'           => __( 'Locations', $this->plugin_slug ),
			'parent_item_colon'   => __( 'Parent Location:', $this->plugin_slug ),
			'all_items'           => __( 'All Locations', $this->plugin_slug ),
			'view_item'           => __( 'View Location', $this->plugin_slug ),
			'add_new_item'        => __( 'Add New Location', $this->plugin_slug ),
			'add_new'             => __( 'Add New', $this->plugin_slug ),
			'edit_item'           => __( 'Edit Location', $this->plugin_slug ),
			'update_item'         => __( 'Update Location', $this->plugin_slug ),
			'search_items'        => __( 'Search Location', $this->plugin_slug ),
			'not_found'           => __( 'Not found', $this->plugin_slug ),
			'not_found_in_trash'  => __( 'Not found in Trash', $this->plugin_slug ),
			);
		$locations_args = array(
			'label'               => __( 'google_locations', $this->plugin_slug ),
			'description'         => __( 'Individual Google Map locations used by the Google Mapper plugin', $this->plugin_slug ),
			'labels'              => $locations_labels,
			'supports'            => array( 'title', ),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => false,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 5,
			'menu_icon'           => '',
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'rewrite'             => false,
			'capability_type'     => 'page',
			);
		$maps_labels = array(
			'name'                => _x( 'Maps', 'Post Type General Name', $this->plugin_slug ),
			'singular_name'       => _x( 'Map', 'Post Type Singular Name', $this->plugin_slug ),
			'menu_name'           => __( 'Maps', $this->plugin_slug ),
			'parent_item_colon'   => __( 'Parent Map:', $this->plugin_slug ),
			'all_items'           => __( 'All Maps', $this->plugin_slug ),
			'view_item'           => __( 'View Map', $this->plugin_slug ),
			'add_new_item'        => __( 'Add New Map', $this->plugin_slug ),
			'add_new'             => __( 'Add New', $this->plugin_slug ),
			'edit_item'           => __( 'Edit Map', $this->plugin_slug ),
			'update_item'         => __( 'Update Map', $this->plugin_slug ),
			'search_items'        => __( 'Search Map', $this->plugin_slug ),
			'not_found'           => __( 'Not found', $this->plugin_slug ),
			'not_found_in_trash'  => __( 'Not found in Trash', $this->plugin_slug ),
			);
		$maps_args = array(
			'label'               => __( 'google_maps', $this->plugin_slug ),
			'description'         => __( 'Individual Google Maps used by the Google Mapper plugin', $this->plugin_slug ),
			'labels'              => $maps_labels,
			'supports'            => array( 'title', ),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => false,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 5,
			'menu_icon'           => '',
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'rewrite'             => false,
			'capability_type'     => 'page',
			);
		register_post_type( 'google_locations', $locations_args );
		register_post_type( 'google_maps', $maps_args );
	}

	public function ajax_get_locations() {

		check_ajax_referer( $this->plugin_slug . '_get_locations', 'security' );

		$args = array(
			'posts_per_page'   => 20,
			'offset'           => 0,
			);

		$locations = get_posts( $locations_args );

		$response = json_encode( array( 'locations' => $locations ) );

		header('Content-Type: application/json');

		echo $response;

		exit;
	}

}
