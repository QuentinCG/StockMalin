<?php //------------Connexion à la base de donnée------------
	include 'connect.php'; ?>
<?php //------------Ajout du header + menu------------
	$date_top="OK"; //Activation de la date
	$title_top="Ventes";
	include 'top.php'; 
?>
<?php //------------Traitement des demandes de l'utilisateur (ajout/edition/suppresion d'ventes)------------
  if(isset($_POST["submit_vente"]))
  {
	//Nouvelle vente
	if($_POST["submit_vente"]=="create"){
		if($_POST["id_client"]!=0 AND $_POST["montant_HFP"]!="" AND $_POST["etat_vente"]!="6. Vente annulee"){
		
			//Ajout du nom du client :
			$id_client_create=$_POST["id_client"];
			$sql_create = "SELECT nom FROM client WHERE id='$id_client_create'"; 
			$reponse_create = $bdd->query($sql_create);
			$client_nom_create=$reponse_create->fetch()[0];
			$reponse_create->closeCursor(); // Termine le traitement de la requête
			
			//Ajout du prenom du client :
			$id_client_create=$_POST["id_client"];
			$sql_create = "SELECT prenom FROM client WHERE id='$id_client_create'"; 
			$reponse_create = $bdd->query($sql_create);
			$client_prenom_create=$reponse_create->fetch()[0];
			$reponse_create->closeCursor(); // Termine le traitement de la requête
						
			//Ajout de la collection majoritaire :
			$id_collection_create=$_POST["id_collection_majoritaire"];
			$sql_create = "SELECT nom FROM collection WHERE id='$id_collection_create'"; 
			$reponse_create = $bdd->query($sql_create);
			$collection_create=$reponse_create->fetch()[0];
			$reponse_create->closeCursor(); // Termine le traitement de la requête

			$req = $bdd->prepare('INSERT INTO vente(montant_HFP, montant_FP, mode_envoi, id_client, nom_client_commentaire, prenom_client_commentaire, etat_vente, date_fin_vente,id_collection_majoritaire, collection_majoritaire) VALUES(:montant_HFP, :montant_FP, :mode_envoi, :id_client, :nom_client_commentaire, :prenom_client_commentaire, :etat_vente, :date_fin_vente, :id_collection_majoritaire, :collection_majoritaire)');
			$req->execute(array(
			'montant_HFP' => $_POST["montant_HFP"],
			'montant_FP' => $_POST["montant_FP"],
			'mode_envoi' => $_POST["mode_envoi"],
			'id_client' => $_POST["id_client"],
			'nom_client_commentaire' => $client_nom_create,
			'prenom_client_commentaire' => $client_prenom_create,
			'etat_vente' => $_POST["etat_vente"],
			'date_fin_vente' => $_POST["date_fin_vente"],
			'id_collection_majoritaire' => $_POST["id_collection_majoritaire"],
			'collection_majoritaire' => $collection_create
			));

			//Editer total_achats du client (ajouter montant_HFP au client) :
			$add_to_client=$_POST["montant_HFP"];
			$reponse_edit_client = $bdd->query("UPDATE client SET total_achats=total_achats+".$add_to_client." WHERE id='".$id_client_create."'");
			
			echo '<center><font color=#286400>La vente avec le client '.$client_prenom_create.' '.$client_nom_create.' a bien été ajoutée !<br/>Vous pouvez à présent ajouter les articles à cette vente.</font></center><br/><br/>';
	
			//On lance automatiquement la page d'ajout d'articles :
			$sql_lancement="SELECT id FROM vente ORDER BY id DESC LIMIT 0, 1";
			$reponse_lancement = $bdd->query($sql_lancement);
			$id_vente_lancement=$reponse_lancement->fetch()[0];
			$reponse_create->closeCursor(); // Termine le traitement de la requête			
	?>
			<form name="autosend1" id="autosend1" method="post" action="vente2.php">
			<input type="hidden" name="id_vente" value="<?php echo $id_vente_lancement; ?>">
			<input type="hidden" name="type_de_requete" value="creation">
			</form>
			 <!-- Enable autosend POST-->
			 <script LANGUAGE="javascript">
			 document.autosend1.submit();
			 </script>
	<?php
		
		}
		else 
			echo '<center><font color=#B22222>La vente avec le client n\'a pas été ajouté car vous n\'avez précisé un des paramètres suivants :<br/>le montant_HFP, le nom du client ou que vous avez creer une vente annulée !</font></center><br/><br/>';
	}
	//Editer les articles de l'vente
	else if($_POST["submit_vente"]=="editer_articles" AND isset($_POST["id_vente"])){
		//On lance la page d'ajout d'articles :
	?>
		<form name="autosend2" id="autosend2" method="post" action="vente2.php">
		<input type="hidden" name="id_vente" value="<?php echo $_POST["id_vente"]; ?>">
		<input type="hidden" name="type_de_requete" value="edition">
		</form>
		<!-- Enable autosend POST-->
		<script LANGUAGE="javascript">
		document.autosend2.submit();
		</script>		
	<?php
	}
		//Editer la vente (informations de base)
	else if($_POST["submit_vente"]=="editer" AND isset($_POST["id_vente"])){
		if($_POST["id_client"]!=0){
			
			//Récupération de l'état passé de la vente :
			$id_vente_test=$_POST["id_vente"];
			$sql_lancement="SELECT etat_vente FROM vente WHERE id='$id_vente_test'";
			$reponse_lancement = $bdd->query($sql_lancement);
			$etat_vente_lancement=$reponse_lancement->fetch()[0];
			$reponse_lancement->closeCursor(); // Termine le traitement de la requête					
			
			 //Interdiction de passer du statut "annulé" aux autres statuts ! 
			if(!($_POST["etat_vente"]!="6. Vente annulee" AND $etat_vente_lancement=="6. Vente annulee"))
			{
				//Ajout du nom du client dans "client" grace à id_client :
				$id_client_create=$_POST["id_client"];
				$sql_create = "SELECT nom FROM client WHERE id='$id_client_create'"; 
				$reponse_create = $bdd->query($sql_create);
				$client_nom_edit=$reponse_create->fetch()[0];
				$reponse_create->closeCursor(); // Termine le traitement de la requête

				//Ajout du nom du client dans "client" grace à id_client :
				$id_client_create=$_POST["id_client"];
				$sql_create = "SELECT prenom FROM client WHERE id='$id_client_create'"; 
				$reponse_create = $bdd->query($sql_create);
				$client_prenom_edit=$reponse_create->fetch()[0];
				$reponse_create->closeCursor(); // Termine le traitement de la requête
						
				//Ajout de la collection majoritaire :
				$id_collection_create=$_POST["id_collection_majoritaire"];
				$sql_create = "SELECT nom FROM collection WHERE id='$id_collection_create'"; 
				$reponse_create = $bdd->query($sql_create);
				$collection_edit=$reponse_create->fetch()[0];
				$reponse_create->closeCursor(); // Termine le traitement de la requête
				
				//Vérifier si le montant hors frais de port a été modifié (si il l'est, on édite total_achats du client par la différence).
				$id_vente=$_POST["id_vente"];
				$sql_create = "SELECT montant_HFP FROM vente WHERE id='$id_vente'"; 
				$reponse_create = $bdd->query($sql_create);
				$ancien_montant_HFP=$reponse_create->fetch()[0];
				$reponse_create->closeCursor();
				if($ancien_montant_HFP!=$_POST["montant_HFP"] AND $_POST["etat_vente"]!="6. Vente annulee")
				{
					$id_client_edit=$_POST["id_client"];
					$montant_a_ajouter=$_POST["montant_HFP"]-$ancien_montant_HFP;
					$reponse_edit_client = $bdd->query("UPDATE client SET total_achats=total_achats+$montant_a_ajouter WHERE id='$id_client_edit'");
					$reponse_edit_client->closeCursor(); // Termine le traitement de la requête			
					
				}
				
				//Si on est passé de (1->5) à 6 pour l'etat de vente, alors on edite total_achats du client (diminution) et on edite le stock des articles en conséquence !
				$id_vente=$_POST["id_vente"];
				$sql_create = "SELECT etat_vente FROM vente WHERE id='$id_vente'"; 
				$reponse_create = $bdd->query($sql_create);
				$ancien_etat_vente=$reponse_create->fetch()[0];
				$reponse_create->closeCursor();			
				if($ancien_etat_vente!="6. Vente annulee" AND $_POST["etat_vente"]=="6. Vente annulee"){
					//Edition du total_achats du client :
					$id_client_edit=$_POST["id_client"];
					$montant_a_enlever=$_POST["montant_HFP"];
					$reponse_edit_client = $bdd->query("UPDATE client SET total_achats=total_achats-$montant_a_enlever WHERE id='$id_client_edit'");
					$reponse_edit_client->closeCursor(); // Termine le traitement de la requête		
					
					//Edition des stocks totaux (incrementer) :
					$id_vente_suppr=$_POST["id_vente"];
					$sql_test = "SELECT quantite,id_article FROM lien_vente WHERE id_vente='$id_vente_suppr';"; 
					$reponse_incrementation = $bdd->query($sql_test);
					while ($donnees_quantite_achat = $reponse_incrementation->fetch())
					{
						$quantite=$donnees_quantite_achat['quantite'];
						$id_article_incr=$donnees_quantite_achat['id_article'];
						
						$reponse_incr2 = $bdd->query('UPDATE article SET stock=stock+'.$quantite.' WHERE id ='.$id_article_incr.';');	
						$reponse_incr2->closeCursor(); // Termine le traitement de la requête		
						}		
					$reponse_incrementation->closeCursor(); // Termine le traitement de la requête			
						
					echo '<center><font color=#286400>La vente est passé en mode annulée !<br/>Les articles ont bien été incrémentés (si la vente n\'était pas annulée).</font></center><br/><br/>';

					
				}
				
				//Edition de la vente :
				$req = $bdd->prepare('UPDATE vente SET montant_HFP= :montant_HFP, montant_FP =:montant_FP, mode_envoi =:mode_envoi, id_client = :id_client, nom_client_commentaire = :nom_client_commentaire, prenom_client_commentaire = :prenom_client_commentaire, etat_vente = :etat_vente, date_fin_vente = :date_fin_vente, id_collection_majoritaire = :id_collection_majoritaire, collection_majoritaire = :collection_majoritaire WHERE id = :id');
				$req->execute(array(
				'id' => $_POST["id_vente"],
				'montant_HFP' => $_POST["montant_HFP"],
				'montant_FP' => $_POST["montant_FP"],
				'mode_envoi' => $_POST["mode_envoi"],
				'id_client' => $_POST["id_client"],
				'nom_client_commentaire' => $client_nom_edit,
				'prenom_client_commentaire' => $client_prenom_edit,
				'etat_vente' => $_POST["etat_vente"],
				'date_fin_vente' => $_POST["date_fin_vente"],
				'id_collection_majoritaire' => $_POST["id_collection_majoritaire"],
				'collection_majoritaire' => $collection_edit
				));
				
				echo '<center><font color=#286400>La vente ('.$client_prenom_edit.' '.$client_nom_edit.' le '.$_POST["date_fin_vente"].') a bien été édité.</font></center><br/>'; 		


			}
			else
				echo '<center><font color=#B22222>La vente avec le client n\'a pas été édité car vous n\'avez pas le droit de passer du statut annulé à un autre statut (cause : risque d\'erreurs dans le programme) !</font></center><br/><br/>';
					
		}
		else
			echo '<center><font color=#B22222>La vente avec le client n\'a pas été édité car vous n\'avez précisé le nom du client !</font></center><br/><br/>';
		
	}
	//Supprimer la vente
	else if($_POST["submit_vente"]=="supprimer" AND isset($_POST["id_vente"])){
		$id_vente_suppr=$_POST["id_vente"];

		//Récupération de l'état de la vente  :
		$sql_create = "SELECT etat_vente FROM vente WHERE id='$id_vente_suppr'"; 
		$reponse_create = $bdd->query($sql_create);
		$etat_vente_suppr=$reponse_create->fetch()[0];
		$reponse_create->closeCursor();			

		//Récupération du prix de vente hfp  :
		$sql_create = "SELECT montant_HFP FROM vente WHERE id='$id_vente_suppr'"; 
		$reponse_create = $bdd->query($sql_create);
		$montant_HFP_suppr=$reponse_create->fetch()[0];
		$reponse_create->closeCursor();			

		//Récupération du client associé  :
		$sql_create = "SELECT id_client FROM vente WHERE id='$id_vente_suppr'"; 
		$reponse_create = $bdd->query($sql_create);
		$id_client_suppr=$reponse_create->fetch()[0];
		$reponse_create->closeCursor();		
		
		//Si vente non annulée alors on édite les stocks total_achats :
		if($etat_vente_suppr!="6. Vente annulee"){
		
			//Edition du total_achats du client :
			$reponse_edit_client = $bdd->query("UPDATE client SET total_achats=total_achats-$montant_HFP_suppr WHERE id='$id_client_suppr'");
			$reponse_edit_client->closeCursor(); // Termine le traitement de la requête		
				
			//Edition des stocks totaux (incrementer) :
			$id_vente_suppr=$_POST["id_vente"];
			$sql_test = "SELECT quantite,id_article FROM lien_vente WHERE id_vente='$id_vente_suppr';"; 
			$reponse_incrementation = $bdd->query($sql_test);
			while ($donnees_quantite_achat = $reponse_incrementation->fetch())
			{
				$quantite=$donnees_quantite_achat['quantite'];
				$id_article_incr=$donnees_quantite_achat['id_article'];
				
				$reponse_incr2 = $bdd->query('UPDATE article SET stock=stock+'.$quantite.' WHERE id ='.$id_article_incr.';');	
				$reponse_incr2->closeCursor(); // Termine le traitement de la requête		
			}		
			$reponse_incrementation->closeCursor(); // Termine le traitement de la requête			
		}

		//Supprimer les liens ventes contenant l'id de la vente :
		$reponse_suppr_liens_achats = $bdd->query("DELETE FROM lien_vente WHERE id_vente='$id_vente_suppr'");
		//Supprimer la vente :
		$reponse_suppr_achat = $bdd->query("DELETE FROM vente WHERE id='$id_vente_suppr'");			
		
		echo '<center><font color=#286400>La vente a été completement supprimée !<br/>Les articles ont bien été incrémentés.</font></center><br/><br/>';
	}
}
?>
<!------------------------------------------------Tableau des ventes (à creer et anciens)---------------------------->
<?php
  //------------Trier les ventes selon le choix de l'utilisateur------------
  if(isset($_GET["classement"]))
  {
	$classement=$_GET["classement"];
	switch($classement)
    {	
      case "client_2":
				$sql = 'SELECT * FROM vente ORDER BY prenom_client_commentaire ASC'; 
                break;
      case "client_1" :
				$sql = 'SELECT * FROM vente ORDER BY prenom_client_commentaire DESC'; 
                break;				
      case "montant_HFP_2":
				$sql = 'SELECT * FROM vente ORDER BY montant_HFP ASC'; 
                break;
      case "montant_HFP_1" :
				$sql = 'SELECT * FROM vente ORDER BY montant_HFP DESC'; 
                break;				
      case "montant_FP_2":
				$sql = 'SELECT * FROM vente ORDER BY montant_FP ASC'; 
                break;
      case "montant_FP_1" :
				$sql = 'SELECT * FROM vente ORDER BY montant_FP DESC'; 
                break;				
      case "etat_vente_2":
				$sql = 'SELECT * FROM vente ORDER BY etat_vente ASC'; 
                break;
      case "etat_vente_1" :
				$sql = 'SELECT * FROM vente ORDER BY etat_vente DESC'; 
                break;				
      case "date_fin_vente_2":
				$sql = 'SELECT * FROM vente ORDER BY date_fin_vente ASC'; 
                break;
      case "date_fin_vente_1" :
				$sql = 'SELECT * FROM vente ORDER BY date_fin_vente DESC'; 
                break;				
       case "mode_envoi_2":
				$sql = 'SELECT * FROM vente ORDER BY mode_envoi ASC'; 
                break;
      case "mode_envoi_1" :
				$sql = 'SELECT * FROM vente ORDER BY mode_envoi DESC'; 
                break;				
       case "collection_majoritaire_2":
				$sql = 'SELECT * FROM vente ORDER BY collection_majoritaire ASC'; 
                break;
      case "collection_majoritaire_1" :
				$sql = 'SELECT * FROM vente ORDER BY collection_majoritaire DESC'; 
                break;				
      default :
				$sql = 'SELECT * FROM vente ORDER BY etat_vente ASC, date_fin_vente DESC'; 
                break;
    }
  }
  else
  {
	// Par défaut, on trie par nom descendant
	$sql = "SELECT * FROM vente ORDER BY etat_vente ASC, date_fin_vente DESC"; 
	$classement="no_classement";
  }
?>


<!------------Tableau des ventes------------>
<TABLE BORDER="1"> 
  <CAPTION><h1>Liste des ventes :<h1/></CAPTION> 
  <TR> 
	 <TH> Client<br/>
	 	<form method="get" action="vente.php"><input type="hidden" name="classement" value="client_1"/><input type="image" src="img/arrow_top.png" alt="top" /></FORM>
		<form method="get" action="vente.php"><input type="hidden" name="classement" value="client_2"/><input type="image" src="img/arrow_bottom.png" alt="bottom" /></FORM>
	 </TH> 
	 <TH> Etat des ventes<br/>
	 	<form method="get" action="vente.php"><input type="hidden" name="classement" value="etat_vente_1"/><input type="image" src="img/arrow_top.png" alt="top" /></FORM>
		<form method="get" action="vente.php"><input type="hidden" name="classement" value="etat_vente_2"/><input type="image" src="img/arrow_bottom.png" alt="bottom" /></FORM>
	 </TH> 
	 <TH> Date de fin<br/>de vente<br/>
	 	<form method="get" action="vente.php"><input type="hidden" name="classement" value="date_fin_vente_1"/><input type="image" src="img/arrow_top.png" alt="top" /></FORM>
		<form method="get" action="vente.php"><input type="hidden" name="classement" value="date_fin_vente_2"/><input type="image" src="img/arrow_bottom.png" alt="bottom" /></FORM>
</TH> 
	 <TH> Montant HFP<br/>
	 	<form method="get" action="vente.php"><input type="hidden" name="classement" value="montant_HFP_1"/><input type="image" src="img/arrow_top.png" alt="top" /></FORM>
		<form method="get" action="vente.php"><input type="hidden" name="classement" value="montant_HFP_2"/><input type="image" src="img/arrow_bottom.png" alt="bottom" /></FORM>
</TH> 
	 <TH> Montant FP<br/>
	 	<form method="get" action="vente.php"><input type="hidden" name="classement" value="montant_FP_1"/><input type="image" src="img/arrow_top.png" alt="top" /></FORM>
		<form method="get" action="vente.php"><input type="hidden" name="classement" value="montant_FP_2"/><input type="image" src="img/arrow_bottom.png" alt="bottom" /></FORM>
</TH> 
	 <TH> Mode d'envoi<br/>
	 	<form method="get" action="vente.php"><input type="hidden" name="classement" value="mode_envoi_1"/><input type="image" src="img/arrow_top.png" alt="top" /></FORM>
		<form method="get" action="vente.php"><input type="hidden" name="classement" value="mode_envoi_2"/><input type="image" src="img/arrow_bottom.png" alt="bottom" /></FORM>
</TH> 
	 <TH> Adresse de livraison</TH> 
	 <TH> Collection majoritaire<br/>
	 	<form method="get" action="vente.php"><input type="hidden" name="classement" value="collection_majoritaire_1"/><input type="image" src="img/arrow_top.png" alt="top" /></FORM>
		<form method="get" action="vente.php"><input type="hidden" name="classement" value="collection_majoritaire_2"/><input type="image" src="img/arrow_bottom.png" alt="bottom" /></FORM>
</TH> 
	 <TH> Editer la vente</TH> 
 </TR> 

<!--------------- Formulaire d'ajout d'une vente :------------------------>
<?php //Liste des collections
					$affichage_options_collection="";
					$reponse_type = $bdd->query('SELECT id,nom FROM collection ORDER BY nom ASC');
					while ($donnees_type = $reponse_type->fetch())
					{	
						$id_temp=$donnees_type['id'];
						$nom_temp=$donnees_type['nom'];
						$affichage_options_collection.="<option value=\"".$id_temp."\"";
						//if($id_temp==$id_type_general) //Type selectionné précedement par l'utilisateur
							//echo " selected";
						$affichage_options_collection.=">".$nom_temp."</option>\n";
					}
					$reponse_type->closeCursor(); // Termine le traitement de la requête
?>
<?php //Liste des clients
					$affichage_options_client="<option value=\"0\">----Client-----</option>\n";
					$reponse_vendeur = $bdd->query('SELECT id,prenom,nom FROM client ORDER BY prenom ASC');
					while ($donnees_vendeur = $reponse_vendeur->fetch())
					{	
						$id_temp=$donnees_vendeur['id'];
						$nom_temp=$donnees_vendeur['nom'];
						$prenom_temp=$donnees_vendeur['prenom'];
						$affichage_options_client.="<option value=\"".$id_temp."\"";
						//if($id_temp==$id_type_general) //Type selectionné précedement par l'utilisateur
							//echo " selected";
						$affichage_options_client.=">".$prenom_temp." ".$nom_temp."</option>\n";
					}
					$reponse_vendeur->closeCursor(); // Termine le traitement de la requête
?>
<?php //Liste des états (à la création d'une vente) :
	$affichage_options_etat="<option value=\"1. Vente ouverte\">1. Vente ouverte</option>\n
							 <option value=\"2. Vente acceptee\">2. Vente acceptée</option>\n
							 <option value=\"3. Reception payement\">3. Reception payement</option>\n
							 <option value=\"4. Livraison article\">4. Livraison article</option>\n
							 <option value=\"5. Client livre\">5. Client livré</option>\n";
?>
<?php
	$affichage_options_mode_envoi="<option value=\"LV\">LV</option>\n
							 <option value=\"LP\">LP</option>\n
							 <option value=\"LM\">LM</option>\n
							 <option value=\"C\">C</option>\n";
?>
<TR> 
	<form action="vente.php?classement=<?php echo $classement; ?>" method="post">
	   <input type="hidden" name="submit_vente" value="create">
		<TD><select name="id_client"><?php echo $affichage_options_client;?></select></TD>
	   <TD><select name="etat_vente"><?php echo $affichage_options_etat;?></select></TD>
	   <TD><input type="text" style="width: 75px;" name="date_fin_vente" class="datepicker"></TD>
	   <TD><input type="text" name="montant_HFP"></TD>
	   <TD><input type="text" name="montant_FP"></TD>
	   <TD><select name="mode_envoi"><?php echo $affichage_options_mode_envoi;?></select></TD>
	   <TD>Affichage<br/>automatique</TD>
	   <TD><select name="id_collection_majoritaire"><?php echo $affichage_options_collection;?></select></TD>
	   <TD>
		<input type="submit" value="Ajouter la vente">
	</form>  
	  </TD> 
</TR>

<?php 
$reponse_classement = $bdd->query($sql);
while ($donnees_classement = $reponse_classement->fetch())
{
?>
<TR> 
	<form action="vente.php?classement=<?php echo $classement; ?>" method="post">
	   <input type="hidden" name="submit_vente" value="editer">
	   <input type="hidden" name="id_vente" value="<?php echo $donnees_classement["id"]; ?>">
	   
		<TD><?php echo $donnees_classement["prenom_client_commentaire"]." ".$donnees_classement["nom_client_commentaire"]; ?>
			<input type="hidden" name="id_client" value="<?php echo $donnees_classement["id_client"]; ?>">		
		</TD>
	   <TD><select name="etat_vente">
			<?php 
				echo "<option value=\"1. Vente ouverte\" ";
				if($donnees_classement["etat_vente"]=="1. Vente ouverte")
					echo "selected";
				echo ">1. Vente ouverte</option>\n
					  <option value=\"2. Vente acceptee\" ";
				if($donnees_classement["etat_vente"]=="2. Vente acceptee")
					echo "selected";					  
				echo  ">2. Vente acceptée</option>\n
					  <option value=\"3. Reception payement\" ";
				if($donnees_classement["etat_vente"]=="3. Reception payement")
					echo "selected";					  
				echo ">3. Reception payement</option>\n
					  <option value=\"4. Livraison article\" ";
				if($donnees_classement["etat_vente"]=="4. Livraison article")
					echo "selected";					  										 
				echo ">4. Livraison article</option>\n
					<option value=\"5. Client livre\" ";
				if($donnees_classement["etat_vente"]=="5. Client livre")
					echo "selected";
				echo ">5. Client livré</option>\n			
					  <option value=\"6. Vente annulee\" ";
				if($donnees_classement["etat_vente"]=="6. Vente annulee")
					echo "selected";					  										 
				echo ">6. Vente annulée</option>\n";	   
			?>
			</select>
	   </TD>
	   <TD><input type="text" style="width: 75px;" name="date_fin_vente" class="datepicker" value="<?php echo $donnees_classement["date_fin_vente"]; ?>"></TD>
	   <TD><input type="text" name="montant_HFP" value="<?php echo $donnees_classement["montant_HFP"]; ?>"></TD>
	   <TD><input type="text" name="montant_FP" value="<?php echo $donnees_classement["montant_FP"]; ?>"></TD>
	   <TD><select name="mode_envoi">
			<?php	echo "<option value=\"LV\" ";
					if($donnees_classement["mode_envoi"]=="LV")
						echo "selected";
					echo ">LV</option>\n
						  <option value=\"LP\" ";
					if($donnees_classement["mode_envoi"]=="LP")
						echo "selected";
					echo ">LP</option>\n
						  <option value=\"LM\" ";
					if($donnees_classement["mode_envoi"]=="LM")
						echo "selected";
					echo ">LM</option>\n
						  <option value=\"C\" ";
					if($donnees_classement["mode_envoi"]=="C")
						echo "selected";
					echo ">C</option>\n";
			?>	
			</select>
	   </TD>
	   <TD><?php //Adresse de livraison du client :
	   $id_client_adresse=$donnees_classement["id_client"];
			$reponse_adresse = $bdd->query("SELECT adresse_client FROM client WHERE id='$id_client_adresse'");
			$adresse_client=$reponse_adresse->fetch()[0];
			$reponse_adresse->closeCursor();	
			echo $adresse_client;
	   ?></TD>
	   <TD><select name="id_collection_majoritaire">
	   <?php 
	   		$affichage_options_collection="";
			$reponse_type = $bdd->query('SELECT id,nom FROM collection ORDER BY nom ASC');
			while ($donnees_type = $reponse_type->fetch())
			{	
				$id_temp=$donnees_type['id'];
				$nom_temp=$donnees_type['nom'];
				$affichage_options_collection.="<option value=\"".$id_temp."\"";
				if($id_temp==$donnees_classement["id_collection_majoritaire"]) 
					$affichage_options_collection.=" selected";
				$affichage_options_collection.=">".$nom_temp."</option>\n";
			}
			$reponse_type->closeCursor(); // Termine le traitement de la requête
			
			echo $affichage_options_collection;
			
	   ?></select></TD>
	   <TD>
		<input type="submit" value="Editer la vente">
	</form><br/>
	<form action="vente.php?classement=<?php echo $classement; ?>" method="post">
	   <input type="hidden" name="submit_vente" value="supprimer">
	   <input type="hidden" name="id_vente" value="<?php echo $donnees_classement['id']; ?>">	
	   <input value="Supprimer la vente" onclick="return(confirm('Etes-vous sûr de vouloir supprimer la vente ?\nSi vous le faites, les articles liés à cette vente seront incrémentés !'));" type="submit">	
	</form><br/>
	<form action="vente.php?classement=<?php echo $classement; ?>" method="post">
		<input type="hidden" name="submit_vente" value="editer_articles">
		<input type="hidden" name="id_vente" value="<?php echo $donnees_classement['id']; ?>">	
		<input value="Ajouter/Supprimer des articles" type="submit">	
	</form>	  
	  </TD> 
</TR>
			
<?php
}
?>

</TABLE>
<?php //------------Ajout des balises de fin------------
	include 'bottom.php';
?>