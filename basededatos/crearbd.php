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
		//Estructura para la base de datos Biblioteca
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
		
		//Estructura para la tabla Libros
		$sql = "CREATE TABLE Libros ( cod_libro varchar(9) NOT NULL, " .
				"titulo varchar(50) NOT NULL, " .
				"autor varchar(30) NOT NULL, " .
				"genero VARCHAR(9) NOT NULL, " .
				"año_edicion year(4) DEFAULT NULL, " .
				"editorial varchar(50) NOT NULL, " .
				"disponible int(2) DEFAULT NULL," .
				"img varchar(50) NOT NULL," .
				"idioma varchar(20) NOT NULL, " .
				"descripcion varchar(650) NOT NULL, " .
				"primary key (cod_libro)) ENGINE = MYISAM DEFAULT CHARSET=utf8mb4;";
		if(!$this->db->query($sql)){
			echo "ERROR: La tabla \"Libros\" ya existe o no se ha podido crear.<br>";
		}else{
			echo "Tabla \"Libros\" creada con éxito.<br>";
		}
		
		//Estructura para la tabla Usuarios
		$sql = "CREATE TABLE Usuarios (nombre varchar(20) NOT NULL, " .
				"apellidos varchar(50) NOT NULL, " .
				"dni varchar(10) NOT NULL, " .
				"email varchar(40) NOT NULL, " .
				"fecha_nacimiento date DEFAULT NULL, " .
				"contraseña varchar(250) NOT NULL, " .
				"primary key  (dni)) ENGINE = MYISAM  DEFAULT CHARSET=utf8mb4;";
		if(!$this->db->query($sql)){
			echo "ERROR: La tabla \"Usuarios\" ya existe o no se ha podido crear.<br>";
		}else{
			echo "Tabla \"Usuarios\" creada con éxito.<br>";
		}
		
		//Estructura para la tabla Prestamos
		$sql = "CREATE TABLE Prestamos (cod_libro varchar(9) NOT NULL," .
				"fecha_salida date DEFAULT NULL, " .
				"fecha_devolucion date DEFAULT NULL, " .
				"dni varchar(10) NOT NULL, " .
				"id_prestamo int(11) NOT NULL AUTO_INCREMENT, " .
				"primary key (id_prestamo), " .
				"foreign key(dni) references usuarios(dni), " .
				"foreign key(cod_libro) references libros(cod_libro))ENGINE = MYISAM DEFAULT CHARSET=utf8mb4;";			
		if(!$this->db->query($sql)){
			echo "ERROR: La tabla \"Prestamos\" ya existe o no se ha podido crear.<br>";
		}else{
			echo "Tabla \"Prestamos\" creada con éxito.<br>";
		}
		//Estructura para la tabla Sancionados
		$sql = "CREATE TABLE Sancionados (nombre VARCHAR(20) NOT NULL, " .
				"dni VARCHAR(10) NOT NULL, " .
				"hasta date DEFAULT NULL, " .
				"primary key (dni), " .
				"foreign key(dni) references Usuarios(dni))ENGINE = MYISAM DEFAULT CHARSET=utf8mb4;" ;
		if(!$this->db->query($sql)){
			echo "ERROR: La tabla \"Sancionados\" ya existe o no se ha podido crear.<br>";
		}else{
			echo "Tabla \"Sancionados\" creada con éxito.<br>";
		}
		//Estructura para la tabla Lista_espera
		$sql = "CREATE TABLE Lista_espera (id_reserva int(11) NOT NULL AUTO_INCREMENT, " .
				"dni varchar(10) NOT NULL, " .
				"codigo varchar(9) NOT NULL, " .
				"fecha date DEFAULT NULL, " .
				"fecha_fin date DEFAULT NULL, " .
				"primary key (id_reserva), " .
				"foreign key(dni) references usuarios(dni))ENGINE = MYISAM DEFAULT CHARSET=utf8mb4;" ;
		if(!$this->db->query($sql)){
			echo "ERROR: La tabla \"Lista_espera\" ya existe o no se ha podido crear.<br>";
		}else{
			echo "Tabla \"Lista_espera\" creada con éxito.<br>";
		}
	}
	
	function insertarLibros(){
		//Datos para la tabla Libros
				$sql = "INSERT INTO Libros (cod_libro,titulo,autor,genero,año_edicion,editorial,disponible,img,idioma,descripcion) VALUES
						('E1', 'la celestina', 'Fernando de Rojas', 'Tragicomedia', 2016, 'santilana', 4, 'celestina', 'castellano', 'Su loca pasión por Melibea, lleva a Calisto a romper todas las barreras morales y sociales y a aliarse con una vieja alcahueta. El destino de Calisto y Melibea, engarzado con habilidad insospechada por Celestina, culmina fatalmente con la muerte de ambos. Desde el momento en que entra en escena, Celestina irrumpe no sólo en toda la obra, sino en toda la literatura, hasta convertirse en un personaje literario de fama universal. Reflejo de una sociedad conflictiva –la española del siglo xv– e intensa expresión de las más grandes pasiones humanas, Celestina resume y liquida la tradición medieval y abre las puertas a tiempos nuevos.'),
						('E2', 'cazadores de sombras', 'cassandra clare', 'Fantasia', 2008, 'planeta S,A', 0, 'cazadores_de_sombras', 'castellano', 'Demonios, hombres lobo, vampiros, ángeles y hadas conviven en esta trilogía de fantasía urbana donde no falta el romance.\r\nEn el Pandemonium, la discoteca de moda de Nueva York, Clary sigue a un atractivo chico de pelo azul hasta que presencia su muerte a manos de tres jóvenes cubiertos de extraños tatuajes. Desde esa noche, su destino se une al de esos tres cazadores de sombras, guerreros dedicados a liberar a la tierra de demonios y, sobre todo, al de Jace, un chico con aspecto de ángel y tendencia a actuar como un idiota…'),
						('E3', 'la madre de frankenstein', 'Almudena grandes', 'Ciencia Ficcion', 2020, 'tusquets editoriales', 10, 'la_madre_de_frankenstein', 'castellano', 'El apasionante relato de una mujer y un hombre que optaron por resistir en los tiempos más difíciles.La novela más intensa y emotiva del ciclo de los Episodios de una Guerra Interminable.\r\nFinales de los años 50 en España; al sur de Madrid, en Ciempozuelos, un manicomio femenino regentado por las monjas del Sagrado Corazón de Jesús. Allí vive recluida una interna esquizofrénica, Aurora Rodríguez Carballeira, un personaje estrafalario que consigue asombrar a las monjas, a las internas y a las limpiadoras en cuanto se arranca a tocar el piano. '),
						('E4', 'Historia social de la literatura española', 'Carlos Blanco Aguina', 'Literatura', 2000, 'akal', 4, 'historia_social', 'castellano', 'Este libro ofrece una visión de conjunto de la creación literaria en España desde la Edad Media hasta principios de los años ochenta. En él se aborda el estudio de los principales autores y obras a partir de sus respectivos contextos sociohistóricos, con lo que la Literatura se configura como un producto ligado indisolublemente a la Historia, sin la cual es imposible obtener una adecuada representación de la misma.'),
						('E5', 'Roma antigua Historia de un imperio global', 'Ana M.ª Suárez Piñei', 'Historia', 2019, 'akal', 3, 'roma_antigua', 'castellano', '\'Roma antigua. Historia de un imperio global\' constituye una sintética y amena presentación de los acontecimientos y procesos esenciales que determinan la historia de la antigua Roma'),
						('E6', 'dracula', 'Jorge Martínez Juárez', 'Infantil', 2011, 'akal', 2, 'dracula', 'castellano', 'Cuando Jonathan Harker recibe el encargo de viajar a Transilvania no sabe que allí se enfrentará a uno de los personajes más siniestros de la historia, el Conde Drácula. Para combatir su poderoso influjo, un grupo de valientes hombres y mujeres deberán hacer uso de toda su fuerza e inteligencia para proteger sus vidas. Con ellos viajaremos a través de este relato de terror adaptado ahora para jóvenes lectores.'),
						('E7', 'La leyenda del príncipe Rama', ' Jorge Martínez Juárez', 'Literatura', 2012, 'akal', 2, 'pricipe_rama', 'castellano', 'Rama es el heredero del rey de Ayodia, una ciudad de la India de hace miles de años. Para cumplir una promesa de su padre, se ve obligado a marchar al exilio junto a su hermano Laksmana y su joven esposa Sita. En la jungla surcada por ríos sagrados como el Ganges encuentran la sabiduría de los anacoretas, pero también la amenaza de los terribles y sanguinarios raksasas. Rama y los suyos tendrán que demostrar su valor en una de las más grandes batallas de todos los tiempos. Este relato está basado en El Ramayana, la célebre epopeya hindú .'),
						('E8', 'Cómo conversar con un fascista', 'Marcia Tiburi ', 'Politica', 2018, 'inter pares', 6, 'fascista', 'castellano', 'En estos tiempos en el que los nervios y las emociones se encuentran a flor de piel, este libro surge con un propósito filosófico-político: pensar con los lectores sobre cuestiones de cultura política que se viven día a día, de un modo abierto, sin caer en la jerga académica. El argumento principal es cómo pensar en un método o una postura que se contraponga al discurso del odio y a sus reflejos en la sociedad y en las redes sociales. La realidad de la que parte es la brasileña, pero su alcance es global, porque hoy día el fascismo social se extiende por todo el mundo y se filtra en todas las capas sociales.'),
						('E9', 'El libro del feminismo', 'Lucy Mangan', 'Sociologia', 2020, 'akal', 6, 'feminismo', 'castellano', '\"\"\"El libro del feminismo\"\" recoge algunas de las ideas feministas más destacadas desde el siglo XVIII hasta el presente. En él figuran místicas, escritoras, científicas, políticas, artistas y muchas otras mujeres que aportaron nuevos pensamientos, actitudes, definiciones, reglas, prioridades y percepciones,que nos ayudan a comprender cómo se organiza el mundo actual, y cuánto camino tiene el movimiento aún por recorrer. \"'),
						('E10', 'Reina roja', 'Juan Gómez Jurado', 'Thriller', 2018, 'Ediciones B', 4, 'reina', 'castellano', 'Antonia Scott es una mujer muy especial. Tiene un don que es al mismo tiempo una maldición: una extraordinaria inteligencia. Gracias a ella ha salvado decenas de vidas, pero también lo ha perdido todo. Hoy se parapeta contra el mundo en su piso casi vacío de Lavapiés, del que no piensa volver a salir. Ya no queda nada ahí fuera que le interese lo más mínimo.\r\nEl inspector Jon Gutiérrez está acusado de corrupción, suspendido de empleo y sueldo. Es un buen policía metido en un asunto muy feo, y ya no tiene mucho que perder.'),
						('E11', 'Sidi', 'Arturo Pérez Reverte', 'Ciencia Ficcion', 2019, 'Alfaguara', 3, 'sidi', 'castellano', 'No tenía patria ni rey, sólo un puñado de hombres fieles.\r\nNo tenían hambre de gloria, sólo hambre.\r\nAsí nace un mito.\r\nAsí se cuenta una leyenda.\r\n\r\n«El arte del mando era tratar con la naturaleza humana, y él había dedicado su vida a aprenderlo. Colgó la espada del arzón, palmeó el cuello cálido del animal y echó un vistazo alrededor: sonidos metálicos, resollar de monturas, conversaciones en voz baja. Aquellos hombres olían a estiércol de caballo, cuero, aceite de armas, sudor y humo de leña.'),
						('E12', 'Don  Quijote de la mancha', 'Miguel de Cervantes Saavedra', 'Literatura', 2015, 'S.L.U. ESPASA LIBROS', 8, 'don_quijote', 'castellano', 'Las andanzas del famoso hidalgo al que se le sorbió el seso leyendo novelas de caballerías se han convertido en una de las grandes obras del canon universal. Miguel de Cervantes consiguió una obra divertidísima, rica y revolucionaria, de una indiscutible modernidad tanto en su primera entrega de 1605, como en la segunda parte de 1615. '),
						('E13', 'Platero y yo', 'Juan Ramon Jimenez', 'Poesía', 2006, 'S.L.U. ESPASA LIBROS', 5, 'platero', 'castellano', 'Narración lírica de Juan Ramón Jiménez que recrea poéticamente la vida y muerte del burro Platero y formada por breves capítulos que pueden conseiderarse poemas en prosa. Una edición que conmemora los 50 años de la concesión del premio Nobel al autor. El libro reproduce una edición facsimilar publicada en 1937 en Argentina con dibujos de Fernando Marco.'),
						('E14', 'El si de las niñas', 'Leandro Fernandez de Moratin', 'Teatro', 2005, 'CATEDRA', 5, 'el_si_de_las_niñas', 'castellano', 'Moratín, neoclásico por raciocinio y por criterio artístico, lleva en sí, por temperamento, los tiempos nuevos. En esta obra, justamente celebrada como la mejor de su producción, reivindica el derecho de los jóvenes al matrimonio por amor y no por imposición familiar. Desde un tono de bondad amable, «El sí de las niñas» es un alegato contra los métodos educativos de la época en los mismos inicios del siglo XIX, hecho por un autor dramático que, por ilustrado, trataba de educar desde las tablas.'),
						('E15', 'Banlada de pájaros cantores y serpientes', 'Suzanne Collins', 'Ciencia Ficcion', 2018, 'Molino', 4, 'pajaros_cantores', 'castellano', 'La ambición será su motor.\r\nLa rivalidad, su motivación.\r\nPero alcanzar el poder tiene un precio.\r\nEs la mañana de la cosecha que dará comienzo a los décimos Juegos del Hambre. En el Capitolio, Coriolanus Snow, de dieciocho años, se prepara para una oportunidad única: alcanzar la gloria como mentor de los Juegos. La casa de los Snow, antes tan influyente, atraviesa tiempos difíciles, y su destino depende de que Coriolanus consiga superar a sus compañeros en ingenio, estrategia y encanto como mentor del tributo que le sea adjudicado.\r\n\r\nTodo está en su contra. Lo han humillado al asignarle a la tributo del Distrito 12. '),
						('E16', 'Los juegos del hambre', 'Suzanne Collins', 'Ciencia Ficcion', 2012, 'Molino', 5, 'juegos_del_hambre', 'castellano', 'GANAR SIGNIFICA FAMA Y RIQUEZA. PERDER SIGNIFICA UNA MUERTE SEGURA. En una oscura versión del futuropróximo, doce chicos y doce chicas se ven obligados a participar en un reality show llamado Los juegos del hambre. Solohay una regla: matar o morir. Cuando Katniss Everdeen, una joven de dieciséis añosse presenta voluntaria para ocuparel lugar de su hermana en los juegos, lo entiende como una condena a muerte. Sin embargo Katniss ya ha visto la muertede cerca y la supervivencia forma parte de su naturaleza. ¡Que empiecen los septuagésimo cuartos juegos del hambre!'),
						('E17', 'Los juegos del hambre : En llamas', 'Suzanne Collins', 'Ciencia Ficcion', 2012, 'Molino', 5, 'en_llamas', 'castellano', 'Katniss Everdeen ha sobrevivido a Los juegos del hambre. Pero el Capitolio quiere venganza. Contra todopronóstico, Katniss Everdeen y Peeta Mellark siguen vivos. Aunque Katniss debería sentirse aliviada, se rumorea queexiste una rebelión contra el Capitolio, una rebelión que puede que Katniss y Peeta hayan ayudado a inspirar. La naciónles observa y hay mucho en juego. Un movimiento en falso y las consecuencias serán inimaginables.'),
						('E18', 'El inversor inteligente', 'Benjamin Graham', 'Economía', 2007, ' DEUSTO S.A. EDICIONES', 2, 'el_inversor', 'castellano', 'Considerado el más importante consejero en inversión del siglo XX, Benjamin Graham enseñó e inspiró a financieros de todo el mundo. Presentó su filosofía, basada en el concepto de \"invertir en valor\", en El inversor inteligente, un libro que se convirtió en la biblia de los inversores ya desde su primera publicación en 1949. En él, Benjamin Graham alerta a los inversores sobre cómo evitar errores de estrategia, al tiempo que describe cómo desarrollar un plan racional para comprar acciones y aumentar su valor.'),
						('E19', 'Adiós a los bancos', 'Miguel Fernandez Ordoñez', 'Economia', 2020, 'Taurus', 2, 'bancos', 'castellano', '¿Qué razón hay para impedir a los ciudadanos el acceso a depósitos públicos y seguros? ¿Qué lleva a los estados a proteger a los bancos privados en vez de fomentar competencia e incentivar la innovación? ¿Dónde estriba la fragilidad de los depósitos en los bancos privados?\r\nLa gran crisis de 2008 puso de manifiesto la debilidad del dinero usado en los países desarrollados. Millones de trabajadores arrojados al paro y millones de euros dedicados a salvar los bancos son algunos de los daños gigantescos que causan las crisis bancarias.'),
						('E20', 'Los juegos del hambre: Sinsajo ', 'Suzanne Collins', 'Ciencia Ficcion', 2012, 'Molino', 5, 'sinsajo', 'castellano', 'Katnis Everdeen ha sobrevivido dos veces a Los juegos del hambre, pero no está a salvo. La revolución seextiende y, al parecer, todos han tenido algo que ver en el meticuloso plan, todos excepto Katniss. Aun así su papel enla batalla final es el más importante de todos. Katniss debe convertirse en el Sinsajo, en el símbolo de la rebelión...a cualquier precio. ¡Que empiecen los septuagésimo sextos juegos del hambre!\r\n'),
						('E21', 'Los hombres que no amaban a las mujeres', 'Stieg Larsson', 'Novela', 2015, 'Destino', 5, 'los_hombres', 'castellano', 'Harriet Vanger desapareció hace treinta y seis años en una isla sueca propiedad de su poderosa familia. A pesar del despliegue policial, no se encontró ni rastro de la muchacha. ¿Se escapó? ¿Fue secuestrada? ¿Asesinada?\r\n\r\nEl caso está cerrado y los detalles olvidados. Pero su tío Henrik Vanger, un empresario retirado, vive obsesionado con resolver el misterio antes de morir. En las paredes de su estudio cuelgan cuarenta y tres flores secas y enmarcadas. Las primeras siete fueron regalos de su sobrina; las otras llegaron puntualmente para su cumpleaños, de forma anónima, desde que Harriet desapareció.'),
						('E22', 'La reina en el palacio de  las corrientes de aire', 'Stieg Larsson', 'Novela', 2015, 'Destino', 5, 'reina_palacio', 'castellano', 'Los lectores que llegaron con el corazón en un puño al final de La chica que soñaba con una cerilla y un bidón de gasolina quizás prefieran no seguir leyendo estas líneas y descubrir por sí mismos cómo sigue la serie y, sobre todo, qué le sucede a Lisbeth Salander.\r\n\r\nComo ya imaginábamos, Lisbeth no está muerta, aunque no hay muchas razones para cantar victoria: con una bala en el cerebro, necesita un milagro, o el más habilidoso cirujano, para salvar la vida. Le esperan semanas de confinamiento en el mismo centro donde un paciente muy peligroso sigue acechándola: Alexander Zalachenko, Zala. '),
						('E23', 'A Song of ice and fire', 'George R. R.', 'Fantasia', 1996, 'Ediciones Gigamesh', 6, 'tronos', 'ingles', 'The future of the Seven Kingdoms hangs in the balance.\r\n\r\nIn the east, Daenerys, last scion of House Targaryen, her dragons grown to terrifying maturity, rules as queen of a city built on dust and death, beset by enemies.\r\n\r\nNow that her whereabouts are known many are seeking Daenerys and her dragons. Among them the dwarf, Tyrion Lannister, who has escaped King′s Landing with a price on his head, wrongfully condemned to death for the murder of his nephew, King Joffrey. But not before killing his hated father, Lord Tywin.'),
						('E24', 'A Storm of Swords', 'George R. R.', 'Fantasia', 2015, 'Harpercollins pub', 5, 'song', 'ingles', 'The Seven Kingdoms are divided by revolt and blood feud, and winter approaches like an angry beast. Beyond the Northern borders, wildlings leave their villages to gather in the ice and stone wasteland of the Frostfangs. From there, the renegade Brother Mance Rayder will lead them South towards the Wall. Robb Stark wears his new-forged crown in the Kingdom of the North, but his defences are ranged against attack from the South, the land of House Stark\'s enemies the Lannisters. His sisters are trapped there, dead or likely yet to die, at the whim of the Lannister boy-king Joffrey or his depraved mother Cersei, regent of the Iron Throne.'),
						('E25', 'Los pilares de la tierra', 'Ken Follett', 'Novela', 2010, 'Debolsillo', 3, 'pilares', 'castellano', 'El gran maestro de la narrativa de acción y suspense nos transporta a la Edad Media, a un fascinante mundo de reyes, damas, caballeros, pugnas feudales, castillos y ciudades amuralladas. El amor y la muerte se entrecruzan vibrantemente en este magistral tapiz cuyo centro es la construcción de una catedral gótica. La historia se inicia con el ahorcamiento público de un inocente y finaliza con la humillación de un rey. Los pilares de la Tierra es la obra maestra de Ken Follett y constituye una excepcional evocación de una época de violentas pasiones.'),
						('E26', 'The curious incident of dog in the  night-time', 'Mark Haddon', 'Novela', 2014, 'Random house childrens books', 8, 'curious', 'ingles', 'The narrator of Mark Haddon\'s The Curious Incident of the Dog in the Night-Time is 15-year-old Christopher who has a form of autism called Asperger\'s Syndrome. His obsessive and unusual take on life creates lots of hilarious situations but also brings incredible poignancy to the story. Christopher finds it hard to relate to people, particularly their emotions and feelings, so when he finds a dead dog and decides to solve the mystery of its death, his quest leads him into a difficult and unfamiliar territory that threatens to upset his carefully ordered existence'),
						('E28', 'Mi cuaderno de miedos', 'Fernando Palazuelos', 'Infantil', 2020, 'El gallo de oro', 6, 'miedo', 'castellano', '\"Niko es un niño muy especial. Lo saben su madre, su padre, su hermanita que aún no habla, e incluso su pequeño perro. A veces siente inseguridades y miedos. Pero el truco que le propone un día su padre le resultará muy útil para tenerlos a buen recaudo e incluso para pasárselo bien con ellos. Este es un cuento especial. A la vez que un cuaderno de campo, como los de los naturalistas, este cuaderno narra la historia de un niño azul.'),
						('E29', 'Antologia poetica', 'Federico García Lorca', 'Poesía', 2019, 'Micomicona Ediciones', 8, 'antologia', 'castellano', 'Aquí tienes las claves para comprender la poesía de FEDERICO GARCÍA LORCA. Los grandes poetas no son sencillos casi nunca: necesitan de la ayuda de especialistas para exprimir de sus obras el jugo más saludable. Verso a verso, prácticamente, casi beso a verso, tienes comentado todos los poemas seleccionados en esta antología. Con estas recomendaciones, tu concentración y tu sensibilidad, podrás adentrarte en el mundo del poeta que marca los destinos de la poesía hispana desde hace un siglo.'),
						('E30', 'Historia de una escalera', 'Antonio Buero Vallejo', 'Teatro', 2010, 'S.L.U. ESPASA LIBROS', 6, 'escalera', 'castellano', 'Buero Vallejo ha sabido igualar vida y pensamiento, conducta y prédica. De su lucidez y de su ejemplaridad, de su trabajo, ha surgido el teatro de más altura, tensión y trascendencia de la posguerra española. Como ha sabido demostrar con Historia de una escalera, hito en la recuperación teatral de España.\r\n');" ;
		if(!$this->db->query($sql)){
			echo "ERROR: No se ha podido insertar los datos en la tabla \"Libros\".<br>";
		}else{
			echo "Datos insertados en la tabla \"Libros\" con éxito.<br>";
		}
	}
}

$conexion = ConectaBD::getInstance();
$conexion->creaBase();
$conexion->creaTablas();
$conexion->insertarLibros();
?>
