<?php
/**
 * Order Status Rules for WooCommerce - General Section Settings
 *
 * @version 2.0.0
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd.
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
	 * @version 2.0.0
	 * @since   1.0.0
	 */
	function get_settings() {

		$plugin_settings = array(
			array(
				'title'    => __( 'Order Status Rules Options', 'order-status-rules-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_order_status_rules_plugin_options',
			),
			array(
				'title'    => __( 'Order Status Rules', 'order-status-rules-for-woocommerce' ),
				'desc'     => '<strong>' . __( 'Enable plugin', 'order-status-rules-for-woocommerce' ) . '</strong>',
				'desc_tip' => __( 'Manage WooCommerce order statuses. Beautifully.', 'order-status-rules-for-woocommerce' ),
				'id'       => 'alg_wc_order_status_rules_plugin_enabled',
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Total rules', 'order-status-rules-for-woocommerce' ),
				'desc_tip' => __( 'New settings sections will be added if you change this option and "Save changes".', 'order-status-rules-for-woocommerce' ),
				'desc'     => apply_filters( 'alg_wc_order_status_rules_settings',
					sprintf( '<p>' . 'You will need %s plugin to add more than one rule.' . '</p>',
						'<a target="_blank" href="https://wpfactory.com/item/order-status-rules-for-woocommerce/">Order Status Rules for WooCommerce Pro</a>' ) ),
				'id'       => 'alg_wc_order_status_rules_total',
				'default'  => 1,
				'type'     => 'number',
				'custom_attributes' => apply_filters( 'alg_wc_order_status_rules_settings', array( 'readonly' => 'readonly' ), 'rules_total_atts' ),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_order_status_rules_plugin_options',
			),
		);

		$tools = array(
			array(
				'title'    => __( 'Tools', 'order-status-rules-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_order_status_rules_tools_options',
				'desc'     => implode( '<br>', array( $this->get_next_scheduled_desc(), $this->get_enabled_rules_desc() ) ),
			),
			array(
				'title'    => __( 'Run all rules now', 'order-status-rules-for-woocommerce' ),
				'desc'     => '<strong>' . __( 'Run', 'order-status-rules-for-woocommerce' ) . '</strong>',
				'desc_tip' => __( 'Check the box and save changes to run all rules now.', 'order-status-rules-for-woocommerce' ),
				'id'       => 'alg_wc_order_status_rules_run_now',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_order_status_rules_tools_options',
			),
		);

		return array_merge( $plugin_settings, $tools );
	}

}

endif;

return new Alg_WC_Order_Status_Rules_Settings_General();
