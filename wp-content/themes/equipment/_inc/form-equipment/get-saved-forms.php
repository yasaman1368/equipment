<?php

/**
 * Retrieves saved forms accessible to the current user based on their locations
 *
 * @return void Sends JSON response
 */
function get_saved_forms()
{
  global $wpdb;
  try {

    $table_name = $wpdb->prefix . 'equipment_forms';

    $user_id = get_current_user_id();
    $user_locations = get_user_locations($user_id);

    $forms = $wpdb->get_results(
      "SELECT id, form_name, locations FROM {$table_name}",
      ARRAY_A
    );
    
    if ($forms === null) {
      throw new RuntimeException('Database query failed');
    }

    $user_role = eqiupment_get_user_role($user_id);
    if ($user_role !== 'مدیر') {
      $accessible_forms = array_filter($forms, function ($form) use ($user_locations) {
        $form_locations = json_decode($form['locations'], true) ?: [];
        return !empty(array_intersect($user_locations, $form_locations));
      });
    }

    $response = [
      'forms' => array_values($accessible_forms),
      'status' => empty($accessible_forms) ? 'empty' : 'hasform'
    ];

    wp_send_json_success($response);
  } catch (Exception $e) {
    wp_send_json_error([
      'message' => 'درخواست از دیتا بیس ناموفق بود.',
      'debug' => $e->getMessage()
    ]);
  }
}

add_action('wp_ajax_get_saved_forms', 'get_saved_forms');
