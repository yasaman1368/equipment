jQuery(document).ready(function ($) {
    const $name = $('#name'),
          $family = $('#family'),
          $address = $('#address'),
          $gender = $('#gender'),
          $city = $('#city'),
          $age = $('#age'),
           $date = $('#date'),
          $field = $('#categories'),
          $nationalNumInput = $('#national_num'),
          $phoneInput = $('#phone'),
          ajaxUrl = $('input[name=ajax-url]').val(),
          nonce = $('input[name=nonce]').val();

    // Add phone input event listener once
    $phoneInput.on('input', () => {
        $phoneInput.val(convertNumberToEnglish($phoneInput.val().trim()));
    });

    $nationalNumInput.on('input', () => {
        $nationalNumInput.val(convertNumberToEnglish($nationalNumInput.val().trim()));
    });

    function convertNumberToEnglish(number) {
        return number.replace(/[۰-۹]/g, (match) => {
            return String.fromCharCode(match.charCodeAt(0) - 1728);
        });
    }

    $('#submit-btn').on('click', function (e) {
        e.preventDefault();
        // Collect form data
        const name = $name.val(),
              family = $family.val(),
              address = $address.val(),
              city = $city.val(),
              age = $age.val(),
              gender = $gender.val(),
              field = $field.val(),
               date = $date.val(),
              national_num = $nationalNumInput.val(),
              phone = $phoneInput.val();
             

        // AJAX request
        $.ajax({
            type: "POST",
            url: ajaxUrl,
            data: {
                action: 'fajr_add_member',
                nonce: nonce,
                name: name,
                family: family,
                national_num: national_num,
                phone: phone,
                address: address,
                city: city,
                gender: gender,
                date:date,
                age: age,
                field: field
            },
        beforeSend: function () {
                // Optionally show a loading spinner or disable the button
            },
            success: function (response) {
            if (!response.success) {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطا!',
                    text: response.msg || 'مشکلی پیش آمد، لطفا دوباره تلاش کنید.',
                    });
                    return false;
                }
                Swal.fire({
                    icon: 'success',
                    title: 'موفق!',
                    text: 'ثبت نام با موفقیت انجام شد',
                });
            },
            error: function (error) {
            const errorMsg = error.responseJSON && error.responseJSON.msg ? error.responseJSON.msg : 'مشکلی پیش آمد، لطفا دوباره تلاش کنید.';
                    Swal.fire({
                        icon: "error",
                        title: "ثبت نام شما ناموفق بود",
                text: errorMsg,
                    });
            },
            complete: function () {
                // Optionally, reset the form or redirect the user
                // $('#contact_form').trigger("reset"); // Reset the form if needed
            }
        });
        });
    });