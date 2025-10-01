<div class="registration-container">
  <!-- 🔹 Progress Bar -->
  <div class="progress-container">
    <div class="progress-bar" id="progress-bar"></div>
  </div>

  <form id="student-registration-form">
    <!-- مرحله ۰: علاقه‌مندی -->
    <div class="form-step active animate" id="step-0">
      <h4> ثبت‌نام کلاسهای فوق برنامه ریاضی</h4>
      <button type="button" class="next-btn">شروع ثبت نام </button>
    </div>

    <!-- مرحله ۱: اسم -->
    <div class="form-step animate" id="step-1">
      <h2> وارد کردن نام</h2>
      <label for="student-name">نام و نام خانوادگی:</label>
      <input type="text" id="student-name" name="student_name" required />
      <button type="button" class="prev-btn">قبلی</button>
      <button type="button" class="next-btn">ادامه</button>
    </div>

    <!-- مرحله ۲: انتخاب کلاس -->
    <div class="form-step animate" id="step-2">
      <h2> انتخاب کلاس</h2>
      <div class="class-options">
        <div class="class-card" data-class="7">
          <img src="<?PHP echo get_template_directory_uri() . '/assets/img/student-register/7.webp' ?>" alt="کلاس ۱">
          <h5>کلاس حل مسئله و تمرین </h5>
          <h5> ویژه هفتم</h5>
          <p><small style="color: #aaa;">(12 جلسه)</small></p>
        </div>
        <div class="class-card" data-class="8">
          <img src="<?PHP echo get_template_directory_uri() . '/assets/img/student-register/8.webp' ?>" alt="کلاس ۲">
          <h5>کلاس حل مسئله و تمرین </h5>
          <h5> ویژه هشتم</h5>
          <p><small style="color: #aaa;">(12 جلسه)</small></p>
        </div>
        <div class="class-card" data-class="9">
          <img src="<?PHP echo get_template_directory_uri() . '/assets/img/student-register/9.webp' ?>" alt="کلاس ۳">
          <h5>کلاس حل مسئله و تست </h5>
          <h5> ویژه نهم</h5>
          <p><small style="color: #aaa;">(12 جلسه)</small></p>
        </div>
      </div>
      <input type="hidden" name="class_name" id="selected-class" required>
      <button type="button" class="prev-btn">قبلی</button>
    </div>

    <!-- مرحله ۳: انتخاب روز -->
    <div class="form-step animate" id="step-3">
      <h5 style="margin-bottom: 25px;">چه روزهایی براتون مناسب‌تره تا در کلاس شرکت کنید؟</h5>
      <div class="time-options">
        <label><input type="radio" name="class_days" value="even" required> روزهای زوج</label>
        <label><input type="radio" name="class_days" value="odd"> روزهای فرد</label>
      </div>
      <button type="button" class="prev-btn">قبلی</button>
      <button type="button" class="next-btn" id="go-step-4" disabled>ادامه</button>
    </div>

    <!-- مرحله ۴: انتخاب ساعت -->
    <div class="form-step animate" id="step-4">
      <h5 style="margin-bottom: 25px;">چه زمان‌هایی براتون مناسب‌تره تا در کلاس شرکت کنید؟</h5>
      <div class="time-options">
        <label><input type="radio" name="class_time" value="1" required> 16:00 - 17:15</label>
        <label><input type="radio" name="class_time" value="2"> 17:20 - 18:35</label>
        <label><input type="radio" name="class_time" value="3"> 18:40 - 19:55</label>
      </div>
      <button type="button" class="prev-btn">قبلی</button>
      <button type="submit" class="submit-btn" id="finished" disabled> تایید نهایی</button>
    </div>
  </form>
  <div id="success-message" class="hidden">
    <h2>🎉 ثبت‌نام شما با موفقیت انجام شد</h2>
    <p>زمان برگزاری کلاس شما پس از برنامه ریزی اعلام خواهد شد.</p>
    <p style="margin-top: 15px; color: green; font-weight: bold;">✅ تمام مراحل با موفقیت به پایان رسید.</p>
  </div>


</div>

<style>
  body {
    background-color: #008080;
    padding: 20px;
  }

  /* 🔹 Progress bar style */
  .progress-container {
    width: 100%;
    background: #e0e0e0;
    height: 8px;
    border-radius: 10px;
    margin-bottom: 20px;
    overflow: hidden;
  }

  .progress-bar {
    width: 0%;
    height: 100%;
    background: linear-gradient(90deg, #D4AF37, #f5d76e);
    transition: width 0.4s ease-in-out;
  }

  /* 🔹 انیمیشن مرحله‌ها */
  .form-step {
    display: none;
    opacity: 0;
    transform: translateX(30px);
    transition: opacity 0.4s, transform 0.4s;
  }

  .form-step.active {
    display: block;
    opacity: 1;
    transform: translateX(0);
  }

  /* استایل کلی */
  .registration-container {
    max-width: 500px;
    margin: 40px auto;
    background: #F5F5F5;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
    font-family: 'Vazirmatn', sans-serif;
    direction: rtl;
    text-align: center;
    color: black;
  }

  h2 {
    margin-bottom: 20px;
    font-size: 22px;
  }

  input[type="text"] {
    width: 100%;
    padding: 12px;
    margin: 15px 0;
    border-radius: 12px;
    border: none;
    font-size: 16px;
  }

  button {
    padding: 12px 25px;
    margin: 15px 5px;
    border-radius: 12px;
    border: none;
    background: #D4AF37;
    color: white;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s;
    font-family: 'Vazirmatn';
  }

  button:disabled {
    background: #aaa;
    cursor: not-allowed;
  }

  button:hover:not(:disabled) {
    background: #D4AF75;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15);


  }


  /* کارت‌های کلاس */
  .class-options {
    display: grid;
    grid-template-columns: 1fr;
    gap: 15px;
  }

  .class-card {
    background: #fff;
    color: #333;
    border-radius: 15px;
    padding: 15px;
    cursor: pointer;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15);
    transition: 0.3s;
  }

  .class-card img {
    width: 110px;
    height: 150px;
    border-radius: 12px;
    object-fit: cover;
  }

  .class-card h3 {
    margin-top: 10px;
  }

  .class-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
  }

  .class-card.selected {
    border: 3px solid #D4AF37;
  }

  /* گزینه‌های زمان */
  .time-options label {
    display: block;
    background: #fff;
    color: #333;
    padding: 12px;
    border-radius: 12px;
    margin: 10px 0;
    cursor: pointer;
    transition: 0.3s;
  }

  .time-options input {
    margin-left: 10px;
  }

  .time-options label:hover {
    background: #f0f0f0;
  }

  /* پیام موفقیت */
  .hidden {
    display: none;
  }



  #student-name:focus-visible {
    outline: 2px solid #D4AF37;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
  }

  #success-message {
    background: #d4f7d4;
    padding: 20px;
    border-radius: 15px;
    margin-top: 20px;
    color: #333;
  }
</style>

<!-- 📌 اضافه کردن SweetAlert2 از CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  document.addEventListener("DOMContentLoaded", () => {
    const steps = document.querySelectorAll(".form-step");
    const progressBar = document.getElementById("progress-bar");
    let currentStep = 0;

    function updateProgressBar() {
      const percent = ((currentStep) / (steps.length - 1)) * 100;
      progressBar.style.width = percent + "%";
    }

    function showStep(index) {
      steps.forEach((step, i) => {
        step.classList.toggle("active", i === index);
      });
      updateProgressBar();
    }

    // ---------------- مرحله‌ها ----------------
    document.querySelectorAll(".next-btn").forEach(btn => {
      btn.addEventListener("click", () => {
        if (currentStep < steps.length - 1) {
          if (currentStep == 1 && !validateName()) {
            Swal.fire({
              icon: "error",
              title: "خطا",
              text: "نام وارد شده معتبر نیست. لطفا دوباره وارد کنید.",
              confirmButtonText: "باشه"
            });
            return; // مانع ادامه می‌شود
          }
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

    // شروع نمایش مرحله ۰
    showStep(currentStep);
    // ---------------- اعتبارسنجی نام ----------------
    const nameInput = document.getElementById("student-name");
    const step1NextBtn = document.querySelector("#step-1 .next-btn");

    function validateName() {
      const value = nameInput.value.trim();
      const regex = /^[آ-یa-zA-Z\s\.\-]+$/;

      if (value.length < 3) {
        step1NextBtn.disabled = true;
        return false;
      } else if (!regex.test(value)) {
        step1NextBtn.disabled = true;
        return false;
      }
      step1NextBtn.disabled = false;
      return true;
    }

    nameInput.addEventListener("input", validateName);

    // ---------------- انتخاب کلاس ----------------
    const classCards = document.querySelectorAll(".class-card");
    const selectedClassInput = document.getElementById("selected-class");

    classCards.forEach(card => {
      card.addEventListener("click", () => {
        classCards.forEach(c => c.classList.remove("selected"));
        card.classList.add("selected");
        selectedClassInput.value = card.dataset.class;
        setTimeout(() => {
          currentStep++;
          showStep(currentStep);
        }, 500)
      });
    });

    // ---------------- انتخاب روز ----------------
    const dayRadios = document.querySelectorAll("input[name='class_days']");
    const goStep4Btn = document.getElementById("go-step-4");

    dayRadios.forEach(radio => {
      radio.addEventListener("change", () => {
        goStep4Btn.disabled = false;
      });
    });

    // ---------------- انتخاب ساعت ----------------
    const timeRadios = document.querySelectorAll("input[name='class_time']");
    const finishedBtn = document.getElementById("finished");

    timeRadios.forEach(radio => {
      radio.addEventListener("change", () => {
        finishedBtn.disabled = false;
      });
    });

    // ---------------- ارسال فرم ----------------
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
            class_days: formData.get("class_days"),
          }),
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            Swal.fire({
              icon: "success",
              title: "ثبت موفق",
              text: "ثبت‌نام شما با موفقیت انجام شد ✅",
              confirmButtonText: "خیلی خوب"
            }).then(() => {
              // 🔹 فرم رو مخفی کن
              document.getElementById("student-registration-form").style.display = "none";
              // 🔹 پیام موفقیت رو نمایش بده
              document.getElementById("success-message").classList.remove("hidden");
            });
          } else {
            Swal.fire({
              icon: "error",
              title: "خطا",
              text: data.message || "ثبت‌نام انجام نشد.",
              confirmButtonText: "باشه"
            });
          }
        })
        .catch(err => {
          Swal.fire({
            icon: "error",
            title: "مشکل در ارتباط",
            text: "مشکلی رخ داد! لطفاً دوباره تلاش کنید.",
            confirmButtonText: "باشه"
          });
        });
    });
  });
</script>