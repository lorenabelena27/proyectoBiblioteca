<?php
include_once '../../conexion/ConectaBD.php';
include_once '../../php_comun/password.php';
include_once '../../php_comun/filtrado.php';
include_once 'funciones.php';
$peticion=json_decode($_POST['x']);
$nombre= filtrado($peticion ->nom);
$apellidos= filtrado($peticion->ape);
$dni= filtrado($peticion->dni);
$email= filtrado($peticion->email);
$fecha_na=$peticion->nac;
$pas= filtrado($peticion->pass);
$pass= filtrado($peticion->conf);
$fecha=strtotime($fecha_na);
$hoy= strtotime(date("Y-m-d"));
$error=false; 
$respuesta=array();
//comprobaciones a lado del servidor
if(empty($nombre)){
	array_push($respuesta,"Debes introducir un Nombre");
	$error=true;
}elseif(validaNom($nombre)==false){
	array_push($respuesta,"El nombre solo puede contener letras");
	$error=true;
}
if(empty($apellidos)){
	array_push($respuesta,"Debes introducir un Apellidos");
	$error=true;
}elseif(validaNom($apellidos)==false){
	array_push($respuesta,"Los apellidos solo puede contener letras");
	$error=true;
}
if(empty($dni)){
	array_push($respuesta,"Debes introducir un DNI");
	$error=true;
}else{
	if(validaDni($dni)==false){
		array_push($respuesta,"Formato DNI no valido ");
		$error=true;
	}elseif(validaDni($dni)== "dni no valido"){
		array_push($respuesta,"DNI no valido");
		$error=true;
	}
}
if(empty($email)){
	array_push($respuesta,"Debes introducir un Email");
	$error=true;
}else{
	if(validaEmail($email)==false){
		array_push($respuesta,"Formato Email no valido ");
		$error=true;
	}elseif(validaEmail($email)=="email no valido"){
		array_push($respuesta,"Tu dominio no existe , prueba con gmail.com,hotmail.com,yahoo.es");
		$error=true;
	}
}

if(empty($fecha)){
	array_push($respuesta,"Debes introducir una Fecha");
	$error=true;
}elseif($fecha>$hoy){
	array_push($respuesta,"Debes introducir una Fecha menor que actual");
	$error=true;
}
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
//si todos los datos estan validados se llama a la funcion
if($error==false){
	
	alta($nombre,$apellidos,$dni,$email,$fecha_na,$pas);
	
}else{
	echo json_encode($respuesta);
	}
?>