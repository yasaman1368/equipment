<?php add_action('wp_ajax_get_sections_cat', 'get_sections_cat');
function get_sections_cat()
{

    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'creat-exam')) {
        wp_send_json(['msg' => 'access denied'], 403);
    }
    add_action('init', 'flush_rewrite_rules');
    $parent_cat_slug = sanitize_text_field($_POST['parentCat']);
    $parent_cat = get_term_by('slug', $parent_cat_slug, 'test-cat');
    if (!$parent_cat) {
        wp_send_json([
            'error' => true,
            'message' => 'مشکلی در نمایش فصل های کتاب پیش آمده',
        ], 403);
    }
    $args = [
        'taxonomy' => 'test-cat',
        'child_of' => $parent_cat->term_id,
        'hide_empty' => false,
        'orderby' =>  'term_id',
        'order' => 'DESC'
    ];
    $childs_test_cat = get_terms($args);
    if (is_wp_error($childs_test_cat)) {
        wp_send_json([
            'error' => true,
            'message' => 'مشکلی در نمایش فصل های کتاب پیش آمده',
        ], 403);
    }
    ob_start();
?>
    <?php foreach ($childs_test_cat as $child_cat) {
    ?>

        <input
            type="checkbox"
            class="btn-check section-cat"
            id="<?php echo $child_cat->slug ?>"
            value="<?php echo $child_cat->slug ?>"
            autocomplete="off" />
        <label
            class="btn btn-outline-info"
            for="<?php echo $child_cat->slug ?>"
            data-bs-toggle="tooltip" data-bs-placement="top"
            data-bs-custom-class="custom-tooltip"
            data-bs-title="<?php echo $child_cat->description ?>"><?php echo $child_cat->name ?></label>
<?php }

    $html = ob_get_clean();
    wp_send_json([
        'success' => true,
        'html' => $html
    ], 200);
}
