@extends('layouts.app')
@section('title', __('text.home'))

@section('content')
<div class="body-inner">
    
    <div class="">
        <h2 class="mb-5">{{__('text.dashboard_overview')}}</h2>
        <div class="row">
            <div class="col-md-6 mb-4">
                <label for="daterange" class="form-label">{{__('text.date_range')}}</label>
                <input type="date" class="form-control" id="daterange" name="daterange" lang="pl">
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
                    <p class="mb-0 text-white"><strong class="item fs-4" id="sum_qty">0
                    </strong></p>
                    <p class="fs-7 text-neutral-50 mb-0">
                    {{__('text.total_items_overall')}}
                    </p>
                </div>
            </div>

        </div>
        {{-- <div class="row">
            <div class="col-md-12 mb-4 mt-5">
            <p class="text-center"><strong>{{__('text.year_overview')}}</strong></p>
                <div class="" style="height:300px">
                    <canvas id="overviewgraph" ></canvas>
                </div>
            </div>
            
        </div>
        <div class="border-top pt-5">
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
        </div> --}}
    </div>

</div>


@push('scripts')

<script type="module">
// $(function() {
    



    $(function() {
        flatpickr("#daterange", {
            locale: currentLang, 
            dateFormat: "d/m/Y", 
            mode: "range",
            defaultDate: [new Date(), new Date()],
            onChange: function(selectedDates, dateStr, instance) {
                console.log(selectedDates)
                $.ajax({
                    url: '{!! route("reports.getSumData") !!}', // Replace with your actual route
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        date_range: selectedDates
                    },
                    success: function(data) {
                        $("#sum_price").text(data.sum_price)
                        $("#sum_qty").text(data.sum_qty)
                    }
                });
            },
        });
    })
</script>
@endpush
@endsection