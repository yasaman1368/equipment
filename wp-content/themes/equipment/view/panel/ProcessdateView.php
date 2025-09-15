<?php
$equipment_id = sanitize_text_field($_GET["equipment_id"]);

global $wpdb;
$query = $wpdb->prepare("SELECT proccess_history FROM workflow WHERE equipment_id=%s ", $equipment_id);
$result = $wpdb->get_var($query);
$result = json_decode($result, true);
?>
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