<?php
/*
Plugin Name: Clear Additional CSS
Description: Clear the Additional CSS on all subsites in the multisite network.
Author: Dan Keech
Version: 1.3
*/

function clear_additional_css()
{
    global $wpdb;

    $blog_ids = $wpdb->get_col("SELECT blog_id FROM {$wpdb->blogs}");

    foreach ($blog_ids as $blog_id) {
        switch_to_blog($blog_id);

        // Get the current theme slug
        $theme_slug = get_option('stylesheet');

        // Get the custom CSS post ID
        $custom_css_post_id = get_theme_mod('custom_css_post_id', $theme_slug);

        // Check if the custom CSS post ID exists
        if ($custom_css_post_id) {
            // Update the post content of the custom CSS post to an empty string
            wp_update_post(array(
                'ID' => $custom_css_post_id,
                'post_content' => '',
            ));
        }

        restore_current_blog();
    }
}

function clear_additional_css_admin_menu()
{
    add_submenu_page(
        'settings.php',
        'Clear Additional CSS',
        'Clear Additional CSS',
        'manage_options',
        'clear-additional-css',
        'clear_additional_css_callback'
    );
}
add_action('network_admin_menu', 'clear_additional_css_admin_menu');

function clear_additional_css_callback()
{
    if (isset($_POST['clear_additional_css_submit'])) {
        clear_additional_css();
        echo '<div class="notice notice-success is-dismissible"><p>Additional CSS has been cleared for all subsites.</p></div>';
    }
?>
    <div class="wrap">
        <h1>Clear Additional CSS</h1>
        <form method="post" action="">
            <?php wp_nonce_field('clear_additional_css_action'); ?>
            <p>Click the button below to clear the Additional CSS for all subsites in the multisite network.</p>
            <input type="submit" name="clear_additional_css_submit" class="button button-primary" value="Clear Additional CSS">
        </form>
    </div>
<?php
}
