<?php

add_action('wp_ajax_add_new_user', 'add_new_user_callback');

function validate_request_add_new_user()
{
  if (
    !isset($_POST['add_new_user_nonce']) ||
    !wp_verify_nonce($_POST['add_new_user_nonce'], 'add_new_user_action')
  ) {
    wp_send_json_error('خطای امنیتی رخ داده است.');
  }

  $required_fields = ['fullname', 'phone', 'password', 'repeat_password', 'role'];
  foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
      wp_send_json_error('لطفا تمام فیلدها را پر کنید.');
    }
  }
}

function get_column_name_by_role($role)
{
  if ($role === 'supervisor') {
    $column_name = 'supervisors_id';
  } else if ($role === 'users_id') {
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
    return;
  }
  $json = $wpdb->get_var(
    $wpdb->prepare(
      "SELECT $column FROM location_supervisors_users WHERE location_name = %s",
      $location
    )
  );
  $data = json_decode($json, true);
  return is_array($data) ? $data : [];
}

function update_location_users($user_ids, $role, $location)
{
  global $wpdb;
  $column = get_column_name_by_role($role);
  if (!$column) {
    return;
  }
  $result = $wpdb->update(
    'location_supervisors_users',
    [$column => json_encode($user_ids)],
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
    if (!in_array($user_id, $users)) {
      $users[] = $user_id;
      update_location_users($users, $role, $location);
    }
  }
}

function add_new_user_callback()
{
  $locations = json_decode(wp_unslash($_POST['locations'] ?? ''), true);
  if (!is_array($locations) || empty($locations)) {
    wp_send_json_error(['message' => 'اطلاعات موقعیت‌ها اشتباه است'], 400);
  }

  validate_request_add_new_user();

  $fullname        = sanitize_text_field($_POST['fullname']);
  $phone           = sanitize_text_field($_POST['phone']);
  $role            = sanitize_text_field($_POST['role']);
  $password        = $_POST['password'];
  $repeat_password = $_POST['repeat_password'];

  if ($password !== $repeat_password) {
    wp_send_json_error('رمز عبور و تکرار آن مطابقت ندارند.');
  }

  $locations = array_map('sanitize_text_field', $locations);

  if (username_exists($phone)) {
    wp_send_json_error('نام کاربری یا شماره موبایل قبلا ثبت شده است.');
  }

  $user_id = wp_create_user($phone, $password, $phone);

  if (is_wp_error($user_id)) {
    wp_send_json_error('خطا در ایجاد کاربر: ' . $user_id->get_error_message(), 500);
  }

  update_user_meta($user_id, '_role', $role);
  update_user_meta($user_id, '_locations', $locations);
  wp_update_user([
    'ID'           => $user_id,
    'display_name' => $fullname
  ]);

  add_user_to_locations($user_id, $locations, $role);

  wp_send_json_success('کاربر ' . $fullname . ' با موفقیت افزوده شد.');
}
