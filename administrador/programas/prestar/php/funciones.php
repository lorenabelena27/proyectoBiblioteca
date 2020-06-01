<?php
//validación del dni
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
function pedir($dni,$codigo){
	$con= ConectaBD::getInstance();
	//comprobar que el usuario existe
	if ( !( $query = $con->prepare( "select * from Usuarios where dni=:dni") ) ){
		echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
	}elseif ( ! $query->bindParam( ":dni", $dni) ) { 
			echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
	}else{
		
		$query->execute();
		$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
		if(!empty($resultado)){
					//COMPROBAR SI EL USUARIO ESTA SANCIONADO
			if ( !( $query = $con->prepare( "select dni from Sancionados where dni=:dni" ) ) ){
				echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
			}elseif ( ! $query->bindParam( ":dni", $dni) ) { 
				echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
			}else{
				$query->execute();
				$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
				//SI NO ESTA SANCIONADO
				if(empty($resultado)){
					//COMPROBAR SI EL USUARIO TIENE EL LIBRO PRESTADO
					if ( !( $query = $con->prepare( "select dni,estado from Prestamos where dni=:dni and cod_libro=:cod_libro" ) ) ){
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
							if ( !( $query = $con->prepare( "select dni, estado from Lista_espera where dni=:dni and codigo=:cod_libro" ) ) ){
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
									if ( !( $query = $con->prepare( "select disponible from Libros where cod_libro=:codigo" ) ) ){
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
											
											$respuesta = array();
											array_push($respuesta,"PRESTAR");
											array_push($respuesta,$hoy);
											array_push($respuesta,$devolucion);
											array_push($respuesta,$dni);
											array_push($respuesta,$codigo);
											echo json_encode($respuesta);
					
										}else{
											//NO HAY DISPONIBLES
											//¿HAY RESERVADOS?
											
											if ( !( $query = $con->prepare( "select fecha from Lista_espera where codigo=:codigo and id_reserva = (select max(id_reserva) from Lista_espera where codigo=:codigo)" ) ) ){
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
													
													if ( !( $query = $con->prepare( "select fecha_devolucion from Prestamos where cod_libro=:codigo and id_prestamo = (select min(id_prestamo) from Prestamos where cod_libro=:codigo)" ) ) ){
														echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
													}elseif ( ! $query->bindParam( ":codigo", $codigo) ) { 
														echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
													}else{
														$query->execute();
														$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
														$fecha=$resultado[0]["fecha_devolucion"];
														
														//HECHO LO NUEVO
														
														$respuesta = array();
														array_push($respuesta,"RESERVAR");
														array_push($respuesta,$fecha);
														array_push($respuesta,$dni);
														array_push($respuesta,$codigo);
														echo json_encode($respuesta);
													}
													
												}else{
													//echo json_encode("HAY RESERVAS");
													//SI
												//SELECT DE LA FECHA DE LA ULTIMA RESERVA (FECHA Y COUNT)
												//$fecha = fecha de la ultima reserva
												//$nReservas = nº reservas para esa fecha
												
												//Y FECHA FIN
													if ( !( $query = $con->prepare( "select count(fecha) as reservas, fecha , fecha_fin from Lista_espera
																					where codigo=:codigo
																					and fecha = (select fecha from Lista_espera 
																								where id_reserva = (select max(id_reserva) from Lista_espera where  codigo=:codigo))" ) ) ){
														echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
													}elseif ( ! $query->bindParam( ":codigo", $codigo) ) { 
														echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
													}else{
														$query->execute();
														$resultado=$query ->fetchAll(PDO::FETCH_ASSOC);
														$nReservas=$resultado[0]["reservas"];
														$fechaUltimaR=$resultado[0]["fecha"];
														$fechaFinUltimaR=$resultado[0]["fecha_fin"];
														//$fecha fin = $resul HECHO
														//¿HAY MAS PRESTAMOS QUE ACABEN EN ESA FECHA?
														
														if ( !( $query = $con->prepare( "select count(fecha_devolucion) as prestamos from Prestamos
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
																//HECHO LO NUEVO
																array_push($respuesta,"RESERVAR");
																array_push($respuesta,$fechaUltimaR);
																array_push($respuesta,$dni);
																array_push($respuesta,$codigo);
																echo json_encode($respuesta);
															}else{
																//NO
																//¿HAY ALGUN PRESTAMO QUE ACABE MAS TARDE?
																
																if ( !( $query = $con->prepare( "select fecha_devolucion from Prestamos
																					where cod_libro=:codigo
																					and id_prestamo = (select min(id_prestamo) from Prestamos where fecha_devolucion > :fecha and cod_libro=:codigo)" ) ) ){
																	echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
																}elseif ( ! $query->bindParam( ":fecha", $fechaUltimaR) ) { 
																	echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
																}elseif ( ! $query->bindParam( ":codigo", $codigo) ) { 
																	echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
																}else{
																	$query->execute();
																	$resultado=$query ->fetchAll(PDO::FETCH_ASSOC);
																	if(!empty($resultado)){
																		//SI
																		//RESERVA PARA ESA FECHA
																		$fecha=$resultado[0]["fecha_devolucion"];
																		//HECHO LO NUEVO
																		$respuesta = array();
																		array_push($respuesta,"RESERVAR");
																		array_push($respuesta,$fecha);
																		array_push($respuesta,$dni);
																		array_push($respuesta,$codigo);
																		echo json_encode($respuesta);
																	}else{
																		//NO
																		//¿CUAL ES LA FECHA DE LA ULTIMA RESERVA Y CUANTAS HAY?
																		//$fechaUltimaR
																		//$nReservas
																		//$fechaFinUltimaR
																		//count reservas que acaban en fechaUltimaR
																		//MINIMA FECHA EN RESERVAS DE ULTIMA RESERVA fecha_fin > $fechaUltimaR y cuantas
																		if ( !( $query = $con->prepare( "select count(id_reserva) from Lista_espera where fecha_fin=:fechaUltimaR and codigo=:codigo" ) ) ){
																			echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
																		}elseif ( ! $query->bindParam( ":fechaUltimaR", $fechaUltimaR) ) { 
																			echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
																		}elseif ( ! $query->bindParam( ":codigo", $codigo) ) { 
																			echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
																		}else{
																			$query->execute();
																			$resultado=$query ->fetchAll(PDO::FETCH_ASSOC);
																			$numReservasFin= intval($resultado[0]["count(id_reserva)"]);
																			if($numReservasFin == 0){
																				//SI ES 0 SE REALIZA UNA RESERVA CON FECHA = FECHA FIN DE LA PRIMERA RESERVA
																				if ( !( $query = $con->prepare( "select fecha_fin from Lista_espera where 
																														id_reserva=(select min(id_reserva) from Lista_espera where codigo = :codigo)" ) ) ){
																					echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
																				}elseif ( ! $query->bindParam( ":codigo", $codigo) ) { 
																					echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
																				}else{
																					$query->execute();
																					$resultado=$query ->fetchAll(PDO::FETCH_ASSOC);
																					//echo json_encode($resultado);
																					$fecha=$resultado[0]["fecha_fin"];
																					$respuesta = array();
																					array_push($respuesta,"RESERVAR");
																					array_push($respuesta,$fecha);
																					array_push($respuesta,$dni);
																					array_push($respuesta,$codigo);
																					echo json_encode($respuesta);
																				}
																			}else{
																				//SI ES !=
																				$respuesta = array();
																				array_push($respuesta,"RESERVAR");
																				if($nReservas < $numReservasFin){
																					//SI $nReservas < $numReservasFin RESERVA CON FECHA = $fechaUltimaR
																					array_push($respuesta,$fechaUltimaR);
																					array_push($respuesta,$dni);
																					array_push($respuesta,$codigo);
																					echo json_encode($respuesta);
																				}else{
																					//NO --> RESERVA CON FECHA = FECHA FIN ULTIMA RESERVA
																					if ( !( $query = $con->prepare( "select min(fecha_fin) from Lista_espera where 
																														fecha_fin >:fechaUltimaR and codigo=:codigo" ) ) ){
																						echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
																					}elseif ( ! $query->bindParam( ":fechaUltimaR", $fechaUltimaR) ) { 
																						echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
																					}elseif ( ! $query->bindParam( ":codigo", $codigo) ) { 
																						echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
																					}else{
																						$query->execute();
																						$resultado=$query ->fetchAll(PDO::FETCH_ASSOC);
																						$fecha=$resultado[0]["min(fecha_fin)"];
																						array_push($respuesta,$fecha);
																						array_push($respuesta,$dni);
																						array_push($respuesta,$codigo);
																						echo json_encode($respuesta);
																					}
																				}
																			}
																		}
																	}
																}
															}
														}
													}
												}	
											}
										}
									}
								}else{
									echo json_encode("RESERVADO");
								}
							}
						}else{
							$estado=intval($resultado[0]["estado"]);
							if($estado==0){
								if ( !( $query = $con->prepare( "update Prestamos set estado=1 where cod_libro=:codigo and dni=:dni " ) ) ){
										echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
									}elseif ( ! $query->bindParam( ":dni", $dni) ) { 
											echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
									}elseif ( ! $query->bindParam( ":codigo", $codigo) ) { 
											echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
									}else{
										$query->execute();
									}
							}else{
								echo json_encode("PRESTADO");
							}
						}
					}
				}else{
					$respuesta = "SANCIONADO";
					echo json_encode("SANCIONADO");
				}
			}
		}else{
			echo json_encode("NOUSUARIO");
		}
	}
}
//funcion que reserva un libro
function reservarLibro($dni,$codigo,$fecha){
	$con= ConectaBD::getInstance();
	$fecha_fin = date("Y-m-d",strtotime($fecha."+ 15 days"));
	//inseta la peticion en lista de espera
	if ( !( $query = $con->prepare( "INSERT INTO Lista_espera(dni,codigo,fecha,fecha_fin) VALUES (:dni,:codigo,:fecha,:fecha_fin)" ) ) ){
		echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
	}elseif ( ! $query->bindParam( ":dni", $dni) ) { 
		echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
	}elseif ( ! $query->bindParam( ":codigo", $codigo) ) { 
		echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
	}elseif ( ! $query->bindParam( ":fecha", $fecha) ) { 
		echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
	}elseif ( ! $query->bindParam( ":fecha_fin", $fecha_fin) ) { 
		echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
	}else{
		$query->execute();
		$filas = $query->rowCount();
		if($filas==0){
			echo json_encode("No se ha podido reservar");
		}else{
			echo json_encode($fecha);
		}
		
	}
}
//funcion para prestar un libro
function prestarLibro($dni,$codigo,$fechaIni,$fechaFin){
	$con= ConectaBD::getInstance();
	//inserta el libro en Prestamos
	if ( !( $query = $con->prepare( "INSERT INTO Prestamos(cod_libro,fecha_salida,fecha_devolucion,dni,estado) VALUES (:codigo,:fechaIni,:fechaFin,:dni, 1)" ) ) ){
		echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
	}elseif ( ! $query->bindParam( ":codigo", $codigo) ) { 
		echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
	}elseif ( ! $query->bindParam( ":fechaIni", $fechaIni) ) { 
		echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
	}elseif ( ! $query->bindParam( ":fechaFin", $fechaFin) ) { 
		echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
	}elseif ( ! $query->bindParam( ":dni", $dni) ) { 
		echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
	}else{
		$query->execute();
		//resta uno a disponibles en la tabla libros
		if ( !( $query = $con->prepare( "update Libros set disponible=disponible-1 where cod_libro=:codigo " ) ) ){
			echo "Falló la preparacioón: " . $con->errno . " - " . $con->error; 
		}elseif ( ! $query->bindParam( ":codigo", $codigo) ) { 
			echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
		}else{
			$query->execute();
		}
		$respuesta="Se ha realizado su prestamo puede pasar a retirar el libro ";
		echo json_encode($respuesta);
	}
}
?>