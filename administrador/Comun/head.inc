<!DOCTYPE html>
<html lang="es">

<head>
		<title>Bibliofa</title>
		<meta charset="UTF-8">
		<meta name="description" content="Bibliofa">
		<meta name="author" content="Lorena">
		<link rel="stylesheet" type="text/css" href ='css/normalize.css'>
		<link rel="stylesheet" type="text/css" href ='css/estilo.css'>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
		<?php if (!isset($_SESSION["dni"])) echo "<script  src = \"programas/acceso/js/acceso.js\"></script>";?>
		<?php if (isset($_SESSION["dni"])) echo "<link rel=\"stylesheet\" type=\"text/css\" href ='css/estiloInicioAdmin.css'>";?>
		<?php if (isset($_SESSION["dni"])) echo "<link rel=\"stylesheet\" type=\"text/css\" href ='css/estiloMenu.css'>";?>
		<?php if (!(!$_SERVER['QUERY_STRING'])) echo "<link rel=\"stylesheet\" type=\"text/css\" href ='css/estiloFormulario.css'>";?>
		<?php if ($_SERVER['QUERY_STRING']=="devolver") echo "<script  src = \"programas/devolver/js/devolver.js\"></script>";?>
		<?php if ($_SERVER['QUERY_STRING']=="prestar") echo "<script  src = \"programas/prestar/js/pedir.js\"></script>";?>
</head>		
	
