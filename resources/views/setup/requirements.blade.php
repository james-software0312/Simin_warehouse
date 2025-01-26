@extends('layouts.main')
@section('title', __('text.installation'))

@section('content')

<div class="body-inner">
    <div class="container px-5 py-5 ">
    <div class="row justify-content-center">
    <div class="col-12 col-md-8">

        <div class="card">
            <div class="card-header">
                Minimum Requirements
            </div>
            <div class="card-body">

                <ul>
                    @foreach($checks as $key => $check)
                    <li>
                    @lang('setup.' . $key)
                    @if($check)
                    <i class="text-success">ok</i>
                    @else
                    <i class="text-danger">no</i>
                    @endif
                    </li>
                    @endforeach
                </ul>

                @if($success)
                <a href="{{ route('setup.license') }}" class="btn btn-primary">
                    Setup License
                </a>
                @endif
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