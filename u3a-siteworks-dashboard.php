<?php // phpcs:ignore PSR1.Files.SideEffects.FoundWithSymbols

/*
Plugin Name: u3a SiteWorks Dashboard
Plugin URI: https://u3awpdev.org.uk/
Description: Provides a customised dashboard for users below 'administrator'
Version: 1.1.0
Author: u3a SiteWorks team
Author URI: https://siteworks.u3a.org.uk/
Plugin URI: https://siteworks.u3a.org.uk/
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if (!defined('ABSPATH')) {
    exit;
}

define('SW_DASHBOARD_VERSION', '1.1.0');
// Set to current plugin version number

// Plugin only relevant on admin interface pages.
if (!is_admin()) {
    return;
}

// Use the plugin update service on SiteWorks update server

require 'inc/plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;
$u3aMmUpdateChecker = PucFactory::buildUpdateChecker(
    'https://siteworks.u3a.org.uk/wp-update-server/?action=get_metadata&slug=u3a-siteworks-dashboard', //Metadata URL
    __FILE__, //Full path to the main plugin file or functions.php.
    'u3a-siteworks-dashboard'
);
// HTML tags allowed in custom dashboard panel

define('U3A_CUSTOM_DASHBOARD_TAGS', array(
        'p' => array(),
        'a' => array(
            'href' => true,
        ),
        'em' => array(),
        'strong' => array(),
        'ul' => array(),
        'ol' => array(),
        'li' => array()
    ));
// Add the dashboard customisation menu
require 'u3a-siteworks-dashboard-admin.php';
// Add a link to the settings page to the plugin entry on the plugins page
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'u3a_db_settings_link');
// Customise the menu
require 'u3a-siteworks-dashboard-setup.php';
