<?php
function busqueda($dato){
	$con= ConectaBD::getInstance();
	if ( !( $query = $con->prepare( "select * from libros where autor like %:dato% || titulo like %:dato%" ) ) ){
		echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
	}elseif ( ! $query->bindParam( ":dato", $dato) ) { 
		echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
	}else{
		$query->execute();
		$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($respuesta);
	}
	
	
}
?>