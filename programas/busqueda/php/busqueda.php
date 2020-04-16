<?php
include_once '../../conexion/ConectaBD.php';
include_once 'funciones.php';
$peticion=json_decode($_POST['x']);
$dato= filtrado($peticion->dato);

if($dato==" "){
	echo json_decode("Debe introducir un titulo o un nombre de autor");
}else{
	busqueda($dato);
}
?>