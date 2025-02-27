@extends('layouts.app')
@section('title', __('text.home'))

@section('content')
<div class="body-inner">
    
    <div class="">
        <h2 class="mb-5">{{__('text.dashboard_overview')}}</h2>
        <div class="row mb-4">
            {{-- <div class="col-md-6 mb-4">
                <label for="daterange" class="form-label">{{__('text.date_range')}}</label>
                <input type="date" class="form-control" id="daterange" name="daterange" lang="pl">
            </div> --}}
            <div class="col-md-4 ">
                <label for="startdate" class="form-label">{{__('text.start_date')}}</label>
                <input type="date" class="form-control" id="startdate" name="startdate" lang="pl">
            </div>
            <div class="col-md-4 ">
                <label for="enddate" class="form-label">{{__('text.end_date')}}</label>
                <input type="date" disabled class="form-control" id="enddate" name="enddate" required>
            </div>
            <div class="col-md-4" style="margin-top: 35px">
                <button type="button" class="btn btn-primary d-flex align-items-center" id="apply_btn">
                    <span class="material-symbols-rounded">check</span> {{__('text.apply_filters')}}
                </button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="bg-primary-pressed  p-3 rounded">
                    <p class="mb-0 text-white">
                        <strong class="item fs-4" id="sum_price">
                            0
                        </strong> PLN</p>
                    <p class="fs-7 text-neutral-50 mb-0">
                        {{__('text.total_price')}}
                    </p>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="bg-primary-pressed p-3 rounded">
                    <p class="mb-0 text-white"><strong class="item fs-4" id="sum_carton_qty">0
                    </strong> {{__('text.carton')}}</p>
                    <p class="fs-7 text-neutral-50 mb-0">
                    {{__('text.total_items_overall')}}
                    </p>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="bg-primary-pressed p-3 rounded">
                    <p class="mb-0 text-white"><strong class="item fs-4" id="sum_pair_qty">0
                    </strong> {{__('text.pair')}}</p>
                    <p class="fs-7 text-neutral-50 mb-0">
                    {{__('text.total_items_overall')}}
                    </p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="bg-green  p-3 rounded">
                    <p class="mb-0 text-white">
                        <p class=" text-white">
                            <strong class="item fs-4" id="sum_price">{{$current_date_sum['price']}}</strong> PLN
                        </p>
                        <p class=" text-white">
                            <strong class="item fs-4" id="sum_price">{{$current_date_sum['carton_qty']}}</strong> {{__('text.carton')}}
                        </p>
                        <p class=" text-white">
                            <strong class="item fs-4" id="sum_price">{{$current_date_sum['pair_qty']}}</strong> {{__('text.pair')}}
                        </p>
                    </p>
                    <p class="fs-7 text-neutral-50 mb-0">
                        {{__('text.total_items_date')}}
                    </p>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="bg-red p-3 rounded">
                    <p class="mb-0 text-white">
                        <p class=" text-white">
                            <strong class="item fs-4" id="sum_price">{{$current_month_sum['price']}}</strong> PLN
                        </p>
                        <p class=" text-white">
                            <strong class="item fs-4" id="sum_price">{{$current_month_sum['carton_qty']}}</strong> {{__('text.carton')}}
                        </p>
                        <p class=" text-white">
                            <strong class="item fs-4" id="sum_price">{{$current_month_sum['pair_qty']}}</strong> {{__('text.pair')}}
                        </p>
                    </p>
                    <p class="fs-7 text-neutral-50 mb-0">
                    {{__('text.total_items_month')}}
                    </p>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="bg-warning-pressed p-3 rounded">
                    <p class="mb-0 text-white">
                        <p class=" text-white">
                            <strong class="item fs-4" id="sum_price">{{$current_year_sum['price']}}</strong> PLN
                        </p>
                        <p class=" text-white">
                            <strong class="item fs-4" id="sum_price">{{$current_year_sum['carton_qty']}}</strong> {{__('text.carton')}}
                        </p>
                        <p class=" text-white">
                            <strong class="item fs-4" id="sum_price">{{$current_year_sum['pair_qty']}}</strong> {{__('text.pair')}}
                        </p>
                    </p>
                    <p class="fs-7 text-neutral-50 mb-0">
                    {{__('text.total_items_year')}}
                    </p>
                </div>
            </div>
        </div>

        <!-- <div class="border-top pt-5">
            <div class="row">
                <div class="col-md-4">
                    <p class="text-center"><strong>{{__('text.top_10_items_by_quantity')}}</strong></p>
                    <canvas id="productgraphByQty" width="300" height="300"></canvas>
                </div>
                <div class="col-md-4">
                    <p class="text-center"><strong>{{__('text.top_10_items_by_sale')}}</strong></p>
                    <canvas id="productgraph" width="300" height="300"></canvas>
                </div>
                <div class="col-md-4">
                    <p class="text-center"><strong>{{__('text.total_items_in_warehouse')}}</strong></p>
                    <canvas id="warehousegraph" width="300" height="300"></canvas>

                </div>
            </div>
        </div> -->
    </div>

</div>


@push('scripts')

<script type="module">
    $(function() {
            //Graph warehouse
        // Parse the PHP data to JavaScript
        var warehousedata = @json($totalstockwarehouse);

        // Create an array for labels and dataset values
        var warehousename = warehousedata.map(item => item.warehousename);
        var warehousevalue = warehousedata.map(item => item.total);

        // Generate random colors for each dataset
        var colors = warehousename.map(() => '#' + Math.floor(Math.random()*16777215).toString(16));
        var warehousegraph = document.getElementById("warehousegraph");
        // var bystatus = new Chart(warehousegraph, {
        //     type: 'pie',
            
        //     data: {
        //         labels: warehousename,
        //         datasets: [
        //         {
                    
        //             data: warehousevalue,
        //             backgroundColor: colors,
        //             borderWidth: 1
        //         }
        //         ]
        //     },
        //     options: {
                
        //             plugins: {
        //             legend: {
        //                 position: 'bottom',
        //             }
        //     }   }
        // });


        //Graph top product
        // Parse the PHP data to JavaScript
        var top10productquantityData = @json($top10productquantity);

        // Create an array for labels and dataset values
        var productname = top10productquantityData.map(item => item.name);
        var productvalue = top10productquantityData.map(item => item.quantity);

        // Generate random colors for each dataset
        var colorsproduct = productname.map(() => '#' + Math.floor(Math.random()*16777215).toString(16));
        var productgraph = document.getElementById("productgraphByQty");
        // var bystatus = new Chart(productgraph, {
        //     type: 'pie',
            
        //     data: {
        //         labels: productname,
        //         datasets: [
        //         {
                    
        //             data: productvalue,
        //             backgroundColor: colorsproduct,
        //             borderWidth: 1
        //         }
        //         ]
        //     },
        //     options: {
                
        //             plugins: {
        //             legend: {
        //                 position: 'bottom',
        //             }
        //     }   }
        // });

        //overview graph
        var dataTotalcheckin = @json($dataTotalCheckin);
        var dataTotalcheckout = @json($dataTotalCheckout);
        var overviewgraph = document.getElementById("overviewgraph");

        var overviewgraphs = new Chart(overviewgraph, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov',
                    'Dec'
                ],
                datasets: [
                {
                    label: '{{__('text.checkin')}}',
                    data: dataTotalcheckin,
                    backgroundColor: '#20c997',
                    borderColor: '#20c997',
                    borderWidth: 1
                },

                {
                    label: '{{__('text.checkout')}}',
                    data: dataTotalcheckout,
                    backgroundColor: '#c92085',
                    borderColor: '#c92085',
                    borderWidth: 1
                },
                
                ]
            },
            options: {
                maintainAspectRatio: false,
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
                            stepSize:2,
                            

                        }
                    },
                }
                // Add your Chart.js options here
            }
        });


        var top10productbysaleData = @json($top10productbysale);

        // Create an array for labels and dataset values
        var productnameBySale = top10productbysaleData.map(item => item.name);
        var productvalueBySale = top10productbysaleData.map(item => item.quantity);

        // Generate random colors for each dataset
        var colorsproductBySale = productnameBySale.map(() => '#' + Math.floor(Math.random()*16777215).toString(16));
        var productgraph = document.getElementById("productgraph");
        // var bySale = new Chart(productgraph, {
        //     type: 'pie',
            
        //     data: {
        //         labels: productnameBySale,
        //         datasets: [
        //         {
                    
        //             data: productvalueBySale,
        //             backgroundColor: colorsproductBySale,
        //             borderWidth: 1
        //         }
        //         ]
        //     },
        //     options: {
                
        //             plugins: {
        //             legend: {
        //                 position: 'bottom',
        //             }
        //     }   }
        // });

        flatpickr("#startdate", {
            locale: currentLang, // Set the locale to Polish
            dateFormat: "d/m/Y",
            defaultDate: new Date(),
        });

        flatpickr("#enddate", {
            locale: currentLang, // Set the locale to Polish
            dateFormat: "d/m/Y",
            defaultDate: new Date(),
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

        const today = new Date();

        // Format the date as "d/m/Y"
        const formattedDate = 
            ('0' + today.getDate()).slice(-2) + '/' + 
            ('0' + (today.getMonth() + 1)).slice(-2) + '/' + 
            today.getFullYear();
        $.ajax({
            url: '{!! route("reports.getSumData") !!}', // Replace with your actual route
            type: 'GET',
            dataType: 'json',
            data: {
                date_range: [formattedDate, formattedDate]
            },
            success: function(data) {
                $("#sum_price").text(data.sum_price)
                $("#sum_carton_qty").text(data.sum_carton_qty)
                $("#sum_pair_qty").text(data.sum_pair_qty)
            }
        });

        $("#apply_btn").on('click', function() {
            var selectedDates = [$("#startdate").val(), $("#enddate").val()];
            $.ajax({
                url: '{!! route("reports.getSumData") !!}', // Replace with your actual route
                type: 'GET',
                dataType: 'json',
                data: {
                    date_range: selectedDates
                },
                success: function(data) {
                    $("#sum_price").text(data.sum_price)
                    $("#sum_carton_qty").text(data.sum_carton_qty)
                    $("#sum_pair_qty").text(data.sum_pair_qty)
                }
            });
        })
    })
</script>
@endpush
@endsection