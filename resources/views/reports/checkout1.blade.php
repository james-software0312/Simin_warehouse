@extends('layouts.app')
@section('title', __('text.reports'))

@section('content')
<div class="body-inner">

    <div class="">
        <h2 class="mb-5">{{__('text.checkout_reports')}}</h2>
        <div class="row">
            <div class="col-md-8 mb-4">
                <canvas id="graph"></canvas>
            </div>
            <div class="col-md-4 mb-4">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <div class="bg-neutral-20 p-3 rounded">
                            <p class="mb-0"><strong class="item fs-4">
                                    {{$totalallitem['Overall']}}
                                </strong></p>
                            <p class="fs-7 text-neutral-80 mb-0">
                                {{__('text.total_items_overall')}}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <div class="bg-neutral-20 p-3 rounded">
                            <p class="mb-0"><strong class="item fs-4">{{$totalallitem['Year']}}</strong></p>
                            <p class="fs-7 text-neutral-80 mb-0">
                                {{__('text.total_items_this_year')}}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="bg-neutral-20 p-3 rounded">
                            <p class="mb-0"><strong class="item fs-4">{{$totalallitem['Month']}}</strong></p>
                            <p class="fs-7 text-neutral-80 mb-0">
                                {{__('text.total_items_this_month')}}
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="border-top pt-5">
            <h4 class="mb-3">{{__('text.filter_data')}}</h4>
            <form id="formreport" method="POST" class="mb-4">
                @csrf
                <div class="row">
                    <div class="col-md-4 ">
                        <label for="startdate" class="form-label">{{__('text.start_date')}}</label>
                        <input type="date" class="form-control" id="startdate" name="startdate">
                    </div>
                    <div class="col-md-4 ">
                        <label for="enddate" class="form-label">{{__('text.end_date')}}</label>
                        <input type="date" disabled class="form-control" id="enddate" name="enddate" required>
                    </div>

                    <div class="col-md-4 ">
                        <div class="">
                            <label for="category" class="form-label">{{__('text.category')}}</label>
                            <select name="category" id="category" class="form-control">
                                <option value="">{{__('text.select')}}</option>
                                @foreach($totalcategories as $category)
                                <option value="{{ $category->code }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <label for="category" class="error"></label>
                        </div>
                    </div>

                    <div class="col-md-4 ">
                        <div class="">
                            <label for="supplier" class="form-label">{{__('text.customer')}}</label>
                            <select name="supplier" id="supplier" class="form-control">
                                <option value="">{{__('text.select')}}</option>
                                @foreach($totalcustomer as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                            <label for="supplier" class="error"></label>
                        </div>
                    </div>

                    <div class="col-md-4 ">
                        <div class="">
                            <label for="reference" class="form-label">{{__('text.reference')}}</label>
                            <input type="text" id="reference" name="reference" class="form-control"
                                placeholder="{{__('text.reference')}}" />
                            <label for="reference" class="error"></label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <button type="input" class="btn btn-primary d-flex align-items-center" id="button"><span
                                    class="material-symbols-rounded">check</span> {{__('text.submit')}}</button>
                        </div>
                    </div>
                </div>
            </form>

            <div class="table-responsive-sm">
                <table class="table " id="data">
                    <thead>
                        <tr>
                            <th>{{__('text.id')}}</th>
                            <th>{{__('text.reference')}}</th>
                            <th>{{__('text.customer')}}</th>
                            <th>{{__('text.category')}}</th>
                            <th>{{__('text.quantity')}}</th>
                            <th>{{__('text.date')}}</th>
                            <th>{{__('text.detail')}}</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

</div>
<!-- Define the modal content and title -->

<!-- Modal -->
<div class="modal fade" id="detailModel" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <!-- Modal content -->
    <div class="modal-dialog modal-lg">
        <div class="modal-content ">
            <!-- Modal header -->
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{__('text.checkout_detail')}}</h5>
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
                            <p class="mb-0 text-neutral-80"><strong>{{__('text.reference')}}:</strong></p>
                            <p class="mb-0 referencecontent">reference</p>
                        </div>
                        <div class="col-md-3 mb-3">
                            <p class="mb-0 text-neutral-80"><strong>{{__('text.date')}}:</strong></p>
                            <p class="mb-0 datecontent">date</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <p class="mb-0 text-neutral-80"><strong>{{__('text.customer')}}:</strong></p>
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
                                        <th width="80%"><small class="text-neutral-80">{{__('text.item_name')}}</small></th>
                                        <th width="6%"><small class="text-neutral-80">{{__('text.quantity')}}</small></th>
                                    </tr>
                                </thead>
                                <tbody id="datalistpopup">

                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <p class="descriptioncontent">{{__('text.description_here')}}</p>
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

        $("#printButton").on('click', function() {
            // Print the modal content
            window.print();
        });

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
                url: '/warehouse/transaction/detail/' + DataId, // Replace with your actual route
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


        //Chart monthly
        var graph = document.getElementById('graph').getContext('2d');

        var dataTotal = @json($dataTotal);

        var graph = new Chart(graph, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov',
                    'Dec'
                ],
                datasets: [{
                    label: 'Total Checkout (this year)',
                    data: dataTotal,
                    backgroundColor: '#6E3FF3',
                    borderColor: '#6E3FF3',
                    borderWidth: 1
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            //color: '#000000'
                        }
                    }
                },
                scales: {
                    y: {
                        ticks: {
                            beginAtZero: true,
                            stepSize: 2,


                        }
                    },
                }
                // Add your Chart.js options here
            }
        });

        //general search report
        $('#formreport').on('submit', function(e) {
            e.preventDefault();
            tabledatareport.draw();



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


        var tabledatareport = $('#data').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{!! route('reports.getcheckinreport') !!}',
                data: function(d) {
                    d.category = $('select[name=category]').val();
                    d.supplier = $('select[name=supplier]').val();
                    d.reference = $('input[name=reference]').val();
                    d.startdate = $('input[name=startdate]').val();
                    d.enddate = $('input[name=enddate]').val();
                },
            },

            dom: '<"d-flex align-items-md-center flex-column flex-md-row justify-content-md-between pb-3"Bf>rt<"pt-3 d-flex align-items-md-center flex-column flex-md-row justify-content-md-between"p><"clear">',
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
                    data: 'supplier',
                    name: 'supplier'
                },
                {
                    data: 'category',
                    name: 'category'
                },
                {
                    data: 'totalquantity',
                    name: 'totalquantity'
                },
                {
                    data: 'transactiondate',
                    name: 'transactiondate'
                },
                {
                    data: 'detail',
                    name: 'detail',
                    orderable: false,
                    searchable: false
                }
            ],
            buttons: [{
                    extend: 'csv',
                    text: '<div class="d-flex align-items-center"><span class="material-symbols-rounded">text_snippet</span> CSV</div>',
                    className: 'btn btn-sm btn-fill btn-info ',
                    title: 'Checkout Report',
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5]
                    }
                },
                {
                    extend: 'pdf',
                    text: '<div class="d-flex align-items-center"><span class="material-symbols-rounded">picture_as_pdf</span> PDF</div>',
                    className: 'btn btn-sm btn-fill btn-info ',
                    title: 'Checkout Report',
                    orientation: 'landscape',
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5]
                    },
                    customize: function(doc) {
                        doc.styles.tableHeader.alignment = 'left';
                        doc.content[1].table.widths = Array(doc.content[1].table.body[0]
                                .length + 1)
                            .join('*').split('');
                    }
                }
            ]

        });

    });
    </script>
    @endpush
    @endsection
