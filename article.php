<?php //------------Connexion à la base de donnée------------
	include 'connect.php'; ?>
<?php //------------Ajout du header + menu------------
	$title_top="Articles";
	$date_top="OK"; //Activation de la date
	include 'top.php'; 
?>

	
<?php //Récupération du type de collection à afficher (si non indiqué -->tous)
  if(isset($_GET["id_type"]) AND isset($_GET["id_collection"]))
  {
    if($_GET["id_type"]>0 AND $_GET["id_collection"]<=0)
		echo '<center><font color=#B22222>Il faut selectionner un type ET une collection afin de pouvoir créer ou visionner les articles correspondants</font></center><br/><br/>';

	if($_GET["id_type"]>0 AND $_GET["id_collection"]>0){
		$id_type_get=$_GET["id_type"]; //ID DU TYPE !!!!

		$reponse = $bdd->query("SELECT nom FROM type_collection WHERE id='$id_type_get'");
		$nom_type_get = $reponse->fetch()[0]; // NOM DU TYPE !!!
		$reponse->closeCursor();	
		
		$id_collection_get=$_GET["id_collection"]; //ID DE LA COLLECTION !!!!
		
		$reponse = $bdd->query("SELECT nom FROM collection WHERE id='$id_collection_get'");
		$nom_collection_get = $reponse->fetch()[0]; // NOM DE LA COLLECTION !!!
		$reponse->closeCursor();
	}
	else{
		$id_type_get='0';
		$nom_type_get='0';
		$id_collection_get='0';
		$nom_collection_get='0';
  }
	
  }else{
		$id_type_get='0';
		$nom_type_get='0';
		$id_collection_get='0';
		$nom_collection_get='0';
  }
	
  
?>


<?php //Récupération du classement du tableau (si non indiqué : classement par nom)
  //------------Trier les articles selon le choix de l'utilisateur------------
  if(isset($_GET["classement"]))
  {
	$classement=$_GET["classement"];
	switch($classement)
    {
      case "nom_2":
				$sql_classement = 'SELECT * FROM article'; 
                if($id_collection_get!=0)
					$sql_classement.=" WHERE id_collection='$id_collection_get'";
				$sql_classement.=' ORDER BY nom ASC';	
				break;
      case "nom_1" :
				$sql_classement = 'SELECT * FROM article'; 
                if($id_collection_get!=0)
					$sql_classement.=" WHERE id_collection='$id_collection_get'";
				$sql_classement.=' ORDER BY nom DESC';	
				break;
      case "nom_collection_commentaire_2" :
				$sql_classement = 'SELECT * FROM article'; 
                if($id_collection_get!=0)
					$sql_classement.=" WHERE id_collection='$id_collection_get'";
				$sql_classement.=' ORDER BY nom_collection_commentaire ASC';	
                break;	
       case "nom_collection_commentaire_1" :
				$sql_classement = 'SELECT * FROM article'; 
                if($id_collection_get!=0)
					$sql_classement.=" WHERE id_collection='$id_collection_get'";
				$sql_classement.=' ORDER BY nom_collection_commentaire DESC';	
                break;
       case "stock_2" :
				$sql_classement = 'SELECT * FROM article'; 
                if($id_collection_get!=0)
					$sql_classement.=" WHERE id_collection='$id_collection_get'";
				$sql_classement.=' ORDER BY stock ASC';	
                break;	
       case "stock_1" :
				$sql_classement = 'SELECT * FROM article'; 
                if($id_collection_get!=0)
					$sql_classement.=" WHERE id_collection='$id_collection_get'";
				$sql_classement.=' ORDER BY stock DESC';	
                break;	
       case "numero_article_2" :
				$sql_classement = 'SELECT * FROM article'; 
                if($id_collection_get!=0)
					$sql_classement.=" WHERE id_collection='$id_collection_get'";
				$sql_classement.=' ORDER BY numero_article+0 ASC';	//Le +0 permet de trier des VARCHAR sous forme de INT !!	
                break;	
       case "numero_article_1" :
				$sql_classement = 'SELECT * FROM article'; 
                if($id_collection_get!=0)
					$sql_classement.=" WHERE id_collection='$id_collection_get'";
				$sql_classement.=' ORDER BY numero_article+0 DESC';	//Le +0 permet de trier des VARCHAR sous forme de INT !!
                break;	
				default :
				$sql_classement = 'SELECT * FROM article'; 
                if($id_collection_get!=0)
					$sql_classement.=" WHERE id_collection='$id_collection_get'";
				$sql_classement.=' ORDER BY numero_article+0 ASC, nom_collection_commentaire ASC'; //Le +0 permet de trier des VARCHAR sous forme de INT !!
               break;
    }
  }
  else
  {
	// Par défaut, on trie par nom descendant
	$sql_classement = 'SELECT * FROM article'; 
    if($id_collection_get!=0)
		$sql_classement.=" WHERE id_collection='$id_collection_get'";
	$sql_classement.=' ORDER BY numero_article+0 ASC, nom_collection_commentaire ASC';	//Le +0 permet de trier des VARCHAR sous forme de INT !!	
	
	$classement="no_classement";
  }
?>

<?php
if(isset($_POST["submit_article"])){
	//Nouvel article (si nom non vide)
	if($_POST["submit_article"]=="create"){
		if($_POST["nom"]!="" AND $_POST["id_collection"]!="0"){
		
			$id_nouveau=$_POST["id_collection"];
			$sql = "SELECT nom FROM collection WHERE id='$id_nouveau'";  // Récupération du nom en fonction de l'id
			$reponse = $bdd->query($sql);
			$nom_collection_commentaire=$reponse->fetch()[0];
			$reponse->closeCursor();
		
			$req = $bdd->prepare('INSERT INTO article(numero_article, nom, id_collection,nom_collection_commentaire, stock, date_dernier_vendu, description) VALUES(:numero_article, :nom, :id_collection,:nom_collection_commentaire, :stock, :date_dernier_vendu, :description)');
			$req->execute(array(
			'nom' => $_POST["nom"],
			'id_collection' => $id_nouveau,
			'nom_collection_commentaire' => $nom_collection_commentaire,
			'date_dernier_vendu' => $_POST["date_dernier_vendu"],
			'stock' => $_POST["stock"],
			'numero_article' => $_POST["numero_article"],
			'description' => $_POST["description"]
			));
			echo '<center><font color=#286400>L\'article '.$_POST["nom"].' a bien été ajouté !</font></center><br/><br/>';
		}
		else {
			echo '<center><font color=#B22222>L\'article '.$_POST["nom"].' n\'a pas été ajouté car vous n\'avez pas précisé le nom ou la collection associée !</font></center><br/>';
			echo '<center><font color=#B22222>Il faut selectionner un type et une collection afin de pouvoir créer les articles correspondants</font></center><br/><br/>';
		}
	}
	else if($_POST["submit_article"]=="editer"){ //Editer un article

		$id_nouveau=$_POST["id_collection"];
		$sql = "SELECT nom FROM collection WHERE id='$id_nouveau'";  // Récupération du nom en fonction de l'id
		$reponse = $bdd->query($sql);
		$nom_collection_commentaire=$reponse->fetch()[0];
		$reponse->closeCursor();	
	
		$req = $bdd->prepare('UPDATE article SET numero_article= :numero_article, nom= :nom, id_collection=:id_collection,nom_collection_commentaire=:nom_collection_commentaire,date_dernier_vendu=:date_dernier_vendu,stock=:stock,description=:description WHERE id = :id');
		$req->execute(array(
		'id' => $_POST["id"],
		'numero_article' => $_POST["numero_article"],
		'nom' => $_POST["nom"],
		'id_collection' => $id_nouveau,
		'nom_collection_commentaire' => $nom_collection_commentaire,
		'date_dernier_vendu' => $_POST["date_dernier_vendu"],
		'stock' => $_POST["stock"],
		'description' => $_POST["description"]
		));
		
		echo '<center><font color=#286400>L\'article '.$_POST["nom"].' a bien été édité.</font></center><br/>'; 		

	}
	else if($_POST["submit_article"]=="supprimer"){
		if($_POST['id']!=NULL){
		
		
			//Test pour savoir si une vente est liée à l'article :
			$id_article_test=$_POST['id'];
			$sql2 = "SELECT COUNT(*) FROM lien_vente WHERE id_article='$id_article_test'"; 
			$reponse2 = $bdd->query($sql2);
			$nombre_ventes_liees_avec_article=$reponse2->fetch()[0];
			$reponse2->closeCursor(); // Termine le traitement de la requête						
			
			//Test pour savoir si un achat est lié à l'article :
			$id_article_test=$_POST['id'];
			$sql2 = "SELECT COUNT(*) FROM lien_achat WHERE id_article='$id_article_test'"; 
			$reponse2 = $bdd->query($sql2);
			$nombre_achats_liees_avec_article=$reponse2->fetch()[0];
			$reponse2->closeCursor(); // Termine le traitement de la requête						
			
			if($nombre_achats_liees_avec_article==0 AND $nombre_ventes_liees_avec_article==0){ //On supprime l'article uniquement si il n'est pas lié à une vente/achat
				$id_article=$_POST['id'];
				$reponse_suppr = $bdd->query("DELETE FROM article WHERE id='$id_article'");
				echo '<center><font color=#286400>L\'article '. $_POST["nom"]. ' a été supprimé</font></center><br/>';
				$reponse_suppr->closeCursor(); // Termine le traitement de la requête
			}
			else 
				echo '<center><font color=#B22222>L\'article '. $_POST["nom"]. ' n\'a pas été supprimé car '.$nombre_ventes_liees_avec_article.' articles sont liés à une vente et '.$nombre_achats_liees_avec_article.' articles sont liés à un achat !</font></center><br/>';
		}
		else 
			echo '<center><font color=#B22222>L\'article '. $_POST["nom"]. ' n\'a pas été supprimé car l\'identifiant est mauvais (NULL)</font></center><br/>';

	}
}
 ?>
	

<!-------Debut de la selection de la collection------->
<script language="JavaScript">
function Choix(form) {
l = form.id_type.selectedIndex;
if (l == 0) {
  for (l=0;l<1000;l++) {
    form.id_collection.options[l].text="";
    form.id_collection.options[l].value="";
    }
  return;
  }
form.id_collection.selectedIndex = 0;
switch (l) {
<?php
	//Récupération du nombre de types :
	$reponse = $bdd->query('SELECT COUNT(*) FROM type_collection');
	$taille_type = $reponse->fetch()[0];
	$reponse->closeCursor();

	//Récupération du nom et id des types
	$i=0;
	$reponse = $bdd->query('SELECT id,nom FROM type_collection ORDER BY nom');
	while ($donnees = $reponse->fetch())
	{
		$nom_type[$i]=$donnees['nom'];
		$id_type[$i]=$donnees['id'];
		$i++;
	}
	$reponse->closeCursor();
		
	for ($i = 0; $i < $taille_type; $i++) {
		echo "case ".($i+1)." : var txt=new Array(";
		$sql="SELECT nom FROM collection WHERE id_type='$id_type[$i]' ORDER BY nom";
		$reponse = $bdd->query($sql);
		while ($donnee = $reponse->fetch())
		{
			echo"'".addslashes($donnee['nom'])."', ";
		}
		$reponse->closeCursor();
			
		echo "'');";

		echo "\n		 var value=new Array(";
		
		$sql="SELECT id FROM collection WHERE id_type='$id_type[$i]' ORDER BY nom";
		$reponse = $bdd->query($sql);
		while ($donnee = $reponse->fetch())
		{
			echo"'".$donnee['id']."', ";
		}
		$reponse->closeCursor();
			
		echo "'');";
		
		echo "break;\n";
	}
?>
}
form.id_collection.options[0].text="";
for (j=0;j<txt.length;j++) {
  form.id_collection.options[j+1].text=txt[j];
  form.id_collection.options[j+1].value=value[j];
  }
for(j=txt.length;j<1000;j++){
  form.id_collection.options[j+1].text="";
  form.id_collection.options[j+1].value="";
  }
} 
</script>	
<center>
	<h2>Choix de la collection</h2>
	<form method="get" action="article.php">
		<input type="hidden" name="classement" value="<?php echo $classement;?>"/>
		<select name="id_type" onchange="Choix(this.form)">
			<option value="0" selected="selected"></option>
			<?php 
				$reponse = $bdd->query('SELECT id,nom FROM type_collection ORDER BY nom');
				while ($donnees = $reponse->fetch())
				{
					echo '<option value="'.$donnees['id'].'">'.$donnees['nom']."</option>\n";
				}
			?>
		</select>
		<br/>
		<select name="id_collection">
						<option  value="<?php echo $id_collection_get; ?>" selected="selected"></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
						<option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option><option value="" selected=""></option>
		</select>
		<!--Envoie de la requête-->
		<br/>
		<input type="submit" value="Selectionner la collection">   
	</form>
	<?php //Qu'affiche t'on ? (une collection ? tout ?)
		if($id_collection_get!='0' AND $id_type_get!='0'){
			echo "<br/><h1>Articles de ".$nom_type_get."-->".$nom_collection_get." :</h1>";
	?>
</center>
<!-------Fin de la selection de la collection------->

<!------------Tableau des articles------------>
<TABLE BORDER="1"> 
  <TR> 
	 <TH>Numéro<br/>de<br/>l'article<br/>
	 	<form method="get" action="article.php"><input type="hidden" name="classement" value="numero_article_1"/><input type="hidden" name="id_type" value="<?php echo $id_type_get; ?>"/><input type="hidden" name="id_collection" value="<?php echo $id_collection_get; ?>"/><input type="image" src="img/arrow_top.png" alt="top" /></FORM>
		<form method="get" action="article.php"><input type="hidden" name="classement" value="numero_article_2"/><input type="hidden" name="id_type" value="<?php echo $id_type_get; ?>"/><input type="hidden" name="id_collection" value="<?php echo $id_collection_get; ?>"/><input type="image" src="img/arrow_bottom.png" alt="bottom" /></FORM>	 
	 </TH> 
	 <TH> Article<br/>
	 	<form method="get" action="article.php"><input type="hidden" name="classement" value="nom_1"/><input type="hidden" name="id_type" value="<?php echo $id_type_get; ?>"/><input type="hidden" name="id_collection" value="<?php echo $id_collection_get; ?>"/><input type="image" src="img/arrow_top.png" alt="top" /></FORM>
		<form method="get" action="article.php"><input type="hidden" name="classement" value="nom_2"/><input type="hidden" name="id_type" value="<?php echo $id_type_get; ?>"/><input type="hidden" name="id_collection" value="<?php echo $id_collection_get; ?>"/><input type="image" src="img/arrow_bottom.png" alt="bottom" /></FORM>	 
	 </TH> 
	 <TH> Collection associée<br/>
	 	<form method="get" action="article.php"><input type="hidden" name="classement" value="nom_collection_commentaire_1"/><input type="hidden" name="id_type" value="<?php echo $id_type_get; ?>"/><input type="hidden" name="id_collection" value="<?php echo $id_collection_get; ?>"/><input type="image" src="img/arrow_top.png" alt="top" /></FORM>
		<form method="get" action="article.php"><input type="hidden" name="classement" value="nom_collection_commentaire_2"/><input type="hidden" name="id_type" value="<?php echo $id_type_get; ?>"/><input type="hidden" name="id_collection" value="<?php echo $id_collection_get; ?>"/><input type="image" src="img/arrow_bottom.png" alt="bottom" /></FORM>	 
	 </TH> 
	 <TH> Stock<br/>
	 	<form method="get" action="article.php"><input type="hidden" name="classement" value="stock_1"/><input type="hidden" name="id_type" value="<?php echo $id_type_get; ?>"/><input type="hidden" name="id_collection" value="<?php echo $id_collection_get; ?>"/><input type="image" src="img/arrow_top.png" alt="top" /></FORM>
		<form method="get" action="article.php"><input type="hidden" name="classement" value="stock_2"/><input type="hidden" name="id_type" value="<?php echo $id_type_get; ?>"/><input type="hidden" name="id_collection" value="<?php echo $id_collection_get; ?>"/><input type="image" src="img/arrow_bottom.png" alt="bottom" /></FORM>	 	 	 
	 </TH>
	 <TH>Date du<br/>dernier<br/>article<br/>vendu<br/>
	 	<form method="get" action="article.php"><input type="hidden" name="classement" value="date_dernier_vendu_1"/><input type="hidden" name="id_type" value="<?php echo $id_type_get; ?>"/><input type="hidden" name="id_collection" value="<?php echo $id_collection_get; ?>"/><input type="image" src="img/arrow_top.png" alt="top" /></FORM>
		<form method="get" action="article.php"><input type="hidden" name="classement" value="date_dernier_vendu_2"/><input type="hidden" name="id_type" value="<?php echo $id_type_get; ?>"/><input type="hidden" name="id_collection" value="<?php echo $id_collection_get; ?>"/><input type="image" src="img/arrow_bottom.png" alt="bottom" /></FORM>	 	 	 
	 </TH>
	 <TH> Description</TH>
	 <TH> Editer</TH>
 </TR>
<!------------Formulaire d'ajout d'un article------------>
<TR> 
	<form action="article.php?classement=<?php echo $classement; ?>&id_type=<?php echo $id_type_get; ?>&id_collection=<?php echo $id_collection_get; ?>" method="post">
		<input type="hidden" name="submit_article" value="create">
		<TD><input type="text" style="width: 50px;" name="numero_article"></TD><!--numéro de l'article-->
		<TD><input type="text" style="width: 200px;" name="nom"></TD> <!--nom-->
		<TD>
			<input type="hidden" name="id_collection" value="<?php echo $id_collection_get; ?>">
		<?php
			if($id_type_get>0 AND $id_collection_get>0){
			echo '<center>'.$nom_type_get.'-->'.$nom_collection_get.'</center>';
			}else
			echo 'Veuillez selectionner une collection afin de créer un nouvel article';
		?>
		</TD>
		<TD><input type="text" style="width: 60px;" name="stock"></TD>
		<TD><input type="text" style="width: 75px;" name="date_dernier_vendu" class="datepicker"></TD>
		<TD><textarea type="text" style="width: 200px; height: 80px;" name="description"></textarea></TD>
		<TD><input type="submit" value="Ajouter un article"></TD> 	
   </form>
 </TR>	
<!------------Affichage des articles déjà existants------------>
<?php
	$reponse_classement = $bdd->query($sql_classement);
	while ($donnees_classement = $reponse_classement->fetch())
	{	
?> 
<TR> 
	<form action="article.php?classement=<?php echo $classement; ?>&id_type=<?php echo $id_type_get; ?>&id_collection=<?php echo $id_collection_get; ?>" method="post">
		<input type="hidden" name="submit_article" value="editer">
		<input type="hidden" name="id" value="<?php echo $donnees_classement['id']; ?>">
		<input type="hidden" name="id_collection" value="<?php echo $donnees_classement['id_collection']; ?>">
		<TD><input type="text" style="width: 50px;" name="numero_article"value="<?php echo $donnees_classement['numero_article']; ?>"></TD><!--numéro de l'article-->
		<TD><input type="text" style="width: 200px;" name="nom" value="<?php echo $donnees_classement['nom']; ?>"></TD> <!--nom-->
		<TD>
		<?php echo $donnees_classement['nom_collection_commentaire'];?>
		</TD>
		<TD><input type="text" name="stock" style="width: 60px;" value="<?php echo $donnees_classement['stock'];?>"></TD>
		<TD><input type="text" style="width: 75px;" name="date_dernier_vendu" class="datepicker" value="<?php echo $donnees_classement['date_dernier_vendu'];?>"></TD>
		<TD><textarea type="text" style="width: 200px; height: 80px;" name="description"><?php echo $donnees_classement['description'];?></textarea></TD>
		<TD><input type="submit" value="Editer l'article" <?php //onclick="return(confirm('Attention\nSi vous éditez le stock car vous avez effectuer une vente/achat, cela risque d\'entrer en conflit avec ceux-ci !'));" ?>>
   </form>
   <br/>   
<!-----Formulaire de suppression d'une collection------------->
	<form action="article.php?classement=<?php echo $classement; ?>&id_type=<?php echo $id_type_get; ?>&id_collection=<?php echo $id_collection_get; ?>" method="post">
		   <input type="hidden" name="submit_article" value="supprimer">
		   <input type="hidden" name="id" value="<?php echo $donnees_classement['id']; ?>">
		   <input type="hidden" name="nom" value="<?php echo $donnees_classement['nom']; ?>">
		   <input type="submit" value="Supprimer l'article" onclick="return(confirm('Etes-vous sûr de vouloir supprimer la collection <?php echo $donnees_classement['nom']; ?> ?'));">
	</form>
	
	</TD>
 
 </TR>	
 <?php
	}
	$reponse_classement->closeCursor(); // Termine le traitement de la requête
?>

</TABLE>

<?php
	}else{
	echo "<br/><h1>Pour afficher les articles, veuillez selectionner une collection et un type  !</h1>";
	}
?>
<?php //------------Ajout des balises de fin------------
	include 'bottom.php';
?>