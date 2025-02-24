@extends('layouts.app')
@section('title', __('text.settings'))

@section('content')

<div class="body-inner">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
        <span class="material-symbols-rounded">task_alt</span> {{ session('success') }}
    </div>
    @endif
    <div class="mb-4">
        <h2 class="mb-5">{{ __('text.title') }}</h2>
        <ul class="nav nav-tabs" id="settingtab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="company-tab" data-bs-toggle="tab" data-bs-target="#company"
                    type="button" role="tab" aria-controls="company" aria-selected="true">{{ __('text.company_setting') }}</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="status-tab" data-bs-toggle="tab" data-bs-target="#status" type="button"
                    role="tab" aria-controls="status" aria-selected="false">{{ __('text.logo') }}</button>
            </li>
        </ul>
    </div>
    <div class="tab-content" id="TabContent">
        <div class="tab-pane fade show active" id="company" role="tabpanel" aria-labelledby="company-tab">
            <form id="updatedataform" method="POST" action="{{ route('setting.update') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="companyname" class="form-label">{{ __('text.company_name') }}</label>
                            <input type="text" class="form-control" id="companyname" name="company"
                                placeholder="{{ __('text.company_placeholder') }}" value="{{ $data->company }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="pagename" class="form-label">{{ __('text.page_name') }}</label>
                            <input type="text" class="form-control" id="pagename" name="pagename"
                                placeholder="{{ __('text.page_placeholder') }}" value="{{ $data->pagename }}" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="timezone" class="form-label">{{ __('text.time_zone') }}</label>
                            <select class="form-control" name="timezone" id="timezone">
                                <option value="">{{ __('text.select') }}</option>
                                <?php
                                    $timezones = timezone_identifiers_list();
                                    foreach ($timezones as $timezone) {
                                        $tz = new \DateTimeZone($timezone);
                                        $offset = $tz->getOffset(new \DateTime()) / 3600; // Convert offset to hours
                                        $offset_formatted = sprintf("%+03d:%02d", $offset, abs($offset % 1) * 60);
                                        $selected = ($timezone == $data->timezone) ? 'selected' : ''; // Check if this option should be selected
                                        echo "<option value=\"$timezone\" $selected>$timezone (GMT $offset_formatted)</option>";
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="datetime" class="form-label">{{ __('text.date_format') }}</label>
                            <select name="datetime" id="datetime" class="form-control">
                                <option value="">{{ __('text.select') }}</option>
                                <option value="d-m-Y"  {{ $data->datetime == 'd-m-Y' ? 'selected' : '' }}>{{ __('text.format.d-m-Y') }}</option>
                                <option value="m-d-Y"  {{ $data->datetime == 'm-d-Y' ? 'selected' : '' }}>{{ __('text.format.m-d-Y') }}</option>
                                <option value="Y-m-d"  {{ $data->datetime == 'Y-m-d' ? 'selected' : '' }}>{{ __('text.format.Y-m-d') }}</option>
                                <option value="d/m/Y" {{ $data->datetime == 'd/m/Y' ? 'selected' : '' }}>{{ __('text.format.d/m/Y') }}</option>
                                <option value="m/d/Y"  {{ $data->datetime == 'm/d/Y' ? 'selected' : '' }}>{{ __('text.format.m/d/Y') }}</option>
                                <option value="Y/m/d"  {{ $data->datetime == 'Y/m/d' ? 'selected' : '' }}>{{ __('text.format.Y/m/d') }}</option>
                                <option value="d.m.Y"  {{ $data->datetime == 'd.m.Y' ? 'selected' : '' }}>{{ __('text.format.d\m\Y') }}</option>
                                <option value="m.d.Y"  {{ $data->datetime == 'm.d.Y' ? 'selected' : '' }}>{{ __('text.format.m\d\Y') }}</option>
                                <option value="Y.m.d"  {{ $data->datetime == 'Y.m.d' ? 'selected' : '' }}>{{ __('text.format.Y\m\d') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="company_email" class="form-label">{{ __('text.company_email') }}</label>
                            <input type="text" class="form-control" id="company_email" name="company_email"
                                placeholder="{{ __('text.company_email') }}" value="{{ $data->company_email }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="company_phone" class="form-label">{{ __('text.company_phone') }}</label>
                            <input type="text" class="form-control" id="company_phone" name="company_phone"
                                placeholder="{{ __('text.company_phone') }}" value="{{ $data->company_phone }}" required>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="company_address" class="form-label">{{ __('text.company_address') }}</label>
                            <input type="text" class="form-control" id="company_address" name="company_address"
                                placeholder="{{ __('text.company_address') }}" value="{{ $data->company_address }}" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <input type="hidden" name="id" value="1" id="id" />
                        <button type="submit" class="btn btn-primary d-flex align-items-center">
                            <span class="material-symbols-rounded">save</span>{{ __('text.save') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="tab-pane fade" id="status" role="tabpanel" aria-labelledby="status-tab">
            <form id="updatedataformimage" method="POST" action="{{ route('setting.updatewithimage') }}"  enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-12 mb-5">
                        <p><strong>{{ __('text.logo') }}</strong></p>
                        <div class="dropzone" id="logo-dropzone">
                            <div class="drop-message text-center">
                                <p class="mb-2 font-m">{{ __('text.logo_instructions') }}</p>
                                <input name="logo" type="file" id="logo-input" accept="image/*" style="display: none;">
                                <button id="select-logo" type="button" class="btn btn-primary mb-4">
                                    {{ __('text.upload_image') }}
                                </button>
                            </div>
                            <img id="logo-preview" width="300" src="{{  $photo  }}" alt="Uploaded Logo">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-primary d-flex align-items-center">
                            <span class="material-symbols-rounded">save</span>{{ __('text.save') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>



@push('scripts')
<script type="module">
$(function() {

    const dropzone = $('#logo-dropzone');
    const logoInput = $('#logo-input');
    const logoPreview = $('#logo-preview');

    const dropzoneHeader = $('#header-dropzone');
    const headerInput = $('#header-input');
    const headerPreview = $('#header-preview');

    $("#select-logo").on('click', function() {
        $("#logo-input").click();
    });
    $("#select-header").on('click', function() {
        $("#header-input").click();
    });

    dropzone.on('dragover', function(e) {
        e.preventDefault();
        dropzone.addClass('hover');
    });

    dropzone.on('dragleave', function() {
        dropzone.removeClass('hover');
    });

    dropzone.on('drop', function(e) {
        e.preventDefault();
        dropzone.removeClass('hover');

        const file = e.originalEvent.dataTransfer.files[0];
        handleFile(file);
    });

    logoInput.on('change', function() {
        const file = logoInput[0].files[0];
        handleFile(file);
    });

    function handleFile(file) {
        if (file) {
            const reader = new FileReader();

            reader.onload = function(e) {
                logoPreview.attr('src', e.target.result);
                logoPreview.css('display', 'block');
            };

            reader.readAsDataURL(file);
        }
    }

    dropzoneHeader.on('dragover', function(e) {
        e.preventDefault();
        dropzoneHeader.addClass('hover');
    });

    dropzoneHeader.on('dragleave', function() {
        dropzoneHeader.removeClass('hover');
    });

    dropzoneHeader.on('drop', function(e) {
        e.preventDefault();
        dropzoneHeader.removeClass('hover');

        const file = e.originalEvent.dataTransfer.files[0];
        handleFileHeader(file);
    });

    headerInput.on('change', function() {
        const file = headerInput[0].files[0];
        handleFileHeader(file);
    });

    function handleFileHeader(file) {
        if (file) {
            const reader = new FileReader();

            reader.onload = function(e) {
                headerPreview.attr('src', e.target.result);
                headerPreview.css('display', 'block');
            };

            reader.readAsDataURL(file);
        }
    }

});


//function upload image
function UploadLogo() {
    // Use an AJAX request to fetch the data for the given group
    $.ajax({
        url: 'UploadLogo/' + Id, // Replace with your actual route
        type: 'POST',
        dataType: 'json',
        success: function(data) {
            // Populate the form fields with the retrieved data
            $('#editid').val(data.id);
            $('#editemail').val(data.email);
        },
        error: function() {
            // Handle errors if needed
        }
    });
}

// Initialize jQuery Validation
$('#updatedataform').validate({

    submitHandler: function(form) {
        form.submit();
    }
});

$('#updatedataformimage').validate({
    rules: {

    },
    submitHandler: function(form) {
        form.submit();
    }
});
</script>
@endpush
@endsection
