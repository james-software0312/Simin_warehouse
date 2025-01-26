@extends('layouts.app')
@section('title', __('text.checkin_list'))

@section('content')

<div class="body-inner">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
        <span class="material-symbols-rounded">task_alt</span> {{ session('success') }}
    </div>
    @endif
    <div class="mb-4 d-flex align-items-center justify-content-between">
        <h2>{{__('text.checkin_list')}}</h2>
        <div class="text-end">
            <a href="{{route('transaction.checkin')}}" class="btn btn-primary d-flex align-items-center">
                <span class="material-symbols-rounded">add</span>{{__('text.add')}}</a>
        </div>
    </div>
</div>