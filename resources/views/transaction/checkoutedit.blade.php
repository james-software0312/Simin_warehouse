@extends('layouts.app')
@section('title', __('text.checkout_items'))

@section('content')

<style>
    .mobile-label {
        display: none;
    }
    .mobile-hide {
        display: block;
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
    
    <div class="mb-4">
        <h2>{{ __('text.checkout_items') }}</h2>
        
        <form id="editdataform" method="POST" action="{{ route('transaction.updatecheckout') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="id" name="id" value="{{ $singletransaction->id }}">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="date" class="form-label">{{ __('text.date') }}</label>
                        <input type="date" class="form-control" id="date" name="transactiondate" value="{{ date('Y-m-d', strtotime($singletransaction->selldate)) }}" required>
                    </div>
                </div>
                <div class="col-md-6"></div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="reference" class="form-label">{{ __('text.reference_number') }}</label>
                        <input type="text" value="{{ $singletransaction->reference }}" readonly class="form-control" id="reference" name="reference" placeholder="{{ __('text.reference') }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="show_reference" class="form-label">{{__('text.transaction_number')}}</label>
                        <input type="text" value="{{$singletransaction->show_reference}}" readonly class="form-control" id="show_reference" name="show_reference" placeholder="Reference" required >
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="warehouse" class="form-label">{{__('text.warehouse')}}</label>
                        <select name="warehouse" id="warehouse" class="form-control" disabled>
                            <option value="">{{__('text.select')}}...</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" @if($singletransaction->warehouseid == $warehouse->id) selected @endif >{{ $warehouse->name }}</option>
                        @endforeach
                        </select>
                        <label for="warehouse" class="error"></label>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="contact" class="form-label">{{ __('text.customer') }}</label>
                        <input type="hidden" name="contactselect" id="contactselect" value="{{ $singletransaction->contactid }}"/>
                        <select name="contactid" id="contact" class="form-control" required>
                            <option value="">{{ __('text.select') }}</option>
                            @foreach($contacts as $contact)
                                <option value="{{ $contact->id }}" {{ $contact->id == $singletransaction->contactid ? 'selected' : '' }}>
                                    {{ $contact->name }}
                                </option>
                            @endforeach
                        </select>
                        <label for="contact" class="error"></label>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="item" class="form-label">{{ __('text.item') }}</label>
                        <input type="text" class="form-control" id="item" name="item" placeholder="{{ __('text.search_item') }}">
                        <ul id="searchResults"></ul>
                        
                        <div class="searchitemresult">
                            <table id="selectedItemsTable" width="100%" class="">
                                <thead>
                                    <tr>
                                        <th width="35%"><small>{{ __('text.item_name') }}</small></th>
                                        <th><small>{{ __('text.quantity') }}</small></th>
                                        <th><small>{{ __('text.sale_price') }}</small></th>
                                        <th class="realprice-header"><small>{{ __('text.real_price') }}</small></th>
                                        <th class="discount-header"><small>{{ __('text.discount') }}</small></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="selectedItemsBody">
                                    @foreach($transaction as $item)
                                        <tr 
                                            data-id="{{ $item->stockitemid }}" 
                                            data-unitid="{{$item->stockunit}}"
                                            data-unitconverter="{{$item->unitconverter}}"
                                            data-unitconverter1="{{$item->unitconverter1}}"
                                            data-unitconverterto="{{$item->unitconverterto}}"
                                            data-price="{{$item->stock_price}}"
                                        >
                                            <td class="mobile-inline">
                                                <div style="width: 95%">
                                                    <span class="itemname">{{ $item->name }}</span><br/>
                                                    <span class="itemcode">{{ $item->code }}</span>
                                                </div>
                                                <a href="#blank" class="remove-item mobile-label"><span class="material-symbols-rounded">delete</span></a>
                                            </td>
                                            <td style="display:flex;flex-direction:row">
                                                <input type="hidden" name="stockitemid[]" value="{{ $item->stockitemid }}">
                                                <div class="input-group" style="margin-right: 10px">
                                                    <div class="input-group-text qty-plus">+</div>
                                                    <input id="quantity" required class="form-control quantity-input" name="quantity[]" value="{{ $item->quantity }}">
                                                    <div class="input-group-text qty-minus">-</div>
                                                </div>
                                                <select class="hidden form-control unit-input" name="unit[]">
                                                @foreach($units as $unit)
                                                    <option value="{{$unit->id}}" {{$item->unitid == $unit->id ? 'selected' : ''}}>{{$unit->name}}</option>
                                                @endforeach
                                                </select>
                                            </td>

                                            <td>
                                                <label class="mobile-label">{!!__("text.sale_price")!!}</label>
                                                <input required class="form-control price-input" name="price[]" type="number" min="1" value="{{$item->price}}" disabled>
                                            </td>
                                            <td class="realprice-value">
                                                <label class="mobile-label">{!!__("text.real_price")!!}</label>
                                                <input required class="form-control realprice-input" name="realprice[]" type="number" value="{{$item->price + $item->discount}}">
                                            </td>
                                            <td class="discount-value">
                                                <label class="mobile-label">{!!__("text.discount")!!}</label>
                                                <div class="d-flex align-items-center">
                                                    <input required class="form-control discount-input" name="discount[]" value="{{$item->discount}}" disabled style="margin-left: 5px; margin-right: 5px">{{ __("text.PLN") }}
                                                </div>
                                            </td>
                                            <td align="center">
                                                <a href="#blank" class="remove-item mobile-hide">
                                                    <span class="material-symbols-rounded">delete</span>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
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
                <div class="col-md-6">
                    <label for="discount_type" class="form-label">{{ __('text.discount_type') }} </label>
                    <div class="d-flex mt-2">
                        <div class="form-check" style="margin-right: 20px">
                            <input class="form-check-input" type="radio" name="discount_type" id="discount_type1" {{$singletransaction->discount_type == 'peritem' ? 'checked' : ''}} value="peritem">
                            <label class="form-check-label" for="discount_type1">
                                {{__('text.discount_type1')}}
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="discount_type" id="discount_type2" {{$singletransaction->discount_type == 'total' ? 'checked' : ''}} value="total">
                            <label class="form-check-label" for="discount_type2">
                                {{__('text.discount_type2')}}
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="total_discount" class="form-label">{{ __('text.total_discount') }} </label>
                    <input type="number" name="total_discount" id="total_discount" class="form-control" value="{{$singletransaction->discount}}" {{$singletransaction->discount_type == 'total' ? '' : 'disabled'}} />
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <label for="with_invoice" class="form-label">{{ __('text.with_invoice') }} </label>
                    <div class="mb-3">
                        <input type="checkbox" data-toggle="switchbutton" id="with_invoice" name="with_invoice"  {{$singletransaction->withinvoice ? 'checked' : ''}}>
                    </div>
                </div>
                @if($singletransaction->pre_order)
                <div class="col-md-3">
                    <label for="pre_order" class="form-label">{{ __('text.pre_order') }} </label>
                    <div class="mb-3">
                        <input type="checkbox" data-toggle="switchbutton" id="pre_order" name="pre_order" {{$singletransaction->pre_order ? 'checked' : ''}}>
                    </div>
                </div>
                @else
                <div class="col-md-3"></div>
                @endif
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="payment_type" class="form-label">{{ __('text.payment_type') }}</label>
                        <select name="payment_type" id="payment_type" class="form-control" required>
                            <option value="bank_transfer" {{$singletransaction->payment_type == 'bank_transfer' ? 'selected': ''}}>{{ __('text.bank_transfer') }}</option>
                            <option value="cash" {{$singletransaction->payment_type == 'cash' ? 'selected': ''}}>{{ __('text.cash') }}</option>
                            {{-- <option value="pre_order" {{$singletransaction->payment_type == 'pre_order' ? 'selected': ''}}>{{ __('text.pre_order') }}</option> --}}
                            <option value="cash_on_delivery" {{$singletransaction->payment_type == 'cash_on_delivery' ? 'selected': ''}}>{{ __('text.cash_on_delivery') }}</option>
                        </select>
                        <label for="payment_type" class="error"></label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="description" class="form-label">{{ __('text.note') }}</label>
                        <textarea id="description" class="form-control" name="description" placeholder="{{ __('text.note') }}">{{ $singletransaction->description }}</textarea>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <label for="confirmed" class="form-label">{{ __('text.confirmed') }} </label>
                    <div class="mb-3">
                        <input type="checkbox" data-toggle="switchbutton" id="confirmed" name="confirmed"  {{$singletransaction->confirmed ? 'checked disabled' : ''}}>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 d-flex justify-content-end">
                    <input type="hidden" name="selectedItems" id="selectedItemsInput">
                    <a href="{{route('transaction.checkoutlist')}}" class="btn btn-danger d-flex align-items-center">
                        <span class="material-symbols-rounded">close</span>{{__('text.Cancel')}}</a>&nbsp;&nbsp;
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
</div>

<!-- Define the modal content and title -->
     



@push('scripts')
<script type="module">
    
        $(function() {
            
            //Search Item
            $('#item').on('input', function () {
                
                var query = $(this).val();

                if (query.length >= 2) { // Minimum characters to trigger the search
                    $.ajax({
                        url: '{{ route("transaction.searchitem") }}',
                        method: 'GET',
                        data: { query: query },
                        success: function (data) {
                            $('#searchResults').empty();
                            
                            $.each(data, function (index, item) {
                                $('#searchResults').append('<li class="search-result" data-id="' + item.id + '" data-price="' + item.price + '" data-unitid="' + item.unitid + '" data-unitconverterto="' + item.unitconverterto + '"data-unitconverter="' + item.unitconverter + '" data-unitconverter1="' + item.unitconverter1 + '" data-itemquantity="' + item.quantity + '"><span data-name="'+item.name+'" class="itemname">' + item.name + '(' + item.quantity + item.unitname + ')</span><br/><span data-code="'+item.code+'" class="itemcode">'+item.code+'</span></li>');
                                // $('#searchResults').append('<li class="search-result" data-id="' + item.id + '" data-price="' + item.price + '"><span data-name="'+item.name+'" class="itemname">' + item.name + '</span><br/><span data-code="'+item.code+'" class="itemcode">'+item.code+'</span></li>');
                                // Customize the display based on your model's structure
                            });
                        }
                    });
                } else {
                    $('#searchResults').empty();
                }
            });

            // Handle click on a search result
            $('#searchResults').on('click', '.search-result', function () {
                $('#searchresultmsg').addClass('d-none');
                $('#selectedItemsTable').removeClass('d-none');
                $("#noitem").addClass('d-none');
                $('#item').val('');
                var itemId = $(this).data('id');
                var price = $(this).data('price'); // default quantity
                // unit
                var unitid =  $(this).data('unitid');
                var unitconverterto = $(this).data('unitconverterto');
                var unitconverter =  $(this).data('unitconverter');
                var unitconverter1 =  $(this).data('unitconverter1');

                var itemCodeName = $(this).find('.itemcode').data('code');
                var itemName = $(this).find('.itemname').data('name');
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
                    existingRow.find('.quantity-input').val(currentQuantity + (currentUnit == 1 ? Math.max(unitconverter,unitconverter1) : 1));
                } else {
                    // Item does not exist, add a new row
                    var quantityInput = '<div class="input-group" style="margin-right: 10px"><div class="input-group-text qty-minus">-</div><input id="quantity" required class="form-control quantity-input" name="quantity[]" value="' + quantity + '" style="text-align:center"><div class="input-group-text qty-plus">+</div></div>';
                    var itemCode = '<input type="hidden" name="stockitemid[]" value="' + itemId + '">';
                    var priceInput = '<label class="mobile-label">{!!__("text.sale_price")!!}</label><input required class="form-control price-input" name="price[]" type="number" step="0.01" value="' + price + '" disabled>';
                    var realpriceInpput = '<label class="mobile-label">{!!__("text.real_price")!!}</label><input class="form-control realprice-input" name="realprice[]" type="number" step="0.01" value="' + price + '">';
                    var discountInput = '<label class="mobile-label">{!!__("text.discount")!!}</label><div class="d-flex align-items-center"><input id="discount" required class="form-control discount-input" name="discount[]" value="' + discount + '" style="margin-right: 5px" disabled /> {{ __("text.PLN") }}</div>';
                    // var unitInput = $("#unit_list").html();
                    var unitInput = `<select class="form-control unit-input" name="unit[]"><option value="${unitid}">${$("#unit_list").find("option[value="+unitid+"]").text()}</option><option value="${unitconverterto}">${$("#unit_list").find("option[value="+unitconverterto+"]").text()}</option></select>`;

                    var newRow = '<tr data-id="' + itemId + '" data-unitid="' + unitid + '" data-unitconverter="' + unitconverter + '" data-unitconverter1="' + unitconverter1 + '" data-price="' + price + '" data-unitid="' + unitid + '" data-unitconverterto="' + unitconverterto + '"><td class="mobile-inline"><div style="width: 95%"><span class="itemname">' + itemName + '</span><br/><span class="itemcode">' + itemCodeName + '</span></div><a href="#blank" class="remove-item mobile-label"><span class="material-symbols-rounded">delete</span></a></td><td style="display:flex;flex-direction:row">' + itemCode + quantityInput + unitInput + '</td><td>' + priceInput + '</td><td class="realprice-value">' + realpriceInpput + '</td><td class="discount-value">' + discountInput + '</td><td align="center">&nbsp;<a href="#blank" class="remove-item mobile-hide"><span class="material-symbols-rounded">delete</span></a></td></tr>';

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
                                element.closest('tr').find('.price-input').prop('disabled', false).val(element.closest('tr').data('price') * (element.closest('tr').data('unitconverter') / element.closest('tr').data('unitconverter1'))).prop('disabled', true);
                                element.closest('tr').find('.realprice-input').val(element.closest('tr').find('.realprice-input').val() * (element.closest('tr').data('unitconverter') / element.closest('tr').data('unitconverter1')));

                            } else {
                                element.closest('tr').find('.price-input').prop('disabled', false).val(element.closest('tr').data('price')).prop('disabled', true);
                                element.closest('tr').find('.realprice-input').val(element.closest('tr').find('.realprice-input').val() * (element.closest('tr').data('unitconverter1') / element.closest('tr').data('unitconverter')));
                            }
                            element.closest('tr').find('.discount-input').val(
                                element.closest('tr').find('.realprice-input').val() - element.closest('tr').find('.price-input').val()
                            );
                            calculateDiscount();
                        } else {
                            element.val(1);
                            alert("{!!__('text.not_available_qty')!!}");
                        }
                    }
                })
            })

            $('#selectedItemsTable').on('input', '.discount-input', function () {
                if ($("#discount_type2").prop("checked")) {
                    $(this).val(0);
                    return alert("you can't input");
                }
                calculateDiscount();
            });
            $("#selectedItemsTable").on('input', '.quantity-input', function() {
                calculateDiscount();
            })
            $("#selectedItemsTable").on('click', '.qty-plus', function() {
                var unitconverter = $(this).closest('tr').data('unitconverter');
                var unitconverter1 = $(this).closest('tr').data('unitconverter1');
                var old_val = $(this).prev().val();
                var new_val = 0;
                if (unitconverter > unitconverter1) {
                    new_val = old_val * 1 + unitconverter * 1;
                } else {
                    new_val = old_val * 1 + unitconverter1 * 1;
                }
                $(this).prev().val(new_val);
                $(this).prev().trigger('input');
            })

            $("#selectedItemsTable").on('click', '.qty-minus', function() {
                var unitconverter = $(this).closest('tr').data('unitconverter');
                var unitconverter1 = $(this).closest('tr').data('unitconverter1');
                var old_val = $(this).next().val();
                var new_val = 0;
                if (unitconverter > unitconverter1) {
                    new_val = old_val * 1 - unitconverter * 1;
                } else {
                    new_val = old_val * 1 - unitconverter1 * 1;
                }
                
                if (new_val > 0) {
                    $(this).next().val(new_val);
                    $(this).next().trigger('input');
                }
            })
            $("#selectedItemsTable").on('input', '.realprice-input', function() {
                if ($("#discount_type2").prop("checked")) {
                    return alert("you can't input");
                }
                $(this).closest("tr").find("input.discount-input").val($(this).val() - $(this).closest("tr").find("input.price-input").val())
                calculateDiscount();
            });


            const calculateDiscount = () => {
                if ($("#discount_type1").prop("checked")) {
                    var total_discount = 0;
                    $("#selectedItemsTable tbody tr").each(function() {
                        total_discount += $(this).find('.discount-input').val() * $(this).find('.quantity-input').val();
                    })
                    $("#total_discount").val(total_discount);
                }
            }
            const initDiscuount = () => {
                
                if (!$("#discount_type1").prop("checked")) {
                    $("#selectedItemsTable").find(".discount-value").hide();
                    $("#selectedItemsTable").find(".discount-header").hide();
                    $("#selectedItemsTable").find(".realprice-header").hide();
                    $("#selectedItemsTable").find(".realprice-value").hide();
                }
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
                        $("#selectedItemsBody tr").each(function() {
                            $(this).find("input.discount-input").val($(this).find("input.realprice-input").val() - $(this).find("input.realprice-input").closest("tr").find("input.price-input").val())
                        });
                        calculateDiscount();
                    }
                })
            }
            initDiscuount();
           
            jQuery("#contact").select2();

            //do the change
            $('#contact').val($("#contactselect").val()).trigger("change");

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

        });

           

        // Initialize jQuery Validation
        $('#editdataform').validate({
            rules: {
                reference: {
                    required: true,
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
                    $(".price-input").prop("disabled", false);
                    $(".discount-input").prop("disabled", false);
                    $("#noitem").addClass('d-none');
                    $("#total_discount").prop("disabled", false);
                    form.submit();
                }
            }
        });

        $("#payment_type").on('change', function() {
            $.ajax({
				url: '{{ route("transaction.getUpdatedShowRef") }}',
				method: 'GET',
				data: {
					payment_type: $(this).val(), 
					date: $("#date").val(),
					id: $("#id").val()
				},
				success: function(res) {
					$("#show_reference").val(res.updated_ref_num);
				}
			})
        })
        
    </script>
    @endpush
@endsection