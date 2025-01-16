<?php
if (!isset($_GET['user_id']) || !isset($_GET['exam_code'])) {
    echo '<div class="alert alert-danger"> رکوردی ثبت نشده است </div>';
    return;
}
global $wpdb;
$user_id = intval($_GET['user_id']);
$exam_code = intval($_GET['exam_code']);
$exam_results = $wpdb->get_row($wpdb->prepare("SELECT * FROM exam_users_data_result WHERE user_id=%d AND exam_code=%d", $user_id, $exam_code));
$user = get_user_by('ID', $user_id);

if (!$exam_results) {
    echo '<div class="alert alert-danger"> رکوردی ثبت نشده است </div>';
    return;
}

$teacher_id = intval($exam_results->teacher_id);
$current_user_id = get_current_user_id();
if ($user_id !== $current_user_id && $teacher_id !== $current_user_id) {
    echo '<div class="alert alert-danger"> رکوردی برای شما نمایش نمی دهد </div>';
    return;
}

$exam_data = json_decode($exam_results->exam_data);
$renderd_exam = get_object_vars($exam_data->renderd_exam);
$key_questions = json_decode($exam_results->key_questions, true);
$exam_answers = json_decode($exam_results->exam_answer, true);
$count_questions = count($key_questions);
$question_ids = array_keys($key_questions);
$questions = get_posts(['post__in' => $question_ids, 'post_type' => 'test', 'posts_per_page' => -1]);
$questions_map = array_column($questions, 'post_content', 'ID');

$i = 0;
?>
<div class="analysis-section bg-light p-3 rounded">
    <h2 class="text-center my-2 p-2">برگه آزمون </h2>
    <div class="exam-name text-center row g-1">

        <span class="col-sm-6 col-md-3 bg-info p-2 bg-gradient  shadow-sm    rounded">نام و نام خانوادگی : <span class="fw-bold"><?php echo   $user->display_name; ?></span></span>
        <span class="col-sm-6 col-md-3 bg-info p-2 bg-gradient  shadow-sm    rounded">کد آزمون : <span class="fw-bold"><?php echo $exam_code ?></span></span>
        <span class="col-sm-6 col-md-3 bg-info p-2 bg-gradient  shadow-sm    rounded">نام آزمون : <span class="fw-bold"><?php echo json_decode(($exam_data->created_exam)->exam_data)->exam_name ?></span></span>
        <span class="col-sm-6 col-md-3 bg-info p-2 bg-gradient  shadow-sm    rounded">تعداد سوالات : <span class="fw-bold"><?php echo $count_questions; ?></span></span>
        <span class="col-12  bg-info p-2 bg-gradient  shadow-sm    rounded">امتیاز : <span class="fw-bold "><span class="text-success px-2 "><?php echo $exam_results->score; ?> </span> از <?php echo $count_questions; ?></span></span>
    </div>
    <?php foreach ($key_questions as $question_id => $key_question): ?>
        <?php
        $question_id = intval($question_id);
        $key_question = intval($key_question);
        $option = intval($exam_answers[$question_id] ?? 0);
        ?>
        <div class="question mt-4">
            <div class="question-text p-3">
                <div class="fs-6">
                    <span><?php echo $i + 1 ?>:</span>
                    <?php echo $questions_map[$question_id] ?? ''; ?>
                    <span class="px-3 rounded mx-1 d-inline-block <?php echo ($option === $key_question) ? 'text-success' : 'text-danger'; ?>">
                        <?php echo ($option === 0 ? 'بدون پاسخ ' : ''); ?>


                    </span>
                </div>
            </div>
            <?php $c = 0; ?>
            <?php foreach ($renderd_exam[$question_id] as $option_value): ?>
                <div class="option px-3 py-1  ">
                    <?php echo $renderd_exam[$question_id][$c]; ?>
                    <?php if ($key_question - 1 === $c && $option !== $key_question): ?>
                        <span class=" your-answer"><i class=" bi bi-check-lg text-success"></i></span>
                    <?php elseif ($key_question - 1 === $c && $option === $key_question): ?>
                        <span class=" your-answer"><i class=" bi bi-check-all text-success"></i></span>
                    <?php elseif ($option - 1 === $c): ?>
                        <span class=" your-answer"><i class=" bi bi-x-lg text-danger"></i></span>
                    <?php endif; ?>
                </div>
                <?php $c++; ?>
            <?php endforeach; ?>
            <hr>
        </div>
        <?php $i++; ?>
    <?php endforeach; ?>
    <a href="<?php echo home_url('panel/report') ?>">
        <button type="button" class="btn btn-primary m-4">بازگشت</button>
    </a>
</div>