<?php
// Prevent direct access
defined('ABSPATH') || exit;

class Workflow_Notifications
{
  private $wpdb;
  private $current_user_id;
  private $current_user_role;
  private $status_persian = [
    "Pending" => "انتظار تایید ناظر",
    "SupervisorApproved" => "تایید توسط ناظر",
    "SupervisorReject" => "رد توسط ناظر",
    "FinalApprove" => "تایید توسط مدیر",
    "ManagerReject" => "رد توسط مدیر",
  ];

  public function __construct()
  {
    global $wpdb;
    $this->wpdb = $wpdb;
    $this->current_user_id = get_current_user_id();
    $this->current_user_role = get_user_meta($this->current_user_id, '_role', true);
  }

  public function render()
  {
    if ($this->current_user_role === 'supervisor') {
      $this->render_supervisor_view();
    } elseif ($this->current_user_role === 'manager' || current_user_can('administrator')) {
      $this->render_manager_view();
    } elseif ($this->current_user_role === 'user') {
      $this->render_user_view();
    }

    $this->render_modal();
  }

  private function render_supervisor_view()
  {
    $users_relative_by_supervisor = get_users_relative_by_supervisor($this->current_user_id);

    if (empty($users_relative_by_supervisor)) {
      $this->render_no_users_message();
      return;
    }

    $user_ids = implode(',', array_map('intval', $users_relative_by_supervisor));
    $workflows = $this->get_workflows(
      "current_status IN (%s, %s) AND user_id IN ($user_ids)",
      ['Pending', 'ManagerReject'] //Reject-> ManagerReject
    );

    $this->render_workflows_table($workflows);
  }

  private function render_manager_view()
  {
    $workflows = $this->get_workflows(
      "current_status = %s",
      ['SupervisorApproved']
    );

    $this->render_workflows_table($workflows);
  }

  private function render_user_view()
  {
    $workflows = $this->get_workflows(
      "current_status= %s AND user_id=%d",
      ['SupervisorReject', $this->current_user_id]
    );

    $this->render_workflows_table($workflows);
  }

  private function get_workflows($where_clause, $placeholders)
  {
    $query = $this->wpdb->prepare(
      "SELECT * FROM workflow 
             WHERE $where_clause
             ORDER BY update_at DESC",
      $placeholders
    );

    return $this->wpdb->get_results($query, ARRAY_A);
  }

  private function render_workflows_table($workflows)
  {
    if (empty($workflows)) {
      $this->render_no_workflows_message();
      return;
    }

    $this->render_notification_count(count($workflows));


?>
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
            <tr class="table-primary" data-equipment-id="<?php echo esc_attr($workflow['equipment_id']) ?>">

              <?php if ($workflow['current_status'] === 'SupervisorReject'):
                $latest_comment = $this->get_latest_comment($workflow);
              ?>
                <input type="hidden" name="workflow_latest_comment" data-user-modal="true"
                  <?php echo 'data-comment-' . $workflow['equipment_id'] . ' ="' . esc_attr($latest_comment) . '"' ?>>
              <?php endif ?>

              <td scope="row"><?php echo esc_html($i + 1) ?></td>
              <td><?php echo esc_html($workflow['equipment_id']) ?></td>
              <td><?php echo esc_html(get_user_meta($workflow['user_id'], 'nickname', true)) ?></td>
              <td><?php echo esc_html($this->status_persian[$workflow['current_status']] ?? $workflow['current_status']) ?></td>
              <td class="dateTime"><?php echo esc_html($workflow['update_at']) ?></td>
              <td>
                <button
                  data-workflow-id="<?php echo esc_attr($workflow['workflow_id']) ?>"
                  data-equipment-id="<?php echo esc_attr($workflow['equipment_id']) ?>"
                  type="button"
                  class="btn btn-primary btn-lg displayEquipmentView"
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
  <?php
  }

  private function render_notification_count($count)
  {
  ?>
    <input type="hidden" name="notificationCount" value="<?php echo esc_attr($count) ?>">
  <?php
  }

  private function render_no_workflows_message()
  {
  ?>
    <div class="bg-info p-2 text-center fw-bolder rounded shadow">
      موردی برای بررسی وجود ندارد.
    </div>
  <?php
  }

  private function render_no_users_message()
  {
  ?>
    <div class="bg-info p-2 text-center fw-bolder rounded shadow">
      کاربری تحت نظارت شما وجود ندارد.
    </div>
  <?php
  }

  private function render_modal()
  {
  ?>
    <div class="modal fade" id="modalIddisplayEquipment" tabindex="-1" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalTitleId">بازبینی تجهیز</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="container-fluid">
              <form id="form-container"></form>
            </div>
          </div>
          <div class="modal-footer">

            <button type="button" class="btn btn-success processHistory"   data-bs-dismiss="modal">مشاهده تاریخچه</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">بستن</button>
          </div>
        </div>
      </div>
    </div>
<?php
  }

  private function get_latest_comment($workflow)
  {
    $history = json_decode($workflow['proccess_history'] ?? '', true);

    if (json_last_error() !== JSON_ERROR_NONE || !is_array($history) || empty($history)) {
      return 'خطا در پردازش اطلاعات';
    }

    $latest = end($history);
    return $latest['action']['comment'] ?? 'بدون نظر';
  }
}


$workflow_notifications = new Workflow_Notifications();
$workflow_notifications->render();
