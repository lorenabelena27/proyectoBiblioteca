<?php
//para validar la contraseña en el servidor
function validaPass($contrasena){
	$patronPass="/^(?=\w*\d)(?=\w*[A-Z])(?=\w*[a-z])\S{8,16}$/";
	if(preg_match($patronPass,$contrasena)==false){
		return false;
	}else{
		return true;
	}
}
//se actualiza la tabla usuarios con la nueva contraseña
function cambio($pass,$dni){
	$con= ConectaBD::getInstance();

	$nuevaPass=Password::hashs($pass);
	
	if ( !( $query = $con->prepare( "update Usuarios set contraseña = :nuevaPass where dni=:dni" ) ) ){
		echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
	}elseif ( ! $query->bindParam( ":dni", $dni) ) { 
		echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
	}elseif ( ! $query->bindParam( ":nuevaPass", $nuevaPass) ) { 
		echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
	}else{
		$query->execute();
		$respuesta=$query->rowCount();
		if($respuesta==0){
			$respuesta="No";
		}else{
			$respuesta="Si";
		}
	}
	echo json_encode($respuesta);

}
?>