<?php
function render_equipment_form($atts)
{
    global $wpdb;
    $table_name_forms = $wpdb->prefix . 'equipment_forms';
    $table_name_fields = $wpdb->prefix . 'equipment_form_fields';
    $table_name_data = $wpdb->prefix . 'equipment_data';

    $form_id = $atts['id'];
    $equipment_id = $atts['equipment_id'];

    // دریافت فیلدهای فرم
    $fields = $wpdb->get_results("SELECT * FROM $table_name_fields WHERE form_id = $form_id", ARRAY_A);

    // دریافت داده‌های ذخیره شده (اگر وجود دارد)
    $saved_data = [];
    if ($equipment_id) {
        $saved_data = $wpdb->get_results("SELECT * FROM $table_name_data WHERE form_id = $form_id AND equipment_id = $equipment_id", ARRAY_A);
    }

    // ایجاد فرم
    $html = '<form method="post">';
    foreach ($fields as $field) {
        $html .= '<div class="form-group">';
        $html .= '<label>' . esc_html($field['field_name']) . '</label>';

        $field_value = '';
        foreach ($saved_data as $data) {
            if ($data['field_id'] == $field['id']) {
                $field_value = $data['value'];
                break;
            }
        }

        switch ($field['field_type']) {
            case 'text':
            case 'number':
            case 'date':
                $html .= '<input type="' . esc_attr($field['field_type']) . '" name="field_' . esc_attr($field['id']) . '" value="' . esc_attr($field_value) . '" class="form-control">';
                break;
            case 'select':
                $options = json_decode($field['options'], true);
                $html .= '<select name="field_' . esc_attr($field['id']) . '" class="form-control">';
                foreach ($options as $option) {
                    $selected = ($field_value == $option) ? 'selected' : '';
                    $html .= '<option value="' . esc_attr($option) . '" ' . $selected . '>' . esc_html($option) . '</option>';
                }
                $html .= '</select>';
                break;
            case 'checkbox':
            case 'radio':
                $options = json_decode($field['options'], true);
                foreach ($options as $option) {
                    $checked = ($field_value == $option) ? 'checked' : '';
                    $html .= '<div class="form-check">';
                    $html .= '<input type="' . esc_attr($field['field_type']) . '" name="field_' . esc_attr($field['id']) . '[]" value="' . esc_attr($option) . '" ' . $checked . ' class="form-check-input">';
                    $html .= '<label class="form-check-label">' . esc_html($option) . '</label>';
                    $html .= '</div>';
                }
                break;
        }
        $html .= '</div>';
    }
    $html .= '<button type="submit" class="btn btn-primary">ذخیره</button>';
    $html .= '</form>';

    return $html;
}
add_shortcode('equipment_form', 'render_equipment_form');
