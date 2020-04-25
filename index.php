<?php 
	session_start();
	include "Comun/head.inc"; ?>
<body>
<?php

include "Comun/cabecera.inc";
if(isset($_SESSION['usuario'])){
	include "Inicio/menu.inc";
	if ($_SERVER['QUERY_STRING'] == "cerrar") {
		unset($_SESSION['usuario']);
		session_destroy();
		header("Location: index.php");
	}elseif ($_SERVER['QUERY_STRING'] == "busqueda") {
		include "programas/listar_libros/listar_libros.inc";
	}elseif ($_SERVER['QUERY_STRING'] == "listarLibros") {
		include "programas/listar_libros/listar_libros.inc";
	}elseif ($_SERVER['QUERY_STRING'] == "reserva") {

	}else{
		include "Inicio/cuerpoUsuario.inc";
	}
	include "Comun/busqueda.inc";
}else{
	if (!$_SERVER['QUERY_STRING']) {
		include "Inicio/cuerpo.inc";
	}elseif ($_SERVER['QUERY_STRING'] == "alta") {
		include "programas/alta/alta.inc";
	}
}


include "Comun/footer.inc";
?>
</body>
</html>