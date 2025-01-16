<?php

add_action('wp_ajax_creator_classroom', 'creator_classroom');
function creator_classroom()
{
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'create-classroom')) {
        wp_send_json(['msg' => 'access denied'], 403);
    }
    $classroomName = sanitize_text_field($_POST['classroomName']) . '|' . sanitize_text_field($_POST['schoolName']);
    $teacher_id = get_current_user_id();
    global $wpdb;

    $data = [
        'teacher_id' => $teacher_id,
        'classroom_name' => $classroomName
    ];
    $format = [
        '%d',
        '%s'
    ];
    $stmt = $wpdb->insert('classrooms', $data, $format);
    if ($stmt) {
        wp_send_json([
            'success' => true,
            'message' => 'کلاس  ' . $classroomName . ' افزوده شد. ',
        ], 200);
    } else {
        wp_send_json([
            'error' => true,
            'message' => 'ساخت کلاس جدید با مشکل مواجه شد'
        ], 403);
    }
}
