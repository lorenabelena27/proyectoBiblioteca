document.addEventListener("readystatechange",cargarEvento,false);
//se llama a la funcione
function cargarEvento(){
	
	if(document.readyState=="interactive"){
		misLibros();
	}
}

//se hace la peticion a php para los libros
function misLibros(){
	var peticion=new XMLHttpRequest();
	peticion.addEventListener("readystatechange",gestionarMisLibros,false);
	peticion.open("POST","programas/mis_libros/php/mis_libros.php",false);
	peticion.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	var datos = "x= ";
	peticion.send(datos);
}
//se gestiona la respuesta
function gestionarMisLibros(evento){
	
	if (evento.target.readyState == 4 && evento.target.status == 200) {
		respuesta = JSON.parse(evento.target.responseText);
		mostrarMisLibros(respuesta);
	}
}
//respuesta de los libros del usario 
function mostrarMisLibros(respuesta){
	//si no se tiene libros
	if(respuesta[0] == 0 && respuesta[1] == 0){
		var h1=document.createElement("h2");
		h1.innerHTML="No tienes libros en préstamo ni reservados. ¿Por qué no buscas uno? <a href=\"index.php?listarLibros\">Listado de libros</a>";
		document.getElementById("misLibros").appendChild(h1);
		document.getElementById("misLibros").style.margin="200px 0 200px 0";
		document.getElementById("misLibros").style.backgroundColor="rgb(0,0,0,0.3)";
	}else{
		//si se tiene libros en prestamos
		if(respuesta[0] != 0){
			var h1=document.createElement("h1");
			var txth1=document.createTextNode("EN PRÉSTAMO");
			h1.appendChild(txth1);
			//se crea la cabecera de la "tabla" hecha con flex-box
			var tabla=document.createElement("div");
			tabla.setAttribute("id","tablaPrestamos");
			var cabeceraTabla=document.createElement("div");
			cabeceraTabla.setAttribute("class","cabeceraTabla");
			cabeceraTabla.innerHTML="<span id=\"cabeceraLibroP\">Libro</span><span>Desde</span><span>Hasta</span>";
			tabla.appendChild(cabeceraTabla);	
			//muestra los datos de los libros en prestamos
			for(var i=0; i<respuesta[0].length; i++){
				var divLibro=document.createElement("div");
				divLibro.setAttribute("class","libroMisLibros");			
					for( p in respuesta[0][i]){
						var divPropLibro=document.createElement("div");
						
						if(p=="img"){
							var img=document.createElement("img");
							img.setAttribute("src","./imgLibros/"+respuesta[0][i][p]+".jpg");
							divPropLibro.setAttribute("class","propImg");
							divPropLibro.appendChild(img);
						}else if(p=="titulo"){
							divPropLibro.setAttribute("class","infoMisLibros infoMisLibros-titulo");
							var texto=document.createTextNode(respuesta[0][i][p]);
							divPropLibro.appendChild(texto);
						}else{
							var sep1 = new RegExp("-", "g");
							var datos = respuesta[0][i][p].split(sep1);
							divPropLibro.setAttribute("class","infoMisLibros");
							var texto=document.createTextNode(datos[2]+"-"+datos[1]+"-"+datos[0]);
							divPropLibro.appendChild(texto);
						}
						
						divLibro.appendChild(divPropLibro);
					}
				
				tabla.appendChild(divLibro);	
			}
			document.getElementById("misLibros").appendChild(h1);
			document.getElementById("misLibros").appendChild(tabla);
		}
		//si tien libros en lista de espera
		if(respuesta[1] != 0){
			var h1=document.createElement("h1");
			var txth1=document.createTextNode("RESERVADOS");
			h1.appendChild(txth1);
			//se crea la cabecera de la "tabla" hecha con flex-box
			var tabla=document.createElement("div");
			tabla.setAttribute("id","tablaReservas");
			var cabeceraTabla=document.createElement("div");
			cabeceraTabla.setAttribute("class","cabeceraTabla");
			cabeceraTabla.innerHTML="<span id=\"cabeceraLibroR\">Libro</span><span>A partir del</span>";
			tabla.appendChild(cabeceraTabla);	
			for(var i=0; i<respuesta[1].length; i++){
				var divLibro=document.createElement("div");
				divLibro.setAttribute("class","libroMisLibros");	
					//muestra los datos de los libros en lista de espera			
					for( p in respuesta[1][i]){
						var divPropLibro=document.createElement("div");
						
						if(p=="img"){
							var img=document.createElement("img");
							img.setAttribute("src","./imgLibros/"+respuesta[1][i][p]+".jpg");
							divPropLibro.setAttribute("class","propImg");
							divPropLibro.appendChild(img);
						}else if(p=="titulo"){
							divPropLibro.setAttribute("class","infoMisLibros infoMisLibros-titulo");
							var texto=document.createTextNode(respuesta[1][i][p]);
							divPropLibro.appendChild(texto);
						}else{
							var sep1 = new RegExp("-", "g");
							var datos = respuesta[1	][i][p].split(sep1);
							divPropLibro.setAttribute("class","infoMisLibros");
							var texto=document.createTextNode(datos[2]+"-"+datos[1]+"-"+datos[0]);
							divPropLibro.appendChild(texto);
						}
						divLibro.appendChild(divPropLibro);
					}
				tabla.appendChild(divLibro);	
			}
			document.getElementById("misLibros").appendChild(h1);
			document.getElementById("misLibros").appendChild(tabla);
		}
	}
	
}