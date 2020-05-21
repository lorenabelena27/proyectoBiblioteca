 <div id= "inicio">
 <section class="wrapper2">
	  <div> </div>
     
    </section>
      <section class="posts">
  
	  <div class="post">
		
		<div class="contenido_post">
			<div class="post_txt">
				<h1>ÚLTIMAS PUBLICACIONES</h1>
			</div>
			<div id="ultimasPublicaciones"></div>
		</div>
	  </div>
	    <?php include "./vista_usuario/libro_semana.inc"; ?>
	  <div class="post">
		
		<div class="contenido_post">
			<div class="post_txt">
				<h1>LIBRO DE LA SEMANA</h1>
			</div>
			<div id="libro_semana">
				<img src="./imgLibros/<?php	echo $prueba; ?>.jpg" alt="img">
				<div class="resumenLibSemana">
					<h2><?php echo $titulo; ?></h2>
					<p><?php echo $resumen; ?></p>
				</div>
			</div>
		</div>
	  </div>
	  <div class="post">
		
		<div class="contenido_post">
			<div class="post_txt">
				<h1 id="h1Recomendados">LIBROS RECOMENDADOS</h1>
			</div>
			<div id="librosRecomendados"></div>

		</div>
	  </div> 
    </section>
</div>
