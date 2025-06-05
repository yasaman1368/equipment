<?php /* Template Name: student-grades */ ?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>کارنامه دانش آموزان</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-secondary" id="main-body">
    <?php

    if (isset($_GET['admin']) && $_GET['admin'] === 'alireza-abedi'): ?>
        <?php
        global $wpdb;
        $class = 'numbers_of_students_db';

        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT first_name, last_name,rate,class_name  FROM $class WHERE view = %s",
            0
        ));

        ?>
        <div
            class="table-responsive">
            <table
                class="table table-primary">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">نام و نام خانوادگی</th>
                        <th scope="col">رتبه در کلاس</th>
                        <th scope="col">نام کلاس</th>

                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    foreach ($results as $item) {
                    ?>
                        <tr class="">
                            <td scope="row"><?php echo $i ?></td>
                            <td><?php echo $item->first_name . ' ' . $item->last_name  ?></td>
                            <td><?php echo $item->rate ?></td>
                            <td><?php echo $item->class_name ?></td>
                        </tr>
                    <?php
                        $i++;
                    } ?>

                </tbody>
            </table>
        </div>

    <?php else: ?>
        <div class="container mt-5">
            <div class="text-center">
                <button type="button" class="btn btn-success btn-lg" id="toggle-class">
                    <i class="fas fa-check"></i> مشاهده نفرات برتر
                </button>
            </div>
        </div>
        <!-- 7-1 -->
        <div class="container mt-5 d-none  top-rate">
            <h4 class="text-center p-2">نفرات برتر کلاس هفتم1</h4>
            <table class="table table-bordered table-striped ">
                <thead class="thead-dark ">
                    <th>رتبه</th>
                    <th>نام</th>
                    <th>نام خانوادگی</th>
                    <th>میانگین نمرات</th>
                </thead>
                <tbody>
                    <tr>
                        <td><i class="bi bi-award rounded bg-warning"></i> 1</td>
                        <td>فرهام</td>
                        <td>ابراهیمی</td>
                        <td>20</td>
                    </tr>
                    <tr>
                        <td><i class="bi bi-award rounded bg-warning"></i> 2</td>
                        <td>علیرضا</td>
                        <td>درویشی</td>
                        <td>19.93</td>
                    </tr>
                    <tr>
                        <td><i class="bi bi-award rounded bg-warning"></i> 3</td>
                        <td>امیرعلی</td>
                        <td>مهمار</td>
                        <td>19.84</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- 7-2 -->
        <div class="container mt-5 d-none   top-rate">
            <h4 class="text-center p-2">نفرات برتر کلاس هفتم 2</h4>
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>رتبه</th>
                        <th>نام</th>
                        <th>نام خانوادگی</th>
                        <th>میانگین نمرات</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><i class="bi bi-award rounded bg-warning"></i> ۱</td>
                        <td>محمد مهدی</td>
                        <td>فولادی</td>
                        <td>19.9333</td>
                    </tr>
                    <tr>
                        <td><i class="bi bi-award rounded bg-warning"></i> ۲</td>
                        <td>محمد جواد</td>
                        <td>حجری</td>
                        <td>19.88</td>
                    </tr>
                    <tr>
                        <td><i class="bi bi-award rounded bg-warning"></i> ۳</td>
                        <td>علی</td>
                        <td>توانا</td>
                        <td>19.8</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- 8-1 -->
        <div class="container mt-5 d-none  top-rate">
            <h4 class="text-center p-2"> نفرات برتر کلاس هشتم 1</h4>
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>رتبه</th>
                        <th>نام</th>
                        <th>نام خانوادگی</th>
                        <th>میانگین نمرات</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><i class="bi bi-award rounded bg-warning"></i> ۱</td>
                        <td>كاوه</td>
                        <td>عابدي</td>
                        <td>19.9</td>
                    </tr>
                    <tr>
                        <td><i class="bi bi-award rounded bg-warning"></i> ۲</td>
                        <td>امیرحافظ</td>
                        <td>سهولي مفرد</td>
                        <td>19.8</td>
                    </tr>
                    <tr>
                        <td><i class="bi bi-award rounded bg-warning"></i> ۳</td>
                        <td>رضا</td>
                        <td>ده جان</td>
                        <td>19.7667</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- 8-2 -->
        <div class="container mt-5 d-none  top-rate">
            <h4 class="text-center p-2">نفرات برتر کلاس هشتم 2</h4>
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>رتبه</th>
                        <th>نام</th>
                        <th>نام خانوادگی</th>
                        <th>میانگین نمرات</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><i class="bi bi-award rounded bg-warning"></i> ۱</td>
                        <td>باربد</td>
                        <td>عابدي</td>
                        <td>20</td>
                    </tr>
                    <tr>
                        <td><i class="bi bi-award rounded bg-warning"></i> ۲</td>
                        <td>كسري</td>
                        <td>مكاريان</td>
                        <td>20</td>
                    </tr>
                    <tr>
                        <td><i class="bi bi-award rounded bg-warning"></i> ۳</td>
                        <td>اميرعلي</td>
                        <td>قديمي فرد</td>
                        <td>19.9667</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- 9-1 -->
        <div class="container mt-5 d-none  top-rate">
            <h4 class="text-center p-2">نفرات برتر کلاس نهم 1</h4>
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>رتبه</th>
                        <th>نام</th>
                        <th>نام خانوادگی</th>
                        <th>میانگین نمرات</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><i class="bi bi-award rounded bg-warning"></i> ۱</td>
                        <td>علي اصغر</td>
                        <td>سهولي</td>
                        <td>19.3333</td>
                    </tr>
                    <tr>
                        <td><i class="bi bi-award rounded bg-warning"></i> ۲</td>
                        <td>محمدصدرا</td>
                        <td>مرادي</td>
                        <td>18.88</td>
                    </tr>
                    <tr>
                        <td><i class="bi bi-award rounded bg-warning"></i> ۳</td>
                        <td>محمدجواد</td>
                        <td>عابدي</td>
                        <td>18.7667</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- 9-2 -->
        <div class="container mt-5 d-none  top-rate">
            <h4 class="text-center p-2">نفرات برتر کلاس نهم 2</h4>
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>رتبه</th>
                        <th>نام</th>
                        <th>نام خانوادگی</th>
                        <th>میانگین نمرات</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><i class="bi bi-award rounded bg-warning"></i> ۱</td>
                        <td>كوروش</td>
                        <td>فولادی</td>
                        <td>19.96</td>
                    </tr>
                    <tr>
                        <td><i class="bi bi-award rounded bg-warning"></i> ۲</td>
                        <td>اميرحسين</td>
                        <td>عباسی</td>
                        <td>19.86</td>
                    </tr>
                    <tr>
                        <td><i class="bi bi-award rounded bg-warning"></i> ۳</td>
                        <td>عليرضا</td>
                        <td>باقری</td>
                        <td>19.76</td>
                    </tr>
                </tbody>
            </table>
        </div>




        <!-- Modal -->
        <div class="modal fade" id="reportCardModal" tabindex="-1" aria-labelledby="reportCardModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="reportCardModalLabel">ورود اطلاعات دانش آموز</h5>
                    </div>
                    <div class="modal-body">
                        <form id="studentForm">

                            <div class="mb-3">
                                <label for="nationalCode" class="form-label">کد ملی</label>
                                <input type="text" class="form-control" id="nationalCode" required>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" form="studentForm" class="btn btn-primary">ثبت</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Results Section -->
        <div id="results" class="container mt-4" style="display:none;">
            <!-- Student Info -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title" id="studentName"></h5>
                    <p class="card-text " id="studentInfo"></p>
                    <div class="bg-info rounded p-2" id="student-rate"></div>
                </div>
            </div>

            <!-- Grades Table -->
            <table class="table table-striped  table-bordered table-responsive">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">درس</th>
                        <th scope="col">نمره</th>
                    </tr>
                </thead>
                <tbody id="resultsBody">
                    <!-- Results will be populated here -->
                </tbody>
                <tfoot>
                    <tr>
                        <td class="text-end"><strong>میانگین نمرات:</strong></td>
                        <td id="averageScore"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    <?php endif ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const myModal = new bootstrap.Modal(document.getElementById('reportCardModal'), {
                backdrop: 'static',
                keyboard: false
            });
            myModal.show();

            const studentForm = document.getElementById('studentForm');
            const resultsBody = document.getElementById('resultsBody');
            const resultsDiv = document.getElementById('results');
            const averageScoreCell = document.getElementById('averageScore');
            const nationalNumInput = document.getElementById('nationalCode');
            const studentName = document.getElementById('studentName');
            const studentInfo = document.getElementById('studentInfo');
            const studentRate = document.getElementById('student-rate');


            nationalNumInput.addEventListener('input', () => {
                nationalNumInput.value = convertNumberToEnglish(nationalNumInput.value.trim());
            });

            function convertNumberToEnglish(number) {
                return number.replace(/[۰-۹]/g, (match) => {
                    return String.fromCharCode(match.charCodeAt(0) - 1728);
                });
            }



            studentForm.addEventListener('submit', function(event) {
                event.preventDefault();

                const nationalCode = nationalNumInput.value;

                if (!nationalCode) {
                    alert('لطفاً کد ملی را وارد کنید.');
                    return;
                }

                fetch('/wp-admin/admin-ajax.php?action=fetch_student_report', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `national_code=${encodeURIComponent(nationalCode)}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            myModal.hide();

                            // نمایش اطلاعات دانش آموز
                            studentName.textContent = `نام دانش آموز: ${data.grades.first_name+' '+data.grades.last_name}`;
                            studentInfo.textContent = `کد ملی: ${nationalCode} | کلاس: ${data.grades.class_name} `;
                            studentRate.textContent = `رتبه در کلاس: ${data.grades.rate==0?'در رتبه بندی قرار نگرفتید':data.grades.rate}`;

                            // نمایش نمرات
                            resultsBody.innerHTML = ''; // پاک کردن نتایج قبلی
                            let totalScore = 0;
                            let count = 0;
                            const subjects = data.grades;
                            let rows = '';

                            for (const subject in subjects) {
                                if (subjects.hasOwnProperty(subject)) {
                                    if (subject !== 'id' && subject !== 'national_code' && subject !== 'class_name' && subject !== 'view' && subject !== 'avrage_score' && subject !== 'rate' && subject !== 'first_name' && subject !== 'last_name') {
                                        let score = subjects[subject];
                                        const subjectName = subject;
                                        if (score === 'غ') {
                                            rows += `<tr><td >${subjectName}</td><td class="bg-danger text-white">غایب</td></tr>`;
                                        }
                                        console.log(score);
                                        if (!isNaN(score)) {
                                            score = parseFloat(subjects[subject]);
                                            let classDanger = '';
                                            if (score < 14) {
                                                classDanger = `class="bg-danger text-white"`
                                            }
                                            rows += `<tr><td >${subjectName}</td><td ${classDanger} >${score}</td></tr>`;
                                            totalScore += score; // جمع نمرات برای محاسبه میانگین
                                            count++; // تعداد درس‌ها
                                        }
                                    }
                                }
                            }

                            resultsBody.innerHTML = rows; // نمایش نمرات
                            const averageScore = (count > 0) ? (totalScore / count).toFixed(2) : 0;
                            averageScoreCell.textContent = averageScore; // نمایش میانگین
                            resultsDiv.style.display = 'block'; // نمایش بخش نتایج
                        } else {
                            alert('دانش آموزی با این اطلاعات یافت نشد.');
                        }
                    })
                    .catch((error) => {
                        console.error('Error:', error);
                    });
            });


            const toggleClass = document.getElementById('toggle-class');
            const containerRate = document.querySelectorAll('.top-rate'); // Cache the selection
            let flag = false; // Initialize the flag outside the event listener
            toggleClass.addEventListener('click', function handleClassDisplay() {
                flag = !flag; // Toggle the flag
                if (flag) {
                    toggleClass.classList.add('btn-warning');
                    toggleClass.classList.remove('btn-success');
                    toggleClass.innerHTML = 'پنهان کردن نفرات برتر';
                } else {
                    toggleClass.classList.remove('btn-warning');
                    toggleClass.classList.add('btn-success');
                    toggleClass.innerHTML = ' مشاهده نفرات برتر';
                }
                containerRate.forEach(div => {
                    div.classList.toggle('d-none', !flag); // Toggle visibility based on flag
                });
            });



        });
    </script>




    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>


</html>