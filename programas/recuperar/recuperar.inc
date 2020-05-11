
<div id="formuRecu">

<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST" id="formuRecupe">
	<label  for="id_emailRe" >Email: </label>
	<input type="text" name="email" id="id_emailRe" value=""/><br><br>
	<div  id="div_error_emailR" class="">
		<p id="error_emailR"></p>
	</div>
	
	<input type="button" name="enviar" id="id_enviarR" value="Enviar"/> 
	
</form>
	<div  id="div_res_correo" class="">
		<p id="res_correo"></p>
	</div>
<br><h3><a href="index.php">Volver a la página de inicio</a></h3>
</div>