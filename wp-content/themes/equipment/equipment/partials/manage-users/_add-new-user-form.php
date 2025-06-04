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

    <!-- Add User Button -->
    <div class="d-grid">
        <button type="submit" name="submit_user" class="btn btn-primary">افزودن کاربر</button>
    </div>
</form>