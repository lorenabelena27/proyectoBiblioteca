<?php
//comprobara que el trabajador es quien dice ser
function acceso($nTrabajador,$pass){
	$con= ConectaBD::getInstance();
	if ( !( $query = $con->prepare( "select contraseña ,dni from Administradores where id_admin=:nTrabajador" ) ) ){
		echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
	}elseif ( ! $query->bindParam( ":nTrabajador", $nTrabajador) ) { 
		echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
	}else{
		$query->execute();
		$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
		if(empty($resultado[0])){
			
			$respuesta="No";

		}else{
			$pass_hash = $resultado[0]['contraseña'];
			$dni=$resultado[0]['dni'];
			if(Password::verify($pass, $pass_hash)){
				session_start();
				$_SESSION["dni"]=$dni;
				$respuesta="Entras";
			}else{
				$respuesta="No";
			}
		}
		echo json_encode($respuesta);
	}
}	



			
?>