<?php
function get_form_fields()
{
  $form_id = intval($_POST['form_id']);

  global $wpdb;
  $table_name_fields = $wpdb->prefix . 'equipment_form_fields';
  $fields = $wpdb->get_results(
    $wpdb->prepare(
      "SELECT * FROM {$table_name_fields} WHERE form_id = %d",
      $form_id
    ),
    ARRAY_A
  );
  wp_send_json_success(['fields' => $fields, 'form_locations' => get_form_locations($form_id)]);
}
add_action('wp_ajax_get_form_fields', 'get_form_fields');
