<?php
add_action('wp_ajax_show_students_scores', 'show_students_scores');
function show_students_scores()
{

    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'creat-exam')) {
        wp_send_json(['msg' => 'access denied'], 403);
    }

    global $wpdb;
    $table = 'exam_users_data_result';
    $exam_code = intval($_POST['examCode']);


    $results = $wpdb->get_results($wpdb->prepare("SELECT user_id, score FROM {$table} WHERE exam_code = %d AND status = 1", $exam_code));

    if (!$results) {
        wp_send_json([
            'error' => true,
            'message' => 'نتیجه ای پیدا نشد'
        ], 403);
    }

    ob_start();
?>
    <div class="table-responsive rounded">
        <table
            class="table table-striped table-hover table-borderless table-primary align-middle rounded">
            <thead class="table-info">
                <tr>
                    <th>ردیف</th>
                    <th>نام دانش آموز</th>
                    <th>نمره</th>
                    <th>برگه آزمون</th>
                    <th>نام کلاس</th>
                </tr>
            </thead>
            <tbody class="table-group-divider">

                <?php
                $i = 0;
                foreach ($results as $result) {
                    $i++;
                    $student_id = $result->user_id;
                    $student = get_user_by('ID', $student_id);

                ?>
                    <tr class="table-primary">
                        <td scope="row"><?php echo $i ?></td>
                        <td><?php
                            echo $student->display_name
                            ?></td>
                        <td><?php
                            echo $result->score;
                            ?></td>
                        <td><button
                                type="button"
                                class="btn btn-outline-success analysisExam"
                                data-exam-code="<?php echo $exam_code ?>"
                                data-user-id="<?php echo $student_id ?>">
                                نمایش
                            </button>
                        </td>
                        <td>
                            <?php
                            $classrooms_id = get_user_meta($student_id, '_classroom_id', true);

                            if ('' !== $classrooms_id) {
                                $classrooms_id = json_decode($classrooms_id, true);
                            }

                            $classroom_id = $classrooms_id[get_current_user_id()];
                            if (!$classroom_id) {
                            ?>
                                <button
                                    type="button"
                                    class="btn btn-outline-success add-to-classroom"
                                    data-exam-code="<?php echo $exam_code ?>"
                                    data-user-id-btn="<?php echo $student_id ?>"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalId">
                                    <i class="bi bi-plus"></i>افزودن به کلاس </button>
                            <?php

                            } else {
                                $classroom_name = $wpdb->get_var($wpdb->prepare("SELECT classroom_name FROM classrooms WHERE id=%d", $classroom_id));
                                if ($classroom_name) {
                                    echo $classroom_name;
                                } else {
                                    echo 'کلاسی ثبت نشده';
                                    delete_user_meta($student_id, '_classrooms_id');
                                }
                            }
                            ?>

                        </td>
                    </tr>
                <?php
                } ?>
            </tbody>
        </table>
    </div>


<?php
    $html = ob_get_clean();
    wp_send_json([
        'success' => true,
        'html' => $html
    ], 200);
}
