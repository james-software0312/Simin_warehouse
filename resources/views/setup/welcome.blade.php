@extends('layouts.main')
@section('title', __('text.installation'))

@section('content')

<div class="body-inner">
    <div class="container px-5 py-5 ">
    <div class="row justify-content-center">
    <div class="col-12 col-md-8">
        @if($errors->any())
        <div class="card mb-1">
            <div class="card-body text-danger">
                {{$errors->first()}}
            </div>
        </div>
        @endif
        <div class="card">
            <div class="card-header">
                Welcome To Web Installer
            </div>
            <div class="card-body">

                <p>Step By Step Installer</p>

                <ol>
                    <li>Check Minimum Requirements</li>
                    <li>Enter license information</li>
                    <li>Enter Database Details</li>
                    <li>Setup User Account</li>
                </ol>

                <a href="{{ route('setup.requirements') }}" class="btn btn-primary">
                    Check Minimum Requirements
                </a>
            </div>
        </div>

    </div>
</div>




</div>






@push('scripts')
<script>

</script>
@endpush
@endsection