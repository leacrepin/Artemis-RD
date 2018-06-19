<?php

//Nombre d'image
$nb=1;
for($j=1;$j<10;$j++){
    if(file_exists("images/{$j}.png")){
		$nb++;
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
if(file_exists('images/'.$_GET["entreprise"].'.png')){
        $header->addImage('images/'.$_GET["entreprise"].'.png',array('height' => 25,'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::RIGHT));
}else{
        $header->addImage('images/defaut.png',array('height' => 25,'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::RIGHT));
}

//logo Entreprise
for($i=1; $i<=10; $i++){
    $pagedegarde->addText(" ",array('name' => 'Calibri', 'size' => '12', 'bold' => 'true'),[ 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER ]);
}
if(file_exists('images/'.$_GET["entreprise"].'.png')){
$pagedegarde->addImage('images/'.$_GET["entreprise"].'.png',array('height' => 79,'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER));
}else{
        $pagedegarde->addImage('images/defaut.png',array('height' => 79,'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER));
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
				$_GET["entreprise"]
		),
		'rStyle',[ 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER ]
);

$pagedegarde->addText(
		htmlspecialchars(
				$_GET["date"]
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
for($n=1;$nb>=$n;$n++){
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
?>