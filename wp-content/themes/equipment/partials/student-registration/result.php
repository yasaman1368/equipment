
<div class="registration-stats-container">
  <h4>📊 نتایج ثبت‌نام کلاس‌های فوق برنامه ریاضی</h4>

  <div class="total-stats-box">
    <h6>تعداد کل ثبت‌نام‌ها: <span id="total-registered">0</span> نفر</h6>
  </div>

  <div class="class-buttons">
    <button class="class-btn" data-class="7">مشاهده نتایج کلاس هفتم</button>
    <button class="class-btn" data-class="8">مشاهده نتایج کلاس هشتم</button>
    <button class="class-btn" data-class="9">مشاهده نتایج کلاس نهم</button>
  </div>

  <div id="class-results" class="hidden class-results-box">
    <h4 id="class-title"></h4>
    <ul id="class-details"></ul>
  </div>
</div>
<script>
  document.addEventListener("DOMContentLoaded", () => {
    const totalRegisteredEl = document.getElementById("total-registered");
    const classBtns = document.querySelectorAll(".class-btn");
    const classResultsBox = document.getElementById("class-results");
    const classTitle = document.getElementById("class-title");
    const classDetails = document.getElementById("class-details");

    // 🔹 گرفتن آمار کل از سرور
    fetch("<?php echo admin_url('admin-ajax.php'); ?>?action=get_class_stats")
      .then(res => res.json())
      .then(data => {
     
        if (data.success) {
          totalRegisteredEl.textContent = data.data.total;
        }
      });

    // 🔹 کلیک روی هر کلاس برای دریافت جزئیات
   classBtns.forEach(btn => {
  btn.addEventListener("click", () => {
    const classId = btn.dataset.class;

    fetch("<?php echo admin_url('admin-ajax.php'); ?>?action=get_class_details&class_id=" + classId)
      .then(res => res.json())
      .then(data => {
        classResultsBox.classList.remove("hidden");

        if (data.success) {
          classTitle.textContent = "📘 آمار کلاس " + data.data.class_name;
          classDetails.innerHTML = `
            <li>تعداد کل ثبت‌نامی‌ها: ${data.data.total} نفر</li>
            <li>روزهای زوج: ${data.data.even_days} نفر</li>
            <li>روزهای فرد: ${data.data.odd_days} نفر</li>
            <li>ساعت 16:00 - 17:15 → ${data.data.time_1} نفر</li>
            <li>ساعت 17:20 - 18:35 → ${data.data.time_2} نفر</li>
            <li>ساعت 18:40 - 19:55 → ${data.data.time_3} نفر</li>
            <br>
            <button id="viewDetailsBtn" >
              👀 مشاهده اسامی و جزئیات ثبت‌نام کلاس
            </button>
          `;

          // وقتی روی دکمه کلیک می‌شود
          const viewBtn = document.getElementById("viewDetailsBtn");
          viewBtn.addEventListener("click", () => {
            // آدرس مورد نظر رو اینجا بنویس
            window.location.href = `?page=class-details&class_id=${classId}`;
          });

        } else {
          classTitle.textContent = "❌ خطا در دریافت آمار";
          classDetails.innerHTML = "<li>داده‌ای یافت نشد.</li>";
        }
      });
  });
});

  });
</script>
