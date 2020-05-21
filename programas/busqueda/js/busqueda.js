//variable donde se guardara la respuesta
var libros;
document.addEventListener("readystatechange",cargarEvento,false);
//se carga los eventos 
function cargarEvento(){
	
	if(document.readyState=="interactive"){
		document.getElementById("volverB").addEventListener("click",volverB,false);
		document.getElementById("id_buscar").addEventListener("click",busqueda,false);
		document.getElementById("cerrar_busqueda").addEventListener("click",cerrarBusqueda,false);
		document.getElementById("botonMenu").addEventListener("click",menuDesplegable,false);
	}
}
//funcion para hacer la peticion a php
function busqueda(){
	//se comprueba los datos
	var todoBien=false;
	var dato=document.getElementById("id_libro").value;
	if(dato==" " || dato==""){
		document.getElementById("error_busq").innerHTML="Debe introducir un titulo o nombre de autor";	
	}else{
		todoBien=true;
	}
	//si los datos estan bien se realiza la petición
	if(todoBien==true){
		document.getElementById("error_busq").innerHTML="";
		var myobj={dato:dato}
		myobj=JSON.stringify(myobj);
		var peticion=new XMLHttpRequest();
		peticion.addEventListener("readystatechange",gestionarBusqueda,false);
		peticion.open("POST","programas/busqueda/php/busqueda.php",false);
		peticion.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		var datos = "x="+myobj;
		peticion.send(datos);
	}
	
}
//funcion que gestiona la peticion
function gestionarBusqueda(evento){
	
	if (evento.target.readyState == 4 && evento.target.status == 200) {
			respuesta = JSON.parse(evento.target.responseText);
			respuestaBusqueda(respuesta);
        }
}
//funcion respuesta
function respuestaBusqueda(respuesta){
	//dependiendo de la respuesta se gestiona 
	//desaparece el inicio , listar libros o mis libros y aparece la busqueda 
	if(respuesta.length > 0){
		if(document.getElementById("inicio")){
			document.getElementById("inicio").style.display="none";
		}
		
		if(document.getElementById("libros_listar")){
			document.getElementById("libros_listar").style.display="none";
		}
		
		if(document.getElementById("misLibros")){
			document.getElementById("misLibros").style.display="none";
		}
		
		document.getElementById("busqueda").style.display="block";
		mostrarBusqueda();
	}else{
		//en este caso es el contrario 
		if(document.getElementById("inicio")){
			document.getElementById("inicio").style.display="block";
		}
		
		if(document.getElementById("libros_listar")){
			document.getElementById("libros_listar").style.display="block";
		}
		
		document.getElementById("busqueda").style.display="none";
		document.getElementById("error_busq").innerHTML="No hay resultados";
	}
	document.getElementById("id_libro").value="";
}
//muestra los datos de la busqueda
function mostrarBusqueda(){
	
	libros = respuesta;
	document.getElementById("libros_busqueda").innerHTML="";	
	for(var i=0;i<libros.length;i++){
		var div=document.createElement("div");
		div.setAttribute("class","libro");
		var img=document.createElement("div");
		var img2=document.createElement("img");
		img2.setAttribute("src","imgLibros/"+libros[i]["img"]+".jpg");
		
		var infoLibro=document.createElement("div");
		infoLibro.setAttribute("class","lado atras");
		infoLibro.innerHTML = "<h2>"+libros[i]["titulo"]+"</h2>";
		infoLibro.innerHTML += "<h4>"+libros[i]["autor"]+"</h4>";
		infoLibro.innerHTML += "<h4>"+libros[i]["genero"]+"</h4>";
		infoLibro.innerHTML += "<div class=\"fichaTecnica\">Ficha Técnica</div>";
		
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
		img.appendChild(img2);
		div.appendChild(img);
		div.appendChild(infoLibro);
		div.appendChild(spanAnio);
		div.appendChild(spanRes);
		div.appendChild(spanEditorial);
		div.appendChild(spanIdioma);
		div.appendChild(spanCodigo);
		document.getElementById("libros_busqueda").appendChild(div);
	}
	//se llama ala funcion ficha tecnica para mostrar los datos del libro
	fichaTecnicaB();
}
//funcion carga el evento del enlace de la ficha tecnica
function fichaTecnicaB(){
	for(var i=0;i<document.getElementsByClassName("fichaTecnica").length;i++){
		document.getElementsByClassName("fichaTecnica")[i].addEventListener("click",mostrarFTB,false);
	}
}
//muestra la ficha tecnica
function mostrarFTB(){
	
	document.getElementById("estadoLibro").style.display="none";
	var ruta = this.parentNode.parentNode.firstChild.firstChild.getAttribute('src');
	var titulo=this.parentNode.firstChild.innerHTML;
	var autor=this.parentNode.firstChild.nextSibling.innerHTML;
	var genero=this.parentNode.firstChild.nextSibling.nextSibling.innerHTML;
	var año=this.parentNode.nextSibling.innerHTML;
	var des=this.parentNode.nextSibling.nextSibling.innerHTML;
	var editorial=this.parentNode.nextSibling.nextSibling.nextSibling.innerHTML;
	var idioma=this.parentNode.nextSibling.nextSibling.nextSibling.nextSibling.innerHTML;
	var codigo=this.parentNode.nextSibling.nextSibling.nextSibling.nextSibling.nextSibling.innerHTML;
	document.getElementById("info").style.display="flex";
	document.getElementById("busqueda").style.display="none";
	document.getElementById("imgPortada").innerHTML="";
	document.getElementById("informacionRes").innerHTML="";
	document.getElementById("ficha").innerHTML="";
	document.getElementById("imgPortada").innerHTML="<img src=\""+ruta+"\">";
	document.getElementById("informacionRes").innerHTML="<h1>"+titulo+"</h1><p>"+des+"</p>";
	document.getElementById("ficha").innerHTML="<h3>Ficha Técnica</h3><p>Autor: "+autor+"</p><p>Género: "+genero+"</p><p>Año: "+año+"</p><p>Idioma: "+idioma+"</p><p>Editorial: "+editorial+"</p><p>ISBN :<span id=\"codigo\">"+codigo+"</span></p>";
	document.getElementById("volver").style.display="none";
	document.getElementById("volverB").style.display="block";
	document.getElementById("volverI").style.display="none";
}

function volverB(){
	
	document.getElementById("info").style.display="none";
	document.getElementById("busqueda").style.display="block";
	
}

function cerrarBusqueda(){
	
	document.getElementById("busqueda").style.display=" none";
	if(document.getElementById("inicio")){
		document.getElementById("inicio").style.display=" block";
	}
	
	if(document.getElementById("libros_listar")){
		document.getElementById("libros_listar").style.display="block";
	}
	
	if(document.getElementById("misLibros")){
			document.getElementById("misLibros").style.display="block";
		}
}

function menuDesplegable(){
	if(document.getElementById("menuOculto").style.display=="flex"){
		document.getElementById("menuOculto").style.display="none";
	}else{
		document.getElementById("menuOculto").style.display="flex";
	}
}