<!DOCTYPE html>
<html lang="es">

<head>
		<title>Bibliofa</title>
		<meta charset="UTF-8">
		<meta name="description" content="Bibliofa">
		<meta name="author" content="Lorena">
		<link rel="stylesheet" type="text/css" href ='css/normalize.css'>
		<link rel="stylesheet" type="text/css" href ='css/estilo.css'>
		<link rel="stylesheet" type="text/css" href ='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css'>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
		<?php if ($_SERVER['QUERY_STRING'] == "alta") echo "<link rel=\"stylesheet\" type=\"text/css\" href =\"css/estilofor.css\">";?>
		<?php if ($_SERVER['QUERY_STRING'] == "alta") echo "<script type=\"text/javascript\" src = \"programas/alta/js/alta.js\"></script>";?>
		<?php if (isset($_SESSION["usuario"])) echo "<script type=\"text/javascript\" src = \"programas/js_comun/funciones.js\"></script>";?>
		<?php if (!isset($_SESSION["usuario"])) echo "<script type=\"text/javascript\" src = \"programas/acceso/js/acceso.js\"></script>";?>
		<?php if (isset($_SESSION["usuario"])) echo "<link rel=\"stylesheet\" type=\"text/css\" href =\"css/estilomenu.css\">";?>
		<?php if (isset($_SESSION["usuario"])) echo "<link rel=\"stylesheet\" type=\"text/css\" href =\"css/estilolistado.css\">";?>
		<?php if ($_SERVER['QUERY_STRING'] == "listarLibros") echo "<script type=\"text/javascript\" src = \"programas/listar_libros/js/listar_libros.js\"></script>";?>
		<?php if (isset($_SESSION["usuario"])) echo "<script type=\"text/javascript\" src = \"programas/busqueda/js/busqueda.js\"></script>";?>
		<?php if (isset($_SESSION["usuario"])) echo "<script type=\"text/javascript\" src = \"programas/reserva/js/reserva.js\"></script>";?>
</head>		
	
	<body>