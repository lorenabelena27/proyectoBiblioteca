
   <nav class="flex-nav" id="menuUsu">
      <ul>
       <?php if($_SERVER['QUERY_STRING'] == "listarLibros") echo "<li id=\"btn-filtrar\" class=\"toggleNav\">☰</li>"; ?>
	   
        <li class="prog"><a href="index.php"><div><h4>Inicio</h4></div></a></li>
		<li class="prog"><a href="index.php?listarLibros"><div><h4>Listar Libros</h4></div></a></li>
        <li class="prog"><a href="index.php?misLibros"><div><h4>Mis libros</h4></div></a></li>
		<li class="prog"><input type="text" id="id_libro" name="email" value=" "/> <input type="button" name="buscar" id="id_buscar"value="Buscar"/>
			<br><span id="error_busq"></span>
		</li>
      </ul>
    </nav>
	
	