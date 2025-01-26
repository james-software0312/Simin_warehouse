@extends('layouts.app')
@section('title', __('text.warehouse'))

@section('content')
<div class="body-inner">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
            <span class="material-symbols-rounded">task_alt</span> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
            <span class="material-symbols-rounded">error</span> {{ session('error') }}
        </div>
    @endif
    
    <div class="mb-4 d-flex align-items-center justify-content-between">
        <h2>{{ __('text.warehouse') }}</h2>
        <div class="text-end">
            <button type="button" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#AddModal">
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
                    <th>{{ __('text.is_primary') }}</th>
                    <th>{{ __('text.description') }}</th>
                    <th>{{ __('text.action') }}</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<x-modal>
    <form id="adddataform" method="POST" action="{{ route('warehouse.store') }}">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">{{ __('text.name') }}</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="{{ __('text.name') }}" required >
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">{{ __('text.description') }}</label>
            <textarea id="description" class="form-control" name="description" placeholder="{{ __('text.description') }}"></textarea>
        </div>
        {{-- <div class="mb-3">
            <label for="new_is_primary" class="form-label">{{ __('text.is_primary') }} </label>
            <div class="mb-3">
                <input type="checkbox" data-toggle="switchbutton" id="new_is_primary" name="new_is_primary" >
            </div>
        </div> --}}
    </form>
</x-modal>

<x-delete>
    <form id="deletedataform" method="POST" action="{{ route('warehouse.destroy')}}">
        @csrf
        <input type="hidden" name="deleteid" id="deleteid" value="">
    </form>
</x-delete>

<x-edit>
    <form id="editdataform" method="POST" action="{{ route('warehouse.update') }}">
        @csrf
        <input type="hidden" name="editid" id="editid" value="">
        
        <div class="mb-3">
            <label for="name" class="form-label">{{ __('text.name') }}</label>
            <input type="text" class="form-control" id="editname" name="name" placeholder="{{ __('text.name') }}" required >
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">{{ __('text.description') }}</label>
            <textarea id="editdescription" class="form-control" name="description" placeholder="{{ __('text.description') }}"></textarea>
        </div>
        <div class="mb-3">
            <label for="edit_is_primary" class="form-label">{{ __('text.is_primary') }} </label>
            <div class="mb-3">
                <input type="hidden"  id="is_primary" name="is_primary" value="0">
                <input type="checkbox"  id="edit_is_primary" name="edit_is_primary">
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


                // Use an AJAX request to fetch the data for the given group
                $.ajax({
                    url: '/warehouse/' + DataId, // Replace with your actual route
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        console.log(data);
                        // Populate the form fields with the retrieved data
                        $('#editid').val(data.id);
                        $('#editname').val(data.name);
                        $('#editdescription').val(data.description);
                        $('#edit_is_primary').prop('checked', data.is_primary == 1);
                    },
                    error: function() {
                        // Handle errors if needed
                    }
                });
            });

            $('#data').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{!! route('warehouse.get') !!}',
                dom: '<"d-flex align-items-md-center flex-column flex-md-row justify-content-md-between pb-3"Bf>rt<"pt-3 d-flex align-items-md-center flex-column flex-md-row justify-content-md-between"lp><"clear">',
                language: {
                    url: langUrl // Polish language JSON file
                },
                columns: [
                    { data: 'id', name: 'id', orderable: false, searchable: false, visible: false },
                    { data: 'name', name: 'name' },
                    {
                        data: 'is_primary',
                        name: 'is_primary',
                        render: function(data, type, row) {
                            if (data == 1) {
                                return '<span class="bg-primary text-white p-1 rounded">{{__("text.yes")}}</span>';
                            } else {
                                return '';
                            }
                        }
                    },              
                    { data: 'description', name: 'description' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                buttons: [
                {
                    extend: 'csv',
                    text: '<div class="d-flex align-items-center"><span class="material-symbols-rounded">text_snippet</span> CSV</div>',
                    className: 'btn btn-sm btn-fill btn-info ',
                    title: 'Warehouse Data',
                    exportOptions: {
                        columns: [1, 2]
                    }
                },
                {
                    extend: 'pdf',
                    text: '<div class="d-flex align-items-center"><span class="material-symbols-rounded">picture_as_pdf</span> PDF</div>',
                    className: 'btn btn-sm btn-fill btn-info ',
                    title: 'Warehouse Data',
                    orientation: 'landscape',
                    exportOptions: {
                        columns: [1, 2]
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
            messages: {
                name: {
                    required: '{!!__('text.field_required')!!}'
                }
            },
            submitHandler: function (form) {
                form.submit();
            }
        });
        $('#editdataform').validate({
            messages: {
                name: {
                    required: '{!!__('text.field_required')!!}'
                }
            },
            submitHandler: function (form) {
                form.submit();
            }
        });
        
        $('#edit_is_primary').change(function() {
            $('#is_primary').val(this.checked ? 1 : 0);
        });
    </script>
    @endpush
@endsection

