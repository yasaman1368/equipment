 
<!-- Add Field Modal -->
<div class="modal fade" id="modal-add-form" tabindex="-1" aria-labelledby="addFieldModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addFieldModalLabel">
                    <i class="bi bi-plus-circle me-2"></i>
                    افزودن فیلد جدید
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="modal-new-field-form" class="needs-validation" novalidate>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="new-feature-name" class="form-label">
                                <i class="bi bi-tag me-1"></i>
                                نام فیلد
                            </label>
                            <input type="text" class="form-control" name="input-name" 
                                   id="new-feature-name" required placeholder="عنوان فیلد را وارد کنید">
                            <div class="invalid-feedback">لطفاً نام فیلد را وارد کنید</div>
                        </div>
                        <div class="col-md-6">
                            <label for="input-type" class="form-label">
                                <i class="bi bi-list-ul me-1"></i>
                                نوع فیلد
                            </label>
                            <select class="form-select" name="input-type" id="input-type" required>
                                <option value="">-- انتخاب نوع --</option>
                                <option value="text">متن</option>
                                <option value="number">عدد</option>
                                <option value="email">ایمیل</option>
                                <option value="tel">تلفن</option>
                                <option value="select">لیست کشویی</option>
                                <option value="multiselect">انتخاب چندگانه</option>
                                <option value="date">تاریخ</option>
                                <option value="datetime">تاریخ و زمان</option>
                                <option value="time">زمان</option>
                                <option value="checkbox">چک‌باکس</option>
                                <option value="radio">دکمه رادیویی</option>
                                <option value="file">فایل</option>
                                <option value="image">تصویر</option>
                                <option value="textarea">متن چندخطی</option>
                                <option value="geo">موقعیت جغرافیایی</option>
                            </select>
                            <div class="invalid-feedback">لطفاً نوع فیلد را انتخاب کنید</div>
                        </div>
                    </div>

                    <!-- Options Container for Select/Checkbox/Radio -->
                    <div class="options-container mt-3 d-none" id="container-options-modal">
                        <div class="card border">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="bi bi-gear me-1"></i>
                                    گزینه‌های <span id="selected-input"></span>
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="options-list" id="input-option-container"></div>
                                <button type="button" id="add-new-option" 
                                        class="btn btn-outline-primary btn-sm mt-2">
                                    <i class="bi bi-plus me-1"></i>
                                    افزودن گزینه
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Settings -->
                    <div class="field-settings mt-3">
                        <div class="card border">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="bi bi-sliders me-1"></i>
                                    تنظیمات اضافی
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="field-required">
                                            <label class="form-check-label" for="field-required">
                                                فیلد اجباری
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="field-unique">
                                            <label class="form-check-label" for="field-unique">
                                                مقدار یکتا
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    انصراف
                </button>
                <button type="button" id="save-new-field" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>
                    ذخیره فیلد
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add Form Name Modal -->
<div class="modal fade" id="modal-add-form-name" tabindex="-1" aria-labelledby="addFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="addFormModalLabel">
                    <i class="bi bi-folder-plus me-2"></i>
                    ایجاد فرم جدید
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="modal-form-name" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="new-form-name" class="form-label">
                            <i class="bi bi-card-heading me-1"></i>
                            نام فرم
                        </label>
                        <input type="text" class="form-control" name="input-name" 
                               id="new-form-name" required placeholder="نام فرم را وارد کنید">
                        <div class="invalid-feedback">لطفاً نام فرم را وارد کنید</div>
                        <div class="form-text">نامی توصیفی برای فرم خود انتخاب کنید</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    انصراف
                </button>
                <button type="button" id="save-modal-form-name" class="btn btn-success">
                    <i class="bi bi-check-circle me-1"></i>
                    ایجاد فرم
                </button>
            </div>
        </div>
    </div>
</div>