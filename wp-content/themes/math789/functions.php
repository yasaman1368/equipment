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
include_once '_inc/fetch-student-report/fetch-student-report.php';
add_action('after_setup_theme', 'theme_setup_f');
function theme_setup_f()
{
    add_theme_support('post-thumbnails');
    add_image_size('mysize', 200, 200, ['center', 'center']);
}
// function handle_db_students_grades()
// {
//     global $wpdb;
//     // Prepare the SQL statement to prevent SQL injection
//     $class_name = 'نهم 2';
//     $query = $wpdb->prepare("SELECT * FROM `numbers_of_students_db` WHERE class_name = %s", $class_name);

//     // Execute the query and handle errors
//     $all_results = $wpdb->get_results($query);
//     if ($all_results === null) {
//         error_log('Database query failed: ' . $wpdb->last_error);
//         echo 'An error occurred while retrieving data. Please try again later.';
//         return; // Exit the function early on error
//     }
//     // Array to hold student averages and ratings
//     $students_data = [];
//     // Loop through each student and calculate the average score
//     foreach ($all_results as $student) {
//         // Collect grades
//         $grades = [
//             $student->{'قرآن'},
//             $student->{'پیام های آسمان'}, // Fixed: Accessing property with space
//             $student->{'فارسی'},
//             $student->{'املای فارسی'},
//             $student->{'نگارش'},
//             $student->{'عربی'},
//             $student->{'زبان خارجه'},
//             $student->{'علوم تجربی'},
//             $student->{'ریاضی'},
//             $student->{'تربیت بدنی'},
//             $student->{'مطالعات اجتماعی'},
//             $student->{'فرهنگ و هنر'},
//             $student->{'کار و فناوری'},
//             $student->{'تفکر و سبک زندگی'},
//             $student->{'آمادگی دفاعی'},
//             $student->{'انضباط'}
//         ];
//         // Check if any grade is 'غ'
//         if (in_array('غ', $grades)) {
//             continue; // Skip this student if any grade is 'غ'
//         }
//         // Filter out non-numeric values
//         $valid_grades = array_filter($grades, function ($grade) {
//             return is_numeric($grade);
//         });
//         // Calculate average score
//         if (count($valid_grades) > 0) {
//             $average_score = array_sum($valid_grades) / count($valid_grades);
//         } else {
//             $average_score = 0; // No valid grades
//         }
//         // Store student data
//         $students_data[] = [
//             'id' => $student->{'id'},
//             'average_score' => $average_score,
//             'first_name' => $student->{'first_name'},
//             'last_name' => $student->{'last_nam'}
//         ];
//     }
//     // Sort students by average score in descending order
//     usort($students_data, function ($a, $b) {
//         return $b['average_score'] <=> $a['average_score'];
//     });
//     // Assign rates based on ranking
//     foreach ($students_data as $index => &$student) {
//         $student['rate'] = $index + 1; // Rate starts from 1 for the highest average
//         // Update the database with the average score and rate
//         $wpdb->update(
//             'numbers_of_students_db',
//             [
//                 'avrage_score' => $student['average_score'],
//                 'rate' => $student['rate']
//             ],
//             ['id' => $student['id']]
//         );
//     }
//     // Output the results (optional)
//     echo '<pre>' . print_r($students_data, true) . '</pre>';
// }
// handle_db_students_grades();
