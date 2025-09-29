class FormManager {
  constructor() {
    this.modals = {};
    this.initEventListeners();
    this.cacheModals();
  }

  cacheModals() {
    this.modals.addForm = new bootstrap.Modal(
      document.getElementById("modal-add-form")
    );
    this.modals.addFormName = new bootstrap.Modal(
      document.getElementById("modal-add-form-name")
    );
  }

  initEventListeners() {
    // Form name modal
    document
      .getElementById("save-modal-form-name")
      .addEventListener("click", () => this.handleAddNewForm());

    // Field modal
    document
      .getElementById("input-type")
      .addEventListener("change", () => this.handleFeatureTypeChange());
    document
      .getElementById("save-new-field")
      .addEventListener("click", (e) => this.handleSaveNewField(e));
    document
      .getElementById("add-new-option")
      .addEventListener("click", () => this.handleAddNewOption());

    // Main buttons
    document
      .getElementById("show-forms")
      .addEventListener("click", () => this.handleShowForms());
    document
      .getElementById("form-selector")
      .addEventListener("change", (e) => this.handleFormSelectorChange(e));
    document
      .getElementById("save-form-btn")
      .addEventListener("click", () => this.handleSaveForm());
  }

  handleAddNewForm() {
    const newFormName = document.getElementById("new-form-name").value.trim();
    const formNameHeader = document.getElementById("placeholder-form-name");
    const mainContainerForm = document.getElementById("main-container-form");
    const formContainer = document.getElementById("show-saved-forms");

    if (!newFormName) {
      this.showAlert("خطا!", "نام فرم الزامی است", "error");
      return;
    }

    formNameHeader.textContent = newFormName;
    mainContainerForm.classList.remove("d-none");
    formContainer.classList.add("d-none");

    // پاک کردن مقدار ورودی
    document.getElementById("new-form-name").value = "";

    // بستن مودال
    this.modals.addFormName.hide();
    const backdrop = document.querySelector("div.modal-backdrop");
    if (backdrop) {
      backdrop.remove()
    }
  }

  handleFeatureTypeChange() {
    const featureTypeElement = document.getElementById("input-type");
    const featureType = featureTypeElement.value;
    const containerOptionsInput = document.getElementById(
      "container-options-modal"
    );
    const featureName = document.getElementById("new-feature-name");

    if (["select", "checkbox", "radio"].includes(featureType)) {
      document.getElementById("selected-input").textContent =
        featureTypeElement.options[
          featureTypeElement.selectedIndex
        ].textContent;
      containerOptionsInput.classList.remove("d-none");
    } else if (featureType === "geo") {
      featureName.value = "موقعیت جغرافیایی";
      featureName.disabled = true;
    } else {
      featureName.disabled = false;
    }
  }

  handleSaveNewField(event) {
    event.preventDefault();

    const featureName = document.getElementById("new-feature-name").value;
    const featureType = document.getElementById("input-type").value;
    const formContainerDiv = document.getElementById("form-container-builder");

    if (!featureName || !featureType) {
      this.showAlert("خطا!", "نام و نوع وبژگی را وارد کنید", "error");
      return;
    }

    const field = this.createField(featureName, featureType);
    if (field) {
      formContainerDiv.appendChild(field);
      document.getElementById("modal-new-field-form").reset();
      this.modals.addForm.hide();
        const backdrop = document.querySelector("div.modal-backdrop");
    if (backdrop) {
      backdrop.remove()
    }
    }
  }

  createField(featureName, featureType) {
    const numberOfFields =
      document.querySelectorAll("#form-container-builder .col-sm-6").length + 1;
    const divColSM6 = document.createElement("div");
    divColSM6.classList.add("col-sm-6", "border-bottom");

    const divMb3 = document.createElement("div");
    divMb3.classList.add("mb-3");

    const label = document.createElement("label");
    label.textContent = featureName;
    label.classList.add("form-label");

    let inputElement;

    switch (featureType) {
      case "text":
      case "number":
      case "date":
      case "time":
        inputElement = this.createInputField(featureType, numberOfFields);
        break;
      case "file":
        inputElement = this.createInputField(featureType, numberOfFields);
        inputElement.accept = "image/*";
        break;
      case "select":
        inputElement = this.createSelectField(featureName, numberOfFields);
        break;
      case "checkbox":
      case "radio":
        inputElement = this.createCheckboxRadioField(
          featureName,
          featureType,
          numberOfFields
        );
        break;
      case "geo":
        inputElement = this.createGeoField();
        break;
    }

    if (!inputElement) return null;

    divMb3.append(label, inputElement);
    divColSM6.appendChild(divMb3);
    return divColSM6;
  }

  createInputField(type, numberOfFields) {
    const input = document.createElement("input");
    input.type = type;
    input.classList.add("form-control");
    input.name = "input-" + numberOfFields;
    return input;
  }

  createSelectField(featureName, numberOfFields) {
    const optionsBuilder = document.getElementById("input-option-container");
    const options = optionsBuilder.querySelectorAll("input.input-option");
    const selectElement = document.createElement("select");
    selectElement.classList.add("form-control");
    selectElement.name = "input-" + numberOfFields;

    const firstOption = document.createElement("option");
    firstOption.textContent = "--انتخاب کنید--";
    selectElement.appendChild(firstOption);

    let hasValidOptions = false;

    options.forEach((option) => {
      const innerText = option.value.trim();
      if (innerText) {
        const newOption = document.createElement("option");
        newOption.value = innerText;
        newOption.innerText = innerText;
        selectElement.appendChild(newOption);
        hasValidOptions = true;
      }
    });

    if (!hasValidOptions) {
      this.showAlert(
        "خطا",
        "حداقل یک گزینه برای لیست کشویی وارد کنید.",
        "error"
      );
      return null;
    }

    document.getElementById("container-options-modal").classList.add("d-none");
    optionsBuilder.innerHTML = "";
    return selectElement;
  }

  createCheckboxRadioField(featureName, featureType, numberOfFields) {
    const optionsBuilder = document.getElementById("input-option-container");
    const options = optionsBuilder.querySelectorAll("input.input-option");
    const container = document.createElement("div");

    let hasValidOptions = false;

    options.forEach((option) => {
      const innerText = option.value.trim();
      if (innerText) {
        const label = document.createElement("label");
        const inputElement = document.createElement("input");
        const inputuniqcode = `${featureType}-${numberOfFields}-${innerText.replace(
          /\s+/g,
          "-"
        )}`;

        label.textContent = innerText;
        label.htmlFor = inputuniqcode;
        label.classList.add("form-label", "m-2", "p-2");

        inputElement.type = featureType;
        inputElement.value = innerText;
        inputElement.name = `input-${numberOfFields}`;
        inputElement.id = inputuniqcode;

        label.prepend(inputElement);
        container.appendChild(label);
        hasValidOptions = true;
      }
    });

    if (!hasValidOptions) {
      this.showAlert(
        "خطا!",
        "حداقل یک گزینه برای گزینه رادیویی یا چک باکس وارد کنید.",
        "error"
      );
      return null;
    }

    document.getElementById("container-options-modal").classList.add("d-none");
    optionsBuilder.innerHTML = "";
    return container;
  }

  createGeoField() {
    const input = document.createElement("input");
    input.id = "capture-geo-btn";
    input.type = "button";
    input.classList.add("form-control", "bg-warning");
    input.value = "موقعیت جغرافیایی";
    return input;
  }

  handleAddNewOption() {
    const inputOptionContainer = document.getElementById(
      "input-option-container"
    );
    const newInputOption = document.createElement("input");
    newInputOption.type = "text";
    newInputOption.classList.add("form-control", "m-2", "input-option");
    newInputOption.name = "input-option";
    newInputOption.placeholder = "گزینه را وارد کنید";
    inputOptionContainer.appendChild(newInputOption);
  }

  handleShowForms() {
    const formContainer = document.getElementById("show-saved-forms");
    const mainContainerForm = document.getElementById("main-container-form");
    mainContainerForm.classList.add("d-none");
    formContainer.classList.remove("d-none");

    this.fetchData("/wp-admin/admin-ajax.php?action=get_saved_forms")
      .then((data) => {
        if (data.success) {
          this.populateFormSelector(data.data.forms);
        } else {
          this.showAlert("خطا!", "خطا در دریافت فرم‌ها.", "error");
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        this.showAlert("خطا!", "خطا در دریافت فرم‌ها.", "error");
      });
  }

  populateFormSelector(forms) {
    const formSelector = document.getElementById("form-selector");
    formSelector.innerHTML = "<option selected>--انتخاب فرم--</option>";

    forms.forEach((form) => {
      const option = document.createElement("option");
      option.value = form.id;
      option.textContent = form.form_name;
      formSelector.appendChild(option);
    });
  }

  handleFormSelectorChange(event) {
    const formId = event.target.value;
    const showFormsNameChecks = document.getElementById("show-header-forms");
    const showFormNameCheck = document.getElementById("show-form-name-check");

    showFormNameCheck.innerText =
      event.target.options[event.target.selectedIndex].textContent;
    showFormsNameChecks.classList.remove("d-none");

    this.fetchData("/wp-admin/admin-ajax.php?action=get_form_fields", {
      form_id: formId,
    })
      .then((data) => {
        if (data.success) {
          this.displayFormData(data.data);
        } else {
          this.showAlert("خطا!", "خطا در دریافت فیلدهای فرم.", "error");
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        this.showAlert("خطا!", "خطا در دریافت فیلدهای فرم.", "error");
      });
  }

  displayFormData(formData) {
    // Display locations
    const locationDiv = document.getElementById("locations-display");
    locationDiv.innerHTML = "";

    if (
      formData.form_locations &&
      Array.isArray(formData.form_locations) &&
      formData.form_locations.length > 0
    ) {
      const locationLabel = document.createElement("h6");
      locationLabel.classList.add("text-dark", "my-2");
      locationLabel.innerText = "لیست محل‌های فرم:";
      locationDiv.appendChild(locationLabel);

      formData.form_locations.forEach((location) => {
        const locationsSpan = document.createElement("span");
        locationsSpan.classList.add(
          "p-1",
          "text-dark",
          "rounded",
          "shadow",
          "bg-light",
          "mx-1"
        );
        locationsSpan.innerText = location;
        locationDiv.appendChild(locationsSpan);
      });
    }

    // Display form fields
    const formContainer = document.getElementById("display-form");
    formContainer.innerHTML = "";

    formData.fields.forEach((field) => {
      const fieldDiv = document.createElement("div");
      fieldDiv.classList.add("col-sm-6", "border-bottom", "p-2");

      const label = document.createElement("label");
      label.textContent =
        field.field_name === "equipment_id" ? "سریال تجهیز" : field.field_name;
      label.classList.add("form-label");

      let inputElement;
      const options = JSON.parse(field.options);

      switch (field.field_type) {
        case "text":
        case "number":
        case "date":
        case "time":
          inputElement = this.createInputField(field.field_type);
          break;
        case "file":
          inputElement = this.createInputField(field.field_type);
          inputElement.accept = "image/*";
          break;
        case "select":
          inputElement = this.createFilledSelectField(options);
          break;
        case "checkbox":
        case "radio":
          fieldDiv.appendChild(label);
          this.createCheckboxRadioDisplay(field, fieldDiv);
          break;
        case "button": // For geo location
          inputElement = document.createElement("input");
          inputElement.id = "capture-geo-btn";
          inputElement.type = "button";
          inputElement.classList.add("form-control", "bg-warning");
          inputElement.value = "موقعیت جغرافیایی";
          break;
      }

      if (inputElement) {
        fieldDiv.appendChild(label);
        fieldDiv.appendChild(inputElement);
      }

      formContainer.appendChild(fieldDiv);
    });

    // Add remove button
    this.addRemoveButton(formContainer);
  }

  createFilledSelectField(options) {
    const select = document.createElement("select");
    select.classList.add("form-control");

    options.forEach((option) => {
      const optionElement = document.createElement("option");
      optionElement.value = option;
      optionElement.textContent = option;
      select.appendChild(optionElement);
    });

    return select;
  }

  createCheckboxRadioDisplay(field, container) {
    const options = JSON.parse(field.options);

    options.forEach((option) => {
      const optionContainer = document.createElement("div");
      optionContainer.classList.add("form-check");

      const inputElement = document.createElement("input");
      inputElement.type = field.field_type;
      inputElement.classList.add("form-check-input");
      inputElement.name = `field-${field.id}`;
      inputElement.value = option;

      const optionLabel = document.createElement("label");
      optionLabel.textContent = option;
      optionLabel.classList.add("form-check-label");

      optionContainer.appendChild(inputElement);
      optionContainer.appendChild(optionLabel);
      container.appendChild(optionContainer);
    });
  }

  addRemoveButton(container) {
    const removeBtn = document.createElement("button");
    removeBtn.type = "button";
    removeBtn.textContent = "حذف فرم";
    removeBtn.classList.add("btn", "btn-danger", "btn-lg", "shadow", "mt-2");
    removeBtn.addEventListener("click", () => this.handleRemoveForm());
    container.appendChild(removeBtn);
  }

  handleSaveForm() {
    const formName = document.getElementById(
      "placeholder-form-name"
    ).textContent;
    const formContainerDiv = document.getElementById("form-container-builder");
    const fields = [];

    formContainerDiv.querySelectorAll(".col-sm-6").forEach((fieldDiv) => {
      const label = fieldDiv.querySelector(".form-label").textContent;
      const input = fieldDiv.querySelector("input, select");

      if (!input) return;

      const fieldType =
        input.tagName.toLowerCase() === "select" ? "select" : input.type;
      let options = [];
      let value = "";

      if (fieldType === "select") {
        options = Array.from(input.options).map((option) => option.value);
        value = input.value;
      } else if (["checkbox", "radio"].includes(fieldType)) {
        const inputs = fieldDiv.querySelectorAll(`input[type="${fieldType}"]`);
        const selectedValues = [];

        inputs.forEach((input) => {
          if (input.checked) selectedValues.push(input.value);
          options.push(input.value);
        });

        value = selectedValues.join(",");
      } else {
        value = input.value;
      }

      fields.push({
        field_name: label,
        field_type: fieldType,
        options: options,
        value: value,
      });
    });

    const locationsInput = document.querySelectorAll(
      'input[name="locations[]"]:checked'
    );
    const locations = Array.from(locationsInput).map((input) => input.value);

    const formData = {
      form_name: formName,
      locations: locations,
      fields: fields,
    };

    this.fetchData("/wp-admin/admin-ajax.php?action=save_form_data", {
      form_data: JSON.stringify(formData),
    })
      .then((data) => {
        if (data.success) {
          this.showAlert("موفق!", data.data.message, "success").then(() =>
            this.handleShowForms()
          );
        } else {
          this.showAlert("خطا!", "خطا در ذخیره‌سازی فرم.", "error");
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        this.showAlert("خطا!", "خطا در ذخیره‌سازی فرم.", "error");
      });
  }

  handleRemoveForm() {
    const formSelector = document.getElementById("form-selector");
    const formId = formSelector.options[formSelector.selectedIndex].value;

    this.showConfirm(
      "آیا از حذف فرم مطمئن هستید؟",
      "این عمل قابل بازگشت نیست! اطلاعات تجهیزاتی که با این فرم ساخته شده نیز حذف خواهد شد.",
      "warning",
      "بله، حذف شود!",
      "لغو"
    ).then((result) => {
      if (result.isConfirmed) {
        this.fetchData("/wp-admin/admin-ajax.php?action=remove_form", {
          form_id: formId,
        })
          .then((data) => {
            if (data.success) {
              this.showAlert("حذف شد!", data.data.message, "success").then(
                () => {
                  window.location.href = `${window.location.origin}/panel/formmaker/?showforms=on`;
                }
              );
            } else {
              this.showAlert("خطا!", data.data.message, "error");
            }
          })
          .catch((error) => {
            console.error("Error:", error);
            this.showAlert("خطا!", "خطا در حذف فرم.", "error");
          });
      }
    });
  }

  // Utility methods
  fetchData(url, body = {}) {
    const params = new URLSearchParams(body);
    return fetch(url, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: params,
    }).then((response) => response.json());
  }

  showAlert(title, text, icon) {
    return Swal.fire({ title, text, icon });
  }

  showConfirm(title, text, icon, confirmText, cancelText) {
    return Swal.fire({
      title,
      text,
      icon,
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: confirmText,
      cancelButtonText: cancelText,
    });
  }
}

// Initialize the FormManager when DOM is loaded
document.addEventListener("DOMContentLoaded", () => {
  new FormManager();
});
