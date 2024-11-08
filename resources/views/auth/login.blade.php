<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<title>Aleph Manager</title>
    <meta name="robots" content="noindex">
    <meta name="googlebot" content="noindex">
    <link rel="shortcut icon" type="image/png" href="{{ asset('build/aleph_theme/img/favicon.png') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('build/assets/bootstrap/dist/css/bootstrap.min.css') }}">

    <script type="text/javascript" src="{{ asset('build/assets/jquery/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('build/assets/jqueryui/jquery-ui.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('build/assets/form/dist/jquery.form.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('build/assets/bootstrap/dist/js/bootstrap.min.js') }}"></script>

    <style>
        <?php 
        #$copa_background_login_custom = 1;
        if($copa_background_login_custom == '0'){
        ?>
            body, html {
            height: 100%;
            background-repeat: no-repeat;
            background: url('/uploads/images/<?= $background_login_custom_path ?>') center top no-repeat;
            background-repeat: no-repeat;
            background-size: cover;
            }
        <?php 
        }else{
            ?>
            body, html {
                height: 100%;
                background-repeat: no-repeat;
                background: url(/images/login-background.jpg) center top no-repeat;
                background-repeat: no-repeat;
                background-size: cover;
            }
        <?php
        }
        ?>

        #logo-home{
            width: 270px;
            height: 80px;
            background-size: cover;
        }

        @media screen
        and (min-device-width: 1200px)
        and (-webkit-min-device-pixel-ratio: 1) {
            .login-box{
                width: 500px;
                height: auto;
                top: 50%;
                left: 50%;
                position: absolute;
                text-align: center;
                margin-top: -175px;
                margin-left: -250px;
            }
        }

        @media only screen
        and (min-device-width: 320px)
        and (max-device-width: 480px)
        and (-webkit-min-device-pixel-ratio: 2)
        and (orientation: portrait) {
            .login-box{
                width: 350px;
                height: auto;
                top: 50%;
                left: 50%;
                position: absolute;
                text-align: center;
                margin-top: -175px;
                margin-left: -175px;
            }
        }

        /*.card-container.card {
            max-width: 350px;
            padding: 40px 40px;
        }*/

        .btn {
            font-weight: 700;
            height: 36px;
            -moz-user-select: none;
            -webkit-user-select: none;
            user-select: none;
            cursor: default;
        }

        /*
         * Card component
         */
        /*.card {
            background-color: #F7F7F7;
            
            padding: 20px 25px 30px;
            margin: 0 auto 25px;
            margin-top: 50px;
            
            -moz-border-radius: 2px;
            -webkit-border-radius: 2px;
            border-radius: 2px;
            -moz-box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
            -webkit-box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
            box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
        }*/

        .profile-img-card {
            width: 96px;
            height: 96px;
            margin: 0 auto 10px;
            display: block;
            -moz-border-radius: 50%;
            -webkit-border-radius: 50%;
            border-radius: 50%;
        }

        /*
         * Form styles
         */
        .profile-name-card {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            margin: 10px 0 0;
            min-height: 1em;
        }

        .reauth-email {
            display: block;
            color: #404040;
            line-height: 2;
            margin-bottom: 10px;
            font-size: 14px;
            text-align: center;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            -moz-box-sizing: border-box;
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
        }

        .form-signin #inputEmail,
        .form-signin #inputPassword {
            direction: ltr;
            height: 44px;
            font-size: 16px;
        }

        .form-signin input[type=email],
        .form-signin input[type=password],
        .form-signin input[type=text],
        .form-signin button {
            width: 100%;
            display: block;
            margin-bottom: 10px;
            z-index: 1;
            position: relative;
            -moz-box-sizing: border-box;
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
        }

        .form-signin .form-control:focus {
            border-color: rgb(104, 145, 162);
            outline: 0;
            -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075),0 0 8px rgb(104, 145, 162);
            box-shadow: inset 0 1px 1px rgba(0,0,0,.075),0 0 8px rgb(104, 145, 162);
        }

        .btn.btn-signin {
            /*background-color: #4d90fe; */
            background-color: #0055CC;
            /* background-color: linear-gradient(rgb(104, 145, 162), rgb(12, 97, 33));*/
            padding: 0px;
            font-weight: 700;
            font-size: 14px;
            height: 36px;
            -moz-border-radius: 3px;
            -webkit-border-radius: 3px;
            border-radius: 3px;
            border: none;
            -o-transition: all 0.218s;
            -moz-transition: all 0.218s;
            -webkit-transition: all 0.218s;
            transition: all 0.218s;
        }

        .btn.btn-signin:hover,
        .btn.btn-signin:active,
        .btn.btn-signin:focus {
            background-color: #005fff;
        }

        .forgot-password {
            color: #FFFFFF;
        }

        .login-google{
            background-color: #ff4444;
            padding: 4px;
            border-radius: 8px;
            color: white;
            text-decoration: none;
            font-size: initial;
        }
        
        .login-google:hover{
            background-color: #f16565;
        }

        .login-azure{
            background-color: #0072C6;
            /*padding: 9px;*/
            border-radius: 8px;
            color: white;
            text-decoration: none;
            font-size: initial;
        }

        .login-azure:hover{
            background-color: #116fb5;
        }

        .login-onelogin{
            background-color: #6f7175;
            padding: 4px;
            border-radius: 8px;
            color: white;
            text-decoration: none;
            font-size: initial;
        }

        .login-onelogin:hover{
            background-color: #8b8f95;
        }

        .login-google img{
            width: 25px;
        }

        .login-azure img{
            width: 30px;
        }

        .login-onelogin img{
            width: 30px;
        }

        .login-buttons-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 20px;
        }

        .login-buttons-container .login-buttons {
            width: 100%;
            max-width: 75%;
            margin-bottom: 10px;
        }

        .login-buttons:hover{
            color: #FFFFFF;
            text-decoration: none;
        }

        .login-box{
            background: #082247d1;
            background-image: initial;
            background-position-x: initial;
            background-position-y: initial;
            background-size: initial;
            background-repeat-x: initial;
            background-repeat-y: initial;
            background-attachment: initial;
            background-origin: initial;
            background-clip: initial;
            background-color: rgba(8, 34, 71, 0.82);
            border:none;
        }

        .card-heading{
            background: none!important;
            border:none;
            padding-top: 30px;
        }
        
        #show_password{
            width: 100%;
            padding: 0;
            text-align: left!important;
            float: right;
            color: white;
            font-size: 16px;
            margin-bottom: 5px;
        }
    </style>
    <script>
        function show_password(elemento){

            var x = document.getElementById("password");

            if($(elemento).prop('checked')){
                x.type = "text";
            }else{
                x.type = "password";
            }
        }
    </script>
</head>

<body>


		@if (session('error'))
		<div class="alert alert-danger">
			{{ session('error') }}
		</div>
		@endif


	<div class="container">
		<div class="row">
			<div class="card card-default login-box">
				<div class="card-heading">
					<img id="logo-home" src="images/aleph_logo.gif" alt="" title="">
				</div>
				<div class="card-body">
    				<div style="margin-top:1rem;">
                        @if ($errors->any())
                            <div class="alert alert-danger text-red-500">
                                    @foreach ($errors->all() as $error)
                                    {{ $error }}
                                    @endforeach
                            </div>
                        @endif
                        @if (session('error')) 
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif
                        @if (session('success'))
                            <div class="alert alert-success" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif
                    </div>
                    <form action="{{ 'login' }}" method="post" class="form-signin">
                        @csrf
                        <span id="reauth-email" class="reauth-email"></span>
                        <input type="text" id="email" name="email" class="form-control" placeholder="Usuario o email" required autofocus>
                        <input id="password" type="password" autocomplete="off" name="password" class="form-control" placeholder="Contraseña" required>
                        <span id="show_password"><input type="checkbox" value="1" onchange="show_password(this)" /> Mostrar contraseña</span>
                        <button class="btn btn-lg btn-primary btn-block btn-signin" type="submit">Iniciar sesi&oacute;n</button>
                    </form>
                    <a href="{{ route('password.request') }}" class="login-buttons forgot-password">
                        ¿Olvidó la contraseña?
                    </a>
						
					<div class="login-buttons-container">
					</div>

				</div>
			</div>
		</div>
	</div>

</body>
</html>