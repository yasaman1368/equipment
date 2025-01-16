<?php
add_action('wp_ajax_nopriv_math_login', 'math_login');
add_action('wp_ajax_math_login', 'math_login');

function math_login(): void
{

    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'ajax-nonce')) {
        wp_send_json(['msg' => 'access denied'], 403);
    }

    $email = sanitize_text_field($_POST['email']);
    $userPass = $_POST['userPass']; // No need to sanitize here
    $rememberMe = rest_sanitize_boolean($_POST['rememberMe']);

    validateEmailUserPass($email, $userPass);

    $user_login = get_user_by('email', $email);
    if (!$user_login) {
        wp_send_json([
            'error' => true,
            'msg' => 'نام کاربری یا کلمه عبور اشتباه است!!!'
        ], 403);
    }

    $creds = [
        'user_login' => sanitize_user($user_login->user_login),
        'user_password' => $userPass,
        'remember' => $rememberMe
    ];

    $user = wp_signon($creds);
    if (is_wp_error($user)) {
        wp_send_json([
            'error' => true,
            'msg' => 'نام کاربری یا کلمه عبور اشتباه است!!!'
        ], 403);
    }

    wp_set_current_user($user->ID);
    wp_send_json([
        'success' => true,
        'msg' => 'ورود شما موفقیت آمیز بود در حال انتقال ...'
    ], 200);
}

function validateEmailUserPass($email, $userPass): void
{
    if (empty($email) || empty($userPass)) {
        $msg = empty($email) ? 'لطفا ایمیل خود را وارد نمایید.' : 'لطفا کلمه عبور خود را وارد نمایید.';
        wp_send_json(['error' => true, 'msg' => $msg], 403);
    }

    if (!is_email($email)) {
        wp_send_json(['error' => true, 'msg' => 'لطفا ایمیل معتبر وارد نمایید'], 403);
    }
}
add_filter('auth_cookie_expiration', 'wp_lr_set_cookie', 1);
function wp_lr_set_cookie($expiration)
{
    return $expiration = 60 * 60 * 48;
}
