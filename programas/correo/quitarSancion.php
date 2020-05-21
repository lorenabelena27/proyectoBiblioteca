<?php
include_once __DIR__ .'/../conexion/ConectaBDC.php';

function verSancionados(){
	$con= ConectaBDC::getInstance();
	$fecha_actual = date("Y-m-d");

	//se mira los usuarios de sancionados que termina su sancion hoy
	if ( !( $query = $con->prepare( "select * from Sancionados	where hasta=:fecha_actual") ) ){
		echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
	}elseif ( ! $query->bindParam( ":fecha_actual", $fecha_actual) ) { 
			echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
	}else{
		
		$query->execute();
		$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
		for($i=0;$i<sizeof($resultado);$i++){
			$dni=$resultado[$i]["dni"];

			//se borra al usuario de la tabla sancionados
			if ( !( $query = $con->prepare( "DELETE FROM Sancionados WHERE dni=:dni") ) ){
				echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
			}elseif ( ! $query->bindParam( ":dni", $dni) ) { 
					echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
			}else{
				$query->execute();
			}
		}	
	}
}

verSancionados()

?>