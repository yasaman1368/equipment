<?php
include_once '_inc/register-assets/register-assets.php';
include_once '_inc/utilities/user.php';
include_once '_inc/register-login-users/login.php';
include_once '_inc/register-login-users/register.php';
include_once '_inc/register-login-users/sendSMS.php';
include_once '_inc/test-custom-post-taxonomy/test-custom-post-taxonomy.php';
include_once '_inc/add-meta-boxes-test/add-meta-box-question-options.php';
include_once '_inc/add-meta-boxes-test/add-meta-box-question-level.php';
include_once '_inc/exam/exam-score.php';
include_once '_inc/exam/creator-exam.php';
include_once '_inc/exam/save_exam_db.php';
include_once '_inc/exam/search_exam_by_code.php';
include_once '_inc/exam/render_html_exam_by_code.php';
include_once '_inc/exam/get_sections_cat.php';
include_once '_inc/exam-result/show_students_scores.php';
include_once '_inc/mail/SendMail.php';
include_once '_inc/register-login-users/edit_user_data.php';
include_once '_inc/register-login-users/recover_password.php';
include_once '_inc/register-login-users/set_recover_password.php';
include_once '_inc/exam/show-created-exam.php';
include_once '_inc/exam/remove-exam.php';
include_once '_inc/exam/change_status_exam_active.php';
include_once '_inc/classroom/creator-classroom.php';
include_once '_inc/classroom/add_student_to_classroom.php';
include_once '_inc/exam/save_quetion_creat_custom_exam.php';
include_once '_inc/classroom/remove_classroom.php';
include_once '_inc/classroom/edit_classroom_name.php';
include_once '_inc/classroom/remove_student_from_classroom.php';
include_once '_inc/weekly-exam-schedule/load-exam-schedule.php';
include_once '_inc/weekly-exam-schedule/save-exam-data-schedule.php';
include_once '_inc/weekly-exam-schedule/update_exam_schedule.php';
include_once '_inc/contact/contact-us.php';
include_once '_inc/helper-class/MailLayout.php';
include_once '_inc/utilities/custom-exerpt-post.php';
include_once '_inc/comment-theme/comment-theme.php';
include_once '_inc/students-writer/students-writer.php';
include_once '_inc/utilities/thumbnail-image-defualt.php';
include_once '_inc/pagination/porsnegar_pagination.php';
add_action('after_setup_theme', 'theme_setup_f');
function theme_setup_f()
{
    add_theme_support('post-thumbnails');
    add_image_size('mysize', 200, 200, ['center', 'center']);
}
// global $wpdb;

// // Fetch user IDs from the database
// $ids = $wpdb->get_results($wpdb->prepare("SELECT user_id FROM exam_users_data_result"), ARRAY_A);

// // Initialize an array to hold all exam user IDs
// $all_exam_users_id = [];

// // Populate the array with user IDs
// foreach ($ids as $id) {
//     $all_exam_users_id[] = $id['user_id'];
// }



// // Count the occurrences of each user ID
// $all_exam_users_id_count_values = array_count_values($all_exam_users_id);

// // Initialize an array to hold IDs of cheating users
// $cheating_user_id = [];

// // Identify users who have taken more than 2 exams
// foreach ($all_exam_users_id_count_values as $id => $num) {
//     if ($num > 2) {
//         $cheating_user_id[] = $id;
//     }
// }

// // Optionally, you can update another option with the cheating user IDs
// update_option('_cheating_user_ids', $cheating_user_id);
// foreach ($cheating_user_id as $id) {
//     update_user_meta($id, '_score_points', 0);
// }
