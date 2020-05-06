<?php

function paginacion($tamanio){
	$con= ConectaBD::getInstance();
	$res = array();		
	if ( !( $query = $con->prepare("select count(*) from libros ") ) ){
		echo "Falló la preparación: " . $Id->errno . " - " . $Id->error; 	
	}else{
		$query->execute();
		$row = $query->fetchAll(PDO::FETCH_ASSOC);
		$num_filas= $row[0]["count(*)"]; 
		array_push($res, $num_filas);
		$empezar_desde=0;
		if ( !( $query = $con->prepare("select * from libros  limit $empezar_desde,$tamanio") ) ){
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

function pagina($pag,$tamanio){
	$con= ConectaBD::getInstance();
	$empezar_desde=($pag-1)*$tamanio;				
		if ( !( $query = $con->prepare("select * from libros  limit $empezar_desde,$tamanio") ) ){
			echo "Falló la preparación: " . $Id->errno . " - " . $Id->error; 	
		}else{
			$query->execute();
			$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
			echo json_encode($resultado);
		}
}
function categorias(){
	$con= ConectaBD::getInstance();
	if ( !( $query = $con->prepare("select distinct genero from libros order by genero asc ") ) ){
		echo "Falló la preparación: " . $Id->errno . " - " . $Id->error; 	
	}else{
		$query->execute();
		$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($resultado);
	}
}
function librosCT($categoria, $tamanio){
	
	$con= ConectaBD::getInstance();
	$res = array();		
	if ( !( $query = $con->prepare("select count(*) from libros where genero = :categoria ") ) ){
		echo "Falló la preparación: " . $Id->errno . " - " . $Id->error; 	
	}elseif ( ! $query->bindParam( ":categoria", $categoria) ) { 
		echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
	}else{
		$query->execute();
		$row = $query->fetchAll(PDO::FETCH_ASSOC);
		$num_filas= $row[0]["count(*)"]; 
		array_push($res, $num_filas);
		$empezar_desde=0;
		if ( !( $query = $con->prepare("select * from libros where genero = :categoria limit $empezar_desde,$tamanio ") ) ){
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

function paginaF($pag,$tamanio, $categoria){
	$con= ConectaBD::getInstance();
	$empezar_desde=($pag-1)*$tamanio;				
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