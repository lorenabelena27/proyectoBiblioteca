<?php
include_once  __DIR__ . '/../conexion/ConectaBDC.php';
//funcion para comprobar las entregas de los libros
function entregaLibros(){
	$con= ConectaBDC::getInstance();
	$fecha_actual = date("d-m-Y");
	$fecha_envio=date("Y-m-d",strtotime($fecha_actual."+ 1 days")); 
	//se comprueba las fechas de entrega del dia siguiente del sistema y se obtiene la informacion del usuario
	if ( !( $query = $con->prepare( "select u.email , u.nombre ,l.titulo , p.fecha_devolucion from Usuarios u 
										join Prestamos p on u.dni=p.dni 
										join Libros l on l.cod_libro=p.cod_libro
										where l.cod_libro=p.cod_libro and p.fecha_devolucion=:fecha_envio") ) ){
		echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
	}elseif ( ! $query->bindParam( ":fecha_envio", $fecha_envio) ) { 
			echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
	}else{
		$query->execute();
		$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
		//se recorre resultados y se llama a la funcion de correo
		for($i=0;$i<sizeof($resultado);$i++){
			$email=$resultado[$i]["email"];
			$nombre=$resultado[$i]["nombre"];
			$titulo=$resultado[$i]["titulo"];
			$fecha_devolucion=$resultado[$i]["fecha_devolucion"];
			correo($email,$nombre,$titulo,$fecha_devolucion);
		}
		
	}
}
//funcion para enviar un correo a los usuarios recordadoles la fecha de entrega
function correo($email,$nombre,$titulo,$fecha_devolucion){
		
		$dia=substr($fecha_devolucion,-2);
		$mes=substr($fecha_devolucion,5,2);	
		$anio=substr($fecha_devolucion,0,4);	
		ini_set( 'display_errors', 1 );
		error_reporting( E_ALL );
		$from = "bibliofa@bibliofa.com";
		$to = $email;
		$subject = "Devoloción de libro";
		$message = "Buenos días ".$nombre." desde Bibliofa te recordamos que la fecha de entrega del libro ".$titulo." es mañana dia ".$dia."-".$mes."-".$anio ;
		$headers = "From:" . $from;
		mail($to,$subject,$message, $headers);
		
}
entregaLibros()

?>
