<?php
include_once '../../conexion/ConectaBD.php';
include_once '../../php_comun/filtrado.php';
include_once 'funciones.php';
$peticion=json_decode($_POST['x']);
//datos enviados desde js
$dato= filtrado($peticion->dato);
//se comprueba que el dato no este vacio si no esta vacio se llama a la funcion
if($dato==" "){
	echo json_encode("Debe introducir un titulo o un nombre de autor");
}else{
	busqueda($dato);
}
?>