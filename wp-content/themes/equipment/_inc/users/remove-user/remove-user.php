<?php
add_action('wp_ajax_remove_user', 'remove_user');
add_action('wp_ajax_nopriv_remove_user', 'remove_user');

function remove_user()
{
    $user_id = _sanitize_text_fields($_POST['user_id']);

    if ($user_id) {
        require_once(ABSPATH . 'wp-admin/includes/user.php');
        $stmt = wp_delete_user($user_id);
        if ($stmt) {
            wp_send_json_success([
                'message' => 'کاربر مورد نظر با موفقیت حذف شد'
            ]);
        } else {
            wp_send_json_error([
                'message' => 'کاربر مورد نظر  حذف نشد'
            ]);
        }
    }
}
