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
                Enter Database Details
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('setup.database.submit') }}" autocomplete="off">
                    @csrf
                    <div class="mb-3">
                        <label>Host <span class="text-danger">*</span></label>
                        <input type="text" name="host" class="form-control" value="{{ old('host') ?:'127.0.0.1' }}" placeholder="Enter Database Host">
                        <div class="form-text">Enter Server IP In Case Of Remote Database, 127.0.0.1 Is Default</div>
                    </div>
                    <div class="mb-3">
                        <label>Port <span class="text-danger">*</span></label>
                        <input type="text" name="port" class="form-control" value="{{ old('port') ?:'3306' }}" placeholder="Enter Database Port">
                        <div class="form-text">Enter Database Port. Default Is 3306</div>
                    </div>
                    <div class="mb-3">
                        <label>Database Name <span class="text-danger">*</span></label>
                        <input type="text" name="database" value="{{ old('database')}}" class="form-control" placeholder="Enter Database Name">
                        <div class="form-text">Enter Database Name Here, Create New Database If Haven't Already</div>
                    </div>
                    <div class="mb-3">
                        <label>Database User <span class="text-danger">*</span></label>
                        <input autocomplete="off" type="text" name="user" value="{{ old('user')}}" class="form-control" placeholder="Enter Database User Name Here">
                        <div class="form-text">Enter Database Username Here, Create New User If Haven't Already</div>
                    </div>
                    <div class="mb-3">
                        <label>Database User Password <span class="text-danger">*</span></label>
                        <input autocomplete="new-password" type="password" name="password" value="{{ old('password')}}" class="form-control" placeholder="Enter Database User Password">
                        <div class="form-text">Enter Database Password Here</div>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Details</button>
                </form>
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