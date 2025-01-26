@extends('layouts.app')
@section('title', __('text.customers'))

@section('content')
<div class="body-inner">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
        <span class="material-symbols-rounded">task_alt</span> {{ session('success') }}
    </div>
    @endif
    <div class="mb-4 d-flex align-items-center justify-content-between">
        <h2>{{ __('text.customers') }}</h2>
        <div class="text-end">
            <button type="button" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal"
                data-bs-target="#AddModal">
                <span class="material-symbols-rounded">add</span>{{ __('text.add') }}
            </button>
        </div>
    </div>
    <div class="border-top pt-2">
        <h4 class="mb-2">{{__('text.filter_data')}}</h4>
        <form id="form_customer" method="POST" class="mb-4">
            @csrf
            <div class="row">
                <div class="col-md-3">
                    <div class="">
                        <label for="name" class="form-label">{{__('text.name')}}</label>
                        <input type="text" id="name" name="name" class="form-control"
                            placeholder="{{__('text.name')}}" />
                        <label for="name" class="error"></label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="">
                        <label for="email" class="form-label">{{__('text.email')}}</label>
                        <input type="text" id="email" name="email" class="form-control"
                            placeholder="{{__('text.email')}}" />
                        <label for="email" class="error"></label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="">
                        <label for="whatsapp" class="form-label">{{__('text.whatsapp')}}</label>
                        <input type="text" id="whatsapp" name="whatsapp" class="form-control"
                            placeholder="{{__('text.whatsapp')}}" />
                        <label for="whatsapp" class="error"></label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="">
                        <label for="vat_number" class="form-label">{{__('text.vat_number')}}</label>
                        <input type="text" id="vat_number" name="vat_number" class="form-control"
                            placeholder="{{__('text.vat_number')}}" />
                        <label for="vat_number" class="error"></label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3 d-flex" >
                        <button type="button" class="btn btn-secondary d-flex align-items-center d-none" id="resetBtn"><span
                                class="material-symbols-rounded">close</span> {{__('text.reset')}}</button> &nbsp;
                        <button type="input" class="btn btn-primary d-flex align-items-center" id="button"><span
                                        class="material-symbols-rounded">check</span> {{__('text.apply_filters')}}</button>
                    </div>
                </div>
            </div>
        </form>
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
<!-- Define the modal content and title -->

<x-large-modal>
    <form id="adddataform" method="POST" action="{{ route('customer.store') }}">
        @csrf

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="name" class="form-label">{{ __('text.name') }}</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="{{ __('text.name') }}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="surname" class="form-label">{{ __('text.surname') }}</label>
                    <input type="text" class="form-control" id="surname" name="surname" placeholder="{{ __('text.surname') }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="company" class="form-label">{{ __('text.company') }}</label>
                    <input type="text" class="form-control" id="company" name="company" placeholder="{{ __('text.company') }}" >
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="vat_number" class="form-label">{{ __('text.vat_number') }}</label>
                    <input type="text" class="form-control" id="vat_number" name="vat_number" placeholder="{{ __('text.vat_number') }}" >
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="address" class="form-label">{{ __('text.address') }}</label>
                    <input type="text" class="form-control" id="address" name="address" placeholder="{{ __('text.address') }}" >
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="city" class="form-label">{{ __('text.city') }}</label>
                    <input type="text" class="form-control" id="city" name="city" placeholder="{{ __('text.city') }}" >
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="postal_code" class="form-label">{{ __('text.postal_code') }}</label>
                    <input type="text" class="form-control" id="postal_code" name="postal_code" placeholder="{{ __('text.postal_code') }}" >
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="country" class="form-label">{{ __('text.country') }}</label>
                    <input type="text" class="form-control" id="country" name="country" placeholder="{{ __('text.country') }}" >
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="email" class="form-label">{{ __('text.email') }}</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="{{ __('text.email') }}" >
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="phone" class="form-label">{{ __('text.phone') }}</label>
                    <input type="text" class="form-control" id="phone" name="phone" placeholder="{{ __('text.phone') }}" >
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="whatsapp" class="form-label">{{ __('text.whatsapp') }}</label>
                    <input type="text" class="form-control" id="whatsapp" name="whatsapp" placeholder="{{ __('text.whatsapp') }}" >
                </div>
            </div>
            <div class="col-md-12">
                <div class="mb-3">
                    <label for="description" class="form-label">{{ __('text.notes') }}</label>
                    <textarea id="description" class="form-control" name="description" placeholder="{{ __('text.notes') }}"></textarea>
                </div>                
            </div>
        </div>
        <input type="hidden" value="1" name="status" id="status" />
    </form>
</x-modal>

<x-delete>
    <form id="deletedataform" method="POST" action="{{ route('customer.destroy')}}">
        @csrf
        <input type="hidden" name="deleteid" id="deleteid" value="">
    </form>
</x-delete>

<x-large-edit>
    <form id="editdataform" method="POST" action="{{ route('customer.update') }}">
        @csrf
        <input type="hidden" name="editid" id="editid" value="">

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="name" class="form-label">{{ __('text.name') }}</label>
                    <input type="text" class="form-control" id="edit_name" name="name" placeholder="{{ __('text.name') }}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="surname" class="form-label">{{ __('text.surname') }}</label>
                    <input type="text" class="form-control" id="edit_surname" name="surname" placeholder="{{ __('text.surname') }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="company" class="form-label">{{ __('text.company') }}</label>
                    <input type="text" class="form-control" id="edit_company" name="company" placeholder="{{ __('text.company') }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="vat_number" class="form-label">{{ __('text.vat_number') }}</label>
                    <input type="text" class="form-control" id="edit_vat_number" name="vat_number" placeholder="{{ __('text.vat_number') }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="address" class="form-label">{{ __('text.address') }}</label>
                    <input type="text" class="form-control" id="edit_address" name="address" placeholder="{{ __('text.address') }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="city" class="form-label">{{ __('text.city') }}</label>
                    <input type="text" class="form-control" id="edit_city" name="city" placeholder="{{ __('text.city') }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="postal_code" class="form-label">{{ __('text.postal_code') }}</label>
                    <input type="text" class="form-control" id="edit_postal_code" name="postal_code" placeholder="{{ __('text.postal_code') }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="country" class="form-label">{{ __('text.country') }}</label>
                    <input type="text" class="form-control" id="edit_country" name="country" placeholder="{{ __('text.country') }}" >
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="email" class="form-label">{{ __('text.email') }}</label>
                    <input type="email" class="form-control" id="edit_email" name="email" placeholder="{{ __('text.email') }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="phone" class="form-label">{{ __('text.phone') }}</label>
                    <input type="text" class="form-control" id="edit_phone" name="phone" placeholder="{{ __('text.phone') }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="whatsapp" class="form-label">{{ __('text.whatsapp') }}</label>
                    <input type="text" class="form-control" id="edit_whatsapp" name="whatsapp" placeholder="{{ __('text.whatsapp') }}">
                </div>
            </div>
            <div class="col-md-12">
                <div class="mb-3">
                    <label for="description" class="form-label">{{ __('text.notes') }}</label>
                    <textarea id="edit_description" class="form-control" name="description" placeholder="{{ __('text.notes') }}"></textarea>
                </div>                
            </div>
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
        $('#editid').val(DataId);


        // Use an AJAX request to fetch the data for the given group
        $.ajax({
            url: 'customer/' + DataId, // Replace with your actual route
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                // Populate the form fields with the retrieved data
                $('#edit_id').val(data.id);
                $('#edit_name').val(data.name);
                $('#edit_surname').val(data.surname);
                $('#edit_company').val(data.company);
                $('#edit_address').val(data.address);
                $('#edit_city').val(data.city);
                $('#edit_postal_code').val(data.postal_code);
                $('#edit_country').val(data.country);
                $('#edit_email').val(data.email);
                $('#edit_phone').val(data.phone);
                $('#edit_vat_number').val(data.vat_number);
                $('#edit_whatsapp').val(data.whatsapp);
                $('#edit_description').val(data.description);
            },
            error: function() {
                // Handle errors if needed
            }
        });
    });

    var customerTable = $('#data').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{!! route('customer.get') !!}',
            data: function(d) {
                d.status = 1;
                d.name = $('input[name=name]').val();
                d.email = $('input[name=email]').val();
                d.whatsapp = $('input[name=whatsapp]').val();
                d.vat_number = $('input[name=vat_number]').val();
            }
        },
        dom: '<"d-flex align-items-md-center flex-column flex-md-row justify-content-md-between pb-3"Bf>rt<"pt-3 d-flex align-items-md-center flex-column flex-md-row justify-content-md-between"lp><"clear">',
        
        drawCallback: function() {
            var api = this.api();
            // Get total quantity and total price from the server-side response
            var response = api.ajax.json();
        },
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
                title: 'Customers Data',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5, 6]
                }
            },
            {
                extend: 'pdf',
                text: '<div class="d-flex align-items-center"><span class="material-symbols-rounded">picture_as_pdf</span> PDF</div>',
                className: 'btn btn-sm btn-fill btn-info ',
                title: 'Customers Data',
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
    
    $('#form_customer').on('submit', function(e) {
        console.log("---")
        e.preventDefault();
        customerTable.draw();
    });

});



// Initialize jQuery Validation
$('#adddataform').validate({
    rules: {
        name: {
            required: true,
            // uniqueemail: true
        },
        // phone: {
        //     required: true,
        //     number: true,
        //     minlength: 5,
        //     maxlength: 15
        // }
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
        name: {
            required: true,
            // uniqueemailedit: true
        },
        // phone: {
        //     required: true,
        //     number: true,
        //     minlength: 5,
        //     maxlength: 15
        // }
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