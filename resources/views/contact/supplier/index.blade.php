@extends('layouts.app')
@section('title', __('text.suppliers'))

@section('content')
<div class="body-inner">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
        <span class="material-symbols-rounded">task_alt</span> {{ session('success') }}
    </div>
    @endif
    <div class="mb-4 d-flex align-items-center justify-content-between">
        <h2>{{ __('text.suppliers') }}</h2>
        <div class="text-end">
            <button type="button" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal"
                data-bs-target="#AddModal">
                <span class="material-symbols-rounded">add</span>{{ __('text.add') }}
            </button>
        </div>
    </div>
    <div class="table-responsive-sm">
        <table class="table" id="data">
            <thead>
                <tr>
                    <th>{{ __('text.id') }}</th>
                    <th>{{ __('text.name') }}</th>
                    <th>{{ __('text.company') }}</th>
                    <th>{{ __('text.email') }}</th>
                    <th>{{ __('text.phone') }}</th>
                    <th>{{ __('text.address') }}</th>
                    <th>{{ __('text.note') }}</th>
                    <th>{{ __('text.action') }}</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<x-modal>
    <form id="adddataform" method="POST" action="{{ route('supplier.store') }}">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">{{ __('text.name') }}</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="{{ __('text.name') }}" required>
        </div>
        <div class="mb-3">
            <label for="company" class="form-label">{{ __('text.company') }}</label>
            <input type="text" class="form-control" id="company" name="company" placeholder="{{ __('text.company') }}" >
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">{{ __('text.email') }}</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="{{ __('text.email') }}" >
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">{{ __('text.phone') }}</label>
            <input type="text" class="form-control" id="phone" name="phone" placeholder="{{ __('text.phone') }}" >
        </div>

        <div class="mb-3">
            <label for="address" class="form-label">{{ __('text.address') }}</label>
            <textarea id="address" class="form-control" name="address" placeholder="{{ __('text.address') }}"></textarea>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">{{ __('text.notes') }}</label>
            <textarea id="description" class="form-control" name="description" placeholder="{{ __('text.notes') }}"></textarea>
        </div>
        <input type="hidden" value="2" name="status" id="status" />
    </form>
</x-modal>

<x-delete>
    <form id="deletedataform" method="POST" action="{{ route('supplier.destroy')}}">
        @csrf
        <input type="hidden" name="deleteid" id="deleteid" value="">
    </form>
</x-delete>

<x-edit>
    <form id="editdataform" method="POST" action="{{ route('supplier.update') }}">
        @csrf
        <input type="hidden" name="editid" id="editid" value="">

        <div class="mb-3">
            <label for="name" class="form-label">{{ __('text.name') }}</label>
            <input type="text" class="form-control" id="editname" name="name" placeholder="{{ __('text.name') }}" required>
        </div>
        <div class="mb-3">
            <label for="company" class="form-label">{{ __('text.company') }}</label>
            <input type="text" class="form-control" id="editcompany" name="company" placeholder="{{ __('text.company') }}" >
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">{{ __('text.email') }}</label>
            <input type="email" class="form-control" id="editemail" name="email" placeholder="{{ __('text.email') }}" >
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">{{ __('text.phone') }}</label>
            <input type="text" class="form-control" id="editphone" name="phone" placeholder="{{ __('text.phone') }}" >
        </div>

        <div class="mb-3">
            <label for="address" class="form-label">{{ __('text.address') }}</label>
            <textarea id="editaddress" class="form-control" name="address" placeholder="{{ __('text.address') }}"></textarea>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">{{ __('text.notes') }}</label>
            <textarea id="editdescription" class="form-control" name="description" placeholder="{{ __('text.notes') }}"></textarea>
        </div>
    </form>
</x-edit>

@push('scripts')
<script type="module">
$(function() {


    // Triggered when the "Edit" button is clicked
    $('#EditModal').on('show.bs.modal', function(event) {

        // Get the group ID from the data attribute
        var button = $(event.relatedTarget);
        var DataId = button.data('editid');


        // Use an AJAX request to fetch the data for the given group
        $.ajax({
            url: 'supplier/' + DataId, // Replace with your actual route
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                // Populate the form fields with the retrieved data
                $('#editid').val(data.id);
                $('#editname').val(data.name);
                $('#editcompany').val(data.company);
                $('#editemail').val(data.email);
                $('#editphone').val(data.phone);
                $('#editaddress').val(data.address);
                $('#editdescription').val(data.description);
            },
            error: function() {
                // Handle errors if needed
            }
        });
    });

    $('#data').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{!! route('supplier.get') !!}?status=2',
        dom: '<"d-flex align-items-md-center flex-column flex-md-row justify-content-md-between pb-3"Bf>rt<"pt-3 d-flex align-items-md-center flex-column flex-md-row justify-content-md-between"lp><"clear">',
        language: {
            url: langUrl // Polish language JSON file
        },
        columns: [{
                data: 'id',
                name: 'id',
                orderable: false,
                searchable: false,
                visible: false
            },
            {
                data: 'name',
                name: 'name'
            },
            {
                data: 'company',
                name: 'company'
            },
            {
                data: 'email',
                name: 'email'
            },
            {
                data: 'phone',
                name: 'phone'
            },
            {
                data: 'address',
                name: 'address'
            },
            {
                data: 'description',
                name: 'description'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }
        ],
        buttons: [{
                extend: 'csv',
                text: '<div class="d-flex align-items-center"><span class="material-symbols-rounded">text_snippet</span> CSV</div>',
                className: 'btn btn-sm btn-fill btn-info ',
                title: 'Suppliers Data',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5, 6]
                }
            },
            {
                extend: 'pdf',
                text: '<div class="d-flex align-items-center"><span class="material-symbols-rounded">picture_as_pdf</span> PDF</div>',
                className: 'btn btn-sm btn-fill btn-info ',
                title: 'Suppliers Data',
                orientation: 'landscape',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5, 6]
                },
                customize: function(doc) {
                    doc.styles.tableHeader.alignment = 'left';
                    doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1)
                        .join('*').split('');
                }
            }
        ]
    });
});



// Initialize jQuery Validation
$('#adddataform').validate({
    rules: {
        
    },
    messages: {
        name: {
            required: '{!!__('text.field_required')!!}'
        }
    },
    submitHandler: function(form) {
        form.submit();
    }
});
$('#editdataform').validate({
    rules: {
        
        
    },
    messages: {
        name: {
            required: '{!!__('text.field_required')!!}'
        }
    },
    submitHandler: function(form) {
        form.submit();
    }
});
</script>
@endpush
@endsection