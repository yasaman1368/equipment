<?php

function remove_equipment_data()
{
    if (!isset($_POST['serial_number'])) {
        wp_send_json_error(array('message' => 'serial_number is missing in the request.'));
        return;
    }

    $serial_number = intval($_POST['serial_number']);

    global $wpdb;
    $table_name_equipments = $wpdb->prefix . 'equipments';
    $table_name_equipment_data = $wpdb->prefix . 'equipment_data';

    $wpdb->delete($table_name_equipments, ['id' => $serial_number]);

    $wpdb->delete($table_name_equipment_data, ['equipment_id' => $serial_number]);

    wp_send_json_success(array('message' => 'تجهیز با موفقیت حذف شد'));
}

add_action('wp_ajax_remove_equipment_data', 'remove_equipment_data');
