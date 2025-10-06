<?php
// ✅ آمار کل ثبت‌نام‌ها
add_action('wp_ajax_get_class_stats', 'get_class_stats');
add_action('wp_ajax_nopriv_get_class_stats', 'get_class_stats');
function get_class_stats() {
  global $wpdb;
  $table ='student_registration';

  $total = (int) $wpdb->get_var("SELECT COUNT(*) FROM $table");
  wp_send_json_success(['total' => $total]);
}

// ✅ آمار تفکیکی هر کلاس
add_action('wp_ajax_get_class_details', 'get_class_details');
add_action('wp_ajax_nopriv_get_class_details', 'get_class_details');
function get_class_details() {
  global $wpdb;
  $table = 'student_registration';
  $class_id = sanitize_text_field($_GET['class_id']);

  // نام فارسی برای هر کلاس
  $class_map = [
    '7' => 'هفتم',
    '8' => 'هشتم',
    '9' => 'نهم'
  ];

  $total = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE class_name = %s", $class_id));
  $even = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE class_name = %s AND class_days = 'even'", $class_id));
  $odd = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE class_name = %s AND class_days = 'odd'", $class_id));
  $time1 = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE class_name = %s AND class_time = '1'", $class_id));
  $time2 = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE class_name = %s AND class_time = '2'", $class_id));
  $time3 = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE class_name = %s AND class_time = '3'", $class_id));

  wp_send_json_success([
    'class_name' => $class_map[$class_id] ?? '',
    'total' => $total,
    'even_days' => $even,
    'odd_days' => $odd,
    'time_1' => $time1,
    'time_2' => $time2,
    'time_3' => $time3,
   
  ]);
}
