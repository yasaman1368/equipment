class FormManager {
    constructor() {
        this.initEventListeners();
    }

    initEventListeners() {
        document.getElementById('save-modal-form-name').addEventListener('click', this.handleAddNewForm.bind(this));
        document.getElementById('input-type').addEventListener('change', this.handleFeatureTypeChange.bind(this));
        document.getElementById('save-modal').addEventListener('click', this.handleSaveModal.bind(this));
        document.getElementById('add-new-option').addEventListener('click', this.handleAddNewOption.bind(this));
        document.getElementById('show-forms').addEventListener('click', this.handleShowForms.bind(this));
        document.getElementById('form-selector').addEventListener('change', this.handleFormSelectorChange.bind(this));
        document.getElementById('save-form-btn').addEventListener('click', this.handleSaveForm.bind(this));
    }

    handleAddNewForm() {
        const newFormName = document.getElementById('new-form-name').value.trim();
        const formNameHeader = document.getElementById('placeholder-form-name');
        const mainContainerForm = document.getElementById('main-container-form');
        const myModal = document.getElementById('modal-add-form-name');
        const formContainer = document.getElementById('show-saved-forms');
        formContainer.classList.add('d-none');

        if (!newFormName) {
            Swal.fire({
                title: 'خطا!',
                text: 'نام فرم الزامی است',
                icon: 'error'
            });
            return null;
        }

        formNameHeader.textContent = newFormName;
        mainContainerForm.classList.remove('d-none');
        bootstrap.Modal.getInstance(myModal).hide();
        document.getElementById('new-form-name').value = '';
    }

    handleFeatureTypeChange() {
        const featureTypeElement = document.getElementById('input-type');
        const featureType = featureTypeElement.value;
        const containerOptionsInput = document.getElementById('container-options-modal');
        const featureName = document.getElementById('new-feature-name')
        if (['select', 'checkbox', 'radio'].includes(featureType)) {
            document.getElementById('selected-input').textContent = featureTypeElement.options[featureTypeElement.selectedIndex].textContent;
            containerOptionsInput.classList.remove('d-none');
        }else if(featureType==='geo'){
            featureName.value='';
            featureName.disabled=false;
            featureName.value='موقعیت جغرافیایی';
            featureName.disabled=true;
        }
    }

    handleSaveModal(event) {
        event.preventDefault();
    
        const featureName = document.getElementById('new-feature-name').value;
        const featureType = document.getElementById('input-type').value;
        const formContainerDiv = document.getElementById('form-container-builder');
        const myModal = document.getElementById('modal-add-form');
    
        if (!featureName || !featureType) {
            Swal.fire({
                title: 'خطا!',
                text: 'نام و نوع وبژگی را وارد کنید',
                icon: 'error'
            });
            return;
        }
    
        const field = this.createField(featureName, featureType);
        if (field) {
            formContainerDiv.appendChild(field);
            document.getElementById('modal-form').reset();
            bootstrap.Modal.getInstance(myModal).hide();
        }
    }

    createField(featureName, featureType) {
        const numberOfFields = document.querySelectorAll('#form-container-builder .col-sm-6').length + 1;
        const divColSM6 = document.createElement('div');
        divColSM6.classList.add('col-sm-6', 'border-bottom');
        const divMb3 = document.createElement('div');
        divMb3.classList.add('mb-3');
        const label = document.createElement('label');
        label.textContent = featureName;
        label.classList.add('form-label');
        let inputElement;
    
        if (['text', 'number', 'date', 'time', 'file'].includes(featureType)) {
            inputElement = document.createElement('input');
            inputElement.type = featureType;
            inputElement.classList.add('form-control');
            inputElement.name = 'input-' + numberOfFields;
            if (featureType === 'file') {
                inputElement.accept = 'image/*';
            }
        } else if (featureType === 'select') {
            inputElement = this.createSelectField(featureName, numberOfFields);
            if (!inputElement) {
                return null;
            }
        } else if (['checkbox', 'radio'].includes(featureType)) {
            inputElement = this.createCheckboxRadioField(featureName, featureType, numberOfFields);
            if (!inputElement) {
                return null;
            }
        } else if (featureType === 'geo') {
            inputElement = document.createElement('input');
            inputElement.id = 'capture-geo-btn';
            inputElement.type = 'button';
            inputElement.classList.add('form-control', 'bg-warning');
            inputElement.value = 'موقعیت جغرافیایی';
        }
    
        if (inputElement) {
            divMb3.append(label, inputElement);
            divColSM6.appendChild(divMb3);
            return divColSM6;
        }
        return null;
    }

    createSelectField(featureName, numberOfFields) {
        const optionsBuilder = document.getElementById('input-option-container');
        const options = optionsBuilder.querySelectorAll('input.input-option');
        const selectElement = document.createElement('select');
        selectElement.classList.add('form-control');
        selectElement.name = 'input-' + numberOfFields;
        const firstOption = document.createElement('option');
        firstOption.textContent = '--انتخاب کنید--';
        selectElement.appendChild(firstOption);
    
        let hasValidOptions = false;
    
        Array.from(options).forEach(option => {
            const innerText = option.value.trim();
            if (innerText !== '') {
                const newOption = document.createElement('option');
                newOption.value = innerText;
                newOption.innerText = innerText;
                selectElement.appendChild(newOption);
                hasValidOptions = true;
            }
        });
    
        if (!hasValidOptions) {
            Swal.fire({
                title: 'خطا',
                text: 'حداقل یک گزینه برای لیست کشویی وارد کنید.',
                icon: 'error'
            });
            return null;
        }
    
        document.getElementById('container-options-modal').classList.add('d-none');
        optionsBuilder.innerHTML = '';
        return selectElement;
    }

    createCheckboxRadioField(featureName, featureType, numberOfFields) {
        const optionsBuilder = document.getElementById('input-option-container');
        const options = optionsBuilder.querySelectorAll('input.input-option');
        const divCheckboxSection = document.createElement('div');
    
        let hasValidOptions = false;
    
        Array.from(options).forEach(option => {
            const innerText = option.value.trim();
            if (innerText !== '') {
                const label = document.createElement('label');
                const inputElement = document.createElement('input');
                const inputuniqcode = featureType + '-' + numberOfFields;
                label.textContent = innerText;
                label.for = inputuniqcode;
                label.classList.add('form-label', 'm-2', 'p-2');
                inputElement.type = featureType;
                inputElement.value = innerText;
                inputElement.name = inputuniqcode;
                label.prepend(inputElement);
                divCheckboxSection.appendChild(label);
                hasValidOptions = true;
            }
        });
    
        if (!hasValidOptions) {
            Swal.fire({
                title: 'خطا!',
                text: 'حداقل یک گزینه برای گزینه رادیویی یا چک باکس وارد کنید.',
                icon: 'error'
            });
            return null;
        }
    
        document.getElementById('container-options-modal').classList.add('d-none');
        optionsBuilder.innerHTML = '';
        return divCheckboxSection;
    }

    handleAddNewOption() {
        const inputOptionContainer = document.getElementById('input-option-container');
        const newInputOption = document.createElement('input');
        newInputOption.type = 'text';
        newInputOption.classList.add('form-control', 'm-2', 'input-option');
        newInputOption.name = 'input-option';
        newInputOption.placeholder = 'گزینه را وارد کنید';
        inputOptionContainer.appendChild(newInputOption);
    }

    handleShowForms() {
        const formContainer = document.getElementById('show-saved-forms');
        const mainContainerForm = document.getElementById('main-container-form');
        mainContainerForm.classList.add('d-none');
        formContainer.classList.remove('d-none');

        fetch('/wp-admin/admin-ajax.php?action=get_saved_forms')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const formSelector = document.getElementById('form-selector');
                    formSelector.innerHTML = '<option selected>--انتخاب فرم--</option>';
                    data.data.forms.forEach(form => {
                        const option = document.createElement('option');
                        option.value = form.id;
                        option.textContent = form.form_name;
                        formSelector.appendChild(option);
                    });
                } else {
                    Swal.fire({
                        title: 'خطا!',
                        text: 'خطا در دریافت فرم‌ها.',
                        icon: 'error'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }

    handleFormSelectorChange(event) {
        const formId = event.target.value;
        const showFormsNameChecks = document.getElementById('show-header-forms');
        const showFormNameCheck = document.getElementById('show-form-name-check');
        showFormNameCheck.innerText = event.target.options[event.target.selectedIndex].textContent;
        showFormsNameChecks.classList.remove('d-none');

        fetch('/wp-admin/admin-ajax.php?action=get_form_fields', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `form_id=${formId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const locationDiv = document.getElementById('locations-display');
                locationDiv.innerHTML='';
                
              
                if(data.data.form_locations && data.data.form_locations instanceof Array && data.data.form_locations.length>0 ){
                
                    if(locationDiv){
                        
                    
                           const locationLabel=document.createElement('h6');
                            locationLabel.classList.add('text-dark','my-2');
                            locationLabel.innerText='لیست محل‌های فرم:';
                            locationDiv.appendChild(locationLabel);

                    data.data.form_locations.forEach(location => {
                         let locationsSpan=document.createElement('span');
                         locationsSpan.classList.add('p-1','text-dark','rounded','shadow','bg-light','mx-1');
                         locationsSpan.innerText=` ${location} `;       
                         locationDiv.appendChild(locationsSpan)
                        })

                    } else{
                        throw new Error('Location div not found.')
                    }
                    }
                

                const formContainer = document.getElementById('display-form');
                formContainer.innerHTML = '';
                data.data.fields.forEach(field => {
                    const fieldDiv = document.createElement('div');
                    const options = JSON.parse(field.options);
                    fieldDiv.classList.add('col-sm-6', 'border-bottom');
                    const label = document.createElement('label');
                    label.textContent = field.field_name;
                    label.classList.add('form-label');
                    let inputElement;

                    if (['text', 'number', 'date','time','file'].includes(field.field_type)) {
                        inputElement = document.createElement('input');
                        inputElement.type = field.field_type;
                        inputElement.classList.add('form-control');
                        if(field.fieldType==='file'){
                            inputElement.accept='image/*';
                        }
                    } else if (field.field_type === 'select') {
                        inputElement = document.createElement('select');
                        inputElement.classList.add('form-control');
                        options.forEach(option => {
                            const optionElement = document.createElement('option');
                            optionElement.value = option;
                            optionElement.textContent = option;
                            inputElement.appendChild(optionElement);
                        });
                    } else if (['checkbox', 'radio'].includes(field.field_type)) {
                        fieldDiv.appendChild(label);
                        options.forEach(option => {
                            const optionContainer = document.createElement('div');
                            optionContainer.classList.add('form-check');
                            const inputElement = document.createElement('input');
                            inputElement.type = field.field_type;
                            inputElement.classList.add('form-check-input');
                            inputElement.name = `field`;
                            const optionLabel = document.createElement('label');
                            optionLabel.textContent = option;
                            optionLabel.classList.add('form-check-label');
                            optionContainer.appendChild(inputElement);
                            optionContainer.appendChild(optionLabel);
                            fieldDiv.appendChild(optionContainer);
                        });
                        formContainer.appendChild(fieldDiv);
                    }else if(field.field_type==='button'){
                        // button type for geo location;
                        inputElement=document.createElement('input');
                        inputElement.id='capture-geo-btn';
                        inputElement.type='button';
                        inputElement.classList.add('form-control','bg-warning')
                        inputElement.value='موقعیت جغرافیایی';
                     
                    }

                    if (inputElement) {
                        fieldDiv.appendChild(label);
                        if (!['checkbox', 'radio'].includes(field.field_type)) {
                            fieldDiv.appendChild(inputElement);
                        }
                        fieldDiv.classList.add('p-2')
                        formContainer.appendChild(fieldDiv);
                    }
                });
                const removeBtn=document.createElement('input');
                removeBtn.type='button';
                removeBtn.value='حذف فرم';
                removeBtn.classList.add('btn','btn-danger','btn-lg','shadow','mt-2');
                formContainer.appendChild(removeBtn)
                removeBtn.addEventListener('click',()=>this.handlRemoveFrom())
            } else {
                Swal.fire({
                    title: 'خطا!',
                    text: 'خطا در دریافت فیلدهای فرم.',
                    icon: 'error'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    handleSaveForm() {
        const formName = document.getElementById('placeholder-form-name').textContent;
        const formContainerDiv = document.getElementById('form-container-builder');
        const fields = [];
        
        formContainerDiv.querySelectorAll('.col-sm-6').forEach((fieldDiv) => {
            const label = fieldDiv.querySelector('.form-label').textContent;
            const input = fieldDiv.querySelector('input, select');
            const fieldType = input.tagName.toLowerCase() === 'select' ? 'select' : input.type;
            let options = [];
            let value = '';

            if (fieldType === 'select') {
                options = Array.from(input.options).map(option => option.value);
                value = input.value;
            } else if (['checkbox', 'radio'].includes(fieldType)) {
                const inputs = fieldDiv.querySelectorAll(`input[type="${fieldType}"]`);
                const selectedValues = [];
                inputs.forEach(input => {
                    if (input.checked) {
                        selectedValues.push(input.value);
                    }
                });
                value = selectedValues.join(',');
                options = Array.from(inputs).map(input => input.value);
            } else {
                value = input.value;
            }

            fields.push({
                field_name: label,
                field_type: fieldType,
                options: options,
                value: value
            });
        });

        const locationsInput= document.querySelectorAll('input[name="locations[]"]:checked');
        const locations = Array.from(locationsInput).map(input => input.value);1       
        const formData = {
            form_name: formName,
            locations: locations,
            fields: fields
        };

        fetch('/wp-admin/admin-ajax.php?action=save_form_data', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `form_data=${JSON.stringify(formData)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'موفق!',
                    text: data.data.message,
                    icon: 'success'
                }).then(() => {
                    this.handleShowForms(); // Show all forms after saving
                });
            } else {
                Swal.fire({
                    title: 'خطا!',
                    text: 'خطا در ذخیره‌سازی فرم.',
                    icon: 'error'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
    handlRemoveFrom(){
        const formselector=document.getElementById('form-selector')
        const formId=formselector.options[formselector.selectedIndex].value
       
        Swal.fire({
            title: 'آیا از حذف فرم مطمئن هستید؟',
            text: " این عمل قابل بازگشت نیست!اطلاعات تجهیزاتی که با این فرم ساخته شده نیز حذف خواهد شد.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'بله، حذف شود!',
            cancelButtonText: 'لغو'
        }).then((result) => {
            if (result.isConfirmed) {
                this.fetchData('/wp-admin/admin-ajax.php?action=remove_form', { form_id: formId })
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'حذف شد!',
                                text:data.data.message ,
                                icon: 'success'
                            }).then(() => {
                                window.location.href = window.location.origin + '/panel/formmaker/?showforms=on';
                            });
                        } else {
                            Swal.fire({
                                title: 'خطا!',
                                text: data.data.message,
                                icon: 'error'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }
        });
    }

    fetchData(url, body = {}) {
        // If body is FormData, send it directly without headers
        if (body instanceof FormData) {
            return fetch(url, {
                method: 'POST',
                body: body, // No headers needed for FormData
            }).then(response => response.json());
        } else {
            // For JSON data, use URLSearchParams
            const params = new URLSearchParams(body);
            return fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: params
            }).then(response => response.json());
        }
    }
}

// Initialize the FormManager
new FormManager();