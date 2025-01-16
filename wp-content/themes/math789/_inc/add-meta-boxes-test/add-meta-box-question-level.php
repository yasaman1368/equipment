<?php
add_action('add_meta_boxes', 'question_level');
add_action('save_post', 'level_question_save');
function question_level()
{
    add_meta_box('question-level', 'سطح سوال', 'render_html_question_level', 'test', 'normal');
}
function render_html_question_level()
{
    wp_nonce_field('select-level-question-nonce', 'select-level-question-nonces')
?>

    <label for="question-levels-option">سطح سوال را انتخاب کنید</label>
    <select name="levels" id="question-levels-option" class="form-select w-50">

        <option value="0">سطح سوال را انتخاب کنید</option>
        <option value="1" <?php echo selected(get_post_meta(get_the_ID(), '_question_level', true), 1) ?>>سخت</option>
        <option value="2" <?php echo selected(get_post_meta(get_the_ID(), '_question_level', true), 2) ?>>متوسط</option>
        <option value="3" <?php echo selected(get_post_meta(get_the_ID(), '_question_level', true), 3) ?>>آسان</option>
    </select>

<?php
}
function level_question_save($post_id)
{
    if (isset($_POST['select-level-question-nonces'])) {
        $kmkt_p_brand_nonces = $_POST['select-level-question-nonces'];
        if (!wp_verify_nonce($kmkt_p_brand_nonces, 'select-level-question-nonce')) {
            return;
        }
    } else {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }
    if (isset($_POST['levels'])) {
        $question_level = $_POST['levels'];
        update_post_meta($post_id, '_question_level', $question_level);
    }
}
