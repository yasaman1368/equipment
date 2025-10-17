<?php
$action = $_REQUEST['action'] ?? null;
$form_id = isset($_POST['form_id']) ? intval($_POST['form_id']) : 0;

switch ($action) {
  case 'export_equipments':
    export_equipments();
    break;
  case 'dlExcelFormatter':
    if ($form_id > 0) {
      get_excel_format($form_id);
    }
    break;
  case 'export_equipments_data_from_form':
    if ($form_id > 0) {
      export_equipments_data_from_form($form_id);
    }
    break;
}
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

  <!-- Select2 CSS -->
  <link rel="stylesheet" href='https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css' />




</head>

<body class="bg-secondary-subtle">
  <?php get_template_part('partials/nav/nav') ?>

  <?php require $view; ?>

</body>




<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
<!-- jQuery (latest version) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap JS (latest version) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo get_template_directory_uri() . '/assets/js/app.js' ?>"></script>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- Custom Script -->
<script src="<?php echo get_template_directory_uri() . '/assets/js/workflow.js' ?>"></script>
<script src="<?php echo get_template_directory_uri() . '/assets/js/importExportManager.js' ?>"></script>
<?php if (strpos($_SERVER['REQUEST_URI'], 'formmaker')): ?>
  <script src="<?php echo get_template_directory_uri() ?>/assets/js/manage-form.js"></script>
<?php elseif (strpos($_SERVER['REQUEST_URI'], 'equipmenttracker')): ?>
  <script src="<?php echo get_template_directory_uri() ?>/assets/js/manage-equipment.js"></script>

<?php elseif (strpos($_SERVER['REQUEST_URI'], 'manageuser')): ?>
  <script src="<?php echo get_template_directory_uri() ?>/assets/js/users.js"></script>


<?php endif ?>
