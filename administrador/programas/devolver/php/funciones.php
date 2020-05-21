<?php
//funcion para validar el formato del dni
function validaDni($dni){
	$patron="/^[0-9]{8}[A-Za-z]{1}$/"; 
	if (preg_match($patron,$dni)==true){ 
		$numero=substr($dni,0,8); 
		$letras="TRWAGMYFPDXBNJZSQVHLCKE"; 
		$letrasM="trwagmyfpdxbnjzsqvhlcke"; 
		$resto=intval($numero)%23;	
		if ((strcmp($dni[8],$letras[$resto])!=0) && (strcmp($dni[8],$letrasM[$resto])!=0)){
			return "dni no valido";	
		}else{
			return "dni valido";
		}
	}else { 
		return false;
	}
}
//funcion para devolver el libro
function devolver($dni,$codigo){
	$con= ConectaBD::getInstance();
	$mensaje=array();
	//comprobar que el usuario existe
	if ( !( $query = $con->prepare( "select * from Usuarios where dni=:dni") ) ){
		echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
	}elseif ( ! $query->bindParam( ":dni", $dni) ) { 
			echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
	}else{
		
		$query->execute();
		$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
		if(!empty($resultado)){
			//se borra el libro de prestamos
			if ( !( $query = $con->prepare( "delete from Prestamos where dni=:dni and cod_libro=:codigo ") ) ){
				echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
			}elseif ( ! $query->bindParam( ":dni", $dni) ) { 
					echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
			}elseif ( ! $query->bindParam( ":codigo", $codigo) ) { 
					echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
			}else{
				$query->execute();
				$resultado = $query->rowCount();
				//se añade a prestamos si el codigo esta en lista de espera
				if($resultado !=0){
					array_push($mensaje,"Devolución realizada con éxito");
					if ( !( $query = $con->prepare( "select dni from Lista_espera where codigo=:codigo and id_reserva=(select min(id_reserva) from Lista_espera where codigo=:codigo) ") ) ){
						echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
					}elseif ( ! $query->bindParam( ":codigo", $codigo) ) { 
							echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
					}else{
						
						$query->execute();
						$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);

						if(!empty($resultado)){
							
							$dni2=$resultado[0]["dni"];
							if ( !( $query = $con->prepare( "delete from Lista_espera where dni=:dni and codigo=:codigo ") ) ){
								echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
							}elseif ( ! $query->bindParam( ":dni", $dni2) ) { 
								echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
							}elseif ( ! $query->bindParam( ":codigo", $codigo) ) { 
								echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
							}else{
								$query->execute();
								$resultado = $query->rowCount();
								if($resultado !=0){
									$fecha_actual = date("Y-m-d");
									$fecha_inicio=date("Y-m-d",strtotime($fecha_actual."+ 1 days")); 
									$fecha_limite_recogida=date("Y-m-d",strtotime($fecha_inicio."+ 1 days")); 
									$fecha_fin=date("Y-m-d",strtotime($fecha_actual."+ 15 days")); 
									//inseta la reserva en prestamos
									if ( !( $query = $con->prepare( " insert into Prestamos (cod_libro,fecha_salida,fecha_devolucion,dni) VALUES (:codigo,:fecha_inicio,:fecha_fin,:dni) ") ) ){
										echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
									}elseif ( ! $query->bindParam( ":dni", $dni2) ) { 
										echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
									}elseif ( ! $query->bindParam( ":codigo", $codigo) ) { 
										echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
									}elseif ( ! $query->bindParam( ":fecha_inicio", $fecha_inicio) ) { 
										echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
									}elseif ( ! $query->bindParam( ":fecha_fin", $fecha_fin) ) { 
										echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
									}else{
										$query->execute();
										$resultado = $query->rowCount();
										if($resultado!=0){
											//se buscan los datos del usuario
											if ( !( $query = $con->prepare( "select * from Usuarios where dni=:dni") ) ){
												echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
											}elseif ( ! $query->bindParam( ":dni", $dni2) ) { 
												echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
											}else{
												$query->execute();
												$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
												$nombre=$resultado[0]["nombre"];
												$email=$resultado[0]["email"];
												//se busca el titulo que se va a prestar
												if ( !( $query = $con->prepare( "select titulo from Libros where cod_libro=:codigo") ) ){
													echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
												}elseif ( ! $query->bindParam( ":codigo", $codigo) ) { 
													echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
												}else{
													$query->execute();
													$titulo_libro= $query ->fetchAll(PDO::FETCH_ASSOC);
													$titulo=$titulo_libro[0]["titulo"];
													 correo($nombre,$fecha_inicio,$fecha_fin,$fecha_limite_recogida,$email,$titulo);
												}
											}
										}
									}
								}else{
									array_push($mensaje,"No se ha borrado la reserva");
								}
							}

						}else{
							if ( !( $query = $con->prepare( "update Libros set disponible=disponible+1 where cod_libro=:codigo " ) ) ){
								echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
							}elseif ( ! $query->bindParam( ":dni", $dni) ) { 
									echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
							}elseif ( ! $query->bindParam( ":codigo", $codigo) ) { 
									echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
							}else{
								$query->execute();
							}
						}
					}
				}
			}
		}else{
			array_push($mensaje,"El usuario no existe");
		}
	}
	echo json_encode($mensaje);
}

//envio de correo al usuario
function correo($nombre,$fecha_inicio,$fecha_fin,$fecha_limite_recogida,$email,$titulo){
		
		$dia=substr($fecha_inicio,-2);
		$mes=substr($fecha_inicio,5,2);	
		$anio=substr($fecha_inicio,0,4);
		
		$dia1=substr($fecha_fin,-2);
		$mes1=substr($fecha_fin,5,2);	
		$anio1=substr($fecha_fin,0,4);
		
		$dia2=substr($fecha_limite_recogida,-2);
		$mes2=substr($fecha_limite_recogida,5,2);	
		$anio2=substr($fecha_limite_recogida,0,4);
		
		ini_set( 'display_errors', 1 );
		error_reporting( E_ALL );
		$from = "bibliofa@bibliofa.com";
		$to = $email;
		$subject = "Sancion";
		$message = "Buenos días ".$nombre." desde Bibliofa le informamos de que su libro ".$titulo ." ya esta disponible desde el dia "
					.$dia."-".$mes."-".$anio." hasta ".$dia1."-".$mes1."-".$anio1." puede pasar a recogerlo hasta".$dia2."-".$mes2."-".$anio2;
		$headers = "From:" . $from;
		mail($to,$subject,$message, $headers);
		
}
?>