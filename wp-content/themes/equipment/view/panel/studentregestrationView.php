<div class="registration-container">
  <form id="student-registration-form">
    <!-- مرحله ۱: اسم -->
    <div class="form-step active" id="step-1">
      <h2>ثبت نام کلاس</h2>
      <label for="student-name">اسم خود را وارد کنید:</label>
      <input type="text" id="student-name" name="student_name" required />
      <button type="button" class="next-btn">ادامه</button>
    </div>

    <!-- مرحله ۲: انتخاب کلاس -->
    <div class="form-step" id="step-2">
      <h2>انتخاب کلاس</h2>
      <label for="class-select">کلاس مورد نظر:</label>
      <select id="class-select" name="class_name" required>
        <option value="">یک کلاس انتخاب کنید</option>
        <option value="ریاضی">ریاضی</option>
        <option value="علوم">علوم</option>
        <option value="زبان">زبان</option>
      </select>
      <button type="button" class="prev-btn">قبلی</button>
      <button type="button" class="next-btn">ادامه</button>
    </div>

    <!-- مرحله ۳: انتخاب زمان -->
    <div class="form-step" id="step-3">
      <h2>انتخاب زمان</h2>
      <label for="time-select">زمان کلاس:</label>
      <select id="time-select" name="class_time" required>
        <option value="">یک زمان انتخاب کنید</option>
        <option value="۸ صبح">۸ صبح</option>
        <option value="۱۰ صبح">۱۰ صبح</option>
        <option value="۲ بعدازظهر">۲ بعدازظهر</option>
      </select>
      <button type="button" class="prev-btn">قبلی</button>
      <button type="submit" class="submit-btn">تایید نهایی</button>
    </div>
  </form>
  <div id="success-message" class="hidden">
    <h2>✅ ثبت‌نام شما با موفقیت انجام شد!</h2>
  </div>
</div>

<style>
  /* استایل کلی */
  .registration-container {
    max-width: 400px;
    margin: 40px auto;
    background: #fefefe;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    font-family: 'Tahoma', sans-serif;
    direction: rtl;
    text-align: center;
  }

  h2 {
    color: #333;
    margin-bottom: 20px;
  }

  input,
  select {
    width: 100%;
    padding: 12px;
    margin: 15px 0;
    border-radius: 12px;
    border: 1px solid #ccc;
    transition: 0.3s;
  }

  input:focus,
  select:focus {
    border-color: #6c63ff;
    box-shadow: 0 0 6px #6c63ff88;
  }

  button {
    padding: 10px 20px;
    margin: 10px 5px;
    border-radius: 12px;
    border: none;
    background: #6c63ff;
    color: white;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s;
  }

  button:hover {
    background: #524ed8;
  }

  .form-step {
    display: none;
  }

  .form-step.active {
    display: block;
  }

  .hidden {
    display: none;
  }

  #success-message {
    background: #d4f7d4;
    padding: 20px;
    border-radius: 15px;
    margin-top: 20px;
  }
</style>

<script>
  document.addEventListener("DOMContentLoaded", () => {
    const steps = document.querySelectorAll(".form-step");
    let currentStep = 0;

    function showStep(index) {
      steps.forEach((step, i) => {
        step.classList.toggle("active", i === index);
      });
    }

    document.querySelectorAll(".next-btn").forEach(btn => {
      btn.addEventListener("click", () => {
        if (currentStep < steps.length - 1) {
          currentStep++;
          showStep(currentStep);
        }
      });
    });

    document.querySelectorAll(".prev-btn").forEach(btn => {
      btn.addEventListener("click", () => {
        if (currentStep > 0) {
          currentStep--;
          showStep(currentStep);
        }
      });
    });

    // ارسال به بک‌اند
    document.getElementById("student-registration-form").addEventListener("submit", (e) => {
      e.preventDefault();
      const formData = new FormData(e.target);

      fetch("<?php echo admin_url('admin-ajax.php'); ?>", {
          method: "POST",
          body: new URLSearchParams({
            action: "register_student",
            student_name: formData.get("student_name"),
            class_name: formData.get("class_name"),
            class_time: formData.get("class_time"),
          }),
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            document.getElementById("student-registration-form").style.display = "none";
            document.getElementById("success-message").classList.remove("hidden");
          } else {
            alert("خطا: " + data.message);
          }
        })
        .catch(err => alert("مشکلی رخ داد!"));
    });
  });
</script>