<?php
    

//Récuperation des images
    foreach($_FILES as $resultat){
        if(!is_array($resultat["tmp_name"])){
			if(file_exists("images/".$_POST["nom"].".png")){
				unlink("images/".$_POST["nom"].".png");
			}
            $rst=move_uploaded_file($resultat["tmp_name"],"images/".$_POST["nom"].".png");
        }
    }
	if($rst==true){
		echo "Téléchargement effectué ";
	}else{
		echo "Erreur Téléchargement ";
	}