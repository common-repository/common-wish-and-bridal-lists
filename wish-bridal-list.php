<?php
/*
	Plugin Name: Common Wish and Bridal Lists
	Plugin URI: http://wordpress.org/plugins/common-wish-and-bridal-lists/
	Description: A comprehensive, modern and flexible Wish and Bridal lists for WooCommerce.
	Version: 1.3.1
	Author: briar
	Author URI: http://briar.fun/
	License:     GPL2
	License URI: https://www.gnu.org/licenses/gpl-2.0.html
	Text Domain: wb-list
	Domain Path: /languages
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Class Wish_Bridal_List
 *
 * Main plugin class.
 */
final class Wish_Bridal_List {

	/**
	 * @var string Plugin version.
	 */
	public $version = '1.3.1';

	/**
	 * @var null Instance of main class.
	 */
	protected static $_instance = null;


	/**
	 * @var WB_Wish Instance of WB_Wish class.
	 */
	protected $wish = null;

	/**
	 * @var WB_Bridal Instance of WB_Bridal class.
	 */
	protected $bridal = null;

	/**
	 * Return instance of main class. Singleton.
	 *
	 * @return null|Wish_Bridal_List
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Check is plugin Woocommerce is active.
	 *
	 * @return bool
	 */
	private function is_woocommerce_active() {
		return in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
	}

	/**
	 * Wish_Bridal_List constructor.
	 */
	public function __construct() {

		//Rely on Woocommerce functionality.
		if ( !$this->is_woocommerce_active() ) {
			return;
		}

		// Add links on Plugins page
		add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array($this, 'action_links') );


		$this->includes();

		add_action( 'init', array( $this, 'init' ), 0 );

	}

	/**
	 * Includes additional files.
	 *
	 */
	private function includes() {
		
		
		include_once('includes/base/trait-wb-data.php');
		include_once('includes/base/trait-wb-ajax.php');
		include_once('includes/base/trait-wb-link.php');
		include_once('includes/base/trait-wb-shortcode.php');
		include_once('includes/base/class-wb-base.php');

		include_once( 'includes/class-wb-wish.php' );
		include_once( 'includes/class-wb-bridal.php' );

		include_once( 'includes/class-wb-process.php' );

		include_once( 'includes/wb-functions.php' );

		include_once( 'includes/class-wb-setting.php' );

	}

	/**
	 * Get WB_Wish and WB_Bridal class instance.
	 */
	public function init() {

		add_action( 'wp_enqueue_scripts', array( $this, 'front_assets' ) );

		// Multi-language support
		load_plugin_textdomain( 'wb-list', false, plugin_basename( dirname( __FILE__ ) ) . "/languages" );

		$this->wish = new WB_Wish;

		if ( $this->is_bridal_enabled() ) {
			$this->bridal = new WB_Bridal;
		}

	}

	/**
	 * Return WB_Wish instance.
	 *
	 * @return WB_Wish
	 */
	public function getWish() {
		return $this->wish;
	}

	/**
	 * Return WB_Bridal instance.
	 *
	 * @return WB_Bridal
	 */
	public function getBridal() {
		return $this->bridal;
	}

	/**
	 * Check is Bridal List enabled.
	 *
	 * @return bool
	 */
	public function is_bridal_enabled() {
		return 'yes' == get_option('wb_bridal_enable');
	}


	/**
	 * @param $id
	 *
	 * @return WB_Wish|WB_Bridal
	 */
	public function getClass( $id ) {
		return $this->{$id};
	}

	/**
	 * Enqueue front scripts and styles.
	 */
	function front_assets() {

		$prefix = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG )? '' : '.min';

		wp_enqueue_script( 'wb-list', $this->plugin_url()."/assets/js/wb-list{$prefix}.js", array('jquery', 'jquery-blockui', 'jquery-ui-datepicker'), $this->version);

		$params = array(
			'ajax_url' 		    => admin_url( 'admin-ajax.php' ),
		);

		if ( $this->is_bridal_enabled() ) {
			$params['is_bridal_current_user'] = $this->bridal->is_current_user_owner();
			$params['bridal_nonce'] = wp_create_nonce('wb_save_bridal_data');
		}

		wp_localize_script( 'wb-list', 'wb_params', $params );

		wp_enqueue_style( 'font-awesome', $this->plugin_url().  '/assets/lib/font-awesome/css/font-awesome.min.css' );

		wp_enqueue_style( 'jquery-ui-datepicker', $this->plugin_url() . '/assets/lib/components-jqueryui/themes/smoothness/jquery-ui.min.css' );

		wp_enqueue_style( 'wb-list', $this->plugin_url().'/assets/css/wb-list.css', array('font-awesome', 'jquery-ui-datepicker'), $this->version );

	}

	/**
	 * Return plugin directory url without trailing slash.
	 *
	 * @return string Plugin url
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', __FILE__ ) );
	}

	/**
	 * Add custom link on Plugins page in admin.
	 *
	 * @param $links
	 *
	 * @return array
	 */
	public function action_links( $links ) {
		$links[] = '<a href="'. esc_url( get_admin_url(null, 'admin.php?page=wc-settings&tab=products&section=wishlist') ) .'">Settings</a>';

		return $links;
	}

}

/**
 *  Return main class instance.
 */
function WB() {
	return Wish_Bridal_List::instance();
}

WB();