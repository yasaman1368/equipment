<?php
add_action('wp_ajax_save_equipment_data', 'save_equipment_data');
function save_equipment_data()
{
  $result = EquipmentDataProcessor::process($_POST, $_FILES);
  if (is_wp_error($result)) {
    wp_send_json_error(['message' => $result->get_error_message()]);
  } else {
    wp_send_json_success($result);
  }
}
