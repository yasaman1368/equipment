<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// add_action('wp_ajax_get_excel_format', 'get_excel_format');

function get_excel_format($form_id)
{
  $spreadsheet = new Spreadsheet();
  $sheet = $spreadsheet->getActiveSheet();

  // Populate the spreadsheet with column names
  $columns = get_columns_formatter_excel($form_id);
  $colIndex = 0;
  foreach ($columns as $item) {
    $columnLetter = getColumnLetter($colIndex);
    $sheet->setCellValue($columnLetter . '1', $item);
    $colIndex++;
  }

   // Create an Xlsx writer
    $writer = new Xlsx($spreadsheet);
    $filename = 'exported_formater_form_id'.$form_id.'.xlsx';

    // Set headers for the download
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    // Output the file to the browser directly
    ob_clean(); // Clear the output buffer
    flush(); // Flush system output buffer
    $writer->save('php://output');
    exit;

}

function get_columns_formatter_excel($form_id)
{

  global $wpdb;
  $table_name = $wpdb->prefix . 'equipment_form_fields';
  $query = $wpdb->prepare("SELECT field_name FROM $table_name WHERE form_id=%d", $form_id);
  $columns = $wpdb->get_col($query);
  if (!$columns) {
    return false;
  }
  array_unshift($columns,'equipment_id');
  
  return $columns;
}
