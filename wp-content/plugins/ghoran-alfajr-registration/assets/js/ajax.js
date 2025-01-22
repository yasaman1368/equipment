jQuery(document).ready(function ($) {
    const $name = $('#name');
    const $family = $('#family');
    const $address = $('#address');
    const $city = $('#shahr');
    const $date = $('#date');
    const $age = $('#age');
    const $field = $('#field');
    const $nationalNumInput = $('#national_num');
    const $phoneInput = $('#phone');
    const ajaxUrl = $('input[name=ajax-url]').val();
    const nonce = $('input[name=nonce]').val();

    // Add phone input event listener once
    $phoneInput.on('input', () => {
        const phoneNumber = $phoneInput.val().trim();
        $phoneInput.val(convertNumberToEnglish(phoneNumber));
    });
    $nationalNumInput.on('input', () => {
        const nationalNum = $nationalNumInput.val().trim();
        $nationalNumInput.val(convertNumberToEnglish(nationalNum ));
    });

    function convertNumberToEnglish(number) {
        const persianNumbers = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        const englishNumbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        let convertedNumber = number;

        for (let i = 0; i < persianNumbers.length; i++) {
            convertedNumber = convertedNumber.split(persianNumbers[i]).join(englishNumbers[i]);
        }
        return convertedNumber;
    }

    $('#submit-btn').on('click', function (e) {
        e.preventDefault();
        
        // Collect form data
        const name = $name.val();
        const family = $family.val();
        const address = $address.val();
        const city = $city.val();
        const date = $date.val();
        const age = $age.val();
        const field = $field.val();
        const gender = document.querySelector("input[name='gender']:checked") ? 
            document.querySelector("input[name='gender']:checked").value : '';
        const national_num = $nationalNumInput.val();
        const phone = $phoneInput.val(); // Corrected variable scope

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
                date: date,
                gender: gender,
                age: age,
                field: field
            },
            before: function () {
                // Optionally show a loading spinner or disable the button
            },
            success: function (response) {
                let responsedata = response; // No need to parse as we expect JSON
                if (!responsedata.success) {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطا!',
                        text: responsedata.msg || 'مشکلی پیش آمد، لطفا دوباره تلاش کنید.',
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
                if (error.responseJSON && error.responseJSON.error) {
                    Swal.fire({
                        icon: "error",
                        title: "ثبت نام شما ناموفق بود",
                        text: error.responseJSON.msg || 'مشکلی پیش آمد، لطفا دوباره تلاش کنید.',
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "خطا",
                        text: 'مشکلی پیش آمد، لطفا دوباره تلاش کنید.',
                    });
                }
            },
            complete: function () {
                // Optionally, reset the form or redirect the user
                 $('#contact_form').trigger("reset"); // Reset the form if needed
            }
        });
    });
});