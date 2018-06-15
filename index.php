<?php if (empty($_POST)){ ?>


<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.light_blue-blue.min.css" /> 
    <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>	
    <link rel="stylesheet" href="style.css"/>
    <title>Faire mon fichier client</title>
  </head>

  <body>
	<h1>Faire mon fichier client</h1>
        
    <form id="header" action = "index.php" method = "POST" enctype="multipart/form-data">
        <div class="ajout">
		<div class="ajout1">
                    <select id="date" class="select_stats" name="date">
<?php
		$annee_ok = 1970;
		$mois_ok = 1;
 
		$Mois = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
		for($aa=date('Y'); $aa>=$annee_ok; $aa--) // annee
		{
			for($mm=12; $mm>=1; $mm--) // mois
			{
				// entre mois/annee en cours (date('Y')/date('n')) et $mois_ok/$annee_ok enregistr�
				if( !($aa==date('Y') && $mm>date('n')) && !($aa==$annee_ok && $mm<$mois_ok) ) {
?>		<option value="<?php echo $Mois[$mm-1].' '.$aa; ?>" ><?php echo $Mois[$mm-1].' '.$aa; ?></option>
<?php			} // fin if
			} // fin for mois
		} // fin for annee
?>
	</select><br>
                        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
				<input type = "text" name = "entreprise" class="mdl-textfield__input" id="entreprise">
				<label class="mdl-textfield__label" for="entreprise"> Nom de l'entreprise :</label>
			</div></div></div>
        
        <h5>Veuillez mettre le logo client</h5><input type = "file" name = "image1">
	  <br><h5>Veuillez mettre les charts "Bilan des Actions"</h5>
          <input id="image" type="file" name="image[]" multiple>
	  <br>
	  <br><br><br>
      <input type = "submit" class="mdl-button mdl-button--raised mdl-button--colored">
      <input type = "reset" class="mdl-button mdl-button--raised mdl-button--colored">
    </form>

  </body>
</html>

<?php }else{
    
  
//Supprimer les anciennes images
for($j=1;$j<10;$j++){
    if(file_exists("images/{$j}.png")){
	unlink("images/{$j}.png");
    }
}

//Récuperation des images
$nb=1;
    foreach($_FILES as $resultat){
        if(!is_array($resultat["tmp_name"])){
            $rst=move_uploaded_file($resultat["tmp_name"],"images/".$nb.".png");
            $nb++;
        }else{
            foreach($resultat["tmp_name"] as $image){
                $rst=move_uploaded_file($image,"images/".$nb.".png");
                $nb++;
            }
        }
	
    }
                                        
//REQUIRE
require_once 'vendor/autoload.php';

//Création du doc
$phpWord = new \PhpOffice\PhpWord\PhpWord();

//Langue
$fr=new \PhpOffice\PhpWord\Style\Language(\PhpOffice\PhpWord\Style\Language::FR_FR);
$phpWord->getSettings()->setThemeFontLang($fr);

//Fonts Texte

$font=$phpWord->addFontStyle('rStyle', array('color'=> '313131','name' => 'Calibri', 'size' => '36','spaceBefore' => 0.32, 'spaceAfter' => 0.32));
$font->setSmallCaps();
$font2=$phpWord->addFontStyle('titre table matiere', array('color'=> '1A9386','name' => 'Calibri', 'size' => '14','spaceBefore' => 0.32, 'spaceAfter' => 0.32));
$font2->setSmallCaps();
$font4=$phpWord->addFontStyle('Artemis-RD',array('color'=> '1A9386','name' => 'Calibri', 'size' => '10'));
$font4->setBold();
$font5=$phpWord->addFontStyle('LigneVerte',array('color'=> '1A9386','name' => 'Calibri', 'size' => '10.5'));
$font5->setBold();
$font6=$phpWord->addFontStyle('date', array('color'=> '1A9386','name' => 'Calibri', 'size' => '36', 'bold' => 'true','spaceBefore' => 0.32, 'spaceAfter' => 0.32));
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
$section = $phpWord->addSection();

//Numérotation des pages
$footer=$section->addFooter();
$footer->addPreserveText("{PAGE}");

//Logo artemis-RD footer
$footer->addImage('images/ArtemisRD.png',array('height' => 50, 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER));

//Logo entreprise header
$header = $section->addHeader();
if(file_exists("images/1.png")){
        $header->addImage('images/1.png',array('height' => 25,'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::RIGHT));
}else{
        $header->addImage('images/defaut.png',array('width' => 25,'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::RIGHT));
}

//logo Entreprise
for($i=1; $i<=10; $i++){
    $pagedegarde->addText(" ",array('name' => 'Calibri', 'size' => '12', 'bold' => 'true'),[ 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER ]);
}
if(file_exists("images/1.png")){
        $pagedegarde->addImage('images/1.png',array('height' => 79,'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER));
}else{
        $pagedegarde->addImage('images/defaut.png',array('width' => 150,'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER));
}


// Page de garde

for($i=1; $i<=6; $i++){
    $pagedegarde->addText(" ",array('name' => 'Calibri', 'size' => '12', 'bold' => 'true'),[ 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER ]);
}

$pagedegarde->addText(
		htmlspecialchars(
				'Bilan Mensuel SI'
		),
		'rStyle',[ 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]
);


$pagedegarde->addText(
		htmlspecialchars(
				$_POST["entreprise"]
		),
		array('color'=> '313131','name' => 'Calibri', 'size' => '36','spaceBefore' => 0.32, 'spaceAfter' => 0.32),[ 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER ]
);

$pagedegarde->addText(
		htmlspecialchars(
				$_POST["date"]
		),
		'date',[ 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER ]
);

//Barre ArtemisRD
for($i=1; $i<=17; $i++){
    $pagedegarde->addText(" ",array('name' => 'Calibri', 'size' => '12', 'bold' => 'true'),[ 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER ]);
}
$pagedegarde->addText(htmlspecialchars('Artemis-RD'),'Artemis-RD',[ 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER ]);
$pagedegarde->addText('+33 (0)9 52 31 26 70 - 8 quai de la Fontaine, 30000 Nîmes',
		array('name' => 'Calibri', 'size' => '10'),[ 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER ]
    );
$pagedegarde->addText(htmlspecialchars("____________________________________________________________"),'LigneVerte',[ 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER ]);
$pagedegarde->addImage('images/ArtemisRD.png',array('width' => 70, 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER));



//table des matières
$section->addText(htmlspecialchars("______________________________________________________________________________________"),'LigneVerte',[ 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER ]);
$section->addText("Table des matières",'titre table matiere',[ 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER ]);
$section->addText(htmlspecialchars("______________________________________________________________________________________"),'LigneVerte',[ 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER ]);
$section->addTextBreak(2);
$toc2 = $section->addTOC($font3,$fontStyle);
$section->addText('PS: Veuillez réactualiser la table des matières si vous voulez les pages ainsi que numéros, pensez à tout remettre en gras comme vous le souhaitez.');
$section->addPageBreak();

// Body
$section->addTitle('Bilan des actions', 1);
$section->addTextBreak(2);
for($n=2;$nb>=$n;$n++){
    if(file_exists("images/{$n}.png")){
        $section->addImage('images/'.$n.'.png',array('height' => 280,'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER));
        $section->addTextBreak(5);
    }else{
        //$section->addImage('images/defaut.png',array('width' => 150,'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER));
    }
}
$section->addTextBreak(2);
$section->addTitle('Incidents Critiques et Majeurs', 1);
$section->addText('Some text...');
$section->addTextBreak(2);
$section->addTitle('Incidents Critiques', 1);
$section->addText('Some text...');
$section->addTextBreak(2);
$section->addTitle('Incidents Mineurs', 1);
$section->addText('Some text...');
$section->addTextBreak(2);
$section->addTitle('Changement', 1);
$section->addText('Some text...');
$section->addTextBreak(2);
$section->addTitle('Actions de suivi', 1);
$section->addText('Some text...');
$section->addTextBreak(2);

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment;filename="Bilan_Mensuel_SI.docx"');

$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord);
$objWriter->save('php://output');
    
    
    
}