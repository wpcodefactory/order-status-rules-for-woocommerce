<?php
/**
 * Order Status Rules for WooCommerce - Crons Class
 *
 * @version 3.9.3
 * @since   1.4.0
 *
 * @author WPFactory
 *
 * @package WPFactory\WC_Order_Status_Rules
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Order_Status_Rules_Crons' ) ) :

	/**
	 * Alg_WC_Order_Status_Rules_Crons class.
	 *
	 * @version 3.9.3
	 * @since   1.4.0
	 */
	class Alg_WC_Order_Status_Rules_Crons {

		/**
		 * Constructor.
		 *
		 * @version 3.0.0
		 * @since   1.4.0
		 *
		 * @todo (dev) `add_custom_cron_schedules`: Only if `minutely`, `fifteen_minutes`, or `thirty_minutes` was selected.
		 * @todo (dev) Better `unschedule_cron()` (maybe on settings save?).
		 */
		public function __construct() {
			if ( 'yes' === get_option( 'alg_wc_order_status_rules_plugin_enabled', 'yes' ) ) {
				if ( 'yes' === get_option( 'alg_wc_order_status_rules_use_wp_cron', 'yes' ) ) {
					add_action( 'init', array( $this, 'schedule_cron' ) );
					add_action( 'alg_wc_order_status_rules_process_rules', array( $this, 'process_rules_cron' ) );
					add_filter( 'cron_schedules', array( $this, 'add_custom_cron_schedules' ) ); // phpcs:ignore WordPress.WP.CronInterval.CronSchedulesInterval
				} else {
					add_action( 'init', array( $this, 'unschedule_cron' ) );
				}
			}
		}

		/**
		 * Process rules cron.
		 *
		 * @version 3.9.3
		 * @since   1.4.0
		 *
		 * @todo (desc) `update_option( 'alg_wc_order_status_rules_process_rules_cron_time', time() );`
		 */
		public function process_rules_cron() {
			alg_wc_order_status_rules()->core->process_rules( false );
		}

		/**
		 * Get schedules.
		 *
		 * @version 1.4.0
		 * @since   1.4.0
		 *
		 * @todo (dev) Move this to `core` (no need for `core->crons` then)?
		 * @todo (desc) Better titles?
		 */
		public function get_schedules() {
			return array(
				'minutely'        => __( 'Once every minute', 'order-status-rules-for-woocommerce' ),
				'fifteen_minutes' => __( 'Once every 15 minutes', 'order-status-rules-for-woocommerce' ),
				'thirty_minutes'  => __( 'Once every 30 minutes', 'order-status-rules-for-woocommerce' ),
				'hourly'          => __( 'Once hourly', 'order-status-rules-for-woocommerce' ),
				'twicedaily'      => __( 'Twice daily', 'order-status-rules-for-woocommerce' ),
				'daily'           => __( 'Once daily', 'order-status-rules-for-woocommerce' ),
				'weekly'          => __( 'Once weekly', 'order-status-rules-for-woocommerce' ),
			);
		}

		/**
		 * Add custom cron schedules.
		 *
		 * @version 1.4.0
		 * @since   1.4.0
		 *
		 * @param array $schedules Existing cron schedules.
		 *
		 * @return array Modified cron schedules.
		 *
		 * @see https://developer.wordpress.org/reference/functions/wp_get_schedules/
		 *
		 * @todo (dev) Add more intervals?
		 */
		public function add_custom_cron_schedules( $schedules ) {
			$schedules['minutely']        = array(
				'interval' => MINUTE_IN_SECONDS,
				'display'  => __( 'Once Every Minute', 'order-status-rules-for-woocommerce' ),
			);
			$schedules['fifteen_minutes'] = array(
				'interval' => MINUTE_IN_SECONDS * 15,
				'display'  => __( 'Once Every 15 Minutes', 'order-status-rules-for-woocommerce' ),
			);
			$schedules['thirty_minutes']  = array(
				'interval' => MINUTE_IN_SECONDS * 30,
				'display'  => __( 'Once Every 30 Minutes', 'order-status-rules-for-woocommerce' ),
			);
			return $schedules;
		}

		/**
		 * Unschedule cron.
		 *
		 * @version 1.4.0
		 * @since   1.3.0
		 *
		 * @see https://developer.wordpress.org/reference/functions/wp_unschedule_event/
		 *
		 * @todo (dev) Also run on plugin deactivation?
		 * @todo (dev) Remove `alg_wc_order_status_rules_process_rules_time_schedule`.
		 */
		public function unschedule_cron() {
			foreach ( $this->get_schedules() as $schedule_id => $schedule_title ) {
				$event_timestamp = wp_next_scheduled( 'alg_wc_order_status_rules_process_rules', array( $schedule_id ) );
				if ( $event_timestamp ) {
					wp_unschedule_event( $event_timestamp, 'alg_wc_order_status_rules_process_rules', array( $schedule_id ) );
					update_option( 'alg_wc_order_status_rules_process_rules_time_schedule', false );
				}
			}
		}

		/**
		 * Schedule cron.
		 *
		 * @version 1.4.0
		 * @since   1.0.0
		 *
		 * @see https://developer.wordpress.org/reference/functions/wp_schedule_event/
		 *
		 * @todo (dev) Separate events for each rule.
		 * @todo (dev) Remove `alg_wc_order_status_rules_process_rules_time_schedule`.
		 */
		public function schedule_cron() {
			$selected_schedule = get_option( 'alg_wc_order_status_rules_wp_cron_schedule', 'hourly' );
			foreach ( $this->get_schedules() as $schedule_id => $schedule_title ) {
				$event_timestamp = wp_next_scheduled( 'alg_wc_order_status_rules_process_rules', array( $schedule_id ) );
				if ( $schedule_id === $selected_schedule ) {
					update_option( 'alg_wc_order_status_rules_process_rules_time_schedule', $event_timestamp );
					if ( ! $event_timestamp ) {
						wp_schedule_event( time(), $schedule_id, 'alg_wc_order_status_rules_process_rules', array( $schedule_id ) );
					}
				} elseif ( $event_timestamp ) {
					wp_unschedule_event( $event_timestamp, 'alg_wc_order_status_rules_process_rules', array( $schedule_id ) );
				}
			}
		}
	}

endif;

return new Alg_WC_Order_Status_Rules_Crons();
