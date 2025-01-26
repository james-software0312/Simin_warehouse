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
                Ready for the next step
            </div>
            <div class="card-body">
                <form  id="submitlicense" method="POST" action="{{ route('setup.license.submit') }}" autocomplete="off">
                    @csrf
                    
                   
                   
                   
                    <button type="submit" class="btn btn-primary submitdata">Next</button>
                </form>
                
            </div>
        </div>

    </div>
</div>
</div>




</div>






@push('scripts')
<script type="module">
    $('#submitlicense').validate({
    submitHandler: function(form) {
        $(".submitdata").html('Please wait...');
        $(".submitdata").prop("disabled",true);
        form.submit();
    }
});
</script>
@endpush
@endsection