<?php
/**
 * Order Status Rules for WooCommerce - Action Scheduler Class
 *
 * @version 3.9.6
 * @since   2.3.0
 *
 * @author WPFactory
 *
 * @package WPFactory\WC_Order_Status_Rules
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Order_Status_Rules_Action_Scheduler' ) ) :

	/**
	 * Alg_WC_Order_Status_Rules_Action_Scheduler class.
	 *
	 * @version 3.9.6
	 * @since   2.3.0
	 */
	class Alg_WC_Order_Status_Rules_Action_Scheduler {

		/**
		 * Action.
		 *
		 * @version 3.5.0
		 * @since   3.5.0
		 *
		 * @var string
		 */
		public $action;

		/**
		 * Constructor.
		 *
		 * @version 2.3.0
		 * @since   2.3.0
		 *
		 * @see https://actionscheduler.org/
		 */
		public function __construct() {
			$this->action = 'alg_wc_order_status_rules_process_rules_as';
			if ( 'yes' === get_option( 'alg_wc_order_status_rules_plugin_enabled', 'yes' ) ) {
				if ( 'yes' === get_option( 'alg_wc_order_status_rules_use_action_scheduler', 'no' ) ) {
					add_action( 'init', array( $this, 'schedule_action' ) );
					add_action( $this->action, array( $this, 'process_rules_action_scheduler' ) );
				} else {
					add_action( 'init', array( $this, 'unschedule_action' ) );
				}
			}
		}

		/**
		 * Process rules action scheduler.
		 *
		 * @version 3.9.6
		 * @since   2.3.0
		 */
		public function process_rules_action_scheduler() {
			alg_wc_order_status_rules()->core->process_rules( false );
		}

		/**
		 * Unschedule action.
		 *
		 * @version 2.3.0
		 * @since   2.3.0
		 */
		public function unschedule_action() {
			as_unschedule_all_actions( $this->action );
		}

		/**
		 * Schedule action.
		 *
		 * @version 2.3.0
		 * @since   2.3.0
		 */
		public function schedule_action() {
			if (
				function_exists( 'as_has_scheduled_action' ) &&
				( $interval_in_seconds = get_option( 'alg_wc_order_status_rules_action_scheduler_interval', 3600 ) ) && // phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.FoundInControlStructure, Generic.CodeAnalysis.AssignmentInCondition.Found
				false === as_has_scheduled_action( $this->action, array( $interval_in_seconds ) )
			) {
				as_unschedule_all_actions( $this->action );
				as_schedule_recurring_action(
					time(),
					$interval_in_seconds,
					$this->action,
					array( $interval_in_seconds )
				);
			}
		}
	}

endif;

return new Alg_WC_Order_Status_Rules_Action_Scheduler();
