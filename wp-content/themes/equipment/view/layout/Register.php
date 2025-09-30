
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

  <!-- Select2 CSS -->
  <link rel="stylesheet" href='https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css' />




</head>
<body>
  
  <?php
require_once $view;
?>
</body>

</html>