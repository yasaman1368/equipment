<!-- Nav tabs -->
<ul class="nav nav-tabs " id="myTab" role="tablist">

    <li class="nav-item" role="presentation">
        <button
            class="nav-link active"
            id="manage-users-tab"
            data-bs-toggle="tab"
            data-bs-target="#manage-users"
            type="button"
            role="tab"
            aria-controls="manage-users"
            aria-selected="false">
            مدیریت کابران
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button
            class="nav-link"
            id="display-users-tab"
            data-bs-toggle="tab"
            data-bs-target="#display-users"
            type="button"
            role="tab"
            aria-controls="display-users"
            aria-selected="false">
            نمایش کاربران
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button
            class="nav-link "
            id="account-setting-tab"
            data-bs-toggle="tab"
            data-bs-target="#account-setting"
            type="button"
            role="tab"
            aria-controls="account-setting"
            aria-selected="true">
            تنظیمات حساب
        </button>
    </li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
    <!-- account setting -->
    <?php get_template_part('partials/manage-users/account-setting') ?>
    <!-- manage users -->
    <?php get_template_part('partials/manage-users/manage-users') ?>
    <!-- display users -->
    <?php get_template_part('partials/manage-users/display-users') ?>

</div>