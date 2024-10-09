<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<title>{{ config('app.name', 'YAFO') }}</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
	<!-- Fonts -->
	<link href="/build/assets/css/dataTables.css" rel="stylesheet">
	<link href="/build/assets/css/exvite.css" rel="stylesheet">
	<link href="/build/assets/css/jquery-ui.css" rel="Stylesheet">
	<link href="/build/assets/awesome/css/all.min.css" rel="Stylesheet">
	<link href="/build/assets/awesome/css/all.min.css" rel="Stylesheet">
	<!-- Scripts -->
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
        .error403{
            font-size: 3rem;
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="/build/assets/js/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.7/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.min.js"></script>
    <script src="/build/assets/js/dataTables.js"></script>
    <script src="/build/assets/js/cdn.min.js" defer></script>
    <script src="/build/assets/js/sweetalert.min.js"></script>
    <script src="/build/assets/js/customv7.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>

	<script>
		$(document).ready(function() {
			//new DataTable('#example');

			/*
									targets: [0],
						orderData: [4, 'desc']

			*/
			new DataTable('#example', {
				order: [[4, 'desc']]
			});

			new DataTable('#example', {
				order: [[4, 'desc']]
			});

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