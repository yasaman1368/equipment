<?php
add_action('wp_ajax_remove_classroom', 'remove_classroom');
function remove_classroom()
{
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'create-classroom')) {
        wp_send_json(['msg' => 'access denied'], 403);
    }
    $classroom_id = intval($_POST['classroomId']);
    $classroomName = sanitize_text_field($_POST['classroomName']);

    global $wpdb;
    $stmt = $wpdb->delete('classrooms', ['id' => $classroom_id], ['%d']);
    if ($stmt === false) {
        wp_send_json([
            'error' => true,
            'message' => 'حذف کلاس  با مشکل مواجه شد'
        ], 403);
    }
    wp_send_json([
        'success' => true,
        'message' => 'کلاس  ' . $classroomName . ' حذف شد. ',
    ], 200);
}
