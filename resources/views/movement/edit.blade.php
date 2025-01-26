@extends('layouts.app')
@section('title', __('text.movement_items'))

@section('content')
<style>
    .selectedItemsTable {
        display: inline;
    }
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
            display: flex;
            flex-direction: row;
        }

        #selectedItemsTable tfoot tr {
            display: flex; /* Hide table headers on smaller screens */
            flex-direction: column;
        }

        #selectedItemsTable tfoot th {
            width: 100%;
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
        <h2>{{__('text.movement_items')}}</h2>
        
        <form id="editdataform" method="POST" action="{{ route('movement.update') }}" enctype="multipart/form-data"> 
                @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <label for="name" class="form-label">{{__('text.date')}}</label>
                            <input type="date" class="form-control" id="date" name="movement_date" value="{{ date('Y-m-d',strtotime($singletransaction->movement_date))}}"  required >
                        </div>
                        <div class="col-md-6">
                            <label for="reference" class="form-label">{{__('text.reference_number')}}</label>
                            <input type="text" value="{{$singletransaction->reference}}" readonly class="form-control" id="reference" name="reference" placeholder="Reference" required >
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label for="warehouse" class="form-label">{{__('text.source_warehouse')}}</label>
                            <select name="source_warehouse_id" id="source_warehouse_id" class="form-control" required>
                                <option value="">{{__('text.select')}}...</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" @if($singletransaction->source_warehouse_id == $warehouse->id) selected @endif>{{ $warehouse->name }}</option>
                            @endforeach
                            </select>
                            <label for="source_warehouse_id" class="error"></label>
                        </div>
                        <div class="col-md-6">
                            <label for="warehouse" class="form-label">{{__('text.target_warehouse')}}</label>
                            <select name="target_warehouse_id" id="target_warehouse_id" class="form-control" required>
                                <option value="">{{__('text.select')}}...</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" @if($singletransaction->target_warehouse_id == $warehouse->id) selected @endif>{{ $warehouse->name }}</option>
                            @endforeach
                            </select>
                            <label for="target_warehouse_id" class="error"></label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="item" class="form-label">{{__('text.item')}}</label>
                                    <input type="text" class="form-control" id="item" name="item" placeholder="{{__('text.search_code_or_item_name')}}..." readonly >
                                    <ul id="searchResults"></ul>
                                    <div class="searchitemresult">
                                       
                                        
                                        <table id="selectedItemsTable" width="100%" class="" style="display: inline"> 
                                            <thead>
                                                <tr>
                                                    <th width="50%"><small>{{__('text.item_name')}}</small></th>
                                                    <th width="30%"><small>{{__('text.quantity')}}</small></th>
                                                    <th width="15%"><small>{{__('text.price')}}</small></th>
                                                    <th width="10%"></th>
                                                </tr>
                                            </thead>
                                            <tbody id="selectedItemsBody">
                                            @foreach($transaction as $item)
                                                @if($hasSeeHiddenPermission || $item->quantity - $item->hidden_amount > 0)
                                                <tr 
                                                    data-id="{{$item->stockitemid}}"
                                                    data-unitconverter="{{$item->unitconverter}}"
                                                    data-unitconverter1="{{$item->unitconverter1}}"
                                                >
                                                    <td class="mobile-inline">
                                                        <div style="width: 95%">
                                                            <span class="itemname">{{$item->name}}</span>
                                                            <br/><span class="itemcode">{{$item->code}}</span>
                                                        </div>
                                                        <a href="#blank" class="remove-item mobile-label">
                                                            <span class="material-symbols-rounded">delete</span>
                                                        </a>
                                                    </td>
                                                    
                                                    <td class="mobile-inline" style="display: flex;">
                                                        <input type="hidden" name="stockitemid[]" value="{{$item->stockitemid}}">
                                                        <div class="input-group" style="margin-right: 10px">
                                                            <div class="input-group-text qty-minus">-</div>
                                                            <input id="quantity" required class="form-control quantity-input" name="quantity[]" type="number" min="1" value="{{$hasSeeHiddenPermission ? $item->quantity : $item->quantity - $item->hidden_amount}}">
                                                            <div class="input-group-text qty-plus">+</div>
                                                        </div>
                                                        <div class="unitInput" style="width: 100%">
                                                            <select class="hidden form-control" name="unit[]">
                                                            @foreach($units as $unit)
                                                            @if($unit->name=='karton')
                                                                <option value="{{$unit->id}}" {{$item->unitid == $unit->id ? 'selected' : ''}}>{{$unit->name}}</option>
                                                            @endif
                                                            @endforeach
                                                            </select>
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <label class="mobile-label">{!!__("text.price")!!}</label>
                                                        <input id="price" required class="form-control price-input" name="price[]" type="number" min="1" value="{{$item->price}}" disabled>
                                                    </td>

                                                    <td valign="center">&nbsp;
                                                        <a href="#blank" class="remove-item mobile-hide">
                                                        <span class="material-symbols-rounded">delete</span>
                                                        </a>
                                                    </td>
                                                </tr>
                                                @endif
                                            @endforeach
                                                <!-- Selected items will be added here dynamically -->
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th width="50%"></th>
                                                    <th width="15%"><small>{{__('text.total_quantity')}}:&nbsp;</small><span id="total_quantity">0</span>carton</th>
                                                    <th width="15%"><small>{{__('text.total_price')}}:&nbsp;</small><span id="total_price">0</span>{{ __('text.PLN') }}</th>
                                                    <th width="10%"></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                        <input type="hidden" name="itemselecteds" id="itemselecteds" required/>
                                        <label for="quantity" class="error"></label>
                                        
                                    </div>
                                    <small id="noitem" for="noitem" class="text-red d-none">{{__('text.no_item_selected')}}.</small>
                                </div>
                        </div>
                    </div>

                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="description" class="form-label">{{__('text.note')}}</label>
                                <textarea id="description" class="form-control" name="description" placeholder="{{__('text.note')}}">{{$item->description}}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 d-flex justify-content-end">
                            <input type="hidden" name="selectedItems" id="selectedItemsInput">
                            <a href="{{route('transaction.checkinlist')}}" class="btn btn-danger d-flex align-items-center">
                                <span class="material-symbols-rounded">close</span>{{__('text.Cancel')}}</a>&nbsp;&nbsp;
                            <button type="submit" class="btn btn-primary d-flex align-items-center" id="submit"><span class="material-symbols-rounded">check</span> {{__('text.submit')}}</button>
                        </div>
                    </div>
            </form>
    </div>
    <div id="unit_list" style="display: none">
        <select class="hidden form-control" name="unit[]">
        @foreach($units as $unit)
        @if($unit->name=='karton')
            <option value="{{$unit->id}}">{{$unit->name}}</option>
        @endif
        @endforeach
        </select>
    </div>
</div>
<!-- Define the modal content and title -->
     



@push('scripts')
<script type="module">
    
        $(function() {
            
            //Search Item
            $('#item').on('input', function () {
                var warehouseid = $("#source_warehouse").val();
            var query = $(this).val();

                if (query.length >= 2) { // Minimum characters to trigger the search
                    $.ajax({
                        url: '{{ route("transaction.searchitem") }}',
                        method: 'GET',
                        data: { query: query, warehouseid: warehouseid },
                        success: function (data) {
                            $('#searchResults').empty();
                            data = data.filter((item)=>(item.single_quantity > 0));

                            $.each(data, function (index, item) {
                                $('#searchResults').append('<li class="search-result" data-id="' + item.id + '" data-unitid="' + item.unitid + '" data-unitconverterto="' + item.unitconverterto + '"><span data-name="'+item.name+'" class="itemname">' + item.name + '</span><br/><span data-code="'+item.code+'" class="itemcode">'+item.code+'</span></li>');
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
                var price =  $(this).data('price');
                var unitid = $(this).data('unitid');
                var unitconverterto = $(this).data('unitconverterto');
                var itemCodeName = $(this).find('.itemcode').data('code');
                var itemName = $(this).find('.itemname').data('name');
                var quantity = 1; // default quantity

                // Check if the item already exists in the table
                var existingRow = $('#selectedItemsBody tr[data-id="' + itemId + '"]');
                if (existingRow.length > 0) {
                    // Item already exists, update the quantity
                    var currentQuantity = parseInt(existingRow.find('.quantity-input').val());
                    existingRow.find('.quantity-input').val(currentQuantity + 1);
                } else {
                    // Item does not exist, add a new row
                    var quantityInput = '<input id="quantity" required class="form-control quantity-input" name="quantity[]" type="number" min="1" value="' + quantity + '">';
                    var priceInput = '<input id="price" required class="form-control price-input" name="price[]" type="number" min="1" step="0.01" value="' + price + '" disabled>';
                    // var unitInput = `<select class="form-control" name="unit[]"><option value="${unitid}">${$("#unit_list").find("option[value="+unitid+"]").text()}</option><option value="${unitconverterto}">${$("#unit_list").find("option[value="+unitconverterto+"]").text()}</option></select>`;
                    var unitInput = $("#unit_list").html()

                    var itemCode = '<input type="hidden" name="stockitemid[]" value="' + itemId + '">';
                    var newRow = '<tr data-id="' + itemId + '"><td class="mobile-inline"><div style="width: 95%"><span class="itemname">' + itemName + '</span><br/><span class="itemcode">' + itemCodeName + '</span><a href="#blank" class="remove-item mobile-label"><span class="material-symbols-rounded">delete</span></a></div></td><td>' + itemCode + quantityInput + '</td><td class="unitInput">' + unitInput + '</td><td>' + priceInput +  '</td><td align="center">&nbsp;<a href="#blank" class="remove-item mobile-hide"><span class="material-symbols-rounded">delete</span></a></td></tr>';
                    $('#selectedItemsBody').append(newRow);
                }


                // Clear the search input and results
                $('#searchInput').val('');
                $('#searchResults').empty();
            });

            // Handle click to remove item from the table
            $('#selectedItemsTable').on('click', '.remove-item', function () {
                $(this).closest('tr').remove();
                // You can add an AJAX call here to remove the item from the server-side storage.
            });

            $("#selectedItemsTable").on('click', '.qty-plus', function() {
                var old_val = $(this).prev().val();
                var new_val = old_val * 1 + 1;
                $(this).prev().val(new_val);
                $(this).prev().trigger('input');
            })

            $("#selectedItemsTable").on('click', '.qty-minus', function() {
                var old_val = $(this).next().val();
                var new_val = old_val * 1 - 1;
                if (new_val > 0) {
                    $(this).next().val(new_val);
                    $(this).next().trigger('change');
                }
            })
           
            jQuery("#contact").select2();

            //do the change
            $('#contact').val($("#contactselect").val()).trigger("change");

            

        });

           

        // Initialize jQuery Validation
        $('#editdataform').validate({
            rules: {
                reference: {
                    required: true,
                },
            },
            messages: {
                movement_date: {
                    required: '{!!__('text.field_required')!!}'
                },
                reference: {
                    required: '{!!__('text.field_required')!!}'
                },
                source_warehouse_id: {
                    required: '{!!__('text.field_required')!!}'
                },
                target_warehouse_id: {
                    required: '{!!__('text.field_required')!!}'
                },
            },
            submitHandler: function (form) {
                if ($('#selectedItemsBody tr').length === 0) {
                    // Display a required message (you can customize this based on your needs)
                    $("#noitem").removeClass('d-none');
                    
                    return false; // Prevent form submission
                }else{
                    $("#noitem").addClass('d-none');
                    form.submit();
                }
            }
        });
        $(document).ready(function() {
            // Function to calculate the total quantity and price
            function calculateTotals() {
                let totalQuantity = 0;
                let totalPrice = 0;

                // Loop through each row in the table
                $('#selectedItemsBody tr').each(function() {
                    // Get the quantity and price values
                    let quantity = parseFloat($(this).find('.quantity-input').val()) || 0;
                    let price = parseFloat($(this).find('.price-input').val()) || 0;

                    // Add to total quantity and price
                    totalQuantity += quantity;
                    totalPrice += quantity * price;
                });

                // Update the total values in the footer
                $('#total_quantity').text(totalQuantity.toFixed(0));
                $('#total_price').text(totalPrice.toFixed(2));
            }

            // Trigger calculation on input changes
            $(document).on('input', '.quantity-input, .price-input', function() {
                calculateTotals();
            });

            // Initial calculation on page load
            calculateTotals();
        });

        $(document).on('change', '.quantity-input', function () {
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
                    if (!data.avaiable) {
                        element.val(1);
                        alert("{!!__('text.not_available_qty')!!}");
                    }
                }
            })
        });

    </script>
    @endpush
@endsection

