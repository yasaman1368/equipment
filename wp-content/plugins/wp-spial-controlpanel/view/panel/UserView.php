<?php
$current_user = wp_get_current_user();
$user_id = $current_user->ID;
$user_phone = get_user_meta($user_id, '_math_user_phone', true);

?>
<div class="col-12 pl">
    <div class="profile-content">
        <div class="profile-stats">
            <div class="profile-address">
                <div class="middle-container">
                    <form class="form-user-data-edit form-checkout bg-light p-4 rounded"
                        data-url="<?php echo admin_url('admin-ajax.php') ?>"
                        data-nonce="<?php echo wp_create_nonce('form-edit-nonce') ?>">
                        <h5 class="mb-4">ویرایش اطلاعات کاربری:</h5>
                        <div class="form-checkout-row form-edit-user-data">
                            <div class="row">
                                <div class="filed-data col-sm-6">

                                    <label for="displayName"> نام و نام خانوادگی
                                    </label>
                                    <input type="text" name="displayName" id="displayName"
                                        class="input-namelast-checkout form-control" value="<?php echo esc_html($current_user->display_name) ?>">
                                </div>
                                <div class="filed-data col-sm-6">

                                    <label for="niceName"> نام کاربری
                                    </label>
                                    <input type="text" name="niceName" id="niceName"
                                        value="<?php echo esc_html($current_user->user_nicename) ?>"
                                        class="input-namelast-checkout form-control">
                                </div>
                                <div class="filed-data col-sm-6">

                                    <label for="user-phone">شماره موبایل
                                        </abbr></label>
                                    <input type="text" name="user-phone" id="user-phone"
                                        value="<?php echo esc_html(get_user_meta($user_id, '_math_user_phone', true))  ?>"
                                        class="input-email-checkout form-control">
                                </div>
                                <div class="filed-data col-sm-6">

                                    <label for="email">ایمیل
                                        </abbr></label>
                                    <input type="text" id="email"
                                        class="input-email-checkout form-control"
                                        value="<?php echo esc_html($current_user->user_email) ?>"
                                        disabled>
                                </div>
                                <div class="filed-data col-sm-6">
                                    <label for="old-pass">
                                        رمز عبور قبلی
                                    </label>
                                    <input type="text" name="old-pass" id="old-pass"
                                        class="input-password-checkout form-control">
                                </div>
                                <div class="filed-data col-sm-6">

                                    <label for="new-pass">رمز عبور جدید
                                        </abbr></label>
                                    <input type="text" name="new-pass" id="new-pass"
                                        class="input-password-checkout form-control">
                                </div>
                                <div class="filed-data col-sm-6">

                                    <label for="password">
                                        <label for="repeat-new-pass">
                                            تکرار رمز عبور جدید
                                        </label>
                                        <input type="text" name="repeat-new-pass" id="repeat-new-pass"
                                            class="input-password-checkout form-control">
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="user-data-edit-btn mt-5 btn btn-success">
                                        ثبت تغییرات
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>