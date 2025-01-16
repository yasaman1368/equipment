<?php
function initialize_send_mail($phpmailer)
{
    // $phpmailer->isSMTP();
    // $phpmailer->Host = 'alipor.hossain@gmail.com';
    // $phpmailer->SMTPAuth = true; // Ask it to use authenticate using the Username and Password properties
    // $phpmailer->Port = 587;
    // $phpmailer->Username = 'alipor.hossain@gmail.com';
    // $phpmailer->Password = '@Yasaman125369';
    // $phpmailer->SMTPSecure = false;
    // $phpmailer->SMTPAutoTLS = false;
    // $phpmailer->From = "alipor.hossain@gmail.com";
    // $phpmailer->FromName = "ریاضی 789";
    $phpmailer->isSMTP();
    $phpmailer->Host = 'mail.porsnegar.ir';
    $phpmailer->SMTPAuth = true; // Ask it to use authenticate using the Username and Password properties
    $phpmailer->Port = '587';
    $phpmailer->Username = 'info@porsnegar.ir';
    $phpmailer->Password = 'yasaman125369';
    $phpmailer->SMTPSecure = false;
    $phpmailer->SMTPAutoTLS = false;
    $phpmailer->From = "info@porsnegar.ir";
    $phpmailer->FromName = "porsnegar.ir";
}
add_action('phpmailer_init', 'initialize_send_mail');
