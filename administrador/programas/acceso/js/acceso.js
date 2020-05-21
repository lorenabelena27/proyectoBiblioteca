document.addEventListener("readystatechange",cargarEvento,false);
//evento para el boton 
function cargarEvento(){
	
	if(document.readyState=="interactive"){
		document.getElementById("id_enviar1").addEventListener("click",peticionAcceso,false);
	}
}
//peticion para el acceso del tabajador "administrador"
function peticionAcceso(){
	//comprobaciones
	var numTrabajador =document.getElementById("id_trabajador").value;
	var p1=document.getElementById("error_trabajador");
	var todoBien=false;
	var error="";
	if(numTrabajador==""){
		error="Debe introducir un número de trabajador";
		p1.innerHTML=error;
	}else{
		todoBien=true;
	}
	var pass1 =document.getElementById("id_pass1").value;
	var p2=document.getElementById("error_pass1");
	if(pass1==""){
		error="Debe introducir una Contraseña";
		document.getElementById("error_pass1").innerHTML= error;
	}else{
		todoBien=true;
	}
	//peticion
	if(todoBien==true){
		var myobj={nTrabajador:numTrabajador,contra:pass1}
		myobj=JSON.stringify(myobj);
		var peticion=new XMLHttpRequest();
		peticion.addEventListener("readystatechange",gestionarAcceso,false);
		peticion.open("POST","programas/acceso/php/acceso.php",false);
		peticion.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		var datos = "x="+myobj;
		peticion.send(datos);
	}
	
}
function gestionarAcceso(evento){
	
	if (evento.target.readyState == 4 && evento.target.status == 200) {
		respuesta = JSON.parse(evento.target.responseText);
		if(respuesta=="Entras"){
			respuestaAcceso(respuesta);
		}else if(respuesta=="No"){
			document.getElementById("error_pass1").innerHTML= "";
			document.getElementById("error_trabajador").innerHTML= "Usuario y/o contraseña incorrectas";
		}
    }
}
function respuestaAcceso(respuesta){
	window.location="index.php";
}