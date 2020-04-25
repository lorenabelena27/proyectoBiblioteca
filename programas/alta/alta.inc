
<div id="prueba">
<form action="<?php echo $_SERVER['PHP_SELF'];?> method="POST">
	<label  for="id_nombre" >Nombre: </label>
	<input type="text" name="nombre" id="id_nombre" value=""/><br><br>
	<div  id="div_error_nombre" class="">
		<p id="error_nombre"></p>
	</div>
	<label for="id_apellidos">Apellidos: </label>
	<input type="text" name="apellidos"id="id_apellidos" value=""/><br> <br>
	<div id="div_error_apellidos" class="">
		<p id="error_apellidos"></p>
	</div>
	<label for="id_dni">DNI: </label>
	<input type="text" name="dni" id="id_dni" value=""/><br> <br>
	<div id="div_error_dni"class="">
		<p id="error_dni"></p>
	</div>
	<label for="id_email">Email: </label>
	
	<input type="email" name="email"  id="id_email" value=""/><br> <br>
	<div id="div_error_email"class="">
		<p id="error_email"></p>
	</div>
	<label for="id_nacimiento">Fecha de nacimiento: </label>
	<input type="date" name="nacimiento" id="id_nacimiento" value=""/><br> <br>
	<div id="div_error_fecha"class="">
		<p id="error_fecha"></p>
	</div>
	<label for="id_pass">Contraseña: </label>
	<input type="password" name="pass"  id="id_pass"/><br><br> 
	<div id="div_error_pass"class="error">
		<p id="error_pass">La contraseña debe tener al menos 8 caracteres , un dígito, minúsculas y al menos una mayúscula.</p>
	</div>
	<label for="id_confir">Confirmar contraseña: </label>
	<input type="password" name="confir"  id="id_confir"/><br><br> 
	<div id="div_error_confir"class="">
		<p id="error_confir"></p>
	</div>
	<input type="button" name="enviar" id="id_enviar" value="Enviar"/>  
</form>
</div>