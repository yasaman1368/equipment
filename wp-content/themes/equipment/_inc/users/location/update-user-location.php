<?php
add_action("wp_ajax_update_user_locations", "callback_update_user_locations");

function callback_update_user_locations()
{
  $user_id = intval($_POST["userId"]);
  $locations = json_decode(stripslashes($_POST["locations"]), true);
  if (!is_array($locations)) {
    wp_send_json_error(['message' => "موقعیتی برای ویرایش وجود ندارد"],403);
  }

  $role = get_user_meta($user_id, "_role", true);
  $current_locations = array_map("sanitize_text_field", (array) $locations);
  $prev_locations = (array) get_user_meta($user_id, "_locations", true);
  $prev_minus_current_locations = array_diff($prev_locations, $current_locations);
   $current_minus_prev_locations = array_diff($current_locations, $prev_locations);


  delete_user_from_locations($user_id, $prev_minus_current_locations, $role);
  add_user_to_locations($user_id, $current_minus_prev_locations, $role);


  update_user_meta($user_id, "_locations", $current_locations);


  wp_send_json_success(['message' => 'موقعیت کاربر با موفقیت ویرایش شد']);
}
