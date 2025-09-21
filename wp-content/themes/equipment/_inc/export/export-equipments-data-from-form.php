<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

function export_equipments_data_from_form($form_id)
{
  $initial_data = get_equipments_data($form_id);
  $data = prepare_data($initial_data);

  $spreadsheet = new Spreadsheet();
  $sheet = $spreadsheet->getActiveSheet();

  // گرفتن ستون‌ها
  $columns = get_columns_formatter_excel($form_id);

  // هدر جدول (ردیف اول)
  foreach ($columns as $colIndex => $colName) {
    $columnLetter = getColumnLetter($colIndex);
    $sheet->setCellValue($columnLetter . '1', $colName);
  }

  // داده‌ها
  $row = 2;
  foreach ($data as $item) {
    foreach ($columns as $colIndex => $colName) {
      $columnLetter = getColumnLetter($colIndex);
      $sheet->setCellValue(
        $columnLetter . $row,
        $item[$colIndex] ?? ''
      );
    }
    $row++;
  }

  // ایجاد خروجی
  $writer = new Xlsx($spreadsheet);
  $filename = 'exported_data.xlsx';

  header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
  header('Content-Disposition: attachment; filename="' . $filename . '"');
  header('Cache-Control: max-age=0');

  ob_clean();
  flush();
  $writer->save('php://output');
  exit;
}

function get_equipments_data($form_id)
{
  global $wpdb;
  $table_name = $wpdb->prefix . 'equipment_data';
  $query = $wpdb->prepare("SELECT * FROM $table_name WHERE form_id=%s", $form_id);
  return $wpdb->get_results($query, ARRAY_A);
}

function prepare_data($initial_data)
{
  $result = [];

  foreach ($initial_data as $row) {
    $eqId = $row['equipment_id'];
    $value = $row['value'];

    if (!isset($result[$eqId])) {
      $result[$eqId] = [];
    }
    $result[$eqId][] = $value;
  }

  return array_values($result); 
}
