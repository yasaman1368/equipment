<?php
add_action('wp_ajax_add_location', 'handle_add_location');

function handle_add_location()
{

    if (!user_is_manager()) {
        return;
    }
    // Validate nonce
    if (
        empty($_POST['add-location-nonce']) ||
        !wp_verify_nonce($_POST['add-location-nonce'], 'add-location')
    ) {
        wp_send_json_error(['message' => 'شما مجوز انجام این عمل را ندارید.'], 403);
    }

    // Validate and sanitize input
    $raw_location = $_POST['location'] ?? '';
    $location = sanitize_text_field($raw_location);

    if (empty($location)) {
        wp_send_json_error(['message' => 'نام موقعیت را بدرستی وارد کنید'], 422);
    }

    // Get and sanitize existing locations
    $locations = get_option('_locations', []);
    $locations = array_map('sanitize_text_field', (array) $locations);

    // Check for duplicate
    if (in_array($location, $locations, true)) {
        wp_send_json_error(['message' => 'این موقعیت قبلا ثبت شده است'], 409);
    }

    // Add new location
    $locations[] = $location;
    update_option('_locations', $locations);

    wp_send_json_success(['message' => 'موقعیت با موفقیت ثبت شد'], 200);
}

// Remove location AJAX handler
add_action('wp_ajax_remove_location', 'handle_remove_location');
function handle_remove_location()
{

    if (!user_is_manager()) {
        return;
    }

    if (empty($_POST['add-location-nonce']) || !wp_verify_nonce($_POST['add-location-nonce'], 'add-location')) {
        wp_send_json_error(['message' => 'شما مجوز انجام این عمل را ندارید.'], 403);
    }

    $index = isset($_POST['index']) ? intval($_POST['index']) : null;
    $locations = get_option('_locations', []);

    if ($index === null || !isset($locations[$index])) {
        wp_send_json_error(['message' => 'موقعیت مورد نظر یافت نشد'], 404);
    }


    // Remove the location
    $removed_location = sanitize_text_field($locations[$index]);
    unset($locations[$index]);

    // Re-index array and update option
    $locations = array_values($locations);
    update_option('_locations', $locations);

    wp_send_json_success([
        'message' => sprintf('موقعیت "%s" با موفقیت حذف شد', $removed_location)
    ], 200);
}

// Get locations AJAX handler
add_action('wp_ajax_get_locations', 'handle_get_locations');
function handle_get_locations()
{
    $locations = get_option('_locations', []);
    wp_send_json_success(['locations' => $locations], 200);
}
