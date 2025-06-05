<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

function export_equipments()
{
    // if (!current_user_can('manage_options')) { // Check user capability
    //     wp_die('Unauthorized user');
    // }

    global $wpdb;

    // Fetch data from the database using wpdb
    $table_name = $wpdb->prefix . 'equipments'; // Replace with your table name
    $query = "SELECT * FROM $table_name";
    $data = $wpdb->get_results($query, ARRAY_A); // Fetch data as an associative array

    if (empty($data)) {
        die("No data found to export.");
    }

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Populate the spreadsheet with column names
    $columns = get_columns_equipments();
    $colIndex = 0;
    foreach ($columns as $item) {
        $columnLetter = getColumnLetter($colIndex);
        $sheet->setCellValue($columnLetter . '1', $item);
        $colIndex++;
    }


    // Populate the spreadsheet with data dynamically
    $row = 2;
    foreach ($data as $item) {
        $colIndex = 0;
        foreach ($columns as $column) {
            $columnLetter = getColumnLetter($colIndex);
            $sheet->setCellValue($columnLetter . $row, $item[$column] ?? ''); // Use null coalescing to handle missing keys
            $colIndex++;
        }
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
function get_columns_equipments()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'equipments'; // Replace with your table name

    // Query to get column names
    $query = "SHOW COLUMNS FROM $table_name";
    $columns = $wpdb->get_results($query);

    if (!empty($columns)) {
        $columns_array = [];
        foreach ($columns as $column) {

            $columns_array[] = $column->Field;
        }

        return $columns_array;
    } else {
        return false;
    }
}

// Helper function to get column letter by index
function getColumnLetter($index)
{
    $letter = '';
    while ($index >= 0) {
        $letter = chr($index % 26 + 65) . $letter;
        $index = floor($index / 26) - 1;
    }
    return $letter;
}
