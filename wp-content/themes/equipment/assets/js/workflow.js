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
  equipmentData: [],

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
    document.addEventListener("click", (e) => {
      if (e.target.classList.contains("displayHistory")) {
        this.getHistorySteps(e.target.dataset.equipmentId);
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

      this.equipmentData = response.data;

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
        const inputElement = this.createInputDiabeldElement(item);
        fieldDiv.appendChild(inputElement);
      }

      this.formContainer.appendChild(fieldDiv);
    });

    const input = document.createElement("input");
    input.setAttribute("type", "hidden");
    input.setAttribute("data-equipment-id", data.equipment_id);
    this.formContainer.appendChild(input);
    //------------------------------

    const divTextareaComment = document.createElement("div");
    divTextareaComment.classList.add(
      "col-12",
      "mb-3",
      "p-2",
      "d-none",
      "divTextareaComment"
    );

    const labelComment = document.createElement("label");
    labelComment.setAttribute("for", "comment");
    labelComment.textContent = "نظر";

    const textareaComment = document.createElement("textarea");
    textareaComment.classList.add("form-control", "w-100");
    textareaComment.name = "comment";
    textareaComment.id = "comment";
    textareaComment.rows = 3;

    divTextareaComment.append(labelComment, textareaComment);
    this.formContainer.appendChild(divTextareaComment);

    const divGroupBtns = document.createElement("div");
    divGroupBtns.classList.add("d-flex", "w-100"); // Changed from btn-group to flex
    divGroupBtns.setAttribute("role", "group");
    divGroupBtns.setAttribute("aria-label", "Three button group");

    const rejectCommentElement = document.querySelector(
      `input[data-comment-${data.equipment_id}]`
    );
    const isUserModal = rejectCommentElement?.dataset.userModal === "true";

    if (isUserModal) {
      const comment = rejectCommentElement.getAttribute(
        `data-comment-${data.equipment_id}`
      );
      labelComment.textContent = "دلیل رد اطلاعات ارسالی";
      divTextareaComment.classList.remove("d-none");
      textareaComment.readOnly = true;
      textareaComment.value = comment;
      textareaComment.disabled = true;

      divGroupBtns.appendChild(
        Utils.createButton("ویرایش", ["btn-primary"], "editButton", (e) =>
          this.enableEditMode(e)
        )
      );
    } else {
      divGroupBtns.append(
        Utils.createButton("تایید", ["btn-success"], "approveButton", (e) =>
          this.handleApproveEquipmentData(e)
        ),
        Utils.createButton("ویرایش", ["btn-primary"], "editButton", (e) =>
          this.enableEditMode(e)
        ),
        Utils.createButton("رد", ["btn-danger"], "rejectButton", (e) =>
          this.enableRejectMode(e)
        )
      );
    }

    this.formContainer.appendChild(divGroupBtns);
  },

  createInputDiabeldElement(item) {
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
        inputElement.disabled = true;
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
          inputElement.disabled = true;
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
          input.disabled = true;
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
        inputElement.disabled = true;
        break;
      case "button":
        // Button for geo-location
        inputElement = document.createElement("button");
        inputElement.id = "capture-geo-btn";
        inputElement.type = "button";
        inputElement.classList.add("form-control", "bg-warning");
        inputElement.textContent = "موقعیت جغرافیایی";
        inputElement.disabled = true;
        break;
      default:
        inputElement = document.createElement("input");
        inputElement.type = "text";
        inputElement.classList.add("form-control");
        inputElement.value = item.value;
        inputElement.name = `field_${item.id}`; // Ensure the name is set correctly
        inputElement.disabled = true;
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

  enableEditMode(e) {
    e.preventDefault();

    this.formContainer
      .querySelectorAll("input, select, textarea")
      .forEach((input) => {
        input.disabled = false;
      });

    const rejectButton = this.formContainer.querySelector(
      "button[data-name=rejectButton]"
    );
    const approveButton = this.formContainer.querySelector(
      "button[data-name=approveButton]"
    );

    rejectButton?.remove();
    approveButton?.remove();

    const isUserModal =
      document.querySelector("input[data-user-modal]")?.dataset.userModal ===
      "true";

    const divTextareaComment = document.querySelector(".divTextareaComment");
    divTextareaComment.classList.remove("d-none");

    if (isUserModal) {
      const textareaComment = document.querySelector("textarea[name=comment]");
      textareaComment.disabled = true;
    }

    // Change the "Edit" button to a "Save" button
    const editButton = this.formContainer.querySelector(
      "button[data-name=editButton]"
    );
    editButton.textContent = "ذخیره";
    editButton.classList.remove("btn-primary");
    editButton.classList.add("btn-success");
    editButton.removeEventListener("click", this.editButtonClickHandler);
    editButton.addEventListener("click", (e) => {
      e.preventDefault();
      this.handleSaveData();
    });
  },

  handleSaveData() {
    const equipmentId = document.querySelector("input[data-equipment-id]")
      .dataset.equipmentId;

    // const formId = this.formSelector.value; // Ensure form_id is included
    const formData = new FormData();

    // Append serial number and form ID
    formData.append("equipment_id", equipmentId);
    // formData.append("form_id", formId); // Include form_id in the request

    // Append form data as JSON
    const formFields = {};
    this.formContainer.querySelectorAll("input, select").forEach((input) => {
      const fieldId = input.name.replace("field_", ""); // Extract field_id from the name attribute
      if (!fieldId) return;
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

    removeExtraData = ({ id, field_name, value }) => ({
      id,
      field_name,
      value,
    });

    hasChanged = (x) => x.value !== formFields[x.id];

    createAction = ({ id, field_name, value }) => ({
      field_name,
      initial_value: value,
      changed_value: formFields[id],
    });

    let editedFromData = {};
    createFormData = (x) => {
      const id = x.id;
      const value = formFields[x.id];

      return (editedFromData[id] = value);
    };
    const changes = this.equipmentData.data
      .map(removeExtraData)
      .filter(hasChanged);

    const editedWorkflowData = changes.map(createAction);
    //prepar formData
    changes.map(createFormData);

    //get comment for editing
    const commentElement = document.getElementById("comment");
    const comment = commentElement.value;
    // Append form fields as JSON
    if (comment) editedWorkflowData.push({ comment: comment });

    formData.append("action_workflow", JSON.stringify(editedWorkflowData));
    formData.append("form_data", JSON.stringify(editedFromData));
    this.handleEditedEquipmentData(formData, equipmentId);
  },
  async handleApproveEquipmentData(e) {
    e.preventDefault();

    const equipmentId = document.querySelector("input[data-equipment-id]")
      .dataset.equipmentId;

    try {
      const response = await ApiService.post("process_equipment_review", {
        equipment_id: equipmentId,
      });

      if (response.success) {
        Utils.removeTableRow(equipmentId);

        ModalUtils.hide("modalIddisplayEquipment");
        Notification.show("success", response.data.message);
      }
    } catch (error) {
      console.error("Error adding equipment data:", error);
      const message =
        error?.response?.data?.message ||
        "خطایی در افزودن اطلاعات مورد نظر رخ داده است";
      Notification.show("error", message);
    }
  },
  async handleEditedEquipmentData(formData, equipmentId) {
    try {
      const response = await ApiService.post("save_equipment_data", formData);
      if (response.success) {
        Utils.removeTableRow(equipmentId);

        ModalUtils.hide("modalIddisplayEquipment");
        Notification.show("success", response.data.message);
      }
    } catch (error) {
      console.error("Error adding equipment data:", error);
      const message =
        error?.response?.data?.message ||
        "خطایی در افزودن اطلاعات مورد نظر رخ داده است";
      Notification.show("error", message);
    }
  },
  enableRejectMode(e) {
    e.preventDefault();

    const editButton = this.formContainer.querySelector(
      "button[data-name=editButton]"
    );
    const approveButton = this.formContainer.querySelector(
      "button[data-name=approveButton]"
    );

    editButton?.remove();
    approveButton?.remove();

    const divTextareaComment = document.querySelector(".divTextareaComment");
    divTextareaComment.classList.remove("d-none");

    const textareaComment = divTextareaComment.querySelector(
      "textarea[name=comment]"
    );

    textareaComment.focus();
    textareaComment.placeholder = "دلیل رد اطلاعات مورد نظر را وارد کنید...";

    e.target.removeEventListener("click", this.rejectButtonClickHandler);
    e.target.addEventListener("click", (e) => {
      e.preventDefault();
      this.handleRejectEquipment();
    });
  },
  async handleRejectEquipment() {
    const textareaComment = document.querySelector("textarea[name=comment]");
    const equipmentId = document.querySelector("input[data-equipment-id]")
      .dataset.equipmentId;

    try {
      const response = await ApiService.post("process_equipment_review", {
        equipment_id: equipmentId,
        action_workflow: JSON.stringify({
          comment: textareaComment.value,
        }),
      });

      if (response.success) {
        Utils.removeTableRow(equipmentId);

        ModalUtils.hide("modalIddisplayEquipment");
        Notification.show("success", response.data.message);
      }
    } catch (error) {
      console.error("Error adding equipment data:", error);
      const message =
        error?.response?.data?.message ||
        "خطایی در افزودن اطلاعات مورد نظر رخ داده است";
      Notification.show("error", message);
    }
  },

  async getHistorySteps(equipmentId) {
    try {
      const response = await ApiService.get("get_process_history", {
        equipment_id: equipmentId,
      });
      if (response.success) {
        this.displayHistory(JSON.parse(response.data.data), equipmentId);
      }
    } catch (error) {
      console.error("Error adding equipment data:", error);
      const message =
        error?.response?.data?.message ||
        "خطایی در افزودن اطلاعات مورد نظر رخ داده است";

      ModalUtils.hide("processHistory");
      Notification.show("error", message);
    }
  },
  displayHistory(steps, equipmentId) {
    const processHistoryBtn = document.querySelectorAll(".processHistory");
    Array.from(processHistoryBtn).map((btn) => {
      btn.setAttribute("data-equipment-id", equipmentId);
      btn.addEventListener("click", this.processHistoryDirection);
    });

    const statusConfig = {
      Pending: { color: "secondary", text: "در انتظار" },
      SupervisorApproved: { color: "success", text: "تایید ناظر" },
      SupervisorReject: { color: "danger", text: "رد ناظر" },
      FinalApprove: { color: "primary", text: "تایید نهایی" },
      ManagerReject: { color: "danger", text: "رد مدیر" },
    };

    const roleNames = {
      user: "کاربر",
      supervisor: "ناظر",
      manager: "مدیر",
    };
    steps.forEach((item, index) => {
      const step = index + 1;
      const status = statusConfig[item.current_status] || {
        color: "dark",
        text: item.current_status,
      };
      const role = roleNames[item.active_role] || item.active_role;

      let actionHtml = "";

      if (item.action) {
        if (Array.isArray(item.action)) {
          let changes = item.action.filter((a) => a.field_name);
          let commentObj = item.action.find((a) => a.comment);

          if (changes.length > 0) {
            actionHtml += `<ul class="small ps-3">`;
            changes.forEach((change) => {
              actionHtml += `<li>
              <span class="fw-bold">${change.field_name}</span> : 
              <span class="text-danger">${change.initial_value}</span> ⟶ 
              <span class="text-success">${change.changed_value}</span>
            </li>`;
            });
            actionHtml += `</ul>`;
          }

          if (commentObj) {
            actionHtml += `<p class="text-muted mb-0"><strong>نظر:</strong> ${commentObj.comment}</p>`;
          }
        } else {
          if (item.action.comment) {
            actionHtml += `<p class="text-muted mb-0"><strong>نظر:</strong> ${item.action.comment}</p>`;
          }
        }
      }

      const card = document.createElement("div");
      card.className = `card mb-3 border-${status.color} shadow-sm`;

      card.innerHTML = `
      <div class="card-body">
        <h6 class="card-title text-${status.color}">مرحله ${step}</h6>
        <p class="mb-1">
          <strong>${item.nickname || role}</strong>
          <span class="badge bg-${status.color} text-light">${
        status.text
      }</span>
        </p>
        ${actionHtml}
      </div>
    `;

      historyContainer.appendChild(card);
    });
  },
  processHistoryDirection(e) {
    e.preventDefault();
    window.location.href =
      window.location.origin +
      `/panel/processdate?equipment_id=${e.target.dataset.equipmentId}`;
  },
};

// ======================
// UTILITIES
// ======================
const Utils = {
  removeTableRow(equipmentId) {
    const trElement = document.querySelector(
      'tr[data-equipment-id="' + equipmentId + '"]'
    );
    if (trElement) trElement.remove();
  },

  createButton(text, classes, name, onClick) {
    const btnClasses = ["btn", "mt-3", "flex-fill", "mx-1"];
    const btn = document.createElement("button");

    btn.textContent = text;
    btn.classList.add(...btnClasses, ...classes);
    btn.type = "button";
    btn.dataset.name = name;
    btn.addEventListener("click", onClick);
    return btn;
  },
};
// ======================
// INITIALIZATION
// ======================
document.addEventListener("DOMContentLoaded", () => {
  Workflow.init();
  const dateElements = document.querySelectorAll(".dateTime");
  dateElements.forEach((el) => {
    const dateTimeObj = new Date(el.textContent.trim());
    if (!isNaN(dateTimeObj)) {
      el.textContent = dateTimeObj.toLocaleDateString("fa-IR", {
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
        hour12: false,
      });
    }
  });
});
