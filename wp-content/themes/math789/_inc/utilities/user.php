<?php
function user_display_name($user_id): string
{
    $user_info = get_userdata($user_id);

    // Check if user exists
    if ($user_info) {
        // Return the display_name
        return $user_info->display_name;
    } else {
        return null; // User not found
    }
}
