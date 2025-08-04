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
// USER MANAGEMENT
// ======================
const UserManager = {
  init: () => {
    UserManager.bindEvents();
    UserManager.getUsersList();
    LocationManager.init();
  },

  bindEvents: () => {
    // Search functionality
    document
      .getElementById("searchUserButton")
      .addEventListener("click", () => {
        const searchTerm = document.getElementById("searchUser").value;
        UserManager.fetchUsers(searchTerm);
      });

    // Add user form
    document
      .getElementById("addUserForm")
      .addEventListener("submit", UserManager.handleAddUser);

    // Update user form
    document
      .getElementById("update-user")
      .addEventListener("submit", UserManager.handleUpdateUser);

    // Account settings form
    document
      .getElementById("account-setting-form")
      .addEventListener("submit", UserManager.handleUpdateUser);
  },

  getUsersList: async (page = 1) => {
    try {
      const data = await ApiService.post("get_users_list", { paged: page });

      if (!data.success) {
        throw new Error("Failed to fetch user list");
      }

      document.getElementById("usersList").innerHTML = data.data;
      UserManager.bindPaginationEvents();
      UserManager.bindGearIconEvents();
    } catch (error) {
      console.error("Error getting user list:", error);
      Notification.show("error", "Failed to load user list");
    }
  },

  fetchUsers: async (searchTerm = "", page = 1) => {
    try {
      const data = await ApiService.post("search_users", {
        term: searchTerm,
        paged: page,
      });

      if (!data.success) {
        throw new Error("Failed to fetch search results");
      }

      document.getElementById("usersList").innerHTML = data.data;
      UserManager.bindPaginationEvents(searchTerm);
    } catch (error) {
      console.error("Error fetching users:", error);
      Notification.show("error", "Failed to search users");
    }
  },

  bindPaginationEvents: (searchTerm = "") => {
    const paginationLinks = document.querySelectorAll(".page-link, .page-item");

    paginationLinks.forEach((link) => {
      link.addEventListener("click", (e) => {
        e.preventDefault();
        const page =
          link.getAttribute("data-pagination") ||
          link.parentElement.getAttribute("data-pagination");

        if (searchTerm) {
          UserManager.fetchUsers(searchTerm, page);
        } else {
          UserManager.getUsersList(page);
        }
      });
    });
  },

  bindGearIconEvents: () => {
    document.querySelectorAll(".bi-gear").forEach((icon) => {
      icon.addEventListener("click", () => {
        const userId = icon.getAttribute("data-user-id");
        UserManager.prepareUserModal(userId);
      });
    });
  },

  prepareUserModal: (userId) => {
    const modalDiv = document.getElementById("userManagementModalDiv");
    const navLinks = modalDiv.querySelectorAll("button.nav-link");
    const tabPanes = modalDiv.querySelectorAll("div.tab-pane.fade");
    const deleteUserTab = document.getElementById("delete-user-tab");
    const deleteUser = document.getElementById("delete-user");

    navLinks.forEach((element) => element.classList.remove("active"));
    tabPanes.forEach((element) => element.classList.remove("show", "active"));

    deleteUserTab.classList.add("active");
    deleteUser.classList.add("active", "show");

    UserManager.fetchUserDetails(userId);
  },

  fetchUserDetails: async (userId) => {
    try {
      const data = await ApiService.get("get_user_details", {
        user_id: userId,
      });

      if (!data.success) {
        throw new Error("Failed to fetch user details");
      }

      UserManager.populateModal(data.data);
      ModalUtils.show("userManagementModal");
    } catch (error) {
      console.error("Error fetching user details:", error);
      Notification.show("error", "Failed to load user details");
    }
  },

  populateModal: (user) => {
    LocationManager.renderLocations();
    // Populate user details
    document.getElementById("username-modal").value = user.user_login;
    document.getElementById("display-name-modal").value = user.display_name;
    document.getElementById("email-modal").placeholder = user.user_email || "";
    document.getElementById("user-id-holder").value = user.user_id;
    document.getElementById("role-selector").innerHTML = user.html_selector;
    document
      .getElementById("updateLocationsBtn")
      .setAttribute("data-user-id", user.user_id);
    const currentUserLocations = document.getElementById("currentLocations");

    currentUserLocations.innerHTML = "";
    const locationsArray = user.locations;

    if (locationsArray instanceof Array && locationsArray.length > 0) {
      LocationManager.editingUserlocationArray = locationsArray;

      locationsArray.forEach((location) => {
        const locationHtml = `<div class="location-tag">
              <span class="close-circle ml-2" data-section='editing'  onclick="LocationManager.removeSelectedLocation(event,'${location}')">×</span>
              <span>${location}</span>
            </div>`;
        currentUserLocations.innerHTML += locationHtml;
      });
    } else {
      currentUserLocations.innerHTML =
        '<span class="bg-danger shadow  rounded text-white p-2" >موقعیتی ثبت نشده است</span>';
    }

    // Update display names
    document.querySelectorAll(".display-name-modal").forEach((element) => {
      element.textContent = user.display_name;
    });

    // Set up remove button
    const removeUserBtn = document.getElementById("remove-user-modal");
    removeUserBtn.setAttribute("data-user-id", user.user_id);
    removeUserBtn.onclick = UserManager.handleRemoveUser;
  },

  handleRemoveUser: async function () {
    const userId = this.getAttribute("data-user-id");

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
        UserManager.getUsersList();
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

  handleAddUser: async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);

    try {
      const data = await ApiService.post("add_new_user", formData);

      if (data.success) {
        Notification.show("success", data.data);
        e.target.reset();
        LocationManager.resetLocations();
        UserManager.getUsersList();
      } else {
        Notification.show("error", data.data);
      }
    } catch (error) {
      console.error("Error adding user:", error);
      const message =
        error?.response?.data?.message || "افزودن کاربر با خطا مواجه شد";
      Notification.show("error", message);
    }
  },

  handleUpdateUser: async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);

    try {
      const data = await ApiService.post("update_user", formData);

      if (data.success) {
        Notification.show("success", data.data);
        UserManager.getUsersList();
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
  editingUserId: "",
  locations: [],
  editingUserlocationArray: [],
  select2Initialized: false,

  init: () => {
    LocationManager.bindEvents();
    LocationManager.fetchLocations();
    LocationManager.initSelect2();
    LocationManager.prepareEdingUserLocationsSelector();
  },

  bindEvents: () => {
    document
      .getElementById("locations-form")
      ?.addEventListener("submit", LocationManager.handleAddLocation);

    document
      .getElementById("updateLocationsBtn")
      ?.addEventListener("click", LocationManager.updateEdidetLocations);

    document.addEventListener("click", (e) => {
      if (e.target.classList.contains("remove-location")) {
        e.preventDefault();
        LocationManager.handleRemoveLocation(e);
      }

      if (e.target.classList.contains("addLocationButton")) {
        const selectorId = e.target.getAttribute("data-selector-id");

        LocationManager.addLocation(selectorId);
      }
    });
  },

  initSelect2: () => {
    const modal = document.getElementById("add-location");

    modal.addEventListener("shown.bs.modal", () => {
      if (LocationManager.select2Initialized) return;

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
          if (data.text.toUpperCase().includes(params.term.toUpperCase()))
            return data;
          return null;
        },
      });

      jQuery("#locationSelector").on("select2:open", () => {
        setTimeout(() => {
          document.querySelector(".select2-search__field").focus();
        }, 0);
      });

      LocationManager.select2Initialized = true;
    });

    modal.addEventListener("hide.bs.modal", () => {
      jQuery("#locationSelector").select2("destroy");
      LocationManager.select2Initialized = false;
    });
  },

  fetchLocations: async () => {
    const locationsList = document.getElementById("locations-list");

    try {
      const data = await ApiService.get("get_locations");

      if (!data.success) {
        throw new Error("Failed to fetch locations");
      }

      locationsList.innerHTML = "";

      data.data.locations.forEach((location, index) => {
        const row = document.createElement("tr");
        row.setAttribute("data-index", index);
        row.innerHTML = `
          <td>${location}</td>
          <td>
            <button class="btn btn-danger btn-sm remove-location" 
                    data-index="${index}"
                    data-location="${location}">
              Remove
            </button>
          </td>
        `;
        locationsList.appendChild(row);
      });
    } catch (error) {
      console.error("Error fetching locations:", error);

      messageDiv = document.createElement("div");
      messageDiv.classList.add(
        "bg-danger",
        "fw-bold",
        "p-2",
        "m-2",
        "rounded",
        "text-light",
        "w-100",
        "text-center"
      );
      messageDiv.innerHTML =
        error?.response?.data?.message ||
        "خطایی در دریافت موقعیت ها رخ داده است";
      locationsList?.appendChild(messageDiv);
    }
  },

  handleAddLocation: async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);

    try {
      const data = await ApiService.post("add_location", formData);
      if (data.success) {
        Notification.show("success", data.data.message);
        e.target.reset();
        LocationManager.fetchLocations();
      }
    } catch (error) {
      console.error("Error adding location:", error);
      const message =
        error?.response?.data?.message || "خطایی در افزودن موقعیت رخ داده است";
      Notification.show("error", message);
    }
  },

  handleRemoveLocation: async (e) => {
    const index = e.target.getAttribute("data-index");
    const location = e.target.getAttribute("data-location");

    try {
      const result = await Notification.confirm({
        title: "آیا مطمئن هستید؟",
        text: `آیا می‌خواهید موقعیت "${location}" را حذف کنید؟`,
      });

      if (!result.isConfirmed) return;

      const nonce = document.querySelector(
        "input[name=add-location-nonce]"
      ).value;
      const data = await ApiService.post("remove_location", {
        "add-location-nonce": nonce,
        location: location,
      });

      if (data.success) {
        Notification.show("success", data.data.message);
        document.querySelector(`tr[data-index="${index}"]`).remove();
      }
    } catch (error) {
      console.error("Error adding location:", error);
      const message =
        error?.response?.data?.message || "خطایی در حذف موقعیت رخ داده است";
      Notification.show("error", message);
    }
  },

  //below function use for add new user section
  renderLocations: (selectorId = "editingLocationsSelector") => {
    let container;
    let section;
    let displayPreparedLocations = [];

    if (selectorId === "locationSelector") {
      container = document.getElementById("user-locations");
      container.innerHTML = "";
      section = "creating";

      if (LocationManager.locations.length === 0) {
        container.innerHTML =
          '<div class="empty-locations">هنوز موقعیتی انتخاب نشده است</div>';
        return;
      }

      displayPreparedLocations = LocationManager.locations;
    } else if (selectorId === "editingLocationsSelector") {
      container = document.getElementById("currentLocations");
      container.innerHTML = "";
      section = "editing";

      if (LocationManager.editingUserlocationArray.length === 0) {
        container.innerHTML =
          '<div class="empty-locations">هنوز موقعیتی انتخاب نشده است</div>';
        return;
      }

      displayPreparedLocations = LocationManager.editingUserlocationArray;
    }

    displayPreparedLocations.forEach((location) => {
      const tag = document.createElement("div");
      tag.className = "location-tag";
      tag.innerHTML = `
        <span class="close-circle ml-2" data-section="${section}"  onclick="LocationManager.removeSelectedLocation(event,'${location}')">×</span>
        <span>${location}</span>
      `;
      container.appendChild(tag);
    });
  },

  removeSelectedLocation: (event, location) => {
    const section = event.target.getAttribute("data-section");
    let selectorId = "";

    if (section === "creating") {
      selectorId = "locationSelector";
      const index = LocationManager.locations.indexOf(location);
      if (index !== -1) {
        LocationManager.locations.splice(index, 1);
      }

      console.log(LocationManager.locations);
      document.querySelector("input[name=locations]").value = JSON.stringify(
        LocationManager.locations
      );
    } else if (section === "editing") {
      selectorId = "editingLocationsSelector";
      let index = LocationManager.editingUserlocationArray.indexOf(location);
      if (index !== -1) {
        LocationManager.editingUserlocationArray.splice(index, 1);
      }
    }
    LocationManager.renderLocations(selectorId);
  },

  resetLocations: () => {
    LocationManager.locations = [];
    LocationManager.editingUserlocationArray = [];
    document.querySelector("input[name=locations]").value = "";
    LocationManager.renderLocations();
  },

  addLocation: (selectorId) => {
    const selector = document.getElementById(selectorId);
    const location = selector.value;

    if (!location) {
      Notification.show("error", "لطفا موقعیت را انتخاب کنید");
      return;
    }

    if (selectorId === "locationSelector") {
      if (LocationManager.locations.includes(location)) {
        Notification.show("error", "این موقعیت قبلا اضافه شده است");
        return;
      }
      LocationManager.locations.push(location);
      document.querySelector("input[name=locations]").value = JSON.stringify(
        LocationManager.locations
      );
      LocationManager.renderLocations(selectorId);

      const modal = bootstrap.Modal.getInstance(
        document.getElementById("add-location")
      );
      if (modal) modal.hide();

      selector.value = "";
    }

    if (selectorId === "editingLocationsSelector") {
      if (LocationManager.editingUserlocationArray.includes(location)) {
        Notification.show("error", "این موقعیت قبلا اضافه شده است");
        return;
      }

      LocationManager.editingUserlocationArray.push(location);
      console.log(LocationManager.editingUserlocationArray);
    }

    LocationManager.renderLocations(selectorId);
    selector.value = "";
  },

  //  I want to manage edit user's location section
  prepareEdingUserLocationsSelector: async () => {
    const selector = document.getElementById("editingLocationsSelector");
    try {
      const data = await ApiService.get("get_locations");

      if (!data.success) {
        throw new Error("Failed to fetch locations editing user section");
      }

      selector.innerHTML = "";
      data.data.locations.forEach((location, index) => {
        const option = document.createElement("option");
        option.setAttribute("data-index", index);
        option.textContent = location;
        option.value = location;
        selector.appendChild(option);
      });
    } catch (error) {
      console.error("Error fetching locations:", error);
    } finally {
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
          if (data.text.toUpperCase().includes(params.term.toUpperCase()))
            return data;
          return null;
        },
      });

      jQuery("#editingLocationsSelector").on("select2:open", () => {
        setTimeout(() => {
          document.querySelector(".select2-search__field").focus();
        }, 0);
      });
    }
  },

  updateEdidetLocations: async () => {
    const userId = document
      .getElementById("updateLocationsBtn")
      .getAttribute("data-user-id");
    try {
      const data = await ApiService.post("update_user_locations", {
        locations: JSON.stringify(LocationManager.editingUserlocationArray),
        userId: userId,
      });
console.log(data.success)
      if (data.success) {
        Notification.show("success", data.data.message);
      }
      if (data.success) {
        Notification.show("success", data.data.message);
      }
    } catch (error) {
      console.error("Error adding location:", error);
      const message =
        error?.response?.data?.message || "خطایی در افزودن موقعیت رخ داده است";
      Notification.show("error", message);
    } finally {
      LocationManager.resetLocations();
      ModalUtils.hide("userManagementModal");
    }
  },
};

// ======================
// INITIALIZATION
// ======================
document.addEventListener("DOMContentLoaded", () => {
  UserManager.init();
});
