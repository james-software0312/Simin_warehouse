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
                Configure Your Website
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('setup.configuration.submit') }}" autocomplete="off" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label>Company Name <span class="text-danger">*</span></label>
                        <input type="text" name="company" class="form-control" value="" placeholder="Enter Your Company Name" required>
                        <input type="hidden" name="pagename" id="pagename" value="Page"/>
                    </div>

                   
                    
                    
                    <div class="mb-3">
                        <label>App Logo <span class="text-danger">*</span></label>
                        <input type="file" name="logo" class="form-control" placeholder="Select App Logo" required>
                    </div>
                   
                    <div class="mb-3">
                        <label>App Timestamp <span class="text-danger">*</span></label>
                        <select name="timezone" class="form-control" required>
                            <option value="Asia/Singapore">Asia/Singapore</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Date format <span class="text-danger">*</span></label>
                        <select name="datetime" class="form-control" required>
                        <option value="d-m-Y" selected>d-m-Y</option>
                                <option value="m-d-Y">m-d-Y</option>
                                <option value="Y-m-d">Y-m-d</option>
                                <option value="d/m/Y">d/m/Y</option>
                                <option value="m/d/Y">m/d/Y</option>
                                <option value="Y/m/d">Y/m/d</option>
                                <option value="d.m.Y">d.m.Y</option>
                                <option value="m.d.Y">m.d.Y</option>
                                <option value="Y.m.d">Y.m.d</option>
                        </select>
                    </div>

                    
                    
                    <button type="submit" class="btn btn-primary">Save Setting</button>
                </form>
            </div>
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