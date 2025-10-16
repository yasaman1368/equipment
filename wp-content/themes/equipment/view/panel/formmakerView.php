<?php
/**
 * Form Management System
 * Security and access control
 */
if (!is_manager()) {
    wp_redirect(home_url('panel'));
    exit;
}
/**
 * Render location checkboxes from database
 */
function render_location_checkboxes() {
    global $wpdb;
    $table_name = 'location_supervisors_users';
    $locations = $wpdb->get_col("SELECT location_name FROM {$table_name}");
    
    $output = '';
    foreach ($locations as $location) {
        $value = esc_attr($location);
        $label = esc_html($location);
        $output .= "
        <div class='form-check form-check-inline me-3 mb-2'>
            <input class='form-check-input' type='checkbox' name='locations[]' value='{$value}' id='location-{$value}'>
            <label class='form-check-label' for='location-{$value}'>
                <i class='bi bi-geo-alt me-1'></i>
                {$label}
            </label>
        </div>";
    }
    
    return $output;
}
?>

<div class="container-fluid">
    <!-- Header Section -->
    <section class="form-management-header mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-body bg-gradient-primary text-white rounded-3">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="card-title h3 mb-2 text-dark">
                            <i class="bi bi-clipboard-data me-2"></i>
                            سیستم مدیریت فرم‌ها
                        </h1>
                        <p class="card-text opacity-75 text-dark">ایجاد و مدیریت فرم‌های سفارشی</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <button class="btn btn-light btn-lg me-2" id="add-new-form" 
                                data-bs-toggle="modal" data-bs-target="#modal-add-form-name">
                            <i class="bi bi-plus-circle me-1"></i>
                            فرم جدید
                        </button>
                        <button class="btn btn-outline-light btn-lg text-dark" id="show-forms">
                            <i class="bi bi-grid-3x3-gap me-1"></i>
                            نمایش فرم‌ها
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Form Builder Section -->
    <section class="form-builder-section d-none" id="main-container-form">
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent py-3">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-tools text-primary me-2"></i>
                            سازنده فرم
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Form Meta Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-meta-card p-3 bg-light rounded-3">
                                    <label class="form-label text-muted small">نام فرم</label>
                                    <h4 class="text-primary mb-0" id="placeholder-form-name">
                                        <i class="bi bi-card-heading me-2"></i>
                                        <span class="placeholder-text">نام فرم انتخاب نشده</span>
                                    </h4>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="locations-card p-3 bg-light rounded-3">
                                    <label class="form-label text-muted small">
                                        <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                        موقعیت‌های مکانی
                                    </label>
                                    <div class="locations-container mt-2" id="locations-container">
                                        <?php echo render_location_checkboxes(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Fields Container -->
                        <div class="form-fields-container">
                            <div id="form-container-builder" class="row g-3" data-custom-form-id="10">
                                <!-- Equipment ID Field (Fixed) -->
                                <div class="col-md-6">
                                    <div class="field-card card border">
                                        <div class="card-body">
                                            <label for="equipment-id" class="form-label fw-semibold">
                                                <i class="bi bi-upc-scan me-2"></i>
                                                سریال تجهیز
                                            </label>
                                            <input type="text" name="equipment-id" class="form-control" 
                                                   id="equipment-id" placeholder="سریال تجهیز را وارد کنید">
                                        </div>
                                    </div>
                                </div>
                                <!-- Dynamic fields will be added here -->
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="form-actions mt-4">
                            <div class="row">
                                <div class="col-12">
                                    <button type="button" class="btn btn-primary btn-lg me-2" 
                                            data-bs-toggle="modal" data-bs-target="#modal-add-form">
                                        <i class="bi bi-plus-circle me-1"></i>
                                        افزودن فیلد جدید
                                    </button>
                                    <button type="button" id="save-form-btn" 
                                            class="btn btn-success btn-lg">
                                        <i class="bi bi-check-circle me-1"></i>
                                        ذخیره فرم
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Saved Forms Section -->
    <section class="saved-forms-section mt-4 " id="show-saved-forms">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent py-3">
                <h5 class="card-title mb-0">
                    <i class="bi bi-archive text-success me-2"></i>
                    فرم‌های ذخیره شده
                </h5>
            </div>
            <div class="card-body">
                <!-- Form Selector -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-selector-card">
                            <label for="form-selector" class="form-label fw-semibold">
                                انتخاب فرم
                            </label>
                            <select class="form-select form-select-lg" name="form-selector" id="form-selector">
                                <option value="" selected>-- لطفاً یک فرم انتخاب کنید --</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Selected Form Details -->
                <div class="selected-form-details" id="show-header-forms">
                    <div class="form-detail-card card bg-light border-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-2">نام فرم انتخاب شده</h6>
                                    <h4 class="text-primary" id="show-form-name-check">-</h4>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-2">موقعیت‌های مرتبط</h6>
                                    <div id="locations-display" class="locations-tags"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Fields Display -->
                <div class="form-preview mt-4">
                    <div class="row g-3" id="display-form"></div>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- Modals -->
<?php  get_template_part( 'partials/form-maker/modals' ) ; ?>