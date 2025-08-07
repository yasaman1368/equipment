<?php 
function handle_notification() {
  $user_id = get_current_user_id();
 $supervisors_id=get_suprevisors_relative_by_user($user_id);

}

