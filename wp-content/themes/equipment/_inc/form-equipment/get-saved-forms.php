<?php

function get_saved_forms()
{
    global $wpdb;
    $table_name_forms = $wpdb->prefix . 'equipment_forms';

    $forms = $wpdb->get_results("SELECT id, form_name FROM $table_name_forms", ARRAY_A);

    $status = empty($forms) ? 'empty' : 'hasform';

    if ($forms === null) {
        wp_send_json_error(['message' => 'درخواست از دیتا بیس ناموفق بود.']);
        return;
    }
    wp_send_json_success(['forms' => $forms, 'status' => $status]);
}
add_action('wp_ajax_get_saved_forms', 'get_saved_forms');
