<?php
$user_satus = is_user_logged_in();
print_r($user_satus);
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="<?php echo  get_template_directory_uri() . '/assets/img/gassFavIcon.png' ?>" type="image/png">

    <title>سیستم ردیاب تجهیزات</title>
    <!-- Bootstrap CSS RTL (latest version) -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css"
        rel="stylesheet" />
    <!-- Bootstrap Icons -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="<?php echo get_template_directory_uri() ?>/assets/css/style.css" />
    <!-- sweet alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body,
        html {
            height: 100%;
        }
    </style>
</head>

<body class="bg-secondary-subtle">
    <?php if ($user_satus) {
        wp_redirect(home_url('panel'));
    }
    ?>

    <!-- login form -->
    <div class="container-fluid h-100">
        <div class="row justify-content-center align-items-center h-100">
            <div class="col col-sm-6 col-md-6 col-lg-4 col-xl-3">
                <!-- Add an image above the form -->
                <div class="text-center mb-3">
                    <img src="<?php echo get_template_directory_uri() . '/assets/img/gassFavIcon.png' ?>" class="img-fluid" alt="لوگو">
                </div>
                <form id="loginForm">
                    <?php wp_nonce_field('login-nonce-action', 'login-nonce-name'); ?>
                    <div class="form-group mb-2">
                        <input _ngcontent-c0="" class="form-control form-control-lg" placeholder="نام کاربری" type="text" name="username">
                    </div>
                    <div class="form-group mb-2">
                        <input class="form-control form-control-lg" placeholder="رمز عبور" type="password" name="password">
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="rememberMe" name="rememberMe">
                        <label class="form-check-label" for="rememberMe">مرا به خاطر بسپار</label>
                    </div>
                    <div class="form-group mb-2">
                        <button class="btn btn-info btn-lg btn-block">ورود</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
<footer>
    <script>
        const loginForm = document.getElementById("loginForm");
        loginForm.addEventListener("submit", function(event) {
            event.preventDefault(); // Prevent default form submission

            const formData = new FormData(loginForm);
            const username = formData.get('username')
            const password = formData.get('password')
            // Validate fields
            if (!username || !password) {

                const Toast = Swal.mixin({
                    toast: true,
                    position: "bottom-start",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    }
                });
                Toast.fire({
                    icon: "error",
                    title: "نام کاربری و رمز عبور را وارد کنید."
                });
                return;
            }

            fetch("wp-admin/admin-ajax.php?action=handle_user_login", {
                    method: "POST",
                    body: formData,
                    headers: {
                        'Accept': 'application/json'
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {

                        const Toast = Swal.mixin({
                            toast: true,
                            position: "bottom-start",
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.onmouseenter = Swal.stopTimer;
                                toast.onmouseleave = Swal.resumeTimer;
                            }
                        });
                        Toast.fire({
                            icon: "success",
                            title: data.data.message
                        });

                        window.location.href = data.data.redirect_url;
                    } else {

                        const Toast = Swal.mixin({
                            toast: true,
                            position: "bottom-start",
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.onmouseenter = Swal.stopTimer;
                                toast.onmouseleave = Swal.resumeTimer;
                            }
                        });
                        Toast.fire({
                            icon: "error",
                            title: "نام کاربری یا رمز عبور اشتباه است."
                        });

                    }
                })
                .catch(error => {
                    console.error("خطا در ارسال داده‌ها:", error);
                });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script> -->
    <!-- jQuery (latest version) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS (latest version) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo get_template_directory_uri() . '/assets/js/app.js' ?>"></script>
</footer>

</html>