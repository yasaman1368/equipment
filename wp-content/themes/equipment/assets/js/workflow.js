// ======================
// MODAL UTILITIES
// ======================
const ModalUtils = {
  show: (modalId) => {
    const modalEl = document.getElementById(modalId);
    const modal =
      bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
    modal.show();
  },

  hide: (modalId) => {
    const modalEl = document.getElementById(modalId);
    if (modalEl) {
      const modal = bootstrap.Modal.getInstance(modalEl);
      if (modal) {
        modal.hide();
      } else {
        // If no instance exists but we need to hide, create and immediately hide
        const newModal = new bootstrap.Modal(modalEl);
        newModal.hide();
      }
    }
  },
};

// ======================
// NOTIFICATION UTILITIES
// ======================
const Notification = {
  toast: Swal.mixin({
    toast: true,
    position: "bottom-start",
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
      toast.onmouseenter = Swal.stopTimer;
      toast.onmouseleave = Swal.resumeTimer;
    },
  }),

  show: (type, message) => {
    Notification.toast.fire({
      icon: type,
      title: message,
    });
  },

  confirm: (options) => {
    return Swal.fire({
      title: options.title || "Are you sure?",
      text: options.text || "",
      icon: options.icon || "warning",
      showCancelButton: true,
      confirmButtonText: options.confirmText || "Yes",
      cancelButtonText: options.cancelText || "Cancel",
    });
  },
};

// ======================
// API SERVICE
// ======================
const ApiService = {
  request: async (method, action, { params = {}, data } = {}) => {
    const url = new URL("/wp-admin/admin-ajax.php", window.location.origin);
    url.searchParams.append("action", action);

    if (method === "GET" && params) {
      Object.entries(params).forEach(([key, value]) => {
        url.searchParams.append(key, value);
      });
    }

    let response;
    try {
      response = await fetch(url, {
        method,
        headers: {
          Accept: "application/json",
          ...(method === "POST" && !(data instanceof FormData)
            ? { "Content-Type": "application/x-www-form-urlencoded" }
            : {}),
        },
        ...(method === "POST"
          ? {
              body: data instanceof FormData ? data : new URLSearchParams(data),
            }
          : {}),
      });
    } catch (error) {
      console.error(`Error in ${method} ${action}:`, error);
      throw error;
    }

    if (!response.ok) {
      const errorBody = await response.json();
      const error = new Error(`Request failed with status ${response.status}`);
      error.status = response.status;
      error.response = errorBody;
      throw error;
    }

    return await response.json();
  },

  get(action, params = {}) {
    return this.request("GET", action, { params });
  },

  post(action, data = {}) {
    return this.request("POST", action, { data });
  },
};

// ======================
// WORKFLOW
// ======================
const Workflow = {
  formContainer: document.getElementById("form-container"),
  init() {
    this.bindEvent();
  },

  bindEvent() {
    document.addEventListener("DOMContentLoaded", this.notificationCount());
    document.addEventListener("click", (e) => {
      if (e.target.classList.contains("displayEquipmentView")) {
        this.fetchEquipmentData(e.target.dataset.equipmentId);
      }
    });
  },
  //show notifitions for user's
  notificationCount() {
    counterElement =
      document.querySelector("input[name=notificationCount]") || "";
    holderNotificationsCount = document.querySelector("#notificationCount");

    if (!counterElement) {
      holderNotificationsCount.classList.add("d-none");
      return;
    }
    numberNotification = counterElement.value;
    holderNotificationsCount.textContent = numberNotification;
  },
  //show equipment
  async fetchEquipmentData(equipmentId) {
    try {
      response = await ApiService.post("get_equipment_data", {
        equipment_id: equipmentId,
      });
      if (!response.success) throw new Error("Error fetching equipment data");

      this.displayEquipmentData(response.data);
      // Attach event listener for geo-location button
      this.captureGeoBtn = document.getElementById("capture-geo-btn");
      if (this.captureGeoBtn) {
        this.captureGeoBtn.addEventListener("click", () =>
          this.handleGeoLocation()
        );
      }
      ModalUtils.show("modalIddisplayEquipment");
    } catch (error) {
      console.error("Error fetching equipment data:", error);
      Notification.show("error", "دریافت اطلاعات تحهیز با خطا مواجه شده است");
    }
  },

  displayEquipmentData(data) {
    this.formContainer.innerHTML = ""; // Clear previous form
    data.data.forEach((item) => {
      const fieldDiv = document.createElement("div");
      fieldDiv.classList.add("col-sm-12", "border-bottom", "p-2");
      const label = document.createElement("label");
      label.textContent = item.field_name;
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
  },

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
        inputElement.disabled = item.field_name === "equipment_serial";
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
  },
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
  },

  // approve equpment data
  // reject equipment data
};

// ======================
// INITIALIZATION
// ======================
document.addEventListener("DOMContentLoaded", () => {
  Workflow.init();
});
