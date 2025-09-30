
<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" href="<?php echo  get_template_directory_uri() . '/assets/img/gassFavIcon.png' ?>" type="image/png">

  <title>سیستم ردیاب تجهیزات</title>
  <!-- Bootstrap CSS RTL (latest version) -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css"
    rel="stylesheet" />
  <!-- Bootstrap Icons -->
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
  <link rel="stylesheet" href="<?php echo get_template_directory_uri() ?>/assets/css/style.css" />
  <!-- sweet alert -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Select2 CSS -->
  <link rel="stylesheet" href='https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css' />


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

</head>
<body>
  
  <?php
require_once $view;
?>
</body>
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
</html>