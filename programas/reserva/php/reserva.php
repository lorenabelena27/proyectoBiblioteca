<?php
session_start();
header("Cache-Control: no-cache");
header("Pragma: no-cache");
include_once '../../conexion/ConectaBD.php';
include_once 'funciones.php';
$peticion=json_decode($_POST['x']);
$dni=$_SESSION["dni"];
if(isset($peticion->reserva)){
	$codigo=$peticion->codigo;
	reserva($dni,$codigo);
}elseif(isset($peticion->fecha)){
	$codigo=$peticion->codigo;
	$fecha=$peticion->fecha;
	reservarLibro($dni,$codigo,$fecha);
	
}elseif(isset($peticion->codigoPrestar)){
	$codigo=$peticion->codigoPrestar;
	$fechaIni=$peticion->fechaIni;
	$fechaFin=$peticion->fechaFin;
	prestarLibro($dni,$codigo,$fechaIni,$fechaFin);
	
}
?>