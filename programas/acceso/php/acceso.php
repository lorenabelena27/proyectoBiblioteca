<?php
include_once '../../conexion/ConectaBD.php';
include_once '../../php_comun/password.php';
include_once '../../php_comun/filtrado.php';
include_once 'funciones.php';
$peticion=json_decode($_POST['x']);
$email= filtrado($peticion->email);
$pas= filtrado($peticion->contra);
$error=false; 
$respuesta=array();

if(empty($email)){
	array_push($respuesta,"Debes introducir un Email");
	$error=true;
}

if(empty($pas)){
	array_push($respuesta,"Debes introducir una Contraseña");
	$error=true;
}
if(empty($error)){
	acceso($email,$pas);
}else{
	echo json_encode($respuesta);
}
?>