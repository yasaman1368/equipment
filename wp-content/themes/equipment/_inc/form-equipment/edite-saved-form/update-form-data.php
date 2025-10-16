<?php
// اضافه کردن action برای آپدیت فرم

/**
 * Update form data - Fixed equipment_id duplication issue
 */
function update_form_data() {
    try {
        if (!is_manager()) {
            wp_send_json_error(['message' => 'شما دسترسی لازم برای این عملیات را ندارید.']);
        }

        if (empty($_POST['form_data'])) {
            wp_send_json_error(['message' => 'داده‌های فرم دریافت نشد.']);
        }

        $raw = wp_unslash($_POST['form_data']);
        $form_data = json_decode($raw, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($form_data)) {
            wp_send_json_error(['message' => 'فرمت داده‌ها نامعتبر است.']);
        }

        global $wpdb;
        $forms_table = $wpdb->prefix . 'equipment_forms';
        $fields_table = $wpdb->prefix . 'equipment_form_fields';

        $form_id = intval($form_data['form_id'] ?? 0);
        
        $existing_form = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$forms_table} WHERE id = %d",
            $form_id
        ));

        if (!$existing_form) {
            wp_send_json_error(['message' => 'فرم مورد نظر یافت نشد.']);
        }

        $wpdb->query('START TRANSACTION');

        // آپدیت اطلاعات اصلی فرم
        $form_update_data = [
            'form_name' => sanitize_text_field($form_data['form_name'] ?? ''),
            'locations' => wp_json_encode(array_map('sanitize_text_field', $form_data['locations'] ?? [])),
            'updated_at' => current_time('mysql')
        ];

        $updated = $wpdb->update(
            $forms_table,
            $form_update_data,
            ['id' => $form_id],
            ['%s', '%s', '%s'],
            ['%d']
        );

        if ($updated === false) {
            throw new Exception('خطا در بروزرسانی اطلاعات فرم.');
        }

        // مدیریت فیلدها
        $incoming_field_ids = [];

        // دریافت فیلد equipment_id برای محافظت
        $equipment_field_id = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$fields_table} WHERE form_id = %d AND field_name = 'equipment_id'",
            $form_id
        ));

        // پردازش فیلدهای داینامیک
        foreach ($form_data['fields'] as $field) {
            $field_name = sanitize_text_field($field['field_name'] ?? '');
            $field_type = sanitize_text_field($field['field_type'] ?? 'text');
            $field_id = isset($field['field_id']) ? intval($field['field_id']) : 0;
            $options = is_array($field['options'] ?? []) ? 
                array_map('sanitize_text_field', $field['options']) : [];

            // رد کردن فیلدهای خالی
            if (empty($field_name)) {
                continue;
            }

            // اگر فیلد equipment_id است، آن را مدیریت کن
            if ($field_name === 'equipment_id') {
                if ($field_id && $equipment_field_id) {
                    $incoming_field_ids[] = $equipment_field_id;
                    
                    // آپدیت فیلد equipment_id اگر نیاز باشد
                    $update_result = $wpdb->update(
                        $fields_table,
                        [
                            'field_type' => $field_type,
                            'options' => wp_json_encode($options),
                            'updated_at' => current_time('mysql')
                        ],
                        ['id' => $equipment_field_id],
                        ['%s', '%s', '%s'],
                        ['%d']
                    );
                }
                continue;
            }

            if (!empty($field['field_id'])) {
                // آپدیت فیلد موجود
                $field_id = intval($field['field_id']);
                $incoming_field_ids[] = $field_id;

                $update_result = $wpdb->update(
                    $fields_table,
                    [
                        'field_name' => $field_name,
                        'field_type' => $field_type,
                        'options' => wp_json_encode($options),
                        'updated_at' => current_time('mysql')
                    ],
                    ['id' => $field_id],
                    ['%s', '%s', '%s', '%s'],
                    ['%d']
                );

                if ($update_result === false) {
                    throw new Exception("خطا در بروزرسانی فیلد: {$field_name}");
                }
            } else {
                // افزودن فیلد جدید
                $insert_result = $wpdb->insert(
                    $fields_table,
                    [
                        'form_id' => $form_id,
                        'field_name' => $field_name,
                        'field_type' => $field_type,
                        'options' => wp_json_encode($options),
                        'created_at' => current_time('mysql'),
                        'updated_at' => current_time('mysql')
                    ],
                    ['%d', '%s', '%s', '%s', '%s', '%s']
                );

                if (!$insert_result) {
                    throw new Exception("خطا در ایجاد فیلد جدید: {$field_name}");
                }

                $incoming_field_ids[] = $wpdb->insert_id;
            }
        }

        // حتماً equipment_id را به لیست فیلدهای محافظت شده اضافه کن
        if ($equipment_field_id) {
            $incoming_field_ids[] = $equipment_field_id;
        }

        // حذف فیلدهای قدیمی (فقط اگر فیلدی برای حذف وجود دارد)
        if (!empty($incoming_field_ids)) {
            $placeholders = implode(',', array_fill(0, count($incoming_field_ids), '%d'));
            
            $delete_query = $wpdb->prepare(
                "DELETE FROM {$fields_table} WHERE form_id = %d AND id NOT IN ({$placeholders})",
                array_merge([$form_id], $incoming_field_ids)
            );

            $delete_result = $wpdb->query($delete_query);

            if ($delete_result === false) {
                throw new Exception('خطا در حذف فیلدهای قدیمی.');
            }
        } else {
            // اگر هیچ فیلدی نیست، فقط فیلدهای غیر equipment_id را حذف کن
            $delete_result = $wpdb->delete(
                $fields_table,
                [
                    'form_id' => $form_id,
                    'field_name' => 'equipment_id'
                ],
                ['%d', '%s'],
                ['!=']
            );
        }

        $wpdb->query('COMMIT');

        wp_send_json_success([
            'message' => 'فرم با موفقیت بروزرسانی شد.',
            'form_id' => $form_id,
            'updated_fields' => count($incoming_field_ids)
        ]);

    } catch (Exception $e) {
        $wpdb->query('ROLLBACK');
        
        error_log('Form update error: ' . $e->getMessage());
        
        wp_send_json_error([
            'message' => 'خطا در بروزرسانی فرم.',
            'debug' => WP_DEBUG ? $e->getMessage() : ''
        ]);
    }
}

add_action('wp_ajax_update_form_data', 'update_form_data');