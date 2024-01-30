<?php
/**
 * Order Status Rules for WooCommerce - Main Class
 *
 * @version 3.5.0
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Order_Status_Rules' ) ) :

final class Alg_WC_Order_Status_Rules {

	/**
	 * Plugin version.
	 *
	 * @var   string
	 * @since 1.0.0
	 */
	public $version = ALG_WC_ORDER_STATUS_RULES_VERSION;

	/**
	 * core.
	 *
	 * @version 3.5.0
	 * @since   3.5.0
	 */
	public $core;

	/**
	 * @var   Alg_WC_Order_Status_Rules The single instance of the class
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main Alg_WC_Order_Status_Rules Instance
	 *
	 * Ensures only one instance of Alg_WC_Order_Status_Rules is loaded or can be loaded.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 *
	 * @static
	 * @return  Alg_WC_Order_Status_Rules - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Alg_WC_Order_Status_Rules Constructor.
	 *
	 * @version 3.2.0
	 * @since   1.0.0
	 *
	 * @access  public
	 */
	function __construct() {

		// Check for active WooCommerce plugin
		if ( ! function_exists( 'WC' ) ) {
			return;
		}

		// Set up localisation
		add_action( 'init', array( $this, 'localize' ) );

		// Declare compatibility with custom order tables for WooCommerce
		add_action( 'before_woocommerce_init', array( $this, 'wc_declare_compatibility' ) );

		// Pro
		if ( 'order-status-rules-for-woocommerce-pro.php' === basename( ALG_WC_ORDER_STATUS_RULES_FILE ) ) {
			require_once( 'pro/class-alg-wc-order-status-rules-pro.php' );
		}

		// Include required files
		$this->includes();

		// Admin
		if ( is_admin() ) {
			$this->admin();
		}

	}

	/**
	 * localize.
	 *
	 * @version 1.7.0
	 * @since   1.4.0
	 */
	function localize() {
		load_plugin_textdomain( 'order-status-rules-for-woocommerce', false, dirname( plugin_basename( ALG_WC_ORDER_STATUS_RULES_FILE ) ) . '/langs/' );
	}

	/**
	 * wc_declare_compatibility.
	 *
	 * @version 3.4.0
	 * @since   3.2.0
	 *
	 * @see     https://github.com/woocommerce/woocommerce/wiki/High-Performance-Order-Storage-Upgrade-Recipe-Book#declaring-extension-incompatibility
	 */
	function wc_declare_compatibility() {
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			$files = ( defined( 'ALG_WC_ORDER_STATUS_RULES_FILE_FREE' ) ?
				array( ALG_WC_ORDER_STATUS_RULES_FILE, ALG_WC_ORDER_STATUS_RULES_FILE_FREE ) : array( ALG_WC_ORDER_STATUS_RULES_FILE ) );
			foreach ( $files as $file ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', $file, true );
			}
		}
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 *
	 * @version 1.7.0
	 * @since   1.0.0
	 */
	function includes() {
		$this->core = require_once( 'class-alg-wc-order-status-rules-core.php' );
	}

	/**
	 * admin.
	 *
	 * @version 1.7.0
	 * @since   1.1.0
	 */
	function admin() {
		// Action links
		add_filter( 'plugin_action_links_' . plugin_basename( ALG_WC_ORDER_STATUS_RULES_FILE ), array( $this, 'action_links' ) );
		// Settings
		add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_woocommerce_settings_tab' ) );
		// Version update
		if ( get_option( 'alg_wc_order_status_rules_version', '' ) !== $this->version ) {
			add_action( 'admin_init', array( $this, 'version_updated' ) );
		}
	}

	/**
	 * Show action links on the plugin screen.
	 *
	 * @version 1.7.0
	 * @since   1.0.0
	 *
	 * @param   mixed $links
	 * @return  array
	 */
	function action_links( $links ) {
		$custom_links = array();
		$custom_links[] = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_wc_order_status_rules' ) . '">' . __( 'Settings', 'woocommerce' ) . '</a>';
		if ( 'order-status-rules-for-woocommerce.php' === basename( ALG_WC_ORDER_STATUS_RULES_FILE ) ) {
			$custom_links[] = '<a target="_blank" style="font-weight: bold; color: green;" href="https://wpfactory.com/item/order-status-rules-for-woocommerce/">' .
				__( 'Go Pro', 'order-status-rules-for-woocommerce' ) . '</a>';
		}
		return array_merge( $custom_links, $links );
	}

	/**
	 * Add Order Status Rules settings tab to WooCommerce settings.
	 *
	 * @version 1.7.0
	 * @since   1.0.0
	 */
	function add_woocommerce_settings_tab( $settings ) {
		$settings[] = require_once( 'settings/class-alg-wc-settings-order-status-rules.php' );
		return $settings;
	}

	/**
	 * version_updated.
	 *
	 * @version 1.1.0
	 * @since   1.1.0
	 */
	function version_updated() {
		update_option( 'alg_wc_order_status_rules_version', $this->version );
	}

	/**
	 * Get the plugin url.
	 *
	 * @version 1.7.0
	 * @since   1.0.0
	 *
	 * @return  string
	 */
	function plugin_url() {
		return untrailingslashit( plugin_dir_url( ALG_WC_ORDER_STATUS_RULES_FILE ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @version 1.7.0
	 * @since   1.0.0
	 *
	 * @return  string
	 */
	function plugin_path() {
		return untrailingslashit( plugin_dir_path( ALG_WC_ORDER_STATUS_RULES_FILE ) );
	}

}

endif;
