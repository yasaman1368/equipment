<?php

// this function in product no used yet
function get_suprevisors_relative_by_user($current_user_id)
{
  global $wpdb;

  $supervisors_id = [];
  $user_locations = get_user_meta($current_user_id, '_locations', ARRAY_A);

  foreach ($user_locations as $user_location) {
    $query = $wpdb->prepare("SELECT supervisors_id  FROM location_supervisors_users WHERE location_name=%s ", $user_location);
    $supervisors_id[] = json_decode($wpdb->get_var($query));
  }






  // $merged_supervisors = array_merge(...$supervisors_id);
  // $unique_supervisors_id = array_values(array_unique($merged_supervisors));

  // return $unique_supervisors_id;
}

// function get_users_relative_by_supervisor($current_user_id)
// {
//   global $wpdb;

//   $users_id = [];
//   $supervisor_locations = get_user_meta($current_user_id, '_locations', ARRAY_A);

//   foreach ($supervisor_locations as $supervisor_location) {
//     $query = $wpdb->prepare("SELECT users_id  FROM location_supervisors_users WHERE location_name=%s ", $supervisor_location);
//     $users_id[] = json_decode($wpdb->get_var($query));
//   }

//   $merged_users = array_merge(...$users_id);
//   $unique_users_id = array_values(array_unique($merged_users));

//   return $unique_users_id;
// }
