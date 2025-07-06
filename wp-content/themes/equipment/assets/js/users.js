// ======================
// MODAL UTILITIES
// ======================
const ModalUtils={
    show:(modalId)=>{
        const modal=new bootstrap.Modal(document.getElementById(modalId));
        modal.show();
    },
    hide:(modalId)=>{
        const modalEl=document.getElementById(modalId);
        const modalInstance=modalEl.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modalInstance.hide();
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
    }
  }),

  show: (type, message) => {
    Notification.toast.fire({
      icon: type,
      title: message
    });
  },

  confirm: (options) => {
    return Swal.fire({
      title: options.title || 'Are you sure?',
      text: options.text || '',
      icon: options.icon || 'warning',
      showCancelButton: true,
      confirmButtonText: options.confirmText || 'Yes',
      cancelButtonText: options.cancelText || 'Cancel'
    });
  }
};

// ======================
// API SERVICE
// ======================
const ApiService = {
  request: async (method, action, { params = {}, data } = {}) => {
    const url = new URL('/wp-admin/admin-ajax.php', window.location.origin);
    url.searchParams.append('action', action);


    if (method === 'GET' && params) {
      Object.entries(params).forEach(([key, value]) => {
        url.searchParams.append(key, value);
      });
    }

    let response;
    try {
      response = await fetch(url, {
        method,
        headers: {
          'Accept': 'application/json',
          ...(method === 'POST' && !(data instanceof FormData)
            ? { 'Content-Type': 'application/x-www-form-urlencoded' }
            : {}),
        },
        ...(method === 'POST' ? {
          body: data instanceof FormData ? data : new URLSearchParams(data)
        } : {})
      });
    } catch (error) {
      console.error(`Error in ${method} ${action}:`, error);
      throw error;
    }

    if (!response.ok) {
      const text = await response.text();
      throw new Error(`Request failed with status ${response.status}: ${text}`);
    }

    return await response.json();
  },

  get(action, params = {}) {
    return this.request('GET', action, { params });
  },

  post(action, data = {}) {
    return this.request('POST', action, { data });
  }
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
    document.getElementById('searchUserButton').addEventListener('click', () => {
      const searchTerm = document.getElementById('searchUser').value;
      UserManager.fetchUsers(searchTerm);
    });

    // Add user form
    document.getElementById('addUserForm').addEventListener('submit', UserManager.handleAddUser);

    // Update user form
    document.getElementById('update-user').addEventListener('submit', UserManager.handleUpdateUser);

    // Account settings form
    document.getElementById('account-setting-form').addEventListener('submit', UserManager.handleUpdateUser);
  },

  getUsersList: async (page = 1) => {
    try {
      const data = await ApiService.post('get_users_list', { paged: page });
      
      if (!data.success) {
        throw new Error('Failed to fetch user list');
      }

      document.getElementById('usersList').innerHTML = data.data;
      UserManager.bindPaginationEvents();
      UserManager.bindGearIconEvents();
    } catch (error) {
      console.error('Error getting user list:', error);
      Notification.show('error', 'Failed to load user list');
    }
  },

  fetchUsers: async (searchTerm = '', page = 1) => {
    try {
      const data = await ApiService.post('search_users', {
        term: searchTerm,
        paged: page
      });

      if (!data.success) {
        throw new Error('Failed to fetch search results');
      }

      document.getElementById('usersList').innerHTML = data.data;
      UserManager.bindPaginationEvents(searchTerm);
    } catch (error) {
      console.error('Error fetching users:', error);
      Notification.show('error', 'Failed to search users');
    }
  },

  bindPaginationEvents: (searchTerm = '') => {
    const paginationLinks = document.querySelectorAll('.page-link, .page-item');
    
    paginationLinks.forEach(link => {
      link.addEventListener('click', (e) => {
        e.preventDefault();
        const page = link.getAttribute('data-pagination') || 
                    link.parentElement.getAttribute('data-pagination');
        
        if (searchTerm) {
          UserManager.fetchUsers(searchTerm, page);
        } else {
          UserManager.getUsersList(page);
        }
      });
    });
  },

  bindGearIconEvents: () => {
    document.querySelectorAll('.bi-gear').forEach(icon => {
      icon.addEventListener('click', () => {
        const userId = icon.getAttribute('data-user-id');
        UserManager.prepareUserModal(userId);
      });
    });
  },

  prepareUserModal: (userId) => {
    const modalDiv = document.getElementById('userManagementModalDiv');
    const navLinks = modalDiv.querySelectorAll('button.nav-link');
    const tabPanes = modalDiv.querySelectorAll('div.tab-pane.fade');
    const deleteUserTab = document.getElementById('delete-user-tab');
    const deleteUser = document.getElementById('delete-user');

    navLinks.forEach(element => element.classList.remove('active'));
    tabPanes.forEach(element => element.classList.remove('show', 'active'));
    
    deleteUserTab.classList.add('active');
    deleteUser.classList.add('active', 'show');
    
    UserManager.fetchUserDetails(userId);
  },

  fetchUserDetails: async (userId) => {
    try {
      const data = await ApiService.get('get_user_details',{user_id:userId});
      
      if (!data.success) {
        throw new Error('Failed to fetch user details');
      }

      UserManager.populateModal(data.data);
      ModalUtils.show('userManagementModal');
    } catch (error) {
      console.error('Error fetching user details:', error);
      Notification.show('error', 'Failed to load user details');
    }
  },

  populateModal: (user) => {
    
    // Populate user details
    document.getElementById('username-modal').value = user.user_login;
    document.getElementById('display-name-modal').value = user.display_name;
    document.getElementById('email-modal').placeholder = user.user_email || '';
    document.getElementById('user-id-holder').value = user.user_id;
    document.getElementById('role-selector').innerHTML = user.html_selector;
    
    
    // Update display names
    document.querySelectorAll('.display-name-modal').forEach(element => {
      element.textContent = user.display_name;
    });

    // Set up remove button
    const removeUserBtn = document.getElementById('remove-user-modal');
    removeUserBtn.setAttribute('data-user-id', user.user_id);
    removeUserBtn.onclick = UserManager.handleRemoveUser;
  },

  handleRemoveUser: async function() {
    const userId = this.getAttribute('data-user-id');
    
    try {
      const result = await Notification.confirm({
        title: 'ایا مطمئنید ?',
        text: 'ایا مطمئنید که می‌خواهید کاربر را حذف کنید؟',
        icon: 'warning',
        confirmButtonText: 'حذف',
        cancelButtonText: 'انصراف'
      });

      if (!result.isConfirmed) return;

      const data = await ApiService.post('remove_user', { user_id: userId });
      
      if (data.success) {
        Notification.show('success', data.data.message);
        UserManager.getUsersList();
      } else {
        Notification.show('error', data.data.message);
      }
    } catch (error) {
      console.error('Error removing user:', error);
      Notification.show('error', 'Failed to remove user');
    } finally {
      ModalUtils.hide('userManagementModal');
    }
  },

  handleAddUser: async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);

    try {
      const data = await ApiService.post('add_new_user', formData);
      
      if (data.success) {
        Notification.show('success', data.data);
        e.target.reset();
        LocationManager.resetLocations();
        UserManager.getUsersList();
      } else {
        Notification.show('error', data.data);
      }
    } catch (error) {
      console.error('Error adding user:', error);
      Notification.show('error', 'Failed to add user');
    }
  },

  handleUpdateUser: async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);

    try {
      const data = await ApiService.post('update_user', formData);
      
      if (data.success) {
        Notification.show('success', data.data);
        UserManager.getUsersList();
        e.target.reset();
      } else {
        Notification.show('error', data.data);
      }
    } catch (error) {
      console.error('Error updating user:', error);
      Notification.show('error', 'حذف کاربر ناموفق بود');
    } finally {
      ModalUtils.hide('userManagementModal');
    }
  }
};

// ======================
// LOCATION MANAGEMENT
// ======================

const LocationManager={
  locations: [],
  select2Initialized: false,

  init:()=>{
    LocationManager.bindEvents();
    LocationManager.fetchLocations();
    LocationManager.initSelect2();
  },

 bindEvents: () => {

    document.getElementById('locations-form').addEventListener('submit', LocationManager.handleAddLocation);

    
    document.addEventListener('click', (e) => {
      if (e.target.classList.contains('remove-location')) {
        e.preventDefault();
        LocationManager.handleRemoveLocation(e);
        
      }
      
      if (e.target.classList.contains('addLocationButton')) {
        LocationManager.addLocation();
      }
    });
  },

  initSelect2: () => {
    const modal = document.getElementById('add-location');
    
    modal.addEventListener('shown.bs.modal', () => {
      if (LocationManager.select2Initialized) return;
      
      jQuery('#locationSelector').select2({
        placeholder: "یک موقعیت را انتخاب کنید",
        allowClear: true,
        dropdownParent: jQuery('#add-location'),
        width: '100%',
        minimumInputLength: 0,
        language: {
          noResults: () => "No results found"
        },
        matcher: (params, data) => {
          if (jQuery.trim(params.term) === '') return data;
          if (data.text.toUpperCase().includes(params.term.toUpperCase())) return data;
          return null;
        }
      });

      jQuery('#locationSelector').on('select2:open', () => {
        setTimeout(() => {
          document.querySelector('.select2-search__field').focus();
        }, 0);
      });

      LocationManager.select2Initialized = true;
    });

    modal.addEventListener('hide.bs.modal', () => {
      jQuery('#locationSelector').select2('destroy');
      LocationManager.select2Initialized = false;
    });
  },

   fetchLocations: async () => {
    try {
      const data = await ApiService.get('get_locations');
      
      if (!data.success) {
        throw new Error('Failed to fetch locations');
      }

      const locationsList = document.getElementById('locations-list');
      locationsList.innerHTML = '';

      data.data.locations.forEach((location, index) => {
        const row = document.createElement('tr');
        row.setAttribute('data-index', index);
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
      console.error('Error fetching locations:', error);
      Notification.show('error', 'دریافت موقعیت ها ناموفق است');
    }
  },

  handleAddLocation: async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);

    try {
      const data = await ApiService.post('add_location', formData);
      
      if (data.success) {
        Notification.show('success', data.data.message);
        e.target.reset();
        LocationManager.fetchLocations();
      } else {
        Notification.show('error', data.data.message);
      }
    } catch (error) {
      console.error('Error adding location:', error);
      Notification.show('error', 'موقعیت اضافه نشده است');
    }
  },

  handleRemoveLocation: async (e) => {
    const index = e.target.getAttribute('data-index');
    const location = e.target.getAttribute('data-location');

    try {
      const result = await Notification.confirm({
        title: 'آیا مطمئن هستید؟',
        text: `آیا می‌خواهید موقعیت "${location}" را حذف کنید؟`
      });

      if (!result.isConfirmed) return;

      const nonce = document.querySelector('input[name=add-location-nonce]').value;
      const data = await ApiService.post('remove_location', {
        'add-location-nonce': nonce,
        index: index
      });

      if (data.success) {
        Notification.show('success', data.data.message);
        document.querySelector(`tr[data-index="${index}"]`).remove();
      } else {
        Notification.show('error', data.data.message);
      }
    } catch (error) {
      console.error('Error removing location:', error);
      Notification.show('error', 'حذف موقعیت انجام نشد');
    }
  },

  renderLocations: () => {
    const container = document.getElementById('user-locations');
    container.innerHTML = '';

    if (LocationManager.locations.length === 0) {
       container.innerHTML = '<div class="empty-locations">هنوز موقعیتی انتخاب نشده است</div>';
      return;
    }

    LocationManager.locations.forEach(location => {
      const tag = document.createElement('div');
      tag.className = 'location-tag';
      tag.innerHTML = `
        <span class="close-circle ml-2" onclick="LocationManager.removeSelectedLocation('${location}')">×</span>
        <span>${location}</span>
      `;
      container.appendChild(tag);
    });
  },

  removeSelectedLocation: (location) => {
    const index = LocationManager.locations.indexOf(location);
    if (index !== -1) {
      LocationManager.locations.splice(index, 1);
    }

    document.querySelector('input[name=locations]').value = JSON.stringify(LocationManager.locations);
    LocationManager.renderLocations();
  },

  resetLocations: () => {
    LocationManager.locations = [];
    document.querySelector('input[name=locations]').value = '';
    LocationManager.renderLocations();
  },

  addLocation: () => {
    const selector = document.getElementById('locationSelector');
    const location = selector.value;

    if (!location) {
      Notification.show('error', 'لطفا موقعیت را انتخاب کنید');
      return;
    }

    if (LocationManager.locations.includes(location)) {
      Notification.show('error', 'این موقع قبلا اضافه شده است');
      return;
    }

    LocationManager.locations.push(location);
    document.querySelector('input[name=locations]').value = JSON.stringify(LocationManager.locations);
    LocationManager.renderLocations();
    
    const modal = bootstrap.Modal.getInstance(document.getElementById('add-location'));
    if (modal) modal.hide();
    
    selector.value = '';
  }

}


// ======================
// INITIALIZATION
// ======================
document.addEventListener('DOMContentLoaded', () => {
  UserManager.init();
});


