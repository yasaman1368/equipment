// ======================
// MODAL UTILITIES
// ======================
const ModalUtils = {
  /**
   * Shows a Bootstrap modal
   * @param {string} modalId - ID of the modal element
   */
  show(modalId) {
    const modalEl = document.getElementById(modalId);
    if (!modalEl) {
      console.error(`Modal element with ID ${modalId} not found`);
      return;
    }
    
    const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
    modal.show();
  },

  /**
   * Hides a Bootstrap modal
   * @param {string} modalId - ID of the modal element
   */
  hide(modalId) {
    const modalEl = document.getElementById(modalId);
    if (!modalEl) return;

    const modal = bootstrap.Modal.getInstance(modalEl);
    if (modal) {
      modal.hide();
    } else {
      // If no instance exists but we need to hide, create and immediately hide
      new bootstrap.Modal(modalEl).hide();
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

  /**
   * Shows a toast notification
   * @param {string} type - Notification type (success, error, etc.)
   * @param {string} message - Notification message
   */
  show(type, message) {
    this.toast.fire({
      icon: type,
      title: message,
    });
  },

  /**
   * Shows a confirmation dialog
   * @param {Object} options - Configuration options
   * @returns {Promise} - Promise that resolves with the user's choice
   */
  confirm(options = {}) {
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
  /**
   * Makes an API request
   * @param {string} method - HTTP method (GET, POST, etc.)
   * @param {string} action - API action
   * @param {Object} options - Request options
   * @returns {Promise} - Promise that resolves with the response data
   */
  async request(method, action, { params = {}, data } = {}) {
    const url = new URL("/wp-admin/admin-ajax.php", window.location.origin);
    url.searchParams.append("action", action);

    if (method === "GET" && params) {
      Object.entries(params).forEach(([key, value]) => {
        url.searchParams.append(key, value);
      });
    }

    const headers = {
      Accept: "application/json",
      ...(method === "POST" && !(data instanceof FormData)
        ? { "Content-Type": "application/x-www-form-urlencoded" }
        : {}),
    };

    const requestOptions = {
      method,
      headers,
      ...(method === "POST" && {
        body: data instanceof FormData ? data : new URLSearchParams(data),
      }),
    };

    try {
      const response = await fetch(url, requestOptions);

      if (!response.ok) {
        const errorBody = await response.json();
        const error = new Error(`Request failed with status ${response.status}`);
        error.status = response.status;
        error.response = errorBody;
        throw error;
      }

      return await response.json();
    } catch (error) {
      console.error(`Error in ${method} ${action}:`, error);
      throw error;
    }
  },

  get(action, params = {}) {
    return this.request("GET", action, { params });
  },

  post(action, data = {}) {
    return this.request("POST", action, { data });
  },
};

// ======================
// USER MANAGEMENT
// ======================
const UserManager = {
  currentPage: 1,
  searchTerm: "",

  init() {
    this.bindEvents();
    this.getUsersList();
    LocationManager.init();
  },

  bindEvents() {
    // Search functionality
    document.getElementById("searchUserButton").addEventListener("click", () => {
      this.searchTerm = document.getElementById("searchUser").value;
      this.fetchUsers(this.searchTerm);
    });

    // Add user form
    document.getElementById("addUserForm").addEventListener("submit", (e) => 
      this.handleAddUser(e)
    );

    // Update user form
    document.getElementById("update-user").addEventListener("submit", (e) => 
      this.handleUpdateUser(e)
    );

    // Account settings form
    document.getElementById("account-setting-form").addEventListener("submit", (e) => 
      this.handleUpdateUser(e)
    );
  },

  async getUsersList(page = 1) {
    this.currentPage = page;
    try {
      const data = await ApiService.post("get_users_list", { paged: page });

      if (!data.success) {
        throw new Error("Failed to fetch user list");
      }

      document.getElementById("usersList").innerHTML = data.data;
      this.bindPaginationEvents();
      this.bindGearIconEvents();
    } catch (error) {
      console.error("Error getting user list:", error);
      Notification.show("error", "Failed to load user list");
    }
  },

  async fetchUsers(searchTerm = "", page = 1) {
    this.searchTerm = searchTerm;
    this.currentPage = page;
    
    try {
      const data = await ApiService.post("search_users", {
        term: searchTerm,
        paged: page,
      });

      if (!data.success) {
        throw new Error("Failed to fetch search results");
      }

      document.getElementById("usersList").innerHTML = data.data;
      this.bindPaginationEvents(searchTerm);
    } catch (error) {
      console.error("Error fetching users:", error);
      Notification.show("error", "Failed to search users");
    }
  },

  bindPaginationEvents(searchTerm = "") {
    document.querySelectorAll(".page-link, .page-item").forEach((link) => {
      link.addEventListener("click", (e) => {
        e.preventDefault();
        const page = link.dataset.pagination || link.parentElement.dataset.pagination;
        
        if (searchTerm) {
          this.fetchUsers(searchTerm, page);
        } else {
          this.getUsersList(page);
        }
      });
    });
  },

  bindGearIconEvents() {
    document.querySelectorAll(".bi-gear").forEach((icon) => {
      icon.addEventListener("click", () => {
        const userId = icon.dataset.userId;
        this.prepareUserModal(userId);
      });
    });
  },

  prepareUserModal(userId) {
    const modalDiv = document.getElementById("userManagementModalDiv");
    if (!modalDiv) return;

    const navLinks = modalDiv.querySelectorAll("button.nav-link");
    const tabPanes = modalDiv.querySelectorAll("div.tab-pane.fade");
    const deleteUserTab = document.getElementById("delete-user-tab");
    const deleteUser = document.getElementById("delete-user");

    navLinks.forEach((el) => el.classList.remove("active"));
    tabPanes.forEach((el) => el.classList.remove("show", "active"));

    deleteUserTab?.classList.add("active");
    deleteUser?.classList.add("active", "show");

    this.fetchUserDetails(userId);
  },

  async fetchUserDetails(userId) {
    try {
      const data = await ApiService.get("get_user_details", { user_id: userId });

      if (!data.success) {
        throw new Error("Failed to fetch user details");
      }

      this.populateModal(data.data);
      ModalUtils.show("userManagementModal");
    } catch (error) {
      console.error("Error fetching user details:", error);
      Notification.show("error", "Failed to load user details");
    }
  },

  populateModal(user) {
    LocationManager.renderLocations();
    
    // Set basic user info
    document.getElementById("username-modal").value = user.user_login;
    document.getElementById("display-name-modal").value = user.display_name;
    document.getElementById("email-modal").placeholder = user.user_email || "";
    document.getElementById("user-id-holder").value = user.user_id;
    document.getElementById("role-selector").innerHTML = user.html_selector;
    
    // Set locations
    const currentUserLocations = document.getElementById("currentLocations");
    currentUserLocations.innerHTML = "";
    
    if (Array.isArray(user.locations) && user.locations.length > 0) {
      LocationManager.editingUserlocations = [...user.locations];
      user.locations.forEach((location) => {
        currentUserLocations.innerHTML += `
          <div class="location-tag">
            <span class="close-circle ml-2" data-section='editing' onclick="LocationManager.removeSelectedLocation(event,'${location}')">×</span>
            <span>${location}</span>
          </div>`;
      });
    } else {
      currentUserLocations.innerHTML = `
        <span class="bg-danger shadow rounded text-white p-2">
          موقعیتی ثبت نشده است
        </span>`;
    }

    // Update display names
    document.querySelectorAll(".display-name-modal").forEach((el) => {
      el.textContent = user.display_name;
    });

    // Set up remove button
    const removeUserBtn = document.getElementById("remove-user-modal");
    if (removeUserBtn) {
      removeUserBtn.dataset.userId = user.user_id;
      removeUserBtn.onclick = () => this.handleRemoveUser(user.user_id);
    }
  },

  async handleRemoveUser(userId) {
    try {
      const result = await Notification.confirm({
        title: "ایا مطمئنید ?",
        text: "ایا مطمئنید که می‌خواهید کاربر را حذف کنید؟",
        icon: "warning",
        confirmButtonText: "حذف",
        cancelButtonText: "انصراف",
      });

      if (!result.isConfirmed) return;

      const data = await ApiService.post("remove_user", { user_id: userId });

      if (data.success) {
        Notification.show("success", data.data.message);
        this.getUsersList(this.currentPage);
      } else {
        Notification.show("error", data.data.message);
      }
    } catch (error) {
      console.error("Error removing user:", error);
      Notification.show("error", "Failed to remove user");
    } finally {
      ModalUtils.hide("userManagementModal");
    }
  },

  async handleAddUser(e) {
    e.preventDefault();
    const formData = new FormData(e.target);

    try {
      const data = await ApiService.post("add_new_user", formData);

      if (data.success) {
        Notification.show("success", data.data);
        e.target.reset();
        LocationManager.resetLocations();
        this.getUsersList(this.currentPage);
      } else {
        Notification.show("error", data.data);
      }
    } catch (error) {
      console.error("Error adding user:", error);
      const message = error?.response?.data?.message || "افزودن کاربر با خطا مواجه شد";
      Notification.show("error", message);
    }
  },

  async handleUpdateUser(e) {
    e.preventDefault();
    const formData = new FormData(e.target);

    try {
      const data = await ApiService.post("update_user", formData);

      if (data.success) {
        Notification.show("success", data.data);
        this.getUsersList(this.currentPage);
        e.target.reset();
      } else {
        Notification.show("error", data.data);
      }
    } catch (error) {
      console.error("Error updating user:", error);
      Notification.show("error", "حذف کاربر ناموفق بود");
    } finally {
      ModalUtils.hide("userManagementModal");
    }
  },
};

// ======================
// LOCATION MANAGEMENT
// ======================
const LocationManager = {
  locations: [],
  editingUserlocations: [],
  select2Initialized: false,

  init() {
    this.bindEvents();
    this.fetchAvailableLocations();
    this.initSelect2();
    this.prepareEdingUserLocationsSelector();
  },

  bindEvents() {
    document.getElementById("locations-form")?.addEventListener("submit", (e) => 
      this.handleAddLocation(e)
    );

    document.getElementById("updateLocationsBtn")?.addEventListener("click", () => 
      this.updateEdidetLocations()
    );

    document.addEventListener("click", (e) => {
      if (e.target.classList.contains("remove-location")) {
        e.preventDefault();
        this.handleRemoveLocation(e);
      }

      if (e.target.classList.contains("addLocationButton")) {
        const selectorId = e.target.dataset.selectorId;
        this.addLocation(selectorId);
      }
    });
  },

  initSelect2() {
    const modal = document.getElementById("add-location");
    if (!modal) return;

    modal.addEventListener("shown.bs.modal", () => {
      if (this.select2Initialized) return;

      jQuery("#locationSelector").select2({
        placeholder: "یک موقعیت را انتخاب کنید",
        allowClear: true,
        dropdownParent: jQuery("#add-location"),
        width: "100%",
        minimumInputLength: 0,
        language: {
          noResults: () => "No results found",
        },
        matcher: (params, data) => {
          if (jQuery.trim(params.term) === "") return data;
          if (data.text.toUpperCase().includes(params.term.toUpperCase())) return data;
          return null;
        },
      });

      jQuery("#locationSelector").on("select2:open", () => {
        setTimeout(() => {
          document.querySelector(".select2-search__field").focus();
        }, 0);
      });

      this.select2Initialized = true;
    });

    modal.addEventListener("hide.bs.modal", () => {
      jQuery("#locationSelector").select2("destroy");
      this.select2Initialized = false;
    });
  },

  async fetchAvailableLocations() {
    const locationsList = document.getElementById("locations-list");
    if (!locationsList) return;

    try {
      const data = await ApiService.get("get_locations");

      if (!data.success) {
        throw new Error("Failed to fetch locations");
      }

      locationsList.innerHTML = data.data.locations.map((location, index) => `
        <tr data-index="${index}">
          <td>${location}</td>
          <td>
            <button class="btn btn-danger btn-sm remove-location" 
                    data-index="${index}"
                    data-location="${location}">
              Remove
            </button>
          </td>
        </tr>
      `).join("");
    } catch (error) {
      console.error("Error fetching locations:", error);
      
      const messageDiv = document.createElement("div");
      messageDiv.className = "bg-danger fw-bold p-2 m-2 rounded text-light w-100 text-center";
      messageDiv.textContent = error?.response?.data?.message || 
        "خطایی در دریافت موقعیت ها رخ داده است";
      locationsList.appendChild(messageDiv);
    }
  },

  async handleAddLocation(e) {
    e.preventDefault();
    const formData = new FormData(e.target);

    try {
      const data = await ApiService.post("add_location", formData);
      if (data.success) {
        Notification.show("success", data.data.message);
        e.target.reset();
        this.fetchAvailableLocations();
      }
    } catch (error) {
      console.error("Error adding location:", error);
      const message = error?.response?.data?.message || "خطایی در افزودن موقعیت رخ داده است";
      Notification.show("error", message);
    }
  },

  async handleRemoveLocation(e) {
    const index = e.target.dataset.index;
    const location = e.target.dataset.location;

    try {
      const result = await Notification.confirm({
        title: "آیا مطمئن هستید؟",
        text: `آیا می‌خواهید موقعیت "${location}" را حذف کنید؟`,
      });

      if (!result.isConfirmed) return;

      const nonce = document.querySelector("input[name=add-location-nonce]").value;
      const data = await ApiService.post("remove_location", {
        "add-location-nonce": nonce,
        location: location,
      });

      if (data.success) {
        Notification.show("success", data.data.message);
        document.querySelector(`tr[data-index="${index}"]`)?.remove();
      }
    } catch (error) {
      console.error("Error removing location:", error);
      const message = error?.response?.data?.message || "خطایی در حذف موقعیت رخ داده است";
      Notification.show("error", message);
    }
  },

  renderLocations(selectorId = "editingLocationsSelector") {
    const isCreatingMode = selectorId === "locationSelector";
    const containerId = isCreatingMode ? "user-locations" : "currentLocations";
    const container = document.getElementById(containerId);
    if (!container) return;

    const locations = isCreatingMode ? this.locations : this.editingUserlocations;

    if (locations.length === 0) {
      container.innerHTML = `
        <div class="empty-locations">
          ${isCreatingMode ? "هنوز موقعیتی انتخاب نشده است" : "موقعیتی ثبت نشده است"}
        </div>`;
      return;
    }

    container.innerHTML = locations.map(location => `
      <div class="location-tag">
        <span class="close-circle ml-2" data-section="${isCreatingMode ? 'creating' : 'editing'}" 
              onclick="LocationManager.removeSelectedLocation(event,'${location}')">×</span>
        <span>${location}</span>
      </div>
    `).join("");
  },

  removeSelectedLocation(event, location) {
    const section = event.target.dataset.section;
    const isCreatingMode = section === "creating";
    const targetArray = isCreatingMode ? this.locations : this.editingUserlocations;

    this.removeFromArray(targetArray, location);
    
    if (isCreatingMode) {
      document.querySelector("input[name=locations]").value = JSON.stringify(this.locations);
    }
    
    this.renderLocations(isCreatingMode ? "locationSelector" : "editingLocationsSelector");
  },

  resetLocations() {
    this.locations = [];
    this.editingUserlocations = [];
    document.querySelector("input[name=locations]").value = "";
    this.renderLocations();
  },

  addLocation(selectorId) {
    const selector = document.getElementById(selectorId);
    if (!selector) return;

    const location = selector.value;
    if (!location) {
      Notification.show("error", "لطفا موقعیت را انتخاب کنید");
      return;
    }

    const isCreatingMode = selectorId === "locationSelector";
    const targetArray = isCreatingMode ? this.locations : this.editingUserlocations;

    if (targetArray.includes(location)) {
      Notification.show("error", "این موقعیت قبلا اضافه شده است");
      return;
    }

    targetArray.push(location);

    if (isCreatingMode) {
      document.querySelector("input[name=locations]").value = JSON.stringify(this.locations);
      ModalUtils.hide("add-location");
    }

    this.renderLocations(selectorId);
    selector.value = "";
  },

  removeFromArray(array, item) {
    const index = array.indexOf(item);
    if (index !== -1) array.splice(index, 1);
  },

  async prepareEdingUserLocationsSelector() {
    const selector = document.getElementById("editingLocationsSelector");
    if (!selector) return;

    try {
      const data = await ApiService.get("get_locations");

      if (!data.success) {
        throw new Error("Failed to fetch locations editing user section");
      }

      selector.innerHTML = data.data.locations.map((location, index) => `
        <option data-index="${index}" value="${location}">${location}</option>
      `).join("");

      jQuery("#editingLocationsSelector").select2({
        placeholder: "یک موقعیت را انتخاب کنید",
        allowClear: true,
        dropdownParent: jQuery("#userManagementModal"),
        width: "100%",
        minimumInputLength: 0,
        language: {
          noResults: () => "No results found",
        },
        matcher: (params, data) => {
          if (jQuery.trim(params.term) === "") return data;
          if (data.text.toUpperCase().includes(params.term.toUpperCase())) return data;
          return null;
        },
      });

      jQuery("#editingLocationsSelector").on("select2:open", () => {
        setTimeout(() => {
          document.querySelector(".select2-search__field").focus();
        }, 0);
      });
    } catch (error) {
      console.error("Error fetching locations:", error);
    }
  },

  async updateEdidetLocations() {
    const btn = document.getElementById("updateLocationsBtn");
    if (!btn) return;

    const userId = btn.dataset.userId;
    try {
      const data = await ApiService.post("update_user_locations", {
        locations: JSON.stringify(this.editingUserlocations),
        userId: userId,
      });

      if (data.success) {
        Notification.show("success", data.data.message);
        this.resetLocations();
        ModalUtils.hide("userManagementModal");
      }
    } catch (error) {
      console.error("Error updating locations:", error);
      const message = error?.response?.data?.message || "خطایی در افزودن موقعیت رخ داده است";
      Notification.show("error", message);
    }
  },
};

// ======================
// INITIALIZATION
// ======================
document.addEventListener("DOMContentLoaded", () => {
  UserManager.init();
});