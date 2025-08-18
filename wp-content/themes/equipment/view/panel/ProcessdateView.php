<?php
$equipment_id = sanitize_text_field($_GET["equipment_id"]);

global $wpdb;
$query = $wpdb->prepare("SELECT proccess_history FROM workflow WHERE equipment_id=%s ", $equipment_id);
$result = $wpdb->get_var($query);
$result = json_decode($result, true);
?>

<!DOCTYPE html>
<html lang="fa">

<head>
  <meta charset="UTF-8">
  <title>Vertical Timeline</title>
  <style>
    body {
      font-family: Vazirmatn, sans-serif;
      direction: rtl;
      background: #f9f9f9;
    }

    .timeline {
      position: relative;
      margin: 0 auto;
      padding: 10px 40px 10px 10px;
      width: 70%;
    }

    .timeline::after {
      content: '';
      position: absolute;
      right: 0;
      top: 0;
      width: 4px;
      height: 100%;
      background: #4CAF50;
      border-radius: 2px;
    }

    .timeline-item {
      position: relative;
      margin: 20px 40px 20px 0;
      padding: 15px;
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
    }

    .timeline-item::before {
      content: '';
      position: absolute;
      right: -48px;
      top: 20px;
      width: 18px;
      height: 18px;
      background: #4CAF50;
      border: 3px solid #fff;
      border-radius: 50%;
      box-shadow: 0 0 0 2px #4CAF50;
    }

    .timeline-item h3 {
      margin: 0 0 5px;
      font-size: 16px;
      color: #333;
    }

    .timeline-item p {
      margin: 0;
      font-size: 14px;
      color: #555;
    }

    .meta {
      font-size: 12px;
      color: #888;
      margin-bottom: 8px;
    }

    .changes {
      margin-top: 8px;
      font-size: 13px;
      color: #666;
      border-top: 1px dashed #eee;
      padding-top: 8px;
    }

    .change-item {
      margin-bottom: 4px;
    }
  </style>
</head>

<body>

  <div class="timeline">
    <?php if (!empty($result)): ?>
      <?php foreach ($result as $item): ?>
        <div class="timeline-item">
          <div class="meta">
            وضعیت: <?php echo esc_html($item['current_status']); ?> |
            نقش: <?php
                  $role = $item['active_role'];
                  if ($role === 'user') echo 'کاربر';
                  elseif ($role === 'supervisor') echo 'ناظر';
                  elseif ($role === 'manager') echo 'مدیر';
                  ?>
          </div>
          <h3>
            <?php
            if ($role === 'user') echo 'کاربر ';
            elseif ($role === 'supervisor') echo 'ناظر ';
            elseif ($role === 'manager') echo 'مدیر ';
            echo esc_html($item['nickname']);
            ?>
          </h3>

          <?php
          $action = $item['action'];
          if (isset($action)): ?>
            <?php if (is_array($action)): ?>
              <?php foreach ($action as $action): ?>
                <?php if (isset($action['comment'])): ?>
                  <p><?php echo esc_html($action['comment']); ?></p>
                <?php endif; ?>

                <?php if (isset($action['field_name'])): ?>
                  <div class="changes">
                    <div class="change-item">
                      تغییر فیلد <?php echo esc_html($action['field_name']); ?>:
                      از <?php echo esc_html($action['initial_value']); ?>
                      به <?php echo esc_html($action['changed_value']); ?>
                    </div>
                  </div>
                <?php endif; ?>
              <?php endforeach; ?>
            <?php elseif (isset($action['comment'])): ?>
              <p><?php echo esc_html($action['comment']); ?></p>
            <?php endif; ?>
          <?php else: ?>
            <p>ایجاد درخواست اولیه</p>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="timeline-item">
        <p>هیچ فعالیتی ثبت نشده است</p>
      </div>
    <?php endif; ?>
  </div>

</body>

</html>