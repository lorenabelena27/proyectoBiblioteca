document.addEventListener("readystatechange",cargarEvento,false);

function cargarEvento(){
	if(document.readyState=="interactive"){
		document.getElementById("reservar").addEventListener("click",reserva,false);
	}
}
function reserva(){
	var libro=document.getElementById("codigo").innerHTML;
	alert(libro);
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
		alert("RESPUESTA: "+evento.target.responseText);
		respuesta = JSON.parse(evento.target.responseText);
		alert("RESPUESTA: "+respuesta.length);
		respuestaReserva(respuesta);
	}
}
function respuestaReserva(respuesta){
		alert(respuesta);
		if(respuesta[0]=="RESERVAR"){
			var fechaR = respuesta[1];
			alert("vamos a reservar");
			if(confirm("¿Quieres reservar "+document.getElementById("informacionRes").firstChild.innerHTML+"? \n Fecha aproximada: "+fechaR)){
				alert("RESERVANDO...");
				var libro=document.getElementById("codigo").innerHTML;
				reservarLibro(libro,fechaR);
			}
		}
}

function reservarLibro(codigo,fecha){
	alert(codigo);
	alert(fecha);
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
	respuestaReserva(respuesta);
	}
}
function respuestaReservarL(respuesta){
	alert(respuesta);
}