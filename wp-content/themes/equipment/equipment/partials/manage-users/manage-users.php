<div
    class="tab-pane active"
    id="manage-users"
    role="tabpanel"
    aria-labelledby="manage-users-tab">
    <div class="container-fluid">
        <div class="row g-2 p-2">
            <div class="col-md-6 col-lg-4">
                <div
                    class="card text-white bg-light">
                    <div class="card-body">
                        <h4 class="text-center text-dark mb-4">افزودن کاربر جدید</h4>
                        <?php get_template_part('partials/manage-users/_add-new-user-form') ?>

                    </div>
                </div>
            </div>

            <!-- <div class="col-md-6 col-lg-4">
                <div
                    class="card text-white bg-secondary">
                    <div class="card-body">
                        <h4 class="card-title">Title</h4>
                        <p class="card-text">Text</p>
                    </div>
                </div>
            </div> -->
        </div>
    </div>
</div>

<!-- ------------------------------------- -->


<!-- Modal -->
<div class="modal fade " id="userManagementModal" tabindex="-1" aria-labelledby="userManagementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-light">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="userManagementModalLabel">مدیریت حساب <span class="text-success display-name-modal"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="بستن"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body" id="userManagementModalDiv">
                <!-- Tabs for Sections -->
                <ul class="nav nav-tabs" id="userManagementTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="delete-user-tab" data-bs-toggle="tab" data-bs-target="#delete-user" type="button" role="tab" aria-controls="delete-user" aria-selected="true">حذف کاربر</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="edit-user-tab" data-bs-toggle="tab" data-bs-target="#edit-user" type="button" role="tab" aria-controls="edit-user" aria-selected="false">ویرایش کاربر</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="add-capabilities-tab" data-bs-toggle="tab" data-bs-target="#add-capabilities" type="button" role="tab" aria-controls="add-capabilities" aria-selected="false">افزودن نقش/قابلیت</button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="userManagementTabsContent">
                    <!-- Delete User Section -->
                    <div class="tab-pane fade show active" id="delete-user" role="tabpanel" aria-labelledby="delete-user-tab">
                        <div class="mt-3">
                            <p>آیا مطمئن هستید که می‌خواهید <span class="text-danger display-name-modal"></span> را حذف کنید؟</p>
                            <button class="btn btn-danger" id="remove-user-modal">حذف کاربر</button>
                        </div>
                    </div>

                    <!-- Edit User Section -->
                    <div class="tab-pane fade" id="edit-user" role="tabpanel" aria-labelledby="edit-user-tab">
                        <div class="mt-3">
                            <form id="update-user">
                                <?php // wp_nonce_field('update_user_nonce_action', 'update_user_nonce_name') 
                                ?>
                                <div class="mb-3">
                                    <label for="username-modal" class="form-label">نام کاربری</label>
                                    <input type="text" class="form-control" id="username-modal" name="username-modal" disabled>
                                </div>
                                <div class="mb-3">
                                    <label for="display-name-modal" class="form-label">نام و نام خانوادگی</label>
                                    <input type="text" class="form-control" id="display-name-modal" name="display-name-modal" placeholder="نام و نام خانوادگی را وارد کنید">
                                </div>
                                <div class="mb-3">
                                    <label for="new-password-modal" class="form-label">رمز عبور جدید</label>
                                    <input type="password" class="form-control" id="new-password-modal" name="new-password-modal" placeholder="رمز عبور جدید را وارد کنید">
                                </div>
                                <div class="mb-3">
                                    <label for="confirm-password-modal" class="form-label">تکرار رمز عبور جدید</label>
                                    <input type="password" class="form-control" id="confirm-password-modal" name="confirm-password-modal" placeholder="تکرار رمز عبور جدید را وارد کنید">
                                </div>
                                <div class="mb-3">
                                    <label for="email-modal" class="form-label">ایمیل</label>
                                    <input type="email" class="form-control" id="email-modal" name="email-modal" placeholder="ایمیل را وارد کنید">
                                </div>
                                <!-- Role Selector -->
                                <div class="input-group mb-3">
                                    <label class="input-group-text" for="roleSelector">نقش</label>
                                    <select class="form-select" name="role" id="role-selector" required>

                                    </select>

                                </div>
                                <input type="hidden" name="user-id" value="">
                                <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>
                            </form>

                        </div>
                    </div>

                    <!-- Add Capabilities/Roles Section -->
                    <div class="tab-pane fade" id="add-capabilities" role="tabpanel" aria-labelledby="add-capabilities-tab">
                        <div class="mt-3">
                            <form>
                                <div class="mb-3">
                                    <label for="select-role" class="form-label">انتخاب نقش</label>
                                    <select class="form-select" id="select-role" disabled>
                                        <option value="administrator">مدیر</option>
                                        <option value="editor">ویرایشگر</option>
                                        <option value="author">نویسنده</option>
                                        <option value="contributor">مشارکت‌کننده</option>
                                        <option value="subscriber">مشترک</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">انتخاب قابلیت‌ها</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="edit_posts" id="edit_posts" disabled>
                                        <label class="form-check-label" for="edit_posts">
                                            ویرایش
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="publish_posts" id="publish_posts" disabled>
                                        <label class="form-check-label" for="publish_posts">
                                            انتشار
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="delete_posts" id="delete_posts" disabled>
                                        <label class="form-check-label" for="delete_posts">
                                            حذف
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="manage_options" id="manage_options" disabled>
                                        <label class="form-check-label" for="manage_options">
                                            مدیریت تنظیمات
                                        </label>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary" disabled>افزودن نقش/قابلیت</button>
                            </form>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">بستن</button>
            </div>
        </div>
    </div>
</div>