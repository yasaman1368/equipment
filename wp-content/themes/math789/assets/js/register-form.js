jQuery(document).ready(function($) {
    //insert loginView.php for register form
    const $roleSelect = $('#role');
    const $lessonSelect = $('.lesson-select');
    const $educationBaseSelect = $('.education-base-select');
    const $roleParent = $roleSelect.parent('div');

    $roleSelect.change(function(e) {
        e.preventDefault();
        const role = $(this).val();

        if (role === 'teacher') {
            $roleParent.removeClass('col s12').addClass('col s6');
            $lessonSelect.show();
            $educationBaseSelect.hide();
        } else if (role === 'student') {
            $roleParent.removeClass('col s12').addClass('col s6');
            $educationBaseSelect.show();
            $lessonSelect.hide();
        } else {
            $roleParent.removeClass('col s6').addClass('col s12');
            $educationBaseSelect.hide();
            $lessonSelect.hide();
        }
    });
   
});