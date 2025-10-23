
class FormManager {
  constructor() {
    this.currentEditingFormId = null;
    this.fieldCounter = 0;
    this.modals = {};
    this.elements = {};
    this.init();
  }

  init() {
    this.cacheElements();
    this.initModals();
    this.bindEvents();
    this.loadInitialData();
  }

  cacheElements() {
    const elementIds = {
      mainContainer: "main-container-form",
      savedFormsContainer: "show-saved-forms",
      formBuilder: "form-container-builder",
      formPreview: "display-form",
      addNewFormBtn: "add-new-form",
      showFormsBtn: "show-forms",
      saveFormBtn: "save-form-btn",
      saveNewFieldBtn: "save-new-field",
      saveFormNameBtn: "save-modal-form-name",
      addNewOptionBtn: "add-new-option",
      formSelector: "form-selector",
      inputType: "input-type",
      newFeatureName: "new-feature-name",
      newFormName: "new-form-name",
      placeholderFormName: "placeholder-form-name",
      showFormNameCheck: "show-form-name-check",
      locationsDisplay: "locations-display",
      optionsContainer: "container-options-modal",
      inputOptionContainer: "input-option-container",
      selectedInput: "selected-input",
    };

    this.elements = {};
    Object.keys(elementIds).forEach((key) => {
      this.elements[key] = document.getElementById(elementIds[key]);
    });
  }

  initModals() {
    this.modals.addForm = new bootstrap.Modal("#modal-add-form");
    this.modals.addFormName = new bootstrap.Modal("#modal-add-form-name");
  }

 bindEvents() {
    const {
        addNewFormBtn,
        showFormsBtn,
        saveFormBtn,
        saveFormNameBtn,
        saveNewFieldBtn,
        inputType,
        addNewOptionBtn,
        formSelector,
    } = this.elements;

    addNewFormBtn.addEventListener("click", () => this.showFormBuilder());
    showFormsBtn.addEventListener("click", () => this.showSavedForms());
    saveFormBtn.addEventListener("click", () => this.saveForm());
    saveFormNameBtn.addEventListener("click", () => this.createNewForm());
    saveNewFieldBtn.addEventListener("click", (e) => this.addNewField(e));
    inputType.addEventListener("change", () => this.handleFieldTypeChange());
    addNewOptionBtn.addEventListener("click", () => this.addOptionField());
    formSelector.addEventListener("change", (e) =>
        this.loadForm(e.target.value)
    );

    // اضافه کردن event listener برای مودال
    this.setupModalEvents();
}

setupModalEvents() {
    const modalElement = document.getElementById('modal-new-field-form');
    if (modalElement) {
        modalElement.addEventListener('hidden.bs.modal', () => {
            this.resetEditingState();
        });
        
        modalElement.addEventListener('show.bs.modal', () => {
            // اطمینان از اینکه حالت ویرایش ریست شده است
            if (!this.currentEditingField) {
                this.restoreSaveButton();
            }
        });
    }
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

    this.setFormName(formName);
    this.showFormBuilder();
    this.modals.addFormName.hide();
    this.resetModal("#modal-form-name");
  }

 async addNewField(event) {
    event.preventDefault();
    
    // اگر در حال ویرایش هستیم، از ایجاد فیلد جدید جلوگیری می‌کنیم
    if (this.currentEditingField) {
        return;
    }
    
    const fieldData = this.getFieldData();

    if (!this.validateFieldData(fieldData)) return;

    const fieldElement = this.createFieldElement(fieldData);
    this.elements.formBuilder.appendChild(fieldElement);

    this.modals.addForm.hide();
    this.resetModal("#modal-new-field-form");
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
      qr_code: () => this.createQrCodeTemplate(fieldData, fieldId),
    };

    return templates[fieldData.field_type]
      ? templates[fieldData.field_type]()
      : this.createInputTemplate(fieldData, fieldId, "text");
  }

  createInputTemplate(fieldData, fieldId, type) {
    return `
      <div class="field-card card border h-100" data-field-id="${fieldId}">
        <div class="card-body">
          ${this.createFieldHeader(fieldData)}
          <input type="${type}" class="form-control" 
                 ${fieldData.required ? "required" : ""}
                 placeholder="${fieldData.placeholder || ""}">
          ${this.createFieldHelpText(fieldData)}
          ${this.createFieldActions()}
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
          ${this.createFieldHeader(fieldData)}
          <select class="form-select" ${fieldData.required ? "required" : ""}>
            <option value="">-- انتخاب کنید --</option>
            ${options}
          </select>
          ${this.createFieldActions()}
        </div>
      </div>
    `;
  }

  createCheckboxTemplate(fieldData, fieldId) {
    const options = fieldData.options
      .map(
        (option, index) => `
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="${fieldId}" 
                 value="${option}" id="${fieldId}-${index}">
          <label class="form-check-label" for="${fieldId}-${index}">${option}</label>
        </div>
      `
      )
      .join("");

    return `
      <div class="field-card card border h-100" data-field-id="${fieldId}">
        <div class="card-body">
          ${this.createFieldHeader(fieldData)}
          <div class="checkbox-container">${options}</div>
          ${this.createFieldActions()}
        </div>
      </div>
    `;
  }

  createRadioTemplate(fieldData, fieldId) {
    const options = fieldData.options
      .map(
        (option, index) => `
        <div class="form-check">
          <input class="form-check-input" type="radio" name="${fieldId}" 
                 value="${option}" id="${fieldId}-${index}">
          <label class="form-check-label" for="${fieldId}-${index}">${option}</label>
        </div>
      `
      )
      .join("");

    return `
      <div class="field-card card border h-100" data-field-id="${fieldId}">
        <div class="card-body">
          ${this.createFieldHeader(fieldData)}
          <div class="radio-container">${options}</div>
          ${this.createFieldActions()}
        </div>
      </div>
    `;
  }

  createTextareaTemplate(fieldData, fieldId) {
    return `
      <div class="field-card card border h-100" data-field-id="${fieldId}">
        <div class="card-body">
          ${this.createFieldHeader(fieldData)}
          <textarea class="form-control" rows="3" 
                    ${fieldData.required ? "required" : ""}
                    placeholder="${fieldData.placeholder || ""}"></textarea>
          ${this.createFieldActions()}
        </div>
      </div>
    `;
  }

  createQrCodeTemplate(fieldData, fieldId) {
    return `
      <div class="field-card card border h-100" data-field-id="${fieldId}">
        <div class="card-body">
          ${this.createFieldHeader(fieldData)}
          <div class="qr-code-field">
            <div class="input-group mb-2">
              <input type="text" class="form-control qr-input" 
                     placeholder="${fieldData.placeholder || "QR کد اسکن شود"}"
                     ${fieldData.required ? "required" : ""} readonly>
              <button type="button" class="btn btn-outline-primary scan-qr-btn">
                <i class="bi bi-qr-code-scan"></i> اسکن QR
              </button>
            </div>
            <div class="qr-reader-container d-none mt-2">
              <div id="qr-reader-${fieldId}" class="qr-reader"></div>
              <button type="button" class="btn btn-sm btn-outline-secondary mt-2 cancel-scan-btn">
                لغو اسکن
              </button>
            </div>
          </div>
          ${this.createFieldActions()}
        </div>
      </div>
    `;
  }

  createGeoTemplate(fieldData, fieldId) {
    return `
      <div class="field-card card border h-100" data-field-id="${fieldId}">
        <div class="card-body">
          ${this.createFieldHeader(fieldData)}
          <button type="button" class="btn btn-outline-warning w-100 geo-btn">
            <i class="bi bi-geo-alt"></i> دریافت موقعیت جغرافیایی
          </button>
          <div class="geo-coordinates mt-2 d-none">
            <small class="text-muted">عرض جغرافیایی: <span class="latitude">-</span></small><br>
            <small class="text-muted">طول جغرافیایی: <span class="longitude">-</span></small>
          </div>
          ${this.createFieldActions()}
        </div>
      </div>
    `;
  }

  createFieldHeader(fieldData) {
    return `
      <div class="d-flex justify-content-between align-items-start mb-2">
        <label class="form-label fw-semibold">
          ${fieldData.field_name}
          ${fieldData.required ? '<span class="text-danger">*</span>' : ""}
        </label>
        <span class="badge bg-secondary">${this.getFieldTypeLabel(
          fieldData.field_type
        )}</span>
      </div>
    `;
  }

  createFieldHelpText(fieldData) {
    return fieldData.helpText
      ? `<div class="form-text">${fieldData.helpText}</div>`
      : "";
  }

  createFieldActions() {
    return `
        <div class="field-actions mt-2 d-flex gap-1">
            <button type="button" class="btn btn-sm btn-outline-warning edit-field">
                <i class="bi bi-pencil"></i>
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger remove-field">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    `;
  }

  addFieldActions(fieldWrapper, fieldId) {
    const removeBtn = fieldWrapper.querySelector(".remove-field");
    const editBtn = fieldWrapper.querySelector(".edit-field");

    if (removeBtn) {
      removeBtn.addEventListener("click", () => this.removeField(fieldWrapper));
    }

    if (editBtn) {
      editBtn.addEventListener("click", () => this.editField(fieldWrapper));
    }

    // Add geo location functionality
    const geoBtn = fieldWrapper.querySelector(".geo-btn");
    if (geoBtn) {
      geoBtn.addEventListener("click", () => this.getGeolocation(fieldWrapper));
    }

    // Add QR code scanning functionality
    const scanQrBtn = fieldWrapper.querySelector(".scan-qr-btn");
    if (scanQrBtn) {
      scanQrBtn.addEventListener("click", () =>
        this.handleQrCodeScan(fieldWrapper, fieldId)
      );
    }

    const cancelScanBtn = fieldWrapper.querySelector(".cancel-scan-btn");
    if (cancelScanBtn) {
      cancelScanBtn.addEventListener("click", () =>
        this.cancelQrCodeScan(fieldWrapper)
      );
    }
  }

  // Field data management
  getFieldData() {
    const fieldName = this.elements.newFeatureName.value.trim();
    const fieldType = this.elements.inputType.value;
    const isRequired =
      document.getElementById("field-required")?.checked || false;
    const isUnique = document.getElementById("field-unique")?.checked || false;

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
      qr_code: "QR کد اسکن شود",
    };
    return placeholders[fieldType] || "مقدار را وارد کنید";
  }

  // Form management
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
    const formName = this.getFormName();
    const locations = this.getSelectedLocations();
    const fields = this.collectFormFields();

    return {
      form_name: formName,
      locations: locations,
      fields: fields,
    };
  }

  getFormName() {
    return this.elements.placeholderFormName.querySelector(".placeholder-text")
      .textContent;
  }

  getSelectedLocations() {
    return Array.from(
      document.querySelectorAll('input[name="locations[]"]:checked')
    ).map((checkbox) => checkbox.value);
  }

  collectFormFields() {
    const fields = [];

    // Add equipment_id field (always first and required)
    fields.push({
      field_name: "equipment_id",
      field_type: "text",
      options: [],
      value: document.getElementById("equipment-id")?.value || "",
      required: true,
    });

    // Add dynamic fields
    const fieldElements =
      this.elements.formBuilder.querySelectorAll(".field-card");

    fieldElements.forEach((fieldElement) => {
      // Skip equipment_id field (already added)
      if (
        fieldElement.dataset.fieldId === "equipment-field" ||
        fieldElement.querySelector("#equipment-id")
      ) {
        return;
      }

      const fieldData = this.collectFieldData(fieldElement);
      if (fieldData) {
        fields.push(fieldData);
      }
    });

    return fields;
  }

  collectFieldData(fieldElement) {
    const fieldId = fieldElement.dataset.fieldId;
    const labelElement = fieldElement.querySelector(".form-label");
    if (!labelElement) return null;

    const fieldName = labelElement.textContent.replace("*", "").trim();
    const fieldType = this.detectFieldType(fieldElement);
    const isRequired = labelElement.innerHTML.includes('text-danger">*</span>');

    let options = [];
    let value = "";

    if (["select", "multiselect", "checkbox", "radio"].includes(fieldType)) {
      options = this.collectFieldOptions(fieldElement, fieldType);
      value = this.collectFieldValue(fieldElement, fieldType);
    } else {
      const input = fieldElement.querySelector("input, textarea");
      value = input ? input.value : "";
    }

    return {
      field_name: fieldName,
      field_type: fieldType,
      options: options,
      value: value,
      required: isRequired,
      unique_field: false,
    };
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

    if (fieldType === "qr_code") {
      const input = fieldElement.querySelector(".qr-input");
      return input ? input.value : "";
    }

    const input = fieldElement.querySelector("input, textarea");
    return input ? input.value : "";
  }

  // Edit form functionality
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

    this.showFormBuilder();
    this.setFormName(formData.form_name);
    this.setLocationsCheckboxes(JSON.parse(formData.locations || "[]"));

    this.resetFormBuilder();
    this.rebuildFormFields(fields);
    this.showUpdateButton();
  }

  rebuildFormFields(fields) {
    // Add equipment_id field
    const equipmentField = fields.find(
      (field) => field.field_name === "equipment_id"
    );
    if (equipmentField) {
      this.setEquipmentField(equipmentField);
    }

    // Add other fields
    fields.forEach((field) => {
      if (field.field_name === "equipment_id") return;

      const fieldData = {
        field_name: field.field_name,
        field_type: field.field_type,
        options: JSON.parse(field.options || "[]"),
        required: field.required === 1 || field.required === true,
        unique: field.unique_field || false,
      };

      const fieldElement = this.createFieldElement(fieldData);
      fieldElement.querySelector(".field-card").dataset.originalFieldId =
        field.id;

      this.setFieldValue(fieldElement, field);
      this.elements.formBuilder.appendChild(fieldElement);
    });
  }

  setEquipmentField(equipmentField) {
    const equipmentInput = document.getElementById("equipment-id");
    if (equipmentInput && equipmentField.value) {
      equipmentInput.value = equipmentField.value;
    }

    const equipmentFieldElement = this.elements.formBuilder.querySelector(
      '[data-field-id="equipment-field"]'
    );
    if (equipmentFieldElement && equipmentField.id) {
      equipmentFieldElement.dataset.originalFieldId = equipmentField.id;
      if (equipmentField.required) {
        equipmentInput.required = true;
      }
    }
  }

  setFieldValue(fieldElement, field) {
    const input = fieldElement.querySelector("input, select, textarea");
    if (!input || !field.value) return;

    if (input.type === "checkbox" || input.type === "radio") {
      const values = field.value.split(",");
      const inputs = fieldElement.querySelectorAll(`[name="${input.name}"]`);
      inputs.forEach((input) => {
        input.checked = values.includes(input.value);
      });
    } else if (input.tagName === "SELECT") {
      input.value = field.value;
    } else {
      input.value = field.value;
    }
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

    const fieldElements =
      this.elements.formBuilder.querySelectorAll(".field-card");

    formData.fields = formData.fields.map((field) => {
      if (field.field_name === "equipment_id") {
        const equipmentFieldElement = this.elements.formBuilder.querySelector(
          '[data-field-id="equipment-field"]'
        );
        if (
          equipmentFieldElement &&
          equipmentFieldElement.dataset.originalFieldId
        ) {
          field.field_id = parseInt(
            equipmentFieldElement.dataset.originalFieldId
          );
        }
      } else {
        const fieldElement = Array.from(fieldElements).find((el) => {
          const label = el.querySelector(".form-label");
          return (
            label &&
            label.textContent.replace("*", "").trim() === field.field_name
          );
        });

        if (fieldElement && fieldElement.dataset.originalFieldId) {
          field.field_id = parseInt(fieldElement.dataset.originalFieldId);
        }
      }
      return field;
    });

    return formData;
  }

  // Form preview and display
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

  addFormActionButtons() {
    const actionContainer = document.createElement("div");
    actionContainer.className = "col-12 mt-4";
    actionContainer.innerHTML = `
      <div class="d-flex gap-2 justify-content-center">
        <button type="button" class="btn btn-warning btn-lg" id="edit-form-btn">
          <i class="bi bi-pencil-square me-1"></i> ویرایش فرم
        </button>
        <button type="button" class="btn btn-danger btn-lg" id="remove-form-btn">
          <i class="bi bi-trash me-1"></i> حذف فرم
        </button>
      </div>
    `;

    this.elements.formPreview.appendChild(actionContainer);

    document
      .getElementById("edit-form-btn")
      .addEventListener("click", () => this.editForm());
    document
      .getElementById("remove-form-btn")
      .addEventListener("click", () => this.removeForm());
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

  validateFormData(formData) {
    if (!formData.form_name || formData.form_name === "نام فرم انتخاب نشده") {
      this.showNotification("لطفاً نام فرم را وارد کنید", "error");
      return false;
    }

    if (formData.locations.length === 0) {
      this.showNotification("لطفاً حداقل یک موقعیت مکانی انتخاب کنید", "error");
      return false;
    }

    const dynamicFields = formData.fields.filter(
      (field) => field.field_name !== "equipment_id"
    );
    if (dynamicFields.length === 0) {
      this.showNotification("لطفاً حداقل یک فیلد به فرم اضافه کنید", "error");
      return false;
    }

    return true;
  }

  // Field type handling
  handleFieldTypeChange() {
    const fieldType = this.elements.inputType.value;
    const optionsContainer = this.elements.optionsContainer;

    if (["select", "checkbox", "radio", "multiselect"].includes(fieldType)) {
      optionsContainer.classList.remove("d-none");
      this.elements.selectedInput.textContent =
        this.getFieldTypeLabel(fieldType);
      this.initializeOptionsContainer();
    } else {
      optionsContainer.classList.add("d-none");
    }

    if (fieldType === "geo") {
      this.elements.newFeatureName.value = "موقعیت جغرافیایی";
      this.elements.newFeatureName.disabled = true;
    } else if (fieldType === "qr_code") {
      this.elements.newFeatureName.value = "QR کد";
      this.elements.newFeatureName.disabled = true;
    } else {
      this.elements.newFeatureName.disabled = false;
    }
  }

  initializeOptionsContainer() {
    this.elements.inputOptionContainer.innerHTML = "";
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

    const removeBtn = this.elements.inputOptionContainer.querySelector(
      `[data-option-id="${optionId}"] .remove-option`
    );
    removeBtn.addEventListener("click", (e) => {
      e.target.closest(".input-group").remove();
    });
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
      qr_code: "QR کد",
    };
    return labels[type] || type;
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
      qr_code: "[QR کد]",
    };
    return previews[field.field_type] || "[مقدار]";
  }

  detectFieldType(fieldElement) {
    const input = fieldElement.querySelector("input, select, textarea");
    if (!input) return "text";

    if (input.tagName.toLowerCase() === "select") {
      return input.multiple ? "multiselect" : "select";
    }

    if (input.type === "checkbox") return "checkbox";
    if (input.type === "radio") return "radio";
    if (fieldElement.querySelector(".qr-code-field")) return "qr_code";

    return input.type || "text";
  }

  // Form operations
  setFormName(formName) {
    this.elements.placeholderFormName.querySelector(
      ".placeholder-text"
    ).textContent = formName;
  }

  setLocationsCheckboxes(locations) {
    const checkboxes = document.querySelectorAll('input[name="locations[]"]');
    checkboxes.forEach((checkbox) => {
      checkbox.checked = locations.includes(checkbox.value);
    });
  }

  getFormNameFromSelector() {
    const selectedOption =
      this.elements.formSelector.options[
        this.elements.formSelector.selectedIndex
      ];
    return selectedOption ? selectedOption.textContent : "-";
  }

  showUpdateButton() {
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

  // Field operations
  async removeField(fieldWrapper) {
    if (!confirm("آیا از حذف این فیلد مطمئن هستید؟")) return;

    const removeBtn = fieldWrapper.querySelector(".remove-field");
    const originalHtml = removeBtn.innerHTML;
    removeBtn.innerHTML = '<i class="bi bi-hourglass-split"></i>';
    removeBtn.disabled = true;

    try {
      const fieldCard = fieldWrapper.querySelector(".field-card");
      const originalFieldId = fieldCard?.dataset?.originalFieldId;

      if (this.currentEditingFormId && originalFieldId) {
        const success = await this.removeFieldFromServer(originalFieldId);
        if (success) {
          fieldWrapper.remove();
          this.showNotification("فیلد با موفقیت حذف شد", "success");
        }
      } else {
        fieldWrapper.remove();
        this.showNotification("فیلد حذف شد", "success");
      }
    } catch (error) {
      this.showNotification("خطا در حذف فیلد", "error");
    } finally {
      removeBtn.innerHTML = originalHtml;
      removeBtn.disabled = false;
    }
  }

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

  // Form removal
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
      this.showNotification("خطا در حذف فرم", "error");
    }
  }

  // Form loading
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

    this.elements.formPreview.innerHTML = "";
    this.elements.locationsDisplay.innerHTML = "";
    this.elements.showFormNameCheck.innerHTML = "-";
  }

  // Reset methods
  resetFormBuilder() {
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

    const updateBtn = document.getElementById("update-form-btn");
    if (updateBtn) updateBtn.remove();

    this.elements.saveFormBtn.classList.remove("d-none");
  }

resetEditingState() {
    this.currentEditingField = null;
    this.restoreSaveButton();
}

// اصلاح متد resetModal
resetModal(modalSelector) {
    const form = document.querySelector(`${modalSelector} form`);
    if (form) {
        form.reset();
        form.classList.remove("was-validated");
    }

    this.elements.optionsContainer.classList.add("d-none");
    this.elements.inputOptionContainer.innerHTML = "";
    this.elements.newFeatureName.disabled = false;
    
    // ریست کردن حالت ویرایش
    this.resetEditingState();
}

  // QR Code functionality
  handleQrCodeScan(fieldWrapper, fieldId) {
    const qrReaderContainer = fieldWrapper.querySelector(
      ".qr-reader-container"
    );
    const scanBtn = fieldWrapper.querySelector(".scan-qr-btn");
    const qrInput = fieldWrapper.querySelector(".qr-input");

    qrReaderContainer.classList.remove("d-none");
    scanBtn.disabled = true;

    const qrReaderElement = document.getElementById(`qr-reader-${fieldId}`);

    if (!window.Html5Qrcode) {
      this.showNotification("کتابخانه اسکن QR بارگذاری نشده است", "error");
      return;
    }

    const html5QrCode = new Html5Qrcode(`qr-reader-${fieldId}`);

    html5QrCode
      .start(
        { facingMode: "environment" },
        { fps: 10, qrbox: 250 },
        (qrCodeMessage) => {
          qrInput.value = qrCodeMessage;
          this.cancelQrCodeScan(fieldWrapper);
          this.showNotification("QR کد با موفقیت اسکن شد", "success");
        },
        (errorMessage) => {
          console.log(`QR Code scan error: ${errorMessage}`);
        }
      )
      .catch((err) => {
        this.showNotification("خطا در راه‌اندازی اسکنر QR", "error");
        this.cancelQrCodeScan(fieldWrapper);
      });

    fieldWrapper._qrScanner = html5QrCode;
  }

  cancelQrCodeScan(fieldWrapper) {
    const qrReaderContainer = fieldWrapper.querySelector(
      ".qr-reader-container"
    );
    const scanBtn = fieldWrapper.querySelector(".scan-qr-btn");

    qrReaderContainer.classList.add("d-none");
    scanBtn.disabled = false;

    if (fieldWrapper._qrScanner) {
      fieldWrapper._qrScanner.stop().catch(() => {});
      fieldWrapper._qrScanner = null;
    }
  }

  // Geolocation functionality
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
      if (alert) alert.remove();
    }, 5000);
  }

editField(fieldWrapper) {
    const fieldCard = fieldWrapper.querySelector('.field-card');
    const fieldId = fieldCard.dataset.fieldId;
    const originalFieldId = fieldCard.dataset.originalFieldId;
    
    // جمع‌آوری اطلاعات فیلد فعلی
    const label = fieldWrapper.querySelector('.form-label');
    const fieldName = label.textContent.replace('*', '').trim();
    
    const badge = fieldWrapper.querySelector('.badge');
    const fieldType = this.getFieldTypeFromBadge(badge.textContent);
    
    const isRequired = label.innerHTML.includes('text-danger">*</span>');
    
    // پر کردن فرم ویرایش
    this.elements.newFeatureName.value = fieldName;
    this.elements.inputType.value = fieldType;
    document.getElementById('field-required').checked = isRequired;
    
    // مدیریت گزینه‌ها برای فیلدهای انتخابی
    this.handleFieldTypeChange();
    
    if (['select', 'checkbox', 'radio', 'multiselect'].includes(fieldType)) {
        const options = this.collectCurrentFieldOptions(fieldWrapper, fieldType);
        this.populateOptionsInModal(options);
    }
    
    // ذخیره اطلاعات فیلد برای به‌روزرسانی
    this.currentEditingField = {
        wrapper: fieldWrapper,
        fieldId: fieldId,
        originalFieldId: originalFieldId
    };
    
    // تغییر دکمه به حالت ویرایش
    this.setupUpdateButton();
    
    // نمایش مودال
    this.modals.addForm.show();
}

  getFieldTypeFromBadge(badgeText) {
    const typeMap = {
      متن: "text",
      عدد: "number",
      ایمیل: "email",
      تلفن: "tel",
      لیست: "select",
      "چند انتخابی": "multiselect",
      چندگزینه: "checkbox",
      "تک انتخابی": "radio",
      تاریخ: "date",
      زمان: "time",
      فایل: "file",
      تصویر: "image",
      "متن طولانی": "textarea",
      موقعیت: "geo",
      "QR کد": "qr_code",
    };
    return typeMap[badgeText] || "text";
  }

  collectCurrentFieldOptions(fieldWrapper, fieldType) {
    const options = [];

    if (fieldType === "select" || fieldType === "multiselect") {
      const selectOptions = fieldWrapper.querySelectorAll("option");
      selectOptions.forEach((option) => {
        if (option.value && option.value !== "-- انتخاب کنید --") {
          options.push(option.value);
        }
      });
    } else if (fieldType === "checkbox" || fieldType === "radio") {
      const inputs = fieldWrapper.querySelectorAll(
        'input[type="checkbox"], input[type="radio"]'
      );
      inputs.forEach((input) => {
        options.push(input.value);
      });
    }

    return options;
  }

  populateOptionsInModal(options) {
    this.elements.inputOptionContainer.innerHTML = "";

    options.forEach((option) => {
      this.addOptionFieldWithValue(option);
    });
  }

  addOptionFieldWithValue(value) {
    const optionId = `option-${Date.now()}`;
    const optionHtml = `
        <div class="input-group mb-2" data-option-id="${optionId}">
            <input type="text" class="form-control input-option" 
                   placeholder="مقدار گزینه را وارد کنید" value="${value}">
            <button type="button" class="btn btn-outline-danger remove-option" data-option-id="${optionId}">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    `;

    this.elements.inputOptionContainer.insertAdjacentHTML(
      "beforeend",
      optionHtml
    );

    const removeBtn = this.elements.inputOptionContainer.querySelector(
      `[data-option-id="${optionId}"] .remove-option`
    );
    removeBtn.addEventListener("click", (e) => {
      e.target.closest(".input-group").remove();
    });
  }

setupUpdateButton() {
    const saveBtn = this.elements.saveNewFieldBtn;
    
    // ذخیره وضعیت اصلی
    this.originalSaveBtnState = {
        innerHTML: saveBtn.innerHTML,
        onclick: saveBtn.onclick
    };
    
    // تغییر به حالت ویرایش
    saveBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i> بروزرسانی فیلد';
    saveBtn.onclick = (e) => this.updateField(e);
    
    // اضافه کردن event listener برای بازگشت به حالت عادی هنگام بسته شدن مودال
    const modalElement = this.modals.addForm._element;
    const handleModalClose = () => {
        this.restoreSaveButton();
        modalElement.removeEventListener('hidden.bs.modal', handleModalClose);
    };
    
    modalElement.addEventListener('hidden.bs.modal', handleModalClose);
}

restoreSaveButton() {
    const saveBtn = this.elements.saveNewFieldBtn;
    if (this.originalSaveBtnState) {
        saveBtn.innerHTML = this.originalSaveBtnState.innerHTML;
        saveBtn.onclick = this.originalSaveBtnState.onclick;
    }
    this.currentEditingField = null;
}

  async updateField(event) {
    event.preventDefault();
    const fieldData = this.getFieldData();

    if (!this.validateFieldData(fieldData)) return;

    try {
      // اگر در حال ویرایش فیلد موجود هستیم
      if (this.currentEditingField && this.currentEditingField.wrapper) {
        if (this.currentEditingField.originalFieldId) {
          // به‌روزرسانی در دیتابیس
          const success = await this.updateFieldInServer(
            this.currentEditingField.originalFieldId,
            fieldData
          );

          if (!success) {
            this.showNotification("خطا در به‌روزرسانی فیلد در سرور", "error");
            return;
          }
        }

        // به‌روزرسانی در رابط کاربری
        this.updateFieldInUI(this.currentEditingField.wrapper, fieldData);

        this.modals.addForm.hide();
        this.resetModal("#modal-new-field-form");
        this.showNotification("فیلد با موفقیت به‌روزرسانی شد", "success");

        // ریست کردن وضعیت ویرایش
        this.currentEditingField = null;
      }
    } catch (error) {
      console.error("Error updating field:", error);
      this.showNotification("خطا در به‌روزرسانی فیلد", "error");
    }
  }

  async updateFieldInServer(fieldId, fieldData) {
    try {
      const response = await this.apiCall("update_form_field", {
        field_id: fieldId,
        field_data: JSON.stringify(fieldData),
      });

      return response.success;
    } catch (error) {
      console.error("Error updating field:", error);
      return false;
    }
  }

  updateFieldInUI(fieldWrapper, fieldData) {
    const fieldCard = fieldWrapper.querySelector(".field-card");

    // به‌روزرسانی هدر فیلد
    const label = fieldWrapper.querySelector(".form-label");
    label.innerHTML = `
        ${fieldData.field_name}
        ${fieldData.required ? '<span class="text-danger">*</span>' : ""}
    `;

    // به‌روزرسانی نوع فیلد
    const badge = fieldWrapper.querySelector(".badge");
    badge.textContent = this.getFieldTypeLabel(fieldData.field_type);

    // به‌روزرسانی محتوای فیلد
    this.updateFieldContent(fieldWrapper, fieldData);
  }

  updateFieldContent(fieldWrapper, fieldData) {
    const fieldBody = fieldWrapper.querySelector(".card-body");
    const oldInput = fieldBody.querySelector(
      "input, select, textarea, .checkbox-container, .radio-container"
    );

    if (oldInput) {
      oldInput.remove();
    }

    // ایجاد محتوای جدید
    const newContent = this.createFieldContent(fieldData);
    const helpText = fieldWrapper.querySelector(".form-text");

    if (helpText) {
      helpText.insertAdjacentHTML("beforebegin", newContent);
    } else {
      const fieldActions = fieldWrapper.querySelector(".field-actions");
      fieldActions.insertAdjacentHTML("beforebegin", newContent);
    }

    // اضافه کردن اکشن‌های مربوطه
    this.addFieldActions(fieldWrapper, fieldCard.dataset.fieldId);
  }

  createFieldContent(fieldData) {
    const templates = {
      text: () => `<input type="text" class="form-control" 
                          ${fieldData.required ? "required" : ""}
                          placeholder="${fieldData.placeholder || ""}">`,

      number: () => `<input type="number" class="form-control" 
                             ${fieldData.required ? "required" : ""}
                             placeholder="${fieldData.placeholder || ""}">`,

      select: () => {
        const options = fieldData.options
          .map((option) => `<option value="${option}">${option}</option>`)
          .join("");
        return `<select class="form-select" ${
          fieldData.required ? "required" : ""
        }>
                        <option value="">-- انتخاب کنید --</option>
                        ${options}
                    </select>`;
      },

      checkbox: () => {
        const options = fieldData.options
          .map(
            (option, index) => `
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" 
                               value="${option}">
                        <label class="form-check-label">${option}</label>
                    </div>
                `
          )
          .join("");
        return `<div class="checkbox-container">${options}</div>`;
      },

      // سایر انواع فیلدها...
    };

    return templates[fieldData.field_type]
      ? templates[fieldData.field_type]()
      : "";
  }
}

// Initialize when DOM is ready
document.addEventListener("DOMContentLoaded", () => {
  new FormManager();
});