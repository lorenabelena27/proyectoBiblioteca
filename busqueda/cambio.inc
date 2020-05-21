<div id="formuCambio">

<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST" id="formuCam">
	<label  for="id_passC" >Contraseña: </label>
	<input type="password" name="pass" id="id_passCa" value=""/><br><br>
	<div  id="div_error_passC" class="error">
		<p id="error_id_passC">La contraseña debe tener al menos 8 caracteres , un dígito, minúsculas y al menos una mayúscula.</p>
	</div>
	<label  for="id_passCR" >Repita Contraseña: </label>
	<input type="password" name="pas" id="id_passCaR" value=""/><br><br>
	<div  id="div_error_passCR" class="">
		<p id="error_id_passCR"></p>
	</div>
	<input type="button" name="enviar" id="id_enviarC" value="Enviar"/> 
	
</form>
	<div  id="div_passCaR" class="">
		<p id="passCaR"></p>
	</div>

</div>