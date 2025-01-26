@extends('layouts.app')
@section('title', __('text.reports'))

@section('content')
<div class="body-inner">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
            <span class="material-symbols-rounded">task_alt</span> {{ session('success') }}
        </div>
    @endif
    <div class="">
        <h2 class="mb-5">{{__('text.reports')}}</h2>
        <div class="row">
            <div class="col-md-6">
                <ul class="ps-0">
                    <!-- <li class="d-flex align-items-center mb-2"><span class="material-symbols-rounded">chevron_right</span> <a href="{{ route('reports.overall') }}">{{__('text.overall_report')}}</a> </li> -->
                    <li class="d-flex align-items-center mb-2"><span class="material-symbols-rounded">chevron_right</span> <a href="{{ route('reports.checkin') }}">{{__('text.checkin_report')}}</a> </li>
                    <li class="d-flex align-items-center mb-2"><span class="material-symbols-rounded">chevron_right</span> <a href="{{ route('reports.checkout') }}">{{__('text.checkout_report')}}</a> </li>
                </ul>
            </div>
            <!-- <div class="col-md-6">
                <ul class="ps-0">
                    <li class="d-flex align-items-center mb-2"><span class="material-symbols-rounded">chevron_right</span> <a href="{{ route('reports.stock') }}">{{__('text.stock_report')}}</a> </li>
                    <li class="d-flex align-items-center mb-2"><span class="material-symbols-rounded">chevron_right</span> <a href="{{ route('reports.warehouse') }}">{{__('text.warehouse_report')}}</a> </li>
                    <li class="d-flex align-items-center mb-2"><span class="material-symbols-rounded">chevron_right</span> <a href="{{ route('reports.category') }}">{{__('text.category_report')}}</a> </li>
                </ul>
            </div> -->
        </div>
    </div>
</div>

<!-- Define the modal content and title -->
     

   

    


@push('scripts')

    @endpush
@endsection

