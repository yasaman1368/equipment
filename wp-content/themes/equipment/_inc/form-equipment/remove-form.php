<?php


function remove_form()
{
    $id_to_delete = isset($_POST['form_id']) ? intval($_POST['form_id']) : 0;
    if ($id_to_delete > 0) {
        global $wpdb;
        $table_name_forms = $wpdb->prefix . 'equipment_forms';
        $forms = $wpdb->delete(
            $table_name_forms,
            ['id' => $id_to_delete],
            ['%d']
        );
        if ($forms) {
            wp_send_json_success(['forms' => $forms, 'message' => 'فرم مورد و تجهیزات مرتبط حذف شد.']);
        }
    }

    wp_send_json_error(['message' => 'درخواست حذف ناموفق بود.']);
}
add_action('wp_ajax_remove_form', 'remove_form');
