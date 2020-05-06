<?php
include_once 'ConectaBD.php';

function numLibros(){
	$con= ConectaBD::getInstance();
	if ( !( $query = $con->prepare("select count(*) from libros ") ) ){
		echo "Falló la preparación: " . $Id->errno . " - " . $Id->error; 	
	}else{
		$query->execute();
		$row = $query->fetchAll(PDO::FETCH_ASSOC);
		$num_filas= $row[0]["count(*)"];
		$seleccionado=rand(1,$num_filas);
		$cod="E".$seleccionado;
		if ( !( $query = $con->prepare("select * from libros where cod_libro=:cod ") ) ){
			echo "Falló la preparación: " . $Id->errno . " - " . $Id->error; 	
		}elseif ( ! $query->bindParam( ":cod", $cod) ) { 
			echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
		}else{
			$query->execute();
			$resultado = $query->fetchAll(PDO::FETCH_ASSOC);
			$libro= $resultado[0];
			return $libro;
		}
	}
}

$libro=new ArrayObject(numLibros());
$fichero = '../../vista_usuario/libro_semana.inc';
$archivo = fopen($fichero,'w+');
fwrite($archivo, "");
fclose($archivo);
$titulo=$libro["titulo"];
$prueba=$libro["img"];
$resumen=$libro["descripcion"];
$texto="<?php \$titulo=\"".$titulo."\";\$prueba=\"".$prueba."\";\$resumen=\"".$resumen."\";";
$fichero = '../../vista_usuario/libro_semana.inc';
$archivo = fopen($fichero,'w+');
fwrite($archivo, $texto);

?>