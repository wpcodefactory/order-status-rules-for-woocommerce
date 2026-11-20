<?php
/**
 * Order Status Rules for WooCommerce - Process Payment Order Status Class
 *
 * @version 3.1.0
 * @since   3.1.0
 *
 * @author WPFactory
 *
 * @package WPFactory\WC_Order_Status_Rules
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Order_Status_Rules_Process_Payment' ) ) :

	/**
	 * Alg_WC_Order_Status_Rules_Process_Payment class.
	 *
	 * @version 3.1.0
	 * @since   3.1.0
	 */
	class Alg_WC_Order_Status_Rules_Process_Payment {

		/**
		 * Constructor.
		 *
		 * @version 3.1.0
		 * @since   3.1.0
		 *
		 * @todo (feature) Conditional, e.g., `$order->has_downloadable_item() ? 'on-hold' : 'processing'`.
		 */
		public function __construct() {
			// Direct bank transfer.
			add_filter(
				'woocommerce_bacs_process_payment_order_status',
				array( $this, 'bacs_process_payment_order_status' ),
				PHP_INT_MAX,
				2
			);

			// Check payments.
			add_filter(
				'woocommerce_cheque_process_payment_order_status',
				array( $this, 'cheque_process_payment_order_status' ),
				PHP_INT_MAX,
				2
			);

			// Cash on delivery (COD).
			add_filter(
				'woocommerce_cod_process_payment_order_status',
				array( $this, 'cod_process_payment_order_status' ),
				PHP_INT_MAX,
				2
			);
		}

		/**
		 * BACS process payment order status.
		 *
		 * @version 3.1.0
		 * @since   3.1.0
		 *
		 * @param string   $status Status.
		 * @param WC_Order $order  Order object.
		 *
		 * @return string Status.
		 */
		public function bacs_process_payment_order_status( $status, $order ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
			$_status = get_option( 'alg_wc_order_status_rules_bacs_process_payment_order_status', '' );
			return (
				'' !== $_status ?
				$_status :
				$status
			);
		}

		/**
		 * Cheque process payment order status.
		 *
		 * @version 3.1.0
		 * @since   3.1.0
		 *
		 * @param string   $status Status.
		 * @param WC_Order $order  Order object.
		 *
		 * @return string Status.
		 */
		public function cheque_process_payment_order_status( $status, $order ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
			$_status = get_option( 'alg_wc_order_status_rules_cheque_process_payment_order_status', '' );
			return (
				'' !== $_status ?
				$_status :
				$status
			);
		}

		/**
		 * COD process payment order status.
		 *
		 * @version 3.1.0
		 * @since   3.1.0
		 *
		 * @param string   $status Status.
		 * @param WC_Order $order  Order object.
		 *
		 * @return string Status.
		 */
		public function cod_process_payment_order_status( $status, $order ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
			$_status = get_option( 'alg_wc_order_status_rules_cod_process_payment_order_status', '' );
			return (
				'' !== $_status ?
				$_status :
				$status
			);
		}
	}

endif;

return new Alg_WC_Order_Status_Rules_Process_Payment();
