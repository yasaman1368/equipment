<?php
global $wpdb;

$user_id = get_current_user_id();
$user_role = get_user_meta($user_id, '_role', true);
$column_name = $user_role . '_id';
$query = $wpdb->prepare("SELECT * FROM workflow WHERE {$column_name}=%d", $user_id);
$workflow = $wpdb->get_results($query, ARRAY_A);

?>

<div class="container">
  <div
    class="table-responsive">
    <table
      class="table table-striped table-hover table-borderless table-dark align-middle">
      <thead class="table-light">
        <caption>
          گردش کار
        </caption>
        <tr class="text-center">
          <th>ردیف</th>
          <th>شماره سریال</th>
          <th>وضعیت</th>
          <th>نقش فعال</th>
          <th>زمان ثبت</th>
          <th>عملیات</th>
        </tr>
      </thead>
      <tbody class="table-group-divider" id="workflow-table-body">
        <?php if (empty($workflow)) {
        ?>
          <tr class="bg-light text-dark">
            <td colspan="6" class="bg-light text-center fw-bold">
              گردش کاری برای شما ثبت نشده است.
            </td>
          </tr>
        <?php
        } else {
        ?>
          <?php
          $i = 1;
          $status_persian = [
            "Pending" => "انتظار تایید ناظر",
            "SupervisorApproved" => "تایید توسط ناظر",
            "SupervisorReject" => "رد توسط ناظر",
            "FinalApprove" => "تایید توسط مدیر",
            "ManagerReject" => "رد توسط مدیر",
          ];

          foreach ($workflow as $row) {

          ?>
            <tr
              class="table-primary">
              <td scope="row"><?php echo $i++ ?></td>

              <td><?php echo $row['equipment_id'] ?></td>
              <td><?php echo $status_persian[$row['current_status']] ?></td>
              <td><?php echo $row['active_role'] ?></td>
              <td
                class="dateTime"
                onclick=dateTimeFormatter(this)>
                <?php echo $row['update_at'] ?></td>
              <td>
                <div class="d-grid gap-2">
                  <button
                    type="button"
                    name=""
                    id=""
                    class="btn btn-primary">
                    جزییات
                  </button>
                </div>
              </td>
            </tr>
          <?php } ?>
          <script>
            document.addEventListener("DOMContentLoaded", dateTimeFormatter);

            function dateTimeFormatter(e) {
              const elements = document.querySelectorAll('.dateTime');
              if (elements.length) {
                elements.forEach(el => {
                  const dateTime = el.textContent.trim()
                  const dateTimeObj = new Date(dateTime);
                  const persianDate = dateTimeObj.toLocaleDateString('fa-IR', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: false // Use 24-hour format
                  })
                  el.textContent = persianDate;
                })
              } else {
                setTimeout(dateTimeFormatter, 100);
              }
            }
          </script>
        <?php } ?>
      </tbody>
      <tfoot>

      </tfoot>
    </table>
  </div>

</div>
