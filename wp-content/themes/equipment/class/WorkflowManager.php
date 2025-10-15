<?php
class WorkflowManager
{
  private $db;
  private $current_user_id;
  private $current_user_role;
  public function __construct($wpdb, $action_data)
  {
    $this->db = new WorkflowDB($wpdb);
    $this->current_user_id = get_current_user_id();
    $this->current_user_role = get_user_meta($this->current_user_id, '_role', true);
    if ($action_data) {
      $this->db->action_data = json_decode(stripslashes($action_data), true);
    }
  }

  public function handle($equipment_id, $action)
  {
    $current_status = $this->db->getCurrentStatus($equipment_id);
    if (!$current_status) {
      if ($this->current_user_role === 'user') {
        return $this->db->saveWorkflow($equipment_id, 'Pending', 'user', $this->current_user_id)
          ? true
          : new WP_Error('db_error', 'بروز رسانی گردش کار با خطا مواجه شد');
      }
      if ($this->current_user_role === 'supervisor') {
        return $this->db->saveWorkflow($equipment_id, 'SupervisorApproved', 'supervisor', $this->current_user_id)
          ? true
          : new WP_Error('db_error', 'بروز رسانی گردش کار با خطا مواجه شد');
      }
      if ($this->current_user_role === 'manager') {
    
        return $this->db->saveWorkflow($equipment_id, 'FinalApprove', 'manager', $this->current_user_id)
          ? true
          : new WP_Error('db_error', 'بروز رسانی گردش کار با خطا مواجه شد');
      }
    }

    $next_status = WorkflowStatus::getNextStatus($current_status, $action);
    if (!$next_status) {
      return new WP_Error('invalid_transition', 'Invalid status transition');
    }

    $next_role = WorkflowRole::getNextRole($current_status);

    return $this->db->saveWorkflow($equipment_id, $next_status, $next_role, $this->current_user_id)
      ? true
      : new WP_Error('db_error', 'بروز رسانی گردش کار با خطا مواجه شد');
  }
}
