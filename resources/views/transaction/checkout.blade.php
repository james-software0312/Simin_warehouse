@extends('layouts.app')
@section('title', __('text.checkout_items'))
@push('style')
<style>
    #scanner {
        width: 100%;
        height: auto;
        display: block;
        position: relative;
    }
    #scanner video {
        width: 100%; /* Ensure the video fills the width of the container */
        /* height: auto;  */
        object-fit: cover; /* Optional: Ensures the video covers the container without stretching */
    }
    .mobile-label {
        display: none;
    }
    .mobile-hide {
        display: block;
    }
    #data {
        cursor: pointer;
    }
    @media (max-width: 768px) {
        #selectedItemsBody,
        #selectedItemsBody tr,
        #selectedItemsBody td {
            display: block;
            width: 100%;
        }

        #selectedItemsBody tr {
            margin-bottom: 15px; /* Space between rows */
        }

        #selectedItemsBody td {
            display: flex;
            flex-direction: column;
            margin-bottom: 10px;
        }

        #selectedItemsBody td::before {
            content: attr(data-label); /* Add labels for accessibility */
            font-weight: bold;
            margin-bottom: 5px;
        }

        #selectedItemsTable thead {
            display: none; /* Hide table headers on smaller screens */
        }
        .mobile-label {
            display: block;
        }

        #selectedItemsBody td.mobile-inline {
            display: -webkit-inline-box;
        }

        .mobile-hide {
            display: none;
        }
    }
</style>
@endpush
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
    <div class="">
        <h2>{{ __('text.checkout_items') }}</h2>
        <form id="adddataform" method="POST" action="{{ route('transaction.storecheckout') }}" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="date" class="form-label">{{ __('text.date') }}</label>
                        <input type="date" class="form-control" id="date" name="transactiondate"  value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="col-md-6"></div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="reference" class="form-label">{{ __('text.reference_number') }}</label>
                        <input type="text" value="{{$ref}}" class="form-control" id="reference" name="reference" placeholder="{{ __('text.reference') }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="show_reference" class="form-label">{{ __('text.transaction_number') }}</label>
                        <input type="text" value="{{$show_ref}}" class="form-control" id="show_reference" name="show_reference" placeholder="{{ __('text.reference') }}" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="warehouse" class="form-label">{{__('text.warehouse')}}</label>
                        <select name="warehouse" id="warehouse" class="form-control" required>
                            {{-- <option value="">{{__('text.select')}}...</option> --}}
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" @if($loop->first) selected @endif>{{ $warehouse->name }}</option>
                        @endforeach
                        </select>
                        <label for="warehouse" class="error"></label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="contact" class="form-label">{{ __('text.customer') }}</label>
                        <select name="contactid" id="contact" class="form-control" required>
                            {{-- <option value="">{{ __('text.select') }}</option> --}}
                            @foreach($contacts as $contact)
                                <option value="{{ $contact->id }}" @if($loop->first) selected @endif>{{ $contact->name }}</option>
                            @endforeach
                        </select>
                        <label for="contact" class="error"></label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <label for="item" class="form-label">{{ __('text.item') }}</label>
                            <a href="#" class="btn btn-red d-flex align-items-center mb-2" data-bs-toggle="modal" data-bs-target="#scanModal" id="btn_scan">
                                <span class="material-symbols-rounded">scan</span>{{__('text.scan')}}</a>
                        </div>
                        <input type="hidden" id="barcode" name="barcode">
                        {{-- <input type="text" class="form-control" id="item" name="item" placeholder="{{ __('text.search_code_or_item_name') }}"> --}}

                        <div class="d-flex"><a data-bs-toggle="modal" data-bs-target="#filterModel" id="btnedit" class="btn btn-sm btn-success d-flex align-items-center" data-toggle="modal">
                            <span class="material-symbols-rounded">edit</span> {{ __('text.items') }}</a></div>
                            <br>
                        <ul id="searchResults"></ul>
                        <div class="searchitemresult">
                            <small id="searchresultmsg" class="text-left mb-0">{{ __('text.search_results') }}</small>
                            <table id="selectedItemsTable" width="100%" class="d-none">
                                <thead>
                                    <tr>
                                        <th width="30%"><small>{{ __('text.item_name') }}</small></th>
                                        <th><small>{{ __('text.quantity') }}</small></th>
                                        <th><small>{{ __('text.sale_price') }}</small></th>
                                        <th class="realprice-header"><small>{{ __('text.real_price') }}</small></th>
                                        <th class="discount-header"><small>{{ __('text.discount') }}</small></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="selectedItemsBody">
                                    <!-- Selected items will be added here dynamically -->
                                </tbody>
                            </table>

                            <input type="hidden" name="itemselecteds" id="itemselecteds" required/>
                            <label for="quantity" class="error"></label>
                        </div>
                        <small id="noitem" for="noitem" class="text-red d-none">{{ __('text.no_item_selected') }}</small>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <label for="discount_type" class="form-label">{{ __('text.discount_type') }} </label>
                    <div class="d-flex mt-2">
                        <div class="form-check" style="margin-right: 20px">
                            <input class="form-check-input" type="radio" name="discount_type" id="discount_type1" checked value="peritem">
                            <label class="form-check-label" for="discount_type1">
                                {{__('text.discount_type1')}}
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="discount_type" id="discount_type2" value="total">
                            <label class="form-check-label" for="discount_type2">
                                {{__('text.discount_type2')}}
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-check row">
                        <label for="vat" class="form-label">{{ __('text.vat') }}</label>
                        <select class="form-control" name="vat" id = "vat">
                            @foreach($vats as $vat)
                                <option value="{{$vat->name}}" @if($vat->name == 23) selected @endif>{{$vat->name}}%</option>
                            @endforeach
                            </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="total_discount" class="form-label">{{ __('text.total_discount') }} </label>
                    <input type="number" name="total_discount" id="total_discount" class="form-control" value="0" max="0" disabled />
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <label for="with_invoice" class="form-label">{{ __('text.with_invoice') }} </label>
                    <div class="mb-3">
                        <input type="checkbox" data-toggle="switchbutton" id="with_invoice" name="with_invoice">
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="pre_order" class="form-label">{{ __('text.pre_order') }} </label>
                    <div class="mb-3">
                        <input type="checkbox" data-toggle="switchbutton" id="pre_order" name="pre_order">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="payment_type" class="form-label">{{ __('text.payment_type') }}</label>
                        <select name="payment_type" id="payment_type" class="form-control" required>
                            <option value="cash" selected>{{ __('text.cash') }}</option>
                            <option value="bank_transfer">{{ __('text.bank_transfer') }}</option>
                            {{-- <option value="pre_order">{{ __('text.pre_order') }}</option> --}}
                            <option value="cash_on_delivery">{{ __('text.cash_on_delivery') }}</option>
                        </select>
                        <label for="payment_type" class="error"></label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="description" class="form-label">{{ __('text.note') }}</label>
                        <textarea id="description" class="form-control" name="description" placeholder="{{ __('text.note') }}"></textarea>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <label for="confirmed" class="form-label">{{ __('text.confirmed') }} </label>
                    <div class="mb-3">
                        <input type="checkbox" data-toggle="switchbutton" id="confirmed" name="confirmed">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <input type="hidden" name="selectedItems" id="selectedItemsInput">
                    <button type="submit" class="btn btn-primary d-flex align-items-center" id="submit">
                        <span class="material-symbols-rounded">check</span> {{ __('text.submit') }}
                    </button>
                </div>
            </div>
        </form>
        <div id="unit_list" style="display: none">
            <select class="hidden form-control unit-input" name="unit[]">
            @foreach($units as $unit)
                <option value="{{$unit->id}}">{{$unit->name}}</option>
            @endforeach
            </select>
        </div>
    </div>

    <div class="modal fade" id="scanModal" tabindex="-1" aria-labelledby="scanModalLabel" aria-hidden="true" data-backdrop="static">
        <!-- Modal content -->
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal header -->
                <div class="modal-header">
                    <h5 class="modal-title" id="scanModalLabel">{{ __('text.scan') }}</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="modal-body">
                    <div id="scanner"></div>
                    <div id="no_found" style="padding-top:10px;">
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
                                <td valign="top" colspan="2">
                                    {{ __('text.product_no_found') }}
                                </td>

                            </tr>
                        </table>
                    </div>
                    <div id="product_details" style="padding-top:10px;">
                        <table width="100%">
                            <tr>
                                <td valign="top" colspan="2">
                                    <center>
                                    {{-- <img src="" alt="barcode" width="100"  class="qrcodedetail"/> --}}

                                    <p class="codedetail"></p>
                                    </center>
                                </td>

                            </tr>
                            <tr>
                                <td valign="top" width="20%">
                                    <p class="me-4"><strong>{{__('text.sub_category')}}:</strong></p>
                                </td>
                                <td valign="top">
                                    <p class="categorydetail"></p>
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" width="20%">
                                    <p class="me-4"><strong>{{__('text.price')}}:</strong></p>
                                </td>
                                <td valign="top">
                                    <p class="pricedetail"></p>
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
                                    <p class="me-4"><strong>{{__('text.name')}}:</strong></p>
                                </td>
                                <td valign="top">
                                    <p class="namedetail"></p>
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
                    <button type="button" class="btn btn-red d-flex align-items-center" id="btn_retry"><span class="material-symbols-rounded">
                        scan
                    </span> {{__('text.retry')}}</button>
                    <button type="button" class="btn btn-primary d-flex align-items-center" id="btn_addproduct"><span class="material-symbols-rounded">
                    check
                    </span> {{__('text.add')}}</button>
                    <button type="button" class="btn btn-secondary d-flex align-items-center" data-bs-dismiss="modal">
                        <span class="material-symbols-rounded">close</span>{{__('text.close')}}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Define the modal content and title -->





<!-- Modal -->
<div class="modal fade" id="filterModel" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content ">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ __('text.Search_name') }}</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body overflow-scroll">
                <input type="text" class="form-control" id="item" name="item" placeholder="Search code or item name..."  >
                <div class="table-responsive-sm">
                    <table class="table" id="data" style="width:100%!important;">
                        <thead>
                            <tr>
                                <th>{{__('text.name')}}</th>
                                <th>{{__('text.selling_price')}}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary d-flex align-items-center modal-close" data-bs-dismiss="modal">
                    <span class="material-symbols-rounded">
                        close
                    </span>{{ __('text.close') }}
                </button>
            </div>
        </div>
    </div>
</div>



@push('scripts')

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script type="module">
    // console.log('123123123123', {{ $units }});
    let unit = @json($units);
    var real_unit;
    $(function() {

        //Search Item
        $('#item').on('input', function () {
            var warehouseid = $("#warehouse").val();
            var query = $(this).val();
            console.log('1',query);

            if (query.length >= 2 && warehouseid) { // Minimum characters to trigger the search


                console.log(query);




                if ($.fn.DataTable.isDataTable('#data')) {
                    $('#data').DataTable().destroy();
                }


                const tablestockitem = $('#data').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("transaction.searchitem") }}',
                    data: { query: query, warehouseid },
                },
                dom: '<"d-flex align-items-md-center flex-column flex-md-row justify-content-md-between pb-3"Bf>rt<"pt-3 d-flex align-items-md-center flex-column flex-md-row justify-content-md-between"lp><"clear">',
                language: {
                    url: langUrl // Polish language JSON file
                },
                bFilter: false,
                order: [[1, "desc"]],
                columns: [

                    // { data: 'code', name: 'code' },
                    { data: 'name', name: 'name' },
                    { data: 'price', name: 'price', render: function(data, type, row) {
                        return `${row.price ? row.price + ' {{__("text.PLN")}}' : 'Undefined'}`
                    } },
                    // { data: 'convertedQty', name: 'convertedQty'},

                ],
                buttons: [

                ],
                rowCallback: function(row, data, index) {
                    // Add a click event to the row to redirect to the Edit screen
                    $(row).on('click', function() {
                        if (!$(event.target).closest('td').hasClass('action') && !$(event.target).is('input[type="checkbox"]')) {


                            $('#searchresultmsg').addClass('d-none');
                            $('#selectedItemsTable').removeClass('d-none');
                            $("#noitem").addClass('d-none');
                            $('#item').val('');
                            console.log(data.price);
                            var itemId =data.id;
                            var price =  data.price;
                            // unit part
                            var unitid =  data.unitid;
                            var unitconverterto = data.unitconverterto;
                            var unitconverter =  data.unitconverter;
                            var unitconverter1 =  data.unitconverter1;
                            var itemCodeName = data.code;
                            var itemName = data.name;
                            // var itemName = 'para';
                            // var itemCodeName = $(this).find('.itemcode').data('code');
                            // var itemName = $(this).find('.itemname').text();
                            // var quantity = 1; // default quantity
                            var quantity = unitid == 1 ? Math.max(unitconverter,unitconverter1) : 1; // default quantity
                            var discount = 0;
                            console.log(itemId,price,unitid,unitconverterto,unitconverter,unitconverter1,itemCodeName,itemName,quantity)
                            // Check if the item already exists in the table
                            var existingRow = $('#selectedItemsBody tr[data-id="' + itemId + '"]');
                            if (existingRow.length > 0) {
                                // Item already exists, update the quantity
                                var currentQuantity = parseInt(existingRow.find('.quantity-input').val());
                                // existingRow.find('.quantity-input').val(currentQuantity + 1);
                                var currentUnit = parseInt(existingRow.find('.unit-input').val());
                                // existingRow.find('.quantity-input').val(currentQuantity + (currentUnit == 1 ? Math.max(unitconverter,unitconverter1) : 1));
                            } else {
                                // Item does not exist, add a new row
                                var quantityInput = '<div class="input-group" style="margin-right: 10px"><input type="hidden" name="unitconverter[]" id="unitconverter" value="'+unitconverter+'"><div class="input-group-text qty-minus" style = "cursor: pointer;" >-</div><input disabled id="quantity" required class="form-control quantity-input" name="quantity[]" value="' + unitconverter + '" style="text-align:center;"><div class="input-group-text qty-plus" style = "cursor: pointer;">+</div></div>';
                                var itemCode = '<input type="hidden" name="stockitemid[]" value="' + itemId + '">';
                                var priceInput = '<label class="mobile-label">{!!__("text.sale_price")!!}</label><input required class="form-control price-input" name="price[]" type="number" min="0" step="0.01" value="' + price + '" disabled />';
                                var realpriceInput = '<label class="mobile-label">{!!__("text.real_price")!!}</label><input required class="form-control realprice-input" name="realprice[]" type="number" min="0" step="0.01" value="' + price + '" />';
                                var discountInput = '<label class="mobile-label">{!!__("text.discount")!!}</label><div class="d-flex align-items-center"><input id="discount" required class="form-control discount-input" name="discount[]" value="' + discount + '" style="margin-right: 5px" disabled /> {{ __("text.PLN") }}</div>';
                                // var unitInput = $("#unit_list").html();
                                // var unitInput = `<select class="form-control unit-input" name="unit[]"><option value="${unitid}">${$("#unit_list").find("option[value="+unitid+"]").text()}</option><option value="${unitconverterto}">${$("#unit_list").find("option[value="+unitconverterto+"]").text()}</option></select>`;
                                var unitInput = `<select class="form-control unit-input" name="unit[]">
                                        <option value="2" ${unitid == 2 || unitconverterto == 2 ? 'selected' : ''}>para</option>
                                    </select>`;
                                    //  ${real_unit} is first
                                var newRow = '<tr data-id="' + itemId + '" data-unitid="' + unitid + '" data-unitconverter="' + unitconverter + '" data-unitconverter1="' + unitconverter1 + '" data-price="' + price + '" data-unitid="' + unitid + '" data-unitconverterto="' + unitconverterto + '"><td class="mobile-inline"><div style="width: 95%"><span class="itemname">' + itemName + '</span><br/><span class="itemcode">' + itemCodeName + '</span></div><a href="#blank" class="remove-item mobile-label"><span class="material-symbols-rounded">delete</span></a></td><td style="display:flex;flex-direction:row">' + itemCode + quantityInput + '' + unitInput + '</td><td>' + priceInput + '</td><td class="realprice-value">' + realpriceInput + '</td><td class="discount-value">' + discountInput + '</td><td align="center">&nbsp;<a href="#blank" class="remove-item mobile-hide"><span class="material-symbols-rounded">delete</span></a></td></tr>';

                                $('#selectedItemsBody').append(newRow);
                            }


                            // Clear the search input and results
                            $('#searchInput').val('');
                            $('#searchResults').empty();
                            initDiscuount();
                            $(".modal-close").click();
                        }

                    });
                }
                });








                // $.ajax({
                //     url: '{{ route("transaction.searchitem") }}',
                //     method: 'GET',
                //     data: { query: query, warehouseid: warehouseid },
                //     success: function (data) {
                //         $('#searchResults').empty();
                //         data = data.filter((item)=>(item.single_quantity > 0));
                //         console.log("-data",data)

                //         $.each(data, function (index, item) {
                //             for(let i = 0; i < unit.length; i ++){
                //                 if(unit[i]['id'] ==1) {
                //                     real_unit = unit[i]['name'];
                //                 }
                //             }
                //         console.log(item);
                //             $('#searchResults').append('<li class="search-result" data-id="' + item.id + '" data-price="' + item.price + '" data-unitid="' + item.unitid + '" data-unitconverterto="' + item.unitconverterto + '"data-unitconverter="' + item.unitconverter + '" data-unitconverter1="' + item.unitconverter1 + '" data-itemquantity="' + item.quantity + '"><span data-name="'+item.name+'" class="itemname">' + item.name + '(' + item.single_quantity + ' ' + real_unit + ')</span><br/><span data-code="'+item.code+'" class="itemcode">'+item.code+'</span></li>');
                //             // Customize the display based on your model's structure
                //         });
                //     }
                // });
            } else {
                $('#searchResults').empty();
            }
        });

        //IN HERE table.

        // Handle click on a search result
        $('#searchResults').on('click', '.search-result', function () {
            $('#searchresultmsg').addClass('d-none');
            $('#selectedItemsTable').removeClass('d-none');
            $("#noitem").addClass('d-none');
            $('#item').val('');
            var itemId = $(this).data('id');
            var price =  $(this).data('price');
            // unit part
            var unitid =  $(this).data('unitid');
            var unitconverterto = $(this).data('unitconverterto');
            var unitconverter =  $(this).data('unitconverter');
            var unitconverter1 =  $(this).data('unitconverter1');

            var itemCodeName = $(this).find('.itemcode').data('code');
            var itemName = $(this).find('.itemname').text();
            // var quantity = 1; // default quantity
            var quantity = unitid == 1 ? Math.max(unitconverter,unitconverter1) : 1; // default quantity
            var discount = 0;
            // Check if the item already exists in the table
            var existingRow = $('#selectedItemsBody tr[data-id="' + itemId + '"]');
            if (existingRow.length > 0) {
                // Item already exists, update the quantity
                var currentQuantity = parseInt(existingRow.find('.quantity-input').val());
                // existingRow.find('.quantity-input').val(currentQuantity + 1);
                var currentUnit = parseInt(existingRow.find('.unit-input').val());
                // existingRow.find('.quantity-input').val(currentQuantity + (currentUnit == 1 ? Math.max(unitconverter,unitconverter1) : 1));
            } else {
                // Item does not exist, add a new row
                var quantityInput = '<div class="input-group" style="margin-right: 10px"><input type="hidden" name="unitconverter[]" id="unitconverter" value="'+unitconverter+'"><div class="input-group-text qty-minus" style = "cursor: pointer;" >-</div><input disabled id="quantity" required class="form-control quantity-input" name="quantity[]" value="' + unitconverter + '" style="text-align:center;"><div class="input-group-text qty-plus" style = "cursor: pointer;">+</div></div>';
                var itemCode = '<input type="hidden" name="stockitemid[]" value="' + itemId + '">';
                var priceInput = '<label class="mobile-label">{!!__("text.sale_price")!!}</label><input required class="form-control price-input" name="price[]" type="number" min="0" step="0.01" value="' + price + '" disabled />';
                var realpriceInput = '<label class="mobile-label">{!!__("text.real_price")!!}</label><input required class="form-control realprice-input" name="realprice[]" type="number" min="0" step="0.01" value="' + price + '" />';
                var discountInput = '<label class="mobile-label">{!!__("text.discount")!!}</label><div class="d-flex align-items-center"><input id="discount" required class="form-control discount-input" name="discount[]" value="' + discount + '" style="margin-right: 5px" disabled /> {{ __("text.PLN") }}</div>';
                // var unitInput = $("#unit_list").html();
                // var unitInput = `<select class="form-control unit-input" name="unit[]"><option value="${unitid}">${$("#unit_list").find("option[value="+unitid+"]").text()}</option><option value="${unitconverterto}">${$("#unit_list").find("option[value="+unitconverterto+"]").text()}</option></select>`;
                var unitInput = `<select class="form-control unit-input" name="unit[]">
                        <option value="2" ${unitid == 2 || unitconverterto == 2 ? 'selected' : ''}>para</option>
                    </select>`;
                var newRow = '<tr data-id="' + itemId + '" data-unitid="' + unitid + '" data-unitconverter="' + unitconverter + '" data-unitconverter1="' + unitconverter1 + '" data-price="' + price + '" data-unitid="' + unitid + '" data-unitconverterto="' + unitconverterto + '"><td class="mobile-inline"><div style="width: 95%"><span class="itemname">' + itemName + '</span><br/><span class="itemcode">' + itemCodeName + '</span></div><a href="#blank" class="remove-item mobile-label"><span class="material-symbols-rounded">delete</span></a></td><td style="display:flex;flex-direction:row">' + itemCode + quantityInput + '' + unitInput + '</td><td>' + priceInput + '</td><td class="realprice-value">' + realpriceInput + '</td><td class="discount-value">' + discountInput + '</td><td align="center">&nbsp;<a href="#blank" class="remove-item mobile-hide"><span class="material-symbols-rounded">delete</span></a></td></tr>';

                $('#selectedItemsBody').append(newRow);
            }


            // Clear the search input and results
            $('#searchInput').val('');
            $('#searchResults').empty();
            initDiscuount();
        });

        // Handle click to remove item from the table
        $('#selectedItemsTable').on('click', '.remove-item', function () {
            $(this).closest('tr').remove();
            calculateDiscount();
            // You can add an AJAX call here to remove the item from the server-side storage.
        });

        $('#selectedItemsTable').on('change', '.unit-input', function () {
            var element = $(this);
            $.ajax({
                url: '{{route("transaction.checksellquantity")}}',
                type: 'GET',
                data: {
                    stockitemid: $(this).closest('tr').data('id'),
                    quantity: $(this).closest('tr').find('.quantity-input').val(),
                    unit: element.val()
                },
                success: function(data) {
                    if (data.avaiable) {
                        // change price
                        if (element.val() != element.closest('tr').data('unitid')) {
                            // Enable the disabled input field, set its value, and disable it again
                            element.closest('tr').find('.price-input').prop('disabled', false).val((element.closest('tr').data('price') * (element.closest('tr').data('unitconverter') / element.closest('tr').data('unitconverter1'))).toFixed(2)).prop('disabled', true);
                            element.closest('tr').find('.realprice-input').val((element.closest('tr').find('.realprice-input').val() * (element.closest('tr').data('unitconverter') / element.closest('tr').data('unitconverter1')).toFixed(2)));
                        } else {
                            element.closest('tr').find('.price-input').prop('disabled', false).val((element.closest('tr').data('price')).toFixed(2)).prop('disabled', true);
                            element.closest('tr').find('.realprice-input').val((element.closest('tr').find('.realprice-input').val() * (element.closest('tr').data('unitconverter1') / element.closest('tr').data('unitconverter'))).toFixed(2));
                        }
                        element.closest('tr').find('.discount-input').val((
                            element.closest('tr').find('.realprice-input').val() - element.closest('tr').find('.price-input').val()).toFixed(2)
                        );
                        calculateDiscount();
                    } else {
                        element.val(element.closest('tr').data('unitconverter'));
                        alert("{!!__('text.not_available_qty')!!}");
                    }
                }
            })
        })
        $('#selectedItemsTable').on('change', '.quantity-input', function () {
            console.log($(this).val());
            // this quantity value is available.
            var element = $(this);
            $.ajax({
                url: '{{route("transaction.checksellquantity")}}',
                type: 'GET',
                data: {
                    stockitemid: $(this).closest('tr').data('id'),
                    quantity: $(this).val(),
                    unit: element.closest('tr').find("select").val()
                },
                success: function(data) {
                    if (data.avaiable) {
                        calculateDiscount();
                    } else {
                        element.val(element.closest('tr').data('unitconverter'));
                        alert("{!!__('text.not_available_qty')!!}");
                    }
                }
            })
        });

        $("#selectedItemsTable").on('click', '.qty-plus', function() {
            var unitconverter = $(this).closest('tr').data('unitconverter');
            var unitconverter1 = $(this).closest('tr').data('unitconverter1');
            var old_val = $(this).prev().val();
            var new_val = 0;
            new_val = unitconverter + parseInt(old_val);
            // if (unitconverter > unitconverter1) {
            //     new_val = old_val * 1 + unitconverter * 1;
            // } else {
            //     new_val = old_val * 1 + unitconverter1 * 1;
            // }
            $(this).prev().val(new_val);
            $(this).prev().trigger('change');
            console.log($(this).prev().val(), $(this).prev().attr('name') );
        })

        $("#selectedItemsTable").on('click', '.qty-minus', function() {
            var unitconverter = $(this).closest('tr').data('unitconverter');
            var unitconverter1 = $(this).closest('tr').data('unitconverter1');
            var old_val = $(this).next().val();
            var new_val = 0;
            // if (unitconverter > unitconverter1) {
            //     new_val = old_val * 1 - unitconverter * 1;
            // } else {
            //     new_val = old_val * 1 - unitconverter1 * 1;
            // }
            new_val = parseInt(old_val)- unitconverter;
            if (new_val > 0) {
                $(this).next().val(new_val);
                $(this).next().trigger('change');
            }
        })

        $('#selectedItemsTable').on('input', '.discount-input', function () {
            if ($("#discount_type2").prop("checked")) {
                $(this).val(0);
                return alert("you can't input");
            }
            calculateDiscount();
        });

        $("#selectedItemsTable").on('input', '.realprice-input', function() {
            if ($("#discount_type2").prop("checked")) {
                return alert("you can't input");
            }
            let  real_discount = ($(this).val() - $(this).closest("tr").find("input.price-input").val()).toFixed(2)
            console.log(">>>>>>>>>>>>>>>>>>>>>", ($(this).val() - $(this).closest("tr").find("input.price-input").val()).toFixed(3));
            $(this).closest("tr").find("input.discount-input").val(real_discount)
            calculateDiscount();
        });

        const calculateDiscount = () => {
            if ($("#discount_type1").prop("checked")) {
                var total_discount = 0;
                $("#selectedItemsTable tbody tr").each(function() {
                    total_discount += $(this).find('.discount-input').val() * $(this).find('.quantity-input').val();
                })
                $("#total_discount").val((total_discount).toFixed(2));
            }
        }


        $("#discount_type1").on('change', function() {
            if ($(this).prop('checked')) {
                $("#total_discount").prop('disabled', true)
            }
        })

        $("#discount_type2").on('change', function() {
            if ($(this).prop('checked')) {
                $("#total_discount").prop('disabled', false)
            }
        })

        const initDiscuount = () => {
            $("input[name=discount_type]").on('change', function() {
                if (!$("#discount_type1").prop("checked")) {
                    $("#selectedItemsTable").find(".discount-value").hide();
                    $("#selectedItemsTable").find(".discount-header").hide();
                    $("#selectedItemsTable").find(".realprice-header").hide();
                    $("#selectedItemsTable").find(".realprice-value").hide();
                    $("#total_discount").val(0);
                } else {
                    $("#selectedItemsTable").find(".discount-value").show();
                    $("#selectedItemsTable").find(".discount-header").show();
                    $("#selectedItemsTable").find(".realprice-header").show();
                    $("#selectedItemsTable").find(".realprice-value").show();
                    $("#selectedItemsTable").find(".discount-value input").val(0);
                    $("#selectedItemsBody tr").each(function() {
                        $(this).find("input.discount-input").val($(this).find("input.realprice-input").val() - $(this).find("input.realprice-input").closest("tr").find("input.price-input").val())
                    });
                    calculateDiscount();
                }
                // $("#selectedItemsTable tbody tr").each(function() {
                //     // $(this).find('.discount-input').val(0);
                //     $(this).find('.discount-input').parent().hide();
                // })
            });
        }
        initDiscuount();

        // Initialize jQuery Validation
        $('#adddataform').validate({
            rules: {
                reference: {
                    required: true,
                    uniquecode:true
                },
            },
            messages: {
                transactiondate: {
                    required: '{!!__('text.field_required')!!}'
                },
                reference: {
                    required: '{!!__('text.field_required')!!}'
                },
                show_reference: {
                    required: '{!!__('text.field_required')!!}'
                },
                contactid: {
                    required: '{!!__('text.field_required')!!}'
                },
                warehouse: {
                    required: '{!!__('text.field_required')!!}'
                },
            },
            submitHandler: function (form) {
                if ($('#selectedItemsBody tr').length === 0) {
                    // Display a required message (you can customize this based on your needs)
                    $("#noitem").removeClass('d-none');

                    return false; // Prevent form submission
                }else{
                    $("#total_discount").prop("disabled", false);
                    $(".price-input").prop("disabled", false);
                    $(".quantity-input").prop("disabled", false);
                    $(".discount-input").prop("disabled", false);
                    $("#noitem").addClass('d-none');
                    form.submit();
                }
            }
        });
        const html5QrCodeScanner = new Html5QrcodeScanner("scanner", {
            fps: 10,
            qrbox: 250 ,
            disableImageUpload: true,
            // preferredCameraId: null,
        }, false);
        $("#btn_addproduct").on('click', function() {
            $('#product_details').hide();
            $('#no_found').hide();
            $("#btn_addproduct").addClass('d-none');
            $("#btn_retry").addClass('d-none');

            if(selectedProduct){
                $('#searchresultmsg').addClass('d-none');
                $('#selectedItemsTable').removeClass('d-none');
                $("#noitem").addClass('d-none');
                $('#item').val('');

                var itemId = selectedProduct.id;
                var price =  selectedProduct.price;
                // unit part
                var unitid =  selectedProduct.unitid;
                var unitconverterto = selectedProduct.unitconverterto;
                var unitconverter =  selectedProduct.unitconverter;
                var unitconverter1 =  selectedProduct.unitconverter1;

                var itemCodeName = selectedProduct.code;
                var itemName = selectedProduct.name;
                // var quantity = 1; // default quantity
                var quantity = unitid == 1 ? Math.max(unitconverter,unitconverter1) : 1; // default quantity
                var discount = 0;
                // Check if the item already exists in the table
                var existingRow = $('#selectedItemsBody tr[data-id="' + itemId + '"]');
                if (existingRow.length > 0) {
                    // Item already exists, update the quantity
                    var currentQuantity = parseInt(existingRow.find('.quantity-input').val());
                    // existingRow.find('.quantity-input').val(currentQuantity + 1);
                    var currentUnit = parseInt(existingRow.find('.unit-input').val());
                    // existingRow.find('.quantity-input').val(currentQuantity + (currentUnit == 1 ? Math.max(unitconverter,unitconverter1) : 1));
                } else {
                    // Item does not exist, add a new row
                    var quantityInput = '<div class="input-group" style="margin-right: 10px"><input type="hidden" name="unitconverter[]" id="unitconverter" value="'+unitconverter+'"><div class="input-group-text qty-minus" style = "cursor: pointer;" >-</div><input disabled id="quantity" required class="form-control quantity-input" name="quantity[]" value="' + unitconverter + '" style="text-align:center;"><div class="input-group-text qty-plus" style = "cursor: pointer;">+</div></div>';
                var itemCode = '<input type="hidden" name="stockitemid[]" value="' + itemId + '">';
                var priceInput = '<label class="mobile-label">{!!__("text.sale_price")!!}</label><input required class="form-control price-input" name="price[]" type="number" min="0" step="0.01" value="' + price + '" disabled />';
                var realpriceInput = '<label class="mobile-label">{!!__("text.real_price")!!}</label><input required class="form-control realprice-input" name="realprice[]" type="number" min="0" step="0.01" value="' + price + '" />';
                var discountInput = '<label class="mobile-label">{!!__("text.discount")!!}</label><div class="d-flex align-items-center"><input id="discount" required class="form-control discount-input" name="discount[]" value="' + discount + '" style="margin-right: 5px" disabled /> {{ __("text.PLN") }}</div>';
                // var unitInput = $("#unit_list").html();
                // var unitInput = `<select class="form-control unit-input" name="unit[]"><option value="${unitid}">${$("#unit_list").find("option[value="+unitid+"]").text()}</option><option value="${unitconverterto}">${$("#unit_list").find("option[value="+unitconverterto+"]").text()}</option></select>`;
                var unitInput = `<select class="form-control unit-input" name="unit[]">
                        <option value="2" ${unitid == 2 || unitconverterto == 2 ? 'selected' : ''}>${real_unit}</option>
                    </select>`;
                var newRow = '<tr data-id="' + itemId + '" data-unitid="' + unitid + '" data-unitconverter="' + unitconverter + '" data-unitconverter1="' + unitconverter1 + '" data-price="' + price + '" data-unitid="' + unitid + '" data-unitconverterto="' + unitconverterto + '"><td class="mobile-inline"><div style="width: 95%"><span class="itemname">' + itemName + '</span><br/><span class="itemcode">' + itemCodeName + '</span></div><a href="#blank" class="remove-item mobile-label"><span class="material-symbols-rounded">delete</span></a></td><td style="display:flex;flex-direction:row">' + itemCode + quantityInput + '' + unitInput + '</td><td>' + priceInput + '</td><td class="realprice-value">' + realpriceInput + '</td><td class="discount-value">' + discountInput + '</td><td align="center">&nbsp;<a href="#blank" class="remove-item mobile-hide"><span class="material-symbols-rounded">delete</span></a></td></tr>';

                $('#selectedItemsBody').append(newRow);


                    // var quantityInput = '<div class="input-group" style="margin-right: 10px"><input type="hidden" name="unitconverter[]" id="unitconverter" value="'+unitconverter+'"><div class="input-group-text qty-minus">-</div><input id="quantity" required class="form-control quantity-input" name="quantity[]" type="number" min="1" value="' + quantity + '" style="text-align:center;"><div class="input-group-text qty-plus">+</div></div>';
                    // var itemCode = '<input type="hidden" name="stockitemid[]" value="' + itemId + '">';
                    // var priceInput = '<label class="mobile-label">{!!__("text.sale_price")!!}</label><input required class="form-control price-input" name="price[]" type="number" min="0" step="0.01" value="' + price + '" disabled />';
                    // var realpriceInput = '<label class="mobile-label">{!!__("text.real_price")!!}</label><input required class="form-control realprice-input" name="realprice[]" type="number" min="0" step="0.01" value="' + price + '" />';
                    // var discountInput = '<label class="mobile-label">{!!__("text.discount")!!}</label><div class="d-flex align-items-center"><input id="discount" required class="form-control discount-input" name="discount[]" value="' + discount + '" style="margin-right: 5px" disabled /> {{ __("text.PLN") }}</div>';
                    // // var unitInput = $("#unit_list").html();
                    // var unitInput = `<select class="form-control unit-input" name="unit[]"><option value="${unitid}">${$("#unit_list").find("option[value="+unitid+"]").text()}</option><option value="${unitconverterto}">${$("#unit_list").find("option[value="+unitconverterto+"]").text()}</option></select>`;

                    // var newRow = '<tr data-id="' + itemId + '" data-unitid="' + unitid + '" data-unitconverter="' + unitconverter + '" data-unitconverter1="' + unitconverter1 + '" data-price="' + price + '" data-unitid="' + unitid + '" data-unitconverterto="' + unitconverterto + '"><td class="mobile-inline"><div style="width: 95%"><span class="itemname">' + itemName + '</span><br/><span class="itemcode">' + itemCodeName + '</span></div><a href="#blank" class="remove-item mobile-label"><span class="material-symbols-rounded">delete</span></a></td><td style="display:flex;flex-direction:row">' + itemCode + quantityInput + unitInput + '</td><td>' + priceInput + '</td><td class="realprice-value">' + realpriceInput + '</td><td class="discount-value">' + discountInput + '</td><td align="center">&nbsp;<a href="#blank" class="remove-item mobile-hide"><span class="material-symbols-rounded">delete</span></a></td></tr>';

                    // $('#selectedItemsBody').append(newRow);
                }
                initDiscuount();
            }

            if (html5QrCodeScanner) {
                html5QrCodeScanner.clear();  // Stop the camera and scanner
            }
            html5QrCodeScanner.render(qrCodeSuccessCallback)
                .catch(err => {
                    console.error("Failed to start scanning:", err);
                });
        });
        $("#btn_retry").on('click', function() {
            selectedProduct = null;
            $('#product_details').hide();
            $('#no_found').hide();
            $("#btn_addproduct").addClass('d-none');
            $("#btn_retry").addClass('d-none');


            if (html5QrCodeScanner) {
                html5QrCodeScanner.clear();  // Stop the camera and scanner
            }
            html5QrCodeScanner.render(qrCodeSuccessCallback)
                .catch(err => {
                    console.error("Failed to start scanning:", err);
                });
        });
        $("#btn_scan").on('click', function() {
            selectedProduct = null;
            $('#product_details').hide();
            $('#no_found').hide();
            $("#btn_addproduct").addClass('d-none');
            $("#btn_retry").addClass('d-none');


            // Start the scanner
            html5QrCodeScanner.render(qrCodeSuccessCallback)
                .catch(err => {
                    console.error("Failed to start scanning:", err);
                });
        });
        let selectedProduct;
        const qrCodeSuccessCallback = (decodedText, decodedResult) => {
            selectedProduct = null;
            $("#btn_addproduct").addClass('d-none');
            // Handle the scanned result
            console.log(`QR Code detected: ${decodedText}`);
            // document.getElementById('result').innerText = `QR Code: ${decodedText}`;
            // alert(decodedText);
            if(decodedText){
                var warehouseid = $("#warehouse").val();
                $.ajax({
                        url: '{{ route("transaction.scan") }}',
                        method: 'GET',
                        data: { code: decodedText, warehouseid: warehouseid },
                        success: function (data) {
                            console.log("-data",data)
                            if(data){
                                if(data.success){
                                    selectedProduct = data.data;
                                    $('.qrcodedetail').attr('src', data.dataURL);
                                    $('.photodetail').attr('src', data.photo);
                                    $('.codedetail').html(data.data.code);
                                    $('.categorydetail').html(data.data.itemsubtype);
                                    $('.quantitydetail').html(data.data.quantity);
                                    $('.pricedetail').html(data.data.price);
                                    $('.namedetail').html(data.data.name);
                                    $('.unitdetail').html(data.data.unit);
                                    $('.warehousedetail').html(data.data.warehouse);
                                    $('#product_details').show();
                                    $('#no_found').hide();
                                    if(data.data.quantity > 0)
                                        $("#btn_addproduct").removeClass('d-none');
                                    $("#btn_retry").removeClass('d-none');
                                    if (html5QrCodeScanner) {
                                        html5QrCodeScanner.clear();  // Stop the camera and scanner
                                    }

                                }else{
                                    $('.qrcodedetail').attr('src', data.dataURL);
                                    $('.codedetail').html(decodedText);

                                    $('#product_details').hide();
                                    $('#no_found').show();
                                }

                            }else{
                                $('#product_details').hide();
                                $('#no_found').show();
                            }
                        }
                    });

            }
        };
        $('#scanModal').on('hidden.bs.modal', function () {
            if (html5QrCodeScanner) {
                html5QrCodeScanner.clear();
            }
        });

        $("#payment_type").on('change', function() {
            $.ajax({
				url: '{{ route("transaction.getNewShowRef") }}',
				method: 'GET',
				data: {payment_type: $(this).val(), date: $("#date").val()},
				success: function(res) {
					$("#show_reference").val(res.new_ref_num);
				}
			})
        });

        $("#date").on('change', function() {
            $.ajax({
				url: '{{ route("transaction.getNewShowRef") }}',
				method: 'GET',
				data: {payment_type: $("#pre_order").prop('checked') ? 'pre_order' : $("#payment_type").val(), date: $(this).val()},
				success: function(res) {
					$("#show_reference").val(res.new_ref_num);
				}
			})
        });

        $("#pre_order").on('change', function() {
            if ($(this).prop('checked')) {
                $.ajax({
                    url: '{{ route("transaction.getNewShowRef") }}',
                    method: 'GET',
                    data: {payment_type: 'pre_order', date: $("#date").val()},
                    success: function(res) {
                        $("#show_reference").val(res.new_ref_num);
                    }
                })
            } else {
                $.ajax({
                    url: '{{ route("transaction.getNewShowRef") }}',
                    method: 'GET',
                    data: {payment_type: $("#payment_type").val(), date: $("#date").val()},
                    success: function(res) {
                        $("#show_reference").val(res.new_ref_num);
                    }
                })
            }
        });
    });

</script>
@endpush
@endsection

