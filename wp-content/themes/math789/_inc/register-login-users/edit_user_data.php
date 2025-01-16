<?php
add_action('wp_ajax_edit_user_data', 'edit_user_data');

function edit_user_data()
{
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'form-edit-nonce')) {
        wp_send_json(['message' => 'access denied'], 403);
    }

    $user_id = get_current_user_id();
    $user_data = get_userdata($user_id); // Store user data to avoid multiple calls

    $display_name = sanitize_text_field($_POST['displayName']);
    $nice_name = sanitize_text_field($_POST['niceName']);
    $phone = sanitize_text_field($_POST['user-phone']);
    $old_pass = sanitize_text_field($_POST['old-pass']);
    $new_pass = sanitize_text_field($_POST['new-pass']);
    $repeat_new_pass = sanitize_text_field($_POST['repeat-new-pass']);

    // Validate old password
    if (!wp_check_password($old_pass, $user_data->user_pass, $user_id)) {
        wp_send_json(['error' => true, 'message' => 'رمز قبلی با رمز شما مطابقت ندارد '], 403);
    }

    // Validate new password
    if (mb_strlen($new_pass) < 6) {
        wp_send_json(['error' => true, 'message' => 'رمز ورود نمی تواند کمتر از 6 حرف باشد'], 403);
    }

    if ($repeat_new_pass !== $new_pass) {
        wp_send_json(['error' => true, 'message' => 'رمز جدید با تکرار رمز مطابقت ندارد '], 403);
    }

    // Prepare user data for update
    $userdata = [
        'ID' => $user_id,
        'user_pass' => $new_pass,
        'user_nicename' => $nice_name,
        'display_name' => $display_name,
    ];

    // Update user data
    $userUpdateData = wp_update_user($userdata);

    if (!is_wp_error($userUpdateData)) {
        wp_send_json(['success' => true, 'message' => 'تغییرات با موفقیت انجام شد'], 200);
    } else {
        wp_send_json(['error' => true, 'message' => 'ویرایش بدلیل بروز خطایی صورت نگرفت'], 403);
    }
}
