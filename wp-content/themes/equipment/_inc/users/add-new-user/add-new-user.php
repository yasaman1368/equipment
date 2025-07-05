<?php

add_action('wp_ajax_add_new_user', 'add_new_user_callback');


function add_new_user_callback()
{
    if (!isset($_POST['add_new_user_nonce']) || !wp_verify_nonce($_POST['add_new_user_nonce'], 'add_new_user_action')) {
        wp_send_json_error('خطای امنیتی رخ داده است.');
    }

    $required = ['fullname', 'phone', 'password', 'repeat_password', 'role', 'locations'];
    foreach ($required as $item) {
        if (empty($_POST[$item])) {
            wp_send_json_error('لطفا تمام فیلدها را پر کنید.');
        }
    }

    $fullname = sanitize_text_field($_POST['fullname']);
    $phone = sanitize_text_field($_POST['phone']);
    $role = sanitize_text_field($_POST['role']);
    $password = $_POST['password']; // Will be hashed later
    $repeat_password = $_POST['repeat_password'];


    if ($password !== $repeat_password) {
        wp_send_json_error('رمز عبور و تکرار آن مطابقت ندارند.');
    }

    // if (strlen($password) < 8) {
    //     wp_send_json_error('Password must be at least 8 characters.', 400);
    // }

    $locations = $_POST['locations'];
    $locations = json_decode(wp_unslash($locations), true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($locations)) {
        wp_send_json_error('اطلاعات موقعیت ها اشتباه است', 400);
    }

    $sanitized_locations = array_map('sanitize_text_field', $locations);


    // Check if username/phone already exists
    if (username_exists($phone)) {
        wp_send_json_error('نام کاربری یا شماره موبایل قبلا ثبت شده است.');
    }

    // Create New User
    $user_id = wp_create_user($phone, $password, $phone); // Username and email are the same (phone)

    if (is_wp_error($user_id)) {
        wp_send_json_error('خطا در ایجاد کاربر: ' . $user_id->get_error_message(), 500);
    }

    //add role
    update_user_meta($user_id, '_role', $role);
    update_user_meta($user_id, '_locations', $sanitized_locations);

    // Update User Meta (Full Name)
    wp_update_user(array(
        'ID' => $user_id,
        'display_name' => $fullname
    ));

    // Success Response
    wp_send_json_success(' کاربر' . $fullname . '  با موفقیت افزوده شد.');
}
