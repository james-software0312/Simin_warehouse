@extends('layouts.app')
@section('title', __('text.activity_log'))

@section('content')
<div class="body-inner">

    <div class="mb-4 d-flex align-items-center justify-content-between">
        <h2>{{ __('text.activity_log') }}</h2>
    </div>
    
    <div class="table-responsive-sm">
        <table class="table" id="data">
            <thead>
                <tr>
                    <th>{{ __('text.id') }}</th>
                    <th>{{ __('text.name') }}</th>
                    <th>{{ __('text.description') }}</th>
                    <th>{{ __('text.detail') }}</th>
                    <th>{{ __('text.user') }}</th>
                    <th>{{ __('text.created') }}</th>
                    <th>{{ __('text.action') }}</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="detailModel" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content ">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ __('text.detail') }}</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body overflow-scroll">
                <div class="">
                    <table>
                        <tr>
                            <td valign="top">
                                <p class="me-4"><strong>{{ __('text.subject_type') }}:</strong></p>
                            </td>
                            <td valign="top">
                                <p id="subjecttype"></p>
                            </td>
                        </tr>
                        <tr>
                            <td valign="top">
                                <p class="me-4"><strong>{{ __('text.properties') }}:</strong></p>
                            </td>
                            <td valign="top">
                                <p><code id="properties"></code></p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary d-flex align-items-center" data-bs-dismiss="modal">
                    <span class="material-symbols-rounded">
                        close
                    </span>{{ __('text.close') }}
                </button>
            </div>
        </div>
    </div>
</div>




@push('scripts')
<script type="module">
$(function() {


    // Triggered when the "Edit" button is clicked
    $('#detailModel').on('show.bs.modal', function(event) {

        // Get the group ID from the data attribute
        var button = $(event.relatedTarget);
        var DataId = button.data('editid');


        // Use an AJAX request to fetch the data for the given group
        $.ajax({
            url: 'activity/' + DataId, // Replace with your actual route
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                // Populate the form fields with the retrieved data
                $('#subjecttype').html(data.subject_type);
                $('#properties').html(data.properties);

            },
            error: function() {
                // Handle errors if needed
            }
        });
    });

    $('#data').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{!! route('activity.get') !!}',
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
                data: 'log_name',
                name: 'name'
            },
            {
                data: 'description',
                name: 'description'
            },
            {
                data: 'properties',
                name: 'detail'
            },
            {
                data: 'user',
                name: 'user'
            },
            {
                data: 'created_at',
                name: 'created_at'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            },
        ],
        buttons: [{
                extend: 'csv',
                text: '<div class="d-flex align-items-center"><span class="material-symbols-rounded">text_snippet</span> CSV</div>',
                className: 'btn btn-sm btn-fill btn-info ',
                title: 'Activity Data',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5]
                }
            },
            {
                extend: 'pdf',
                text: '<div class="d-flex align-items-center"><span class="material-symbols-rounded">picture_as_pdf</span> PDF</div>',
                className: 'btn btn-sm btn-fill btn-info ',
                title: 'Activity Data',
                orientation: 'landscape',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5]
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
</script>
@endpush
@endsection