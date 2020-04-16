<?php
		$peticion = json_decode($_GET['x']);
		if(isset($peticion->borrar_cuen)){
			$cuenta = $peticion->borrar_cuen;
			$con= Conecta::getInstance();
			$con->borrar($cuenta);
		}elseif(isset($peticion->comprobar_cuen)){
			$cuenta = $peticion->comprobar_cuen;
			$con= Conecta::getInstance();
			$con->saldo($cuenta);
		}else{
		$cuenta = $peticion -> cuen;
		$mal=false;
		for($i=0; $i < strlen($cuenta); $i++){
		  if(!is_numeric(substr($cuenta,$i,1))){
			$mal=true;
		  }
		}
		if($mal==true){
			echo json_encode("Número de cuenta incorrecto");
		}elseif(isset($peticion->cuen) && isset($peticion->op) && isset($peticion->desc) && isset($peticion->imp)){
			$cuenta = $peticion -> cuen;
			$op = $peticion -> op;
			$desc = $peticion -> desc;
			$imp = $peticion -> imp;
			$con= Conecta::getInstance();
			if($op == 0){
				$con->ingresar($cuenta,$imp,$desc);
				$con->actualiza_cli($cuenta,$op,$imp);
			}elseif($op == 1){
				$con->retirar($cuenta,$imp,$desc);
				$con->actualiza_cli($cuenta,$op,$imp);
			}
		}else{
			$suma = 0;
			for($i=0; $i < strlen($cuenta); $i++){
			  if($i < (strlen($cuenta)-1)){
				$suma+= substr($cuenta,$i,1);
			  }
			}
			if($suma%9!=substr($cuenta,strlen($cuenta)-1,1)){
            	echo json_encode("Número de cuenta incorrecto");
            }else{
            	$con= Conecta::getInstance();
			if($con->existe_Cuenta($cuenta)){
				$con->datos($cuenta);
			}else{
				echo json_encode("no cuenta");
			}
            }
			
		}
		}
	//}
//}
class Conecta{
	private $archivo;
	private $contenido;
	private $bd;
	private $usuario_admin;
	private $clave_admin;
	
	private $db;
	private static $instance = NULL;
	
	private function __construct(){
		$this->archivo = "../conf/banco.conf"; 
		$this->contenido = parse_ini_file( $this->archivo, true); 
		$this->bd= $this->contenido["bd"];
		$this->usuario_admin= $this->contenido["usuario_admin"];
		$this->clave_admin= $this->contenido["clave_admin"];
		
		$dsn="mysql:host=localhost;dbname=Banco;charset=utf8";
		$usu= $this->usuario_admin;
		$pass= $this->clave_admin;
		try{
			$this->db =new PDO($dsn,$usu,$pass);
		}catch(PDOException $e){
			die("Error:".$e->getMessage()."</br>");
		}
	}
	private function __clone() { }
	
	public static function getInstance(){
        if (is_null(self::$instance)) {
            self::$instance = new Conecta();
        }
        return self::$instance;
    }
	public function getConBD(){return $this ->db;}
	//FUNCIONES COMUNES
	
	public function existe_Cuenta($cuenta){
		$existe = false;
		if ( !( $query = $this ->db->prepare( "select * from cuentas where cu_ncu=:cuenta " ) ) ){
			echo "Falló la preparacioón: " . $this ->db->errno . " - " . $this ->db->error; 
		}elseif ( ! $query->bindParam( ":cuenta", $cuenta) ) { 
			echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
		}else{
			$query->execute();
			$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
			if(!empty($resultado)){
				$resultado = true;
			}
			
			return $resultado;
		}
		
	}
	
	//FUNCIONES DE CIERRE DE CUENTAS
	public function datos($cuenta){
		$datos = array();
		if ( !( $query = $this ->db->prepare( "SELECT * from cuentas where cu_ncu = :cuenta" ) ) ){
			echo "Falló la preparacioón: " . $this ->db->errno . " - " . $this ->db->error; 
		}elseif ( ! $query->bindParam( ":cuenta", $cuenta) ) { 
			echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
		}else{
			$query->execute();
			$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
			array_push($datos, $resultado[0]);
			if ( !( $query = $this ->db->prepare( "SELECT * from clientes where cl_dni = (select cu_dn1 from cuentas where cu_ncu =:cuenta)" ) ) ){
			echo "Falló la preparacioón: " . $this ->db->errno . " - " . $this ->db->error; 
		}elseif ( ! $query->bindParam( ":cuenta", $cuenta) ) { 
			echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
		}else{
			$query->execute();
			$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
			array_push($datos, $resultado[0]);
			
		}
		if ( !( $query = $this ->db->prepare( "SELECT * from clientes where cl_dni = (select cu_dn2 from cuentas where cu_ncu =:cuenta)" ) ) ){
			echo "Falló la preparacioón: " . $this ->db->errno . " - " . $this ->db->error; 
		}elseif ( ! $query->bindParam( ":cuenta", $cuenta) ) { 
			echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
		}else{
			$query->execute();
			$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
			if($resultado != null){
				array_push($datos, $resultado[0]);
			}
			
			echo json_encode($datos);
		}
		}
		
	}
	
	public function saldo($cuenta){
		if ( !( $query = $this ->db->prepare( "SELECT cu_sal from cuentas where cu_ncu = :cuenta" ) ) ){
			echo "Falló la preparacioón: " . $this ->db->errno . " - " . $this ->db->error; 
		}elseif ( ! $query->bindParam( ":cuenta", $cuenta) ) { 
			echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
		}else{
			$query->execute();
			$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
			echo json_encode($resultado[0]["cu_sal"]);
			
		}
		
	}
	
	public function borrar($cuenta){
		if ( !( $query = $this ->db->prepare( "delete from movimientos where mo_ncu = :cuenta" ) ) ){
			echo "Falló la preparacioón: " . $this ->db->errno . " - " . $this ->db->error; 
		}elseif ( ! $query->bindParam( ":cuenta", $cuenta) ) { 
			echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
		}else{
			$query->execute();
			if ( !( $query = $this ->db->prepare( "update clientes set cl_ncu = (cl_ncu-1) where
			cl_dni = (select cu_dn1 from cuentas where cu_ncu = :cuenta)
			or cl_dni = (select cu_dn2 from cuentas where cu_ncu = :cuenta)" ) ) ){
			echo "Falló la preparacioón: " . $this ->db->errno . " - " . $this ->db->error; 
		}elseif ( ! $query->bindParam( ":cuenta", $cuenta) ) { 
			echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
		}else{
			$query->execute();
			if ( !( $query = $this ->db->prepare( "delete from clientes where cl_ncu = 0" ) ) ){
			echo "Falló la preparacioón: " . $this ->db->errno . " - " . $this ->db->error; 
		}else{
			$query->execute();
			if ( !( $query = $this ->db->prepare( "delete from cuentas where cu_ncu = :cuenta" ) ) ){
			echo "Falló la preparacioón: " . $this ->db->errno . " - " . $this ->db->error; 
			}elseif ( ! $query->bindParam( ":cuenta", $cuenta) ) { 
			echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
		}	else{
			$query->execute();
			
		}
		}
			
		}
		
	}
}

//FUNCIONES DE INGRESOS Y REINTEGROS
public function existe_Cuenta_op($cuenta){
		$existe = false;
		if ( !( $query = $this ->db->prepare( "select cu_sal from cuentas where cu_ncu=:cuenta " ) ) ){
			echo "Falló la preparacioón: " . $this ->db->errno . " - " . $this ->db->error; 
		}elseif ( ! $query->bindParam( ":cuenta", $cuenta) ) { 
			echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
		}else{
			$query->execute();
			$resultado= $query ->fetchAll(PDO::FETCH_ASSOC);
			//$resultado = $resultado["cu_sal"];
			if(empty($resultado)){
				$resultado = false;
			}
			
			return $resultado;
		}
		
	}
public function ingresar($cuenta,$imp,$desc){
		if ( !( $query = $this ->db->prepare( "update cuentas set cu_sal= (cu_sal + :imp) where cu_ncu = :cuenta  " ) ) ){
			echo "Falló la preparacioón: " . $this ->db->errno . " - " . $this ->db->error; 
		}elseif ( ! $query->bindParam( ":imp", $imp) ) { 
			echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
		}elseif ( ! $query->bindParam( ":cuenta", $cuenta) ) { 
			echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
		}else{
			$query->execute();
			if($query->rowCount() > 0){
				$fecha = date("Y-m-d");
				$hora = date("H:i");
				if ( !( $query = $this ->db->prepare( "insert into movimientos (mo_ncu,mo_fec,mo_hor,mo_des,mo_imp) values (:cuenta, :fecha, :hora, :descripcion, :importe) " ) )){
					echo "Falló la preparacioón: " . $this ->db->errno . " - " . $this ->db->error; 
				}elseif ( ! $query->bindParam( ":cuenta", $cuenta) ) { 
					echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
				}elseif ( ! $query->bindParam( ":fecha", $fecha) ) { 
					echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
				}elseif ( ! $query->bindParam( ":hora", $hora) ) { 
					echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
				}elseif ( ! $query->bindParam( ":descripcion", $desc) ) { 
					echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
				}elseif ( ! $query->bindParam( ":importe", $imp) ) { 
					echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
				}else{
					$query->execute();
					}
			}else{
				echo json_encode("No se ha podido realizar la operación");
			}
		}
	}
	
	public function retirar($cuenta,$imp, $desc){
		if ( !( $query = $this ->db->prepare( "update cuentas set cu_sal= (cu_sal - :imp) where cu_ncu = :cuenta  " ) ) ){
			echo "Falló la preparacioón: " . $this ->db->errno . " - " . $this ->db->error; 
		}elseif ( ! $query->bindParam( ":imp", $imp) ) { 
			echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
		}elseif ( ! $query->bindParam( ":cuenta", $cuenta) ) { 
			echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
		}else{
			$query->execute();
			if($query->rowCount() > 0){
				$fecha = date("Y-m-d");
				$hora = date("H:i");
				$imp = -$imp;
				if ( !( $query = $this ->db->prepare( "insert into movimientos (mo_ncu,mo_fec,mo_hor,mo_des,mo_imp) values (:cuenta, :fecha, :hora, :descripcion, :importe) " ) )){
					echo "Falló la preparacioón: " . $this ->db->errno . " - " . $this ->db->error; 
				}elseif ( ! $query->bindParam( ":cuenta", $cuenta) ) { 
					echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
				}elseif ( ! $query->bindParam( ":fecha", $fecha) ) { 
					echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
				}elseif ( ! $query->bindParam( ":hora", $hora) ) { 
					echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
				}elseif ( ! $query->bindParam( ":descripcion", $desc) ) { 
					echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
				}elseif ( ! $query->bindParam( ":importe", $imp) ) { 
					echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
				}else{
					$query->execute();
					}
			}else{
				echo json_encode("No se ha podido realizar la operación");
			}
		}

		
	}
	public function actualiza_cli($cuenta,$op,$imp){
		if($op==0){
			if ( !( $query = $this ->db->prepare( "update clientes set cl_sal= (cl_sal + :imp) 
				where cl_dni = (select cu_dn1 from cuentas where cu_ncu = :cuenta)
				or cl_dni = (select cu_dn2 from cuentas where cu_ncu = :cuenta)" ) ) ){
			echo "Falló la preparacioón: " . $this ->db->errno . " - " . $this ->db->error; 
			}elseif ( ! $query->bindParam( ":imp", $imp) ) { 
				echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
			}elseif ( ! $query->bindParam( ":cuenta", $cuenta) ) { 
				echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
			}else{
				$query->execute();
			}
			}elseif($op==1){
				if ( !( $query = $this ->db->prepare( "update clientes set cl_sal= (cl_sal - :imp) 
				where cl_dni = (select cu_dn1 from cuentas where cu_ncu = :cuenta)
				or cl_dni = (select cu_dn2 from cuentas where cu_ncu = :cuenta)" ) ) ){
			echo "Falló la preparacioón: " . $this ->db->errno . " - " . $this ->db->error; 
			}elseif ( ! $query->bindParam( ":imp", $imp) ) { 
				echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
			}elseif ( ! $query->bindParam( ":cuenta", $cuenta) ) { 
				echo "Falló la ejecución: " . $query->errno . "- " . $query->error;
			}else{
				$query->execute();
			}
			}
	}
}

?>
	
