<?php
/**
 * Order Status Rules for WooCommerce - Default Status Class
 *
 * @version 3.1.0
 * @since   3.1.0
 *
 * @author WPFactory
 *
 * @package WPFactory\WC_Order_Status_Rules
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Order_Status_Rules_Default_Status' ) ) :

	/**
	 * Alg_WC_Order_Status_Rules_Default_Status class.
	 *
	 * @version 3.1.0
	 * @since   3.1.0
	 */
	class Alg_WC_Order_Status_Rules_Default_Status {

		/**
		 * Constructor.
		 *
		 * @version 3.1.0
		 * @since   3.1.0
		 *
		 * @todo (feature) Conditional, e.g., per order payment gateway.
		 */
		public function __construct() {
			add_filter(
				'woocommerce_default_order_status',
				array( $this, 'default_order_status' ),
				PHP_INT_MAX
			);
		}

		/**
		 * Default order status.
		 *
		 * @param string $status Current default status.
		 *
		 * @return string Modified default status.
		 *
		 * @version 3.1.0
		 * @since   3.1.0
		 */
		public function default_order_status( $status ) {
			$_status = get_option( 'alg_wc_order_status_rules_default_order_status', '' );
			return (
				'' !== $_status ?
				$_status :
				$status
			);
		}
	}

endif;

return new Alg_WC_Order_Status_Rules_Default_Status();
