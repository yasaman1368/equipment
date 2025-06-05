<?php
// Add AJAX handler for adding a new user
add_action('wp_ajax_add_new_user', 'add_new_user_callback');
add_action('wp_ajax_nopriv_add_new_user', 'add_new_user_callback');

function add_new_user_callback()
{
    // Verify Nonce
    if (!isset($_POST['add_new_user_nonce']) || !wp_verify_nonce($_POST['add_new_user_nonce'], 'add_new_user_action')) {
        wp_send_json_error('خطای امنیتی رخ داده است.');
    }

    // Sanitize Inputs
    $fullname = sanitize_text_field($_POST['fullname']);
    $phone = sanitize_text_field($_POST['phone']);
    $password = sanitize_text_field($_POST['password']);
    $repeat_password = sanitize_text_field($_POST['repeat_password']);
    $role = sanitize_text_field($_POST['role']);

    // Validate Inputs
    if (empty($fullname) || empty($phone) || empty($password) || empty($repeat_password) || empty($role)) {
        wp_send_json_error('لطفا تمام فیلدها را پر کنید.');
    }

    if ($password !== $repeat_password) {
        wp_send_json_error('رمز عبور و تکرار آن مطابقت ندارند.');
    }

    // Check if username/phone already exists
    if (username_exists($phone)) {
        wp_send_json_error('نام کاربری یا شماره موبایل قبلا ثبت شده است.');
    }

    // Create New User
    $user_id = wp_create_user($phone, $password, $phone); // Username and email are the same (phone)

    if (is_wp_error($user_id)) {
        wp_send_json_error('خطا در ایجاد کاربر: ' . $user_id->get_error_message());
    }
    //add role
    update_user_meta($user_id, '_role', $role);
    // Update User Meta (Full Name)
    wp_update_user(array(
        'ID' => $user_id,
        'display_name' => $fullname
    ));

    // Success Response
    wp_send_json_success('کاربر با موفقیت افزوده شد.');
}
