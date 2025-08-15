<?php add_action('wp_ajax_approve_equipment_data', 'approve_equipment_data');

function approve_equipment_data()
{
  $equipment_id = sanitize_text_field($_POST['equipment_id']);
  $action_workflow = $_POST['action_workflow'] ?? null;
  $message='داده‌ها با موفقیت ذخیره شدند';
  if ($action_workflow) {
    $action_status = 'rejected';
    $message = 'داده‌ها رد شدند';
  }

  global $wpdb;
  $workflow = new WorkflowManager($wpdb, $action_workflow);
  $workflow_result = $workflow->handle($equipment_id, $action_status);

  if (is_wp_error($workflow_result)) {
    wp_send_json_error(['message' => $workflow_result->get_error_message()], 403);
  }

  wp_send_json_success([
    'message' => $message,
  ], 200);
}
