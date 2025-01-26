@extends('layouts.app')
@section('title', __('text.reports'))

@section('content')
<div class="body-inner">

    <div class="">
        <h2 class="mb-5">{{__('text.stock_reports')}}</h2>
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
                                {{__('text.total_items_year')}}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="bg-neutral-20 p-3 rounded">
                            <p class="mb-0"><strong class="item fs-4">{{$totalallitem['Month']}}</strong></p>
                            <p class="fs-7 text-neutral-80 mb-0">
                                {{__('text.total_items_month')}}
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
                                <option value="">{{__('text.select')}}...</option>
                                @foreach($totalcategories as $category)
                                <option value="{{ $category->code }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <label for="category" class="error"></label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="">
                            <label for="warehouse" class="form-label">{{__('text.warehouse')}}</label>
                            <select name="warehouse" id="warehouse" class="form-control warehousedata">
                                <option value="">{{__('text.select')}}...</option>
                                @foreach($totalwarehouses as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                            <label for="warehouse" class="error"></label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="">
                            <label for="shelf" class="form-label">{{__('text.shelf')}}</label>
                            <select name="shelf" id="shelf" class="form-control shelfdata">
                                
                            </select>
                            <label for="shelf" class="error"></label>
                        </div>
                    </div>

                    <div class="col-md-4 ">
                        <div class="">
                            <label for="unit" class="form-label">{{__('text.unit')}}</label>
                            <select name="unit" id="unit" class="form-control">
                                <option value="">{{__('text.select')}}...</option>
                                @foreach($totalunits as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                @endforeach
                            </select>
                            <label for="unit" class="error"></label>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <button type="input" class="btn btn-primary d-flex align-items-center" id="button"><span
                                class="material-symbols-rounded">check</span> {{__('text.apply_filters')}}</button>
                    </div>

                </div>
            </form>

            <div class="table-responsive-sm">
                <table class="table " id="data">
                    <thead>
                        <tr>
                            <th>{{__('text.id')}}</th>
                            <th>{{__('text.code')}}</th>
                            <th>{{__('text.name')}}</th>
                            <th>{{__('text.category')}}</th>
                            <th>{{__('text.unit')}}</th>
                            <th>{{__('text.warehouse')}}</th>
                            <th>{{__('text.shelf')}}</th>
                            <th>{{__('text.quantity')}}</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Define the modal content and title -->







@push('scripts')

<script type="module">
$(function() {


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
                label: 'Total Stocks (this year)',
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
                        stepSize: 2

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
            url: '{!! route('reports.getstockreport') !!}',
            data: function(d) {
                d.category = $('select[name=category]').val();
                d.warehouse = $('select[name=warehouse]').val();
                d.shelf = $('select[name=shelf]').val();
                d.unit = $('select[name=unit]').val();
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
                data: 'code',
                name: 'code'
            },
            {
                data: 'name',
                name: 'name'
            },
            {
                data: 'category',
                name: 'category'
            },
            {
                data: 'unit',
                name: 'unit'
            },
            {
                data: 'warehouse',
                name: 'warehouse'
            },
            {
                data: 'shelf',
                name: 'shelf'
            },
            {
                data: 'quantity',
                name: 'quantity'
            },
        ],
        buttons: [{
                extend: 'csv',
                text: '<div class="d-flex align-items-center"><span class="material-symbols-rounded">text_snippet</span> CSV</div>',
                className: 'btn btn-sm btn-fill btn-info ',
                title: 'Stock Report',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5, 6, 7]
                }
            },
            {
                extend: 'pdf',
                text: '<div class="d-flex align-items-center"><span class="material-symbols-rounded">picture_as_pdf</span> PDF</div>',
                className: 'btn btn-sm btn-fill btn-info ',
                title: 'Stock Report',
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
        ]

    });

});
</script>
@endpush
@endsection