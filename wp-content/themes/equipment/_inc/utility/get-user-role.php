<?php
function eqiupment_get_user_role($user_id = 1)
{

    $get_role = get_user_meta($user_id, '_role', true);
    if ($get_role === 'manager') {
        $role = 'مدیر';
    } else if ($get_role === 'helper') {
        $role = 'ناظر';
    } else if ($get_role === 'user') {
        $role = 'کاربر';
    } else {
        $role = 'مدیر';
    }
    return $role;
}

function user_is_manager(): bool
{
    $user_id = get_current_user_id();
    $role = eqiupment_get_user_role($user_id);

    if ($role !== 'مدیر') {
        wp_send_json_error(['message' => 'شما دسترسی لازم برای انجام این عملیات را ندارید.'], 403);
        return false;
    }

    return true;
}
