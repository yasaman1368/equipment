<?php

use function PHPSTORM_META\type;

if (!is_user_logged_in()) wp_redirect(home_url('panel/login'));  ?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پُرس نگار</title>
    <link rel="icon" type="image/x-icon" sizes="180x180" href="<?php echo  get_template_directory_uri() ?>/assets/images/PS.png">
    <link rel="stylesheet" href="<?php echo YAS_SCP_URL_STYLE . 'style.css' ?>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

</head>

<body>
    <div class="user-header-dashboard">
        <div class="col-12 display-name-content">
            <?php $current_user = wp_get_current_user(); ?>
            <div class="text-muted fs-6 fw-light">
                سلام
            </div>
            <div class="user-wellcome">

                <h4><?php echo $current_user->display_name ?><span>👋</span></h4>
                <div class="">
                    <div class="emoji-student">
                        <img src="<?php echo get_template_directory_uri() . '/assets/images/theme/student-son.jpg'  ?>" alt="">
                    </div>
                    <div class=" btn-logout">
                        <a href="<?php echo wp_logout_url(home_url()) ?>">
                            <i class="bi bi-x-circle-fill"></i>
                        </a>
                    </div>
                    <?php $current_user = wp_get_current_user();

                    if (isset($current_user->roles) && in_array('subscriber', $current_user->roles)):

                        $user_id = (int) $current_user->ID;

                        $point = get_user_meta($user_id, '_score_points', true);

                    ?>
                        <div class="position-relative star-rating-student ">
                            <span><?php echo $point ? $point : 0

                                    ?></span>
                            <i class="bi bi-star-fill  fs-3 text-warning "></i>
                        </div>
                    <?php endif
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="container default-container">
        <?php require $view ?>
    </div>
    </div>

    <div class="bottom-menu">
        <div class="home-icon  rounded-4 m-2  text-info  panel">
            <a href="<?php echo home_url('panel') ?>">
                <span class="icon"><i class="bi bi-house "></i></span>
                <span class="name"> خانه</span>
            </a>
        </div>
        <!-- <div class="exams-icon  rounded-4 m-2  text-info exams">
            <a href="<?php // echo home_url('panel/exams') 
                        ?>">
                <span class="icon"><i class="bi bi-journal-text"></i></span>
                <span class="name"> آزمون ها</span>
            </a>
        </div> -->
        <div class="report-cart-icon  rounded-4 m-2  text-info report">
            <a href="<?php echo home_url('panel/report') ?>">
                <span class="icon"><i class="bi bi-card-checklist"></i></span>
                <span class="name">کارنامه آزمون ها</span>
            </a>
        </div>
        <?php $user_roles = wp_get_current_user()->roles;
        if (!in_array('subscriber', $user_roles)):
        ?>
            <div class="book-icon  rounded-4 m-2  text-info">
                <a href="<?php echo home_url('panel/classroomoffice') ?>">
                    <span class="icon"><i class="bi bi-book  "></i></span>
                    <span class="name"> دفتر کلاسی</span>
                </a>
            </div>
        <?php endif ?>
        <div class="person-icon  rounded-4 m-2  text-info user">
            <a href="<?php echo home_url('panel/user') ?>">
                <span class="icon"><i class="bi bi-person  "></i></span>
                <span class="name"> ویرایش</span>
            </a>
        </div>

        <!-- <div class="bell-icon  rounded-4 m-2  text-info">
            <span class="icon"><i class="bi bi-bell  "></i></span>
            <span class="name"> اخبار</span>
        </div>  -->
    </div>
</body>
<footer>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo esc_url(get_template_directory_uri() . '/assets/js/ajax/exam.js') ?>"></script>

    <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
    <!-- classroom -->
    <?php
    $parts = explode('/', $_SERVER['REQUEST_URI']);
    ?>
    <?php if (end($parts) === 'classroomoffice'): ?>
        <script src="<?php echo get_template_directory_uri() . '/assets/js/ajax/classroom.js' ?>"></script>
    <?php endif; ?>
</footer>

</html>