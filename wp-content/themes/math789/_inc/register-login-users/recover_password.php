<?php
add_action('wp_ajax_nopriv_recover_password', 'recover_password');

function recover_password(): void
{
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'ajax-nonce')) {
        wp_die('access denied', 'forbidden');
    }
    $recover_email = sanitize_text_field($_POST['email']);
    yas_validate_email($recover_email);
    $recover_email_link = generate_recover_email_link($recover_email);
    yas_send_email($recover_email, $recover_email_link);
}
function yas_validate_email($email)
{
    if (!is_email($email)) {
        wp_send_json([
            'error' => true,
            'message' => 'ایمیل معتبر خود را وارد نمایید!'
        ], 403);
    }
    if (!email_exists($email)) {
        wp_send_json([
            'error' => true,
            'message' => 'کاربری با ایمیل مورد نظر شما یافت نشد!'
        ], 403);
    }
}

function generate_recover_email_link($recover_email)
{
    $token = date('Ymd') . md5($recover_email) . rand(100000, 999999);
    return add_query_arg(['recoverToken' => $token], site_url('panel/recoverpassword'));
}

function yas_send_email($email, $recover_email_link)
{
    $header = ['Content-Type:text/html;charset=UTF-8'];
    $send_recover_email = wp_mail($email, 'بازیابی کلمه عبور', $recover_email_link, $header);
    if ($send_recover_email) {
        add_recover_token($email, $recover_email_link);
        wp_send_json([
            'success' => true,
            'message' => 'لینک بازیابی کلمه به ایمیل شما ارسال گردید.'
        ], 200);
    } else {
        wp_send_json([
            'error' => true,
            'message' => 'خطایی در ارسال ایمیل صورت گرفته است!'
        ], 403);
    }
}
function add_recover_token($email, $recover_email_link)
{
    global $wpdb;
    $table = 'recover_password_verify';
    $token = explode('=', $recover_email_link);
    $data = [
        'token' => $token[1],
        'email' => $email
    ];
    $format = ['%s', '%s'];
    $stmt = $wpdb->insert($table, $data, $format);
    if (!$stmt) {
        wp_send_json([
            'error' => true,
            'message' => ' در بازیابی رمز  عبور خطایی  صورت گرفته است با پشتیبانی تماس بگیرید'
        ], 403);
    }
}
function find_recover_token($token)
{
    global $wpdb;
    $table = 'recover_password_verify';
    return $wpdb->get_var($wpdb->prepare("SELECT token FROM {$table} WHERE token =%s", $token));
}
