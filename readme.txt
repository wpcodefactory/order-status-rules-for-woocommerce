=== Order Status Rules for WooCommerce ===
Contributors: algoritmika, anbinder
Tags: woocommerce, order status, order, status, woo commerce
Requires at least: 4.4
Tested up to: 5.9
Stable tag: 2.8.1
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Manage WooCommerce order statuses. Beautifully.

== Description ==

**Order Status Rules for WooCommerce** plugin lets you schedule automatic WooCommerce order status changes.

For example: automatically change order status to cancelled, when 24 hours have passed since order status was set to pending.

### &#9989; Order Status Rule Options ###

* Order **status from**.
* Order **status to**.
* **Time trigger** (in seconds, minutes, hours, days or weeks).
* Minimum and maximum **order amount** and **order quantity**.
* Required **payment gateways**.
* Required **shipping methods**.
* Required billing and shipping **countries**.
* Required **products**, product **categories**, product **tags** or product **stock status**.
* Required **users** or user **roles**.
* Required **coupons**.
* And more...

### &#128472; Feedback ###

* We are open to your suggestions and feedback. Thank you for using or trying out one of our plugins!
* [Visit plugin site](https://wpfactory.com/item/order-status-rules-for-woocommerce/).

== Installation ==

1. Upload the entire plugin folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. Start by visiting plugin settings at "WooCommerce > Settings > Order Status Rules".

== Changelog ==

= 2.8.2 - 18/07/2022 =
* Dev - Add deploy script.

= 2.8.1 - 21/04/2022 =
* Fix - Rule - Conditions - Coupons - "Any coupon" option was ignored (plugin was processing the "Specific coupon(s)" option instead). This is fixed now.
* Dev - Rule - Conditions - Coupons - "No coupons" option added.

= 2.8.0 - 20/04/2022 =
* Dev - Rule - Conditions - "Coupons" options added.
* Dev - Rule - Conditions - "Paying customer" options added.
* Dev - Admin settings descriptions updated.
* Dev - Code refactoring.

= 2.7.2 - 15/04/2022 =
* Fix - Admin settings - Users - "Guest" user was removed by mistake in v2.6.1. This is fixed now.
* Dev - Code refactoring.

= 2.7.1 - 14/04/2022 =
* Fix - Advanced - Disabled conditions - "Billing countries" and "Shipping countries" options could not be disabled. This is fixed now.

= 2.7.0 - 14/04/2022 =
* Dev - Rule - Conditions - "Product stock status" options added.
* Dev - Rule - Conditions - "Billing countries" and "Shipping countries" options added.
* Dev - Rule - Conditions - "All products in the order must match the selection (vs at least one product)" options added to "Products", "Product categories" and "Product tags" conditions. Defaults to `no`.

= 2.6.1 - 13/04/2022 =
* Dev - Admin settings - Users - Using AJAX now.
* Dev - Code refactoring.
* WC tested up to: 6.4.

= 2.6.0 - 08/04/2022 =
* Dev - Advanced - "Disabled conditions" option added.
* Dev - Code refactoring.
* WC tested up to: 6.3.

= 2.5.1 - 26/01/2022 =
* Dev - Advanced - "Orders sorting" options added.
* Tested up to: 5.9.
* WC tested up to: 6.1.

= 2.5.0 - 17/01/2022 =
* Dev - Conditions - Products - Admin settings are using AJAX now.
* Dev - Code refactoring.
* WC tested up to: 6.0.

= 2.4.1 - 14/12/2021 =
* Dev - Rule - Conditions - "Date created before" and "Date created after" options added.

= 2.4.0 - 09/12/2021 =
* Dev - Rule - Conditions - Order meta - "Multiple meta values" option added (i.e. multiple values are now allowed in the "Meta value" option, as a comma-separated list).
* Dev - Rule - Skip days - Algorithm improved.
* Dev - Order Status History - "No order status rules are scheduled to be applied..." message added.
* Dev - Code refactoring.
* WC tested up to: 5.9.

= 2.3.0 - 27/10/2021 =
* Dev - Advanced - Periodical Processing - "Action Scheduler" options added.
* Dev - Advanced - "Rules processing hooks" option added (defaults to `Order status changed`). Can be filtered with the `alg_wc_order_status_rules_hooks` filter.
* Dev - Advanced - Admin settings rearranged: "Periodical Processing Options", "Order Status History Options" subsections added. Settings descriptions updated.

= 2.2.0 - 22/10/2021 =
* Fix - Admin settings - The issue when "Total rules" option has just been changed, but number of rule settings tabs doesn't reflect it, is fixed now.
* Dev - Triggering order status rules processing on any order status change now (i.e. rules with zero time triggers will be processed immediately now).

= 2.1.0 - 21/10/2021 =
* Dev - Rule - "Shipping methods" option added.
* Dev - Rule - "Minimum order quantity" and "Maximum order quantity" options added.
* Dev - Advanced - "On non-matching order status" option added.
* Dev - Safe-checks added when retrieving order's payment gateway.
* Dev - Admin "Rule" settings restyled ("Conditions" subsection added).
* Dev - Code refactoring.
* WC tested up to: 5.8.

= 2.0.0 - 06/09/2021 =
* Dev - Admin settings rearranged. Now each order status rule has its own settings section. Tools moved from the "Advanced" section to the "General" section.
* Dev - Rule - "Order meta" options added.
* Dev - Optional `alg_wc_order_status_rules_process_rules_redirect` URL param added.
* Dev - Developers - `alg_wc_order_status_rules_do_apply_rule` filter added.
* Dev - Developers - `alg_wc_order_status_after_save_settings` filter renamed to `alg_wc_order_status_rules_after_save_settings`.

= 1.9.0 - 25/08/2021 =
* Dev - Rule - "User roles" option added.
* Dev - Rule - "Users" option added.
* Dev - Rule - Minimum/Maximum order amount - Decimal values are allowed in settings now (step set to `0.000001`).
* WC tested up to: 5.6.

= 1.8.1 - 11/08/2021 =
* Dev - Rule - "Minimum order amount" and "Maximum order amount" options added.
* Dev - Rule - Admin settings - "Select all" and "Deselect all" buttons added to all "multiselect" options.

= 1.8.0 - 02/08/2021 =
* Dev - Rule - Time trigger - It's possible to set the value to zero now.
* Dev - Advanced & Tools - "My Account > Orders" options added.
* Dev - Advanced & Tools - On empty order status change history - Defaults to "Use order date modified" now.
* Dev - Save status change - Hook priority set to `10` now (was `PHP_INT_MAX`).
* Dev - Admin settings descriptions updated.
* Dev - Code refactoring.

= 1.7.0 - 29/07/2021 =
* Fix - PHP error fixed. Was occurring when creating a new order by admin with "On empty order status change history" option set to "Use order date ...".
* Dev - Advanced - Compatibility Options - "Doctreat" option added.
* Dev - Debug - Now adding to the log if rules was processed manually (i.e. via "Tools > Run all rules now").
* Dev - Plugin is initialized on `plugins_loaded` action now.
* Dev - Code refactoring.
* Tested up to: 5.8.
* WC tested up to: 5.5.

= 1.6.1 - 13/05/2021 =
* Fix - "PHP Parse error" fixed.

= 1.6.0 - 11/05/2021 =
* Dev - Rule - "Products" option added.
* Dev - Rule - "Product categories" option added.
* Dev - Rule - "Product tags" option added.
* Dev - Order Status History - Descriptions updated in the meta box, e.g. rule title added.
* Dev - Code refactoring.

= 1.5.0 - 20/04/2021 =
* Dev - Rule - "Payment gateways" option added.
* Dev - Admin settings restyled: new "Advanced & Tools" section added.
* Dev - Minor code refactoring.
* Tested up to: 5.7.
* WC tested up to: 5.2.

= 1.4.0 - 26/02/2021 =
* Dev - Advanced - Use WP cron - "WP cron interval" option added (defaults to "Once Hourly").
* Dev - Advanced - "On empty order status change history" option added.
* Dev - Rule - Time trigger - "Unit" option added (defaults to "hour(s)").
* Dev - Rule - Time trigger - Defaults to `1` now.
* Dev - Advanced - "Debug" option added.
* Dev - Localization - `load_plugin_textdomain()` moved to the `init` hook.
* Dev - Admin descriptions updated.
* Dev - Code refactoring.
* Tested up to: 5.6.
* WC tested up to: 5.0.

= 1.3.1 - 17/09/2020 =
* Dev - Allow rules processing via URL - Hook priority increased.

= 1.3.0 - 15/09/2020 =
* Dev - Advanced - "Use WP cron" option added (defaults to `yes`).
* Dev - Advanced - "Allow rules processing via URL" option added (defaults to `no`). This is an alternative to WP crons (i.e. allows using "real" (i.e. server) cron jobs instead).
* WC tested up to: 4.5.
* Tested up to: 5.5.

= 1.2.0 - 27/03/2020 =
* Fix - "Reset settings" admin notice fixed.
* Dev - Tools - "Run all rules now" tool added.
* Dev - "Next cron event is scheduled on ..." info added to admin settings.
* Dev - Code refactoring.
* Dev - Admin settings descriptions updated.
* WC tested up to: 4.0.
* Tested up to: 5.3.

= 1.1.0 - 11/07/2019 =
* Dev - Admin settings descriptions updated. "Your settings have been reset" notice added.
* Dev - "Total rules" default value changed to `1`.
* Dev - Rule - "Admin title (optional)" option added.
* Dev - Rule - "Enable/Disable" default value changed to `no`.
* Dev - Code refactoring.
* Plugin URI updated.
* Tested up to: 5.2.
* WC tested up to: 3.6.

= 1.0.1 - 07/06/2018 =
* Fix - Break added in `process_rules()` function, so multiple status updates wouldn't happen at once.
* Dev - "Skip days" rules options added.

= 1.0.0 - 16/05/2018 =
* Initial Release.

== Upgrade Notice ==

= 1.0.0 =
This is the first release of the plugin.
