document.addEventListener("readystatechange",cargarEvento,false);
function cargarEvento(){
	if(document.readyState=="interactive"){
		document.getElementById("id_buscar").addEventListener("click",busqueda,false);
	}
}
function busqueda(){
	var error;
	var p = createElement("p");
	var dato=document.getElementById("id_libro");
	if(dato == " "){
		error="Debes introducir un dato";
		p.innerHTML=error;
	}else{
		var myobj={dato:dato};
		myobj=JSON.stringify(myobj);
		var peticion=new XMLHttpRequest();
		peticion.addEventListener("readystatechange",gestionarBusqueda,false);
		peticion.open("POST","programas/listar_libros/php/busqueda.php",false);
		peticion.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		var datos = "x= ";
		peticion.send(datos);
	}
	function 
}