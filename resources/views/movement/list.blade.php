@extends('layouts.app')
@section('title', __('text.movement_list'))

@section('content')

<div class="body-inner">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
        <span class="material-symbols-rounded">task_alt</span> {{ session('success') }}
    </div>
    @endif
    <div class="mb-4 d-flex align-items-center justify-content-between">
        <h2>{{__('text.movement_list')}}</h2>
        <div class="text-end">
            <div class="d-flex">
                <!-- <a href="#" class="btn btn-green d-flex align-items-center" id="btn_download" style="margin-right: 10px"> -->
                    <!-- <span class="material-symbols-rounded">download</span>{{ __('text.download') }}</a> -->
                <a href="{{route('movement.create')}}" class="btn btn-primary d-flex align-items-center">
                    <span class="material-symbols-rounded">add</span>{{__('text.add')}}</a>
            </div>
        </div>
    </div>
    <!-- filter -->
    <div class="border-top pt-2">
        <h4 class="mb-2">{{__('text.filter_data')}}</h4>
        <form id="filter_checkinlist" method="POST" class="mb-4">
            @csrf
            <div class="row">
                <div class="col-md-4">
                    <div class="">
                        <label for="keyword" class="form-label">{{__('text.search')}}</label>
                        <input type="text" id="keyword" name="keyword" class="form-control"
                            placeholder="{{__('text.search_transaction')}}" />
                        <label for="keyword" class="error"></label>
                    </div>
                </div>
                <div class="col-md-2">
                    <label for="startdate" class="form-label">{{__('text.start_date')}}</label>
                    <input type="date" class="form-control" id="startdate" name="startdate">
                </div>
                <div class="col-md-2">
                    <label for="enddate" class="form-label">{{__('text.end_date')}}</label>
                    <input type="date" disabled class="form-control" id="enddate" name="enddate">
                </div>

                <div class="col-md-2">
                    <label for="source_warehouse" class="form-label">{{__('text.source_warehouse')}}</label>
                    <select name="source_warehouse" id="source_warehouse" class="form-control">
                        <option value="">{{__('text.select')}}</option>
                        @foreach($warehouses as $item)
                        <option value='{{$item->id}}'>{{$item->name}}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="target_warehouse" class="form-label">{{__('text.target_warehouse')}}</label>
                    <select name="target_warehouse" id="target_warehouse" class="form-control">
                        <option value="">{{__('text.select')}}</option>
                        @foreach($warehouses as $item)
                        <option value='{{$item->id}}'>{{$item->name}}</option>
                        @endforeach
                    </select>
                </div>

            </div>
            <div class="row mt-2">
                <div class="col-md-4">
                    <div class="mb-3 d-flex">
                        <button type="input" class="btn btn-primary d-flex align-items-center" id="button"><span class="material-symbols-rounded">check</span> {{__('text.apply_filters')}}</button>
                        <button type="input" class="btn btn-yellow d-flex align-items-center" style="margin-left: 5px" id="btn_reset"><span class="material-symbols-rounded">check</span> {{__('text.reset')}}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <!-- end filter -->
    <div class="table-responsive-sm">
        <table class="table " id="data">
            <thead>
                <tr>
                    <th>{{__('text.id')}}</th>
                    <th>{{__('text.reference')}}</th>
                    <th>{{__('text.source_warehouse')}}</th>
                    <th>{{__('text.target_warehouse')}}</th>
                    <th>{{__('text.date')}}</th>
                    <th>{{__('text.total_quantity')}}</th>
                    <th>{{__('text.total_price')}}</th>
                    <th>{{__('text.creator')}}</th>
                    <th>{{__('text.action')}}</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>




<x-delete>
    <form id="deletedataform" method="POST" action="{{ route('movement.destroy')}}">
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
                <h5 class="modal-title" id="exampleModalLabel">{{__('text.movement_detail')}}</h5>
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
                            <small class="mb-0 text-neutral-80"><strong>{{__('text.source_warehouse')}}:</strong></small>
                            <p class="mb-0 source_warehouse_name_p"></p>
                            <small class="mb-0 text-neutral-80"><strong>{{__('text.target_warehouse')}}:</strong></small>
                            <p class="mb-0 target_warehouse_name_p"></p>
                        </div>
                    </div>



                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <table border="0" width="100%" class="table ">
                                <thead>
                                    <tr>
                                        <th width="80%"><small class="text-neutral-80">{{__('text.item_name')}}</small></th>
                                        <th width="6%"><small class="text-neutral-80">{{__('text.quantity')}}</small></th>
                                        <th width="6%"><small class="text-neutral-80">{{__('text.unit')}}</small></th>
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
    $("#source_warehouse").select2();
    $("#target_warehouse").select2();
    flatpickr("#startdate", {
        locale: currentLang, // Set the locale to Polish
        dateFormat: "d/m/Y" // Format to match your backend expectations
    });

    flatpickr("#enddate", {
        locale: currentLang, // Set the locale to Polish
        dateFormat: "d/m/Y" // Format to match your backend expectations
    });
    function updateTable(data) {
        var tbody = $('#datalistpopup');

        // Clear existing rows
        tbody.empty();

        // Loop through the data and create new rows
        $.each(data, function(index, item) {
            var row = $('<tr><td>' + item.name + '</td><td><span>' + item.quantity + '</span></td><td><span>' + item.movement_unitname + '</span></td></tr>');
            // var baseUnit = item.unitid == item.stockitem_unitid ? item.stock_base_unit_name : item.stock_converted_unit_name;
            // var convertedUnit = item.unitid != item.stockitem_unitid ? item.stock_base_unit_name : item.stock_converted_unit_name;

            // var row = $('<tr><td>' + item.name + '</td><td><span>' + item.quantity + "&nbsp;" + baseUnit + '</span></td><td><span>' + item.converted_quantity + "&nbsp;" + convertedUnit + '</span></td></tr>');
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
            url: '/warehouse/movement/detail/' + DataId, // Replace with your actual route
            type: 'GET',
            dataType: 'json',
            data: {
                status: 1
            },
            success: function(data) {
                // Populate the form fields with the retrieved data
                $('.qrcodedetail').attr('src', data.dataURL);
                $('.referencecontent').html(data.data[0].reference);
                $('.datecontent').html(data.movement_date[0]);
                $('.source_warehouse_name_p').html(data.data[0].source_warehouse_name);
                $('.target_warehouse_name_p').html(data.data[0].target_warehouse_name);
                $('.descriptioncontent').html(data.data[0].description);
                $('.createdatecontent').html(data.created_at[0]);
                updateTable(data.data);

            },
            error: function() {
                // Handle errors if needed
            }
        });
    });


    // Triggered when the "Edit" button is clicked


    var movementlistTable = $('#data').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{!! route('movement.get') !!}',
            data: function(d) {
                d.keyword = $('input[name=keyword]').val();
                d.startdate = $('input[name=startdate]').val();
                d.enddate = $('input[name=enddate]').val();
                d.source_warehouse = $('#source_warehouse').val();
                d.target_warehouse = $('#target_warehouse').val();
            }
        },
        drawCallback: function() {
            var api = this.api();
            // Get total quantity and total price from the server-side response
            var response = api.ajax.json();
            console.log(response.total_quantity, response.total_price)
            $("#total_quantity").text(response.total_quantity)
            $("#total_price").text(response.total_price)
        },
        dom: '<"d-flex align-items-md-center flex-column flex-md-row justify-content-md-between pb-3"Bf>rt<"pt-3 d-flex align-items-md-center flex-column flex-md-row justify-content-md-between"lp><"clear">',
        language: {
            url: langUrl // Polish language JSON file
        },
        bFilter: false,
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
                data: 'source_warehouse_name',
                name: 'source_warehouse_name'
            },
            {
                data: 'target_warehouse_name',
                name: 'target_warehouse_name'
            },
            {
                data: 'movement_date',
                name: 'date'
            },
            {
                data: 'total_quantity',
                name: 'total_quantity'
            },
            {
                data: 'total_price',
                name: 'total_price'
            },
            {
                data: 'creator',
                name: 'creator'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }
        ],
        buttons: [
            // {
            //     extend: 'csv',
            //     text: '<div class="d-flex align-items-center"><span class="material-symbols-rounded">text_snippet</span> CSV</div>',
            //     className: 'btn btn-sm btn-fill btn-info ',
            //     title: 'Checkin Data',
            //     exportOptions: {
            //         columns: [1, 2, 3, 4]
            //     }
            // },
            // {
            //     extend: 'pdf',
            //     text: '<div class="d-flex align-items-center"><span class="material-symbols-rounded">picture_as_pdf</span> PDF</div>',
            //     className: 'btn btn-sm btn-fill btn-info ',
            //     title: 'Checkin Data',
            //     orientation: 'landscape',
            //     exportOptions: {
            //         columns: [1, 2, 3, 4]
            //     },
            //     customize: function(doc) {
            //         doc.styles.tableHeader.alignment = 'left';
            //         doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1)
            //             .join('*').split('');
            //     }
            // }
        ]
    });

    //general search report
    $('#filter_checkinlist').on('submit', function(e) {
        e.preventDefault();
        movementlistTable.draw();
    });

    $("#btn_reset").on('click', function() {
        $('input[name=keyword]').val('');
        $('input[name=startdate]').val('');
        $('input[name=enddate]').val('');
        $('#supplier').val('').trigger('change');
        $('#source_warehouse').val('').trigger('change');
        $('#target_warehouse').val('').trigger('change');
        movementlistTable.draw();
    });

    $('#startdate').on('input', function() {
        // Check if the start date field is not empty
        if ($(this).val().trim() !== '') {
            $('#enddate').prop('disabled', false);
        } else {
            $('#enddate').prop('disabled', true);
            $('#enddate').prop('required', true);
        }
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

$("#btn_download").on('click', function() {
    var keyword = $('input[name=keyword]').val();
    var startdate = $('input[name=startdate]').val();
    var enddate = $('input[name=enddate]').val();
    var downloadUrl = `{!! route('movement.export') !!}?keyword=` + encodeURIComponent(keyword) + 
                      `&startdate=` + encodeURIComponent(startdate) + 
                      `&enddate=` + encodeURIComponent(enddate);

    // Trigger a file download by changing the window location
    window.location.href = downloadUrl;
})



</script>
@endpush
@endsection