
<?php
//14 a las 20:00 comprueba los prestamos del dia 13 conn estado 0
//si encuentra alguno
//mira si hay reservas de ese libro
//si las hay --> borra prestamo, manda correo de ok al de la reserva y cambia la reserva a prestamo 0 y f_ini de mañana 15
//si no hay --> borra prestamo
include_once __DIR__ .'/../conexion/ConectaBDC.php';
function compruebaRecogida(){
	$con= ConectaBDC::getInstance();
	$fecha_actual = date("d-m-Y");
	$fecha_prestamo=date("Y-m-d",strtotime($fecha_actual."- 1 days")); 
	$fecha_nuevo_prestamo=date("Y-m-d",strtotime($fecha_actual."+ 1 days")); 
	//prestamos de ayer con codigo 0 (no los han recogido)
	if ( !( $query = $con->prepare( "SELECT * FROM Prestamos WHERE fecha_salida = :fecha_prestamo and estado = '0'") ) ){
		echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
	}elseif ( ! $query->bindParam( ":fecha_prestamo", $fecha_prestamo) ) { 
			echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
	}else{
		$query->execute();
		$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
		if(!empty($resultado)){
			for($i=0;$i<sizeof($resultado);$i++){
				//para cada prestamo de ayer no recogido se ejecuta la funcion noRecogido
				noRecogido($resultado[$i],$fecha_nuevo_prestamo);
			}
		}
	}
}
//funcion para comprobar los libros no recogidos
function noRecogido($registro,$fecha_nuevo_prestamo){
	$con= ConectaBDC::getInstance();
	$codigo=$registro['cod_libro'];
	$id_prestamo=$registro['id_prestamo'];
	
	if ( !( $query = $con->prepare( "SELECT * FROM Lista_espera WHERE codigo = :codigo and 
									id_reserva=(select min(id_reserva) from Lista_espera where codigo=:codigo)") ) ){
		echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
	}elseif ( ! $query->bindParam( ":codigo", $codigo) ) { 
			echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
	}else{
		//si la devolucion se a realizado
		$query->execute();
		$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
		$dni=$resultado[0]['dni'];
		if(empty($resultado)){
			//borrar prestamo
			borrarPrestamo($id_prestamo);
			sumarDisponible($codigo);
			correoNoRecogido($codigo,$dni);
		}else{
			$id_reserva=$resultado[0]['id_reserva'];
			
			$fecha_fin=date("Y-m-d",strtotime($fecha_nuevo_prestamo."+ 15 days"));
			//borrar prestamo
			borrarPrestamo($id_prestamo);
			//borrar la reserva
			borrarReserva($id_reserva);borrarReserva($id_reserva);borrarReserva($id_reserva);
			//hacer prestamo de la reserva
			hacerPrestamoCero($codigo,$dni,$fecha_nuevo_prestamo,$fecha_fin);
		}
	}
	
}
//se borra el prestams en la lista de Prestamo
function borrarPrestamo($id_prestamo){
	$con= ConectaBDC::getInstance();
	if ( !( $query = $con->prepare( "DELETE FROM Prestamos where id_prestamo=:id_prestamo") ) ){
		echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
	}elseif ( ! $query->bindParam( ":id_prestamo", $id_prestamo) ) { 
			echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
	}else{
		$query->execute();
		
	}
}
//funcion que una vez que se devueve se suma ese libro a disponibles de la tabla Libros
function sumarDisponible($codigo){
	$con= ConectaBDC::getInstance();
	if ( !( $query = $con->prepare( "UPDATE Libros set disponible=disponible+1 where cod_libro=:codigo") ) ){
		echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
	}elseif ( ! $query->bindParam( ":codigo", $codigo) ) { 
			echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
	}else{
		$query->execute();
		
	}
}
//funcion que borra la reserva en la tabla Lista de espera
function borrarReserva($id_reserva){
	$con= ConectaBDC::getInstance();
	if ( !( $query = $con->prepare( "DELETE FROM Lista_espera where id_reserva=:id_reserva") ) ){
		echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
	}elseif ( ! $query->bindParam( ":id_reserva", $id_reserva) ) { 
			echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
	}else{
		$query->execute();
		
	}
}
//se inserta en la tabla prestamo
function hacerPrestamoCero($codigo,$dni,$fecha_nuevo_prestamo,$fecha_fin){
	$con= ConectaBDC::getInstance();
	//cod_libro fecha_salida fecha_devolucion dni id_prestamo estado
	$fecha_limite_recogida=date("Y-m-d",strtotime($fecha_nuevo_prestamo."+ 1 days")); 
	//se inserta el prestamo en la tabla Prestamos
	if ( !( $query = $con->prepare( "INSERT INTO Prestamos (cod_libro,fecha_salida,fecha_devolucion,dni) 
									VALUES (:codigo,:fecha_nuevo_prestamo,:fecha_fin,:dni)") ) ){
		echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
	}elseif ( ! $query->bindParam( ":codigo", $codigo) ) { 
			echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
	}elseif ( ! $query->bindParam( ":fecha_nuevo_prestamo", $fecha_nuevo_prestamo) ) { 
			echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
	}elseif ( ! $query->bindParam( ":fecha_fin", $fecha_fin) ) { 
			echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
	}elseif ( ! $query->bindParam( ":dni", $dni) ) { 
			echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
	}else{
		$query->execute();
		//se obtiene los datos del usuario
		if ( !( $query = $con->prepare( "SELECT nombre,email FROM Usuarios where dni=:dni") ) ){
			echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
		}elseif ( ! $query->bindParam( ":dni", $dni) ) { 
				echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
		}else{
			$query->execute();
			$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
			$nombre=$resultado[0]['nombre'];
			$email=$resultado[0]['email'];
			//se obtiene los datos del libro
			if ( !( $query = $con->prepare( "SELECT titulo FROM Libros where cod_libro=:codigo") ) ){
				echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
			}elseif ( ! $query->bindParam( ":codigo", $codigo) ) { 
					echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
			}else{
				$query->execute();
				$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
				$titulo=$resultado[0]['titulo'];
				//se llama a la funcion correo
				correo($email,$nombre,$titulo,$fecha_nuevo_prestamo,$fecha_fin,$fecha_limite_recogida);
				
			}
		}
	}
}
//funcion para enviar al usuario de la reserva y cuando puede ir a por el libro
function correo($email,$nombre,$titulo,$fecha_nuevo_prestamo,$fecha_fin,$fecha_limite_recogida){
		
		$diaI=substr($fecha_nuevo_prestamo,-2);
		$mesI=substr($fecha_nuevo_prestamo,5,2);	
		$anioI=substr($fecha_nuevo_prestamo,0,4);
		
		$diaF=substr($fecha_fin,-2);
		$mesF=substr($fecha_fin,5,2);	
		$anioF=substr($fecha_fin,0,4);
		
		$diaL=substr($fecha_limite_recogida,-2);
		$mesL=substr($fecha_limite_recogida,5,2);	
		$anioL=substr($fecha_limite_recogida,0,4);
		
		ini_set( 'display_errors', 1 );
		error_reporting( E_ALL );
		$from = "bibliofa@bibliofa.com";
		$to = $email;
		$subject = "Tu libro está disponible";
		$message = "Buenos tardes ".$nombre." desde Bibliofa te informamos de que tu reserva de ".$titulo." ya está disponible. \nFecha de inicio del préstamo ".$diaI."-".$mesI."-".$anioI.". \nFecha fin del préstamo ".$diaF."-".$mesF."-".$anioF.". \nFecha límite de recogida  ".$diaL."-".$mesL."-".$anioL."." ;
		$headers = "From:" . $from;
		mail($to,$subject,$message, $headers);
		
}

function correoNoRecogido($codigo,$dni){
	$con= ConectaBDC::getInstance();
	//cod_libro fecha_salida fecha_devolucion dni id_prestamo estado
	
	if ( !( $query = $con->prepare( "SELECT nombre,email FROM Usuarios where dni=:dni") ) ){
		echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
	}elseif ( ! $query->bindParam( ":dni", $dni) ) { 
			echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
	}else{
		$query->execute();
		$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
		$nombre=$resultado[0]['nombre'];
		$email=$resultado[0]['email'];
		
		if ( !( $query = $con->prepare( "SELECT titulo FROM Libros where cod_libro=:codigo") ) ){
			echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
		}elseif ( ! $query->bindParam( ":codigo", $codigo) ) { 
				echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
		}else{
			$query->execute();
			$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
			$titulo=$resultado[0]['titulo'];
			ini_set( 'display_errors', 1 );
			error_reporting( E_ALL );
			$from = "bibliofa@bibliofa.com";
			$to = $email;
			$subject = "Libro no recogido";
			$message = "Buenos tardes ".$nombre." desde Bibliofa te informamos de que el plazo para recoger ".$titulo." ha expirado. \nSi aún lo quiere, tendrá que volver a solicitarlo." ;
			$headers = "From:" . $from;
			mail($to,$subject,$message, $headers);
			
		}
	}

}
compruebaRecogida();

?>
