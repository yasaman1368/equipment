<?php

function remove_equipment_data()
{
  if (!isset($_POST['equipment_id'])) {
    wp_send_json_error(array('message' => 'equipment_id is missing in the request.'));
    return;
  }

  $equipment_id = sanitize_text_field($_POST['equipment_id']);

  global $wpdb;
  $table_name_equipments = $wpdb->prefix . 'equipments';
  $wpdb->delete($table_name_equipments, ['equipment_id' => $equipment_id]);

  wp_send_json_success(array('message' => 'تجهیز با موفقیت حذف شد'));
}

add_action('wp_ajax_remove_equipment_data', 'remove_equipment_data');
