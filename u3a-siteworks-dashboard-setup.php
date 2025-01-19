<?php // phpcs:ignore PSR1.Files.SideEffects.FoundWithSymbols

// Customise WordPress dashboard for any user below level of Editor
// Optionally customise WordPress Dashboard for Editors as well

add_action('admin_init', 'u3a_dashboard_customise');

function u3a_dashboard_customise()
{
    // Replace Dashboard for Editors as well?
    $u3a_dashboard_panel_showall = get_option('u3a_dashboard_panel_showall', '9');
    $capability = ($u3a_dashboard_panel_showall == '1') ? 'manage_options' : 'delete_others_pages';

    if (!current_user_can($capability)) {
        add_action('admin_head', 'u3a_dashboard_hide_menu');
        add_action('wp_dashboard_setup', 'u3a_dashboard_remove_all_metaboxes');
        add_action('wp_dashboard_setup', 'u3a_dashboard_add_custom_panel');
    }
    // Add SiteWorks widget for all users

    add_action('wp_dashboard_setup', 'u3a_dashboard_add_siteworks_panel');
    function u3a_dashboard_admin_style()
    {
        wp_enqueue_style(
            'u3a_dashboard-admin-style',
            plugins_url('u3a-siteworks-dashboard.css', __FILE__),
            array(),
            SW_DASHBOARD_VERSION
        );
    }
        add_action('admin_enqueue_scripts', 'u3a_dashboard_admin_style');
}

// Hide some menu entries
function u3a_dashboard_hide_menu()
{
    remove_menu_page('tools.php'); //Tools
}

// Ref https://developer.wordpress.org/apis/handbook/dashboard-widgets/
function u3a_dashboard_remove_all_metaboxes()
{
    // Remove Welcome panel
    remove_action('welcome_panel', 'wp_welcome_panel');
    // Remove the rest of the dashboard widgets
    remove_meta_box('dashboard_primary', 'dashboard', 'side');
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
    remove_meta_box('health_check_status', 'dashboard', 'normal');
    remove_meta_box('dashboard_right_now', 'dashboard', 'normal');
    remove_meta_box('dashboard_activity', 'dashboard', 'normal');
}

function u3a_dashboard_add_custom_panel()
{
    // check if the custom panel has been defined
    $u3a_dashboard_panel = trim(get_option('u3a_dashboard_panel', ''));
    if (!empty($u3a_dashboard_panel)) {
        wp_add_dashboard_widget(
            'u3a-dashboard-custom-panel',         // Widget slug.
            get_option('u3a_dashboard_panel_title', 'Information for website users'), // Title.
            'u3a_dashboard_custom_panel_render'   // Display function.
        );
    }
}

function u3a_dashboard_add_siteworks_panel()
{

    wp_add_dashboard_widget(
        'u3a_dashboard_widget',         // Widget slug.
        'u3a SiteWorks information',         // Title.
        'u3a_dashboard_siteworks_widget_render'   // Display function.
    );
}

function u3a_dashboard_custom_panel_render()
{
    print wp_kses(get_option('u3a_dashboard_panel', ''), U3A_CUSTOM_DASHBOARD_TAGS);
}

function u3a_dashboard_siteworks_widget_render()
{

    // Get the Siteworks

    // Display whatever you want to show.
    print <<< END
    <div style="display:flex; justify-content:space-between; border: 1px solid silver; padding: 0 10px;">
    <p style="margin-right:20px;">Click on Help for information on using SiteWorks with WordPress 
    and to contact our Help Desk.</p>
    <p><a class="button-primary" href="https://siteworks.u3a.org.uk/user-guide-betterdocs/" target="_blank">Help</a></p>
    </div>
    <p>For general information about the u3a SiteWorks project and the SiteWorks 
    additions to WordPress please visit the <a href="https://siteworks.u3a.org.uk/">SiteWorks website</a>.</p>
    <p>The <a href="https://u3awpdev.org.uk/">u3a WordPress Development Forum</a> 
    is another source of help and information relating to WordPress and web development in general.</p>
    
END;
}
