document.addEventListener("readystatechange",cargarEvento,false);
function cargarEvento(){
	if(document.readyState=="interactive"){
		document.getElementById("id_buscar").addEventListener("click",busqueda,false);
	}
}
function busqueda(){
	var error;
	var dato=document.getElementById("id_libro");
	if(dato==" "){
		error="Debe introducir un titulo o nombre de autor";
		alert(error);
	}else{
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
function gestionarBusqueda(){
	if (evento.target.readyState == 4 && evento.target.status == 200) {
			alert(evento.target.responseText);
			respuesta = JSON.parse(evento.target.responseText);
        }
}
function respuestaBusqueda(respuesta){}