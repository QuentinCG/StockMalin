<?php //------------Connexion à la base de donnée------------
	include 'connect.php'; ?>
<?php //------------Ajout du header + menu------------
	$title_top="TEST !!!!!";
	$date_top="OK"; //Activation de la date
	include 'top.php'; 
?>

<?php 
		   $liste_clients="<!--Debut de la liste clients-->\n<select name=\"id_client\">\n<option value=\"0\"></option>\n"; 
					$reponse = $bdd->query('SELECT nom,prenom,id FROM client ORDER BY prenom ASC');
					while ($donnees = $reponse->fetch())
					{
						if($donnees['prenom']!=NULL AND $donnees['nom']!=NULL AND $donnees['id']!=NULL)
							$liste_clients.="<option value=\"".$donnees['id']."\">".$donnees['prenom']." ".$donnees['nom']."</option>\n";
					}
					$reponse->closeCursor(); // Termine le traitement de la requête
			$liste_clients.="</select>\n<!--Fin de la liste clients-->\n";

		//echo $liste_clients;
?>
<?php //------------Ajout des balises de fin------------
	include 'bottom.php';
?>