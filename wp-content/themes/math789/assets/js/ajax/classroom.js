jQuery(document).ready(function ($) {
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
    const nonce=$('input[name=ajax-nonce]').val()
    const ajaxUrl=$('input[name=ajax-url]').val()
   $('#add-new-classroom').click(function (e) { 
    e.preventDefault();
    const schoolName=$('#schoolName').val();
    const classroomName=$('#classroom-name').val();
    $.ajax({
        type: "post",
        url:ajaxUrl ,
        dataType:'json',
        data: {
            action:'creator_classroom',
            nonce:nonce,
            schoolName:schoolName,
            classroomName:classroomName
        },
        success: function (response) {
            Toast.fire({
                icon: 'success',
                title: response.message
            });
            
        },
        error:function(error){
            if(error.responseJSON.error){
                Toast.fire({
                    icon: 'error',
                    title: error.responseJSON.message
                });
            }
        },
        complete:function(){
            window.location=window.location.origin+'/panel/classroomoffice'
        }
    });
    
   });
   $('.classroom-show').click(function (e) { 
    e.preventDefault();
   const classroomId=$(this).data('classroom-id');
   window.location.href=window.location.origin+`/panel/classroomoffice?classroom_id=${classroomId}`;
 
   });

    
});