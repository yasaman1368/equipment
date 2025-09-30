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



