<?php
add_action('wp_ajax_load_exam_data', 'load_exam_data');
add_action('wp_ajax_nopriv_load_exam_data', 'load_exam_data');




function load_exam_data()
{
    global $wpdb;

    // Fetch data from the database
    $results = $wpdb->get_results("SELECT * FROM wp_exam_schedule ", ARRAY_A); // Replace with your actual table name

    // Send JSON response
    wp_send_json($results);
}
