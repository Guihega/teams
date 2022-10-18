<?php

$txt_file = 'periodo.txt';

$lines = file($txt_file);
$periodos;
$rfc;
foreach ($lines as $num=>$line)
{
	$periodos=$line;
	echo 'Line '.$num.': '.$line.'<br/>';
}

echo 'Periodos '.$periodos.'<br/>';

echo "Hola SIGETEAMS <br>root: ".$_SERVER['DOCUMENT_ROOT'];

## PRUEBA CNX SEDUCA ##
include($_SERVER['DOCUMENT_ROOT']."/inc/CnxSEDUCA.php");
$cnx = new CnxInt();

$qry = "SELECT * FROM TblComunidades WHERE GpoCtrEsc=233151980";
$Rqry = $cnx->consultaMInt($qry);
echo "<br>SEDUCA Comunidad=".$Rqry[0]['CveEntCom']."<br>";


## PRUEBA CNX SICDE ##
include($_SERVER['DOCUMENT_ROOT']."/inc/cnx_ctrlesc.php");
$cnxce = new Cnx();

$qryCtrl = "SELECT * FROM CAMILA.CURSO_PROFESOR_DIS WHERE IDPROFCURSO='233151980' ";
$mIce = $cnxce->consultaM($qryCtrl);
$mCom = oci_fetch_all($mIce, $res);
//echo $qryCtrl;
//print_r($res);
//var_dump($res);

// foreach ($res as $key => $value) {
// 	//echo "{$key} => {$value} ". "<br>";
//     // if ($key == "CORREOUAEM" || $key == "CORREOPROFESOR" || $key == "CVEPER") {
// 	   //  echo "{$key} => {$value} ". "<br>";
// 	   //  foreach ($value as $clave => $valor) {
// 	   //  	echo "{$clave} => {$valor} ". "<br>";
// 	   //  }
//     // }
//     echo "{$key} => {$value} ". "<br>";
//     foreach ($value as $clave => $valor) {
//     	echo "{$clave} => {$valor} ". "<br>";
//     }
// }

echo "<br>SICDE Plan: ". $res['CVEPLN'][0]. "<br>";
// echo "<br>=================================================<br>";
// //Consulta RFC
// echo "<br>=================================================<br>";
echo "Consulta RFC<br>";
	$qryCtrl = "SELECT DISTINCT CVEPRF FROM CAMILA.CURSO_PROFESOR_DIS WHERE CVEPER in ($periodos) AND CORREOUAEM='afabilan@uaemex.mx' OR CORREOPROFESOR='afabilan@uaemex.mx";
echo $qryCtrl. "<br>";
$mIce = $cnxce->consultaM($qryCtrl);
$mCom = oci_fetch_all($mIce, $res);
// echo "<br>1: ". $mIce;
// echo "<br>2: ". $mCom;
$rfc = $res['CVEPRF'][0];
echo "<br>RFC: ". $rfc . "<br>";

//var_dump($res);
// foreach ($res as $key => $value) {
//     echo "{$key} => {$value} ". "<br>";
//     foreach ($value as $clave => $valor) {
//     	echo "{$clave} => {$valor} ". "<br>";
//     }
// }

//echo "<br>Tabla de grupos: ". $res['CVEPLN'][0];
echo "<br>=================================================<br>";
echo "<br>=================================================<br>";
echo "Consulta GRUPOS<br>";
//Consulta de grupos
$rfc = "SAAE701126";
$qryCtrl = "SELECT DISTINCT IDPROFCURSO, CVEPRF,CVEPER,NOMESP,CVEPLN,CVEMAT,NOMMAT,CVEGPO FROM CAMILA.ALUMNO_CURSO_DIS WHERE CVEPRF = '$rfc' AND CVEPER in ($periodos)";
echo $qryCtrl. "<br>";
$mIce = $cnxce->consultaM($qryCtrl);
$mCom = oci_fetch_all($mIce, $res);
var_dump($res);
foreach ($res as $key => $value) {
    echo "{$key} => {$value} ". "<br>";
    foreach ($value as $clave => $valor) {
    	echo "{$clave} => {$valor} ". "<br>";
    }
}

echo "<br>=================================================<br>";
?>