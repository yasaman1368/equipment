<?php
function handle_workflow($equipment_id)
{
  $equipment_status = get_status($equipment_id);
  $current_status = set_current_status($equipment_status);
  echo '<pre>';
  var_dump($equipment_status);
  echo '</pre>';
  echo '</br>';
}

function get_status($equipment_id)
{
  global $wpdb;
  $table_name = 'workflow';
  $sql = $wpdb->prepare("SELECT current_status FROM $table_name WHERE equipment_id=%s", $equipment_id);
  $equipment_status = $wpdb->get_var($sql);
  return $equipment_status;
}

function set_current_status($equipment_status)
{
  $role = get_user_meta(get_current_user_id(), "_role", true);
  // 'Pending','SupervisorApproved','SupervisorReject','FinalApprove','Reject'
  if ($role === "user" && $equipment_status === null) {
    $current_status = "Pending";
  }
  if ($role === "supervisor") {
    if ($equipment_status === 'Pending') {
      $current_status = 'SupervisorApproved';
    }
  }
  if ($role === "manager") {
    if ($equipment_status === 'SupervisorApproved') {
      $current_status = 'FinalApprove';
    }
  }

  // $current_status = $equipment_status;
  // if ($role == 'admin') {
  //   $current_status = 'ready';
  // } elseif ($role == 'user') {
  //   $current_status = 'in_progress';
  // }
  return $current_status;
}
