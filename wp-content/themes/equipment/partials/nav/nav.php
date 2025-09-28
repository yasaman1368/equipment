<input type="hidden" name="" id="count-notification" value="<?php echo get_workflow_notification_count() ?>">

<nav
  class="navbar navbar-expand-sm navbar-dark bg-dark mb-3 position-absoulte w-100"
  style="height: 56px;"
  dir="ltr"
  lang="en">
  <a href="<?php echo wp_logout_url(home_url()); ?>" class="btn  btn-outline-danger navbar-brand  rounded-5  mt-2 border-0 ml-2">
    <i class=" bi bi-power icon-nav "></i> <!-- Bootstrap icon for logout -->
  </a>
  <a class=" btn  btn-outline-success navbar-brand  rounded-5  mt-2 border-0 ml-2 " href="<?php echo home_url() ?>"><i class="bi bi-house icon-nav"></i></a>
  <a class=" navbar-brand fs-6" href="javascript:viod(0)">
    <?php
    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;
    echo $current_user->display_name . '( ' . $role = eqiupment_get_user_role($user_id) . ' )';
    ?>
  </a>
  <button
    class=" navbar-toggler d-lg-none"
    type="button"
    data-bs-toggle="collapse"
    data-bs-target="#collapsibleNavId"
    aria-controls="collapsibleNavId"
    aria-expanded="false"
    aria-label="Toggle navigation"><i class="bi bi-list"></i></button>
  <div class="collapse navbar-collapse bg-dark z-3" style="height: inherit;" id="collapsibleNavId">
    <ul class="navbar-nav me-auto mt-2 mt-lg-0 p-3" dir="rtl">
      <li class="nav-item">
        <a class="nav-link active href=" <?php echo home_url('panel/equipmenttracker') ?>" aria-current="page"> <span class="visually-hidden">(current)</span></a>
      </li>
      <li class="nav-item">
        <div class="position-relative">

        </div>
        <a class="nav-link " href="<?php echo home_url('panel/notification') ?>">تجهیزات نیازمند بررسی
          <span href="<?php echo home_url('panel/notification') ?>" id="notificationCount" class="bg-danger notificationCount"></span>
        </a>
      </li>

      <li class="nav-item dropdown">
        <a
          class="nav-link "
           href="<?php echo home_url('panel/equipmenttracker') ?>"
          id="equipments-dropdown"
          data-bs-toggle="dropdown"
          aria-haspopup="true"
          aria-expanded="false">تجهیزات</a>
        
         
    

      </li>
      <li class="nav-item">
        <a class="nav-link " href="<?php echo home_url('panel/workflow') ?>">گردش کار</a>
      </li>
      <?php if (is_manager()): ?>
        <li class="nav-item dropdown">
          <a
            class="nav-link dropdown-toggle"
            href="#"
            id="dropdownId"
            data-bs-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false">مدیریت فرم ها</a>
          <div class="dropdown-menu" aria-labelledby="dropdownId">
            <a class="dropdown-item" href="<?php echo home_url('panel/formmaker/?newform=on') ?>">فرم جدید</a>
            <a class="dropdown-item" href="<?php echo home_url('panel/formmaker/?showforms=on') ?>">نمایش فرم ها</a>
          </div>

        </li>


        <li class="nav-item">
          <a class="nav-link " href="<?php echo home_url('panel/importexport') ?>">Import/Export</a>
        </li>
        <li class="nav-item">
          <a class="nav-link " href="<?php echo home_url('panel/manageuser') ?>">مدیریت کاربران</a>
        </li>
      <?php endif; ?>

    </ul>

  </div>

</nav>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const navLinks = document.querySelectorAll('.nav-item .nav-link');
    navLinks.forEach(function(link) {
      link.addEventListener('click', function() {
        navLinks.forEach(function(link) {
          link.classList.remove('active');
        })
        this.classList.add('active');
      })
    })
  })
</script>