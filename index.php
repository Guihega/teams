<?php
  // header("X-Frame-Options: DENY");
  // header("Referrer-Policy: no-referrer");
  // header('X-Content-Type-Options: nosniff');
  // header("X-XSS-Protection: 1");
  // header("strict-transport-security: max-age=600");
  // header("Content-Security-Policy: default-src * self blob: data: gap:; style-src * self 'unsafe-inline' blob: data: gap:; script-src * 'self' 'unsafe-eval' 'unsafe-inline' blob: data: gap:; object-src * 'self' blob: data: gap:; img-src * self 'unsafe-inline' blob: data: gap:; connect-src self * 'unsafe-inline' blob: data: gap:; frame-src * self blob: data: gap:;");

  // ini_set('session.cookie_lifetime', 1);
  // ini_set('session.use_cookies', 1);
  // ini_set('session.use_only_cookies', 1);
  // ini_set('session.use_strict_mode', 1);
  // ini_set('session.cookie_httponly', 1);
  // ini_set('session.cookie_secure', 1);
  // ini_set('session.use_trans_sid', 0);
  // ini_set('session.cache_limiter', 'private_no_expire');
  // ini_set('session.hash_function', 'sha256');

	ob_start();

  //Validamos si existe o no la sesión
	session_start();
  //Limpiamos las variables de sesión   
  session_unset();
  //Destruìmos la sesión
  session_destroy();
?> 
<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Inicio uaemex</title>
		<link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet">
		<link href="/css/inicio.css" rel="stylesheet">
	</head>
	<body>
  	<div class="container-fluid">
	  	<div class="row mt-4">
	  		<div class="col-md-4 col-sm-12 text-center">
	  			<img class="mb-3 logo" src="img/logo/uaem-logo.png" alt="">
	  		</div>
	  		<div class="col-md-4 col-sm-12 text-center">
	  			<span class="lblTitulo mb-3">SISTEMA INSTITUCIONAL PARA LA GENERACIÓN DE EQUIPOS EN MICROSOFT TEAMS</span>
	  		</div>
	  		<div class="col-md-4 col-sm-12 text-center">
	  			<span class="lblSd mb-3">SD</span>
	  		</div>
	  	</div>
	  	<div class="divider"></div>
	  	<div class="row mt-5">
	  		<div class="col-md-4 col-sm-12 ">
	  		</div>
	  		<div class="col-md-4 col-sm-12 text-center">
	  			<div class="row">
		  			<div class="col-md-10 greenRectangle"></div>
		  			<div class="col-md-2 rectangleGreen"></div>
	  			</div>
				<main class="form-signin">
						<!-- <form method="post" id="frmAcceso" action="panel.php"> -->
					<form method="post" id="frmAcceso">
					    <div class="form-field d-flex align-items-center form-floating mb-3 mt-3">
					     	<input type="email" class="form-control txtUserName text-center" id="email" name="email" placeholder="Correo institucional" required autocomplete="off">
					     	<label class="lblUserName" for="email">Correo institucional</label>
			        	</div>
					    <div class="form-field d-flex align-items-center form-floating mb-3">
					    	<input type="password" class="form-control txtPassword text-center" id="password" name="password" placeholder="Contraseña" required autocomplete="off">
					    	<label class="lblPassword" for="password">Contraseña</label>
					    </div>
				    	<button class="w-100 btn btn-lg btn-success btnEntrar" type="submit">Entrar</button>
				    	<div class="divider mt-5"></div>
				    	<div class="mt-5"><label class="avisoPrivacidad"><span>Para ingresar use su correo y contraseña institucional. </span></label></div>
		  			</form>
				</main>
	  		</div>
	  		<div class="col-md-4 col-sm-12 ">
	  		</div>
	  	</div>
  	</div>
  	<div class="modal fade" id="spinner" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
			    <div class="modal-content">
			    	<div class="">
			  			<div class="spinner-grow text-success" role="status">
						  <span class="visually-hidden">Loading...</span>
						</div>
						<div class="spinner-grow text-success" role="status">
						  <span class="visually-hidden">Loading...</span>
						</div>
						<div class="spinner-grow text-success" role="status">
						  <span class="visually-hidden">Loading...</span>
						</div>
						<div class="spinner-grow text-success" role="status">
						  <span class="visually-hidden">Loading...</span>
						</div>
						<div class="spinner-grow text-success" role="status">
						  <span class="visually-hidden">Loading...</span>
						</div>
					</div>
			    </div>
		    </div>
		</div>
    <!-- <script src="js/jquery-3.1.1.min.js"></script> -->
    <script src="js/jquery-3.6.1.min.js"></script>
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script> 
  	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script type="text/javascript" src="js/login.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@microsoft/microsoft-graph-client/lib/graph-js-sdk.js"></script>
	</body>
</html>