jQuery(document).ready(function ($) {
        //dashboard  - bottom menu 
let locationPath = window.location.pathname;
let latestRout = locationPath.substring(locationPath.lastIndexOf('/') + 1);
$('div.bottom-menu span.name').css('display','none');
$('div.bottom-menu div.' + latestRout).siblings().removeClass('text-white  bg-info')
$('div.bottom-menu div.' + latestRout).siblings().find('span.name').css('display','none')
$('div.bottom-menu div.' + latestRout).addClass('text-white  bg-info')
$('div.bottom-menu div.' + latestRout).find('span.name').css('display','inline-block');

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

    const ajaxUrl = $('input[name=ajax-url]').val();
    const ajaxNonce = $('input[name=ajax-nonce]').val();

//in teacher panel exam for handling sections of a lessson:


const checkboxDiv= $('.chekbox-group-section');
const $baseEducation=$('#base-education');
const $lesson=$('#lessons');
let perviousBase='';
let perviouslesson='';
function handelChange(){
    const base=$baseEducation.val();
    const lesson=$lesson.val();
    if(lesson==='all' || base==='all'){
        $('.alert.alert-info.alert-dismissible').show()
        checkboxDiv.hide()
        return;
    };
    checkboxDiv.show();
    $('.alert.alert-info.alert-dismissible').hide();
    
    if (perviousBase!==base ||perviouslesson!==lesson) {
        perviousBase=base;
        perviouslesson=lesson;
        renderingSections(base,lesson)
        
    }

}

$baseEducation.change(handelChange);
$lesson.change(handelChange);
function renderingSections(base,lesson){
    let  parentCat=lesson+'-'+base;
    $.ajax({
        type: "post",
        url: ajaxUrl,
        data: {
            parentCat:parentCat,
            action:'get_sections_cat',
            nonce:ajaxNonce,
            
        } ,
        dataType: "json",
        success: function (response) {
            if(response.success){
                checkboxDiv.html(response.html);
                $('[data-bs-toggle="tooltip"]').tooltip();
                
            }
        },
        error:function(response){
            if(response.responseJSON.error){
                checkboxDiv.hide()
                Toast.fire({
                    icon: 'error',
                    title: response.responseJSON.message
                });
            }
            
        }  
    });

}

$('#finishedExamCreate').hide()
$('body').on('submit','#creat-exam-form',function (e) { 
    e.preventDefault();
    
    const lesson = $lesson.val();
    const base = $baseEducation.val();
    const examName=$('#exam_name').val();
 
    if (lesson === 'all' || base === 'all') {
        Toast.fire({
            icon: 'info',
            title: 'پایه و کتاب درسی را انتخاب کنید'
        });
        return false;
    }
    
    const questionsNumber = $('#questions-number').val();

    if (!questionsNumber ) {
        Toast.fire({
            icon: 'info',
            title: 'تعداد سوالات را مشخص کنید.'
        });
        return false;
    }
    const testTime=$('#test-time').val();
    if (!testTime ) {
        Toast.fire({
            icon: 'info',
            title: 'وقت آزمون را مشخص کنید.'
        });
        return false;
    }


    const catsSlugArray = $('.chekbox-group-section input[type="checkbox"]:checked').map(function () {
        return $(this).val();
    }).get(); // Use .map() to create an array directly
    
    const catsNumber = catsSlugArray.length;

    if (catsNumber < 1) {
        Toast.fire({
            icon: 'info',
            title: 'فصل  را انتخاب کنید'
        });
        
    }

    const questionsNumberEachCat = Math.round(questionsNumber / catsNumber); // Use Math.floor for consistent division
    const questionsNumberLastCat = calculateQuestionsNumberLastCat(questionsNumber, catsNumber, questionsNumberEachCat);
    
    const catsQuestions = {};
    for (let index = 0; index < catsNumber; index++) {
        catsQuestions[catsSlugArray[index]] = (index === catsNumber - 1) ? questionsNumberLastCat : questionsNumberEachCat;
    }
    
 $.ajax({
    type: "post",
    url: ajaxUrl,
        data:{
            action:'creator_exam',
            nonce:ajaxNonce,
            catsQuestions:catsQuestions,
            lesson:lesson,
            base:base,
            examName:examName,
            testTime:testTime

        },
    dataType: "json",
    success: function (response) {
            if (response.success) {
                $('.exam-suggestion').removeClass('d-none');
                $('.exam-suggestion').html(response.html);
                localStorage.setItem('exam_data', response.exam_data);
                $('#finishedExamCreate').show();
                MathJax.typeset();
            }
        },
        error: function (error) {
            if(error.responseJSON.error){
                $('.exam-suggestion').removeClass('d-none');    
                $('.exam-suggestion').html(error.responseJSON.html);
            }     
    }
}); 
});

function calculateQuestionsNumberLastCat(questionsNumber, catsNumber, questionsNumberEachCat) {
    const checkNumberQuestion = catsNumber * questionsNumberEachCat;
    const diff = Math.abs(checkNumberQuestion - questionsNumber);
    
    if (diff === 0) {
        return questionsNumberEachCat;
    }
    return (checkNumberQuestion > questionsNumber) ? questionsNumberEachCat - diff : questionsNumberEachCat + diff;
}
$('body').on('click','#finishedExamCreate',function (e) { 
    e.preventDefault();
    let examData= JSON.parse(localStorage.getItem('exam_data'));
    const  classroomId=$('#classrooms option:selected').val();
    $.ajax({
        type: "post",
        url: ajaxUrl,
        data: {
            examData:examData,
            action:'save_exam_db',
            nonce:ajaxNonce,
            userId:$(this).data('user-id'),
            classroomId:classroomId 
        } ,
        dataType: "json",
        success: function (response) {
           
            if(response.success){
                Toast.fire({
                    icon: 'success',
                    title: response.message
                });
                window.location.href=`${window.location.origin}/panel?exam_status=active`
            }
        },
        error:function(response){
            if(response.responseJSON.error){
                Toast.fire({
                    icon: 'error',
                    title: response.responseJSON.message
                });
            }
            
        }

    
});
});
$('#search-exam-button').on('click',function (e) { 
    e.preventDefault();
 let examCode=$('input[aria-describedby="search-exam-button"]').val();
 let userId=$('input[aria-describedby="search-exam-button"]').data('current-user-id');

 
 $.ajax({
    type: "post",
    url: ajaxUrl,
    data: {
        action:'search_exam_by_code',
        nonce:ajaxNonce,
        // the exam code that created by the teacher
        examCode:examCode,
        //current-user(student)-id
        userId:userId
    } ,
    dataType: "json",
    success: function (response) {
        if(response.success){
         
            $('.default-container').html(response.html)
        }
    },
    error:function(response){
        if(response.responseJSON.error){
            Toast.fire({
                icon: 'error',
                title: response.responseJSON.message
            });
        }
        
    }
}); 
});
$('body').on('click','.render-exam-html',function (e) { 
    e.preventDefault();
 let examCode=$(this).data('exam-data');
 let userId=$(this).data('user-id');

 
 $.ajax({
    type: "post",
    url: ajaxUrl,
    data: {
        action:'render_html_exam_by_code',
        nonce:ajaxNonce,
        examCode:examCode,
        userId:userId,
       
    } ,
    dataType: "json",
    success: function (response) {
        if(response.success){
      
            $('.default-container').html(response.html);
            MathJax.typeset();
        }
    },
    error:function(response){
        if(response.responseJSON.error){
            Toast.fire({
                icon: 'error',
                title: response.responseJSON.message
            });
        }
        
    }
}); 
});

 $('.show-score-btn').click(function (e) { 
        e.preventDefault();
        let examCode=$(this).data('exam-code')
        $.ajax({
            type: "post",
            url: ajaxUrl,
            data: {
                action:'show_students_scores',
                nonce:ajaxNonce,
                examCode:examCode,
          
               
            } ,
            dataType: "json",
            success: function (response) {
                if(response.success){
              $('.container-modal-show-scores').css('display', 'block');
                    $('.modal-center-absoult').html(response.html);
                
                }
            },
            error:function(response){
                if(response.responseJSON.error){
                    Toast.fire({
                        icon: 'error',
                        title: response.responseJSON.message
                    });
                }
                
            }
        }); 
 });
 $('.form-user-data-edit').submit(function (e) { 
    e.preventDefault();
    let el=$(this)
    let formData=el.serialize();
    $.ajax({
        type: "post",
        url: el.data('url'),
        data: formData+'&action=edit_user_data&nonce='+el.data('nonce'),
        dataType: "json",
        success: function (response) {
            if(response.success){
                Toast.fire({
                    icon:'success',
                    title:response.message
                })
            }
        },
        error:function(error){
            if(error.responseJSON.error){
                Toast.fire({
                    icon: 'error',
                    title: error.responseJSON.message
                });
            }
            
        }
    });

 });
 //analysis exam
 $('body').on('click','.analysisExam',function (e) { 
    e.preventDefault();
    let el=$(this)
    userId=el.data('user-id');
    examCode=el.data('exam-code');
    window.location=window.location.origin+`/panel/TestKeyPage?user_id=${userId}&exam_code=${examCode}`    
 });
 $('body').on('click','#analysisAllResults',function (e) { 
    e.preventDefault();
    let el=$(this)
    userId=el.data('user-id');
    examCode=el.data('exam-code');
    window.location=window.location.origin+`/panel/analysis?exam_code=${examCode}`    
 });
 // show created exam for teacher
 $('.show-exam').click(function (e) { 
    e.preventDefault();
    let examCode=$(this).data('exam-code');
    $.ajax({
        type: "post",
        url: ajaxUrl,
        data:{
            action:'show_created_exam',
            nonce:ajaxNonce,
            examCode:examCode
        },
        dataType: "json",
        success: function (response) {
            if(response.success){
                $('#active-exams').html(response.html);
                MathJax.typeset();
            }
        },
        error:function (error){
            if(error.responseJSON.error){
                $('#active-exams').html(response.html);
            }
        }
    });

    
 });
$('.remove-exam').click(function (e) { 
    e.preventDefault();
    const el=$(this);
    const examCode=el.data('exam-code');
    const parentRow=el.closest('tr.table-secondry');
    const message=' با حذف آزمون تمامی اطلاعات و تحلیل های آزمون برای شما و دانش آموزان حذف خواهد شد ،آیا اطمینان دارید؟'
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
    });
    swalWithBootstrapButtons.fire({
        title: '',
        text: message,
        icon: "warning",
        showCancelButton: true,
        cancelButtonText: "خیر، بازگشت",
        confirmButtonText: "بله، حذف آزمون",
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "post",
                url: ajaxUrl,
                data:{
                    action:'remove_exam',
                    nonce:ajaxNonce,
                    examCode:examCode
                },
                dataType: "json",
                success: function (response) {
                    if(response.success){
                        Toast.fire({
                            icon:'success',
                            title:response.message
                        })
                        
                        parentRow.remove()
                    }
                },
                error:function (error){
                    if(error.responseJSON.error){
                        Toast.fire({
                            icon:'error',
                            title:error.responseJSON.message
                        })
                    }
                }
            });
        }
    });
    
    
});
$('body').on('click','.status-active',function (e) { 
    e.preventDefault();
    const el=$(this)
    const examCode=el.data('exam-code');
    const status=parseInt(el.data('status'));
    console.log(status); 
    $.ajax({
        type: "post",
        url: ajaxUrl,
        data:{
            action:'change_status_exam_active',
            nonce:ajaxNonce,
            examCode:examCode,
            status:status
        },
        dataType: "json",
        success: function (response) {
            if(response.success){
                el.closest('td.status-exam-button').html(response.html);
            }
        },
        error:function (error){
            if(error.responseJSON.error){
           
            }
        }
    });
    });

$('body').on('click','.add-to-classroom',function (e) { 
const studentId=$(this).data('user-id-btn');
$('button.add-student-to-classroom').data('student-id',studentId);

});
 
$('body').on('click','button.add-student-to-classroom',function (e) { 
    e.preventDefault();
    const el=$(this);
   const studentId=el.data('student-id')
   const classroom=$('input[name=classroom]:checked')
   const classroomId=classroom.data('classroom-id')
   const classroomName=classroom.siblings('label.form-check-label').text()

   
  $.ajax({
    type:'post' ,
    url: ajaxUrl,
    data: {
        action:"add_student_to_classroom",
        nonce:ajaxNonce,
        studentId:studentId,
        classroomId:classroomId
    },
    dataType: "json",
    success: function (response) {
        if(response.success){

            Toast.fire({
                icon: 'success',
                title: response.message+' '+classroomName+' '+' افزوده شد'
            });
            $(`button[data-user-id-btn=${studentId}]`).closest('td').html(classroomName)
           
            
        }
        if(response.info){
            
            Toast.fire({
                icon: 'info',
                title: response.message+' '+classroomName+' '+' ثبت شده است'
            });
           
        }
        
        
    },
    error:function(error){


        if(error.responseJSON.success){

            Toast.fire({
                icon: 'error',
                title: error.responseJSON.message
            });
        }
    }
  });
});
$('.creat-exam-cart-plan').click(function (e) { 
    e.preventDefault();
    const el=$(this).data('creat-type');
   
    $('.creat-exam-cart-plan').removeClass('p-5').addClass('p-2')
    if(el==='fast'){
        $('#creat-exam-form').removeClass('d-none')
        $('#creat-exam-form_custom').addClass('d-none')
        
    }else if(el==='desgin'){
        $('#creat-exam-form_custom').removeClass('d-none')
        $('#creat-exam-form').addClass('d-none')
        }
    
});

$('#creat-exam-form_custom').submit(function (e) { 
    e.preventDefault();
    const $form = $(this);
    const data = $form.serialize();
    const $inputExamCode = $('#add-new-question');
    const examCode = $inputExamCode.data('exam-code');

    $.ajax({
        type: "post",
        url: ajaxUrl,
        data:`${data}&action=save_quetion_creat_custom_exam&examcode=${encodeURIComponent(examCode)}`,
        dataType: "json",
        success: function (response) {
            if (response.success) {
                Toast.fire({
                    icon: 'success',
                    title: response.message
                });
                const $examSuggestion = $('.exam-suggestion');
                $examSuggestion.removeClass('d-none').html(response.html);
                $inputExamCode.data('exam-code', response.exam_code);
              $('textarea').val('');
              MathJax.typeset();
             

        }
    },
        error: function (error) {
            const errorMessage = error.responseJSON && error.responseJSON.error 
                ? error.responseJSON.message 
                : 'An unexpected error occurred.';
                Toast.fire({
                    icon: 'error',
                title: errorMessage
                });
    },
        complete: function () {
            localStorage.removeItem('question-content');
            localStorage.removeItem('option-A');
            localStorage.removeItem('option-B');
            localStorage.removeItem('option-C');
        localStorage.removeItem('option-D');
    }
   });
});

// Store textarea values in variables for efficiency
const textareaNames = ['question-content', 'option-A', 'option-B', 'option-C', 'option-D'];
textareaNames.forEach(name => {
    $(`textarea[name=${name}]`).val(localStorage.getItem(name) || '');
});

// Save to local storage without preventDefault
$('textarea').on('input', function () {
    let el = $(this);
    let name = el.attr('name');
    let value = el.val();
    localStorage.setItem(name, value);
});
$('.remove-classroom').click(function (e) { 
    const el = $(this);
    const classroomId = el.data('classroom-id');
    const classroomName = el.data('classroom-name');
    const message = ' از حذف کلاس اطمینان دارید؟';
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
    });
    
    swalWithBootstrapButtons.fire({
        title: '',
        text: message,
        icon: "warning",
        showCancelButton: true,
        cancelButtonText: "خیر",
        confirmButtonText: "بله",
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "post",
                url: ajaxUrl,
                data: {
                    action: 'remove_classroom',
                    nonce: ajaxNonce, 
                    classroomId: classroomId,
                    classroomName: classroomName
                },
                dataType: "json",
                success: function (response) {
                    if (response.success) {
                        Toast.fire({
                            icon: 'success',
                            title: response.message
                        });
                        el.closest('.card-classroom').remove();
                    }
                },
                error: function (error) {
                    const message = error.responseJSON ? error.responseJSON.message : 'An error occurred';
                        Toast.fire({
                        icon: 'error',
                        title: message
                    });
                }
            });
        }
    });
});
    $('body').on('click', '.edit-classroom', function (e) {
        const $this = $(this);
        const $siblingsDiv = $this.closest('span').siblings('div');
        const className = $siblingsDiv.find('span.classroom-name').text();
        const schoolName = $siblingsDiv.find('span.school-name').text();
        const classroomId = $this.closest('span').siblings('input.classroom-show').data('classroom-id');

        $('#schoolName-edit').val(schoolName);
        $('#classroom-name-edit').val(className);
    $('#edit-classroom-btn').data('classroom-id', classroomId);
    });

    $('#edit-classroom-btn').click(function (e) { 
        const newClassroomName = $('#classroom-name-edit').val();
        const newSchoolName = $('#schoolName-edit').val();
        const classroomId = $(this).data('classroom-id');

        $.ajax({
            type: "post",
            url: ajaxUrl,
            data: {
                action: 'edit_classroom_name',
                newClassroomName: newClassroomName,
                newSchoolName: newSchoolName,
                classroomId: classroomId,
                nonce: ajaxNonce,
            },
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    Toast.fire({
                        icon: 'success',
                        title: response.message
                    });

                    const $classroomInput = $('input[data-classroom-id=' + classroomId + ']');
                    const $siblingsDiv = $classroomInput.siblings('div');
                    $siblingsDiv.find('span.classroom-name').text(response.classroomName);
                    $siblingsDiv.find('span.school-name').text(response.schoolName);
                    $('#modalId-edit').modal('hide');
                }
        },
            error: function (error) {
                const message = error.responseJSON ? error.responseJSON.message : 'An error occurred';
                    Toast.fire({
                    icon: 'error',
                    title: message
                });
            }
        });    
    
});
$('.creat-exam-cart-plan').click(function (e) { 
    $('.exam-suggestion').empty().addClass('d-none'); 
});
$('i.remove-student-from-class').click(function (e) { 
    const el = $(this);
    const studentId = el.data('student-id');
    const classroomId = el.data('classroom-id');
    const ajaxNonce = $('input[name=ajax-nonce]').data('ajax-nonce'); // Get the value directly
    const ajaxUrl = $('input[name=ajax-url]').data('ajax-url'); // Get the value directly
    const studentName=el.closest('td').siblings('.student-name').text();
    const message=' از حذف '+studentName+' اطمینان دارید؟'
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
    });
    swalWithBootstrapButtons.fire({
        title: '',
        text: message,
        icon: "warning",
        showCancelButton: true,
        cancelButtonText: "خیر",
        confirmButtonText: "بله",
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "post",
                url: ajaxUrl,
                data: {
                action: 'remove_student_from_classroom',
                studentId: studentId,
                classroomId: classroomId,
                nonce: ajaxNonce
                },
                dataType: "json",
                success: function (response) {
                    if (response.success) {
                        Toast.fire({
                            icon: 'success',
                            title: response.message
                        })
                        el.closest('tr').remove();      
                }
            }
            ,
            error: function (error) {
                const message = error.responseJSON ? error.responseJSON.message : 'An error occurred';
                    Toast.fire({
                    icon: 'error',
                    title: message
                });
            }
            });
        }
    });
       
});
});