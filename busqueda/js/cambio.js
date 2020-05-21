document.addEventListener("readystatechange",cargarEvento,false);
//cambio de contraseña 
function cargarEvento(){
	
	if(document.readyState=="interactive"){
		document.getElementById("id_enviarC").addEventListener("click",peticionCambio,false);
	}
}
//validar la contraseña
function validaPass(contrasena){
	
	var patronPass=/^(?=\w*\d)(?=\w*[A-Z])(?=\w*[a-z])\S{8,16}$/
	if(patronPass.test(contrasena)==false){
		return false;
	}
}
//se recoge la nueva contraseña para enviarla como peticion al php para hacer el cambio
function peticionCambio(){
	var todoBien=false;
	var contrasena=document.getElementById("id_passCa").value;
	var p1=document.getElementById("error_id_passC");
	//se comprueba la contraseña
	if(contrasena==""){
		error="Debes introducir una Contraseña";
		document.getElementById("div_error_passC").className ="error";
		document.getElementById("div_error_passC").style.diplay="block";
		p1.innerHTML=error;
	}else if(validaPass(contrasena)==false){
		error="La contraseña debe tener al entre 8 y 16 caracteres, al menos un dígito, al menos una minúscula y al menos una mayúscula.";
		document.getElementById("div_error_passC").className ="error";
		document.getElementById("div_error_passC").style.diplay="block";
		p1.innerHTML=error;
	}else{
		p1.innerHTML="";
		document.getElementById("div_error_passC").className ="ocultar";
		todoBien=true;
	}
	//se comprueba la contraseña y que sean iguales
	var confir=document.getElementById("id_passCaR").value;
	var p2=document.getElementById("error_id_passCR");
	if(confir==""){
		error="Debes introducir la Contraseña anterior";
		document.getElementById("div_error_passCR").className ="error";
		document.getElementById("div_error_passCR").style.diplay="block";
		p2.innerHTML=error;
	}else if(contrasena!=confir){
		error="Las contraseña no coinciden";
		document.getElementById("div_error_passCR").className ="error";
		document.getElementById("div_error_passCR").style.diplay="block";
		p2.innerHTML=error;
	}else{
		p2.innerHTML="";
		document.getElementById("div_error_passCR").className ="ocultar";
		todoBien=true;
	}
	//si todo esta bien se envia la peticion
	if(todoBien==true){
		var myobj={pass:contrasena,pas:confir}
		myobj=JSON.stringify(myobj);
		var peticion=new XMLHttpRequest();
		peticion.addEventListener("readystatechange",gestionarCambio,false);
		peticion.open("POST","programas/cambio/php/cambio.php",false);
		peticion.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		var datos = "x="+myobj;
		peticion.send(datos);
	}
	
}

function gestionarCambio(evento){
	
	if (evento.target.readyState == 4 && evento.target.status == 200) {
		respuesta = JSON.parse(evento.target.responseText);
		if(respuesta!=="No"){
			respuestaCambio(respuesta);
		}else if(respuesta=="No"){
			document.getElementById("passCaR").innerHTML= "No se ha realizado el cambio";
		}
    }
}
function respuestaCambio(respuesta){

	document.getElementById("formuCam").setAttribute("class","ocultar");
	document.getElementById("passCaR").innerHTML= "Su contraseña ha sido cambiada con exito";
}
