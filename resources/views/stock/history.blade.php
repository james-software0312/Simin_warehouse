@extends('layouts.app')
@section('title', __('text.history'))

@section('content')

<div class="body-inner">
    
    <div class="alert alert-success alert-dismissible fade show  align-items-center row" role="alert">
        <div class="col-md-4 d-flex     align-items-center">                
            <img src="{{$photo}}" class="photodetail img-fluid" width="40" style="height: 40px;" />  &nbsp;&nbsp;&nbsp;
            <span class="me-4" style="font-size: large;"><strong>{{$data->name}}</strong></span> 
        </div>
        <div class="col-md-4">
            <span class="me-4"><strong>{{__('text.category')}}:</strong></span> 
            <span class="me-4"> {{ $data->category }} </span>
        </div>
        <div class="col-md-4">
            <span class="me-4"><strong>{{__('text.sub_type')}}:</strong></span> 
            <span class="me-4"> {{ $data->itemsubtype }} </span>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
        <span class="material-symbols-rounded">task_alt</span> {{ session('success') }}
    </div>
    @endif
    <?php
        $isCheckIn = $modules->contains(function($module) {
            return ($module->module == 'purchase') && ($module->hasViewPermission || $module->hasEditPermission);
        });

        $isCheckOut = $modules->contains(function($module) {
            return ($module->module == 'transaction') && ($module->hasViewPermission || $module->hasEditPermission);
        });
    ?>
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a href="{{route('stock.edit', ['id' => $stockitemid])}}" class="nav-link" aria-current="page" href="#">{{__('text.edit_stock_item')}}</a>
        </li>
        <li class="nav-item">
            <a href="{{route('stock.history', ['id' => $stockitemid])}}" class="nav-link active" href="#" tabindex="-1" aria-disabled="true">{{__('text.history')}}</a>
        </li>
        @if($isCheckOut)
        <li class="nav-item">
            <a href="{{route('stock.sellpricehistory', ['id' => $stockitemid])}}" class="nav-link" href="#" tabindex="-1" aria-disabled="true">{{__('text.selling_price_history')}}</a>
        </li>
        @endif
        @if($isCheckIn)
        <li class="nav-item">
            <a href="{{route('stock.purchasepricehistory', ['id' => $stockitemid])}}" class="nav-link" href="#" tabindex="-1" aria-disabled="true">{{__('text.purchase_price_history')}}</a>
        </li>
        @endif
        <li class="nav-item">
            <a href="{{route('stock.pricehistory', ['id' => $data->id])}}" class="nav-link" href="#" tabindex="-1" aria-disabled="true">{{__('text.price_history')}}</a>
        </li>
    </ul>
    <div class="table-responsive-sm">
        <table class="table " id="data">
            <thead>
                <tr>
                    <th>{{__('text.id')}}</th>
                    <th>{{__('text.reference')}}</th>
                    <th>{{__('text.type')}}</th>
                    <th>{{__('text.date')}}</th>
                    <th>{{__('text.customer')}}</th>
                    <th>{{__('text.quantity')}}</th>
                    <th>{{__('text.quantity_packed')}}</th>
                    <th>{{__('text.price')}}</th>
                </tr>
            </thead>
        </table>
    </div>
</div>




<x-delete>
    <form id="deletedataform" method="POST" action="{{ route('transaction.destroy')}}">
        @csrf
        <input type="hidden" name="deleteid" id="deleteid" value="">
        <input type="hidden" name="status" id="status" value="1">


    </form>
</x-delete>




<!-- Modal -->
<div class="modal fade" id="detailModel" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <!-- Modal content -->
    <div class="modal-dialog modal-lg">
        <div class="modal-content ">
            <!-- Modal header -->
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{__('text.checkin_detail')}}</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <!-- Modal body -->
            <div class="modal-body overflow-scroll ">
                <div class="p-4">

                    <div class="row border-bottom mb-3 pb-4">
                        <div class="col-md-2 mb-3">
                            <img src="" alt="barcode" width="100" class="qrcodedetail" />
                        </div>
                        <div class="col-md-3 mb-3">
                            <small class="mb-0 text-neutral-80"><strong>{{__('text.reference')}}:</strong></small>
                            <p class="mb-0 referencecontent">{{__('text.reference')}}</p>
                        </div>
                        <div class="col-md-3 mb-3">
                            <small class="mb-0 text-neutral-80"><strong>{{__('text.date')}}:</strong></small>
                            <p class="mb-0 datecontent">{{__('text.date')}}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <small class="mb-0 text-neutral-80"><strong>{{__('text.supplier')}}:</strong></small>
                            <p class="mb-0 suppliercontent">{{__('text.supplier')}}</p>
                            <p class="mb-0 companycontent">{{__('text.company')}}</p>
                            <p class="mb-0 emailcontent">{{__('auth.email')}}</p>
                        </div>
                    </div>



                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <table border="0" width="100%" class="table ">
                                <thead>
                                    <tr>
                                        <th width="80%"><small class="text-neutral-80">{{__('text.item_name')}}</small></th>
                                        <th width="6%"><small class="text-neutral-80">{{__('text.quantity')}}</small></th>
                                    </tr>
                                </thead>
                                <tbody id="datalistpopup">

                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <small class="mb-0 text-neutral-80"><strong>{{__('text.description')}}:</strong></small>
                            <p class="descriptioncontent">description here</p>
                        </div>
                    </div>

                    <div class="row mt-4">


                        <div class="col-md-12">
                            <small class="mb-0 text-neutral-80"><em><strong>{{__('text.created_at')}}:</strong> <span
                                        class="createdatecontent">22222</span></em></small>

                        </div>
                    </div>

                </div>




            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
                <button class="btn btn-primary d-flex align-items-center" id="printButton"><span
                        class="material-symbols-rounded">print</span> {{__('text.print')}}</button>
                <button type="button" class="btn btn-secondary d-flex align-items-center" data-bs-dismiss="modal"> <span
                        class="material-symbols-rounded">
                        close
                    </span>{{__('text.close')}}</button>

            </div>
        </div>
    </div>
</div>
@push('scripts')
<script type="module">
$(function() {

    function updateTable(data) {
        var tbody = $('#datalistpopup');

        // Clear existing rows
        tbody.empty();

        // Loop through the data and create new rows
        $.each(data, function(index, item) {
            var row = $('<tr><td>' + item.name + '</td><td>' + item.quantity + '</td></tr>');
            // Add other cells as needed
            tbody.append(row);
        });
    }


    $('#detailModel').on('show.bs.modal', function(event) {

        // Get the group ID from the data attribute
        var button = $(event.relatedTarget);
        var DataId = button.data('btndetail');
        // Use an AJAX request to fetch the data for the given group
        $.ajax({
            url: '/transaction/detail/' + DataId, // Replace with your actual route
            type: 'GET',
            dataType: 'json',
            data: {
                status: 1
            },
            success: function(data) {
                // Populate the form fields with the retrieved data
                $('.qrcodedetail').attr('src', data.dataURL);
                $('.referencecontent').html(data.data[0].reference);
                $('.datecontent').html(data.transactiondate[0]);
                $('.suppliercontent').html(data.data[0].supplier);
                $('.companycontent').html(data.data[0].suppliercompany);
                $('.emailcontent').html(data.data[0].supplieremail);
                $('.descriptioncontent').html(data.data[0].description);
                $('.createdatecontent').html(data.created_at[0]);
                $('.warehousedetail').html(data.data.warehouse);
                updateTable(data.data);

            },
            error: function() {
                // Handle errors if needed
            }
        });
    });


    // Triggered when the "Edit" button is clicked


    $('#data').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{!! route('transaction.getHistory') !!}?stockitemid={{ $stockitemid }}",
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
                data: 'reference',
                name: 'reference'
            },
            {
                data: 'type',
                name: 'type',
                render: function(data, type, row) {
                    if (data == 'sell') {
                        return '<span class="bg-green text-white p-1 rounded">{{ __("text.checkout") }}</span>';
                    } else if (data == 'purchase') {
                        return '<span class="bg-primary text-white p-1 rounded">{{ __("text.checkin") }}</span>';
                    } else if (data == 'movement_in') {
                        return '<span class="bg-neutral-80 text-white p-1 rounded">{{ __("text.movement_in") }}</span>';
                    } else if (data == 'movement_out') {
                        
                        return '<span class="bg-orange text-white p-1 rounded">{{ __("text.movement_out") }}</span>';
                    } else {
                        return 'Unknown';
                    }
                }
            },
            {
                data: 'date',
                name: 'date'
            },
            {
                data: 'contactname',
                name: 'contactname'
            },
            {
                data: 'quantity',
                name: 'quantity'
            },
            {
                data: 'converted_quantity',
                name: 'converted_quantity'
            },
            {
                data: 'price',
                name: 'price'
            },
        ],
        buttons: [{
                extend: 'csv',
                text: '<div class="d-flex align-items-center"><span class="material-symbols-rounded">text_snippet</span> CSV</div>',
                className: 'btn btn-sm btn-fill btn-info ',
                title: 'Checkin Data',
                exportOptions: {
                    columns: [1, 2, 3, 4]
                }
            },
            {
                extend: 'pdf',
                text: '<div class="d-flex align-items-center"><span class="material-symbols-rounded">picture_as_pdf</span> PDF</div>',
                className: 'btn btn-sm btn-fill btn-info ',
                title: 'Checkin Data',
                orientation: 'landscape',
                exportOptions: {
                    columns: [1, 2, 3, 4]
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

//print button

$("#printButton").on('click', function() {
    // Print the modal content
    window.print();
});

// Initialize jQuery Validation

$('#editdataform').validate({
    rules: {
        code: {
            required: true,
            uniquecodeedit: true
        },
    },

    submitHandler: function(form) {
        form.submit();
    }
});
</script>
@endpush
@endsection