
<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

add_action('wp_ajax_import_equipments_data_from_form', 'import_equipments_data_from_form');


function import_equipments_data_from_form()
{
  global $wpdb;

  if (empty($_FILES['excel_file']['tmp_name'])) {
    wp_send_json(['success' => false, 'message' => 'فایل ارسال نشد']);
  }

  try {
    $file_tmp = $_FILES['excel_file']['tmp_name'];
    $spreadsheet = IOFactory::load($file_tmp);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();
    print_r($rows);
die;
    // ستون‌های اکسل (اولین ردیف)
    $columns = $rows[0];


   
}
}