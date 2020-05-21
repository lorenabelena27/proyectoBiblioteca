<?php
session_start();
include_once '../../conexion/ConectaBD.php';
//mantiene las sessiones en firefox
header("Cache-Control: no-cache");
header("Pragma: no-cache");
include_once 'funciones.php';
//datos enviados desde js
$peticion=json_decode($_POST['x']);
$dni=$_SESSION["dni"];
//las diferentes peticiones
if(isset($peticion->recomendados)){
	librosRecomendados($dni);	
}else{
	ultimasPublicaciones();
}
?>