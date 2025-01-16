jQuery(document).ready(function($) {
    $('.darksoul-form').submit(function(e) {
        e.preventDefault();

        const form = $(this);
        const data = form.serialize();
        const ajaxUrl = $('#ajax-url').data('ajax-url');
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        // Disable the submit button to prevent multiple submissions
        form.find('button[type="submit"]').prop('disabled', true);

        $.ajax({
            type: "POST", // Use the appropriate HTTP method
            url: ajaxUrl, // Replace with your actual URL
            data: data + '&action=contact_us', // Use the serialized data
            dataType: "json", // Replace with the expected data type
            success: function(response) {
                if(response.success){
                    
                    Toast.fire({
                        icon: 'success',
                        title: response.message
                    });
                    window.location=window.location.origin; 
                }
            },
            error: function(response) {
                // Handle error
                  if(response.responseJSON.error){
               
                Toast.fire({
                    icon: 'error',
                    title: response.responseJSON.message
                });
            }
            },
            complete: function() {
                // Re-enable the submit button
                form.find('button[type="submit"]').prop('disabled', false);
               
            }
        });
    });
});