@extends('layouts.app')
@section('title', __('text.edit_stock_item'))

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.12/themes/default/style.min.css" />
<div class="body-inner">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
            <span class="material-symbols-rounded">task_alt</span> {{ session('success') }}
        </div>
    @endif
    <?php
        $isCheckIn = $modules->contains(function($module) {
            return ($module->module == 'purchase') && ($module->hasViewPermission || $module->hasEditPermission);
        });

        $isCheckOut = $modules->contains(function($module) {
            return ($module->module == 'transaction') && ($module->hasViewPermission || $module->hasEditPermission);
        });
    ?>
    <div class="">
       
        <form id="editdataform" method="POST" action="{{ route('vat.update') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="editid" id="editid" value="{{$data->id}}">
                <div class="row">
                    <div class="col-md-12 row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="description" class="form-label">{{__('text.vat')}}</label>    
                                <input type="number" class="form-control" id="editprice" name="name" required min="0" max="100" >
                                </div>
                        </div>
                    </div>
                    <div class="col-md-12 row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="description" class="form-label">{{__('text.description')}}</label>
                                <textarea style="height: 200px;" rows="5" id="editdescription" class="form-control" name="description" placeholder="{{__('text.description')}}">{{$data->description}}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <a href="{{route('vat.index')}}" class="btn btn-danger d-flex align-items-center">
                            <span class="material-symbols-rounded">close</span>{{__('text.Cancel')}}</a>&nbsp;&nbsp;
                        <button type="submit" class="btn btn-primary d-flex align-items-center" id="submit"><span   class="material-symbols-rounded">check</span> {{__('text.submit')}}</button>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>
@endsection