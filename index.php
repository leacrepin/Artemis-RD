<html lang="fr">
  <head>
    <meta charset="UTF-8" />
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
			<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
				<input type = "text" name = "date" class="mdl-textfield__input" id="date">
				<label class="mdl-textfield__label" for="date"> Mois/Annee :</label>
                        </div><br>
                        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
				<input type = "text" name = "entreprise" class="mdl-textfield__input" id="entreprise">
				<label class="mdl-textfield__label" for="entreprise"> Nom de l'entreprise :</label>
			</div></div></div>
        
	  <p>Veuillez mettre le logo client</p><input type = "file" name = "image1">
	  <br><p>Veuillez mettre les charts "Bilan des Actions"</p>
          <input type = "file" name = "image2">
	  <br>
          <input type = "file" name = "image3">
	  <br>
          <input type = "file" name = "image4">
	  <br>
          <input type = "file" name = "image5">
	  <br>
          <input type = "file" name = "image6">
	  <br>
          <input type = "file" name = "image7">
	  <br>
          <input type = "file" name = "image8">
	  <br>
          <input type = "file" name = "image9">
	  <br>
      <input type = "submit" class="mdl-button mdl-button--raised mdl-button--colored">
      <input type = "reset" class="mdl-button mdl-button--raised mdl-button--colored">
    </form>

  </body>
</html>