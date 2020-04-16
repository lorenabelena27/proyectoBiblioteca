<?php
include_once '../../conexion/ConectaBD.php';

include_once 'funciones.php';
$peticion=json_decode($_POST['x']);
$tamanio=12;
if(isset($peticion->pag)){
	$pag = $peticion->pag;
	json_encode(pagina($pag,$tamanio));	
}else{
	json_encode(paginacion($tamanio));	
}


?>