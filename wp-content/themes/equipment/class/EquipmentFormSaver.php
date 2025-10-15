<?php
class EquipmentFormSaver
{
  public static function save($form_id, $equipment_id, $form_data)
  {
    global $wpdb;
    $table_name = $wpdb->prefix . 'equipment_data';
    $existing = $wpdb->get_results($wpdb->prepare(
      "SELECT * FROM $table_name WHERE equipment_id = %s",
      $equipment_id
    ));

    $current_time = current_time('mysql');

    if (!empty($existing)) {
      foreach ($form_data as $field_id => $value) {
        $existing_value = $wpdb->get_var($wpdb->prepare(
          "SELECT value FROM $table_name WHERE equipment_id = %s AND field_id = %d",
          $equipment_id,
          $field_id
        ));

        if ($existing_value !== $value) {
          $wpdb->update($table_name, ['value' => $value], [
            'equipment_id' => $equipment_id,
            'field_id' => $field_id
          ]);
        }
      }
    } else {
      foreach ($form_data as $field_id => $value) {
        $wpdb->insert($table_name, [
          'form_id' => $form_id,
          'equipment_id' => $equipment_id,
          'field_id' => $field_id,
          'value' => $value,
          'created_at' => $current_time,
          'updated_at' => $current_time
        ]);
      }
    }
  }
}
