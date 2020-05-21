<?php
include_once __DIR__ .'/../conexion/ConectaBDC.php';

function verSancionados(){
	$con= ConectaBDC::getInstance();
	$fecha_actual = date("Y-m-d");
	$fecha_sancion=date("Y-m-d",strtotime($fecha_actual."+ 15 days")); 
	//se ve los usuarios que la fecha de entrega es hoy 
	if ( !( $query = $con->prepare( "select p.dni ,u.nombre,u.email from Prestamos p
										join Usuarios u on p.dni=u.dni
										where p.fecha_devolucion=:fecha_actual") ) ){
		echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
	}elseif ( ! $query->bindParam( ":fecha_actual", $fecha_actual) ) { 
			echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
	}else{
		
		$query->execute();
		$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
		for($i=0;$i<sizeof($resultado);$i++){
			$dni=$resultado[$i]["dni"];
			$nombre=$resultado[$i]["nombre"];
			$email=$resultado[$i]["email"];
			//se inserta el usuario en sancionados
			if ( !( $query = $con->prepare( "INSERT INTO Sancionados(nombre, dni, hasta) VALUES (:nombre,:dni,:fecha_sancion)") ) ){
				echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
			}elseif ( ! $query->bindParam( ":nombre", $nombre) ) { 
					echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
			}elseif ( ! $query->bindParam( ":dni", $dni) ) { 
					echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
			}elseif ( ! $query->bindParam( ":fecha_sancion", $fecha_sancion) ) { 
					echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
			}else{
				$query->execute();
				correo($nombre,$fecha_sancion,$email);
			}
		}	
	}
}
//envio de correo al usuario
function correo($nombre,$fecha_sancion,$email){
		
		$dia=substr($fecha_sancion,-2);
		$mes=substr($fecha_sancion,5,2);	
		$anio=substr($fecha_sancion,0,4);	
		ini_set( 'display_errors', 1 );
		error_reporting( E_ALL );
		$from = "bibliofa@bibliofa.com";
		$to = $email;
		$subject = "Sancion";
		$message = "Buenos días ".$nombre.": Desde Bibliofa nos ponemos en comunicación con usted para informarle de que usted esta sancionado  hasta ".$dia."-".$mes."-".$anio." no podra sacar ningun libro.";
		$headers = "From:" . $from;
		mail($to,$subject,$message, $headers);
		
}
verSancionados()

?>
