<?php 
$isManager=is_manager();
if($isManager){
  ?>
  <input type="hidden" id="isManager"  value="isManager">
  <?php 
}
?>

<div class="container bg-secondary rounded p-3 container-equipment my-5">
    <div class="text-white m-2 p-3">
        <h4>ردیاب تجهیزات</h4>
    </div>
    <div class="row mt-3">
        <div class="col-md-6 p-2">
            <div class="input-group input-group-lg">
                <span class="input-group-text" id="inputGroup-sizing-lg"><i class="bi bi-card-text"></i></span>
                <input type="text" class="form-control"
                    aria-describedby="scan-qr-btn"
                    aria-label="Sizing example input"
                    id="serial-input"
                    placeholder="سریال تجهیز را وارد کنید">
                <button id="scan-qr-btn" class="btn btn-outline-light" type="button">اسکن QR Code</button>
            </div>

        </div>



        <div class="col-md-3 p-2 ">
            <div class="input-group input-group-lg">
                <button id="search-btn" class="btn btn-outline-light w-100 ">جستجو</button>
            </div>
        </div>

    </div>
    <div id="qr-reader" class="mt-4"></div>
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="descipction">
                <p id="description-search-result" class=" fw-bold text-warning">
                </p>
            </div>
            <select id="form-selector" class="form-control" style="display: none;"></select>
        </div>
    </div>
    <div id="form-container" class="row mt-4"></div>
    <div class="row mt-4">
        <div class="col-md-12 text-right">
            <button id="save-data-btn" class="btn btn-success" style="display: none;">ذخیره اطلاعات</button>
        </div>
    </div>
</div>