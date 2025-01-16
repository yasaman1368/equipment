<?php /* Template Name: contact us */


?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تماس با ما</title>
    <link rel="icon" type="image/x-icon" sizes="180x180" href="http://learnup.local/wp-content/themes/math789/assets/images/PS.png">
    <link rel="stylesheet" href="http://learnup.local/wp-content/plugins/wp-spial-controlpanel/assets/front/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" integrity="sha384-dpuaG1suU0eT09tx5plTaGMLBsfDLzUCCUXOY2j/LSvXYuG6Bqs43ALlhIqAJVRb" crossorigin="anonymous">
    <script src="https://www.google.com/recaptcha/api.js?hl=fa" async defer></script>
    <!-- Your code -->

    <style>
        body {
            margin: 0;
            display: flex;
            width: 100%;
            height: 100vh;
            flex-direction: column;
            font-family: "Poppins", sans-serif;
            /* background-image: linear-gradient(120deg, #f093fb 0%, #f5576c 100%); */
            background: linear-gradient(20deg, rgba(7,173,195,1) 22%, rgba(28,197,219,1) 47%, rgba(126,224,220,1) 78%, rgba(149,232,223,1) 95%, rgba(238,246,177,1) 100%);
        }

        .darksoul-contact {
            margin: auto;
            width: 70%;
            height: 90%;
            display: flex;
            align-items: end;
            justify-content: end;
        }

        .darksoul-contact .darksoul-form {
            margin: auto;
            width: 60%;
            height: 90%;
            background-color: rgba(255, 255, 255, 0.159);
            border-radius: 40px;
            display: flex;
            flex-direction: column;
            backdrop-filter: blur(30px);
            z-index: 12;
        }

        .darksoul-contact .darksoul-form h1 {
            margin: auto;
            font-weight: 500;
            font-family: "Alfa Slab One", serif;
            color: rgb(255, 255, 255);
        }

        .darksoul-contact .darksoul-form .darksoul-input {
            margin: auto;
            width: 60%;
            height: 50px;
            display: flex;
        }

        .darksoul-contact .darksoul-form .darksoul-input .d-label {
            position: absolute;
            margin: auto;
            color: rgb(255, 255, 255);
            font-size: small;
            border-radius: 10px;
            margin-left: 20px;
            margin-top: 17px;
            font-weight: 600;
        }

        .darksoul-contact .darksoul-form .darksoul-input input {
            position: relative;
            margin: auto;
            width: 100%;
            height: 50px;
            outline: none;
            border: none;
            border: 1.3px solid rgb(255, 255, 255);
            border-radius: 10px;
            color: #000000;
            padding-left: 20px;
            background-color: transparent;
            font-size: medium;
            font-family: "Alfa Slab One", serif;
        }

        .darksoul-contact .darksoul-form .darksoul-tarea {
            margin: auto;
            width: 60%;
            height: 100px;
            display: flex;
        }

        .darksoul-contact .darksoul-form .darksoul-tarea .d-tarea-label {
            position: absolute;
            margin: auto;
            color: rgb(255, 255, 255);
            font-size: small;
            background-color: rgba(255, 255, 255, 0);
            margin-left: 20px;
            margin-top: 17px;
            border-radius: 10px;
            font-weight: 600;
        }

        .darksoul-contact .darksoul-form .darksoul-tarea textarea {
            position: relative;
            margin: auto;
            width: 100%;
            height: 100px;
            outline: none;
            border: none;
            border: 1.3px solid rgb(255, 255, 255);
            border-radius: 10px;
            color: #000000;
            padding-top: 20px;
            padding-left: 20px;
            background-color: transparent;
            font-family: "Poppins", sans-serif;
            font-size: small;
            font-weight: 600;
            resize: none;
        }

        .darksoul-contact .darksoul-form input[type="submit"] {
            margin: auto;
            margin-top: 20px;
            width: fit-content;
            height: 40px;
            padding-left: 10px;
            padding-right: 10px;
            background-color: white;
            border: none;
            border-radius: 10px;
            font-weight: 500;
            font-size: medium;
            font-family: "Sigmar", sans-serif;
            cursor: pointer;
        }

        .darksoul-contact .darksoul-form input[type="submit"]:hover {
            background-color: #000000;
            color: white;
            box-shadow: 1px 1px 120px rgba(255, 255, 255, 0.782);
        }

        @media only screen and (max-width: 1000px) {
            body {
                margin: 0;
                display: flex;
                width: 100%;
                height: 100vh;
                flex-direction: column;
                font-family: "Poppins", sans-serif;
                /* background-image: linear-gradient(120deg, #f093fb 0%, #f5576c 100%); */
            }

            .darksoul-contact {
                margin: auto;
                width: 90%;
                height: 90%;
                display: flex;
                align-items: start;
                justify-content: start;
            }

            .darksoul-contact .darksoul-form {
                margin: auto;
                width: 90%;
                height: 90%;
                background-color: rgba(255, 255, 255, 0.159);
                border-radius: 40px;
                display: flex;
                flex-direction: column;
                backdrop-filter: blur(30px);
                z-index: 12;
            }

            .darksoul-contact .darksoul-form h1 {
                margin: auto;
                font-weight: 500;
                font-family: "Alfa Slab One", serif;
                color: rgb(255, 255, 255);
            }

            .darksoul-contact .darksoul-form .darksoul-input {
                margin: auto;
                width: 80%;
                height: 50px;
                display: flex;
            }

            .darksoul-contact .darksoul-form .darksoul-input .d-label {
                position: absolute;
                margin: auto;
                color: rgb(255, 255, 255);
                font-size: small;
                border-radius: 10px;
                margin-left: 20px;
                margin-top: 17px;
                font-weight: 600;
            }

            .darksoul-contact .darksoul-form .darksoul-input input {
                position: relative;
                margin: auto;
                width: 100%;
                height: 50px;
                outline: none;
                border: none;
                border: 1.3px solid rgb(255, 255, 255);
                border-radius: 10px;
                color: #000000;
                padding-left: 20px;
                background-color: transparent;
                font-size: medium;
                font-family: "Alfa Slab One", serif;
            }

            .darksoul-contact .darksoul-form .darksoul-tarea {
                margin: auto;
                width: 80%;
                height: 100px;
                display: flex;
            }

            .darksoul-contact .darksoul-form .darksoul-tarea .d-tarea-label {
                position: absolute;
                margin: auto;
                color: rgb(255, 255, 255);
                font-size: small;
                background-color: rgba(255, 255, 255, 0);
                margin-left: 20px;
                margin-top: 17px;
                border-radius: 10px;
                font-weight: 600;
            }

            .darksoul-contact .darksoul-form .darksoul-tarea textarea {
                position: relative;
                margin: auto;
                width: 100%;
                height: 100px;
                outline: none;
                border: none;
                border: 1.3px solid rgb(255, 255, 255);
                border-radius: 10px;
                color: #000000;
                padding-top: 20px;
                padding-left: 20px;
                background-color: transparent;
                font-family: "Poppins", sans-serif;
                font-size: small;
                font-weight: 600;
                resize: none;
            }

            .darksoul-contact .darksoul-form input[type="submit"] {
                margin: auto;
                margin-top: 20px;
                width: fit-content;
                height: 40px;
                padding-left: 10px;
                padding-right: 10px;
                background-color: white;
                border: none;
                border-radius: 10px;
                font-weight: 500;
                font-size: medium;
                font-family: "Sigmar", sans-serif;
                cursor: pointer;
            }

            .darksoul-contact .darksoul-form input[type="submit"]:hover {
                background-color: #000000;
                color: white;
                box-shadow: 1px 1px 120px rgba(255, 255, 255, 0.782);
            }
        }

        .disclaimer {
            font-family: "Belanosima", sans-serif;
            position: absolute;
            bottom: 0px;
            left: 0;
            margin-left: auto;
            right: 0;
            margin-right: auto;
            width: fit-content;
            color: rgb(255, 255, 255);
            text-align: center;
        }

        .disclaimer a {
            text-decoration: none;
            color: #ffffff;
            font-family: "Kaushan Script", cursive;
            font-weight: 900;
        }

        .disclaimer a:hover {
            text-decoration: overline;
        }

        .g-recaptcha {
            margin: auto;
        }
    </style>
</head>

<body>
    <nav
        class="navbar navbar-expand-lg navbar-light bg-info-subtle fixed-top">
        <div class="container">

            <a class="navbar-brand" href="<?php echo home_url() ?>">
                <span> <img width="80px" height="44px" src="<?php echo get_template_directory_uri() ?>/assets/images/789-icon.png" alt="porsnegar-text"> </span>
            </a>

            <button
                class="navbar-toggler hidden-lg-up"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#collapsibleNavId"
                aria-controls="collapsibleNavId"
                aria-expanded="false"
                aria-label="Toggle navigation"></button>
            <div class="collapse navbar-collapse" id="collapsibleNavId">

                <ul class="navbar-nav me-auto mt-2 mt-lg-0">
                    <?php if (!is_user_logged_in()): ?>
                        <li class="nav-item">
                            <a class="nav-link active" href="<?php echo home_url('panel/login') ?>" aria-current="page">ورود

                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo home_url('panel/login?action=#register') ?>">
                                ثبت نام
                            </a>
                        </li>
                    <?php else : ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo home_url('panel') ?>">
                                پنل کاربری
                            </a>
                        </li>
                    <?php endif ?>

                </ul>

            </div>
        </div>
    </nav>

    <div class="darksoul-contact" id="contact-div" dir="rtl">

        <form class="darksoul-form">
            <h1 id="contact">تماس با ما</h1>
            <div class="darksoul-input">
                <label id="l-name" class="mx-2 d-label"> نام و نام خانوادگی</label>
                <input type="text" name="name" id="name" value="" onfocus="labelmove('name')">
            </div>
            <div class="darksoul-input">
                <label id="l-email" class="mx-2 d-label">آدرس ایمیل</label>
                <input type="email" name="email" id="email" onfocus="labelmove('email')">
            </div>
            <div class="darksoul-input">
                <label id="l-sub" class="mx-2 d-label">موضوع</label>
                <input type="text" name="subject" id="subject" onfocus="labelmove('subject')">
            </div>

            <div class="darksoul-tarea">
                <label id="l-tarea" class="mx-2 d-tarea-label">متن پیام</label>
                <textarea name="message" id="message" onfocus="labelmove('message')"></textarea>
            </div>
            <div class="g-recaptcha" data-sitekey="6LcR2IkqAAAAAEI3NOCz94uCXblhUqMbHBkDanU7"></div>

            <input type="submit" value="ارسال پیام">
            <input type="hidden" id="ajax-url" data-ajax-url="<?php echo admin_url('admin-ajax.php') ?>">
            <input type="hidden" id="nonce" name="nonce" value="<?php echo wp_create_nonce('contact-nonce') ?>">
        </form>
    </div>
    <p class="disclaimer">پرس نگار</p>
    <footer>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
        <script src="<?php echo get_template_directory_uri() . '/assets/js/ajax/contact.js' ?>  "></script>
        <script>
            var lname = document.getElementById("l-name");
            var lemail = document.getElementById("l-email");
            var lsub = document.getElementById("l-sub");
            var ltarea = document.getElementById("l-tarea")

            var textname = document.getElementById("name"); // I Really don't why 'name' is not working
            var email = document.getElementById("email");
            var subject = document.getElementById("subject");
            var message = document.getElementById("message");




            function labelmove(inputname) {
                if (inputname == "name") {
                    lname.style.transition = "all 0.2s";
                    lname.style.marginTop = "-15px";
                    lname.style.marginLeft = "10px";
                    lname.style.zIndex = "10";
                    lname.style.transform = "scale(0.9)";
                    lname.style.padding = "5px 15px 5px 15px";
                    lname.style.backgroundColor = "#000000";
                }

                if (inputname == "email") {
                    lemail.style.transition = "all 0.2s";
                    lemail.style.marginTop = "-15px";
                    lemail.style.marginLeft = "10px";
                    lemail.style.zIndex = "10";
                    lemail.style.transform = "scale(0.9)";
                    lemail.style.padding = "5px 15px 5px 15px";
                    lemail.style.backgroundColor = "#000000";
                }

                if (inputname == "subject") {
                    lsub.style.transition = "all 0.2s";
                    lsub.style.marginTop = "-15px";
                    lsub.style.marginLeft = "10px";
                    lsub.style.zIndex = "10";
                    lsub.style.transform = "scale(0.9)";
                    lsub.style.padding = "5px 15px 5px 15px";
                    lsub.style.backgroundColor = "#000000";
                }

                if (inputname == "message") {
                    ltarea.style.transition = "all 0.2s";
                    ltarea.style.marginTop = "-15px";
                    ltarea.style.marginLeft = "10px";
                    ltarea.style.zIndex = "10";
                    ltarea.style.transform = "scale(0.9)";
                    ltarea.style.padding = "5px 15px 5px 15px";
                    ltarea.style.backgroundColor = "#000000";
                }
            }

            window.onclick = function(event) {
                if (event.target != textname && textname.value.length == 0) {
                    lname.style.transform = "scale(1)";
                    lname.style.transition = "all 0.2s";
                    lname.style.marginTop = "17px";
                    lname.style.marginLeft = "20px";
                    lname.style.zIndex = "0";
                    lname.style.padding = "0px";
                    lname.style.backgroundColor = "transparent";
                }
                if (event.target != email && email.value.length == 0) {
                    lemail.style.transform = "scale(1)";
                    lemail.style.transition = "all 0.2s";
                    lemail.style.marginTop = "17px";
                    lemail.style.marginLeft = "20px";
                    lemail.style.zIndex = "0";
                    lemail.style.padding = "0px";
                    lemail.style.backgroundColor = "transparent";
                }
                if (event.target != subject && subject.value.length == 0) {
                    lsub.style.transform = "scale(1)";
                    lsub.style.transition = "all 0.2s";
                    lsub.style.marginTop = "17px";
                    lsub.style.marginLeft = "20px";
                    lsub.style.zIndex = "0";
                    lsub.style.padding = "0px";
                    lsub.style.backgroundColor = "transparent";
                }
                if (event.target != message && message.value.length == 0) {
                    ltarea.style.transform = "scale(1)";
                    ltarea.style.transition = "all 0.2s";
                    ltarea.style.marginTop = "17px";
                    ltarea.style.marginLeft = "20px";
                    ltarea.style.zIndex = "0";
                    ltarea.style.padding = "0px";
                    ltarea.style.backgroundColor = "transparent";
                }
            }
        </script>
    </footer>
</body>

</html>