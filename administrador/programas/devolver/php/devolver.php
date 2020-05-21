<?php
session_start();
include_once '../../conexion/ConectaBD.php';
include_once 'funciones.php';
//mantiene las sessiones en firefox
header("Cache-Control: no-cache");
header("Pragma: no-cache");
$peticion=json_decode($_POST['x']);
$dni= $peticion->dni;
$codigo= $peticion->codigo;
$error=false; 
$respuesta=array();
//comprobaciones de dni
if(empty($dni)){
	array_push($respuesta,"Debes introducir un DNI");
	$error=true;
	if(validaDni($dni)==false){
		array_push($respuesta,"Formato DNI no valido ");
		$error=true;
	}elseif(validaDni($dni)== "dni no valido"){
		array_push($respuesta,"DNI no valido");
		$error=true;
	}
}
//comprobacion de codigo de libro
if(empty($codigo)){
	array_push($respuesta,"Debes introducir un codigo de libro");
	$error=true;
}
//funcion para devolver el libro
if(empty($error)){
	devolver($dni,$codigo);
}else{
	echo json_encode($respuesta);
}
?>