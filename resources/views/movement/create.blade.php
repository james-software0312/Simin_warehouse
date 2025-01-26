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
            display: -webkit-inline-box;
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
    <div class="">
        <h2>{{__('text.movement_items')}}</h2>
        <form id="adddataform" method="POST" action="{{ route('movement.store') }}" enctype="multipart/form-data"> 
                @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <label for="name" class="form-label">{{__('text.date')}}</label>
                            <input type="date" class="form-control" id="date" name="movement_date" formart="dd/mm/YYYY" value="{{ date('Y-m-d') }}" required >
                        </div>
                        <div class="col-md-6">
                            <label for="reference" class="form-label">{{__('text.reference_number')}}</label>
                            <input type="text" value="{{$ref}}" class="form-control" id="reference" name="reference" placeholder="Reference" required >
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label for="warehouse" class="form-label">{{__('text.source_warehouse')}}</label>
                            <select name="source_warehouse_id" id="source_warehouse_id" class="form-control" required>
                                <option value="">{{__('text.select')}}...</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                            @endforeach
                            </select>
                            <label for="source_warehouse_id" class="error"></label>
                        </div>
                        <div class="col-md-6">
                            <label for="warehouse" class="form-label">{{__('text.target_warehouse')}}</label>
                            <select name="target_warehouse_id" id="target_warehouse_id" class="form-control" required>
                                <option value="">{{__('text.select')}}...</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                            @endforeach
                            </select>
                            <label for="target_warehouse_id" class="error"></label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="item" class="form-label">{{__('text.item')}}</label>
                                    <input type="text" class="form-control" id="item" name="item" placeholder="Search code or item name..."  >
                                    <ul id="searchResults"></ul>
                                    <div class="searchitemresult">
                                        <small id="searchresultmsg" class="text-left mb-0">{{__('text.search_results')}}...</small>
                                        
                                        <table id="selectedItemsTable" width="100%" class="d-none" style="display: inline"> 
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
                                                <!-- Selected items will be added here dynamically -->
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th width="40%" class="mobile-label"></th>
                                                    <th width="15%"><small>{{__('text.total_quantity')}}:&nbsp;</small><span id="total_quantity">0</span>&nbsp;carton</th>
                                                    <th width="15%" class="mobile-label"></th>
                                                    <th width="15%"><small>{{__('text.total_price')}}:&nbsp;</small><span id="total_price">0</span>{{ __('text.PLN') }}</th>
                                                    <th width="10%" class="mobile-label"></th>
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
                                <textarea id="description" class="form-control" name="description" placeholder="{{__('text.note')}}"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                        <input type="hidden" name="selectedItems" id="selectedItemsInput">
                        <button type="submit" class="btn btn-primary d-flex align-items-center" id="submit"><span class="material-symbols-rounded">check</span> {{__('text.submit')}}</button>
                        </div>
                    </div>
            </form>
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

</div>


@push('scripts')
<script type="module">
    
    $(function() {
        
        //Search Item
            $('#item').on('input', function () {
                var warehouseid = $("#source_warehouse_id").val();
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
                                $('#searchResults').append('<li class="search-result" data-id="' + item.id + '" data-price="' + item.price + '" data-unitid="' + item.unitid + '" data-unitconverterto="' + item.unitconverterto + '"data-unitconverter="' + item.unitconverter + '" data-unitconverter1="' + item.unitconverter1 + '" data-itemquantity="' + item.quantity + '"><span data-name="'+item.name+'" class="itemname">' + item.name + '(' + item.quantity + ' ' + item.unitname + ')</span><br/><span data-code="'+item.code+'" class="itemcode">'+item.code+'</span></li>');
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
            var unitconverter =  $(this).data('unitconverter');
            var unitconverter1 =  $(this).data('unitconverter1');
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
                var quantityInput = '<div class="input-group" style="margin-right: 10px"><div class="input-group-text qty-minus">-</div><input id="quantity" required class="form-control quantity-input" name="quantity[]" value="' + quantity + '" style="text-align:center;"><div class="input-group-text qty-plus">+</div></div>';
                var priceInput = '<label class="mobile-label">{!!__("text.price")!!}</label><input id="price" required class="form-control price-input" name="price[]" type="number" min="0" step="0.01" value="' + price + '" disabled>';
                var subtotal_priceInput = '<label class="mobile-label">{!!__("text.total_price")!!}</label><input id="subtotal_price" required class="form-control subtotal_price-input" name="subtotal_price[]" type="number" min="1" step="0.01" value="' + price + '" disabled>';
                // var unitInput = `<select class="form-control" name="unit[]"><option value="${unitid}">${$("#unit_list").find("option[value="+unitid+"]").text()}</option><option value="${unitconverterto}">${$("#unit_list").find("option[value="+unitconverterto+"]").text()}</option></select>`;
                var unitInput = $("#unit_list").html()
                
                var itemCode = '<input type="hidden" name="stockitemid[]" value="' + itemId + '">';
                var newRow = '<tr data-id="' + itemId + '" data-unitconverter="' + unitconverter + '" data-unitconverter1="' + unitconverter1 + '"><td class="mobile-inline"><div style="width: 95%"><span class="itemname">' + itemName + '</span><br/><span class="itemcode">' + itemCodeName + '</span></div><a href="#blank" class="remove-item mobile-label"><span class="material-symbols-rounded">delete</span></a></td><td style="display:flex;flex-direction:row">' + itemCode + quantityInput + '<div class="unitInput" style="width: 100%">' + unitInput + '</div></td><td>' + priceInput + '</td><td>' + subtotal_priceInput + '</td><td align="center">&nbsp;<a href="#blank" class="remove-item mobile-hide"><span class="material-symbols-rounded">delete</span></a></td></tr>';
                $('#selectedItemsBody').append(newRow);

                // select for product unit as default
                $('#selectedItemsBody').find("tr[data-id='" + itemId + "']").find(".unitInput").find("select").find('option').filter(function() {
                    return $(this).val() == unitid;  // Change 'Unit 2' to the text you want to match
                }).prop('selected', true);
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

        $("#selectedItemsTable").on('click', '.qty-plus', function() {
            var old_val = $(this).prev().val();
            var new_val = old_val * 1 + 1;
            $(this).prev().val(new_val);
            $(this).prev().trigger('change');
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
    });

    // Initialize jQuery Validation
    $('#adddataform').validate({
        rules: {
            reference: {
                required: true,
                uniquecode:true 
            }
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
    $('#editdataform').validate({
        rules: {
            reference: {
                required: true,
                uniquecodeedit:true
            },
        },
        
        submitHandler: function (form) {
            form.submit();
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

