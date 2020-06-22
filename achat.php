<?php //------------Connexion à la base de donnée------------
	include 'connect.php'; ?>
<?php //------------Ajout du header + menu------------
	$date_top="OK"; //Activation de la date
	$title_top="Achats";
	include 'top.php'; 
?>


<?php //------------Traitement des demandes de l'utilisateur (ajout/edition/suppresion d'achats)------------
  if(isset($_POST["submit_achat"]))
  {
	//Nouvel achat
	if($_POST["submit_achat"]=="create"){
		if($_POST["id_vendeur"]!=0 AND $_POST["cout"]!="" AND $_POST["date"]!=""){
		
			$id_type_majoritaire_create=$_POST["id_type_majoritaire"];
			$sql_create = "SELECT nom FROM type_collection WHERE id='$id_type_majoritaire_create'"; 
			$reponse_create = $bdd->query($sql_create);
			$type_majoritaire_create=$reponse_create->fetch()[0];
			$reponse_create->closeCursor(); // Termine le traitement de la requête

			$id_type_secondaire_create=$_POST["id_type_secondaire"];
			$sql_create = "SELECT nom FROM type_collection WHERE id='$id_type_secondaire_create'"; 
			$reponse_create = $bdd->query($sql_create);
			$type_secondaire_create=$reponse_create->fetch()[0];
			$reponse_create->closeCursor(); // Termine le traitement de la requête
			
			//Ajout du nom du vendeur dans "vendeur" grace à id_vendeur :
			$id_vendeur_create=$_POST["id_vendeur"];
			$sql_create = "SELECT nom FROM fournisseur WHERE id='$id_vendeur_create'"; 
			$reponse_create = $bdd->query($sql_create);
			$vendeur_create=$reponse_create->fetch()[0];
			$reponse_create->closeCursor(); // Termine le traitement de la requête
			
			$req = $bdd->prepare('INSERT INTO achat(cout, vendeur, id_vendeur, id_type_majoritaire, type_majoritaire, id_type_secondaire, type_secondaire, date) VALUES(:cout, :vendeur, :id_vendeur, :id_type_majoritaire, :type_majoritaire, :id_type_secondaire, :type_secondaire, :date)');
			if ($req->execute(array(
			'cout' => $_POST["cout"],
			'vendeur' => $vendeur_create,
			'id_vendeur' => $_POST["id_vendeur"],
			'id_type_majoritaire' => $_POST["id_type_majoritaire"],
			'id_type_secondaire' => $_POST["id_type_secondaire"],
			'date' => $_POST["date"],
			'type_majoritaire' => $type_majoritaire_create,
			'type_secondaire' => $type_secondaire_create
			)))
            {
            	echo '<center><font color=#286400>L\'achat avec le vendeur '.$vendeur_create.' a bien été ajouté !<br/>Vous pouvez à présent ajouter les articles à cet achat.</font></center><br/><br/>';
            }
            else
            {
                $error = implode(', ', $req->errorInfo());
                echo '<center><font color="red">Erreur lors de l\'ajout de l\'achat avec le vendeur: '.$error.'</font></center><br/><br/>';
 
            }

	
			//On lance automatiquement la page d'ajout d'articles :
			$sql_lancement="SELECT id FROM achat ORDER BY id DESC LIMIT 0, 1";
			$reponse_lancement = $bdd->query($sql_lancement);
			$id_achat_lancement=$reponse_lancement->fetch()[0];
			$reponse_create->closeCursor(); // Termine le traitement de la requête			
	?>
			<form name="autosend1" id="autosend1" method="post" action="achat2.php">
			<input type="hidden" name="id_achat" value="<?php echo $id_achat_lancement; ?>">
			<input type="hidden" name="type_de_requete" value="creation">
			</form>
			 <!-- Enable autosend POST-->
			 <script LANGUAGE="javascript">
			 document.autosend1.submit();
			 </script>
	<?php
		
		}
		else 
			echo '<center><font color=#B22222>L\'achat avec le vendeur n\'a pas été ajouté car vous n\'avez précisé un des paramètres suivants :<br/>le cout, le nom du vendeur, la date !</font></center><br/><br/>';
	}
	//Editer les articles de l'achat
	else if($_POST["submit_achat"]=="editer_articles" AND isset($_POST["id_achat"])){
		//On lance la page d'ajout d'articles :
	?>
		<form name="autosend2" id="autosend2" method="post" action="achat2.php">
		<input type="text" name="useless" value="">
		<input type="hidden" name="id_achat" value="<?php echo $_POST["id_achat"]; ?>">
		<input type="hidden" name="type_de_requete" value="edition">
		</form>
		<!-- Enable autosend POST-->
		<script LANGUAGE="javascript">
		document.autosend2.submit();
		</script>		
	<?php
	}
		//Editer l'achat (informations de base)
	else if($_POST["submit_achat"]=="editer" AND isset($_POST["id_achat"])){
		if($_POST["id_vendeur"]!=0){
			$id_achat_recup=$_POST["id_achat"];
			//Récupération de type_majoritaire et type_secondaire :
			$reponse_recup_type = $bdd->query("SELECT type_majoritaire, type_secondaire FROM achat WHERE id='$id_achat_recup'");
			while ($recup_type = $reponse_recup_type->fetch())
			{
				$type_majoritaire_edition=$recup_type["type_majoritaire"];
				$type_secondaire_edition=$recup_type["type_secondaire"];
			}

			//Ajout du nom du vendeur dans "vendeur" grace à id_vendeur :
			$id_vendeur_create=$_POST["id_vendeur"];
			$sql_create = "SELECT nom FROM fournisseur WHERE id='$id_vendeur_create'"; 
			$reponse_create = $bdd->query($sql_create);
			$vendeur_create=$reponse_create->fetch()[0];
			$reponse_create->closeCursor(); // Termine le traitement de la requête
			
			//Edition de l'achat :
			$req = $bdd->prepare('UPDATE achat SET cout= :cout, vendeur =:vendeur, id_vendeur =:id_vendeur, id_type_majoritaire = :id_type_majoritaire, id_type_secondaire = :id_type_secondaire, type_majoritaire = :type_majoritaire, type_secondaire = :type_secondaire, date = :date WHERE id = :id');
			$req->execute(array(
			'id' => $_POST["id_achat"],
			'cout' => $_POST["cout"],
			'id_vendeur' => $_POST["id_vendeur"],
			'vendeur' => $vendeur_create,
			'id_type_majoritaire' => $_POST["id_type_majoritaire"],
			'id_type_secondaire' => $_POST["id_type_secondaire"],
			'type_majoritaire' => $type_majoritaire_edition,
			'type_secondaire' => $type_secondaire_edition,
			'date' => $_POST["date"]
			));
			
			echo '<center><font color=#286400>L\'achat ('.$vendeur_create.' le '.$_POST["date"].') a bien été édité.</font></center><br/>'; 		


		}
		else
			echo '<center><font color=#B22222>L\'achat avec le vendeur n\'a pas été édité car vous n\'avez précisé le nom du vendeur !</font></center><br/><br/>';
		
	}
	//Supprimer l'achat
	else if($_POST["submit_achat"]=="supprimer" AND isset($_POST["id_achat"])){
		$id_achat_suppr=$_POST["id_achat"];

		//Vérifier que "stock moins achat >=0 pour tout les articles de l'achat) :		
		$ok_continue_suppr="OK";

		$sql_test = "SELECT quantite,id_article FROM lien_achat WHERE id_achat='$id_achat_suppr';"; 
		$reponse_test = $bdd->query($sql_test);
		while ($donnees_quantite_achat = $reponse_test->fetch())
		{
			$id_article_achat=$donnees_quantite_achat['id_article'];
			$reponse_test2 = $bdd->query("SELECT stock FROM article WHERE id='$id_article_achat';");
			if(($reponse_test2->fetch()[0]-$donnees_quantite_achat['quantite'])<0)
				$ok_continue_suppr="ERROR";
			$reponse_test2->closeCursor(); // Termine le traitement de la requête			
		}		
		$reponse_test->closeCursor(); // Termine le traitement de la requête			

		if($ok_continue_suppr=="OK"){
			//Décrémenter les articles dont le lien achat est celui de l'achat à supprimer :
			$sql_test = "SELECT quantite,id_article FROM lien_achat WHERE id_achat='$id_achat_suppr';"; 
			$reponse_decrementation = $bdd->query($sql_test);
			while ($donnees_quantite_achat = $reponse_decrementation->fetch())
			{
				$quantite=$donnees_quantite_achat['quantite'];
				$id_article_decr=$donnees_quantite_achat['id_article'];
				
				$reponse_decrementation2 = $bdd->query('UPDATE article SET stock=stock-'.$quantite.' WHERE id ='.$id_article_decr.';');	
				$reponse_decrementation2->closeCursor(); // Termine le traitement de la requête		
				}		
			$reponse_decrementation->closeCursor(); // Termine le traitement de la requête			
		
			//Supprimer les liens achats contenant l'id de l'achat :
			$reponse_suppr_liens_achats = $bdd->query("DELETE FROM lien_achat WHERE id_achat='$id_achat_suppr'");
			//Supprimer l'achat :
			$reponse_suppr_achat = $bdd->query("DELETE FROM achat WHERE id='$id_achat_suppr'");
			
			echo '<center><font color=#286400>L\'achat a été completement supprimé !<br/>Les articles ont bien été décrémentés.</font></center><br/><br/>';
		}
		else 
			echo '<center><font color=#B22222>>L\'achat n\'a pas été supprimé car il y a un problème de stock (stock - quantité de l\'achat < 0)!<br/>Verifiez vos stocks !</font></center><br/><br/>';

	}

}
?>

<!------------------------------------------------Tableau des achats (à creer et anciens)---------------------------->
<?php
  //------------Trier les achats selon le choix de l'utilisateur------------
  if(isset($_GET["classement"]))
  {
	$classement=$_GET["classement"];
	switch($classement)
    {
      case "date_2":
				$sql = 'SELECT * FROM achat ORDER BY date ASC'; 
                break;
      case "date_1" :
				$sql = 'SELECT * FROM achat ORDER BY date DESC'; 
                break;				
      case "vendeur_2":
				$sql = 'SELECT * FROM achat ORDER BY vendeur ASC'; 
                break;
      case "vendeur_1" :
				$sql = 'SELECT * FROM achat ORDER BY vendeur DESC'; 
                break;				
      case "cout_2":
				$sql = 'SELECT * FROM achat ORDER BY cout ASC'; 
                break;
      case "cout_1" :
				$sql = 'SELECT * FROM achat ORDER BY cout DESC'; 
                break;				
      case "type_majoritaire_2":
				$sql = 'SELECT * FROM achat ORDER BY type_majoritaire ASC'; 
                break;
      case "type_majoritaire_1" :
				$sql = 'SELECT * FROM achat ORDER BY type_majoritaire DESC'; 
                break;				
      case "type_secondaire_2":
				$sql = 'SELECT * FROM achat ORDER BY type_secondaire ASC'; 
                break;
      case "type_secondaire_1" :
				$sql = 'SELECT * FROM achat ORDER BY type_secondaire DESC'; 
                break;				
      default :
				$sql = 'SELECT * FROM achat ORDER BY date DESC'; 
                break;
    }
  }
  else
  {
	// Par défaut, on trie par nom descendant
	$sql = 'SELECT * FROM achat ORDER BY date DESC'; 
	$classement="no_classement";
  }

?>
<!------------Tableau des types------------>
<TABLE BORDER="1"> 
  <CAPTION><h1>Liste des achats :<h1/></CAPTION> 
  <TR> 
	 <TH> Date<br/>
	 	<form method="get" action="achat.php"><input type="hidden" name="classement" value="date_1"/><input type="image" src="img/arrow_top.png" alt="top" /></FORM>
		<form method="get" action="achat.php"><input type="hidden" name="classement" value="date_2"/><input type="image" src="img/arrow_bottom.png" alt="bottom" /></FORM>
	 </TH> 
	 <TH> Fournisseur<br/>
	 	<form method="get" action="achat.php"><input type="hidden" name="classement" value="vendeur_1"/><input type="image" src="img/arrow_top.png" alt="top" /></FORM>
		<form method="get" action="achat.php"><input type="hidden" name="classement" value="vendeur_2"/><input type="image" src="img/arrow_bottom.png" alt="bottom" /></FORM>
	 </TH> 
	 <TH> Coût<br/>
	 	<form method="get" action="achat.php"><input type="hidden" name="classement" value="cout_1"/><input type="image" src="img/arrow_top.png" alt="top" /></FORM>
		<form method="get" action="achat.php"><input type="hidden" name="classement" value="cout_2"/><input type="image" src="img/arrow_bottom.png" alt="bottom" /></FORM>
</TH> 
	 <TH> Type<br/>majoritaire<br/>
	 	<form method="get" action="achat.php"><input type="hidden" name="classement" value="type_majoritaire_1"/><input type="image" src="img/arrow_top.png" alt="top" /></FORM>
		<form method="get" action="achat.php"><input type="hidden" name="classement" value="type_majoritaire_2"/><input type="image" src="img/arrow_bottom.png" alt="bottom" /></FORM>
</TH> 
	 <TH> Type<br/>secondaire<br/>
	 	<form method="get" action="achat.php"><input type="hidden" name="classement" value="type_secondaire_1"/><input type="image" src="img/arrow_top.png" alt="top" /></FORM>
		<form method="get" action="achat.php"><input type="hidden" name="classement" value="type_secondaire_2"/><input type="image" src="img/arrow_bottom.png" alt="bottom" /></FORM>
</TH> 
	 <TH> Editer l'achat</TH> 
 </TR> 
<!--------------- Formulaire d'ajout d'un achat :------------------------>
<?php
					$affichage_options_type="";
					$reponse_type = $bdd->query('SELECT id,nom FROM type_collection ORDER BY nom ASC');
					while ($donnees_type = $reponse_type->fetch())
					{	
						$id_temp=$donnees_type['id'];
						$nom_temp=$donnees_type['nom'];
						$affichage_options_type.="<option value=\"".$id_temp."\"";
						//if($id_temp==$id_type_general) //Type selectionné précedement par l'utilisateur
							//echo " selected";
						$affichage_options_type.=">".$nom_temp."</option>\n";
					}
					$reponse_type->closeCursor(); // Termine le traitement de la requête
?>
<?php
					$affichage_options_vendeur="<option value=\"0\">----fournisseur-----</option>\n";
					$reponse_vendeur = $bdd->query('SELECT id,nom FROM fournisseur ORDER BY nom ASC');
					while ($donnees_vendeur = $reponse_vendeur->fetch())
					{	
						$id_temp=$donnees_vendeur['id'];
						$nom_temp=$donnees_vendeur['nom'];
						$affichage_options_vendeur.="<option value=\"".$id_temp."\"";
						//if($id_temp==$id_type_general) //Type selectionné précedement par l'utilisateur
							//echo " selected";
						$affichage_options_vendeur.=">".$nom_temp."</option>\n";
					}
					$reponse_vendeur->closeCursor(); // Termine le traitement de la requête
?>
<TR> 
	<form action="achat.php?classement=<?php echo $classement; ?>" method="post">
	   <input type="hidden" name="submit_achat" value="create">
	   <TD><input type="text" style="width: 75px;" name="date" class="datepicker"></TD>
	   <TD><select name="id_vendeur"><?php echo $affichage_options_vendeur;?></select></TD>
	   <TD><input type="text" name="cout">€</TD>
	   
	   <TD><select name="id_type_majoritaire"><?php echo $affichage_options_type;?></select></TD><!--Type majoritaire-->
	   <TD><select name="id_type_secondaire"><?php echo $affichage_options_type;?></select></TD><!--Type secondaire-->
	   <TD>
		<input type="submit" value="Ajouter un achat">
	</form>  
	  </TD> 
</TR>
<!----------------------------Affichage des achats precedents :-------------------------------------->
<?php 
	$reponse_classement = $bdd->query($sql);
	while ($donnees_classement = $reponse_classement->fetch())
	{
?>
<TR> 
	<form action="achat.php?classement=<?php echo $classement; ?>" method="post">
	   <input type="hidden" name="submit_achat" value="editer">
	   <input type="hidden" name="id_achat" value="<?php echo $donnees_classement['id']; ?>">
	   <TD><input type="text" style="width: 75px;" name="date" value="<?php echo $donnees_classement['date']; ?>" class="datepicker"></TD>
	   <TD>
	   <?php
					$affichage_options_vendeur="<option value=\"0\">----fournisseur-----</option>\n";
					$reponse_vendeur = $bdd->query('SELECT id,nom FROM fournisseur ORDER BY nom ASC');
					while ($donnees_vendeur = $reponse_vendeur->fetch())
					{	
						$id_temp=$donnees_vendeur['id'];
						$nom_temp=$donnees_vendeur['nom'];
						$affichage_options_vendeur.="<option value=\"".$id_temp."\"";
						if($id_temp==$donnees_classement['id_vendeur']) //Type selectionné précedement par l'utilisateur
							$affichage_options_vendeur.=" selected";
						$affichage_options_vendeur.=">".$nom_temp."</option>\n";
					}
					$reponse_vendeur->closeCursor(); // Termine le traitement de la requête
?>
	   <select name="id_vendeur"><?php echo $affichage_options_vendeur;?></select>
	   
	   </TD>
	   <TD><input type="text" name="cout" value="<?php echo $donnees_classement['cout']; ?>" >€</TD>
	   <?php
	   $affichage_options_type_maj=" ";
	   $affichage_options_type_sec=" ";
	   	$reponse_type = $bdd->query('SELECT id,nom FROM type_collection ORDER BY nom ASC');
		while ($donnees_type = $reponse_type->fetch())
		{	
			$id_temp=$donnees_type['id'];
			$nom_temp=$donnees_type['nom'];
			$affichage_options_type_maj.="<option value=\"".$id_temp."\"";
			$affichage_options_type_sec.="<option value=\"".$id_temp."\"";
			if($id_temp==$donnees_classement['id_type_majoritaire'])
				$affichage_options_type_maj.=" selected";
			if($id_temp==$donnees_classement['id_type_secondaire'])
				$affichage_options_type_sec.=" selected";
			$affichage_options_type_sec.=">".$nom_temp."</option>\n";
			$affichage_options_type_maj.=">".$nom_temp."</option>\n";
		}	
		$reponse_type->closeCursor(); // Termine le traitement de la requête
	   ?>
	   <TD><select name="id_type_majoritaire"><?php echo $affichage_options_type_maj;?></select></TD><!--Type majoritaire-->
	   <TD><select name="id_type_secondaire"><?php echo $affichage_options_type_sec;?></select></TD><!--Type secondaire-->
	   <TD>
		<input type="submit" value="Editer l'achat">
	</form><br/>
	<form action="achat.php?classement=<?php echo $classement; ?>" method="post">
	   <input type="hidden" name="submit_achat" value="supprimer">
	   <input type="hidden" name="id_achat" value="<?php echo $donnees_classement['id']; ?>">	
	   <input value="Supprimer l'achat" onclick="return(confirm('Etes-vous sûr de vouloir supprimer l\'achat ?\nSi vous le faites, les articles liés à cet achat seront décrémentés !'));" type="submit">	
	</form><br/>
	<form action="achat.php?classement=<?php echo $classement; ?>" method="post">
		<input type="hidden" name="submit_achat" value="editer_articles">
		<input type="hidden" name="id_achat" value="<?php echo $donnees_classement['id']; ?>">	
		<input value="Ajouter/Supprimer des articles" type="submit">	
	</form>	
	  </TD> 
</TR>

<?php 	
	}
	$reponse_classement->closeCursor();
?>
</TABLE>  
<?php //------------Ajout des balises de fin------------
	include 'bottom.php';
?>