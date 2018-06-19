<?php 
if(!empty($_POST['site'])) {
	$url = $_POST['site'];
	$fichier = './images/'.$_POST['numero'].'.png';
    file_put_contents($fichier, file_get_contents($url));
}
?>