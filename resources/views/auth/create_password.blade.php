<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
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
        body, html {
            height: 100%;
            background-repeat: no-repeat;
            background: url(/images/login-background.jpg) center top no-repeat;
            background-repeat: no-repeat;
            background-size: cover;
        }

        .logo-home{
            width: 270px;
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
            color: #005fff;
        }

        .forgot-password:hover,
        .forgot-password:active,
        .forgot-password:focus{
            color: #0055CC !important;
        }
        #show_password{
             width: 100%;
             padding: 0;
             text-align: left!important;
             float: right;
             color: black;
             font-size: 16px;
             margin-bottom: 5px;
         }
    </style>
    <script>
        function show_password(elemento){
            var x = document.getElementById("password");
            var z = document.getElementById("password_confirmation");

            if($(elemento).prop('checked')){
                x.type = "text";
                z.type = "text";
            }else{
                x.type = "password";
                z.type = "password";
            }
        }
    </script>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="card card-default login-box" style="padding-top:5px;">
            
            @if(isset($token))
                <div class="card-heading" >
					<img class="logo-home" src="{{ asset('build/aleph_theme/img/logo.png') }}" alt="" title="">
                </div>
                <div class="card-body">
					<div style="margin-top:1rem;">
						@if ($errors->any())
							<div class="alert alert-danger text-red-500">
								<ul>
									@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
									@endforeach
								</ul>
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
                    <form action="{{ route('crear_password') }}" method="post" name="change-password">
						@csrf
                        <div class="form-group">
                            <label for="password">Ingrese una contraseña que tenga como mínimo 8 caracteres de longitud (debe contener: 1 caracter especial, 1 número, 1 letra en mayúsculas, 1 letra en minúsculas)</label>
                            <input id="password" type="password" required minlength="8" maxlength="20" 
								pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$%^&+=*.,;_-])(?=.*[a-zA-Z0-9]).{8,}$" 
								name="password" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Repita la contraseña</label>
                            <input id="password_confirmation" type="password" required minlength="8" maxlength="20" 
								pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$%^&+=*.,;_-])(?=.*[a-zA-Z0-9]).{8,}$" 
								name="password_confirmation" class="form-control">
                        </div>

                        <span id="show_password"><input type="checkbox" value="1" onchange="show_password(this)" /> Mostrar contraseña</span>
                        <input type="hidden" value="{{ $token }}" name="token" />
						<input type="hidden" value="{{ $email }}" name="email" />
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </form>
                </div>
            @else
				@if (session('error')) 
					<div class="alert alert-danger">
						{{ session('error') }}
					</div>
				@endif
			@endif
        </div>
    </div>
</div>
</body>