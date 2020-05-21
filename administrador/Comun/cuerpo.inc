<?php if(!isset($_SESSION["dni"])){?>
		<div class="formulario">
			<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST">
				<label  for="id_email1">Nº Trabajador: </label><input class="inputAcc" type="text" id="id_trabajador" name="email"/>
				<label  for="id_pass1">Contraseña: </label><input class="inputAcc" type="password" id="id_pass1" name="pass"/><br> 
				<input class="botonEntrar" type="button" name="entrar" id="id_enviar1" value="Entrar"/>  
			</form>	
			<p id="error_trabajador"></p>
			<p id="error_pass1"></p>
			<br>
		</div>
	<?php }?>