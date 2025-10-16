<?php

function remove_form_field() {
    try {
        // Security checks
        if (!is_manager()) {
            wp_send_json_error(['message' => 'شما دسترسی لازم برای این عملیات را ندارید.']);
        }

        $field_id = intval($_POST['field_id'] ?? 0);
        if (!$field_id) {
            wp_send_json_error(['message' => 'شناسه فیلد نامعتبر است.']);
        }

        global $wpdb;
        $fields_table = $wpdb->prefix . 'equipment_form_fields';
        $equipments_table = $wpdb->prefix . 'equipments';

        // ابتدا اطلاعات فیلد را بگیریم
        $field_info = $wpdb->get_row($wpdb->prepare(
            "SELECT field_name, form_id FROM {$fields_table} WHERE id = %d",
            $field_id
        ));

        if (!$field_info) {
            wp_send_json_error(['message' => 'فیلد مورد نظر یافت نشد.']);
        }

        // اگر فیلد equipment_id باشد، اجازه حذف ندهیم
        if ($field_info->field_name === 'equipment_id') {
            wp_send_json_error(['message' => 'امکان حذف فیلد سریال تجهیز وجود ندارد.']);
        }

        // شروع تراکنش برای حفظ یکپارچگی داده‌ها
        $wpdb->query('START TRANSACTION');

        // حذف فیلد از جدول فیلدها
        $delete_result = $wpdb->delete(
            $fields_table,
            ['id' => $field_id],
            ['%d']
        );

        if ($delete_result === false) {
            throw new Exception('خطا در حذف فیلد از پایگاه داده.');
        }

        // اگر ستون مربوطه در جدول تجهیزات وجود دارد، آن را هم حذف کنیم
        $column_name = $field_info->field_name;
        $existing_columns = $wpdb->get_col("SHOW COLUMNS FROM `$equipments_table`");
        
        if (in_array($column_name, $existing_columns)) {
            $alter_result = $wpdb->query(
                $wpdb->prepare("ALTER TABLE `$equipments_table` DROP COLUMN `%s`", $column_name)
            );
            
            if ($alter_result === false) {
                throw new Exception("خطا در حذف ستون از جدول تجهیزات: {$column_name}");
            }
        }

        // کامیت تراکنش
        $wpdb->query('COMMIT');

        wp_send_json_success([
            'message' => 'فیلد با موفقیت حذف شد.',
            'field_id' => $field_id,
            'field_name' => $field_info->field_name
        ]);

    } catch (Exception $e) {
        // رول‌بک در صورت خطا
        $wpdb->query('ROLLBACK');
        
        error_log('Remove form field error: ' . $e->getMessage());
        
        wp_send_json_error([
            'message' => 'خطا در حذف فیلد.',
            'debug' => WP_DEBUG ? $e->getMessage() : ''
        ]);
    }
}

// اضافه کردن action hook
add_action('wp_ajax_remove_form_field', 'remove_form_field');


