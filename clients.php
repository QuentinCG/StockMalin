<!-- SELECT SUM(montant_HFP), nom_client_commentaire, prenom_client_commentaire FROM `vente` WHERE 1 GROUP BY id_client ORDER BY SUM(montant_HFP) -->

<?php //------------Connexion à la base de donnée------------
	include 'connect.php'; ?>
<?php //------------Ajout du header + menu------------
	$title_top="Clients";
	include 'top.php'; 
?>
<?php

//----------On verifie si une commande "nouveau client"/"editer client"/"supprimer client" a été lancée :------------
  if(isset($_POST["submit_client"]))
  {
	//Nouveau client
	if($_POST["submit_client"]=="create"){
		if(isset($_POST["prenom"])){
			if($_POST["prenom"]!=NULL){
				$req = $bdd->prepare('INSERT INTO client(prenom, nom, adresse_client, adresse_livraison, num_fixe, num_mobile, adresse_mail, remarques) VALUES(:prenom, :nom, :adresse_client, :adresse_livraison, :num_fixe, :num_mobile, :adresse_mail, :remarques)');
				$req->execute(array(
				'prenom' => $_POST["prenom"],
				'nom' => $_POST["nom"],
				'adresse_client' => $_POST["adresse_client"],
				'adresse_livraison' => $_POST["adresse_livraison"],
				'num_fixe' => $_POST["num_fixe"],
				'num_mobile' => $_POST["num_mobile"],
				'adresse_mail' => $_POST["adresse_mail"],
				'remarques' => $_POST["remarques"]
				));

				echo '<center><font color=#286400>Le client '.$_POST["prenom"]. ' '. $_POST["nom"].' a bien été ajouté !</font></center><br/><br/>';
			}
			else
				echo '<center><font color=#B22222>Le client n\'a pas été ajouté car vous n\'avez pas donner de prenom/pseudo !</font></center><br/><br/>';
		}
		else
			echo '<center><font color=#B22222>Le client n\'a pas été ajouté car vous n\'avez pas donner de prenom/pseudo !</font></center><br/><br/>';
	}
	//Editer un client
	else if($_POST["submit_client"]=="editer"){
		if(isset($_POST["id"])){
			if($_POST["id"]!=0){
				$req = $bdd->prepare('UPDATE client SET prenom = :prenom,  nom= :nom, adresse_client = :adresse_client, adresse_livraison = :adresse_livraison, num_fixe = :num_fixe, num_mobile = :num_mobile, adresse_mail = :adresse_mail, remarques = :remarques WHERE id = :id');
				$req->execute(array(
					'id' => $_POST["id"],
					'prenom' => $_POST["prenom"],
					'nom' => $_POST["nom"],
					'adresse_client' => $_POST["adresse_client"],
					'adresse_livraison' => $_POST["adresse_livraison"],
					'num_fixe' => $_POST["num_fixe"],
					'num_mobile' => $_POST["num_mobile"],
					'adresse_mail' => $_POST["adresse_mail"],
					'remarques' => $_POST["remarques"]
					));
				
				echo '<center><font color=#286400>Le client '.$_POST["prenom"]. ' '. $_POST["nom"]. ' a bien été édité.</font></center><br/>'; 		
			}
			else
				echo '<center><font color=#B22222>Le client n\'a pas été édité car l\'id est incorrect !</font></center><br/><br/>';
		}
		else
			echo '<center><font color=#B22222>Le client n\'a pas été édité car l\'id est incorrect !</font></center><br/><br/>';	
	}
	//Supprimer un client
	else if($_POST["submit_client"]=="supprimer"){
		if($_POST['id']!=NULL){
			//Vérifier que le client n'est pas associé à une vente (vente), une collection (lien_client), un type (lient_client) !
				$id_client_test_suppr=$_POST['id'];
				//Récupération du nombre de liens vente-client :
				$sql_client_test_suppr = "SELECT COUNT(*) FROM vente WHERE id_client='$id_client_test_suppr'"; 
				$reponse_client_test_suppr = $bdd->query($sql_client_test_suppr);
				$nbr_client_test_suppr=$reponse_client_test_suppr->fetch()[0];
				$reponse_client_test_suppr->closeCursor(); // Termine le traitement de la requête						

			//Récupération du nombre de liens client-(collection ou type) :
				$sql_client_test_suppr = "SELECT COUNT(*) FROM lien_client WHERE id_client='$id_client_test_suppr'"; 
				$reponse_client_test_suppr = $bdd->query($sql_client_test_suppr);
				$nbr_client_test_suppr_coltyp=$reponse_client_test_suppr->fetch()[0];
				$reponse_client_test_suppr->closeCursor(); // Termine le traitement de la requête						
				
				if($nbr_client_test_suppr_coltyp==0 AND $nbr_client_test_suppr==0){
					$id=$_POST['id'];
					$reponse = $bdd->query("DELETE FROM client WHERE id='$id'");
					echo '<center><font color=#286400>Le client '.$_POST["prenom"]. ' '. $_POST["nom"]. ' a été supprimé</font></center><br/>';
				}
				else
					echo '<center><font color=#B22222>Le client n\'a pas été supprimé car il y a encore '.$nbr_client_test_suppr.' ventes et '.$nbr_client_test_suppr_coltyp.' collections/types associés à cette personne !</font></center><br/>';
		}
		else
			echo '<center><font color=#B22222>Le client '.$_POST["prenom"]. ' '. $_POST["nom"]. ' n\a pas été supprimé car son id n\'est pas valide .</font></center><br/>';
	}
	//Supprimer un lien client-article/collection
	else if($_POST["submit_client"]=="delete_link")
	{
		if(isset($_POST['id']))
		{
			if($_POST['id']!=0){
				$id=$_POST['id'];
				//echo $id;
				$reponse = $bdd->query("DELETE FROM lien_client WHERE id='$id'");
				echo '<center><font color=#286400>Le lien client-type/collection a été supprimé</font></center><br/>';
			}
			else
				echo '<center><font color=#B22222>Le lien client-type/collection n\'a pas été supprimé car l\'id est incorrect !</font></center><br/>';			
		}
		else
			echo '<center><font color=#B22222>Le lien client-type/collection n\'a pas été supprimé car l\'id est incorrect !</font></center><br/>';			
	}
	else if($_POST["submit_client"]=="create_link")
	{	
		if(isset($_POST["id_client"]) AND isset($_POST["id_type"])){
		 if($_POST["id_client"]>0 AND $_POST["id_type"]>0){
			$id_type=$_POST["id_type"];
			$id_client=$_POST["id_client"];
			$nom_client_commentaire=$_POST["nom_client_commentaire"];
			$prenom_client_commentaire=$_POST["prenom_client_commentaire"];
			
			$reponse55 = $bdd->query("SELECT nom FROM type_collection WHERE id='$id_type'");
			$type_commentaire=$reponse55->fetch()[0];
			$reponse55->closeCursor(); // Termine le traitement de la requête

			$req = $bdd->prepare('INSERT INTO lien_client(id_client, id_collection, nom_client_commentaire, prenom_client_commentaire, collection_commentaire, id_type, type_commentaire) VALUES(:id_client, :id_collection, :nom_client_commentaire, :prenom_client_commentaire, :collection_commentaire, :id_type, :type_commentaire)');
			$req->execute(array(
			'id_client' => $id_client,
			'id_collection' => 0,
			'nom_client_commentaire' => $nom_client_commentaire,
			'prenom_client_commentaire' => $prenom_client_commentaire,
			'collection_commentaire' => "",
			'id_type'=> $id_type,
			'type_commentaire' => $type_commentaire
			));			
			
	
			echo '<center><font color=#286400>Le lien entre le client '.$prenom_client_commentaire.' '.$nom_client_commentaire.' et le type '.$type_commentaire.' a bien été ajouté !</font></center><br/><br/>';
		 }
		 else echo '<center><font color=#B22222>Indiquez un nom du type (lien client-type non crée) ...</font></center><br/><br/>';
		}
		else echo '<center><font color=#B22222>Indiquez un nom du type (lien client-type non crée) ...</font></center><br/><br/>';
	}
	
	
	
  }
  
  // Trier les clients selon le choix de l'utilisateur
  if(isset($_GET["classement"]))
  {
	$classement=$_GET["classement"];
	switch($_GET["classement"])
    {
      case "prenom_2":
				$sql = 'SELECT * FROM client ORDER BY prenom ASC'; 
                break;
      case "prenom_1" :
				$sql = 'SELECT * FROM client ORDER BY prenom DESC'; 
                break;
      case "nom_2" :
				$sql = 'SELECT * FROM client ORDER BY nom ASC'; 
                break;
      case "nom_1" :
				$sql = 'SELECT * FROM client ORDER BY nom DESC'; 
                break;
      case "total_achats_2" :
				$sql = 'SELECT * FROM client ORDER BY total_achats ASC'; 
                break;
      case "total_achats_1" :
				$sql = 'SELECT * FROM client ORDER BY total_achats DESC'; 
                break;      default :
				$sql = 'SELECT * FROM client ORDER BY total_achats DESC, prenom DESC'; 
                break;
    }
  }
  else
  {
	// Par défaut, on trie par etat des ventes puis par total achat
	$sql = 'SELECT * FROM client ORDER BY total_achats DESC, prenom DESC'; 
	$classement="no_classement";
  }
	
	
//--------------------------On affiche tout le contenu de la table clients en la triant-----------------------------------
$reponse = $bdd->query($sql);
?>
<TABLE BORDER="1"> 
  <CAPTION><h1>Liste des clients :<h1/></CAPTION> 
  <TR> 
  
	 <TH> Prénom/Pseudo<br/> 
		<form method="get" action="clients.php?classement=<?php echo $classement; ?>"><input type="hidden" name="classement" value="prenom_1"/><input type="image" src="img/arrow_top.png" alt="top" /></FORM>
		<form method="get" action="clients.php?classement=<?php echo $classement; ?>"><input type="hidden" name="classement" value="prenom_2"/><input type="image" src="img/arrow_bottom.png" alt="bottom" /></FORM>
	</TH> 
	 <TH> Nom<br/> 
		<form method="get" action="clients.php?classement=<?php echo $classement; ?>"><input type="hidden" name="classement" value="nom_1"/><input type="image" src="img/arrow_top.png" alt="top" /></FORM>
		<form method="get" action="clients.php?classement=<?php echo $classement; ?>"><input type="hidden" name="classement" value="nom_2"/><input type="image" src="img/arrow_bottom.png" alt="bottom" /></FORM> 
	 </TH> 
	 <TH> Adresses</TH> 
	 <TH> Numéros de<br/>téléphone</TH> 
	 <TH> Adresse mail</TH> 
	 <TH> Total<br/>ventes<br/> 
		<form method="get" action="clients.php?classement=<?php echo $classement; ?>"><input type="hidden" name="classement" value="total_achats_1"/><input type="image" src="img/arrow_top.png" alt="top" /></FORM>
		<form method="get" action="clients.php?classement=<?php echo $classement; ?>"><input type="hidden" name="classement" value="total_achats_2"/><input type="image" src="img/arrow_bottom.png" alt="bottom" /></FORM>
	 </TH> 
	 <TH> Ventes<br/>associées</TH>
	 <TH> Remarques</TH> 
	 <TH> Editer le client</TH> 
	 <TH    style="width: 220px;"> Interets du client</TH> 
  </TR> 
 
  <TR> 
<!---------Formulaire d'ajout d'un client------------------>
<form action="clients.php?classement=<?php echo $classement; ?>"  method="post">
   <input type="hidden" name="submit_client" value="create">
   <TD><input type="text" style="width: 80px;" name="prenom"></TD>
   <TD><input type="text" name="nom"  style="width: 100px;" ></TD> 
   <TD><textarea name="adresse_client" rows="2"  style="width: 220px;"></textarea><br/>
       <textarea name="adresse_livraison" rows="2"  style="width: 220px;"></textarea></TD>    
   <TD><input type="text"  style="width: 100px;" name="num_fixe"><br/>
   <input type="text"  style="width: 100px;" name="num_mobile"></TD> 
   <TD><input type="text"  name="adresse_mail"></TD>    
   <TD></TD> 
   <TD></TD> 
   <TD><textarea type="text" style="width: 150px; height: 80px;" name="remarques"></textarea></TD> 
   <TD><input type="submit" value="Ajouter le client"></TD> 
   </form>
   <TD>Cette section permet de lier des types/collections au client.</TD> 

 </TR>
 
<?php
// On affiche chaque entrée une à une
while ($donnees = $reponse->fetch())
{
?>
 <TR> 
    <!-----Formulaire d'édition d'un client (+affichage)------------->

 <form action="clients.php?classement=<?php echo $classement; ?>" method="post">
   <input type="hidden" name="submit_client" value="editer">
   <input type="hidden" name="id" value="<?php echo $donnees['id']; ?>">
   <TD><input type="text" name="prenom"  style="width: 80px;" value="<?php echo $donnees['prenom']; ?>"></TD> 
   <TD><input type="text" name="nom"  style="width: 100px;" value="<?php echo $donnees['nom']; ?>"></TD>
   <TD><textarea name="adresse_client" rows="2" style="width: 220px;"><?php echo /*nl2br(*/$donnees['adresse_client']/*)*/; ?></textarea><br/>
   <textarea name="adresse_livraison" rows="2" style="width: 220px;"><?php echo /*nl2br(*/$donnees['adresse_livraison']/*)*/; ?></textarea></TD> 
   <TD><input type="text"  style="width: 100px;" name="num_fixe" value="<?php echo $donnees['num_fixe']; ?>"><br/>
   <input type="text"  style="width: 100px;" name="num_mobile" value="<?php echo $donnees['num_mobile']; ?>"></TD> 
   <TD><input type="text" name="adresse_mail" value="<?php echo $donnees['adresse_mail']; ?>"></TD> 
   <TD><?php echo $donnees['total_achats']; ?> €</TD> 
   <TD><!--Les ventes--><p style="width: 250px;" >
	<?php 
    $id1=$donnees['id'];
   	$sql2 = "SELECT collection_majoritaire,etat_vente,montant_HFP FROM vente WHERE id_client='$id1'"; 
	$reponse2 = $bdd->query($sql2);
	
	while ($donnees2 = $reponse2->fetch())
	{
		echo $donnees2["collection_majoritaire"]." : ".$donnees2["etat_vente"]." (".$donnees2["montant_HFP"]."€)<br/>";
	}
	?>
   
   </p>
   </TD>    
   <TD><textarea  style="width: 150px; height: 60px;" name="remarques" ><?php echo /*nl2br(*/$donnees['remarques']/*)*/; ?></textarea></TD> 
   <TD>
   <input type="submit" value="Editer le client"> 
   </form><br/>   
   <!-----Formulaire de suppression d'un client------------->
   <form action="clients.php?classement=<?php echo $classement; ?>" method="post" >
   <input type="hidden" name="submit_client" value="supprimer">
   <input type="hidden" name="id" value="<?php echo $donnees['id']; ?>">
   <input type="hidden" name="nom" value="<?php echo $donnees['nom']; ?>">
   <input type="hidden" name="prenom" value="<?php echo $donnees['prenom']; ?>">
   <input type="submit" value="Supprimer le client" onclick="return(confirm('Etes-vous sûr de vouloir supprimer le client <?php echo $donnees['prenom']. ' ' .$donnees['nom']; ?>?'));">
   </form>
	</TD> 
	<!--Liens clients-collections/types-->
	<TD>
 
  
<?php  //"LISTE DES TYPES ($liste_types utilisé par la suite)
		   $liste_types="<!--Debut de la liste des types-->\n<select name=\"id_type\">\n<option value=\"0\">--liste des types--</option>\n"; 
					$reponse44 = $bdd->query('SELECT nom,id FROM type_collection ORDER BY nom ASC');
					while ($donnees99 = $reponse44->fetch())
					{
						if($donnees99['nom']!=NULL)
							$liste_types.="<option value=\"".$donnees99['id']."\">".$donnees99['nom']."</option>\n";
					}
					$reponse44->closeCursor(); // Termine le traitement de la requête
			$liste_types.="</select>\n<!--Fin de la liste des types-->\n";

		//echo $liste_types;
?>
	<!--Ajout du formulaire d'ajout d'un lien client-type-->
   <form action="clients.php?classement=<?php echo $classement; ?>" method="post" >
				<input type="hidden" name="submit_client" value="create_link">
				<input type="hidden" name="id_client" value="<?php echo $donnees['id']; ?>">	
				<input type="hidden" name="nom_client_commentaire" value="<?php echo $donnees['nom']; ?>">	
				<input type="hidden" name="prenom_client_commentaire" value="<?php echo $donnees['prenom']; ?>">	
				<?php echo $liste_types; //liste des clients (id_type) ?>
				<input type="submit" value="Lier le client">
	</form>	
	<!--Fin du formulaire d'ajout d'un lien client-type-->
		<br/>
		
	<?php  //Lien client-collections/types
			$id_client=$donnees['id'];
			$sql_nb_coll = "SELECT id,id_collection,collection_commentaire,type_commentaire FROM lien_client WHERE id_client='$id_client'"; 
			$reponse_nb_coll = $bdd->query($sql_nb_coll);
			while ($donnees_article_m = $reponse_nb_coll->fetch())
			{
				if($donnees_article_m["id_collection"]!="0" AND $donnees_article_m["collection_commentaire"]!=NULL)
					echo $donnees_article_m["collection_commentaire"];
				else
					echo $donnees_article_m["type_commentaire"];
	?>		
			<!-- Suppression du lien client-collection -->
		<form action="clients.php?classement=<?php echo $classement; ?>" method="post" >
			<input type="hidden" name="submit_client" value="delete_link">
			<input type="hidden" name="id" value="<?php echo $donnees_article_m['id']; //id du lien client-collection/type ?>">
			<input type="image" src="img/bouton_supprimer.png" alt="top" value="Supprimer le lien client-collection/type" onclick="return(confirm('Etes-vous sûr de vouloir supprimer ce lien client-collection/type ?'));"/>
		</form>
		<?php
			echo '<br/>';
			}
			$reponse_nb_coll->closeCursor(); // Termine le traitement de la requête			
		?>
 
 
 
	</TD>
 
  </TR>
<?php
}

$reponse->closeCursor(); // Termine le traitement de la requête

?>


</TABLE>

<?php //------------Ajout des balises de fin------------
	include 'bottom.php';
?>