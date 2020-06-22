<?php echo("<?xml version=\"1.0\" encoding=\"ANSI\"?>\n"); ?>
<!--<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">-->


<?php //------------Ajout du header + menu------------
    error_reporting(0);
	$title_top="Ajout d'articles dans l'achat en cours";
	$date_top="OK"; //Activation de la date
	include 'top_ansi.php'; 
	include 'config.php'; 
?>


<?php


/* Variables de connexion : ajustez ces paramètres selon votre propre environnement */

$serveur = $database_ip;

$admin   = $database_username;

$mdp     = $database_password;

$bdd    = $database_name;

$connexion = mysql_pconnect($serveur, $admin, $mdp);

/* On récupère si elle existe la valeur de la région envoyée par le formulaire ainsi que l'id de vente */

if(isset($_POST['id_achat']) AND isset($_POST['type_de_requete'])){
	$id_achat=$_POST['id_achat'];
	$type_de_requete=$_POST['type_de_requete'];
	if($id_achat>0)
		echo ""/*"id de l'achat : ".$id_achat."<br/>"*/;
	else
		echo '<center><font color=#B22222>ATTENTION : Aucun achat n\'a été sélectionné, vous ne pouvez donc pas ajouter d\'articles ...<br/>Veuillez revenir dans la liste des achats et cliquer sur  "Ajouter un article à l\'achat !"</font></center><br/>';		
}
else{
	$id_achat="";
		echo '<center><font color=#B22222>ATTENTION : Aucun achat n\'a été sélectionné, vous ne pouvez donc pas ajouter d\'articles ...<br/>Veuillez revenir dans la <a href="achat.php">liste des achats</a> et cliquer sur  "Ajouter un article à l\'achat !"</font></center><br/>';		
}

//Classement
if(isset($_POST['classement']))
	$classement=$_POST['classement'];
else
	$classement="";

$idr2 = isset($_POST['departement'])?$_POST['departement']:null;
$idr = (isset($_POST['region']))?$_POST['region']:null;

?>

<?php //Suppression d'un lien article-achat :

if(isset($_POST['supprimer_lien_article']) && isset($_POST['id_achat']) && isset($_POST['lien_article_a_suppr']) && isset($_POST['ok_suppr']) && isset($_POST['id_article_a_suppr'])){
	if($_POST['supprimer_lien_article']=="1" && $_POST['id_achat']>0 && $_POST['lien_article_a_suppr']>0){
	
		$suppr_id_lien_achat=$_POST['lien_article_a_suppr'];
		$suppr_id_achat=$_POST['id_achat'];
		$suppr_id_article=$_POST['id_article_a_suppr'];
		$reponse85 = mysql_query("SELECT `quantite` FROM `lien_achat` WHERE `id`='$suppr_id_lien_achat';", $connexion);
		while($donnees85 = mysql_fetch_assoc($reponse85))
		{
			$suppr_quantite=$donnees85['quantite'];
		}
		
		$reponse86 = mysql_query("SELECT `stock` FROM `article` WHERE `id`='$suppr_id_article';", $connexion);
		while($donnees86 = mysql_fetch_assoc($reponse86))
		{
			$quantite_en_stock_suppr=$donnees86['stock'];
		}

		if(($quantite_en_stock_suppr-$suppr_quantite)>=0 && $suppr_quantite>=0){ //Eviter de décrémenter un stock qui va devenir négatif si l'on fait l'opération)
			//On peut décrémenter le stock et supprimer le lien article-achat
			//Suppression du lien article-achat :		
			$reponse2= mysql_query("DELETE FROM lien_achat WHERE id='$suppr_id_lien_achat'");
			//Décrémenter le stock de l'article (suppression d'un "ajout"-->il faut décrémenter):
			$reponse3= mysql_query("UPDATE article SET stock=stock-".$suppr_quantite." WHERE id=".$suppr_id_article."");
			echo '<center><font color=#286400>Le lien entre l\'achat et l\'article a bien été supprimé<br/>Le stock de l\'article en question a été décrémenté de '.$suppr_quantite.'.</font></center><br/>';
		
		}
		else
			echo '<center><font color=#B22222>Problème lors de la suppression du lien entre l\'achat et un article.<br/>Ceci vient du fait que si l\'on supprime cet achat, le stock de l\'article sera négatif.<br/>Vous avez dû faire une erreure quelquepart (éditer !</font></center><br/>';		

	}
	else
		echo '<center><font color=#B22222>Problème lors de la suppression du lien entre l\'achat et un article.<br/>Ceci ne devrait pas arriver, contactez l\'administrateur.</font></center><br/>';		

}
?>


<?php //Le reste (création/affichage) :
	$quantite=1;
	$prix_unitaire="";
if(isset($_POST['region']) && isset($_POST['departement']) && isset($_POST['ville']) && isset($_POST['quantite']) && isset($_POST['prix_unitaire']))

{

    $type_selectionnee = $_POST['region']; 

    $collection_selectionne = $_POST['departement'];

	$article_selectionne = $_POST["ville"];
	
	$quantite = $_POST['quantite'];
	
	$prix_unitaire = $_POST['prix_unitaire'];

?>

<?php //echo "<p>Vous avez sélectionné ".$quantite." article".(($quantite>1)?"s":"")." ".$article_selectionne." (collection ".$collection_selectionne." et type ".$type_selectionnee.")</p>"; ?>

<?php
	if(isset($_POST['ok'])){ //Ajout d'un article demandé si les conditions sont bien remplis :
		if($id_achat>0 && $quantite>0 && $article_selectionne>0 && $collection_selectionne>0 && $type_selectionnee>0){
		//$collection_selectionne (->id_collection), $article_selectionne(->id_article), $quantite(->quantite), $id_achat(->id_achat)
		
		///article_commentaire et collection_commentaire :
		$reponse1 = mysql_query("SELECT `nom`,`nom_collection_commentaire` FROM `article` WHERE `id`='$article_selectionne';", $connexion);
		while($donnees1 = mysql_fetch_assoc($reponse1))
		{
			$article_commentaire=$donnees1['nom'];
			$collection_commentaire=$donnees1['nom_collection_commentaire'];
		}
		//Ajout de l'article à l'achat :		
		$reponse2= mysql_query('INSERT INTO lien_achat(id_article, article_commentaire, id_collection,collection_commentaire, id_achat, quantite, prix_unitaire) VALUES("'.$article_selectionne.'","'.$article_commentaire.'","'.$collection_selectionne.'","'.$collection_commentaire.'","'.$id_achat.'","'.$quantite.'","'.$prix_unitaire.'")');
		//Ajout dans le stock :
		$reponse3= mysql_query("UPDATE article SET stock=stock+".$quantite." WHERE id=".$article_selectionne."");
		echo '<center><font color=#286400>L\'article '.$article_commentaire.' ('.$collection_commentaire.') a bien été ajouté à l\'achat (x'.$quantite.') !<br/>Le stock de l\'article a également été édité.</font></center><br/><br/>';
		}
		else
			echo '<center><font color=#B22222>Impossible d\'ajouter l\'article car aucun achat ou article ou quantité n\'a été défini(e)...</font></center><br/><br/>';
	}


}
?>



<!----------------------------------------------------------------------------------TYPE------------------------------------------------------->
<?php

/* On établit la connexion à MySQL avec mysql_pconnect() plutôt qu'avec mysql_connect()

*  car on aura besoin de la connexion un peu plus loin dans le script */

if($connexion != false)

{

    $choixbase = mysql_select_db($bdd, $connexion);

    $sql1 = "SELECT `id`, `nom`".

    " FROM `type_collection`".

    " ORDER BY `nom`";

    $rech_regions = mysql_query($sql1);

    $code_region = array();

    $region = array();

    /* On active un compteur pour les régions */

    $nb_regions = 0;

    if($rech_regions != false)

    {

        while($ligne = mysql_fetch_assoc($rech_regions))

        {

            array_push($code_region, $ligne['id']);

            array_push($region, $ligne['nom']);



            /* On incrémente de compteur */

            $nb_regions++;

        }

    }

    ?>



<?php //Permet de garder les paramètres de la page si on change l'ordre !
	$ajout_coherence="<input type=\"hidden\" name=\"id_achat\" value=\"".$id_achat."\"/><input type=\"hidden\" name=\"type_de_requete\" value=\"".$type_de_requete."\"/>";
	if(isset($_POST['region']))
		$ajout_coherence.="<input type=\"hidden\" name=\"region\" value=\"".$_POST['region']."\"/>";
	if(isset($_POST['departement']))
		$ajout_coherence.="<input type=\"hidden\" name=\"departement\" value=\"".$_POST['departement']."\"/>";
	if(isset($_POST['ville']))
		$ajout_coherence.="<input type=\"hidden\" name=\"ville\" value=\"".$_POST['ville']."\"/>";
	if(isset($_POST['quantite']))
		$ajout_coherence.="<input type=\"hidden\" name=\"quantite\" value=\"".$_POST['quantite']."\"/>";
	if(isset($_POST['prix_unitaire']))
		$ajout_coherence.="<input type=\"hidden\" name=\"prix_unitaire\" value=\"".$_POST['prix_unitaire']."\"/>";		
?>



<?php //Affichage de l'achat + type de requète :
	if($id_achat>0){
		//Récupération des informations sur l'achat :	
			$reponse_req = mysql_query("SELECT vendeur,cout,date FROM achat WHERE id='$id_achat'");
			while ($donnees_req = mysql_fetch_assoc($reponse_req))
			{	
				$vendeur_aff=$donnees_req['vendeur'];
				$cout_aff=$donnees_req['cout'];
				$date_aff=$donnees_req['date'];
			}

		//Affichage des informations :
		if($type_de_requete=="creation")
			echo "<center><h2>Ajout des articles pour le nouvel achat (vendeur ".$vendeur_aff." pour ".$cout_aff."€ le ".$date_aff.") :</h2></center>";
		else
			echo "<center><h2>Ajout des articles pour l'achat (vendeur ".$vendeur_aff." pour ".$cout_aff."€ le ".$date_aff.") :</h2></center>";
	}
?>

<!----------TABLEAU : ---------->

<table> 
<tbody border="1">
<TR>
	<TH><p style="border-padding:100px;">Type</p></TH>
	<TH><p style="border-padding:100px;">Collection
		<form method="post" action="achat2.php"><input type="hidden" name="classement" value="collection_1"/><?php echo $ajout_coherence; ?><input type="image" src="img/arrow_top.png" alt="top" /></FORM>
		<form method="post" action="achat2.php"><input type="hidden" name="classement" value="collection_2"/><?php echo $ajout_coherence; ?><input type="image" src="img/arrow_bottom.png" alt="top" /></FORM>
	</p></TH>
	<TH><p style="border-padding:100px;">Article
		<form method="post" action="achat2.php"><input type="hidden" name="classement" value="article_1"/><?php echo $ajout_coherence; ?><input type="image" src="img/arrow_top.png" alt="top" /></FORM>
		<form method="post" action="achat2.php"><input type="hidden" name="classement" value="article_2"/><?php echo $ajout_coherence; ?><input type="image" src="img/arrow_bottom.png" alt="top" /></FORM>
	</p></TH>
	<TH><p style="border-padding:100px;">Quantité		
		<form method="post" action="achat2.php"><input type="hidden" name="classement" value="quantite_1"/><?php echo $ajout_coherence; ?><input type="image" src="img/arrow_top.png" alt="top" /></FORM>
		<form method="post" action="achat2.php"><input type="hidden" name="classement" value="quantite_2"/><?php echo $ajout_coherence; ?><input type="image" src="img/arrow_bottom.png" alt="top" /></FORM>
	</p></TH>
	<TH><p style="border-padding:100px;">Prix unitaire		
		<form method="post" action="achat2.php"><input type="hidden" name="classement" value="prix_unitaire_1"/><?php echo $ajout_coherence; ?><input type="image" src="img/arrow_top.png" alt="top" /></FORM>
		<form method="post" action="achat2.php"><input type="hidden" name="classement" value="prix_unitaire_2"/><?php echo $ajout_coherence; ?><input type="image" src="img/arrow_bottom.png" alt="top" /></FORM>
	</p></TH>	<TH>Ajout de l'article</TH>
</TR>
<form action="<?php echo($_SERVER['PHP_SELF']); ?>" method="post" id="chgdept">
<input type="hidden" name="classement" value="<?php echo $classement; ?>"/>
<TR>
<TD>
<select name="region" id="region" onchange="document.forms['chgdept'].submit();">

  <option value="0">- - - Type - - -</option>

    <?php

    for($i = 0; $i < $nb_regions; $i++)

    {

?>

  <option value="<?php echo($code_region[$i]); ?>"<?php echo((isset($idr) && $idr == $code_region[$i])?" selected=\"selected\"":null); ?>><?php echo($region[$i]); ?></option>

<?php

    }

    ?>

</select>
</TD>
<TD>
<!--------------------------------------------COLLECTION------------------------------------------------------------->
    <?php

    mysql_free_result($rech_regions);

    /* On commence par vérifier si on a envoyé un numéro de région et le cas échéant s'il est différent de -1 */



    if(isset($idr) && $idr != -1)

    {

        /* Cération de la requête pour avoir les départements de cette région */

        $sql2 = "SELECT `id`, `nom`".

        " FROM `collection`".

        " WHERE `id_type` = ". $idr ."".

        " ORDER BY `nom`;";

        if($connexion != false)

        {

            $rech_dept = mysql_query($sql2, $connexion);

            /* Un petit compteur pour les départements */

            $nd = 0;

            /* On crée deux tableaux pour les numéros et les noms des départements */

            $code_dept = array();

            $nom_dept = array();

            /* On va mettre les numéros et noms des départements dans les deux tableaux */

            while($ligne_dept = mysql_fetch_assoc($rech_dept))

            {

                array_push($code_dept, $ligne_dept['id']);

                array_push($nom_dept, $ligne_dept['nom']);

                $nd++;

            }

            /* Maintenant on peut construire la liste déroulante */

            ?>
<select name="departement" id="departement" onchange="document.forms['chgdept'].submit();">
<option value="0">- - - Collection - - -</option>
            <?php  

            for($d = 0; $d<$nd; $d++)

            {

                ?>

  <option value="<?php echo($code_dept[$d]); ?>"<?php echo((isset($collection_selectionne) && $collection_selectionne == $code_dept[$d])?" selected=\"selected\"":null); ?>><?php echo $nom_dept[$d]; ?></option>

                <?php

            }

?>

</select>
<?php

        }

        /* Un petit coup de balai */

        mysql_free_result($rech_dept);

    }
	else{
?>
<select name="departement" id="departement">
<option value="0">- - - Collection - - -</option>
</select>
<?php
	}

?>
</TD>
<!----------------------------------------------------ARTICLE------------------------------------------------------------------------------------------------------>
<TD>
   <?php


    /* On commence par vérifier si on a envoyé un numéro de région et le cas échéant s'il est différent de -1 */



    if(isset($idr2) && $idr2 != -1)
    {

        /* Cération de la requête pour avoir les départements de cette région */

        $sql2 = "SELECT `id`, `numero_article`, `nom`".

        " FROM `article`".

        " WHERE `id_collection` = ". $idr2 ."".

        " ORDER BY numero_article+0;";

        if($connexion != false)

        {

            $rech_ville = mysql_query($sql2, $connexion);

            /* Un petit compteur pour les départements */

            $nd = 0;

            /* On crée deux tableaux pour les numéros et les noms des départements */

            $code_ville = array();

            $nom_ville = array();
			
			$numero_ville = array();
            /* On va mettre les numéros et noms des départements dans les deux tableaux */

            while($ligne_ville = mysql_fetch_assoc($rech_ville))

            {

                array_push($code_ville, $ligne_ville['id']);

                array_push($nom_ville, $ligne_ville['nom']);

				array_push($numero_ville, $ligne_ville['numero_article']);

                $nd++;

            }

            /* Maintenant on peut construire la liste déroulante */

            ?>
<select name="ville" id="ville">
<option value="0">- - - Article - - -</option>

            <?php  

            for($d = 0; $d<$nd; $d++)

            {

                ?>

  <option value="<?php echo($code_ville[$d]); ?>"<?php echo((isset($article_selectionne) && $article_selectionne == $code_ville[$d])?" selected=\"selected\"":null); ?>><?php if($numero_ville[$d]!="") echo $numero_ville[$d].". ".$nom_ville[$d]; else echo $nom_ville[$d]; ?></option>

                <?php

            }

?>

</select>

<?php

        }

        /* Un petit coup de balai */

        mysql_free_result($rech_ville);

    }
	else{

?>
<select name="ville" id="ville">
<option value="0">- - - Article - - -</option>
</select>
<?php
		}
?>
</TD>
<TD><input type="text" name="quantite" value="<?php echo $quantite; ?>"/></TD>
<TD><input type="text" name="prix_unitaire" value="<?php echo $prix_unitaire; ?>"/></TD>
	<input type="hidden" name="id_achat" value="<?php echo $id_achat; ?>"/>
	<input type="hidden" name="type_de_requete" value="<?php echo $type_de_requete; ?>"/>
<TD><input type="submit" name="ok" id="ok" value="Ajouter l'article" /></TD>
</TR>
<!-------------------------------Affichage des articles associés à l'achat : -------------------------------------->
<?php	//Classement (pour l'affichage)	

	$sql_affichage="SELECT * FROM `lien_achat` WHERE `id_achat`='$id_achat'";
	
	switch($classement)
    {
      case "collection_2":
				$sql_affichage .= " ORDER BY collection_commentaire ASC;"; 
                break;
      case "collection_1" :
				$sql_affichage .= " ORDER BY collection_commentaire DESC;"; 
                break;
      case "article_2" :
				$sql_affichage .= " ORDER BY article_commentaire ASC;"; 
                break;
      case "article_1" :
				$sql_affichage .= " ORDER BY article_commentaire DESC;"; 
                break;
      case "quantite_2" :
				$sql_affichage .= " ORDER BY quantite ASC;"; 
                break;
      case "quantite_1" :
				$sql_affichage .= " ORDER BY quantite DESC;"; 
                break;      
      case "prix_unitaire_2" :
				$sql_affichage .= " ORDER BY prix_unitaire ASC;"; 
                break;
      case "prix_unitaire_1" :
				$sql_affichage .= " ORDER BY prix_unitaire DESC;"; 
                break;      
		default :
				$sql_affichage .= " ORDER BY collection_commentaire,article_commentaire;"; 
                break;
    }
	$reponse3 = mysql_query($sql_affichage, $connexion);
	while($donnees3 = mysql_fetch_assoc($reponse3))
	{
	$id_collectionn=$donnees3['id_collection'];
?>
<TR>
	<TD> <?php 
			$reponse99=mysql_query("SELECT type_commentaire FROM `collection` WHERE `id`='$id_collectionn';", $connexion);
			while($donnees99 = mysql_fetch_assoc($reponse99))
				echo $donnees99['type_commentaire'];
			?></TD>
	<TD><?php echo $donnees3['collection_commentaire']; ?></TD>
	<TD><?php echo $donnees3['article_commentaire']; ?></TD>
	<TD><?php echo $donnees3['quantite']; ?></TD>
	<TD><?php echo $donnees3['prix_unitaire']; ?></TD>
	<TD><form action="achat2.php" method="post">
		<input type="hidden" name="supprimer_lien_article" value="1"/>
		<input type="hidden" name="id_achat" value="<?php echo $id_achat; //Pour ne pas que l'utilisateur retombe sur une page disant que l'achat n'est pas valide?>"/>
		<input type="hidden" name="type_de_requete" value="<?php echo $type_de_requete; //Pour ne pas que l'utilisateur retombe sur une page disant que l'achat n'est pas valide?>"/>
		<input type="hidden" name="lien_article_a_suppr" value="<?php echo $donnees3['id']; ?>"/>		
		<input type="hidden" name="id_article_a_suppr" value="<?php echo $donnees3['id_article']; ?>"/>		
		<input type="submit" name="ok_suppr" value="Supprimer cet article de l'achat"  onclick="return(confirm('Etes-vous sûr de vouloir supprimer cet article de l\'achat ? (le stock de l\'article sera également décrémenté)'));"/>	
		<input type="hidden" name="classement" value="<?php echo $classement; ?>"/>
		</form>
	</TD>
</TR>
<?php
	}
?>

<!----------------------------Fin de l'affichage des articles associés à l'achat : ------------------------------->

</table> 
</tbody>



</form>
<?php

    /* Terminé, on ferme la connexion */

    mysql_close($connexion);

}

else

{

    /* Si on arrive là, c'est pas bon signe, il faut vérifier les

    * paramètres de connexion, mot de passe, serveur pas démarré etc... */

?>

<p>Un incident s'est produit lors de la connexion à la base de données, veuiillez essayer à nouveau ultérieurement.</p>

<?php

}

?>


<?php //------------Ajout des balises de fin------------
	include 'bottom_ansi.php';
?>
<!--
</body>

</html>-->