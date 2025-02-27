<?php
function fajr_callback()
{
?>
    <!DOCTYPE html>
    <html lang="fa" dir="rtl">

    <head>
        <link href="<?php echo get_template_directory_uri() ?>/assets/images/PS.png" rel="shortcut icon" />
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php wp_title(); ?></title>
        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous" type="text/javascript"></script>
        <!-- Toastr -->
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" />
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
        <!-- date birthday cdn -->
        <link rel="stylesheet" href="https://unpkg.com/@majidh1/jalalidatepicker/dist/jalalidatepicker.min.css">
        <script type="text/javascript" src="https://unpkg.com/@majidh1/jalalidatepicker/dist/jalalidatepicker.min.js"></script>
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" integrity="sha384-dpuaG1suU0eT09tx5plTaGMLBsfDLzUCCUXOY2j/LSvXYuG6Bqs43ALlhIqAJVRb" crossorigin="anonymous">
        <!-- Bootstrap Icons -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    </head>

    <body class="bg-light">
        <div class="container" dir="rtl">

            <div class="text-center mt-4">
                <p>جهت ثبت نام و آگاهی از روند برگزاری مسابقات عضو کانال مسابقات به آدرس <a href="https://eitaa.com/joinchat/738656557C7242554f95" target="_blank">@QURANFAJRdayyer</a> شوید.</p>
                <!-- <a href="<?php // echo home_url('quran-alfajr/?submit=QF') 
                                ?>" class="btn-register">برای ثبت نام کلیک کنید</a> -->
                <!-- <div class="qr-code mt-3">
                    <p>با اسکن QR کد موجود در پوستر نیز می‌توانید به سایت ثبت نام مراجعه کرده و ثبت نام کنید.</p>
                    <img src="https://via.placeholder.com/150" alt="QR Code">
                </div> -->
                <p class="text-danger mt-3 fw-bold fs-1">🛑 آخرین مهلت ثبت نام ۲۰ بهمن ماه 🛑</p>
            </div>
            <?php
            $status = false;
            if ($status): ?>
                <form class="well form-horizontal" action="#" method="post" id="contact_form">
                    <fieldset>
                        <div class="container">
                            <div class="row">

                                <!-- Form Name -->
                                <div class="fs-1 p-2 bg-dark text-white text-center rounded-2 mt-5 mb-2">فرم ثبت نام مسابقات قرآن فجر </div>
                                <!-- Text input for First Name -->
                                <div class="form-group m-2">
                                    <label class="col-sm-6 control-label p-2">نام</label>
                                    <div class="col-md-6 inputGroupContainer">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                                            <input id="name" name="first_name" placeholder="نام" class="form-control show-form" type="text" required />
                                        </div>
                                    </div>
                                </div>
                                <!-- Text input for Last Name -->
                                <div class="form-group m-2">
                                    <label class="col-sm-6 control-label p-2">نام خانوادگی</label>
                                    <div class="col-md-6 inputGroupContainer">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-person-lines-fill"></i></span>
                                            <input id="family" name="last_name" placeholder="نام خانوادگی" class="form-control show-form" type="text" required />
                                        </div>
                                    </div>
                                </div>
                                <!-- Text input for National ID -->
                                <div class="form-group m-2">
                                    <label class="col-sm-6 control-label p-2">شماره ملی</label>
                                    <div class="col-md-6 inputGroupContainer">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-credit-card"></i></span>
                                            <input id="national_num" name="national_num" placeholder="شماره ملی" class="form-control show-form" type="text" required />
                                        </div>
                                    </div>
                                </div>
                                <!-- Text input for Phone -->
                                <div class="form-group m-2">
                                    <label class="col-sm-6 control-label p-2">تلفن همراه</label>
                                    <div class="col-md-6 inputGroupContainer">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                            <input id="phone" name="phone" placeholder="0917000000" class="form-control show-form" type="text" required />
                                        </div>
                                    </div>
                                </div>
                                <!-- Radio checks for Gender -->
                                <div class="form-group m-2">
                                    <label class="col-sm-6 control-label p-2">جنسیت:</label>
                                    <div class="col-md-6">
                                        <select id="gender" class="form-select" name="gender" onchange="formUpdate()" required>
                                            <option value="" disabled selected>-- انتخاب کنید --</option>
                                            <option value="female">زن</option>
                                            <option value="male">مرد</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- Date of Birth -->
                                <div class="form-group m-2">
                                    <label class="col-sm-6 control-label p-2">تاریخ تولد</label>
                                    <div class="col-md-6 inputGroupContainer">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                            <input type="text" name="quantity" id="date" class="form-control" placeholder="روز/ماه/سال" data-jdp required>
                                        </div>
                                    </div>
                                </div>
                                <script type="text/javascript">
                                    jalaliDatepicker.startWatch();
                                </script>
                                <!-- Age Group Selection -->
                                <div class="form-group m-2">
                                    <label class="col-sm-6 control-label p-2">گروه سنی</label>
                                    <div class="col-md-6 selectContainer">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                                            <select name="age" id="age" class="form-control selectpicker show-form" onchange="formUpdate()" required>
                                                <option value="">گروه سنی خود را انتخاب کنید</option>
                                                <option value="children">نونهال</option>
                                                <option value="teenagers">نوجوان</option>
                                                <option value="adults">بزرگسال</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <!-- City Selection -->
                                <div class="form-group m-2">
                                    <label class="col-sm-6 control-label p-2"> شهرستان محل سکونت</label>
                                    <div class="col-md-6 selectContainer">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                            <select name="city" id="city" class="form-control selectpicker show-form" disabled required>
                                                <option value="">شهر را انتخاب کنید...</option>

                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <!-- Competition Field Selection -->

                                <div class="form-group m-2">
                                    <label class="col-md-6 control-label p-2">رشته مسابقه:</label>
                                    <div class="col-md-6 selectContainer">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-trophy"></i></span>
                                            <select name="fieldMatch" id="categories" class="form-control selectpicker show-form" disabled require>
                                                <option value="">رشته مسابقه را انتخاب کنید...</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Address Input -->
                                    <div class="form-group m-2">
                                        <label class="col-sm-6 control-label p-2">آدرس</label>
                                        <div class="col-md-6 inputGroupContainer">
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-house"></i></span>
                                                <input id="address" name="address" placeholder="آدرس محل سکونت" class="form-control show-form" type="text" required />
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Success message -->
                                    <div class="alert alert-success" role="alert" id="success_message" style="display: none;">
                                        Success <i class="bi bi-check-circle"></i> Thanks for contacting us, we will get back to you shortly.
                                    </div>


                                    <!-- Submit Button -->
                                    <div class="form-group m-2 mb-5">
                                        <label class="col-sm-6 control-label p-2"></label>
                                        <div class="col-md-6">
                                            <button type="button" class="btn btn-primary" id="submit-btn">
                                                ارسال <span class="bi bi-send"></span>
                                            </button>
                                            <input type="hidden" name="ajax-url" value="<?php echo admin_url('admin-ajax.php') ?>">
                                            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce() ?>">
                                        </div>
                                    </div>

                                </div>
                            </div>
                    </fieldset>
                </form>
            <?php endif; ?>
        </div>

        <script src="<?php echo FAJR_PLUGIN_URL . '/assets/js/ajax.js'; ?>" type="text/javascript"></script>
        <script src="<?php echo FAJR_PLUGIN_URL . '/assets/js/form.js'; ?>" type="text/javascript"></script>
        <script src="<?php echo FAJR_PLUGIN_URL . '/assets/js/toastr.js'; ?>" type="text/javascript"></script>
        <!-- Bootstrap JS Bundle with Popper -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous" type="text/javascript"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.all.min.js"></script>
    </body>

    </html>
<?php
}
add_shortcode('fajr', 'fajr_callback');
?>