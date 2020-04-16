<?php
class ConectaBD{
	private $archivo;
	private $contenido;
	private $bd;
	private $usuario_admin;
	private $clave_admin;
	
	private $db;
	private static $instance = NULL;
	private function __construct() {
		$this->archivo = "biblioteca.conf"; 
		$this->contenido = parse_ini_file( $this->archivo, true); 
		$this->bd= $this->contenido["bd"];
		$this->usuario_admin= $this->contenido["usuario_admin"];
		$this->clave_admin= $this->contenido["clave_admin"];
		
		$dsn="mysql:host=localhost;charset=utf8";
		$usuario= $this->usuario_admin;
		$pass=$this->clave_admin;
		try{
			$this->db =new PDO($dsn,$usuario,$pass);
			$this->db->exec("set character set utf8");
		}catch(PDOException $e){
			die("Error:".$e->getMessage()."</br>");
		}
	}
	
	private function __clone() { }
	
	public static function getInstance(){
		if (is_null(self::$instance)) {
			self::$instance = new conectaBD();
		}
		return self::$instance;
	}
	
	public function getConBD(){return $this ->db;}
	
	public function creaBase(){
		$sql="CREATE DATABASE Biblioteca";
		if(!$this->db->query($sql)){
			echo "ERROR: La base de datos \"".$this->bd ."\" ya existe o no se ha podido crear.<br>";
		}else{
			echo "Base de datos \"".$this->bd ."\" creada con éxito.<br>";
		}
	}
	public function creaTablas(){
		$sql="use Biblioteca";
		if(!$this->db->query($sql)){
			echo "ERROR: No se puede acceder a la base de datos \"".$this->bd ."\".<br>";
		}
		$sql = "CREATE TABLE Libros (cod_libro VARCHAR(9) NOT NULL, " .
				"titulo VARCHAR(30) NOT NULL, " .
				"autor VARCHAR(20) NOT NULL, " .
				"genero VARCHAR(9) NOT NULL, " .
				"año_edicion DATE , " .
				"editorial VARCHAR(20) NOT NULL, " .
				"disponible VARCHAR(2), " .
				"PRIMARY KEY (cod_libro)) ENGINE = MYISAM;";
		if(!$this->db->query($sql)){
			echo "ERROR: La tabla \"Libros\" ya existe o no se ha podido crear.<br>";
		}else{
			echo "Tabla \"Libros\" creada con éxito.<br>";
		}
		$sql = "CREATE TABLE Prestamos (cod_libro VARCHAR(9) NOT NULL," .
				"fecha_salida DATE , " .
				"fecha_devolucion DATE , " .
				"dni VARCHAR(10) NOT NULL, " .
				"FOREIGN KEY (cod_libro) REFERENCES Libros (cod_libro)," .
				"FOREIGN KEY (dni) REFERENCES Usuarios (dni)" .
				")ENGINE = MYISAM;";
		if(!$this->db->query($sql)){
			echo "ERROR: La tabla \"Prestamos\" ya existe o no se ha podido crear.<br>";
		}else{
			echo "Tabla \"Prestamos\" creada con éxito.<br>";
		}
		$sql = "CREATE TABLE Usuarios (nombre VARCHAR(20) NOT NULL, " .
				"apellidos VARCHAR(20) NOT NULL, " .
				"dni VARCHAR(10) NOT NULL, " .
				"email VARCHAR(40) NOT NULL, " .
				"fecha_nacimiento DATE , " .
				"contraseña VARCHAR(50) NOT NULL, " .
				"PRIMARY KEY (dni)) ENGINE = MYISAM;";
		if(!$this->db->query($sql)){
			echo "ERROR: La tabla \"Usuarios\" ya existe o no se ha podido crear.<br>";
		}else{
			echo "Tabla \"Usuarios\" creada con éxito.<br>";
		}
				$sql = "CREATE TABLE Sancionados (nombre VARCHAR(20) NOT NULL, " .
				"dni VARCHAR(10) NOT NULL, " .
				"hasta DATE , " .
				"FOREIGN KEY (dni) REFERENCES Usuarios (dni))ENGINE = MYISAM;" ;
		if(!$this->db->query($sql)){
			echo "ERROR: La tabla \"Sancionados\" ya existe o no se ha podido crear.<br>";
		}else{
			echo "Tabla \"Sancionados\" creada con éxito.<br>";
		}
	}
	
	
}

$conexion = ConectaBD::getInstance();
$conexion->creaBase();
$conexion->creaTablas();

?>
