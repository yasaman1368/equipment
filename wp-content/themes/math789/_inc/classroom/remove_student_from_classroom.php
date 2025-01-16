<?php
add_action('wp_ajax_remove_student_from_classroom', 'remove_student_from_classroom');

function remove_student_from_classroom()
{
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'remove-student')) {
        wp_send_json(['msg' => 'access denied'], 403);
    }

    // Check if classroomId and studentId are set
    if (!isset($_POST['classroomId']) || !isset($_POST['studentId'])) {
        wp_send_json(['error' => true, 'message' => 'Invalid classroom or student ID.'], 400);
    }

    $classroom_id = intval($_POST['classroomId']);
    $student_id = intval($_POST['studentId']);

    // Fetch user classrooms only if the student ID is valid
    $user_classrooms_json = get_user_meta($student_id, '_classroom_id', true);
    $user_classrooms = json_decode($user_classrooms_json, true);

    if (is_array($user_classrooms) && isset($user_classrooms[get_current_user_id()])) {
        unset($user_classrooms[get_current_user_id()]);
        update_user_meta($student_id, '_classroom_id', json_encode($user_classrooms));
    } else {
        wp_send_json([
            'error' => true,
            'message' => 'در روند حذف از کلاس مشکلی پیشامد'
        ], 403);
    }

    global $wpdb;
    $classroom_ids_json = $wpdb->get_var($wpdb->prepare("SELECT students_id FROM classrooms WHERE id = %d", $classroom_id));
    $classroom_ids = json_decode($classroom_ids_json, true);

    if (is_array($classroom_ids)) {
        $updated_classroom_ids = array_diff($classroom_ids, [$student_id]);
        $updated_classroom_ids_json = json_encode($updated_classroom_ids);

        $stmt = $wpdb->update(
            'classrooms',
            ['students_id' => $updated_classroom_ids_json],
            ['id' => $classroom_id],
            ['%s'],
            ['%d']
        );

        if ($stmt === false) {
            wp_send_json(['error' => true, 'message' => 'Database error occurred while updating classroom.'], 500);
        }
        wp_send_json([
            'success' => true,
            'message' => user_display_name($student_id) . ' از کلاس حذف شد'
        ], 200);
    } else {
        wp_send_json(['error' => true, 'message' => 'Classroom not found or no students enrolled.'], 404);
    }
}
