<?php //------------Connexion à la base de donnée------------
	include 'connect.php'; ?>
<?php //------------Ajout du header + menu------------
	$title_top="Collections";
	$date_top="OK"; //Activation de la date
	include 'top.php'; 
    include 'config.php';
?>

<?php //Initialisation de la liste des articles manquants
 $articles_manquants="";
?>

<?php
  if(isset($_POST["submit_collection"]))
  {
	//Nouvelle collection (si nom non vide)
	if($_POST["submit_collection"]=="create"){
		if($_POST["nom"]!="" AND $_POST["id_type"]!="0"){
		
			$id_nouveau=$_POST["id_type"];
			$sql = "SELECT nom FROM type_collection WHERE id='$id_nouveau'";  // Récupération du nom en fonction de l'id
			$reponse = $bdd->query($sql);
			$type_commentaire=$reponse->fetch()[0];
			$reponse->closeCursor();
		
		
			$req = $bdd->prepare('INSERT INTO collection(prix, ref_stockage, nom, id_type, type_commentaire, date_dernier_vendu, date_secondaire) VALUES(:prix, :ref_stockage, :nom, :id_type, :type_commentaire, :date_dernier_vendu, :date_secondaire)');
			if ($req->execute(array(
			    'prix' => $_POST["prix"],
			    'ref_stockage' => $_POST["ref_stockage"],
			    'nom' => $_POST["nom"],
			    'id_type' => $id_nouveau,
			    'type_commentaire' => $type_commentaire,
			    'date_dernier_vendu' => $_POST["date_dernier_vendu"],
			    'date_secondaire' => $_POST["date_secondaire"]
			    )))
            {
			    echo '<center><font color=#286400>La collection '.$_POST["nom"].' a bien été ajoutée !</font></center><br/><br/>';
            }
            else 
            {
                $error = implode(', ', $req->errorInfo());
                echo '<center><font color="red">Erreur lors de l\'ajout de la collection: '.$error.'</font></center><br/><br/>';
            }
		}
		else 
			echo '<center><font color=#B22222>La collection '.$_POST["nom"].' n\'a pas été ajouté car vous n\'avez pas précisé le nom ou le type associé !</font></center><br/><br/>';
	}
	//Editer une collection

	else if($_POST["submit_collection"]=="editer"){
	
		$id_nouveau=$_POST["id_type"];
		$sql = "SELECT nom FROM type_collection WHERE id='$id_nouveau'";  // Récupération du nom en fonction de l'id
		$reponse = $bdd->query($sql);
		$type_commentaire=$reponse->fetch()[0];
		$reponse->closeCursor();	
	
		$req = $bdd->prepare('UPDATE collection SET ref_stockage= :ref_stockage, prix= :prix, nom= :nom, id_type=:id_type,type_commentaire=:type_commentaire,date_dernier_vendu=:date_dernier_vendu,date_secondaire=:date_secondaire WHERE id = :id');
		$req->execute(array(
		'id' => $_POST["id"],
		'prix' => $_POST["prix"],
		'ref_stockage' => $_POST["ref_stockage"],
		'nom' => $_POST["nom"],
		'id_type' => $id_nouveau,
		'type_commentaire' => $type_commentaire,
		'date_dernier_vendu' => $_POST["date_dernier_vendu"],
		'date_secondaire' => $_POST["date_secondaire"]
		));
		
		echo '<center><font color=#286400>La collection '.$_POST["nom"].' a bien été éditée.</font></center><br/>'; 		

	}
	//Supprimer une collection si elle n'est pas associée à des articles/clients.
	else if($_POST["submit_collection"]=="supprimer"){
		if($_POST['id']!=NULL){
				
			//Test pour savoir si la collection est lié à un article :
			$id_collection_test=$_POST['id'];
			$sql2 = "SELECT COUNT(*) FROM article WHERE id_collection='$id_collection_test'"; 
			$reponse2 = $bdd->query($sql2);
			$nombre_collections_liees_avec_type=$reponse2->fetch()[0];
			$reponse2->closeCursor(); // Termine le traitement de la requête
			
			//Test pour savoir si un client est lié à la collection :
			$id_collection_test=$_POST['id'];
			$sql2 = "SELECT COUNT(*) FROM lien_client WHERE id_collection='$id_collection_test'"; 
			$reponse2 = $bdd->query($sql2);
			$nombre_clients_liees_avec_collection=$reponse2->fetch()[0];
			$reponse2->closeCursor(); // Termine le traitement de la requête						
			
			//Test pour savoir si une vente est liée à une collection :
			$id_collection_test=$_POST['id'];
			$sql2 = "SELECT COUNT(*) FROM lien_vente WHERE id_collection='$id_collection_test'"; 
			$reponse2 = $bdd->query($sql2);
			$nombre_ventes_liees_avec_collection=$reponse2->fetch()[0];
			$reponse2->closeCursor(); // Termine le traitement de la requête						
			
			//Test pour savoir si un achat est lié à une collection :
			$id_collection_test=$_POST['id'];
			$sql2 = "SELECT COUNT(*) FROM lien_achat WHERE id_collection='$id_collection_test'"; 
			$reponse2 = $bdd->query($sql2);
			$nombre_achats_liees_avec_collection=$reponse2->fetch()[0];
			$reponse2->closeCursor(); // Termine le traitement de la requête						
			
			
			
			if($nombre_collections_liees_avec_type==0 AND $nombre_clients_liees_avec_collection==0 AND $nombre_ventes_liees_avec_collection==0 AND $nombre_achats_liees_avec_collection==0){ //On peut supprimer la collection
				$reponse_suppr = $bdd->query("DELETE FROM collection WHERE id='$id_collection_test'");
				echo '<center><font color=#286400>La collection '. $_POST["nom"]. ' a été supprimée</font></center><br/>';
				$reponse_suppr->closeCursor(); // Termine le traitement de la requête
			}
			else{ //On ne peut pas supprimer le type car il est lié à des collections
				echo '<center><font color=#B22222>La collection '. $_POST["nom"]. ' n\'a pas été supprimée car '.$nombre_collections_liees_avec_type.' articles, '.$nombre_clients_liees_avec_collection.' clients, '.$nombre_ventes_liees_avec_collection.' articles dans des ventes et '.$nombre_achats_liees_avec_collection.' articles dans des achats sont associés à cette collection !<br/>
				Pour supprimer cette collection, veuillez au préalable supprimer les articles/clients/ventes/achats liés à cette collection !</font></center><br/>';
			}
		}
		else 
			echo '<center><font color=#B22222>La collection '. $_POST["nom"]. ' n\'a pas été supprimé car l\'identifiant est mauvais (NULL)</font></center><br/>';

	}
	else if($_POST["submit_collection"]=="create_link"){
		if(isset($_POST["id_client"]) AND isset($_POST["id_collection"])){
		 if($_POST["id_client"]>0 AND $_POST["id_collection"]>0){
	
			$id_client=$_POST["id_client"];
			$id_collection=$_POST["id_collection"];
			
			
			$sql2 = "SELECT nom, prenom FROM client WHERE id='$id_client'"; 
			$reponse2 = $bdd->query($sql2);
			while ($donnees = $reponse2->fetch())
			{	
				$nom_client_commentaire=$donnees['nom'];
				$prenom_client_commentaire=$donnees['prenom'];
			}
			$reponse2->closeCursor(); // Termine le traitement de la requête
			
			$sql2 = "SELECT nom,id_type,type_commentaire FROM collection WHERE id='$id_collection'"; 
			$reponse2 = $bdd->query($sql2);
			while ($donnees = $reponse2->fetch())
			{
				$collection_commentaire=$donnees['nom'];
				$id_type=$donnees['id_type'];
				$type_commentaire=$donnees['type_commentaire'];
				
			}
			$reponse2->closeCursor(); // Termine le traitement de la requête

			$req = $bdd->prepare('INSERT INTO lien_client(id_client, id_collection, nom_client_commentaire, prenom_client_commentaire, collection_commentaire, id_type, type_commentaire) VALUES(:id_client, :id_collection, :nom_client_commentaire, :prenom_client_commentaire, :collection_commentaire, :id_type, :type_commentaire)');
			$req->execute(array(
			'id_client' => $id_client,
			'id_collection' => $id_collection,
			'nom_client_commentaire' => $nom_client_commentaire,
			'prenom_client_commentaire' => $prenom_client_commentaire,
			'collection_commentaire' => $collection_commentaire,
			'id_type'=> $id_type,
			'type_commentaire' => $type_commentaire
			));

			echo '<center><font color=#286400>Le lien entre le client '.$prenom_client_commentaire.' '.$nom_client_commentaire.' et la collection '.$collection_commentaire.' a bien été ajouté !</font></center><br/><br/>';
		 }
		 else echo '<center><font color=#B22222>Indiquez un nom de client (lien client-collection non crée) ...</font></center><br/><br/>';
		}
		else echo '<center><font color=#B22222>Indiquez un nom de client (lien client-collection non crée) ...</font></center><br/><br/>';
	}
	
	//Supprimer un lien client-"type-collection"
	else if($_POST["submit_collection"]=="delete_link"){
		if($_POST['id']!=NULL){
			$id=$_POST['id'];
			$reponse = $bdd->query("DELETE FROM lien_client WHERE id='$id'");
			echo '<center><font color=#286400>Le lien client-collection a été supprimé</font></center><br/>';
		}
	}
	
  }
?>
<?php //Récupération du type de collection à afficher (si non indiqué -->tous)
  if(isset($_GET["id_type_general"]))
  {
	$id_type_general=$_GET["id_type_general"];
	
  }
  else
	$id_type_general='0';
	
	
	//Récupération de la recherche (si non indiqué --> tous)
	if(isset($_GET["nom_collection_recherche"])){
	//echo $_GET["recherche"]." ".$_GET["nom_collection_recherche"];
		if($_GET["nom_collection_recherche"]!=""){
			$nom_collection_recherche=$_GET["nom_collection_recherche"];
			//echo $nom_collection_recherche;
		}
		else
			$nom_collection_recherche="";
			
	}		
	else
		$nom_collection_recherche="";
?>
<?php //Récupération du classement du tableau (si non indiqué : classement par nom)
  //------------Trier les types selon le choix de l'utilisateur------------
	$sql_classement = 'SELECT * FROM collection'; 
    if($id_type_general!=0)
		$sql_classement.=" WHERE id_type='$id_type_general'";
	if($nom_collection_recherche!="" AND $id_type_general!=0){
		$sql_classement.=" AND (nom LIKE '%$nom_collection_recherche%')";
	}
	else if($nom_collection_recherche!="")
		$sql_classement.="  WHERE nom LIKE '%$nom_collection_recherche%'";
		
  if(isset($_GET["classement"]))
  { 
	$classement=$_GET["classement"];
	switch($classement)
    {
      case "nom_2":
				$sql_classement.=' ORDER BY nom ASC';	
				break;
      case "nom_1" :
				$sql_classement.=' ORDER BY nom DESC';	
				break;
      case "type_associe_2" :
				$sql_classement.=' ORDER BY type_commentaire ASC';	
                break;	
       case "type_associe_1" :
				$sql_classement.=' ORDER BY type_commentaire DESC';	
                break;
      case "prix_2" :
				$sql_classement.=' ORDER BY prix ASC';	
                break;	
       case "prix_1" :
				$sql_classement.=' ORDER BY prix DESC';	
                break;
       case "date_dernier_vendu_2" :
				$sql_classement.=' ORDER BY date_dernier_vendu ASC';	
                break;	
       case "date_dernier_vendu_1" :
				$sql_classement.=' ORDER BY date_dernier_vendu DESC';	
                break;	
       case "date_secondaire_2" :
				$sql_classement.=' ORDER BY date_secondaire ASC';	
                break;	
       case "date_secondaire_1" :
				$sql_classement.=' ORDER BY date_secondaire DESC';	
                break;		
		default :
				$sql_classement.=' ORDER BY nom ASC';	
               break;
    }
  }
  else
  {
	// Par défaut, on trie par nom descendant
	$sql_classement.=' ORDER BY nom ASC';	
	
	$classement="no_classement";
  }
  //Selectionner uniquement les bon types si on le demande :
  if($id_type_general==0 AND $nom_collection_recherche==""){
	$sql_classement="";
	echo '<center><font color=#286400>Veuillez selectionner un type ou rechercher une collection en particulier pour afficher ce que vous voulez.</font></center><br/><br/>';
  }
?>


<!-- Choix du type de collection à afficher -->
<center>
<p><h3>Choix du type de collection :</h3>
    <form method="get" action="collection.php">
		<select name="id_type_general">
		<option value="0">----Tous----</option>
		<?php
			$sql = 'SELECT id,nom FROM type_collection ORDER BY nom ASC'; 
			$reponse = $bdd->query($sql);
			while ($donnees = $reponse->fetch())
			{	
				$id_temp=$donnees['id'];
				$nom_temp=$donnees['nom'];
				echo "<option value=\"".$id_temp."\"";
				if($id_temp==$id_type_general){ //Type selectionné précedement par l'utilisateur
					echo " selected";
					$nom_type_page=$nom_temp;
				}
				echo ">".$nom_temp."</option>\n";
			}
			$reponse->closeCursor(); // Termine le traitement de la requête
		?>
		</select>
	<input type="hidden" name="classement" value="<?php echo $classement; ?>"/><!-- Necessaire pour ne pas perdre les infos-->
	<input type="submit" value="Visioner uniquement les collections de ce type">
    </form>

	<!-- Generer un fichier PDF contenant les articles disponibles -->
	<center>
	<p>			
	<!--RECHERCHE :-->
	
	<center><h3>Rechercher une collection précise :</h3></center>
	<form method="get" action="collection.php">
	<input type="hidden" name="recherche" value="recherhe_ok"/>
	<input type="text" style="width: 200px;" name="nom_collection_recherche" value="<?php echo $nom_collection_recherche; ?>">
	<input type="hidden" name="classement" value="<?php echo $classement; ?>"/><input type="hidden" name="id_type_general" value="<?php echo $id_type_general; ?>"/>
	<input type="submit" value="Afficher cette collection">
	</form>
	<br/><br/>
	<h3>Generer un fichier PDF contenant les articles disponibles :</h3>
		<form method="get" action="generate_pdf_bdd.php">
			<select name="id_type">
			<?php
				$sql = 'SELECT id,nom FROM type_collection ORDER BY nom ASC'; 
				$reponse = $bdd->query($sql);
				while ($donnees = $reponse->fetch())
				{	
					$id_temp=$donnees['id'];
					$nom_temp=$donnees['nom'];
					echo "<option value=\"".$id_temp."\"";
					if($id_temp==$id_type_general){ //Type selectionné précedement par l'utilisateur
						echo " selected";
						$nom_type_page=$nom_temp;
					}
					echo ">".$nom_temp."</option>\n";
				}
				$reponse->closeCursor(); // Termine le traitement de la requête
			?>
			</select>
		<input type="submit" value="Generer le fichier pdf">
		</form>
</p>
</center>

<!------------Tableau des collections------------>
<TABLE BORDER="1"> 
  <CAPTION><h1>Liste des collections :<h1/></CAPTION> 
  <TR> 
	 <TH> Collection<br/>
	 	<form method="get" action="collection.php"><input type="hidden" name="nom_collection_recherche" value="<?php echo $nom_collection_recherche; ?>"><input type="hidden" name="classement" value="nom_1"/><input type="hidden" name="id_type_general" value="<?php echo $id_type_general; ?>"/><input type="image" src="img/arrow_top.png" alt="top" /></FORM>
		<form method="get" action="collection.php"><input type="hidden" name="nom_collection_recherche" value="<?php echo $nom_collection_recherche; ?>"><input type="hidden" name="classement" value="nom_2"/><input type="hidden" name="id_type_general" value="<?php echo $id_type_general; ?>"/><input type="image" src="img/arrow_bottom.png" alt="bottom" /></FORM>	 
	 </TH> 
	 <TH> Type associée<br/>
	 	<form method="get" action="collection.php"><input type="hidden" name="nom_collection_recherche" value="<?php echo $nom_collection_recherche; ?>"><input type="hidden" name="classement" value="type_associe_1"/><input type="hidden" name="id_type_general" value="<?php echo $id_type_general; ?>"/><input type="image" src="img/arrow_top.png" alt="top" /></FORM>
		<form method="get" action="collection.php"><input type="hidden" name="nom_collection_recherche" value="<?php echo $nom_collection_recherche; ?>"><input type="hidden" name="classement" value="type_associe_2"/><input type="hidden" name="id_type_general" value="<?php echo $id_type_general; ?>"/><input type="image" src="img/arrow_bottom.png" alt="bottom" /></FORM>	 
	 </TH> 
	 <TH><p style="width: 250px;"> Articles associés<br/>(+quantité)</p></TH> 
	 <TH> Prix approximatif<br/>par article<br/>
	 	<form method="get" action="collection.php"><input type="hidden" name="nom_collection_recherche" value="<?php echo $nom_collection_recherche; ?>"><input type="hidden" name="classement" value="prix_1"/><input type="hidden" name="id_type_general" value="<?php echo $id_type_general; ?>"/><input type="image" src="img/arrow_top.png" alt="top" /></FORM>
		<form method="get" action="collection.php"><input type="hidden" name="nom_collection_recherche" value="<?php echo $nom_collection_recherche; ?>"><input type="hidden" name="classement" value="prix_2"/><input type="hidden" name="id_type_general" value="<?php echo $id_type_general; ?>"/><input type="image" src="img/arrow_bottom.png" alt="bottom" /></FORM>	 
	 </TH> 
	 <TH>Valeur<br/>de la<br/>collection</TH> 
	 <TH>Référence<br/>de<br/>stockage<br/></TH> 
	 <TH>Date du<br/>dernier<br/>article<br/>vendu<br/>
	 	<form method="get" action="collection.php"><input type="hidden" name="nom_collection_recherche" value="<?php echo $nom_collection_recherche; ?>"><input type="hidden" name="classement" value="date_dernier_vendu_1"/><input type="hidden" name="id_type_general" value="<?php echo $id_type_general; ?>"/><input type="image" src="img/arrow_top.png" alt="top" /></FORM>
		<form method="get" action="collection.php"><input type="hidden" name="nom_collection_recherche" value="<?php echo $nom_collection_recherche; ?>"><input type="hidden" name="classement" value="date_dernier_vendu_2"/><input type="hidden" name="id_type_general" value="<?php echo $id_type_general; ?>"/><input type="image" src="img/arrow_bottom.png" alt="bottom" /></FORM>	 	 
	 </TH>
	 <TH> Date<br/>secondaire<br/>
	 	<form method="get" action="collection.php"><input type="hidden" name="nom_collection_recherche" value="<?php echo $nom_collection_recherche; ?>"><input type="hidden" name="classement" value="date_secondaire_1"/><input type="hidden" name="id_type_general" value="<?php echo $id_type_general; ?>"/><input type="image" src="img/arrow_top.png" alt="top" /></FORM>
		<form method="get" action="collection.php"><input type="hidden" name="nom_collection_recherche" value="<?php echo $nom_collection_recherche; ?>"><input type="hidden" name="classement" value="date_secondaire_2"/><input type="hidden" name="id_type_general" value="<?php echo $id_type_general; ?>"/><input type="image" src="img/arrow_bottom.png" alt="bottom" /></FORM>	 	 	 
	 </TH>
	 <TH> PAMP<br/>automatique (prix d'achat max pondéré)</TH>
	 <TH> Description Le Bon Coin</TH>
	 <TH> Editer</TH>
 	 <TH> Clients associés</TH>
</TR> 

<!------------Formulaire d'ajout d'une collection------------>
<TR> 
	<form action="collection.php?classement=<?php echo $classement; ?>&id_type_general=<?php echo $id_type_general; ?>&nom_collection_recherche=<?php echo $nom_collection_recherche; ?>" method="post">
		<input type="hidden" name="submit_collection" value="create">
		<TD><input type="text" style="width: 200px;" name="nom"></TD> <!--nom-->
		<TD>
			<select name="id_type">
				<option value="0">---Choisir un type---</option>
				<?php
					$sql = 'SELECT id,nom FROM type_collection ORDER BY nom ASC'; 
					$reponse = $bdd->query($sql);
					while ($donnees = $reponse->fetch())
					{	
						$id_temp=$donnees['id'];
						$nom_temp=$donnees['nom'];
						echo "<option value=\"".$id_temp."\"";
						if($id_temp==$id_type_general) //Type selectionné précedement par l'utilisateur
							echo " selected";
						echo ">".$nom_temp."</option>\n";
					}
					$reponse->closeCursor(); // Termine le traitement de la requête
				?>
			</select>
		</TD><!--id_type et type_commentaire-->
		<TD>Les articles associés (+quantité)<br/>s'affichent directement une fois<br/>les articles crées et liés à la<br/>collection.</TD>
		<TD><input type="text" style="width: 50px;" name="prix">€</TD> <!--prix-->
		<TD>affichage<br/>automatique<br/>de la valeur<br/>de la collection</TD> <!--valeur de la collection-->
		<TD><input type="text" style="width: 110px;" name="ref_stockage"></TD> <!--ref_stockage-->
		<TD><input type="text" style="width: 75px;" name="date_dernier_vendu" class="datepicker"></TD>
		<TD><input type="text" style="width: 75px;" name="date_secondaire" class="datepicker"></TD>
		<!--<TD><input type="text" style="width: 60px;" name="PAMP"></TD>-->
		<TD>Généré<br/>automatiquement<br/>à partir des achats</TD>
		<TD>Vous trouverez ici un affichage automatique des annonces du bon coin</TD>
		<TD><input type="submit" value="Ajouter une collection"></TD> 	
   </form>
 		<TD>Pour ajouter un lien client-collection, il faut en premier lieu créer la collection !</a></TD>
</TR>	
 
<?php  //"LISTE DES CLIENTS ($liste_clients utilisé par la suite)
		   $liste_clients="<!--Debut de la liste clients-->\n<select name=\"id_client\">\n<option value=\"0\">--liste des clients--</option>\n"; 
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
<!------------Affichage des  collections déjà existantes------------>
<?php
	$reponse_classement = $bdd->query($sql_classement);
   if($id_type_general!=0 OR $nom_collection_recherche!=""){ //Affichage uniquement si un type ou une recherche à été selectionnée/faite
	
	while ($donnees_classement = $reponse_classement->fetch())
	{	
?> 
<TR> 
	<form action="collection.php?classement=<?php echo $classement; ?>&id_type_general=<?php echo $id_type_general; ?>&nom_collection_recherche=<?php echo $nom_collection_recherche; ?>" method="post">
		<input type="hidden" name="submit_collection" value="editer">
		<input type="hidden" name="id" value="<?php echo $donnees_classement['id']; ?>">
		<TD><input type="text" style="width: 200px;" name="nom" value="<?php echo $donnees_classement['nom']; ?>"></TD> <!--nom-->
		<TD>
			<select name="id_type">
				<?php
					$sql = 'SELECT id,nom FROM type_collection ORDER BY nom ASC'; 
					$reponse = $bdd->query($sql);
					while ($donnees = $reponse->fetch())
					{	
						$id_temp=$donnees['id'];
						$nom_temp=$donnees['nom'];
						echo "<option value=\"".$id_temp."\"";
						if($id_temp==$donnees_classement['id_type']) //Type selectionné précedement par l'utilisateur
							echo " selected";
						echo ">".$nom_temp."</option>\n";
					}
					$reponse->closeCursor(); // Termine le traitement de la requête
				?>
			</select>
		</TD><!--id_type et type_commentaire-->
		<?php 
			$id_coll=$donnees_classement['id'];
			$sql_nb_coll = "SELECT MIN(stock) FROM article WHERE id_collection='$id_coll'"; 
			$reponse_nb_coll = $bdd->query($sql_nb_coll);
			$min_stock=$reponse_nb_coll->fetch()[0];
			//echo $min_stock;
			$reponse_nb_coll->closeCursor(); // Termine le traitement de la requête
		?>
		<TD>
		<?php 
			$id_coll=$donnees_classement['id'];
			$sql_nb_coll = "SELECT nom,stock,numero_article FROM article WHERE id_collection='$id_coll' ORDER BY numero_article+0"; //Le +0 permet de trier des VARCHAR sous forme de INT !!	
			$reponse_nb_coll = $bdd->query($sql_nb_coll);

			while ($donnees_article = $reponse_nb_coll->fetch())
			{
				echo $donnees_article["numero_article"].". ".$donnees_article['nom'].' ('.$donnees_article['stock'].'),<br/>';
			}
			$reponse_nb_coll->closeCursor(); // Termine le traitement de la requête
		?>		
		</TD>

		<?php 
			$id_coll=$donnees_classement['id'];
			$sql_nb_coll = "SELECT nom,stock,numero_article FROM article WHERE (id_collection='$id_coll' AND stock<($min_stock+1)) ORDER BY numero_article+0"; 
			$reponse_nb_coll = $bdd->query($sql_nb_coll);
			$articles_manquants.="<br/><h2>Collection ".$donnees_classement['nom']." (".$min_stock." collections)</h2>";
			while ($donnees_article_m = $reponse_nb_coll->fetch())
			{
				//echo $donnees_article_m['nom'].' ('.$donnees_article_m['stock'].'),<br/>';
				$articles_manquants.=$donnees_article_m["numero_article"].'. '.$donnees_article_m['nom'].' ('.$donnees_article_m['stock'].' en stock)<br/>';
			}
			$reponse_nb_coll->closeCursor(); // Termine le traitement de la requête
		?>				
		<TD><input type="text" style="width: 50px;" name="prix" value="<?php echo $donnees_classement['prix']; ?>">€</TD> <!--prix-->
		<TD>
		<?php
		$id_coll=$donnees_classement['id'];
		//Récupération du prix de chaque article
		$sql_valeur_articles = "SELECT prix FROM collection WHERE id='$id_coll'"; 
		$reponse_valeur_articles = $bdd->query($sql_valeur_articles);
		$prix_par_article=$reponse_valeur_articles->fetch()[0];
		
		//echo $prix_par_article;
		//Récupération du nombre d'articles de la collection
		$sql_nombre_d_articles = "SELECT SUM(stock) FROM article WHERE id_collection='$id_coll'"; 
		$reponse_nombre_d_articles = $bdd->query($sql_nombre_d_articles);
		$nombre_d_articles=$reponse_nombre_d_articles->fetch()[0];		
		
		//echo $nombre_d_articles;
		//Affichage de la valeur de la collection
		echo $prix_par_article*$nombre_d_articles;
		
		?>€
		</TD> <!--valeur de la collection-->
		<TD><input type="text" style="width: 110px;" name="ref_stockage" value="<?php echo $donnees_classement['ref_stockage']; ?>"></TD> <!--ref_stockage-->
		<TD><input type="text" name="date_dernier_vendu" style="width: 75px;" class="datepicker" value="<?php echo $donnees_classement['date_dernier_vendu']; ?>"></TD>
		<TD><input type="text" name="date_secondaire" style="width: 75px;" class="datepicker" value="<?php echo $donnees_classement['date_secondaire']; ?>"></TD>
		<TD>
		<?php
		//Affichage du pamp automatique 
		//Aucun affichage si aucun achat (si quantité_totale>0)
		//Affichage du pamp si au moins un achat (Somme(quantité_locale*pamp_local)/quantité_totale)
			$id_coll=$donnees_classement['id'];
			$sql_nb_coll = "SELECT SUM(quantite*prix_unitaire)/SUM(quantite) AS pamp_auto FROM lien_achat WHERE id_collection='$id_coll'"; //Le +0 permet de trier des VARCHAR sous forme de INT !!	
			$reponse_nb_coll = $bdd->query($sql_nb_coll);
			$pamp_auto=$reponse_nb_coll->fetch()[0];
			if($pamp_auto>0)
				echo number_format($pamp_auto,2)." €"; //affichage du pamp avec uniquement deux décimales
			else
				echo "Aucun achat (>0€) dans cette collection";
			$reponse_nb_coll->closeCursor(); // Termine le traitement de la requête		
		
		?>
		</TD>
		<TD><!-- Annonce le bon coin-->
		<textarea type="text" style="width: 150px; height: 80px;"><?php
		 $id_coll=$donnees_classement['id'];
		 $id_type_collection_boncoin=$donnees_classement['id_type'];
		 $nom_collection_bon_coin=$donnees_classement['nom'];
		 $type_bon_coin=$donnees_classement['type_commentaire'];
		 //Récupération des éléments pour générer l'annonce :
		 $sql_nb_articles_dispo="SELECT COUNT(stock) FROM article WHERE (id_collection='$id_coll' AND stock>0)"; //article
		 $reponse_nb_articles_dispo = $bdd->query($sql_nb_articles_dispo);
		 $nb_articles_dispo=$reponse_nb_articles_dispo->fetch()[0];	
		 //echo $nb_articles_dispo."\n";
		 if($nb_articles_dispo>0){
			 $sql_titre_article_dispo="SELECT nom,numero_article FROM article WHERE (id_collection='$id_coll' AND stock>0) ORDER BY numero_article+0"; //article
			 $reponse_titre_article_dispo= $bdd->query($sql_titre_article_dispo);
			 $titres_article_dispo="";
			 while($donnees_titres_articles=$reponse_titre_article_dispo->fetch()){	
				$titres_article_dispo.="- N°".$donnees_titres_articles["numero_article"]." : ".$donnees_titres_articles["nom"]."\n";
			}
			 //echo $titres_article_dispo."\n";

			 $sql_ref_bon_coin="SELECT ref_bon_coin FROM type_collection WHERE id='$id_type_collection_boncoin'"; //type
			 $reponse_ref_bon_coin = $bdd->query($sql_ref_bon_coin);
			 $ref_bon_coin=$reponse_ref_bon_coin->fetch()[0];	
			 //echo $ref_bon_coin."\n";

			 $sql_nom_type_bon_coin="SELECT nom FROM type_collection WHERE id='$id_type_collection_boncoin'"; //type
			 $reponse_nom_type_bon_coin = $bdd->query($sql_nom_type_bon_coin);
			 $nom_type_bon_coin=$reponse_nom_type_bon_coin->fetch()[0];	
			 //echo $nom_type_bon_coin."\n";
			 
			 $prix_par_article=$donnees_classement['prix']; //collection
			 //echo $prix_par_article."\n";
			 
			 $nom_collection_boncoin=mb_strtoupper($donnees_classement['nom'], 'UTF-8');
			 //(Le nom de la collection doit etre en majuscule)
			 //echo $nom_collection_boncoin."\n";
			 
			 //Affichage de l'annonce :
			 $annonce_le_bon_coin="Vends ".$type_bon_coin." ".$nom_collection_bon_coin." ;\n\n".$nb_articles_dispo." titre";
			 if($nb_articles_dispo>1)
				$annonce_le_bon_coin.="s";
			 $annonce_le_bon_coin.=" disponible";
			 if($nb_articles_dispo>1)
				$annonce_le_bon_coin.="s";
			 $annonce_le_bon_coin.=" :\n".$titres_article_dispo."\n\nPrix : ".$prix_par_article." euros l'unité.\n\n".$additional_description_leboncoin."\n\nVoir mes autres ".$nom_type_bon_coin." en tapant ".$ref_bon_coin." dans le moteur de recherche du site";		 
			 echo $annonce_le_bon_coin;
		 }
		?></textarea></TD>
		<TD><input type="submit" value="Editer la collection">
   </form>
   		<br/>   
<!-----Formulaire de suppression d'une collection------------->
	<form action="collection.php?classement=<?php echo $classement; ?>&id_type_general=<?php echo $id_type_general; ?>&nom_collection_recherche=<?php echo $nom_collection_recherche; ?>" method="post">
		   <input type="hidden" name="submit_collection" value="supprimer">
		   <input type="hidden" name="id" value="<?php echo $donnees_classement['id']; ?>">
		   <input type="hidden" name="nom" value="<?php echo $donnees_classement['nom']; ?>">
		   <input type="submit" value="Supprimer la collection" onclick="return(confirm('Etes-vous sûr de vouloir supprimer la collection <?php echo $donnees_classement['nom']; ?> ?'));">
	</form>
		<TD>
		<!--Ajout du formulaire d'ajout d'un lien client-collection-->
			<form action="collection.php?classement=<?php echo $classement; ?>&id_type_general=<?php echo $id_type_general; ?>&nom_collection_recherche=<?php echo $nom_collection_recherche; ?>" method="post">
				<input type="hidden" name="submit_collection" value="create_link"> <!--type d'edition : ajout lien (submit_collection=ajout_lien_client_collection)-->
				<input type="hidden" name="id_collection" value="<?php echo $donnees_classement['id'];  //id de la collection (id)	 ?>">	
				<?php echo $liste_clients; //liste des clients (id_client) ?>
				<input type="submit" value="Lier le client">
			</form>
			
		<!--Fin du formulaire d'ajout d'un lien client-collection-->
		<br/>
		<?php  //Lien client-collection
			$id_coll=$donnees_classement['id'];
			$sql_nb_coll = "SELECT nom_client_commentaire,prenom_client_commentaire,id FROM lien_client WHERE id_collection='$id_coll'"; 
			$reponse_nb_coll = $bdd->query($sql_nb_coll);
			while ($donnees_article_m = $reponse_nb_coll->fetch())
			{
				echo $donnees_article_m['prenom_client_commentaire'].' '.$donnees_article_m['nom_client_commentaire'];
				
		?>		
			<!-- Suppression du lien client-collection -->
			<form action="collection.php?classement=<?php echo $classement; ?>&id_type_general=<?php echo $id_type_general; ?>&nom_collection_recherche=<?php echo $nom_collection_recherche; ?>" method="post">
			<input type="hidden" name="submit_collection" value="delete_link">
			<input type="hidden" name="id" value="<?php echo $donnees_article_m['id']; //id du lien client-collection ?>">
			<input type="image" src="img/bouton_supprimer.png" alt="top" value="Supprimer le lien client-collection" onclick="return(confirm('Etes-vous sûr de vouloir supprimer ce lien client-collection ?'));"/>
		</form>
		<?php
			echo '<br/>';
			}
			$reponse_nb_coll->closeCursor(); // Termine le traitement de la requête			
		?>
		</TD>
	</TD> 	
</TR>
 <?php
	}
	$reponse_classement->closeCursor(); // Termine le traitement de la requête
   }
?>
</TABLE> 


<?php 
	if($articles_manquants!=""){
?>
		<p>
		<h1>Liste des articles à acheter pour completer <?php if (isset($nom_type_page)) echo 'les collections "'.$nom_type_page.'"'; else echo 'toutes les collections';?> :</h1>
<?php
		echo $articles_manquants."</p>";
	}
?>

<?php //------------Ajout des balises de fin------------
	include 'bottom.php';
?>