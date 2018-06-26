<?php
    

//Récuperation des images
    foreach($_FILES as $resultat){
        if(!is_array($resultat["tmp_name"])){
            $rst=move_uploaded_file($resultat["tmp_name"],"images/".$_POST["nom"].".png");
        }
    }
	if($rst==true){
		echo "Téléchargement effectué ";
	}else{
		echo "Erreur Téléchargement ";
	}