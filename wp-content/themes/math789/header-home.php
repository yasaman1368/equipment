<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="icon" type="image/x-icon" sizes="180x180" href="<?php echo  get_template_directory_uri() ?>/assets/images/PS.png">

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.rtl.min.css"
        integrity="sha384-DOXMLfHhQkvFFp+rWTZwVlPVqdIhpDVYT9csOnHSgWQWPX0v5MCGtjCJbY6ERspU"
        crossorigin="anonymous" />
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link href="<?php echo get_stylesheet_directory_uri() . '/style.css' ?>" rel="stylesheet" />

    <link rel="stylesheet" href="<?php echo get_template_directory_uri() ?>/assets/home/style.css" />
    <link rel="stylesheet" href="<?php echo get_template_directory_uri() ?>/assets/home/responsive.css" />

    <!-- swiper crousel -->

    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />

    <!-- Swiper JS -->
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <title>پُرس نگار</title>
    <style>
        .header {
            flex-shrink: 0;
            /* Prevent the header from shrinking */
        }

        .content {
            flex-grow: 1;
            /* This section will take the remaining space */
        }

        .row {
            flex-shrink: 0;
            /* Prevent rows from shrinking */
        }
    </style>
</head>

<body>
    <div class="container-fluid p-1">
        <div class="row header">
            <div class="col-12">
                <div class="bg-light rounded container-header p-2 shadow">
                    <div class="header-logo-menu">
                        <div class="logo-math789 rounded p-2">
                            <a href="<?php echo home_url() ?>">
                                <span> <img src="<?php echo get_template_directory_uri() ?>/assets/images/789-icon.png" alt="porsnegar-text"> </span>
                            </a>
                            <i class="bi bi-dot dot-animation"></i>
                        </div>
                        <div class="header-menu">
                            <ul>
                                <li class="px-2 fw-normal">
                                    <a href="<?php echo home_url('contact-us') ?>"> تماس با ما </a>
                                </li>
                                <!-- <li class="px-2 fw-normal">
                                    <a href="<?php // echo home_url('blog') 
                                                ?>">وبلاگ </a>
                                </li> -->

                            </ul>
                        </div>
                    </div>
                    <div class="header-login-register">

                        <?php if (!is_user_logged_in()): ?>
                            <a href="<?php echo home_url('panel/login') ?>">
                                <span class="header-login rounded-5 shadow-none">ورود <i class="bi bi-dot dot-animation"></i></span>

                            </a>
                            <a href="<?php echo home_url('panel/login?action=#register') ?>">

                                <span
                                    class="header-sing-in bg-dark rounded-5 text-center text-white shadow-sm">ثبت نام</span>
                            </a>
                        <?php else : ?>

                            <a href="<?php echo home_url('panel') ?>">

                                <span
                                    class="header-sing-in bg-warning rounded-5 text-center text-white shadow-sm">پنل کاربری</span>
                            </a> <?php endif ?>
                    </div>
                </div>
                <?php if (is_user_logged_in()): ?>
                    <div class="bg-secondary m-2 rounded p-2 text-white ">
                        <span class=""><?php echo user_display_name(get_current_user_id())  ?></span> عزیز خوش آمدید
                    </div>
                <?php endif ?>
            </div>
        </div>