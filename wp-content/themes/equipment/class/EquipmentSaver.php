<?php

class EquipmentSaver
{
  public static function save($equipment_id, $form_data)
  {
    global $wpdb;
    $table_name = $wpdb->prefix . 'equipments';
    $fields_table = $wpdb->prefix . 'equipment_form_fields';

    $equipment_data = [];

    foreach ($form_data as $field_id => $value) {
      $field_name = $wpdb->get_var($wpdb->prepare(
        "SELECT field_name FROM $fields_table WHERE id = %d",
        $field_id
      ));
      if ($field_name) {
        $equipment_data[$field_name] = $value;
      }
    }

    $existing = $wpdb->get_row($wpdb->prepare(
      "SELECT * FROM $table_name WHERE equipment_id = %d",
      $equipment_id
    ));

    if ($existing) {
      if (empty($equipment_data)) return true;
      $wpdb->update($table_name, $equipment_data, ['equipment_id' => $equipment_id]);
    } else {
      $wpdb->insert($table_name, array_merge(['equipment_id' => $equipment_id], $equipment_data));
    }

    return true;
  }
}
