<?php
/*
Plugin Name: Custom WP 101 Plugin
Description: Display content from WP Corner in the admin area.
Version: 1.0
Author: Your Name
*/

// Add CSS and JS files
function custom_wp_101_enqueue_scripts() {
    // Enqueue CSS
    wp_enqueue_style('custom-wp-101-css', plugins_url('assets/styles.css', __FILE__));

    // Enqueue JS
    wp_enqueue_script('custom-wp-101-js', plugins_url('assets/script.js', __FILE__), array('jquery'), '1.0', true);
}
add_action('admin_enqueue_scripts', 'custom_wp_101_enqueue_scripts');

// Add Admin Menu Link
function custom_wp_101_menu() {
    add_menu_page(
        'WP 101',
        'WP 101',
        'manage_options',
        'custom-wp-101-plugin',
        'custom_wp_101_display_content',
        'dashicons-welcome-learn-more',
        80
    );
}
add_action('admin_menu', 'custom_wp_101_menu');

// Display Content Function
function custom_wp_101_display_content() {
    // Fetch content from the external website
    $external_content = wp_remote_get('https://wpcorner.co/wp-101-tutorials/');

    // Check if request was successful
    if (is_array($external_content) && !is_wp_error($external_content)) {
        $content_body = wp_remote_retrieve_body($external_content);

        // Create a DOMDocument object to manipulate the HTML
        $dom = new DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML($content_body);
        libxml_clear_errors();

        // Update links to open within the same admin page
        $links = $dom->getElementsByTagName('a');
        foreach ($links as $link) {
            $href = $link->getAttribute('href');
            $new_href = add_query_arg('external_link', $href, admin_url('admin.php?page=custom-wp-101-plugin'));
            $link->setAttribute('href', $new_href);
            $link->setAttribute('target', '_self');
        }

        // Display the updated content
        echo '<div class="wrap">';
        echo '<h1>WP 101 Tutorials</h1>';
        echo $dom->saveHTML(); // Display the updated content
        echo '</div>';
    } else {
        echo '<div class="wrap"><p>Error fetching content from WP Corner.</p></div>';
    }
}
