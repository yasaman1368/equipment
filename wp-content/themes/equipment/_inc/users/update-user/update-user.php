<?php

// Add AJAX handler for updating user details
add_action('wp_ajax_update_user', 'update_user');
add_action('wp_ajax_nopriv_update_user', 'update_user'); // Optional: Allow non-logged-in users to access it

function update_user()
{
    // Verify nonce for security (optional but recommended)
    // if (!isset($_POST['update_user_nonce_name']) || !wp_verify_nonce($_POST['update_user_nonce_name'], 'update_user_nonce_action')) {
    //     wp_send_json_error('خطای امنیتی رخ داده است.');

    // }

    // Get the user ID (you can pass this via POST or retrieve it dynamically)


    $user_id = isset($_POST['user-id']) ? intval($_POST['user-id']) : 0;
    if (!$user_id) {
        wp_send_json_error('شناسه کاربری نامعتبر است');
    }

    // Get the form data from the POST request
    $username = isset($_POST['username-modal']) ? sanitize_user($_POST['username-modal']) : '';
    $display_name = isset($_POST['display-name-modal']) ? sanitize_text_field($_POST['display-name-modal']) : '';
    $new_password = isset($_POST['new-password-modal']) ? $_POST['new-password-modal'] : '';
    $confirm_password = isset($_POST['confirm-password-modal']) ? $_POST['confirm-password-modal'] : '';
    $email = isset($_POST['email-modal']) ? sanitize_email($_POST['email-modal']) : '';
    $role = isset($_POST['role']) ? sanitize_text_field($_POST['role']) : '';

    // Validate the form data
    if (!empty($email) && !is_email($email)) {
        wp_send_json_error('آدرس ایمیل نامعتبر است');
    }
    if (!empty($new_password)) {
        if ($new_password !== $confirm_password) {
            wp_send_json_error('رمزهای عبور مطابقت ندارند');
        }
        if (strlen($new_password) < 6) {
            wp_send_json_error('رمز عبور باید حداقل ۶ کاراکتر باشد');
        }
    }

    // Prepare user data for update
    $user_data = array(
        'ID' => $user_id, // User ID is required
    );

    // Add fields to the user data array only if they are not empty
    if (!empty($username)) {
        $user_data['user_login'] = $username;
    }
    if (!empty($display_name)) {
        $user_data['display_name'] = $display_name;
    }
    if (!empty($email)) {
        $user_data['user_email'] = $email;
    }

    // Update the user
    $user_id_updated = wp_update_user($user_data);

    // Check if the user was updated successfully
    if (is_wp_error($user_id_updated)) {
        wp_send_json_error($user_id_updated->get_error_message());
    }

    // Update the role if provided
    if (!empty($role)) {
        update_user_meta($user_id, '_role', $role);
    }

    // Update the password if provided
    if (!empty($new_password)) {
        wp_set_password($new_password, $user_id);
    }

    // Return success response
    wp_send_json_success('کاربر با موفقیت به‌روزرسانی شد');
}
