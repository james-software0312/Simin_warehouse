@extends('layouts.app')
@section('title', __('text.reports'))

@section('content')
<div class="body-inner">

    <div class="">
        <h2 class="mb-5">{{ __('text.category_reports') }}</h2>
        <div class="row">
            <div class="col-md-8 mb-4">
                <canvas id="graph"></canvas>
            </div>
            <div class="col-md-4 mb-4">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <div class="bg-neutral-20 p-3 rounded">
                            <p class="mb-0"><strong class="item fs-4">
                                    {{$totalcategories}}
                                </strong></p>
                            <p class="fs-7 text-neutral-80 mb-0">
                                {{ __('text.total_categories') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="border-top pt-5">
            <div class="table-responsive-sm">
                <table class="table" id="data">
                    <thead>
                        <tr>
                            <th>{{ __('text.id') }}</th>
                            <th>{{ __('text.name') }}</th>
                            <th>{{ __('text.total_item') }}</th>
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

    var categoryData = @json($categorychart);
    var categoryLabels = categoryData.map(item => item.categoryname);
    var categoryTotals = categoryData.map(item => item.total);


    var graph = new Chart(graph, {
        type: 'bar',
        data: {
            labels: categoryLabels,
            datasets: [{
                label: 'Categories',
                data: categoryTotals,
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


    var tabledatareport = $('#data').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{!! route('reports.getcategoryreport') !!}',

        },

        dom: '<"d-flex align-items-md-center flex-column flex-md-row justify-content-md-between pb-3"Bf>rt<"pt-3 d-flex align-items-md-center flex-column flex-md-row justify-content-md-between"p><"clear">',
        language: {
            url: langUrl // Polish language JSON file
        },
        bFilter: false,
        columns: [{
                data: 'categoryid',
                name: 'categoryid',
                orderable: false,
                searchable: false,
                visible: false
            },
            {
                data: 'categoryname',
                name: 'categoryname'
            },
            {
                data: 'total',
                name: 'total'
            }
        ],
        buttons: [{
                extend: 'csv',
                text: '<div class="d-flex align-items-center"><span class="material-symbols-rounded">text_snippet</span> CSV</div>',
                className: 'btn btn-sm btn-fill btn-info ',
                title: 'Warehouse Report',
                exportOptions: {
                    columns: [1, 2]
                }
            },
            {
                extend: 'pdf',
                text: '<div class="d-flex align-items-center"><span class="material-symbols-rounded">picture_as_pdf</span> PDF</div>',
                className: 'btn btn-sm btn-fill btn-info ',
                title: 'Warehouse Report',
                orientation: 'landscape',
                exportOptions: {
                    columns: [1, 2]
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