<?php

if (isset($_GET['newform']) && $_GET['newform'] === 'on') {
?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let newFormButton = document.getElementById("add-new-form");
            newFormButton.click();
        })
    </script>

<?php
} elseif (isset($_GET['showforms']) && $_GET['showforms'] === 'on') {
?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let showFormsButton = document.getElementById("show-forms");
            showFormsButton.click();
        })
    </script>
<?php
}
?>



<div class="container">
    <div class="card shadow text-center m-2">

        <div class="card-body bg-secondary rounded text-white">
            <h4 class="card-title">مدیریت فرم ها</h4>
            <button
                class="btn btn-success p-2 m-3"
                id="add-new-form"
                data-bs-toggle="modal"
                data-bs-target="#modal-add-form-name">
                فرم جدید
                <i class="bi bi-folder-plus"></i>
            </button>
            <button
                class="btn btn-success p-2 m-3"
                id="show-forms">
                نمایش فرم ها
                <i class="bi bi-card-checklist"></i>
            </button>

        </div>
    </div>
</div>

<div class="container p-2 bg-light rounded d-none" id="main-container-form">
    <div id="form-container-builder" class="row" data-custom-form-id="10">
        <div class="col-sm-12 ">
            <div
                class="card text-dark bg-warning mb-2">

                <div class="card-body">
                    <p class="card-text">نام فرم:</p>
                    <h4 class="card-title" id="placeholder-form-name"></h4>
                </div>

                <div class="col-sm-12  p-2  ">
                    <span class="mb-3 multi-location-flex">
                        <label for="select-location" class="form-label mt-3">
                            <i class="bi bi-geo-alt-fill text-danger"></i> موقعیت های مکانی
                        </label>

                        <?php $locations = get_option('_locations');

                        foreach ($locations as $location) {
                            $value = esc_attr($location);
                            $label = esc_html($location);
                            echo "
                     <p class='p-2 mb-1 d-inline-block'>
                         <input class='form-check-input' type='checkbox' name='locations[]' value='{$value}' id='location-{$value}'>
                         <label class='form-check-label' for='location-{$value}'>{$label}</label>
                      </p> ";
                        } ?>

                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 ">
        <div class="mb-3">
            <label for="" class="form-label">سریال تجهیز</label>
            <input
                type="text"
                name="equipment-id"
                class="form-control"
                id="equipment-id"
                aria-describedby="helpId"
                placeholder="" />

        </div>
    </div>



    <div class="row">
        <div class="d-grid gap-2 w-auto m-3">
            <button
                type="button"
                name=""
                id=""
                class="btn btn-primary w-auto"
                data-bs-toggle="modal"
                data-bs-target="#modal-add-form">
                افزودن ویژگی جدید
            </button>
        </div>
    </div>
    <div class="row">
        <button
            type="button"
            id="save-form-btn"
            class="btn btn-success mt-5 d-block mx-auto  w-25">
            ذخیره فرم
        </button>
    </div>
</div>

<!-- show forms -->
<div class="container d-none bg-secondary rounded" id="show-saved-forms">
    <div class="mb-3 p-2">
        <label for="" class="form-label">لیست فرم ها</label>
        <select
            class="form-select form-select-lg"
            name="form-selector"
            id="form-selector">
            <option selected>--انتخاب فرم--</option>

        </select>
    </div>
    <div class="container  d-none" id="show-header-forms">
        <div class="col-sm-12 " id="show-form-name-div">
            <div
                class="my-1 p-2 text-white bg-secondary">

                <div class="body">
                    <h6 class="text-dark">نام فرم</h6>
                    <h4 class="card-title" id="show-form-name-check"></h4>

                </div>
            </div>

        </div>

    </div>
    <div class="row mt-2 p-2" id="display-form">

    </div>



</div>


<!-- modal for add new feild to form -->

<div
    class="modal fade"
    id="modal-add-form"
    tabindex="-1"
    role="dialog"
    aria-labelledby="modalTitleId"
    aria-hidden="true"
    data-backdrop="static"
    data-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content custom-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleId">افزودن ویژگی جدید</h5>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="containe r-fluid">
                    <div class="mb-3">
                        <form action="" id="modal-form">
                            <label for="new-feature-name" class="form-label">
                                <i class="bi bi-person"></i> نام ویژگی
                            </label>
                            <input
                                type="text"
                                class="form-control"
                                name="input-name"
                                id="new-feature-name"
                                aria-describedby="helpId"
                                placeholder="" />
                            <label for="input-type" class="form-label">
                                <i class="bi bi-list"></i> نوع اطلاعات ورودی
                            </label>
                            <select
                                class="form-select form-select-lg"
                                name="input-type"
                                id="input-type">
                                <option value="">-- انتخاب کنید --</option>
                                <option value="text">متن</option>
                                <option value="number">شماره</option>
                                <option value="select">لیست کشویی</option>
                                <option value="date">تاریخ</option>
                                <option value="checkbox">چک باکس</option>
                                <option value="radio">گزینه‌ی رادیویی</option>
                                <option value="time">زمان</option>
                                <option value="file">تصویر</option>
                                <option value="geo">موقعیت جغرافیایی</option>
                            </select>
                            <div class="container-options text-center m-2 mt-3 d-none" id="container-options-modal">
                                <div class="bg-warning p-1 text-center rounded m-2">
                                    <p>موارد
                                        <span id="selected-input"></span>
                                        را وارد کنید
                                    </p>
                                </div>
                                <div class="options" id="input-option-container">

                                </div>
                                <a href="javascript:void(0)" id="add-new-option" class=" btn btn-success add-option m-3 p-2">
                                    افزودن گزینه دیگر
                                    <i class="bi bi-plus-lg"></i>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">
                    بستن
                </button>
                <button type="submit" id="save-modal" class="btn btn-primary">
                    ذخیره
                </button>

            </div>
            </form>
        </div>
    </div>
</div>
<!-- modal for insert form name  -->
<div
    class="modal fade"
    id="modal-add-form-name"
    tabindex="-1"
    role="dialog"
    aria-labelledby="modalTitleId"
    aria-hidden="true"
    data-backdrop="static"
    data-keyboard="false">
    >
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content custom-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleId">نام فرم جدید </h5>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="containe r-fluid">
                    <div class="mb-3">
                        <form action="" id="modal-form-name">

                            <label for="new-feature-name" class="form-label">

                                <i class="bi bi-list"></i> نام فرم
                            </label>
                            <input
                                type="text"
                                class="form-control"
                                name="input-name"
                                id="new-form-name"
                                aria-describedby="helpId"
                                placeholder="" />

                        </form>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">
                    بستن
                </button>
                <button type="submit" id="save-modal-form-name" class="btn btn-primary">
                    ذخیره
                </button>

            </div>

        </div>
    </div>
</div>