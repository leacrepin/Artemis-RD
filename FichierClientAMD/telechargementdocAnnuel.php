<?php

include ("../../../../inc/includes.php");
include ("../../../../inc/config.php");

Session::checkLoginUser();
Session::checkRight("profile", READ);

global $DB;

//Fonction pour convertir le temps
function convertirTemps($duree){
	$jour=intval(($duree / 3600) / 24);
	$heures=intval(($duree / 3600)% 24);
	$minutes=intval(($duree % 3600) / 60);
	$secondes=intval((($duree % 3600) % 60));
	return(" ".$jour."j ".$heures."h ".$minutes."m ".$secondes."s ");
}

//BEGIN BDD

$datas = "BETWEEN '".$_GET["date1"]." 00:00:00' AND '".$_GET["date2"]." 23:59:59'";
$date = "du ".$_GET["date1"]." au ".$_GET["date2"];
$date2 = $_GET["date1"]."_".$_GET["date2"];
$id_ent = $_GET["id"];

//Calcul de la date (si il s'agit d'un mois en particulier ou non)

function bissextile($a){
	if($a%400==0||(($a%4==0)&($a%100!=0))){
		return(29);//année bissextile
	}else{
		return(28);//année non bissextile
	}
}

$moisLettres=array('Janvier','Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');

function nombreDeJour($mois, $a){
	if($mois==2){
		return(bissextile($a));
	}else{//mois!=2
		if($mois==4 ||$mois==6 ||$mois==9 ||$mois==11){
			return(30);
		}else{
			return(31);
		}
	}
}
$jour1=substr($_GET["date1"], -2);
$jour2=substr($_GET["date2"], -2);
$mois1=substr($_GET["date1"], -5, 2);
$mois2=substr($_GET["date2"], -5, 2);
$année1=substr($_GET["date1"], -10,4);
$année2=substr($_GET["date2"], -10,4);

if($jour1=="01" && $jour2==nombreDeJour((int)$mois1,(int)$année1) && $année1==$année2 && $mois1==$mois2){
	$date = $moisLettres[(int) $mois1-1].' '.$année1;
	$date2 = $mois1.''.$année1;
}
if($jour1=="01" && $jour2=="31" && $mois1=="01" && $mois2=="12" && $année1==$année2){
	$date = $année1;
	$date2 = $année1;
}
//Base de donnée -> Recherche s'il y a des problèmes de catégorie

$query2 = "
			SELECT COUNT(id) as nb
			FROM glpi_tickets 
			WHERE glpi_tickets.date ".$datas." 
			AND glpi_tickets.is_deleted = 0
			AND glpi_tickets.entities_id = ".$id_ent."
			AND glpi_tickets.type = 1
			AND glpi_tickets.itilcategories_id = 0
			AND glpi_tickets.status = '6'
			ORDER BY date";

			$erreur = $DB->query($query2) or die('erro');
			$ligne=$DB->fetch_assoc($erreur);
			if($ligne['nb']!=0){
				$query2 = "
				SELECT id
				FROM glpi_tickets 
				WHERE glpi_tickets.date ".$datas." 
				AND glpi_tickets.is_deleted = 0
				AND glpi_tickets.entities_id = ".$id_ent."
				AND glpi_tickets.type = 1
				AND glpi_tickets.itilcategories_id = 0
				AND glpi_tickets.status = '6'
				ORDER BY date";

				$erreur = $DB->query($query2) or die('erro');
				$str="Erreur: Il manque des catégories !(Ticket(s):";
				while($ligne=$DB->fetch_assoc($erreur)){
					$str=$str." ".$ligne['id'];
				}
				die($str.")");
			}

//Base de donnée -> Liste des incidents critiques et majeurs
$query2 = "
			SELECT name, solve_delay_stat as temps, actiontime as temps2
			FROM glpi_tickets
			WHERE glpi_tickets.date ".$datas."
			AND glpi_tickets.is_deleted = 0
			AND glpi_tickets.entities_id = ".$id_ent."
			AND priority = 3
			AND type = 1
			AND glpi_tickets.status = '6'
			ORDER BY date";

			$critiqueetmajeur = $DB->query($query2) or die('erro');

//Base de donnée -> Somme du temps des incidents critiques et majeurs
$query2 = "
			SELECT SUM(actiontime) as temps, AVG(solve_delay_stat) as moyenne
			FROM glpi_tickets
			WHERE glpi_tickets.date ".$datas."
			AND glpi_tickets.is_deleted = 0
			AND glpi_tickets.entities_id = ".$id_ent."
			AND priority = 3
			AND glpi_tickets.status = '6'
			AND type = 1";

			$critiqueetmajeurtemps = $DB->query($query2) or die('erro');

//Base de donnée -> Liste des incidents critique
$query = "
			SELECT name, actiontime as temps
			FROM glpi_tickets
			WHERE glpi_tickets.date ".$datas."
			AND glpi_tickets.is_deleted = 0
			AND glpi_tickets.entities_id = ".$id_ent."
			AND priority = 2
			AND type = 1
			AND glpi_tickets.status = '6'
			ORDER BY date";

			$critique = $DB->query($query) or die('erro');
			
//Base de donnée -> Somme du temps des incidents critique
$query = "
			SELECT SUM(actiontime) as temps, AVG(solve_delay_stat) as moyenne
			FROM glpi_tickets
			WHERE glpi_tickets.date ".$datas."
			AND glpi_tickets.is_deleted = 0
			AND glpi_tickets.entities_id = ".$id_ent."
			AND priority = 2
			AND glpi_tickets.status = '6'
			AND type = 1";

			$critiquetemps = $DB->query($query) or die('erro');

//Base de donnée -> Liste des incidents mineurs
$query3 = "
			SELECT name, actiontime as temps
			FROM glpi_tickets
			WHERE glpi_tickets.date ".$datas."
			AND glpi_tickets.is_deleted = 0
			AND glpi_tickets.entities_id = ".$id_ent."
			AND priority = 1
			AND type = 1
			AND glpi_tickets.status = '6'
			ORDER BY date";

			$mineur = $DB->query($query3) or die('erro');
			
//Base de donnée -> Somme du temps des incidents mineurs
$query3 = "
			SELECT SUM(actiontime) as temps, AVG(solve_delay_stat) as moyenne
			FROM glpi_tickets
			WHERE glpi_tickets.date ".$datas."
			AND glpi_tickets.is_deleted = 0
			AND glpi_tickets.entities_id = ".$id_ent."
			AND priority = 1
			AND glpi_tickets.status = '6'
			AND type = 1";

			$mineurtemps = $DB->query($query3) or die('erro');
			
//Base de donnée -> Liste des changements
$query3 = "
			SELECT name, actiontime as temps
			FROM glpi_tickets
			WHERE glpi_tickets.date ".$datas."
			AND glpi_tickets.is_deleted = 0
			AND glpi_tickets.entities_id = ".$id_ent."
			AND priority = 1
			AND type = 5
			AND glpi_tickets.status = '6'
			ORDER BY date";

			$changement = $DB->query($query3) or die('erro');
			
//Base de donnée -> Somme du temps des changements
$query3 = "
			SELECT SUM(actiontime) as temps, AVG(solve_delay_stat) as moyenne
			FROM glpi_tickets
			WHERE glpi_tickets.date ".$datas."
			AND glpi_tickets.is_deleted = 0
			AND glpi_tickets.entities_id = ".$id_ent."
			AND priority = 1
			AND glpi_tickets.status = '6'
			AND type = 5";

			$changementtemps = $DB->query($query3) or die('erro');
			
//Base de donnée -> Liste des suivis
$query3 = "
			SELECT glpi_tickettasks.content as textesuivi, glpi_tickettasks.actiontime as temps
			FROM glpi_tickets
			JOIN glpi_tickettasks ON glpi_tickets.id=glpi_tickettasks.tickets_id
			WHERE glpi_tickets.date ".$datas."
			AND glpi_tickets.is_deleted = 0
			AND glpi_tickets.entities_id = ".$id_ent."
			AND glpi_tickets.type = 3
			AND glpi_tickets.status = '6'
			ORDER BY glpi_tickets.date";

			$suivi = $DB->query($query3) or die('erro');
			
//Base de donnée -> Suivis somme du temps
$query3 = "
			SELECT SUM(glpi_tickettasks.actiontime) as temps
			FROM glpi_tickets
			JOIN glpi_tickettasks ON glpi_tickets.id=glpi_tickettasks.tickets_id
			WHERE glpi_tickets.date ".$datas."
			AND glpi_tickets.is_deleted = 0
			AND glpi_tickets.status = '6'
			AND glpi_tickets.entities_id = ".$id_ent."
			AND glpi_tickets.type = 3";

			$tempssuivi = $DB->query($query3) or die('erro');

//Nombre d'image
$nb=6;

                                        
//REQUIRE DOC
require_once 'vendor/autoload.php';

//Création du doc
$phpWord = new \PhpOffice\PhpWord\PhpWord();

//Langue
$fr=new \PhpOffice\PhpWord\Style\Language(\PhpOffice\PhpWord\Style\Language::FR_FR);
$phpWord->getSettings()->setThemeFontLang($fr);

//Fonts Texte

$font=$phpWord->addFontStyle('rStyle', array('color'=> '313131','name' => 'Calibri', 'size' => '36'));
$font->setSmallCaps();
$font2=$phpWord->addFontStyle('titre table matiere', array('color'=> '0099B1','name' => 'Calibri', 'size' => '14'));
$font2->setSmallCaps();
$font4=$phpWord->addFontStyle('Artemis-RD',array('color'=> '0099B1','name' => 'Calibri', 'size' => '10'));
$font4->setBold();
$font5=$phpWord->addFontStyle('LigneVerte',array('color'=> '0099B1','name' => 'Calibri', 'size' => '10.5'));
$font5->setBold();
$font6=$phpWord->addFontStyle('rStyle2', array('color'=> '0099B1','name' => 'Calibri', 'size' => '36'));
$font6->setSmallCaps();

//Numérotation Titres
$phpWord->addNumberingStyle(
    'hNum',
    array('type' => 'multilevel', 'levels' => array(
        array('pStyle' => 'Heading1', 'format' => 'decimal', 'text' => '%1.'),
        array('pStyle' => 'Heading2', 'format' => 'decimal', 'text' => '%1.%2'),
        array('pStyle' => 'Heading3', 'format' => 'decimal', 'text' => '%1.%2.%3'),
        )
    )
);


// Define the TOC font style
$fontStyle = array('spaceAfter' => 60, 'size' => 12);
$fontStyle2 = array('size' => 12,'color'=> '313131','name' => 'Calibri');
$font3=$phpWord->addFontStyle('TOCStyle',$fontStyle2);
$font3->setBold();


// Add title style
$phpWord->addTitleStyle(1, array('color'=> '313131','size' => 16), array('numStyle' => 'hNum', 'numLevel' => 0));


//Section
$pagedegarde = $phpWord->addSection();
$tabledesmatieres = $phpWord->addSection();
$section = $phpWord->addSection();

//Numérotation des pages
$footer=$section->addFooter();
$footer->addPreserveText("{PAGE}");

//Logo artemis-RD footer
$footer->addImage('images/AMD.png',array('height' => 50, 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER));

//Logo entreprise header
$header = $section->addHeader();
if(file_exists('images/'.$_GET["entreprise"].'.png')){
        $header->addImage('images/'.$_GET["entreprise"].'.png',array('height' => 25,'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER));
}else{
        $header->addImage('images/defaut.png',array('height' => 25,'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER));
}






// -----------------------Page de garde----------------------------
//logo Entreprise
$pagedegarde->addTextBreak(5);
if(file_exists('images/'.$_GET["entreprise"].'.png')){
$pagedegarde->addImage('images/'.$_GET["entreprise"].'.png',array('height' => 79,'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER));
}else{
        $pagedegarde->addImage('images/defaut.png',array('height' => 79,'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER));
}


$pagedegarde->addTextBreak(3);

$pagedegarde->addText(
		htmlspecialchars(
				'Bilan Annuel SI'
		),
		'rStyle',[ 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]
);


$pagedegarde->addText(
		htmlspecialchars(
				$_GET["entreprise"]
		),
		'rStyle',[ 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER ]
);

$pagedegarde->addText(
		htmlspecialchars(
				$date
		),
		'rStyle2',[ 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER ]
);

/*
//Barre ArtemisRD
$pagedegarde->addTextBreak(8);
$pagedegarde->addText(htmlspecialchars('Artemis-RD'),'Artemis-RD',[ 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER ,'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(0)]);
$pagedegarde->addText('+33 (0)9 52 31 26 70 - 8 quai de la Fontaine, 30000 Nîmes',
		array('name' => 'Calibri', 'size' => '10', 'color'=> '57585A'),[ 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER ,'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(0)]
    );
$pagedegarde->addText(htmlspecialchars("____________________________________________________________"),'LigneVerte',[ 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER ,'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(0),'spaceBefore' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(0)]);
$pagedegarde->addImage('images/ArtemisRD.png',array('width' => 70, 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER,'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(0)));
*/


//------------------Table des matières--------------------

$tabledesmatieres->addText(htmlspecialchars("______________________________________________________________________________________"),'LigneVerte',[ 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER ]);
$tabledesmatieres->addText("Table des matières",'titre table matiere',[ 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER ]);
$tabledesmatieres->addText(htmlspecialchars("______________________________________________________________________________________"),'LigneVerte',[ 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER ]);
$tabledesmatieres->addTextBreak(1);
$toc2 = $tabledesmatieres->addTOC($font3,$fontStyle);
$tabledesmatieres->addText('PS: Veuillez réactualiser la table des matières si vous voulez les pages ainsi que numéros, pensez à tout remettre en gras comme vous le souhaitez.');
$tabledesmatieres->addPageBreak();

// -----------------------Body----------------------------


//Bilan des actions
$section->addTitle('Bilan des actions', 1);
$section->addTextBreak(1);
for($n=1;$nb>=$n;$n++){
    if(file_exists("images/{$n}.png")){
        $section->addImage('images/'.$n.'.png',array('height' => 280,'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER));
        $section->addTextBreak(1);
    }
}
$section->addTextBreak(1);

//Style tableaux
$tableStyle = array(
	'cantSplit' => true,
    'borderColor' => '000000',
	'borderSize'  => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(0.5),
    'borderTopSize'  => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(0.6),
	'borderLeftSize'  => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(0.6),
	'borderRightSize'  => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(0.6),
	'borderBottomSize'  => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(0.6)
);
$phpWord->addTableStyle('myTable', $tableStyle);
//Gris->'8F8F8F' BleuArtemis->'0099B1' VertAMD->'006A51'
$cellRowSpan = array('bgColor' => '006A51');//Colonne de gauche
$cellRowSpan2 = array('bgColor' => '006A51');//Colonne de droite


//Incidents critiques et majeurs
$temps=$DB->fetch_assoc($critiqueetmajeurtemps);
if($temps['temps']!=0){
$section->addTitle('Incidents Critiques et Majeurs', 1);
$section->addTextBreak(1);

$table = $section->addTable('myTable');

$table->addRow();
$table->addCell(7000, $cellRowSpan)->addText("Etiquettes de lignes",array('color'=> '313131','size' => 12));
$table->addCell(3000, $cellRowSpan2)->addText("Temps passé",array('color'=> '313131','size' => 12));

while($ligne=$DB->fetch_assoc($critiqueetmajeur)){
	$table->addRow();
	$table->addCell(7000)->addText(stripslashes($ligne['name']),array('color'=> '313131','size' => 12));
	$table->addCell(3000)->addText(convertirTemps($ligne['temps']),array('color'=> '313131','size' => 12));
}
$table->addRow();
$table->addCell(7000, $cellRowSpan)->addText("Total général",array('color'=> '313131','size' => 12));
$table->addCell(3000)->addText(convertirTemps($temps['temps']),array('color'=> '313131','size' => 12));

$section->addTextBreak(1);
$table = $section->addTable('myTable');

$table->addRow();
$table->addCell(7000,$cellRowSpan2)->addText("Moyenne de Résolution",array('color'=> '313131','size' => 12));
$table->addCell(3000)->addText(convertirTemps($temps['moyenne']),array('color'=> '313131','size' => 12));

$section->addTextBreak(1);
}


//Incidents critiques
$temps=$DB->fetch_assoc($critiquetemps);
if($temps['temps']!=0){
$section->addTitle('Incidents Critiques', 1);
$section->addTextBreak(1);
$table = $section->addTable('myTable');

$table->addRow();
$table->addCell(7000, $cellRowSpan)->addText("Etiquettes de lignes",array('color'=> '313131','size' => 12));
$table->addCell(3000, $cellRowSpan2)->addText("Temps passé",array('color'=> '313131','size' => 12));

while($ligne=$DB->fetch_assoc($critique)){
	$table->addRow();
	$table->addCell(7000)->addText(stripslashes($ligne['name']),array('color'=> '313131','size' => 12));
	$table->addCell(3000)->addText(convertirTemps($ligne['temps']),array('color'=> '313131','size' => 12));
}
$table->addRow();
$table->addCell(7000, $cellRowSpan)->addText("Total général",array('color'=> '313131','size' => 12));
$table->addCell(3000)->addText(convertirTemps($temps['temps']),array('color'=> '313131','size' => 12));

$section->addTextBreak(1);
$table = $section->addTable('myTable');

$table->addRow();
$table->addCell(7000,$cellRowSpan2)->addText("Moyenne de Résolution",array('color'=> '313131','size' => 12));
$table->addCell(3000)->addText(convertirTemps($temps['moyenne']),array('color'=> '313131','size' => 12));

$section->addTextBreak(1);
}

//Incidents mineurs
$temps=$DB->fetch_assoc($mineurtemps);
if($temps['temps']!=0){
$section->addTitle('Incidents Mineurs', 1);
$section->addTextBreak(1);
$table = $section->addTable('myTable');

$table->addRow();
$table->addCell(7000, $cellRowSpan)->addText("Etiquettes de lignes",array('color'=> '313131','size' => 12));
$table->addCell(3000, $cellRowSpan2)->addText("Temps passé",array('color'=> '313131','size' => 12));

while($ligne=$DB->fetch_assoc($mineur)){
	$table->addRow();
	$table->addCell(7000)->addText(stripslashes($ligne['name']),array('color'=> '313131','size' => 12));
	$table->addCell(3000)->addText(convertirTemps($ligne['temps']),array('color'=> '313131','size' => 12));
}
$table->addRow();
$table->addCell(7000, $cellRowSpan)->addText("Total général",array('color'=> '313131','size' => 12));
$table->addCell(3000)->addText(convertirTemps($temps['temps']),array('color'=> '313131','size' => 12));

$section->addTextBreak(1);
$table = $section->addTable('myTable');

$table->addRow();
$table->addCell(7000,$cellRowSpan2)->addText("Moyenne de Résolution",array('color'=> '313131','size' => 12));
$table->addCell(3000)->addText(convertirTemps($temps['moyenne']),array('color'=> '313131','size' => 12));

$section->addTextBreak(1);
}


//Changement
$temps=$DB->fetch_assoc($changementtemps);
if($temps['temps']!=0){
$section->addTitle('Changement', 1);
$section->addTextBreak(1);
$table = $section->addTable('myTable');

$table->addRow();
$table->addCell(7000, $cellRowSpan)->addText("Etiquettes de lignes",array('color'=> '313131','size' => 12));
$table->addCell(3000, $cellRowSpan2)->addText("Temps passé",array('color'=> '313131','size' => 12));

while($ligne=$DB->fetch_assoc($changement)){
	$table->addRow();
	$table->addCell(7000)->addText(stripslashes($ligne['name']),array('color'=> '313131','size' => 12));
	$table->addCell(3000)->addText(convertirTemps($ligne['temps']),array('color'=> '313131','size' => 12));
}
$table->addRow();
$table->addCell(7000, $cellRowSpan)->addText("Total général",array('color'=> '313131','size' => 12));
$table->addCell(3000)->addText(convertirTemps($temps['temps']),array('color'=> '313131','size' => 12));

$section->addTextBreak(1);
$table = $section->addTable('myTable');

$table->addRow();
$table->addCell(7000,$cellRowSpan2)->addText("Moyenne de Résolution",array('color'=> '313131','size' => 12));
$table->addCell(3000)->addText(convertirTemps($temps['moyenne']),array('color'=> '313131','size' => 12));

$section->addTextBreak(1);
}else{
	$section->addTitle('Changement', 1);
	$section->addTextBreak(1);
	$section->addText("Pas de changement sur la période",array('color'=> '313131','size' => 12));
	$section->addTextBreak(1);
}

//Actions de suivi

$section->addTitle('Actions de suivi', 1);
$section->addTextBreak(1);
$table = $section->addTable('myTable');

$table->addRow();
$table->addCell(8000, $cellRowSpan)->addText("Etiquettes de lignes",array('color'=> '313131','size' => 12));
$table->addCell(2000, $cellRowSpan2)->addText("Temps passé",array('color'=> '313131','size' => 12));

$table->addRow();
$table->addCell(8000)->addText("SUIVI",array('color'=> '313131','size' => 12));
$ligne=$DB->fetch_assoc($tempssuivi);
$table->addCell(2000)->addText(($ligne['temps']/3600)." h",array('color'=> '313131','size' => 12));

while($ligne=$DB->fetch_assoc($suivi)){
	$table->addRow();
	$text=$ligne['textesuivi'];
	$processed = htmlentities($text);
   if($processed == $text){//NON HTML
	   $text=preg_replace('~\R~u', '</w:t><w:br/><w:t>', $text);
	   $table->addCell(8000)->addText(stripslashes($text),array('color'=> '313131','size' => 12));
   }else{//HTML
		$text=preg_replace('~\R~u', '<br/>', $text);
		$text='<body style="color:313131;font-size:16px;">'.stripslashes($text).'</body>';
		$text=html_entity_decode($text);
		$cell = $table->addCell(8000); \PhpOffice\PhpWord\Shared\Html::addHtml($cell, $text);
   }
	$table->addCell(2000)->addText(($ligne['temps']/3600)." h",array('color'=> '313131','size' => 12));
}

$section->addTextBreak(2);
$filename='TDB_'.$_GET["entreprise"].'_'.$date2.'.docx';
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment;filename="'.$filename.'"');

$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord);
$objWriter->save('php://output');
?>