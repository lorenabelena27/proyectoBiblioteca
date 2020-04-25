<?php
function busqueda($dato){
	$dato=strtolower($dato);
	$con= ConectaBD::getInstance();
	if ( !( $query = $con->prepare( "select * from libros where lower(autor) like '%".$dato."%' or lower(titulo) like '%".$dato."%'" ) ) ){
		echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
	}else{
		$query->execute();
		$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($resultado);
	}

}
?>