<?php
class MailLayout
{
    public static function contact_layout($name, $email, $subject, $message)
    {

        return $mail_layout = '<!DOCTYPE html>
        <html lang="fa">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f4f4f4;
                    margin: 0;
                    padding: 0;
                }

                .email-container {
                    background-color: #ffffff;
                    margin: 20px auto;
                    padding: 20px;
                    border-radius: 5px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                    max-width: 600px;
                }

                .header {
                    background-color: #0073aa;
                    color: #ffffff;
                    padding: 10px;
                    text-align: center;
                    border-radius: 5px 5px 0 0;
                }

                .content {
                    padding: 20px;
                }

                .footer {
                    background-color: #f4f4f4;
                    color: #666666;
                    padding: 10px;
                    text-align: center;
                    border-radius: 0 0 5px 5px;
                }
            </style>
        </head>

        <body dir="rtl">
            <div class="email-container">
                <div class="header">
                    <h1>پیام جدید از فرم تماس با ما</h1>
                </div>
                <div class="content">
                    <p>نام: ' . $name . '</p>
                    <p>ایمیل: ' . $email . '</p>
                    <p>موضوع: ' . $subject . '</p>
                    <p>پیام:</p>
                    <p>' . nl2br($message) . '</p>
                </div>
                <div class="footer">
                    <p>این ایمیل به صورت خودکار ارسال شده است. لطفا به آن پاسخ ندهید.</p>
                </div>
            </div>
        </body>

        </html>';
    }
}
