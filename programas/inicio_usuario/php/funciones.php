<?php
function ultimasPublicaciones(){
	$con= ConectaBD::getInstance();
	if ( !( $query = $con->prepare("SELECT count(*) as nLibros FROM libros") ) ){
		echo "Falló la preparación: " . $Id->errno . " - " . $Id->error; 	
	}elseif ( ! $query->bindParam( ":dni", $dni) ) { 
		echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
	}else{
		$query->execute();
		$nLibros= $query ->fetchAll(PDO::FETCH_ASSOC);
		$nLibros=intval($nLibros[0]["nLibros"]);
	}
	$empezar_desde=$nLibros-5;
	$tamanio=5;
	if ( !( $query = $con->prepare("select * from libros  limit $empezar_desde,$tamanio") ) ){
		echo "Falló la preparación: " . $Id->errno . " - " . $Id->error; 	
	}elseif ( ! $query->bindParam( ":dni", $dni) ) { 
		echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
	}else{
		$query->execute();
		$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($resultado);
		
	}
}

function librosRecomendados($dni){
	if(isset($_SESSION["recomendados"])){
		echo json_encode($_SESSION["recomendados"]);
	}else{
		$con= ConectaBD::getInstance();
		if ( !( $query = $con->prepare("SELECT DISTINCT l.genero FROM libros l
										join prestamos p on p.cod_libro = l.cod_libro
										where p.dni = :dni") ) ){
			echo "Falló la preparación: " . $Id->errno . " - " . $Id->error; 	
		}elseif ( ! $query->bindParam( ":dni", $dni) ) { 
			echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
		}else{
			$query->execute();
			$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
			$generos=array();
			if(!empty($resultado)){
				for($i=0;$i<sizeof($resultado);$i++){
					array_push($generos,$resultado[$i]["genero"]);
				}
			}
		}
		if ( !( $query = $con->prepare("SELECT DISTINCT l.genero FROM libros l
										join lista_espera li on li.codigo = l.cod_libro
										where li.dni = :dni") ) ){
			echo "Falló la preparación: " . $Id->errno . " - " . $Id->error; 	
		}elseif ( ! $query->bindParam( ":dni", $dni) ) { 
			echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
		}else{
			$query->execute();
			$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
			if(!empty($resultado)){
				for($i=0;$i<sizeof($resultado);$i++){
					if(!in_array($resultado[$i]["genero"],$generos)){
						array_push($generos,$resultado[$i]["genero"]);
					}
					
				}
			}
		}
		if(!empty($generos)){
			$libros=array();
			//generos es un array con todos los generos que tiene en prestamos o en lista de espera
			if(sizeof($generos)==5){
				for($i=0;$i<5;$i++){
					$genero=$generos[$i];
					$libroAleatorio=libroAleatorio($genero);
					array_push($libros,$libroAleatorio);
				}
			}elseif(sizeof($generos)>5){
				$generosElegidos=array();
				for($i=0;$i<5;$i++){
					do{
						$gen=rand(0,(sizeof($generos)-1));
					}while(in_array($generos[$gen],$generosElegidos));
					$genero=$generos[$gen];
					array_push($generosElegidos,$genero);
					$libroAleatorio=libroAleatorio($genero);
					array_push($libros,$libroAleatorio);
				}
			}elseif(sizeof($generos)<5){
				//literatura, ciencia fic, infantil
				for($i=0;$i<5;$i++){
					if($i<sizeof($generos)){//0<4 1<4 2<4 3<4 4<4 
						$gen=$i;
						$genero=$generos[$i];
					}else{
						$gen=rand(0,(sizeof($generos)-1));
						$genero=$generos[$gen];
					}
					do{
						$libroAleatorio=libroAleatorio($genero);
						$nLibrosGenElegidos=0;
						for($i=0;$i<sizeof($libros);$i++){
							if(strcmp($libros[$i]["genero"],$generos[$gen])==0){
								$nLibrosGenElegidos++;
							}
						}
						//echo json_encode($libroValido);
					}while(!libroValido($libroAleatorio,$libros) && ($nLibrosGenElegidos<numLibrosGen($genero)));
					if(libroValido($libroAleatorio,$libros)){
						array_push($libros,$libroAleatorio);
					}
					
				}
			}
			$_SESSION["recomendados"]=$libros;
			echo json_encode($libros);
		}else{
			
			if ( !( $query = $con->prepare("SELECT count(l.cod_libro), l.* FROM libros l
											join prestamos p on l.cod_libro=p.cod_libro
											group by l.cod_libro
											order by count(l.cod_libro) desc
											limit 5") ) ){
				echo "Falló la preparación: " . $Id->errno . " - " . $Id->error; 	
			}elseif ( ! $query->bindParam( ":dni", $dni) ) { 
				echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
			}else{
				$query->execute();
				$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
				$respuesta=array();
				array_push($respuesta,"TENDENCIAS");
				array_push($respuesta,$resultado);
				echo json_encode($respuesta);
				
			}
		}
	}
	
}

function libroAleatorio($genero){
	$con= ConectaBD::getInstance();
	if ( !( $query = $con->prepare("SELECT * FROM libros
									where genero = :genero
									ORDER BY RAND() LIMIT 1") ) ){
		echo "Falló la preparación: " . $Id->errno . " - " . $Id->error; 	
	}elseif ( ! $query->bindParam( ":genero", $genero) ) { 
		echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
	}else{
		$query->execute();
		$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
		return($resultado[0]);
	}
}

function libroValido($libro,$arrayLibros){
	$valido=true;
	for($i=0;$i<sizeof($arrayLibros);$i++){
		if(strcmp($arrayLibros[$i]["cod_libro"],$libro["cod_libro"])==0){
			$valido=false;
		}
	}
	return $valido;
}

function numLibrosGen($genero){
	$con= ConectaBD::getInstance();
	if ( !( $query = $con->prepare("SELECT count(*) as nLibros FROM libros where genero=:genero") ) ){
		echo "Falló la preparación: " . $Id->errno . " - " . $Id->error; 	
	}elseif ( ! $query->bindParam( ":dni", $dni) ) { 
		echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
	}else{
		$query->execute();
		$nLibros= $query ->fetchAll(PDO::FETCH_ASSOC);
		$nLibros=intval($nLibros[0]["nLibros"]);
	}
}
?>