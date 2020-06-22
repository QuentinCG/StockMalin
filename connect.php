<!-- Début page connect.php-->
<?php
include 'config.php'; 
error_reporting(0);

try
{
	//--------------------------------------------On se connecte à MySQL---------------------------------------------
	$bdd = new PDO('mysql:host='.$database_ip.';dbname='.$database_name, $database_username, $database_password);
	
	$type_encodage = $bdd->query("SET NAMES UTF8");
}
catch (Exception $e)
{
	// En cas d'erreur, on affiche un message et on arrête tout
        die('GROS PROBLEME :<br/>Impossible de se connecter à la base de donnée :<br/>' . $e->getMessage());
}

// Si tout va bien, on peut continuer
?>
<!-- Fin page connect.php-->
