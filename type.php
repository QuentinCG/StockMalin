<?php //------------Connexion à la base de donnée------------
	include 'connect.php'; ?>
<?php //------------Ajout du header + menu------------
	$title_top="Types de collections";
	include 'top.php'; 
?>

<?php //------------Traitement des demandes de l'utilisateur (ajout/edition/suppresion de types de collections)------------
  if(isset($_POST["submit_type"]))
  {
	//Nouveau type (si nom non vide)
	if($_POST["submit_type"]=="create"){
		if($_POST["nom"]!=""){
			$req = $bdd->prepare('INSERT INTO type_collection(nom, description,ref_bon_coin) VALUES(:nom,:description,:ref_bon_coin)');
			$req->execute(array(
			'nom' => $_POST["nom"],
			'ref_bon_coin' => $_POST["ref_bon_coin"],
			'description' => $_POST["description"]
			));

			echo '<center><font color=#286400>Le type de collection '.$_POST["nom"].' a bien été ajouté !</font></center><br/><br/>';
		}
		else 
			echo '<center><font color=#B22222>Le type de collection '.$_POST["nom"].' n\'a pas été ajouté car vous n\'avez pas précisé le nom !</font></center><br/><br/>';
	}
	//Editer un type
	else if($_POST["submit_type"]=="editer"){
		$req = $bdd->prepare('UPDATE type_collection SET nom= :nom, ref_bon_coin =:ref_bon_coin, description = :description WHERE id = :id');
		$req->execute(array(
		'id' => $_POST["id"],
		'nom' => $_POST["nom"],
		'ref_bon_coin' => $_POST["ref_bon_coin"],
		'description' => $_POST["description"]
		));
		
		echo '<center><font color=#286400>Le type '.$_POST["nom"].' a bien été édité.</font></center><br/>'; 		
	
	}
	//Supprimer un type si il n'est pas associé à une collection.
	else if($_POST["submit_type"]=="supprimer"){
			if($_POST['id']!=NULL){
				
				//Test pour savoir si le type est lié à une collection :
				$id_type_test=$_POST['id'];
				$sql2 = "SELECT COUNT(*) FROM collection WHERE id_type='$id_type_test'"; 
				$reponse2 = $bdd->query($sql2);
				$nombre_collections_liees_avec_type=$reponse2->fetch()[0];
				$reponse2->closeCursor(); // Termine le traitement de la requête
				
				//Test pour savoir si un client est lié au type :
				$id_type_test=$_POST['id'];
				$sql2 = "SELECT COUNT(*) FROM lien_client WHERE id_type='$id_type_test'"; 
				$reponse2 = $bdd->query($sql2);
				$nombre_clients_liees_avec_type=$reponse2->fetch()[0];
				$reponse2->closeCursor(); // Termine le traitement de la requête				
				
				
				if($nombre_collections_liees_avec_type==0 AND $nombre_clients_liees_avec_type==0){ //Aucune collection liée au type
							$reponse_suppr = $bdd->query("DELETE FROM type_collection WHERE id='$id_type_test'");
							echo '<center><font color=#286400>Le type '. $_POST["nom"]. ' a été supprimé</font></center><br/>';
							$reponse_suppr->closeCursor(); // Termine le traitement de la requête
				}
				else{ //On ne peut pas supprimer le type car il est lié à des collections
						echo '<center><font color=#B22222>Le type '. $_POST["nom"]. ' n\'a pas été supprimé car '.$nombre_collections_liees_avec_type.' collections et '.$nombre_clients_liees_avec_type.' clients sont associés à ce type !<br/>
						Pour supprimer ce type, veuillez au préalable supprimer les collections et les clients liés à ce type !</font></center><br/>';
				
				}
			}
			else 
				echo '<center><font color=#B22222>Le type '. $_POST["nom"]. ' n\'a pas été supprimé car l\'identifiant est mauvais (NULL)</font></center><br/>';
		
	}













	else if($_POST["submit_type"]=="create_link"){
		if(isset($_POST["id_client"]) AND isset($_POST["id_type"])){
		 if($_POST["id_client"]>0 AND $_POST["id_type"]>0){
				
			$id_client=$_POST["id_client"];
			$id_type=$_POST["id_type"];
			
			
			$sql2 = "SELECT nom, prenom FROM client WHERE id='$id_client'"; 
			$reponse2 = $bdd->query($sql2);
			while ($donnees = $reponse2->fetch())
			{	
				$nom_client_commentaire=$donnees['nom'];
				$prenom_client_commentaire=$donnees['prenom'];
			}
			$reponse2->closeCursor(); // Termine le traitement de la requête
			
			$sql2 = "SELECT nom FROM type_collection WHERE id='$id_type'"; 
			$reponse2 = $bdd->query($sql2);
			$type_commentaire=$donnees = $reponse2->fetch()[0];
			$reponse2->closeCursor(); // Termine le traitement de la requête

			$req = $bdd->prepare('INSERT INTO lien_client(id_client, id_type, nom_client_commentaire, prenom_client_commentaire, type_commentaire) VALUES(:id_client, :id_type, :nom_client_commentaire, :prenom_client_commentaire, :type_commentaire)');
			$req->execute(array(
			'id_client' => $id_client,
			'nom_client_commentaire' => $nom_client_commentaire,
			'prenom_client_commentaire' => $prenom_client_commentaire,
			'id_type'=> $id_type,
			'type_commentaire' => $type_commentaire
			));

			echo '<center><font color=#286400>Le lien entre le client '.$prenom_client_commentaire.' '.$nom_client_commentaire.' et le type '.$type_commentaire.' a bien été ajouté !</font></center><br/><br/>';
		 }
		 else echo '<center><font color=#B22222>Indiquez un nom de client (lien client-collection non crée) ...</font></center><br/><br/>';

		}
		else echo '<center><font color=#B22222>Indiquez un nom de client (lien client-collection non crée) ...</font></center><br/><br/>';
	}
	
	//Supprimer un lien client-"type-collection"
	else if($_POST["submit_type"]=="delete_link"){
		if($_POST['id']!=NULL){
			$id=$_POST['id'];
			$reponse = $bdd->query("DELETE FROM lien_client WHERE id='$id'");
			echo '<center><font color=#286400>Le lien client-type a été supprimé</font></center><br/>';
		}
	}	
}
?>
<?php
  //------------Trier les types selon le choix de l'utilisateur------------
  if(isset($_GET["classement"]))
  {
	$classement=$_GET["classement"];
	switch($classement)
    {
      case "nom_2":
				$sql = 'SELECT * FROM type_collection ORDER BY nom ASC'; 
                break;
      case "nom_1" :
				$sql = 'SELECT * FROM type_collection ORDER BY nom DESC'; 
                break;				
      default :
				$sql = 'SELECT * FROM type_collection ORDER BY nom ASC'; 
                break;
    }
  }
  else
  {
	// Par défaut, on trie par nom descendant
	$sql = 'SELECT * FROM type_collection ORDER BY nom ASC'; 
	$classement="no_classement";
  }

?>


<!------------Tableau des types------------>
<TABLE BORDER="1"> 
  <CAPTION><h1>Liste des types de collections :<h1/></CAPTION> 
  <TR> 
	 <TH> Type de collection<br/>
	 	<form method="get" action="type.php?classement=<?php echo $classement; ?>"><input type="hidden" name="classement" value="nom_1"/><input type="image" src="img/arrow_top.png" alt="top" /></FORM>
		<form method="get" action="type.php?classement=<?php echo $classement; ?>"><input type="hidden" name="classement" value="nom_2"/><input type="image" src="img/arrow_bottom.png" alt="bottom" /></FORM>
	 </TH> 
	 <TH> Référence<br/>Bon Coin</TH> 
	 <TH> Collections associées </TH> 
	 <TH> Valeur totale </TH>
	 <TH> Description </TH> 
	 <TH> Editer </TH> 
	 <TH> Liens<br/>client-type</TH> 
 </TR> 

<!------------Formulaire d'ajout d'un type de collection------------>
<TR> 
	<form action="type.php?classement=<?php echo $classement; ?>" method="post">
	   <input type="hidden" name="submit_type" value="create">
	   <TD><input type="text" style="width: 200px;" name="nom"></TD><!--Type de collection-->
	   <TD><input type="text" name="ref_bon_coin"></TD><!--Type de collection-->
	   <TD>Pour associer des collections aux types, il faut le faire dans la <a href="collection.php">section "collection"</a>.</TD> <!--Collections associées-->
	   <TD>Calcul automatique</TD>
	   <TD><textarea type="text" style="width: 300px; height: 80px;" name="description"></textarea></TD><!-- Description-->
	   <TD><input type="submit" value="Ajouter un type de collection"></TD> 
   </form>
 	   <TD>Pour associer des clients aux types, il faut en premier lieu creer le type</TD>
</TR>
<!------------Affichage des types de collections déjà existants------------>
<?php
	$reponse = $bdd->query($sql);
	while ($donnees = $reponse->fetch())
	{	
?> 

	<TR>
		<form action="type.php?classement=<?php echo $classement; ?>" method="post">
		   <input type="hidden" name="submit_type" value="editer">
		   <input type="hidden" name="id" value="<?php echo $donnees['id']; ?>">
		   <TD><?php echo $donnees['nom']; ?><input type="hidden" style="width: 200px;" name="nom" value="<?php echo $donnees['nom']; ?>"></TD><!--Type de collection-->
		   <TD><input type="text" name="ref_bon_coin" value="<?php echo $donnees['ref_bon_coin']; ?>"></TD><!--Reference du bon coin-->
		   <TD>
		   <?php
				$id_type_echo=$donnees['id'];
		      	$sql3 = "SELECT nom,id FROM collection WHERE id_type='$id_type_echo' ORDER BY nom ASC"; 
				$reponse3 = $bdd->query($sql3);
	
				while ($donnees3 = $reponse3->fetch())
				{
				echo $donnees3['nom'].',<br/>';
				
				}
		   ?>
		   
		   </TD> <!--Collections associées-->
		   
		   
		   
		<TD>
		<?php
		$id_type_echo=$donnees['id'];
		$sql_valeur_type="
		SELECT SUM(a.stock*c.prix) 
		FROM collection AS c, article AS a 
		WHERE c.id_type = $id_type_echo
		AND c.id = a.id_collection	
		";
		
		$reponse_valeur_types = $bdd->query($sql_valeur_type);
		$prix_par_type=$reponse_valeur_types->fetch()[0];
		echo $prix_par_type;
		/*
		//echo $prix_par_article;
		//Récupération du nombre d'articles de la collection
		$sql_nombre_d_articles = "SELECT SUM(stock) FROM article WHERE id_collection='$id_coll'"; 
		$reponse_nombre_d_articles = $bdd->query($sql_nombre_d_articles);
		$nombre_d_articles=$reponse_nombre_d_articles->fetch()[0];		
		
		//echo $nombre_d_articles;
		//Affichage de la valeur de la collection
		echo $prix_par_article*$nombre_d_articles;
		*/
		?>€
		</TD> <!--valeur du type de collection-->
		   
		   
		   
		   
		   <TD><textarea type="text" style="width: 300px; height: 80px;" name="description"><?php echo $donnees['description']; ?></textarea></TD><!-- Description-->
		   <TD><input type="submit" value="Editer le type">
		</form>
		<br/>   
<!-----Formulaire de suppression d'un type------------->
		<form action="type.php?classement=<?php echo $classement; ?>" method="post">
		   <input type="hidden" name="submit_type" value="supprimer">
		   <input type="hidden" name="id" value="<?php echo $donnees['id']; ?>">
		   <input type="hidden" name="nom" value="<?php echo $donnees['nom']; ?>">
		   <input type="submit" value="Supprimer le type" onclick="return(confirm('Etes-vous sûr de vouloir supprimer le type <?php echo $donnees['nom']; ?> ?'));">
	   </form>
		</TD>
		<TD><!--Lien client-type-->
			<?php  //"LISTE DES CLIENTS ($liste_clients utilisé par la suite)
					   $liste_clients="<!--Debut de la liste clients-->\n<select name=\"id_client\">\n<option value=\"0\">--liste des clients--</option>\n"; 
								$reponse_a = $bdd->query('SELECT nom,prenom,id FROM client ORDER BY prenom ASC');
								while ($donnees_a = $reponse_a->fetch())
								{
									if($donnees_a['prenom']!=NULL AND $donnees_a['nom']!=NULL AND $donnees_a['id']!=NULL)
										$liste_clients.="<option value=\"".$donnees_a['id']."\">".$donnees_a['prenom']." ".$donnees_a['nom']."</option>\n";
								}
								$reponse_a->closeCursor(); // Termine le traitement de la requête
						$liste_clients.="</select>\n<!--Fin de la liste clients-->\n";

					//echo $liste_clients;
			?>		
		
			<!--Ajout du formulaire d'ajout d'un lien client-collection-->
				<form action="type.php?classement=<?php echo $classement; ?>" method="post">
					<input type="hidden" name="submit_type" value="create_link">
					<input type="hidden" name="id_type" value="<?php echo $donnees['id'];  //id de la collection (id)	 ?>">	
					<?php echo $liste_clients; //liste des clients (id_client) ?>
					<input type="submit" value="Lier le client">
				</form>
			<!--Fin du formulaire d'ajout d'un lien client-collection-->		
			<br/>
			<?php  //Lien client-type
				$id_type=$donnees['id'];
				$sql_nb_type = "SELECT nom_client_commentaire,prenom_client_commentaire,id FROM lien_client WHERE (id_type='$id_type' AND id_collection='0')"; 
				$reponse_nb_type = $bdd->query($sql_nb_type);
				while ($donnees_type_m = $reponse_nb_type->fetch())
				{
					echo $donnees_type_m['prenom_client_commentaire'].' '.$donnees_type_m['nom_client_commentaire'];
					
			?>		
				<!-- Suppression du lien client-collection -->
				<form action="type.php?classement=<?php echo $classement; ?>" method="post">
				<input type="hidden" name="submit_type" value="delete_link">
				<input type="hidden" name="id" value="<?php echo $donnees_type_m['id']; //id du lien client-collection ?>">
				<input type="image" src="img/bouton_supprimer.png" alt="top" value="Supprimer le lien client-type" onclick="return(confirm('Etes-vous sûr de vouloir supprimer ce lien client-type ?'));"/>
			</form>		
			<?php
				echo '<br/>';
				}
				$reponse_nb_type->closeCursor(); // Termine le traitement de la requête			
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