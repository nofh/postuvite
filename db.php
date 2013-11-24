<?php

/*
https://phpmyadmin.ovh.net/index.php?server=415;token=79e47d30eef63472065ed934319ebb9e
Serveur: mysql51-88.perso	
Login:	agiwebmod1
db: agiwebmod1
*/

$user = "luern";
$pass = "luern987412";
try
{
    // connexion persistante
    $dbh = new PDO('mysql:host=localhost;dbname=test', $user, $pass, array(PDO::ATTR_PERSISTENT => true));
}
catch(PDOException $e)
{
    echo "Erreur " . $e->getMessage();
    die();
} 

// requete
foreach( $dbh->query('SELECT * FROM t') as $row )
{
    print_r($row);
}

// transaction 
try
{
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $dbh->beginTransaction();
    $dbh->exec("INSERT INTO t (un) VALUES ( 10);");
    $dbh->exec("INSERT INTO t (un) VALUES ( 20);");
    $dbh->commit();
}
catch(Exception $e)
{
    $dbh->rollBack();
    echo "FAILED: " . $e->getMessage();
}
// fermeture 
$dbh = null;
?>
