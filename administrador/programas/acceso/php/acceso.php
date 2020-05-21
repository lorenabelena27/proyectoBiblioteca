<?php
include_once '../../conexion/ConectaBD.php';
include_once '../../php_comun/password.php';
include_once '../../php_comun/filtrado.php';
include_once 'funciones.php';
//peticiones
$peticion=json_decode($_POST['x']);
$nTrabajador= filtrado($peticion->nTrabajador);
$pas= filtrado($peticion->contra);
$error=false; 
$respuesta=array();
//comprobaciones
if(empty($nTrabajador)){
	array_push($respuesta,"Debe introducir un Número de trabajador");
	$error=true;
}

if(empty($pas)){
	array_push($respuesta,"Debe introducir una Contraseña");
	$error=true;
}
//se hace la llamada a la funcion
if(empty($error)){
	acceso($nTrabajador,$pas);
}else{
	echo json_encode($respuesta);
}
?>