document.addEventListener("readystatechange",cargarEvento,false);

function cargarEvento(){
	if(document.readyState=="interactive"){
		document.getElementById("id_enviar").addEventListener("click",peticionAlta,false);
	}
}
function validaDni(dni){
	var patronDNI=/^[0-9]{8}[A-Za-z]$/;
	var letras = ['T', 'R', 'W', 'A', 'G', 'M', 'Y', 'F', 'P', 'D', 'X', 'B', 'N', 'J', 'Z', 'S', 'Q', 'V', 'H', 'L', 'C', 'K', 'E', 'T'];
	if(patronDNI.test(dni)==false){	
		return false;
	}else{
		var letraDNI = dni.substring(8, 9).toUpperCase();
		var numDNI = parseInt(dni.substring(0, 8));
		var letraCorrecta = letras[numDNI % 23];
	
			if(letraDNI!= letraCorrecta){
				return "no es valido";	
			} else{
				return true;
			}
	}
}
function validaEmail(email){
	var patronEmail=/^([a-zA-Z])+([\w\._])*([A-Za-z])+@/;
	var dominios =["gmail.com","hotmail.com","yahoo.es","yahoo.com"];

	if (patronEmail.test(email)==false){
		return false;
	}else {
		var pos=email.split("@")[1];
		var dominio = dominios.indexOf(pos);
		if(dominio==-1){
			return "no valido";
		}else{
			return true;
		}	
	}
}
function validaFecha(fecha){
	var fechaN=new Date(fecha);
	var hoy=new Date();
	if(fechaN.getTime()>hoy.getTime()){
		return false;
	}
}

function validaPass(contrasena){
	var patronPass=/^(?=\w*\d)(?=\w*[A-Z])(?=\w*[a-z])\S{8,16}$/
	if(patronPass.test(contrasena)==false){
		return false;
	}
}
function peticionAlta(){
	var error;
	var todoBien=false;
	var nombre=document.getElementById("id_nombre").value;
	var p0=document.getElementById("error_nombre");
	if(nombre==""){		
		error="Debes introducir un nombre";
		document.getElementById("div_error_nombre").className ="error";
		document.getElementById("div_error_nombre").style.diplay="block";
		p0.innerHTML=error;
	}else{
		p0.innerHTML="";
		document.getElementById("div_error_nombre").className ="ocultar";
		todoBien=true;
	}
	var apellidos=document.getElementById("id_apellidos").value;
	var p1=document.getElementById("error_apellidos");
	if(apellidos==""){
		error="Debes introducir un apellidos";
		document.getElementById("div_error_apellidos").className ="error";
		document.getElementById("div_error_apellidos").style.diplay="block";
		p1.innerHTML=error;
	}else{
		p1.innerHTML="";
		document.getElementById("div_error_apellidos").className ="ocultar";
		todoBien=true;
	}
	var dni=document.getElementById("id_dni").value;
	var compruebaDNI=validaDni(dni);
	var p2=document.getElementById("error_dni");
	if(dni==""){
		error="Debes introducir el DNI";
		document.getElementById("div_error_dni").className ="error";
		document.getElementById("div_error_dni").style.diplay="block";
		p2.innerHTML=error;
	}else if (compruebaDNI== false) {
		error="Formato de DNI invalido";
		document.getElementById("div_error_dni").className ="error";
		document.getElementById("div_error_dni").style.diplay="block";
		p2.innerHTML=error;	
	}else if (compruebaDNI== "no es valido"){
		error="DNI invalido";
		document.getElementById("div_error_dni").className ="error";
		document.getElementById("div_error_dni").style.diplay="block";
		p2.innerHTML=error;
	}else{
		p2.innerHTML="";
		document.getElementById("div_error_dni").className ="ocultar";
		todoBien=true;
	}
	var email=document.getElementById("id_email").value;
	var compruebaEmail=validaEmail(email);
	var p3=document.getElementById("error_email");
	if(email==""){
		error="Debes introducir un Email";
		document.getElementById("div_error_email").className ="error";
		p3.innerHTML=error;
	}else if (compruebaEmail== false) {
		error="Formato de Email invalido";
		document.getElementById("div_error_email").className ="error";
		p3.innerHTML=error;	
	}else if (compruebaEmail== "no valido"){
		
		error="Tu dominio no existe , prueba con gmail.com,hotmail.com,yahoo.es";
		document.getElementById("div_error_email").className ="error";
		document.getElementById("div_error_email").style.diplay="block";
		p3.innerHTML=error;
	}else{
		p3.innerHTML="";
		document.getElementById("div_error_email").className ="ocultar";
		todoBien=true;
	}
	var fecha=document.getElementById("id_nacimiento").value;
	var p4=document.getElementById("error_fecha");
	if(fecha==""){
		error="Debes introducir una Fecha";
		document.getElementById("div_error_fecha").className ="error";
		document.getElementById("div_error_fecha").style.diplay="block";
		p4.innerHTML=error;
	}else if(validaFecha(fecha)==false){
		error="Debes introducir una Fecha menor que actual";
		document.getElementById("div_error_fecha").className ="error";
		document.getElementById("div_error_fecha").style.diplay="block";
		p4.innerHTML=error;
	}else{
		p4.innerHTML="";
		document.getElementById("div_error_fecha").className ="ocultar";
		todoBien=true;
	}
	var contrasena=document.getElementById("id_pass").value;
	var p5=document.getElementById("error_pass");
	if(contrasena==""){
		error="Debes introducir una Contraseña";
		document.getElementById("div_error_pass").className ="error";
		document.getElementById("div_error_pass").style.diplay="block";
		//p5.innerHTML=error;
	}else if(validaPass(contrasena)==false){
		error="La contraseña debe tener al entre 8 y 16 caracteres, al menos un dígito, al menos una minúscula y al menos una mayúscula.";
		document.getElementById("div_error_pass").className ="error";
		document.getElementById("div_error_pass").style.diplay="block";
		p5.innerHTML=error;
	}else{
		p5.innerHTML="";
		document.getElementById("div_error_pass").className ="ocultar";
		todoBien=true;
	}
	var confir=document.getElementById("id_confir").value;
	var p6=document.getElementById("error_confir");
	if(confir==""){
		error="Debes introducir la Contraseña anterior";
		document.getElementById("div_error_confir").className ="error";
		document.getElementById("div_error_confir").style.diplay="block";
		p6.innerHTML=error;
	}else if(contrasena!=confir){
		error="Las contraseña no coinciden";
		document.getElementById("div_error_confir").className ="error";
		document.getElementById("div_error_confir").style.diplay="block";
		p6.innerHTML=error;
	}else{
		p6.innerHTML="";
		document.getElementById("div_error_confir").className ="ocultar";
		todoBien=true;
	}
	
	if(todoBien==true){
		var myobj={nom:nombre,ape:apellidos,dni:dni,email:email,nac:fecha,pass:contrasena,conf:confir}
		myobj=JSON.stringify(myobj);
		var peticion=new XMLHttpRequest();
		peticion.addEventListener("readystatechange",gestionarAlta,false);
		peticion.open("POST","programas/alta/php/alta.php",false);
		peticion.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		var datos = "x="+myobj;
		peticion.send(datos);
	}	
}	
function gestionarAlta(evento){
	if (evento.target.readyState == 4 && evento.target.status == 200) {
			
			respuesta = JSON.parse(evento.target.responseText);
			if(respuesta=="Usuario registrado"){
				respuestaAlta(respuesta);
			}else{
				alert(respuesta);
			}
        }
}	
function respuestaAlta(respuesta){
	alert("Usuario dado de alta");	
	window.location="index.php";

}
	