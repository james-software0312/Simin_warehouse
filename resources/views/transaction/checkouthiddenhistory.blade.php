@extends('layouts.app')
@section('title', __('text.hidden_history'))

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

    <div class="mb-4 d-flex align-items-center justify-content-between">
        <h2>{{ __('text.hidden_history') }}</h2>
    </div>
    <div class="row">
        <div class="col-md-12">
            <p><span style="font-weight: bold">{{__('text.customer')}}</span>: {{$sell->customername}}</p>
            <p><span style="font-weight: bold">{{__('text.date')}}</span>: {{$sell->selldate}}</p>
        </div>
        <h4 style="background-color: #cdcdcd; padding: 5px 3px">{{__('text.sale_detail')}}</h4>
        <table class="table">
            <thead>
                <tr>
                    <th>{{__('text.item')}}</th>
                    <th>{{__('text.code')}}</th>
                    <th>{{__('text.quantity')}}</th>
                    <th>{{__('text.sale_price')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sell_detail as $item)
                <tr>
                    <td>{{$item->name}}</td>
                    <td>{{$item->code}}</td>
                    <td>
                        {{$item->quantity}} {{$item->sellunitname}}
                    </td>
                    <td>{{$item->price + $item->discount}}{{ __("text.PLN") }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="row d-none">
        <h4 style="background-color: #cdcdcd; padding: 5px 3px">{{__('text.hidden_purchases')}}</h4>
        <table class="table">
            <thead>
                <tr>
                    <th>{{__('text.reference')}}</th>
                    <th>{{__('text.supplier')}}</th>
                    <th>{{__('text.item')}}</th>
                    <th>{{__('text.code')}}</th>
                    <th>{{__('text.date')}}</th>
                    <th>{{__('text.hidden_amount')}}</th>
                    <th>{{__('text.purchase_price')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($history as $item)
                    <tr>
                        <td>{{$item->reference}}</td>
                        <td>{{$item->supplier}}</td>
                        <td>{{$item->itemname}}</td>
                        <td>{{$item->code}}</td>
                        <td>{{$item->created_at}}</td>
                        <td>{{$item->hidden_amount . " " . $item->transactionunitname}}</td>
                        <td>{{$item->price}} {{ __("text.PLN") }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection