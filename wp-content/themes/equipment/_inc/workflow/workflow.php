<?php
function handle_workflow(string $equipment_id, string $status = 'approved')
{
  $equipment_status = get_status($equipment_id);
  $current_user_id = get_current_user_id();
  $currnet_user_role = get_user_meta($current_user_id, '_role', true);
  if (!$equipment_status || $currnet_user_role === 'user') {
    $current_equipment_status = 'Pending';
    $active_role = 'user';

    $result = handle_workflow_table($equipment_id, $current_equipment_status, $active_role, $current_user_id);
    return $result ? true : new WP_Error('db_error', 'Failed to create initial workflow record');
  }

  $current_equipment_status = set_current_equipment_status($equipment_status, $status);
  if (!$current_equipment_status) {
    return new WP_Error('invalid_transition', 'Invalid status transition');
  }

  $active_role = set_active_role($current_equipment_status);

  $result = handle_workflow_table($equipment_id, $current_equipment_status, $active_role, $current_user_id);
  return $result ? true : new WP_Error('db_error', 'Failed to update workflow record');
}

function get_status($equipment_id)
{
  global $wpdb;
  $table_name = 'workflow';
  $sql = $wpdb->prepare("SELECT current_status FROM $table_name WHERE equipment_id=%s", $equipment_id);

  return $wpdb->get_var($sql);
}


function set_current_equipment_status($equipment_status, $status)
{
  $current_equipment_status = null;

  if ($equipment_status === 'Pending' && $status === "approved") {
    $current_equipment_status = 'SupervisorApproved';
  } else if ($equipment_status === "Pending" && $status === "rejected") {
    $current_equipment_status = 'SupervisorReject';
  } else if ($equipment_status === 'Supervisorapproved' && $status === "approved") {
    $current_equipment_status = 'FinalApprove';
  } else if ($equipment_status === 'Supervisorapproved' && $status === "rejected") {
    $current_equipment_status = 'ManagerReject';
  }

  return $current_equipment_status;
}


function set_active_role($current_equipment_status)
{
  switch ($current_equipment_status) {
    case 'Pending':
      return 'supervisor';
    case 'SupervisorApproved':
      return 'manager';
    case 'SupervisorReject':
      return 'user';
    case 'FinalApprove':
      return 'manager';
    case 'ManagerReject':
      return 'supervisor';
    default:
      return 'user'; // Default fallback
  }
}


function   handle_workflow_table($equipment_id, $current_equipment_status, $active_role, $current_user_id)
{
  global $wpdb;
  $table_name = 'workflow';
  $role = '';
  $exists = $wpdb->get_var(
    $wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE equipment_id = %s", $equipment_id)
  );

  $data = [
    'equipment_id' => $equipment_id,
    'current_status' => $current_equipment_status,
    'active_role' => $active_role,
    $active_role . '_id' => $current_user_id,
  ];

  if ($exists) {
    $where = ['equipment_id' => $equipment_id];
    $result = $wpdb->update($table_name, $data, $where);
    return $result !== false;
  } else {
    $result = $wpdb->insert($table_name, $data);
    return $result !== false;
  }
}
