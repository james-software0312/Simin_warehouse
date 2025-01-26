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
                        Setup Complete
                    </div>
                    <div class="card-body">
                        <p>Your setup is completed, now you can start to use the application</p>
                        <a class="btn btn-primary" href="{{ url('/') }}">Start now</a>
                    </div>
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