
<?php
include_once '_inc/register-assets/register-assets.php';
include_once '_inc/utility/locations.php';
include_once '_inc/autoLoader/AutoLoader.php';
include_once '_inc/form-equipment/save-form-data.php';
include_once '_inc/form-equipment/show-edite-equiment-forms.php';
include_once '_inc/form-equipment/get-saved-forms.php';
include_once '_inc/form-equipment/get-form-fields.php';
include_once '_inc/form-equipment/save-equipment-data.php';
include_once '_inc/form-equipment/get-equipment-data.php';
include_once '_inc/form-equipment/get-form-and-fields.php';
include_once '_inc/form-equipment/remove-equipment-data.php';
include_once '_inc/form-equipment/remove-form.php';
include_once '_inc/export/export-equipments.php';
include_once '_inc/users/search-users/handler-user-search.php';
include_once '_inc/users/add-new-user/add-new-user.php';
include_once '_inc/users/get-user-list/get-user-list.php';
include_once '_inc/users/remove-user/remove-user.php';
include_once '_inc/users/get-user-details/get_user_details.php';
include_once '_inc/users/update-user/update-user.php';
include_once '_inc/users/user-login/handle-user-login.php';
include_once '_inc/fetch-student-report/fetch-student-report.php';
include_once '_inc/users/location/CRUD-location.php';
include_once '_inc/users/location/add-new-user-to-location.php';
include_once '_inc/users/location/update-user-location.php';
include_once '_inc/utility/get-user-role.php';
include_once '_inc/utility/get-count-nitifications.php';
include_once '_inc/workflow/process_equipment_review.php';
include_once '_inc/workflow/get-process-history.php';
include_once '_inc/export/get-excel-format.php';
include_once '_inc/export/export-equipments-data-from-form.php';
include_once '_inc/import/import-equipments-data-from-form.php';
include_once '_inc/student-registeration/register-student.php';
include_once '_inc/student-registeration/get-class-stats.php';

//****** to do this below function  i think will use but till i dont use
include_once '_inc/utility/get_users_relative_by_supervisor.php';
include_once '_inc/utility/get_supervisors_relative_by_user.php';
include_once 'panel/router.php';


define('COMPOSER_ROOT', get_template_directory() . '/composer');
include_once COMPOSER_ROOT . '/vendor/autoload.php';


// require_once(ABSPATH . 'wp-admin/includes/user.php');

// for( $i= 36; $i< 70; $i++ ){
//   wp_delete_user($i);
// }


// اضافه کردن action برای آپدیت فرم
function update_form_data()
{
  try {
    $form_data = json_decode(stripslashes($_POST['form_data']), true);

    global $wpdb;
    $forms_table = $wpdb->prefix . 'equipment_forms';
    $fields_table = $wpdb->prefix . 'equipment_form_fields';

    // آپدیت اطلاعات اصلی فرم
    $wpdb->update(
      $forms_table,
      [
        'form_name' => sanitize_text_field($form_data['form_name']),
        'locations' => json_encode($form_data['locations']),
        'updated_at' => current_time('mysql')
      ],
      ['id' => intval($form_data['form_id'])]
    );

    // آپدیت فیلدهای موجود و اضافه کردن فیلدهای جدید
    foreach ($form_data['fields'] as $field) {
      if (isset($field['field_id']) && !empty($field['field_id'])) {
        // آپدیت فیلد موجود
        $wpdb->update(
          $fields_table,
          [
            'field_name' => sanitize_text_field($field['field_name']),
            'field_type' => sanitize_text_field($field['field_type']),
            'options' => json_encode($field['options']),
            'updated_at' => current_time('mysql')
          ],
          ['id' => intval($field['field_id'])]
        );
      } else {
        // اضافه کردن فیلد جدید
        $wpdb->insert(
          $fields_table,
          [
            'form_id' => intval($form_data['form_id']),
            'field_name' => sanitize_text_field($field['field_name']),
            'field_type' => sanitize_text_field($field['field_type']),
            'options' => json_encode($field['options']),
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
          ]
        );
      }
    }

    wp_send_json_success(['message' => 'فرم با موفقیت بروزرسانی شد.']);
  } catch (Exception $e) {
    wp_send_json_error(['message' => 'خطا در بروزرسانی فرم: ' . $e->getMessage()]);
  }
}
add_action('wp_ajax_update_form_data', 'update_form_data');

// اضافه کردن action برای حذف فیلد
function remove_form_field()
{
  try {
    $field_id = intval($_POST['field_id']);

    global $wpdb;
    $fields_table = $wpdb->prefix . 'equipment_form_fields';

    $result = $wpdb->delete(
      $fields_table,
      ['id' => $field_id]
    );

    if ($result !== false) {
      wp_send_json_success(['message' => 'فیلد با موفقیت حذف شد.']);
    } else {
      wp_send_json_error(['message' => 'خطا در حذف فیلد.']);
    }
  } catch (Exception $e) {
    wp_send_json_error(['message' => 'خطا در حذف فیلد: ' . $e->getMessage()]);
  }
}
add_action('wp_ajax_remove_form_field', 'remove_form_field');
