<?php
add_action('wp_ajax_render_html_exam_by_code', 'render_html_exam_by_code');

function render_html_exam_by_code()
{
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'creat-exam')) {
        wp_send_json(['msg' => 'access denied'], 403);
    }

    global $wpdb;
    $exam_code = sanitize_text_field($_POST['examCode']);
    $user_id = sanitize_text_field($_POST['userId']);
    $status_active = is_exam_active($wpdb, $exam_code);
    if (intval($status_active) === 0) {
        wp_send_json(['error' => true, 'message' => 'آزمون غیر فعال است'], 403);
    }
    $data = get_exam_data($wpdb, $exam_code);
    $exam_data = json_decode($data->exam_data);

    // Default values
    $teacher_id = $data->user_id;
    $questionsIdArray = $exam_data->questionsIdArray;
    $test_time = $exam_data->test_time ? intval($exam_data->test_time) * 60 : 0;
    $exam_name = $exam_data->exam_name ? sanitize_text_field($exam_data->exam_name) : 'بدون نام';
    $classroom_id = (json_decode(get_user_meta($user_id, '_classroom_id', true), true))[$teacher_id];

    ob_start();
?>
    <div class="container bg-light rounded p-3 mt-5">
        <span class=" text-white p-2  position-sticky  text-center rounded-5" id="display-timer"></span>
        <h2 class="text-center"><?php echo esc_html($exam_name); ?></h2>
        <form id="mathExamForm">
            <?php
            $i = 0;
            foreach ($questionsIdArray as $questionId):
                $i++;
                $question = get_post($questionId);
                $options = [
                    get_post_meta($questionId, '_option-A', true),
                    get_post_meta($questionId, '_option-B', true),
                    get_post_meta($questionId, '_option-C', true),
                    get_post_meta($questionId, '_option-D', true)
                ];
                shuffle($options);
            ?>
                <div class="question mt-4" data-question-number="<?php echo $i ?>">
                    <h5>سوال <?php echo $i ?>: <?php echo $question->post_content ?></h5>
                    <?php foreach ($options as  $index => $option):
                        $question_number[$index] =  $option;
                        if (get_post_meta($questionId, '_option-A', true) == $option) {
                            $keyquestions[$questionId] = $index + 1;
                        }
                    ?>
                        <div class="option-custom-exam"
                            data-question="<?php echo esc_attr($i); ?>"
                            data-question-id="<?php echo esc_attr($questionId); ?>"
                            data-answer="<?php echo esc_attr($index + 1); ?>"
                            onclick="selectOption(this)">
                            <span class="index-option"><?php echo esc_html(($index + 1) . ') ' . $option); ?></span>
                        </div>

                    <?php endforeach ?>
                    <?php $exam_data_rendered[$questionId] = $question_number ?>
                </div>
            <?php endforeach; ?>
            <button type="button" class="btn btn-outline-danger mt-3 mb-5" onclick="submitAnswers()">ثبت پاسخ ها</button>
            <input type="hidden" name="ajax-nonce"
                data-user-id="<?php echo esc_attr($user_id); ?>"
                data-exam-code="<?php echo esc_attr($exam_code); ?>"
                data-teacher-id="<?php echo esc_attr($teacher_id); ?>"
                data-classroom-id="<?php echo esc_attr($classroom_id); ?>"
                value="<?php echo wp_create_nonce('ajax-nonce'); ?>">
            <input type="hidden" name="ajax-url" value="<?php echo esc_url(admin_url('admin-ajax.php')); ?>">
        </form>
    </div>
    <script>
        let answers = {};
        const ajaxUrl = $('input[name=ajax-url]').val();
        const ajaxNonce = $('input[name=ajax-nonce]').val();
        const examCode = $('input[name=ajax-nonce]').data('exam-code');
        const userId = $('input[name=ajax-nonce]').data('user-id');
        const teacherId = $('input[name=ajax-nonce]').data('teacher-id');
        const classroomId = $('input[name=ajax-nonce]').data('classroom-id');
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        function selectOption(option) {
            let questionId = option.getAttribute('data-question-id');
            let questionAnswer = option.getAttribute('data-answer');
            answers[questionId] = questionAnswer;

            // Remove selected class from all options and add to the clicked option
            let options = option.parentElement.querySelectorAll('.option-custom-exam');
            options.forEach(opt => opt.classList.remove('selected'));
            option.classList.add('selected');
        }

        function submitAnswers() {
            const questions = $('.question');
            const answersArray = [];
            const questionsNumber = [];

            questions.each(function() {
                questionsNumber.push(this.getAttribute('data-question-number'));
            });

            const selectedAnswers = questions.find('.selected').parent('.question');
            selectedAnswers.each(function() {
                answersArray.push(this.getAttribute('data-question-number'));
            });

            let forgetedQuestion = questionsNumber.filter(item => !answersArray.includes(item));
            let message = forgetedQuestion.length === 0 ?
                'شما به همه سوالات پاسخ داده اید آیا از ثبت پاسخ خود اطمینان دارید؟' :
                'شما به سوالات ' + forgetedQuestion.join('و') + ' پاسخ نداده اید آیا از ثبت پاسخ خود اطمینان دارید؟';

            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: "btn btn-success",
                    cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });
            swalWithBootstrapButtons.fire({
                title: '',
                text: message,
                icon: "warning",
                showCancelButton: true,
                cancelButtonText: "خیر، بازگشت",
                confirmButtonText: "بله، ثبت پاسخ",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "post",
                        url: ajaxUrl,
                        data: {
                            answers: answers,
                            action: 'exam_score',
                            nonce: ajaxNonce,
                            examCode: examCode,
                            userId: userId,
                            teacherId: teacherId,
                            classroomId: classroomId
                        },
                        dataType: "json",
                        success: function(response) {
                            if (response.success) {
                                Toast.fire({
                                    icon: 'success',
                                    title: response.message
                                });
                                window.location = window.location.origin + '/panel/report';
                            }
                        },
                        error: function(response) {
                            if (response.responseJSON.error) {
                                Toast.fire({
                                    icon: 'error',
                                    title: response.responseJSON.message
                                });
                            }

                        },
                        complete: function() {
                            localStorage.removeItem('endTime')
                        }

                    });
                }
            });
        }
        $('#display-timer').click(function(e) {
            e.preventDefault();
            $(this).toggleClass('position-sticky');
        });
        let duration = <?php echo $test_time ?>;
        const timerDisplay = document.getElementById('display-timer');
        let endTime;

        function startTimer() {
            const existingEndTime = localStorage.getItem('endTime');
            if (existingEndTime) {
                endTime = parseInt(existingEndTime);
                duration = Math.max(0, Math.floor((endTime - Date.now()) / 1000))
            } else {
                endTime = Date.now() + duration * 1000;
                localStorage.setItem('endTime', endTime);
            }
            const interval = setInterval(() => {
                if (duration <= 0) {
                    clearInterval(interval);

                    alert('زمان آزمون به پایان رسید در حال ثیت پاسخ های شما');
                    $.ajax({
                        type: "post",
                        url: ajaxUrl,
                        data: {
                            answers: answers,
                            action: 'exam_score',
                            nonce: ajaxNonce,
                            examCode: examCode,
                            userId: userId,
                            teacherId: teacherId
                        },
                        dataType: "json",
                        success: function(response) {
                            if (response.success) {
                                Toast.fire({
                                    icon: 'success',
                                    title: response.message
                                });
                                window.location = window.location.origin + '/panel/report';
                            }
                        },
                        error: function(response) {
                            if (response.responseJSON.error) {
                                Toast.fire({
                                    icon: 'error',
                                    title: response.responseJSON.message
                                });
                            }
                        },
                        complete: function() {
                            localStorage.removeItem('endTime')
                        }
                    });

                } else {
                    const minutes = Math.floor(duration / 60);
                    const seconds = duration % 60;
                    timerDisplay.textContent = `${minutes}:${seconds < 10 ? '0' + seconds : seconds}`;
                    duration--;
                }
            }, 1000)
        }
        if (duration != 0) {
            startTimer();
        }
    </script>
<?php
    $html = ob_get_clean();
    $exam_data_randered_and_created = [
        'renderd_exam' => $exam_data_rendered,
        'created_exam' => $data
    ];

    $exam_exist = check_exist_exam_user($wpdb, $user_id, $exam_code);
    if (!$exam_exist) {
        $saved_exam_rendered_data = save_render_exam_db($wpdb, $user_id, $exam_data_randered_and_created, $exam_code, $keyquestions, $teacher_id);
    } else {
        $update_exam_rendered_data = update_render_exam_db($wpdb, $user_id, $exam_data_randered_and_created, $exam_code, $keyquestions, $teacher_id);
    }

    if ($saved_exam_rendered_data === false || $update_exam_rendered_data === false) {
        wp_send_json(['error' => true, 'message' => 'ساخت آزمون با مشکل مواجه شد،با پشتبانی تماس بگیرید'], 403);
    }

    wp_send_json(['success' => true, 'html' => $html], 200);
}

function save_render_exam_db($wpdb, $user_id, $exam_data_randered_and_created, $exam_code, $keyquestions, $teacher_id)
{
    $table = 'exam_users_data_result';
    $data = [
        'user_id' => $user_id,
        'exam_data' => json_encode($exam_data_randered_and_created),
        'exam_code' => $exam_code,
        'teacher_id' => $teacher_id,
        'key_questions' => json_encode($keyquestions)
    ];
    $format = ['%d', '%s', '%s', '%d', '%s'];
    return $wpdb->insert($table, $data, $format) !== false;
}

function check_exist_exam_user($wpdb, $user_id, $exam_code)
{
    $table = 'exam_users_data_result';
    return $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$table} WHERE user_id = %d AND exam_code = %s", $user_id, $exam_code)) > 0;
}

function update_render_exam_db($wpdb, $user_id, $exam_data_randered_and_created, $exam_code, $keyquestions, $teacher_id)
{
    $table = 'exam_users_data_result';
    $data = [
        'exam_data' => json_encode($exam_data_randered_and_created),
        'teacher_id' => $teacher_id,
        'key_questions' => json_encode($keyquestions)
    ];
    $where = ['user_id' => $user_id, 'exam_code' => $exam_code];
    $format = ['%s', '%d', '%s'];
    return $wpdb->update($table, $data, $where, $format) !== false;
}
