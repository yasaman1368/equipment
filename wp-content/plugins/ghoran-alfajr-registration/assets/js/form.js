
      jQuery(document).ready(function ($) {
    
        let childrenF = document.querySelectorAll("option.childrenF");
        let childrenM = document.querySelectorAll("option.childrenM");
        let adulteFieldMale = document.querySelectorAll("option.adulte-male");
        let adulteFieldFemale = document.querySelectorAll(
          "option.adulte-female"
          );
          
        let agefield = $("#age");
            //gender
        $("input.radio").change(function (e) {
          e.preventDefault();
          agefield.removeAttr("disabled");
          $('option').removeAttr('selected') 
          // shahr
          let gender = $("input[type='radio'][name='gender']:checked").val();
          if(gender==='زن'){
            $('option.shar').not('.shahrFemale').hide()
          }                 
            });
            //age
            $("#age").change(function (e) {
              e.preventDefault();
              let gender = $("input[type='radio'][name='gender']:checked").val();
             
          $("#field").removeAttr("disabled");

          if (this.selectedIndex === 1 && gender === "مرد") {
            $(childrenM).removeAttr("hidden");
            $(childrenF).attr("hidden", "true");
            $(adulteFieldFemale).attr("hidden", "true");
            $(adulteFieldMale).attr("hidden", "true");
            $('option.shar').not('.child').hide()
          } 
          if (this.selectedIndex === 1 && gender === "زن") {
            $(childrenF).removeAttr("hidden");
            $(childrenM).attr("hidden", "true");
            $(adulteFieldFemale).attr("hidden", "true");
            $(adulteFieldMale).attr("hidden", "true");
            $('option.shar').not('.child').hide()
          } 
           if (this.selectedIndex === 2 && gender === "زن") {
            $(adulteFieldMale).attr("hidden", "true");
            $(childrenF).attr("hidden", "true");
            $(childrenM).attr("hidden", "true");
            $(adulteFieldFemale).removeAttr("hidden");
            $('option.shar').filter('.shahrFemale').show()
          }
          if (this.selectedIndex === 2 && gender === "مرد"){
            $(adulteFieldMale).removeAttr("hidden");
            $(childrenF).attr("hidden", "true");
            $(childrenM).attr("hidden", "true");
            $(adulteFieldFemale).attr("hidden", "true");
            $('option.shar').show()
          }
           // form data
         
           $('.show-form').on('mouseover',function (e) { 
             e.preventDefault();
            let fullname=$('#name').val()+' '+$('#family').val();
            

            let email=$('#email').val()
            let phone=$('#phone').val()
            let shar=$('#shahr').val()
            let address=shar+'_'+$('#address').val();
          
            let field=$('#age').val()+'-'+$('#field').val()
            // let gender = $('input.radio').val();
            const selectedGender = document.querySelector("input[name='gender']:checked").value;
           
          //   if($('input#female').checked){
          //     gender='زن'
          // }else{
          //     gender='مرد'
          //    }
          $('#item1').empty().append(`<span">${fullname}</span>`);
          $('#item2').empty().append(`<span">${email}</span>`);
          $('#item3').empty().append(`<span">${phone}</span>`);
          $('#item4').empty().append(`<span">${address}</span>`);
          $('#item5').empty().append(`<span>${selectedGender}</span>`);
            $('#item6').empty().append(`<span">${field}'</span>`);
           
          });
        })})