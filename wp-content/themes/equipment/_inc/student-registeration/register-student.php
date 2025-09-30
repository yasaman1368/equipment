
<?php
add_action('wp_ajax_register_student', 'register_student');
add_action('wp_ajax_nopriv_register_student', 'register_student');

function register_student()
{
  global $wpdb;
  $table_name = 'student_registration';

  $student_name = sanitize_text_field($_POST['student_name']);
  $class_name   = sanitize_text_field($_POST['class_name']);
  $class_time   = sanitize_text_field($_POST['class_time']);
  $class_days   = sanitize_text_field($_POST['class_days']);

  if (empty($student_name) || empty($class_name) || empty($class_time)) {
    wp_send_json(['success' => false, 'message' => 'همه فیلدها الزامی هستند']);
  }

  $inserted = $wpdb->insert($table_name, [
    'student_name' => $student_name,
    'class_name'   => $class_name,
    'class_time'   => $class_time,
    'class_days'   => $class_days,
  ]);

  if ($inserted) {
    wp_send_json(['success' => true, 'message' => 'ثبت‌نام با موفقیت انجام شد']);
  } else {
    wp_send_json(['success' => false, 'message' => 'خطا در ثبت اطلاعات']);
  }
}
