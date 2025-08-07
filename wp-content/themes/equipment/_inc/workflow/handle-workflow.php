<?php
function handle_workflow($equipment_id, $action = 'approved') {
  $equipment_status = get_status($equipment_id);
  $user_id = get_current_user_id();
  $user_role = get_user_meta($user_id, '_role', true);

  if (!$equipment_status || $user_role === 'user') {
    $status = 'Pending';
    $role = 'user';

    return handle_workflow_table($equipment_id, $status, $role, $user_id)
      ? true
      : new WP_Error('db_error', 'ایجاد مرحله اولیه ناموفق بود');
  }

  $next_status = set_next_status($equipment_status, $action);
  if (!$next_status) return new WP_Error('invalid', 'وضعیت نامعتبر');

  $next_role = set_next_role($next_status);

  return handle_workflow_table($equipment_id, $next_status, $next_role, $user_id)
    ? true
    : new WP_Error('db_error', 'به‌روزرسانی گردش کار ناموفق بود');
}
