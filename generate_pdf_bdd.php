<?php
  error_reporting(0); //Supprimer les messages d'erreur (ATTENTION, CELA PEUT ETRE UNE MAUVAISE IDEE)

 //------------Ajout du header + menu------------
	$title_top="Génération d'un fichier PDF contenant les articles disponibles";
	$date_top="OK"; //Activation de la date
	include 'top.php'; 
	include 'config.php'; 
?>
<?php
	$content="";
	
   //requête SQL:
	if(isset($_GET["id_type"])){
		echo '<center>Veuillez ne pas éditer le fichier liste_collections.pdf se trouvant sur le bureau si vous voulez regénérer celui-ci.</center><br/>';
		$id_type=$_GET["id_type"];
		$sql0= "SELECT id,nom,prix FROM collection WHERE id_type='$id_type' ORDER BY nom ASC";


	$cnx = mysql_connect($database_ip, $database_username, $database_password)or die(mysql_error()); ;
  
    //sélection de la base de données:
    $db = mysql_select_db($database_name) ;
          
//exécution de la requête:
    $requete0 = mysql_query($sql0, $cnx) ;

?>

<?php
require('pdf/fpdf.php');

class PDF extends FPDF
{
// En-tête
function Header()
{
	// Logo
	$this->Image('favicon.png',10,10,10);
	// Police Arial gras 15
	$this->SetFont('Arial','B',15);
	// Décalage à droite
	$this->Cell(80);
	// Titre
	$date_du_jour=date("d")."/".date("m")."/".date("Y");
	$this->Cell(40,10,'Liste des articles disponibles le '.$date_du_jour.' :',0,0,'C');
	// Saut de ligne
	$this->Ln(20);
}

// Pied de page
function Footer()
{
	// Positionnement à 1,5 cm du bas
	$this->SetY(-15);
	// Police Arial italique 8
	$this->SetFont('Arial','I',8);
	// Numéro de page
	$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
}
}
// Instanciation de la classe dérivée
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
//$pdf->Cell(40,20,$content);
$pdf->SetFont('Times','',12);

$collection_aff="";
while($res = mysql_fetch_array($requete0)) {
	$id_collection=$res['id'];

    $requete_test = mysql_query("SELECT COUNT(*) AS compteur FROM article WHERE (stock > 0 AND id_collection='$id_collection')", $cnx ) ;
	$test=mysql_fetch_array($requete_test);
	//echo $test['compteur']."<br/>";
	if($test["compteur"]>0) //Si il y a des articles dispo :
	{
		$pdf->SetFont('Times','B',15);
		$pdf->Cell(0,10,"",0,4);	
		$pdf->Cell(0,10,$res['nom']." (".$res['prix']." euros l'article):",0,2);
		$pdf->SetFont('Times','',12);

		$sql = "SELECT nom,numero_article FROM article WHERE (stock > 0 AND id_collection='$id_collection') ORDER BY numero_article+0 ASC" ;
		$requete = mysql_query( $sql, $cnx ) ;
		
		while($res1 = mysql_fetch_array($requete)) {
			$pdf->Cell(0,10,$res1['numero_article'].". ".$res1['nom'],0,1);	
		}
	}
}

// Path from config file
$pdf->Output($path_for_pdf_output."/liste_collections_$id_type.pdf");
echo "<center><font color=#286400>Le fichier pdf contenant la liste des articles disponibles a bien été généré .<br/>Il se trouve actuellement sur le bureau (liste_collection_".$id_type.".pdf) !</font></center><br/>";

	}
	else{
		echo '<center><font color=#B22222>Vous n\'avez pas choisi de type de collection.<br/>Veuillez en choisir un afin de générer le fichier PDF !</font></center><br/>';
	}
?>
<?php //------------Ajout des balises de fin------------
	include 'bottom.php';
?>