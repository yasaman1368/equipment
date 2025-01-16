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
                <th scope="col">نمایش آزمون</th>
                <th scope="col">وضعیت آزمون</th>
                <th scope="col">حذف آزمون</th>
            </tr>
        </thead>

        <tbody class="table-group-divider">
            <?php
            $i = 0;

            foreach ($stmt as $row): $i++;
                $exam_data = json_decode($row->exam_data);
            ?>
                <tr
                    class="table-secondry">
                    <?php
                    $examName = esc_html($exam_data->exam_name ?: 'بدون نام');
                    $baseEducation = esc_html($exam_data->base_education ?? '');
                    $examCode = esc_html($row->exam_code ?? '');
                    $lessonName = esc_html($translate_name_lesson[$exam_data->lesson ?? ''] ?? '');

                    echo "<td scope='row'>{$i}</td>";
                    echo "<td>{$examName}</td>";
                    echo "<td>{$baseEducation}</td>";
                    echo "<td>{$examCode}</td>";
                    echo "<td>{$lessonName}</td>";
                    ?>

                    <?php $exam_active = is_exam_active($wpdb, $row->exam_code) ?>
                    <td><button
                            type="button"
                            class="btn btn-outline-primary show-exam"
                            data-exam-code="<?php echo esc_html($row->exam_code) ?>">
                            نمایش
                        </button>
                    </td>
                    <td class="status-exam-button">

                        <?php if (intval($exam_active) === 1): ?>
                            <button
                                type="button"
                                class="btn btn-primary status-active"
                                data-exam-code="<?php echo esc_html($row->exam_code) ?>"
                                data-status="1">

                                فعال
                            </button>
                        <?php else : ?>
                            <button
                                type="button"
                                class="btn btn-warning status-active"
                                data-status="0"
                                data-exam-code="<?php echo esc_html($row->exam_code) ?>">
                                غیر فعال
                            </button>
                        <?php endif ?>
                    </td>
                    <td>
                        <button
                            type="button"
                            class="btn btn-danger remove-exam"

                            data-exam-code="<?php echo esc_html($row->exam_code) ?>">
                            حذف
                        </button>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>
<div class="container-modal-show-scores h-100 w-100 text-center">
    <div class="modal-content-detail h-100">
        <span class="colse-square ">
            <i class="bi bi-x-square-fill "></i>
        </span>
        <div class="modal-center-absoult h-100">

        </div>
    </div>
</div>