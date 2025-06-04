<?php
function get_form_and_fields()
{
    $form_id = intval($_POST['form_id']);

    global $wpdb;
    $table_name_forms = $wpdb->prefix . 'equipment_forms';
    $table_name_fields = $wpdb->prefix . 'equipment_form_fields';

    // دریافت فرم
    $form = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name_forms WHERE id = %d", $form_id), ARRAY_A);

    // دریافت فیلدهای فرم
    $fields = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name_fields WHERE form_id = %d", $form_id), ARRAY_A);

    // پاسخ به درخواست AJAX
    wp_send_json_success(array('form' => $form, 'fields' => $fields));
}
add_action('wp_ajax_get_form_and_fields', 'get_form_and_fields');
