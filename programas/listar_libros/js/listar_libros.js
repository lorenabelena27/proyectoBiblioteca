var libros;
var total_libros;
var pagina;
var okCategorias=false;
var listaFiltro = false;
var gen;
document.addEventListener("readystatechange",cargarEvento,false);
function cargarEvento(){
	if(document.readyState=="interactive"){
		document.getElementById("volver").addEventListener("click",volver,false);
		document.getElementById("btn-filtrar").addEventListener("click",mostrarFiltros,false);
		document.getElementById("btn-filtrar").addEventListener("click",cargarCategorias,false);
		listarLibros();
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
	if(respuesta.length == 2 && typeof(respuesta[0]) == "string"){
		total_libros = respuesta[0];
		libros = respuesta[1];
	}else{
		libros = respuesta;
	}
	libros.sort(function(a,b){ return (a["titulo"] - b["titulo"])});
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
		document.getElementById("libros").appendChild(div);
	}
	while(document.getElementById("paginacion").hasChildNodes()){
		document.getElementById("paginacion").removeChild(document.getElementById("paginacion").firstChild);
	}
	var clase="pagina";
	if(listaFiltro==true){
		clase="paginaFiltro";
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
		
		p.setAttribute("class",clase);
		p.innerHTML=i;	
		li.appendChild(p);
		ul.appendChild(li);
	}
	document.getElementById("paginacion").appendChild(ul);
	if(listaFiltro==true){
		cargarPaginacionFiltro();
	}else{
		cargarPaginacion();
	}
	fichaTecnica();
	
}

function mostrarPagina(){
	pagina = parseInt(this.innerHTML);
	var pagi = parseInt(this.innerHTML);
	var myobj={pag:pagi};
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
	document.getElementById("libros_listar").style.display="none";
	document.getElementById("paginacion").style.display="none";
	document.getElementById("imgPortada").innerHTML="";
	document.getElementById("informacionRes").innerHTML="";
	document.getElementById("ficha").innerHTML="";
	document.getElementById("imgPortada").innerHTML="<img src=\""+ruta+"\">";
	document.getElementById("informacionRes").innerHTML="<h1>"+titulo+"</h1><p>"+des+"</p>";
	document.getElementById("ficha").innerHTML="<h3>Ficha Técnica</h3><p>Autor: "+autor+"</p><p>Género: "+genero+"</p><p>Año: "+año+"</p><p>Idioma: "+idioma+"</p><p>Editorial: "+editorial+"</p><p>ISBN :<span id=\"codigo\">"+codigo+"</span></p>";
	document.getElementById("volver").style.display="block";
	document.getElementById("volverB").style.display="none";
	document.getElementById("volverI").style.display="none";
	
}

function volver(){
	document.getElementById("info").style.display="none";
	document.getElementById("libros_listar").style.display="block";
	document.getElementById("paginacion").style.display="flex";	
}

function mostrarFiltros(){

	if(document.getElementById("menuFiltros").className=="vertical-oculto"){
		setTimeout(a, 500);
		setTimeout(b, 200);
	}else{
		setTimeout(a, 1000);
		setTimeout(b, 200);
	}

}

function a(){
	if(document.getElementById("menuFiltros").className=="vertical-oculto"){
		document.getElementById("menuFiltros").className="vertical-menu";
	}else{
		document.getElementById("menuFiltros").className="vertical-oculto";
	}
}

function b(){
	if(document.getElementById("menuFiltros").className=="vertical-oculto"){
		document.getElementById("menuFiltros").style.width="15%";
	}else{
		document.getElementById("menuFiltros").style.width="0%";
	}
}
function cargarCategorias(){
	if(okCategorias==false){
		var categoria=true;
		var myobj={cat:categoria};
		myobj=JSON.stringify(myobj);
		var peticion=new XMLHttpRequest();
		peticion.addEventListener("readystatechange",gestionarCategorias,false);
		peticion.open("POST","programas/listar_libros/php/listar_libros.php",false);
		peticion.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		var datos = "x="+myobj;
		peticion.send(datos);
	}
	
}
function gestionarCategorias(evento){
	if (evento.target.readyState == 4 && evento.target.status == 200) {
		respuesta = JSON.parse(evento.target.responseText);
		mostrarCategorias(respuesta);
    }
}
function mostrarCategorias(respuesta){
	okCategorias = true;
	while(document.getElementById("menuFiltros").firstChild.innerHTML != document.getElementById("menuFiltros").lastChild.innerHTML){
		document.getElementById("menuFiltros").removeChild((document.getElementById("menuFiltros").lastChild));
	}
	for(var i=0;i<respuesta.length;i++){
		var span=document.createElement("span");
		span.setAttribute("class","categoria");
		span.innerHTML=respuesta[i]["genero"];
		document.getElementById("menuFiltros").appendChild(span);
	}
	for(var i=0;i<document.getElementsByClassName("categoria").length;i++){
			document.getElementsByClassName("categoria")[i].addEventListener("click",cargarLibrosCT,false);
	}	
}
function cargarLibrosCT(){
	gen =this.innerHTML;
	var myobj={catego:gen};
	myobj=JSON.stringify(myobj);
	var peticion=new XMLHttpRequest();
	peticion.addEventListener("readystatechange",gestionarLibrosCT,false);
	peticion.open("POST","programas/listar_libros/php/listar_libros.php",false);
	peticion.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	var datos = "x="+myobj;
	peticion.send(datos);
}
function gestionarLibrosCT(evento){
	if (evento.target.readyState == 4 && evento.target.status == 200) {
		respuesta = JSON.parse(evento.target.responseText);
		mostrarLibrosCT(respuesta);
    }
}
function mostrarLibrosCT(respuesta){
	listaFiltro=true;
	respuestaLibros(respuesta);
}

function cargarPaginacionFiltro(){
	for(var i=0;i<document.getElementsByClassName("paginaFiltro").length;i++){
		document.getElementsByClassName("paginaFiltro")[i].addEventListener("click",mostrarPaginaFiltro,false);
	}
}

function mostrarPaginaFiltro(){
	pagina = parseInt(this.innerHTML);
	var pagi = parseInt(this.innerHTML);
	var myobj={pagF:pagi, catF: gen};
	myobj=JSON.stringify(myobj);
	var peticion=new XMLHttpRequest();
	peticion.addEventListener("readystatechange",gestionarPagina,false);
	peticion.open("POST","programas/listar_libros/php/listar_libros.php",false);
	peticion.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	var datos = "x="+myobj;
	peticion.send(datos);
}