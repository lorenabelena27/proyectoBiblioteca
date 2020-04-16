var libros;
var total_libros;
var pagina;
document.addEventListener("readystatechange",cargarEvento,false);
function cargarEvento(){
	if(document.readyState=="interactive"){
		document.getElementById("volver").addEventListener("click",volver,false);
		listarLibros();
		for(var i=0;i<document.getElementsByClassName("libro").length;i++){
			document.getElementsByClassName("libro")[i].addEventListener("mouseover",mostrarInfo,false);
		}
		cargarPaginacion();
	}
}
function cargarPaginacion(){
	for(var i=0;i<document.getElementsByClassName("pagina").length;i++){
		document.getElementsByClassName("pagina")[i].addEventListener("click",mostrarPagina,false);
	}
}

function listarLibros(){
		var peticion=new XMLHttpRequest();
		peticion.addEventListener("readystatechange",gestionarListado,false);
		peticion.open("POST","programas/listar_libros/php/listar_libros.php",false);
		peticion.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		var datos = "x= ";
		peticion.send(datos);
}
function gestionarListado(evento){
	if (evento.target.readyState == 4 && evento.target.status == 200) {
		respuesta = JSON.parse(evento.target.responseText);
		respuestaLibros(respuesta);
    }
}
function respuestaLibros(respuesta){
	if(respuesta.length == 2){
		total_libros = respuesta[0];
		libros = respuesta[1];
	}else{
		libros = respuesta;
	}
	var total_pag = Math.ceil(total_libros/12);

	document.getElementById("libros").innerHTML="";	
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
		img.appendChild(img2);
		div.appendChild(img);
		div.appendChild(infoLibro);
		div.appendChild(spanAnio);
		div.appendChild(spanRes);
		div.appendChild(spanEditorial);
		div.appendChild(spanIdioma);
		document.getElementById("libros").appendChild(div);
	}
	while(document.getElementById("paginacion").hasChildNodes()){
		document.getElementById("paginacion").removeChild(document.getElementById("paginacion").firstChild);
	}
	
	var desde = pagina-7;
	var hasta = pagina+7;
	if(desde >0){
		desde = pagina-7;
		if(hasta > total_pag){
			hasta = total_pag;
		}
	}else{
		desde = 1;
		if(total_pag <=15){
			hasta=total_pag;
		}else{
			hasta = 15;
		}
		
	}
	//hasta 15 --> 1: 1-15 2: 1-15 9: 2-16 10:3-17
	var ul = document.createElement("ul");
	for(var i=desde;i<=hasta;i++){
		var li = document.createElement("li");
		
		if(i == pagina){
			li.setAttribute("class","zoom pag_actual");
		}else{
			li.setAttribute("class","zoom");
		}
		var p = document.createElement("p");
		
		p.setAttribute("class","pagina");
		p.innerHTML=i;	
		li.appendChild(p);
		ul.appendChild(li);
	}
	document.getElementById("paginacion").appendChild(ul);
	cargarPaginacion();
	fichaTecnica();
	
}

function mostrarInfo(){
	
}

function mostrarPagina(){
	pagina = parseInt(this.innerHTML);
	var pagi = parseInt(this.innerHTML);
	var myobj={pag:pagi}
	myobj=JSON.stringify(myobj);
	var peticion=new XMLHttpRequest();
	peticion.addEventListener("readystatechange",gestionarPagina,false);
	peticion.open("POST","programas/listar_libros/php/listar_libros.php",false);
	peticion.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	var datos = "x="+myobj;
	peticion.send(datos);
}

function gestionarPagina(evento){
	if (evento.target.readyState == 4 && evento.target.status == 200) {
		alert(evento.target.responseText);
		respuesta = JSON.parse(evento.target.responseText);
		respuestaLibros(respuesta);
    }
}

function fichaTecnica(){
	for(var i=0;i<document.getElementsByClassName("fichaTecnica").length;i++){
		document.getElementsByClassName("fichaTecnica")[i].addEventListener("click",mostrarFT,false);
	}
}

function mostrarFT(){

	var ruta = this.parentNode.parentNode.firstChild.firstChild.getAttribute('src');
	var titulo=this.parentNode.firstChild.innerHTML;
	var autor=this.parentNode.firstChild.nextSibling.innerHTML;
	var genero=this.parentNode.firstChild.nextSibling.nextSibling.innerHTML;
	var año=this.parentNode.nextSibling.innerHTML;
	var des=this.parentNode.nextSibling.nextSibling.innerHTML;
	var editorial=this.parentNode.nextSibling.nextSibling.nextSibling.innerHTML;
	var idioma=this.parentNode.nextSibling.nextSibling.nextSibling.nextSibling.innerHTML;
	document.getElementById("info").style.display="flex";
	document.getElementById("libros").style.display="none";
	document.getElementById("paginacion").style.display="none";
	document.getElementById("imgPortada").innerHTML="";
	document.getElementById("informacionRes").innerHTML="";
	document.getElementById("ficha").innerHTML="";
	document.getElementById("imgPortada").innerHTML="<img src=\""+ruta+"\">";
	document.getElementById("informacionRes").innerHTML="<h1>"+titulo+"</h1><p>"+des+"</p>";
	document.getElementById("ficha").innerHTML="<h3>Ficha Técnica</h3><p>Autor: "+autor+"</p><p>Género: "+genero+"</p><p>Año: "+año+"</p><p>Idioma: "+idioma+"</p><p>Editorial: "+editorial+"</p>";
}

function volver(){
	document.getElementById("info").style.display="none";
	document.getElementById("libros").style.display="flex";
	document.getElementById("paginacion").style.display="flex";
	
}

