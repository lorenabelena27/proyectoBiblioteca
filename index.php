<?php 
	session_start();
	include "Comun/head.inc"; ?>
<body>
<?php

include "Comun/cabecera.inc";
if(isset($_SESSION['usuario'])){
	include "vista_usuario/menu.inc";
	if ($_SERVER['QUERY_STRING'] == "cerrar") {
		unset($_SESSION['usuario']);
		session_destroy();
		header("Location: index.php");
	}elseif ($_SERVER['QUERY_STRING'] == "busqueda") {
		include "programas/listar_libros/listar_libros.inc";
	}elseif ($_SERVER['QUERY_STRING'] == "listarLibros") {
		include "programas/listar_libros/listar_libros.inc";
	}elseif ($_SERVER['QUERY_STRING'] == "misLibros") {
		include "programas/mis_libros/mis_libros.inc";
	}elseif ($_SERVER['QUERY_STRING'] == "cambio") {
		include "programas/cambio/cambio.inc";
	}else{
		include "vista_usuario/cuerpoUsuario.inc";
	}
	include "Comun/busqueda.inc";
}else{
	if (!$_SERVER['QUERY_STRING']) {
		include "Comun/cuerpo.inc";
	}elseif ($_SERVER['QUERY_STRING'] == "alta") {
		include "programas/alta/alta.inc";
	}elseif ($_SERVER['QUERY_STRING'] == "recuperar") {
		include "programas/recuperar/recuperar.inc";
	}
}


include "Comun/footer.inc";
?>
</body>
</html>