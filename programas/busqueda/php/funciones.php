<?php
//funcion para realizar la busqueda de un libro
function busqueda($dato){
	$dato=strtolower($dato);
	$con= ConectaBD::getInstance();
	//se busqua dicho libro en la tabla Libros se busca por titulo y autor
	if ( !( $query = $con->prepare( "select * from Libros where lower(autor) like '%".$dato."%' or lower(titulo) like '%".$dato."%'" ) ) ){
		echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
	}else{
		$query->execute();
		$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($resultado);
	}

}
?>