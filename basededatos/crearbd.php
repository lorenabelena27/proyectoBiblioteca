<?php
include '../programas/php_comun/password.php';
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
				"estado int(1) DEFAULT 0, " .
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
				"foreign key(dni) references usuarios(dni)".
				"foreign key(codigo) references libros(cod_libro)")ENGINE = MYISAM DEFAULT CHARSET=utf8mb4;" ;
		if(!$this->db->query($sql)){
			echo "ERROR: La tabla \"Lista_espera\" ya existe o no se ha podido crear.<br>";
		}else{
			echo "Tabla \"Lista_espera\" creada con éxito.<br>";
		}
		
		$sql = "CREATE TABLE Administradores (id_admin int(11) NOT NULL AUTO_INCREMENT, " .
				"dni varchar(10) NOT NULL, " .
				"contraseña varchar(250) NOT NULL, " .
				"primary key (id_admin))ENGINE = MYISAM DEFAULT CHARSET=utf8mb4;" ;
				
		if(!$this->db->query($sql)){
			echo "ERROR: La tabla \"Administradores\" ya existe o no se ha podido crear.<br>";
		}else{
			echo "Tabla \"Administradores\" creada con éxito.<br>";
		}
	}
	
	function insertarLibros(){
		//Datos para la tabla Libros
		$sql = "INSERT INTO Libros (cod_libro,titulo,autor,genero,año_edicion,editorial,disponible,img,idioma,descripcion) VALUES				
	('E1', 'La celestina', 'Fernando de Rojas', 'Literatura', 2016, 'santilana', 0, 'celestina', 'castellano', 'Su loca pasión por Melibea, lleva a Calisto a romper todas las barreras morales y sociales y a aliarse con una vieja alcahueta. El destino de Calisto y Melibea, engarzado con habilidad insospechada por Celestina, culmina fatalmente con la muerte de ambos. Desde el momento en que entra en escena, Celestina irrumpe no sólo en toda la obra, sino en toda la literatura, hasta convertirse en un personaje literario de fama universal. Reflejo de una sociedad conflictiva –la española del siglo xv– e intensa expresión de las más grandes pasiones humanas, Celestina resume y liquida la tradición medieval y abre las puertas a tiempos nuevos.'),
	('E2', 'Cazadores de sombras', 'cassandra clare', 'Fantasia', 2008, 'planeta S,A', 2, 'cazadores_de_sombras', 'castellano', 'Demonios, hombres lobo, vampiros, ángeles y hadas conviven en esta trilogía de fantasía urbana donde no falta el romance.\r\nEn el Pandemonium, la discoteca de moda de Nueva York, Clary sigue a un atractivo chico de pelo azul hasta que presencia su muerte a manos de tres jóvenes cubiertos de extraños tatuajes. Desde esa noche, su destino se une al de esos tres cazadores de sombras, guerreros dedicados a liberar a la tierra de demonios y, sobre todo, al de Jace, un chico con aspecto de ángel y tendencia a actuar como un idiota…'),
	('E3', 'La madre de frankenstein', 'Almudena grandes', 'Ciencia Ficcion', 2020, 'tusquets editoriales', 0, 'la_madre_de_frankenstein', 'castellano', 'El apasionante relato de una mujer y un hombre que optaron por resistir en los tiempos más difíciles.La novela más intensa y emotiva del ciclo de los Episodios de una Guerra Interminable.\r\nFinales de los años 50 en España; al sur de Madrid, en Ciempozuelos, un manicomio femenino regentado por las monjas del Sagrado Corazón de Jesús. Allí vive recluida una interna esquizofrénica, Aurora Rodríguez Carballeira, un personaje estrafalario que consigue asombrar a las monjas, a las internas y a las limpiadoras en cuanto se arranca a tocar el piano. '),
	('E4', 'Historia social de la literatura española', 'Carlos Blanco Aguina', 'Literatura', 2000, 'akal', 3, 'historia_social', 'castellano', 'Este libro ofrece una visión de conjunto de la creación literaria en España desde la Edad Media hasta principios de los años ochenta. En él se aborda el estudio de los principales autores y obras a partir de sus respectivos contextos sociohistóricos, con lo que la Literatura se configura como un producto ligado indisolublemente a la Historia, sin la cual es imposible obtener una adecuada representación de la misma.'),
	('E5', 'Roma antigua Historia de un imperio global', 'Ana M.ª Suárez Piñei', 'Historia', 2019, 'akal', 2, 'roma_antigua', 'castellano', '\'Roma antigua. Historia de un imperio global\' constituye una sintética y amena presentación de los acontecimientos y procesos esenciales que determinan la historia de la antigua Roma'),
	('E6', 'Dracula', 'Jorge Martínez Juárez', 'Infantil', 2011, 'akal', 0, 'dracula', 'castellano', 'Cuando Jonathan Harker recibe el encargo de viajar a Transilvania no sabe que allí se enfrentará a uno de los personajes más siniestros de la historia, el Conde Drácula. Para combatir su poderoso influjo, un grupo de valientes hombres y mujeres deberán hacer uso de toda su fuerza e inteligencia para proteger sus vidas. Con ellos viajaremos a través de este relato de terror adaptado ahora para jóvenes lectores.'),
	('E7', 'La leyenda del príncipe Rama', ' Jorge Martínez Juárez', 'Literatura', 2012, 'akal', 0, 'pricipe_rama', 'castellano', 'Rama es el heredero del rey de Ayodia, una ciudad de la India de hace miles de años. Para cumplir una promesa de su padre, se ve obligado a marchar al exilio junto a su hermano Laksmana y su joven esposa Sita. En la jungla surcada por ríos sagrados como el Ganges encuentran la sabiduría de los anacoretas, pero también la amenaza de los terribles y sanguinarios raksasas. Rama y los suyos tendrán que demostrar su valor en una de las más grandes batallas de todos los tiempos. Este relato está basado en El Ramayana, la célebre epopeya hindú .'),
	('E8', 'Cómo conversar con un fascista', 'Marcia Tiburi ', 'Politica', 2018, 'inter pares', 5, 'fascista', 'castellano', 'En estos tiempos en el que los nervios y las emociones se encuentran a flor de piel, este libro surge con un propósito filosófico-político: pensar con los lectores sobre cuestiones de cultura política que se viven día a día, de un modo abierto, sin caer en la jerga académica. El argumento principal es cómo pensar en un método o una postura que se contraponga al discurso del odio y a sus reflejos en la sociedad y en las redes sociales. La realidad de la que parte es la brasileña, pero su alcance es global, porque hoy día el fascismo social se extiende por todo el mundo y se filtra en todas las capas sociales.'),
	('E9', 'El libro del feminismo', 'Lucy Mangan', 'Sociologia', 2020, 'akal', 5, 'feminismo', 'castellano', '\"\"\"El libro del feminismo\"\" recoge algunas de las ideas feministas más destacadas desde el siglo XVIII hasta el presente. En él figuran místicas, escritoras, científicas, políticas, artistas y muchas otras mujeres que aportaron nuevos pensamientos, actitudes, definiciones, reglas, prioridades y percepciones,que nos ayudan a comprender cómo se organiza el mundo actual, y cuánto camino tiene el movimiento aún por recorrer. \"'),
	('E10', 'Reina roja', 'Juan Gómez Jurado', 'Thriller', 2018, 'Ediciones B', 3, 'reina', 'castellano', 'Antonia Scott es una mujer muy especial. Tiene un don que es al mismo tiempo una maldición: una extraordinaria inteligencia. Gracias a ella ha salvado decenas de vidas, pero también lo ha perdido todo. Hoy se parapeta contra el mundo en su piso casi vacío de Lavapiés, del que no piensa volver a salir. Ya no queda nada ahí fuera que le interese lo más mínimo.\r\nEl inspector Jon Gutiérrez está acusado de corrupción, suspendido de empleo y sueldo. Es un buen policía metido en un asunto muy feo, y ya no tiene mucho que perder.'),
	('E11', 'Sidi', 'Arturo Pérez Reverte', 'Ciencia Ficcion', 2019, 'Alfaguara', 1, 'sidi', 'castellano', 'No tenía patria ni rey, sólo un puñado de hombres fieles.\r\nNo tenían hambre de gloria, sólo hambre.\r\nAsí nace un mito.\r\nAsí se cuenta una leyenda.\r\n\r\n«El arte del mando era tratar con la naturaleza humana, y él había dedicado su vida a aprenderlo. Colgó la espada del arzón, palmeó el cuello cálido del animal y echó un vistazo alrededor: sonidos metálicos, resollar de monturas, conversaciones en voz baja. Aquellos hombres olían a estiércol de caballo, cuero, aceite de armas, sudor y humo de leña.'),
	('E12', 'Don  Quijote de la mancha', 'Miguel de Cervantes Saavedra', 'Literatura', 2015, 'S.L.U. ESPASA LIBROS', 7, 'don_quijote', 'castellano', 'Las andanzas del famoso hidalgo al que se le sorbió el seso leyendo novelas de caballerías se han convertido en una de las grandes obras del canon universal. Miguel de Cervantes consiguió una obra divertidísima, rica y revolucionaria, de una indiscutible modernidad tanto en su primera entrega de 1605, como en la segunda parte de 1615. '),
	('E13', 'Platero y yo', 'Juan Ramon Jimenez', 'Poesía', 2006, 'S.L.U. ESPASA LIBROS', 5, 'platero', 'castellano', 'Narración lírica de Juan Ramón Jiménez que recrea poéticamente la vida y muerte del burro Platero y formada por breves capítulos que pueden conseiderarse poemas en prosa. Una edición que conmemora los 50 años de la concesión del premio Nobel al autor. El libro reproduce una edición facsimilar publicada en 1937 en Argentina con dibujos de Fernando Marco.'),
	('E14', 'El si de las niñas', 'Leandro Fernandez de Moratin', 'Teatro', 2005, 'CATEDRA', 5, 'el_si_de_las_ninas', 'castellano', 'Moratín, neoclásico por raciocinio y por criterio artístico, lleva en sí, por temperamento, los tiempos nuevos. En esta obra, justamente celebrada como la mejor de su producción, reivindica el derecho de los jóvenes al matrimonio por amor y no por imposición familiar. Desde un tono de bondad amable, «El sí de las niñas» es un alegato contra los métodos educativos de la época en los mismos inicios del siglo XIX, hecho por un autor dramático que, por ilustrado, trataba de educar desde las tablas.'),
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
	('E26', 'The curious incident of dog in the  night-time', 'Mark Haddon', 'Novela', 2014, 'Random house childrens books', 7, 'curious', 'ingles', 'The narrator of Mark Haddon\'s The Curious Incident of the Dog in the Night-Time is 15-year-old Christopher who has a form of autism called Asperger\'s Syndrome. His obsessive and unusual take on life creates lots of hilarious situations but also brings incredible poignancy to the story. Christopher finds it hard to relate to people, particularly their emotions and feelings, so when he finds a dead dog and decides to solve the mystery of its death, his quest leads him into a difficult and unfamiliar territory that threatens to upset his carefully ordered existence'),
	('E28', 'Mi cuaderno de miedos', 'Fernando Palazuelos', 'Infantil', 2020, 'El gallo de oro', 5, 'miedo', 'castellano', '\"Niko es un niño muy especial. Lo saben su madre, su padre, su hermanita que aún no habla, e incluso su pequeño perro. A veces siente inseguridades y miedos. Pero el truco que le propone un día su padre le resultará muy útil para tenerlos a buen recaudo e incluso para pasárselo bien con ellos. Este es un cuento especial. A la vez que un cuaderno de campo, como los de los naturalistas, este cuaderno narra la historia de un niño azul.'),
	('E29', 'Antologia poetica', 'Federico García Lorca', 'Poesía', 2019, 'Micomicona Ediciones', 8, 'antologia', 'castellano', 'Aquí tienes las claves para comprender la poesía de FEDERICO GARCÍA LORCA. Los grandes poetas no son sencillos casi nunca: necesitan de la ayuda de especialistas para exprimir de sus obras el jugo más saludable. Verso a verso, prácticamente, casi beso a verso, tienes comentado todos los poemas seleccionados en esta antología. Con estas recomendaciones, tu concentración y tu sensibilidad, podrás adentrarte en el mundo del poeta que marca los destinos de la poesía hispana desde hace un siglo.'),
	('E30', 'Historia de una escalera', 'Antonio Buero Vallejo', 'Teatro', 2010, 'S.L.U. ESPASA LIBROS', 5, 'escalera', 'castellano', 'Buero Vallejo ha sabido igualar vida y pensamiento, conducta y prédica. De su lucidez y de su ejemplaridad, de su trabajo, ha surgido el teatro de más altura, tensión y trascendencia de la posguerra española. Como ha sabido demostrar con Historia de una escalera, hito en la recuperación teatral de España.\r\n'),
	('E27', 'Y  Julia retó a los dioses', 'Santiago Posteguillo', 'Novela histórica', 2020, 'Planeta', 2, 'dioses', 'castellano', 'Cuando el enemigo es tu propio hijo…, ¿existe la victoria?\r\n\r\nMantenerse en lo alto es mucho más difícil que llegar. Julia está en la cúspide de su poder, pero la traición y la división familiar amenazan con echarlo todo a perder. Para colmo de males, el médico Galeno diagnostica que la emperatriz padece lo que él, en griego, llama karkinos, y que los romanos, en latín, denominan cáncer. El enfrentamiento brutal entre sus dos hijos aboca la dinastía de Julia al colapso. En medio del dolor físico y moral que padece la augusta, cualquiera se hubiera rendido. Se acumulan tantos desastres que Julia siente que es como si luchara contra los dioses.'),
	('E31', 'Obesos y Famélicos ', 'Raj Patel', 'Sociologia', 2020, 'Malpaso Ediciones', 3, 'obesos', 'castellano', '¿Cómo es posible que coincida con una epidemia de obesidad que afecta en especial a los pobres? Desde las semillas hasta el supermercado, desde los oligopolios hasta la Organización Mundial del Comercio, desde el Banco Mundial hasta el empobrecimiento de los agricultores: nada escapa al brillante y estremecedor análisis de una autoridad mundial. ¿Por qué hay soja en casi todos los alimentos? ¿A quién benefician los transgénicos? ¿Quién decide lo que comemos'),
	('E32', 'Aquelarre', 'W.AA', 'Sociologia', 2020, 'Advook Editorial', 6, 'aquelarre', 'castellano', 'Camo es un viejo picudo, tacaño y gruñón que vive recluido en su finca sólo con la hija; incluso la mujer le ha dejado, exasperada, para irse a vivir con el hijo que había tenido de un matrimonio anterior. Con todo, al azar, siempre tan aguafiestas, hace que techos, un joven de familia bien, cazando en las tierras del mal encarado, vea la chica y se enamore locamente. Como era de esperar, el viejo se niega a dársela por esposa; pero, después de una serie de trifulcas, el huraño le será castigada de una manera muy aleccionadora'),
	('E33', 'Curso de Sociología General I', 'Pierre Bourdieu', 'Sociologia', 2020, 'Siglo XXI', 3, 'curso', 'castellano', 'Hay que volver, dice Bourdieu, a los conceptos fundamentales, no meramente para hacer divulgación sino para transmitir en qué consiste el trabajo del investigador y hasta qué punto se empobrece o se automatiza si se lo da por sentado. Para eso, hay que desafiar el sentido común que nos dice que el sociólogo estudia las estructuras y los procesos susceptibles de un análisis estadístico independiente de los individuos, o bien que sólo se ocupa de los sujetos concretos y las interacciones observables entre ellos.'),
	('E34', 'A los que vienen', 'Manuela Carmena', 'Politica', 2019, 'Aguilar', 2, 'manuela', 'castellano', 'En este libro Manuela quiere recoger los principales temas y preocupaciones que ya ha puesto de relevancia en su alcaldía y que ahora quiere compartir de otra manera, desde la vida civil, con las nuevas generaciones que vienen con fuerza y que quizás puedan necesitar algunas cariñosas y valiosas palabras de una de las más queridas figuras públicas que nos han gobernado en las últimas décadas'),
	('E35', 'Bajo la mole', 'Antonio Gramsci', 'Politica', 2009, 'Sequitur', 3, 'bajo', 'castellano', 'Selección de los artículos, hasta ahora inéditos en español, de Antonio Gramsci (1891-1937) publicados en la edición turinesa del periódico socialistas Avanti! a lo largo de la Primera Guerra Mundial. Una sorprendente dimensión de Gramsci: fino escritor, irónico y duro crítico de las costumbres de ese momento (de civilización burguesa y proyecto obrero), de ese país (la joven Italia), de esa Europa (entre la guerra y la revolución).'),
	('E36', 'Imperios', 'Herfried Münkler', 'Politica', 2020, 'Nola Editores', 2, 'imperios', 'castellano', '¿Qué caracteriza a los imperios? ¿Qué peligros esconde un orden imperial? ¿Qué oportunidades ofrece? De pronto estas preguntas dejaron de tener un interés puramente histórico. Estados Unidos ocupa entretanto una supremacía que muchos juzgan amenazadora. ¿Los políticos en Washington determinan las reglas que deben regir en el resto del mundo? ¿O existe una lógica del dominio mundial ante la que deben doblegarse? Herfried Münkler muestra cómo funciona un imperio y qué tipos de imperios han existido en el pasado. Un espléndido paseo a través de la historia y, al mismo tiempo, un análisis brillante de un tema de actualidad.'),
	('E37', 'El nacionalismo como fuente de beneficios', 'Rudolf Rocker', 'Politica', 2020, 'Pepitas de calabaza', 5, 'nacionalismo', 'castellano', 'Escritas casi a la par que su obra más importante, Nacionalismo y cultura—y complementarias a esta—, las gemas que componen este libro, publicadas en la prensa anarquista de principios del siglo xx a ambos lados del Atlántico, son—todavía hoy—de una clarividencia asombrosa para diseccionar el nacionalismo, el fascismo y el culto a la religión del Estado.\r\n«La obra de Rocker es extraordinariamente instructiva y testimonia una rara originalidad de espíritu. Incontables hechos y relaciones se han expuesto en ella de una manera completamente nueva y persuasiva»'),
	('E38', 'Naturaleza muerta', 'Miquel Molina', 'Sociologia', 2020, 'Edhasa', 3, 'naturaleza', 'castellano', 'Conocemos la taxidermia como el arte de disecar animales. Desde el siglo XIX, desempeñó un importante papel para la conservación y los naturalistas, pues, bien hecha, es una práctica que permite apreciar de cerca unas criaturas que tal vez jamás tengamos la ocasión de ver en su medio natural. Sin embargo, eso conllevó dar un paso más: el \"empajar\" al ser humano, con la intención, supuestamente, de mostrar a las generaciones venideras el aspecto real de los que les precedieron. Como si fueran muñecos de cera, se intentó exhibir en público a naturalezas muertas sostenidas con alambres.'),
	('E39', 'Capital e ideología', 'Thomas Piketty', 'Economía', 2019, 'Deusto S.A. Ediciones', 2, 'capital', 'castellano', 'El esperado nuevo libro del mayor referente mundial en economía.\r\nTras el gran éxito deEl capital en el siglo xxi, el reconocido economista francés, Thomas Piketty vuelve analizar el panorama económico-político actual para presentar las claves que lo definen y las expectativas de futuro que se presentan, considerando los acontecimientos de los últimos años. Basándose en datos comparativos inéditos, este libro propone una Historia a la vez económica, social, intelectual y política de los regímenes no igualitarios, desde las sociedades trifuncionales y esclavistas antiguas hasta las sociedades postcoloniales e hipercapitalistas modernas.'),
	('E40', 'La trastienda de Trump', 'Daniel Estulin', 'Economía', 2017, 'Planeta', 1, 'trump', 'castellano', 'En la línea de sus exitosos El club Bilderberg y Fuera de control, Daniel Estulin nos cuenta lo que nadie sabe: ¿quién es realmente Donald Trump? ¿Cuáles son los hilos que le sustentan? ¿Y quién le financia?\r\nEl 9 de noviembre de 2016 ocurrió lo que todo el mundo creía imposible: Donald Trump, magnate multimillonario con un discurso absolutamente xenófobo, salvaje y populista, ganó la presidencia a la Casa Blanca, poniendo en peligro valores como la democracia y la paz en el mundo.'),
	('E41', 'Recordar contraseña', 'Defreds Jose.A Gomez Iglesias', 'Poesía', 2019, 'S.L.U.Espasa Libros', 4, 'pass', 'castellano', 'La vida son recuerdos.\r\nNos pasamos la vida recordando.\r\n¿En qué momento nos conocimos? ¿Dónde habré dejado las llaves?\r\n¿A quién me recuerda esta canción? ¿A qué hora era la reunión?\r\nAlgunas veces nos gusta echar la vista atrás para recordar\r\nmomentos vividos, días especiales y personas\r\nque ya no están pero que, allí donde estén, nunca querrás olvidar.\r\nOtras, en cambio, nuestra mente nos hace retroceder\r\na justo todo lo contrario: a momentos que no quieres recordar,\r\ndías que desearías no haber vivido y a personas\r\nque ya no pintan nada en tu vida.\r\nAhí, acurrucados y almacenados en nuestro cerebro,\r\nadmiran el paso de nuestra vida.'),
	('E42', 'Te voy a doler siempre', 'Lea Sánchez', 'Poesía', 2019, 'Mueve tu lengua', 5, 'dolor', 'castellano', 'Este libro no es un libro cualquiera; este libro tiene dos hermanos: Te lo diré bajito: qué bueno que viniste y Vamos a subir al cielo a pie. Sin embargo, al contrario de lo que nos pasa en la vida, en la que el hermano pequeño es el consentido, Te voy a doler siempre tiene una madurez prematura que hace que los anteriores cobren especial sentido. La vida es un bucle y tú decides con quién dar vueltas ese quizás sea el mejor mareo al que nos enfrentemos. Lo malo viene cuando no sabes el punto exacto que separa lo bonito y lo tóxico, este libro no te va a descubrir la pólvora.'),
	('E43', 'Mientras tanto', 'Nekane Gonzalez', 'Poesía', 2020, 'Mueve tu lengua', 2, 'mientras', 'castellano', '\"Mientras tanto es un nuevo viaje en el que todo puede suceder, un lugar donde del dolor solo quedan restos de aprendizaje y una oportunidad que se abre ante ti para vivir en primera persona. Es un espacio para lo importante: poder ser tú misma, aprender de los errores, hacer las paces con tu pasado y cambiar el guion de aquello que no te hace feliz. Con todo el amor propio que conlleva. Este libro es una invitación a mirarte cara a cara frente al espejo y mantener la mirada con valentía. Aunque duela.\"'),
	('E44', 'La asamblea de las mujeres', 'Aristofanes', 'Teatro', 2020, 'Catedra', 3, 'asamblea', 'castellano', '\"La asamblea de las mujeres\" presenta el tema del mundo al revés (en este caso, las mujeres en la asamblea imponiendo un nuevo sistema de gobierno) y del disfraz (se disfrazan de hombres para legitimar su acceso y participación en dicha asamblea, los cuales, como mujeres, tienen absolutamente prohibidos).\r\n\"La asamblea de las mujeres\" presenta el tema del mundo al revés (en este caso, las mujeres en la asamblea imponiendo un nuevo sistema de gobierno) y del disfraz (se disfrazan de hombres para legitimar su acceso y participación en dicha asamblea, los cuales, como mujeres, tienen absolutamente prohibidos).'),
	('E45', 'Cómo se llama', 'Rodrigo Garcia', 'Teatro', 2020, 'La uña rota', 4, 'como', '', 'Antes de la nueva glaciación. Una voz habla desde el futuro. Una voz brillante, libre de ataduras, sin pelos en la lengua, que no se siente representante de nada, ni siquiera de sí mismo. Un monólogo afilado que en realidad es un diálogo con un lector improbable, de otra época, (¿nosotros?), a quien guía por un laberinto de pensamientos bien ordenados, y da cuenta de hechos ocurridos en los siglos venideros. Por ejemplo: \"Fue tal la decadencia de la primera mitad del siglo XXI (tocó a su fin en 2065) que obligaban al artista a comportarse como un alcalde de pueblo o concejal de Cultura\"'),
	('E46', 'La fundacion', 'Antonio Buero Vallejo', 'Teatro', 2020, 'S.L.U. Espasa Libros', 3, 'fundacion', 'castellano', 'Presentada como una fábula, plantea al lector-espectador un choque entre realidad y ficción, que se resuelve paulatinamente a favor de la verdad. Cuando, identificados con el protagonista de la obra, creemos que nos encontramos cómodamente instalados en una Fundación, descubrimos que estamos en una cárcel. Es el reflejo de nuestro mundo y de nuestra sociedad. La Introducción y la Guía de lectura de Javier Díez de Revenga, catedrático de la Universidad de Murcia, explican cómo no se trata aquí tan solo de una lección ética, social o filosófica, sino de cómo, a través del arte, llegamos a integrarnos en el conflicto y a buscar una solución.'),
	('E47', 'Hex', 'Thomas Olde Heuvelt', 'Novela', 2020, 'Nocturna Ediciones', 2, 'hex', 'castellano', ' John ConnollyBienvenido a Black Spring, una población pintoresca con un macabro secreto: una mujer recorre las calles con la boca y los ojos cosidos, entra en los hogares y espía a la gente mientras duerme. La llaman la Bruja de Black Rock. Los vecinos se han acostumbrado tanto a su presencia que a veces se les olvida lo que ocurrirá si algún día abre los ojos. Para protegerse de curiosos, los fundadores de Black Spring han instalado equipos de vigilancia con los que mantienen la zona en cuarentena. Hasta que unos adolescentes, hartos de su aislamiento, deciden saltarse las normas y convertir la maldición en una experiencia viral. '),
	('E48', 'De animales a dioses', 'Yuval Noah Harari', 'Novela histórica', 2014, 'Debate', 3, 'sapiens', 'castellano', 'Hace 70.000 años al menos seis especies de humanos habitaban la Tierra. Hoy solo queda una, la nuestra:Homo Sapiens.¿Cómo logró nuestra especie imponerse en la lucha por la existencia? ¿Por qué nuestros ancestros recolectores se unieron para crear ciudades y reinos? ¿Cómo llegamos a creer en dioses, en naciones o en los derechos humanos; a confiar en el dinero, en los libros o en las leyes? ¿Cómo acabamos sometidos a la burocracia, a los horarios y al consumismo? ¿Y cómo será el mundo en los milenios venideros?\r\n\r\nEnSapiens, Yuval Noah Harari traza una breve historia de la humanidad, desde los primeros humanos que caminaron sobre la Tierra'),
	('E49', 'Roma Victoriosa', 'Javier Negrete', 'Novela histórica', 0000, 'La esfera de los libros', 2, 'victoriosa', 'castellano', 'En Roma victoriosa no dará a conocer el origen de la ciudad, de los siete reyes, de la caída de la monarquía y de los primeros siglos de la República. Asistiremos a las vicisitudes de los primeros tiempos, cuando no sólo no estaba claro si Roma llegaría a ser grande, sino incluso si sobreviviría como ciudad. Después veremos a los romanos enfrentarse con el gran general Pirro, empezar su larga historia de conflictos con los galos y mantener dos guerras largas y terriblemente cruentas con Cartago. En el ínterin, comprobaremos cómo las legiones se fueron convirtiendo en la máquina militar que admiró y aterrorizó al mundo.'),
	('E50', 'Berlín la caida :1945', 'Antony Beevor', 'Novela histórica', 2018, 'Critica', 2, 'berlin', 'castellano', 'Beevor combina como nadie un extraordinario talento de militar e historiador con unas dotes narrativas fuera de lo común para describir tanto la complejidad de las grandes operaciones militares y la lógica de las decisiones de sus mandos como los sentimientos de la gente común atrapada en un torbellino de fuego y metralla: la desesperación de Hitler, los deseos de venganza de Stalin, la impotencia de Guderian o la astucia de Zhukov, pero también la paradójica inocencia de unos niños jugando a la guerra con espadas de madera en mitad de sus casas destruidas por las bombas o el asco y el resentimiento de las mujeres brutalmente violadas .'),
	('E51', 'Proyecto Abuelita', 'Anne Fine', 'Infantil', 2018, 'Nordica', 4, 'abuelita', 'castellano', 'La abuela de Iván, Sofía, Tania y Nicolás resulta a veces un poco loca, confunde caras, nombres y no sabe ni en que día vive, pero cuando sus hijos deciden llevarla a una residencia de ancianos, sus cuatro nietos buscarán una solución divertida y llena de ternura para que esto no ocurra.Así comienzan el proyecto Abuelita, plan para hacer cambiar de opinión a sus progenitores y poder seguir disfrutando de la compañía de su excéntrica abuela.'),
	('E52', 'La vida fantástica', 'Didac Bautista', 'Infantil', 2020, 'Planeta', 5, 'vida', 'castellano', 'Los sueños de un niño que lucha contra el cáncer.\r\n«Mi enfermedad me ha hecho vivir etapas difíciles y he tenido que pasar por muchos momentos tristes. Pero todos estos instantes me han enseñado a valorar las cosas de verdad: estar con las personas que me quieren y luchan para hacerme feliz, disfrutar de cada instante, avanzar y no rendirme nunca. Si en tu vida mantienes la esperanza, disfrutarás de una vida fantástica.»'),
	('E53', 'La maravillosa medicina de Jorge', 'Roald Dahl', 'Infantil', 2005, 'Alfaguara', 3, 'medicina', 'castellano', 'Jorge, empeñado en cambiar a su desagradable abuela, inventa una maravillosa medicina con la que consigue transformarla. Pero nada resulta como Jorge esperaba. Los animales de la granja también toman la medicina... y las situaciones más disparatadas no se hacen esperar.'),
	('E54', 'La chica invisible', 'Blue Jeans', 'Thriller', 2018, 'Planeta', 3, 'chica', 'castellano', 'Aurora Ríos es invisible para casi todos. Los acontecimientos del pasado han hecho que se aísle del mundo y que apenas se relacione. A sus diecisiete años, no tiene amigos y está harta de que los habitantes de aquel pueblo hablen a su espalda. Una noche de mayo, su madre no la encuentra en casa cuando regresa del trabajo. No es lo habitual. Aurora aparece muerta a la mañana siguiente en el vestuario de su instituto, el Rubén Darío. Tiene un golpe en la cabeza y han dejado una brújula junto a su cuerpo. ¿Quién es el responsable de aquel terrible suceso? '),
	('E55', 'El puzle de cristal', 'Blue Jeans', 'Thriller', 2020, 'Planeta', 3, 'puzle', 'castellano', 'El primer martes de enero del nuevo año, Julia recibe una inquietante e inesperada llamada. Hugo Velero, uno de los compañeros de piso de Iván Pardo, le asegura que el chico del piercing en la ceja ha desaparecido. Iván le ha hablado mucho a su amigo de su inteligencia y su capacidad deductiva, por lo que le pide ayuda a Julia para encontrarlo. La joven, en principio, piensa que es una broma y no acepta. Pero, casualmente, su abuela Pilar, una entrañable y curiosa septuagenaria, con las mismas capacidades mentales que su nieta, vive cerca del edificio en el que ahora reside el joven del que estuvo enamorada.'),
	('E56', 'La quinta víctima', 'J.D. Barker', 'Thriller', 2020, 'Booket', 3, 'quinta', 'castellano', 'El FBI ha retirado del caso Anson Bishop (el asesino de El Cuarto Mono) a Porter y su equipo, que pronto se enfrentan a una nueva serie de asesinatos: tras estar desaparecida durante tres semanas, el cuerpo de Ella Reynolds aparece en un estanque del Parque Jackson, aunque el agua hace meses que se heló. Además, lleva la ropa de otra joven desaparecida hace tan sólo dos días. Porter y su equipo empiezan a reconstruir las pistas de este nuevo caso y, al mismo tiempo, en secreto, Porter sigue el rastro de Anson. Cuando sus superiores lo descubren, Porter queda suspendido de sus funciones mientras Clair y Nash buscan al asesino del lago. '),
	('E57', 'Historia de los judíos', 'Luis Suárez Fernández', 'Historia', 2015, 'Editorial Ariel', 5, 'judios', 'castellano', 'Entre el versículo del Deuteronomio, que los investigadores señalan como el más antiguo, y el día de hoy media un tiempo de cuatro mil años —no se reclame exactitud— durante los cuales la historia de Israel se desenvuelve como unidad, en medio de vicisitudes con frecuencia terribles y dolorosas. Es una trayectoria sorprendente: de acuerdo con las leyes y tendencias que gobiernan el suceder histórico, el pueblo, privado de su tierra y de sus estructuras políticas, disperso por el globo y agitado por vientos muy fuertes, hubiera podido desaparecer. No puede aportarse ninguna razón lógica de que no haya sido así. '),
	('E58', 'La familia del Prado', 'Juan Eslava Galán', 'Historia', 2019, 'Booket', 2, 'prado', 'castellano', 'El Museo del Prado no es solamente la mejor pinacoteca del mundo; es también el álbum familiar de las dinastías españolas, los Austrias y los Borbones, que han regido los destinos de España desde hace cinco siglos. En este libro, Juan Eslava Galán, con su inconfundible estilo ameno y riguroso, nos propone un recorrido por el museo, del mismo modo que repasamos nuestro álbum familiar contando quién fue cada persona. Pero no se trata en esta ocasión de una historia de nuestro país, sino de una historia del día a día de sus protagonistas: de sus reyes, esposas e hijos, pero también de personajes ilustres, pintores, amantes y plebeyos. ');";
		
		if(!$this->db->query($sql)){
			echo "ERROR: No se ha podido insertar los datos en la tabla \"Libros\".<br>";
		}else{
			echo "Datos insertados en la tabla \"Libros\" con éxito.<br>";
		}
	}
	function insertarAdmin(){
		$pass=Password::hashs("Nohay2sin3");
		$sql = "INSERT INTO Administradores (dni,contraseña) VALUES ('02306613N',\"$pass\")";
		if(!$this->db->query($sql)){
				echo "ERROR: No se ha podido insertar los datos en la tabla \"Administradores\".<br>";
		}else{
			echo "Datos insertados en la tabla \"Administradores\" con éxito.<br>";
		}
	}
}


$conexion = ConectaBD::getInstance();
$conexion->creaBase();
$conexion->creaTablas();
//$conexion->insertarLibros();
$conexion->insertarAdmin();

?>
