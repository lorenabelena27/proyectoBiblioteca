<?php
function misLibros($dni){
	$respuesta=array();
	$con= ConectaBD::getInstance();
	if ( !( $query = $con->prepare("SELECT l.img, l.titulo, p.fecha_salida, p.fecha_devolucion FROM prestamos p 
									join libros l on l.cod_libro = p.cod_libro
									where p.dni = :dni") ) ){
		echo "Falló la preparación: " . $Id->errno . " - " . $Id->error; 	
	}elseif ( ! $query->bindParam( ":dni", $dni) ) { 
		echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
	}else{
		$query->execute();
		$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
		if(empty($resultado)){
			array_push($respuesta,0);
		}else{
			array_push($respuesta,$resultado);
		}
		if ( !( $query = $con->prepare("SELECT l.img, l.titulo, li.fecha FROM lista_espera li 
										join libros l on l.cod_libro = li.codigo
										where li.dni = :dni") ) ){
			echo "Falló la preparación: " . $Id->errno . " - " . $Id->error; 	
		}elseif ( ! $query->bindParam( ":dni", $dni) ) { 
			echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
		}else{
			$query->execute();
			$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
			if(empty($resultado)){
				array_push($respuesta,0);
			}else{
				array_push($respuesta,$resultado);
			}
		}
	}
	
	echo json_encode($respuesta);
}
?>