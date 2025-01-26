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
        <h2 class="mb-5">{{__('text.overall_reports')}}</h2>
        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="bg-neutral-20 p-3 rounded">
                    <p class="mb-0"><strong class="item fs-4">{{$totalitem}}</strong></p>
                    <p class="fs-7 text-neutral-80 mb-0">
                        {{__('text.items')}}
                    </p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="bg-neutral-20 p-3 rounded">
                    <p class="mb-0"><strong class="item fs-4">{{$totalcategories}}</strong></p>
                    <p class="fs-7 text-neutral-80 mb-0">
                        {{__('text.categories')}}
                    </p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="bg-neutral-20 p-3 rounded">
                    <p class="mb-0"><strong class="item fs-4">{{$totalwarehouses}}</strong></p>
                    <p class="fs-7 text-neutral-80 mb-0">
                        {{__('text.warehouses')}}
                    </p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="bg-neutral-20 p-3 rounded">
                    <p class="mb-0"><strong class="item fs-4">{{$totalunits}}</strong></p>
                    <p class="fs-7 text-neutral-80 mb-0">
                        {{__('text.units')}}
                    </p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="bg-neutral-20 p-3 rounded">
                    <p class="mb-0"><strong class="item fs-4">{{$totalshelf}}</strong></p>
                    <p class="fs-7 text-neutral-80 mb-0">
                        {{__('text.shelf')}}
                    </p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="bg-neutral-20 p-3 rounded">
                    <p class="mb-0"><strong class="item fs-4">{{$totalcheckins}}</strong></p>
                    <p class="fs-7 text-neutral-80 mb-0">
                        {{__('text.checkins')}}
                    </p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="bg-neutral-20 p-3 rounded">
                    <p class="mb-0"><strong class="item fs-4">{{$totalcheckouts}}</strong></p>
                    <p class="fs-7 text-neutral-80 mb-0">
                        {{__('text.checkouts')}}
                    </p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="bg-neutral-20 p-3 rounded">
                    <p class="mb-0"><strong class="item fs-4">{{$totalusers}}</strong></p>
                    <p class="fs-7 text-neutral-80 mb-0">
                        {{__('text.users')}}
                    </p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="bg-neutral-20 p-3 rounded">
                    <p class="mb-0"><strong class="item fs-4">{{$totalcontacts}}</strong></p>
                    <p class="fs-7 text-neutral-80 mb-0">
                        {{__('text.contacts')}}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Define the modal content and title -->
     

   

    


@push('scripts')

    @endpush
@endsection

