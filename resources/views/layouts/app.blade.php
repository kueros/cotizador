@props(['breadcrumbs' => [], 'title' => ''])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<title>{{ config('app.name', 'YAFO') }}</title>
	<link rel="shortcut icon" type="image/png" href="{{ asset('aleph_theme/img/favicon.png') }}"/>

	<!--SECCION CSS-->
	<link rel="stylesheet" type="text/css" href="{{ asset('build/assets/bootstrap/dist/css/bootstrap.min.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('build/aleph_theme/css/customv5.css?v3') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('build/assets/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('build/assets/jqueryui/themes/base/jquery-ui.min.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('build/assets/font-awesome/css/all.min.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('build/assets/font-awesome/css/fontawesome.min.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('build/assets/bootstrap-icons/font/bootstrap-icons.min.css') }}">
	<!--SECCION JS-->
	<script type="text/javascript" src="{{ asset('build/assets/jquery/jquery.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('build/assets/jqueryui/jquery-ui.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('build/assets/form/dist/jquery.form.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('build/assets/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('build/assets/datatables.net/js/dataTables.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('build/assets/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('build/assets/sweet-alert/resources/js/sweetalert.all.js') }}"></script>
	<script type="text/javascript" src="{{ asset('build/assets/html2canvas/dist/html2canvas.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('build/assets/buttons/dataTables.buttons.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('build/assets/amcharts/plugins/export/libs/jszip/jszip.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('build/assets/tableexport.jquery.plugin/libs/pdfmake/pdfmake.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('build/assets/tableexport.jquery.plugin/libs/pdfmake/vfs_fonts.js') }}"></script>
	<script type="text/javascript" src="{{ asset('build/assets/buttons/buttons.html5.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('build/assets/buttons/buttons.print.min.js') }}"></script>
</head>

<body class="font-sans antialiased">

	<style>
		.accordion-header > button{
			background-color: #e2e9ff;
			color: #337ab7;
		}

		.accordion-header > button:hover{
			color: #23527c;
			text-decoration: underline;
		}

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

	@include('layouts.partials.header')
	
	<header>
		@if(Auth::check())
			@include('layouts.partials.navigation')
			<x-breadcrumb :breadcrumbs="$breadcrumbs" :page="$title" />
		@endif
	</header>

	<div class="min-h-screen bg-gray-100">
		<main class="container">
			{{ $slot }}
		</main>
	</div>

	@include('layouts.partials.footer')

	<script type="text/javascript" src="{{ asset('build/assets/jquery/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('build/assets/jqueryui/jquery-ui.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('build/assets/datatables.net/js/dataTables.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('build/assets/datatables.net/js/dataTables.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('build/assets/buttons/dataTables.buttons.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('build/assets/buttons/buttons.html5.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('build/assets/buttons/buttons.print.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('build/assets/sweet-alert/resources/js/sweetalert.all.js') }}"></script>
    <script type="text/javascript" src="{{ asset('build/aleph_theme/js/customv7.js?v='.date('Y-m-d')) }}"></script>

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

			new DataTable('#permisos');

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
			
			// Esperar 5 segundos antes de ocultar el mensaje
			setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);

		});

		function show_password(elemento){

            var x = document.getElementById("password");
            if($(elemento).prop('checked')){
                x.type = "text";
            }else{
                x.type = "password";
            }

            var x = document.getElementById("password_confirmation");
            if($(elemento).prop('checked')){
                x.type = "text";
            }else{
                x.type = "password";
            }
		}
	</script>
</body>

</html>