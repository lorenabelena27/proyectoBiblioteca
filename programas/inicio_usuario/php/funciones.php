<?php
//funcion para las ultimas publicaciones
function ultimasPublicaciones(){
	$con= ConectaBD::getInstance();
	//se mira el numero de libros de la tabla libros
	if ( !( $query = $con->prepare("SELECT count(*) as nLibros FROM Libros") ) ){
		echo "Falló la preparación: " . $Id->errno . " - " . $Id->error; 	
	}elseif ( ! $query->bindParam( ":dni", $dni) ) { 
		echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
	}else{
		$query->execute();
		$nLibros= $query ->fetchAll(PDO::FETCH_ASSOC);
		$nLibros=intval($nLibros[0]["nLibros"]);
	}
	//preparación para solo sacar 5 libros
	$empezar_desde=$nLibros-5;
	$tamanio=5;
	//se saca la información de los libros
	if ( !( $query = $con->prepare("select * from Libros  limit $empezar_desde,$tamanio") ) ){
		echo "Falló la preparación: " . $Id->errno . " - " . $Id->error; 	
	}elseif ( ! $query->bindParam( ":dni", $dni) ) { 
		echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
	}else{
		$query->execute();
		$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($resultado);
		
	}
}
//funcion para los libros recomendados
function librosRecomendados($dni){
	//saca los libros recomendados segun las categorias que tiene en prestamos y libro de espera
	if(isset($_SESSION["recomendados"])){
		echo json_encode($_SESSION["recomendados"]);
	}else{
		$con= ConectaBD::getInstance();
		if ( !( $query = $con->prepare("SELECT DISTINCT l.genero FROM Libros l
										join Prestamos p on p.cod_libro = l.cod_libro
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
		if ( !( $query = $con->prepare("SELECT DISTINCT l.genero FROM Libros l
										join Lista_espera li on li.codigo = l.cod_libro
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
					//se crea un libro aleatorio cada vez de cada genero
					$genero=$generos[$i];
					$libroAleatorio=libroAleatorio($genero);
					array_push($libros,$libroAleatorio);
				}
			}elseif(sizeof($generos)>5){
				$generosElegidos=array();
				for($i=0;$i<5;$i++){
					//se eligen generos distinto ya que hay mas de 5
					do{
						$gen=rand(0,(sizeof($generos)-1));
					//se realiza la  accion mientras el genero ya este en el array $generosElegidos
					}while(in_array($generos[$gen],$generosElegidos));
					$genero=$generos[$gen];
					array_push($generosElegidos,$genero);
					$libroAleatorio=libroAleatorio($genero);
					array_push($libros,$libroAleatorio);
				}
			}elseif(sizeof($generos)<5){
				//literatura, ciencia fic, infantil
				for($i=0;$i<5;$i++){
					//si hay dos generos se saca un libro de cada genero
					if($i<sizeof($generos)){
						$gen=$i;
						$genero=$generos[$i];
					}else{
						//los demas son aleatorios
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
					//se repite	la accion mientras el libro no sea valido y el numero de ese genero elegido sea menor que el numero de libros de ese genero 
					//que hay en la base de datos
					}while(!libroValido($libroAleatorio,$libros) && ($nLibrosGenElegidos<numLibrosGen($genero)));
					if(libroValido($libroAleatorio,$libros)){
						array_push($libros,$libroAleatorio);
					}
					
				}
			}
			$_SESSION["recomendados"]=$libros;
			echo json_encode($libros);
		}else{
			//select para tendencias cuaando el usuario no tiene ningun libro reservado , ni en prestamo se muestran los mas prestados
			if ( !( $query = $con->prepare("SELECT count(l.cod_libro), l.* FROM Libros l
											join Prestamos p on l.cod_libro=p.cod_libro
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
//funcion para obtener un libro aleatorio
function libroAleatorio($genero){
	$con= ConectaBD::getInstance();
	if ( !( $query = $con->prepare("SELECT * FROM Libros
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
//se comprueba que el libro no esta en el array
function libroValido($libro,$arrayLibros){
	$valido=true;
	for($i=0;$i<sizeof($arrayLibros);$i++){
		if(strcmp($arrayLibros[$i]["cod_libro"],$libro["cod_libro"])==0){
			$valido=false;
		}
	}
	return $valido;
}
//funcion que cuenta los libros segun el genero
function numLibrosGen($genero){
	$con= ConectaBD::getInstance();
	if ( !( $query = $con->prepare("SELECT count(*) as nLibros FROM Libros where genero=:genero") ) ){
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