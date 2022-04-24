<?php
/**
 * Order Status Rules for WooCommerce - Core Class
 *
 * @version 2.8.0
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Order_Status_Rules_Core' ) ) :

class Alg_WC_Order_Status_Rules_Core {

	/**
	 * Constructor.
	 *
	 * @version 2.8.0
	 * @since   1.0.0
	 *
	 * @todo    [next] (dev) remove `alg_wc_order_status_rules_plugin_enabled` (or move `process_rules_manual`, etc. inside the `alg_wc_order_status_rules_plugin_enabled`)
	 * @todo    [maybe] (dev) remove rules with trigger set to zero from crons?
	 * @todo    [maybe] (dev) `process_rules_for_order` on admin order edit page, order updated action, etc.?
	 * @todo    [maybe] (dev) `process_rules_for_order` on `woocommerce_order_status_changed`: use `woocommerce_order_status_ . $status_to` filter instead?
	 * @todo    [maybe] (dev) pre-check for possible infinite loops in rules
	 */
	function __construct() {

		if ( 'yes' === get_option( 'alg_wc_order_status_rules_plugin_enabled', 'yes' ) ) {

			// Track order status change
			add_action( 'woocommerce_order_status_changed', array( $this, 'save_status_change' ), 1, 4 );

			// Hooks (e.g. immediately process rules on any order status change)
			$hooks = get_option( 'alg_wc_order_status_rules_hooks', array( 'woocommerce_order_status_changed' ) );
			foreach ( $hooks as $hook ) {
				add_action( $hook, array( $this, 'process_rules_for_order' ) );
			}

			// Process rules via URL
			if ( 'yes' === get_option( 'alg_wc_order_status_rules_allow_url', 'no' ) ) {
				add_action( 'init', array( $this, 'process_rules_url' ), PHP_INT_MAX );
			}

			// Admin order edit page meta box, etc.
			require_once( 'class-alg-wc-order-status-rules-admin.php' );

			// My account > Orders
			require_once( 'class-alg-wc-order-status-rules-my-account.php' );

			// Compatibility
			require_once( 'class-alg-wc-order-status-rules-compatibility.php' );

		}

		// Conditions
		$this->conditions = require_once( 'class-alg-wc-order-status-rules-conditions.php' );

		// Crons
		$this->crons = require_once( 'class-alg-wc-order-status-rules-crons.php' );

		// Action scheduler
		$this->action_scheduler = require_once( 'class-alg-wc-order-status-rules-action-scheduler.php' );

		// Process rules manually
		add_action( 'alg_wc_order_status_rules_after_save_settings', array( $this, 'process_rules_manual' ) );

	}

	/**
	 * add_to_log.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 */
	function add_to_log( $message ) {
		if ( function_exists( 'wc_get_logger' ) && ( $log = wc_get_logger() ) ) {
			$log->log( 'info', $message, array( 'source' => 'order-status-rules' ) );
		}
	}

	/**
	 * do_debug.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 */
	function do_debug() {
		if ( ! isset( $this->do_debug ) ) {
			$this->do_debug = ( 'yes' === get_option( 'alg_wc_order_status_rules_debug', 'no' ) );
		}
		return $this->do_debug;
	}

	/**
	 * on_no_history.
	 *
	 * @version 1.8.0
	 * @since   1.4.0
	 */
	function on_no_history() {
		if ( ! isset( $this->on_no_history ) ) {
			$this->on_no_history = get_option( 'alg_wc_order_status_rules_no_history', 'use_date_modified' );
		}
		return $this->on_no_history;
	}

	/**
	 * process_rules_url.
	 *
	 * @version 1.3.0
	 * @since   1.3.0
	 *
	 * @todo    [maybe] (dev) optional "key" (for security)
	 * @todo    [maybe] (dev) optional "rule ID to process"
	 */
	function process_rules_url() {
		if ( isset( $_REQUEST['alg_wc_order_status_rules_process_rules'] ) ) {
			$this->process_rules();
		}
	}

	/**
	 * process_rules_manual.
	 *
	 * @version 1.2.0
	 * @since   1.2.0
	 */
	function process_rules_manual() {
		if ( 'yes' === get_option( 'alg_wc_order_status_rules_run_now', 'no' ) ) {
			update_option( 'alg_wc_order_status_rules_run_now', 'no' );
			$this->process_rules( false );
			if ( method_exists( 'WC_Admin_Settings', 'add_message' ) ) {
				WC_Admin_Settings::add_message( __( '"Run all rules now" tool executed.', 'order-status-rules-for-woocommerce' ) );
			} else {
				add_action( 'admin_notices', array( $this, 'admin_notice_process_rules_manual' ) );
			}
		}
	}

	/**
	 * admin_notice_process_rules_manual.
	 *
	 * @version 1.2.0
	 * @since   1.2.0
	 */
	function admin_notice_process_rules_manual() {
		echo '<div class="notice notice-warning is-dismissible"><p><strong>' .
			__( '"Run all rules now" tool executed.', 'order-status-rules-for-woocommerce' ) . '</strong></p></div>';
	}

	/**
	 * get_trigger_time_skip_days.
	 *
	 * @version 2.4.0
	 * @since   2.4.0
	 */
	function get_trigger_time_skip_days( $last_record_time, $trigger_time, $skip_days = false ) {
		if ( ! empty( $skip_days ) && count( $skip_days ) < 7 ) {
			$total = $last_record_time;
			$valid = 0;
			while ( $valid <= $trigger_time ) {
				$step = ( strtotime( 'tomorrow', $total ) - $total );
				if ( ! in_array( date( 'N', $total ), $skip_days ) ) {
					$valid += $step;
				}
				$total += $step;
			}
			$trigger_time = ( $total - $last_record_time - ( $valid - $trigger_time ) );
		}
		return $trigger_time;
	}

	/**
	 * get_time_remaining.
	 *
	 * @version 2.4.0
	 * @since   1.0.1
	 *
	 * @todo    [now] (dev) rename `get_time_remaining()` to `get_seconds_remaining()`, `$last_record_time` to `$start`, `$trigger_time` to `$offset`, `get_trigger_time_skip_days()` to `get_offset_skip_days()`?
	 */
	function get_time_remaining( $last_record_time, $trigger_time, $skip_days = false, $current_time = false ) {
		return ( $last_record_time + $this->get_trigger_time_skip_days( $last_record_time, $trigger_time, $skip_days ) - ( $current_time ? $current_time : current_time( 'timestamp' ) ) );
	}

	/**
	 * get_order_status_change_history.
	 *
	 * @version 1.7.0
	 * @since   1.4.0
	 *
	 * @todo    [next] (dev) use `getTimestamp()`, not `getOffsetTimestamp()`
	 */
	function get_order_status_change_history( $order_id ) {
		$data = get_post_meta( $order_id, '_alg_wc_order_status_change_history', true );
		if ( empty( $data ) && 'do_nothing' != $this->on_no_history() ) {
			$order = wc_get_order( $order_id );
			if ( ( $date = ( 'use_date_created' === $this->on_no_history() ? $order->get_date_created() : $order->get_date_modified() ) ) ) {
				$data = array(
					array(
						'time' => $date->getOffsetTimestamp(),
						'from' => 'N/A',
						'to'   => $order->get_status(),
					),
				);
			}
		}
		return $data;
	}

	/**
	 * init_options.
	 *
	 * @version 2.8.0
	 * @since   1.6.0
	 *
	 * @todo    [next] [!] (dev) call this only once, e.g. in constructor, or on `init` action
	 * @todo    [next] (dev) `$this->options`: rename?
	 * @todo    [next] (dev) `$this->options['from']`: redo?
	 * @todo    [maybe] (dev) code refactoring?
	 */
	function init_options() {
		if ( ! isset( $this->options ) ) {

			// Rules options: General
			$this->options = array(
				'enabled'                => get_option( 'alg_wc_order_status_rules_enabled',                array() ),
				'from'                   => get_option( 'alg_wc_order_status_rules_from',                   array() ),
				'to'                     => get_option( 'alg_wc_order_status_rules_to',                     array() ),
				'time_triggers'          => get_option( 'alg_wc_order_status_rules_time_trigger',           array() ),
				'time_trigger_units'     => get_option( 'alg_wc_order_status_rules_time_trigger_unit',      array() ),
				'skip_days'              => get_option( 'alg_wc_order_status_rules_skip_days',              array() ),
				'titles'                 => get_option( 'alg_wc_order_status_rules_title',                  array() ),
			);

			// Rules options: Conditions
			$disabled_conditions = get_option( 'alg_wc_order_status_rules_disabled_conditions', array() );
			foreach ( $this->conditions->get() as $condition_id => $condition_title ) {
				$is_enabled = ( ! in_array( $condition_id, $disabled_conditions ) );
				switch ( $condition_id ) {
					case 'meta':
						$keys = array( 'meta_key', 'meta_value', 'meta_value_is_multiple' );
						break;
					case 'coupons':
						$keys = array( 'coupons', 'specific_coupons' );
						break;
					case 'products':
					case 'product_cats':
					case 'product_tags':
					case 'product_stock_status':
						$keys = array( $condition_id, "{$condition_id}_require_all" );
						break;
					default:
						$keys = array( $condition_id );
				}
				foreach ( $keys as $key ) {
					$this->options[ $key ] = ( $is_enabled ? get_option( 'alg_wc_order_status_rules_' . $key, array() ) : array() );
				}
			}
			$this->options['from'] = array_slice( $this->options['from'], 0, apply_filters( 'alg_wc_order_status_rules_rules_total', 1 ), true );

			// General options
			$this->do_use_last_record = ( 'use_last_record' === get_option( 'alg_wc_order_status_rules_non_matching', 'do_nothing' ) );

		}
	}

	/**
	 * do_apply_rule.
	 *
	 * @version 2.8.0
	 * @since   1.6.0
	 *
	 * @todo    [now] (dev) safe-check: `status_from != status_to`
	 */
	function do_apply_rule( $rule_id, $args ) {
		$this->init_options();
		$result = (
			( isset( $this->options['enabled'][ $rule_id ] ) && 'yes' === $this->options['enabled'][ $rule_id ] ) &&
			( isset( $this->options['time_triggers'][ $rule_id ] ) ) &&
			( isset( $this->options['to'][ $rule_id ] ) ) &&
			( 'wc-' . $args['order_status'] == $args['from'] && 'wc-' . $args['last_record']['to'] == $args['from'] ) &&
			$this->conditions->check( $this->options, $rule_id, $args )
		);
		return apply_filters( 'alg_wc_order_status_rules_do_apply_rule', $result, $args['order'], $rule_id, $args );
	}

	/**
	 * process_rules.
	 *
	 * @version 2.5.1
	 * @since   1.0.0
	 *
	 * @see     https://github.com/woocommerce/woocommerce/wiki/wc_get_orders-and-WC_Order_Query
	 *
	 * @todo    [next] (dev) code refactoring: `args`, `step`, `skip`?
	 * @todo    [next] (dev) optimization: stop on `( $last_record['to'] !== $args['order_status'] && ! $this->do_use_last_record )`
	 * @todo    [next] (dev) optimization: `$this->options['from']`: enabled rules only
	 * @todo    [next] (dev) optimization: meta query: `_alg_wc_order_status_change_history` not empty? (only if `'do_nothing' === $this->on_no_history()`)
	 * @todo    [next] (dev) log: add more info
	 * @todo    [next] (desc) use `alg_wc_order_status_rules_process_rules_time_run`
	 */
	function process_rules( $do_die = true ) {
		update_option( 'alg_wc_order_status_rules_process_rules_time_run', time() );
		if ( $this->do_debug() ) {
			$this->add_to_log( __( 'Process rules: Started', 'order-status-rules-for-woocommerce' ) .
				( ! $do_die ? ' (' . __( 'manual', 'order-status-rules-for-woocommerce' ) . ')' : '' ) );
		}
		$this->init_options();
		if ( ! empty( $this->options['from'] ) ) {
			$user_args = get_option( 'alg_wc_order_status_rules_wc_get_orders_args', array() );
			$orders = wc_get_orders( array(
				'limit'    => -1,
				'status'   => $this->options['from'],
				'return'   => 'ids',
				'orderby'  => ( isset( $user_args['orderby'] ) ? $user_args['orderby'] : 'date' ),
				'order'    => ( isset( $user_args['order'] )   ? $user_args['order']   : 'DESC' ),
			) );
			foreach ( $orders as $order_id ) {
				$this->process_rules_for_order( $order_id );
			}
		}
		if ( $this->do_debug() ) {
			$this->add_to_log( __( 'Process rules: Finished', 'order-status-rules-for-woocommerce' ) );
		}
		if ( isset( $_REQUEST['alg_wc_order_status_rules_process_rules_redirect'] ) ) {
			wp_redirect( esc_url_raw( $_REQUEST['alg_wc_order_status_rules_process_rules_redirect'] ) );
			exit;
		}
		if ( $do_die ) {
			die();
		}
	}

	/**
	 * process_rules_for_order.
	 *
	 * @version 2.8.0
	 * @since   2.2.0
	 *
	 * @todo    [now] (dev) `$unit = ( isset( $this->options['time_trigger_units'][ $i ] ) ? $this->options['time_trigger_units'][ $i ] : 'hour' );`
	 * @todo    [now] (dev) rename `$i` to `$rule_id`?
	 */
	function process_rules_for_order( $order_id ) {
		$this->init_options();
		if ( $this->do_debug() ) {
			$this->add_to_log( sprintf( __( 'Process rules: Order #%s', 'order-status-rules-for-woocommerce' ), $order_id ) );
		}
		$status_history = $this->get_order_status_change_history( $order_id );
		if ( ! empty( $status_history ) ) {
			$status_history      = array_reverse( $status_history, true );
			$last_record         = current( $status_history );
			$order               = wc_get_order( $order_id );
			$args                = array( 'order_status' => $order->get_status(), 'order' => $order );
			$last_record['to']   = ( $last_record['to'] !== $args['order_status'] && $this->do_use_last_record ? $args['order_status'] : $last_record['to'] );
			$args['last_record'] = $last_record;
			foreach ( $this->options['from'] as $i => $from ) {
				$args['from'] = $from;
				$step = $this->get_trigger_unit_step( ( isset( $this->options['time_trigger_units'][ $i ] ) ? $this->options['time_trigger_units'][ $i ] : 'hour' ) );
				$skip = ( isset( $this->options['skip_days'][ $i ] ) ? $this->options['skip_days'][ $i ] : false );
				if (
					$this->do_apply_rule( $i, $args ) &&
					( $this->get_time_remaining( $last_record['time'], $this->options['time_triggers'][ $i ] * $step, $skip ) <= 0 )
				) {
					$rule = sprintf( __( 'Rule #%s', 'order-status-rules-for-woocommerce' ), $i ) .
						( ! empty( $this->options['titles'][ $i ] ) ? ': ' . $this->options['titles'][ $i ] : '' );
					$note = sprintf( __( 'Status updated by "Order Status Rules for WooCommerce" plugin (%s).', 'order-status-rules-for-woocommerce' ), $rule );
					remove_action( 'woocommerce_order_status_changed', array( $this, 'process_rules_for_order' ), 11 );
					$order->update_status( substr( $this->options['to'][ $i ], 3 ), $note );
					add_action(    'woocommerce_order_status_changed', array( $this, 'process_rules_for_order' ), 11 );
					// Debug
					if ( $this->do_debug() ) {
						$this->add_to_log( sprintf( __( 'Process rules: Order #%s: from %s to %s (%s)', 'order-status-rules-for-woocommerce' ),
							$order_id, $from, $this->options['to'][ $i ], $rule ) );
					}
					break;
				}
			}
		} elseif ( $this->do_debug() ) {
			$this->add_to_log( sprintf( __( 'Process rules: Order #%s: No order status change history found', 'order-status-rules-for-woocommerce' ), $order_id ) );
		}
	}

	/**
	 * get_trigger_unit_step.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 */
	function get_trigger_unit_step( $unit ) {
		switch ( $unit ) {
			case 'second':
				return 1;
			case 'minute':
				return MINUTE_IN_SECONDS;
			case 'hour':
				return HOUR_IN_SECONDS;
			case 'day':
				return DAY_IN_SECONDS;
			case 'week':
				return WEEK_IN_SECONDS;
			default: // 'hour'
				return HOUR_IN_SECONDS;
		}
	}

	/**
	 * save_status_change.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 *
	 * @todo    [next] (dev) when `$from` doesn't exist `woocommerce_order_status_changed` is not called; check `do_action( 'woocommerce_order_status_' . $status_transition['to'], $this->get_id(), $this );`
	 * @todo    [next] (dev) save `time()`, not `current_time()`
	 * @todo    [maybe] (dev) mark status change as "changed by plugin" (vs "changed manually/otherwise")
	 */
	function save_status_change( $order_id, $from, $to, $order ) {
		$status_history = get_post_meta( $order_id, '_alg_wc_order_status_change_history', true );
		if ( empty( $status_history ) ) {
			$status_history = array();
		}
		$status_history[] = array(
			'time' => current_time( 'timestamp' ),
			'from' => $from,
			'to'   => $to,
		);
		update_post_meta( $order_id, '_alg_wc_order_status_change_history', $status_history );
	}

	/**
	 * get_date_time_format.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 */
	function get_date_time_format() {
		return get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
	}

}

endif;

return new Alg_WC_Order_Status_Rules_Core();
