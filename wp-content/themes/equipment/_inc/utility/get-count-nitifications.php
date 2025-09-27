<?php
defined('ABSPATH') || exit;

/**
 * گرفتن تعداد اعلان‌های workflow بر اساس نقش کاربر فعلی
 */
function get_workflow_notification_count() {
    global $wpdb;

    $current_user_id   = get_current_user_id();
    $current_user_role = get_user_meta($current_user_id, '_role', true);

    // supervisor
    if ($current_user_role === 'supervisor') {
        $users_relative_by_supervisor = get_users_relative_by_supervisor($current_user_id);

        if (empty($users_relative_by_supervisor)) {
            return 0;
        }

        $user_ids = implode(',', array_map('intval', $users_relative_by_supervisor));

        $query = $wpdb->prepare(
            "SELECT COUNT(*) 
             FROM workflow 
             WHERE current_status IN (%s, %s) 
             AND user_id IN ($user_ids)",
            ['Pending', 'ManagerReject']
        );
    }

    // manager or admin
    elseif ($current_user_role === 'manager' || current_user_can('administrator')) {
        $query = $wpdb->prepare(
            "SELECT COUNT(*) 
             FROM workflow 
             WHERE current_status = %s",
            ['SupervisorApproved']
        );
    }

    // normal user
    elseif ($current_user_role === 'user') {
        $query = $wpdb->prepare(
            "SELECT COUNT(*) 
             FROM workflow 
             WHERE current_status = %s 
             AND user_id = %d",
            ['SupervisorReject', $current_user_id]
        );
    }

    // اگر نقشی نبود
    else {
        return 0;
    }

    return (int) $wpdb->get_var($query);
}
