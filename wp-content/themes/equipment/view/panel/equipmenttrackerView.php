<?php 
$isManager = is_manager();
if($isManager){
  ?>
  <input type="hidden" id="isManager" value="isManager">
  <?php 
}
?>

<div class="container bg-secondary rounded p-3 container-equipment my-5">
    <div class="text-white m-2 p-3">
        <h4><i class="bi bi-search me-2"></i>ردیاب تجهیزات</h4>
    </div>
    
    <!-- Search Section -->
    <div class="row mt-3">
        <div class="col-md-6 p-2">
            <div class="input-group input-group-lg">
                <span class="input-group-text bg-dark text-white" id="inputGroup-sizing-lg">
                    <i class="bi bi-upc-scan"></i>
                </span>
                <input type="text" 
                       class="form-control"
                       id="serial-input"
                       placeholder="سریال تجهیز را وارد کنید"
                       aria-label="سریال تجهیز"
                       aria-describedby="scan-qr-btn">
                <button id="scan-qr-btn" 
                        class="btn btn-outline-warning" 
                        type="button"
                        data-bs-toggle="tooltip"
                        title="اسکن QR کد">
                    <i class="bi bi-qr-code-scan me-1"></i>اسکن QR
                </button>
            </div>
            <div class="form-text text-light mt-1">
                سریال تجهیز را وارد کنید یا با استفاده از دکمه اسکن QR، کد را اسکن کنید
            </div>
        </div>

        <div class="col-md-3 p-2">
            <div class="input-group input-group-lg">
                <button id="search-btn" 
                        class="btn btn-primary w-100"
                        data-bs-toggle="tooltip"
                        title="جستجوی اطلاعات تجهیز">
                    <i class="bi bi-search me-1"></i>جستجو
                </button>
            </div>
        </div>

        <div class="col-md-3 p-2">
            <div class="input-group input-group-lg">
                <button id="clear-btn" 
                        class="btn btn-outline-light w-100"
                        type="button"
                        data-bs-toggle="tooltip"
                        title="پاک کردن فرم">
                    <i class="bi bi-arrow-clockwise me-1"></i>پاک کردن
                </button>
            </div>
        </div>
    </div>

    <!-- QR Reader Section -->
    <div id="qr-reader" class="mt-4 p-3 bg-dark rounded" style="display: none;">
        <div class="text-center text-white mb-3">
            <i class="bi bi-qr-code me-2"></i>
            <span>دوربین را مقابل QR کد قرار دهید</span>
        </div>
        <div class="text-center">
            <button id="cancel-scan-btn" class="btn btn-sm btn-outline-light">
                <i class="bi bi-x-circle me-1"></i>لغو اسکن
            </button>
        </div>
    </div>

    <!-- Search Results and Form Selection -->
    <div class="row mt-4">
        <div class="col-md-12">
            <!-- Search Result Message -->
            <div class="descipction">
                <p id="description-search-result" class="fw-bold text-warning mb-3"></p>
            </div>
            
            <!-- Form Selector -->
            <div id="form-selector-container" style="display: none;">
                <label for="form-selector" class="form-label text-white">
                    <i class="bi bi-list-ul me-2"></i>انتخاب فرم
                </label>
                <select id="form-selector" class="form-select form-select-lg">
                    <option value="">-- لطفا یک فرم انتخاب کنید --</option>
                </select>
                <div class="form-text text-light">
                    فرم مرتبط با تجهیز مورد نظر را انتخاب کنید
                </div>
            </div>
        </div>
    </div>

    <!-- Form Container -->
    <div id="form-container" class="row mt-4"></div>

    <!-- Action Buttons -->
    <div class="row mt-4">
        <div class="col-md-12 text-left">
            <button id="save-data-btn" 
                    class="btn btn-success btn-lg"
                    style="display: none;">
                <i class="bi bi-check-circle me-1"></i>ذخیره اطلاعات
            </button>
            
            <!-- Loading Indicator -->
            <div id="loading-indicator" class="spinner-border text-primary mt-2" style="display: none;" role="status">
                <span class="visually-hidden">در حال بارگذاری...</span>
            </div>
        </div>
    </div>

    <!-- Status Messages -->
    <div id="status-messages" class="mt-3"></div>
</div>

<!-- Success/Error Modal -->
<div class="modal fade" id="resultModal" tabindex="-1" aria-labelledby="resultModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resultModalLabel">نتیجه عملیات</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="resultModalBody">
                <!-- Modal content will be inserted here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">بستن</button>
            </div>
        </div>
    </div>
</div>

<style>
.container-equipment {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border: 1px solid #dee2e6;
}

#qr-reader {
    border: 2px dashed #6c757d;
}

.form-control:focus, .form-select:focus {
    border-color: #ffc107;
    box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
}

.btn-outline-warning:hover {
    color: #000;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .input-group-lg {
        flex-direction: column;
    }
    
    .input-group-lg > .form-control,
    .input-group-lg > .btn {
        border-radius: 0.375rem;
        margin-bottom: 0.5rem;
    }
    
    .input-group-lg > .input-group-text {
        border-radius: 0.375rem;
        margin-bottom: 0.5rem;
    }
}

/* Custom scrollbar for form container */
#form-container {
    max-height: 600px;
    overflow-y: auto;
}

#form-container::-webkit-scrollbar {
    width: 8px;
}

#form-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

#form-container::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

#form-container::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Field styling */
.field-required .form-label::after {
    content: " *";
    color: #dc3545;
}

/* Validation styles */
.is-invalid {
    border-color: #dc3545 !important;
}

.is-valid {
    border-color: #198754 !important;
}

.invalid-feedback {
    display: none;
    color: #dc3545;
    font-size: 0.875em;
}

.is-invalid ~ .invalid-feedback {
    display: block;
}
</style>

<script>
// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Add clear button functionality
document.getElementById('clear-btn').addEventListener('click', function() {
    document.getElementById('serial-input').value = '';
    document.getElementById('form-container').innerHTML = '';
    document.getElementById('description-search-result').textContent = '';
    document.getElementById('description-search-result').className = 'fw-bold text-warning';
    document.getElementById('form-selector-container').style.display = 'none';
    document.getElementById('form-selector').style.display = 'none';
    document.getElementById('save-data-btn').style.display = 'none';
    document.getElementById('form-selector').value = '';
});

    // Add cancel scan functionality
    document.getElementById('cancel-scan-btn').addEventListener('click', function() {
        document.getElementById('qr-reader').style.display = 'none';
        // Stop QR scanner if active
        if (window.html5QrCode) {
            window.html5QrCode.stop().catch(() => {});
        }
    });
});
</script>