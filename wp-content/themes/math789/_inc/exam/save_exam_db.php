<?php
add_action('wp_ajax_save_exam_db', 'save_exam_db');
function save_exam_db()
{
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'creat-exam')) {
        wp_send_json(['msg' => 'access denied'], 403);
    }
    $exam_data = json_encode($_POST['examData']);
    $userId = intval($_POST['userId']);
    $classroom_id = intval($_POST['classroomId']);
    $exam_code = create_exam_code($userId);
    $stmt = insert_data_exam_db($userId, $exam_data, $exam_code, $classroom_id);
    if ($stmt) {
        wp_send_json([
            'success' => true,
            'message' => 'آزمون شما با موفقیت ساخته شد',
        ], 200);
    } else {

        wp_send_json([
            'error' => true,
            'message' => 'آزمون شما ساخته نشد با پشتیبانی  تماس بگیرید',
        ], 403);
    }
}
function insert_data_exam_db($userId, $exam_data, $exam_code, $classroom_id)
{

    global $wpdb;
    $table = 'created_exam_data';
    $data = [
        'user_id' => $userId,
        'exam_code' => $exam_code,
        'exam_data' => $exam_data,
        'classroom_id' => $classroom_id,
    ];
    $format = ['%d', '%d', '%s', '%d'];
    $stmt = $wpdb->insert($table, $data, $format);
    return $stmt;
}
function create_exam_code($userId): int
{
    $exam_code = $userId . rand(99, 999);
    global $wpdb;
    $table = 'created_exam_data';
    $stmt = $wpdb->get_col("SELECT exam_code FROM {$table}");
    if (in_array($exam_code, $stmt)) {
        create_exam_code($userId);
    }

    return $exam_code;
}
