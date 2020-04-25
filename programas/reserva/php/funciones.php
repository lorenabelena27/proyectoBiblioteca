<?php
function reserva($dni,$codigo){
	$con= ConectaBD::getInstance();
	//COMPROBAR SI EL USUARIO ESTA SANCIONADO
	if ( !( $query = $con->prepare( "select dni from sancionados where dni=:dni" ) ) ){
		echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
	}elseif ( ! $query->bindParam( ":dni", $dni) ) { 
		echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
	}else{
		$query->execute();
		$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
		//SI NO ESTA SANCIONADO
		if(empty($resultado)){
			//COMPROBAR SI EL USUARIO TIENE EL LIBRO PRESTADO
			if ( !( $query = $con->prepare( "select dni from prestamos where dni=:dni and cod_libro=:cod_libro" ) ) ){
			echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
			}elseif ( ! $query->bindParam( ":dni", $dni) ) { 
				echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
			}elseif ( ! $query->bindParam( ":cod_libro", $codigo) ) { 
				echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
			}else{
				$query->execute();
				$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
				//SI NO TIENE EL LIBRO PRESTADO
				if(empty($resultado)){
					//COMPROBAR SI TIENE EL LIBRO RESERVADO
					if ( !( $query = $con->prepare( "select dni from lista_espera where dni=:dni and codigo=:cod_libro" ) ) ){
					echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
					}elseif ( ! $query->bindParam( ":dni", $dni) ) { 
						echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
					}elseif ( ! $query->bindParam( ":cod_libro", $codigo) ) { 
						echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
					}else{
						$query->execute();
						$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
						//SI NO LO TIENE RESERVADO
						if(empty($resultado)){
							if ( !( $query = $con->prepare( "select disponible from libros where cod_libro=:codigo" ) ) ){
							echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
						}elseif ( ! $query->bindParam( ":codigo", $codigo) ) { 
							echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
						}else{
							$query->execute();
							$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
							//SI HAY DISPONIBLES
							if(intval($resultado[0]["disponible"])>0){
								$hoy=date("Y-m-d");
								$devolucion=date("Y-m-d",strtotime($hoy."+ 15 days")); 
								if ( !( $query = $con->prepare( "INSERT INTO prestamos(cod_libro,fecha_salida,fecha_devolucion,dni) VALUES (:codigo,:hoy,:devolucion,:dni)" ) ) ){
									echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
								}elseif ( ! $query->bindParam( ":codigo", $codigo) ) { 
									echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
								}elseif ( ! $query->bindParam( ":hoy", $hoy) ) { 
									echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
								}elseif ( ! $query->bindParam( ":devolucion", $devolucion) ) { 
									echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
								}elseif ( ! $query->bindParam( ":dni", $dni) ) { 
									echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
								}else{
									$query->execute();
									if ( !( $query = $con->prepare( "update libros set disponible=disponible-1 where cod_libro=:codigo " ) ) ){
										echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
									}elseif ( ! $query->bindParam( ":codigo", $codigo) ) { 
										echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
									}else{
										$query->execute();
									}
									$respuesta="Se ha realizado su prestamo puede pasar a retirar el libro ";
									echo json_encode($respuesta);
								}			
							}else{
								//NO HAY DISPONIBLES
								//¿HAY RESERVADOS?
								
								if ( !( $query = $con->prepare( "select fecha from lista_espera where codigo=:codigo and id_reserva = (select max(id_reserva) from lista_espera where codigo=:codigo)" ) ) ){
									echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
								}elseif ( ! $query->bindParam( ":codigo", $codigo) ) { 
									echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
								}else{
									$query->execute();
									$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
									if(empty($resultado)){
										//NO
										//RESERVA CON FECHA FIN DEL PRIMER PRESTAMO
										//echo json_encode("NO HAY RESERVAS");
										
										if ( !( $query = $con->prepare( "select fecha_devolucion from prestamos where cod_libro=:codigo and id_prestamo = (select min(id_prestamo) from prestamos where cod_libro=:codigo)" ) ) ){
											echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
										}elseif ( ! $query->bindParam( ":codigo", $codigo) ) { 
											echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
										}else{
											$query->execute();
											$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
											$fecha=$resultado[0]["fecha_devolucion"];
											$respuesta = array();
											array_push($respuesta,"RESERVAR");
											array_push($respuesta,$fecha);
											echo json_encode($respuesta);
										}
										
									}else{
										//echo json_encode("HAY RESERVAS");
										//SI
									//SELECT DE LA FECHA DE LA ULTIMA RESERVA (FECHA Y COUNT)
									//$fecha = fecha de la ultima reserva
									//$nReservas = nº reservas para esa fecha
										if ( !( $query = $con->prepare( "select count(fecha) as reservas, fecha from lista_espera
																		where codigo=:codigo
																		and fecha = (select fecha from lista_espera 
																					where id_reserva = (select max(id_reserva) from lista_espera where  codigo=:codigo))" ) ) ){
											echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
										}elseif ( ! $query->bindParam( ":codigo", $codigo) ) { 
											echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
										}else{
											$query->execute();
											$resultado=$query ->fetchAll(PDO::FETCH_ASSOC);
											$nReservas=$resultado[0]["reservas"];
											$fechaUltimaR=$resultado[0]["fecha"];
											//¿HAY MAS PRESTAMOS QUE ACABEN EN ESA FECHA?
											
											if ( !( $query = $con->prepare( "select count(fecha_devolucion) as prestamos from prestamos
																		where fecha_devolucion=:fecha
																		and cod_libro = :codigo" ) ) ){
												echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
											}elseif ( ! $query->bindParam( ":fecha", $fechaUltimaR) ) { 
												echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
											}elseif ( ! $query->bindParam( ":codigo", $codigo) ) { 
												echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
											}else{
												$query->execute();
												$resultado=$query ->fetchAll(PDO::FETCH_ASSOC);
												$nPrestamos=$resultado[0]["prestamos"];
												
												if($nPrestamos > $nReservas){
													//SI
													//HACE UNA RESERVA PARA ESA FECHA
													$respuesta = array();
													array_push($respuesta,"RESERVAR");
													array_push($respuesta,$fechaUltimaR);
													echo json_encode($respuesta);
												}else{
													//NO
													//json_encode("peinese señor cuesta, peeeeinese");
													if ( !( $query = $con->prepare( "select fecha_devolucion from prestamos
																		where cod_libro=:codigo
																		and id_prestamo = (select min(id_prestamo) from prestamos where fecha_devolucion > :fecha and cod_libro=:codigo)" ) ) ){
														echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
													}elseif ( ! $query->bindParam( ":fecha", $fechaUltimaR) ) { 
														echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
													}elseif ( ! $query->bindParam( ":codigo", $codigo) ) { 
														echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
													}else{
														$query->execute();
														$resultado=$query ->fetchAll(PDO::FETCH_ASSOC);
														$fecha=$resultado[0]["fecha_devolucion"];
														$respuesta = array();
														array_push($respuesta,"RESERVAR");
														array_push($respuesta,$fecha);
														echo json_encode($respuesta);
													}
													
													
													//¿HAY ALGUN PRESTAMO QUE ACABE MAS TARDE?
														//SI
														//HACE UNA RESERVA PARA ESA FECHA
														
														//NO
														//COGE LA FECHA DE LA ULTIMA RESERVA . +15 DIAS Y HACER RESERVA CON ESA FECHA
												}
												
											
											}
										
										}
									
									}
									
									
									
									
								}
									
								/*if ( !( $query = $con->prepare( "select count(l1.dni), l2.fecha from lista_espera l1 
																join lista_espera l2 
																on l2.codigo = l1.codigo
																where l1.codigo=:codigo and l2.id_reserva=(select max(id_reserva) from lista_espera where codigo=:codigo)" ) ) ){
									echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
								}elseif ( ! $query->bindParam( ":codigo", $codigo) ) { 
									echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
								}else{
									$query->execute();
									$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
									if($resultado[0]["count(l1.dni)"] == 0){
										
									}else{
										echo json_encode($resultado);
									}
								}*/		
							}
						}
						}else{
							echo json_encode("RESERVADO");
						}
					}
				
					
				}else{
					echo json_encode("PRESTADO");
				}
			}
		}else{
			$respuesta = "SANCIONADO";
			echo json_encode("SANCIONADO");
		}
		//echo json_encode($respuesta);
	}
	
	
}
function reservarLibro($dni,$codigo,$fecha){
	$con= ConectaBD::getInstance();
	if ( !( $query = $con->prepare( "INSERT INTO lista_espera(dni,codigo,fecha) VALUES (:dni,:codigo,:fecha)" ) ) ){
		echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
	}elseif ( ! $query->bindParam( ":dni", $dni) ) { 
		echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
	}elseif ( ! $query->bindParam( ":codigo", $codigo) ) { 
		echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
	}elseif ( ! $query->bindParam( ":fecha", $fecha) ) { 
		echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
	}else{
		$query->execute();
		$filas = $query->rowCount();
		if($filas==0){
			echo json_encode("No se ha podido reservar");
		}else{
			echo json_encode("Has reservado el libro QUE EMOCIOOOOON");
		}
		
	}
}
?>