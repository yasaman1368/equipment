<?php
function set_next_role($status) {
  switch ($status) {
    case 'Pending': return 'supervisor';
    case 'SupervisorApproved': return 'manager';
    case 'SupervisorReject': return 'user';
    case 'ManagerReject': return 'supervisor';
    case 'FinalApprove': return 'manager';
    default: return 'user';
  }
}
