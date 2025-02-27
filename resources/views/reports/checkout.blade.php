@extends('layouts.app')
@section('title', __('text.reports'))

@section('content')
<div class="body-inner">
    <div class="">
        <h2>{{ __('text.checkout_reports') }}</h2>
    </div>
    <!-- filter -->
    <div class="border-top pt-2">
        <form id="filter" method="POST" class="mb-4">
            @csrf
            <div class="row">
                <div class="col-md-2">
                    <div class="">
                        <label for="keyword" class="form-label">{{__('text.search')}}</label>
                        <input type="text" id="keyword" name="keyword" class="form-control"
                            placeholder="{{__('text.search_transaction')}}" />
                        <label for="keyword" class="error"></label>
                    </div>
                </div>
                <div class="col-md-2 ">
                    <label for="startdate" class="form-label">{{__('text.start_date')}}</label>
                    <input type="date" class="form-control" id="startdate" name="startdate">
                </div>
                <div class="col-md-2 ">
                    <label for="enddate" class="form-label">{{__('text.end_date')}}</label>
                    <input type="date" disabled class="form-control" id="enddate" name="enddate">
                </div>

                <div class="col-md-2">
                    <label for="customer" class="form-label">{{__('text.customer')}}</label>
                    <select name="customer" id="customer" class="form-control">
                        <option value="">{{__('text.select')}}</option>
                        @foreach($customers as $item)
                        <option value='{{$item->id}}'>{{$item->name}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="customer" class="form-label">{{__('text.creator')}}</label>
                    <select name="creator" id="creator" class="form-control">
                        <option value="">{{__('text.select')}}</option>
                        @foreach($creators as $item)
                        <option value='{{$item->id}}'>{{$item->name}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="warehouse" class="form-label">{{__('text.warehouse')}}</label>
                    <select name="warehouse" id="warehouse" class="form-control">
                        <option value="">{{__('text.select')}}</option>
                        @foreach($warehouses as $item)
                        <option value='{{$item->id}}'>{{$item->name}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="category" class="form-label">{{__('text.category')}}</label>
                    <select name="category" id="category" class="form-control">
                        <option value="">{{__('text.select')}}</option>
                        @foreach($categories as $item)
                        <option value='{{$item->id}}'>{{$item->title}}</option>
                        @endforeach
                    </select>
                </div>

            </div>
            <div class="row mt-2">
                <div class="col-md-12">
                    <div class="mb-3 d-flex">
                        <button type="input" class="btn btn-primary d-flex align-items-center" id="button"><span class="material-symbols-rounded">check</span> {{__('text.apply_filters')}}</button>
                        <button type="input" class="btn btn-yellow d-flex align-items-center" style="margin-left: 5px" id="btn_reset"><span class="material-symbols-rounded">check</span> {{__('text.reset')}}</button>
                        <a href="#" class="btn btn-green d-flex align-items-center" style="margin-left: 5px" id="btn_download" style="margin-right: 10px">
                            <span class="material-symbols-rounded">download</span>{{ __('text.download') }}
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <!-- table -->
     <div class="row table-responsive">
         <table class="table" id="data">
            <thead>
                <tr>
                    <th>{{ __('text.id') }}</th>
                    <th>No</th>
                    <th>{{ __('text.creator') }}</th>
                    <th>{{ __('text.customer') }}</th>
                    <th>{{ __('text.category') }}</th>
                    <th>{{ __('text.name') }}</th>
                    <th>{{ __('text.size') }}</th>
                    <th>{{ __('excel.carton') }}</th>
                    <th>{{ __('excel.packing') }}</th>
                    <th>{{ __('text.pair') }}</th>
                    <th>{{ __('text.unit') }}</th>
                    <th>{{ __('text.price') }}</th>
                    <th>{{ __('text.total_price') }}</th>
                    <th width="100px">{{ __('text.date') }}</th>
                </tr>
                <tfoot>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>{{__('text.total_cart')}} : </th>
                        <th id="total_cart_quantity"></th>
                        <th>{{__('text.total_Pair')}} : </th>
                        <th id="total_pair_quantity"></th>
                        <th>{{__('text.sub_total')}} :</th>
                        <th><span id="total_sub_price"></span> {{ __('text.PLN') }}</th>
                        <th></th>
                        <th></th>

                    </tr>
                </tfoot>
            </thead>
        </table>
     </div>
</div>
@push('scripts')
<script type="module">

    $(function() {
        $("#customer").select2();
        $("#warehouse").select2();
        $("#category").select2();
        var tabledatareport = $('#data').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{!! route('reports.getcheckoutreport') !!}',
                data: function(d) {
                    d.customer = $('#customer').val();
                    d.warehouse = $('#warehouse').val();
                    d.creator = $("#creator").val();
                    d.category = $('#category').val();
                    d.keyword = $('input[name=keyword]').val();
                    d.startdate = $('input[name=startdate]').val();
                    d.enddate = $('input[name=enddate]').val();
                },
            },
            drawCallback: function() {
            let totalQuantity = 0;
            let totalPrice = 0;
            let totalCartonQuantity =0;
            let totalPairQuantity=0;
            $('#data tbody tr').each(function() {
                let carton_quantity = parseFloat($(this).find('td').eq(6).text()) || 0; // Assuming the total quantity is in the 3rd column
                let pair_quantity = parseFloat($(this).find('td').eq(8).text()) || 0; // Assuming the price is in the 4th column
                let total_price = parseFloat($(this).find('td').eq(11).text()) || 0; // Assuming the price is in the 4th column

                totalCartonQuantity += carton_quantity;
                totalPairQuantity += pair_quantity;
                totalPrice += total_price;
            });
            $("#total_cart_quantity").text(totalCartonQuantity);
            $("#total_pair_quantity").text(totalPairQuantity);
            $("#total_sub_price").text(totalPrice);
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
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'creator',
                    name: 'creator'
                },
                {
                    data: 'customer',
                    name: 'customer'
                },
                {
                    data: 'category',
                    name: 'category'
                },
                // {
                //     data: 'code',
                //     name: 'code'
                // },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'size',
                    name: 'size'
                },
                {
                    data: 'carton_quantity',
                    name: 'carton_quantity'
                },
                {
                    data: 'unitconverter',
                    name: 'unitconverter'
                },
                {
                    data: 'pair_quantity',
                    name: 'pair_quantity'
                },
                {
                    data: 'unit_name',
                    name: 'unit_name'
                },
                {
                    data: 'price',
                    name: 'price'
                },
                {
                    data: 'total_price',
                    name: 'total_price'
                },
                {
                    data: 'selldate',
                    name: 'selldate'
                },
                // {
                //     data: 'detail',
                //     name: 'detail',
                //     orderable: false,
                //     searchable: false
                // }
            ],
            buttons: [
            ]
        });
        //general search report
        $('#filter').on('submit', function(e) {
            e.preventDefault();
            tabledatareport.draw();
        });

        $("#btn_reset").on('click', function() {
            $('input[name=keyword]').val('');
            $('input[name=startdate]').val('');
            $('input[name=enddate]').val('');
            $('#customer').val('').trigger('change');
            $('#warehouse').val('').trigger('change');
            // $('#category').val('').trigger('change');
            tabledatareport.draw();
        });
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


    $("#btn_download").on('click', function() {
        var keyword = $('input[name=keyword]').val();
        var startdate = $('input[name=startdate]').val();
        var enddate = $('input[name=enddate]').val();
        var downloadUrl = `{!! route('transaction.checkinexport') !!}?keyword=` + encodeURIComponent(keyword) +
                        `&startdate=` + encodeURIComponent(startdate) +
                        `&enddate=` + encodeURIComponent(enddate);

        // Trigger a file download by changing the window location
        window.location.href = downloadUrl;
    })
</script>
@endpush
@endsection
