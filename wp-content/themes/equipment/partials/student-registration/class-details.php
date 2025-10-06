<?php
global $wpdb;

if (isset($_GET['class_id'])) {
  $class_id = $_GET['class_id'] ?? 0;
}

$studentsList = $wpdb->get_results(
  $wpdb->prepare("SELECT * FROM student_registration WHERE class_name=%s", $class_id),
  ARRAY_A
);

$class_day = [
  'odd' => 'فرد',
  'even' => 'زوج'
];

$class_time = [
  '1' => '۱۶:۰۰ تا ۱۷:۱۵',
  '2' => '۱۷:۲۰ تا ۱۸:۳۵',
  '3' => '۱۸:۴۰ تا ۱۹:۵۵',
];
$classes = [
  '7' => 'هفتم',
  '8' => 'هشتم',
  '9' => 'نهم',
];

$class_name_title = $classes[$class_id];

?>


  <style>
    h5{
      text-align: center;
      color: white;
      margin-bottom: 25px;
      /* font-size: 24px; */
      text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.2);
    }

    table {
      max-width: 1000px;
      width: 95%;
      margin: 0 auto;
      border-collapse: collapse;
      background: white;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
      animation: fadeIn 1s ease-in-out;
    }

    th,
    td {
      padding: 14px 18px;
      text-align: center;
      font-size: 15px;
    }

    th {
      background: #D4AF37;
      color: #fff;
      font-weight: bold;
      letter-spacing: 0.5px;
    }

    tr:nth-child(even) {
      background-color: #f8f8f8;
    }

    tr:hover {
      background-color: #e7f5f5;
      transition: 0.3s;
    }

    td {
      color: #333;
    }

    .empty {
      text-align: center;
      color: #fff;
      margin-top: 20px;
      font-size: 18px;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    .table-container {
  width: 100%;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
  border-radius: 15px;
}

/* استایل برای موبایل */
@media (max-width: 768px) {
  table {
    width: 100%;
    font-size: 13px;
  }

  th, td {
    padding: 10px 8px;
  }

  h5 {
    font-size: 18px;
  }

  /* سایه نرم‌تر برای موبایل */
  table {
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  }
}

  </style>
</head>

<body>

  <h5>لیست دانش‌آموزان ثبت‌نام‌شده <?php echo $class_name_title ?>
</h5>
<div class="table-container">
  <table>
    <tr>
      <th>ردیف</th>
      <th>نام دانش‌آموز</th>
      <th>زمان کلاس</th>
      <th>روزهای کلاس</th>
      <th>تاریخ ثبت‌نام</th>
    </tr>

    <?php


    if (count($studentsList) > 0) {
      $i = 1;
      foreach ($studentsList as $row) {
        $miladi_date = $row['creat_at'];
        echo "<tr>
                <td>{$i}</td>
                <td>{$row['student_name']}</td>
                <td>{$class_time[$row['class_time']]}</td>
                <td>{$class_day[$row['class_days']]}</td>
                <td class='miladi-date'>{$miladi_date}</td>
            </tr>";
        $i++;
      }
    } else {
      echo "<tr><td colspan='5'>هیچ دانش‌آموزی ثبت‌نام نکرده است.</td></tr>";
    }
    ?>
  </table>
</div>
  <script>
    // 🟢 تابع تبدیل تاریخ میلادی به شمسی با جاوااسکریپت
    function toJalali(gregorianDate) {
      const d = new Date(gregorianDate);
      if (isNaN(d)) return gregorianDate; // در صورت خطا همان مقدار میلادی را برگردان

      const faDate = new Intl.DateTimeFormat('fa-IR-u-nu-latn', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
      }).format(d);

      return faDate;
    }

    // 🟡 اعمال تابع به همه سلول‌هایی که کلاس 'miladi-date' دارند
    document.querySelectorAll('.miladi-date').forEach(td => {
      td.textContent = toJalali(td.textContent);
    });
  </script>

</body>
