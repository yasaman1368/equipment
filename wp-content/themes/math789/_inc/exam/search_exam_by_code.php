<?php

use function PHPSTORM_META\type;

add_action('wp_ajax_search_exam_by_code', 'search_exam_by_code');

function search_exam_by_code()
{
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'creat-exam')) {
        wp_send_json(['msg' => 'access denied'], 403);
    }

    global $wpdb;
    $exam_code = intval($_POST['examCode']);
    $user_id = intval($_POST['userId']);
    $status_active = is_exam_active($wpdb, $exam_code);
    if (intval($status_active) === 0) {
        wp_send_json(['error' => true, 'message' => 'آزمون غیر فعال است'], 403);
    }
    check_valide_exam_user($wpdb, $user_id, $exam_code);
    $data = get_exam_data($wpdb, $exam_code);

    if (is_wp_error($data)) {
        wp_send_json($data, 403);
    }

    $exam_data = json_decode($data->exam_data);
    $questionsIdArray = $exam_data->questionsIdArray;
    $countQuestions = count($questionsIdArray);
    $test_time = $exam_data->test_time ? intval($exam_data->test_time) : 'نامعین';
    $exam_name = $exam_data->exam_name ? sanitize_text_field($exam_data->exam_name) : 'بدون نام';
    $lesson = $exam_data->lesson ? sanitize_text_field($exam_data->lesson) : 'نامعین';
    $teacher = get_user_by('ID', $data->user_id);
    $teacher_display_name = $teacher->display_name;

    $html = '
    <div class="container mt-5 bg-light rounded">
        <h3 class="p-4">اطلاعات آزمون</h3>
        <div class="row justify-content-center align-items-center text-center pt-2 g-2">
            <div class="col-sm-3">
                <div class="exam-data-content shadow-sm bg-white p-2 rounded">
                    <div class="exam-data-title text-muted fw-lighter p-1">نام آزمون</div>
                    <div class="exam-name-text text-success fw-bolder p-1">' . esc_html($exam_name) . '</div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="exam-data-content shadow-sm bg-white p-2 rounded">
                    <div class="exam-data-title text-muted fw-lighter p-1">نام درس</div>
                    <div class="exam-name-text text-success fw-bolder p-1">' . esc_html($lesson) . '</div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="exam-data-content shadow-sm bg-white p-2 rounded">
                    <div class="exam-data-title text-muted fw-lighter p-1">تعداد سوال</div>
                    <div class="exam-name-text text-success fw-bolder p-1">' . esc_html($countQuestions) . '</div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="exam-data-content shadow-sm bg-white p-2 rounded">
                    <div class="exam-data-title text-muted fw-lighter p-1">وقت آزمون</div>
                    <div class="exam-name-text text-success fw-bolder p-1">' . esc_html($test_time) . ' دقیقه </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="exam-data-content shadow-sm bg-white p-2 rounded">
                    <div class="exam-data-title text-muted fw-lighter p-1">طراح سوال</div>
                    <div class="exam-name-text text-success fw-bolder p-1">' . esc_html($teacher_display_name) . '</div>
                </div>
            </div>
            <div class="text-center align-items-center">
                <button type="button" class="btn btn-outline-success my-3 render-exam-html" data-user-id="' . esc_attr($user_id) . '" data-exam-data="' . esc_attr($exam_code) . '">شروع آزمون</button>
                <input type="hidden" name="ajax-nonce" value="' . wp_create_nonce('creat-exam') . '">
                <input type="hidden" name="ajax-url" value="' . admin_url('admin-ajax.php') . '">
            </div>
        </div>
    </div>';

    wp_send_json(['success' => true, 'html' => $html], 200);
}

function check_valide_exam_user($wpdb, $user_id, $exam_code)
{
    $table = 'exam_users_data_result';
    $stmt = $wpdb->get_row($wpdb->prepare("SELECT status, score FROM {$table} WHERE user_id= %d AND exam_code= %d", $user_id, $exam_code));
    if ($stmt->status == 1) {
        wp_send_json(['error' => true, 'message' => 'شما در این آزمون شرکت کرده اید و نمره شما: ' . esc_html($stmt->score) . ' ثبت شده است'], 403);
    }
    return true;
}

function get_exam_data($wpdb, $exam_code)
{
    $table = 'created_exam_data';
    $stmt = $wpdb->get_row($wpdb->prepare("SELECT exam_data, user_id FROM {$table} WHERE exam_code = %d", $exam_code));

    if (!$stmt) {
        return new WP_Error('no_exam', 'آزمونی با این کد ثبت نشده است');
    }
    return $stmt;
}
function is_exam_active($wpdb, $exam_code)
{
    $table = 'created_exam_data';
    $status_active = $wpdb->get_var($wpdb->prepare("SELECT status FROM {$table} WHERE exam_code = %d", $exam_code));
    return $status_active;
}
