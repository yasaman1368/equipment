<?php
add_action("wp_ajax_get_process_history", "get_process_history");
function get_process_history()
{
  $equipment_id = sanitize_text_field($_GET["equipment_id"]);

  global $wpdb;
  $query = $wpdb->prepare("SELECT proccess_history FROM workflow WHERE equipment_id=%s ", $equipment_id);
  $result = $wpdb->get_var($query);

  if ($result === null) {
    wp_send_json_error(['message' => 'بارگیری تاریخچه با خطا مواجه شد.'], 403);
  }
  wp_send_json_success(['data' => $result], 200);
}
