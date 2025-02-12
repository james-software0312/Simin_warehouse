@extends('layouts.app')
@section('title', __('text.category'))

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.12/themes/default/style.min.css" />

<div class="body-inner">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
            <span class="material-symbols-rounded">task_alt</span> {{ session('success') }}
        </div>
    @endif
    <div class="mb-4 d-flex align-items-center justify-content-between">
        <h2>{{ __('text.category') }}</h2>
    </div>
	<div class="row">
        <div class="col-md-6">
            <div id="jstree"></div>
        </div>
		<div class="col-md-6">
			<img src="" alt="" id="category_img" style="width: 100%">
		</div>
	</div>


</div>

<x-modal>
    <form id="adddataform" method="POST" action="{{ route('category.store') }}"  enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">{{ __('text.name') }}</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="{{ __('text.name') }}" required>
		</div>
		<div class="mb-3">
			<label for="image" class="form-label">{{ __('text.image') }}</label>
            <input type="file" class="form-control" id="photo" name="photo">

			<input type="hidden" name="parent_id" id="parent_id" />
        </div>
    </form>
</x-modal>

<x-delete>
    <form id="deletedataform" method="POST" action="{{ route('category.destroy')}}">
        @csrf
        <input type="hidden" name="deleteid" id="delete_id">
    </form>
</x-delete>

<x-deleteImage>
    <form id="deleteimagedataform" method="POST" action="{{ route('category.deleteImage')}}">
        @csrf
        <input type="hidden" name="deleteimage_id" id="deleteimageid">
    </form>
</x-deleteImage>

<x-edit>
    <form id="editdataform" method="POST" action="{{ route('category.update') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="editid" id="editid" value="">

        <div class="mb-3">
            <label for="editname" class="form-label">{{ __('text.name') }}</label>
            <input type="text" class="form-control" id="editname" name="name" placeholder="{{ __('text.name') }}" required>
        </div>
		<div class="mb-3">
			<label for="image" class="form-label">{{ __('text.image') }}</label>
            <input type="file" class="form-control" id="photo" name="photo">
		</div>
    </form>
</x-edit>


<script type="module" src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.12/jstree.min.js"></script>
<script>
    $(document).ready(function () {
        // Get the CSRF token from the meta tag
        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        // Set up the AJAX request to include the CSRF token in the headers
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        });
        const $tree = $('#jstree')
        $tree.jstree({
            core: {
                data: function (node, callback) {
                    $.ajax({
                        url: "{{ route('category.get') }}",
                        dataType: "json",
                        success: function (data) {
                            // Transform `parent_id` to `parent`
                            const transformedData = data.map(item => ({
                                id: item.id,
                                text: item.title,
                                image: item.path,
                                parent: item.parent_id === null ? "#" : item.parent_id,
                                state: {
                                    opened: true // This opens all layers initially
                                }
                            }));
                            callback(transformedData);
                        }
                    });
                },
                check_callback: true, // Allows CRUD operations
            },
            plugins: ["contextmenu", "dnd", "state", "types"], // Add necessary plugins
            contextmenu: {
                items: function ($node) {
                    return {
                        CreateRoot: {
                            label: "{{ __('text.Createroot') }}",
                            action: function () {
								const modal = new bootstrap.Modal($('#AddModal'));
								$("#parent_id").val(null);
								modal.show();
                            }
                        },
                        Create: {
                            label: "{{ __('text.Create') }}",
                            action: function () {
								const modal = new bootstrap.Modal($('#AddModal'));
								$("#parent_id").val($node.id);
								modal.show();
                            }
                        },
                        Rename: {
                            label: "{{ __('text.Rename') }}",
                            action: function () {
								$("#editid").val($node.id);
								$("#editname").val($node.text);
								const modal = new bootstrap.Modal($('#EditModal'));
								modal.show();
                            }
                        },
                        Delete: {
                            label: "{{ __('text.Delete') }}",
                            action: function () {
								console.log($node)
								$("#delete_id").val($node.id);
                                const modal = new bootstrap.Modal($('#DeleteModal'));
								modal.show();
                            }
                        },
                        Image_Delete: {
                            label: "{{ __('text.Image_Delete') }}",
                            action: function () {
                                $("#deleteimageid").val($node.id);
                                $("#category_img").html('');
                                const modal = new bootstrap.Modal($('#DeleteImageModal'));
								modal.show();
                            }
                        }
                    };
                }
            }
        });

		$tree.on('select_node.jstree', function (e, data) {
			// The clicked node data is available in `data.node`
			console.log('Node selected:', data.node);

			// Example: Show details of the clicked node
			$("#category_img").attr("src", '{!!env('MEDIA_UPLOADER_URL')!!}' + '/' + data.node.original.image);
		});

		$('#adddataform').validate({
			rules: {
			},
			messages: {
				name: {
					required: '{!!__('text.field_required')!!}'
				},
			},
			submitHandler: function(form) {
				form.submit();
			}
		});
		$('#editdataform').validate({
			rules: {

			},
			messages: {
				name: {
					required: '{!!__('text.field_required')!!}'
				},

			},
			submitHandler: function(form) {
				form.submit();
			}
		});
    });
</script>
@endsection
