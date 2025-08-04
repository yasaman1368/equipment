<?php
add_action('wp_ajax_add_location', 'handle_add_location');
add_action('wp_ajax_remove_location', 'handle_remove_location');
add_action('wp_ajax_get_locations', 'handle_get_locations');

function validate_request()
{
    user_is_manager();

    if (
        empty($_POST['add-location-nonce']) ||
        !wp_verify_nonce($_POST['add-location-nonce'], 'add-location')
    ) {
        wp_send_json_error(['message' => 'شما مجوز انجام این عمل را ندارید.'], 403);
    }
}



function handle_add_location()
{
    validate_request();

    $location = sanitize_text_field($_POST['location'] ?? '');

    if (empty($location)) {
        wp_send_json_error(['message' => 'نام موقعیت را بدرستی وارد کنید'], 422);
    }

    global $wpdb;
    $table_name = 'location_supervisors_users';

    $existing_locations = $wpdb->get_col("SELECT location_name FROM {$table_name}");
    $existing_locations = array_map('sanitize_text_field', (array)$existing_locations);

    if (in_array($location, $existing_locations, true)) {
        wp_send_json_error(['message' => 'این موقعیت قبلا ثبت شده است'], 409);
    }

    $result = $wpdb->insert($table_name, ['location_name' => $location]);
    if (!$result) {
        wp_send_json_error(['message' => 'خطایی در ثبت موقعیت رخ داده است'], 500);
    }

    wp_send_json_success(['message' => 'موقعیت با موفقیت ثبت شد'], 200);
}


function handle_remove_location()
{

    validate_request();

    $location = sanitize_text_field($_POST['location'] ?? '');

    if (empty($location)) {
        wp_send_json_error(['message' => 'موقعیت مشخص نشده است.'], 422);
    }

    global $wpdb;
    $result = $wpdb->delete('location_supervisors_users', ['location_name' => $location]);
    if (!$result) {
        wp_send_json_error(['message' => 'خطایی در حذف موقعیت رخ داده است'], 500);
    }

    wp_send_json_success([
        'message' => sprintf('موقعیت "%s" با موفقیت حذف شد', $location)
    ], 200);
}


function handle_get_locations()
{
    user_is_manager();

    global $wpdb;
    $table_name = 'location_supervisors_users';
    $locations = $wpdb->get_col($wpdb->prepare("SELECT location_name FROM {$table_name}", ARRAY_A));

    if (empty($locations)) {
        wp_send_json_error(['message' => 'هیچ موقعیتی وجود ندارد'], 404);
    }
    wp_send_json_success(['locations' => $locations], 200);
}
