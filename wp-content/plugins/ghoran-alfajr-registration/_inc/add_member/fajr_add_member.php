<?php
add_action('wp_ajax_nopriv_fajr_add_member', 'fajr_add_member');
add_action('wp_ajax_fajr_add_member', 'fajr_add_member');

function fajr_add_member()
{
    // Verify nonce for security
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'])) {
        wp_send_json([
            'error' => true,
            'msg' => 'Access denied!'
        ], 403);
    }

    global $wpdb;
    $table = $wpdb->prefix . 'fajr_qoran';

    // Sanitize input fields
    $name = sanitize_text_field($_POST['name']);
    $family = sanitize_text_field($_POST['family']);
    $national_num = intval($_POST['national_num']);
    $phone = sanitize_text_field($_POST['phone']);
    $gender = sanitize_text_field($_POST['gender']);
    $field = sanitize_text_field($_POST['field']);
    $age = sanitize_text_field($_POST['age']);
    $address = sanitize_text_field($_POST['address']);
    $city = sanitize_text_field($_POST['city']);
    $date = sanitize_text_field($_POST['date']);

    // Check for empty fields
    $validation_result = validate_member_fields($name, $family, $phone, $address, $gender, $national_num, $city, $age, $field);
    if ($validation_result) {
        wp_send_json($validation_result, 400);
    }

    // Check if the national number already exists
    $existing_national_nums = $wpdb->get_col($wpdb->prepare("SELECT national_num FROM $table WHERE national_num = %d", $national_num));

    if (!empty($existing_national_nums)) {
        wp_send_json([
            'error' => true,
            'msg' => 'با این شماره ملی قبلا ثبت نام انجام شده' // "This national number has already been registered"
        ], 409);
    }

    // Prepare the data for insertion
    $data = [
        'name' => $name,
        'family' => $family,
        'national_num' => $national_num,
        'phone' => $phone,
        'gender' => $gender,
        'field' => $field,
        'age' => $age,
        'address' => $address,
        'city' => $city,
        'date' => $date

    ];

    // Define the format for each field
    $format = ['%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s']; // Use %d for integer fields

    // Insert the data into the database
    $stmt = $wpdb->insert($table, $data, $format);

    // Check for insert success
    if ($stmt === false) {
        wp_send_json([
            'error' => true,
            'msg' => 'خطا در ثبت اطلاعات' // "Error in saving data"
        ], 500);
    } else {
        wp_send_json([
            'success' => true,
            'msg' => 'ثبت نام با موفقیت انجام شد' // "Registration successful"
        ], 200);
    }
}

function validate_member_fields($name, $family, $phone, $address, $gender, $national_num, $city, $age, $field)
{
    if (empty($name)) {
        return [
            'error' => true,
            'msg' => 'نام الزامی است' // "Name is required"
        ];
    }

    if (empty($family)) {
        return [
            'error' => true,
            'msg' => 'نام خانوادگی الزامی است' // "Family name is required"
        ];
    }

    if (empty($phone)) {
        return [
            'error' => true,
            'msg' => 'شماره تلفن الزامی است' // "Phone number is required"
        ];
    }

    if (empty($address)) {
        return [
            'error' => true,
            'msg' => 'آدرس الزامی است' // "Address is required"
        ];
    }
    if (empty($city)) {
        return [
            'error' => true,
            'msg' => 'نام شهر الزامی است' // "Address is required"
        ];
    }

    if (empty($gender)) {
        return [
            'error' => true,
            'msg' => 'جنسیت الزامی است' // "Gender is required"
        ];
    }


    if (empty($national_num)) {
        return [
            'error' => true,
            'msg' => 'شماره ملی الزامی است' // "National number is required"
        ];
    }

    if (strlen($national_num) !== 10) {
        return [
            'error' => true,
            'msg' => 'شماره ملی را صحیح وارد کنید' // "National number is required"
        ];
    }

    if (empty($field)) {
        return [
            'error' => true,
            'msg' => 'انتخاب رشته مسابقه الزامی است' // "National number is required"
        ];
    }
    if (empty($age)) {
        return [
            'error' => true,
            'msg' => 'انتخاب گروه سنی الزامی است' // "National number is required"
        ];
    }

    // If all fields are valid, return null
    return null;
}
