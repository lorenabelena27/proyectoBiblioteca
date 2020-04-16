document.addEventListener("readystatechange",cargarEvento,false);

function cargarEvento(){
	if(document.readyState=="interactive"){
		document.getElementById("id_enviar1").addEventListener("click",peticionAcceso,false);
	}
}
function peticionAcceso(){
	var email1 =document.getElementById("id_email1").value;
	var p1=document.getElementById("error_email1");
	var todoBien=false;
	var error="";
	if(email1==""){
		error="Debes introducir un Email";
		p1.innerHTML=error;
	}else{
		todoBien=true;
	}
	var pass1 =document.getElementById("id_pass1").value;
	var p2=document.getElementById("error_pass1");
	if(pass1==""){
		error="Debes introducir una Contraseña";
		document.getElementById("error_pass1").innerHTML= error;
	}else{
		todoBien=true;
	}
	
	if(todoBien==true){
		var myobj={email:email1,contra:pass1}
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
		}else{
			alert(respuesta);
		}
		
    }
}
function respuestaAcceso(respuesta){
	window.location="index.php";
}