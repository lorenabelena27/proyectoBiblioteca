<?php

class ConectaBD{
	private $archivo;
	private $contenido;
	private $bd;
	private $usuario_admin;
	private $clave_admin;
	
	private $db;
	private static $instance = NULL;
	
	private function __construct(){
		$this->archivo =  "../../../basededatos/biblioteca.conf"; 

		$this->contenido = parse_ini_file( $this->archivo, true); 
		$this->bd= $this->contenido["bd"];
		$this->usuario_admin= $this->contenido["usuario_admin"];
		$this->clave_admin= $this->contenido["clave_admin"];
		
		$dsn="mysql:host=localhost;dbname=".$this->bd .";charset=utf8";
		$usu=$this->usuario_admin;
		$pass=$this->clave_admin;
		try{
			$this->db =new PDO($dsn,$usu,$pass);
		}catch(PDOException $e){
			die("Error:".$e->getMessage()."</br>");
		}
	}
	private function __clone() { }
	
	public static function getInstance(){
        if (is_null(self::$instance)) {
            self::$instance = new ConectaBD();
        }
        return self::$instance->db;
    }
	public function getConBD(){return $this ->db;}
}	
?>