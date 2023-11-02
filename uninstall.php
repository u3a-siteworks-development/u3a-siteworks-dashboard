<?php

// exit if uninstall constant is not defined

if (!defined('WP_UNINSTALL_PLUGIN')) exit;

// Remove the option fields used by the dashboard plugin

delete_option('u3a_dashboard_panel');
delete_option('u3a_dashboard_panel_title');
delete_option('u3a_dashboard_panel_showall');
