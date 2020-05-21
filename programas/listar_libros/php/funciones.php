<?php
//funcion para a paginacion
function paginacion($tamanio){
	$con= ConectaBD::getInstance();
	$res = array();	
	//cuenta los libros de la base de datos
	if ( !( $query = $con->prepare("select count(*) from Libros ") ) ){
		echo "Falló la preparación: " . $Id->errno . " - " . $Id->error; 	
	}else{
		$query->execute();
		$row = $query->fetchAll(PDO::FETCH_ASSOC);
		$num_filas= $row[0]["count(*)"]; 
		array_push($res, $num_filas);
		$empezar_desde=0;
		//se selecionan los libros poniendo un limite de que enpieza en 0 hasta 12
		if ( !( $query = $con->prepare("select * from Libros  limit $empezar_desde,$tamanio") ) ){
			echo "Falló la preparación: " . $Id->errno . " - " . $Id->error; 	
		}else{
			$query->execute();
			$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
			for($i=0;$i<sizeof($resultado);$i++){
				foreach($resultado[$i] as $clave => $valor){
					$resultado[$i][$clave] =ucfirst($resultado[$i][$clave]);
				}
			}
			array_push($res,$resultado);
			echo json_encode($res);
		}
	}

}
//para la paginacion 
function pagina($pag,$tamanio){
	$con= ConectaBD::getInstance();
	$empezar_desde=($pag-1)*$tamanio;
		//se selecionan los libros poniendo un limite de que enpieza en 0 hasta 12	
		if ( !( $query = $con->prepare("select * from Libros  limit $empezar_desde,$tamanio") ) ){
			echo "Falló la preparación: " . $Id->errno . " - " . $Id->error; 	
		}else{
			$query->execute();
			$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
			echo json_encode($resultado);
		}
}
//funcion para las categorias
function categorias(){
	$con= ConectaBD::getInstance();
	//seleciona las categorias de forma ascendente
	if ( !( $query = $con->prepare("select distinct genero from Libros order by genero asc ") ) ){
		echo "Falló la preparación: " . $Id->errno . " - " . $Id->error; 	
	}else{
		$query->execute();
		$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($resultado);
	}
}
//funcion libros por categoria para el menu de filtros
function librosCT($categoria, $tamanio){
	
	$con= ConectaBD::getInstance();
	$res = array();		
	//cuenta los libros de la base de datos	por categoria del genero que le pasa js
	if ( !( $query = $con->prepare("select count(*) from Libros where genero = :categoria ") ) ){
		echo "Falló la preparación: " . $Id->errno . " - " . $Id->error; 	
	}elseif ( ! $query->bindParam( ":categoria", $categoria) ) { 
		echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
	}else{
		$query->execute();
		$row = $query->fetchAll(PDO::FETCH_ASSOC);
		$num_filas= $row[0]["count(*)"]; 
		array_push($res, $num_filas);
		$empezar_desde=0;
		//se selecionan los libros por categoria poniendo un limite de que enpieza en 0 hasta 12
		if ( !( $query = $con->prepare("select * from Libros where genero = :categoria limit $empezar_desde,$tamanio ") ) ){
			echo "Falló la preparación: " . $Id->errno . " - " . $Id->error; 	
		}elseif ( ! $query->bindParam( ":categoria", $categoria) ) { 
			echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
		}else{
			$query->execute();
			$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
			for($i=0;$i<sizeof($resultado);$i++){
				foreach($resultado[$i] as $clave => $valor){
					$resultado[$i][$clave] =ucfirst($resultado[$i][$clave]);
				}
			}
			array_push($res,$resultado);
			echo json_encode($res);
		}
	}
}
//funcion para la paginacion
function paginaF($pag,$tamanio, $categoria){
	$con= ConectaBD::getInstance();
	$empezar_desde=($pag-1)*$tamanio;		
		//se selecionan los libros por categoria poniendo un limite de que enpieza en 0 hasta 12
		if ( !( $query = $con->prepare("select * from libros where genero = :categoria limit $empezar_desde,$tamanio") ) ){
			echo "Falló la preparación: " . $Id->errno . " - " . $Id->error; 	
		}elseif ( ! $query->bindParam( ":categoria", $categoria) ) { 
			echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
		}else{
			$query->execute();
			$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
			echo json_encode($resultado);
		}
}
?>