<?php
if (!isset($_GET['exam_code'])) {
    return;
}

$exam_code = intval($_GET['exam_code']);
global $wpdb;
$table = 'analysis_exam_data';
$analysisExamData = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE exam_code= %d", $exam_code));

if (!$analysisExamData) {
    echo '<div class="alert alert-info">تحلیلی برای این آزمون وجود ندارد</div>';
    return;
}

$teacher_id = intval($analysisExamData->teacher_id);
$current_user_id = get_current_user_id();

if ($teacher_id !== $current_user_id) {
    echo '<div class="alert alert-danger">دیدن تحلیل برای شما مجاز نیست!!!</div>';
    $header = ['Content-Type:text/html;charset=UTF-8'];
    $request_uri = $_SERVER['REQUEST_URI'];
    $message = "کاربر با آیدی:$current_user_id در حال فصولی در قسمت:$request_uri است.";
    wp_mail('alipor.hossain@gmail.com', 'فضولی', $message, $header);
    return;
}

$number_test_participants = intval($analysisExamData->number_test_participants);
$scores = json_decode($analysisExamData->scores, true) ?? []; // Decode once and provide default
$exam_count_true_answer = json_decode($analysisExamData->exam_count_true_answer, true) ?? [];
$questionsId = array_keys($exam_count_true_answer);
$questions = get_posts(['post__in' => $questionsId, 'post_type' => 'test', 'posts_per_page' => -1]);
$questions_map = array_column($questions, 'post_content', 'ID');

$average_score = $number_test_participants > 0 ? round(array_sum($scores) / count($scores), 2) : 0;
$max_score = !empty($scores) ? max($scores) : 0;
$min_score = !empty($scores) ? min($scores) : 0;

?>

<div class="row bg-light rounded p-2">
    <h3>تحلیل کلاس</h3>
    <div class="table-responsive-sm rounded">
        <table class="table table-striped table-hover table-borderless table-primary align-middle text-center rounded">
            <thead class="table-light">
                <tr class="table-primary">
                    <th>تعداد شرکت کنندگان:</th>
                    <th>میانگین نمرات:</th>
                    <th>بالاترین نمره:</th>
                    <th>پایین ترین نمره:</th>
                </tr>
            </thead>
            <tbody class="table-group-divider">
                <tr class="table">
                    <td scope="row"><?php echo $number_test_participants; ?></td>
                    <td><?php echo $average_score; ?></td>
                    <td><?php echo $max_score; ?></td>
                    <td><?php echo $min_score; ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <h3>تحلیل سوالات</h3>
    <div class="table-responsive-sm rounded">
        <table class="table table-striped table-hover table-borderless table-primary align-middle text-center rounded">
            <thead class="table-light">
                <tr class="table-primary">
                    <th>#</th>
                    <th>متن سوال</th>
                    <th>تعداد پاسخ درست</th>
                    <th>تعداد پاسخ نادرست</th>
                    <th>درصد پاسخ صحیح</th>
                </tr>
            </thead>
            <tbody class="table-group-divider">
                <?php
                $i = 1;
                foreach ($exam_count_true_answer as $questionId => $number_true_answer) {
                    $percent = $number_test_participants > 0 ? round(($number_true_answer / $number_test_participants) * 100) : 0;
                    $class = 'bg-danger';
                    if ($percent >= 60) {
                        $class = 'bg-success';
                    } elseif ($percent >= 40) {
                        $class = 'bg-warning';
                    }
                ?>
                    <tr class="table">
                        <td scope="row"><?php echo $i; ?></td>
                        <td><?php echo $questions_map[$questionId] ?? ''; ?></td>
                        <td><?php echo $number_true_answer; ?></td>
                        <td><?php echo $number_test_participants - $number_true_answer; ?></td>
                        <td class="<?php echo $class; ?> text-white"><?php echo $percent; ?><span>%</span></td>
                    </tr>
                <?php $i++;
                } ?>
            </tbody>
        </table>
    </div>
</div>