<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پُرس نگار</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/css/materialize.min.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri() . '/assets/css/login.css' ?>">
    <link rel="icon" type="image/x-icon" sizes="180x180" href="<?php echo  get_template_directory_uri() ?>/assets/images/PS.png">

    <!-- font cdn -->
    <link rel="preconnect" href="https://fdn.fontcdn.ir" />
    <link rel="preconnect" href="https://v1.fontapi.ir" />
    <link href="https://v1.fontapi.ir/css/Vazir" rel="stylesheet" />
</head>

<body>
    <div class="container white z-depth-2">
        <ul class="tabs teal">
            <li class="tab col s3"><a class="white-text" href="#register">ثبت نام</a></li>
            <li class="tab col s3"><a class="white-text active" href="#login">ورود</a></li>
        </ul>
        <div id="login" class="col s12" dir="rtl">
            <form class="col s12" method="post" id="loginform">
                <div class="form-container">
                    <h3 class="teal-text">سلام</h3>
                    <div class="row">
                        <div class="input-field col s12">
                            <input id="email" type="email" class="validate email"
                                name="email"
                                value="<?php echo (!empty($_POST['email'])) ? esc_attr($_POST['email']) : ''; ?>">
                            <label for="email">ایمیل</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <input id="user_pass" type="password" class="validate"
                                name="password">
                            <label for="password">رمز عبور</label>
                        </div>
                    </div>
                    <br>
                    <p class="login-remember">
                        <label for="rememberme">
                            <input name="rememberme" type="checkbox" id="rememberme" value="forever">
                            مرا به خاطر بسپار
                        </label>
                    </p>
                    <center>
                        <button class="btn waves-effect waves-light teal" type="submit" name="action">
                            ورود</button>
                        <br>
                        <br>
                        <a href="<?php echo site_url('panel/recoverpassword') ?>">بازیابی رمز عبور؟</a>
                    </center>
                </div>
                <input type="hidden" name="ajax-url" value="<?php echo admin_url('admin-ajax.php') ?>">
                <input type="hidden" name="ajax-nonce" value="<?php echo wp_create_nonce('ajax-nonce') ?>">
            </form>

        </div>
        <div id="register" class="col s12" dir="rtl">

            <form class="col s12" method="post" id="verify-code">
                <div class="form-container">
                    <div class="row">
                        <div class="input-field col s12">
                            <input id="inputVarifyCode" type="text" class="validate" name="phone">
                            <label for="phone">کد تایید</label>
                        </div>
                    </div>

                    <center>
                        <button class="btn waves-effect waves-light teal" type="submit" name="action">بررسی کد </button>
                    </center>
                </div>
            </form>
            <?php if (!is_user_logged_in()): ?>
                <form class="col s12 " method="post" id="regPhone">
                    <div class="form-container">
                        <h3 class="teal-text">خوش آمدید</h3>
                        <div class="row">
                            <div class="input-field col s12">
                                <input id="phoneSend" type="tel" class="validate" name="phone">
                                <label for="phone">موبایل</label>
                            </div>
                        </div>

                        <center>
                            <button class="btn waves-effect waves-light teal" type="submit" name="action" disabled>ارسال کد تایید</button>
                        </center>
                    </div>
                </form>
            <?php else: ?>
                <div class="login-alert">شما اکنون عضو سایت هستید
                    <a href="<?php echo home_url('panel') ?>">ورود به پیشخوان</a>
                </div>
            <?php endif ?>
            <form class="col s12" method="post" id="registrationform" class="registrationform">
                <div class="form-container">
                    <h3 class="teal-text">خوش آمدید</h3>
                    <div class="row">
                        <div class="input-field col s6">
                            <input id="last_name" type="text" class="validate" name="last_name">
                            <label for="last_name">نام خانوادگی</label>
                        </div>
                        <div class="input-field col s6">
                            <input id="first_name" type="text" class="validate" name="first_name">
                            <label for="first_name">نام</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s6 lesson-select">
                            <select id="lesson" class="register-select " name="lesson">
                                <option value="0">انتخاب درس</option>
                                <option value="math">ریاضی</option>
                                <option value="science">علوم تجربی</option>
                                <option value="english">انگلیسی</option>
                                <!-- 
                                <option value="writing-skills">آموزش مهارت های نوشتاری (نگارش و انشا)</option>
                                <option value="social-studies">مطالعات اجتماعی</option>
                                <option value="thinking-shared">تفکر و سبک زندگی مشترک دختران و پسران</option>
                                <option value="thinking-girls">تفکر و سبک زندگی (دختران)</option>
                                <option value="culture-art">فرهنگ و هنر</option>
                                <option value="heavenly-messages">پیام های آسمانی</option>
                                <option value="heavenly-messages-sunni">ضمیمه پیام های آسمانی (ویژه اهل سنت)</option>
                                <option value="english-workbook">کتاب کار انگلیسی</option>
                                <option value="quran-teaching">آموزش قرآن</option>
                                <option value="arabic">عربی</option>
                                <option value="work-technology">کار و فناوری</option>
                                <option value="persian">فارسی</option> 
                                <option value="thinking-boys">تفکر و سبک زندگی ویژه پسران</option> 
                                <option value="heavenly-messages-sunni">پیام های آسمان ویژه اهل سنت</option>  -->
                            </select>
                        </div>
                        <div class="input-field col s6 education-base-select">
                            <select id="education-base" class="register-select " name="education-base">
                                <option value="0">انتخاب پایه</option>
                                <option value="seventh">هفتم</option>
                                <option value="eighth">هشتم</option>
                                <option value="ninth">نهم</option>
                            </select>
                        </div>
                        <div class="input-field col s12">
                            <select id="role" class="register-select role-select" name="role">
                                <option value="0">نقش</option>
                                <option value="teacher">معلم</option>
                                <option value="student">دانش آموز</option>
                            </select>
                        </div>

                    </div>

                    <div class="row">
                        <div class=" gender-radio-btn-container col s12">
                            <h6>جنسیت</h6>
                            <label for="gender-male">
                                <input id="gender-male" type="radio" class="gender" name="gender" value="male">
                                مرد
                            </label>
                            <label for="gender-female">
                                <input id="gender-female" type="radio" class="gender" name="gender" value="female">
                                زن
                            </label>


                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <input id="email-reg" type="email" class="validate" name="email">
                            <label for="email">ایمیل</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="input-field col s12">
                            <input id="password" type="password" class="validate" name="password">
                            <label for="password">رمز عبور</label>
                        </div>
                    </div>
                    <div class="row">

                        <center>
                            <button class="btn waves-effect waves-light teal" type="submit" name="action">ثبت نام</button>
                        </center>
                    </div>
            </form>
        </div>
    </div>
</body>
<footer>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/js/materialize.min.js"></script>
    <script src="<?php echo esc_url(get_template_directory_uri() . '/assets/js/ajax/register-login-users.js'); ?>" id="ajax-handle"></script>
    <script src="<?php echo esc_url(get_template_directory_uri() . '/assets/js/register-form.js'); ?>"></script>
    <script>
        document.addEventListener('click', () => {
            let formRegister = document.getElementById('regPhone')
            formRegister.style.display = 'block'
        })
    </script>

</footer>

</html>