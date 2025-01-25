<?php
function show_members()
{

    global $wpdb;
    $table = $wpdb->prefix   . 'fajr_qoran';
    $members = $wpdb->get_results("SELECT  * FROM $table ");

?>

    <!DOCTYPE html>
    <html lang="fa" dir="rtl">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php wp_title(); ?></title>
        <link href="<?php echo get_template_directory_uri() ?>/assets/images/PS.png" rel="shortcut icon" />
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" integrity="sha384-dpuaG1suU0eT09tx5plTaGMLBsfDLzUCCUXOY2j/LSvXYuG6Bqs43ALlhIqAJVRb" crossorigin="anonymous">
        <!-- Bootstrap Icons -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    </head>

    <body class="bg-light">
        <?php if ($members): ?>
            <div class="container mt-5">
                <h2 class="text-center">جدول ثبت نام مسابقات قرآنی</h2>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>نام</th>
                                <th>نام خانوادگی</th>
                                <th>کد ملی</th>
                                <th>شماره موبایل</th>
                                <th>جنسیت</th>
                                <th>گروه سنی</th>
                                <th>رشته مسابقه</th>
                                <th>شهر</th>
                                <th>آدرس</th>
                                <th> زمان ثبت نام</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;

                            foreach ($members as $member):
                                $age_group = $member->age;
                                $age_group = $age_group === 'adults' ? 'بزرگسال' : ($age_group === 'teenagers' ? 'نوجوان' : 'نونهال');
                            ?>
                                <tr>
                                    <td> <?php echo $i++ ?></td>
                                    <td><?= htmlspecialchars($member->name) ?></td>
                                    <td><?= htmlspecialchars($member->family) ?></td>
                                    <td><?= htmlspecialchars($member->national_num) ?></td>
                                    <td><?= htmlspecialchars($member->phone) ?></td>
                                    <td><?= htmlspecialchars($member->gender === 'male' ? 'مرد' : 'زن') ?></td>
                                    <td><?= htmlspecialchars($age_group) ?></td>
                                    <td><?= htmlspecialchars($member->field) ?></td>
                                    <td><?= htmlspecialchars($member->city) ?></td>
                                    <td><?= htmlspecialchars($member->address) ?></td>
                                    <td><?= htmlspecialchars($member->create_at) ?></td>
                                </tr>

                            <?php endforeach; ?>

                            <!-- می‌توانید ردیف‌های بیشتری اضافه کنید -->
                        </tbody>
                    </table>
                </div>
            </div>

        <?php else :  ?>
            <div class="alert alert-info p-3 mt-5 fw-bolder shadow mx-auto w-75 text-center">
                تاکنون هیچ ثبت نامی انجام نشده است!!!
            </div>
        <?php endif ?>
    </body>

    </html>

<?php
}
add_shortcode('show-members', 'show_members');
?>