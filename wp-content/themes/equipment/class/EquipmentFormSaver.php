<?php
/**
 * ================================
 *  CLASS: EquipmentFormSaver
 * ================================
 */
class EquipmentFormSaver
{
  public static function save($form_id, $equipment_id, $form_data)
  {
    global $wpdb;
    $table_name = $wpdb->prefix . 'equipment_data';
    $current_time = current_time('mysql');

    foreach ($form_data as $field_id => $value) {
      // بررسی وجود رکورد قبلی
      $existing = $wpdb->get_var($wpdb->prepare(
        "SELECT id FROM $table_name WHERE equipment_id = %s AND field_id = %d",
        $equipment_id,
        $field_id
      ));

      if ($existing) {
        $wpdb->update(
          $table_name,
          ['value' => $value, 'updated_at' => $current_time],
          ['equipment_id' => $equipment_id, 'field_id' => $field_id]
        );
      } else {
        $wpdb->insert(
          $table_name,
          [
            'form_id' => $form_id,
            'equipment_id' => $equipment_id,
            'field_id' => $field_id,
            'value' => $value,
            'created_at' => $current_time,
            'updated_at' => $current_time
          ]
        );
      }
    }
  }
}
