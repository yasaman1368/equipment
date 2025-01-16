<?php
add_action('wp_ajax_change_status_exam_active', 'change_status_exam_active');

function change_status_exam_active()
{
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'creat-exam')) {
        wp_send_json(['msg' => 'access denied'], 403);
    }

    $exam_code = intval($_POST['examCode']);
    $status = intval($_POST['status']);
    $new_status = ($status === 0) ? 1 : 0;

    $status_active = update_exam_status($new_status, $exam_code, $GLOBALS['wpdb']);

    if ($status_active === false) {
        wp_send_json(['success' => false, 'message' => 'Database update failed'], 500);
    } else {
        $message = ($new_status === 0) ? "وضعیت آزمون به غیر فعال تغییر کرد" : "وضعیت آزمون به فعال تغییر کرد";
        $button_html = generate_button_html($exam_code, $new_status);

        wp_send_json([
            'success' => true,
            'message' => $message,
            'html' => $button_html
        ], 200);
    }
}

function update_exam_status($new_status, $exam_code, $wpdb)
{
    $table = 'created_exam_data';
    $data = ['status' => $new_status];
    $where = ['exam_code' => $exam_code];

    return $wpdb->update($table, $data, $where, ['%d'], ['%d']);
}

function generate_button_html($exam_code, $new_status)
{
    $status_class = ($new_status === 0) ? 'btn btn-warning' : 'btn btn-primary';
    $status_text = ($new_status === 0) ? 'غیر فعال' : 'فعال';

    return '<button
                type="button"
                class="' . $status_class . ' status-active"
                data-status="' . $new_status . '"
                data-exam-code="' . $exam_code . '">
                ' . $status_text . '
            </button>';
}
