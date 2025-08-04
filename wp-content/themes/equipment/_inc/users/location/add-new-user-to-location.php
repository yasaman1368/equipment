<?php
function get_column_name_by_role($role)
{
  if ($role === 'supervisor') {
    $column_name = 'supervisors_id';
  } else if ($role === 'user') {
    $column_name = 'users_id';
  } else {
    return false;
  }

  return $column_name;
}

function get_users_in_location($location, $role)
{
  global $wpdb;
  $column = get_column_name_by_role($role);

  if (!$column) {
    return false;
  }
  $query = $wpdb->prepare("SELECT $column FROM location_supervisors_users WHERE location_name = %s", $location);
  $json = $wpdb->get_var($query);
  $data = json_decode($json, true);
  return is_array($data) ? $data : [];
}

function update_location_users($users_id, $role, $location)
{
  global $wpdb;

  $column = get_column_name_by_role($role);
  if (!$column) {
    return false;
  }

  $result = $wpdb->update(
    'location_supervisors_users',
    [$column => json_encode($users_id)],
    ['location_name' => $location]
  );
  if ($result === false) {
    wp_send_json_error('خطایی در ثبت مخاطب رخ داده است.');
  }
  return $result;
}

function add_user_to_locations($user_id, array $locations, $role)
{
  foreach ($locations as $location) {
    $users = get_users_in_location($location, $role);

    if (empty($users) || !is_array($users)) {
      update_location_users([$user_id], $role, $location);
      continue;
    }

    if (!in_array($user_id, $users)) {
      $users[] = $user_id;
      update_location_users($users, $role, $location);
    }
  }
}

function delete_user_from_locations($user_id,  $locations, $role)
{

  foreach ($locations as $location) {
    $users = get_users_in_location($location, $role);

    if (empty($users) || !is_array($users) || !in_array($user_id, $users)) {
      continue;
    }

    $index = array_search($user_id, $users);
    if ($index !== false) {
      unset($users[$index]);
      $users = array_values($users);
    }
    delete_location_users($users, $role, $location);
  }
 
}

function  delete_location_users($users, $role, $location)
{

  global $wpdb;

  $column = get_column_name_by_role($role);
  if (!$column) {
    return false;
  }

  $result = $wpdb->update(
    'location_supervisors_users',
    [$column => json_encode($users)],
    ['location_name' => $location]
  );

  if ($result === false) {
    wp_send_json_error('خطایی در ثبت مخاطب رخ داده است.');
  }

  return $result;
}
