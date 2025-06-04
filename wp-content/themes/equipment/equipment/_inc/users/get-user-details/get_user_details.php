<?php
// Add this to handle the AJAX request for fetching user details
add_action('wp_ajax_get_user_details', 'get_user_details');
add_action('wp_ajax_nopriv_get_user_details', 'get_user_details'); // If you want non-logged-in users to access it
function get_user_details()
{
    // Verify user permissions (optional, but recommended)
    // if (!current_user_can('edit_users')) {
    //     wp_send_json_error('You do not have permission to access this data.');
    //     return;
    // }
    // Use filter_input for better input handling
    $user_id = filter_input(INPUT_GET, 'user_id', FILTER_VALIDATE_INT);

    if ($user_id) {
        $user = get_userdata($user_id);
        if (!$user) {
            wp_send_json_error('User not found');
            return;
        }
        // Get user role directly from the user object
        $user_role = get_user_meta($user_id, '_role', true); // Assuming roles are stored as an array
        // Generate HTML for the role selection
        $roles = [
            '' => 'انتخاب نقش...',
            'manager' => 'مدیر',
            'helper' => 'معاون',
            'user' => 'کاربر'
        ];
        $html_selector = '';
        // $html_selector = '<select class="form-select" name="role" id="roleSelector" required>';
        foreach ($roles as $value => $label) {
            $selected = ($user_role === $value) ? 'selected' : '';
            $html_selector .= "<option value=\"$value\" $selected>$label</option>";
        }
        //  $html_selector .= '</select>';
        // Send the JSON response
        wp_send_json_success(array(
            'user_login' => $user->user_login,
            'user_email' => $user->user_email,
            'display_name' => $user->display_name,
            'user_id' => $user->ID,
            'html_selector' => $html_selector,
            // Add more fields as needed
        ));
    } else {
        wp_send_json_error('Invalid user ID');
    }
}
