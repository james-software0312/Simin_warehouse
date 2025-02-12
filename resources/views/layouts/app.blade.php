<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - WMS System</title>

    <link href="{{ asset('public/storage/settings/').'/'.$globalsettings->logo }}" rel="icon">

    <!--Font-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;400;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    @vite(['resources/js/app.js'])
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap-switch-button@1.1.0/css/bootstrap-switch-button.min.css" rel="stylesheet">

    {{-- datepicker css --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap-switch-button@1.1.0/dist/bootstrap-switch-button.min.js"></script>
    <script type="module" src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    {{-- datepicker js --}}
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/pl.js"></script>

    <script type="module" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>

    <!-- Include your CSS and JS assets here -->
	@stack('style')
</head>
<body>
    <!---------------------------------
        Header start
        ----------------------------------->
        <div class="header-wrapper">
        	<div class="header-content">
            <nav class="navbar px-4 px-md-3 py-4">
                <div class="navbar-left ps-0 pe-md-5 pb-md-0 pb-3">
                    <div class="mobile-menu pe-3">
                        <button class="menu-trigger"><span class="material-symbols-rounded text-primary">menu</span></button>
                    </div>
                    <div class="navbar-info">
                        <p class="navbar-name mb-0"><strong>{{ __('text.dashboard') }}</strong></p>
                    </div>
                </div>

                <ul class="navbar-right flex-fill justify-content-start justify-content-md-end">
                    <li class="border-left ps-4">
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle text-right px-0" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 100%">
                                <p class="mb-0 d-flex">
                                    <strong>{{ auth()->check() ? auth()->user()->name : __('text.guest') }}</strong>
                                    <span class="material-symbols-rounded">keyboard_arrow_down</span>
                                </p>
                            </button>
                            <div class="dropdown-menu" style="right: 0" aria-labelledby="dropdownMenuButton">
                                <a href="{{ route('profile') }}" class="dropdown-item d-flex align-items-center mb-2">
                                    <span class="material-symbols-rounded">manage_accounts</span>
                                    <i class="text-primary-pressed pe-1"></i> {{ __('text.account_settings') }}
                                </a>
                                <a href="{{ route('logout') }}" class="dropdown-item d-flex align-items-center">
                                    <span class="material-symbols-rounded">logout</span>
                                    <i class="text-primary-pressed pe-1"></i> {{ __('text.logout') }}
                                </a>
                            </div>
                        </div>
                    </li>
                </ul>
            </nav>

        	</div>
        </div>
    	<!---------------------------------
        Header end
        ----------------------------------->

        <!---------------------------------
        Sidebar start
        ----------------------------------->
        <div class="sidebar-wrapper">
        	<div class="close-mobile-menu">
	        	<button class="close-trigger"><span class="material-symbols-rounded text-primary">close</span></button>
	        </div>
        	<div class="logo-wrapper pb-4">

				<img width="80" src="{{ asset('public/storage/settings/').'/'.$globalsettings->logo }}" class="img-fluid" />

        	</div>

        <div class="item-menu d-flex justify-content-between flex-column">
		<ul id="menu" class="mb-auto">
            <li>
                <a href="{{ route('home.index') }}" class="{{ Request::is('home*') ? 'active' : '' }}">
                    <span class="material-symbols-rounded">home</span>
                    <span class="ps-2">{{ __('text.home') }}</span>
                </a>
            </li>
            <?php
            $isStock = false;
            $isCheckIn = false;
            $isCheckOut = false;
            foreach($modules as $module){

                if($module->hasViewPermission || $module->hasEditPermission){
                    if($module->module == 'stock')
                        $isStock = true;
                    else if($module->module == 'purchase')
                        $isCheckIn = true;
                    else if($module->module == 'transaction')
                        $isCheckOut = true;
                }
            }

            ?>

            @if($isStock)
                <li>
                    <a href="{{ route('stock.index') }}" class="{{ Request::is('stock*') ? 'active' : '' }}">
                        <span class="material-symbols-rounded">inventory_2</span>
                        <span class="ps-2">{{ __('text.stock') }}</span>
                    </a>
                </li>
            @endif

            @if($isCheckIn || $isCheckOut)
                <li class="dropdown">
                    <a id="transactionlist" href="#" class="drop-menu rounded-bottom-0 {{ Request::is('transaction*') || Request::is('purchase*') ? 'active' : '' }}">
                        <span class="material-symbols-rounded">step_out</span>
                        <span class="ps-2">{{ __('text.transaction') }}</span>
                        <span class="material-symbols-rounded">expand_more</span>
                    </a>
                    <div class="dropdown-content" style="{{ Request::is('transaction*') || Request::is('purchase*') ? 'display:block' : 'display:none' }}">
                        @if($isCheckIn)
                        <a href="{{ route('transaction.checkinlist') }}" class="rounded-0 {{ Request::is('purchase/checkinlist') ? 'subitemactive' : '' }}">
                            <span class="material-symbols-rounded">chevron_right</span>{{ __('text.list_check_in') }}
                        </a>
                        @endif
                        @if($isCheckOut)
                        <a href="{{ route('transaction.checkoutlist') }}" class="rounded-top-0 {{ Request::is('transaction/checkoutlist') ? 'subitemactive' : '' }}">
                            <span class="material-symbols-rounded">chevron_right</span>{{ __('text.list_check_out') }}
                        </a>
                        @endif
                    </div>
                </li>
            @endif
            {{-- <p>Module Name: {{ $module->module }}</p> --}}
            @foreach($modules as $module)
                @if($module->hasViewPermission || $module->hasEditPermission)

                    @if($module->module == 'warehouse')
                        <li class="dropdown">
                            <a id="warehouselist" href="#" class="drop-menu rounded-bottom-0 {{ Request::is('warehouse*') ? 'active' : '' }}">
                                <span class="material-symbols-rounded">warehouse</span>
                                <span class="ps-2">{{ __('text.warehouse') }}</span>
                                <span class="material-symbols-rounded">expand_more</span>
                            </a>
                            <div class="dropdown-content"  style="{{ Request::is('warehouse*') ? 'display:block' : 'display:none' }}">
                                <a href="{{ route('warehouse.index') }}" class="rounded-0 {{ Request::is('warehouse*') && !Request::is('warehouse/movement/*') ? 'subitemactive' : '' }}">
                                    <span class="material-symbols-rounded">chevron_right</span>{{ __('text.warehouse') }}
                                </a>
                                <a href="{{ route('movement.index') }}" class="rounded-top-0 {{ Request::is('warehouse/movement/*') ? 'subitemactive' : '' }}">
                                    <span class="material-symbols-rounded">chevron_right</span>{{ __('text.movement') }}
                                </a>
                            </div>
                        </li>
                        {{-- <li>
                            <a href="{{ route('warehouse.index') }}" class="{{ Request::is('warehouse*') ? 'active' : '' }}">
                                <span class="material-symbols-rounded">warehouse</span>
                                <span class="ps-2">{{ __('text.warehouse') }}</span>
                            </a>
                        </li> --}}
                    @elseif($module->module == 'category')
                        <li>
                            <a href="{{ route('category.index') }}" class="{{ Request::is('category*') ? 'active' : '' }}">
                                <span class="material-symbols-rounded">category</span>
                                <span class="ps-2">{{ __('text.category') }}</span>
                            </a>
                        </li>
                    @elseif($module->module == 'shelf')
                        <li>
                            <a href="{{ route('shelf.index') }}" class="{{ Request::is('shelf*') ? 'active' : '' }}">
                                <span class="material-symbols-rounded">shelves</span>
                                <span class="ps-2">{{ __('text.shelf') }}</span>
                            </a>
                        </li>
                    @elseif($module->module == 'unit')
                        <li>
                            <a href="{{ route('unit.index') }}" class="{{ Request::is('unit*') ? 'active' : '' }}">
                                <span class="material-symbols-rounded">label</span>
                                <span class="ps-2">{{ __('text.unit') }}</span>
                            </a>
                        </li>
                    @elseif($module->module == 'size')
                        <li>
                            <a href="{{ route('size.index') }}" class="{{ Request::is('size*') ? 'active' : '' }}">
                                <span class="material-symbols-rounded">straighten</span>
                                <span class="ps-2">{{ __('text.size') }}</span>
                            </a>
                        </li>

                    {{-- @elseif($module->module == 'size') --}}
                    <li>
                        <a href="{{ route('color.index') }}" class="{{ Request::is('color*') ? 'active' : '' }}">
                            <span class="material-symbols-rounded">straighten</span>
                            <span class="ps-2">{{ __('text.color') }}</span>
                        </a>
                    </li>
                    @elseif($module->module == 'vat')
                        <li>
                            <a href="{{ route('vat.index') }}" class="{{ Request::is('vat*') ? 'active' : '' }}">
                                <span class="material-symbols-rounded">request_quote</span>
                                <span class="ps-2">{{ __('text.vat') }}</span>
                            </a>
                        </li>
                    @elseif($module->module == 'customer')
                        <li>
                            <a href="{{ route('customer.index') }}" class="{{ Request::is('customer*') ? 'active' : '' }}">
                                <span class="material-symbols-rounded">contact_page</span>
                                <span class="ps-2">{{ __('text.customers') }}</span>
                            </a>
                        </li>
                    @elseif($module->module == 'supplier')
                        <li>
                            <a href="{{ route('supplier.index') }}" class="{{ Request::is('supplier*') ? 'active' : '' }}">
                                <span class="material-symbols-rounded">contacts</span>
                                <span class="ps-2">{{ __('text.suppliers') }}</span>
                            </a>
                        </li>
                    @elseif($module->module == 'user')
                        <li>
                            <a href="{{ route('user.index') }}" class="{{ Request::is('user*') ? 'active' : '' }}">
                                <span class="material-symbols-rounded">supervisor_account</span>
                                <span class="ps-2">{{ __('text.user_role') }}</span>
                            </a>
                        </li>
                    @elseif($module->module == 'activity')
                        <li>
                            <a href="{{ route('activity.index') }}" class="{{ Request::is('activity*') ? 'active' : '' }}">
                                <span class="material-symbols-rounded">description</span>
                                <span class="ps-2">{{ __('text.activity_log') }}</span>
                            </a>
                        </li>
                    @elseif($module->module == 'reports')
                        <li>
                            <a href="{{ route('reports.index') }}" class="{{ Request::is('reports*') ? 'active' : '' }}">
                                <span class="material-symbols-rounded">monitoring</span>
                                <span class="ps-2">{{ __('text.reports') }}</span>
                            </a>
                        </li>
                    @elseif($module->module == 'settings')
                        <li>
                            <a href="{{ route('setting.index') }}" class="{{ Request::is('setting*') ? 'active' : '' }}">
                                <span class="material-symbols-rounded">settings</span>
                                <span class="ps-2">{{ __('text.settings') }}</span>
                            </a>
                        </li>
                    @endif
                @endif
            @endforeach
        </ul>




        		<div class="copyright pb-3">
        			<p class="text-primary">© {{env('APP_NAME')}} {{date('Y');}}</p>
        		</div>
        		</div>

        		</div>
        </div>
    	<!---------------------------------
        Sidebar end
        ----------------------------------->


    <main>
        <div class="body-wrapper px-2 px-md-2 bg-neutral-20 pb-5">
            @yield('content')
        </div>
    </main>

    <footer>
        <!-- Footer content -->
    </footer>

    <!-- Include additional scripts here -->
    <script>
        // Define langUrl globally based on current locale
        var langUrl = '';
        var currentLang = '{{ App::getLocale() }}'; // Get Laravel locale
        // Set the DataTable language URL based on the current language
        if (currentLang === 'pl') {
            console.log(currentLang)
            langUrl = 'https://cdn.datatables.net/plug-ins/1.10.21/i18n/Polish.json';
        } else {
            langUrl = 'https://cdn.datatables.net/plug-ins/1.10.21/i18n/English.json'; // Default to English
        }
    </script>


	<!--Global get delete data -->
	<script type="module">

		$(document).ready(function() {
            // dropdown
            $(document).on('click', '.dropdown-toggle', function() {
                var $dropdown = $(this).closest('.dropdown');
                var $dropdownMenu = $dropdown.find('.dropdown-menu');

                // Close all other open dropdowns
                $('.dropdown').not($dropdown).removeClass('show');
                $('.dropdown-menu').not($dropdownMenu).removeClass('show');

                // Toggle the "show" class for the clicked dropdown and its menu
                $dropdown.toggleClass('show');
                $dropdownMenu.toggleClass('show');

                e.stopPropagation();
            });

            $(document).on('click', function (event) {
                var $actionButton = $(".dropdown-toggle");
                var $dropdownMenu = $actionButton.closest(".dropdown").find(".dropdown-menu");

                // Check if the click happened outside the dropdown button and menu
                if (!$actionButton.is(event.target) && !$actionButton.has(event.target).length &&
                    !$dropdownMenu.is(event.target) && !$dropdownMenu.has(event.target).length) {
                    // Hide the dropdown menu if click is outside
                    $dropdownMenu.removeClass('show');
                    $actionButton.parent().removeClass('show');
                }
            });

            //set to select2 the dropdown
            $(".select2-enable").select2({
                dropdownParent: $("#AddModal")
            });

            $(".select2-enable-edit").select2({
                dropdownParent: $("#EditModal")
            });

            $('.dropdown').click(function () {
                $(this).find('.dropdown-content').slideToggle();
                $(".dropdown-content").toggleClass('active');
            });

            $('.dropdown-content a').click(function (e) {
                e.stopPropagation(); // Prevent the event from reaching the .dropdown click handler
            });

            $('#toggleSidebar').click(function () {
                $('#sidebar').toggleClass('active');
            });


            // Fetch items based on the selected category for warehouse/shelf
            function fetchItems(warehouseid) {
                $.ajax({
                    url: '/shelf/GetByWarehouse/' + warehouseid,
                    type: 'GET',
                    success: function (data) {
                        updateItemDropdown(data);
                    },
                    error: function (error) {
                        console.error('Error fetching items:', error);
                    }
                });
            }


            // Update the options of the second dropdown
            function updateItemDropdown(items) {
                $('.shelfdata').empty();
                $('.shelfdataedit').empty();
                items.forEach(item => {
                    $('.shelfdata').append(`<option value="${item.id}">${item.name}</option>`);
                });
                items.forEach(item => {
                    $('.shelfdataedit').append(`<option value="${item.id}">${item.name}</option>`);
                });
            }
            // Attach event handler to the category dropdown to fetch and update items
            $('.warehousedata').on('change', function () {
                const warehouseid = $(this).val();
                fetchItems(warehouseid);
            });



			var currentRoute = '{{ url()->current() }}';


			// Handle form submission when the button is clicked
            $('#modalSubmitButton').on('click', function () {
                // Validate the form
                if ($('#adddataform').valid()) {
                    // If the form is valid, submit it
                    $('#adddataform').submit();
                }
            });

            // Handle form submission when the button is clicked
            $('#modalEditButton').on('click', function () {
                // Validate the form
                if ($('#editdataform').valid()) {
                    // If the form is valid, submit it
                    $('#editdataform').submit();
                }
            });

            //Handle form delete
            $('#modalDeleteButton').on('click', function () {
                $("#deletedataform").submit();
            });

            $('#modalDeleteImageButton').on('click', function () {
                $("#deleteimagedataform").submit();
            });

			$('#DeleteModal').on('show.bs.modal', function(event) {
				var button = $(event.relatedTarget); // Button that triggered the modal
				var deleteid = button.data('deleteid'); // Extract data from the button
				// Set the value of the hidden input field
				$('#deleteid').val(deleteid);
			});
			$('#DeleteImageModal').on('show.bs.modal', function(event) {
				var button = $(event.relatedTarget); // Button that triggered the modal
                console.log(">>>>>>>>>>>",button);
				var deleteid = button.data('deleteid'); // Extract data from the button
				// Set the value of the hidden input field
				// $('#deleteimageid').val(deleteid);
			});

			$.validator.addMethod("uniqueemail", function(value, element) {
				var result = false;
				// Make an AJAX request to check if the value is unique
				$.ajax({
					async: false, // Ensure the function waits for the AJAX request to complete
					type: "GET",
					url: currentRoute+"/checkemail/"+ value, // Replace with your server route

					success: function(response) {
						result = !response.exists;
					}
				});
				return result;
			}, "This email already in use");

			$.validator.addMethod("uniqueemailedit", function(value, element) {
				var result = false;
				var editid = $("#editid").val();
				// Make an AJAX request to check if the value is unique
				$.ajax({
					async: false, // Ensure the function waits for the AJAX request to complete
					type: "GET",
					url: currentRoute+"/checkemail/"+ value +"/"+editid, // Replace with your server route

					success: function(response) {
						result = !response.exists;
					}
				});
				return result;
			}, "This email already in use");

            $.validator.addMethod("uniquecode", function(value, element) {
				var result = false;
				// Make an AJAX request to check if the value is unique
				$.ajax({
					async: false, // Ensure the function waits for the AJAX request to complete
					type: "GET",
					url: currentRoute+"/checkcode/"+ value, // Replace with your server route

					success: function(response) {
						result = !response.exists;
					}
				});
				return result;
			}, "This code already in use");

			$.validator.addMethod("uniquecodeedit", function(value, element) {
				var result = false;
				var editid = $("#editid").val();
				// Make an AJAX request to check if the value is unique
				$.ajax({
					async: false, // Ensure the function waits for the AJAX request to complete
					type: "GET",
					url: currentRoute+"/checkcode/"+ value +"/"+editid, // Replace with your server route

					success: function(response) {
						result = !response.exists;
					}
				});
				return result;
			}, "This code already in use");



		});
	</script>
	@stack('scripts')
</body>
</html>
