document.addEventListener("readystatechange",cargarEvento,false);
//funcion para validar el formato del dni
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
		document.getElementById("devolverLibro").style.display="none";
		document.getElementById("pedirLibro").addEventListener("click",peticionPedir,false);
	}
}

function peticionPedir(){
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
	}else if (compruebaDNI == false) {
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
		error="Debe introducir el codigo de libro";
		document.getElementById("error_codigo").innerHTML= error;
	}else{
		todoBien=true;
	}
	//realiza la peticion
	if(todoBien==true){
		var myobj={dni:dni,codigo:codigo}
		myobj=JSON.stringify(myobj);
		var peticion=new XMLHttpRequest();
		peticion.addEventListener("readystatechange",gestionarPedir,false);
		peticion.open("POST","programas/prestar/php/pedir.php",false);
		peticion.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		var datos = "x="+myobj;
		peticion.send(datos);
	}
	
}

function gestionarPedir(evento){
	
	if (evento.target.readyState == 4 && evento.target.status == 200) {
		respuesta = JSON.parse(evento.target.responseText);
		respuestaPedir(respuesta);
    }
}
//funcion para pedir un libro
function respuestaPedir(respuesta){
	document.getElementById("dni_usu").value="";
	document.getElementById("cod_lib").value="";
	if(respuesta=="RESERVADO"){
		alert("El usuario ya tiene el libro reservado");
	}else if(respuesta=="PRESTADO"){
		alert("El usuario ya tiene el libro prestado");
	}else if(respuesta=="SANCIONADO"){
		alert("El usuario está sancionado");
	}else if(respuesta[0]=="RESERVAR"){
		var fechaR=respuesta[1];
		var dni=respuesta[2];
		var cod=respuesta[3];
		var sep1 = new RegExp("-", "g");
		var fechaRes = fechaR.split(sep1);
		if(confirm("No está disponible \n Fecha aproximada en la que estara disponible: "+fechaRes[2]+"-"+fechaRes[1]+"-"+fechaRes[0]+" \n¿Quieres reservar? ")){
			reservarLibro(cod,fechaR,dni);
		}
	}else if(respuesta[0]=="PRESTAR"){
		var fechaIni=respuesta[1];
		var fechaFin=respuesta[2];
		var dni=respuesta[3];
		var cod=respuesta[4]
		var sep1 = new RegExp("-", "g");
		var fechaI = fechaIni.split(sep1);
		var fechaF = fechaFin.split(sep1);
		if(confirm("¿Quieres realizar un préstamo ? \n Desde: "+fechaI[2]+"-"+fechaI[1]+"-"+fechaI[0]+" \n Hasta: "+fechaF[2]+"-"+fechaF[1]+"-"+fechaF[0])){
			prestarLibro(cod,fechaIni,fechaFin,dni);
		}
	}			
}
//funcion para reservar el libro
function reservarLibro(codigo,fecha,dni){

	var myobj={codigoRes:codigo,fechaRes:fecha,dniRes:dni}
	myobj=JSON.stringify(myobj);
	var peticion=new XMLHttpRequest();
	peticion.addEventListener("readystatechange",gestionarReservarL,false);
	peticion.open("POST","programas/prestar/php/pedir.php",false);
	peticion.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	var datos = "x="+myobj;
	peticion.send(datos);
	
}
function gestionarReservarL(evento){
	
	if (evento.target.readyState == 4 && evento.target.status == 200) {
	respuesta = JSON.parse(evento.target.responseText);
	respuestaReservarL(respuesta);
	}
}

function respuestaReservarL(respuesta){
	alert("Libro reservado");
}
//funcion para prestar el libro
function prestarLibro(codigo,fechaIni,fechaFin,dni){

	var myobj={codigoPrestar:codigo,fechaIni:fechaIni, fechaFin:fechaFin,dniPrestar:dni}
	myobj=JSON.stringify(myobj);
	var peticion=new XMLHttpRequest();
	peticion.addEventListener("readystatechange",gestionarPrestarL,false);
	peticion.open("POST","programas/prestar/php/pedir.php",false);
	peticion.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	var datos = "x="+myobj;
	peticion.send(datos);
	
}

function gestionarPrestarL(evento){
	
	if (evento.target.readyState == 4 && evento.target.status == 200) {
	respuesta = JSON.parse(evento.target.responseText);
	respuestaPrestar(respuesta);
	}
}
function respuestaPrestar(respuesta){
	alert("Libro prestado");
}