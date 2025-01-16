<?php
global $wpdb;
$current_user = wp_get_current_user();
$user_roles = $current_user->roles;
$current_user_id = $current_user->ID;

if (!in_array('subscriber', $user_roles) && isset($_GET['classroom_id'])) {
    $classroom_id = intval($_GET['classroom_id']);

    $stmt = $wpdb->get_row($wpdb->prepare("SELECT students_id, students_exams_score, classroom_name FROM classrooms WHERE teacher_id=%d AND id=%d", $current_user_id, $classroom_id));

    if (!$stmt) {
?>
        <div class="alert alert-danger"> اطلاعاتی برای این کلاس ثبت نشده است!!!</div>
        <a

            class="btn btn-success text-center m-auto "
            href="<?php echo site_url('panel/classroomoffice') ?>"
            role="button">بازگشت</a>
    <?php
        return;
    }

    if ($stmt->students_id === '' || $stmt->students_exams_score === '') {
    ?>
        <div class="alert alert-danger"> اطلاعاتی برای این کلاس ثبت نشده است</div>
        <a

            class="btn btn-success text-center m-auto "
            href="<?php echo site_url('panel/classroomoffice') ?>"
            role="button">بازگشت</a>
    <?php
        return;
    }

    $students_id = json_decode($stmt->students_id);
    $students_exam_scores = json_decode($stmt->students_exams_score, true); // Use associative array directly.
    $classroom_name = $stmt->classroom_name ?? '';

    if (empty($students_id) || empty($students_exam_scores)) {
    ?>
        <div class="alert alert-danger"> اطلاعاتی برای این کلاس ثبت نشده است</div>
        <a

            class="btn btn-success text-center m-auto "
            href="<?php echo site_url('panel/classroomoffice') ?>"
            role="button">بازگشت</a>
    <?php

        return; // Handle empty student or score data case.
    }

    // Prepare the HTML output
    ob_start(); // Start output buffering
    ?>
    <h6 class="p-2">کلاس <?php echo $classroom_name ?></h6>
    <div class="table-responsive text-center">
        <table class="table table-striped-columns table-hover table-borderless table-primary align-middle">
            <thead class="table-light">
                <tr>
                    <th><i class="bi bi-trash"></i></th>
                    <th>#</th>
                    <th>نام و نام خانوادگی</th>
                    <?php foreach (array_keys($students_exam_scores) as $exams): ?>
                        <th><?php echo esc_html($exams); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody class="table-group-divider">
                <?php
                $counter = 1;
                foreach ($students_id as $student): ?>
                    <tr class="table-primary">
                        <td width='40px'><i class="bi bi-person-x remove-student-from-class" data-bs-toggle="tooltip"
                                data-bs-placement="top"
                                title="حذف از کلاس" data-student-id="<?php echo $student ?>" data-classroom-id="<?php echo $classroom_id ?>"></i></td>
                        <td scope="row" width='40px'><?php echo esc_html($counter++); ?></td>
                        <td class="student-name"><?php echo esc_html(user_display_name($student)); ?></td>
                        <?php foreach ($students_exam_scores as $exam => $scores): ?>
                            <td><?php echo esc_html($scores[$student] ?? 'غایب'); ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <input type="hidden" name="ajax-nonce" data-ajax-nonce="<?php echo wp_create_nonce('remove-student') ?>">
    <input type="hidden" name="ajax-url" data-ajax-url="<?php echo admin_url('admin-ajax.php')  ?>">
    <a

        class="btn btn-success text-center m-auto "
        href="<?php echo site_url('panel/classroomoffice') ?>"
        role="button">بازگشت</a>


<?php
    echo ob_get_clean(); // Output the buffered content

} else {

?>
    <div class="row justify-content-center">
        <div class="bg-light rounded add-new-classroom card-classroom">
            <i class="bi bi-plus d-block text-info text-center fs-1" data-bs-toggle="modal" data-bs-target="#modalId"></i>
            <div class="text-center text-muted">اضافه کردن کلاس</div>
        </div>
        <?php
        $classrooms = $wpdb->get_results($wpdb->prepare("SELECT classroom_name, id FROM classrooms WHERE teacher_id=%d", $current_user_id));

        if ($classrooms) {
            foreach ($classrooms as $classroom) {
                list($classroom_name, $school_name) = explode('|', $classroom->classroom_name);
        ?>
                <div class="bg-light rounded card-classroom position-relative">
                    <span class="trash-classroom position-absolute "> <i class=" remove-classroom text-body-tertiary fw-light bi bi-trash" data-classroom-id="<?php echo esc_attr($classroom->id); ?>" data-classroom-name="<?php echo esc_attr($classroom_name); ?>"></i> <i class="edit-classroom text-body-tertiary fw-light bi bi-pen" data-bs-toggle="modal" data-bs-target="#modalId-edit"></i></span>
                    <div class="mb-3 text-center">
                        <div class="m-2 bg-white p-2 rounded text-success fw-bold">کلاس <span class="classroom-name"><?php echo esc_html($classroom_name); ?></span></div>
                        <div class="m-2 bg-white p-2 rounded text-success fw-bold">مدرسه <span class="school-name"><?php echo esc_html($school_name); ?></span></div>
                    </div>
                    <input type="button" class="d-block btn btn-info m-auto mt-2 classroom-show" data-classroom-id="<?php echo esc_attr($classroom->id); ?>" value="نمایش کلاس">
                </div>
            <?php
            }
        } else {
            ?>
            <div class="bg-light rounded card-classroom">
                <div class="mb-3 text-center">
                    <div class="alert alert-info">کلاسی برای شما ثبت نشده است</div>
                </div>
            </div>
        <?php } ?>
    </div>

    <!-- Modal Body add new class -->
    <div class="modal fade" id="modalId" tabindex="-1" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">افزودن کلاس جدید</h5>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="schoolName" class="form-label">نام مدرسه</label>
                        <input type="text" class="form-control" name="schoolName" id="schoolName" aria-describedby="helpId" autocomplete="off" />
                    </div>
                    <div class="mb-3">
                        <label for="classroom-name" class="form-label">نام کلاس</label>
                        <input type="text" class="form-control" name="classroom-name" id="classroom-name" aria-describedby="helpId" autocomplete="off" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">خروج</button>
                    <button type="button" class="btn btn-primary" id="add-new-classroom">ثبت</button>
                    <input type="hidden" name="ajax-nonce" value="<?php echo esc_attr(wp_create_nonce('create-classroom')); ?>">
                    <input type="hidden" name="ajax-url" value="<?php echo esc_url(admin_url('admin-ajax.php')); ?>">
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Body  edit  classroom name -->
    <div class="modal fade" id="modalId-edit" tabindex="-1" role="dialog" aria-labelledby="modalTitleId-edit" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId-edit">ویرایش کلاس </h5>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="schoolName" class="form-label">نام مدرسه</label>
                        <input type="text" class="form-control" name="schoolName" id="schoolName-edit" aria-describedby="helpId" autocomplete="off" />
                    </div>
                    <div class="mb-3">
                        <label for="classroom-name" class="form-label">نام کلاس</label>
                        <input type="text" class="form-control" name="classroom-name" id="classroom-name-edit" aria-describedby="helpId" autocomplete="off" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">خروج</button>
                    <button type="button" class="btn btn-primary" id="edit-classroom-btn" data-classroom-id="">ثبت</button>
                </div>
            </div>
        </div>
    </div>
<?php } ?>