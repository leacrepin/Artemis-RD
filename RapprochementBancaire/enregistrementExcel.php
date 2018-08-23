<?php 

//Supprimer les anciens Excels
if(file_exists("Excels/Fichier A.xls")){
	unlink("Excels/Fichier A.xls");
}
if(file_exists("Excels/Fichier B.xls")){
	unlink("Excels/Fichier B.xls");
}

//Récuperation des fichiers
$nb=1;
foreach($_FILES as $resultat){
	if($nb==2){
		$rst=move_uploaded_file($resultat["tmp_name"],"Excels/Fichier B.xls");
	}else{
		$rst=move_uploaded_file($resultat["tmp_name"],"Excels/Fichier A.xls");
		$nb++;
	}
}

//Lancement de ma macro
//Excel
$path = realpath(dirname(__FILE__)); 
$FILENAME=$path."\Excels\Fichier Anomalies.xls"; 
$excel=new COM("Excel.application"); //Instanciation de l'objet COM
$excel->Workbooks->Open($FILENAME);
$excel->Visible=1; 
try{
    $excel->Run('cmdAnalyse_Click'); //lance la macro
}catch(Exception $e){echo "bug";}
$excel->Workbooks->Close(); 
$excel->Quit(); 