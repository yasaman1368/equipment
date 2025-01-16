<?php
add_action('wp_ajax_nopriv_set_recover_password', 'set_recover_password');

function set_recover_password()
{
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'ajax-nonce')) {
        wp_die('access denied', 'forbidden');
    }

    $pass = sanitize_text_field($_POST['pass']);
    $repead_pass = sanitize_text_field($_POST['repeadPass']);
    $token = sanitize_text_field($_POST['token']);
    validate_password($pass, $repead_pass);
    $user_id = find_user_id($token);
    $reset_password = wp_set_password($pass, $user_id);
    if (!is_wp_error($reset_password)) {
        delete_recover_password_token($token);
        wp_send_json([
            'success' => true,
            'message' => 'کلمه عبور با موفقیت تغیر کرد.'
        ], 200);
    } else {
        wp_send_json([
            'error' => true,
            'message' => 'خطایی در تغیر کلمه عبور شما زخ داده است!'
        ], 403);
    }
}
function validate_password($pass, $repead_pass)
{
    // Validate new password
    if ($pass == '' || $repead_pass == '') {
        wp_send_json(['error' => true, 'message' => 'کلمه عبور و تکرار آن را وارد نمایید!'], 403);
    }
    if (mb_strlen($pass) < 6) {
        wp_send_json(['error' => true, 'message' => 'رمز ورود نمی تواند کمتر از 6 حرف باشد'], 403);
    }

    if ($repead_pass !== $pass) {
        wp_send_json(['error' => true, 'message' => 'رمز جدید با تکرار رمز مطابقت ندارد '], 403);
    }
}
function find_user_id($token)
{
    global $wpdb;
    $table =  'recover_password_verify';
    $email = $wpdb->get_var($wpdb->prepare("SELECT email FROM {$table} WHERE token =%s", $token));
    $user = get_user_by('email', $email);
    return $user->ID;
}
function  delete_recover_password_token($token)
{
    global $wpdb;
    $table =  'recover_password_verify';
    $where = ['token' => $token];
    $where_format = ['%s'];

    $stmt = $wpdb->delete($table, $where, $where_format);
    if (!$stmt) {
        wp_send_json(['error' => true, 'message' => 'لینک تغییر ایمیل نامعتبر است '], 403);
    }
}
