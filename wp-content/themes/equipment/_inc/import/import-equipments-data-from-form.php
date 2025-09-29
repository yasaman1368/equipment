
<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

add_action('wp_ajax_import_equipments_data_from_form', 'import_equipments_data_from_form');


function import_equipments_data_from_form()
{
  global $wpdb;

  if (empty($_FILES['excel_file']['tmp_name'])) {
    wp_send_json(['success' => false, 'message' => 'فایل ارسال نشد']);
  }
  if (isset($_POST['form_id'])) $form_id = intval($_POST['form_id']);
  $file_tmp = $_FILES['excel_file']['tmp_name'];
  $spreadsheet = IOFactory::load($file_tmp);
  $sheet = $spreadsheet->getActiveSheet();
  $rows = $sheet->toArray();



  // ستون‌های اکسل (اولین ردیف)
  $columns = $rows[0];
  foreach ($rows as $index => $row) {
    if ($index === 0) continue;
    $equipment_id = $row[0];
    $form_data = prepare_form_data($columns, $row, $form_id);

    EquipmentSaver::save($equipment_id,$form_data);
     EquipmentFormSaver::save($form_id, $equipment_id, $form_data);
  }
 wp_send_json_success()
}

function  prepare_form_data($columns, $row, $form_id)
{
  global $wpdb;
  // form_fileds_data
  $query = $wpdb->prepare("SELECT id,field_name FROM pn_equipment_form_fields  WHERE form_id=%d ", $form_id);
  $form_fields_data = $wpdb->get_results($query, ARRAY_A);
  $form_data = [];
  foreach ($columns as $index => $col) {
    if ($index === 0) continue;
    // foreach($form_fields_data as $field_data){
    // $key=$result[$index];
    // $value;
    // $form_data[$key] = $value;
    // }
    $field_id = $form_fields_data[$index]['id'];
    // echo '<pre>';
    // var_dump($field_id);
    // echo '</pre>';
    // echo '</br>';
    $form_data[$field_id]=$row[$index];
  }
  // echo '<pre>';
  // var_dump($results);
  // echo '</pre>';
  // echo '</br>';
  return $form_data;
}




// formdata
// array(4) {
//   [228]=>
//   string(7) "dsfsadf"
//   [229]=>
//   string(1) "4"
//   [230]=>
//   string(7) "c02,c03"
//   [231]=>
//   string(3) "s02"
// }
//rows from excel
// array(5) {
//   [0]=>
//   array(4) {
//     [0]=>
//     string(12) "equipment_id"
//     [1]=>
//     string(3) "num"
//     [2]=>
//     string(2) "ch"
//     [3]=>
//     string(6) "select"
//   }
//   [1]=>
//   array(4) {
//     [0]=>
//     string(2) "11"
//     [1]=>
//     string(1) "1"
//     [2]=>
//     string(3) "c01"
//     [3]=>
//     string(3) "s01"
//   }
//   [2]=>
//   array(4) {
//     [0]=>
//     string(2) "12"
//     [1]=>
//     string(1) "2"
//     [2]=>
//     string(3) "c02"
//     [3]=>
//     string(3) "s02"
//   }
//   [3]=>
//   array(4) {
//     [0]=>
//     string(2) "13"
//     [1]=>
//     string(1) "3"
//     [2]=>
//     string(3) "c03"
//     [3]=>
//     string(3) "s03"
//   }
//   [4]=>
//   array(4) {
//     [0]=>
//     string(2) "14"
//     [1]=>
//     string(1) "4"
//     [2]=>
//     string(11) "c01,c02,c03"
//     [3]=>
//     string(3) "s03"
//   }
// }
//form field data
// array(4) {
//   [0]=>
//   array(2) {
//     ["id"]=>
//     string(3) "228"
//     ["field_name"]=>
//     string(12) "equipment_id"
//   }
//   [1]=>
//   array(2) {
//     ["id"]=>
//     string(3) "229"
//     ["field_name"]=>
//     string(3) "num"
//   }
//   [2]=>
//   array(2) {
//     ["id"]=>
//     string(3) "230"
//     ["field_name"]=>
//     string(2) "ch"
//   }
//   [3]=>
//   array(2) {
//     ["id"]=>
//     string(3) "231"
//     ["field_name"]=>
//     string(6) "select"
//   }
// }
