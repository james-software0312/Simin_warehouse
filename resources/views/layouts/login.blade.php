@extends('layouts.main')
@section('title', __('text.page'))

@section('content')

<div class="body-inner">
    <div class="container px-5 py-5 ">
    <div class="login-container">
        <div class="row">
            <div class="col-md-6 bg-primary px-5 py-5 first-column">
                </div>
                <div class="col-md-6 bg-white p-5 second-column">
                    <form id="login" method="POST" action="{{ route('user.dologin') }}">
                        @csrf
                        @if(session('statuserror'))
                        <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center" role="alert">
                            <span class="material-symbols-rounded">error</span> {{ session('statuserror') }}
                        </div>
                        @endif  
                        <div class="mb-3">
                            
                            <img width="80" src="{{ asset('storage/settings/').'/'.$data->logo }}" class="img-fluid" />
                            <h3 class="mt-2">Welcome back</h3>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Email address"
                                required>
                        </div>

                        <div class="mb-1">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">

                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Password" required>
                                <button class="btn btn-outline-secondary border" type="button" id="togglePassword"><span
                                        class="material-symbols-rounded">visibility_off</span></button>
                            </div>
                            <label for="password" class="error"></label>
                        </div>
                        <div class="text-end">
                            <small class="text-end"><a id="forgotpassword" class="text-end" href="?status=1">Forgot your
                                    password?</a></small>
                        </div>


                        <div class="">
                            <button type="submit" class="btn btn-primary d-flex align-items-center">
                                <span class="material-symbols-rounded">login</span>Login
                            </button>
                        </div>
                    </form>

                    <form id="forgot" method="POST" action="{{ route('forgot-password') }}" class="d-none ">
                        @csrf
                        @if(session('status'))
                        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                            <span class="material-symbols-rounded">task_alt</span> {{ session('status') }}
                        </div>
                        @endif   
                        @if(session('statuserror'))
                        <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center" role="alert">
                            <span class="material-symbols-rounded">error</span> {{ session('statuserror') }}
                        </div>
                        @endif  
                        <div class="mb-3">
                        <img width="80" src="{{ asset('storage/settings/').'/'.$data->logo }}" class="img-fluid" />
                            <h3 class="mt-2">Forgot password?</h3>
                        </div>
                        <div class="mb-3">
                            <label for="forgotemail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="forgotemail" name="forgotemail"
                                placeholder="Email address" required>
                        </div>
                        <div class="text-end">
                            <small class="text-end"><a id="loginbutton" class="text-end" href="?status=0">Have an account login
                                    here?</a></small>
                        </div>
                        <div class="">
                            <button type="submit" class="btn btn-primary d-flex align-items-center">
                                <span class="material-symbols-rounded">login</span>Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>




</div>
<!-- Define the modal content and title -->





@push('scripts')
<script>
$('#login').validate({
    submitHandler: function(form) {
        form.submit();
    }
});

$('#forgot').validate({
    submitHandler: function(form) {
        form.submit();
    }
});

$(document).ready(function() {

    var Param = {{$id = request('status','0')}};
    if(Param == 1){
       
            $("#login").addClass('d-none');
            $("#forgot").addClass('animation');
            $("#forgot").removeClass('d-none');
    }else{
        $("#forgot").addClass('d-none');
            $("#login").addClass('animation');
            $("#login").removeClass('d-none');
    }

    const passwordInput = $('#password');
    const togglePasswordButton = $('#togglePassword');
    const togglePasswordButtonSpan = $('#togglePassword span');

    togglePasswordButton.click(function() {
        if (passwordInput.attr('type') === 'password') {
            passwordInput.attr('type', 'text');
            togglePasswordButtonSpan.text('visibility');
        } else {
            passwordInput.attr('type', 'password');
            togglePasswordButtonSpan.text('visibility_off');
        }
    });

    
});
</script>
@endpush
@endsection