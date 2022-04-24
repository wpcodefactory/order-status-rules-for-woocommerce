<?php
/**
 * Order Status Rules for WooCommerce - Advanced Section Settings
 *
 * @version 2.8.0
 * @since   1.5.0
 *
 * @author  Algoritmika Ltd.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Order_Status_Rules_Settings_Advanced' ) ) :

class Alg_WC_Order_Status_Rules_Settings_Advanced extends Alg_WC_Order_Status_Rules_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 2.0.0
	 * @since   1.5.0
	 *
	 * @todo    [next] (dev) split into separate sections: "Advanced", "My account", etc.
	 */
	function __construct() {
		$this->id   = 'advanced';
		$this->desc = __( 'Advanced', 'order-status-rules-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 2.8.0
	 * @since   1.5.0
	 *
	 * @todo    [now] (desc) Orders sorting
	 * @todo    [maybe] (dev) Orders sorting: Order by: add `none`, `name`, `type`?
	 * @todo    [next] (dev) `alg_wc_order_status_rules_non_matching`: default to `use_last_record`?
	 * @todo    [later] (desc) `alg_wc_order_status_rules_non_matching`: better desc?
	 * @todo    [maybe] (dev) `alg_wc_order_status_rules_non_matching`: add `use_date_created` and/or `use_date_modified` options
	 * @todo    [maybe] (dev) My Account: "... next status change is scheduled on..."
	 * @todo    [maybe] (desc) `alg_wc_order_status_rules_compatibility_doctreat`: better desc, e.g. add link to the theme?
	 * @todo    [maybe] (desc) `alg_wc_order_status_rules_disabled_conditions`: better desc?
	 */
	function get_settings() {

		add_action( 'admin_footer', array( $this, 'add_admin_script' ) );

		$advanced_settings = array(
			array(
				'title'    => __( 'Advanced Options', 'order-status-rules-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_order_status_rules_advanced_options',
			),
			array(
				'title'    => __( 'Orders sorting', 'order-status-rules-for-woocommerce' ),
				'desc'     => __( 'Order by', 'order-status-rules-for-woocommerce' ),
				'id'       => 'alg_wc_order_status_rules_wc_get_orders_args[orderby]',
				'default'  => 'date',
				'type'     => 'select',
				'class'    => 'chosen_select',
				'options'  => array(
					'ID'       => __( 'ID', 'order-status-rules-for-woocommerce' ),
					'rand'     => __( 'Random', 'order-status-rules-for-woocommerce' ),
					'date'     => __( 'Date', 'order-status-rules-for-woocommerce' ),
					'modified' => __( 'Modified', 'order-status-rules-for-woocommerce' ),
				),
			),
			array(
				'desc'     => __( 'Order', 'order-status-rules-for-woocommerce' ),
				'id'       => 'alg_wc_order_status_rules_wc_get_orders_args[order]',
				'default'  => 'DESC',
				'type'     => 'select',
				'class'    => 'chosen_select',
				'options'  => array(
					'DESC' => __( 'Descending', 'order-status-rules-for-woocommerce' ),
					'ASC'  => __( 'Ascending', 'order-status-rules-for-woocommerce' ),
				),
			),
			array(
				'title'    => __( 'Rules processing hooks', 'order-status-rules-for-woocommerce' ),
				'id'       => 'alg_wc_order_status_rules_hooks',
				'default'  => array( 'woocommerce_order_status_changed' ),
				'type'     => 'multiselect',
				'class'    => 'chosen_select',
				'options'  => apply_filters( 'alg_wc_order_status_rules_hooks', array(
					'woocommerce_order_status_changed' => __( 'Order status changed', 'order-status-rules-for-woocommerce' ),
				) ),
			),
			array(
				'title'    => __( 'Allow rules processing via URL', 'order-status-rules-for-woocommerce' ),
				'desc'     => __( 'Enable', 'order-status-rules-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'This will allow to initiate all rules processing via URL: %s.', 'order-status-rules-for-woocommerce' ),
					'<code>' . add_query_arg( 'alg_wc_order_status_rules_process_rules', '', get_site_url() ) . '</code>' ) . '<br>' .
					sprintf( __( 'For example, this could be useful if you are going to disable %s and use "real" (i.e. server) cron jobs instead.', 'order-status-rules-for-woocommerce' ),
						'<span style="text-decoration:underline;">' . __( 'Periodical Processing Options', 'order-status-rules-for-woocommerce' ) . '</span>' ),
				'id'       => 'alg_wc_order_status_rules_allow_url',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Debug', 'order-status-rules-for-woocommerce' ),
				'desc'     => __( 'Enable', 'order-status-rules-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'Will add a log to %s.', 'order-status-rules-for-woocommerce' ),
					'<a target="_blank" href="' . admin_url( 'admin.php?page=wc-status&tab=logs' ) . '">' .
						__( 'WooCommerce > Status > Logs', 'order-status-rules-for-woocommerce' ) . '</a>' ),
				'id'       => 'alg_wc_order_status_rules_debug',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Disabled conditions', 'order-status-rules-for-woocommerce' ),
				'desc_tip' => __( 'Removes the selected conditions from each rule\'s settings.', 'order-status-rules-for-woocommerce' ),
				'desc'     => $this->get_select_all_buttons(),
				'id'       => 'alg_wc_order_status_rules_disabled_conditions',
				'default'  => array(),
				'type'     => 'multiselect',
				'class'    => 'chosen_select',
				'options'  => alg_wc_order_status_rules()->core->conditions->get(),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_order_status_rules_advanced_options',
			),
		);

		$status_history_settings = array(
			array(
				'title'    => __( 'Order Status History Options', 'order-status-rules-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_order_status_rules_status_history_options',
			),
			array(
				'title'    => __( 'Empty order status history', 'order-status-rules-for-woocommerce' ),
				'desc_tip' => __( 'Plugin must be enabled at the time order status change occurs, so there is no order status change history on initial plugin install. This can be solved by using order creation (or modification) date instead (i.e. instead of real status change date). This is ignored for orders with available real status change history.', 'order-status-rules-for-woocommerce' ),
				'id'       => 'alg_wc_order_status_rules_no_history',
				'default'  => 'use_date_modified',
				'type'     => 'select',
				'class'    => 'chosen_select',
				'options'  => array(
					'do_nothing'        => __( 'Do nothing', 'order-status-rules-for-woocommerce' ),
					'use_date_created'  => __( 'Use order date created', 'order-status-rules-for-woocommerce' ),
					'use_date_modified' => __( 'Use order date modified', 'order-status-rules-for-woocommerce' ),
				),
			),
			array(
				'title'    => __( 'Non-matching order status', 'order-status-rules-for-woocommerce' ),
				'desc_tip' => __( 'If order status is not properly changed (e.g. by some plugin), it may happen that current order status does not match the last record in order status change history.', 'order-status-rules-for-woocommerce' ) . ' ' .
					__( 'In this case order status rules will not be applied.', 'order-status-rules-for-woocommerce' ) . ' ' .
					__( 'You can change this behaviour here.', 'order-status-rules-for-woocommerce' ),
				'id'       => 'alg_wc_order_status_rules_non_matching',
				'default'  => 'do_nothing',
				'type'     => 'select',
				'class'    => 'chosen_select',
				'options'  => array(
					'do_nothing'        => __( 'Do nothing', 'order-status-rules-for-woocommerce' ),
					'use_last_record'   => __( 'Use latest record anyway', 'order-status-rules-for-woocommerce' ),
				),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_order_status_rules_status_history_options',
			),
		);

		$periodical_settings = array(
			array(
				'title'    => __( 'Periodical Processing Options', 'order-status-rules-for-woocommerce' ),
				'desc'     => __( 'Although it\'s possible to enable both periodical processing options, we recommend enabling only one of them.', 'order-status-rules-for-woocommerce' ) . '<br>' .
					sprintf( __( 'If you are going to disable both periodical processing options, you may want to enable the %s option and set up "real" (i.e. server) cron job.', 'order-status-rules-for-woocommerce' ),
						'<span style="text-decoration:underline;">' . __( 'Allow rules processing via URL', 'order-status-rules-for-woocommerce' ) . '</span>' ) . ' ' .
					sprintf( __( 'Also you can use our %s tool manually.', 'order-status-rules-for-woocommerce' ),
						'<a target="_blank" href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_wc_order_status_rules&section' ) . '">' . __( 'Run all rules now', 'order-status-rules-for-woocommerce' ) . '</a>' ),
				'type'     => 'title',
				'id'       => 'alg_wc_order_status_rules_periodical_options',
			),
			array(
				'title'    => __( 'Use WP cron', 'order-status-rules-for-woocommerce' ),
				'desc'     => __( 'Enable', 'order-status-rules-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'This will use %s to process the rules periodically.', 'order-status-rules-for-woocommerce' ),
						'<a href="https://developer.wordpress.org/plugins/cron/" target="_blank">' .
							__( 'WordPress crons', 'order-status-rules-for-woocommerce' ) . '</a>' ),
				'id'       => 'alg_wc_order_status_rules_use_wp_cron',
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'desc'     => __( 'Interval', 'order-status-rules-for-woocommerce' ),
				'id'       => 'alg_wc_order_status_rules_wp_cron_schedule',
				'default'  => 'hourly',
				'type'     => 'select',
				'class'    => 'chosen_select',
				'options'  => alg_wc_order_status_rules()->core->crons->get_schedules(),
			),
			array(
				'title'    => __( 'Use Action Scheduler', 'order-status-rules-for-woocommerce' ),
				'desc'     => __( 'Enable', 'order-status-rules-for-woocommerce' ),
				'desc_tip' => sprintf( __( 'This will use %s to process the rules periodically.', 'order-status-rules-for-woocommerce' ),
						'<a href="https://actionscheduler.org/" target="_blank">' .
							__( 'Action Scheduler', 'order-status-rules-for-woocommerce' ) . '</a>' ) .
					'<br>* ' . sprintf( __( 'Action Scheduler has a built in <a href="%s" target="_blank">administration screen</a> for monitoring, debugging and manually triggering scheduled actions. Search for the %s hook there.', 'order-status-rules-for-woocommerce' ),
						admin_url( 'admin.php?page=wc-status&tab=action-scheduler' ), '<code>' . alg_wc_order_status_rules()->core->action_scheduler->action . '</code>' ),
				'id'       => 'alg_wc_order_status_rules_use_action_scheduler',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'desc'     => __( 'Interval (in seconds)', 'order-status-rules-for-woocommerce' ),
				'id'       => 'alg_wc_order_status_rules_action_scheduler_interval',
				'default'  => 3600,
				'type'     => 'number',
				'custom_attributes'  => array( 'min' => 60 ),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_order_status_rules_periodical_options',
			),
		);

		$compatibility_settings = array(
			array(
				'title'    => __( 'Compatibility Options', 'order-status-rules-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_order_status_rules_compatibility_options',
			),
			array(
				'title'    => __( '"Doctreat" theme', 'order-status-rules-for-woocommerce' ),
				'desc'     => __( 'Enable', 'order-status-rules-for-woocommerce' ),
				'desc_tip' => __( 'For the "Doctreat - Doctors Directory WordPress Theme" by AmentoTech.', 'order-status-rules-for-woocommerce' ),
				'id'       => 'alg_wc_order_status_rules_compatibility_doctreat',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_order_status_rules_compatibility_options',
			),
		);

		$my_account_settings = array(
			array(
				'title'    => __( '"My Account" Options', 'order-status-rules-for-woocommerce' ),
				'desc'     => __( 'This is a "bonus" feature of the plugin. As plugin tracks order status changes, we can display them on customer\'s "My Account" page.', 'order-status-rules-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_order_status_rules_my_account_options',
			),
			array(
				'title'    => __( 'My Account > Orders', 'order-status-rules-for-woocommerce' ),
				'desc'     => '<strong>' . __( 'Enable', 'order-status-rules-for-woocommerce' ) . '</strong>',
				'desc_tip' => __( 'Adds order status history to "My Account > Orders".', 'order-status-rules-for-woocommerce' ),
				'id'       => 'alg_wc_osr_my_account_orders_status_history_enabled',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Position', 'order-status-rules-for-woocommerce' ),
				'id'       => 'alg_wc_osr_my_account_orders_status_history_position',
				'default'  => 'order-status',
				'type'     => 'select',
				'class'    => 'chosen_select',
				'options'  => array(
					'order-status'       => __( '"Status" column', 'order-status-rules-for-woocommerce' ),
					'alg-wc-osr-history' => __( 'New column', 'order-status-rules-for-woocommerce' ),
				),
			),
			array(
				'title'    => __( 'Column title', 'order-status-rules-for-woocommerce' ),
				'desc'     => __( 'Ignored unless "New column" is selected for the "Position".', 'order-status-rules-for-woocommerce' ),
				'id'       => 'alg_wc_osr_my_account_orders_status_history_column_title',
				'default'  => __( 'History', 'order-status-rules-for-woocommerce' ),
				'type'     => 'text',
			),
			array(
				'title'    => __( 'Templates', 'order-status-rules-for-woocommerce' ),
				'desc'     => __( 'Before', 'order-status-rules-for-woocommerce' ) . '<br>' .
					sprintf( __( 'Available placeholders: %s', 'order-status-rules-for-woocommerce' ), '<code>' . implode( '</code>, <code>', array(
							'%current_status%',
						) ) . '</code>' ),
				'id'       => 'alg_wc_osr_my_account_orders_status_history_templates[before]',
				'default'  => '%current_status%',
				'type'     => 'textarea',
			),
			array(
				'desc'     => __( 'Each record', 'order-status-rules-for-woocommerce' ) . '<br>' .
					sprintf( __( 'Available placeholders: %s', 'order-status-rules-for-woocommerce' ), '<code>' . implode( '</code>, <code>', array(
							'%record_nr%',
							'%record_date%',
							'%record_time%',
							'%status_from%',
							'%status_to%',
						) ) . '</code>' ),
				'desc_tip' => sprintf( __( '%s is a HTML code for the right arrow symbol.', 'order-status-rules-for-woocommerce' ), htmlentities( '&amp;rarr;' ) ),
				'id'       => 'alg_wc_osr_my_account_orders_status_history_templates[each_record]',
				'default'  => '<br>%status_from% &rarr; %status_to%',
				'type'     => 'textarea',
			),
			array(
				'desc'     => __( 'After', 'order-status-rules-for-woocommerce' ) . '<br>' .
					sprintf( __( 'Available placeholders: %s', 'order-status-rules-for-woocommerce' ), '<code>' . implode( '</code>, <code>', array(
							'%current_status%',
						) ) . '</code>' ),
				'id'       => 'alg_wc_osr_my_account_orders_status_history_templates[after]',
				'default'  => '',
				'type'     => 'textarea',
			),
			array(
				'title'    => __( 'Reverse status history', 'order-status-rules-for-woocommerce' ),
				'desc'     => __( 'Enable', 'order-status-rules-for-woocommerce' ),
				'id'       => 'alg_wc_osr_my_account_orders_status_history_reverse',
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_order_status_rules_my_account_options',
			),
		);

		return array_merge( $advanced_settings, $status_history_settings, $periodical_settings, $compatibility_settings, $my_account_settings );
	}

}

endif;

return new Alg_WC_Order_Status_Rules_Settings_Advanced();
