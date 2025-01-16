<?php
add_action('wp_ajax_save_quetion_creat_custom_exam', 'save_quetion_creat_custom_exam');
function save_quetion_creat_custom_exam()
{
    if (!isset($_POST['ajax-nonce']) || !wp_verify_nonce($_POST['ajax-nonce'], 'creat-exam')) {
        wp_send_json(['msg' => 'access denied'], 403);
        exit;
    }

    $post_content =  stripslashes(sanitize_text_field($_POST['question-content']));
    $userId = get_current_user_id();
    $classroom_id = intval($_POST['classroom']);
    $exam_time = sanitize_text_field($_POST['test-time']);
    $exam_code = intval($_POST['examcode']);
    $exam_name_custom = sanitize_text_field($_POST['exam_name_custom']);
    $base_education = sanitize_text_field($_POST['base-education']);
    $lesson = sanitize_text_field($_POST['lesson']);

    $post_data = [
        'post_title'    => $exam_name_custom,
        'post_content'  => $post_content,
        'post_name'     => 'post_slug',
        'post_status'   => 'publish',
        'post_type'     => 'test',
        'post_author'   => $userId,
        'meta_input'    => [
            '_option-A' => stripslashes(sanitize_text_field($_POST['option-A'])),
            '_option-B' => stripslashes(sanitize_text_field($_POST['option-B'])),
            '_option-C' => stripslashes(sanitize_text_field($_POST['option-C'])),
            '_option-D' => stripslashes(sanitize_text_field($_POST['option-D'])),
        ],
    ];

    $post_id = wp_insert_post(wp_slash($post_data));
    if (!$post_id) {
        wp_send_json(['error' => true, 'message' => 'سوال ذخیره نشد'], 403);
    }

    $questions_id_array = [];
    if ($exam_code === 0) {
        $questions_id_array[] = $post_id;
        $exam_data = [
            'questionsIdArray' => $questions_id_array,
            'exam_name' => $exam_name_custom,
            'test_time' => $exam_time,
            'base_education' => $base_education,
            'lesson' => $lesson
        ];
        $exam_data_json = json_encode($exam_data);
        $exam_code = create_exam_code($userId);
        $stmt = insert_data_exam_db($userId, $exam_data_json, $exam_code, $classroom_id);
        if (!$stmt) {
            wp_send_json(['error' => true, 'message' => 'آزمون ثبت نشد'], 403);
        }
    } else {
        $questions_id_array = get_current_exam($exam_code);
        $questions_id_array[] = $post_id;
        $exam_data = [
            'questionsIdArray' => $questions_id_array,
            'exam_name' => $exam_name_custom,
            'test_time' => $exam_time,
            'base_education' => $base_education,
            'lesson' => $lesson
        ];
        $exam_data_json = json_encode($exam_data);
        update_current_exam($exam_data_json, $exam_code);
    }

    // Cache post objects
    $all_questions = [];
    $post_objects = [];
    foreach ($questions_id_array as $questionId) {
        $post_objects[$questionId] = get_post($questionId);
    }

    foreach ($questions_id_array as $questionId) {
        $post = $post_objects[$questionId];
        $all_questions[$questionId] = [
            'content' => $post->post_content,
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
    $container = '<div class="bg-light p-4 rounded">';
    $container .= '<div> نام آزمون: ' . esc_html($exam_name_custom !== '' ?: 'بدون نام  ') . ' </div>';
    $container .= '<div> وقت آزمون: ' . esc_html($exam_time !== '' ? $exam_time . ' دقیقه ' : 'نامحدود') . ' </div>';
    foreach ($all_questions as $questionId => $questionData) {
        $i++;
        shuffle($questionData['options']);
        $container .= "<div class='question mt-4' data-question-number='{$i}'>";
        $container .= "<h5>سوال {$i}: {$questionData['content']}</h5>";
        foreach ($questionData['options'] as $index => $option) {
            $check = (get_post_meta($questionId, '_option-A', true) == $option) ? '<i class="bi bi-check-lg text-success"></i>' : '';
            $container .= "<div class='option-custom-exam'><span class='index-option'>" . esc_html(($index + 1) . ') ' . $option) . " {$check}</span></div>";
        }
        $container .= "</div><hr>";
    }
    $container .= '<a href="' . esc_url(site_url('panel?exam_status=active')) . '"><button type="button" class="mb-5 btn btn-success">اتمام</button></a></div>';

    wp_send_json([
        'success' => true,
        'exam_code' => $exam_code,
        'html' => $container,
        'message' => 'سوال با موفقیت به آزمون اضافه شد',
    ], 200);
}

function get_current_exam($exam_code)
{
    global $wpdb;
    $stmt = $wpdb->get_var($wpdb->prepare("SELECT exam_data FROM created_exam_data WHERE exam_code=%d", $exam_code));
    $questions_id_array = (json_decode($stmt))->questionsIdArray ?? [];
    return $questions_id_array;
}

function update_current_exam($exam_data, $exam_code)
{
    global $wpdb;
    $stmt = $wpdb->update('created_exam_data', ['exam_data' => $exam_data], ['exam_code' => $exam_code], ['%s'], ['%d']);
    if ($stmt === false) {
        wp_send_json(['error' => true, 'message' => 'آزمون بروزرسانی نشد'], 403);
    }
}
