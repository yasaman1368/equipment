<?php
function get_status($equipment_id) {
  global $wpdb;
  $table = 'workflow';
  return $wpdb->get_var(
    $wpdb->prepare("SELECT current_status FROM $table WHERE equipment_id = %s", $equipment_id)
  );
}

function handle_workflow_table($equipment_id, $status, $role, $user_id) {
  global $wpdb;
  $table = 'workflow';

  $data = [
    'equipment_id' => $equipment_id,
    'current_status' => $status,
    'active_role' => $role,
    $role . '_id' => $user_id,
  ];

  $exists = $wpdb->get_var(
    $wpdb->prepare("SELECT COUNT(*) FROM $table WHERE equipment_id = %s", $equipment_id)
  );

  if ($exists) {
    return $wpdb->update($table, $data, ['equipment_id' => $equipment_id]) !== false;
  } else {
    return $wpdb->insert($table, $data) !== false;
  }
}
