<!-- Début page top.php-->
<!DOCTYPE html>
<html>
	<head>
		<meta charset="ANSI"/>	<!-- type de codage -->
		<meta name="author" lang="fr" content="<?php if(isset($author_top)) echo $author_top.' et Quentin Comte-Gaz'; else echo 'Quentin Comte-Gaz'; ?>"/>
		<link rel="icon" type="image/png" href="favicon.png" />
		<link rel="stylesheet" href="styles/style_tableau.css" /> 	 <!-- insertion de la feuille css styles/style.css -->
		<link rel="stylesheet" href="styles/style_menu_horizontal.css" /> 	 <!-- insertion de la feuille css styles/style.css -->
		<?php //Ajout de la fonction de date si necessaire
		if(isset($date_top))
			if($date_top=="OK")
				echo '
						<link rel="stylesheet" href="date/jquery-ui.css">
						<script src="date/jquery-1.9.1.js"></script>
						<script src="date/jquery-ui.js"></script>
						<script>
						$(function() {
						$( ".datepicker" ).datepicker({
								dateFormat: \'yy-mm-dd\',
								altField: ".date_alternate",
								altFormat: "yy-mm-dd"
							});
						});

						</script>';
		?>
		<title><?php 
				if(isset($title_top)) 
					echo $title_top.' (Stockmalin v1.40)'; 
				else 
					echo 'Page créee par Quentin Comte-Gaz (Stockmalin v1.40)';
		?></title>
	</head>
	
	<body onload="Choix(activate_load)">
	<center><h1><?php echo $title_top; ?></h1></center>
	<!-- Début du menu horizontal -->	
	<ul id="menu">  
		<li><a href="index.php"><img border="0" id="img_menu" src="img/accueil.png" alt="acceuil" /> Accueil</a></li>
		<li><a href="achat.php"><img border="0" id="img_menu" src="img/achat.png" alt="achats" /> Achats</a></li>
		<li><a href="fournisseur.php"><img border="0" id="img_menu" src="img/fournisseur.png" alt="fournisseurs" /> Fournisseurs</a></li>
		<br/><br/><li><a href="vente.php"><img border="0" id="img_menu" src="img/vente.png" alt="ventes" /> Ventes</a></li>  
		<li><a href="clients.php"><img border="0" id="img_menu" src="img/client.png" alt="clients" /> Clients</a></li>  
		<li><a href="#"><img border="0" id="img_menu" src="img/article_collection_type.png" alt="Gestion de stocks"/> Gestion de stocks</a>  
			<ul>  
				<li><a href="article.php"><img border="0" id="img_menu" src="img/article_collection_type.png" alt="articles"/> Articles</a></li>  
				<li><a href="collection.php"><img border="0" id="img_menu" src="img/article_collection_type.png" alt="collections"/> Collections</a></li>  
				<li><a href="type.php"><img border="0" id="img_menu" src="img/article_collection_type.png" alt="types"/> Types</a></li>  
			</ul>  
		</li>  
	</ul> 
	
	<!-- Fin du menu horizontal-->
	
<br/><br/>

<!-- Fin page top.php-->
