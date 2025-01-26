@extends('layouts.app')
@section('title', __('text.purchase_price_history'))

@section('content')

<div class="body-inner">
    <div class="alert alert-success alert-dismissible fade show  align-items-center row" role="alert">
        <div class="col-md-4 d-flex     align-items-center">                
            <img src="{{$photo}}" class="photodetail img-fluid" width="40" style="height: 40px;" />  &nbsp;&nbsp;&nbsp;
            <span class="me-4" style="font-size: large;"><strong>{{$data->name}}</strong></span> 
        </div>
        <div class="col-md-4">
            <span class="me-4"><strong>{{__('text.category')}}:</strong></span> 
            <span class="me-4"> {{ $data->category }} </span>
        </div>
        <div class="col-md-4">
            <span class="me-4"><strong>{{__('text.sub_type')}}:</strong></span> 
            <span class="me-4"> {{ $data->itemsubtype }} </span>
        </div>
    </div>
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
        <span class="material-symbols-rounded">task_alt</span> {{ session('success') }}
    </div>
    @endif
    
    <?php
        $isCheckIn = $modules->contains(function($module) {
            return ($module->module == 'purchase') && ($module->hasViewPermission || $module->hasEditPermission);
        });

        $isCheckOut = $modules->contains(function($module) {
            return ($module->module == 'transaction') && ($module->hasViewPermission || $module->hasEditPermission);
        });
    ?>
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a href="{{route('stock.edit', ['id' => $stockitemid])}}" class="nav-link" aria-current="page" href="#">{{__('text.edit_stock_item')}}</a>
        </li>
        <li class="nav-item">
            <a href="{{route('stock.history', ['id' => $stockitemid])}}" class="nav-link" href="#" tabindex="-1" aria-disabled="true">{{__('text.history')}}</a>
        </li>
        @if($isCheckOut)
        <li class="nav-item">
            <a href="{{route('stock.sellpricehistory', ['id' => $stockitemid])}}" class="nav-link" href="#" tabindex="-1" aria-disabled="true">{{__('text.selling_price_history')}}</a>
        </li>
        @endif
        @if($isCheckIn)
        <li class="nav-item">
            <a href="{{route('stock.purchasepricehistory', ['id' => $stockitemid])}}" class="nav-link active" href="#" tabindex="-1" aria-disabled="true">{{__('text.purchase_price_history')}}</a>
        </li>
        @endif
        <li class="nav-item">
            <a href="{{route('stock.pricehistory', ['id' => $data->id])}}" class="nav-link" href="#" tabindex="-1" aria-disabled="true">{{__('text.price_history')}}</a>
        </li>
    </ul>
    <div class="table-responsive-sm">
        <table class="table " id="data">
            <thead>
                <tr>
                    <th>{{__('text.time')}}</th>
                    <th>{{__('text.reference')}}</th>
                    <th>{{__('text.creator')}}</th>
                    <th>{{__('text.price')}}</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@push('scripts')
<script type="module">
$(function() {
    $('#data').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{!! route('transaction.getpurchasepricehistory') !!}?stockitemid={{ $stockitemid }}",
        dom: '<"d-flex align-items-md-center flex-column flex-md-row justify-content-md-between pb-3"Bf>rt<"pt-3 d-flex align-items-md-center flex-column flex-md-row justify-content-md-between"lp><"clear">',
        language: {
            url: langUrl // Polish language JSON file
        },
        
        columns: [
            {
                data: 'updated_at',
                name: 'updated_at'
            },
            {
                data: 'show_reference',
                name: 'show_reference'
            },
            {
                data: 'creator',
                name: 'creator'
            },
            {
                data: 'price',
                name: 'price'
            },
        ],
        buttons: [{
                extend: 'csv',
                text: '<div class="d-flex align-items-center"><span class="material-symbols-rounded">text_snippet</span> CSV</div>',
                className: 'btn btn-sm btn-fill btn-info ',
                title: 'Checkin Data',
                exportOptions: {
                    columns: [1, 2, 3, 4]
                }
            },
            {
                extend: 'pdf',
                text: '<div class="d-flex align-items-center"><span class="material-symbols-rounded">picture_as_pdf</span> PDF</div>',
                className: 'btn btn-sm btn-fill btn-info ',
                title: 'Checkin Data',
                orientation: 'landscape',
                exportOptions: {
                    columns: [1, 2, 3, 4]
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