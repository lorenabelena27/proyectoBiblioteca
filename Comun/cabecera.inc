<div class="wrapper" id="hoja">
    <div class="bloque0">
		<div class = "titulo_pag">
		<h1>Bibliofa</h1>
		</div>
		<?php if(!isset($_SESSION["usuario"])){?>
		<div class="formulario">
			<form action="<?php echo $_SERVER['PHP_SELF'];?> method="POST">
				<label  for="id_email1">E-mail: </label><input type="text" id="id_email1" name="email"/> 
				<label  for="id_pass1">Contraseña: </label><input type="password" id="id_pass1" name="pass"/><br> 
				<input type="button" name="entrar" id="id_enviar1"value="Entrar"/>  
			</form>	
			<p id="error_email1"></p>
			<p id="error_pass1"></p>
			<br>
			<p>¿No te has registrado? <a href="?alta">Registrate</a></p>	
		</div>
	<?php }else{
			?> <div class="formulario"> 
			<h2>	<?php echo "Bienvenido ".$_SESSION['usuario'] ;?></h2>
				<span><span> </span><a href="index.php?cerrar"> Cerrar sesión</a></span>
			</div>
			<?php }?>
    </div>