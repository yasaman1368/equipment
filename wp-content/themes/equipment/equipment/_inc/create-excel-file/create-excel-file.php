<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

function create_excel_file()
{
    global $wpdb;

    // Fetch data from the database using wpdb
    $table_name = $wpdb->prefix . 'equipment_forms'; // Replace with your table name
    $query = "SELECT * FROM $table_name";
    $data = $wpdb->get_results($query, ARRAY_A); // Fetch data as an associative array

    if (empty($data)) {
        die("No data found to export.");
    }

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    // Set the column headers
    $sheet->setCellValue('A1', 'ID');
    $sheet->setCellValue('B1', 'Name');
    $sheet->setCellValue('C1', 'Email');

    // Populate the spreadsheet with data
    $row = 2;
    foreach ($data as $item) {
        $sheet->setCellValue('A' . $row, $item['id']);
        $sheet->setCellValue('B' . $row, $item['form_name']);
        // $sheet->setCellValue('C' . $row, $item['email']);
        // Add more columns as needed
        $row++;
    }

    // Create an Xlsx writer
    $writer = new Xlsx($spreadsheet);
    $filename = 'exported_data.xlsx';

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

//create_excel_file();
