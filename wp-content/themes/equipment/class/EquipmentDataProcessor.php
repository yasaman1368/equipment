<?php
class EquipmentDataProcessor
{
  public static function process($post, $files)
  {

    $uploaded_files = FileUploader::handle($files);
    if (is_wp_error($uploaded_files)) return $uploaded_files;

    $form_data = self::get_form_data($post);
    if (is_wp_error($form_data)) return $form_data;

    if (!empty($uploaded_files)) {
      $form_data = self::merge_uploaded_files($form_data, $uploaded_files);
    }

    $form_id = intval($post['form_id'] ?? 0) !== 0;
    $equipment_id = sanitize_text_field($post['equipment_id']);

    $status = EquipmentSaver::save($equipment_id, $form_data);
    if (is_wp_error($status)) return $status;

    EquipmentFormSaver::save($form_id, $equipment_id, $form_data);

    global $wpdb;
    $action = $post['action_workflow'] ?? '';
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
      return new WP_Error('missing_form_data', 'form_data is missing.');
    }

    $form_data = json_decode(stripslashes($post['form_data']), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
      return new WP_Error('invalid_json', 'Invalid JSON in form_data.');
    }

    return $form_data;
  }

  private static function merge_uploaded_files($form_data, $uploaded_files)
  {
    $result = [];
    foreach ($uploaded_files as $key => $link) {
      $new_key = preg_replace("/^field_/", "", $key);
      $result[$new_key] = $link;
    }
    return $form_data + $result;
  }
}
