<?php
$current_user = wp_get_current_user();;
?>

<div
    class="tab-pane "
    id="account-setting"
    role="tabpanel"
    aria-labelledby="account-tab">
    <div class="container bg-light mt-3 rounded p-3">
        <form id="account-setting-form">

            <div class="mb-3">
                <label class="form-label">نام کاربری</label>
                <input type="text" class="form-control" name="username-modal" value="<?php echo esc_attr($current_user->user_login); ?>" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">نام و نام خانوادگی</label>
                <input type="text" class="form-control" name="display-name-modal" value="<?php echo esc_attr($current_user->display_name); ?>" placeholder="نام و نام خانوادگی را وارد کنید">
            </div>
            <div class="mb-3">
                <label class="form-label">رمز عبور جدید</label>
                <input type="password" class="form-control" name="new-password-modal" placeholder="رمز عبور جدید را وارد کنید">
            </div>
            <div class="mb-3">
                <label class="form-label">تکرار رمز عبور جدید</label>
                <input type="password" class="form-control" name="confirm-password-modal" placeholder="تکرار رمز عبور جدید را وارد کنید">
            </div>
            <div class="mb-3">
                <label class="form-label">ایمیل</label>
                <input type="email" class="form-control" name="email-modal" value="<?php echo esc_attr($current_user->user_email); ?>" placeholder="ایمیل را وارد کنید">
            </div>
            <!-- Role Selector -->
            <div class="mb-3">
                <label class="form-label">نقش</label>
                <select class="form-select" name="role" disabled>
                    <!-- Add options here -->
                    <?php
                    $user_role = get_user_meta($current_user->ID, '_role', true); // Assuming roles are stored as an array
                    // Generate HTML for the role selection
                    $roles = [
                        '' => 'مدیر ارشد',
                        'manager' => 'مدیر',
                        'helper' => 'معاون',
                        'user' => 'کاربر'
                    ];

                    foreach ($roles as $value => $label) {
                        $selected = ($user_role === $value) ? 'selected' : '';
                        echo "<option value=\"$value\" $selected>$label</option>";
                    }
                    ?>
                </select>
            </div>
            <input type="hidden" name="user-id" value="<?php echo esc_attr($current_user->ID); ?>">
            <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>
        </form>
    </div>
</div>