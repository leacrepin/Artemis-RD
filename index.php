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
        
    <form id="header" action = "telechargementdoc.php" method = "POST" enctype="multipart/form-data">
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