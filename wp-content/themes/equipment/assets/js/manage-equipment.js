class EquipmentFormHandler {
  constructor() {
    this.serialInput = document.getElementById("serial-input");
    this.searchBtn = document.getElementById("search-btn");
    this.formContainer = document.getElementById("form-container");
    this.formSelector = document.getElementById("form-selector");
    this.saveDataBtn = document.getElementById("save-data-btn");
    this.scanQrBtn = document.getElementById("scan-qr-btn");
    this.qrReader = document.getElementById("qr-reader");
    this.searchResult = document.getElementById("description-search-result");
    this.initEventListeners();
  }

  initEventListeners() {
    this.searchBtn.addEventListener("click", () => this.handleSearch());
    this.formSelector.addEventListener("change", () =>
      this.handleFormSelectorChange()
    );
    this.saveDataBtn.addEventListener("click", () => this.handleSaveData());
    this.scanQrBtn.addEventListener("click", () => this.handleScanQr());
  }

  handleSearch() {
    const equipmentId = this.serialInput.value.trim();
    if (!equipmentId) {
      Swal.fire({
        title: "خطا!",
        text: "سریال را بدرستی وارد کنید",
        icon: "error",
      });
      return;
    }
    this.formContainer.innerHTML = "";
    this.formSelector.innerHTML = "";
    this.searchResult.innerHTML = "";
    this.formSelector.style.display = "none";
    this.saveDataBtn.style.display = "none";

    this.fetchData("/wp-admin/admin-ajax.php?action=get_equipment_data", {
      equipment_id: equipmentId,
    })
      .then((data) => {
        if (data.success) {
          if (data.data.status) {
            this.displayEquipmentData(data.data);
            // Attach event listener for geo-location button
            this.captureGeoBtn = document.getElementById("capture-geo-btn");
            if (this.captureGeoBtn) {
              this.captureGeoBtn.addEventListener("click", () =>
                this.handleGeoLocation()
              );
            }
          } else {
            this.searchResult.textContent =
              "تجهیز مورد نظر یافت نشد! برای ثبت اطلاعات فرم مرتبط را انتخاب کنید";
            this.displayFormSelector();
          }
        } else {
          Swal.fire({
            title: "خطا!",
            text: "خطا در جستجوی اطلاعات تجهیز.",
            icon: "error",
          });
        }
      })
      .catch((error) => {
        console.error("Error:", error);
      });
  }

  displayEquipmentData(data) {
    this.formContainer.innerHTML = ""; // Clear previous form
    data.data.forEach((item) => {
      const fieldDiv = document.createElement("div");
      fieldDiv.classList.add("col-sm-6", "border-bottom", "p-2");
      const label = document.createElement("label");
      label.textContent =
        item.field_name === "equipment_id" ? "سریال تجهیز" : item.field_name;
      label.classList.add("form-label");
      fieldDiv.appendChild(label);

      // Handle file/image fields
      if (item.field_type === "file" || item.field_type === "image") {
        if (item.value) {
          // Create a container for the image
          const imageContainer = document.createElement("div");
          imageContainer.classList.add("image-container");

          // Create a thumbnail image
          const thumbnail = document.createElement("img");
          thumbnail.src = item.value; // Use the file URL
          thumbnail.alt = item.field_name;
          thumbnail.classList.add("img-thumbnail", "img-responsive"); // Add Bootstrap classes for responsiveness
          thumbnail.style.maxWidth = "100%"; // Ensure the image is responsive
          thumbnail.style.height = "auto"; // Maintain aspect ratio

          // Create a link to view the full-size image
          const imageLink = document.createElement("a");
          imageLink.href = item.value; // Link to the full-size image
          imageLink.target = "_blank"; // Open in a new tab
          imageLink.appendChild(thumbnail); // Add the thumbnail to the link

          // Add the link to the container
          imageContainer.appendChild(imageLink);

          // Add the container to the field div
          fieldDiv.appendChild(imageContainer);
        } else {
          // If no file is uploaded, display a message
          const noFileMessage = document.createElement("span");
          noFileMessage.textContent = "No file uploaded.";
          noFileMessage.classList.add("text-muted");
          fieldDiv.appendChild(noFileMessage);
        }
      } else if (item.field_type === "geo_location") {
        // Display geo-location data
        const geoLocationDisplay = document.createElement("div");
        geoLocationDisplay.textContent =
          item.value || "موقعیت جغرافیایی ثبت نشده است.";
        geoLocationDisplay.classList.add("text-muted");
        fieldDiv.appendChild(geoLocationDisplay);
      } else {
        // For other field types, create the appropriate input element
        const inputElement = this.createInputElement(item);
        fieldDiv.appendChild(inputElement);
      }

      this.formContainer.appendChild(fieldDiv);
    });

    //button group
    const divGroupBtns = document.createElement("div");
    divGroupBtns.classList.add("btn-group");
    divGroupBtns.setAttribute("role", "group");
    divGroupBtns.setAttribute("aria-label", "Two button group");

    // Add an "Edit" button
    const editButton = document.createElement("button");
    editButton.textContent = "ویرایش";
    editButton.classList.add("btn", "btn-primary", "mt-3");
    editButton.addEventListener("click", () => this.enableEditMode());
    divGroupBtns.appendChild(editButton);

    // Add a "Remove" button
    const removeButton = document.createElement("button");
    removeButton.textContent = "حذف";
    removeButton.classList.add("btn", "btn-danger", "mt-3", "ml-2");
    removeButton.addEventListener("click", () => this.handleRemoveEquipment());
    divGroupBtns.appendChild(removeButton);
    this.formContainer.appendChild(divGroupBtns);
  }

  createInputElement(item) {
    let inputElement;
    let options;
    switch (item.field_type) {
      case "text":
      case "number":
      case "date":
      case "time":
        inputElement = document.createElement("input");
        inputElement.type = item.field_type;
        inputElement.classList.add("form-control");
        inputElement.value = item.value ?? "";
        inputElement.name = `field_${item.id}`; // Ensure the name is set correctly
        inputElement.disabled = item.field_name === "equipment_id";
        break;
      case "select":
        inputElement = document.createElement("select");
        inputElement.classList.add("form-control");
        inputElement.name = `field_${item.id}`; // Ensure the name is set correctly
        options = JSON.parse(item.options);
        options.forEach((option) => {
          const optionElement = document.createElement("option");
          optionElement.value = option;
          optionElement.textContent = option;
          if (option === item.value) {
            optionElement.selected = true;
          }
          inputElement.appendChild(optionElement);
        });
        break;
      case "checkbox":
      case "radio":
        inputElement = document.createElement("div");
        const values = item.value ? item.value.split(",") : "";
        options = JSON.parse(item.options);
        options.forEach((option) => {
          const optionDiv = document.createElement("div");
          optionDiv.classList.add("form-check");
          const input = document.createElement("input");
          input.type = item.field_type;
          input.name = `field_${item.id}`; // Ensure the name is set correctly
          input.value = option;
          input.classList.add("form-check-input");
          if (values.includes(option)) {
            input.checked = true;
          }
          const optionLabel = document.createElement("label");
          optionLabel.textContent = option;
          optionLabel.classList.add("form-check-label");
          optionDiv.appendChild(input);
          optionDiv.appendChild(optionLabel);
          inputElement.appendChild(optionDiv);
        });
        break;
      case "file":
      case "image":
        inputElement = document.createElement("input");
        inputElement.type = "file";
        inputElement.classList.add("form-control");
        inputElement.accept = "image/*";
        inputElement.name = `field_${item.id}`; // Ensure the name is set correctly
        break;
      case "button":
        // Button for geo-location
        inputElement = document.createElement("button");
        inputElement.id = "capture-geo-btn";
        inputElement.type = "button";
        inputElement.classList.add("form-control", "bg-warning");
        inputElement.textContent = "موقعیت جغرافیایی";
        break;
      default:
        inputElement = document.createElement("input");
        inputElement.type = "text";
        inputElement.classList.add("form-control");
        inputElement.value = item.value;
        inputElement.name = `field_${item.id}`; // Ensure the name is set correctly
        break;
    }
    return inputElement;
  }

  displayFormSelector() {
    this.fetchData("/wp-admin/admin-ajax.php?action=get_saved_forms")
      .then((data) => {
        if (data.success) {
          if (data.data.status === "empty") {
            this.formSelector.classList.add("text-danger", "fw-bold");
            this.formSelector.innerHTML =
              '<option  value=""> برای شما هیچ فرمی ساخته نشده است </option>';
            this.formSelector.style.display = "block";
            return;
          }
          this.formSelector.innerHTML =
            '<option value="">-- انتخاب فرم --</option>';
          data.data.forms.forEach((form) => {
            const option = document.createElement("option");
            option.value = form.id;
            option.textContent = form.form_name;
            this.formSelector.appendChild(option);
          });
          this.formSelector.style.display = "block";
        } else {
          Swal.fire({
            title: "خطا!",
            text: "خطا در دریافت فرم‌ها.",
            icon: "error",
          });
        }
      })
      .catch((error) => {
        console.error("Error:", error);
      });
  }

  handleFormSelectorChange() {
    const formId = this.formSelector.value;
    this.fetchData("/wp-admin/admin-ajax.php?action=get_form_fields", {
      form_id: formId,
    })
      .then((data) => {
        if (data.success) {
          this.formContainer.innerHTML = "";
          console.log(data.data);
          data.data.fields.forEach((field) => {
            const fieldDiv = document.createElement("div");
            fieldDiv.classList.add("col-sm-6", "border-bottom", "p-2");
            const label = document.createElement("label");
            label.textContent =
              field.field_name === "equipment_id"
                ? "سریال تجهیز"
                : field.field_name;
            label.classList.add("form-label");
            const inputElement = this.createInputElement(field);
            fieldDiv.appendChild(label);
            fieldDiv.appendChild(inputElement);
            this.formContainer.appendChild(fieldDiv);
          });

          // Check if the captureGeoBtn exists before adding the event listener
          this.captureGeoBtn = document.getElementById("capture-geo-btn");
          if (this.captureGeoBtn) {
            this.captureGeoBtn.addEventListener("click", () =>
              this.handleGeoLocation()
            );
          }

          this.saveDataBtn.style.display = "block";
        } else {
          Swal.fire({
            title: "خطا!",
            text: "خطا در دریافت فیلدهای فرم.",
            icon: "error",
          });
        }
        const equipmentIdInput = this.formContainer.querySelector("input");
        equipmentIdInput.id = "equipment-id";
        equipmentIdInput.value = this.serialInput.value;
        equipmentIdInput.disabled = true;
      })
      .catch((error) => {
        console.error("Error:", error);
      });
  }

  handleSaveData() {
    const equipmentId = this.serialInput.value;
    const formId = this.formSelector.value; // Ensure form_id is included
    const formData = new FormData();

    // Append serial number and form ID
    formData.append("equipment_id", equipmentId);
    formData.append("form_id", formId); // Include form_id in the request

    // Append form data as JSON
    const formFields = {};
    this.formContainer.querySelectorAll("input, select").forEach((input) => {
      const fieldId = input.name.replace("field_", ""); // Extract field_id from the name attribute
      if (input.type === "file") {
        if (input.files.length > 0) {
          formData.append(`field_${fieldId}`, input.files[0]);
        }
      } else if (input.type === "checkbox") {
        // Handle checkboxes
        if (input.checked) {
          if (!formFields[fieldId]) {
            formFields[fieldId] = []; // Initialize as an array if not already
          }
          formFields[fieldId].push(input.value); // Push the value into the array
        }
      } else if (input.type === "radio") {
        // Handle radio buttons (only one can be selected)
        if (input.checked) {
          formFields[fieldId] = input.value;
        }
      } else {
        // Handle other input types
        formFields[fieldId] = input.value;
      }
    });

    // Append geo-location data if available
    const geoLocationInput = this.formContainer.querySelector(
      'input[name="geo_location"]'
    );
    if (geoLocationInput) {
      formFields["geo_location"] = geoLocationInput.value;
    }
    // Convert arrays to comma-separated strings for checkboxes and radio buttons
    for (const key in formFields) {
      if (Array.isArray(formFields[key])) {
        formFields[key] = formFields[key].join(",");
      }
    }

    // Append form fields as JSON
    formData.append("form_data", JSON.stringify(formFields));

    // Send the form data
    this.fetchData(
      "/wp-admin/admin-ajax.php?action=save_equipment_data",
      formData
    )
      .then((data) => {
        if (data.success) {
          Swal.fire({
            title: "Success!",
            text: data.data.message,
            icon: "success",
          }).then(() => {
            window.location.href =
              window.location.origin + "/panel/equipmenttracker";
          });
        } else {
          Swal.fire({
            title: "خطا!",
            text: "خطا در ذخیره‌سازی داده‌ها.",
            icon: "error",
          });
        }
      })
      .catch((error) => {
        console.error("Error:", error);
      });
  }
  enableEditMode() {
    // Enable all input fields
    this.formContainer
      .querySelectorAll("input, select, textarea")
      .forEach((input) => {
        input.disabled = false;
      });

    // Change the "Edit" button to a "Save" button
    const editButton = this.formContainer.querySelector("button");
    editButton.textContent = "ذخیره";
    editButton.classList.remove("btn-primary");
    editButton.classList.add("btn-success");
    editButton.removeEventListener("click", () => this.enableEditMode());
    editButton.addEventListener("click", () => this.handleSaveData());
  }

  handleScanQr() {
    const html5QrCode = new Html5Qrcode("qr-reader");
    html5QrCode
      .start(
        { facingMode: "environment" },
        {
          fps: 10,
          qrbox: 250,
        },
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
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        (position) => {
          const latitude = position.coords.latitude;
          const longitude = position.coords.longitude;
          console.log("Geo-location captured:", { latitude, longitude });

          // Create a hidden input for geo-location data
          const geoInput = document.createElement("input");
          geoInput.type = "hidden";
          geoInput.name = "geo_location";
          geoInput.value = `${latitude},${longitude}`;
          this.formContainer.appendChild(geoInput);

          // Show success message
          Swal.fire({
            title: "Success!",
            text: "موقعیت جغرافیایی با موفقیت ثبت شد.",
            icon: "success",
          });
        },
        (error) => {
          Swal.fire({
            title: "خطا!",
            text: "خطا در دریافت موقعیت جغرافیایی.",
            icon: "error",
          });
        },
        { timeout: 10000 } // Optional: Set a timeout for the request
      );
    } else {
      Swal.fire({
        title: "خطا!",
        text: "مرورگر شما از دریافت موقعیت جغرافیایی پشتیبانی نمی‌کند.",
        icon: "error",
      });
    }
  }
  handleRemoveEquipment() {
    const equipmentId = this.serialInput.value.trim();
    if (!equipmentId) {
      Swal.fire({
        title: "خطا!",
        text: "سریال را بدرستی وارد کنید",
        icon: "error",
      });
      return;
    }

    Swal.fire({
      title: "آیا مطمئن هستید؟",
      text: "این عمل قابل بازگشت نیست!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "بله، حذف شود!",
      cancelButtonText: "لغو",
    }).then((result) => {
      if (result.isConfirmed) {
        this.fetchData(
          "/wp-admin/admin-ajax.php?action=remove_equipment_data",
          { equipment_id: equipmentId }
        )
          .then((data) => {
            if (data.success) {
              Swal.fire({
                title: "حذف شد!",
                text: "تجهیز با موفقیت حذف شد.",
                icon: "success",
              }).then(() => {
                window.location.href =
                  window.location.origin + "/panel/equipmenttracker";
              });
            } else {
              Swal.fire({
                title: "خطا!",
                text: "خطا در حذف تجهیز.",
                icon: "error",
              });
            }
          })
          .catch((error) => {
            console.error("Error:", error);
          });
      }
    });
  }

  fetchData(url, body = {}) {
    // If body is FormData, send it directly without headers
    if (body instanceof FormData) {
      return fetch(url, {
        method: "POST",
        body: body, // No headers needed for FormData
      }).then((response) => response.json());
    } else {
      // For JSON data, use URLSearchParams
      const params = new URLSearchParams(body);
      return fetch(url, {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: params,
      }).then((response) => response.json());
    }
  }
}

document.addEventListener("DOMContentLoaded", () => {
  new EquipmentFormHandler();
});
