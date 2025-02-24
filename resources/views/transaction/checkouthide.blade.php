@extends('layouts.app')
@section('title', __('text.checkout_items'))

@section('content')
<div class="body-inner">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
            <span class="material-symbols-rounded">task_alt</span> {{ session('success') }}
        </div>
    @endif
    <input type="hidden" value="{{$sellReference}}" id="sell_reference" />
    <div class="mb-4">
        <h2>{{ __('text.hide') }} {{$sellOrder->reference}}</h2>

        <div class="items-wrapper">
            @foreach($sellitems as $sellitem)
            <div class="mb-3 item">
                <div class="row" style="background-color: #e9e9e9; padding: 10px 0">
                    <div class="col-md-6">
                        <p style="font-weight: bold; margin-bottom: 0">
                            {{$sellitem->name}}({{$sellitem->code}}),
                            sold quantity:
                                <span id="total_amount_{{$sellitem->stockitemid}}" class="total-amount">
                                    {{$sellitem->quantity}}
                                </span> {{$sellitem->sellunitname}}
                            {{$sellitem->stockunitname}}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p style="margin-bottom: 0">{{__('text.hidden_amount')}}: <span class="hidden-amount" id="hidden_amount_{{$sellitem->stockitemid}}">0</span></p>
                    </div>
                </div>
                <div class="row mb-2">
                    <h5>{{__('text.selected_purchases')}}</h5>
                    <div id="selected_purchase_list_{{$sellitem->stockitemid}}" class="selected-purchase-list">
                        <div class="row" style="border-bottom: 1px solid #c3c3c3">
                            {{-- <div class="col-md-2" style="font-size: 14px">{{__('text.reference')}}</div>
                            <div class="col-md-2" style="font-size: 14px">{{__('text.supplier')}}</div>
                            <div class="col-md-2" style="font-size: 14px">{{__('text.date')}}</div> --}}
                            <div class="col-md-3" style="font-size: 14px">{{__('text.qty')}}</div>
                            <div class="col-md-2" style="font-size: 14px">{{__('text.hidden')}}</div>
                            <div class="col-md-2" style="font-size: 14px">{{__('text.new_hidden')}}</div>
                        </div>
                    </div>
                </div>
                <h5>{{__('text.purchase_list')}}</h5>
                <div class="table-responsive-sm">
                    <table class="table data-table" id="data{{$sellitem->id}}" data-stockitemid='{{$sellitem->stockitemid}}' >
                        <thead>
                            <tr>
                                <th>{{__('text.id')}}</th>
                                {{-- <th>{{__('text.reference')}}</th>
                                <th>{{__('text.name')}}</th>
                                <th>{{__('text.supplier')}}</th>
                                <th>{{__('text.date')}}</th> --}}
                                <th>{{__('text.quantity')}}</th>
                                <th>{{__('text.hidden_amount')}}</th>
                                <th>{{__('text.action')}}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            @endforeach
        </div>
        <div class="d-flex justify-content-end">
            <button class="btn btn-primary d-flex align-items-center" id="save">
                {{__('text.save')}}
            </button>
        </div>
    </div>
</div>
@push('scripts')
<script type="module">
$(function() {
    var selectedPurchaseList = [];
    $('.items-wrapper table').each(function() {
        var stockitemid = $(this).data('stockitemid');
        console.log(stockitemid)
        $(this).DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{!! route('transaction.getcheckinforhide') !!}',
                data: function (d) {
                    d.stockitemid = stockitemid;
                }
            },
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
                // {
                //     data: 'reference',
                //     name: 'reference'
                // },
                // {
                //     data: 'name',
                //     name: 'name'
                // },
                // {
                //     data: 'supplier',
                //     name: 'supplier'
                // },
                // {
                //     data: 'transactiondate',
                //     name: 'date'
                // },
                {
                    data: 'quantity',
                    name: 'quantity',
                    // render: function(data, type, row) {
                    //     return row.unitid == row.stockunitid ? row.quantity : row.quantity * row.unitconverter;
                    // }
                },
                {
                    data: 'hidden_amount',
                    name: 'hidden_amount',
                    // render: function(data, type, row) {
                        // return `<input type="number" value="${row.hidden_amount}" class="form-control hidden-amount" style="width: 100px" data-reference="${row.reference}" data-stockitemid="${row.stockitemid}" />`;
                    // }
                },
                {
                    data: 'action',
                    name: 'action',
                    // render: function(data, type, row) {
                    //     return `<button class"btn btn-primary d-flex align-items-center">Select</button>`;
                    // }
                }
            ],
            buttons: []
        });
    });

    $(".data-table").on('change', '.hidden-amount', function() {
        var reference = $(this).data('reference');
        var stockitemid = $(this).data('stockitemid');
        var total_amount = $(`#total_amount_${stockitemid}`).text() * 1;
        var old_hidden_amount = $(`#hidden_amount_${stockitemid}`).text() * 1;
        var hidden_amount = old_hidden_amount + $(this).val() * 1;
        if (hidden_amount > total_amount) {
            $(this).val(0);
            if (selectedPurchaseList.includes(reference)) {
                var index = selectedPurchaseList.indexOf(reference);
                // Remove the reference from the array if it exists
                if (index !== -1) {
                    selectedPurchaseList.splice(index, 1); // Remove 1 item at the found index
                }
            }
            return alert('error');
        } else {
            $(`#hidden_amount_${stockitemid}`).text(hidden_amount);
            if (!selectedPurchaseList.include(reference)) {
                selectedPurchaseList[reference] = $(this).val();
            } else {
                selectedPurchaseList[reference] = $(this).val();
            }
        }
    });

    $(".data-table").on('click', '.select-hidden', function() {
        var transactionid = $(this).data('transactionid');
        var stockitemid = $(this).data('stockitemid');
        // var reference = $(this).parent().parent().find("td:first-child").text();
        // var supplier = $(this).parent().parent().find("td:nth-child(3)").text();
        // var transactiondate = $(this).parent().parent().find("td:nth-child(4)").text();
        var quantity = $(this).parent().parent().find("td:first-child").text();
        var hiddenamount = $(this).parent().parent().find("td:nth-child(2)").text();
        appendSelectedTransaction(transactionid, quantity, hiddenamount, stockitemid)
    });

    function appendSelectedTransaction(transactionid, quantity, hiddenamount, stockitemid) {
        var exist = false;
        $($(`#selected_purchase_list_${stockitemid} div.row`)).each(function() {
            if ($(this).data('transactionid') == transactionid) {
                exist = true
            }
        })
        if (!exist) {
            var newChild = `<div class="row py-2 my-1 selected-purchase" style="border-bottom: 1px solid #c3c3c3" data-transactionid="${transactionid}" >\n

                <div class="col-md-3 qty" style="word-wrap: break-word; font-size: 14px">${quantity}</div>
                <div class="col-md-2 hidden-qty" style="word-wrap: break-word; font-size: 14px">${hiddenamount}</div>
                <div class="col-md-2" style="word-wrap: break-word; font-size: 14px"><input type="number" class="form-control input-selected-purchase-hidden" data-stockitemid="${stockitemid}" value="0"/></div>
                <div class="col-md-1" style="word-wrap: break-word; font-size: 14px"><button class="btn btn-sm btn-danger d-flex align-items-center delete-selected-purchase">
                            <span class="material-symbols-rounded">delete</span>
                        </button></div>
                </div>`;

            $(`#selected_purchase_list_${stockitemid}`).append(newChild);
        }
    }

    $(".selected-purchase-list").on('click', '.delete-selected-purchase', function() {
        var row = $(this).closest('.row').remove();
    })

    $(".selected-purchase-list").on('change', '.input-selected-purchase-hidden', function() {
        if($(this).val() * 1 + $(this).closest(".row").find('.hidden-qty').text() * 1 > $(this).closest(".row").find('.qty').text()) {
            alert("{!!__('text.not_over_qty')!!}");
            $(this).val(0);
            return;
        }
        let total_new_hidden = 0;
        $(this).closest('.selected-purchase-list').find("input.input-selected-purchase-hidden").each(function() {
            let value = $(this).val().trim(); // Trim any whitespace
            let parsedValue = parseFloat(value); // Parse the value as a float
            if (!isNaN(parsedValue)) {
                total_new_hidden += parsedValue; // Add valid numbers to total_new_hidden
            }
        });

        var totalamount = $(`#total_amount_${$(this).data("stockitemid")}`).text() * 1;
        if (total_new_hidden > totalamount) {
            alert("error");
            $(this).val(0);
        } else {
            $(`#hidden_amount_${$(this).data("stockitemid")}`).text(total_new_hidden);
        }
    });

    $("#save").on('click', function() {
        // validation.
        // check all numbers are matched.
        var matched = true;
        $(".items-wrapper .item").each(function() {
            console.log($(this).find(".total-amount").text().trim() * 1, '=====', $(this).find(".hidden-amount").text().trim() * 1 );
            if ($(this).find(".total-amount").text().trim() * 1 != $(this).find(".hidden-amount").text().trim() * 1) {
                matched = false;
            }
        });
        // console.log(matched)
        // return;
        if (matched) {
            let selectedPurchases = [];
            $(".selected-purchase").each(function() {
                if ($(this).find('input.input-selected-purchase-hidden').val() > 0) {
                    selectedPurchases.push({
                        transactionid: $(this).data('transactionid'),
                        new_hidden: $(this).find('input.input-selected-purchase-hidden').val()
                    })
                }
            });

            $.ajax({
                url: '{!!route("transaction.savecheckouthidden")!!}',
                type: 'GET',
                data: {
                    selectedPurchases,
                    sellReference: $("#sell_reference").val()
                },
                success: function(data) {
                    alert('success');
                    window.location.href = '{!!route("transaction.hiddehistory", ["id" => $sellOrder->reference])!!}'
                }
            })
        } else {
            alert("{!!__('text.not_match_hidden')!!}");
        }
    })
});
</script>
@endpush
@endsection
