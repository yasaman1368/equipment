<?php

function update_form_data()
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
        if (empty($form_data['form_id'])) {
            wp_send_json_error(['message' => 'شناسه فرم الزامی است.']);
        }

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
        $form_id = intval($form_data['form_id']);
        $locations = wp_json_encode(array_map('sanitize_text_field', $form_data['locations']));

        // Update main form
        $updated = $wpdb->update(
            $table_name_forms,
            [
                'form_name' => sanitize_text_field($form_data['form_name']),
                'locations' => $locations,
                'updated_at' => $current_time
            ],
            ['id' => $form_id],
            ['%s', '%s', '%s'],
            ['%d']
        );

        if ($updated === false) {
            throw new Exception('خطا در بروزرسانی فرم اصلی.');
        }

        // Process fields - update existing and add new ones
        if (!empty($form_data['fields']) && is_array($form_data['fields'])) {
            foreach ($form_data['fields'] as $field) {
                // Skip if field data is invalid
                if (empty($field['field_name']) || empty($field['field_type'])) {
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

                // Check if field already exists (has field_id)
                if (!empty($field['field_id'])) {
                    $field_id = intval($field['field_id']);
                    
                    // Update existing field
                    $field_updated = $wpdb->update(
                        $table_name_fields,
                        [
                            'field_name' => $field_name,
                            'field_type' => $field_type,
                            'options' => wp_json_encode($options),
                            'required' => $required,
                            'unique_field' => $unique_field,
                            'updated_at' => $current_time
                        ],
                        [
                            'id' => $field_id,
                            'form_id' => $form_id
                        ],
                        ['%s', '%s', '%s', '%d', '%d', '%s'],
                        ['%d', '%d']
                    );

                    if ($field_updated === false) {
                        throw new Exception("خطا در بروزرسانی فیلد: {$field_name}");
                    }
                } else {
                    // Insert new field (skip equipment_id as it should already exist)
                    if ($field_name !== 'equipment_id' && $field_name !== 'سریال تجهیز') {
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
                            throw new Exception("خطا در ایجاد فیلد جدید: {$field_name}");
                        }
                    }
                }
            }
        }

        // Commit transaction
        $wpdb->query('COMMIT');

        wp_send_json_success([
            'message' => 'فرم با موفقیت بروزرسانی شد.',
            'form_id' => $form_id,
            'fields_count' => $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$table_name_fields} WHERE form_id = %d",
                $form_id
            ))
        ]);

    } catch (Exception $e) {
        // Rollback on error
        $wpdb->query('ROLLBACK');
        
        error_log('Update form data error: ' . $e->getMessage());
        
        wp_send_json_error([
            'message' => 'خطا در بروزرسانی فرم.',
            'debug' => WP_DEBUG ? $e->getMessage() : ''
        ]);
    }
}
add_action('wp_ajax_update_form_data', 'update_form_data');