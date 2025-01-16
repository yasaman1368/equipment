<?php
if (is_user_logged_in()) {
    wp_redirect(home_url());
}
?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/css/materialize.min.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri() . '/assets/css/login.css' ?>">

    <!-- font cdn -->
    <link rel="preconnect" href="https://fdn.fontcdn.ir" />
    <link rel="preconnect" href="https://v1.fontapi.ir" />
    <link href="https://v1.fontapi.ir/css/Vazir" rel="stylesheet" />
</head>

<body>
    <div class="container white z-depth-2">
        <ul class="tabs teal">
            <li class="tab col s3"><a class="white-text active" href="#recover-password">بازیابی رمز ورود</a></li>
        </ul>

        <div id="recover-password" class="col s12" dir="rtl">

            <?php
            if (isset($_GET['recoverToken']) && !empty($_GET['recoverToken'])):
                $token = '';
                $token = find_recover_token($_GET['recoverToken']);
                if ($token):
            ?>
                    <form class="col s12" method="post" id="set-recover-password-form">
                        <div class="form-container">
                            <h3 class="teal-text">سلام</h3>
                            <div class="row">
                                <div class="input-field col s12">
                                    <input id="user_pass" type="password" class="validate"
                                        name="password">
                                    <label for="password">رمز ورود</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <input id="re_user_pass" type="password" class="validate"
                                        name="re-password">
                                    <label for="password"> تکرار رمز ورود</label>
                                </div>
                            </div>


                            <br>

                            <center>
                                <button class="btn waves-effect waves-light teal" type="submit" name="action">
                                    بازیابی</button>
                            </center>
                        </div>
                        <input type="hidden" name="ajax-url" value="<?php echo admin_url('admin-ajax.php') ?>">
                        <input type="hidden" name="ajax-nonce" value="<?php echo wp_create_nonce('ajax-nonce') ?>">
                        <input type="hidden" name="token" value="<?php echo $token ?>">
                    </form>
                <?php else: ?>
                    <div style="color: red;padding:10px;font-size:x-large;text-align:center">
                        لینک بازیابی کلمه ورود شما معتبر نیست
                    </div>
                <?php endif ?>
            <?php else: ?>
                <form class="col s12" method="post" id="recover-password-form">
                    <div class="form-container">
                        <h3 class="teal-text">سلام</h3>
                        <div class="row">
                            <div class="input-field col s12">
                                <input id="recover-email" type="email" class="validate email"
                                    name="email">
                                <label for="email">ایمیل</label>
                            </div>
                        </div>

                        <br>

                        <center>
                            <button class="btn waves-effect waves-light teal" type="submit" name="action">
                                ارسال لینک بازیابی به ایمیل</button>

                        </center>
                    </div>
                    <input type="hidden" name="ajax-url" value="<?php echo admin_url('admin-ajax.php') ?>">
                    <input type="hidden" name="ajax-nonce" value="<?php echo wp_create_nonce('ajax-nonce') ?>">
                </form>
            <?php endif ?>
        </div>

    </div>
</body>
<footer>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/js/materialize.min.js"></script>
    <script src="<?php echo esc_url(get_template_directory_uri() . '/assets/js/ajax/register-login-users.js'); ?>" id="ajax-handle" defer></script>
    <script src="<?php echo esc_url(get_template_directory_uri() . '/assets/js/register-form.js'); ?>"></script>

</footer>

</html>