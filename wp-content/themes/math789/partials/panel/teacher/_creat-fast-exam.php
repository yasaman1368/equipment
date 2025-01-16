<form id="creat-exam-form" method="post" class="d-none p-3 bg-light rounded">
    <div class="row g-1 justify-content-center align-items-center">
        <div class="col-sm-12 col-md-4 col-lg-3">
            <div class="mb-3">
                <label for="test-type" class="form-label">
                    نام آزمون
                    <span class="fw-lighter text-muted ">(اختیاری)</span>
                </label>
                <input type="text" name="exam_name" id="exam_name" class="form-control" placeholder="مثلا : آزمون مهرماه">
            </div>
        </div>
        <div class="col-sm-12 col-md-4 col-lg-3">
            <div class="mb-3">
                <label for="base-education" class="form-label">پایه</label>
                <select class="form-select" name="base-education" id="base-education">
                    <option value="all">همه</option>
                    <option value="7">هفتم</option>
                    <option value="8">هشتم</option>
                    <option value="9">نهم</option>
                </select>
            </div>
        </div>
        <div class="col-sm-12 col-md-4 col-lg-3">
            <div class="mb-3">
                <label for="lesson" class="form-label">کتاب درسی</label>
                <?php $lesson = get_user_meta(get_current_user_id(), '_math_user_lesson', true); ?>
                <select class="form-select" name="lesson" id="lessons">
                    <option value="all" <?php selected($lesson, '') ?>>انتخاب درس</option>
                    <option value="math" <?php selected($lesson, 'math') ?>>ریاضی</option>
                    <option value="science" <?php selected($lesson, 'science') ?>>علوم تجربی</option>
                    <option value="english" <?php selected($lesson, 'english') ?>>انگلیسی</option>
                </select>
            </div>
        </div>
        <div class="col-12">
            <h6>انتخاب فصل:</h6>
            <div
                class="alert alert-info alert-dismissible fade show">
                <strong>نمایش فصل : </strong>
                برای نمایش فصل ها پایه و درس را انتخاب کنید.
            </div>
            <div
                class="btn-group chekbox-group-section"
                role="group"
                aria-label="Basic checkbox toggle button group  ">
            </div>
        </div>
        <div class="col-sm-12 col-md-6">
            <div class="mb-3">
                <div
                    class="form-outline">
                    <label class="form-label" for="questions-number">تعداد سوالات</label>
                    <input min="5" max="20" type="number" id="questions-number" class="form-control" />
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-6">
            <div class="mb-3">
                <div
                    class="form-outline">
                    <label class="form-label" for="test-time">وقت آزمون<span class="text-muted fw-lighter">(دقیقه)</span></label>
                    <input min="1" max="300" type="number" id="test-time" class="form-control" />
                </div>
            </div>
        </div>
    </div>
    <div class="container-buttons">
        <button type="submit" class="btn btn-danger">پیشنهاد آزمون</button>
        <input type="hidden" name="ajax-nonce" value="<?php echo wp_create_nonce('creat-exam') ?>">
        <input type="hidden" name="ajax-url" value="<?php echo admin_url('admin-ajax.php') ?>">
    </div>
</form>