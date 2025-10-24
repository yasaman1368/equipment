class EquipmentFormHandler {
  constructor() {
    this.initializeElements();
    this.initEventListeners();
    this.initRealTimeValidation();
  }

  initializeElements() {
    const elements = {
      serialInput: "serial-input",
      searchBtn: "search-btn",
      formContainer: "form-container",
      formSelector: "form-selector",
      formSelectorContainer: "form-selector-container",
      saveDataBtn: "save-data-btn",
      scanQrBtn: "scan-qr-btn",
      qrReader: "qr-reader",
      searchResult: "description-search-result",
      loadingIndicator: "loading-indicator",
    };

    Object.entries(elements).forEach(([key, value]) => {
      this[key] = document.getElementById(value);
    });
  }

  initEventListeners() {
    const events = {
      click: [
        [this.searchBtn, () => this.handleSearch()],
        [this.saveDataBtn, () => this.handleSaveData()],
        [this.scanQrBtn, () => this.handleScanQr()],
      ],
      change: [[this.formSelector, () => this.handleFormSelectorChange()]],
    };

    Object.entries(events).forEach(([event, handlers]) => {
      handlers.forEach(([element, handler]) =>
        element?.addEventListener(event, handler)
      );
    });
  }

  async handleSearch() {
    const equipmentId = this.serialInput.value.trim();
    if (!this.validateEquipmentId(equipmentId)) return;

    this.resetUI();
    this.showLoading(true);

    try {
      const data = await this.fetchData(
        "/wp-admin/admin-ajax.php?action=get_equipment_data",
        {
          equipment_id: equipmentId,
        }
      );
      this.handleSearchResponse(data);
    } catch (error) {
      this.handleSearchError(error);
    } finally {
      this.showLoading(false);
    }
  }

  validateEquipmentId(equipmentId) {
    if (!equipmentId) {
      this.showAlert("خطا!", "سریال را بدرستی وارد کنید", "error");
      return false;
    }
    return true;
  }

  resetUI() {
    [this.formContainer, this.formSelector, this.searchResult].forEach(
      (el) => (el.innerHTML = "")
    );
    this.hideFormSelector();
    this.saveDataBtn.style.display = "none";
  }

  hideFormSelector() {
    [this.formSelectorContainer, this.formSelector].forEach(
      (el) => (el.style.display = "none")
    );
  }

  showFormSelector() {
    [this.formSelectorContainer, this.formSelector].forEach(
      (el) => (el.style.display = "block")
    );
  }

  handleSearchResponse(data) {
    if (!data.success) {
      this.showError("خطا در جستجوی اطلاعات تجهیز.");
      return;
    }
    data.data.status
      ? this.handleEquipmentFound(data.data)
      : this.handleEquipmentNotFound();
  }

  handleEquipmentFound(equipmentData) {
    this.setSearchResult("تجهیز مورد نظر یافت شد", "success");
    this.displayEquipmentData(equipmentData);
    this.attachGeoLocationListener();
  }

  handleEquipmentNotFound() {
    this.setSearchResult(
      "تجهیز مورد نظر یافت نشد! برای ثبت اطلاعات فرم مرتبط را انتخاب کنید",
      "warning"
    );
    this.displayFormSelector();
  }

  setSearchResult(text, type) {
    this.searchResult.textContent = text;
    this.searchResult.className = `fw-bold text-${
      type === "success" ? "success" : "warning"
    }`;
  }

  handleSearchError(error) {
    console.error("Error:", error);
    this.showError("خطا در ارتباط با سرور.");
  }

  async displayFormSelector() {
    try {
      const data = await this.fetchData(
        "/wp-admin/admin-ajax.php?action=get_saved_forms"
      );
      data.success
        ? this.populateFormSelector(data.data)
        : this.showError("خطا در دریافت فرم‌ها.");
    } catch (error) {
      console.error("Error:", error);
    }
  }

  populateFormSelector(formData) {
    if (formData.status === "empty") {
      this.formSelector.classList.add("text-danger", "fw-bold");
      this.formSelector.innerHTML =
        '<option value="">برای شما هیچ فرمی ساخته نشده است</option>';
    } else {
      this.formSelector.innerHTML =
        '<option value="">-- انتخاب فرم --</option>';
      formData.forms.forEach((form) => {
        const option = document.createElement("option");
        option.value = form.id;
        option.textContent = form.form_name;
        this.formSelector.appendChild(option);
      });
    }
    this.showFormSelector();
  }

  async handleFormSelectorChange() {
    const formId = this.formSelector.value;
    if (!formId) {
      this.formContainer.innerHTML = "";
      this.saveDataBtn.style.display = "none";
      return;
    }

    try {
      const data = await this.fetchData(
        "/wp-admin/admin-ajax.php?action=get_form_fields",
        {
          form_id: formId,
        }
      );

      if (data.success) {
        this.renderFormFields(data.data.fields);
        this.attachGeoLocationListener();
        this.saveDataBtn.style.display = "block";
        this.setEquipmentIdField();
      } else {
        this.showError("خطا در دریافت فیلدهای فرم.");
      }
    } catch (error) {
      console.error("Error:", error);
    }
  }

  renderFormFields(fields) {
    this.formContainer.innerHTML = "";
    fields.forEach((field) => {
      const fieldDiv = document.createElement("div");
      fieldDiv.classList.add("col-sm-6", "border-bottom", "p-2");
      fieldDiv.appendChild(this.createInputElement(field));
      this.formContainer.appendChild(fieldDiv);
    });
  }

  setEquipmentIdField() {
    const equipmentIdInput = this.formContainer.querySelector("#equipment-id");
    if (equipmentIdInput) {
      equipmentIdInput.value = this.serialInput.value;
      equipmentIdInput.disabled = true;
      equipmentIdInput.required = true;
    }
  }

  createInputElement(item) {
    const isRequired = item.required == 1 || item.required == true;
    const container = document.createElement("div");

    container.appendChild(this.createLabel(item, isRequired));
    container.appendChild(this.createInputByType(item, isRequired));

    return container;
  }

  createLabel(item, isRequired) {
    const label = document.createElement("label");
    label.textContent =
      item.field_name === "equipment_id" ? "سریال تجهیز" : item.field_name;
    label.classList.add("form-label");

    if (isRequired) {
      const requiredSpan = document.createElement("span");
      requiredSpan.className = "text-danger";
      requiredSpan.textContent = " *";
      label.appendChild(requiredSpan);
    }

    return label;
  }

  createInputByType(item, isRequired) {
    const fieldConfig = {
      text: () => this.createTextInput(item, isRequired),
      number: () => this.createTextInput(item, isRequired),
      date: () => this.createTextInput(item, isRequired),
      time: () => this.createTextInput(item, isRequired),
      textarea: () => this.createTextareaInput(item, isRequired),
      select: () => this.createSelectInput(item, isRequired),
      checkbox: () => this.createCheckboxInput(item, isRequired),
      radio: () => this.createRadioInput(item, isRequired),
      file: () => this.createFileInput(item, isRequired),
      image: () => this.createFileInput(item, isRequired),
      qr_code: () => this.createQrCodeInput(item, isRequired),
      button: () => this.createButtonInput(item),
    };

    return (fieldConfig[item.field_type] || fieldConfig.text)();
  }

  createTextInput(item, isRequired) {
    const input = document.createElement("input");
    input.type = item.field_type;
    input.name = `field_${item.id}`;
    input.classList.add("form-control");

    const isEquipmentField = item.field_name === "equipment_id";
    if (isEquipmentField) {
      input.value = document.getElementById("serial-input")?.value || "";
      input.disabled = true;
    } else {
      input.value = item.value ?? "";
      input.disabled = false;
    }

    input.required = isRequired;
    return input;
  }

  createTextareaInput(item, isRequired) {
    const textarea = document.createElement("textarea");
    textarea.classList.add("form-control");
    textarea.name = `field_${item.id}`;
    textarea.rows = 4;
    textarea.value = item.value ?? "";
    if (isRequired) textarea.required = true;
    return textarea;
  }

  createSelectInput(item, isRequired) {
    const select = document.createElement("select");
    select.classList.add("form-control");
    select.name = `field_${item.id}`;
    if (isRequired) select.required = true;

    const options = JSON.parse(item.options || "[]");

    const emptyOption = document.createElement("option");
    emptyOption.value = "";
    emptyOption.textContent = "-- انتخاب کنید --";
    if (isRequired) {
      emptyOption.disabled = true;
      emptyOption.selected = !item.value;
    }
    select.appendChild(emptyOption);

    options.forEach((option) => {
      const optionElement = document.createElement("option");
      optionElement.value = option;
      optionElement.textContent = option;
      if (option === item.value) optionElement.selected = true;
      select.appendChild(optionElement);
    });

    return select;
  }

  createCheckboxInput(item, isRequired) {
    return this.createChoiceInput(
      item,
      isRequired,
      "checkbox",
      "checkbox-group"
    );
  }

  createRadioInput(item, isRequired) {
    return this.createChoiceInput(item, isRequired, "radio", "radio-group");
  }

  createChoiceInput(item, isRequired, type, groupClass) {
    const container = document.createElement("div");
    container.classList.add(groupClass);
    if (isRequired && type === "checkbox") container.dataset.required = "true";

    const values =
      type === "checkbox" ? (item.value ? item.value.split(",") : []) : [];
    const options = JSON.parse(item.options || "[]");

    options.forEach((option) => {
      const optionDiv = document.createElement("div");
      optionDiv.classList.add("form-check");

      const input = document.createElement("input");
      input.type = type;
      input.name = `field_${item.id}${type === "checkbox" ? "[]" : ""}`;
      input.value = option;
      input.classList.add("form-check-input");

      if (type === "checkbox") {
        if (values.includes(option)) input.checked = true;
      } else {
        if (isRequired && !item.value) input.required = true;
        if (option === item.value) input.checked = true;
      }

      const label = document.createElement("label");
      label.textContent = option;
      label.classList.add("form-check-label");

      optionDiv.appendChild(input);
      optionDiv.appendChild(label);
      container.appendChild(optionDiv);
    });

    return container;
  }

  createFileInput(item, isRequired) {
    const container = document.createElement("div");

    if (item.value) {
      const fileLink = document.createElement("a");
      fileLink.href = item.value;
      fileLink.target = "_blank";
      fileLink.textContent = "مشاهده فایل بارگذاری شده";
      fileLink.classList.add("text-primary", "d-block", "mb-2");
      container.appendChild(fileLink);
    }

    const fileInput = document.createElement("input");
    fileInput.type = "file";
    fileInput.classList.add("form-control");
    if (item.field_type === "image") fileInput.accept = "image/*";
    fileInput.name = `field_${item.id}`;
    if (isRequired && !item.value) fileInput.required = true;

    container.appendChild(fileInput);
    return container;
  }

  createQrCodeInput(item, isRequired) {
    const container = document.createElement("div");
    container.classList.add("qr-code-field");

    const inputGroup = document.createElement("div");
    inputGroup.classList.add("input-group", "mb-2");

    const input = document.createElement("input");
    input.type = "text";
    input.classList.add("form-control", "qr-input");
    input.value = item.value ?? "";
    input.name = `field_${item.id}`;
    input.placeholder = "QR کد اسکن شود";
    if (isRequired) input.required = true;

    const scanButton = document.createElement("button");
    scanButton.type = "button";
    scanButton.classList.add("btn", "btn-outline-primary", "scan-qr-btn");

    scanButton.innerHTML = '<i class="bi bi-qr-code-scan me-1"></i>اسکن QR';
    scanButton.addEventListener("click", () =>
      this.handleScanQrForField(input)
    );

    inputGroup.appendChild(input);
    inputGroup.appendChild(scanButton);
    container.appendChild(inputGroup);

    return container;
  }

  createButtonInput(item) {
    const button = document.createElement("button");
    button.id = "capture-geo-btn";
    button.type = "button";
    button.classList.add("form-control", "bg-warning");
    button.textContent = "موقعیت جغرافیایی";
    return button;
  }

  displayEquipmentData(data) {
    this.formContainer.innerHTML = "";

    data.data.forEach((item) => {
      const fieldDiv = document.createElement("div");
      fieldDiv.classList.add("col-sm-6", "border-bottom", "p-2");

      if (this.isFileOrImageField(item)) {
        this.renderFileOrImageField(fieldDiv, item);
      } else if (item.field_type === "geo_location") {
        this.renderGeoLocationField(fieldDiv, item);
      } else {
        fieldDiv.appendChild(this.createInputElement(item));
      }

      this.formContainer.appendChild(fieldDiv);
    });

    this.addActionButtons();
  }

  isFileOrImageField(item) {
    return item.field_type === "file" || item.field_type === "image";
  }

  renderFileOrImageField(container, item) {
    container.appendChild(this.createLabel(item, item.required));

    if (item.value) {
      const imageContainer = document.createElement("div");
      imageContainer.classList.add("image-container");

      const thumbnail = document.createElement("img");
      thumbnail.src = item.value;
      thumbnail.alt = item.field_name;
      thumbnail.classList.add("img-thumbnail", "img-responsive");
      thumbnail.style.maxWidth = "100%";
      thumbnail.style.height = "auto";

      const imageLink = document.createElement("a");
      imageLink.href = item.value;
      imageLink.target = "_blank";
      imageLink.appendChild(thumbnail);
      imageContainer.appendChild(imageLink);
      container.appendChild(imageContainer);
    } else {
      const noFileMessage = document.createElement("span");
      noFileMessage.textContent = "فایلی بارگذاری نشده است.";
      noFileMessage.classList.add("text-muted");
      container.appendChild(noFileMessage);
    }
  }

  renderGeoLocationField(container, item) {
    container.appendChild(this.createLabel(item, false));

    const geoDisplay = document.createElement("div");
    geoDisplay.textContent = item.value || "موقعیت جغرافیایی ثبت نشده است.";
    geoDisplay.classList.add("text-muted");
    container.appendChild(geoDisplay);
  }

  addActionButtons() {
    const buttonGroup = document.createElement("div");
    buttonGroup.classList.add("btn-group");
    buttonGroup.setAttribute("role", "group");
    buttonGroup.setAttribute("aria-label", "عملیات تجهیز");

    const editButton = this.createActionButton("ویرایش", "btn-primary", () =>
      this.enableEditMode()
    );
    const removeButton = this.createActionButton("حذف", "btn-danger", () =>
      this.handleRemoveEquipment()
    );

    const isManager = document.getElementById("isManager")?.value ?? "isUser";
    if (isManager !== "isManager") editButton.disabled = true;

    buttonGroup.appendChild(editButton);
    buttonGroup.appendChild(removeButton);
    this.formContainer.appendChild(buttonGroup);
  }

  createActionButton(text, style, clickHandler) {
    const button = document.createElement("button");
    button.textContent = text;
    button.classList.add("btn", style, "mt-3", "ms-2");
    button.addEventListener("click", clickHandler);
    return button;
  }

  async handleSaveData() {
    if (!this.validateRequiredFields()) return;

    const formData = this.prepareFormData();

    try {
      const data = await this.fetchData(
        "/wp-admin/admin-ajax.php?action=save_equipment_data",
        formData
      );

      if (data.success) {
        this.showSuccess(data.data.message);
        this.resetAfterSave();
      } else {
        this.showError("خطا در ذخیره‌سازی داده‌ها.");
      }
    } catch (error) {
      console.error("Error:", error);
    }
  }

  resetAfterSave() {
    this.formContainer.innerHTML = "";
    this.formSelectorContainer.style.display = "none";
    this.searchResult.style.display = "none";
    this.saveDataBtn.style.display = "none";
  }

  prepareFormData() {
    const formData = new FormData();
    formData.append("equipment_id", this.serialInput.value);
    formData.append("form_id", this.formSelector.value);

    const formFields = {};

    this.formContainer
      .querySelectorAll("input, select, textarea")
      .forEach((input) => {
        const fieldId = input.name.replace("field_", "");

        if (input.type === "file") {
          if (input.files.length > 0) {
            formData.append(`field_${fieldId}`, input.files[0]);
          }
        } else if (input.type === "checkbox") {
          if (input.checked) {
            if (!formFields[fieldId]) formFields[fieldId] = [];
            formFields[fieldId].push(input.value);
          }
        } else if (input.type === "radio") {
          if (input.checked) formFields[fieldId] = input.value;
        } else {
          formFields[fieldId] = input.value;
        }
      });

    Object.keys(formFields).forEach((key) => {
      if (Array.isArray(formFields[key])) {
        formFields[key] = formFields[key].join(",");
      }
    });

    formData.append("form_data", JSON.stringify(formFields));
    return formData;
  }

  validateRequiredFields() {
    const requiredFields = this.formContainer.querySelectorAll("[required]");
    let isValid = true;
    const missingFields = [];

    requiredFields.forEach((field) => {
      if (!this.isFieldValid(field)) {
        isValid = false;
        field.classList.add("is-invalid");
        missingFields.push(this.getFieldName(field));
      } else {
        field.classList.remove("is-invalid");
        field.classList.add("is-valid");
      }
    });

    const requiredCheckboxGroups = this.formContainer.querySelectorAll(
      '.checkbox-group[data-required="true"]'
    );
    requiredCheckboxGroups.forEach((group) => {
      const checkboxes = group.querySelectorAll('input[type="checkbox"]');
      const isChecked = Array.from(checkboxes).some((cb) => cb.checked);

      if (!isChecked) {
        isValid = false;
        checkboxes.forEach((cb) => cb.classList.add("is-invalid"));
        missingFields.push(this.getFieldName(group));
      } else {
        checkboxes.forEach((cb) => {
          cb.classList.remove("is-invalid");
          cb.classList.add("is-valid");
        });
      }
    });

    if (!isValid) {
      this.showValidationError(missingFields);
    }

    return isValid;
  }

  isFieldValid(field) {
    const validators = {
      checkbox: () =>
        Array.from(
          this.formContainer.querySelectorAll(`input[name="${field.name}"]`)
        ).some((cb) => cb.checked),
      radio: () =>
        Array.from(
          this.formContainer.querySelectorAll(`input[name="${field.name}"]`)
        ).some((radio) => radio.checked),
      file: () => field.files.length > 0,
      select: () => field.value !== "",
      default: () => field.value.trim() !== "",
    };

    return (validators[field.type] || validators.default)();
  }

  getFieldName(field) {
    const container = field.closest(".col-sm-6");
    const label = container?.querySelector(".form-label");
    return label ? label.textContent.replace("*", "").trim() : "این فیلد";
  }

  showValidationError(missingFields) {
    this.showAlert(
      "فیلدهای اجباری",
      `لطفا فیلدهای زیر را پر کنید:<br><strong>${missingFields.join(
        "، "
      )}</strong>`,
      "warning",
      "متوجه شدم"
    );

    const firstInvalid = this.formContainer.querySelector(".is-invalid");
    if (firstInvalid) {
      firstInvalid.scrollIntoView({ behavior: "smooth", block: "center" });
      firstInvalid.focus();
    }
  }

  enableEditMode() {
    // فعال کردن تمام ورودی‌ها برای ویرایش
    this.formContainer
      .querySelectorAll("input, select, textarea")
      .forEach((input) => {
        if (input.type !== "file" && !input.classList.contains("qr-input")) {
          input.disabled = false;
        }
      });

    // فقط دکمه‌ی "ویرایش" اصلی را به "ذخیره" تغییر بده
    const editButton = this.formContainer.querySelector(
      ".btn-group .btn-primary"
    );
    if (editButton) {
      editButton.textContent = "ذخیره";
      editButton.classList.replace("btn-primary", "btn-success");
      editButton.onclick = () => this.handleSaveData();
    }

    // ✅ دکمه‌های اسکن QR را به حال خود بگذار، فقط اطمینان بده که عملکرد اسکن‌شان برقرار است
    this.formContainer.querySelectorAll(".scan-qr-btn").forEach((btn) => {
      const input = btn.closest(".input-group").querySelector(".qr-input");
      btn.onclick = () => this.handleScanQrForField(input);
    });
  }

  initRealTimeValidation() {
    ["input", "change"].forEach((event) => {
      this.formContainer.addEventListener(event, (e) =>
        this.validateSingleField(e.target)
      );
    });
  }

  validateSingleField(field) {
    if (field.hasAttribute("required")) {
      const isValid = this.isFieldValid(field);
      field.classList.toggle("is-invalid", !isValid);
      field.classList.toggle("is-valid", isValid);
    }
  }

  handleScanQrForField(inputField) {
    const html5QrCode = new Html5Qrcode("qr-reader");
    this.qrReader.style.display = "block";

    html5QrCode
      .start(
        { facingMode: "environment" },
        { fps: 10, qrbox: 250 },
        (qrCodeMessage) => {
          inputField.value = qrCodeMessage;
          html5QrCode.stop();
          this.qrReader.style.display = "none";
          this.showSuccess("QR کد با موفقیت اسکن شد");
        }
      )
      .catch((err) => {
        console.error("Error starting QR scanner:", err);
        this.qrReader.style.display = "none";
      });
  }

  handleScanQr() {
    const html5QrCode = new Html5Qrcode("qr-reader");
    html5QrCode
      .start(
        { facingMode: "environment" },
        { fps: 10, qrbox: 250 },
        (qrCodeMessage) => {
          this.serialInput.value = qrCodeMessage;
          html5QrCode.stop();
        }
      )
      .catch((err) => {
        console.error("Error:", err);
      });
  }

  handleGeoLocation() {
    if (!navigator.geolocation) {
      this.showError("مرورگر شما از دریافت موقعیت جغرافیایی پشتیبانی نمی‌کند.");
      return;
    }

    navigator.geolocation.getCurrentPosition(
      (position) => {
        const { latitude, longitude } = position.coords;
        const geoInput = document.createElement("input");
        geoInput.type = "hidden";
        geoInput.name = "geo_location";
        geoInput.value = `${latitude},${longitude}`;
        this.formContainer.appendChild(geoInput);
        this.showSuccess("موقعیت جغرافیایی با موفقیت ثبت شد.");
      },
      (error) => {
        this.showError("خطا در دریافت موقعیت جغرافیایی.");
      },
      { timeout: 10000 }
    );
  }

  async handleRemoveEquipment() {
    const equipmentId = this.serialInput.value.trim();
    if (!this.validateEquipmentId(equipmentId)) return;

    const result = await this.showAlert(
      "آیا مطمئن هستید؟",
      "این عمل قابل بازگشت نیست!",
      "warning",
      "بله، حذف شود!",
      "لغو",
      true
    );

    if (!result.isConfirmed) return;

    try {
      const data = await this.fetchData(
        "/wp-admin/admin-ajax.php?action=remove_equipment_data",
        {
          equipment_id: equipmentId,
        }
      );

      data.success
        ? this.showSuccess("تجهیز با موفقیت حذف شد.", true)
        : this.showError("خطا در حذف تجهیز.");
    } catch (error) {
      console.error("Error:", error);
    }
  }

  showLoading(show) {
    if (this.loadingIndicator && this.searchBtn) {
      this.loadingIndicator.style.display = show ? "inline-block" : "none";
      this.searchBtn.disabled = show;
      this.searchBtn.innerHTML = show
        ? '<i class="bi bi-hourglass-split me-1"></i>در حال جستجو...'
        : '<i class="bi bi-search me-1"></i>جستجو';
    }
  }

  showSuccess(message, redirect = false) {
    this.showAlert("موفق!", message, "success").then(() => {
      if (redirect)
        window.location.href =
          window.location.origin + "/panel/equipmenttracker";
    });
  }

  showError(message) {
    this.showAlert("خطا!", message, "error");
  }

  showAlert(
    title,
    text,
    icon,
    confirmText = "متوجه شدم",
    cancelText = "",
    showCancel = false
  ) {
    return Swal.fire({
      title,
      text,
      icon,
      showCancelButton: showCancel,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: confirmText,
      cancelButtonText: cancelText,
    });
  }

  attachGeoLocationListener() {
    this.captureGeoBtn = document.getElementById("capture-geo-btn");
    this.captureGeoBtn?.addEventListener("click", () =>
      this.handleGeoLocation()
    );
  }

  fetchData(url, body = {}) {
    const options =
      body instanceof FormData
        ? { method: "POST", body }
        : {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams(body),
          };

    return fetch(url, options).then((response) => response.json());
  }
}

document.addEventListener("DOMContentLoaded", () => new EquipmentFormHandler());
