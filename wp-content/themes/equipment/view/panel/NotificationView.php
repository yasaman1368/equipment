<?php
global $wpdb;

$current_user_id = get_current_user_id();
$current_user_role = get_user_meta($current_user_id, '_role', true);

if ($current_user_role == 'supervisor') {
  $users_relative_by_supervisor = get_users_relative_by_supervisor($current_user_id);

  if (count($users_relative_by_supervisor) > 0) {
    #check if equipment in status is Pending or ManagerRejected display here
    $workflows = [];
    foreach ($users_relative_by_supervisor as $user_id) {
      $query = $wpdb->prepare('SELECT  *  FROM workflow WHERE current_status IN (%s, %s) AND user_id IN (%d)', 'Pending', 'Reject', $user_id);
      $results = $wpdb->get_results($query, ARRAY_A);

      if (!empty($results)) $workflows[] = $results;
    }

    $counts_notification = count($workflows);

    if ($counts_notification > 0) {
      $status_persian = [
        "Pending" => "انتظار تایید ناظر",
        "SupervisorApproved" => "تایید توسط ناظر",
        "SupervisorReject" => "رد توسط ناظر",
        "FinalApprove" => "تایید توسط مدیر",
        "ManagerReject" => "رد توسط مدیر",
      ];
?>
      <input type="hidden" name="notificationCount" value="<?php echo $counts_notification ?>">
      <div
        class="table-responsive text-center">
        <table
          class="table table-striped table-hover table-borderless table-primary align-middle">
          <thead class="table-light">
            <caption>
              جدول بررسی جدید ترین کارها
            </caption>
            <tr>
              <th>ردیف</th>
              <th>شماره تجهیز</th>
              <th>ثبت کننده</th>
              <th>وضعیت</th>
              <th>زمان ثبت</th>
              <th>عملیات</th>
            </tr>
          </thead>
          <tbody class="table-group-divider">
            <?php
            $i = 1;
            foreach ($workflows as $workflow): ?>
              <tr
                class="table-primary">
                <td scope="row"><?php echo $i++ ?></td>
                <td><?php echo $workflow[0]['equipment_id'] ?></td>
                <td><?php echo get_user_meta($workflow[0]['user_id'], 'nickname', true) ?></td>
                <td><?php echo  $status_persian[$workflow[0]['current_status']] ?></td>
                <td><?php echo $workflow[0]['update_at'] ?></td>
                <td><button
                    data-workflow-id="<?php echo $workflow[0]['workflow_id'] ?>"
                    data-equipment-id="<?php echo $workflow[0]['equipment_id'] ?>"
                    type="button"
                    class="btn btn-outline-success">
                    عملیات
                  </button>
                </td>
              </tr>
            <?php endforeach ?>
          </tbody>
          <tfoot>

          </tfoot>
        </table>
      </div>

    <?php
    }
  } else {
    ?>
    <div class="bg-info p-2 text-center fw-bolder rounded shaow">
      موردی برای شما ثبت نشده است
    </div>
<?php
  }
}
?>