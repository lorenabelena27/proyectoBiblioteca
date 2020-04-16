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
function validaDni($dni){
	$patron="/^[0-9]{8}[A-Za-z]{1}$/"; 
	if (preg_match($patron,$dni)==true){ 
		$numero=substr($dni,0,8); 
		$letras="TRWAGMYFPDXBNJZSQVHLCKE"; 
		$resto=intval($numero)%23;	

		if (strcmp($dni[8],$letras[$resto])!=0){
			return "dni no valido";	
		}else{
			return "dni valido";
		}
	}else { 
		return false;
	}
}
function alta($nombre,$apellidos,$dni,$email,$fecha,$pas){
	$con= ConectaBD::getInstance();
	
	if ( !( $query = $con->prepare( "select nombre from usuarios where dni=:dni " ) ) ){
			echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
		}elseif ( ! $query->bindParam( ":dni", $dni) ) { 
			echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
		}else{
			$query->execute();
			$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
			
			if(empty($resultado)){
				$pass=Password::hashs($pas);
				
				if ( !( $query = $con->prepare( "INSERT INTO usuarios(nombre, apellidos, dni, email, fecha_nacimiento, contraseña) VALUES (:nombre,:apellidos,:dni,:email,:fecha,:pass)" ) ) ){
					echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
				}elseif ( ! $query->bindParam( ":nombre", $nombre) ) { // Vincula parámetros con variables echo "Falló la vinculación de parámetros: " . $orden->errno . "- " . $orden->error; }
						echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
				}elseif ( ! $query->bindParam( ":apellidos", $apellidos) ) { // Vincula parámetros con variables echo "Falló la vinculación de parámetros: " . $orden->errno . "- " . $orden->error; }
						echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
				}elseif ( ! $query->bindParam( ":dni", $dni) ) { // Vincula parámetros con variables echo "Falló la vinculación de parámetros: " . $orden->errno . "- " . $orden->error; }
						echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
				}elseif ( ! $query->bindParam( ":email", $email) ) { // Vincula parámetros con variables echo "Falló la vinculación de parámetros: " . $orden->errno . "- " . $orden->error; }
						echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
				}elseif ( ! $query->bindParam( ":fecha", $fecha) ) { // Vincula parámetros con variables echo "Falló la vinculación de parámetros: " . $orden->errno . "- " . $orden->error; }
						echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
				}elseif ( ! $query->bindParam( ":pass", $pass) ) { // Vincula parámetros con variables echo "Falló la vinculación de parámetros: " . $orden->errno . "- " . $orden->error; }
						echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
				}else{
					
					$query->execute();
				}
			}else{
				echo json_encode("El usuario ya existe");	
			}
			
		}
}

			
?>