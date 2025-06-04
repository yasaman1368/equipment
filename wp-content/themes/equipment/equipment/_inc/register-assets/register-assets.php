<?php

function my_theme_scripts_and_styles()
{
    // Register and enqueue Bootstrap CSS (from CDN)
    // wp_enqueue_style(
    //     'bootstrap-css', // Handle
    //     'https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.rtl.min.css', // Source
    //     array(), // Dependencies
    //     '4.3.1', // Version
    //     'all' // Media
    // );

    // Register and enqueue jQuery (from CDN)
    // wp_enqueue_script(
    //     'jquery-slim', // Handle
    //     'https://code.jquery.com/jquery-3.3.1.slim.min.js', // Source
    //     array(), // Dependencies
    //     '3.3.1', // Version
    //     true // Load in footer
    // );

    // Register and enqueue Bootstrap JS (from CDN)
    // wp_enqueue_script(
    //     'bootstrap-js', // Handle
    //     'https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js', // Source
    //     array('jquery-slim'), // Dependencies (jQuery is required for Bootstrap)
    //     '4.3.1', // Version
    //     true // Load in footer
    // );

    // Register and enqueue Quagga JS (from CDN)
    // wp_enqueue_script(
    //     'quagga-js', // Handle
    //     'https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js', // Source
    //     array(), // Dependencies
    //     '0.12.1', // Version
    //     true // Load in footer
    // );

    // Register and enqueue your custom app.js
    // wp_enqueue_script(
    //     'app-js', // Handle
    //     get_template_directory_uri() . '/assets/js/app.js', // Source
    //     array('jquery-slim', 'bootstrap-js'), // Dependencies (jQuery and Bootstrap)
    //     '1.0.0', // Version
    //     true // Load in footer
    // );
    // wp_localize_script('app-js', 'myScriptData', array(
    //     'ajax_url' => admin_url('admin-ajax.php'),
    //     'nonce' => wp_create_nonce('my-ajax-nonce'),
    //     // Add any other data you need here
    // ));
}
//add_action('wp_enqueue_scripts', 'my_theme_scripts_and_styles');
