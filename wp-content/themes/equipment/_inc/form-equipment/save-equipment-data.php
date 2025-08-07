<?php
add_action('wp_ajax_save_equipment_data', 'save_equipment_data');
function save_equipment_data()
{
  if (!empty($_FILES)) {
    $uploaded_files = handle_file_uploads();
    if (is_wp_error($uploaded_files)) {
      wp_send_json_error(array('message' => $uploaded_files->get_error_message()));
      return;
    }
  }

  if (!isset($_POST['form_data'])) {
    wp_send_json_error(array('message' => 'form_data is missing in the request.'));
    return;
  }

  $form_data = json_decode(stripslashes($_POST['form_data']), true);
  if (json_last_error() !== JSON_ERROR_NONE) {
    wp_send_json_error(array('message' => 'Invalid JSON in form_data.'));
    return;
  }


  if (!empty($uploaded_files)) {
    $uploaded_files_transformed = [];
    foreach ($uploaded_files as $key => $link) {
      $new_key = preg_replace("/^field_/", "", $key);
      $uploaded_files_transformed[$new_key] = $link;
    }
    $form_data = $form_data + $uploaded_files_transformed;
  }

  $form_id = intval($_POST['form_id']);
  $equipment_id = sanitize_text_field($_POST['serial_number']);

  global $wpdb;
  $table_name_equipment_data = $wpdb->prefix . 'equipment_data';
  $table_name_form_fields = $wpdb->prefix . 'equipment_form_fields';
  $table_name_equipments = $wpdb->prefix . 'equipments';

  $existing_equipment = $wpdb->get_row($wpdb->prepare(
    "SELECT * FROM $table_name_equipments WHERE id = %d",
    $equipment_id
  ));

  $equipment_data = [];
  foreach ($form_data as $field_id => $value) {
    $field_name = $wpdb->get_var($wpdb->prepare(
      "SELECT field_name FROM $table_name_form_fields WHERE id = %d",
      $field_id
    ));
    if ($field_name) {
      $equipment_data[$field_name] = $value;
    }
  }

  if ($existing_equipment) {
    $wpdb->update(
      $table_name_equipments,
      $equipment_data,
      ['id' => $equipment_id]
    );
  } else {
    $wpdb->insert(
      $table_name_equipments,
      array_merge(['id' => $equipment_id], $equipment_data)
    );
  }

  $existing_data = $wpdb->get_results($wpdb->prepare(
    "SELECT * FROM $table_name_equipment_data WHERE equipment_id = %d",
    $equipment_id
  ));

  if (!empty($existing_data)) {
    foreach ($form_data as $field_id => $value) {
      $existing_value = $wpdb->get_var($wpdb->prepare(
        "SELECT value FROM $table_name_equipment_data WHERE equipment_id = %d AND field_id = %d",
        $equipment_id,
        $field_id
      ));

      if ($existing_value !== $value) {
        $wpdb->update(
          $table_name_equipment_data,
          ['value' => $value],
          ['equipment_id' => $equipment_id, 'field_id' => $field_id]
        );
      }
    }
  } else {
    $data_to_insert = [];
    $current_time = current_time('mysql');
    foreach ($form_data as $field_id => $value) {
      $data_to_insert[] = [
        'form_id' => $form_id,
        'equipment_id' => $equipment_id,
        'field_id' => $field_id,
        'value' => $value,
        'created_at' => $current_time,
        'updated_at' => $current_time
      ];
    }

    if (!empty($data_to_insert)) {
      foreach ($data_to_insert as $data) {
        $wpdb->insert($table_name_equipment_data, $data);
      }
    }
  }

  $workflow = handle_workflow($equipment_id);

  if (is_wp_error($workflow)) {
    wp_send_json_error(['message' => $workflow->get_error_message], 400);
  }
 
  wp_send_json_success(array(
    'message' => 'داده‌ها با موفقیت ذخیره شدند',
    'workflow' => $workflow
  ));
}

/**
 * Handle file uploads and return an array of file URLs.
 *
 * @return array|WP_Error Array of file URLs or WP_Error on failure.
 */
function handle_file_uploads()
{
  $uploaded_files = [];
  foreach ($_FILES as $field_id => $file) {
    if ($file['error'] === UPLOAD_ERR_OK) {
      $uploaded = wp_handle_upload($file, array('test_form' => false));
      if (isset($uploaded['url'])) {
        $uploaded_files[$field_id] = $uploaded['url'];
      } else {
        return new WP_Error('upload_error', 'Failed to upload file.');
      }
    } else {
      return new WP_Error('upload_error', 'File upload error: ' . $file['error']);
    }
  }
  return $uploaded_files;
}
