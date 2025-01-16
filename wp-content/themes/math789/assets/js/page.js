$(document).ready(function () {
 
  
    //show section
    $('ul.menu-list>li>button').click(function () { 
        let sectionName=$(this).attr('class')  
        $('section.md-container').css('display', 'none');
$('#'+sectionName).css('display', 'block');
        
    });
    
});