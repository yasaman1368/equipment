<?php
global $wpdb;
$user = wp_get_current_user();
$user_roles = $user->roles;
$current_user_id = $user->ID;
$current_user_can = array_keys($user->allcaps);
?>
<div class="container-sm mb-3">
    <?php
    if (in_array('edit_posts', $current_user_can)) {
    ?>
        <div class="row m-3">
            <div class="col-sm-6 bg-info-subtle shadow-sm rounded">
                <div class="p-2 m-2 d-flex flex-column align-items-center ">
                    <span class=" text-success fw-bold">
                        <span>
                            <?php echo $user->display_name; ?>
                            عزیز شما
                        </span> نویسنده پرسنگار هستید
                    </span>
                    <a href="<?php echo admin_url(); ?>" class="text-white btn btn-danger p-2 m-3 text-center">
                        مدیریت نوشته ها
                    </a>
                </div>
            </div>
        </div>
    <?php } ?>
    <div class="row g-3">
        <div class="col-sm-3">
            <a href="<?php echo esc_url(home_url('weekly-exam-schedule')) ?>">
                <div class="card text-center bg-info-subtle shadow-sm">
                    <div class="card-body">
                        <h5 class="text-success card-title">برنامه امتحانات هفتگی</h5>
                        <p class="card-text text-muted fw-lighter"> دبیرستان نمونه دولتی شهدا</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-sm-3">
            <a href="https://porsnegar.ir/2024/12/05/%d8%a7%d8%b9%d9%84%d8%a7%d9%85-%d9%81%d8%b1%d8%a7%d8%ae%d9%88%d8%a7%d9%86-%d9%86%d9%88%db%8c%d8%b3%d9%86%d8%af%da%af%db%8c-%d8%a8%d8%b1%d8%a7%db%8c-%d9%be%d8%b1%d8%b3-%d9%86%da%af%d8%a7%d8%b1/">
                <div class="card text-center bg-info-subtle shadow-sm">
                    <div class="card-body">
                        <h5 class="text-warning card-title text-danger"> جذب نویسنده </h5>
                        <p class="card-text text-muted fw-lighter"> ویژه دانش آموزان</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

<?php
// Check if the user is not a subscriber
if (!in_array('subscriber', $user_roles)) {
    $examStatus = isset($_GET['exam_status']) ? $_GET['exam_status'] : '';
    $homeActive = ($examStatus !== 'active') ? 'active' : '';
    $activeExamsActive = ($examStatus === 'active') ? 'active' : '';
?>
    <div class="row justify-content-center align-items-center g-2 mb-4">
        <div class="col-12">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?php echo esc_attr($homeActive); ?>" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="<?php echo $homeActive ? 'true' : 'false'; ?>">
                        ساخت آزمون
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?php echo esc_attr($activeExamsActive); ?>" id="active-exams-tab" data-bs-toggle="tab" data-bs-target="#active-exams" type="button" role="tab" aria-controls="active-exams" aria-selected="<?php echo $activeExamsActive ? 'true' : 'false'; ?>">
                        وضعیت آزمون ها
                    </button>
                </li>
            </ul>
        </div>
    </div>
    <div class="tab-content">
        <div class="tab-pane <?php echo esc_attr($homeActive . ' show'); ?>" id="home" role="tabpanel" aria-labelledby="home-tab">
            <?php get_template_part('partials/panel/teacher/CreateExamForm'); ?>
        </div>
        <div class="tab-pane <?php echo esc_attr($activeExamsActive . ' show'); ?>" id="active-exams" role="tabpanel" aria-labelledby="active-exams-tab">
            <?php get_template_part('partials/panel/teacher/ActivExamTable'); ?>
        </div>
    </div>
<?php
} elseif (in_array('subscriber', $user_roles)) {
?>
    <div class="container mt-2">
        <div class="row align-items-center g-2">
            <div class="col-12">
                <div class="check-exam bg-light text-center p-3 rounded d-flex align-items-center">
                    <h6 class="text-muted fw-lighter">جستجو آزمون با کد</h6>
                    <div class="input-group my-3 check-exam-code" dir="ltr">
                        <button class="btn btn-outline-secondary" type="button" id="search-exam-button">جستجو آزمون</button>
                        <input type="text" class="form-control" placeholder="کد آزمون را وارد کنید" aria-label="Example text with button addon" aria-describedby="search-exam-button" data-current-user-id="<?php echo esc_attr($current_user_id); ?>" dir="rtl">
                        <input type="hidden" name="ajax-nonce" value="<?php echo esc_attr(wp_create_nonce('creat-exam')); ?>">
                        <input type="hidden" name="ajax-url" value="<?php echo esc_url(admin_url('admin-ajax.php')); ?>">
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-2 g-2 bg-white p-3 rounded">
            <div class="fw-bolder fs-5 mt-2 text-muted bg-warning rounded p-2" id="exams-activ-title">آزمون های فعال شما </div>
            <?php
            $student_classrooms_id = get_user_meta($current_user_id, '_classroom_id', true);
            $i = 0;
            if ($student_classrooms_id !== '') {
                $student_classrooms_id = array_values(json_decode($student_classrooms_id, true));
                foreach ($student_classrooms_id as $student_classroom_id) {
                    // Prepare the query once
                    $active_exams = $wpdb->get_results($wpdb->prepare("SELECT * FROM created_exam_data WHERE classroom_id=%d AND status=%d", $student_classroom_id, 1));
            ?>
                    <?php
                    // Store nonce and ajax URL once
                    $ajax_nonce = wp_create_nonce('creat-exam');
                    $ajax_url = admin_url('admin-ajax.php');
                    foreach ($active_exams as $exam) {
                        $table = 'exam_users_data_result';
                        $finished = $wpdb->get_var($wpdb->prepare("SELECT status FROM {$table} WHERE user_id= %d AND exam_code= %d", $current_user_id, $exam->exam_code));
                        if (intval($finished) === 0) {
                            $i++;
                            $exam_data = json_decode($exam->exam_data);
                            if ($exam_data) {
                                $questionsIdArray = $exam_data->questionsIdArray ?? [];
                                $countQuestions = count($questionsIdArray);
                                $test_time = isset($exam_data->test_time) ? intval($exam_data->test_time) : 'نامعین';
                                $exam_name = isset($exam_data->exam_name) ? sanitize_text_field($exam_data->exam_name) : 'بدون نام';
                                $lesson = isset($exam_data->lesson) ? sanitize_text_field($exam_data->lesson) : 'نامعین';
                                $teacher = get_user_by('ID', $exam->user_id);
                                $teacher_display_name = $teacher->display_name ?? 'نامشخص';
                    ?>
                                <div class="col-md-6">
                                    <div class="container mt-5 bg-light rounded shadow-class-hover">
                                        <div class="row justify-content-center align-items-center text-center pt-2 g-2">
                                            <div class="col-sm-3">
                                                <div class="exam-data-content shadow-sm bg-white p-2 rounded">
                                                    <div class="exam-data-title text-muted fw-lighter p-1">نام آزمون</div>
                                                    <div class="exam-name-text text-success fw-bolder p-1"><?php echo esc_html($exam_name); ?></div>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="exam-data-content shadow-sm bg-white p-2 rounded">
                                                    <div class="exam-data-title text-muted fw-lighter p-1">نام درس</div>
                                                    <div class="exam-name-text text-success fw-bolder p-1"><?php echo esc_html($lesson); ?></div>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="exam-data-content shadow-sm bg-white p-2 rounded">
                                                    <div class="exam-data-title text-muted fw-lighter p-1">تعداد سوال</div>
                                                    <div class="exam-name-text text-success fw-bolder p-1"><?php echo esc_html($countQuestions); ?></div>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="exam-data-content shadow-sm bg-white p-2 rounded">
                                                    <div class="exam-data-title text-muted fw-lighter p-1">وقت آزمون</div>
                                                    <div class="exam-name-text text-success fw-bolder p-1"><?php echo esc_html($test_time); ?> دقیقه</div>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="exam-data-content shadow-sm bg-white p-2 rounded">
                                                    <div class="exam-data-title text-muted fw-lighter p-1">طراح سوال</div>
                                                    <div class="exam-name-text text-success fw-bolder p-1"><?php echo esc_html($teacher_display_name); ?></div>
                                                </div>
                                            </div>
                                            <div class="text-center align-items-center">
                                                <button type="button" class="btn btn-outline-success my-3 render-exam-html" data-user-id="<?php echo esc_attr($current_user_id); ?>" data-exam-data="<?php echo esc_attr($exam->exam_code); ?>">شروع آزمون</button>
                                                <input type="hidden" name="ajax-nonce" value="<?php echo esc_attr($ajax_nonce); ?>">
                                                <input type="hidden" name="ajax-url" value="<?php echo esc_url($ajax_url); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                    <?php
                            }
                        }
                    }
                }
                if ($i === 0) {
                    ?>
                    <div class="alert alert-warning">
                        سیستم هیچ آزمون فعالی را برای شما شناسایی نکرد!!!
                        <br>
                        لطفا اگر کد آزمون دارید وارد کنید
                    </div>
                <?php
                }
            } else {
                ?>
                <div class="alert alert-warning mt-3">
                    شما در هیچ کلاسی عضو نیستید.
                    سیستم هیچ آزمون فعالی را برای شما شناسایی نکرد!!!
                    لطفا اگر کد فعال سازی آزمون دارید وارد کنید
                </div>
            <?php
            }
            ?>
        </div>
    </div>
<?php } ?>
<div class="row mt-3">
    <div class="col-sm-6 col-md-4">
        <?php
        global $wpdb;
        $meta_key = '_score_points';
        $top_scores = $wpdb->get_col($wpdb->prepare(
            "
    SELECT DISTINCT meta_value
     FROM PN_usermeta 
     WHERE meta_key = %s 
     ORDER BY CAST(meta_value AS UNSIGNED) DESC
      LIMIT 10",
            $meta_key
        ));
        if (!empty($top_scores)) {
            $meta_query = array(
                'relation' => 'OR',
            );
            foreach ($top_scores as $score) {
                $meta_query[] = array(
                    'key' => $meta_key,
                    'value' => $score,
                    'compare' => '='
                );
            }
            $args = array(
                'role' => 'subscriber',
                'orderby' => 'meta_value_num',
                'order' => 'DESC',
                'meta_query' => $meta_query
            );
            $user_query = new WP_User_Query($args);
            $subscribers = $user_query->get_results();
            if (!empty($subscribers)) {
        ?>
                <div class="bg-white p-2 rounded mt-2 mx-2">
                    <div class="fs-5 p-2 position-relative">
                        ده امتیاز برتر مدرسه
                        <img class="position-absolute top-ten-img" src="<?php echo get_template_directory_uri() . '/assets/images/TopTen.png' ?>" alt="topten">
                    </div>
                    <div class="table-responsive-sm shadow-sm">
                        <table class="table table-hover table-borderless table-success align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>رتبه</th>
                                    <th>نام و نام خانوادگی</th>
                                    <th>امتیاز</th>
                                </tr>
                            </thead>
                            <tbody class="table-group-divider">
                                <?php
                                $i = 1;
                                foreach ($subscribers as $user) {
                                    if ($i === 11) {
                                        break;
                                    }
                                    $score = get_user_meta($user->ID, '_score_points', true);


                                ?>

                                    <tr class="<?php echo get_current_user_id() === $user->ID ? 'table-warning fw-bold shadow border-2' : 'table-success' ?>">
                                        <td scope="row"><?php echo $i ?></td>
                                        <td><?php echo esc_html($user->display_name) ?></td>
                                        <td><?php echo esc_html($score) ?></td>
                                    </tr>
                                <?php
                                    $i++;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php
            } else {
            ?>
                <div class="alert alert-info">
                    هیچ دانش آموزی برای لیست 10 نمره برتر مدرسه یافت نشد
                </div>
            <?php
            }
        } else {
            ?>
            <div class="alert alert-info">
                10 نمره برتر مدرسه یافت نشد
            </div>

        <?php
        } ?>
    </div>
    <?php

    // Get current user classroom id 
    $classrooms_id = get_user_meta($current_user_id, '_classroom_id', true);
    $i = 0;
    // Get classmates id 
    $All_classmates_id = [];
    if ($classrooms_id !== '') {
        $classrooms_id = array_values(json_decode($classrooms_id, true));
        foreach ($classrooms_id as $classroom_id) {
            $students_id_classroom = $wpdb->get_var($wpdb->prepare("SELECT students_id FROM classrooms WHERE id=%d", $classroom_id));
            $classmates_id = json_decode($students_id_classroom, true);
            $All_classmates_id[$classroom_id] = $classmates_id;
        }
    }
    foreach ($All_classmates_id as $classroom_id => $classmates_id) {
        // Classroom name
        $classroom_name = $wpdb->get_var($wpdb->prepare("SELECT classroom_name FROM classrooms WHERE id=%d", $classroom_id));
        $users_score = [];
        foreach ($classmates_id as $user_id) {
            $scores = get_user_meta($user_id, '_score_points', true);
            $users_score[$user_id] = $scores;
        }
        arsort($users_score);
    ?>
        <div class="col-sm-6 col-md-4">
            <div class="bg-white p-2 rounded mt-2 mx-2">
                <div class="fs-5 p-2 position-relative text-center">
                    ده امتیاز برتر کلاس
                    <br>
                    <span class="text-success"><?php echo esc_html($classroom_name) ?></span>
                    <img class="position-absolute top-ten-img" src="<?php echo get_template_directory_uri() . '/assets/images/TopTen.png' ?>" alt="topten">
                </div>
                <div class="table-responsive-sm shadow-sm">
                    <table class="table table-hover table-borderless table-success align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>رتبه</th>
                                <th>نام و نام خانوادگی</th>
                                <th>امتیاز</th>
                            </tr>
                        </thead>
                        <tbody class="table-group-divider">
                            <?php
                            $i = 1;
                            foreach ($users_score as $user_id => $score) {
                                if ($i === 11) {
                                    break;
                                }
                                $user = get_userdata($user_id);
                            ?>
                                <tr class="<?php echo get_current_user_id() === $user->ID ? 'table-warning fw-bold shadow border-2' : 'table-success' ?>">
                                    <td scope=" row"><?php echo $i ?></td>
                                    <td><?php echo esc_html($user->display_name) ?></td>
                                    <td><?php echo esc_html($score) ?></td>
                                </tr>
                            <?php
                                $i++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php
    }
