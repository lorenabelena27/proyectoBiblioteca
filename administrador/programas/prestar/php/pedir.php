<?php
session_start();
include_once '../../conexion/ConectaBD.php';
include_once 'funciones.php';
//mantiene las sessiones en firefox
header("Cache-Control: no-cache");
header("Pragma: no-cache");
$peticion=json_decode($_POST['x']);


if(isset($peticion->dni)){
	$dni= $peticion->dni;
	if(validaDni($dni)==false){
		array_push($respuesta,"Formato DNI no valido ");
		$error=true;
	}elseif(validaDni($dni)== "dni no valido"){
		array_push($respuesta,"DNI no valido");
		$error=true;
	}
	$codigo= $peticion->codigo;
	$error=false; 
	$respuesta=array();
	if(empty($dni)){
		array_push($respuesta,"Debes introducir un DNI");
		$error=true;
	}
	if(empty($codigo)){
		array_push($respuesta,"Debes introducir un codigo de libro");
		$error=true;
	}
	if(empty($error)){
		pedir($dni,$codigo);
	}else{
		echo json_encode($respuesta);
	}
}elseif(isset($peticion->fechaRes)){
	$codigo=$peticion->codigoRes;
	$fecha=$peticion->fechaRes;
	$dni=$peticion->dniRes;
	
	if(empty($error)){
		reservarLibro($dni,$codigo,$fecha);
	}else{
		echo json_encode($respuesta);
	}
	
}elseif(isset($peticion->codigoPrestar)){
	$codigo=$peticion->codigoPrestar;
	$fechaIni=$peticion->fechaIni;
	$fechaFin=$peticion->fechaFin;
	$dni=$peticion->dniPrestar;
	if(empty($error)){
		prestarLibro($dni,$codigo,$fechaIni,$fechaFin);
	}else{
		echo json_encode($respuesta);
	}
	
}


?>