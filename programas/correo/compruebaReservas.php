<?php
/*mira las reservas de ese dia, si disponibles = 0-> correo; se retrasa
select de reservas donde fecha = hoy y disponibles = 0*/
include_once __DIR__ .'/../conexion/ConectaBDC.php';
function compruebaReservas(){
	$con= ConectaBDC::getInstance();
	$fecha_actual = date("Y-m-d"); 
	$fecha_nuevo_prestamo=date("Y-m-d",strtotime($fecha_actual."+ 1 days")); 
	//se comprueba las reservas de fecha siguiente a la del sistema 
	if ( !( $query = $con->prepare( "SELECT l.cod_libro , l.titulo, l.disponible,  li.id_reserva, li.dni, u.nombre, u.email from Libros l
									join lista_espera li on li.codigo=l.cod_libro
									join Usuarios u on u.dni = li.dni
									where li.fecha = :fecha_actual") ) ){
		echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
	}elseif ( ! $query->bindParam( ":fecha_actual", $fecha_actual) ) { 
			echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
	}else{
		$query->execute();
		$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
		if(!empty($resultado)){
			for($i=0;$i<sizeof($resultado);$i++){
				$disponible=$resultado[$i]['disponible'];
				$email=$resultado[$i]["email"];
				$nombre=$resultado[$i]["nombre"];
				$titulo=$resultado[$i]["titulo"];
				if($disponible > 0){
					$id_reserva=$resultado[$i]["id_reserva"];
					$codigo=$resultado[$i]["cod_libro"];
					$dni=$resultado[$i]["dni"];
					$fecha_fin=date("Y-m-d",strtotime($fecha_nuevo_prestamo."+ 15 days"));
					//pasar reserva a prestamo estado 0 f_ini mañana
					borrarReserva($id_reserva);
					hacerPrestamoCero($codigo,$dni,$fecha_nuevo_prestamo,$fecha_fin,$email,$titulo,$nombre);
					//correo ya tienes el libro
				}else{
					//correo, se retrasa
					correoNoDisponible($email,$nombre,$titulo);
				}
				
			}
		}else{
			
		}
		
		
	}
}
//funcion de correo que se envia al usuario cuando el libro todavia no esta disponible
function correoNoDisponible($email,$nombre,$titulo){	
		ini_set( 'display_errors', 1 );
		error_reporting( E_ALL );
		$from = "bibliofa@bibliofa.com";
		$to = $email;
		$subject = "Retraso de reserva";
		echo $message = "Buenos tardes ".$nombre." desde Bibliofa le informamos de que tu reserva de ".$titulo." se retrasa debido a que aún no se ha hecho efectiva su devolución. \nLe avisaremos cuando su libro esté disponible, disculpe las molestias." ;
		$headers = "From:" . $from;
		mail($to,$subject,$message, $headers);
		
}

function hacerPrestamoCero($codigo,$dni,$fecha_nuevo_prestamo,$fecha_fin,$email,$titulo,$nombre){
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
				//se llama a la funcion correo 
				correo($email,$nombre,$titulo,$fecha_nuevo_prestamo,$fecha_fin,$fecha_limite_recogida);

	}
}
//funcion para enviar un correo para que el usuraio pueda recoger el libro 
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
		echo $message = "Buenos tardes ".$nombre." desde Bibliofa te informamos de que tu reserva de ".$titulo." ya está disponible. \nFecha de inicio del préstamo ".$diaI."-".$mesI."-".$anioI.". \nFecha fin del préstamo ".$diaF."-".$mesF."-".$anioF.". \nFecha límite de recogida  ".$diaL."-".$mesL."-".$anioL."." ;
		$headers = "From:" . $from;
		mail($to,$subject,$message, $headers);
		
}
//funcion para borraar la reserva en la tabla lista_espera
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

compruebaReservas();
?>
