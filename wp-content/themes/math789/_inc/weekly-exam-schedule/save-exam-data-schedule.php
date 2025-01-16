<?php
add_action('wp_ajax_save_exam_data', 'save_exam_data');

function save_exam_data()
{
    global $wpdb;

    //  دریافت داده‌ها از درخواست AJAX
    $data = json_decode(stripslashes($_POST['data']), true);
    $classroom_id = array_pop($data);
    foreach ($data as $row) {
        $existing_id = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM wp_exam_schedule WHERE day = %s AND exam_date = %s",
            $row['day'],
            $row['date']
        ));

        if ($existing_id) {
            // به‌روزرسانی رکورد موجود
            $content_subject = json_decode($wpdb->get_var($wpdb->prepare("SELECT content FROM wp_exam_schedule WHERE id=%s ", $existing_id)), true);


            $content_subject[$classroom_id] = ['content' => $row['content'], 'subject' => $row['subject']];
            $wpdb->update(
                'wp_exam_schedule',
                array(
                    'content' => json_encode($content_subject)
                ),
                array('id' => $existing_id),
                array('%s', '%s'),
                array('%d')
            );
        } else {
            // وارد کردن رکورد جدید
            $wpdb->insert(
                'wp_exam_schedule',
                array(
                    'day' => $row['day'],
                    'content' => json_encode([$classroom_id => ['content' => $row['content'], 'subject' => $row['subject']]]),
                    'exam_date' => $row['date']
                ),
                array('%s', '%s', '%s', '%s')
            );
        }
    }

    wp_send_json_success('اطلاعات با موفقیت ذخیره شد!');
}
