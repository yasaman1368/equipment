<?php

/**
 * ================================
 *  CLASS: EquipmentDataProcessor
 * ================================
 */
class EquipmentDataProcessor
{
  public static function process($post, $files)
  {
    // 📂 آپلود فایل‌ها
    $uploaded_files = FileUploader::handle($files);
    if (is_wp_error($uploaded_files)) return $uploaded_files;

    // 🧩 گرفتن داده‌های فرم
    $form_data = self::get_form_data($post);
    if (is_wp_error($form_data)) return $form_data;

    // 📎 ادغام فایل‌ها با داده‌های فرم
    if (!empty($uploaded_files)) {
      $form_data = self::merge_uploaded_files($form_data, $uploaded_files);
    }

    $form_id = intval($post['form_id'] ?? 0);
    $equipment_id = sanitize_text_field($post['equipment_id'] ?? '');

    if (empty($equipment_id)) {
      return new WP_Error('missing_equipment_id', 'شناسه تجهیز ارسال نشده است.');
    }

    // ✅ ذخیره در جدول داده‌ها
    EquipmentFormSaver::save($form_id, $equipment_id, $form_data);

    // ⚙️ به‌روزرسانی گردش کار
    global $wpdb;
    $action = $post['action_workflow'] ?? null;
    $workflow = new WorkflowManager($wpdb, $action);
    $workflow_result = $workflow->handle($equipment_id, 'approved');

    if (is_wp_error($workflow_result)) return $workflow_result;

    return [
      'message' => 'داده‌ها با موفقیت ذخیره شدند',
      'workflow' => $workflow
    ];
  }

  private static function get_form_data($post)
  {
    if (!isset($post['form_data'])) {
      return new WP_Error('missing_form_data', 'form_data ارسال نشده است.');
    }

    $form_data = json_decode(stripslashes($post['form_data']), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
      return new WP_Error('invalid_json', 'ساختار JSON داده‌های فرم نامعتبر است.');
    }

    return $form_data;
  }

  private static function merge_uploaded_files($form_data, $uploaded_files)
  {
    foreach ($uploaded_files as $key => $link) {
      $new_key = preg_replace("/^field_/", "", $key);
      $form_data[$new_key] = $link;
    }
    return $form_data;
  }
}
