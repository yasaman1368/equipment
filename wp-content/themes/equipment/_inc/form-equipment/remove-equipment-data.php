<?php
add_action('wp_ajax_remove_equipment_data', 'remove_equipment_data');

function remove_equipment_data()
{
  if (empty($_POST['equipment_id'])) {
    wp_send_json_error(['message' => 'شناسه تجهیز ارسال نشده است.']);
    return;
  }

  global $wpdb;
  $equipment_id = sanitize_text_field($_POST['equipment_id']);

  // فقط جدول‌های مرتبط با داده‌های فرم و گردش کار را حذف می‌کنیم
  $table_data = $wpdb->prefix . 'equipment_data';
  $table_workflow = $wpdb->prefix . 'workflow';

  // حذف رکوردهای مرتبط در جداول دیگر
  $wpdb->delete($table_data, ['equipment_id' => $equipment_id]);
  $wpdb->delete($table_workflow, ['equipment_id' => $equipment_id]);

  wp_send_json_success(['message' => 'تجهیز و داده‌های مرتبط با موفقیت حذف شدند.']);
}
