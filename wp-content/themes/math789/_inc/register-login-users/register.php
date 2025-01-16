<?php
if (!session_id()) {
    session_start();
}
add_action('wp_ajax_nopriv_math_send_code', 'math_send_code');
add_action('wp_ajax_nopriv_math_verify_verification_code', 'math_verify_verification_code');
add_action('wp_ajax_nopriv_math_registration', 'math_registration');
function  math_send_code()
{
    if (!isset($_POST['data']['nonce']) || !wp_verify_nonce($_POST['data']['nonce'], 'ajax-nonce')) {
        wp_send_json(['msg' => 'access denied'], 403);
    }
    $phone = sanitize_text_field($_POST['data']['phone']);
    vlidationUserPhone($phone);
    existPhone($phone);
    $verifyCode = generateVarifyCode();
    $sendSMS = YasSendSMS($verifyCode, $phone, '250996');
    if ($sendSMS->StrRetStatus !== 'ok') {
        addVerifyCodeDB($phone, $verifyCode);
        wp_send_json([
            'success' => true,
            'message' => 'کد تاییدیه به شماره موبایل شما ارسال شد.'
        ], 200);
    }
}
function vlidationUserPhone($phone): void
{
    if (!preg_match('/^09[0-9]{9}$/', $phone)) {
        wp_send_json([
            'error' => true,
            'message' => 'لطفا شماره موبایل معتبر وارد نمایید'
        ], 403);
    }
}
function     existPhone($phone): void
{
    $args = [
        'meta_query' => '_math_user_phone',
        'meta_value' => $phone,
        'compare' => '='
    ];
    $user = new WP_User_Query($args);
    if ($user->get_total() == 1) {
        wp_send_json([
            'error' => true,
            'message' => 'با این شماره، قبلا ثبت نام انجام شده'
        ], 403);
    }
}
function generateVarifyCode()
{
    return rand(1000, 9999);
}
function addVerifyCodeDB($phone, $verifyCode)
{
    global $wpdb;
    $table = 'math_verify_code';
    $stmt = $wpdb->get_row($wpdb->prepare("SELECT phone FROM {$table} WHERE phone ='%s'", $phone));
    if ($stmt) {
        $data = ['verify_code' => $verifyCode];
        $where = ['phone' => $phone];
        $format = ['%s'];
        $whereformat = ['%s'];
        $wpdb->update($table, $data, $where, $format, $whereformat);
    } else {
        $data = [
            'verify_code' => $verifyCode,
            'phone' => $phone
        ];
        $format = ['%s', '%s'];
        $wpdb->insert($table, $data, $format);
    }
}
function math_verify_verification_code()
{
    if (!isset($_POST['data']['nonce']) || !wp_verify_nonce($_POST['data']['nonce'], 'ajax-nonce')) {
        wp_send_json(['msg' => 'access denied'], 403);
    }
    $verificationCode = sanitize_text_field($_POST['data']['verifyCode']);
    validateVerifyCode($verificationCode);
    checkVerificationCode($verificationCode);
}

function validateVerifyCode($verificationCode)
{
    if ($verificationCode == '') {
        wp_send_json([
            'error' => true,
            'message' => 'کد تاییدیه دریافتی را وارد نمایید'
        ], 403);
    }
    if (strlen($verificationCode) != 4) {
        wp_send_json([
            'error' => true,
            'message' => 'کد تاییده باید شامل 4 رقم باشد'
        ], 403);
    }
}

function checkVerificationCode($verificationCode)
{
    global $wpdb;
    $stmt = $wpdb->get_row($wpdb->prepare("SELECT verify_code,phone FROM math_verify_code WHERE verify_code='%d'", $verificationCode));
    if ($stmt) {
        $_SESSION['current_user_phone'] = $stmt->phone;
    } else {
        wp_send_json([
            'error' => true,
            'message' => 'کد تاییدیه معتبر نمی باشد'
        ], 403);
    }
}

function math_registration()
{
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'ajax-nonce')) {
        wp_send_json(['msg' => 'access denied'], 403);
    }
    $firstName = sanitize_text_field($_POST['first_name']);
    $lastName = sanitize_text_field($_POST['last_name']);
    $role = sanitize_text_field($_POST['role']);
    $lesson = sanitize_text_field($_POST['lesson']);
    $education_base = sanitize_text_field($_POST['education-base']);
    $gender = sanitize_text_field($_POST['gender']);
    $email = sanitize_email($_POST['email']);
    $password = sanitize_text_field($_POST['password']);
    vlidationRegisternigData($firstName, $lastName, $email, $password, $role, $lesson, $education_base, $gender);
    $unique_username = create_unique_user_login($email);
    if ($role === 'teacher') {
        $role = 'contributor';
    } elseif ($role === 'student') {
        $role = 'subscriber';
    }
    $userdata = array(
        'user_login'  => $unique_username, // Required
        'user_pass'   => $password, // Required
        'user_email'  => $email, // Required
        'first_name'  => $firstName, // Optional
        'last_name'   => $lastName, // Optional
        'role'        => $role, // Optional
    );

    // Insert the user into the database
    $user_id = wp_insert_user($userdata);
    add_user_meta($user_id, '_math_user_phone', $_SESSION['current_user_phone']);
    if ($role == 'contributor') {
        add_user_meta($user_id, '_math_user_lesson', $lesson);
    } elseif ($role == 'subscriber') {
        add_user_meta($user_id, '_math_user_education_base', $education_base);
    }
    add_user_meta($user_id, '_math_user_gender', $gender);
    if (!is_wp_error($user_id)) {
        // YasSendSMS()
        session_unset();
        wp_send_json([
            'success' => true,
            'message' => 'ثبت نام شما با موفقیت صورت گرفت'
        ], 200);
    } else {
        wp_send_json([
            'error' => true,
            'message' => 'خطایی در ثبت نام شما صورت گرفته است'
        ], 403);
    }
}
function vlidationRegisternigData($firstName, $lastName, $email, $password, $role, $lesson, $education_base, $gender)
{

    if (empty($firstName) || empty($lastName)) {
        wp_send_json([
            'error' => true,
            'message' => 'نام و نام خانوادگی خود را وارد نمایید'
        ], 403);
    }

    if ($role === '0') {
        wp_send_json([
            'error' => true,
            'message' => 'نقش خود را انتخاب نمایید'
        ], 403);
    }
    if ($role === 'teacher' && $lesson === '0') {
        wp_send_json([
            'error' => true,
            'message' => 'نام درس خود را مشخص کنید'
        ], 403);
    }
    if ($role === 'student' && $education_base === '0') {
        wp_send_json([
            'error' => true,
            'message' => 'پایه تحصیلی خود را مشخص کنید'
        ], 403);
    }
    if (empty($gender)) {
        wp_send_json([
            'error' => true,
            'message' => 'جنسیت خود را مشخص کنید'
        ], 403);
    }
    if (!is_email($email) || empty($email)) {
        wp_send_json([
            'error' => true,
            'message' => 'ایمیل معتبر خود را وارد نمایید'
        ], 403);
    }
    if (email_exists($email)) {
        wp_send_json([
            'error' => true,
            'message' => 'با این ایمیل قبلا ثبت نام انجام شده'
        ], 403);
    }
    if ($password == '') {
        wp_send_json([
            'error' => true,
            'message' => 'کلمه عبور خود را وارد نمایید'
        ], 403);
    }
    if (strlen($password) < 6) {
        wp_send_json([
            'error' => true,
            'message' => 'رمز عبور حداقل 6 حرف یا عدد انتحاب کنید '
        ], 403);
    }
}
function create_unique_user_login($email)
{
    // Get the part of the email before the '@'
    $username = substr($email, 0, strpos($email, '@'));
    while (username_exists($username)) {
        // Append a counter to the username (e.g., user1, user2)
        $username = substr($email, 0, strpos($email, '@')) . rand(10, 99);
    }
    return $username;
}
