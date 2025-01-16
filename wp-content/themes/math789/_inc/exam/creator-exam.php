<?php
add_action('wp_ajax_creator_exam', 'creator_exam');

function creator_exam()
{
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'creat-exam')) {
        wp_send_json(['msg' => 'access denied'], 403);
        exit; // Ensure the function exits after sending a response
    }

    $exam_name = sanitize_text_field($_POST['examName']);
    $base_education = sanitize_text_field($_POST['base']);
    $lesson = sanitize_text_field($_POST['lesson']);
    $cats_questions = $_POST['catsQuestions'];
    $questions_id_array = [];
    $container = '';
    $i = 0;

    // Pre-fetch all question IDs and options in one query
    $all_questions = [];
    foreach ($cats_questions as $cat => $questionsNumber) {
        $args = array(
            'post_type' => 'test',
            'posts_per_page' => intval($questionsNumber),
            'post_status' => 'publish',
            'orderby' => 'rand',
            'tax_query' => array(
                array(
                    'taxonomy' => 'test-cat',
                    'field' => 'slug',
                    'terms' => sanitize_text_field($cat),
                ),
            ),
        );
        $questions = new WP_Query($args);

        if ($questions->have_posts()) {
            foreach ($questions->posts as $question) {
                $questionId = $question->ID;
                $all_questions[$questionId] = [
                    'content' => $question->post_content,
                    'options' => [
                        get_post_meta($questionId, '_option-A', true),
                        get_post_meta($questionId, '_option-B', true),
                        get_post_meta($questionId, '_option-C', true),
                        get_post_meta($questionId, '_option-D', true)
                    ]
                ];
                $questions_id_array[] = $questionId;
            }
        } else {
            wp_send_json([
                'error' => true,
                'html' => "<div class='alert alert-danger'> در فصل '{$cat}' مورد نظر شما هیچ سوالی وجود ندارد</div>"
            ], 403);
            exit;
        }
        wp_reset_postdata();
    }

    // Render the questions
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
    global $wpdb; // Consider passing $wpdb as a parameter instead

    $user_id = get_current_user_id();
    $classrooms = $wpdb->get_results($wpdb->prepare("SELECT id, classroom_name FROM classrooms WHERE teacher_id=%d", $user_id));

    if ($classrooms) {
        $container .= '<div class="col-sm-12 col-md-4 col-lg-3">';
        $container .= '    <div class="mb-3">';
        $container .= '        <label for="lesson" class="form-label"> انتخاب کلاس<span class="fw-lighter text-muted bg-info rounded p-1">(اختیاری)</span></label>';
        $container .= '        <select class="form-select" name="lesson" id="classrooms">';
        $container .= '            <option value="">انتخاب کلاس</option>';

        foreach ($classrooms as $classroom) {
            $classroom_id = esc_attr($classroom->id); // Escape for HTML attribute
            $classroom_name = esc_html($classroom->classroom_name); // Escape for HTML output
            $container .= "            <option value=\"{$classroom_id}\">{$classroom_name}</option>";
        }

        $container .= '        </select>';
        $container .= '    </div>';
        $container .= '</div>';
    } else {
        $container .= "<div class='alert alert-info'>کلاسی ایجاد نکرده اید.</div>";
    }
    $container .= "<div> <button type='button' id='finishedExamCreate' class='mb-5 mt-2 btn btn-danger d-block' data-user-id=" . get_current_user_id() . ">ساخت آزمون</button></div>";
    $exam_data = [
        'questionsIdArray' => $questions_id_array,
        'exam_name' => $exam_name,
        'base_education' => $base_education,
        'test_time' => sanitize_text_field($_POST['testTime']),
        'lesson' => $lesson
    ];

    wp_send_json([
        'success' => true,
        'html' => $container,
        'exam_data' => json_encode($exam_data)
    ], 200);
}
