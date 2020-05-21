<?php
session_start();
include_once '../../conexion/ConectaBD.php';
include_once '../../php_comun/password.php';
include_once 'funciones.php';
//mantiene las sessiones en firefox
header("Cache-Control: no-cache");
header("Pragma: no-cache");
//se cogen los datos de la peticion
$peticion=json_decode($_POST['x']);
$pass= $peticion->pass;
$pas= $peticion->pas;
$error=false; 
$respuesta=array();
$dni=$_SESSION["dni"];
//se comprueba que todos los datos esten bien en el servidor
if(empty($pas)){
	array_push($respuesta,"Debes introducir una Contraseña");
	$error=true;
}elseif(validaPass($pas)==false){
	array_push($respuesta,"Contraseña no valida");
	$error=true;
}
if(empty($pass)){
	array_push($respuesta,"Debes introducir una Contraseña");
	$error=true;
}elseif($pass!=$pas){
	array_push($respuesta,"Las contraseñas no coinciden");
	$error=true;
}
//si todo esta bien se llama a la funcion del cambio de contraseña
if(empty($error)){
	cambio($pass,$dni);
}else{
	echo json_encode($respuesta);
}
?>