@extends('layouts.app')
@section('title', __('text.stock_items'))

@section('content')

<style>
    .fixed-button {
        position: fixed;
        bottom: 20px; /* Adjust this value to move the button up or down */
        right: 20px;  /* Adjust this value to move the button left or right */
    }
    #data tbody tr {
    cursor: pointer;
}

</style>

<div class="body-inner">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
            <span class="material-symbols-rounded">task_alt</span> {{ session('success') }}
        </div>
    @endif
    <div class="mb-2 d-flex align-items-center justify-content-between">
        <h2>{{__('text.stock_items')}}</h2>
        <div class="text-end d-flex">
            <a href="{{route('stock.add')}}" class="btn btn-primary d-flex align-items-center">
                <span class="material-symbols-rounded">add</span>{{__('text.add')}}
            </a>
            <a class="btn btn-red d-flex align-items-center d-none"  data-bs-toggle="modal" data-bs-target="#MultiDeleteModal"   href="#"  id="btn_delete"  style="margin-left: 10px">
                <span class="material-symbols-rounded">delete</span>{{ __('text.delete') }}
            </a>
            <a class="btn btn-green d-flex align-items-center d-none" href="#"  id="btn_print_qr"  style="margin-left: 10px">
                <span class="material-symbols-rounded">print</span>{{ __('text.print_qr_code') }}
            </a>
        </div>
    </div>
    <div class="border-top pt-2">
        <h4 class="mb-2">{{__('text.filter_data')}}</h4>
        <form id="formstockitem" method="POST" class="mb-4">
            @csrf
            <div class="row">
                <div class="col-md-3">
                    <div class="">
                        <label for="keyword" class="form-label">{{__('text.search')}}</label>
                        <input type="text" id="keyword" name="keyword" class="form-control"
                            placeholder="{{__('text.search_item')}}" />
                        <label for="keyword" class="error"></label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="">
                        <label for="subtype" class="form-label">{{__('text.search_sub_category')}}</label>
                        <input type="text" id="subtype" name="subtype" class="form-control"
                            placeholder="{{__('text.search_sub_category')}}" />
                        <label for="subtype" class="error"></label>
                    </div>
                </div>
                <div class="col-md-2">
                    <label for="startdate" class="form-label">{{__('text.start_date')}}</label>
                    <input type="date" class="form-control" id="startdate" name="startdate">
                </div>
                <div class="col-md-2">
                    <label for="enddate" class="form-label">{{__('text.end_date')}}</label>
                    <input type="date" disabled class="form-control" id="enddate" name="enddate" required>
                </div>

                <div class="col-md-2">
                    <label for="supplier" class="form-label">{{__('text.supplier')}}</label>
                    <select name="supplier" id="supplier" class="form-control">
                        <option value="">{{__('text.select')}}</option>
                        @foreach($warehouses as $item)
                        <option value='{{$item->id}}'>{{$item->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="filterby" class="form-label">{{__('text.filterby')}}</label>
                    <select name="filterby" id="filterby" class="form-control">
                        <option value="">{{__('text.select')}}</option>
                        <option value='order'>{{__('text.by_order')}}</option>
                        <option value='website'>{{__('text.by_website')}}</option>
                        <option value='supplier'>{{__('text.by_supplier')}}</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="isVisible" class="form-label">{{ __('text.show_all') }} </label>
                    <div class="mb-3">
                        <input type="checkbox" data-toggle="switchbutton" id="isVisible" name="isVisible" >
                    </div>
                </div>
                <div class="col-md-2">
                    <label for="isWithoutPhoto" class="form-label">{{ __('text.without_photo_only') }} </label>
                    <div class="mb-3">
                        <input type="checkbox" data-toggle="switchbutton" id="isWithoutPhoto" name="isWithoutPhoto" >
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
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
                    <th></th>
                    <th>{{__('text.id')}}</th>
                    <th class="text-center">{{__('text.photo')}}</th>
                    {{-- <th class="text-center">{{__('text.code')}}</th> --}}
                    <th>{{__('text.name')}}</th>
                    {{-- <th>{{__('text.category')}}</th> --}}
                    <!-- <th>{{__('text.sub_type')}}</th> -->
                    <!-- <th>{{__('text.supplier')}}</th> -->
                    {{-- <th>{{__('text.warehouse')}}</th> --}}
                    <th>{{__('text.selling_price')}}</th>
                    <!-- <th>{{__('text.quantity')}}</th> -->
                    <th>{{__('text.quantity_packed')}}</th>
                    {{-- <th>{{__('text.created_at')}}</th> --}}
                    <th></th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<a href="{{route('stock.add')}}" class="btn btn-primary d-flex align-items-center fixed-button">
    <span class="material-symbols-rounded">add</span>
</a>

<!-- Define the modal content and title -->


    <x-delete>
            <form id="deletedataform" method="POST" action="{{ route('stock.destroy')}}">
                @csrf
                <input type="hidden" name="deleteid" id="deleteid" value="">
            </form>
    </x-delete>

        <!-- Modal -->
    <div class="modal fade" id="detailModel" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <!-- Modal content -->
        <div class="modal-dialog">
            <div class="modal-content ">
                <!-- Modal header -->
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{__('text.detail')}}</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="modal-body overflow-scroll">
                    <div class="">
                        <table width="100%">
                            <tr>
                                <td valign="top" colspan="2">
                                    <center>
                                    <img src="" alt="barcode" width="100"  class="qrcodedetail"/>

                                    <p class="codedetail"></p>
                                    </center>
                                </td>

                            </tr>
                            <tr>
                                <td valign="top" width="20%">
                                    <p class="me-4"><strong>{{__('text.category')}}:</strong></p>
                                </td>
                                <td valign="top">
                                    <p class="categorydetail"></p>
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" width="20%">
                                    <p class="me-4"><strong>{{__('text.quantity')}}:</strong></p>
                                </td>
                                <td valign="top">
                                    <p class=""><span class="quantitydetail"></span> <span class="unitdetail"></span></p>
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" width="20%">
                                    <p class="me-4"><strong>{{__('text.warehouse')}}:</strong></p>
                                </td>
                                <td valign="top">
                                    <p class="warehousedetail"></p>
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" width="20%">
                                    <p class="me-4"><strong>{{__('text.photo')}}:</strong></p>
                                </td>
                                <td valign="top">
                                    <img src="" class="photodetail img-fluid" width="250"/>
                                </td>
                            </tr>
                        </table>

                    </div>
                </div>
                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary d-flex align-items-center" data-bs-dismiss="modal"> <span class="material-symbols-rounded">close</span>{{__('text.close')}}</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="MultiDeleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
        <!-- Modal content -->
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="deletedataform" method="POST" action="{{ route('stock.multidelete')}}">
                <!-- Modal header -->
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ __('text.delete_data') }}</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="modal-body">
                    <p> {{ __('text.confirm_delete') }}</p>
                        @csrf
                        <input type="hidden" name="selected_ids" id="selected_ids" value="">
                </div>
                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary d-flex align-items-center" data-bs-dismiss="modal"> <span class="material-symbols-rounded">
                    close
                    </span>{{__('text.close')}}</button>
                    <button type="submit" class="btn btn-primary d-flex align-items-center" id="modalDeleteButton"><span class="material-symbols-rounded">
                    check
                    </span> {{__('text.submit')}}</button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script type="module">
        var selected_ids = [];
        $(function() {
            jQuery("#editunit").select2();
            flatpickr("#startdate", {
                locale: currentLang, // Set the locale to Polish
                dateFormat: "d/m/Y" // Format to match your backend expectations
            });

            flatpickr("#enddate", {
                locale: currentLang, // Set the locale to Polish
                dateFormat: "d/m/Y" // Format to match your backend expectations
            });
            $('#detailModel').on('show.bs.modal', function(event) {

                // Get the group ID from the data attribute
                var button = $(event.relatedTarget);
                var DataId = button.data('btndetail');
                // Use an AJAX request to fetch the data for the given group
                $.ajax({
                    url: 'stock/' + DataId, // Replace with your actual route
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        // Populate the form fields with the retrieved data

                        $('.qrcodedetail').attr('src', data.dataURL);
                        $('.photodetail').attr('src', data.photo);
                        $('.codedetail').html(data.data.code);
                        $('.categorydetail').html(data.data.category);
                        $('.quantitydetail').html(data.data.quantity);
                        $('.unitdetail').html(data.data.unit);
                        $('.warehousedetail').html(data.data.warehouse);

                    },
                    error: function() {
                        // Handle errors if needed
                    }
                });
            });

            const tablestockitem = $('#data').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{!! route('stock.get') !!}',
                    data: function(d) {
                        d.keyword = $('input[name=keyword]').val();
                        d.subtype = $('input[name=subtype]').val();
                        d.startdate = $('input[name=startdate]').val();
                        d.enddate = $('input[name=enddate]').val();
                        d.supplier = $("#supplier").val();
                        d.filterby = $("#filterby").val();
                        d.isVisible = $("#isVisible").is(':checked') ? 1 : 0;
                        d.isWithoutPhoto = $("#isWithoutPhoto").is(':checked') ? 1 : 0;

                    },
                },
                dom: '<"d-flex align-items-md-center flex-column flex-md-row justify-content-md-between pb-3"Bf>rt<"pt-3 d-flex align-items-md-center flex-column flex-md-row justify-content-md-between"lp><"clear">',
                language: {
                    url: langUrl // Polish language JSON file
                },
                bFilter: false,
                order: [[1, "desc"]],
                columns: [
                    {
                        data: null, // Use null for data as we are generating the checkbox manually
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `<input data-id="${row.id}" data-quantity="${row.single_quantity}" type="checkbox" value="1" class="me-2 row-select">`;
                        }
                    },
                    { data: 'id', name: 'id', orderable: true, searchable: true, visible: true },
                    { data: 'item_photo', name: 'photo' },
                    // { data: 'code', name: 'code' },
                    { data: 'name', name: 'name' },
                    // { data: 'category', name: 'category' },
                    // { data: 'itemsubtype', name: 'itemsubtype' },
                    // { data: 'suppiler', name: 'suppiler' },
                    // { data: 'warehouse', name: 'warehouse' },
                    { data: 'price', name: 'price', render: function(data, type, row) {
                        return `${row.price ? row.price + ' {{__("text.PLN")}}' : 'Undefined'}`
                    } },
                    // { data: 'quantity', name: 'quantity',
                        // render: function(data, type, row) {
                        // let pairText = "";
                        // let cartonText = "";
                        // if (row.unit == "pair") {
                        //     pairText = `<span class="text-red">${row.quantity} ${row.unit}</span>`;
                        //     if (row.unitconverter) {
                        //         cartonText = `<br /> <span class="text-green">${row.quantity / row.unitconverter} carton</span>`;
                        //     }
                        // } else {
                        //     cartonText = `<span class="text-green">${row.quantity} ${row.unit}</span>`;
                        //     if (row.unitconverter) {
                        //         pairText = `<br /><span class="text-red"> ${row.quantity / row.unitconverter} pair</span>`;
                        //     }
                        // }

                        // return pairText + cartonText;
                        // }
                    // },
                    { data: 'convertedQty', name: 'convertedQty'},
                    // { data: 'created_at', name: 'created_at', render: function(data, type, row) {
                    //     const date = new Date(row.created_at);

                    //     const year = date.getFullYear();
                    //     const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are 0-based
                    //     const day = String(date.getDate()).padStart(2, '0');
                    //     const hours = String(date.getHours()).padStart(2, '0');
                    //     const minutes = String(date.getMinutes()).padStart(2, '0');
                    //     const seconds = String(date.getSeconds()).padStart(2, '0');

                    //     const formattedDate = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
                    //     return formattedDate;
                    // } },
                    { data: 'action', name: 'action', orderable: false, searchable: false, class: 'action'}
                ],
                buttons: [
                {
                    extend: 'csv',
                    text: '<div class="d-flex align-items-center"><span class="material-symbols-rounded">text_snippet</span> CSV</div>',
                    className: 'btn btn-sm btn-fill btn-info ',
                    title: 'Stock Data',
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6, 7]
                    }
                },
                {
                    extend: 'pdf',
                    text: '<div class="d-flex align-items-center"><span class="material-symbols-rounded">picture_as_pdf</span> PDF</div>',
                    className: 'btn btn-sm btn-fill btn-info ',
                    title: 'Stock Data',
                    orientation: 'landscape',
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6, 7]
                    },
                    customize: function(doc) {
                        doc.styles.tableHeader.alignment = 'left';
                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1)
                            .join('*').split('');
                    }
                }
            ],
             rowCallback: function(row, data, index) {
                 // Add a click event to the row to redirect to the Edit screen
                $(row).on('click', function() {
                    if (!$(event.target).closest('td').hasClass('action') && !$(event.target).is('input[type="checkbox"]')) {
                        // Redirect to the Edit screen
                        window.location.href = '{{ route("stock.edit", ":id") }}'.replace(':id', data.id);
                    }
                    //  window.location.href = '{{ route("stock.edit", ":id") }}'.replace(':id', data.id);
                });
            }
            });

            let selected_quantity = 0;
            $('#data').on('change', '.row-select', function() {
                var rowId = $(this).data('id');
                var quantity = $(this).data('quantity');
                if ($(this).is(':checked')) {
                    console.log('Row selected:', rowId); // Handle selected row
                    selected_ids.push(rowId)
                    selected_quantity = selected_quantity + quantity;
                } else {
                    console.log('Row deselected:', rowId); // Handle deselected row
                    selected_ids = selected_ids.filter(function(id) {
                        return id !== rowId; // Keep all ids that are not equal to rowId
                    });
                    selected_quantity = selected_quantity - quantity;
                }
                if(selected_quantity > 0 || selected_ids.length == 0){
                    $('#btn_delete').addClass('d-none');
                }else {
                    $('#btn_delete').removeClass('d-none');
                }

                if (selected_ids.length == 0) {
                    $('#btn_print_qr').addClass('d-none');
                } else {
                    $('#btn_print_qr').removeClass('d-none');
                }
            });
            //general search report
            $('#formstockitem').on('submit', function(e) {
                e.preventDefault();
                tablestockitem.draw();
                checkFilters();
            });

            // Function to check if any filter is applied
            function checkFilters() {
                const keyword = $('#keyword').val().trim();
                const subtype = $('#subtype').val().trim();
                const startDate = $('#startdate').val().trim();
                const endDate = $('#enddate').val().trim();
                const filterby = $('#filterby').val().trim();
                const supplier = $('#supplier').val().trim();

                if (keyword || startDate || endDate || filterby || supplier || subtype) {
                    $('#resetBtn').removeClass('d-none'); // Show the reset button
                } else {
                    $('#resetBtn').addClass('d-none'); // Hide the reset button
                }
            }
            // Reset button functionality
            $('#resetBtn').on('click', function() {
                $('#keyword').val('');
                $('#subtype').val('');
                $('#startdate').val('');
                $('#enddate').val('');
                $('#filterby').val('');
                $('#supplier').val('');
                $('#enddate').prop('disabled', true); // Disable end date again
                $('#resetBtn').addClass('d-none'); // Hide the reset button
                checkFilters(); // Check filters to update visibility
                tablestockitem.draw();
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

            $("#btn_delete").on('click', function() {
                // console.log(selected_ids)
                if (selected_ids.length == 0) {
                    return alert('{!!__('text.no_selected')!!}')
                }
                $('#selected_ids').val(selected_ids);
            })

            $("#btn_print_qr").on('click', function() {
                var params = selected_ids.join(",");
                window.location.href = '{!!url('/')!!}' + `/stock/print_multi?ids=${params}`;
            })
        });
    </script>
    @endpush
@endsection

