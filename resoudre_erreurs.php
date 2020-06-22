<?php

	//PATCH 1 : VENTE
	$sql_check_patch_1 = "SELECT COUNT(last_edit) FROM debug WHERE last_edit='1'"; 
	$reponse_check_patch_1 = $bdd->query($sql_check_patch_1);
	if($reponse_check_patch_1->fetch()[0]==0){
		$reponse_check_patch_1->closeCursor();

		$ancien_vente="4. Livraison article";
		$new_vente="5. Client livre";
				$req = $bdd->prepare("UPDATE vente SET etat_vente= :etat_vente WHERE etat_vente = :ancien_vente");
				$req->execute(array(
				'etat_vente' => $new_vente,
				'ancien_vente' => $ancien_vente
				));
		$my_query2='INSERT INTO debug(last_edit,explication) VALUES ("1","Correction erreur dans les ventes (erreur avec echo)")';
		$bdd->query($my_query2); 		

		echo "<center><font color=#286400>Problème lié aux ventes corrigé (patch 1)</font></center><br/>";
	}
	
	//PACH 2 :
	/*$sql_check_patch_2 = "SELECT COUNT(last_edit) FROM debug WHERE last_edit='2'"; 
	$reponse_check_patch_2 = $bdd->query($sql_check_patch_2);
	if($reponse_check_patch_2->fetch()[0]==0){
		$reponse_check_patch_2->closeCursor();


		//CODE ICI
		
		$my_query2='INSERT INTO debug(last_edit,explication) VALUES ("2","Correction erreur")';
		$bdd->query($my_query2); 		

		echo "<center><font color=#286400>Problème corrigé (patch 2)</font></center><br/>";
	}*/
	
	//PACH 3 :
	/*$sql_check_patch_3 = "SELECT COUNT(last_edit) FROM debug WHERE last_edit='3'"; 
	$reponse_check_patch_3 = $bdd->query($sql_check_patch_3);
	if($reponse_check_patch_3->fetch()[0]==0){
		$reponse_check_patch_3->closeCursor();


		//CODE ICI
		
		$my_query2='INSERT INTO debug(last_edit,explication) VALUES ("3","Correction erreur")';
		$bdd->query($my_query2); 		

		echo "<center><font color=#286400>Problème corrigé (patch 3)</font></center><br/>";
	}*/

	//PACH 4 :
	/*$sql_check_patch_4 = "SELECT COUNT(last_edit) FROM debug WHERE last_edit='4'"; 
	$reponse_check_patch_4 = $bdd->query($sql_check_patch_4);
	if($reponse_check_patch_4->fetch()[0]==0){
		$reponse_check_patch_4->closeCursor();


		//CODE ICI
		
		$my_query2='INSERT INTO debug(last_edit,explication) VALUES ("4","Correction erreur")';
		$bdd->query($my_query2); 		

		echo "<center><font color=#286400>Problème corrigé (patch 4)</font></center><br/>";
	}*/	

	?>
