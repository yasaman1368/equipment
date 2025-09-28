<?php 
if(!is_manager())wp_redirect( home_url('panel') );
?>

<div class="container mt-5">
  <div class="card shadow p-4">
    <h3 class="text-center mb-4">ورودی و خروجی تجهیزات</h3>

    <!-- انتخاب حالت -->
    <div class="mb-3">
      <label for="mode" class="form-label">انتخاب حالت:</label>
      <select id="mode" class="form-select">
        <option value="import">📥 ورودی تجهیزات</option>
        <option value="export">📤 خروجی تجهیزات</option>
      </select>
    </div>

    <!-- انتخاب فرم -->
    <div class="mb-3">
      <label for="formsSelector" class="form-label">انتخاب فرم:</label>
      <select id="formsSelector" class="form-select">
        <option value="undefined">ابتدا حالت را انتخاب کنید</option>
      </select>
    </div>

    <!-- دکمه دانلود قالب -->
    <div class="mb-3 d-none" id="downloadBtnDiv">
      <p>
        قالب فایل ورود اطلاعات فرم
        <span class="px-2 text-success fw-bold" id="formSelected"></span> :
      </p>
      <button id="excelFormatDownloadBtn" class="btn btn-primary">📥 دریافت قالب</button>
    </div>

    <!-- آپلود فایل -->
    <div class="mb-3" id="excelFileDiv">
      <label for="excelFile" class="form-label">آپلود فایل اکسل:</label>
      <input type="file" id="excelFile" class="form-control" accept=".xlsx,.xls" />
    </div>

    <!-- دکمه اصلی -->
    <div class="text-end">
      <button id="modeBtn" data-mode="import" class="btn btn-success">📑 ورودی اکسل</button>
    </div>
  </div>
</div>

<div class="mt-3 p-2 mx-auto text-center ">
  <a class="btn btn-primary shadow" role="button" onclick="triggerExport()" href="javascript:void(0)">خروجی تمام تجهیزات</a>

  
</div>