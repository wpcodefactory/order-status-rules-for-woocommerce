<?php
/**
 * Order Status Rules for WooCommerce - General Section Settings
 *
 * @version 3.9.5
 * @since   1.0.0
 *
 * @author  WPFactory
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Order_Status_Rules_Settings_General' ) ) :

class Alg_WC_Order_Status_Rules_Settings_General extends Alg_WC_Order_Status_Rules_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.8.1
	 * @since   1.0.0
	 */
	function __construct() {
		$this->id   = '';
		$this->desc = __( 'General', 'order-status-rules-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 3.9.5
	 * @since   1.0.0
	 */
	function get_settings() {
		return array(
			array(
				'title'    => __( 'Order Status Rules Options', 'order-status-rules-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_order_status_rules_plugin_options',
			),
			array(
				'title'    => __( 'Order Status Rules', 'order-status-rules-for-woocommerce' ),
				'desc'     => '<strong>' . __( 'Enable plugin', 'order-status-rules-for-woocommerce' ) . '</strong>',
				'desc_tip' => __( 'Automate WooCommerce order statuses. Beautifully.', 'order-status-rules-for-woocommerce' ),
				'id'       => 'alg_wc_order_status_rules_plugin_enabled',
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Total rules', 'order-status-rules-for-woocommerce' ),
				'desc_tip' => __( 'New settings sections will be added if you change this option and "Save changes".', 'order-status-rules-for-woocommerce' ),
				'id'       => 'alg_wc_order_status_rules_total',
				'default'  => 1,
				'type'     => 'number',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_order_status_rules_plugin_options',
			),
		);
	}

}

endif;

return new Alg_WC_Order_Status_Rules_Settings_General();
