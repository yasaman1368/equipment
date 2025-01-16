<?php
global $wpdb;
$user = wp_get_current_user();
$user_roles = $user->roles;
$user_id = $user->ID;
$table_name = 'exam_users_data_result';
$exams_data = $wpdb->get_results("SELECT * FROM $table_name WHERE user_id = $user_id AND status = 1");

if (!$exams_data && in_array('subscriber', $user_roles)) {
?>
    <div class="alert alert-warning text-center fw-bolder">هیچ آزمونی برای شما ثبت نشده است.</div>

<?php
    return;
}
?>
<?php
if (in_array('subscriber', $user_roles)) {
?>
    <h3>نتایج آزمون</h3>
    <div
        class="table-responsive rounded">
        <table
            class="table table-striped table-hover text-center table-borderless table-info align-middle rounded">
            <thead class="table-light">
                <tr>
                    <th>ردیف</th>
                    <th>نام آزمون</th>
                    <th>کد آزمون</th>
                    <th> نام درس - پایه</th>
                    <th>نتیجه آزمون</th>
                    <th>تحلیل آزمون</th>
                </tr>
            </thead>
            <tbody class="table-group-divider">

                <?php
                $i = 0;
                foreach ($exams_data as $exam): ?>
                    <?php

                    $count_quetions = count((array)json_decode($exam->key_questions));
                    $exam_code = $exams_data[$i]->exam_code ?? '';
                    $created_exam_data = created_exam_data($exam);
                    $exam_name = $created_exam_data->exam_name ?? '';
                    $lesson = $created_exam_data->lesson ?? '';
                    $base = $created_exam_data->base_education ?? "";
                    $score = $exam->score ?? '';
                    $date_finished_exam = $exam->finished ?? '';
                    ?>
                    <tr
                        class="table-info">
                        <td scope="row"><?php echo $i + 1; ?></td>
                        <td><?php echo $exam_name; ?></td>
                        <td><?php echo $exam_code ?></td>
                        <td><?php echo $lesson . ' - ' . $base ?></td>
                        <td>
                            <span><?php echo $score; ?></span> از <span><?php echo $count_quetions ?></span>
                        </td>
                        <td><button
                                type="button"
                                class="btn btn-outline-success analysisExam"
                                data-exam-code="<?php echo $exam->exam_code ?>"
                                data-user-id="<?php echo $user_id ?>">
                                برگه آزمون
                            </button>
                        </td>
                    </tr>
                <?php
                    $i++;
                endforeach
                ?>

            </tbody>

        </table>
    </div>
<?php } else {
?>
    <div
        class="table-responsive ">
        <?php
        global $wpdb;
        $table = 'created_exam_data';
        $user_id = get_current_user_id();
        $stmt = $wpdb->get_results($wpdb->prepare("SELECT exam_code, exam_data FROM {$table} WHERE user_id='%d'", $user_id));
        $translate_name_lesson = [
            'math' => 'ریاضی',
            'science' => 'علوم',
        ];
        if (!$stmt) {
        ?>
            <div class="alert alert-warning">شما آزمون فعالی ندارید</div>
        <?php
            return;
        }
        ?>
        <table
            class="table table-striped  text-center table-hover table-borderless table-info align-middle">
            <thead class="table-light">

                <tr>
                    <th scope="col">ردیف</th>
                    <th scope="col">نام آزمون</th>
                    <th scope="col">پایه</th>
                    <th scope="col">کد آزمون</th>
                    <th scope="col">نام کتاب</th>
                    <th scope="col">نمرات کلاس</th>
                    <th scope="col">تحلیل </th>

                </tr>
            </thead>

            <tbody class="table-group-divider">
                <?php
                $i = 0;
                $exam_active = true;
                foreach ($stmt as $row): $i++;
                    $exam_data = json_decode($row->exam_data);
                ?>
                    <tr
                        class="table-secondry">
                        <td scope="row"><?php echo $i ?></td>
                        <td><?php echo esc_html($exam_data->exam_name == '' ? 'بدون نام' : $exam_data->exam_name) ?></td>
                        <td><?php echo esc_html($exam_data->base_education ?? '-') ?></td>
                        <td><?php echo esc_html($row->exam_code ?? '-') ?></td>
                        <td><?php echo esc_html($translate_name_lesson[$exam_data->lesson ?? '-'] ?? '-') ?></td>
                        <td>
                            <button
                                type="button"
                                class="btn btn-outline-warning  show-score-btn" data-exam-code="<?php echo esc_html($row->exam_code) ?>">
                                نمایش نمرات
                            </button>
                        </td>

                        <td><button
                                type="button"
                                class="btn btn-outline-primary"
                                id="analysisAllResults"
                                data-exam-code="<?php echo esc_html($row->exam_code) ?>">
                                تحلیل آزمون
                            </button>
                        </td>

                    </tr>
                <?php endforeach ?>

            </tbody>
            <tfoot>

            </tfoot>
        </table>
    </div>
    <div class=" container-modal-show-scores h-100 w-100 text-center">
        <div class="modal-content-detail h-100">
            <span class="colse-square ">
                <i class="bi bi-x-square-fill "></i>
            </span>
            <div class="modal-center-absoult h-100">

            </div>
        </div>
    </div>
    <input type="hidden" name="ajax-nonce" value="<?php echo wp_create_nonce('creat-exam') ?>">
    <input type="hidden" name="ajax-url" value="<?php echo admin_url('admin-ajax.php') ?>">
<?php
}

function created_exam_data($exams_data)
{
    $created_exam_data = json_decode($exams_data->exam_data);
    $created_exam_data = $created_exam_data->created_exam;
    $created_exam_data = json_decode($created_exam_data->exam_data);
    return $created_exam_data;
}
?>
<!-- Modal trigger button -->


<!-- Modal Body -->
<!-- if you want to close by clicking outside the modal, delete the last endpoint:data-bs-backdrop and data-bs-keyboard -->
<div
    class="modal fade"
    id="modalId"
    tabindex="-1"
    data-bs-backdrop="true"
    data-bs-keyboard="true"

    role="dialog"
    aria-labelledby="modalTitleId"
    aria-hidden="true">
    <div
        class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-sm"
        role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleId">
                    کلاس مورد نظر را انتخاب کنید </h5>

            </div>
            <div class="modal-body">
                <?php
                $classrooms = $wpdb->get_results($wpdb->prepare("SELECT id, classroom_name  FROM classrooms WHERE teacher_id=%d ", $user_id));
                $i = 1;
                foreach ($classrooms as $classroom):
                    $classroom_name = $classroom->classroom_name;

                ?>
                    <div class="form-check">
                        <input class="form-check-input" data-classroom-id="<?php echo $classroom->id ?>" type="radio" name="classroom" id="classroom-<?php echo $i ?>" />
                        <label class="form-check-label" for="classroom-<?php echo $i ?>"><?php echo $classroom_name ?> </label>
                    </div>
                <?php endforeach; ?>

            </div>
            <div class="modal-footer">
                <button
                    type="button"
                    class="btn btn-danger"
                    data-bs-dismiss="modal">
                    خروج
                </button>
                <button type="button" class="btn btn-primary add-student-to-classroom" data-bs-dismiss="modal" data-student-id="5">افزودن</button>
            </div>
        </div>
    </div>
</div>