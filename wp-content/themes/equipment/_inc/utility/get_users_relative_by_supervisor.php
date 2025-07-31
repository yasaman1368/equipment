<?php
function get_users_relative_by_supervisor($current_user_id)
{
  global $wpdb;

  $users_id = [];
  $supervisor_locations = get_user_meta($current_user_id, '_locations', ARRAY_A);

  foreach ($supervisor_locations as $supervisor_location) {
    $query = $wpdb->prepare("SELECT users_id  FROM location_supervisors_users WHERE location_name=%s ", $supervisor_location);
    $users_id[] = json_decode($wpdb->get_var($query));
  }

  $merged_users = array_merge(...$users_id);
  $unique_users_id = array_values(array_unique($merged_users));

  return $unique_users_id;
}
