<?php add_action('wp_ajax_process_equipment_review', 'process_equipment_review');

function process_equipment_review()
{
  $equipment_id = isset($_POST['equipment_id']) ? sanitize_text_field($_POST['equipment_id']) : '';
  $action_workflow = isset($_POST['action_workflow']) ? $_POST['action_workflow'] : '';

  if (empty($equipment_id)) wp_send_json_error(['message' => 'شناسه تجهیز معتبر نیست.'], 400);

  $is_rejected = !empty($action_workflow);

  $action_status = $is_rejected ? 'rejected' : 'approved';
  $message       = $is_rejected ? 'داده‌ها رد شدند' : 'داده‌ها با موفقیت ذخیره شدند';

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
