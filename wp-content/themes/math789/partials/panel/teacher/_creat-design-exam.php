<form id="creat-exam-form_custom" method="post" class="  p-3 bg-light rounded d-none">
    <div class="row g-1 justify-content-center align-items-center">
        <div class="col-sm-12 col-md-4 col-lg-3">
            <div class="mb-3">
                <label for="test-type" class="form-label">
                    نام آزمون
                    <span class="fw-lighter text-muted ">(اختیاری)</span>
                </label>
                <input style="
    max-width: 300px;
" type="text" name="exam_name_custom" id="exam_name_custom" class="form-control " placeholder="مثلا : آزمون مهرماه">
            </div>
        </div>

        <?php
        global $wpdb; // Consider passing $wpdb as a parameter instead

        $user_id = get_current_user_id();
        $classrooms = $wpdb->get_results($wpdb->prepare("SELECT id, classroom_name FROM classrooms WHERE teacher_id=%d", $user_id));

        if ($classrooms) {
            echo '<div class="col-sm-12 col-md-4 col-lg-3">';
            echo '    <div class="mb-3">';
            echo '        <label for="lesson" class="form-label"> انتخاب کلاس<span class="fw-lighter text-muted bg-info rounded p-1">(اختیاری)</span></label>';
            echo '        <select class="form-select " name="classroom" id="classrooms">';
            echo '            <option value="">انتخاب کلاس</option>';

            foreach ($classrooms as $classroom) {
                $classroom_id = esc_attr($classroom->id); // Escape for HTML attribute
                $classroom_name = esc_html($classroom->classroom_name); // Escape for HTML output
                echo "            <option value=\"{$classroom_id}\">{$classroom_name}</option>";
            }

            echo '        </select>';
            echo '    </div>';
            echo '</div>';
        } else {
            echo "<div class='alert alert-info'>کلاسی ایجاد نکرده اید.</div>";
        }
        ?>
        <div class="col-sm-12 col-md-4 col-lg-3">
            <div class="mb-3">
                <label for="base-education" class="form-label">پایه</label>
                <select class="form-select " name="base-education" id="base-education">
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
                <select class="form-select " name="lesson" id="lessons">
                    <option value="all" <?php selected($lesson, '') ?>>انتخاب درس</option>
                    <option value="math" <?php selected($lesson, 'math') ?>>ریاضی</option>
                    <option value="science" <?php selected($lesson, 'science') ?>>علوم تجربی</option>
                    <option value="english" <?php selected($lesson, 'english') ?>>انگلیسی</option>
                </select>
            </div>
        </div>


        <div class="col-sm-12 col-md-4 col-lg-3">
            <div class="mb-3">
                <div
                    class="form-outline">
                    <label class="form-label " for="test-time">وقت آزمون<span class="text-muted fw-lighter">(دقیقه)</span></label>
                    <input style="
    max-width: 300px;
" min="1" max="300" type="number" name="test-time" class="form-control " />
                </div>
            </div>
        </div>
        <hr>
        <div class="question-content-cart">
            <div class="col-sm-12 ">
                <div class="mb-3">
                    <label for="exampleFormControlTextarea1" class="form-label"> متن سوال را وارد کنید </label>
                    <textarea class="form-control question-content" name="question-content" rows="3">
                        <?php ?>
                    </textarea>
                </div>
            </div>

            <div class="input-group">
                <span class="rounded-right input-group-text bg-success">گزینه <span class="m-1"><i class="bi bi-1-square"></i></span></span>
                <textarea class="form-control textarea-options rounded-left" name="option-A" aria-label="With textarea" placeholder="گزینه صحیح را در این قسمت بنویسید..."></textarea>
            </div>
            <div class="input-group">
                <span class="rounded-right input-group-text bg-danger">گزینه <span class="m-1"><i class="bi bi-2-square"></i></span></span>
                <textarea class="form-control textarea-options rounded-left" name="option-B" aria-label="With textarea"></textarea>
            </div>
            <div class="input-group">
                <span class="rounded-right input-group-text bg-danger">گزینه <span class="m-1"><i class="bi bi-3-square"></i></span></span>
                <textarea class="form-control textarea-options rounded-left" name="option-C" aria-label="With textarea"></textarea>
            </div>
            <div class="input-group">
                <span class="rounded-right input-group-text bg-danger">گزینه <span class="m-1"><i class="bi bi-4-square"></i></span></span>
                <textarea class="form-control textarea-options rounded-left" name="option-D" aria-label="With textarea"></textarea>
            </div>
        </div>
        <button type="submit" id="add-new-question" data-exam-code="" class="btn btn-light"><i class="bi bi-plus fs-1 text-info"></i></button>
        <input type="hidden" name="ajax-nonce" value="<?php echo wp_create_nonce('creat-exam') ?>">
        <input type="hidden" name="ajax-url" value="<?php echo admin_url('admin-ajax.php') ?>">
        <!-- <script src="<?php // echo get_template_directory_uri() . '/assets/js/wp-admin/new-post-wp-admin.js' 
                            ?>"> -->

        </script>
    </div>
</form>