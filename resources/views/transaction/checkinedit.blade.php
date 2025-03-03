@extends('layouts.app')
@section('title', __('text.checkin_items'))

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

        #selectedItemsTable tfoot tr {
            display: flex; /* Hide table headers on smaller screens */
            flex-direction: column;
        }

        #selectedItemsTable tfoot th {
            width: 100%;
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
        <h2>{{__('text.checkin_items')}}</h2>
       
        
        <form id="editdataform" method="POST" action="{{ route('transaction.updatecheckin') }}" enctype="multipart/form-data"> 
                @csrf
                    <input type="hidden" id="id" name="id" value="{{ $singletransaction->order_id }}">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">{{__('text.date')}}</label>
                                <input type="date" class="form-control" id="date" name="transactiondate" value="{{ date('Y-m-d',strtotime($singletransaction->transactiondate))}}" required >
                            </div>
                        </div>
                        <div class="col-md-6"></div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="reference" class="form-label">{{__('text.reference_number')}}</label>
                                <input type="text" value="{{$singletransaction->reference}}" readonly class="form-control" id="reference" name="reference" placeholder="Reference" required >
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="show_reference" class="form-label">{{ __('text.transaction_number') }}</label>
                                <input type="text" value="{{$singletransaction->show_reference}}" class="form-control" id="show_reference" name="show_reference" placeholder="{{ __('text.reference') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="contact" class="form-label">{{__('text.supplier')}}</label>
                                <input type="hidden" name="contactselect" id="contactselect" value="{{$singletransaction->contactid}}"/>
                                <select name="contactid" id="contact" class="form-control" required>
                                    <option value="">{{__('text.select')}}...</option>
                                @foreach($contacts as $contact)
                                    <option value="{{ $contact->id }}">{{ $contact->name }}</option>
                                @endforeach
                                </select>
                                <label for="contact" class="error"></label>
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
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="item" class="form-label">{{__('text.item')}}</label>
                                    <input type="text" class="form-control" id="item" name="item" placeholder="{{__('text.search_code_or_item_name')}}..."  >
                                    <ul id="searchResults"></ul>
                                    <div class="searchitemresult">
                                       
                                        
                                        <table id="selectedItemsTable" width="100%" class=""> 
                                            <thead>
                                                <tr>
                                                    <th width="40%"><small>{{__('text.item_name')}}</small></th>
                                                    <th width="30%"><small>{{__('text.quantity')}}</small></th>
                                                    <th width="15%"><small>{{__('text.price')}}</small></th>
                                                    <th width="15%"><small>{{__('text.total_price')}}</small></th>
                                                    <th width="10%"></th>
                                                </tr>
                                            </thead>
                                            <tbody id="selectedItemsBody">
                                            @foreach($transaction as $item)
                                                @if($hasSeeHiddenPermission || $item->quantity - $item->hidden_amount > 0)
                                                <tr data-id="{{$item->stockitemid}}">
                                                    <td class="mobile-inline">
                                                        <div style="width: 95%">
                                                            <span class="itemname">{{$item->name}}</span>
                                                            <br/><span class="itemcode">{{$item->code}}</span>
                                                        </div>
                                                        <a href="#blank" class="remove-item mobile-label">
                                                            <span class="material-symbols-rounded">delete</span>
                                                        </a>
                                                    </td>
                                                    
                                                    <td style="display: flex; flex-direction: row">
                                                        <input type="hidden" name="stockitemid[]" value="{{$item->stockitemid}}">
                                                        <div style="width:100%">
                                                        <input id="quantity" required class="form-control quantity-input" name="quantity[]" type="number" min="1" value="{{$hasSeeHiddenPermission ? $item->quantity : $item->quantity - $item->hidden_amount}}">
                                                        </div>
                                                        <div style="width:100%">
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
                                                        <input id="price" required class="form-control price-input" name="price[]" type="number" min="0" value="{{$item->price}}">
                                                    </td>
                                                    
                                                    <td>
                                                        <label class="mobile-label">{!!__("text.total_price")!!}</label>
                                                        <input id="subtotal_price" required class="form-control subtotal_price-input" name="subtotal_price[]" type="number" min="0" value="0">
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
                                                    <th width="40%"></th>
                                                    <th width="30%"><small>{{__('text.total_quantity')}}:&nbsp;</small><span id="total_quantity">0</span>&nbsp;{{__('text.carton')}}</th>
                                                    <th width="5%"></th>
                                                    <th width="20%"><small>{{__('text.total_price')}}:&nbsp;</small><span id="total_price">0</span>&nbsp;{{ __('text.PLN') }}</th>
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
                
                var query = $(this).val();

                if (query.length >= 2) { // Minimum characters to trigger the search
                    $.ajax({
                        url: '{{ route("transaction.searchitem") }}',
                        method: 'GET',
                        data: { query: query },
                        success: function (data) {
                            $('#searchResults').empty();

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
                var unitid = $(this).data('unitid');
                var unitconverterto = $(this).data('unitconverterto');
                var itemCodeName = $(this).find('.itemcode').data('code');
                var itemName = $(this).find('.itemname').data('name');
                var quantity = 1; // default quantity
                var price = 0; // default price

                // Check if the item already exists in the table
                var existingRow = $('#selectedItemsBody tr[data-id="' + itemId + '"]');
                if (existingRow.length > 0) {
                    // Item already exists, update the quantity
                    var currentQuantity = parseInt(existingRow.find('.quantity-input').val());
                    existingRow.find('.quantity-input').val(currentQuantity + 1);
                } else {
                    // Item does not exist, add a new row
                    // var quantityInput = '<input id="quantity" required class="form-control quantity-input" name="quantity[]" type="number" min="1" value="' + quantity + '">';
                    // var priceInput = '<input id="price" required class="form-control price-input" name="price[]" type="number" min="0" step="0.01" value="' + price + '">';
                    // var subtotal_priceInput = '<input id="subtotal_price" required class="form-control subtotal_price-input" name="subtotal_price[]" type="number" min="0" step="0.01" value="' + price + '">';
                    
                    // var unitInput = $("#unit_list").html()

                    // var itemCode = '<input type="hidden" name="stockitemid[]" value="' + itemId + '">';
                    // var newRow = '<tr data-id="' + itemId + '"><td><span class="itemname">' + itemName + '</span><br/><span class="itemcode">' + itemCodeName + '</span></td><td>' + itemCode + quantityInput + '</td><td class="unitInput">' + unitInput + '</td><td>' + priceInput +  '</td><td>' + subtotal_priceInput +  '</td><td align="center">&nbsp;<a href="#blank" class="remove-item"><span class="material-symbols-rounded">delete</span></a></td></tr>';
                    // $('#selectedItemsBody').append(newRow);
                    var quantityInput = '<div style="width:100%"><input id="quantity" required class="form-control quantity-input" name="quantity[]" type="number" min="1" value="' + quantity + '"></div>';
                    var priceInput = '<label class="mobile-label">{!!__("text.price")!!}</label><input id="price" required class="form-control price-input" name="price[]" type="number" min="0" step="0.01" value="' + price + '">';
                    var subtotal_priceInput = '<label class="mobile-label">{!!__("text.total_price")!!}</label><input id="subtotal_price" required class="form-control subtotal_price-input" name="subtotal_price[]" type="number" min="0" step="0.01" value="' + price + '">';
                    // var unitInput = `<select class="form-control" name="unit[]"><option value="${unitid}">${$("#unit_list").find("option[value="+unitid+"]").text()}</option><option value="${unitconverterto}">${$("#unit_list").find("option[value="+unitconverterto+"]").text()}</option></select>`;
                    var unitInput = $("#unit_list").html()
                    
                    var itemCode = '<input type="hidden" name="stockitemid[]" value="' + itemId + '">';
                    var newRow = '<tr data-id="' + itemId + '"><td class="mobile-inline"><div style="width: 95%"><span class="itemname">' + itemName + '</span><br/><span class="itemcode">' + itemCodeName + '</span></div><a href="#blank" class="remove-item mobile-label"><span class="material-symbols-rounded">delete</span></a></td><td style="display:flex;flex-direction:row">' + itemCode + quantityInput + '<div class="unitInput"  style="width:100%">' + unitInput + '</div></td><td>' + priceInput + '</td><td>' + subtotal_priceInput + '</td><td align="center">&nbsp;<a href="#blank" class="remove-item mobile-hide"><span class="material-symbols-rounded">delete</span></a></td></tr>';
                    $('#selectedItemsBody').append(newRow);
                }


                // Clear the search input and results
                $('#searchInput').val('');
                $('#searchResults').empty();
                calculateTotals();
            });

            // Handle click to remove item from the table
            $('#selectedItemsTable').on('click', '.remove-item', function () {
                $(this).closest('tr').remove();
                // You can add an AJAX call here to remove the item from the server-side storage.
            });



           
            jQuery("#contact").select2();

            //do the change
            $('#contact').val($("#contactselect").val()).trigger("change");
            $("#date").on('change', function() {
                $.ajax({
                    url: '{{ route("transaction.getUpdatedPurchaseShowRef") }}',
                    method: 'GET',
                    data: {date: $(this).val(), id: $("#id").val()},
                    success: function(res) {
                        $("#show_reference").val(res.updated_ref_num);
                    }
                })
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
                    $("#noitem").addClass('d-none');
                    form.submit();
                }
            }
        });
        $('#selectedItemsTable').on('click', '.remove-item', function () {
            $(this).closest('tr').remove();
            calculateTotals();
        });
        // Function to calculate the total quantity and price
        function calculateTotals() {
            let totalQuantity = 0;
            let totalPrice = 0;

            // Loop through each row in the table
            $('#selectedItemsBody tr').each(function() {
                // Get the quantity and price values
                let quantity = parseFloat($(this).find('input[name="quantity[]"]').val());
                let price = parseFloat($(this).find('input[name="price[]"]').val());

                // Add to total quantity and price                    
        let subtotal = quantity * price;
                $(this).find('input[name="subtotal_price[]"]').val(subtotal);
                totalQuantity += quantity;
                totalPrice += subtotal;
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

    </script>
    @endpush
@endsection

