# WPFactory Cross-Selling
A library designed for WPFactory plugins, aimed at cross-selling by offering WPFactory product recommendations

## Installation

Installation via Composer. Instructions to setup the `composer.json`.

1. Add these objects to the `repositories` array:

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/wpcodefactory/wpfactory-cross-selling"
    },
    {
      "type": "vcs",
      "url": "https://github.com/wpcodefactory/wpfactory-admin-menu"
    }
  ]
}
```

2. Require the library and its dependencies:

```json
{
  "require": {
    "wpfactory/wpfactory-cross-selling": "*",
    "wpfactory/wpfactory-admin-menu": "*"
  }
}
```

3. Use `preferred-install` parameter set as `dist` on `config`.

```json
{
  "config": {
    "preferred-install": "dist"
  }
}
```

**Full Example:**

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/wpcodefactory/wpfactory-cross-selling"
    },
    {
      "type": "vcs",
      "url": "https://github.com/wpcodefactory/wpfactory-admin-menu"
    }
  ],
  "require": {
    "wpfactory/wpfactory-cross-selling": "*",
    "wpfactory/wpfactory-admin-menu": "*"
  },
  "config": {
    "preferred-install": "dist"
  }
}
```

## How to use it?
1. Create/Put the composer.json on the root folder.

2. Then initialize the library with `new \WPFactory\WPFactory_Cross_Selling\WPFactory_Cross_Selling()` from within the main plugin class. Probably the best place is inside the hook `plugins_loaded`. If the main class is already being loaded with that hook, you can simply load the library in the class constructor.
> [!NOTE]  
> Try to remember to only run it inside a `is_admin()` check.

*Example:*

```php
add_action( 'plugins_loaded', function(){  
    $main_plugin_class = new Main_Plugin_Class();  
} );
```

```php
class Main_Plugin_Class(){

    function __construct() { 
        $this->init_cross_selling_library();
    }

    function init_cross_selling_library(){
        if ( ! is_admin() ) {
            return;
        }

        // Composer.
        require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

        // Initializes WPFactory Key Manager library.
        $cross_selling = new \WPFactory\WPFactory_Cross_Selling\WPFactory_Cross_Selling();
        $cross_selling->setup( array( 'plugin_file_path'   => __FILE__ ) );
        $cross_selling->init()
    }

}
```

## Methods

### `setup( array $args = null )`

Setups the library.

**Parameters:**

* **`plugin_file_path`** *(string)* - Plugin file path.


* **`recommendations_page`** *(array)*
  * **`action_link`** *(array)*
    * **`enable`** *(boolean)* - Enables/Disables the plugin action link. Default value: `true`.
    * **`label`** *(string)* - Label for the plugin action link. Default value: `'Recommendations'`.


* **`recommendations_box`** *(array)*
  * **`enable`** *(boolean)* - Enables/Disables the Recommendation box. Default value: `true`.
  * **`wc_settings_tab_id`** *(string)* - WooCommerce settings tab id.
  * **`position`** *(array)* - The position to place the Recommendation Box. Default value: `array( 'wc_settings_tab' )`. Possible values: `wc_settings_tab` 


* **`banners`** *(array)*
  * **`enable`** *(boolean)* - Enables/Disables the Banners. Default value: `true`.
  * **`get_banner_method`** *(string)* - Method to get the banners: `advanced_ads`.
  * **`banner_cache_duration`** *(integer)* - How long the banners should stay cached. Default value: `HOUR_IN_SECONDS`. 
  * **`banner_dismiss_duration`** *(integer)* - How long the banners should stay dismissed after the close button was pressed. Default value: `WEEK_IN_SECONDS`.
  * **`advanced_ads_setup`** *(array)* - Advanced Ads Plugin setup.
    * **`dashboard_banner_group_name`** *(string)* - Advanced Ads group name for the dashboard banner.  Default value `'CS - Dashboard'`.
    * **`recommendations_group_name`** *(string)* - Advanced Ads group name for the recommendation banners.  Default value `'CS - Recommendations'`.

### `init()`

Initializes the library.

## Full example:

```php
$cross_selling = new \WPFactory\WPFactory_Cross_Selling\WPFactory_Cross_Selling();
$cross_selling->setup(array(
    'plugin_file_path'     => $this->get_filesystem_path(),
    'recommendations_box'  => array(
        'enable'             => true,
        'wc_settings_tab_id' => 'alg_wc_cost_of_goods',
        'position'           => array( 'wc_settings_tab' ),
    ),
    'recommendations_page' => array(
        'action_link' => array(
            'enable'  => true,
            'label'   => 'Suggestions',
        ),
    ),
));
$cross_selling->init();
```
