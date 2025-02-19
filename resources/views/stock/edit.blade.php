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
        <ul class="nav nav-tabs mb-5">
            <li class="nav-item">
                <a href="{{route('stock.edit', ['id' => $data->id])}}" class="nav-link active" aria-current="page" href="#">{{__('text.edit_stock_item')}}</a>
            </li>
            <li class="nav-item">
                <a href="{{route('stock.history', ['id' => $data->id])}}" class="nav-link" href="#" tabindex="-1" aria-disabled="true">{{__('text.history')}}</a>
            </li>
            @if($isCheckOut)
            <li class="nav-item">
                <a href="{{route('stock.sellpricehistory', ['id' => $data->id])}}" class="nav-link" href="#" tabindex="-1" aria-disabled="true">{{__('text.selling_price_history')}}</a>
            </li>
            @endif
            @if($isCheckIn)
            <li class="nav-item">
                <a href="{{route('stock.purchasepricehistory', ['id' => $data->id])}}" class="nav-link" href="#" tabindex="-1" aria-disabled="true">{{__('text.purchase_price_history')}}</a>
            </li>
            @endif
            <li class="nav-item">
                <a href="{{route('stock.pricehistory', ['id' => $data->id])}}" class="nav-link" href="#" tabindex="-1" aria-disabled="true">{{__('text.price_history')}}</a>
            </li>
        </ul>
        <form id="editdataform" method="POST" action="{{ route('stock.update') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="editid" id="editid" value="{{$data->id}}">
            <div class="row">
                <div class="col-md-4 row">
                    <div class="col-md-12">
                        <div class="mb-3 text-center">
                            <img src="{{$photo}}" class="photodetail img-fluid" width="250" />
                        </div>
                    </div>
                </div>
                <div class="col-md-8 row">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">{{__('text.name')}}</label>
                                <input type="text" class="form-control" id="editname" name="name" placeholder="{{__('text.name')}}" value="{{$data->name}}" required >
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">{{__('text.code')}}</label>
                                <input type="text" class="form-control" id="editcode" name="code" placeholder="{{__('text.code')}}" value="{{$data->code}}"  required >
                            </div>
                        </div>
                        <div class="row col-md-6">
                            <div class="mb-3 col-md-6">
                                <label for="size" class="form-label">{{__('text.size')}}</label>
                                <select name="size" id="editsize" class="form-control" required>
                                    @foreach($size as $item)
                                    <option value='{{$item->name}}' {{$item->name == $data->size ? 'selected' : ''}}>{{$item->name}}</option>
                                    @endforeach
                                </select>

                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="color" class="form-label">{{__('text.color')}}</label>
                                <select name="color" id="editcolor" class="form-control" required>
                                    @foreach($color as $item)
                                    <option value='{{$item->name}}' {{$item->name == $data->color ? 'selected' : ''}}>{{$item->name}}</option>
                                    @endforeach
                                </select>

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edititemsubtype" class="form-label">{{__('text.sub_type')}}</label>
                                <input type="text" class="form-control" id="edititemsubtype" name="itemsubtype" placeholder="{{__('text.sub_type')}}" value="{{$data->itemsubtype}}" >
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="category" class="form-label">{{__('text.category')}}</label>
                                <input name="categoryid" id="category" style="display: none;" required></input>
                                <label for="category" class="error"></label>
                                <div id="category-tree-container"></div>
                                {{-- <select name="categoryid" id="editcategory" class="form-control" required>
                                    <option value="">{{__('text.select')}}...</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ $category->id == $data->categoryid ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                                </select>
                                <label for="editcategory" class="error"></label> --}}
                            </div>
                        </div>
                        <div class="col-md-8 row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="quantity" class="form-label">{{__('text.quantity')}}</label>
                                    <?php
                                    $quantity = $data->single_quantity;
                                    if($data->unitconverter < $data->unitconverter1 ) $quantity = round($data->single_quantity * $data->unitconverter / $data->unitconverter1, 2);
                                    ?>
                                    <input type="number" class="form-control" id="editquantity" name="quantity" placeholder="{{__('text.quantity')}}" value="{{$quantity}}" {{$hasEditQtyPermission ? 'readonly' : ''}}  disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="warehouse" class="form-label">{{__('text.warehouse')}}</label>
                                    <select name="warehouseid" id="editwarehouse" class="form-control warehousedata" disabled>
                                        <option value="">{{__('text.select')}}...</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" {{ $warehouse->id == $data->warehouseid ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                                    @endforeach
                                    </select>
                                    <label for="editwarehouse" class="error"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                            <label for="unit" class="form-label">{{__('text.unit')}}</label>
                                <select name="unitid" id="editunit" class="form-control" value="{{$data->unit}}" required>
                                    <option value="1" >para</option>
                                {{-- @foreach($units as $unit)
                                    <option value="{{ $unit->id }}" {{ $unit->id == $data->unitid ? 'selected' : '' }}>{{ $unit->name }}</option>
                                @endforeach --}}
                                </select>
                                <label for="editunit" class="error"></label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="unit" class="form-label">{{__('text.auxiliary_unit')}}</label>
                            <select name="unitconverterto" id="editunitconverterto" class="form-control" value="{{$data->unit}}" required>
                                <option value="2" >karton</option>
                                {{-- @foreach($units as $unit)
                                    <option value="{{ $unit->id }}" {{ $unit->id == $data->unitconverterto ? 'selected' : '' }}>{{ $unit->name }}</option>
                                @endforeach --}}
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="converter" class="form-label">{{__('text.converter')}}</label>
                                <div class="row">
                                    <div class="col-3">
                                        <input type="number" class="form-control" id="unitconverter" name="unitconverter" placeholder="{{__('text.converter')}}" required value="{{$data->unitconverter}}" >
                                    </div>
                                    <div class="col-2">
                                        <p class="pt-2"><span id="converter_from">pair</span></p>
                                    </div>
                                    <div class="col-1">
                                        <p class="pt-2"> = </p>
                                    </div>
                                    <div class="col-3">
                                        <input type="number" class="form-control" id="unitconverter1" name="unitconverter1" placeholder="{{__('text.converter')}}" value="{{$data->unitconverter1}}" required >
                                    </div>
                                    <div class="col-2">
                                        <p class="pt-2"><span id="converter_to">converter</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">


                        <!-- <div class="col-md-6">
                            <div class="mb-3">
                            <label for="shelf" class="form-label">{{__('text.shelf')}}</label>
                                <select name="shelfid" id="editshelf" class="form-control shelfdataedit" required>

                                </select>
                                <label for="editshelf" class="error"></label>
                            </div>
                        </div> -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editprice" class="form-label">{{__('text.price_without_vat')}}</label>
                                <input type="number" class="form-control" id="editprice" name="price" placeholder="{{__('text.price_without_vat')}}" required step="0.01" value="{{$data->price}}" >
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editvat" class="form-label">{{__('text.vat')}}</label>

                                <select name="vat" id="editvat" class="form-control" required>
                                    @foreach($vat as $item)
                                        <option value="{{$item->name}}" {{$item->name == $data->vat ? 'selected' : ''}}>{{$item->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="description" class="form-label">{{__('text.description')}}</label>
                                <textarea id="editdescription" class="form-control" name="description" placeholder="{{__('text.description')}}">{{$data->description}}</textarea>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="photo" class="form-label">{{__('text.photo')}}</label>
                                <input type="file" class="form-control" id="editphoto" name="photo">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <input type="hidden" id="is_visible" name="is_visible" value="{{$data->is_visible}}" />
                            <label for="isVisibleChk" class="form-label">{{ __('text.visible_website') }} </label>
                            <div class="mb-3">
                                <input type="checkbox" data-toggle="switchbutton" id="isVisibleChk" name="isVisibleChk" {{$data->is_visible ? 'checked' : ''}}>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <a href="{{route('stock.index')}}" class="btn btn-danger d-flex align-items-center">
                            <span class="material-symbols-rounded">close</span>{{__('text.Cancel')}}</a>&nbsp;&nbsp;
                        <button type="submit" class="btn btn-primary d-flex align-items-center" id="submit"><span   class="material-symbols-rounded">check</span> {{__('text.submit')}}</button>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>
<input type="hidden" id="old_unit" value="{{$data->unitid}}">
<input type="hidden" id="old_unit1" value="{{$data->unitconverterto}}">
<input type="hidden" id="old_converter" value="{{$data->unitconverter}}">
<input type="hidden" id="old_converter1" value="{{$data->unitconverter1}}">
@push('scripts')
<script type="module" src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.12/jstree.min.js"></script>
<script type="module">
    $(function () {
        $("#editwarehouse").select2();
        $("#editsize").select2();
        $("#editvat").select2();

        const categoryTreeContainer = $('#category-tree-container');

        categoryTreeContainer.jstree({
            core: {
                data: function (node, callback) {
                    $.ajax({
                        url: "{{ route('category.get') }}", // Replace with your route
                        dataType: "json",
                        success: function (data) {
                            const transformedData = data.map(item => ({
                                id: item.id,
                                text: item.title,
                                parent: item.parent_id === null ? "#" : item.parent_id,
                                state: {
                                    opened: true, // Opens the node
                                    selected: item.id === {!!$data->categoryid!!} // Replace 3 with the ID of the category to select
                                }
                            }));
                            callback(transformedData);
                        }
                    });
                },
                check_callback: true,
            },
            plugins: ["wholerow", "radio"], // Use `radio` plugin for single selection
            radio: {
                tie_selection: true, // Selecting a node selects the radio button
            }
        });

        // Handle selection
        categoryTreeContainer.on("changed.jstree", function (e, data) {
            if (data && data.selected.length) {
                const selectedCategoryId = data.selected[0];
                console.log("Selected Category ID:", selectedCategoryId);

                // Update the hidden select element
                $('#category').val(selectedCategoryId);
            }
        });
        // $("#editvat").select2({
        //     tags: true, // Enables adding new data
        //     placeholder: 'Select or Add New',
        //     tokenSeparators: [',', ' '], // Optional: Separate by comma or space
        //     createTag: function (params) {
        //         var term = $.trim(params.term);

        //         if (term === '') {
        //             return null;
        //         }

        //         return {
        //             id: term,
        //             text: term,
        //             newTag: true // Tag is marked as new
        //         }
        //     }
        // });

        // if (!['0', '5', '8', '23'].includes('{!! $data->vat !!}')) {
        //     var newOption = new Option('{!! $data->vat !!}', '{!! $data->vat !!}', true, true);
        //     $('#editvat').append(newOption).trigger('change');
        // }

        // $("#editsize").select2({
        //     tags: true, // Enables adding new data
        //     placeholder: 'Select or Add New',
        //     tokenSeparators: [',', ' '], // Optional: Separate by comma or space
        //     createTag: function (params) {
        //         var term = $.trim(params.term);

        //         if (term === '') {
        //             return null;
        //         }

        //         return {
        //             id: term,
        //             text: term,
        //             newTag: true // Tag is marked as new
        //         }
        //     }
        // });
        // if (!['20', '25', '30', '35', '40'].includes('{!! $data->size !!}')) {
        //     var newOption = new Option('{!! $data->size !!}', '{!! $data->size !!}', true, true);
        //     $('#editsize').append(newOption).trigger('change');
        // }

        $('#editdataform').validate({
            rules: {
                code: {
                    required: true,
                    uniquecode:true
                },
            },
            messages: {
                name: {
                    required: '{!!__('text.field_required')!!}'
                },
                code: {
                    required: '{!!__('text.field_required')!!}'
                },
                size: {
                    required: '{!!__('text.field_required')!!}'
                },
                categoryid: {
                    required: '{!!__('text.field_required')!!}'
                },
                itemsubtype: {
                    required: '{!!__('text.field_required')!!}'
                },
                unitid: {
                    required: '{!!__('text.field_required')!!}'
                },
                unitconverterto: {
                    required: '{!!__('text.field_required')!!}'
                },
                unitconverter: {
                    required: '{!!__('text.field_required')!!}'
                },
                unitconverter1: {
                    required: '{!!__('text.field_required')!!}'
                },
                warehouseid: {
                    required: '{!!__('text.field_required')!!}'
                },
                price: {
                    required: '{!!__('text.field_required')!!}'
                },
                vat: {
                    required: '{!!__('text.field_required')!!}'
                },

            },
            submitHandler: function (form) {
                if ($("#editunitconverterto").val() == $("#editunit").val()) {
                    alert("{!!__('text.not_available_units')!!}");
                    return;
                }
                if ($("#unitconverter").val() != 1 && $("#unitconverter1").val() != 1) {
                    alert("{!!__('text.converter_val_error')!!}");
                    return;
                }
                form.submit();
            }
        });

        $("#converter_from").text($("#editunit").find('option[value=' + $("#editunit").val() + ']').text());
        $("#converter_to").text($("#editunitconverterto").find('option[value=' + $("#editunitconverterto").val() + ']').text());

        $("#editunit").on('change', function(e) {
            var selectedValue = $(this).val(); // Get the selected value
            $("#converter_from").text($(this).find('option[value=' + selectedValue + ']').text())
            if ($(this).val() == $('#old_unit1').val()) {
                $("#editunitconverterto").val($('#old_unit').val())
                $("#editunitconverterto").trigger('change')
                $("#unitconverter").val($('#old_converter1').val())
                $("#unitconverter1").val($('#old_converter').val())
            }

            if ($(this).val() == $('#old_unit').val()) {
                $("#editunitconverterto").val($('#old_unit1').val())
                $("#editunitconverterto").trigger('change')
                $("#unitconverter").val($('#old_converter').val())
                $("#unitconverter1").val($('#old_converter1').val())
            }
        });
        $("#editunitconverterto").on('change', function(e) {
            var selectedValue = $(this).val(); // Get the selected value
            $("#converter_to").text($(this).find('option[value=' + selectedValue + ']').text())
        });
        $("#unitconverter").on('change', function() {
            $('#old_converter').val($(this).val());
        });
        $("#unitconverter1").on('change', function() {
            $('#old_converter1').val($(this).val());
        });
        $('#isVisibleChk').change(function() {
            $('#is_visible').val(this.checked ? 1 : 0);
        });
    })
</script>
@endpush
@endsection
