<!-- SELECT SUM(montant_HFP), nom_fournisseur_commentaire, prenom_fournisseur_commentaire FROM `vente` WHERE 1 GROUP BY id_fournisseur ORDER BY SUM(montant_HFP) -->

<?php //------------Connexion à la base de donnée------------
	include 'connect.php'; ?>
<?php //------------Ajout du header + menu------------
	$title_top="Fournisseurs";
	include 'top.php'; 
?>
<?php

//----------On verifie si une commande "nouvaeau fournisseur"/"editer fournisseur"/"supprimer fournisseur" a été lancée :------------
  if(isset($_POST["submit_fournisseur"]))
  {
	//Nouveau fournisseur
	if($_POST["submit_fournisseur"]=="create"){
		if(isset($_POST["nom"])){
			if($_POST["nom"]!=NULL){
				$req = $bdd->prepare('INSERT INTO fournisseur(nom, adresse, num_tel_1, num_tel_2, adresse_mail, remarque) VALUES(:nom, :adresse, :num_tel_1, :num_tel_2, :adresse_mail, :remarque)');
				$req->execute(array(
				'nom' => $_POST["nom"],
				'adresse' => $_POST["adresse"],
				'num_tel_1' => $_POST["num_tel_1"],
				'num_tel_2' => $_POST["num_tel_2"],
				'adresse_mail' => $_POST["adresse_mail"],
				'remarque' => $_POST["remarque"]
				));

				echo '<center><font color=#286400>Le fournisseur '. $_POST["nom"].' a bien été ajouté !</font></center><br/><br/>';
			}
			else
				echo '<center><font color=#B22222>Le fournisseur n\'a pas été ajouté car vous n\'avez pas donner de nom !</font></center><br/><br/>';
		}
		else
			echo '<center><font color=#B22222>Le fournisseur n\'a pas été ajouté car vous n\'avez pas donner de nom !</font></center><br/><br/>';
	}
	//Editer un fournisseur
	else if($_POST["submit_fournisseur"]=="editer"){
		if(isset($_POST["id"])){
			if($_POST["id"]!=0){
				$req = $bdd->prepare('UPDATE fournisseur SET nom= :nom, adresse = :adresse, num_tel_1 = :num_tel_1, num_tel_2 = :num_tel_2, adresse_mail = :adresse_mail, remarque = :remarque WHERE id = :id');
				$req->execute(array(
					'id' => $_POST["id"],
					'nom' => $_POST["nom"],
					'adresse' => $_POST["adresse"],
					'num_tel_1' => $_POST["num_tel_1"],
					'num_tel_2' => $_POST["num_tel_2"],
					'adresse_mail' => $_POST["adresse_mail"],
					'remarque' => $_POST["remarque"]
					));
				
				echo '<center><font color=#286400>Le fournisseur '. $_POST["nom"]. ' a bien été édité.</font></center><br/>'; 		
			}
			else
				echo '<center><font color=#B22222>Le fournisseur n\'a pas été édité car l\'id est incorrect !</font></center><br/><br/>';
		}
		else
			echo '<center><font color=#B22222>Le fournisseur n\'a pas été édité car l\'id est incorrect !</font></center><br/><br/>';	
	}
	//Supprimer un fournisseur
	else if($_POST["submit_fournisseur"]=="supprimer"){
		if($_POST['id']!=NULL){
			//Vérifier que le fournisseur n'est pas associé à un achat (achat), une collection (lien_fournisseur), un type (lient_fournisseur) !
				$id_fournisseur_test_suppr=$_POST['id'];
				//Récupération du nombre de liens achat-fournisseur :
				$sql_fournisseur_test_suppr = "SELECT COUNT(*) FROM achat WHERE id_vendeur='$id_fournisseur_test_suppr'"; 
				$reponse_fournisseur_test_suppr = $bdd->query($sql_fournisseur_test_suppr);
				$nbr_fournisseur_test_suppr=$reponse_fournisseur_test_suppr->fetch()[0];
				$reponse_fournisseur_test_suppr->closeCursor(); // Termine le traitement de la requête										
				
				if($nbr_fournisseur_test_suppr==0){
					$id=$_POST['id'];
					$reponse = $bdd->query("DELETE FROM fournisseur WHERE id='$id'");
					echo '<center><font color=#286400>Le fournisseur '. $_POST["nom"]. ' a été supprimé</font></center><br/>';
				}
				else
					echo '<center><font color=#B22222>Le fournisseur n\'a pas été supprimé car il y a encore '.$nbr_fournisseur_test_suppr.' achats associés à cette personne/lieu !</font></center><br/>';
		}
		else
			echo '<center><font color=#B22222>Le fournisseur '. $_POST["nom"]. ' n\a pas été supprimé car son id n\'est pas valide .</font></center><br/>';
	}
	
  }
  
  // Trier les fournisseurs selon le choix de l'utilisateur
  if(isset($_GET["classement"]))
  {
	$classement=$_GET["classement"];
	switch($_GET["classement"])
    {
      case "nom_2" :
				$sql = 'SELECT * FROM fournisseur ORDER BY nom ASC'; 
                break;
      case "nom_1" :
				$sql = 'SELECT * FROM fournisseur ORDER BY nom DESC'; 
                break;
      default :
				$sql = 'SELECT * FROM fournisseur ORDER BY nom ASC'; 
                break;
    }
  }
  else
  {
	// Par défaut, on trie par etat des ventes puis par total achat
	$sql = 'SELECT * FROM fournisseur ORDER BY nom ASC'; 
	$classement="no_classement";
  }
	
	
//--------------------------On affiche tout le contenu de la table fournisseurs en la triant-----------------------------------
$reponse = $bdd->query($sql);
?>
<TABLE BORDER="1"> 
  <CAPTION><h1>Liste des fournisseurs :<h1/></CAPTION> 
  <TR> 
	 <TH> Fournisseur<br/> 
		<form method="get" action="fournisseur.php?classement=<?php echo $classement; ?>"><input type="hidden" name="classement" value="nom_1"/><input type="image" src="img/arrow_top.png" alt="top" /></FORM>
		<form method="get" action="fournisseur.php?classement=<?php echo $classement; ?>"><input type="hidden" name="classement" value="nom_2"/><input type="image" src="img/arrow_bottom.png" alt="bottom" /></FORM> 
	 </TH> 
	 <TH> Adresse</TH> 
	 <TH> Numéros de<br/>téléphone</TH> 
	 <TH> Adresse mail</TH> 
	 <TH> Remarques</TH> 
	 <TH> Editer le fournisseur</TH> 
  </TR> 
 
  <TR> 
<!---------Formulaire d'ajout d'un fournisseur------------------>
<form action="fournisseur.php?classement=<?php echo $classement; ?>"  method="post">
   <input type="hidden" name="submit_fournisseur" value="create">
   <TD><input type="text" name="nom"  style="width: 100px;" ></TD> 
   <TD><textarea name="adresse" rows="2"  style="width: 220px;"></textarea></TD>    
   <TD><input type="text"  style="width: 100px;" name="num_tel_1"><br/>
   <input type="text"  style="width: 100px;" name="num_tel_2"></TD> 
   <TD><input type="text"  name="adresse_mail"></TD>    
   <TD><textarea type="text" style="width: 150px; height: 80px;" name="remarque"></textarea></TD> 
   <TD><input type="submit" value="Ajouter le fournisseur"></TD> 
   </form>
 </TR>
 
<?php
// On affiche chaque entrée une à une
while ($donnees = $reponse->fetch())
{
?>
 <TR> 
    <!-----Formulaire d'édition d'un fournisseur (+affichage)------------->

 <form action="fournisseur.php?classement=<?php echo $classement; ?>" method="post">
   <input type="hidden" name="submit_fournisseur" value="editer">
   <input type="hidden" name="id" value="<?php echo $donnees['id']; ?>">
   <TD><input type="text" name="nom"  style="width: 100px;" value="<?php echo $donnees['nom']; ?>"></TD>
   <TD><textarea name="adresse" rows="2" style="width: 220px;"><?php echo /*nl2br(*/$donnees['adresse']/*)*/; ?></textarea></TD> 
   <TD><input type="text"  style="width: 100px;" name="num_tel_1" value="<?php echo $donnees['num_tel_1']; ?>"><br/>
   <input type="text"  style="width: 100px;" name="num_tel_2" value="<?php echo $donnees['num_tel_2']; ?>"></TD> 
   <TD><input type="text" name="adresse_mail" value="<?php echo $donnees['adresse_mail']; ?>"></TD> 
   <TD><textarea  style="width: 150px; height: 60px;" name="remarque" ><?php echo /*nl2br(*/$donnees['remarque']/*)*/; ?></textarea></TD> 
   <TD>
   <input type="submit" value="Editer le fournisseur"> 
   </form><br/>   
   <!-----Formulaire de suppression d'un fournisseur------------->
   <form action="fournisseur.php?classement=<?php echo $classement; ?>" method="post" >
   <input type="hidden" name="submit_fournisseur" value="supprimer">
   <input type="hidden" name="id" value="<?php echo $donnees['id']; ?>">
   <input type="hidden" name="nom" value="<?php echo $donnees['nom']; ?>">
   <input type="submit" value="Supprimer le fournisseur" onclick="return(confirm('Etes-vous sûr de vouloir supprimer le fournisseur <?php echo $donnees['nom']; ?> ?'));">
   </form>
	</TD> 
	<!--Liens fournisseurs-collections/types-->
  </TR>
<?php
}

$reponse->closeCursor(); // Termine le traitement de la requête

?>


</TABLE>

<?php //------------Ajout des balises de fin------------
	include 'bottom.php';
?>