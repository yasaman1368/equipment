
function showModal() {
    const myModal = new bootstrap.Modal(document.getElementById('userManagementModal'));
    myModal.show();
}

function hideModal() {
    const myModalEl = document.getElementById('userManagementModal');
    const modalInstance = bootstrap.Modal.getInstance(myModalEl) || new bootstrap.Modal(myModalEl);
    modalInstance.hide();
}
document.addEventListener('DOMContentLoaded', function () {
    // Get user list on page load
    get_users_list();

  // Fetch users from search
  document.getElementById('searchUserButton').addEventListener('click', function () {
    const searchTerm = document.getElementById('searchUser').value;
    fetchUsers(searchTerm);
});

// Function to fetch users based on search term and page number
function fetchUsers(searchTerm = '', page = 1) {
    const params = new URLSearchParams({
        term: searchTerm,
        paged: page,
    });

    fetch(`/wp-admin/admin-ajax.php?action=search_users`, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
        },
        body: params,
    })
        .then(response => response.json())
        .then(data => {
            const usersList = document.getElementById('usersList');
            if (data.success) {
                usersList.innerHTML = data.data; // Update the user list

                // Add event listeners to pagination links for search results
                const paginationLinks = document.querySelectorAll('#usersList .page-link');
                paginationLinks.forEach(link => {
                    link.addEventListener('click', function (e) {
                        e.preventDefault(); // Prevent default link behavior
                        const page = this.parentElement.getAttribute('data-pagination'); // Get the page number
                        fetchUsers(searchTerm, page); // Fetch the user list for the selected page
                    });
                });
            } else {
                console.error('Failed to fetch search results');
            }
        })
        .catch(error => {
            console.error('Error fetching search results:', error);
        });
}

    const form = document.getElementById('addUserForm');
    const messageDiv = document.getElementById('message');
    const usersListDiv = document.getElementById('usersList');
const updateUserForm=document.getElementById('update-user');
    // Fetch user list with pagination
    function get_users_list(paged = 1) {
        let body = {
            paged: paged
        };
        const params = new URLSearchParams(body);
        fetch('/wp-admin/admin-ajax.php?action=get_users_list', {  
            method: 'POST',
            headers: {
                'Accept': 'application/json',
            },
            body: params
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    usersListDiv.innerHTML = data.data; // Update the user list

                    // Add event listeners to pagination links
                    const paginationLinks = document.querySelectorAll('li.page-item');
                    paginationLinks.forEach(link => {
                        link.addEventListener('click', function (e) {
                            e.preventDefault(); // Prevent default link behavior
                            const paged = this.getAttribute('data-pagination'); // Get the page number
                            get_users_list(paged); // Fetch the user list for the selected page
                        });
                    });
                    // Add event listeners to gear icons
                    const gearIcons = document.querySelectorAll('.bi-gear');
                    gearIcons.forEach(icon => {
                        icon.addEventListener('click', function () {
                            const userId = this.getAttribute('data-user-id');
                            const userManagementModalDiv=document.getElementById('userManagementModalDiv')
                            const navLinks = userManagementModalDiv.querySelectorAll('button.nav-link');
                            const tabPanes = userManagementModalDiv.querySelectorAll('div.tab-pane.fade');
                            const deleteUserTab = document.getElementById('delete-user-tab');
                            const deleteUser = document.getElementById('delete-user');
                            // Remove 'active' class from all nav links
                            navLinks.forEach(element => {
                                element.classList.remove('active');
                            });
                            // Add 'active' class to the delete user tab
                            deleteUserTab.classList.add('active');
                            // Remove 'show' and 'active' classes from all tab panes
                            tabPanes.forEach(element => {
                                element.classList.remove('show', 'active');
                            });
                            // Add 'active' and 'show' classes to the delete user tab pane
                            deleteUser.classList.add('active', 'show');
                            fetchUserDetails(userId); // Fetch user details and open modal
                        });
                    });
                } else {
                    console.error('Failed to fetch updated user list');
                }
            })
            .catch(error => {
                console.error('Error fetching updated user list:', error);
            });
    }

    // Function to fetch user details
    function fetchUserDetails(userId) {
        fetch(`/wp-admin/admin-ajax.php?action=get_user_details&user_id=${userId}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
            },
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Populate modal with user details
                    populateModal(data.data);
                    // Open the modal
                   showModal();
                } else {
                    console.error('Failed to fetch user details');
                }
            })
            .catch(error => {
                console.error('Error fetching user details:', error);
            });
    }

    // Function to populate modal with user details
    function populateModal(user) {
        // Populate the modal fields with user details
        document.getElementById('username-modal').value = user.user_login;
        document.getElementById('display-name-modal').value = user.display_name;
        document.getElementById('email-modal').placeholder = user.user_email||'';
        document.querySelector('input[name=user-id]').value=user.user_id;
        document.getElementById('role-selector').innerHTML=user.html_selector
        const removeUserBtn=document.getElementById('remove-user-modal');
        removeUserBtn.setAttribute('data-user-id',user.user_id);
        document.querySelectorAll('.display-name-modal').forEach(element=>
            element.textContent = user.display_name
        )


        // add remove user btn event listener
        removeUserBtn.addEventListener('click',function(){
            userForRemoveId=this.getAttribute('data-user-id');

            fetch(`/wp-admin/admin-ajax.php?action=remove_user`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                },
                body:new URLSearchParams({
                    'user_id':userForRemoveId
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                      
                        // Fetch the updated user list
                        get_users_list();
                        const Toast = Swal.mixin({
                            toast: true,
                            position: "bottom-start",
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                              toast.onmouseenter = Swal.stopTimer;
                              toast.onmouseleave = Swal.resumeTimer;
                            }
                          });
                          Toast.fire({
                            icon: "success",
                            title:data.data.message
                          });
                          
                        } else {
                        
                          const Toast = Swal.mixin({
                            toast: true,
                            position: "bottom-start",
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                              toast.onmouseenter = Swal.stopTimer;
                              toast.onmouseleave = Swal.resumeTimer;
                            }
                          });
                          Toast.fire({
                            icon: "error",
                            title:data.data.message
                          });
                        }
                        hideModal();
                })
                .catch(error => {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: "bottom-start",
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                          toast.onmouseenter = Swal.stopTimer;
                          toast.onmouseleave = Swal.resumeTimer;
                        }
                      });
                      Toast.fire({
                        icon: "error",
                        title: 'خطا در ارسال درخواست.'
                      });
                });

        })
    }

    // Handle form submission for adding a new user
    form.addEventListener('submit', function (event) {
        event.preventDefault(); // Prevent default form submission

        // Get form data
        const formData = new FormData(form);

        // Send AJAX request to add a new user
        fetch('/wp-admin/admin-ajax.php?action=add_new_user', {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json',
            },
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: "bottom-start",
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                          toast.onmouseenter = Swal.stopTimer;
                          toast.onmouseleave = Swal.resumeTimer;
                        }
                      });
                      Toast.fire({
                        icon: "success",
                        title: data.data
                      });

                    form.reset(); // Reset the form

                    // Fetch the updated user list
                    get_users_list();
                } else {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: "bottom-start",
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                          toast.onmouseenter = Swal.stopTimer;
                          toast.onmouseleave = Swal.resumeTimer;
                        }
                      });
                      Toast.fire({
                        icon: "error",
                        title: data.data
                      });
                }
            })
            .catch(error => {
                const Toast = Swal.mixin({
                    toast: true,
                    position: "bottom-start",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                      toast.onmouseenter = Swal.stopTimer;
                      toast.onmouseleave = Swal.resumeTimer;
                    }
                  });
                  Toast.fire({
                    icon: "error",
                    title: 'خطا در ارسال درخواست.'
                  });
            });
    });
    updateUserForm.addEventListener('submit', function (event) {
        event.preventDefault(); // Prevent default form submission

        // Get form data
        const formData = new FormData(updateUserForm);
        requestUpdateUser(formData);
        // Send AJAX request to add a new user
        
    });
    const accountSettingForm= document.getElementById('account-setting-form');
    accountSettingForm.addEventListener('submit',function(event){
        event.preventDefault();
        //Get form data
        const formData= new FormData(accountSettingForm);
        requestUpdateUser(formData);
    })


    function requestUpdateUser(formData){
        fetch('/wp-admin/admin-ajax.php?action=update_user', {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json',
            },
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    
                    const Toast = Swal.mixin({
                        toast: true,
                        position: "bottom-start",
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                          toast.onmouseenter = Swal.stopTimer;
                          toast.onmouseleave = Swal.resumeTimer;
                        }
                      });
                      Toast.fire({
                        icon: "success",
                        title: data.data
                      });

                    // Fetch the updated user list
                    get_users_list();
                    updateUserForm.reset();
                } else {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: "bottom-start",
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                          toast.onmouseenter = Swal.stopTimer;
                          toast.onmouseleave = Swal.resumeTimer;
                        }
                      });
                      Toast.fire({
                        icon: "error",
                        title: data.data
                      });
                    }
                    hideModal();
                })
                .catch(error => {
                const Toast = Swal.mixin({
                    toast: true,
                    position: "bottom-start",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                      toast.onmouseenter = Swal.stopTimer;
                      toast.onmouseleave = Swal.resumeTimer;
                    }
                  });
                  Toast.fire({
                    icon: "error",
                    title: 'خطا در ارسال درخواست.'
                  });
        
            });
    }
});