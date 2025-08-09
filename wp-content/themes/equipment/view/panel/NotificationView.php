<?php
global $wpdb;

$current_user_id = get_current_user_id();
$current_user_role = get_user_meta($current_user_id, '_role', true);

if ($current_user_role == 'supervisor') {
  $users_relative_by_supervisor = get_users_relative_by_supervisor($current_user_id);

  if (count($users_relative_by_supervisor) > 0) {
    // تبدیل آرایه کاربران به لیست جدا شده با کاما برای کوئری SQL
    $user_ids = implode(',', array_map('intval', $users_relative_by_supervisor));

    // دریافت تمام رکوردهای منطبق (نه فقط آخرین رکورد هر کاربر)
    $query = $wpdb->prepare(
      "SELECT * FROM workflow 
            WHERE current_status IN (%s, %s) 
            AND user_id IN ($user_ids)
            ORDER BY update_at DESC", // اخیرترین‌ها اول نمایش داده شوند
      'Pending',
      'Reject'
    );

    $workflows = $wpdb->get_results($query, ARRAY_A);

    $status_persian = [
      "Pending" => "انتظار تایید ناظر",
      "SupervisorApproved" => "تایید توسط ناظر",
      "SupervisorReject" => "رد توسط ناظر",
      "FinalApprove" => "تایید توسط مدیر",
      "ManagerReject" => "رد توسط مدیر",
    ];

    if (!empty($workflows)) {
?>
      <input type="hidden" name="notificationCount" value="<?php echo count($workflows) ?>">
      <div class="table-responsive text-center">
        <table class="table table-striped table-hover table-borderless table-primary align-middle">
          <thead class="table-light">
            <caption>جدول بررسی جدید ترین کارها</caption>
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
            <?php foreach ($workflows as $i => $workflow): ?>
              <tr class="table-primary">
                <td scope="row"><?php echo $i + 1 ?></td>
                <td><?php echo esc_html($workflow['equipment_id']) ?></td>
                <td><?php echo esc_html(get_user_meta($workflow['user_id'], 'nickname', true)) ?></td>
                <td><?php echo esc_html($status_persian[$workflow['current_status']]) ?></td>
                <td><?php echo esc_html($workflow['update_at']) ?></td>
                <td>
                  <button data-workflow-id="<?php echo esc_attr($workflow['workflow_id']) ?>"
                    data-equipment-id="<?php echo esc_attr($workflow['equipment_id']) ?>"
                    type="button"
                    class="btn btn-outline-success  displayEquipmentView"
                    type="button"
                    class="btn btn-primary btn-lg"
                    data-bs-toggle="modal"
                    data-bs-target="#modalIddisplayEquipment">
                    عملیات
                  </button>
                </td>
              </tr>
            <?php endforeach ?>
          </tbody>
        </table>
      </div>
    <?php } else { ?>
      <div class="bg-info p-2 text-center fw-bolder rounded shaow">
        موردی برای بررسی وجود ندارد.
      </div>
    <?php } ?>
  <?php } else { ?>
    <div class="bg-info p-2 text-center fw-bolder rounded shaow">
      کاربری تحت نظارت شما وجود ندارد.
    </div>
  <?php } ?>
<?php } ?>


<!-- Modal -->
<div
  class="modal fade"
  id="modalIddisplayEquipment"
  tabindex="-1"
  role="dialog"
  aria-labelledby="modalTitleId"
  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitleId">
          Modal title
        </h5>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"
          aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <form id="form-container">

          </form>

        </div>
      </div>
      <div class="modal-footer">
        <button
          type="button"
          class="btn btn-secondary"
          data-bs-dismiss="modal">
          Close
        </button>
        <button type="button" class="btn btn-primary">Save</button>
      </div>
    </div>
  </div>
</div>