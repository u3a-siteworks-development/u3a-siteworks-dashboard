<?php
/* 
Plugin Name: u3a SiteWorks Dashboard
Plugin URI: https://u3awpdev.org.uk/
Description: Provides a customised dashboard for users below 'administrator'
Version: 1.2.0
Author: u3a SiteWorks team
Author URI: https://siteworks.u3a.org.uk/
Plugin URI: https://siteworks.u3a.org.uk/
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires Plugins: u3a-siteworks-configuration
*/

if (!defined('ABSPATH')) {
    exit;
}

define ('SW_DASHBOARD_VERSION', '1.2.0');  // Set to current plugin version number

// Plugin only relevant on admin interface pages.
if (!is_admin()) return;

// Use the plugin update service provided in the Configuration plugin

add_action(
    'plugins_loaded',
    function () {
        if (function_exists('u3a_plugin_update_setup')) {
            u3a_plugin_update_setup('u3a-siteworks-dashboard', __FILE__);
        } else {
            add_action(
                'admin_notices',
                function () {
                    print '<div class="error"><p>SiteWorks Dashboard plugin unable to check for updates as the SiteWorks Configuration plugin is not active.</p></div>';
                }
            );
        }
    }
);


// HTML tags allowed in custom dashboard panel

define(
    'U3A_CUSTOM_DASHBOARD_TAGS',
    array(
        'p' => array(),
        'a' => array(
            'href' => true,
        ),
        'em' => array(),
        'strong' => array(),
        'ul' => array(),
        'ol' => array(),
        'li' => array()
    )
);

// Add the dashboard customisation menu
require 'u3a-siteworks-dashboard-admin.php';

// Add a link to the settings page to the plugin entry on the plugins page
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'u3a_db_settings_link');

// Customise the menu
require 'u3a-siteworks-dashboard-setup.php';
