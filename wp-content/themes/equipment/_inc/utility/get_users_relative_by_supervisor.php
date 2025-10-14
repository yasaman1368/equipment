<?php
function get_users_relative_by_supervisor($current_user_id)
{
  global $wpdb;

  $users_id = [];
  $supervisor_locations = get_user_meta($current_user_id, '_locations', true);

  // اگر خالی یا نال بود، آرایه خالی
  if (empty($supervisor_locations) || !is_array($supervisor_locations)) {
    $supervisor_locations = [];
  }

  foreach ($supervisor_locations as $supervisor_location) {
    $query = $wpdb->prepare("SELECT users_id FROM location_supervisors_users WHERE location_name = %s", $supervisor_location);
    $result = $wpdb->get_var($query);

    // اگر مقدار از دیتابیس نال یا خالی بود، ردش کن
    if (empty($result)) {
      continue;
    }

    // json_decode با پیش‌فرض آرایه
    $decoded = json_decode($result, true);

    // اگر ساختار JSON معتبر نبود، ردش کن
    if (!is_array($decoded)) {
      continue;
    }

    $users_id[] = $decoded;
  }

  // اگر هیچ آرایه‌ای وجود نداشت، مقدار خالی برگردون
  if (empty($users_id)) {
    return [];
  }

  // ادغام تمام آرایه‌ها
  $merged_users = array_merge(...$users_id);

  // حذف تکراری‌ها و بازنشانی کلیدها
  $unique_users_id = array_values(array_unique($merged_users));

  return $unique_users_id;
}
