<?php
function quran_alfajr_home()
{
?>
    <!DOCTYPE html>
    <html lang="fa" dir="rtl">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>هفتمین دوره مسابقات قرآن الفجر شهرستان دیر</title>
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- FontAwesome CSS -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
        <!-- Custom CSS -->
        <style>
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background-color: #f8f9fa;
                color: #333;
            }

            .header {
                background-color: #28a745;
                color: white;
                padding: 20px;
                text-align: center;
                animation: fadeIn 1s ease-in;
            }

            .section-title {
                color: #28a745;
                margin-top: 20px;
                margin-bottom: 10px;
                font-size: 1.5rem;
            }

            .list-group-item {
                border: none;
                padding: 10px 15px;
                transition: background-color 0.3s;
            }

            .list-group-item:hover {
                background-color: #e2ffe2;
            }

            .btn-register {
                background-color: #28a745;
                color: white;
                padding: 10px 20px;
                border-radius: 5px;
                text-decoration: none;
                display: inline-block;
                margin-top: 20px;
                transition: background-color 0.3s;
            }

            .btn-register:hover {
                background-color: #218838;
            }

            .qr-code {
                text-align: center;
                margin-top: 20px;
            }

            .qr-code img {
                max-width: 150px;
            }

            @keyframes fadeIn {
                from {
                    opacity: 0;
                }

                to {
                    opacity: 1;
                }
            }
        </style>
    </head>

    <body>
        <div class="header">
            <h1>هفتمین دوره مسابقات قرآن الفجر شهرستان دیر</h1>
            <p>🍀إِنَّ قُرۡءَانَ ٱلۡفَجۡرِ كَانَ مَشۡهُودٗا🍀</p>
        </div>
        <div class="container mt-4">
            <!-- برادران بزرگسال -->
            <div class="section-title">
                <i class="fas fa-user-graduate"></i> برادران بزرگسال
            </div>
            <ul class="shadow rounded list-group">
                <li class="list-group-item">✅ قرائت تحقیق</li>
                <li class="list-group-item">✅ قرائت تدویر</li>
                <li class="list-group-item">✅ حفظ کل</li>
                <li class="list-group-item">✅ حفظ ۲۰ جزء</li>
                <li class="list-group-item">✅ حفظ ۱۰ جزء</li>
                <li class="list-group-item">✅ مفاهیم (سوره فتح از تفسیر نمونه)</li>
            </ul>
            <!-- برادران نوجوان -->
            <div class="section-title mt-4">
                <i class="fas fa-user-friends"></i> برادران نوجوان (۹ تا ۱۶ سال)
            </div>
            <ul class="shadow rounded list-group">
                <li class="list-group-item">❇️ قرائت تقلیدی (۴ دقیقه از اساتید منشاوی، عبدالباسط و مصطفی اسماعیل)</li>
                <li class="list-group-item">❇️ حفظ نیم جزء</li>
                <li class="list-group-item">❇️ حفظ یک جزء</li>
                <li class="list-group-item">❇️ حفظ ۳ جزء</li>
                <li class="list-group-item">❇️ اذان (اذان‌های فارسی مورد پذیرش نیست)</li>
                <li class="list-group-item">❇️ قرائت تدویر سوره فجر</li>
            </ul>
            <!-- خواهران بزرگسال -->
            <div class="section-title mt-4">
                <i class="fas fa-user-nurse"></i> خواهران بزرگسال
            </div>
            <ul class="shadow rounded list-group">
                <li class="list-group-item">💠 حفظ کل</li>
                <li class="list-group-item">💠 حفظ ۲۰ جزء</li>
                <li class="list-group-item">💠 حفظ ۱۰ جزء</li>
                <li class="list-group-item">💠 حفظ ۵ جزء</li>
                <li class="list-group-item">💠 قرائت تدویر</li>
                <li class="list-group-item">💠 مفاهیم (سوره فتح از تفسیر نمونه)</li>
            </ul>
            <!-- خواهران نوجوان -->
            <div class="section-title mt-4">
                <i class="fas fa-user-graduate"></i> خواهران نوجوان (۹ تا ۱۶ سال)
            </div>
            <ul class="shadow rounded list-group">
                <li class="list-group-item">💎 حفظ نیم جزء</li>
                <li class="list-group-item">💎 حفظ یک جزء</li>
                <li class="list-group-item">💎 حفظ ۳ جزء</li>
                <li class="list-group-item">💎 حفظ ۵ جزء</li>
                <li class="list-group-item">💎 ترتیل سوره مبارکه فجر</li>
            </ul>
            <!-- نونهالان -->
            <div class="section-title mt-4">
                <i class="fas fa-baby"></i> نونهالان (زیر ۹ سال)
            </div>
            <ul class="shadow rounded list-group">
                <li class="list-group-item">❇️ حفظ ۱۰ سوره (سوره فیل تا ناس)</li>
                <li class="list-group-item">❇️ حفظ ۲۰ سوره (سوره تین تا ناس)</li>
                <li class="list-group-item">❇️ حفظ نیم جزء</li>
                <li class="list-group-item">❇️ فصیح‌خوانی (روانخوانی سوره تین تا ناس)</li>
            </ul>
            <!-- ثبت نام -->
            <div class="text-center mt-4">
                <!-- <p>جهت ثبت نام و آگاهی از روند برگزاری مسابقات عضو کانال مسابقات به آدرس <a href="https://t.me/QURANFAJRdayyer" target="_blank">@QURANFAJRdayyer</a> شوید.</p> -->
                <a href="<?php echo home_url('quran-alfajr/?submit=QF') ?>" class="btn-register">برای ثبت نام کلیک کنید</a>
                <!-- <div class="qr-code mt-3">
                    <p>همچنین با اسکن QR کد موجود در پوستر نیز می‌توانید به سایت ثبت نام مراجعه کرده و ثبت نام کنید.</p>
                    <img src="https://via.placeholder.com/150" alt="QR Code">
                </div> -->
                <p class="text-danger mt-3">🛑 آخرین مهلت ثبت نام ۲۰ بهمن ماه 🛑</p>
            </div>
        </div>
        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>

    </html>
<?php
}
add_shortcode('quran-alfajr-home', 'quran_alfajr_home');
