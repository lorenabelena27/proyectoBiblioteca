<?php
function validaEmail($email){
	$resp = false;
	$patron="/^([a-zA-Z])+([\w\._])*([a-zA-Z])+@/";	
	$dominios=array("gmail.com","yahoo.com","yahoo.es","hotmail.com"); 
	$pos=strpos($email,"@"); 
	$parte=substr($email,$pos+1);

	if (preg_match($patron,$email)){ 
		if (in_array($parte,$dominios)) { 
			$resp = "email valido";
		}else{
			$resp = "email no valido";
		}
	}else {
		$resp = false; 
	}
	return $resp;
}
function validaPass($contrasena){
	$patronPass="/^(?=\w*\d)(?=\w*[A-Z])(?=\w*[a-z])\S{8,16}$/";
	if(preg_match($patronPass,$contrasena)==false){
		return false;
	}else{
		return true;
	}
}

function acceso($email,$pass){
	$con= ConectaBD::getInstance();
	if ( !( $query = $con->prepare( "select nombre, contraseña from usuarios where email=:email" ) ) ){
		echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
	}elseif ( ! $query->bindParam( ":email", $email) ) { 
		echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
	}else{
		$query->execute();
		$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
		if(empty($resultado[0])){
			$respuesta="El usuario no registrado , compruebe su email";
			echo json_encode($respuesta);
		}else{
			$pass_hash = $resultado[0]['contraseña'];
			$nombre = $resultado[0]['nombre'];
			
			if(Password::verify($pass, $pass_hash)){
				session_start();
				$_SESSION["usuario"]=$nombre;
				$respuesta="Entras";
			}else{
				$respuesta="Contraseña incorrecta";
			}
		}
		echo json_encode($respuesta);
	}
	
	
}	



			
?>