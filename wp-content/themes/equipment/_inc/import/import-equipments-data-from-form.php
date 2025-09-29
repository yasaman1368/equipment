<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

add_action('wp_ajax_import_equipments_data_from_form', 'import_equipments_data_from_form');

function import_equipments_data_from_form() {
    global $wpdb;

    // بررسی فایل آپلود شده
    if (empty($_FILES['excel_file']['tmp_name'])) {
        wp_send_json(['success' => false, 'message' => 'فایل ارسال نشد']);
    }

    $form_id = isset($_POST['form_id']) ? intval($_POST['form_id']) : 0;
    if (!$form_id) {
        wp_send_json(['success' => false, 'message' => 'فرم مشخص نشده است']);
    }

    $file_tmp = $_FILES['excel_file']['tmp_name'];

    try {
        $spreadsheet = IOFactory::load($file_tmp);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        // بررسی وجود داده
        if (count($rows) < 2) {
            wp_send_json(['success' => false, 'message' => 'داده‌ای برای وارد کردن وجود ندارد']);
        }

        // ستون‌های اکسل (ردیف اول)
        $columns = array_values($rows[1]);

        // بازیابی فیلدهای فرم از دیتابیس
        $form_fields_data = $wpdb->get_results(
            $wpdb->prepare("SELECT id, field_name FROM PN_equipment_form_fields WHERE form_id=%d ORDER BY id ASC", $form_id),
            ARRAY_A
        );

        if (empty($form_fields_data)) {
            wp_send_json(['success' => false, 'message' => 'هیچ فیلدی برای این فرم پیدا نشد']);
        }

        // پردازش ردیف‌ها
        foreach ($rows as $index => $row) {
            if ($index === 1) continue; // ردیف هدر

            $row_values = array_values($row);
            $equipment_id = $row_values[0] ?? null;

            if (!$equipment_id) continue;

            $form_data = prepare_form_data($row_values, $form_fields_data);

            EquipmentSaver::save($equipment_id, $form_data);
            EquipmentFormSaver::save($form_id, $equipment_id, $form_data);
        }

        wp_send_json_success(['message' => 'داده‌ها با موفقیت وارد شدند']);
    } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
        wp_send_json(['success' => false, 'message' => 'خطا در بارگذاری فایل اکسل: ' . $e->getMessage()]);
    }
}

/**
 * آماده‌سازی داده‌های فرم از ردیف اکسل
 */
function prepare_form_data(array $row_values, array $form_fields_data): array {
    $form_data = [];

    foreach ($form_fields_data as $index => $field) {
        // پرش ستون اول (equipment_id)
        // if ($index === 0) continue;
        $form_data[$field['id']] = $row_values[$index] ?? null;
    }

    return $form_data;
}
