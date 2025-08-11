<?php
class WorkflowDB
{
  private $wpdb;
  private $table = "workflow";
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
    $data = [
      'equipment_id' => $equipment_id,
      'current_status' => $status,
      'active_role' => $role,
      $role . '_id' => $user_id
    ];

    $exists = $this->wpdb->get_var(
      $this->wpdb->prepare("SELECT COUNT(*) FROM $this->table WHERE equipment_id = %s", $equipment_id)
    );

    if ($exists) {
      $proccess_history = $this->getProccessHistory($equipment_id);

      $proccess_history[] = $data;
      $data['proccess_history'] = json_encode($proccess_history);
      return $this->wpdb->update($this->table, $data, ['equipment_id' => $equipment_id]) !== false;
    } else {
      $proccess_history = json_encode($data);
      $data = [...$data, 'proccess_history' => $proccess_history];

      return $this->wpdb->insert($this->table, $data) !== false;
    }
  }
}
