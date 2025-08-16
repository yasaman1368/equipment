<?php
class WorkflowDB
{
  private $wpdb;
  private $table = "workflow";
  public  $action_data = null;
  public function __construct($wpdb)
  {
    $this->wpdb = $wpdb;
  }

  public function getCurrentStatus($equipment_id)
  {
    return $this->wpdb->get_var(
      $this->wpdb->prepare("SELECT current_status FROM $this->table WHERE equipment_id = %s", $equipment_id)
    );
  }

  public function getProccessHistory($equipment_id)
  {
    $proccess_history = $this->wpdb->get_var(
      $this->wpdb->prepare("SELECT proccess_history FROM $this->table WHERE equipment_id = %s", $equipment_id)
    );

    if (!$proccess_history) return null;

    return json_decode($proccess_history, true);
  }

  public function saveWorkflow($equipment_id, $status, $role, $user_id)
  {
    $current_data = [
      'equipment_id' => $equipment_id,
      'current_status' => $status,
      'active_role' => $role,
      $role . '_id' => $user_id
    ];

    $exists = $this->wpdb->get_var(
      $this->wpdb->prepare("SELECT COUNT(*) FROM $this->table WHERE equipment_id = %s", $equipment_id)
    );

    if ($exists) {

      $prev_proccess_history = $this->getProccessHistory($equipment_id);
      $new_data = $current_data;
      if ($this->action_data) {
        $new_data["action"] = $this->action_data;
      }

      $prev_proccess_history[] = $new_data;

      $data = [...$current_data, 'proccess_history' => json_encode($prev_proccess_history)];
      return $this->wpdb->update($this->table, $data, ['equipment_id' => $equipment_id]) !== false;
    } else {
      $proccess_history = [$current_data];
      $data = [...$current_data, 'proccess_history' => json_encode($proccess_history)];

      return $this->wpdb->insert($this->table, $data) !== false;
    }
  }
}
