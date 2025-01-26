@extends('layouts.app')
@section('title', __('text.account'))

@section('content')

<div class="body-inner">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
            <span class="material-symbols-rounded">task_alt</span> {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center" role="alert">
            <span class="material-symbols-rounded">error</span>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mb-4">
        <h2>{{ __('text.account') }}</h2>
        <p>{{ __('text.change_profile') }}</p>
    </div>

    <div class="">
        <form id="updateprofile" method="POST" action="{{ route('user.update') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="editid" id="editid" value="{{auth()->user()->id}}"> 
            <input type="hidden" name="source" value="1"/>
            
            <div class="mb-3">
                <label for="editname" class="form-label">{{ __('text.name') }}</label>
                <input type="text" class="form-control" id="editname" name="name" placeholder="{{ __('text.name') }}" required value="{{ auth()->user()->name }}">
            </div>
           
            <div class="mb-3">
                <label for="password" class="form-label">{{ __('text.password') }}</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password" placeholder="{{ __('text.password') }}">
                    <button class="btn btn-outline-secondary border" type="button" id="togglePassword">
                        <span class="material-symbols-rounded">visibility_off</span>
                    </button>
                </div>
                <label for="password" class="error"></label>
                <small>{{ __('text.change_password_note') }}</small>
            </div>
            <div class="">
                <button type="submit" class="btn btn-primary d-flex align-items-center">
                    <span class="material-symbols-rounded">check</span>{{ __('text.submit') }}
                </button>
            </div>   
        </form>
    </div>
</div>


@push('scripts')
    <script type="module">
        $(function() {
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



        // Initialize jQuery Validation
        $('#updateprofile').validate({
            
            submitHandler: function (form) {
                form.submit();
            }
        });
    </script>
    @endpush
@endsection

