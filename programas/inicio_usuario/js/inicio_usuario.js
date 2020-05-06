document.addEventListener("readystatechange",cargarEvento,false);

function cargarEvento(){
	
	if(document.readyState=="interactive"){
		ultimasPublicaciones();
		librosRecomendados();
		document.getElementById("volverI").addEventListener("click",volverI,false);
	}
}

function eventoImg(){
	for(var i=0;i<document.getElementsByClassName("imgUltimasP").length;i++){
		document.getElementsByClassName("imgUltimasP")[i].addEventListener("click",fichaUltimaP,false);
	}
}

function ultimasPublicaciones(){
	var peticion=new XMLHttpRequest();
	peticion.addEventListener("readystatechange",gestionarUltimasPublicaciones,false);
	peticion.open("POST","programas/inicio_usuario/php/inicio_usuario.php",false);
	peticion.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	var datos = "x= ";
	peticion.send(datos);
}

function gestionarUltimasPublicaciones(evento){
	if (evento.target.readyState == 4 && evento.target.status == 200) {
		respuesta = JSON.parse(evento.target.responseText);
		mostrarUltimasPublicaciones(respuesta);
	}
}

function mostrarUltimasPublicaciones(respuesta){
	libros = respuesta;
	document.getElementById("ultimasPublicaciones").innerHTML="";	
	for(var i=0;i<libros.length;i++){
		var div=document.createElement("div");
		div.setAttribute("class","libroPubli");
		var img2=document.createElement("img");
		img2.setAttribute("class","imgUltimasP");
		img2.setAttribute("src","imgLibros/"+libros[i]["img"]+".jpg");
		var spanTitulo = document.createElement("span");
		spanTitulo.setAttribute("class","oculto");
		spanTitulo.innerHTML = libros[i]["titulo"];	
		var spanAutor = document.createElement("span");
		spanAutor.setAttribute("class","oculto");
		spanAutor.innerHTML = libros[i]["autor"];
		var spanGenero = document.createElement("span");
		spanGenero.setAttribute("class","oculto");
		spanGenero.innerHTML = libros[i]["genero"];
		var spanAnio = document.createElement("span");
		spanAnio.setAttribute("class","oculto");
		spanAnio.innerHTML = libros[i]["año_edicion"];	
		var spanRes = document.createElement("span");
		spanRes.setAttribute("class","oculto");
		spanRes.innerHTML = libros[i]["descripcion"];
		var spanEditorial = document.createElement("span");
		spanEditorial.setAttribute("class","oculto");
		spanEditorial.innerHTML = libros[i]["editorial"];
		var spanIdioma = document.createElement("span");
		spanIdioma.setAttribute("class","oculto");
		spanIdioma.innerHTML = libros[i]["idioma"];
		var spanCodigo = document.createElement("span");
		spanCodigo.setAttribute("class","oculto");
		spanCodigo.innerHTML = libros[i]["cod_libro"];
		div.appendChild(img2);
		div.appendChild(spanTitulo);
		div.appendChild(spanAutor);
		div.appendChild(spanGenero);
		div.appendChild(spanAnio);
		div.appendChild(spanRes);
		div.appendChild(spanEditorial);
		div.appendChild(spanIdioma);
		div.appendChild(spanCodigo);
		document.getElementById("ultimasPublicaciones").appendChild(div);
	}
}

function cambiarBordeImg(){
	for(var i=0;i<document.getElementsByClassName("imgUltimasP").length;i++){
		if(document.getElementsByClassName("imgUltimasP")[i].style.backgroundColor=="black"){
			document.getElementsByClassName("imgUltimasP")[i].style.backgroundColor="white";
			document.getElementsByClassName("imgUltimasP")[i].style.borderLeft="3px solid black";
			document.getElementsByClassName("imgUltimasP")[i].style.borderRight="3px solid black";
		}else{
			document.getElementsByClassName("imgUltimasP")[i].style.backgroundColor="black";
			document.getElementsByClassName("imgUltimasP")[i].style.borderLeft="3px solid white";
		document.getElementsByClassName("imgUltimasP")[i].style.borderRight="3px solid white";
		}
	}
	setTimeout(cambiarBordeImg, 2000);
}

function fichaUltimaP(){
	var ruta=this.getAttribute("src");
	var titulo=this.nextSibling.innerHTML;
	var autor=this.nextSibling.nextSibling.innerHTML;
	var genero=this.nextSibling.nextSibling.nextSibling.innerHTML;
	var año=this.nextSibling.nextSibling.nextSibling.nextSibling.innerHTML;
	var des=this.nextSibling.nextSibling.nextSibling.nextSibling.nextSibling.innerHTML;
	var editorial=this.nextSibling.nextSibling.nextSibling.nextSibling.nextSibling.nextSibling.innerHTML;
	var idioma=this.nextSibling.nextSibling.nextSibling.nextSibling.nextSibling.nextSibling.nextSibling.innerHTML;
	var codigo=this.nextSibling.nextSibling.nextSibling.nextSibling.nextSibling.nextSibling.nextSibling.nextSibling.innerHTML;
	document.getElementById("info").style.display="flex";
	document.getElementById("inicio").style.display="none";
	document.getElementById("imgPortada").innerHTML="";
	document.getElementById("informacionRes").innerHTML="";
	document.getElementById("ficha").innerHTML="";
	document.getElementById("imgPortada").innerHTML="<img src=\""+ruta+"\">";
	document.getElementById("informacionRes").innerHTML="<h1>"+titulo+"</h1><p>"+des+"</p>";
	document.getElementById("ficha").innerHTML="<h3>Ficha Técnica</h3><p>Autor: "+autor+"</p><p>Género: "+genero+"</p><p>Año: "+año+"</p><p>Idioma: "+idioma+"</p><p>Editorial: "+editorial+"</p><p>ISBN :<span id=\"codigo\">"+codigo+"</span></p>";
	document.getElementById("volver").style.display="none";
	document.getElementById("volverB").style.display="none";
	document.getElementById("volverI").style.display="block";
}

function volverI(){
	document.getElementById("info").style.display="none";
	document.getElementById("inicio").style.display="block";
}

function librosRecomendados(){
	var myobj={recomendados:true};
	myobj=JSON.stringify(myobj);
	var peticion=new XMLHttpRequest();
	peticion.addEventListener("readystatechange",gestionarLibrosRecomendados,false);
	peticion.open("POST","programas/inicio_usuario/php/inicio_usuario.php",false);
	peticion.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	var datos = "x="+myobj;
	peticion.send(datos);
}

function gestionarLibrosRecomendados(evento){
	if (evento.target.readyState == 4 && evento.target.status == 200) {
		respuesta = JSON.parse(evento.target.responseText);
		mostrarLibrosRecomendados(respuesta);
	}
}

function mostrarLibrosRecomendados(respuesta){
	if(respuesta[0]=="TENDENCIAS"){
		document.getElementById("h1Recomendados").innerHTML=respuesta[0];
		libros = respuesta[1];
	}else{
		libros = respuesta;
	}
	
	document.getElementById("librosRecomendados").innerHTML="";	
	for(var i=0;i<libros.length;i++){
		var div=document.createElement("div");
		div.setAttribute("class","libroPubli");
		var img2=document.createElement("img");
		img2.setAttribute("class","imgUltimasP");
		img2.setAttribute("src","imgLibros/"+libros[i]["img"]+".jpg");
		var spanTitulo = document.createElement("span");
		spanTitulo.setAttribute("class","oculto");
		spanTitulo.innerHTML = libros[i]["titulo"];	
		var spanAutor = document.createElement("span");
		spanAutor.setAttribute("class","oculto");
		spanAutor.innerHTML = libros[i]["autor"];
		var spanGenero = document.createElement("span");
		spanGenero.setAttribute("class","oculto");
		spanGenero.innerHTML = libros[i]["genero"];	
		var spanAnio = document.createElement("span");
		spanAnio.setAttribute("class","oculto");
		spanAnio.innerHTML = libros[i]["año_edicion"];	
		var spanRes = document.createElement("span");
		spanRes.setAttribute("class","oculto");
		spanRes.innerHTML = libros[i]["descripcion"];
		var spanEditorial = document.createElement("span");
		spanEditorial.setAttribute("class","oculto");
		spanEditorial.innerHTML = libros[i]["editorial"];
		var spanIdioma = document.createElement("span");
		spanIdioma.setAttribute("class","oculto");
		spanIdioma.innerHTML = libros[i]["idioma"];
		var spanCodigo = document.createElement("span");
		spanCodigo.setAttribute("class","oculto");
		spanCodigo.innerHTML = libros[i]["cod_libro"];
		div.appendChild(img2);
		div.appendChild(spanTitulo);
		div.appendChild(spanAutor);
		div.appendChild(spanGenero);
		div.appendChild(spanAnio);
		div.appendChild(spanRes);
		div.appendChild(spanEditorial);
		div.appendChild(spanIdioma);
		div.appendChild(spanCodigo);
		document.getElementById("librosRecomendados").appendChild(div);
	}
	eventoImg();
	cambiarBordeImg();
}