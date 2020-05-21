document.addEventListener("readystatechange",cargarEvento,false);
//carga el evento en el boton
function cargarEvento(){
	
	if(document.readyState=="interactive"){
		document.getElementById("id_enviarR").addEventListener("click",peticionRecuperar,false);
	}
}
//se envia la peticion a php 
function peticionRecuperar(){
	//se comprueba los datos
	var email1 =document.getElementById("id_emailRe").value;
	var p1=document.getElementById("error_emailR");
	var todoBien=false;
	var error="";
	if(email1==""){
		error="Debes introducir un Email";
		p1.innerHTML=error;
	}else{
		todoBien=true;
	}
	//si no hay errores se envia la peticion
	if(todoBien==true){
		var myobj={email:email1}
		myobj=JSON.stringify(myobj);
		var peticion=new XMLHttpRequest();
		peticion.addEventListener("readystatechange",gestionarRecuperar,false);
		peticion.open("POST","programas/recuperar/php/recuperar.php",false);
		peticion.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		var datos = "x="+myobj;
		peticion.send(datos);
	}
	
}
//se getiona la respuesta
function gestionarRecuperar(evento){
	
	if (evento.target.readyState == 4 && evento.target.status == 200) {
		respuesta = JSON.parse(evento.target.responseText);
		if(respuesta==true){
			respuestaRecuperar(respuesta);
		}else if(respuesta=="No"){
			document.getElementById("error_emailR").innerHTML= "El email no coincide con ningún usuario";
		}
    }
}
//respuesta 
function respuestaRecuperar(respuesta){
	
	document.getElementById("formuRecupe").setAttribute("class","ocultar");
	document.getElementById("res_correo").innerHTML= "Se ha enviado un correo con su nueva contraseña, puede llegar a la bandeja de Spam";
}
