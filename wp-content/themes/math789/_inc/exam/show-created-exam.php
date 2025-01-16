<?php
add_action('wp_ajax_show_created_exam', 'show_created_exam');

function show_created_exam()
{
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'creat-exam')) {
        wp_send_json(['msg' => 'access denied'], 403);
        exit; // Ensure the function exits after sending a response
    }
    $exam_code = intval($_POST['examCode']);
    $exam_data = get_exam_data_display_teacher($exam_code);
    $questions_id = $exam_data['questionsIdArray'];
    $exam_time = $exam_data['test_time'];
    foreach ($questions_id as $questionId) {

        $all_questions[$questionId] = [
            'content' => get_post($questionId)->post_content,
            'options' => [
                get_post_meta($questionId, '_option-A', true),
                get_post_meta($questionId, '_option-B', true),
                get_post_meta($questionId, '_option-C', true),
                get_post_meta($questionId, '_option-D', true)
            ]
        ];
    }

    // Render the questions

    $i = 0;
    $container = '';
    $container .= '<div class=" bg-light p-4 rounded">';
    $container .= '<div> وقت آزمون: ' . $exam_time . ' دقیقه</div>';

    foreach ($all_questions as $questionId => $questionData) {
        $i++;
        shuffle($questionData['options']); // Shuffle options for display
        $container .= "<div class='question mt-4' data-question-number='{$i}'>";
        $container .= "<h5>سوال {$i}: {$questionData['content']}</h5>";
        foreach ($questionData['options'] as $index => $option) {
            $check = (get_post_meta($questionId, '_option-A', true) == $option) ? '<i class="bi bi-check-lg text-success"></i>' : '';
            $container .= "<div class='option-custom-exam'><span class='index-option'>" . esc_html(($index + 1) . ') ' . $option) . " {$check}</span></div>";
        }
        $container .= "</div><hr>";
    }
    $container .= '<a href="' . site_url('panel?exam_status=active') . '"><button type="button"  class="mb-5 btn btn-success" >بازگشت</button></a></div>';
    wp_send_json([
        'success' => true,
        'html' => $container,
    ], 200);
}
function get_exam_data_display_teacher($exam_code)
{
    global $wpdb;
    $exam_data = $wpdb->get_var($wpdb->prepare("SELECT exam_data FROM created_exam_data WHERE exam_code=%d", $exam_code));
    if (!$exam_data) {
        wp_send_json([
            'error' => true,
            'html' => "<div class='alert alert-danger'> آزمونی برای نمایش یافت نشد</div>"
        ], 403);
    }
    return json_decode($exam_data, true);
}
