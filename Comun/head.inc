<!DOCTYPE html>
<html lang="es">

<head>
		<title>Bibliofa</title>
		<meta charset="UTF-8">
		<meta name="description" content="Bibliofa">
		<meta name="author" content="Lorena">
		<link rel="stylesheet" type="text/css" href ='css/normalize.css'>
		<link rel="stylesheet" type="text/css" href ='css/estilo.css'>
		<?php if (!isset($_SESSION["usuario"])) echo "<script  src = \"programas/acceso/js/acceso.js\"></script>";?>
		<link rel="stylesheet" type="text/css" href ='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css'>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
		<?php if ($_SERVER['QUERY_STRING'] == "alta") echo "<link rel=\"stylesheet\" type=\"text/css\" href =\"css/estiloFormulario.css\">";?>
		<?php if ($_SERVER['QUERY_STRING'] == "recuperar") echo "<link rel=\"stylesheet\" type=\"text/css\" href =\"css/estiloFormulario.css\">";?>
		<?php if ($_SERVER['QUERY_STRING'] == "recuperar") echo "<script  src = \"programas/recuperar/js/recuperar.js\"></script>";?>
		<?php if ($_SERVER['QUERY_STRING'] == "alta") echo "<script  src = \"programas/alta/js/alta.js\"></script>";?>
		<?php if (isset($_SESSION["usuario"])) echo "<link rel=\"stylesheet\" type=\"text/css\" href =\"css/estiloMenu.css\">";?>
		<?php if (isset($_SESSION["usuario"])) echo "<link rel=\"stylesheet\" type=\"text/css\" href =\"css/estiloListado.css\">";?>
		<?php if (isset($_SESSION["usuario"])) echo "<link rel=\"stylesheet\" type=\"text/css\" href =\"css/estiloFichaTecnica.css\">";?>
		<?php if (isset($_SESSION["usuario"])) if (! $_SERVER['QUERY_STRING']) echo "<script  src = \"programas/inicio_usuario/js/inicio_usuario.js\"></script>";?>
		<?php if (isset($_SESSION["usuario"])) if (! $_SERVER['QUERY_STRING']) echo "<link rel=\"stylesheet\" type=\"text/css\" href =\"css/estiloInicioUsuario.css\">";?>
		<?php if ($_SERVER['QUERY_STRING'] != "listarLibros") echo "<link rel=\"stylesheet\" type=\"text/css\" href =\"css/estiloBuscar356px.css\">";?>
		<?php if ($_SERVER['QUERY_STRING'] == "listarLibros") echo "<script  src = \"programas/listar_libros/js/listar_libros.js\"></script>";?>
		<?php if ($_SERVER['QUERY_STRING']  == "cambio") echo "<link rel=\"stylesheet\" type=\"text/css\" href =\"css/estiloFormulario.css\">";?>
		<?php if ($_SERVER['QUERY_STRING'] == "cambio") echo "<script  src = \"programas/cambio/js/cambio.js\"></script>";?>
		<?php if ($_SERVER['QUERY_STRING'] == "misLibros") echo "<script  src = \"programas/mis_libros/js/mis_libros.js\"></script>";?>
		<?php if ($_SERVER['QUERY_STRING'] == "misLibros") echo "<link rel=\"stylesheet\" type=\"text/css\" href =\"css/estiloMisLibros.css\">";?>
		<?php if (isset($_SESSION["usuario"])) echo "<script  src = \"programas/busqueda/js/busqueda.js\"></script>";?>
		<?php if (isset($_SESSION["usuario"])) echo "<script  src = \"programas/reserva/js/reserva.js\"></script>";?>
</head>		
	
