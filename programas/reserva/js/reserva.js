document.addEventListener("readystatechange",cargarEvento,false);
//funcion cargar evento del boton reservar
function cargarEvento(){
	
	if(document.readyState=="interactive"){
		document.getElementById("reservar").addEventListener("click",reserva,false);
	}
}
//funcion para realizar la peticion a php
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
//funcion para gestionar la respuesta
function gestionarReserva(evento){
	
	if (evento.target.readyState == 4 && evento.target.status == 200) {
		respuesta = JSON.parse(evento.target.responseText);
		respuestaReserva(respuesta);
	}
}
//fucion repuesta de la reserva
function respuestaReserva(respuesta){
	//dependiendo de la respuesta se realiza una información de cada uno de los casos 
		document.getElementById("estadoLibro").innerHTML="";
		var titulo=document.getElementById("informacionRes").firstChild.innerHTML;
		//el libro se realiza con exito
		if(respuesta=="RESERVADO"){
			document.getElementById("estadoLibro").innerHTML="Ya tienes "+titulo+" reservado, puedes consultar los detalles en la sección \"Mis Libros\"";
			document.getElementById("estadoLibro").style.display="block";
		//el libro ya lo tiene prestado
		}else if(respuesta=="PRESTADO"){
			document.getElementById("estadoLibro").innerHTML="Ya tienes "+titulo+" en préstamo, puedes consultar los detalles en la sección \"Mis Libros\"";
			document.getElementById("estadoLibro").style.display="block";
		//no podra sacar el libro por sancion
		}else if(respuesta=="SANCIONADO"){
			document.getElementById("estadoLibro").innerHTML="No puede reservar ningun libro usted está sancionado";
			document.getElementById("estadoLibro").style.display="block";
		}else if(respuesta[0]=="RESERVAR"){
			//el libro no esta disponible
			var fechaR=respuesta[1];
			var sep1 = new RegExp("-", "g");
			var fechaRes = fechaR.split(sep1);
			//se confirma si quiere el libro y si es asi entrara en lista de espera
			if(confirm(document.getElementById("informacionRes").firstChild.innerHTML+" no está disponible \n Fecha aproximada en la que estara disponible: "+fechaRes[2]+"-"+fechaRes[1]+"-"+fechaRes[0]+" \n¿Quieres reservar? ")){
				var libro=document.getElementById("codigo").innerHTML;
				reservarLibro(libro,fechaR);
			}
		}else if(respuesta[0]=="PRESTAR"){
			//el libro esta disponible
			var fechaIni=respuesta[1];
			var fechaFin=respuesta[2];
			var sep1 = new RegExp("-", "g");
			var fechaI = fechaIni.split(sep1);
			var fechaF = fechaFin.split(sep1);
			//se confirma la reserva
			if(confirm("¿Quieres realizar un préstamo de "+document.getElementById("informacionRes").firstChild.innerHTML+"? \n Desde: "+fechaI[2]+"-"+fechaI[1]+"-"+fechaI[0]+" \n Hasta: "+fechaF[2]+"-"+fechaF[1]+"-"+fechaF[0])){
				var libro=document.getElementById("codigo").innerHTML;
				prestarLibro(libro,fechaIni,fechaFin);
			}
		}								
}
//se realiza la peticion para reservar el libro
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
//funcion que gestiona la respuesta
function gestionarReservarL(evento){
	
	if (evento.target.readyState == 4 && evento.target.status == 200) {
	respuesta = JSON.parse(evento.target.responseText);
	respuestaReservarL(respuesta);
	}
}
//se muestra la respuesta
function respuestaReservarL(respuesta){
	//se trasforma la fecha para que el usuario la vea
	var fechaR=respuesta;
	var sep1 = new RegExp("-", "g");
	var fechaRes = fechaR.split(sep1);
	var titulo=document.getElementById("informacionRes").firstChild.innerHTML;
	document.getElementById("estadoLibro").innerHTML="Reserva realizada de "+titulo+" a partir del día "+fechaRes[2]+"-"+fechaRes[1]+"-"+fechaRes[0];
	document.getElementById("estadoLibro").style.display="block";
	
}
//peticion a php para prestar un libro
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
//funcion que gestiona la respuesta
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