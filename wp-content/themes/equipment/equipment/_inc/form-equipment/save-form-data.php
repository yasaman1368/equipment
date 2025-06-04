<?php
function save_form_data()
{
    // Decode and sanitize input data
    $form_data = json_decode(stripslashes($_POST['form_data']), true);
    global $wpdb;
    $table_name_forms = $wpdb->prefix . 'equipment_forms';
    $table_name_fields = $wpdb->prefix . 'equipment_form_fields';
    $table_name_equipments = $wpdb->prefix . 'equipments';
    // Store current time once
    $current_time = current_time('mysql');
    // Save form
    $inserted = $wpdb->insert(
        $table_name_forms,
        array(
            'form_name' => sanitize_text_field($form_data['form_name']),
            'created_at' => $current_time,
            'updated_at' => $current_time
        )
    );
    if ($inserted === false) {
        wp_send_json_error(array('message' => 'Failed to save form.'));
        return;
    }
    $form_id = $wpdb->insert_id;
    // Check if columns need to be added
    $existing_columns = $wpdb->get_col("SHOW COLUMNS FROM `$table_name_equipments`");
    $new_columns = [];
    foreach ($form_data['fields'] as $field) {
        $field_name = $field['field_name'] !== 'سریال تجهیز' ? sanitize_text_field($field['field_name']) : 'equipment_serial';
        $new_columns[] = $field_name;
        $wpdb->insert(
            $table_name_fields,
            array(
                'form_id' => $form_id,
                'field_name' => $field_name,
                'field_type' => sanitize_text_field($field['field_type']),
                'options' => json_encode($field['options']),
                'created_at' => $current_time,
                'updated_at' => $current_time
            )
        );
    }

    // Add new columns to the equipments table if they do not exist
    foreach ($new_columns as $new_column) {
        if (!in_array($new_column, $existing_columns)) {
            $stmt = $wpdb->query("ALTER TABLE `$table_name_equipments` ADD `$new_column` VARCHAR(255) NOT NULL");
        }
    }
    wp_send_json_success(array('message' => 'فرم با موفقیت ذخیره شد.', 'form_id' => $form_id));
}
add_action('wp_ajax_save_form_data', 'save_form_data');
