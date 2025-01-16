jQuery(document).ready(function ($) {
    const $emailInput = $('.email');
    const $userPassInput = $('#user_pass');
    const $rememberMeInput = $('input[name=rememberme]');
    const $ajaxUrlInput = $('input[name=ajax-url]');
    const $nonceInput = $('input[name=ajax-nonce]');
    const ajaxurl = $ajaxUrlInput.val();
    const nonce = $nonceInput.val();

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

    $('#loginform').submit(function (e) {
        e.preventDefault();
        
        const email = $emailInput.val();
        const userPass = $userPassInput.val();
        const rememberMe = $rememberMeInput.prop('checked');

    $.ajax({
        type: "post",
        url: ajaxurl,
        data: {
                action: 'math_login',
                email: email,
                userPass: userPass,
                rememberMe: rememberMe,
                nonce: nonce
        },
        dataType: "json",
        success: function (response) {
                if (response.success) {
                Toast.fire({
                        icon: 'success',
                        title: response.msg
                    });
                    window.location.href = `${window.location.origin}/panel`;
            }
        },
            error: function (error) {
                if (error.responseJSON.error) {
                Toast.fire({
                    icon: 'error',
                        title: error.responseJSON.msg
                    });
                }
            }
    });
   });
//    validation  phone
$('#phoneSend').on('keyup', function (e) {  
    var phoneRegex = /^(\+98|0|98)?9\d{9}$/; // Regex for Iranian phone numbers
    var $phoneInput = $(this); // Store jQuery object  
    var phone = $phoneInput.val();  
    var $submitButton = $('#regPhone button[type=submit]');
    
    if (phone === '') {  
        $phoneInput.removeClass('invalid').addClass('validate');  
        $submitButton.prop('disabled', true);       
        return; 
    }  
    
    if (!phoneRegex.test(phone)) {
        $phoneInput.removeClass('validate').addClass('invalid');  
    } else {  
        $submitButton.prop('disabled', false);       
        $phoneInput.removeClass('invalid').addClass('validate');  
    }
  });
  
   $('#regPhone').on('submit', function(e) {
    e.preventDefault(); // Prevent the default form submission
    var phone = $('#phoneSend').val();
   

    // If valid, proceed with AJAX
    var phoneData = {
      phone: phone,
      nonce: nonce
    };

    // Perform the AJAX request
    $.ajax({
      url: ajaxurl,
      dataType: "json",
      type: 'POST',
      data: {
        action: 'math_send_code',
        data: phoneData // Send the phone number
      },
      success: function (response) {
        if (response.success) {
        Toast.fire({
                icon: 'success',
                title: response.message
            });
            $('#regPhone').css('display','none');
            $('#verify-code').css('display','block');
    }
},
    error: function (error) {
        if (error.responseJSON.error) {
        Toast.fire({
            icon: 'error',
                title: error.responseJSON.message
            });
    }}
    });
  });
$('#verify-code').submit(function (e) { 
  e.preventDefault();
  var verifyCode = $('#inputVarifyCode').val();
   

    
    var phoneData = {
      verifyCode: verifyCode,
      nonce: nonce
    };

    $.ajax({
      url: ajaxurl,
      dataType: "json",
      type: 'POST',
      data: {
        action: 'math_verify_verification_code',
        data: phoneData 
      },
      success: function (response) {
     
        
        $('#verify-code').css('display','none');
        $('#registrationform').css('display','block');
      
      },
      error: function (error) {
   
        if (error.responseJSON.error) {
        Toast.fire({
            icon: 'error',
                title: error.responseJSON.message
            });
    }}
    });

  
});  


    $('body').on('submit','#registrationform', function(e) {
      e.preventDefault(); // Prevent the default form submission
      var formData = $(this).serialize();
      formData +='&nonce='+nonce
      // Gather the form data
      // var formData = {
      //   last_name: $('#last_name').val(),
      //   first_name: $('#first_name').val(),
      //   email: $('#email-reg').val(),
      //   password: $('#password').val(),
      //   nonce: nonce 
      // };

      // Perform the AJAX request
      $.ajax({
        url: ajaxurl,
        type: 'POST',
        data:formData+'&action=math_registration' // Send the form data
        ,
        success: function (response) {
          if (response.success) {
          Toast.fire({
                  icon: 'success',
                  title: response.message
              });
              window.location.href = `${window.location.origin}/panel`;
      }
  },
      error: function (error) {
          if (error.responseJSON.error) {
          Toast.fire({
              icon: 'error',
                  title: error.responseJSON.message
              });
          }
      }
      });
    });
    $('#recover-password-form').submit(function (e) { 
      e.preventDefault();
      let email = $('#recover-email').val();
      var formData = {
        action: 'recover_password',
        email: email,
        nonce: nonce
      };  
      $.ajax({
        type: "post",
        url: ajaxurl,
        data: formData,
        dataType: "json",
        success: function (response) {
           if (response.success) {
          Toast.fire({
                  icon: 'success',
                  title: response.message
              });
          
        }},
        error: function (error) {
     
          if (error.responseJSON.error) {
          Toast.fire({
              icon: 'error',
                  title: error.responseJSON.message
              });
      }}
      });
      
    });
    $('#set-recover-password-form').submit(function (e) { 
      e.preventDefault();
      let pass = $('#user_pass').val();
      let repeadPass = $('#re_user_pass').val();
      let token=$('input[name=token]').val();
      var formData = {
        action: 'set_recover_password',
        pass:pass,
        repeadPass:repeadPass,
        nonce: nonce,
        token:token
      };  
      $.ajax({
        type: "post",
        url: ajaxurl,
        data: formData,
        dataType: "json",
        success: function (response) {
           if (response.success) {
          Toast.fire({
                  icon: 'success',
                  title: response.message
              });
              window.location=window.location.origin+'/panel/login'
          
        }},
        error: function (error) {
     
          if (error.responseJSON.error) {
          Toast.fire({
              icon: 'error',
                  title: error.responseJSON.message
              });
      }}
      });
      
    });
   
    $('div.input-field').click(function (e) { 
        e.preventDefault();
$(this).find('input').focus();   
    });
  });
