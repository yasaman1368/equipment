
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

    // ستون‌های اکسل (اولین ردیف)
    $columns = $rows[0];


    // ستون‌های دیتابیس رو خودت می‌تونی از جدول یا کانفیگ بخونی
    // مثلا:
    // $db_columns = ['name', 'serial', 'status'];

    // اینجا خودت لاجیک مقایسه / مپینگ رو پیاده کن
    // من به‌طور ساده فرض می‌کنم ستون‌های اکسل به ترتیب با دیتابیس می‌خونن
    // $mapping = [
    //   'name'   => 0,
    //   'serial' => 1,
    //   'status' => 2
    // ];

    $table = $wpdb->prefix . 'equipments';
    $wpdb->query('START TRANSACTION');

    $batch = [];
    $batch_size = 500;
    $inserted = 0;

    foreach ($rows as $index => $row) {
      if ($index === 0) continue; // رد کردن هدر

      $data = [];
      
      // foreach ($mapping as $db_field => $excel_index) {
      //   $data[$db_field] = sanitize_text_field($row[$excel_index]);
      // }

      $batch[] = $data;

      if (count($batch) >= $batch_size) {
        $inserted += insert_batch_equipments_dynamic($batch, $table);
        $batch = [];
      }
    }

    if (!empty($batch)) {
      $inserted += insert_batch_equipments_dynamic($batch, $table);
    }

    $wpdb->query('COMMIT');

    wp_send_json([
      'success'  => true,
      'inserted' => $inserted
    ]);
  } catch (Exception $e) {
    $wpdb->query('ROLLBACK');
    wp_send_json(['success' => false, 'message' => $e->getMessage()]);
  }
}

function insert_batch_equipments_dynamic($data, $table)
{

  global $wpdb;

  if (empty($data)) return 0;

  $fields = array_keys($data[0]);
  $placeholders = [];
  $values = [];

  foreach ($data as $row) {

    foreach ($fields as $field) {
      $values[] = $row[$field];

    }

  }

  // $query = "INSERT INTO $table (" . implode(",", $fields) . ") VALUES " . implode(",", $placeholders);
  // $wpdb->query($wpdb->prepare($query, $values));
  $stmt=$wpdb->insert($table,$data);


  return count($data);
}
