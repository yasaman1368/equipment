<?php
class Workflow_View
{
  private $wpdb;
  private $current_user_id;
  private $current_user_role;
  private $workflows;

  private $status_persian = [
    "Pending"            => "انتظار تایید ناظر",
    "SupervisorApproved" => "تایید توسط ناظر",
    "SupervisorReject"   => "رد توسط ناظر",
    "FinalApprove"       => "تایید توسط مدیر",
    "ManagerReject"      => "رد توسط مدیر",
  ];

  public function __construct()
  {
    global $wpdb;
    $this->wpdb              = $wpdb;
    $this->current_user_id   = get_current_user_id();
    $this->current_user_role = get_user_meta($this->current_user_id, '_role', true);
    $this->workflows         = $this->get_workflows();
  }

  private function get_workflows()
  {
    $column_name = $this->current_user_role . '_id';
    $query       = $this->wpdb->prepare("SELECT * FROM workflow WHERE {$column_name}=%d", $this->current_user_id);
    return $this->wpdb->get_results($query, ARRAY_A);
  }

  public function render()
  {
    $workflows = $this->workflows;
?>
    <div class="container">
      <div class="table-responsive">
        <table class="table table-striped table-hover table-dark align-middle">
          <thead class="table-light">
            <caption>گردش کار</caption>
            <tr class="text-center">
              <th>ردیف</th>
              <th>شماره سریال</th>
              <th>وضعیت</th>
              <th>نقش فعال</th>
              <th>زمان ثبت</th>
              <th>عملیات</th>
            </tr>
          </thead>
          <tbody id="workflow-table-body">
            <?php if (empty($workflows)) : ?>
              <tr class="bg-light text-dark">
                <td colspan="6" class="text-center fw-bold">
                  گردش کاری برای شما ثبت نشده است.
                </td>
              </tr>
            <?php else : ?>
              <?php foreach ($workflows as $index => $workflow) : ?>
                <tr class="table-primary text-center">
                  <td><?php echo $index + 1; ?></td>
                  <td><?php echo esc_html($workflow['equipment_id']); ?></td>
                  <td><?php echo esc_html($this->status_persian[$workflow['current_status']] ?? $workflow['current_status']); ?></td>
                  <td><?php echo esc_html($workflow['active_role']); ?></td>
                  <td class="dateTime"><?php echo esc_html($workflow['update_at']); ?></td>
                  <td>
                    <button
                      type="button"
                      class="btn btn-primary w-100 displayHistory"
                      name="processHistory"
                      data-equipment-id="<?php echo esc_attr($workflow['equipment_id']); ?>"
                      data-bs-toggle="modal"
                      data-bs-target="#processHistory">
                      جزییات
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <?php $this->render_modal(); ?>
  <?php
  }

  private function render_modal()
  {
  ?>
    <div class="modal fade" id="processHistory" tabindex="-1" aria-labelledby="modalTitleId" aria-hidden="true">
      <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-lg">
        <div class="modal-content">

          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title" id="modalTitleId">جزئیات گردش کار</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="بستن"></button>
          </div>

          <div class="modal-body">
            <div id="historyContainer" class="container"></div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-success processHistory" data-bs-dismiss="modal">مشاهده تاریخچه</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">بستن</button>
          </div>
        </div>
      </div>
    </div>
<?php
  }
}

$workflow_view = new Workflow_View();
$workflow_view->render();
