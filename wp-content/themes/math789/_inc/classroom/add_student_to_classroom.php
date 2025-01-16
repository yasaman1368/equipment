<?php
add_action('wp_ajax_add_student_to_classroom', 'add_student_to_classroom');

function add_student_to_classroom()
{
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'creat-exam')) {
        wp_send_json(['msg' => 'access denied'], 403);
    }

    // Sanitize and prepare data
    $student_id = intval($_POST['studentId']);
    $classroom_id = intval($_POST['classroomId']);

    // Get current classrooms ID
    $user_classrooms = get_user_meta($student_id, '_classroom_id', true);
    if ('' !== $user_classrooms) {
        // the meta-field is

        $user_classrooms = json_decode($user_classrooms, true);
    } else {
        $user_classrooms = [];
    }
    if (!in_array($classroom_id, $user_classrooms)) {
        $user_classrooms[get_current_user_id()] = $classroom_id;
        $user_classrooms = json_encode($user_classrooms);
        update_user_meta($student_id, '_classroom_id', $user_classrooms);
    }

    global $wpdb;
    $table = 'classrooms';

    // Fetch students IDs
    $students_id = $wpdb->get_var($wpdb->prepare("SELECT students_id FROM {$table} WHERE id=%d", $classroom_id));
    $students_id = $students_id ? json_decode($students_id, true) : [];

    // Check if student already exists in the classroom
    if (in_array($student_id, $students_id)) {
        $student = get_user_by('ID', $student_id);
        if ($student) {
            wp_send_json([
                'info' => true,
                'message' => $student->display_name . ' در کلاس   '
            ], 200);
        } else {
            wp_send_json(['error' => true, 'message' => 'Student not found'], 404);
        }
    }

    // Add student to classroom
    $students_id[] = $student_id;
    $data = ['students_id' => json_encode($students_id)];

    // Update the classroom
    $stmt = $wpdb->update($table, $data, ['id' => $classroom_id], ['%s'], ['%d']);

    if ($stmt) {
        $student = get_user_by('ID', $student_id);
        if ($student) {
            wp_send_json([
                'success' => true,
                'message' => $student->display_name . ' به کلاس  '
            ], 200);
        } else {
            wp_send_json(['error' => true, 'message' => 'Student not found'], 404);
        }
    } else {
        wp_send_json(['error' => true, 'message' => 'دانش آموز مورد نظر به کلاس  اضافه نشد'], 403);
    }
}
