jQuery(document).ready(function ($) {
  // Caching jQuery selectors for performance
  const childrenF = $("option.childrenF");
  const childrenM = $("option.childrenM");
  const adulteFieldMale = $("option.adulte-male");
  const adulteFieldFemale = $("option.adulte-female");
  const agefield = $("#age");
  const field = $("#field");
  const selectedGenderInput = $("input[type='radio'][name='gender']:checked");
  
  // Constants for gender values
  const GENDER_MALE = 'مرد';
  const GENDER_FEMALE = 'زن';

  // Event handler for gender radio button change
  $("input.radio").change(function () {
      // Enable the age field when gender is selected
      agefield.prop("disabled", false);
      // Deselect all options
      $('option').prop('selected', false);
      const gender = selectedGenderInput.val();

      // Hide options based on selected gender
      if (gender === GENDER_FEMALE) {
          $('option.shar').not('.shahrFemale').hide();
      }
  });

  // Event handler for age dropdown change
  $("#age").change(function () {
      const gender = selectedGenderInput.val();
      // Enable the field dropdown when age is selected
      field.prop("disabled", false);

      // Show or hide options based on age and gender selection
      if (this.selectedIndex === 1) { // Child
          if (gender === GENDER_MALE) {
              childrenM.prop("hidden", false);
              childrenF.prop("hidden", true);
              adulteFieldMale.prop("hidden", true);
              adulteFieldFemale.prop("hidden", true);
              $('option.shar').not('.child').hide();
          } else if (gender === GENDER_FEMALE) {
              childrenF.prop("hidden", false);
              childrenM.prop("hidden", true);
              adulteFieldMale.prop("hidden", true);
              adulteFieldFemale.prop("hidden", true);
              $('option.shar').not('.child').hide();
          }
      } else if (this.selectedIndex === 2) { // Adult
          if (gender === GENDER_FEMALE) {
              adulteFieldMale.prop("hidden", true);
              childrenF.prop("hidden", true);
              childrenM.prop("hidden", true);
              adulteFieldFemale.prop("hidden", false);
              $('option.shar').filter('.shahrFemale').show();
          } else if (gender === GENDER_MALE) {
              adulteFieldMale.prop("hidden", false);
              childrenF.prop("hidden", true);
              childrenM.prop("hidden", true);
              adulteFieldFemale.prop("hidden", true);
              $('option.shar').show();
          }
      }
  });

  // Event handler for mouseover on the form display section
  $('.show-form').on('mouseover', function () {
      // Collecting form data
      const fullname = $('#name').val() + ' ' + $('#family').val();
      const email = $('#email').val();
      const phone = $('#phone').val();
      const shar = $('#shahr').val();
      const address = shar + '_' + $('#address').val();
      const fieldValue = $('#age').val() + '-' + $('#field').val();
      const selectedGender = selectedGenderInput.val();

      // Displaying collected data
      $('#item1').empty().append(`<span>${fullname}</span>`);
      $('#item2').empty().append(`<span>${email}</span>`);
      $('#item3').empty().append(`<span>${phone}</span>`);
      $('#item4').empty().append(`<span>${address}</span>`);
      $('#item5').empty().append(`<span>${selectedGender}</span>`);
      $('#item6').empty().append(`<span>${fieldValue}</span>`);
  });
});