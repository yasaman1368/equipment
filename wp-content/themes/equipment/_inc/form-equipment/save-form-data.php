<?php
function save_form_data()
{
    try {
        // Security checks
        if (!is_manager()) {
            wp_send_json_error(['message' => 'شما دسترسی لازم برای این عملیات را ندارید.']);
        }

        // Validate input
        if (empty($_POST['form_data'])) {
            wp_send_json_error(['message' => 'داده‌های فرم دریافت نشد.']);
        }

        // Decode and sanitize input data
        $raw_data = wp_unslash($_POST['form_data']);
        $form_data = json_decode($raw_data, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($form_data)) {
            wp_send_json_error(['message' => 'فرمت داده‌ها نامعتبر است.']);
        }

        // Validate required fields
        if (empty($form_data['form_name'])) {
            wp_send_json_error(['message' => 'نام فرم الزامی است.']);
        }

        if (empty($form_data['locations']) || !is_array($form_data['locations'])) {
            wp_send_json_error(['message' => 'حداقل یک موقعیت مکانی باید انتخاب شود.']);
        }

        global $wpdb;
        $table_name_forms = $wpdb->prefix . 'equipment_forms';
        $table_name_fields = $wpdb->prefix . 'equipment_form_fields';

        // Start transaction
        $wpdb->query('START TRANSACTION');

        $current_time = current_time('mysql');
        $locations = wp_json_encode(array_map('sanitize_text_field', $form_data['locations']));

        // Insert main form
        $inserted = $wpdb->insert(
            $table_name_forms,
            [
                'form_name' => sanitize_text_field($form_data['form_name']),
                'locations' => $locations,
                'created_at' => $current_time,
                'updated_at' => $current_time
            ],
            ['%s', '%s', '%s', '%s']
        );

        if ($inserted === false) {
            throw new Exception('خطا در ذخیره‌سازی فرم اصلی.');
        }

        $form_id = $wpdb->insert_id;

        // First, always add equipment_id field (always required)
        $equipment_field_inserted = $wpdb->insert(
            $table_name_fields,
            [
                'form_id' => $form_id,
                'field_name' => 'equipment_id',
                'field_type' => 'text',
                'options' => wp_json_encode([]),
                'required' => 1, // equipment_id is always required
                'unique_field' => 0,
                'created_at' => $current_time,
                'updated_at' => $current_time
            ],
            ['%d', '%s', '%s', '%s', '%d', '%d', '%s', '%s']
        );

        if ($equipment_field_inserted === false) {
            throw new Exception('خطا در ایجاد فیلد سریال تجهیز.');
        }

        // Then process dynamic fields
        if (!empty($form_data['fields']) && is_array($form_data['fields'])) {
            foreach ($form_data['fields'] as $field) {
                // Skip if field data is invalid
                if (empty($field['field_name']) || empty($field['field_type'])) {
                    continue;
                }

                // Skip equipment_id as it's already added
                if ($field['field_name'] === 'equipment_id' || $field['field_name'] === 'سریال تجهیز') {
                    continue;
                }

                $field_name = sanitize_text_field($field['field_name']);
                $field_type = sanitize_text_field($field['field_type']);
                $options = !empty($field['options']) && is_array($field['options']) ? 
                    array_map('sanitize_text_field', $field['options']) : [];
                
                // Handle required field - default to 0 (not required) if not set
                $required = isset($field['required']) ? (int) $field['required'] : 0;
                
                // Handle unique field - default to 0 (not unique) if not set
                $unique_field = isset($field['unique_field']) ? (int) $field['unique_field'] : 0;

                // Insert field
                $field_inserted = $wpdb->insert(
                    $table_name_fields,
                    [
                        'form_id' => $form_id,
                        'field_name' => $field_name,
                        'field_type' => $field_type,
                        'options' => wp_json_encode($options),
                        'required' => $required,
                        'unique_field' => $unique_field,
                        'created_at' => $current_time,
                        'updated_at' => $current_time
                    ],
                    ['%d', '%s', '%s', '%s', '%d', '%d', '%s', '%s']
                );

                if ($field_inserted === false) {
                    throw new Exception("خطا در ایجاد فیلد: {$field_name}");
                }
            }
        }

        // Commit transaction
        $wpdb->query('COMMIT');

        wp_send_json_success([
            'message' => 'فرم با موفقیت ذخیره شد.',
            'form_id' => $form_id,
            'fields_count' => $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$table_name_fields} WHERE form_id = %d",
                $form_id
            ))
        ]);

    } catch (Exception $e) {
        // Rollback on error
        $wpdb->query('ROLLBACK');
        
        error_log('Save form data error: ' . $e->getMessage());
        
        wp_send_json_error([
            'message' => 'خطا در ذخیره‌سازی فرم.',
            'debug' => WP_DEBUG ? $e->getMessage() : ''
        ]);
    }
}
add_action('wp_ajax_save_form_data', 'save_form_data');

