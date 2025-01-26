@extends('layouts.main')
@section('title', __('auth.reset_password'))

@section('content')

<div class="body-inner">
    <div class="container px-5 py-5 ">
    <div class="login-container">
        <div class="row">
            
                <div class="col-md-6 bg-primary px-5 py-5 first-column">
                    
                </div>
                <div class="col-md-6 bg-white p-5 second-column">
                    <form id="reset" method="POST" action="{{ route('user.reset') }}">
                        @csrf
                        @if(session('statuserror'))
                        <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center" role="alert">
                            <span class="material-symbols-rounded">error</span> {{ session('statuserror') }}
                        </div>
                        @endif  

                        <div class="mb-3">
                           
                            <h3>{{__('auth.reset_password')}}</h3>
                        </div>
                        

                        <div class="mb-1">
                            <label for="password" class="form-label">New Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Password" required>
                                <button class="btn btn-outline-secondary border" type="button" id="togglePassword"><span
                                        class="material-symbols-rounded">visibility_off</span></button>
                            </div>
                            <label for="password" class="error"></label>
                        </div>

                        <div class="mb-1">
                            <label for="repeatpassword" class="form-label">Repeat Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="repeatpassword" name="repeatpassword"
                                    placeholder="Password" required>
                                <button class="btn btn-outline-secondary border" type="button" id="toggleRepeatPassword"><span
                                        class="material-symbols-rounded">visibility_off</span></button>
                            </div>
                            <label for="repeatpassword" class="error"></label>
                        </div>
                       

                        <div class="">
                            <input type="hidden" name="token" value="{{ $token }}">
                            <button type="submit" class="btn btn-primary d-flex align-items-center">
                                <span class="material-symbols-rounded">login</span>Reset
                            </button>
                        </div>
                    </form>

                    
                </div>
            </div>
        </div>
    </div>




</div>

@push('scripts')
<script type="module">
$('#reset').validate({
    rules: {
        password: {
            minlength: 5,
        },
        repeatpassword: {
            minlength: 5,
            equalTo: "#password"
        }
    },
    submitHandler: function(form) {
        form.submit();
    }
});



$(document).ready(function() {
    const passwordInput = $('#password,#repeatpassword');
    const togglePasswordButton = $('#togglePassword, #toggleRepeatPassword');
    const togglePasswordButtonSpan = $('#togglePassword span, #toggleRepeatPassword span');

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