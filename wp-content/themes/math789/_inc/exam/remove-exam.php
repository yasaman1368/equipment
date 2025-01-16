<?php
add_action('wp_ajax_remove_exam', 'remove_exam');

function remove_exam()
{
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'creat-exam')) {
        wp_send_json(['msg' => 'access denied'], 403);
        exit; // Ensure the function exits after sending a response
    }

    $exam_code = intval($_POST['examCode']);
    $result = remove_exam_db($exam_code);

    if ($result['success']) {
        wp_send_json(['success' => true, 'message' => "آزمون حذف شد"], 200);
    } else {
        wp_send_json(['error' => true, 'message' => $result['message']], 403);
    }
}

function remove_exam_db($exam_code)
{
    global $wpdb;
    $tables = ['analysis_exam_data', 'created_exam_data', 'exam_users_data_result'];
    $errors = [];

    foreach ($tables as $table) {
        $stmt = $wpdb->delete($table, ['exam_code' => $exam_code], ['%d']);
        if ($stmt === false) {
            $errors[] = "Error deleting from $table"; // Consider adding more specific error messages if needed
        }
    }

    if (!empty($errors)) {
        return ['success' => false, 'message' => "آزمونی برای حذف یافت نشد"]; // You can customize the message or log errors
    }

    return ['success' => true];
}
