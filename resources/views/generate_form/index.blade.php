@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <!-- custom-icon Breadcrumb-->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-custom-icon">
                    @foreach ($breadcrumb as $item)
                        <li class="breadcrumb-item">
                            @if($item == 'Job Assignment Form')
                                <a href="{{ route('v1.job-assignment-form')}}">{{ $item }}</a>
                            @else
                                <a href="javascript:void(0);">{{ $item }}</a>
                            @endif
                            <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                        </li>
                    @endforeach
                </ol>
            </nav>
        </div>

        <h3>{{ $title }}</h3>
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Oops! Something went wrong:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="card">
            <div class="card-body">

                <div id="fields-wrapper"></div>

                <button onclick="addField()" class="btn btn-primary">Add Field</button>
                <button onclick="generateJson()" class="btn btn-success">Generate Form</button>

                <h3>Form Structure</h3>
                <pre id="json-output"></pre>
                <div id="rendered-form"></div>


                <script>
                    let fieldCount = 0;

                    function addField() {
                        fieldCount++;

                        const wrapper = document.getElementById('fields-wrapper');

                        const div = document.createElement('div');
                        div.className = 'field-group';
                        div.innerHTML = `
    <div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
        <label>Label:
            <input type="text" class="label-input form-control" placeholder="Field Label" style="width: 150px;">
        </label>

        <label>Type:
            <select class="type-input form-control" onchange="toggleOptions(this)" style="width: 130px;">
                <option value="text">Text</option>
                <option value="number">Number</option>
                <option value="email">Email</option>
                <option value="textarea">Textarea</option>
                <option value="select">Select</option>
                <option value="radio">Radio</option>
                <option value="checkbox">Checkbox</option>
                <option value="date">Date</option>
                <option value="file">File</option>
            </select>
        </label>

        <div class="options-wrapper" style="display: none;">
            <input type="text" class="options-input form-control" placeholder="Options (comma separated)" style="width: 200px;">
        </div>

        <label>Required:
            <input type="checkbox" class="required-input">
        </label>

        <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.field-group').remove()">Remove</button>
    </div>
`;


                        wrapper.appendChild(div);
                    }

                    function toggleOptions(selectElement) {
                        const wrapper = selectElement.closest('.field-group');
                        const optionsDiv = wrapper.querySelector('.options-wrapper');

                        if (['select', 'radio', 'checkbox'].includes(selectElement.value)) {
                            optionsDiv.style.display = 'block';
                        } else {
                            optionsDiv.style.display = 'none';
                        }
                    }

                    function generateJson() {
                        const groups = document.querySelectorAll('.field-group');
                        const structure = [];

                        groups.forEach((group, index) => {
                            const label = group.querySelector('.label-input').value;
                            const type = group.querySelector('.type-input').value;
                            const required = group.querySelector('.required-input').checked;
                            const optionsInput = group.querySelector('.options-input');

                            const field = {
                                name: `field_${index + 1}`,
                                label: label,
                                type: type,
                                required: required
                            };

                            if (optionsInput && ['select', 'radio', 'checkbox'].includes(type)) {
                                const raw = optionsInput.value;
                                const options = raw.split(',').map(opt => opt.trim()).filter(opt => opt);
                                field.options = options;
                            }

                            structure.push(field);
                        });

                        //document.getElementById('json-output').textContent = JSON.stringify(structure, null, 2);
                        renderFormFromJson(structure);
                    }
                </script>
                <script>
function renderFormFromJson(json, containerId = 'rendered-form') {
    const container = document.getElementById(containerId);
    container.innerHTML = ''; // Clear previous content

    json.forEach(field => {
        let formGroup = document.createElement('div');
        formGroup.className = 'form-group mb-3';

        let label = document.createElement('label');
        label.textContent = field.label + (field.required ? ' *' : '');
        formGroup.appendChild(label);

        let input;

        switch (field.type) {
            case 'text':
            case 'number':
            case 'email':
            case 'date':
            case 'file':
                input = document.createElement('input');
                input.type = field.type;
                input.name = field.name;
                input.className = 'form-control';
                if (field.required) input.required = true;
                formGroup.appendChild(input);
                break;

            case 'textarea':
                input = document.createElement('textarea');
                input.name = field.name;
                input.className = 'form-control';
                if (field.required) input.required = true;
                formGroup.appendChild(input);
                break;

            case 'select':
                input = document.createElement('select');
                input.name = field.name;
                input.className = 'form-control';
                if (field.required) input.required = true;

                if (field.options && Array.isArray(field.options)) {
                    field.options.forEach(option => {
                        const opt = document.createElement('option');
                        opt.value = option;
                        opt.textContent = option;
                        input.appendChild(opt);
                    });
                }
                formGroup.appendChild(input);
                break;

            case 'radio':
            case 'checkbox':
                if (field.options && Array.isArray(field.options)) {
                    field.options.forEach((option, index) => {
                        const wrapper = document.createElement('div');
                        wrapper.className = field.type;

                        const inputEl = document.createElement('input');
                        inputEl.type = field.type;
                        inputEl.name = field.name + (field.type === 'checkbox' ? '[]' : '');
                        inputEl.value = option;
                        inputEl.id = `${field.name}_${index}`;

                        const labelEl = document.createElement('label');
                        labelEl.htmlFor = inputEl.id;
                        labelEl.textContent = option;
                        labelEl.style.marginLeft = '8px';

                        wrapper.appendChild(inputEl);
                        wrapper.appendChild(labelEl);

                        formGroup.appendChild(wrapper);
                    });
                }
                break;

            default:
                console.warn('Unknown field type:', field.type);
                break;
        }

        container.appendChild(formGroup);
    });
}
</script>



            </div>
        </div>
    </div>
    </div>
@endsection
