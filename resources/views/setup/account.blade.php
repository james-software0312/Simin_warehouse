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
                Setup Your User Account
            </div>
            <div class="card-body">
                @if(!empty($success))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                    <span class="material-symbols-rounded">task_alt</span> {{ $success }}
                </div>
                @endif
                <form method="POST" action="{{ route('setup.account.submit') }}" autocomplete="off" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label>Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Enter Your Full Name">
                    </div>
                    <div class="mb-3">
                        <label>Email <span class="text-danger">*</span></label>
                        <input type="text" name="email" class="form-control" value="{{ old('email') }}" placeholder="Enter Your E-Mail Address">
                    </div>
                    
                    <div class="mb-3">
                        <label>Password <span class="text-danger">*</span></label>
                        <input autocomplete="new-password" type="password" name="password" value="{{ old('password')}}" class="form-control" placeholder="Enter Your Password">
                    </div>
                    <div class="mb-3">
                        <label>Re-Type Password <span class="text-danger">*</span></label>
                        <input autocomplete="new-password" type="password" name="confirm_password" value="{{ old('password')}}" class="form-control" placeholder="Confirm Your Address">
                    </div>
                    <button type="submit" class="btn btn-primary">Create User</button>
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