<?php
add_action('wp_ajax_fetch_student_report', 'fetch_student_report');
add_action('wp_ajax_nopriv_fetch_student_report', 'fetch_student_report');

function fetch_student_report()
{
    global $wpdb;

    // Validate and sanitize input
    $class = 'numbers_of_students_db'; // Ideally, this should be validated against a list of allowed class names.
    if (!isset($_POST['national_code']) || empty($_POST['national_code'])) {
        echo json_encode(array('success' => false, 'message' => 'Invalid national code.'));
        wp_die();
    }

    $national_code = sanitize_text_field($_POST['national_code']);

    // Use get_var for fetching a single value
    $results = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $class WHERE national_code = %s",
        $national_code
    ));

    if ($results) {
        echo json_encode(array('success' => true, 'grades' => $results));
        $wpdb->update('numbers_of_students_db', ['view' => 'open'], ['national_code' => $national_code]);
    } else {
        echo json_encode(array('success' => false, 'message' => 'No results found.'));
    }
    wp_die();
}
