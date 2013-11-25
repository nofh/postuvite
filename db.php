<?php

/*
*/
/*
$host = "localhost";
$user = "root";
$pass = "";

try
{
    // connexion persistante
    $dbh = new PDO('mysql:host=localhost;dbname=test', $user, $pass, array(PDO::ATTR_PERSISTENT => true));
}
catch(PDOException $e)
{
   // echo "Erreur " . $e->getMessage();
    die();
} 

// requete
foreach( $dbh->query('SELECT * FROM t') as $row )
{
   // print_r($row);
}

// transaction 
try
{
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $dbh->beginTransaction();
    $dbh->exec("INSERT INTO t (a) VALUES ( 10);");
    $dbh->exec("INSERT INTO t (a) VALUES ( 20);");
    $dbh->commit();
}
catch(Exception $e)
{
    $dbh->rollBack();
    echo "FAILED: " . $e->getMessage();
}

// requetes preparer
$valeurNbr = 30;
$stmt = $dbh->prepare("INSERT INTO t (a) VALUES (:nbr);");
$stmt->bindParam(':nbr', $valeurNbr, PDO::PARAM_INT);


//$stmt->debugDumpParams();
$stmt->execute();

$stmt = $dbh->prepare("SELECT * FROM t WHERE a = ?");
$stmt->bindParam(1, $valeurNbr, PDO::PARAM_INT);
$ok = $stmt->execute();
print_r($stmt->fetch(PDO::FETCH_ASSOC));
print_r($stmt->fetch(PDO::FETCH_ASSOC));
print_r($stmt->fetch(PDO::FETCH_ASSOC));

// query
$reponse = $dbh->query('SELECT * FROM t WHERE a = 20');
echo "</br>";
while($donnes = $reponse->fetch())
{
	echo "</br>" . $donnes['a'];
}
$reponse->closeCursor();

// fermeture 
$dbh = null;
*/
class Contact 
{
	private $nom;
	private $prenom;
	private $genre;
	private $grade;
	private $mail;
	private $tel;

	public function __construct($nom, $prenom=null, $genre=null, $grade=null, $mail=null, $tel=null)
	{
		$this->nom = $nom;
		$this->prenom = $prenom;
		$this->genre = $genre;
		$this->grade = $grade;
		$this->mail = $mail;
		$this->tel = $tel;
	}

	public function __toString()
	{
		return "string";
	}
}
class Db
{
	private static $instance;
	private $dbh;


	private function __construct($user, $passwd, $host, $dbname)
	{
		try
		{
			$this->dbh = new PDO("mysql:host=$host;dbname=$dbname", $user, $passwd, array( PDO::ATTR_PERSISTENT => true ) );
		}
		catch(PDOException $e)
		{
			die("Erreur Db : " . $e->getMessage());
		}
	}

	public static function getInstance($user, $passwd, $host='localhost', $dbname='test')
	{
		if ( !isset(self::$instance) )
		{
			$c = __CLASS__;
			self::$instance = new $c($user, $passwd, $host, $dbname);
		}

		return self::$instance;
	}

	public function __destruct()
	{
		if ( !empty($this->dbh) )
			$this->dbh = null;
	}
	
	public function __clone()
	{
		trigger_error("Class singleton", E_USER_ERROR);
	}

	public function initialiseDb($mode=0)
	{
		$rqs = array();

		switch ( $mode )
		{
			case 0:
				echo 'rien';
			break;
			case 1:
				echo 'creation des tables';
				$rqs[] = "CREATE TABLE IF NOT EXISTS `contact` ( `id` int(10) unsigned NOT NULL AUTO_INCREMENT, " . 
															"`nom` varchar(30) DEFAULT NULL,`prenom` varchar(30) DEFAULT NULL," .
															"`genre` varchar(30) DEFAULT 'Madame, Monsieur,',`grade` varchar(150) DEFAULT NULL," .
															"`mail` varchar(30) DEFAULT NULL, `tel` varchar(30) DEFAULT NULL," . 
															"PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ";
			break;
			case 2:
				echo 'remplissage des tables';
				for ( $i = 0; $i < 10 ; $i++)
				{
					$rqs[] = "INSERT INTO contact ( nom ) VALUES ($i)";
				}
			break;
			case 3:
				echo 'vider les tables';
				$rqs[] = "DELETE FROM contact";
			break;
			case 4:
				echo 'supprimer tables';
				$rqs[] = "DROP TABLE contact";
			break;
		}

		//
		foreach ( $rqs as $rq )
		{
			$this->dbh->query($rq);
			echo $rq;
		}
	}

	public function recupererContact($idContact)
	{	
		$contacts = array();

		$stmt = $this->dbh->prepare("SELECT * FROM contact WHERE id = :id");
		$stmt->bindParam(':id', $idContact);
		$ok = $stmt->execute();
		if ( $ok )
		{
			while ( $resultat = $stmt->fetch(PDO::FETCH_ASSOC) )
			{
				$cont = new Contact($resultat['nom']);
				$contacts[] = $cont;
			}
		}

		return ( count($contacts) > 0 ) ? $contacts : false;
	}

	public function ajouterContact($nom, $prenom=null, $genre=null, $grade=null, $mail=null, $tel=null)
	{
		$stmt = $this->dbh->prepare("INSERT INTO contact ( nom, prenom, genre, grade, mail, tel ) VALUES ( :nom, :prenom, :genre, :grade, :mail, :tel)");
		( empty($genre) ) ? $genre = "kmlkml": $genre = $genre;
		$stmt->bindParam(':nom', $nom);
		$stmt->bindParam(':prenom', $prenom);
		$stmt->bindParam(':genre', $genre);
		$stmt->bindParam(':grade', $grade);
		$stmt->bindParam(':mail', $mail);
		$stmt->bindParam(':tel', $tel);
		return $stmt->execute();
	}
}
/*
echo "test Db";
$db = new Db("root","");
//$db->initialiseDb(2);
$rez = $db->recupererContact(100);
echo $rez ;
$db->ajouterContact("nom111");
*/
echo "test db singleton </br>";
$db2 = DB::getInstance("root", "");
$rez = $db2->recupererContact(1);
print_r($rez) ;
?>

