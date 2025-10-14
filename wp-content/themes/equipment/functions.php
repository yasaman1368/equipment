
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
function update_form_data() {
  // if ( ! current_user_can('manage_options') ) { // یا capability مناسب شما مثل is_manager
  //   wp_send_json_error(['message' => 'حق دسترسی ندارید.']);
  // }

  // اگر تصمیم دارید nonce استفاده کنید:
  // check_ajax_referer('form_manager_nonce', 'security');

  if ( empty( $_POST['form_data'] ) ) {
    wp_send_json_error(['message' => 'ورودی نامعتبر.']);
  }

  $raw = wp_unslash( $_POST['form_data'] ); // امن‌سازی اولیه
  $form_data = json_decode( $raw, true );

  if ( json_last_error() !== JSON_ERROR_NONE || ! is_array($form_data) ) {
    wp_send_json_error(['message' => 'فرمت داده نامعتبر.']);
  }

  global $wpdb;
  $forms_table = $wpdb->prefix . 'equipment_forms';
  $fields_table = $wpdb->prefix . 'equipment_form_fields';

  $form_id = intval( $form_data['form_id'] ?? 0 );
  if ( ! $form_id ) {
    wp_send_json_error(['message' => 'شناسه فرم نامعتبر.']);
  }

  // آپدیت اصلی
  $updated = $wpdb->update(
    $forms_table,
    [
      'form_name' => sanitize_text_field( $form_data['form_name'] ?? '' ),
      'locations' => wp_json_encode( $form_data['locations'] ?? [] ),
      'updated_at' => current_time('mysql')
    ],
    ['id' => $form_id],
    ['%s','%s','%s'],
    ['%d']
  );

  // جمع idهای موجود در payload برای بررسی حذف‌های احتمالی
  $incoming_field_ids = [];

  foreach ( $form_data['fields'] as $field ) {
    if ( ! empty( $field['field_id'] ) ) {
      $fid = intval( $field['field_id'] );
      $incoming_field_ids[] = $fid;
      $wpdb->update(
        $fields_table,
        [
          'field_name' => sanitize_text_field( $field['field_name'] ?? '' ),
          'field_type' => sanitize_text_field( $field['field_type'] ?? '' ),
          'options'    => wp_json_encode( $field['options'] ?? [] ),
          'updated_at' => current_time('mysql')
        ],
        ['id' => $fid],
        ['%s','%s','%s','%s'],
        ['%d']
      );
    } else {
      $wpdb->insert(
        $fields_table,
        [
          'form_id' => $form_id,
          'field_name' => sanitize_text_field( $field['field_name'] ?? '' ),
          'field_type' => sanitize_text_field( $field['field_type'] ?? '' ),
          'options' => wp_json_encode( $field['options'] ?? [] ),
          'created_at' => current_time('mysql'),
          'updated_at' => current_time('mysql')
        ],
        ['%d','%s','%s','%s','%s','%s']
      );
    }
  }

  // حذف فیلدهایی که در payload نیستند (اختیاری ولی مفید)
  if (!empty($incoming_field_ids)) {
    // حذف همه فیلدهای فرم که در incoming نیستند
    $placeholders = implode(',', array_fill(0, count($incoming_field_ids), '%d'));
    $sql = $wpdb->prepare(
      "DELETE FROM {$fields_table} WHERE form_id = %d AND id NOT IN ($placeholders)",
      array_merge([$form_id], $incoming_field_ids)
    );
    $wpdb->query($sql);
  }

  wp_send_json_success(['message' => 'فرم با موفقیت بروزرسانی شد.']);
}
add_action('wp_ajax_update_form_data', 'update_form_data');


function remove_form_field() {
  if ( ! current_user_can('manage_options') ) {
    wp_send_json_error(['message' => 'حق دسترسی ندارید.']);
  }

  $field_id = intval( $_POST['field_id'] ?? 0 );
  if ( ! $field_id ) {
    wp_send_json_error(['message' => 'شناسه نامعتبر.']);
  }

  global $wpdb;
  $fields_table = $wpdb->prefix . 'equipment_form_fields';
  $result = $wpdb->delete( $fields_table, ['id' => $field_id], ['%d'] );

  if ( $result !== false ) {
    wp_send_json_success(['message' => 'فیلد با موفقیت حذف شد.']);
  } else {
    wp_send_json_error(['message' => 'خطا در حذف فیلد.']);
  }
}
add_action('wp_ajax_remove_form_field', 'remove_form_field');

