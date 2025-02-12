@extends('layouts.app')
@section('title', __('text.add_stock_item'))

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.12/themes/default/style.min.css" />

<div class="body-inner">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
            <span class="material-symbols-rounded">task_alt</span> {{ session('success') }}
        </div>
    @endif

    <div class="">
        <h2>{{__('text.add_stock_item')}}</h2>
        <form id="adddataform" method="POST" action="{{ route('stock.store') }}" enctype="multipart/form-data">
        @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="name" class="form-label">{{__('text.name')}}</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="{{__('text.name')}}" required >
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">{{__('text.code')}}</label>
                        <input type="text" class="form-control" id="code" name="code" placeholder="{{__('text.code')}}" value="{{$generate}}" required >
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="name" class="form-label">{{__('text.size')}}</label>
                        <select name="size" id="size" class="form-control" required>
                            @foreach($size as $item)
                            <option value='{{$item->name}}' {{$item->name=='36-41'?'selected':''}}>{{$item->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="name" class="form-label">{{__('text.color')}}</label>
                        <select name="color" id="color" class="form-control" required>
                            @foreach($color as $item)
                            <option value='{{$item->name}}' {{$item->name=='36-41'?'selected':''}}>{{$item->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- <div class="col-md-3">
                    <div class="mb-3">
                        <label for="quantity" class="form-label">{{__('text.quantity')}}</label>
                    </div>
                </div> -->
                <input type="hidden" class="form-control" id="quantity" name="quantity" placeholder="{{__('text.quantity')}}" value="0" >

                <div class="col-md-3">
                    <div class="mb-3">
                    <label for="unit" class="form-label">{{__('text.base_unit')}}</label>
                        <select name="unitid" id="unit" class="form-control" required>
                            <option value="1" >para</option>
                            {{-- @foreach($units as $unit)
                                <option value="{{ $unit->id }}" {{$unit->name=='para' ? 'selected' : ''}}>{{ $unit->name }}</option>
                                @endforeach --}}
                            </select>
                            <label for="unit" class="error"></label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="unitconverterto" class="form-label">{{__('text.auxiliary_unit')}}</label>
                            <select name="unitconverterto" id="unitconverterto" class="form-control" required>
                                <option value="2" >karton</option>
                            {{-- @foreach($units as $unit)
                                <option value="{{ $unit->id }}" {{$unit->name=='karton' ? 'selected' : ''}}>{{ $unit->name }}</option>
                            @endforeach --}}
                        </select>
                        <label for="unitconverterto" class="error"></label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="converter" class="form-label">{{__('text.converter')}}</label>
                        <div class="row">
                            <div class="col-3">
                                <input type="number" class="form-control" id="unitconverter" name="unitconverter" placeholder="{{__('text.converter')}}" value="12" required >
                            </div>
                            <div class="col-2">
                                <p class="pt-2"><span id="converter_from">pair</span></p>
                            </div>
                            <div class="col-1">
                                <p class="pt-2"> = </p>
                            </div>
                            <div class="col-3">
                                <input type="number" class="form-control" id="unitconverter1" name="unitconverter1" placeholder="{{__('text.converter')}}" value="1" required >
                            </div>
                            <div class="col-2">
                                <p class="pt-2"><span id="converter_to">converter</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <label for="category" class="form-label">{{__('text.category')}}</label>
                    <input name="categoryid" id="category" style="display: none;" required></input>
                    <label for="category" class="error"></label>
                    <div id="category-tree-container"></div>
                </div>
                <div class="col-md-6 row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">{{__('text.sub_type')}}</label>
                            <input type="text" class="form-control" id="itemsubtype" name="itemsubtype" placeholder="{{__('text.sub_type')}}" >
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="warehouse" class="form-label">{{__('text.warehouse')}}</label>
                            <select name="warehouseid" id="warehouse" class="form-control select2-enable warehousedata" required>
                                <option value="">{{__('text.select')}}...</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" @if($warehouse->is_primary) selected @endif>{{ $warehouse->name }}</option>
                            @endforeach
                            </select>
                            <label for="warehouse" class="error"></label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="price" class="form-label">{{__('text.price_without_vat')}}</label>
                        <input type="number" class="form-control" id="price" name="price" placeholder="{{__('text.price_without_vat')}}" required step="0.01">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="vat" class="form-label">{{__('text.vat')}}</label>

                        <select name="vat" id="vat" class="form-control" required>
                            @foreach($vat as $item)
                            <option value="{{$item->name}}" {{$item->name==23?'selected':''}}>{{$item->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <!-- <div class="row">

                <div class="col-md-6">
                    <div class="mb-3">
                    <label for="shelf" class="form-label">{{__('text.shelf')}}</label>
                        <select name="shelfid" id="shelf" class="form-control select2-enable shelfdata" required>

                        </select>
                        <label for="shelf" class="error"></label>
                    </div>
                </div>
            </div> -->

            <div class="row">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="description" class="form-label">{{__('text.description')}}</label>
                        <textarea id="description" class="form-control" name="description" placeholder="{{__('text.description')}}"></textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="photo" class="form-label">{{__('text.photo')}}</label>
                        <input type="file" class="form-control" id="photo" name="photo">
                    </div>
                </div>
                <div class="col-md-6">
                    <input type="hidden" id="is_visible" name="is_visible" value="0" />
                    <label for="isVisibleChk" class="form-label">{{ __('text.visible_website') }} </label>
                    <div class="mb-3">
                        <input type="checkbox" data-toggle="switchbutton" id="isVisibleChk" name="isVisibleChk" >
                    </div>
                </div>
            </div>
            <div id="message"></div>
            <div class="d-flex ">
                <button type="submit" class="btn btn-primary d-flex align-items-center" id="submit"><span   class="material-symbols-rounded">check</span> {{__('text.submit')}}</button>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script type="module" src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.12/jstree.min.js"></script>
<script type="module">
    $(function () {
        // $("#unit").select2();
        // $("#category").select2();
        $("#warehouse").select2();
        $("#vat").select2();
        $("#size").select2();

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
                                parent: item.parent_id === null ? "#" : item.parent_id
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

        // $("#vat").select2({
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

        // $("#size").select2({
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

        // $("#size").on('change', function() {
        //     var id = $(this).val()
        //     $("#size").val(3)
        //     return;
        //     $.ajax({
        //         url: '{!!route('size.ajaxstore')!!}',
        //         type: 'POST',
        //         data: {name: term},
        //         success: function(data) {
        //             console.log(data.id)
        //             return {
        //                 id: data.id,
        //                 text: term,
        //                 newTag: true // Tag is marked as new
        //             }
        //         }
        //     })
        // })

        $('#adddataform').on('submit', function(e) {
        e.preventDefault();  // Prevent default form submission

        var formData = new FormData(this);

        $.ajax({
            type: 'POST',
            url: "{{ route('stock.store') }}", // Replace with your store route
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                // Clear previous message
                $('#message').empty();

                // Only handle the case when success == false
                if (!response.success) {
                    $('#message').html('<div class="alert alert-danger">' + response.message + '</div>');
                } else {
                    // For successful cases, allow the page to redirect
                    window.location.href = "{{ route('stock.index') }}"; // Replace with your route
                }
            },
            error: function(xhr) {
                $('#message').html('<div class="alert alert-danger">An error occurred. Please try again.</div>');
            }
        });
    });
        $('#adddataform').validate({
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
                if (!$("#category").val()) {
                    alert("{!!__('text.category_field_required')!!}");
                    return;
                }
                if ($("#unitconverterto").val() == $("#unit").val()) {
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
        $("#converter_from").text($("#unit").find('option[value=' + $("#unit").val() + ']').text());
        $("#converter_to").text($("#unitconverterto").find('option[value=' + $("#unitconverterto").val() + ']').text());
        $("#unit").on('change', function(e) {
            var selectedValue = $(this).val(); // Get the selected value
            $("#converter_from").text($(this).find('option[value=' + selectedValue + ']').text())

            // Add any additional logic here
        });

        $("#unitconverterto").on('change', function(e) {
            var selectedValue = $(this).val(); // Get the selected value
            $("#converter_to").text($(this).find('option[value=' + selectedValue + ']').text())

            // Add any additional logic here
        });

        // $('#submit').on('click', function () {
        //     // Validate the form
        //     if ($('#adddataform').valid()) {
        //         // If the form is valid, submit it
        //         $('#adddataform').submit();
        //     }
        // });
        $('#isVisibleChk').change(function() {
            $('#is_visible').val(this.checked ? 1 : 0);
        });
    })
</script>
@endpush
@endsection
