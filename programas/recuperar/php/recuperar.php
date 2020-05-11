<?php
include_once '../../conexion/ConectaBD.php';
include_once '../../php_comun/password.php';
include_once 'funciones.php';
$peticion=json_decode($_POST['x']);
$email= $peticion->email;

$error=false; 
$respuesta=array();

if(empty($email)){
	array_push($respuesta,"Debes introducir un Email");
	$error=true;
}
if(empty($error)){
	recuperar($email);
}else{
	echo json_encode($respuesta);
}
?>