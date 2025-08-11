<?php add_action('wp_ajax_approve_equipment_data', 'approve_equipment_data');

function approve_equipment_data()
{
  $equipment_id = intval($_POST['equipment_id']);
  global $wpdb;
  $workflow = new WorkflowManager($wpdb);
  $workflow_result = $workflow->handle($equipment_id, 'approved');

  if (is_wp_error($workflow_result)) {
    wp_send_json_error(['message' => $workflow_result->get_error_message()], 403);
  }

  wp_send_json_success([
    'message' => 'داده‌ها با موفقیت ذخیره شدند',
  ], 200);
}
