document.addEventListener("readystatechange",cargarEvento,false);
//funcion para comprobar el dni del usuario si tiene un formato valido
function validaDni(dni){
	
	var patronDNI=/^[0-9]{8}[A-Za-z]$/;
	var letras = ['T', 'R', 'W', 'A', 'G', 'M', 'Y', 'F', 'P', 'D', 'X', 'B', 'N', 'J', 'Z', 'S', 'Q', 'V', 'H', 'L', 'C', 'K', 'E', 'T'];
	var letrasMin = ['t', 'r', 'w', 'a', 'g', 'm', 'y', 'f', 'p', 'd', 'x', 'b', 'n', 'j', 'z', 's', 'q', 'v', 'h', 'l', 'c', 'k', 'e', 't'];
	if(patronDNI.test(dni)==false){	
		return false;
	}else{
		var letraDNI = dni.substring(8, 9).toUpperCase();
		var numDNI = parseInt(dni.substring(0, 8));
		var letraCorrecta = letras[numDNI % 23];
		var letraCorrectaM = letrasMin[numDNI % 23];
			if(letraDNI!= letraCorrecta && letraDNI!= letraCorrectaM ){
				return "no es valido";	
			} else{
				return true;
			}
	}
}
function cargarEvento(){
	
	if(document.readyState=="interactive"){
		document.getElementById("pedirLibro").style.display="none";
		document.getElementById("devolverLibro").addEventListener("click",peticionDevolver,false);
	}
}
function peticionDevolver(){
	//preparacion para hacer la peticion
	var dni =document.getElementById("dni_usu").value;
	var p1=document.getElementById("error_dni");
	var compruebaDNI=validaDni(dni);
	var todoBien=false;
	var error="";
	if(dni==""){
		todoBien=false;
		error="Debe introducir un número de dni";
		p1.innerHTML=error;
	}else if (compruebaDNI== false) {
		todoBien=false;
		error="Formato de DNI invalido";	
		p1.innerHTML=error;
	}else if (compruebaDNI== "no es valido"){
		todoBien=false;
		error="DNI invalido";
		p1.innerHTML=error;
	}else{
		todoBien=true;
	}
	var codigo =document.getElementById("cod_lib").value;
	var p2=document.getElementById("error_codigo");
	if(codigo==""){
		todoBien=false;
		error="Debe introducir una codigo de libro";
		document.getElementById("error_codigo").innerHTML= error;
	}else{
		todoBien=true;
	}
	//si todo esta bien se hace la  peticion
	if(todoBien==true){
		var myobj={dni:dni,codigo:codigo}
		myobj=JSON.stringify(myobj);
		var peticion=new XMLHttpRequest();
		peticion.addEventListener("readystatechange",gestionarDevolver,false);
		peticion.open("POST","programas/devolver/php/devolver.php",false);
		peticion.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		var datos = "x="+myobj;
		peticion.send(datos);
	}
	
}
function gestionarDevolver(evento){
	
	if (evento.target.readyState == 4 && evento.target.status == 200) {
		respuesta = JSON.parse(evento.target.responseText);
		respuestaDevolver(respuesta);
    }
}
function respuestaDevolver(respuesta){
	alert(respuesta);
	document.getElementById("dni_usu").value="";
	document.getElementById("cod_lib").value="";
}