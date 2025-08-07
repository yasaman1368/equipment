<?php
function set_next_status($current_status, $action) {
  if ($current_status === 'Pending' && $action === 'approved') return 'SupervisorApproved';
  if ($current_status === 'Pending' && $action === 'rejected') return 'SupervisorReject';

  if ($current_status === 'SupervisorApproved' && $action === 'approved') return 'FinalApprove';
  if ($current_status === 'SupervisorApproved' && $action === 'rejected') return 'ManagerReject';

  return null;
}
