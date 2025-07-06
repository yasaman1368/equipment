<?php
add_action('wp_ajax_get_user_details', 'get_user_details');
function get_user_details()
{

    $user_id = filter_input(INPUT_GET, 'user_id', FILTER_VALIDATE_INT);

    if ($user_id) {
        $user = get_userdata($user_id);
        if (!$user) {
            wp_send_json_error('User not found');
            return;
        }
        $user_role = get_user_meta($user_id, '_role', true);

        $roles = [
            '' => 'انتخاب نقش...',
            'manager' => 'مدیر',
            'helper' => 'ناظر',
            'user' => 'کاربر'
        ];
        $html_selector = '';

        foreach ($roles as $value => $label) {
            $selected = ($user_role === $value) ? 'selected' : '';
            $html_selector .= "<option value=\"$value\" $selected>$label</option>";
        }

        wp_send_json_success(array(
            'user_login' => $user->user_login,
            'user_email' => $user->user_email,
            'display_name' => $user->display_name,
            'user_id' => $user->ID,
            'html_selector' => $html_selector,

        ));
    } else {
        wp_send_json_error('Invalid user ID');
    }
}
