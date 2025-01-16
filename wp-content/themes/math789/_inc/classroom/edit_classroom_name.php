<?php
add_action('wp_ajax_edit_classroom_name', 'edit_classroom_name');
function edit_classroom_name()
{
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'create-classroom')) {
        wp_send_json(['msg' => 'access denied'], 403);
    }
    $class = sanitize_text_field($_POST['newClassroomName']);
    $school = sanitize_text_field($_POST['newSchoolName']);
    $classroom_name = $class . '|' . $school;
    $id = intval($_POST['classroomId']);

    global $wpdb;
    $table = 'classrooms';
    $data = [
        'classroom_name' => $classroom_name,
    ];
    $where = [
        'id' => $id,
    ];
    $format = ['%s'];
    $where_format = ['%d'];
    $stmt = $wpdb->update($table, $data, $where, $format, $where_format);
    if ($stmt === false) {
        wp_send_json([
            'error' => true,
            'message' => 'وایریش کلاس  با مشکل مواجه شد'
        ], 403);
    }
    wp_send_json([
        'success' => true,
        'message' => 'کلاس  ' . $classroom_name . ' ویرایش شد. ',
        'classroomName' => $class,
        'schoolName' => $school,
    ], 200);
}
