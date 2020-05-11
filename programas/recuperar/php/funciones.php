<?php
function recuperar($email){
	$con= ConectaBD::getInstance();
	if ( !( $query = $con->prepare( "select nombre from usuarios where email=:email" ) ) ){
		echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
	}elseif ( ! $query->bindParam( ":email", $email) ) { 
		echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
	}else{
		$query->execute();
		$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
		if(empty($resultado[0])){
			
			$respuesta="No";

		}else{
			$pass = '0123456789AbcdefgHIjKlmNOpqrstuvwXyz';		
			$tamaño = strlen($pass);
			$string = '';
			for($i = 0; $i < 16; $i++) {
				$posicion=mt_rand(0, $tamaño - 1);
				$character = $pass[$posicion];
				$string .= $character;
			}
			$nuevaPass=Password::hashs($string);
			
			if ( !( $query = $con->prepare( "update usuarios set contraseña = :nuevaPass where email=:email" ) ) ){
				echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
			}elseif ( ! $query->bindParam( ":email", $email) ) { 
				echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
			}elseif ( ! $query->bindParam( ":nuevaPass", $nuevaPass) ) { 
				echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
			}else{
				$query->execute();
				$respuesta=$string;
			}
		}
		echo json_encode($respuesta);
	}
}
		
