@extends('layouts.app')
@section('title', __('text.unit'))

@section('content')
<div class="body-inner">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
            <span class="material-symbols-rounded">task_alt</span> {{ session('success') }}
        </div>
    @endif
    <div class="mb-4 d-flex align-items-center justify-content-between">
        <h2>{{ __('text.unit') }}</h2>
        {{-- <div class="text-end">
            <button type="button" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#AddModal">
                <span class="material-symbols-rounded">add</span>{{ __('text.add') }}
            </button>
        </div> --}}
    </div>
    <div class="table-responsive-sm">
        <table class="table" id="data">
            <thead>
                <tr>
                    <th>{{ __('text.id') }}</th>
                    <th>{{ __('text.code') }}</th>
                    <th>{{ __('text.name') }}</th>
                    <th>{{ __('text.description') }}</th>
                    <th>{{ __('text.action') }}</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<x-modal>
    <form id="adddataform" method="POST" action="{{ route('unit.store') }}">
        @csrf
        <div class="mb-3">
            <label for="code" class="form-label">{{ __('text.code') }}</label>
            <input type="text" class="form-control" id="code" name="code" placeholder="{{ __('text.code') }}" required>
        </div>
        <div class="mb-3">
            <label for="name" class="form-label">{{ __('text.name') }}</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="{{ __('text.name') }}" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">{{ __('text.description') }}</label>
            <textarea id="description" class="form-control" name="description" placeholder="{{ __('text.description') }}"></textarea>
        </div>

    </form>
</x-modal>

<x-delete>
    <form id="deletedataform" method="POST" action="{{ route('unit.destroy') }}">
        @csrf
        <input type="hidden" name="deleteid" id="deleteid" value="">
    </form>
</x-delete>

<x-edit>
    <form id="editdataform" method="POST" action="{{ route('unit.update') }}">
        @csrf
        <input type="hidden" name="editid" id="editid" value="">
        <div class="mb-3">
            <label for="editcode" class="form-label">{{ __('text.code') }}</label>
            <input type="text" class="form-control" id="editcode" name="code" placeholder="{{ __('text.code') }}" required>
        </div>
        <div class="mb-3">
            <label for="editname" class="form-label">{{ __('text.name') }}</label>
            <input type="text" class="form-control" id="editname" name="name" placeholder="{{ __('text.name') }}" required>
        </div>
        <div class="mb-3">
            <label for="editdescription" class="form-label">{{ __('text.description') }}</label>
            <textarea id="editdescription" class="form-control" name="description" placeholder="{{ __('text.description') }}"></textarea>
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
                    url: '/unit/' + DataId, // Replace with your actual route
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        console.log(data);
                        // Populate the form fields with the retrieved data
                        $('#editid').val(data.id);
                        $('#editcode').val(data.code);
                        $('#editname').val(data.name);
                        $('#editdescription').val(data.description);
                        $("#editrelatedunits").val(data.relatedid).trigger('change');
                        $('#editrate').val(data.rate);
                    },
                    error: function() {
                        // Handle errors if needed
                    }
                });
            });

            var editRoute = "{!! route('unit.edit', ['id' => '__id__']) !!}";
            var deleteRoute = "{!! route('unit.destroy', ['id' => '__id__']) !!}";
            $('#data').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{!! route('unit.get') !!}',
                dom: '<"d-flex align-items-md-center flex-column flex-md-row justify-content-md-between pb-3"Bf>rt<"pt-3 d-flex align-items-md-center flex-column flex-md-row justify-content-md-between"lp><"clear">',
                language: {
                    url: langUrl // Polish language JSON file
                },
                columns: [
                    { data: 'id', name: 'id', orderable: false, searchable: false, visible: false },
                    { data: 'code', name: 'code' },
                    { data: 'name', name: 'name' },
                    { data: 'description', name: 'description' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, render: function(data, type, row) {
                        var url = editRoute.replace('__id__', row.id);
                        var url_d = deleteRoute.replace('__id__', row.id);
                        return '<a href="' + url + '" class="btn btn-sm btn-success edit-btn" data-id="' + row.id + '">Edit</a>'

                        // IF YOU WANT TO DELETE;
                        // &nbsp;<a data-bs-toggle="modal" data-bs-target="#DeleteModal" id="btndelete" data-deleteid="'+ row.id+'" href="' + url_d + '" class="btn btn-sm btn-warning edit-btn" data-id="' + row.id + '">Delete</a>;
                    }}
                ],
                buttons: [
                {
                    extend: 'csv',
                    text: '<div class="d-flex align-items-center"><span class="material-symbols-rounded">text_snippet</span> CSV</div>',
                    className: 'btn btn-sm btn-fill btn-info ',
                    title: 'Unit Data',
                    exportOptions: {
                        columns: [1, 2, 3]
                    }
                },
                {
                    extend: 'pdf',
                    text: '<div class="d-flex align-items-center"><span class="material-symbols-rounded">picture_as_pdf</span> PDF</div>',
                    className: 'btn btn-sm btn-fill btn-info ',
                    title: 'Unit Data',
                    orientation: 'landscape',
                    exportOptions: {
                        columns: [1, 2, 3]
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
                code: {
                    required: true,
                    uniquecode:true
                },
            },
            messages: {
                name: {
                    required: '{!!__('text.field_required')!!}'
                },
                code: {
                    required: '{!!__('text.field_required')!!}'
                }
            },
            submitHandler: function (form) {
                form.submit();
            }
        });
        $('#editdataform').validate({
            rules: {
                code: {
                    required: true,
                    uniquecodeedit:true
                },
            },
            messages: {
                name: {
                    required: '{!!__('text.field_required')!!}'
                },
                code: {
                    required: '{!!__('text.field_required')!!}'
                }
            },
            submitHandler: function (form) {
                form.submit();
            }
        });
    </script>
    @endpush
@endsection

