<?php
function update_form_field()
{
  $field_id = intval($_POST['field_id']);
  $field_data = json_decode(stripslashes($_POST['field_data']), true);

  // اعتبارسنجی داده‌ها
  if (!$field_id || !$field_data) {
    wp_send_json_error(['message' => 'داده‌های نامعتبر']);
    return;
  }

  global $wpdb;
  $table_name = $wpdb->prefix . 'equipment_form_fields';

  // بررسی وجود فیلد
  $existing_field = $wpdb->get_var($wpdb->prepare(
    "SELECT id FROM {$table_name} WHERE id = %d",
    $field_id
  ));

  if (!$existing_field) {
    wp_send_json_error(['message' => 'فیلد مورد نظر یافت نشد']);
    return;
  }

  $update_data = [
    'field_name' => sanitize_text_field($field_data['field_name']),
    'field_type' => sanitize_text_field($field_data['field_type']),
    'options' => json_encode($field_data['options']),
    'required' => $field_data['required'] ? 1 : 0,
    'unique_field' => $field_data['unique'] ? 1 : 0,
    'updated_at' => current_time('mysql')
  ];

  $result = $wpdb->update(
    $table_name,
    $update_data,
    ['id' => $field_id],
    ['%s', '%s', '%s', '%d', '%d', '%s'],
    ['%d']
  );

  if ($result !== false) {
    wp_send_json_success(['message' => 'فیلد با موفقیت به‌روزرسانی شد']);
  } else {
    wp_send_json_error(['message' => 'خطا در به‌روزرسانی فیلد']);
  }
}
add_action('wp_ajax_update_form_field', 'update_form_field');
