<?php
include_once '../../conexion/ConectaBD.php';

include_once 'funciones.php';
//datos que le envia js
$peticion=json_decode($_POST['x']);
$tamanio=12;
//dependiendo de los datos que recibe se llama a una funcio o a otra
if(isset($peticion->pag)){
	$pag = $peticion->pag;
	json_encode(pagina($pag,$tamanio));	
}elseif(isset($peticion->cat)){
	json_encode(categorias());	
}elseif(isset($peticion->catego)){
	$categoria=$peticion->catego;
	json_encode(librosCT($categoria, $tamanio));	
}elseif(isset($peticion->pagF)){
	$pag = $peticion->pagF;
	$categoria = $peticion->catF;
	json_encode(paginaF($pag,$tamanio,$categoria));	
}else{
	json_encode(paginacion($tamanio));	
}


?>