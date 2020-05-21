<?php
//funcion recuperacion de contraseña
function recuperar($email){
	$con= ConectaBD::getInstance();
	//se comprueba que el usuario esta logueado 
	if ( !( $query = $con->prepare( "select nombre from Usuarios where email=:email" ) ) ){
		echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
	}elseif ( ! $query->bindParam( ":email", $email) ) { 
		echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
	}else{
		$query->execute();
		$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
		if(empty($resultado[0])){
			
			$respuesta="No";

		}else{
			//se crea una cotraseña aleatoria
			$pass = '0123456789AbcdefgHIjKlmNOpqrstuvwXyz';		
			$tamaño = strlen($pass);
			$string = '';
			for($i = 0; $i < 16; $i++) {
				$posicion=mt_rand(0, $tamaño - 1);
				$character = $pass[$posicion];
				$string .= $character;
			}
			//se hashs la nueva contraseña se inserta en la tabla usuarios
			$nuevaPass=Password::hashs($string);
			
			if ( !( $query = $con->prepare( "update Usuarios set contraseña = :nuevaPass where email=:email" ) ) ){
				echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
			}elseif ( ! $query->bindParam( ":email", $email) ) { 
				echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
			}elseif ( ! $query->bindParam( ":nuevaPass", $nuevaPass) ) { 
				echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
			}else{
				$query->execute();
				$respuesta=true;
				//se llama a la funcion correo
				correo($email,$string);
			}
		}
		echo json_encode($respuesta);
	}
}
//se envia un correo al usuario con la nueva contraseña
function correo($email,$passN){
	ini_set( 'display_errors', 1 );
	error_reporting( E_ALL );
	$from = "bibliofa@bibliofa.com";
	$to = $email;
	$subject = "Nueva contraseña";
	$message = "Su nueva contraseña es ".$passN;
	$headers = "From:" . $from;
	mail($to,$subject,$message, $headers);
		
}	
