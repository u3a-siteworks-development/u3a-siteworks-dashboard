<?php

// Add the menu

add_action('admin_menu', 'u3a_dashboard_settings_menu');

function u3a_dashboard_settings_menu()
{
    add_submenu_page(
        'u3a-settings',
        'Dashboard Settings',
        'Dashboard Settings',
        'manage_options',
        'u3a-dashboard-settings',
        'u3a_dashboard_settings_cb'
    );
}

// // Add a link to the settings page to the plugin entry on the plugins page

// add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'u3a_db_settings_link');

function u3a_db_settings_link($links)
{
    $newlink = array();
    $page = admin_url('admin.php?page=u3a-dashboard-settings');
    $newlink['udb_settings'] = "<a href='$page'>Settings</a>";
    // Add the link to the end of the current array

    return array_merge($links,$newlink);
}


// Display the plugin's admin settings page

function u3a_dashboard_settings_cb()
{

    // Check if there is a status returned from a save

    $status = isset($_GET['status']) ? $_GET['status'] : "";  // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- any value other than 1 is ignored
    $status_text = '';
    if ($status == "1") {
        $status_text = '<div class="notice notice-error is-dismissible inline"><p>Changes Saved</p></div>';
    }

    // Retrieve saved data, validate and sanitise as required and set up form content

    $nonce_code =  wp_nonce_field('u3a_settings', 'u3a_nonce', true, false);
    $u3aMQDetect = "<input type=\"hidden\" name=\"u3aMQDetect\" value=\"test'\">\n";
    $submit_button = get_submit_button('Save Settings');
    $u3a_dashboard_panel = wp_kses( get_option('u3a_dashboard_panel', ''), U3A_CUSTOM_DASHBOARD_TAGS );
    $u3a_dashboard_panel_title = esc_html( get_option('u3a_dashboard_panel_title', 'Information for website users') );
    $u3a_dashboard_panel_showall = get_option('u3a_dashboard_panel_showall', '9');
    $u3a_dashboard_panel_showall_chk = ($u3a_dashboard_panel_showall == '1') ? ' checked' : '';

    $wpe_settings = array(
        'teeny' => true,
        'textarea_rows' => 7,
        'textarea_name' => 'u3a_dashboard_panel',
        'tabindex' => 1,
        'media_buttons' => false,
        'wpautop' => false,
        'quicktags' => false,
        'tinymce' => array(
            // Items for the Visual Tab
            'toolbar1' => 'bold,italic,separator,bullist,numlist,separator,link,unlink,separator,undo,redo,',
        ),
    );

    // Generate the admin page

// phpcs:disable WordPress.Security.EscapeOutput.HeredocOutputNotEscaped -- all variables validated or escaped
    print <<< END
    
<div class="wrap">
<h1 class="wp-heading-inline">Dashboard Settings</h1>
$status_text

<h2 class="title">Dashboard Contents</h2>
<p>The standard WordPress Dashboard will be replaced to show this information panel for all users with the role of 'author' or 'contributor' when they log in.  
You might want to use the panel to remind users of the steps to edit content or to provide links to your own documentation.</p>

<form method="POST" action="admin-post.php">
<input type="hidden" name="action" value="u3a_dashboard_settings">
$nonce_code
$u3aMQDetect
<p>
<label for="u3a_dashboard_panel_title">Panel Title:</label> &nbsp;
<input type="text" name="u3a_dashboard_panel_title" id="u3a_dashboard_panel_title" value="$u3a_dashboard_panel_title" class="regular-text">
</p>

<div style="max-width:600px;">
END;

    wp_editor($u3a_dashboard_panel, 'u3a_dashboard_panel', $wpe_settings);

    print <<< END
</div>
<p>
<label for="u3a_dashboard_panel_showall">Replace the standard WordPress Dashboard for Editors as well?</label> &nbsp;
<input type="checkbox" id="u3a_dashboard_panel_showall" name="u3a_dashboard_panel_showall" value="1" $u3a_dashboard_panel_showall_chk>
</p>

$submit_button
</form>

</div>
END;
// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped

}


// Add function to process the Dashboard Settings form submission

add_action('admin_post_u3a_dashboard_settings', 'u3a_dashboard_save_settings');

function u3a_dashboard_save_settings()
{
    // check nonce
    if (check_admin_referer('u3a_settings', 'u3a_nonce') == false) wp_die('Invalid form submission');

    // check for WP magic quotes
    $u3aMQDetect = $_POST['u3aMQDetect'];
    $needStripSlashes = (strlen($u3aMQDetect) > 5) ? true : false; // backslash added to apostrophe in test string?

    $u3a_dashboard_panel_title = isset($_POST['u3a_dashboard_panel_title']) ? sanitize_text_field($_POST['u3a_dashboard_panel_title']) : '';
    if ($needStripSlashes) {
        $u3a_dashboard_panel_title = stripslashes($u3a_dashboard_panel_title);
    }
    update_option('u3a_dashboard_panel_title', $u3a_dashboard_panel_title);

    $u3a_dashboard_panel = isset($_POST['u3a_dashboard_panel']) ? $_POST['u3a_dashboard_panel'] : '';
    if ($needStripSlashes) {
        $u3a_dashboard_panel = stripslashes($u3a_dashboard_panel);
    }

    $u3a_dashboard_panel = wp_kses($u3a_dashboard_panel, U3A_CUSTOM_DASHBOARD_TAGS );
    update_option('u3a_dashboard_panel', $u3a_dashboard_panel);

    $u3a_dashboard_panel_showall = isset($_POST['u3a_dashboard_panel_showall']) ? '1' : '9';
    update_option('u3a_dashboard_panel_showall', $u3a_dashboard_panel_showall);

    // redirect back to u3a dashboard settings page
    wp_safe_redirect(admin_url('admin.php?page=u3a-dashboard-settings&status=1'));
    exit;
}
