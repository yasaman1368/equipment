
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

  .registration-stats-container {
    max-width: 600px;
    margin: 40px auto;
    background: #f5f5f5;
    padding: 30px;
    border-radius: 20px;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
    text-align: center;
    font-family: 'Vazirmatn', sans-serif;
    direction: rtl;
  }

  .registration-stats-container h3 {
    color: #333;
    margin-bottom: 20px;
  }

  .total-stats-box {
    background: #d4f7d4;
    padding: 10px;
    border-radius: 12px;
    margin-bottom: 25px;
    font-weight: bold;
  }

  .class-buttons {
    display: flex;
    justify-content: center;
    gap: 10px;
    flex-wrap: wrap;
  }

  .class-btn {
    background: #D4AF37;
    color: white;
    border: none;
    border-radius: 12px;
    padding: 12px 25px;
    cursor: pointer;
    transition: 0.3s;
  }

  .class-btn:hover {
    background: #D4AF75;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
  }

  .class-results-box {
    background: #fff;
    border-radius: 15px;
    padding: 20px;
    margin-top: 20px;
    text-align: right;
  }

  .class-results-box h4 {
    margin-bottom: 10px;
    color: #444;
  }

  .class-results-box ul {
    list-style: none;
    padding: 0;
  }

  .class-results-box li {
    margin-bottom: 8px;
    background: #f9f9f9;
    padding: 10px;
    border-radius: 10px;
  }

  .hidden {
    display: none;
  }
</style>
<?php
if(isset($_GET['result']) && $_GET['result']==='registeration'){
  get_template_part('partials/student-registration/result');
  return;
}
get_template_part('partials/student-registration/registration');
?>
