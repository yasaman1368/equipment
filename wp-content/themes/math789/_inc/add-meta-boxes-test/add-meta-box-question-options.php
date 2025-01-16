<?php

add_action('add_meta_boxes', 'question_options');

function question_options()
{
    add_meta_box('question_options', 'گزینه های سوال', 'question_options_meta_box_html', 'test', 'normal', 'high');
}

function question_options_meta_box_html($post)
{

    wp_nonce_field('question_options_nonce', 'question_options_nonces');

?>

    <div>
        <span class="btn btn-info p-1 m-2 rounded" id="inline-math-equation">inline-math-equation</span>
        <span class="btn btn-warning p-1 m-2 rounded" id="block-math-equation">block-math-equation</span>
    </div>
    <div class="input-group">
        <span class="input-group-text bg-success">گزینه <span class="m-1"><i class="bi bi-1-square"></i></span></span>
        <textarea class="form-control textarea-options" name="option-A" aria-label="With textarea" placeholder="گزینه صحیح را در این قسمت بنویسید..."><?php echo esc_html(get_post_meta($post->ID, '_option-A', true)); ?></textarea>
    </div>
    <div class="input-group">
        <span class="input-group-text bg-danger">گزینه <span class="m-1"><i class="bi bi-2-square"></i></span></span>
        <textarea class="form-control textarea-options" name="option-B" aria-label="With textarea"><?php echo esc_html(get_post_meta($post->ID, '_option-B', true)); ?></textarea>
    </div>
    <div class="input-group">
        <span class="input-group-text bg-danger">گزینه <span class="m-1"><i class="bi bi-3-square"></i></span></span>
        <textarea class="form-control textarea-options" name="option-C" aria-label="With textarea"><?php echo esc_html(get_post_meta($post->ID, '_option-C', true)); ?></textarea>
    </div>
    <div class="input-group">
        <span class="input-group-text bg-danger">گزینه <span class="m-1"><i class="bi bi-4-square"></i></span></span>
        <textarea class="form-control textarea-options" name="option-D" aria-label="With textarea"><?php echo esc_html(get_post_meta($post->ID, '_option-D', true)); ?></textarea>
    </div>
    <script src="<?php echo get_template_directory_uri() . '/assets/js/wp-admin/new-post-wp-admin.js' ?>">

    </script>
<?php
}

add_action('save_post', 'save_options_meta_box');

function save_options_meta_box($post_id)
{


    if (isset($_POST['question_options_nonces'])) {
        $kmkt_p_brand_nonces = $_POST['question_options_nonces'];
        if (!wp_verify_nonce($kmkt_p_brand_nonces, 'question_options_nonce')) {
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


    if (!empty($_POST['option-A'])) {
        $option_A = sanitize_text_field($_POST['option-A']);
        update_post_meta($post_id, '_option-A', $option_A);
    }
    if (!empty($_POST['option-B'])) {
        $option_B = sanitize_text_field($_POST['option-B']);
        update_post_meta($post_id, '_option-B', $option_B);
    }
    if (!empty($_POST['option-C'])) {
        $option_C = sanitize_text_field($_POST['option-C']);
        update_post_meta($post_id, '_option-C', $option_C);
    }
    if (!empty($_POST['option-D'])) {
        $option_D = sanitize_text_field($_POST['option-D']);
        update_post_meta($post_id, '_option-D', $option_D);
    }
}
