<?php
/**
 * Order Status Rules for WooCommerce - Settings
 *
 * @version 3.3.0
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Settings_Order_Status_Rules' ) ) :

class Alg_WC_Settings_Order_Status_Rules extends WC_Settings_Page {

	/**
	 * Constructor.
	 *
	 * @version 3.3.0
	 * @since   1.0.0
	 */
	function __construct() {
		$this->id    = 'alg_wc_order_status_rules';
		$this->label = __( 'Order Status Rules', 'order-status-rules-for-woocommerce' );
		parent::__construct();
		// Sections
		require_once( 'class-alg-wc-order-status-rules-settings-section.php' );
		require_once( 'class-alg-wc-order-status-rules-settings-general.php' );
		require_once( 'class-alg-wc-order-status-rules-settings-rule.php' );
		for ( $rule_id = 1; $rule_id <= apply_filters( 'alg_wc_order_status_rules_rules_total', 1 ); $rule_id++ ) {
			new Alg_WC_Order_Status_Rules_Settings_Rule( $rule_id );
		}
		require_once( 'class-alg-wc-order-status-rules-settings-advanced.php' );
		require_once( 'class-alg-wc-order-status-rules-settings-tools.php' );
		require_once( 'class-alg-wc-order-status-rules-settings-my-account.php' );
		require_once( 'class-alg-wc-order-status-rules-settings-extra.php' );
	}

	/**
	 * get_settings.
	 *
	 * @version 1.2.0
	 * @since   1.0.0
	 */
	function get_settings() {
		global $current_section;
		return array_merge( apply_filters( 'woocommerce_get_settings_' . $this->id . '_' . $current_section, array() ), array(
			array(
				'title'     => __( 'Reset Settings', 'order-status-rules-for-woocommerce' ),
				'type'      => 'title',
				'id'        => $this->id . '_' . $current_section . '_reset_options',
			),
			array(
				'title'     => __( 'Reset section settings', 'order-status-rules-for-woocommerce' ),
				'desc'      => '<strong>' . __( 'Reset', 'order-status-rules-for-woocommerce' ) . '</strong>',
				'desc_tip'  => __( 'Check the box and save changes to reset.', 'order-status-rules-for-woocommerce' ),
				'id'        => $this->id . '_' . $current_section . '_reset',
				'default'   => 'no',
				'type'      => 'checkbox',
			),
			array(
				'type'      => 'sectionend',
				'id'        => $this->id . '_' . $current_section . '_reset_options',
			),
		) );
	}

	/**
	 * maybe_reset_settings.
	 *
	 * @version 1.2.0
	 * @since   1.0.0
	 */
	function maybe_reset_settings() {
		global $current_section;
		if ( 'yes' === get_option( $this->id . '_' . $current_section . '_reset', 'no' ) ) {
			foreach ( $this->get_settings() as $value ) {
				if ( isset( $value['id'] ) ) {
					$id = explode( '[', $value['id'] );
					delete_option( $id[0] );
				}
			}
			if ( method_exists( 'WC_Admin_Settings', 'add_message' ) ) {
				WC_Admin_Settings::add_message( __( 'Your settings have been reset.', 'order-status-rules-for-woocommerce' ) );
			} else {
				add_action( 'admin_notices', array( $this, 'admin_notice_settings_reset' ) );
			}
		}
	}

	/**
	 * admin_notice_settings_reset.
	 *
	 * @version 1.1.0
	 * @since   1.1.0
	 */
	function admin_notice_settings_reset() {
		echo '<div class="notice notice-warning is-dismissible"><p><strong>' .
			__( 'Your settings have been reset.', 'order-status-rules-for-woocommerce' ) . '</strong></p></div>';
	}

	/**
	 * Save settings.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 */
	function save() {
		parent::save();
		$this->maybe_reset_settings();
		do_action( 'alg_wc_order_status_rules_after_save_settings' );
	}

}

endif;

return new Alg_WC_Settings_Order_Status_Rules();
