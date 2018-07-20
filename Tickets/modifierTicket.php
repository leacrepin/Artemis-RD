<?php
/*
Modifier Ticket 

Statut 0 -> Demande de selectionner le ticket à l'utilisateur
Statut 1 -> Montre toute les infos du ticket à l'utilisateur 
Statut 2 -> Upload avec les nouvelles données rentrées


*/

include ("../../../../inc/includes.php");
include ("../../../../inc/config.php");

Session::checkLoginUser();
Session::checkRight("profile", READ);

global $DB;
   
    switch (date("m")) {
    case "01": $mes = __('January','dashboard'); break;
    case "02": $mes = __('February','dashboard'); break;
    case "03": $mes = __('March','dashboard'); break;
    case "04": $mes = __('April','dashboard'); break;
    case "05": $mes = __('May','dashboard'); break;
    case "06": $mes = __('June','dashboard'); break;
    case "07": $mes = __('July','dashboard'); break;
    case "08": $mes = __('August','dashboard'); break;
    case "09": $mes = __('September','dashboard'); break;
    case "10": $mes = __('October','dashboard'); break;
    case "11": $mes = __('November','dashboard'); break;
    case "12": $mes = __('December','dashboard'); break;
    }
	
	function typeMime($m){
		if ($m == 1) {
			return('Incident');
		}
		if ($m == 2) {
			return('Demande');
		}
		if ($m == 3){
			return('Suivi');
		}
		if ($m == 5){
			return('Changement');
		}
		if ($m == 4){
			return('Evenement');
		}
	}
	
	function statutMime($m){
		if ($m == 1) {
			return('Nouveau');
		}
		if ($m == 2){
			return('En cours (Attribué)');
		}
		if ($m == 3){
			return('En cours (Planifié)');
		}
		if ($m == 4){
			return('En attente');
		}
		if ($m == 5){
			return('Résolu');
		}
		if ($m == 6){
			return('Clos');
		}
	}
	
	function convertirTemps($duree){
		$heures=intval(($duree / 3600)% 24);
		$minutes=intval(($duree % 3600) / 60);
		$secondes=intval((($duree % 3600) % 60));
		return($heures."h ".$minutes."m ");
	}
	

?>        

<html> 
<head>
<meta content="text/html; charset=UTF-8" http-equiv="content-type">
<title> GLPI - Modifier Tickets </title>
<!-- <base href= "<?php $_SERVER['SERVER_NAME'] ?>" > -->
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE11">
<meta http-equiv="content-language" content="en-us">
<meta charset="utf-8">

<link rel="icon" href="../img/dash.ico" type="image/x-icon" />
<link rel="shortcut icon" href="../img/dash.ico" type="image/x-icon" />
<link href="../css/styles.css" rel="stylesheet" type="text/css" />
<link href="../css/bootstrap.css" rel="stylesheet" type="text/css" />
<link href="../css/bootstrap-responsive.css" rel="stylesheet" type="text/css" />
<link href="../css/font-awesome.css" type="text/css" rel="stylesheet" />

<script type="text/javascript" src="../js/jquery.min.js"></script>
<link href="../inc/select2/select2.css" rel="stylesheet" type="text/css">
<script src="../inc/select2/select2.js" type="text/javascript" language="javascript"></script>
<script src="https://cloud.tinymce.com/stable/tinymce.min.js"></script>
<script>
tinymce.init({
  selector: 'textarea',
  height: 500,
  menubar: false,
  plugins: [
    'advlist autolink lists link image charmap print preview anchor textcolor',
    'searchreplace visualblocks code fullscreen',
    'insertdatetime media table contextmenu paste code help wordcount'
  ],
  toolbar: 'insert | undo redo |  formatselect | bold italic backcolor  | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
  content_css: [
    '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
    '//www.tinymce.com/css/codepen.min.css']
});
</script>
<?php echo '<link rel="stylesheet" type="text/css" href="../css/style-'.$_SESSION['style'].'">';  ?>

<script src="../js/highcharts.js"></script>
<script src="../js/modules/exporting.js"></script>
<script src="../js/modules/no-data-to-display.js"></script>

<script src="../js/bootstrap-datepicker.js"></script>
<link href="../css/datepicker.css" rel="stylesheet" type="text/css">

	<link type="text/css" href="../css/jquery-picklist.css" rel="stylesheet" />

</head>

<body style="background-color: #e5e5e5;">

<div id='content'>
	<div id='container-fluid' style="margin: 0px 5% 0px 7%;"> 	
	
		<div id="head-tic" class="fluid" >	
			<a href="../"><i class="fa fa-home" style="font-size:14pt; margin-left:5px; margin-top:15px;"></i><span></span></a>
			<div id="titulo" class="tit-config" style="margin-bottom: 25px;"> <a href="config.php" > Modifier Tickets </a></div> 
		</div>
				                                                           
			<div id="charts" class="fluid chart" style="background-color:#fff;">				
			
					<div id="tabela" class="fluid" >		
					<?php
					
					// Tout les tickets
					$sql_e = "SELECT id FROM glpi_tickets";
					$result_e = $DB->query($sql_e);		?>				
					
<div id="datas-tec2" class="col-md-12 fluid" style="background-color:#fff; margin-top:20px;">
<div id="datas-tecx" class="col-md-12 fluid"> 																															 
<table id='main' class='col-md-12 table-config' border='0' style='width:700px; margin:auto; float:none;'>

<?php if (empty($_POST)){//Statut 0	?>		
			<tr><td>
			<form id="header" action="modifierTicket.php?status=1" method = "POST" enctype="multipart/form-data" style="margin-left:15%;"> 					
					-- Tickets :														
			<select name="sel_id" id="sel_id" >
<?php 
while($ligne=$DB->fetch_assoc($result_e)){
	$id=$ligne['id'];
					echo "<option value='".$id."'>".$id."</option>\n";
} 
?>					
					</select>
					<?php					
					echo "<tr><td align='center'><button type='submit' class='btn btn-primary'  > ".__('Save')."</button></td></tr>";
					Html::closeForm(); 										
				echo "</td>\n";		
			echo "</tr>\n";
}else{

	if($_GET["status"]==1){//Statut 1
		$sql_e = "SELECT COALESCE(glpi_itilcategories.name,'Pas de catégorie') as catname , glpi_tickets.itilcategories_id as cat ,glpi_tickets.id as id ,glpi_tickets.name as name,glpi_tickets.entities_id as entities_id,glpi_tickets.type as type,glpi_tickets.status as status,glpi_entities.name as nameent 
		FROM glpi_tickets 
		JOIN glpi_entities ON glpi_tickets.entities_id=glpi_entities.id 
		LEFT OUTER JOIN glpi_itilcategories ON glpi_itilcategories.id=glpi_tickets.itilcategories_id
		WHERE glpi_tickets.id=".$_POST[sel_id];
		$result_e = $DB->query($sql_e);
		$ligne=$DB->fetch_assoc($result_e);
?>
<tr><td>
	<form id="header" action="modifierTicket.php?status=2" method = "POST" enctype="multipart/form-data" style="margin-left:15%;">
	    <label> Name : <input type ="text" name ="name" value = "<?php echo $ligne["name"]; ?>" required> </label> </br>
		<label> Client : <select name="entities_id" id="entities_id" >
<?php
echo "<option selected='selected' value='".$ligne["entities_id"]."'>".$ligne["nameent"]."</option>\n";
$sql_e = "SELECT id,name FROM glpi_entities ";
$result_ent = $DB->query($sql_e);
while($ent=$DB->fetch_assoc($result_ent)){
					echo "<option value='".$ent["id"]."'>".$ent["name"]."</option>\n";
} 
?>					
					</select></label> </br>
		<label> Type : <select name="type" id="type" >
<?php
echo "<option selected='selected' value='".$ligne["type"]."'>".typeMime($ligne["type"])."</option>\n";
$sql_e = "SELECT distinct type FROM glpi_tickets ";
$result_ent = $DB->query($sql_e);
while($ent=$DB->fetch_assoc($result_ent)){
					echo "<option value='".$ent["type"]."'>".typeMime($ent["type"])."</option>\n";
} 
?>					
					</select></label> </br>
		<label> Catégorie : <select name="cat" id="cat" >
<?php
echo "<option selected='selected' value='".$ligne["cat"]."'>".$ligne["catname"]."</option>\n";
$sql_e = "SELECT distinct id,name FROM glpi_itilcategories ";
$result_ent = $DB->query($sql_e);
while($ent=$DB->fetch_assoc($result_ent)){
					echo "<option value='".$ent["id"]."'>".$ent["name"]."</option>\n";
} 
?>					
					</select></label> </br>
<?php
$sql_e = "SELECT content, actiontime FROM glpi_tickettasks WHERE tickets_id=".$ligne["id"];
$result_ent = $DB->query($sql_e);
$ent=$DB->fetch_assoc($result_ent);
?>
<label> Tâche : <textarea name="comment" id="tasktext" style="width:700px;" rows="15"><?php echo $ent["content"]; ?></textarea>
	</label> </br>	
		 </br>
		 <label> Temps de la Tâche: <select name="time" id="time" >
<?php
echo "<option selected='selected' value='".$ent["actiontime"]."'>".convertirTemps($ent["actiontime"])."</option>\n";
$i=$ent["actiontime"];
while($i<=360000){
	$i=$i+60;
					echo "<option value='".$i."'>".convertirTemps($i)."</option>\n";
} 
?>					
					</select></label> </br>

	    <input type ="hidden" name="id" value=<?php echo $ligne["id"]; ?>>
	    <?php 
		echo "<tr><td align='center'><button type='button' class='btn btn-primary' onclick='javascript:this.form.submit();' > ".__('Save')."</button></td></tr>";
		Html::closeForm(); 										
		echo "</td>\n";		
		echo "</tr>\n";
	
	}else{ //Statut 2
		$id = $_POST["id"];
		$entities_id = $_POST["entities_id"];
		$type = $_POST["type"];
		$name = $_POST["name"];
		$cat = $_POST["cat"];
		$tasktext = $_POST["comment"];
		$time = $_POST["time"];

		$sql = "UPDATE glpi_tickets SET itilcategories_id=$cat, name=\"$name\", entities_id=$entities_id, type=$type WHERE id=$id";

		$rst=$DB->query($sql) or die('erro');
		
		$sql = "UPDATE glpi_tickettasks SET actiontime=$time, content=\"$tasktext\" WHERE tickets_id=$id";

		$rst=$DB->query($sql) or die('erro');
		
		echo "Modification effectuée ";

		echo '<a href="modifierTicket.php"><i class="fa fa-home" style="font-size:14pt; margin-left:5px; margin-top:15px;"></i></a>';
	}
}	
																							 
					                               
		?>	
			  		

			</div>	
	</div>
</div>



<!--</div>-->
</body>
</html>
