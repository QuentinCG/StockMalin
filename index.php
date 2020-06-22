<?php //------------Connexion à la base de donnée------------
	include 'connect.php'; ?>
	
<?php //------------Ajout du header + menu------------
	$title_top="StockMalin";
	include 'top.php'; 
?>

<?php

//Create the new table if not already done :
$my_query="CREATE TABLE IF NOT EXISTS `debug` (`last_edit` int(11) NOT NULL,  `explication` varchar(255) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
$bdd->query($my_query);

//Check the value of the last patch to be sure we have nothing to patch
	$sql_check = "SELECT COUNT(last_edit) FROM debug WHERE last_edit='1'"; 
	$reponse_check = $bdd->query($sql_check);
	if($reponse_check->fetch()[0]==0){//We have to patch
		include 'resoudre_erreurs.php';
	}
	$reponse_check->closeCursor(); // Termine le traitement de la requête
	

?>
<p>
<h1>Introduction :</h1>
Ce "logiciel" permet de gerer en temps réel des stocks/clients/fournisseurs/achats/ventes.<br/>
<br/>
Un article est associé à une collection qui elle même est associée à un type de collection.<br/>
<br/>
Les ventes/Achats en cours décrémentent/incrémentent le stock.<br/>
Il est cependant possible de faire machine arrière si la vente n'a en fait pas eu lieu (en spécifiant que la vente est 'annulée').<br/>
<br/>
Pour éviter d'avoir des articles/collections orphelin(e)s, notez qu'il n'est pas possible de supprimer un type/collection si des collections/ventes/achats y sont liées.<br/>
<br/>
Notez également qu'il n'est pas possible d'éditer plusieurs articles/collections/types/clients en même temps (si vous souhaitez éditer plusieurs clients (ou autre), il faut les éditer les uns après les autres).<br/>
</p>

<p>
<h1>Enregistrer la base de donnée sous format SQL/PDF/CSV for MS Excel :</h1>
Il est impératif de sauvegarder de temps en temps la base de donnée afin d'eviter de perdre les informations contenues dans celle ci en cas de panne.<br/>
<br/>
Voici la manière de procéder :<br/>
1) Lancer le logiciel WampServer<br/>
2) Aller sur le site <a target="_blank" href="http://localhost/phpmyadmin/">http://localhost/phpmyadmin/</a> (login:"root",password:"")<br/>
3) Selectionner la base de donnée "stockmalin" (à gauche)<br/>
4) Cliquer sur "exporter" (dans le menu en haut à droite)<br/>
5) Selectionner le format d'exportation puis appuyer sur le bouton "Exécuter"<br/>
<br/>
Notez que :<br/>
-le format "SQL" permet de faire une sauvegarde de la base de donnée afin de pouvoir la réutiliser sur le site (à faire de temps en temps en conservant bien le fichier généré).<br/>
-le format "PDF" permet d'avoir une vue d'ensemble des données.<br/>
-le format "CSV for MS Excel" permet d'utiliser les données sur Excel.<br/>
</p>

 
<?php //------------Ajout des balises de fin------------
	include 'bottom.php';
?>