<?php

/**
 * Template Name: Weekly exam schedule
 */

$classroom_id = $_GET['classroom_id'] ?? null;

$user_info = get_userdata(get_current_user_id());
$user_roles = $user_info->roles ?? [];
$capability_edite = 'false';
if (in_array('author', $user_roles) || in_array('administrator', $user_roles)) {
    $capability_edite = 'true';
}

?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" sizes="180x180" href="<?php echo  get_template_directory_uri() ?>/assets/images/PS.png">
    <link rel="stylesheet" href="<?php echo YAS_SCP_URL_STYLE . 'style.css' ?>">
    <title>برنامه امتحانات هفتگی</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" integrity="sha384-dpuaG1suU0eT09tx5plTaGMLBsfDLzUCCUXOY2j/LSvXYuG6Bqs43ALlhIqAJVRb" crossorigin="anonymous">
    <style>
        ul li {
            padding: 0 15px;
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
                    <li class="nav-item dropdown">
                        <a
                            class="nav-link dropdown-toggle"
                            href="#"
                            id="dropdownId"
                            data-bs-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false">انتخاب کلاس</a>
                        <div
                            class="dropdown-menu"
                            aria-labelledby="dropdownId"
                            dir="rtl">
                            <a class="dropdown-item" href="<?php echo home_url('weekly-exam-schedule/?classroom_id=18') ?>">هفتم یک</a>
                            <a class="dropdown-item" href="<?php echo home_url('weekly-exam-schedule/?classroom_id=19') ?>">هفتم دو</a>
                            <a class="dropdown-item" href="<?php echo home_url('weekly-exam-schedule/?classroom_id=20') ?>">هشتم یک</a>
                            <a class="dropdown-item" href="<?php echo home_url('weekly-exam-schedule/?classroom_id=21') ?>">هشتم دو</a>
                            <a class="dropdown-item" href="<?php echo home_url('weekly-exam-schedule/?classroom_id=22') ?>">نهم یک</a>
                            <a class="dropdown-item" href="<?php echo home_url('weekly-exam-schedule/?classroom_id=23') ?>">نهم دو</a>

                        </div>
                    </li>
                </ul>

            </div>
        </div>
    </nav>


    <div class="container-sm mt-5">
        <?php if (isset($classroom_id)): ?>
            <h2 class="pt-5 pb-4 text-center ">برنامه امتحانات هفتگی </h2>
            <div id="tables">
                <!-- جداول اینجا قرار می‌گیرند -->
            </div>
            <?php if ($capability_edite === 'true'): ?>
                <div class="d-grid gap-2 col-6 mx-auto">
                    <button id="saveBtn" class="btn btn-primary btn-lg p-2 mb-3">ذخیره</button>
                </div>
            <?php else: ?>

                <div class="d-grid gap-2 col-6 mx-auto">
                    <input type="hidden" id="saveBtn" class="btn btn-primary btn-lg p-2 mb-3">
                </div>
            <?php endif ?>
            <input type="hidden" id="ajax-url" data-ajax-url="<?php echo admin_url('admin-ajax.php') ?>" data-classroom-id="<?php echo $classroom_id ?>">

        <?php else: ?>
            <div class="pt-5">
                <h3 class=" text-center">
                    برنامه امتحانات هفتگی
                </h3>
            </div>
            <div class="row g-2 pt-5">

                <div class="col-sm-6 col-md-4 ">
                    <a href="<?php echo home_url('weekly-exam-schedule/?classroom_id=18') ?>">
                        <div class="card text-center bg-info-subtle shadow-sm  card-calss-name-weekly-schedul">
                            <div class="card-body">
                                <h5 class="text-success card-title">هفتم یک</h5>
                                <p class="card-text text-muted fw-lighter "> نمونه دولتی شهدا</p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-md-4">
                    <div class="card text-center bg-info-subtle shadow-sm    ">
                        <a href="<?php echo home_url('weekly-exam-schedule/?classroom_id=19') ?>">
                            <div class="card-body">
                                <h5 class="text-success card-title">هفتم دو</h5>
                                <p class="card-text text-muted fw-lighter "> نمونه دولتی شهدا</p>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4">
                    <div class="card text-center bg-info-subtle shadow-sm  card-calss-name-weekly-schedul">
                        <a href="<?php echo home_url('weekly-exam-schedule/?classroom_id=20') ?>">
                            <div class="card-body">
                                <h5 class="text-success card-title">هشتم یک</h5>
                                <p class="card-text text-muted fw-lighter "> نمونه دولتی شهدا</p>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4">
                    <div class="card text-center bg-info-subtle shadow-sm  card-calss-name-weekly-schedul">
                        <a href="<?php echo home_url('weekly-exam-schedule/?classroom_id=21') ?>">
                            <div class="card-body">
                                <h5 class="text-success card-title">هشتم دو </h5>
                                <p class="card-text text-muted fw-lighter "> نمونه دولتی شهدا</p>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4">
                    <div class="card text-center bg-info-subtle shadow-sm  card-calss-name-weekly-schedul">
                        <a href="<?php echo home_url('weekly-exam-schedule/?classroom_id=22') ?>">
                            <div class="card-body">
                                <h5 class="text-success card-title">نهم یک</h5>
                                <p class="card-text text-muted fw-lighter "> نمونه دولتی شهدا</p>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4">
                    <div class="card text-center bg-info-subtle shadow-sm  card-calss-name-weekly-schedul">
                        <a href="<?php echo home_url('weekly-exam-schedule/?classroom_id=23') ?>">
                            <div class="card-body">
                                <h5 class="text-success card-title">نهم دو </h5>
                                <p class="card-text text-muted fw-lighter "> نمونه دولتی شهدا</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        <?php endif ?>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const weeks = ['هفته جاری', 'هفته آینده', 'دو هفته آینده'];
            const days = ['شنبه', 'یکشنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه']; // Start week from Saturday
            const classroomId = document.getElementById('ajax-url').dataset.classroomId;
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });

            function generateTables() {
                document.getElementById('tables').innerHTML = '';
                let today = new Date();
                let currentDayIndex = today.getDay(); // Get today's day index (0 = Sunday, 1 = Monday, ..., 6 = Saturday)


                // Adjusting the currentDayIndex to match the new starting day (Saturday)
                currentDayIndex = (currentDayIndex + 1) % 7; // Shift the days to start from Saturday

                weeks.forEach((week, index) => {
                    let table = `<div class="table-responsive  text-center">
                                    <h4 class="text-center p-2 rounded shadow-sm bg-light">${week}</h4>
                                    <table class="table table-striped table-hover text-center  table-info  align-middle table-bordered shadow-sm table-hover">
                                        <thead>
                                            <tr>
                                                <th>روز</th>
                                                <th>نام درس</th>
                                                <th>محتوا</th>
                                                <th>تاریخ آزمون</th>
                                            </tr>
                                        </thead>
                                        <tbody>`;

                    days.forEach((day, dayIndex) => {
                        let examDate = new Date(today); // Create a new date object based on today
                        examDate.setDate(today.getDate() + (dayIndex - currentDayIndex) + (index * 7)); // Adjust date for the correct week and day
                        let formattedDate = examDate.toISOString().split('T')[0];
                        let showFormattedDate = examDate.toLocaleDateString('fa-IR');
                        table += `<tr>
                                    <td>${day}</td>
                                    <td contenteditable="<?php echo $capability_edite ?>" </td>
                                    <td contenteditable="<?php echo $capability_edite ?>" </td>
                                    <td hidden>${formattedDate}</td>
                                    <td>${showFormattedDate}</td>
                                  </tr>`;

                    });
                    table += `</tbody></table></div>`;
                    document.getElementById('tables').innerHTML += table;

                });
            }

            generateTables();

            document.getElementById('saveBtn').addEventListener('click', function() {
                let tables = document.querySelectorAll('#tables table');
                let data = [];

                tables.forEach(table => {
                    let rows = table.querySelectorAll('tbody tr');
                    rows.forEach(row => {
                        let day = row.cells[0].innerText;
                        let subject = row.cells[1].innerText;
                        let content = row.cells[2].innerText;
                        let date = row.cells[3].innerText;

                        data.push({
                            day,
                            subject,
                            content,
                            date,
                        });
                    });
                });

                data.push(classroomId)

                // ارسال داده‌ها به سرور
                $.ajax({
                    url: document.getElementById('ajax-url').dataset.ajaxUrl,
                    method: 'POST',
                    data: {
                        action: 'save_exam_data',
                        data: JSON.stringify(data),

                    },
                    success: function(response) {

                        Toast.fire({
                            icon: 'success',
                            title: 'اطلاعات با موفقیت ذخیره شد!'
                        });
                    },
                    error: function(error) {

                        Toast.fire({
                            icon: 'error',
                            title: 'خطا در ذخیره اطلاعات!'
                        });
                    }
                });
            });

            // بارگذاری اطلاعات ذخیره‌شده
            function loadSavedData() {

                $.ajax({
                    url: document.getElementById('ajax-url').dataset.ajaxUrl,
                    method: 'POST',
                    data: {
                        action: 'load_exam_data',
                    },
                    success: function(response) {
                        let data = response;

                        data.forEach((item, index) => {
                            let subjectContent = JSON.parse(item.content)[classroomId] ?? '';
                            let tableIndex = Math.floor(index / days.length);
                            let rowIndex = index % days.length;
                            let table = document.querySelectorAll('#tables table')[tableIndex];
                            let row = table.querySelectorAll('tbody tr')[rowIndex];
                            row.cells[1].innerText = subjectContent.subject ?? '';
                            row.cells[2].innerText = subjectContent.content ?? '';
                            // row.cells[3].innerText = item.date; // Uncomment if needed
                        });
                    },
                    error: function(error) {
                        //alert('خطا در بارگذاری اطلاعات!');
                    }
                });
            }

            loadSavedData();
        });
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>