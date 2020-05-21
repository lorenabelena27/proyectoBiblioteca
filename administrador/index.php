<?php 
	session_start();
	include "Comun/head.inc"; ?>
<body>
<?php

include "Comun/cabecera.inc";

if(isset($_SESSION['dni'])){
	include "vista_admin/menu.inc";
	if (!$_SERVER['QUERY_STRING']) {
		include "vista_admin/inicio_admin.inc";
	}else{
		include "Comun/formulario.inc";
	}
	if ($_SERVER['QUERY_STRING'] == "cerrar") {
		unset($_SESSION['dni']);
		session_destroy();
		header("Location: index.php");
	}
}else{
	if (!$_SERVER['QUERY_STRING']) {
		include "Comun/cuerpo.inc";
	}else{
		header('Location: index.php');
	}
}


include "Comun/footer.inc";
?>
</body>
</html>
