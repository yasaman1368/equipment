<?php
function get_equipment_data()
{
    $serial_number = sanitize_text_field($_POST['serial_number']);

    global $wpdb;
    $table_name_data = $wpdb->prefix . 'equipment_data';
    $table_name_fields = $wpdb->prefix . 'equipment_form_fields';
    // جستجوی اطلاعات تجهیز بر اساس سریال
    $equipment_data = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name_data WHERE equipment_id = %s", $serial_number), ARRAY_A);

    $data = [];
    foreach ($equipment_data as $item) {
        $field_id = $item["field_id"];
        $field_value = $item["value"];
        $field = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name_fields WHERE id = %d", $field_id), ARRAY_A);


        $data[] = array(
            "id" => $field_id,
            "field_name" => $field[0]["field_name"],
            "field_type" => $field[0]["field_type"],
            "options" => $field[0]["options"],
            "value" => $field_value
        );
    }

    if (!empty($equipment_data)) {
        wp_send_json_success(array('message' => 'اطلاعات تجهیز موجود است.', 'data' => $data, 'status' => true));
    } else {
        wp_send_json_success(array('message' => 'اطلاعات تجهیز موجود نیست.', 'status' => false));
    }
}
add_action('wp_ajax_get_equipment_data', 'get_equipment_data');
