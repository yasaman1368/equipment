// URL to simulate success
const success = 'https://codepen.io/uxjulia/pen/a35d3ea1688ec7d933f257f9ffa67116.html'
// URL to simulate Error
const fail = 'https://codepen.io/uxjulia/pen/d88ca4d7142a75937092ed02e8ddbcb1.html'

$('#button').on('click', start);

function start(){
  showToastr("info", "Please Wait", "I'm fetching some data.");  
  setTimeout(loadResponse, 3000); // Setting arbitrary timeout here so we can see the 'loading' state.
};

function loadResponse(){
    let url, random;
    random = Math.floor((Math.random() * 1000) + 1);
    if (random >= 500) { url = success } else { url = fail } // Randomizing the status returned
    console.log(random);
    $.get(url, function(html){
    if (url == success) {
      toastr.remove();
      showToastr('success', 'Success!', html);
    }
    if (url == fail) {
      toastr.remove();
      showToastr('error', 'Error!', html);
    }
  });
}

function showToastr(type, title, message) {
    let body;
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "toast-bottom-right",
        "preventDuplicates": true,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": 0,
        "onclick": null,
        "onCloseClick": null,
        "extendedTimeOut": 0,
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut",
        "tapToDismiss": false
    };
    switch(type){
        case "info": body = "<span> <i class='fa fa-spinner fa-pulse'></i></span>";
            break;
        default: body = '';
    }
    const content = message + body;
    toastr[type](content, title)
}

