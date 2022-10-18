<?php
	//header("X-Frame-Options: DENY");
	
	// **PREVENTING SESSION HIJACKING**
	// Prevents javascript XSS attacks aimed to steal the session ID
	//ini_set('session.cookie_httponly', 1);

	// **PREVENTING SESSION FIXATION**
	// Session ID cannot be passed through URLs
	//ini_set('session.use_only_cookies', 1);

	// Uses a secure connection (HTTPS) if possible
	//ini_set('session.cookie_secure', 1);

	ob_start();
	if (strlen(session_id()) < 1){
		session_start();//Validamos si existe o no la sesión
	}

	switch ($_GET["op"]){
		case 'datosEmpleado':
			//$codigo = limpiarCadena($_GET['code']);
			// $nombreProfesor = $_POST["empleado"]["nombre"] . " ". $_POST["empleado"]["paterno"] . " ". $_POST["empleado"]["materno"];
			// $rfcProfesor = $_POST["empleado"]["rfc"];
			// $curpProfesor = $_POST["empleado"]["curp"];
			// $correoProfesor = $_POST["empleado"]["correo"];
			// $fotoProfesor = $_POST["empleado"]["foto"];
			$nombreProfesor = limpiarCadena($_POST["empleado"]["nombre"]). " ". limpiarCadena($_POST["empleado"]["paterno"]) . " ". limpiarCadena($_POST["empleado"]["materno"]);
			$rfcProfesor = limpiarCadena($_POST["empleado"]["rfc"]);
			$curpProfesor = limpiarCadena($_POST["empleado"]["curp"]);
			$correoProfesor = limpiarCadena($_POST["empleado"]["correo"]);
			$fotoProfesor = limpiarCadena($_POST["empleado"]["foto"]);

			//Declaramos las variables de sesión
		    $_SESSION['rfc']=$rfcProfesor;
		    $_SESSION['nombre']=$nombreProfesor;
		    $_SESSION['imagen']=$fotoProfesor;
		    $_SESSION['email']=$correoProfesor;
		break;

		case 'panel':
			// error_reporting(E_ALL);
			// ini_set("display_errors", 1);
			// $nombreProfesor = $_POST["empleado"]["nombre"] . " ". $_POST["empleado"]["paterno"] . " ". $_POST["empleado"]["materno"];
			// $rfcProfesor = $_POST["empleado"]["rfc"];
			// $curpProfesor = $_POST["empleado"]["curp"];
			// $correoProfesor = $_POST["empleado"]["correo"];
			// $fotoProfesor = $_POST["empleado"]["foto"];

			// //Declaramos las variables de sesión
			//$_SESSION['rfc']=$rfcProfesor;
			//$_SESSION['nombre']=$nombreProfesor;
			//$_SESSION['imagen']=$fotoProfesor;
			//$_SESSION['email']=$correoProfesor;


			$rfcProfesor = $_SESSION['rfc'];
			$correoProfesor = $_SESSION['email'];

			$txt_file = '../periodo.txt';

			$lines = file($txt_file);
			$periodos;
			$rfc;
			foreach ($lines as $num=>$line)
			{
				$periodos=$line;
			}

			## PRUEBA CNX SICDE ##
			// include($_SERVER['DOCUMENT_ROOT']."/inc/cnx_ctrlesc.php");
			include("../inc/cnx_ctrlesc.php");
			$cnxce = new Cnx();

			$qryCtrl = "SELECT DISTINCT CVEPRF FROM CAMILA.CURSO_PROFESOR_DIS WHERE CVEPER in ($periodos) AND CORREOUAEM='$correoProfesor' OR CORREOPROFESOR='$correoProfesor'";
			$mIce = $cnxce->consultaM($qryCtrl);
			$mCom = oci_fetch_all($mIce, $resp);
			$rfc = $resp['CVEPRF'][0];

			$qryCtrlGpos = "SELECT DISTINCT IDPROFCURSO, CVEPRF,CVEPER,NOMESP,CVEPLN,CVEMAT,NOMMAT,CVEGPO FROM CAMILA.ALUMNO_CURSO_DIS WHERE CVEPRF in('$rfc','$rfcProfesor') AND CVEPER in ($periodos)";

			$mIceGps = $cnxce->consultaM($qryCtrlGpos);
			$mComGps= oci_fetch_all($mIceGps, $respta);
			$_SESSION["listaCursos"] = $respta;
			echo $respta;
		break;

		case 'crearGpos':
			$return = $_POST["strGpos"];
			$splitGpos  = explode(",", $return);
			$objAlumnos = [];

			## PRUEBA CNX SICDE ##
			include($_SERVER['DOCUMENT_ROOT']."/inc/cnx_ctrlesc.php");
			$cnxce = new Cnx();

			foreach ($splitGpos as &$value) {
			    $obj = new stdClass();
				$qryAlmnos = "SELECT DISTINCT CVEALU, NOMALU, APELLIDOPATERNO, APELLIDOMATERNO, EMAILUAEM, CVEPLN, CVEGPO FROM camila.ALUMNO_CURSO_DIS WHERE IDPROFCURSO IN ('$value')";
				$mIceAlmnos = $cnxce->consultaM($qryAlmnos);
				$mComAlmnos = oci_fetch_all($mIceAlmnos, $resAlumnos);
				$obj->curso = $value;
				$obj->alumnos= $resAlumnos;
				array_push($objAlumnos, $obj);
			}
			echo json_encode($objAlumnos);
		break;

		case 'login':
			//$auth = $_SESSION['token'];
			//limpiarCadena(
			// $email = $_POST["email"];
			// $password = $_POST["password"];
			// $base64 = $_POST["base64"];
			$email = limpiarCadena($_POST["email"]);
			$password = limpiarCadena($_POST["password"]);
			$base64 = limpiarCadena($_POST["base64"]);

			$curl = curl_init('http://aplicaciones.uaemex.mx/ldap/api/v1/autenticar');/** Ingresamos la url de la api o servicio a consumir */
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, true );
			curl_setopt($curl, CURLOPT_POST, true);/** Autorizamos enviar datos*/
			$my_user = array(
			"email"=> $email,
			"password"=> $password,
			"base64"=> $base64
			);
			$payload = json_encode($my_user);/** convertimos los datos en el formato solicitado normalmente json*/
			curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
			curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
			$result = curl_exec($curl);/** Ejecutamos petición*/
			curl_close($curl);
			echo $result;
		break;

		case 'obtenerToken':
			//ini_set('display_startup_errors', 1);
			//ini_set('display_errors', 1);
			//error_reporting(-1);
			if(!isset($_SESSION['token']) && empty($_SESSION['token'])) {
				$codigo = $_SESSION["codigo"];
				$ch = curl_init();
			    $url = "https://login.microsoftonline.com/common/oauth2/v2.0/token/";
			    $data = 'client_id=f9b65de4-8994-4de4-bd4f-7dc64e654bbf
				&scope=user.read%20mail.read
				&code='.$codigo.'
				&redirect_uri=https://sigeteams.uaemex.mx/panel.php
				&grant_type=authorization_code
				&client_secret=pem8Q~rb6.FU3EIh9uUwZ-sgAEOAL4TiMw1rkbQX';
			    $getUrl = $url;
			    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
			    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			    curl_setopt($ch, CURLOPT_URL, $getUrl);
			    curl_setopt($ch, CURLOPT_POST, 1);
			    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			    curl_setopt($ch, CURLOPT_TIMEOUT, 80);
			       
			    $response = curl_exec($ch);
			        
			    if(curl_error($ch)){
			        echo 'Error: ' . curl_error($ch);
			    }else{
			    	$respuesta = json_decode($response, true);
			        $token = $respuesta["access_token"];
			        $_SESSION['token'] = "Authorization: Bearer ". $token;
			    }
			    curl_close($ch);
			    //echo $token;
			}
		break;

		case 'crearGrupos':
			if(isset($_SESSION['token']) && !empty($_SESSION['token'])) {
				$iniciales = substr($_SESSION['rfc'], 0, 4);
				$grupos = limpiarCadena($_POST["objCursosTbl"]);
				$auth = $_SESSION['token'];
				$objGrupos = [];
				foreach ($grupos as &$value) {
					$curl = curl_init();

					curl_setopt_array($curl, array(
					  CURLOPT_URL => 'https://graph.microsoft.com/v1.0/teams',
					  CURLOPT_RETURNTRANSFER => true,
					  CURLOPT_ENCODING => '',
					  CURLOPT_MAXREDIRS => 10,
					  CURLOPT_TIMEOUT => 0,
					  CURLOPT_FOLLOWLOCATION => true,
					  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					  CURLOPT_CUSTOMREQUEST => 'POST',
					  CURLOPT_POSTFIELDS => '{
					    "template@odata.bind": "https://graph.microsoft.com/v1.0/teamsTemplates(\'standard\')",
						"displayName": "'.$value["clavePer"].'-'.$value["nomMateria"].'-'.$value["claveGpo"].'-'.$iniciales.'",
						"description": "'.$value["numGpo"].'"
						}',
					  CURLOPT_HTTPHEADER => array(
					    'Content-Type: application/json',
					    $auth
					  ),
					));

					$response = curl_exec($curl);

					curl_close($curl);
					echo $response;
				}
			}
		break;

		case 'listarGpos':
			$auth = $_SESSION['token'];
			$curl = curl_init();

			curl_setopt_array($curl, array(
			  CURLOPT_URL => 'https://graph.microsoft.com/v1.0/me/joinedTeams',
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'GET',
			  CURLOPT_HTTPHEADER => array(
			  	$auth
			  ),
			));

			$response = curl_exec($curl);

			curl_close($curl);
			echo $response;
		break;

		case 'agregarAlumnos':
			$auth = $_SESSION['token'];
			$idGpo = limpiarCadena($_POST["id"]);
			$arrAlumnosTeams =limpiarCadena($_POST["alumnos"]);
			$objAlumnosTeam = [];

			$url = 'https://graph.microsoft.com/v1.0/teams/'.$idGpo.'/members/add';

			foreach ($arrAlumnosTeams as &$value) {
				$usuario = json_encode(array("@odata.type"=>"microsoft.graph.aadUserConversationMember","roles"=>[],"user@odata.bind"=>"https://graph.microsoft.com/v1.0/users('".$value["correo"]."')"),true);
				//$arrayCorreos = json_encode($objAlumnosTeam);
				$curl = curl_init();
	      curl_setopt_array($curl, array(
					CURLOPT_URL => $url,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'POST',
					CURLOPT_POSTFIELDS =>'{
					"values": ['.
					        $usuario.'
					    ]
					}',
					CURLOPT_HTTPHEADER => array(
					'Content-Type: application/json',
					$auth
					),
	            ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
			}
		break;

		case 'acceso':
			header("Location: https://login.microsoftonline.com/common/oauth2/v2.0/authorize?client_id=f9b65de4-8994-4de4-bd4f-7dc64e654bbf&response_type=code&redirect_uri=https://sigeteams.uaemex.mx/panel.php&response_mode=query&scope=offline_access%20user.read%20mail.read&state=12345");
		break;

		case 'mostrarDatos':
			if (!empty($_SESSION["listaCursos"])) {
				$listaCursos = json_encode($_SESSION["listaCursos"], true);
				echo $listaCursos ;
			}
		break;

		case 'detallesGpo':
			$auth = $_SESSION['token'];
			$idGpoTeam = limpiarCadena($_POST["idGrupoTeam"]);
			$numGpoTeam = limpiarCadena($_POST["numGpoTeam"]);
			$curl = curl_init();

			curl_setopt_array($curl, array(
			  CURLOPT_URL => 'https://graph.microsoft.com/v1.0/groups/'.$idGpoTeam.'/members',
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'GET',
			  CURLOPT_HTTPHEADER => array(
			   $auth
			  ),
			));

			$response = curl_exec($curl);

			curl_close($curl);
			//echo $response;
			$arrayGposTeams = $_SESSION["listaCursos"];
			for ($i=0; $i < count($arrayGposTeams); $i++) {
				if ($arrayGposTeams['IDPROFCURSO'][$i] == $numGpoTeam) {
					$arrayDetCurso = json_decode($response, true);
					$numAlumnosTeams = count($arrayDetCurso['value']);
					echo $numAlumnosTeams;
					//echo count($arrayDetCurso[0]['value']);
					//var_dump($response);
					//echo count((array)$response['value']);
					$arrayGposTeams['NUMALUMOSTEAM'][$i] = $numAlumnosTeams;
				}
			}
			$_SESSION["listaCursosTeam"] = $arrayGposTeams;
			//$_SESSION["listaCursos"] = $arrayGposTeams;
		break;

		case 'actualizarGpo':
			$auth = $_SESSION['token'];
			$numGpoTeam = limpiarCadena($_POST["numGpo"]);
			include("../inc/cnx_ctrlesc.php");
			$cnxce = new Cnx();
			$curl = curl_init();

			curl_setopt_array($curl, array(
			  CURLOPT_URL => 'https://graph.microsoft.com/v1.0/me/joinedTeams',
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'GET',
			  CURLOPT_HTTPHEADER => array(
			  	$auth
			  ),
			));

			$gruposTeam = curl_exec($curl);
			curl_close($curl);
			$listGruposTeam = json_decode($gruposTeam, true);
			for ($i=0; $i < count($listGruposTeam['value']); $i++) {
				//echo $listGruposTeam['value'][$i];
				if ($listGruposTeam['value'][$i]['description'] == $numGpoTeam) {
					//echo $listGruposTeam['value'][$i]['id'];
					$idGpoTeam = $listGruposTeam['value'][$i]['id'];
					$curl = curl_init();

					curl_setopt_array($curl, array(
					  CURLOPT_URL => 'https://graph.microsoft.com/v1.0/groups/'. $idGpoTeam .'/members',
					  CURLOPT_RETURNTRANSFER => true,
					  CURLOPT_ENCODING => '',
					  CURLOPT_MAXREDIRS => 10,
					  CURLOPT_TIMEOUT => 0,
					  CURLOPT_FOLLOWLOCATION => true,
					  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					  CURLOPT_CUSTOMREQUEST => 'GET',
					  CURLOPT_HTTPHEADER => array(
					   $auth
					  ),
					));

					$detalleCurso= curl_exec($curl);
					curl_close($curl);

					$listaDetCurso = json_decode($detalleCurso, true);
					//var_dump($listaDetCurso);
					$numItemsGpoTeam = count($listaDetCurso['value']) - 1;

					$listaGposCtrEsc = $_SESSION["listaCursos"];
					for ($j=0; $j < count($listaGposCtrEsc['CVEPRF']); $j++) {
						if ($listaGposCtrEsc['IDPROFCURSO'][$j] == $numGpoTeam) {
							//$numGpoTeam = $listaGposCtrEsc['IDPROFCURSO'][$j];
							$qryAlmnos = "SELECT DISTINCT CVEALU, NOMALU, APELLIDOPATERNO, APELLIDOMATERNO, EMAILUAEM, CVEPLN, CVEGPO FROM camila.ALUMNO_CURSO_DIS WHERE IDPROFCURSO IN ('$numGpoTeam')";
							$mIceAlmnos = $cnxce->consultaM($qryAlmnos);
							$mComAlmnos = oci_fetch_all($mIceAlmnos, $resAlumCtr);
							//var_dump($resAlumCtr['EMAILUAEM']);
							$numItemsCtrEsc = count($resAlumCtr['EMAILUAEM']);

							$url = 'https://graph.microsoft.com/v1.0/teams/'.$idGpoTeam.'/members/add';

							//echo 'Num: '.$listaGposCtrEsc['IDPROFCURSO'][$j] . ' Items Ctr. Esc: '. $numItemsCtrEsc . ' Items Teams: '. $numItemsGpoTeam;
							if ($numItemsCtrEsc > $numItemsGpoTeam ) {
								for ($i=0; $i < count($resAlumCtr['EMAILUAEM']); $i++) { 
									//echo $resAlumCtr['EMAILUAEM'][$i]. '<br>';
									$correo = $resAlumCtr['EMAILUAEM'][$i];
									$usuario = json_encode(array("@odata.type"=>"microsoft.graph.aadUserConversationMember","roles"=>[],"user@odata.bind"=>"https://graph.microsoft.com/v1.0/users('".$correo."')"),true);
										//$arrayCorreos = json_encode($objAlumnosTeam);
									$curl = curl_init();
						      curl_setopt_array($curl, array(
										CURLOPT_URL => $url,
										CURLOPT_RETURNTRANSFER => true,
										CURLOPT_ENCODING => '',
										CURLOPT_MAXREDIRS => 10,
										CURLOPT_TIMEOUT => 0,
										CURLOPT_FOLLOWLOCATION => true,
										CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
										CURLOPT_CUSTOMREQUEST => 'POST',
										CURLOPT_POSTFIELDS =>'{
										"values": ['.
										        $usuario.'
										    ]
										}',
										CURLOPT_HTTPHEADER => array(
										'Content-Type: application/json',
										$auth
										),
						        ));

					        $response = curl_exec($curl);

					        curl_close($curl);
					        echo $response;
								}
							}
						}
					}
					//echo 'Items: ' . $numItemsGpoTeam;
				}
			}
			//$_SESSION["listaCursos"] = $arrayGposTeams;
		break;
		
		case 'salir':
			//Limpiamos las variables de sesión   
	        session_unset();
	        //Destruìmos la sesión
	        if (session_destroy()) {
					  echo "0";
					} else {
					  echo "1";
					}
	        //header("Location: https://sigeteams.uaemex.mx/");
		break;

	}


	function consultarDatosTabla($code)
	{
		//Consultar datos academicos
		$rfcProfesor = $_SESSION['rfc'];
		$correoProfesor = $_SESSION['email'];

		// $txt_file = '../periodo.txt';
		$txt_file = 'periodo.txt';

		$lines = file($txt_file);
		$periodos;
		$rfc;
		foreach ($lines as $num=>$line)
		{
			$periodos=$line;
		}

		// ini_set('display_errors', '1');
		// ini_set('display_startup_errors', '1');
		// error_reporting(E_ALL);

		// include("../inc/cnx_ctrlesc.php");
		include("inc/cnx_ctrlesc.php");
		$cnxce = new Cnx();

		$qryCtrl = "SELECT DISTINCT CVEPRF FROM CAMILA.CURSO_PROFESOR_DIS WHERE CVEPER in ($periodos) AND CORREOUAEM='$correoProfesor' OR CORREOPROFESOR='$correoProfesor'";
		$mIce = $cnxce->consultaM($qryCtrl);
		$mCom = oci_fetch_all($mIce, $resp);
		$rfc = $resp['CVEPRF'][0];

		$qryCtrlGpos = "SELECT DISTINCT IDPROFCURSO, CVEPRF,CVEPER,NOMESP,CVEPLN,CVEMAT,NOMMAT,CVEGPO FROM CAMILA.ALUMNO_CURSO_DIS WHERE CVEPRF in('$rfc','$rfcProfesor') AND CVEPER in ($periodos)";

		$mIceGps = $cnxce->consultaM($qryCtrlGpos);
		$mComGps= oci_fetch_all($mIceGps, $listaGposCtrEsc);
		$_SESSION["listaCursos"] = $listaGposCtrEsc;

		//Generar Token
		if(!isset($_SESSION['token']) && empty($_SESSION['token'])) {
			// $codigo = $_SESSION["codigo"];
			$codigo = $code;
			$_SESSION["codigo"] = $codigo;
			$ch = curl_init();
		  $url = "https://login.microsoftonline.com/common/oauth2/v2.0/token/";
		  $data = 'client_id=f9b65de4-8994-4de4-bd4f-7dc64e654bbf
			&scope=user.read%20mail.read
			&code='.$codigo.'
			&redirect_uri=https://sigeteams.uaemex.mx/panel.php
			&grant_type=authorization_code
			&client_secret=pem8Q~rb6.FU3EIh9uUwZ-sgAEOAL4TiMw1rkbQX';
	    $getUrl = $url;
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	    curl_setopt($ch, CURLOPT_URL, $getUrl);
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 80);
	       
	    $response = curl_exec($ch);
	        
	    if(curl_error($ch)){
	        echo 'Error: ' . curl_error($ch);
	    }else{
	    	$respuesta = json_decode($response, true);
        $token = $respuesta["access_token"];
        $_SESSION['token'] = "Authorization: Bearer ". $token;
	    }
	    curl_close($ch);
	    //echo $_SESSION['token']. "<br>";
		}

		//Listar Grupos de Team
		$auth = $_SESSION['token'];
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => 'https://graph.microsoft.com/v1.0/me/joinedTeams',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'GET',
		  CURLOPT_HTTPHEADER => array(
		  	$auth
		  ),
		));

		$gruposTeam = curl_exec($curl);
		curl_close($curl);
		//echo "Grupos: " . $gruposTeam. "<br>";

		//Detalles de cada grupo
		$listaGposTeams = json_decode($gruposTeam, true);
		//var_dump($listaGposTeams);
		if (count($listaGposTeams['value']) >= 1) {
			for ($i=0; $i <count($listaGposTeams['value']) ; $i++) { 
				//echo $listaGposTeams['value'][$i]['id'];
				$idGpoTeam = $listaGposTeams['value'][$i]['id'];
				$numGpoTeam = $listaGposTeams['value'][$i]['description'];
				$nomGpoTeam = $listaGposTeams['value'][$i]['displayName'];
				//$auth = $_SESSION['token'];
				//$idGpoTeam = $_POST["idGrupoTeam"];
				
				$curl = curl_init();

				curl_setopt_array($curl, array(
				  CURLOPT_URL => 'https://graph.microsoft.com/v1.0/groups/'.$idGpoTeam.'/members',
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => '',
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 0,
				  CURLOPT_FOLLOWLOCATION => true,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => 'GET',
				  CURLOPT_HTTPHEADER => array(
				   $auth
				  ),
				));

				$detalleCurso= curl_exec($curl);
				curl_close($curl);

				$listaDetCurso = json_decode($detalleCurso, true);
				$numItemsGpoTeam = count($listaDetCurso['value']) - 1;
				//echo 'Id: '. $idGpoTeam. 'Nombre: '. $nomGpoTeam . ' No: '. $numGpoTeam .' Items: '. $numItemsGpoTeam .'<br>';
				//var_dump($listaGposTeams['value'][$i]['id']);

				//$arrayGposTeams = $_SESSION["listaCursos"];
				for ($j=0; $j < count($listaGposCtrEsc['CVEPRF']); $j++) {
					//echo $listaGposCtrEsc['IDPROFCURSO'][$j] .'<br>';
					if ($listaGposCtrEsc['IDPROFCURSO'][$j] == $numGpoTeam) {
						//echo 'Id: '. $idGpoTeam. ' Nombre: '. $nomGpoTeam . ' No: '. $numGpoTeam .' Items: '. $numItemsGpoTeam .'<br>';
						$listaGposCtrEsc['NUMALUMOSTEAM'][$j] = $numItemsGpoTeam;
						$listaGposCtrEsc['NOMGPOTEAM'][$j] = $nomGpoTeam;
					}
				}
				// $_SESSION["listaCursos"] = $listaGposCtrEsc;
			}
		}

		for ($j=0; $j < count($listaGposCtrEsc['CVEPRF']); $j++) {
			$numGpoTeam = $listaGposCtrEsc['IDPROFCURSO'][$j];
			$qryAlmnos = "SELECT DISTINCT CVEALU, NOMALU, APELLIDOPATERNO, APELLIDOMATERNO, EMAILUAEM, CVEPLN, CVEGPO FROM camila.ALUMNO_CURSO_DIS WHERE IDPROFCURSO IN ('$numGpoTeam')";
			$mIceAlmnos = $cnxce->consultaM($qryAlmnos);
			$mComAlmnos = oci_fetch_all($mIceAlmnos, $resAlumCtr);
			$numItemsCtrEsc = count($resAlumCtr['EMAILUAEM']);
			$listaGposCtrEsc['NUMALUMOSCTRESC'][$j] = $numItemsCtrEsc;
		}
		$_SESSION["listaCursos"] = $listaGposCtrEsc;
	}

	function limpiarCadena($valor)
	{
	    $valor = str_ireplace("SELECT","",$valor);
	    $valor = str_ireplace("COPY","",$valor);
	    $valor = str_ireplace("DELETE","",$valor);
	    $valor = str_ireplace("DROP","",$valor);
	    $valor = str_ireplace("DUMP","",$valor);
	    $valor = str_ireplace(" OR ","",$valor);
	    $valor = str_ireplace(" AND ","",$valor);
	    $valor = str_ireplace("%","",$valor);
	    $valor = str_ireplace("LIKE","",$valor);
	    $valor = str_ireplace("--","",$valor);
	    $valor = str_ireplace("^","",$valor);
	    $valor = str_ireplace("[","",$valor);
	    $valor = str_ireplace("]","",$valor);
	    $valor = str_ireplace("\\","",$valor);
	    $valor = str_ireplace("script","",$valor);
	    $valor = str_ireplace("UPDATE","",$valor);
	    $valor = str_ireplace("ADD","",$valor);
	    $valor = str_ireplace("NULL","",$valor);
	    $valor = str_ireplace("function","",$valor);
	    $valor = str_ireplace("onEvent","",$valor);
	    $valor = str_ireplace("!","",$valor);
	    $valor = str_ireplace("¡","",$valor);
	    $valor = str_ireplace("&","",$valor);
	    $valor = str_ireplace("<","",$valor);
	    $valor = str_ireplace(">","",$valor);
	    $valor = str_ireplace("{","",$valor);
	    $valor = str_ireplace("}","",$valor);
	    $valor = str_ireplace("alert","",$valor);
	    $valor = str_ireplace(")","",$valor);
	    $valor = str_ireplace("(","",$valor);
	    return $valor;
	} 

ob_end_flush();
?>