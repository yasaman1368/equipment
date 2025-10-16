// JavaScript Modern Class - Optimized Version
class FormManager {
  constructor() {
    this.currentEditingFormId = null;
    this.fieldCounter = 0;
    this.modals = {};
    this.elements = {};
    this.equipmentFieldAdded = false; // کنترل اضافه شدن فیلد سریال تجهیز
    this.init();
  }

  init() {
    this.cacheElements();
    this.initModals();
    this.bindEvents();
    this.loadInitialData();
  }

  cacheElements() {
    this.elements = {
      // Containers
      mainContainer: document.getElementById("main-container-form"),
      savedFormsContainer: document.getElementById("show-saved-forms"),
      formBuilder: document.getElementById("form-container-builder"),
      formPreview: document.getElementById("display-form"),

      // Buttons
      addNewFormBtn: document.getElementById("add-new-form"),
      showFormsBtn: document.getElementById("show-forms"),
      saveFormBtn: document.getElementById("save-form-btn"),
      saveNewFieldBtn: document.getElementById("save-new-field"),
      saveFormNameBtn: document.getElementById("save-modal-form-name"),
      addNewOptionBtn: document.getElementById("add-new-option"),

      // Form Elements
      formSelector: document.getElementById("form-selector"),
      inputType: document.getElementById("input-type"),
      newFeatureName: document.getElementById("new-feature-name"),
      newFormName: document.getElementById("new-form-name"),

      // Display Elements
      placeholderFormName: document.getElementById("placeholder-form-name"),
      showFormNameCheck: document.getElementById("show-form-name-check"),
      locationsDisplay: document.getElementById("locations-display"),

      // Modals
      optionsContainer: document.getElementById("container-options-modal"),
      inputOptionContainer: document.getElementById("input-option-container"),
      selectedInput: document.getElementById("selected-input"),
    };
  }

  initModals() {
    this.modals.addForm = new bootstrap.Modal("#modal-add-form");
    this.modals.addFormName = new bootstrap.Modal("#modal-add-form-name");
  }

  bindEvents() {
    // Main navigation
    this.elements.addNewFormBtn.addEventListener("click", () =>
      this.showFormBuilder()
    );
    this.elements.showFormsBtn.addEventListener("click", () =>
      this.showSavedForms()
    );

    // Form operations
    this.elements.saveFormBtn.addEventListener("click", () => this.saveForm());
    this.elements.saveFormNameBtn.addEventListener("click", () =>
      this.createNewForm()
    );
    this.elements.saveNewFieldBtn.addEventListener("click", (e) =>
      this.addNewField(e)
    );

    // Field type changes
    this.elements.inputType.addEventListener("change", () =>
      this.handleFieldTypeChange()
    );
    this.elements.addNewOptionBtn.addEventListener("click", () =>
      this.addOptionField()
    );

    // Form selection
    this.elements.formSelector.addEventListener("change", (e) =>
      this.loadForm(e.target.value)
    );
  }

  async loadInitialData() {
    try {
      await this.loadSavedForms();
    } catch (error) {
      this.showNotification("خطا در بارگذاری داده‌ها", "error");
    }
  }

  // Core functionality methods
  showFormBuilder() {
    this.elements.savedFormsContainer.classList.add("d-none");
    this.elements.mainContainer.classList.remove("d-none");
    this.resetFormBuilder();
  }

  showSavedForms() {
    this.elements.mainContainer.classList.add("d-none");
    this.elements.savedFormsContainer.classList.remove("d-none");
    this.loadSavedForms();
  }

  async createNewForm() {
    const formName = this.elements.newFormName.value.trim();

    if (!this.validateFormName(formName)) return;

    this.elements.placeholderFormName.querySelector(
      ".placeholder-text"
    ).textContent = formName;
    this.showFormBuilder();
    this.modals.addFormName.hide();
    this.resetModal("#modal-form-name");
  }

  getFieldData() {
    const fieldName = this.elements.newFeatureName.value.trim();
    const fieldType = this.elements.inputType.value;
    const isRequired =
      document.getElementById("field-required")?.checked || false;
    const isUnique = document.getElementById("field-unique")?.checked || false;

    // Get options for select, checkbox, radio
    let options = [];
    if (["select", "checkbox", "radio", "multiselect"].includes(fieldType)) {
      const optionInputs =
        this.elements.inputOptionContainer.querySelectorAll(".input-option");
      options = Array.from(optionInputs)
        .map((input) => input.value.trim())
        .filter((value) => value !== "");
    }

    return {
      field_name: fieldName,
      field_type: fieldType,
      required: isRequired,
      unique: isUnique,
      options: options,
      placeholder: this.getPlaceholderByType(fieldType),
    };
  }

  getPlaceholderByType(fieldType) {
    const placeholders = {
      text: "متن خود را وارد کنید",
      number: "عدد وارد کنید",
      email: "example@email.com",
      tel: "۰۹۱۲۳۴۵۶۷۸۹",
      date: "تاریخ را انتخاب کنید",
      time: "زمان را انتخاب کنید",
      file: "فایل را انتخاب کنید",
      image: "تصویر را انتخاب کنید",
    };
    return placeholders[fieldType] || "مقدار را وارد کنید";
  }

  async addNewField(event) {
    event.preventDefault();

    const fieldData = this.getFieldData();
    if (!this.validateFieldData(fieldData)) return;

    const fieldElement = this.createFieldElement(fieldData);
    this.elements.formBuilder.appendChild(fieldElement);

    this.modals.addForm.hide();
    this.resetModal("#modal-new-field-form");
  }

  handleFieldTypeChange() {
    const fieldType = this.elements.inputType.value;
    const optionsContainer = this.elements.optionsContainer;

    // Show/hide options container for field types that need options
    if (["select", "checkbox", "radio", "multiselect"].includes(fieldType)) {
      optionsContainer.classList.remove("d-none");
      this.elements.selectedInput.textContent =
        this.getFieldTypeLabel(fieldType);
      this.initializeOptionsContainer();
    } else {
      optionsContainer.classList.add("d-none");
    }

    // Handle special field types
    if (fieldType === "geo") {
      this.elements.newFeatureName.value = "موقعیت جغرافیایی";
      this.elements.newFeatureName.disabled = true;
    } else {
      this.elements.newFeatureName.disabled = false;
    }
  }

  initializeOptionsContainer() {
    this.elements.inputOptionContainer.innerHTML = "";
    // Add first option input
    this.addOptionField();
  }

  addOptionField() {
    const optionId = `option-${Date.now()}`;
    const optionHtml = `
            <div class="input-group mb-2" data-option-id="${optionId}">
                <input type="text" class="form-control input-option" placeholder="مقدار گزینه را وارد کنید">
                <button type="button" class="btn btn-outline-danger remove-option" data-option-id="${optionId}">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        `;
    this.elements.inputOptionContainer.insertAdjacentHTML(
      "beforeend",
      optionHtml
    );

    // Add event listener for remove button
    const removeBtn = this.elements.inputOptionContainer.querySelector(
      `[data-option-id="${optionId}"] .remove-option`
    );
    removeBtn.addEventListener("click", (e) => {
      e.target.closest(".input-group").remove();
    });
  }

  // Field creation methods
  createFieldElement(fieldData) {
    const fieldId = `field-${++this.fieldCounter}`;
    const fieldTemplate = this.getFieldTemplate(fieldData, fieldId);

    const fieldWrapper = document.createElement("div");
    fieldWrapper.className = "col-md-6";
    fieldWrapper.innerHTML = fieldTemplate;

    this.addFieldActions(fieldWrapper, fieldId);
    return fieldWrapper;
  }

  getFieldTemplate(fieldData, fieldId) {
    const templates = {
      text: () => this.createInputTemplate(fieldData, fieldId, "text"),
      number: () => this.createInputTemplate(fieldData, fieldId, "number"),
      email: () => this.createInputTemplate(fieldData, fieldId, "email"),
      tel: () => this.createInputTemplate(fieldData, fieldId, "tel"),
      date: () => this.createInputTemplate(fieldData, fieldId, "date"),
      time: () => this.createInputTemplate(fieldData, fieldId, "time"),
      file: () => this.createInputTemplate(fieldData, fieldId, "file"),
      image: () => this.createInputTemplate(fieldData, fieldId, "file"),
      select: () => this.createSelectTemplate(fieldData, fieldId),
      multiselect: () => this.createMultiSelectTemplate(fieldData, fieldId),
      checkbox: () => this.createCheckboxTemplate(fieldData, fieldId),
      radio: () => this.createRadioTemplate(fieldData, fieldId),
      textarea: () => this.createTextareaTemplate(fieldData, fieldId),
      geo: () => this.createGeoTemplate(fieldData, fieldId),
    };

    return templates[fieldData.field_type]
      ? templates[fieldData.field_type]()
      : this.createInputTemplate(fieldData, fieldId, "text");
  }

  createInputTemplate(fieldData, fieldId, type) {
    return `
            <div class="field-card card border h-100" data-field-id="${fieldId}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <label class="form-label fw-semibold">
                            ${fieldData.field_name}
                            ${
                              fieldData.required
                                ? '<span class="text-danger">*</span>'
                                : ""
                            }
                        </label>
                        <span class="badge bg-secondary">${this.getFieldTypeLabel(
                          fieldData.field_type
                        )}</span>
                    </div>
                    <input type="${type}" class="form-control" 
                           ${fieldData.required ? "required" : ""}
                           placeholder="${fieldData.placeholder || ""}">
                    ${
                      fieldData.helpText
                        ? `<div class="form-text">${fieldData.helpText}</div>`
                        : ""
                    }
                    <div class="field-actions mt-2">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-field">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
  }

  createSelectTemplate(fieldData, fieldId) {
    const options = fieldData.options
      .map((option) => `<option value="${option}">${option}</option>`)
      .join("");

    return `
            <div class="field-card card border h-100" data-field-id="${fieldId}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <label class="form-label fw-semibold">
                            ${fieldData.field_name}
                            ${
                              fieldData.required
                                ? '<span class="text-danger">*</span>'
                                : ""
                            }
                        </label>
                        <span class="badge bg-secondary">${this.getFieldTypeLabel(
                          fieldData.field_type
                        )}</span>
                    </div>
                    <select class="form-select" ${
                      fieldData.required ? "required" : ""
                    }>
                        <option value="">-- انتخاب کنید --</option>
                        ${options}
                    </select>
                    <div class="field-actions mt-2">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-field">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
  }

  createCheckboxTemplate(fieldData, fieldId) {
    const options = fieldData.options
      .map(
        (option, index) => `
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="${fieldId}" value="${option}" id="${fieldId}-${index}">
                <label class="form-check-label" for="${fieldId}-${index}">${option}</label>
            </div>
        `
      )
      .join("");

    return `
            <div class="field-card card border h-100" data-field-id="${fieldId}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <label class="form-label fw-semibold">
                            ${fieldData.field_name}
                            ${
                              fieldData.required
                                ? '<span class="text-danger">*</span>'
                                : ""
                            }
                        </label>
                        <span class="badge bg-secondary">${this.getFieldTypeLabel(
                          fieldData.field_type
                        )}</span>
                    </div>
                    <div class="checkbox-container">
                        ${options}
                    </div>
                    <div class="field-actions mt-2">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-field">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
  }

  createRadioTemplate(fieldData, fieldId) {
    const options = fieldData.options
      .map(
        (option, index) => `
            <div class="form-check">
                <input class="form-check-input" type="radio" name="${fieldId}" value="${option}" id="${fieldId}-${index}">
                <label class="form-check-label" for="${fieldId}-${index}">${option}</label>
            </div>
        `
      )
      .join("");

    return `
            <div class="field-card card border h-100" data-field-id="${fieldId}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <label class="form-label fw-semibold">
                            ${fieldData.field_name}
                            ${
                              fieldData.required
                                ? '<span class="text-danger">*</span>'
                                : ""
                            }
                        </label>
                        <span class="badge bg-secondary">${this.getFieldTypeLabel(
                          fieldData.field_type
                        )}</span>
                    </div>
                    <div class="radio-container">
                        ${options}
                    </div>
                    <div class="field-actions mt-2">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-field">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
  }

  createTextareaTemplate(fieldData, fieldId) {
    return `
            <div class="field-card card border h-100" data-field-id="${fieldId}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <label class="form-label fw-semibold">
                            ${fieldData.field_name}
                            ${
                              fieldData.required
                                ? '<span class="text-danger">*</span>'
                                : ""
                            }
                        </label>
                        <span class="badge bg-secondary">${this.getFieldTypeLabel(
                          fieldData.field_type
                        )}</span>
                    </div>
                    <textarea class="form-control" rows="3" 
                              ${fieldData.required ? "required" : ""}
                              placeholder="${
                                fieldData.placeholder || ""
                              }"></textarea>
                    <div class="field-actions mt-2">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-field">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
  }

  createGeoTemplate(fieldData, fieldId) {
    return `
            <div class="field-card card border h-100" data-field-id="${fieldId}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <label class="form-label fw-semibold">
                            ${fieldData.field_name}
                            ${
                              fieldData.required
                                ? '<span class="text-danger">*</span>'
                                : ""
                            }
                        </label>
                        <span class="badge bg-secondary">${this.getFieldTypeLabel(
                          fieldData.field_type
                        )}</span>
                    </div>
                    <button type="button" class="btn btn-outline-warning w-100 geo-btn">
                        <i class="bi bi-geo-alt"></i>
                        دریافت موقعیت جغرافیایی
                    </button>
                    <div class="geo-coordinates mt-2 d-none">
                        <small class="text-muted">عرض جغرافیایی: <span class="latitude">-</span></small><br>
                        <small class="text-muted">طول جغرافیایی: <span class="longitude">-</span></small>
                    </div>
                    <div class="field-actions mt-2">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-field">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
  }

  addFieldActions(fieldWrapper, fieldId) {
    const removeBtn = fieldWrapper.querySelector(".remove-field");
    if (removeBtn) {
        removeBtn.addEventListener("click", () => {
            this.removeField(fieldWrapper);
        });
    }

    // Add geo location functionality
    const geoBtn = fieldWrapper.querySelector(".geo-btn");
    if (geoBtn) {
        geoBtn.addEventListener("click", () => {
            this.getGeolocation(fieldWrapper);
        });
    }
}

  // اضافه کردن این متد در کلاس FormManager
  async removeFieldFromServer(fieldId) {
    if (!fieldId) return false;

    try {
      const response = await this.apiCall("remove_form_field", {
        field_id: fieldId,
      });

      if (response.success) {
        this.showNotification("فیلد با موفقیت حذف شد", "success");
        return true;
      } else {
        this.showNotification("خطا در حذف فیلد از سرور", "error");
        return false;
      }
    } catch (error) {
      this.showNotification("خطا در ارتباط با سرور", "error");
      return false;
    }
  }

 async removeField(fieldWrapper) {
    if (!confirm("آیا از حذف این فیلد مطمئن هستید؟")) {
        return;
    }

    // نمایش وضعیت لودینگ
    const removeBtn = fieldWrapper.querySelector('.remove-field');
    const originalHtml = removeBtn.innerHTML;
    removeBtn.innerHTML = '<i class="bi bi-hourglass-split"></i>';
    removeBtn.disabled = true;

    try {
        // اگر در حالت ویرایش هستیم و فیلد از سرور باید حذف شود
        const fieldCard = fieldWrapper.querySelector('.field-card');
        const originalFieldId = fieldCard?.dataset?.originalFieldId;
        
        if (this.currentEditingFormId && originalFieldId) {
            // حذف از سرور
            const success = await this.removeFieldFromServer(originalFieldId);
            if (success) {
                fieldWrapper.remove();
                this.showNotification("فیلد با موفقیت حذف شد", "success");
            }
        } else {
            // فقط حذف از رابط کاربری
            fieldWrapper.remove();
            this.showNotification("فیلد حذف شد", "success");
        }
    } catch (error) {
        this.showNotification("خطا در حذف فیلد", "error");
    } finally {
        // بازگرداندن وضعیت دکمه
        removeBtn.innerHTML = originalHtml;
        removeBtn.disabled = false;
    }
}

  getGeolocation(fieldWrapper) {
    if (!navigator.geolocation) {
      this.showNotification(
        "مرورگر شما از دریافت موقعیت جغرافیایی پشتیبانی نمی‌کند",
        "error"
      );
      return;
    }

    const geoBtn = fieldWrapper.querySelector(".geo-btn");
    const coordinatesDiv = fieldWrapper.querySelector(".geo-coordinates");
    const latitudeSpan = fieldWrapper.querySelector(".latitude");
    const longitudeSpan = fieldWrapper.querySelector(".longitude");

    geoBtn.disabled = true;
    geoBtn.innerHTML =
      '<i class="bi bi-hourglass-split"></i> در حال دریافت موقعیت...';

    navigator.geolocation.getCurrentPosition(
      (position) => {
        const lat = position.coords.latitude;
        const lng = position.coords.longitude;

        latitudeSpan.textContent = lat.toFixed(6);
        longitudeSpan.textContent = lng.toFixed(6);
        coordinatesDiv.classList.remove("d-none");

        geoBtn.disabled = false;
        geoBtn.innerHTML =
          '<i class="bi bi-check-circle"></i> موقعیت دریافت شد';
        this.showNotification(
          "موقعیت جغرافیایی با موفقیت دریافت شد",
          "success"
        );
      },
      (error) => {
        geoBtn.disabled = false;
        geoBtn.innerHTML =
          '<i class="bi bi-geo-alt"></i> دریافت موقعیت جغرافیایی';

        let errorMessage = "خطا در دریافت موقعیت جغرافیایی";
        switch (error.code) {
          case error.PERMISSION_DENIED:
            errorMessage = "دسترسی به موقعیت جغرافیایی رد شد";
            break;
          case error.POSITION_UNAVAILABLE:
            errorMessage = "اطلاعات موقعیت در دسترس نیست";
            break;
          case error.TIMEOUT:
            errorMessage = "دریافت موقعیت زمان‌بر شد";
            break;
        }
        this.showNotification(errorMessage, "error");
      }
    );
  }

  // Utility methods
  validateFormName(formName) {
    if (!formName) {
      this.showNotification("لطفاً نام فرم را وارد کنید", "error");
      return false;
    }
    if (formName.length < 3) {
      this.showNotification("نام فرم باید حداقل ۳ کاراکتر باشد", "error");
      return false;
    }
    return true;
  }

  validateFieldData(fieldData) {
    if (!fieldData.field_name || !fieldData.field_type) {
      this.showNotification("لطفاً نام و نوع فیلد را وارد کنید", "error");
      return false;
    }

    // Validate options for field types that require them
    if (
      ["select", "checkbox", "radio", "multiselect"].includes(
        fieldData.field_type
      )
    ) {
      if (fieldData.options.length === 0) {
        this.showNotification(
          "لطفاً حداقل یک گزینه برای این نوع فیلد وارد کنید",
          "error"
        );
        return false;
      }
    }

    return true;
  }

  async loadSavedForms() {
    try {
      const response = await this.apiCall("get_saved_forms");
      if (response.success) {
        this.populateFormSelector(response.data.forms);
      }
    } catch (error) {
      this.showNotification("خطا در بارگذاری فرم‌ها", "error");
    }
  }

  populateFormSelector(forms) {
    this.elements.formSelector.innerHTML =
      '<option value="">-- لطفاً یک فرم انتخاب کنید --</option>';

    forms.forEach((form) => {
      const option = document.createElement("option");
      option.value = form.id;
      option.textContent = form.form_name;
      this.elements.formSelector.appendChild(option);
    });
    this.elements.formPreview.innerHTML=''
    this.elements.locationsDisplay.innerHTML=''
    this.elements.showFormNameCheck.innerHTML='-'
  }

  async loadForm(formId) {
    if (!formId) return;

    try {
      const response = await this.apiCall("get_form_fields", {
        form_id: formId,
      });
      if (response.success) {
        this.displayForm(response.data);
      }
    } catch (error) {
      this.showNotification("خطا در بارگذاری فرم", "error");
    }
  }

  displayForm(formData) {
    this.elements.showFormNameCheck.textContent =
      this.getFormNameFromSelector();
    this.displayLocations(formData.form_locations || []);
    this.displayFormFields(formData.fields || []);
  }

  getFormNameFromSelector() {
    const selectedOption =
      this.elements.formSelector.options[
        this.elements.formSelector.selectedIndex
      ];
    return selectedOption ? selectedOption.textContent : "-";
  }

  displayLocations(locations) {
    if (!locations || locations.length === 0) {
      this.elements.locationsDisplay.innerHTML =
        '<span class="text-muted">موقعیتی تعریف نشده</span>';
      return;
    }

    this.elements.locationsDisplay.innerHTML = locations
      .map(
        (location) =>
          `<span class="badge bg-primary me-1 mb-1">${location}</span>`
      )
      .join("");
  }

  displayFormFields(fields) {
    if (!fields || fields.length === 0) {
      this.elements.formPreview.innerHTML =
        '<div class="col-12"><p class="text-muted">فیلدی برای این فرم تعریف نشده است</p></div>';
      return;
    }

    this.elements.formPreview.innerHTML = fields
      .map((field) => this.createPreviewField(field))
      .join("");

    // Add action buttons
    this.addFormActionButtons();
  }

  createPreviewField(field) {
    const fieldName =
      field.field_name === "equipment_id" ? "سریال تجهیز" : field.field_name;
    return `
            <div class="col-md-6">
                <div class="preview-field card border">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <label class="form-label fw-semibold">${fieldName}</label>
                            <span class="badge bg-secondary">${this.getFieldTypeLabel(
                              field.field_type
                            )}</span>
                        </div>
                        <div class="preview-value text-muted">${this.getFieldPreview(
                          field
                        )}</div>
                    </div>
                </div>
            </div>
        `;
  }

  getFieldPreview(field) {
    const previews = {
      text: "[متن]",
      number: "[عدد]",
      email: "[ایمیل]",
      tel: "[تلفن]",
      select: "[انتخاب از لیست]",
      multiselect: "[انتخاب چندگانه]",
      checkbox: "[انتخاب چندگانه]",
      radio: "[انتخاب تکی]",
      date: "[تاریخ]",
      time: "[زمان]",
      file: "[فایل]",
      image: "[تصویر]",
      textarea: "[متن طولانی]",
      geo: "[موقعیت جغرافیایی]",
    };
    return previews[field.field_type] || "[مقدار]";
  }

  addFormActionButtons() {
    const actionContainer = document.createElement("div");
    actionContainer.className = "col-12 mt-4";
    actionContainer.innerHTML = `
            <div class="d-flex gap-2 justify-content-center">
                <button type="button" class="btn btn-warning btn-lg" id="edit-form-btn">
                    <i class="bi bi-pencil-square me-1"></i>
                    ویرایش فرم
                </button>
                <button type="button" class="btn btn-danger btn-lg" id="remove-form-btn">
                    <i class="bi bi-trash me-1"></i>
                    حذف فرم
                </button>
            </div>
        `;

    this.elements.formPreview.appendChild(actionContainer);

    // Add event listeners
    document
      .getElementById("edit-form-btn")
      .addEventListener("click", () => this.editForm());
    document
      .getElementById("remove-form-btn")
      .addEventListener("click", () => this.removeForm());
  }

  // Form saving functionality - FIXED VERSION
  async saveForm() {
    const formData = this.collectFormData();
    if (!this.validateFormData(formData)) return;

    try {
      const response = await this.apiCall("save_form_data", {
        form_data: JSON.stringify(formData),
      });

      if (response.success) {
        this.showNotification("فرم با موفقیت ذخیره شد", "success");
        this.showSavedForms();
      } else {
        this.showNotification("خطا در ذخیره‌سازی فرم", "error");
      }
    } catch (error) {
      this.showNotification("خطا در ذخیره‌سازی فرم", "error");
    }
  }

 collectFormData() {
    const formName = this.elements.placeholderFormName.querySelector(
        ".placeholder-text"
    ).textContent;
    const locations = Array.from(
        document.querySelectorAll('input[name="locations[]"]:checked')
    ).map((checkbox) => checkbox.value);

    const fields = [];

    // ابتدا فیلد سریال تجهیز را اضافه کن (همیشه اولین فیلد)
    const equipmentField = {
        field_name: "equipment_id",
        field_type: "text",
        options: [],
        value: document.getElementById("equipment-id")?.value || "",
        required: true
    };
    fields.push(equipmentField);

    // سپس فیلدهای داینامیک را اضافه کن
    const fieldElements = this.elements.formBuilder.querySelectorAll(".field-card");
    
    fieldElements.forEach((fieldElement) => {
        // رد کردن فیلد equipment_id اصلی (چون قبلاً اضافه شده)
        if (fieldElement.dataset.fieldId === "equipment-field" || 
            fieldElement.querySelector("#equipment-id")) {
            return;
        }

        const fieldId = fieldElement.dataset.fieldId;
        const fieldName = fieldElement
            .querySelector(".form-label")
            .textContent.replace("*", "")
            .trim();
        const fieldType = this.detectFieldType(fieldElement);

        let options = [];
        let value = "";

        if (["select", "multiselect", "checkbox", "radio"].includes(fieldType)) {
            options = this.collectFieldOptions(fieldElement, fieldType);
            value = this.collectFieldValue(fieldElement, fieldType);
        } else {
            const input = fieldElement.querySelector("input, textarea");
            value = input ? input.value : "";
        }

        fields.push({
            field_name: fieldName,
            field_type: fieldType,
            options: options,
            value: value,
        });
    });

    return {
        form_name: formName,
        locations: locations,
        fields: fields,
    };
}

  detectFieldType(fieldElement) {
    const input = fieldElement.querySelector("input, select, textarea");
    if (!input) return "text";

    if (input.tagName.toLowerCase() === "select") {
      return input.multiple ? "multiselect" : "select";
    }

    if (input.type === "checkbox") {
      return "checkbox";
    }

    if (input.type === "radio") {
      return "radio";
    }

    return input.type || "text";
  }

  collectFieldOptions(fieldElement, fieldType) {
    if (fieldType === "select" || fieldType === "multiselect") {
      const options = fieldElement.querySelectorAll("option");
      return Array.from(options)
        .map((option) => option.value)
        .filter((val) => val && val !== "-- انتخاب کنید --");
    }

    if (fieldType === "checkbox" || fieldType === "radio") {
      const inputs = fieldElement.querySelectorAll(
        'input[type="checkbox"], input[type="radio"]'
      );
      return Array.from(inputs).map((input) => input.value);
    }

    return [];
  }

  collectFieldValue(fieldElement, fieldType) {
    if (fieldType === "select") {
      const select = fieldElement.querySelector("select");
      return select ? select.value : "";
    }

    if (fieldType === "multiselect" || fieldType === "checkbox") {
      const inputs = fieldElement.querySelectorAll("input:checked");
      return Array.from(inputs)
        .map((input) => input.value)
        .join(",");
    }

    if (fieldType === "radio") {
      const input = fieldElement.querySelector("input:checked");
      return input ? input.value : "";
    }

    const input = fieldElement.querySelector("input, textarea");
    return input ? input.value : "";
  }

validateFormData(formData) {
    if (!formData.form_name || formData.form_name === "نام فرم انتخاب نشده") {
        this.showNotification("لطفاً نام فرم را وارد کنید", "error");
        return false;
    }

    if (formData.locations.length === 0) {
        this.showNotification("لطفاً حداقل یک موقعیت مکانی انتخاب کنید", "error");
        return false;
    }

    // فقط فیلدهای داینامیک را بشمار (بدون equipment_id)
    const dynamicFields = formData.fields.filter(field => field.field_name !== "equipment_id");
    
    if (dynamicFields.length === 0) {
        this.showNotification("لطفاً حداقل یک فیلد به فرم اضافه کنید", "error");
        return false;
    }

    return true;
}

  // Edit form functionality - FIXED VERSION
  async editForm() {
    const formId = this.elements.formSelector.value;
    if (!formId) return;

    try {
      const response = await this.apiCall("get_form_and_fields", {
        form_id: formId,
      });
      if (response.success) {
        this.displayFormForEditing(
          formId,
          response.data.form,
          response.data.fields
        );
      }
    } catch (error) {
      this.showNotification("خطا در دریافت اطلاعات فرم", "error");
    }
  }

  displayFormForEditing(formId, formData, fields) {
    this.currentEditingFormId = formId;

    // Switch to form builder
    this.showFormBuilder();

    // Set form name
    this.elements.placeholderFormName.querySelector(
      ".placeholder-text"
    ).textContent = formData.form_name;

    // Set locations
    const formLocations = JSON.parse(formData.locations || "[]");
    this.setLocationsCheckboxes(formLocations);

    // Clear existing fields and rebuild
    this.resetFormBuilder();
    this.rebuildFormFields(fields);

    // Show update button
    this.showUpdateButton();
  }

  setLocationsCheckboxes(locations) {
    const checkboxes = document.querySelectorAll('input[name="locations[]"]');
    checkboxes.forEach((checkbox) => {
      checkbox.checked = locations.includes(checkbox.value);
    });
  }

rebuildFormFields(fields) {
    // ابتدا فیلد equipment_id را پیدا کن و نمایش بده
    const equipmentField = fields.find(field => field.field_name === "equipment_id");
    
    if (equipmentField) {
        // فیلد equipment_id را به صورت پیشفرض تنظیم کن
        const equipmentInput = document.getElementById("equipment-id");
        if (equipmentInput && equipmentField.value) {
            equipmentInput.value = equipmentField.value;
        }
        
        // field_id را برای equipment_id ذخیره کن
        const equipmentFieldElement = this.elements.formBuilder.querySelector('[data-field-id="equipment-field"]');
        if (equipmentFieldElement && equipmentField.id) {
            equipmentFieldElement.dataset.originalFieldId = equipmentField.id;
        }
    }

    // سپس فیلدهای دیگر را اضافه کن
    fields.forEach((field) => {
        // Skip equipment_id field (already exists)
        if (field.field_name === "equipment_id") return;

        const fieldData = {
            field_name: field.field_name,
            field_type: field.field_type,
            options: JSON.parse(field.options || "[]"),
            required: field.required || false,
            unique: field.unique || false,
        };

        const fieldElement = this.createFieldElement(fieldData);
        // Add field_id to the element for update purposes
        fieldElement.querySelector(".field-card").dataset.originalFieldId = field.id;
        this.elements.formBuilder.appendChild(fieldElement);
    });
}

  showUpdateButton() {
    // Hide save button, show update button
    this.elements.saveFormBtn.classList.add("d-none");

    const updateBtn = document.createElement("button");
    updateBtn.type = "button";
    updateBtn.id = "update-form-btn";
    updateBtn.className = "btn btn-warning btn-lg";
    updateBtn.innerHTML =
      '<i class="bi bi-check-circle me-1"></i> بروزرسانی فرم';
    updateBtn.addEventListener("click", () => this.updateForm());

    this.elements.saveFormBtn.parentNode.appendChild(updateBtn);
  }

  async updateForm() {
    const formData = this.collectUpdateFormData();
    if (!this.validateFormData(formData)) return;

    try {
      const response = await this.apiCall("update_form_data", {
        form_data: JSON.stringify(formData),
      });

      if (response.success) {
        this.showNotification("فرم با موفقیت بروزرسانی شد", "success");
        this.showSavedForms();
      } else {
        this.showNotification("خطا در بروزرسانی فرم", "error");
      }
    } catch (error) {
      this.showNotification("خطا در بروزرسانی فرم", "error");
    }
  }

 collectUpdateFormData() {
    const formData = this.collectFormData();
    formData.form_id = this.currentEditingFormId;

    // اضافه کردن field_id برای تمام فیلدها شامل equipment_id
    const fieldElements = this.elements.formBuilder.querySelectorAll(".field-card");
    
    formData.fields = formData.fields.map((field, index) => {
        // برای هر فیلد، field_id مربوطه را پیدا کن
        if (field.field_name === "equipment_id") {
            // پیدا کردن field_id برای equipment_id
            const equipmentFieldElement = this.elements.formBuilder.querySelector('[data-field-id="equipment-field"]');
            if (equipmentFieldElement && equipmentFieldElement.dataset.originalFieldId) {
                field.field_id = parseInt(equipmentFieldElement.dataset.originalFieldId);
            }
        } else {
            // برای فیلدهای دیگر
            const fieldElement = Array.from(fieldElements).find(el => {
                const label = el.querySelector('.form-label');
                return label && label.textContent.replace('*', '').trim() === field.field_name;
            });
            
            if (fieldElement && fieldElement.dataset.originalFieldId) {
                field.field_id = parseInt(fieldElement.dataset.originalFieldId);
            }
        }
        return field;
    });

    return formData;
}

// متد کمکی برای پیدا کردن field_id مربوط به equipment_id
getEquipmentFieldId() {
    const fieldElements = this.elements.formBuilder.querySelectorAll(".field-card");
    for (let element of fieldElements) {
        if (element.querySelector("#equipment-id")) {
            return element.dataset.originalFieldId ? parseInt(element.dataset.originalFieldId) : null;
        }
    }
    return null;
}

  // Remove form functionality
  async removeForm() {
    const formId = this.elements.formSelector.value;
    if (!formId) return;

    if (!confirm("آیا از حذف این فرم مطمئن هستید؟ این عمل قابل بازگشت نیست.")) {
      return;
    }

    try {
      const response = await this.apiCall("remove_form", { form_id: formId });
      if (response.success) {
        this.showNotification(response.data.message, "success");
        this.loadSavedForms();
      } else {
        this.showNotification(response.data.message, "error");
      }
    } catch (error) {
      this.showNotification(response.data.message, "error");
    }
  }

  // API communication
  async apiCall(action, data = {}) {
    const formData = new FormData();
    formData.append("action", action);

    Object.entries(data).forEach(([key, value]) => {
      formData.append(key, value);
    });

    const response = await fetch("/wp-admin/admin-ajax.php", {
      method: "POST",
      body: formData,
    });

    return await response.json();
  }

  // UI helpers
  showNotification(message, type = "info") {
    // Simple alert implementation
    const alertClass =
      {
        success: "alert-success",
        error: "alert-danger",
        warning: "alert-warning",
        info: "alert-info",
      }[type] || "alert-info";

    const alertId = "alert-" + Date.now();
    const alertHtml = `
            <div id="${alertId}" class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;

    const container =
      document.querySelector(".container-fluid") || document.body;
    container.insertAdjacentHTML("afterbegin", alertHtml);

    setTimeout(() => {
      const alert = document.getElementById(alertId);
      if (alert) {
        alert.remove();
      }
    }, 5000);
  }

 resetFormBuilder() {
    // همیشه فیلد سریال تجهیز را اضافه کن
    this.elements.formBuilder.innerHTML = `
        <div class="col-md-6">
            <div class="field-card card border" data-field-id="equipment-field">
                <div class="card-body">
                    <label for="equipment-id" class="form-label fw-semibold">
                        <i class="bi bi-upc-scan me-2"></i>
                        سریال تجهیز
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="equipment-id" class="form-control" 
                           id="equipment-id" required 
                           placeholder="سریال تجهیز را وارد کنید">
                    <div class="form-text">این فیلد به صورت پیشفرض در تمام فرم‌ها وجود دارد</div>
                </div>
            </div>
        </div>
    `;
    
    this.fieldCounter = 0;
    this.equipmentFieldAdded = true;

    // Remove update button if exists
    const updateBtn = document.getElementById("update-form-btn");
    if (updateBtn) {
        updateBtn.remove();
    }

    // Show save button
    this.elements.saveFormBtn.classList.remove("d-none");
}

  resetModal(modalSelector) {
    const form = document.querySelector(`${modalSelector} form`);
    if (form) {
      form.reset();
      form.classList.remove("was-validated");
    }

    // Reset options container
    this.elements.optionsContainer.classList.add("d-none");
    this.elements.inputOptionContainer.innerHTML = "";
    this.elements.newFeatureName.disabled = false;
  }

  getFieldTypeLabel(type) {
    const labels = {
      text: "متن",
      number: "عدد",
      email: "ایمیل",
      tel: "تلفن",
      select: "لیست",
      multiselect: "چند انتخابی",
      checkbox: "چندگزینه",
      radio: "تک انتخابی",
      date: "تاریخ",
      time: "زمان",
      file: "فایل",
      image: "تصویر",
      textarea: "متن طولانی",
      geo: "موقعیت",
    };
    return labels[type] || type;
  }
}

// Initialize when DOM is ready
document.addEventListener("DOMContentLoaded", () => {
  new FormManager();
});
