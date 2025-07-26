<?php
add_action('wp_ajax_remove_user', 'remove_user');

function handle_remover_users_in_location($user_id)
{
  $role = get_user_meta($user_id, '_role', true);
  $locations = get_user_meta($user_id, '_locations', true);

  if (!is_array($locations) || empty($locations)) {
    return;
  }

  foreach ($locations as $location) {
    $users = get_users_in_location($location, $role);
    if (empty($users) || !$users) {
      continue;
    }

    if (in_array($user_id, $users)) {
      $index = array_search($user_id, $users);

      unset($users[$index]);
      array_values($users);
      update_location_users($users, $role, $location);
    }
  }
}

function remove_user()
{
  $user_id = _sanitize_text_fields($_POST['user_id']);
  handle_remover_users_in_location($user_id);

  if ($user_id) {
    require_once(ABSPATH . 'wp-admin/includes/user.php');
    $stmt = wp_delete_user($user_id);
    if (!$stmt) {

      wp_send_json_error([
        'message' => 'کاربر مورد نظر  حذف نشد'
      ], 404);
    }
  }
  wp_send_json_success([
    'message' => 'کاربر مورد نظر با موفقیت حذف شد'
  ], 200);
}
