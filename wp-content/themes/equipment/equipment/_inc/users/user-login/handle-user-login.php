<?php
add_action('wp_ajax_handle_user_login', 'handle_user_login');
add_action('wp_ajax_nopriv_handle_user_login', 'handle_user_login'); // For non-logged-in users

function handle_user_login()
{

    if (!isset($_POST['login-nonce-name']) || !wp_verify_nonce($_POST['login-nonce-name'], 'login-nonce-action')) {
        wp_send_json_error('خطای امنیتی رخ داده است.');
    }
    // Get the submitted data
    $username = sanitize_text_field($_POST['username']);
    $password = sanitize_text_field($_POST['password']);
    $rememberMe = isset($_POST['rememberMe']) ? true : false;

    // Attempt to log the user in
    $user = wp_signon([
        'user_login'    => $username,
        'user_password' => $password,
        'remember'      => $rememberMe,
    ]);

    if (is_wp_error($user)) {
        wp_send_json_error(['message' => 'نام کاربری یا رمز عبور اشتباه است.']);
    } else {
        wp_send_json_success([
            'redirect_url' => home_url('panel'),
            'message' => 'ورود موفقیت‌آمیز بود!'

        ]); // Redirect to home page
    }

    wp_die(); // Always include this for AJAX handlers
}
