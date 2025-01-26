<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - WMS System</title>
    <!--Font-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;400;700&display=swap" rel="stylesheet">
    @vite(['resources/js/app.js'])
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
    

    
    <script type="module" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
    <!-- Include your CSS and JS assets here -->
</head>
<body>
    

    <main>
		<div class="container-fluid bg-neutral-20">
			<div class="">
				@yield('content')
			</div>
			<div class="footer text-center py-4">
				<small>© {{env('APP_NAME')}}</small>
			</div>
</div>
    </main>

    <footer>
        <!-- Footer content -->
    </footer>

    <!-- Include additional scripts here -->

	<!--Global get delete data -->
	<script type="module">
		$(document).ready(function() {


		});
	</script>
	@stack('scripts')
</body>
</html>