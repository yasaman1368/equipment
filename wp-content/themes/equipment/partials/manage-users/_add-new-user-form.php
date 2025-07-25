<form id="addUserForm" method="POST">
    <!-- Security Nonce -->
    <?php wp_nonce_field('add_new_user_action', 'add_new_user_nonce'); ?>

    <!-- Full Name Field -->
    <div class="input-group mb-3">
        <input type="text" name="fullname" class="form-control" placeholder="نام و نام خانوادگی" aria-label="نام و نام خانوادگی" required>
    </div>

    <!-- Username or Phone Field -->
    <div class="input-group mb-3">
        <input type="text" name="phone" class="form-control" placeholder="نام کاربری یا شماره موبایل" aria-label="نام کاربری" required>
    </div>

    <!-- Password Field -->
    <div class="input-group mb-3">
        <input type="password" name="password" class="form-control" placeholder="رمز عبور" aria-label="رمز عبور" required>
    </div>

    <!-- Repeat Password Field -->
    <div class="input-group mb-3">
        <input type="password" name="repeat_password" class="form-control" placeholder="تکرار رمز عبور" aria-label="تکرار رمز عبور" required>
    </div>

    <!-- Role Selector -->
    <div class="input-group mb-3">
        <label class="input-group-text" for="roleSelector">نقش</label>
        <select class="form-select" name="role" id="roleSelector" required>
            <option value="" selected>انتخاب نقش...</option>
            <option value="manager">مدیر</option>
            <option value="helper">معاون</option>
            <option value="user">کاربر</option>
        </select>
    </div>


    <button type="button"
        class="btn btn-primary btn-lg"
        data-bs-toggle="modal"
        data-bs-target="#add-location"> افزودن موقعیت جدید </button>
    </div>

    <!-- Add Location Modal -->
    <div class="mb-3">
        <label class="form-label">موقعیت‌های انتخاب شده:</label>
        <div id="user-locations">
            <div class="empty-locations">هنوز موقعیتی انتخاب نشده است</div>
        </div>
        <input type="hidden" name="locations" id="locationsInput" value="[]">
    </div>

    <!-- Add User Button -->
    <div class="d-grid">
        <button type="submit" name="submit_user" class="btn btn-primary">افزودن کاربر</button>
    </div>
</form>



<!-- Modal Body -->
<div
    class="modal fade text-dark"
    id="add-location"
    tabindex="-1"
    data-bs-keyboard="false"
    role="dialog"
    aria-labelledby="modalTitleId"
    aria-hidden="true">
    <div
        class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-sm"
        role="document">
        <div class="modal-content" style="height: auto;">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleId">
                    افزودن موقعیت جدید
                </h5>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body my-2">
                <h6 class="mb-2">لیست موقعیت ها</h6>
                <!-- Location Selector -->
                <?php
                global $wpdb;
                $tabel_name = 'location_supervisors_users';
                $locations = $wpdb->get_col($wpdb->prepare("SELECT location_name FROM {$tabel_name}", ARRAY_A));


                ?>
                <div class="input-group mb-3 ">
                    <select name="selected_location" id="locationSelector" class="location-select">
                        <option value="">موقعیت را انتخاب کنید...</option>
                        <?php foreach ($locations as $location) : ?>
                            <option value="<?php echo esc_attr($location); ?>">
                                <?php echo esc_html($location); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        بستن
                    </button>
                    <button type="button" class="btn btn-success addLocationButton">افزودن موقعیت</button>
                </div>
            </div>
        </div>
    </div>