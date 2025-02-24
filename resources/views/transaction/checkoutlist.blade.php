@extends('layouts.app')
@section('title', __('text.checkout_list'))

@section('content')
<style>
    .form-check-input {
        width: 60px;  /* Width of the switch */
        height: 30px; /* Height of the switch */
    }

    .form-check {
        font-size: 1.5rem; /* Adjust the font size */
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
        <span class="material-symbols-rounded">close</span> {{ session('error') }}
    </div>
    @endif

    <div class="mb-4 ">
        <div class="row">
            <div class="col-md-6 col-sm-12">
                <h2>{{ __('text.checkout_list') }}</h2>
            </div>
            <div class="col-md-6 col-sm-12">
                <div class="d-flex align-items-center justify-content-end">
                    <a href="#" class="btn btn-green d-flex align-items-center" id="btn_print" style="margin-right: 10px">
                        <span class="material-symbols-rounded">print</span>{{ __('text.print') }}</a>
                    <a href="{{ route('transaction.checkout') }}" class="btn btn-primary d-flex align-items-center" style="margin-right: 10px">
                        <span class="material-symbols-rounded">add</span>{{ __('text.add') }}</a>
                        @if($hasSeeHiddenPermission)
                            <a href="#" class="btn btn-red d-flex align-items-center d-none" id="btn_delete" >
                                <span class="material-symbols-rounded">delete</span>{{ __('text.hidden_delete') }}
                            </a>
                        @endif
                </div>
            </div>
        </div>
    </div>
    <!-- filter -->
    <div class="border-top pt-2">
        <h4 class="mb-2">{{__('text.filter_data')}}</h4>
        <form id="filter_checkoutlist" method="POST" class="mb-4">
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
                <div class="col-md-4 ">
                    <label for="startdate" class="form-label">{{__('text.start_date')}}</label>
                    <input type="date" class="form-control" id="startdate" name="startdate" lang="pl">
                </div>
                <div class="col-md-4 ">
                    <label for="enddate" class="form-label">{{__('text.end_date')}}</label>
                    <input type="date" disabled class="form-control" id="enddate" name="enddate" required>
                </div>

            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="mb-3 d-flex">
                        <button type="input" class="btn btn-primary d-flex align-items-center" id="button">
                            <span class="material-symbols-rounded">check</span> {{__('text.apply_filters')}}
                        </button>
                        <button type="input" class="btn btn-yellow d-flex align-items-center" style="margin-left: 5px" id="btn_reset">
                            <span class="material-symbols-rounded">check</span> {{__('text.reset')}}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <!-- end filter -->
    <div class="table-responsive-sm">
        <table class="table" id="data">
            <thead>
                <tr>
                    <th>{{ __('text.id') }}</th>
                    <th></th>
                    <th>{{ __('text.transaction_number') }}</th>
                    <th>{{ __('text.customer') }}</th>
                    <th>{{ __('text.date') }}</th>
                    <th>{{ __('text.pre_order') }}</th>
                    <th>{{ __('text.with_invoice') }}</th>
                    <th>{{ __('text.confirmed') }}</th>
                    <th>{{ __('text.payment_type') }}</th>
                    <th>{{ __('text.note') }}</th>
                    <th>{{ __('text.action') }}</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Define the modal content and title -->

<x-delete>
    <form id="deletedataform" method="POST" action="{{ route('checkout.transaction.destroy')}}">
        @csrf
        <input type="hidden" name="deleteid" id="deleteid" value="">
        <input type="hidden" name="status" id="status" value="2">
    </form>
</x-delete>


<div class="modal fade" id="detailModel" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <!-- Modal content -->
    <div class="modal-dialog modal-lg">
        <div class="modal-content ">
            <!-- Modal header -->
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ __('text.checkout_detail') }}</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="{{ __('text.close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <!-- Modal body -->
            <div class="modal-body overflow-scroll ">
                <div class="p-4">

                    <div class="row border-bottom mb-3 pb-4">
                        <div class="col-md-2 mb-3">
                            <img src="" alt="{{ __('text.barcode') }}" width="100" class="qrcodedetail" />
                        </div>
                        <div class="col-md-3 mb-3">
                            <small class="mb-0 text-neutral-80"><strong>{{ __('text.reference') }}:</strong></small>
                            <p class="mb-0 referencecontent">reference</p>
                        </div>
                        <div class="col-md-3 mb-3">
                            <small class="mb-0 text-neutral-80"><strong>{{ __('text.date') }}:</strong></small>
                            <p class="mb-0 datecontent">date</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <small class="mb-0 text-neutral-80"><strong>{{ __('text.customer') }}:</strong></small>
                            <p class="mb-0 suppliercontent">customer</p>
                            <p class="mb-0 companycontent">company</p>
                            <p class="mb-0 emailcontent">email</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <table border="0" width="100%" class="table ">
                                <thead>
                                    <tr>
                                        <th width="80%"><small class="text-neutral-80">{{ __('text.item_name') }}</small></th>
                                        <th width="6%"><small class="text-neutral-80">{{ __('text.quantity') }}</small></th>
                                        <th width="6%"><small class="text-neutral-80">{{ __('text.quantity_converted') }}</small></th>
                                        <th width="6%"><small class="text-neutral-80">{{ __('text.price') }}</small></th>
                                        <th width="6%"><small class="text-neutral-80">{{ __('text.discount') }}</small></th>
                                    </tr>
                                </thead>
                                <tbody id="datalistpopup">

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td id="details_total_price"></td>
                                        <td id="details_total_discount"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <small class="mb-0 text-neutral-80"><strong>{{ __('text.description') }}:</strong></small>
                            <p class="descriptioncontent">{{ __('text.description_here') }}</p>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <small class="mb-0 text-neutral-80"><em><strong>{{ __('text.created_at') }}:</strong> <span
                                        class="createdatecontent">22222</span></em></small>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
                <button class="btn btn-primary d-flex align-items-center" id="printButton"><span
                        class="material-symbols-rounded">print</span> {{ __('text.print') }}</button>
                <button type="button" class="btn btn-secondary d-flex align-items-center" data-bs-dismiss="modal">
                    <span class="material-symbols-rounded">
                        close
                    </span>{{ __('text.close') }}</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="detailModel" tabindex="-1" aria-labelledby="printModal" aria-hidden="true">
    <!-- Modal content -->
    <div class="modal-dialog modal-lg">
        <div class="modal-content ">
            <!-- Modal header -->
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ __('text.checkout_detail') }}</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="{{ __('text.close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    </div>
</div>
<iframe id="printIframe" style="display:none;"></iframe>
@push('scripts')
<script type="module">
var selected_orders = [];
$(function() {
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
        var totalPrice = 0;
        $.each(data, function(index, item) {
            var baseUnit = item.unitid == item.stockitem_unitid ? item.base_unit_name : item.converted_unit_name;
            var convertedUnit = item.unitid != item.stockitem_unitid ? item.base_unit_name : item.converted_unit_name;
            var realPrice = item.price + item.discount;
            totalPrice += realPrice * item.quantity;
            var row = $('<tr><td>' + item.name + '</td><td>' + item.quantity + "&nbsp;" + baseUnit + '</td><td>' + item.converted_quantity + "&nbsp;" + convertedUnit + '</td><td>' + realPrice + '{{ __("text.PLN") }}</td><td>' + item.discount + '{{ __("text.PLN") }}</td></tr>');
            // Add other cells as needed
            tbody.append(row);
        });
        $("#details_total_price").text(totalPrice + "{{ __('text.PLN') }}")
    }


    $('#detailModel').on('show.bs.modal', function(event) {

        // Get the group ID from the data attribute
        var button = $(event.relatedTarget);
        var DataId = button.data('btndetail');
        // Use an AJAX request to fetch the data for the given group
        console.log('/transaction/selldetail/' + DataId);
        $.ajax({
            url: '/warehouse/transaction/selldetails/' + DataId, // Replace with your actual route
            type: 'GET',
            dataType: 'json',
            data: {
                status: 2
            },
            success: function(data) {
                // Populate the form fields with the retrieved data

                $('.qrcodedetail').attr('src', data.dataURL);

                $('.referencecontent').html(data.data[0].reference);
                $('.datecontent').html(data.data[0].selldate);
                $('.suppliercontent').html(data.data[0].supplier);
                $('.companycontent').html(data.data[0].suppliercompany);
                $('.emailcontent').html(data.data[0].supplieremail);
                $('.descriptioncontent').html(data.data[0].description);
                $('.createdatecontent').html(data.created_at[0]);
                $('.warehousedetail').html(data.data.warehouse);
                $("#details_total_discount").text(data.sellOrder.discount + " {{__('text.PLN')}}");
                updateTable(data.data);

            },
            error: function() {
                // Handle errors if needed
            }
        });
    });

    // Triggered when the "Edit" button is clicked

    var checkoutlistTable = $('#data').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{!! route('transaction.getcheckout') !!}',
            data: function(d) {
                d.keyword = $('input[name=keyword]').val();
                d.startdate = $('input[name=startdate]').val();
                d.enddate = $('input[name=enddate]').val();
            }
        },
        dom: '<"d-flex align-items-md-center flex-column flex-md-row justify-content-md-between pb-3"Bf>rt<"pt-3 d-flex align-items-md-center flex-column flex-md-row justify-content-md-between"lp><"clear">',
        language: {
            url: langUrl // Polish language JSON file
        },
        bFilter: false,
        order: [[1, "desc"]],
        columns: [
            {
                data: 'id',
                name: 'id',
                orderable: true,
                searchable: false,
                visible: false
            },
            {
                data: null, // Use null for data as we are generating the checkbox manually
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return `<input data-reference="${row.reference}" data-hidden="${row.hidden}" type="checkbox" value="1" class="me-2 row-select">`;
                }
            },
            {
                data: 'show_reference',
                name: 'reference',
                render: function(data, type, row) {
                    if (row.hidden) {
                        return row.show_reference + '<span style="color: red">(hidden)</span>'
                    } else {
                        return row.show_reference;
                    }
                }
            },
            {
                data: 'supplier',
                name: 'supplier'
            },
            {
                data: 'selldate',
                name: 'selldate',
                orderable: true,
            },
            {
                data: 'pre_order', name: 'pre_order', render: function(data, type, row) {
                    return `<div class="form-check form-switch"><input class="form-check-input pre_order" type="checkbox" data-reference='${row.reference}' role="switch" ${row.pre_order ? 'checked' : 'disabled'} ></div>`;
                }
            },

            {
                data: 'withinvoice', name: 'withinvoice', render: function(data, type, row) {
                    return `<div class="form-check form-switch"><input class="form-check-input withinvoice" type="checkbox" data-reference='${row.reference}' role="switch" ${row.withinvoice ? 'checked disabled' : (row.confirmed ? '' : 'disabled')} ></div>`;
                }
            },
            {
                data: 'confirmed', name: 'confirmed', render: function(data, type, row) {
                    return `<div class="form-check form-switch"><input class="form-check-input confirmed" type="checkbox" data-reference='${row.reference}' role="switch" ${row.confirmed ? 'checked disabled' : ''}></div>`;
                }
            },
            {
                data: 'payment_type',
                name: 'payment_type',
                render: function(data, type, row) {
                    var text = "";
                    if (row.payment_type == 'bank_transfer') {
                        text = '{!!__("text.bank_transfer")!!}';
                    } else if (row.payment_type == 'cash') {
                        text = '{!!__("text.cash")!!}';
                    } else {
                        text = '{!!__("text.cash_on_delivery")!!}';
                    }
                    return text;
                }
            },
            {
                data: 'description',
                name: 'note'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            },
        ],
        buttons: [
            // {
            //     extend: 'csv',
            //     text: '<div class="d-flex align-items-center"><span class="material-symbols-rounded">text_snippet</span> CSV</div>',
            //     className: 'btn btn-sm btn-fill btn-info ',
            //     title: 'Checkout Data',
            //     exportOptions: {
            //         columns: [1, 2, 3, 4]
            //     }
            // },
            // {
            //     extend: 'pdf',
            //     text: '<div class="d-flex align-items-center"><span class="material-symbols-rounded">picture_as_pdf</span> PDF</div>',
            //     className: 'btn btn-sm btn-fill btn-info ',
            //     title: 'Checkout Data',
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

    $('#data').on('change', '.withinvoice', function() {
        var isChecked = $(this).is(':checked'); // Get whether the checkbox is checked
        console.log("Checkbox changed. Checked: " + isChecked);

        // You can access the row data using closest
        var rowData = $(this).data("reference");
        $.ajax({
            url: '{!!route('transaction.changesellstatus')!!}', // Replace with your actual route
            type: 'GET',
            dataType: 'json',
            data: {
                type: "withinvoice",
                value: isChecked,
                reference: rowData
            },
            success: function(data) {
                if (data.success) {
                    alert("Success");
                    window.location.reload();
                }
            }
        });
    });

    $('#data').on('change', '.pre_order', function() {
        var isChecked = $(this).is(':checked'); // Get whether the checkbox is checked
        console.log("Checkbox changed. Checked: " + isChecked);
        if (confirm("{!!__('text.sure_confirm')!!}")) {
            // You can access the row data using closest
            var rowData = $(this).data("reference");
            $.ajax({
                url: '{!!route('transaction.changesellstatus')!!}', // Replace with your actual route
                type: 'GET',
                dataType: 'json',
                data: {
                    type: "pre_order",
                    value: isChecked,
                    reference: rowData
                },
                success: function(data) {
                    if (data.success) {
                        alert("Success");
                        window.location.reload();
                    }
                }
            });
        } else {
            $(this).prop('checked', !isChecked);
        }
    });

    $('#data').on('change', '.confirmed', function() {
        var checkbox = $(this);  // Store the reference to the checkbox
        var isChecked = checkbox.is(':checked');
        if (confirm("{!!__('text.sure_confirm')!!}")) {

            // You can access the row data using closest
            var rowData = $(this).data("reference");
            $.ajax({
                url: '{!!route('transaction.changesellstatus')!!}', // Replace with your actual route
                type: 'GET',
                dataType: 'json',
                data: {
                    type: "confirmed",
                    value: isChecked,
                    reference: rowData
                },
                success: function(data) {
                    if (data.success) {
                        alert("Success");
                        window.location.reload();
                    }
                }
            });
        } else {
            checkbox.prop('checked', !isChecked);
        }
    });
    let selected_list = [];
    $('#data').on('change', '.row-select', function() {
        var rowId = $(this).data('reference');
        var is_hidden = $(this).data('hidden');
        if ($(this).is(':checked')) {
            console.log('Row selected:', rowId); // Handle selected row
            selected_orders.push(rowId)
            selected_list.push({id:rowId, is_hidden : is_hidden});
        } else {
            console.log('Row deselected:', rowId); // Handle deselected row
            selected_orders = selected_orders.filter(function(id) {
                return id !== rowId; // Keep all ids that are not equal to rowId
            });

            selected_list = selected_list.filter(function(item) {
                return item.id !== rowId; // Keep all ids that are not equal to rowId
            });
        }

        if(selected_list.filter((item)=>(item.is_hidden == 0)).length > 0 || selected_list.length == 0) {
            $('#btn_delete').addClass('d-none');
        } else if (selected_list.length > 0){
            $('#btn_delete').removeClass('d-none');
        }
    });

    //general search report
    $('#filter_checkoutlist').on('submit', function(e) {
        e.preventDefault();
        checkoutlistTable.draw();
    });

    $("#btn_reset").on('click', function() {
        $('input[name=keyword]').val('');
        $('input[name=startdate]').val('');
        $('input[name=enddate]').val('');

        checkoutlistTable.draw();
    });

    $('#startdate').on('input', function() {
        // Check if the start date field is not empty
        if ($(this).val().trim() !== '') {
            // Enable the end date field
            $('#enddate').prop('disabled', false);
        } else {
            // If start date is empty, disable the end date field
            $('#enddate').prop('disabled', true);
            $('#enddate').prop('required', true);
        }
    });


    //print button

    $("#printButton").on('click', function() {
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
        var downloadUrl = `{!! route('transaction.sellexport') !!}?keyword=` + encodeURIComponent(keyword) +
                        `&startdate=` + encodeURIComponent(startdate) +
                        `&enddate=` + encodeURIComponent(enddate);

        // Trigger a file download by changing the window location
        window.location.href = downloadUrl;
    })

    $("#btn_print").on('click', function() {
        console.log(selected_orders)
        if (selected_orders.length == 0) {
            return alert('{!!__('text.no_selected')!!}')
        }
        // window.print();
        // window.location.href = '{!!route("transaction.printSellOrders")!!}';
        $.ajax({
            url: '{!! route("transaction.printSellOrders") !!}',
            type: 'GET',
            data: {
                selected_orders
            },
            success: function(response) {
                var iframe = document.getElementById('printIframe');
                var doc = iframe.contentWindow.document || iframe.contentDocument;

                // Write the response HTML into the iframe's document
                doc.open();
                doc.write(response);

                var link = document.createElement("link");
                        link.rel = "stylesheet";
                        link.href = "https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css";
                        doc.head.appendChild(link); // Append stylesheet to iframe's head

                link.onload = function() {
                    doc.close();  // Close document to apply the styles
                    iframe.contentWindow.focus(); // Focus on the iframe
                    iframe.contentWindow.print(); // Trigger the print dialog
                };
                link.onerror = function() {
                    console.log("Failed to load print stylesheet.");
                };

                // Wait for the content to load and then print
                // iframe.contentWindow.focus(); // Focus on the iframe
                // iframe.contentWindow.print(); // Trigger the print dialog
            },
            error: function(xhr) {
                alert("Failed to retrieve the print data.");
            }
        });
    })

    $("#btn_delete").on('click', function() {
        // console.log(selected_orders)
        if (selected_orders.length == 0) {
            return alert('{!!__('text.no_selected')!!}')
        }
        // window.print();
        // window.location.href = '{!!route("transaction.printSellOrders")!!}';
        $.ajax({
            url: '{!! route("transaction.deletehiddenOrders") !!}',
            type: 'GET',
            // dataType: 'json',
            data: {
                selected_orders
            },
            success: function(response) {
                if(response.success){
                    checkoutlistTable.draw();
                    selected_orders = [];
                    $('#btn_delete').addClass('d-none');
                }else{
                    return alert(response.message)
                }

            },
            error: function(xhr) {
                alert("Failed to retrieve the data.");
            }
        });
    })

});

</script>
@endpush
@endsection
