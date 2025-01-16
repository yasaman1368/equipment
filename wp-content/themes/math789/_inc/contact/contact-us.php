<?php
add_action('wp_ajax_nopriv_contact_us', 'contact_us');
add_action('wp_ajax_contact_us', 'contact_us');
function contact_us()
{
    $nonce = $_POST['nonce'];
    if (!isset($nonce) || !wp_verify_nonce($nonce, 'contact-nonce')) {
        wp_send_json(['error' => true, 'msg' => 'access denied'], 403);
    }

    foreach ($_POST as $item) {
        if (empty($item)) {
            wp_send_json([
                'error' => true,
                'message' => 'تکمیل تمامی فیلدها الزامی است'
            ], 403);
        }
    }
    $name = sanitize_text_field($_POST['name']);
    $email = sanitize_text_field($_POST['email']);
    $subject_contact = sanitize_text_field($_POST['subject']);
    $message = sanitize_text_field($_POST['message']);
    $recaptcha = $_POST['g-recaptcha-response'];
    $secret_key = '6LcR2IkqAAAAALnSGU-Y2PTVMFZWYjPSWvPM7yIm';
    $url = 'https://www.google.com/recaptcha/api/siteverify?secret='
        . $secret_key . '&response=' . $recaptcha;
    $response = file_get_contents($url);
    $response = json_decode($response);
    if ($response->success != true) {

        wp_send_json([
            'error' => true,
            'message' => 'تیک گزینه من ربات نیستم را انتخاب کنید'
        ], 403);
    };




    $to = "alipor.hossain@gmail.com";
    $subject = "پیام جدید از فرم تماس با ما";




    // هدرهای ایمیل
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: noreply@example.com" . "\r\n";
    $email_status = wp_mail($to, $subject, MailLayout::contact_layout($name, $email, $subject_contact, $message), $headers);

    if ($email_status) {
        wp_send_json([
            'success' => true,
            'message' => 'پیام شما با موفقیت ارسال شد'
        ], 200);
    } else {
        wp_send_json([
            'error' => true,
            'message' =>  "خطا در ارسال پیام، لطفا دوباره تلاش کنید"
        ], 403);
    }
}
