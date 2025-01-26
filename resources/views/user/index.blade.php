@extends('layouts.app')
@section('title', __('text.user'))

@section('content')

<div class="body-inner">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
            <span class="material-symbols-rounded">task_alt</span> {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center" role="alert">
            <span class="material-symbols-rounded">error</span>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mb-4 d-flex align-items-center justify-content-between">
        <h2>{{ __('text.user') }}</h2>
        <div class="text-end">
            @if($hasCreatePermission)
                <button type="button" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#AddModal">
                    <span class="material-symbols-rounded">add</span>{{ __('text.add') }}
                </button>
            @endif
        </div>
    </div>
    <div class="table-responsive-sm">
        <table class="table" id="data">
            <thead>
                <tr>
                    <th>{{ __('text.id') }}</th>
                    <th>{{ __('text.name') }}</th>
                    <th>{{ __('text.email') }}</th>
                    <th>{{ __('text.action') }}</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<x-modal>
    <form id="adddataform" method="POST" action="{{ route('user.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">{{ __('text.name') }}</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="{{ __('text.name') }}" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">{{ __('text.email') }}</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="{{ __('text.email') }}" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">{{ __('text.password') }}</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="{{ __('text.password') }}" >
        </div>
    </form>
</x-modal>

<x-delete>
    <form id="deletedataform" method="POST" action="{{ route('user.destroy') }}">
        @csrf
        <input type="hidden" name="deleteid" id="deleteid" value="">
    </form>
</x-delete>

<x-edit>
    <form id="editdataform" method="POST" action="{{ route('user.update') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="editid" id="editid" value="">
        <div class="mb-3">
            <label for="editemail" class="form-label">{{ __('text.email') }}</label>
            <input type="email" class="form-control" id="editemail" name="email" placeholder="{{ __('text.email') }}" disabled>
        </div>
        <div class="mb-3">
            <label for="editname" class="form-label">{{ __('text.name') }}</label>
            <input type="text" class="form-control" id="editname" name="name" placeholder="{{ __('text.name') }}" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">{{ __('text.password') }}</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="{{ __('text.password') }}" >
        </div>
    </form>
</x-edit>

    
    

<div class="modal fade" id="AssignModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <!-- Modal content -->
    <div class="modal-dialog modal-lg">
        <div class="modal-content ">
            <form id="editdataform" method="POST" action="{{ route('user.role') }}">
                    @csrf
                <!-- Modal header -->
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{__('text.assign_role')}} <span class="username"></span></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <!-- Modal body -->
                
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">  
                                <p class="mb-2"><strong>{{__('text.stock')}}</strong></p>
                                <div class="d-flex align-items-center">
                                    <input id="stockview" name="stock[]" type="checkbox" value="1" class="me-2">
                                    <label for="stockview" class="font-small">{{__('text.view')}}</label>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input id="stockcreate" name="stock[]" type="checkbox" value="2" class="me-2">
                                    <label for="stockcreate" class="font-small">{{__('text.create')}}</label>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input id="stockedit" name="stock[]" type="checkbox" value="3" class="me-2">
                                    <label for="stockedit">{{__('text.edit')}}</label>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input id="stockdelete" name="stock[]" type="checkbox" value="4" class="me-2">
                                    <label for="stockdelete">{{__('text.delete')}}</label>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input id="stockeditqty" name="stock[]" type="checkbox" value="7" class="me-2">
                                    <label for="stockeditqty">{{__('text.edit_qty')}}</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-2"><strong>{{__('text.checkin')}}</strong></p>
                                <div class="d-flex align-items-center">
                                    <input id="purchaseview" name="purchase[]" type="checkbox" value="1" class="me-2">
                                    <label for="purchaseview" class="font-small">{{__('text.view')}}</label>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input id="purchasecreate" name="purchase[]" type="checkbox" value="2" class="me-2">
                                    <label for="purchasecreate" class="font-small">{{__('text.create')}}</label>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input id="purchaseedit" name="purchase[]" type="checkbox" value="3" class="me-2">
                                    <label for="purchaseedit">{{__('text.edit')}}</label>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input id="purchasedelete" name="purchase[]" type="checkbox" value="4" class="me-2">
                                    <label for="purchasedelete">{{__('text.delete')}}</label>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input id="purchaseseehidden" name="purchase[]" type="checkbox" value="6" class="me-2">
                                    <label for="purchaseseehidden">{{__('text.see_hidden')}}</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-2"><strong>{{__('text.checkout')}}</strong></p>
                                <div class="d-flex align-items-center">
                                    <input id="transactionview" name="transaction[]" type="checkbox" value="1" class="me-2">
                                    <label for="transactionview" class="font-small">{{__('text.view')}}</label>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input id="transactioncreate" name="transaction[]" type="checkbox" value="2" class="me-2">
                                    <label for="transactioncreate" class="font-small">{{__('text.create')}}</label>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input id="transactionedit" name="transaction[]" type="checkbox" value="3" class="me-2">
                                    <label for="transactionedit">{{__('text.edit')}}</label>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input id="transactiondelete" name="transaction[]" type="checkbox" value="4" class="me-2">
                                    <label for="transactiondelete">{{__('text.delete')}}</label>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input id="transactionseehidden" name="transaction[]" type="checkbox" value="6" class="me-2">
                                    <label for="transactionseehidden">{{__('text.see_hidden')}}</label>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-4">
                                <p class="mb-2"><strong>{{__('text.warehouse')}}</strong></p>
                                <div class="d-flex align-items-center">
                                    <input id="warehouseview" name="warehouse[]" type="checkbox" value="1" class="me-2">
                                    <label for="warehouseview" class="font-small">{{__('text.view')}}</label>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input id="warehousecreate" name="warehouse[]" type="checkbox" value="2" class="me-2">
                                    <label for="warehousecreate" class="font-small">{{__('text.create')}}</label>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input id="warehouseedit" name="warehouse[]" type="checkbox" value="3" class="me-2">
                                    <label for="warehouseedit">{{__('text.edit')}}</label>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input id="warehousedelete" name="warehouse[]" type="checkbox" value="4" class="me-2">
                                    <label for="warehousedelete">{{__('text.delete')}}</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-2"><strong>{{__('text.category')}}</strong></p>
                                <div class="d-flex align-items-center">
                                    <input id="categoryview" name="category[]" type="checkbox" value="1" class="me-2">
                                    <label for="categoryview" class="font-small">{{__('text.view')}}</label>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input id="categorycreate" name="category[]" type="checkbox" value="2" class="me-2">
                                    <label for="categorycreate" class="font-small">{{__('text.create')}}</label>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input id="categoryedit" name="category[]" type="checkbox" value="3" class="me-2">
                                    <label for="categoryedit">{{__('text.edit')}}</label>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input id="categorydelete" name="category[]" type="checkbox" value="4" class="me-2">
                                    <label for="categorydelete">{{__('text.delete')}}</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-2"><strong>{{__('text.shelf')}}</strong></p>
                                <div class="d-flex align-items-center">
                                    <input id="shelfview" name="shelf[]" type="checkbox" value="1" class="me-2">
                                    <label for="shelfview" class="font-small">{{__('text.view')}}</label>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input id="shelfcreate" name="shelf[]" type="checkbox" value="2" class="me-2">
                                    <label for="shelfcreate" class="font-small">{{__('text.create')}}</label>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input id="shelfedit" name="shelf[]" type="checkbox" value="3" class="me-2">
                                    <label for="shelfedit">{{__('text.edit')}}</label>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input id="shelfdelete" name="shelf[]" type="checkbox" value="4" class="me-2">
                                    <label for="shelfdelete">{{__('text.delete')}}</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-2"><strong>{{__('text.unit')}}</strong></p>
                                <div class="d-flex align-items-center">
                                    <input id="unitview" name="unit[]" type="checkbox" value="1" class="me-2">
                                    <label for="unitview" class="font-small">{{__('text.view')}}</label>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input id="unitcreate" name="unit[]" type="checkbox" value="2" class="me-2">
                                    <label for="unitcreate" class="font-small">{{__('text.create')}}</label>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input id="unitedit" name="unit[]" type="checkbox" value="3" class="me-2">
                                    <label for="unitedit">{{__('text.edit')}}</label>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input id="unitdelete" name="unit[]" type="checkbox" value="4" class="me-2">
                                    <label for="unitdelete">{{__('text.delete')}}</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-2"><strong>{{__('text.size')}}</strong></p>
                                <div class="d-flex align-items-center">
                                    <input id="sizeview" name="size[]" type="checkbox" value="1" class="me-2">
                                    <label for="sizeview" class="font-small">{{__('text.view')}}</label>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input id="sizecreate" name="size[]" type="checkbox" value="2" class="me-2">
                                    <label for="sizecreate" class="font-small">{{__('text.create')}}</label>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input id="sizeedit" name="size[]" type="checkbox" value="3" class="me-2">
                                    <label for="sizeedit">{{__('text.edit')}}</label>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input id="sizedelete" name="size[]" type="checkbox" value="4" class="me-2">
                                    <label for="sizedelete">{{__('text.delete')}}</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-2"><strong>{{__('text.vat')}}</strong></p>
                                <div class="d-flex align-items-center">
                                    <input id="vatview" name="vat[]" type="checkbox" value="1" class="me-2">
                                    <label for="vatview" class="font-small">{{__('text.view')}}</label>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input id="vatcreate" name="vat[]" type="checkbox" value="2" class="me-2">
                                    <label for="vatcreate" class="font-small">{{__('text.create')}}</label>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input id="vatedit" name="vat[]" type="checkbox" value="3" class="me-2">
                                    <label for="vatedit">{{__('text.edit')}}</label>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input id="vatdelete" name="vat[]" type="checkbox" value="4" class="me-2">
                                    <label for="vatdelete">{{__('text.delete')}}</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-2"><strong>{{__('text.customers')}}</strong></p>
                                <div class="d-flex align-items-center">
                                    <input id="customerview" name="customer[]" type="checkbox" value="1" class="me-2">
                                    <label for="customerview" class="font-small">{{__('text.view')}}</label>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input id="customercreate" name="customer[]" type="checkbox" value="2" class="me-2">
                                    <label for="customercreate" class="font-small">{{__('text.create')}}</label>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input id="customeredit" name="customer[]" type="checkbox" value="3" class="me-2">
                                    <label for="customeredit">{{__('text.edit')}}</label>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input id="customerdelete" name="customer[]" type="checkbox" value="4" class="me-2">
                                    <label for="customerdelete">{{__('text.delete')}}</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-2"><strong>{{__('text.suppliers')}}</strong></p>
                                <div class="d-flex align-items-center">
                                    <input id="supplierview" name="supplier[]" type="checkbox" value="1" class="me-2">
                                    <label for="supplierview" class="font-small">{{__('text.view')}}</label>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input id="suppliercreate" name="supplier[]" type="checkbox" value="2" class="me-2">
                                    <label for="suppliercreate" class="font-small">{{__('text.create')}}</label>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input id="supplieredit" name="supplier[]" type="checkbox" value="3" class="me-2">
                                    <label for="supplieredit">{{__('text.edit')}}</label>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input id="supplierdelete" name="supplier[]" type="checkbox" value="4" class="me-2">
                                    <label for="supplierdelete">{{__('text.delete')}}</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-2"><strong>{{__('text.user_role')}}</strong></p>
                                <div class="d-flex align-items-center">
                                    <input id="userview" name="user[]" type="checkbox" value="1" class="me-2">
                                    <label for="userview" class="font-small">{{__('text.view')}}</label>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input id="usercreate" name="user[]" type="checkbox" value="2" class="me-2">
                                    <label for="usercreate" class="font-small">{{__('text.create')}}</label>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input id="useredit" name="user[]" type="checkbox" value="3" class="me-2">
                                    <label for="useredit">{{__('text.edit')}}</label>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input id="userdelete" name="user[]" type="checkbox" value="4" class="me-2">
                                    <label for="userdelete">{{__('text.delete')}}</label>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input id="userassign" name="user[]" type="checkbox" value="5" class="me-2">
                                    <label for="userassign">{{__('text.assign')}}</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-2"><strong>{{__('text.activity_log')}}</strong></p>
                                <div class="d-flex align-items-center">
                                    <input id="activityview" name="activity[]" type="checkbox" value="1" class="me-2">
                                    <label for="activityview" class="font-small">{{__('text.view')}}</label>
                                </div>
                                
                            </div>
                            <div class="col-md-4">
                                <p class="mb-2"><strong>{{__('text.settings')}}</strong></p>
                                
                                <div class="d-flex align-items-center">
                                    <input id="settingsedit" name="settings[]" type="checkbox" value="3" class="me-2">
                                    <label for="settingsedit">{{__('text.edit')}}</label>
                                </div>
                                
                            </div>
                            <div class="col-md-4">
                                <p class="mb-2"><strong>{{__('text.reports')}}</strong></p>
                                <div class="d-flex align-items-center">
                                    <input id="reportsview" name="reports[]" type="checkbox" value="1" class="me-2">
                                    <label for="reportsview" class="font-small">{{__('text.view')}}</label>
                                </div>
                            
                            </div>
                            <input type="hidden" name="userid" id="userid">
                        </div>
                    </div>
                
                <!-- Modal footer --> 
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary d-flex align-items-center" data-bs-dismiss="modal"> <span class="material-symbols-rounded">close</span>{{__('text.close')}}</button>
                    <button type="submit" class="btn btn-primary d-flex align-items-center" id="modalsaveassign"><span class="material-symbols-rounded">check</span> {{__('text.submit')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script type="module">
        $(function() {

            $('#data').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{!! route('user.get') !!}',
                dom: '<"d-flex align-items-md-center flex-column flex-md-row justify-content-md-between pb-3"Bf>rt<"pt-3 d-flex align-items-md-center flex-column flex-md-row justify-content-md-between"lp><"clear">',
                language: {
                    url: langUrl // Polish language JSON file
                },
                columns: [
                    { data: 'id', name: 'id', orderable: false, searchable: false, visible: false },
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                buttons: [{
                    extend: 'copy',
                    text: '<div class="d-flex align-items-center"><span class="material-symbols-rounded">file_copy</span> Copy</div>',
                    className: 'btn btn-sm btn-fill btn-info ',
                    title: 'User Data',
                    exportOptions: {
                        columns: [1, 2]
                    }
                },
                {
                    extend: 'csv',
                    text: '<div class="d-flex align-items-center"><span class="material-symbols-rounded">text_snippet</span> CSV</div>',
                    className: 'btn btn-sm btn-fill btn-info ',
                    title: 'User Data',
                    exportOptions: {
                        columns: [1, 2]
                    }
                },
                {
                    extend: 'pdf',
                    text: '<div class="d-flex align-items-center"><span class="material-symbols-rounded">picture_as_pdf</span> PDF</div>',
                    className: 'btn btn-sm btn-fill btn-info ',
                    title: 'User Data',
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


        $('#AssignModal').on('show.bs.modal', function(event) {
                
                // Get the group ID from the data attribute
                var button = $(event.relatedTarget);
                var DataId = button.data('asignid');

                //clear first
                $('#AssignModal :checkbox').prop('checked', false);
                // Use an AJAX request to fetch the data for the given group
                $.ajax({
                    url: 'user/getrolebyid/' + DataId, // Replace with your actual route
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        // Populate the form fields with the retrieved data
                       
                        //set userid
                        $("#userid").val(DataId);

                        data.forEach(function (permission) {
                            if (permission.permission !== null && permission.permission !== undefined) {
                                $('#' + permission.module + 'view').prop('checked', permission.permission.includes('1'));
                                $('#' + permission.module + 'create').prop('checked', permission.permission.includes('2'));
                                $('#' + permission.module + 'edit').prop('checked', permission.permission.includes('3'));
                                $('#' + permission.module + 'delete').prop('checked', permission.permission.includes('4'));
                                $('#' + permission.module + 'assign').prop('checked', permission.permission.includes('5'));
                                $('#' + permission.module + 'seehidden').prop('checked', permission.permission.includes('6'));
                                $('#' + permission.module + 'editqty').prop('checked', permission.permission.includes('7'));
                            }
                        });
                       
                    },
                    error: function() {
                        // Handle errors if needed
                    }
                });
            });

        // Triggered when the "Edit" button is clicked
        $('#EditModal').on('show.bs.modal', function(event) {
                
                // Get the group ID from the data attribute
                var button = $(event.relatedTarget);
                var Id = button.data('editid');


                // Use an AJAX request to fetch the data for the given group
                $.ajax({
                    url: 'user/' + Id, // Replace with your actual route
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                       
                        // Populate the form fields with the retrieved data
                        $('#editid').val(data.id);
                        $('#editname').val(data.name);
                        $('#editemail').val(data.email);
                    },
                    error: function() {
                        // Handle errors if needed
                    }
                });
            });
           

        // Initialize jQuery Validation
        $('#adddataform').validate({
            messages: {
                name: {
                    required: '{!!__('text.field_required')!!}'
                },
                email: {
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

