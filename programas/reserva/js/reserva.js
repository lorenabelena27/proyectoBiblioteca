document.addEventListener("readystatechange",cargarEvento,false);

function cargarEvento(){
	
	if(document.readyState=="interactive"){
		document.getElementById("reservar").addEventListener("click",reserva,false);
	}
}
function reserva(){
	
	var libro=document.getElementById("codigo").innerHTML;
	var existe=true;
	var myobj={reserva:existe,codigo:libro}
	myobj=JSON.stringify(myobj);
	var peticion=new XMLHttpRequest();
	peticion.addEventListener("readystatechange",gestionarReserva,false);
	peticion.open("POST","programas/reserva/php/reserva.php",false);
	peticion.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	var datos = "x="+myobj;
	peticion.send(datos);

}
function gestionarReserva(evento){
	
	if (evento.target.readyState == 4 && evento.target.status == 200) {
		respuesta = JSON.parse(evento.target.responseText);
		respuestaReserva(respuesta);
	}
}
function respuestaReserva(respuesta){
	
		document.getElementById("estadoLibro").innerHTML="";
		var titulo=document.getElementById("informacionRes").firstChild.innerHTML;
		if(respuesta=="RESERVADO"){
			document.getElementById("estadoLibro").innerHTML="Ya tienes "+titulo+" reservado, puedes consultar los detalles en la sección \"Mis Libros\"";
			document.getElementById("estadoLibro").style.display="block";
		}else if(respuesta=="PRESTADO"){
			document.getElementById("estadoLibro").innerHTML="Ya tienes "+titulo+" en préstamo, puedes consultar los detalles en la sección \"Mis Libros\"";
			document.getElementById("estadoLibro").style.display="block";
		}else if(respuesta[0]=="RESERVAR"){
			var fechaR=respuesta[1];
			var sep1 = new RegExp("-", "g");
			var fechaRes = fechaR.split(sep1);
			if(confirm(document.getElementById("informacionRes").firstChild.innerHTML+" no está disponible \n Fecha aproximada en la que estara disponible: "+fechaRes[2]+"-"+fechaRes[1]+"-"+fechaRes[0]+" \n¿Quieres reservar? ")){
				var libro=document.getElementById("codigo").innerHTML;
				reservarLibro(libro,fechaR);
			}
		}else if(respuesta[0]=="PRESTAR"){
			var fechaIni=respuesta[1];
			var fechaFin=respuesta[2];
			var sep1 = new RegExp("-", "g");
			var fechaI = fechaIni.split(sep1);
			var fechaF = fechaFin.split(sep1);
			alert(fechaF);
			if(confirm("¿Quieres realizar un préstamo de "+document.getElementById("informacionRes").firstChild.innerHTML+"? \n Desde: "+fechaI[2]+"-"+fechaI[1]+"-"+fechaI[0]+" \n Hasta: "+fechaF[2]+"-"+fechaF[1]+"-"+fechaF[0])){
				var libro=document.getElementById("codigo").innerHTML;
				prestarLibro(libro,fechaIni,fechaFin);
			}
		}								
}

function reservarLibro(codigo,fecha){

	var myobj={codigo:codigo,fecha:fecha}
	myobj=JSON.stringify(myobj);
	var peticion=new XMLHttpRequest();
	peticion.addEventListener("readystatechange",gestionarReservarL,false);
	peticion.open("POST","programas/reserva/php/reserva.php",false);
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
	
	var titulo=document.getElementById("informacionRes").firstChild.innerHTML;
	document.getElementById("estadoLibro").innerHTML="Reserva realizada de "+titulo+" a partir del día "+respuesta;
	document.getElementById("estadoLibro").style.display="block";
	
}

function prestarLibro(codigo,fechaIni,fechaFin){

	var myobj={codigoPrestar:codigo,fechaIni:fechaIni, fechaFin:fechaFin}
	myobj=JSON.stringify(myobj);
	var peticion=new XMLHttpRequest();
	peticion.addEventListener("readystatechange",gestionarPrestarL,false);
	peticion.open("POST","programas/reserva/php/reserva.php",false);
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
	
	var titulo=document.getElementById("informacionRes").firstChild.innerHTML;
	document.getElementById("estadoLibro").innerHTML="Has realizado un préstamo de "+titulo+" puede pasar a recogerlo";
	document.getElementById("estadoLibro").style.display="block";
}