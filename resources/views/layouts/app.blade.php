<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<title>{{ config('app.name', 'YAFO') }}</title>


	<!-- Fonts -->
	<link rel="preconnect" href="https://fonts.bunny.net">
	<link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
	<link href="https://cdn.datatables.net/2.1.5/css/dataTables.dataTables.css" rel="stylesheet">
	<link href="/build/assets/awesome/css/solid.css" rel="stylesheet">
	<link href="http://code.jquery.com/ui/1.10.2/themes/smoothness/jquery-ui.css" rel="Stylesheet">
	<!-- Scripts -->
	@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">

	<style>
		.colapsable-aleph {
			width: 100%;
			height: 35px;
			padding-top: 8px;
			padding-left: 20px;
			font-weight: 900;
			user-select: none;
			background-color: #e2e9ff;
			margin-top: 10px;
			margin-bottom: 10px;
		}
	</style>

	<div class="min-h-screen bg-gray-100">
		@include('layouts.navigation')

		<!-- Page Heading -->
		@isset($header)
		<header class="bg-white shadow">
			<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
				{{ $header }}
			</div>
		</header>
		@endisset

		<!-- Page Content -->
		<main>
			{{ $slot }}
		</main>
	</div>
	<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
	<script src="https://cdn.datatables.net/2.1.5/js/dataTables.js"></script>
	<script src="http://code.jquery.com/ui/1.10.2/jquery-ui.js"></script>
	<script>
		$(document).ready(function() {
			new DataTable('#example');

			$('.dropdown-submenu a.test').on("click", function(e) {
				$(this).next('ul').toggle();
				e.stopPropagation();
				e.preventDefault();
			});

			$(document).on("click", ".colapsable-aleph", function() {
				arrow_span = $(this).find('span');

				if ($(arrow_span).hasClass('glyphicon-chevron-right')) {
					$(arrow_span).removeClass('glyphicon-chevron-right');
					$(arrow_span).addClass('glyphicon-chevron-down');
				} else {
					$(arrow_span).removeClass('glyphicon-chevron-down');
					$(arrow_span).addClass('glyphicon-chevron-right');
				}
			});

			$(".usuarios_autocomplete").autocomplete({
				source: function(request, response) {
					$.ajax({
						url: baseUrl + "/usuarios/autocomplete",
						type: 'post',
						dataType: "json",
						data: {
							search: request.term
						},
						success: function(data) {
							response(data);
						}
					});
				},
				select: function(event, ui) {
					$(this).val(ui.item.label); // display the selected text
					return false;
				}
			});


		});
	</script>
</body>

</html>