<?php

/**
 * Retrieves saved forms accessible to the current user based on their locations
 *
 * @return void Sends JSON response
 */
function get_saved_forms(){
    global $wpdb;
    
    $table_name_forms = $wpdb->prefix . 'equipment_forms';

    $current_user_id = get_current_user_id();
    $current_user_locations = get_user_locations($current_user_id);

    $forms_rows = $wpdb->get_results("SELECT id, form_name,locations FROM $table_name_forms", ARRAY_A);
    $forms = [];

    foreach ($forms_rows as $row) {

        $locations = json_decode($row['locations']);

        if (is_array($locations)) {

            foreach ($current_user_locations as $location) {

                if (array_search($location, $locations) !== false) {
                    array_push($forms, $row);
                } else {
                    continue;
                }
            }
        }
    }



    $status = empty($forms) ? 'empty' : 'hasform';

    if ($forms === null) {
        wp_send_json_error(['message' => 'درخواست از دیتا بیس ناموفق بود.']);
        return;
    }
    wp_send_json_success(['forms' => $forms, 'status' => $status]);
}
add_action('wp_ajax_get_saved_forms', 'get_saved_forms');
