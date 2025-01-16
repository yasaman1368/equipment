<?php
add_action('wp_ajax_exam_score', 'exam_score');
function exam_score()
{
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'ajax-nonce')) {
        wp_send_json(['msg' => 'access denied'], 403);
    }

    global $wpdb;
    $exam_answer = $_POST['answers'];
    $user_id = intval($_POST['userId']);
    $teacher_id = intval($_POST['teacherId']);
    $exam_code = intval($_POST['examCode']);
    $classroom_id = intval($_POST['classroomId']);

    $keyquestions = get_key_questions($wpdb, $user_id, $exam_code);
    $analysis_data = analysis_exam($wpdb, $exam_code);

    $score = 0;
    $counter_true_answer = $analysis_data ? json_decode($analysis_data->exam_count_true_answer, true) : [];

    foreach ($keyquestions as $questionId => $key) {
        $questionId = intval($questionId);
        $key = intval($key);
        if (isset($exam_answer[$questionId]) && $key === intval($exam_answer[$questionId])) {
            $score++;
            if ($analysis_data) {
                $counter_true_answer[$questionId]++;
            } else {
                $counter_true_answer[$questionId] = 1;
            }
        }
    }

    $collect_scores = $analysis_data ? json_decode($analysis_data->scores, true) : [];
    $collect_scores[] = $score;
    $collect_scores = json_encode($collect_scores);

    if ($analysis_data) {
        $number_test_participants = intval($analysis_data->number_test_participants) + 1;
        $update = update_analysis_data($wpdb, $exam_code, $counter_true_answer, $number_test_participants, $collect_scores);
        if (!$update) {
            wp_send_json(['error' => true, 'message' => ' بروزرسانی آنالیر امتحان ناموفق بود'], 403);
        }
    } else {
        $insert = insert_analysis_data($wpdb, $exam_code, $counter_true_answer, $teacher_id, $collect_scores);
        if (!$insert) {
            wp_send_json(['error' => true, 'message' => 'آنالیر امتحان ناموفق بود'], 403);
        }
    }

    if (!save_user_answer($wpdb, $user_id, $exam_answer, $score, $exam_code, $teacher_id)) {
        error_log("Failed to save exam results for user ID: $user_id, exam code: $exam_code");
        wp_send_json(['error' => true, 'message' => 'بدلیل بروز خطایی نمره آزمون برای شما ثیت نشد با پشتیبانی هماهنگ کنید'], 403);
    }

    calculate_score_points($user_id, $score);

    add_exam_data_to_classroom_user_answer($wpdb, $classroom_id, $user_id, $score, $exam_code, $teacher_id);
    wp_send_json(['success' => true, 'message' => 'آزمون شما با موفقیت پایان یافت'], 200);
}


function calculate_score_points($user_id, $score)
{
    $score_points = get_user_meta($user_id, '_score_points', true);
    $score_points = $score_points ? intval($score_points) : 0;
    $new_score_points = $score_points + intval($score);
    if ($score_points !== $new_score_points) {
        update_user_meta($user_id, '_score_points', $new_score_points);
    }
}

function save_user_answer($wpdb, $user_id, $exam_answer, $score, $exam_code, $teacher_id)
{
    $table = 'exam_users_data_result';
    $data = [
        'exam_answer' => json_encode($exam_answer),
        'score' => $score,
        'teacher_id' => $teacher_id,
        'status' => 1
    ];
    $where = ['user_id' => $user_id, 'exam_code' => $exam_code];
    return $wpdb->update($table, $data, $where);
}

function get_key_questions($wpdb, $user_id, $exam_code)
{
    $table = 'exam_users_data_result';
    $stmt = $wpdb->get_var($wpdb->prepare("SELECT key_questions FROM {$table} WHERE user_id=%d AND exam_code=%d", $user_id, $exam_code));
    if (!$stmt) {
        wp_send_json(['error' => true, 'message' => 'در هنگام محاسبه نمره مشکلی پیش آمده است لطفا با پشتیبانی تماس بگیرید'], 403);
    }
    return json_decode($stmt, true);
}

function analysis_exam($wpdb, $exam_code)
{
    $table = 'analysis_exam_data';
    return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE exam_code=%d", $exam_code));
}

function insert_analysis_data($wpdb, $exam_code, $counter_true_answer, $teacher_id, $collect_scores)
{
    $table = 'analysis_exam_data';
    $data = [
        'exam_count_true_answer' => json_encode($counter_true_answer),
        'exam_code' => $exam_code,
        'number_test_participants' => 1,
        'teacher_id' => $teacher_id,
        'scores' => $collect_scores
    ];
    return $wpdb->insert($table, $data);
}

function update_analysis_data($wpdb, $exam_code, $counter_true_answer, $number_test_participants, $collect_scores)
{
    $table = 'analysis_exam_data';
    $data = [
        'exam_count_true_answer' => json_encode($counter_true_answer),
        'number_test_participants' => $number_test_participants,
        'scores' => $collect_scores,
    ];
    $where = ['exam_code' => $exam_code];
    return $wpdb->update($table, $data, $where);
}

function add_exam_data_to_classroom_user_answer($wpdb, $classroom_id, $user_id, $score, $exam_code, $teacher_id)
{
    $classroom_exam_data = $wpdb->get_var($wpdb->prepare("SELECT students_exams_score FROM classrooms WHERE teacher_id=%d AND id=%d", $teacher_id, $classroom_id));

    $exam_classroom_scores = $classroom_exam_data ? json_decode($classroom_exam_data, true) : [];

    if (!isset($exam_classroom_scores[$exam_code])) {
        $exam_classroom_scores[$exam_code] = [];
    }

    if (!array_key_exists($user_id, $exam_classroom_scores[$exam_code])) {
        $exam_classroom_scores[$exam_code][$user_id] = $score;
        $data = ['students_exams_score' => json_encode($exam_classroom_scores)];
        $where = ['id' => $classroom_id, 'teacher_id' => $teacher_id];
        return $wpdb->update('classrooms', $data, $where);
    }
    return true;
}
