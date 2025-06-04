function triggerExport(event) {
   // event.preventDefult()

    // Create a hidden form
    var form = document.createElement('form');
    form.method = 'GET';
    form.action = '';

    // Add the action parameter
    var input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'action';
    input.value = 'export_equipments';
    form.appendChild(input);

    // Append the form to the body and submit it
    document.body.appendChild(form);
    form.submit();
}



function  fetchData(url, body = {}) {
    const params = new URLSearchParams(body);
    return fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: params
    }).then(response => response.json());
}
