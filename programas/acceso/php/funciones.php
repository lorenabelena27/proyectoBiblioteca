<?php
function acceso($email,$pass){
	$con= ConectaBD::getInstance();
	if ( !( $query = $con->prepare( "select nombre, contraseña ,dni from usuarios where email=:email" ) ) ){
		echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
	}elseif ( ! $query->bindParam( ":email", $email) ) { 
		echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
	}else{
		$query->execute();
		$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
		if(empty($resultado[0])){
			
			$respuesta="No";

		}else{
			$pass_hash = $resultado[0]['contraseña'];
			$nombre = $resultado[0]['nombre'];
			$dni=$resultado[0]['dni'];
			if(Password::verify($pass, $pass_hash)){
				session_start();
				$_SESSION["usuario"]=$nombre;
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