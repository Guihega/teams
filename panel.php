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

	if (strlen(session_id()) < 1){
		session_start();//Validamos si existe o no la sesión
	}

	include 'ajax/acciones.php';
	if(isset($_GET['code'])) {
		//$codigo = $_GET['code'];
	    $codigo = limpiarCadena($_GET['code']);
	    consultarDatosTabla($codigo);
	}
	else{
		$codigo = "";
		$_SESSION['codigo']="";
		header("Location: https://sigeteams.uaemex.mx/");
	}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel uaemex</title>
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
	  	<div class="row mt-2">
	  		<div class="col-md-12 derecha">
		  		<span class="-Input-text"><?php echo $_SESSION['nombre']?></span>
	  		</div>
	  	</div>
	  	<div class="row mt-2">
	  		<div class="col-md-12 izquierda">
	  			<span class="intro">Seleccione los grupos cuyo equipo desee crear en Microsoft TEAMS.</span>
	  		</div>
	  	</div>
	  	<div class="row">
	  		<form id="frmCursos" method="POST">
		  		<div class="col-md-12 col-sm-12 ">
		  			<table class="table table-hover table-striped" id="tblCursos">
						<thead class="headRow">
							<tr>
								<th class="tblHead"></th>
								<th class="tblHead" >Organismo acádemico</th>
								<th class="tblHead" >Programa académico</th>
								<th class="tblHead" >Clave</th>
								<th class="tblHead" >Unidad de aprendizaje</th>
								<th class="tblHead" >Grupo</th>
								<th class="tblHead" ># de alumnos en control escolar</th>
								<th class="tblHead" >Nombre Equipo TEAMS</th>
								<th class="tblHead" ># de alumnos equipo TEAMS</th>
								<th class="tblHead" >Actualizar</th>
							</tr>
						</thead>
				  	<tbody id="tblBodyGrupos">
						<?php
							if (!empty($_SESSION["listaCursos"])) {
								$result = $_SESSION["listaCursos"];
								for ($i=0; $i < count($result['CVEPRF']); $i++) { ?> 
									<tr id="<?php echo "trRow" . $result['IDPROFCURSO'][$i]?>">
										<td class="tdRow">
											<div class="custom-control custom-checkbox">
												<!-- <input type="checkbox" class="custom-control-input cursos" id="<?php echo $result['IDPROFCURSO'][$i]?>" value="<?php echo $result['IDPROFCURSO'][$i]?>" name="cursos[]" disabled > -->
												<?php
										    	if (!empty($result['NUMALUMOSTEAM'][$i]) || isset($result['NUMALUMOSTEAM'][$i]))
										    	{
												    echo '<input type="checkbox" class="custom-control-input cursos bloqueado" id="'.$result['IDPROFCURSO'][$i].'" value="'.$result['IDPROFCURSO'][$i].'" name="cursos[]">';
												  }
												  else{
												  	echo '<input type="checkbox" class="custom-control-input cursos" id="'.$result['IDPROFCURSO'][$i].'" value="'.$result['IDPROFCURSO'][$i].'" name="cursos[]">';
												  }
										    ?>
											</div>
										</td>
								    <td class="tdRow organismo"><?php echo $result['NOMESP'][$i] ?></td>
								    <td class="tdRow programa"><?php echo $result['CVEPLN'][$i] ?></td>
								    <td class="tdRow clave"><?php echo $result['CVEPER'][$i] ?></td>
								    <td class="tdRow unidad"><?php echo $result['NOMMAT'][$i] ?></td>
								    <td class="tdRow grupo"><?php echo $result['CVEGPO'][$i] ?></td>
								    <td class="tdRow numItemsCtrEsc">
								    	<?php 
										    if (!empty($result['NUMALUMOSCTRESC'][$i]) || isset($result['NUMALUMOSCTRESC'][$i])) {
										    	echo $result['NUMALUMOSCTRESC'][$i];
										    }
									    ?>
								    </td>
								    <td class="tdRow nomGpoTeam">
								    	<?php 
										    if (!empty($result['NOMGPOTEAM'][$i]) || isset($result['NOMGPOTEAM'][$i])) {
										    	echo $result['NOMGPOTEAM'][$i];
										    }
									    ?>
								    </td>
								    <td class="tdRow numItemsTeam">
								    	<?php 
										    if (!empty($result['NUMALUMOSTEAM'][$i]) || isset($result['NUMALUMOSTEAM'][$i])) {
										    	echo $result['NUMALUMOSTEAM'][$i];
										    }
										    else{
										    	echo "0";
										    }
									    ?>
								    </td>
								    <td class="tdRow">
									    <?php
									    	if (!empty($result['NUMALUMOSTEAM'][$i]) || isset($result['NUMALUMOSTEAM'][$i]))
									    	{
											    echo '<a href="#" class="btn btn-sm btn-success" onclick="actualizarGpo('.$result['IDPROFCURSO'][$i].')">Actualizar</a>';
											  }
									    ?>
								  	</td>
									</tr>
									<?php
								}
							}
						?>
				  	</tbody>
					</table>
		  		</div>
	  		</form>
	  	</div>
	  	<div class="row mb-5">
	  		<form method="POST" class="fmrBtn">
		  		<div class="col-md-6 derecha">
		  			<!-- <a href="https://sigeteams.uaemex.mx/" class="btn btn-sm btn-success btnSalir">Salir</a> -->
		  			<button class="btn btn-sm btn-success btnSalir">Salir</button>
		  		</div>
		  		<!-- <div class="col-md-4 derecha">
		  			<button class="btn btn-sm btn-success btnAcceso">Autorizar</button>
		  		</div> -->
		  		<div class="col-md-6 derecha">
		  			<button class="btn btn-sm btn-success btnCrearGrupo">CREAR EQUIPO(S) TEAMS CON ALTA DE ALUMNOS</button>
		  		</div>
		  	</form>
		</div>
  	</div>
	<!-- Modal -->
	<div class="modal fade" id="modalConfirm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	  <div class="modal-dialog modal-dialog-centered" role="document">
	    <div class="modal-content">
	      <div class="modal-body">
	        <h6 class="modal-title" id="lblMensajeModal">Los grupos han sido creados y los usuarios asignados</h6></div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-success" data-bs-dismiss="modal">Aceptar</button>
	      </div>
	    </div>
	  </div>
	</div>

	<div id="cargando" style="display:none;">
    <div class="spinner"></div>
    <br/>
    <sapan id="mensajeLoader">Creando grupos...</sapan>
	</div>

	<!-- jQuery -->
<!--   <script src="js/jquery-3.1.1.min.js"></script> -->
  <script src="js/jquery-3.6.1.min.js"></script>
  <!-- Bootstrap 5 -->
  <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
	<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
  <script type="text/javascript" src="js/panel.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@microsoft/microsoft-graph-client/lib/graph-js-sdk.js"></script>
	<!-- polyfilling promise -->
	<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/es6-promise/dist/es6-promise.auto.min.js"></script>
	<!-- polyfilling fetch -->
	<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/whatwg-fetch/dist/fetch.umd.min.js"></script>
	<!-- depending on your browser you might wanna include babel polyfill -->
	<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@babel/polyfill@7.4.4/dist/polyfill.min.js"></script>
	<script type="text/javascript">
		var codigo = "<?php echo $codigo ?>";
		if (codigo.length > 1) {
			$('.btnCrearGrupo').prop('disabled', false);
			$(".cursos").each(function(){
	      $(this).prop('disabled', false);
	  	});
		}
		else{
			$('.btnCrearGrupo').prop('disabled', true);
			$(".cursos").each(function(){
	      $(this).prop('disabled', true);
	  	});
		}
	</script>
	</body>
</html>