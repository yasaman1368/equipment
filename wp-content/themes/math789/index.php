<?php get_header()
?>
<?php
$temlat_uri_assets = get_template_directory_uri() . '/assets/';
?>

<body style="position: relative">
  <div class="aniContainer"><span class="aniBlock"></span></div>
  <header>
    <img src="<?php echo $temlat_uri_assets ?>images/black-ecu (1).png" />
  </header>
  <nav class="menu">
    <div class="menu-switch start"><button class="show-list"></button></div>
    <ul class="menu-list fadeout hide">
      <li><button class="alpha">1.Alpha</button></li>
      <li><button class="beta">2.Beta</button></li>
      <li><button class="gamma">3.Gamma</button></li>
      <li><button class="close">خروج</button></li>
    </ul>
  </nav>
  <article>
    <!-- aplpha beta gama sections -->
    <?php get_template_part('partials/index/question-sections') ?>

  </article>
  <figure class="figure"></figure>

  <!-- Button trigger modal -->
  <section
    class="question">
    <span class="bi bi-patch-question textQ" data-bs-toggle="modal"
      data-bs-target="#staticBackdrop "></span>
    <?php if (!is_user_logged_in()): ?>

      <a href="<?php echo site_url('panel/login') ?>">
        <span class="bi bi-door-open textQ"></span>
      </a>
    <?php else: ?>
      <a href="<?php echo wp_logout_url(site_url()) ?>">
        <span class="bi bi-box-arrow-right textQ"></span>
      </a>
    <?php endif ?>
    <span class="bi bi-arrow-left-circle textQ" id="arrowIcon"></span>
    <!-- <span class="bi bi-list top-menu-math789 "></span> -->
  </section>

  <?php get_template_part('partials/index/modals') ?>
</body>
<?php get_footer() ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="<?php echo $temlat_uri_assets . 'js/main.js' ?>">

</script>