<?php

//Supprimer les anciennes images
for($j=1;$j<10;$j++){
    if(file_exists("images/{$j}.png")){
	unlink("images/{$j}.png");
    }
}

//Ancien code glpi repris

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
?>

<html>
<head>
<title>GLPI - <?php echo __('Charts','dashboard'). " " . __('by Entity','dashboard'); ?></title>
<!-- <base href= "<?php $_SERVER['SERVER_NAME'] ?>" > -->
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
<meta http-equiv="content-language" content="en-us" />
<!--  <meta http-equiv="refresh" content= "120"/> -->

<link rel="icon" href="../img/dash.ico" type="image/x-icon" />
<link rel="shortcut icon" href="../img/dash.ico" type="image/x-icon" />
<link href="../css/styles.css" rel="stylesheet" type="text/css" />
<link href="../css/bootstrap.css" rel="stylesheet" type="text/css" />
<link href="../css/bootstrap-responsive.css" rel="stylesheet" type="text/css" />
<link href="../css/font-awesome.css" type="text/css" rel="stylesheet" />

<script type="text/javascript" src="../js/jquery.min.js"></script>
<link href="../inc/select2/select2.css" rel="stylesheet" type="text/css">
<script src="../inc/select2/select2.js" type="text/javascript" language="javascript"></script>

<script src="../js/highcharts.js"></script>
<script src="../js/modules/exporting.js"></script>
<script src="../js/modules/no-data-to-display.js"></script>
<script src="js/js.cookie.js"></script>

<script src="../js/bootstrap-datepicker.js"></script>
<link href="../css/datepicker.css" rel="stylesheet" type="text/css">

<?php echo '<link rel="stylesheet" type="text/css" href="../css/style-'.$_SESSION['style'].'">';  ?>
<?php echo '<script src="../js/themes/'.$_SESSION['charts_colors'].'"></script>'; ?>

 <!-- odometer -->
<link href="../css/odometer.css" rel="stylesheet">
<script src="../js/odometer.js"></script>

</head>
<body style="background-color: #e5e5e5; margin-left:0%;">

<?php

global $DB;

function bissextile($a){
	if($a%400==0||(($a%4==0)&($a%100!=0))){
		return(29);//année bissextile
	}else{
		return(28);//année non bissextile
	}
}

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

if(!empty($_POST['submit']))
{
	$data_ini =  $_POST['date1'];
	$data_fin = $_POST['date2'];
}

else {
	$data_ini = date("Y-01-01");
	$data_fin = date("Y-12-31");
}


if(!isset($_POST["sel_ent"])) {
	$id_ent = $_GET["sel_ent"];
}

else {
	$id_ent = $_POST["sel_ent"];
}

$ano = date("Y");
$month = date("Y-m");
$datahoje = date("Y-m-d");

//seleciona entidade
$sql_e = "SELECT value FROM glpi_plugin_dashboard_config WHERE name = 'entity' AND users_id = ".$_SESSION['glpiID']."";
$result_e = $DB->query($sql_e);
$sel_ent = $DB->result($result_e,0,'value');

if($sel_ent == '' || $sel_ent == -1) {
	//get user entities
	//$entities = Profile_User::getUserEntities($_SESSION['glpiID'], true);
	$entities = $_SESSION['glpiactiveentities'];
	$ents = implode(",",$entities);

	//$entidade = "AND glpi_entities.id IN (".$ent.")";
}
else {
	//$entidade = "AND glpi_entities.id IN (".$sel_ent.")";
	$ents = $sel_ent;
}

$sql_ent = "
SELECT id, name, completename AS cname
FROM `glpi_entities`
WHERE id IN (".$ents.")
ORDER BY `cname` ASC ";

$result_ent = $DB->query($sql_ent);
$ent = $DB->fetch_assoc($result_ent);


// lista de entidades
function dropdown( $name, array $options, $selected=null )
{
    /*** begin the select ***/
    $dropdown = '<select style="width: 300px; height: 27px;" autofocus onChange="javascript: document.form1.submit.focus()" name="'.$name.'" id="'.$name.'">'."\n";

    $selected = $selected;
    /*** loop over the options ***/
    foreach( $options as $key=>$option )
    {
        /*** assign a selected value ***/
        $select = $selected==$key ? ' selected' : null;

        /*** add each option to the dropdown ***/
        $dropdown .= '<option value="'.$key.'"'.$select.'>'.$option.'</option>'."\n";
    }

    /*** close the select ***/
    $dropdown .= '</select>'."\n";

    /*** and return the completed dropdown ***/
    return $dropdown;
}


$res_ent = $DB->query($sql_ent);
$arr_ent = array();
$arr_ent[0] = "-- ". __('Select a entity','dashboard') . " --" ;

$DB->data_seek($result_ent, 0);

while ($row_result = $DB->fetch_assoc($result_ent)) {
	$v_row_result = $row_result['id'];
	$arr_ent[$v_row_result] = $row_result['cname'] ;
}

$name = 'sel_ent';
$options = $arr_ent;
$selected = $id_ent;

?>

<div id='content' >
	<div id='container-fluid' style="margin: 0px 5% 0px 5%;">
		<div id="charts" class="fluid chart">
			<div id="pad-wrapper" >
				<div id="head" class="fluid">
					<a href="../index.php"><i class="fa fa-home" style="font-size:14pt; margin-left:25px;"></i></a>

					<div id="titulo_graf" >
					  <?php echo __('Tickets','dashboard') ." ". __('by Entity','dashboard'); ?><span style="color:#8b1a1a; font-size:35pt; font-weight:bold;"> </span> 
					</div>
						<div id="datas-tec" class="col-md-12 fluid" >
						<form id="form1" name="form1" class="form2" method="post" action="?date1=<?php echo $data_ini ?>&date2=<?php echo $data_fin ?>&con=1">
						<table border="0" cellspacing="0" cellpadding="1" bgcolor="#efefef">
						<tr>
						<td>
						<?php
							echo'
									<table>
										<tr>
											<td>
											   <div class="input-group date" id="dp1" data-date="'.$data_ini.'" data-date-format="yyyy-mm-dd">
											    	<input class="col-md-9 form-control" size="13" type="text" name="date1" value="'.$data_ini.'" >
											    	<span class="input-group-addon add-on"><i class="fa fa-calendar"></i></span>
										    	</div>
											</td>
											<td>&nbsp;</td>
											<td>
										   	<div class="input-group date" id="dp2" data-date="'.$data_fin.'" data-date-format="yyyy-mm-dd">
											    	<input class="col-md-9 form-control" size="13" type="text" name="date2" value="'.$data_fin.'" >
											    	<span class="input-group-addon add-on"><i class="fa fa-calendar"></i></span>
										    	</div>
											</td>
											<td>&nbsp;</td>
										</tr>
									</table> ';
							?>

						<script language="Javascript">

							$('#dp1').datepicker('update');
							$('#dp2').datepicker('update');

						</script>
						</td>

						<td style="margin-top:2px;">
						<?php
							echo dropdown( $name, $options, $selected );
						?>
						</td>
						</tr>
						<tr><td height="15px"></td></tr>
						<tr>
							<td colspan="2" align="center" style="">
								<button class="btn btn-primary btn-sm" type="submit" name="submit" value="Atualizar" ><i class="fa fa-search"></i>&nbsp; <?php echo __('Consult','dashboard'); ?></button>
							</td>
						</tr>

						</table>
						<?php Html::closeForm(); ?>
						<!-- </form> -->
						</div>
				</div>
			<!-- DIV's -->

			<script type="text/javascript" >
				$(document).ready(function() { $("#sel_ent").select2({dropdownAutoWidth : true}); });
			</script>

			<?php

			if(isset($_REQUEST['con'])) {
				$con = $_REQUEST['con'];
			}
			else { $con = ''; }

			if($con == "1") {

			if(!isset($_POST['date1']))
			{
				$data_ini2 = $_GET['date1'];
				$data_fin2 = $_GET['date2'];
			}

			else {
				$data_ini2 = $_POST['date1'];
				$data_fin2 = $_POST['date2'];
			}


			if($id_ent == " ") {
				echo '<script language="javascript"> alert(" ' . __('Select a entity','dashboard') . ' "); </script>';
				echo '<script language="javascript"> location.href="graf_entidade.php"; </script>';
			}

			if($data_ini == $data_fin) {
				$datas = "LIKE '".$data_ini."%'";
			}

			else {
				$datas = "BETWEEN '".$data_ini." 00:00:00' AND '".$data_fin." 23:59:59'";
			}

			// nome da entidade
			$sql_nm = "
			SELECT id, name, completename AS cname
			FROM `glpi_entities`
			WHERE id = ".$id_ent." ";

			$result_nm = $DB->query($sql_nm);
			$ent_name = $DB->fetch_assoc($result_nm);

			//quant chamados
			$query2 = "
			SELECT COUNT(glpi_tickets.id) as total
			FROM glpi_tickets
			WHERE glpi_tickets.date ".$datas."
			AND glpi_tickets.is_deleted = 0
			AND glpi_tickets.entities_id = ".$id_ent." ";

			$result2 = $DB->query($query2) or die('erro');
			$total = $DB->fetch_assoc($result2);
			

//count by status
$query_stat = "
SELECT
SUM(case when glpi_tickets.status = 1 then 1 else 0 end) AS new,
SUM(case when glpi_tickets.status = 2 then 1 else 0 end) AS assig,
SUM(case when glpi_tickets.status = 3 then 1 else 0 end) AS plan,
SUM(case when glpi_tickets.status = 4 then 1 else 0 end) AS pend,
SUM(case when glpi_tickets.status = 5 then 1 else 0 end) AS solve,
SUM(case when glpi_tickets.status = 6 then 1 else 0 end) AS close
FROM glpi_tickets
WHERE glpi_tickets.is_deleted = 0
AND glpi_tickets.date ".$datas."
AND glpi_tickets.entities_id = ".$id_ent." ";

$result_stat = $DB->query($query_stat);

$new = $DB->result($result_stat,0,'new') + 0;
$assig = $DB->result($result_stat,0,'assig') + 0;
$plan = $DB->result($result_stat,0,'plan') + 0;
$pend = $DB->result($result_stat,0,'pend') + 0;
$solve = $DB->result($result_stat,0,'solve') + 0;
$close = $DB->result($result_stat,0,'close') + 0;

echo '<div id="entidade2" class="col-md-12 fluid" style="margin-bottom: 15px;">';
echo '<div id="name"  style="margin-top: 15px;"><span>'.$ent_name['name'].'</span> - <span class="total_tech"> '.$total['total'].' '.__('Tickets','dashboard').'</span></div>

<div class="row" style="margin: 10px 0px 0 0;" >
<div style="margin-top: 20px; height: 45px;">
							<!-- COLUMN 1 -->
								  <div class="col-sm-3 col-md-3 stat" >
									 <div class="dashbox shad panel panel-default db-blue">
										<div class="panel-body_2">
										   <div class="panel-left red" style = "margin-top: -5px; margin-left: -5px;">
												<i class="fa fa-tags fa-3x fa2"></i>
										   </div>
										   <div class="panel-right">
										     <div id="odometer1" class="odometer" style="font-size: 20px; margin-top: 1px;">  </div><p></p>
                        				<span class="chamado">'. __('Tickets','dashboard').'</span><br>
                        				<span class="date" style="font-size: 16px;"><b>'. _x('status', 'New').' + '.__('Assigned').'</b></span>
										   </div>
										</div>
									 </div>
								  </div>

								  <div class="col-sm-3 col-md-3">
									 <div class="dashbox shad panel panel-default db-yellow">
										<div class="panel-body_2">
										   <div class="panel-left yellow" style = "margin-top: -5px; margin-left: -5px;">
												<i class="fa fa-clock-o fa-3x fa2"></i>
										   </div>
										   <div class="panel-right">
											<div id="odometer2" class="odometer" style="font-size: 20px; margin-top: 1px;">   </div><p></p>
                        				<span class="chamado">'. __('Tickets','dashboard').'</span><br>
                        				<span class="date"><b>'. __('Pending').'</b></span>
										   </div>
										</div>
									 </div>
								  </div>

								  <div class="col-sm-3 col-md-3">
									 <div class="dashbox shad panel panel-default db-red">
										<div class="panel-body_2">
										   <div class="panel-left yellow" style = "margin-top: -5px; margin-left: -5px;">
												<i class="fa fa-check-square fa-3x fa2"></i>
										   </div>
										   <div class="panel-right">
												<div id="odometer3" class="odometer" style="font-size: 20px; margin-top: 1px;">   </div><p></p>
                        				<span class="chamado">'. __('Tickets','dashboard').'</span><br>
                        				<span class="date"><b>'. __('Solved','dashboard').'</b></span>
										   </div>
										</div>
									 </div>
								  </div>
								  <div class="col-sm-3 col-md-3">
									 <div class="dashbox shad panel panel-default db-orange">
										<div class="panel-body_2">
										   <div class="panel-left green" style = "margin-top: -5px; margin-left: -5px;">
												<i class="fa fa-times-circle fa-3x fa2"></i>
										   </div>
								   		<div class="panel-right">
												<div id="odometer4" class="odometer" style="font-size: 20px; margin-top: 1px;">   </div><p></p>
                        				<span class="chamado">'. __('Tickets','dashboard').'</span><br>
                        				<span class="date"><b>'. __('Closed','dashboard').'</b></span>
										   </div>
										</div>
									 </div>
								  </div>
						</div>

</div>
</div>';
?>

<script type="text/javascript" >
	window.odometerOptions = {
	   format: '( ddd).dd'
	};

	setTimeout(function(){
	    odometer1.innerHTML = <?php echo $new + $assig + $plan; ?>;
	    odometer2.innerHTML = <?php echo $pend; ?>;
	    odometer3.innerHTML = <?php echo $solve; ?>;
	    odometer4.innerHTML = <?php echo $close; ?>;
	}, 1000);
</script>
			
			<h1>Faire mon fichier client</h1>
        <p><a tabindex="-1" style="color:#000000;" href=<?php echo "telechargementdocAnnuel.php?id=".$id_ent."&date1=".$data_ini."&date2=".$data_fin."&entreprise=".$ent_name['name'] ; ?> target="_blank"> Télécharger le fichier client </a>
		</p>
    <form id="header" action = "ajoutLogo.php" method = "POST" enctype="multipart/form-data">
	<h5>Veuillez mettre un logo client en .png (optionnel)</h5>
                    <select id="nom" class="select_stats" name="nom">
<?php
$sql_nm = "
			SELECT id, name, completename AS cname
			FROM `glpi_entities`";

			$result_nm = $DB->query($sql_nm);
			$ligne = $DB->fetch_assoc($result_nm);
 
		while($ligne = $DB->fetch_assoc($result_nm)){
?>		<option value="<?php echo $ligne['name']; ?>" ><?php echo $ligne['name']; ?></option>
<?php			}
?>
	</select>
        <input type = "file" name = "image1">
      <input type = "submit" class="mdl-button mdl-button--raised mdl-button--colored">
      <input type = "reset" class="mdl-button mdl-button--raised mdl-button--colored">
    </form>
			<div id="graf_linhas" class="col-md-12" style="height: 450px; margin-top: 20px !important; margin-left: 0px;">
				<?php if($data_ini == $data_fin) {
	$datas = "LIKE '".$data_ini."%'";
}

$data1 = $data_ini;
$data2 = $data_fin;

$unix_data1 = strtotime($data1);
$unix_data2 = strtotime($data2);

$interval = ($unix_data2 - $unix_data1) / 86400;


if($interval >= "31") {

$datas = "BETWEEN '".$data_ini." 00:00:00' AND '".$data_fin." 23:59:59'";

 $querym = "
SELECT DISTINCT DATE_FORMAT(date, '%b-%Y') as day_l,  COUNT(id) as nb, DATE_FORMAT(date, '%y-%m') as day
FROM glpi_tickets
WHERE glpi_tickets.is_deleted = '0'
AND glpi_tickets.date ".$datas."
AND glpi_tickets.entities_id = ".$id_ent."
GROUP BY day
ORDER BY day ";
}

else {

$datas = "BETWEEN '".$data_ini." 00:00:00' AND '".$data_fin." 23:59:59'";

 $querym = "
SELECT DISTINCT DATE_FORMAT(date, '%b-%d') as day_l,  COUNT(id) as nb, DATE_FORMAT(date, '%Y-%m-%d') as day
FROM glpi_tickets
WHERE glpi_tickets.is_deleted = '0'
AND glpi_tickets.date ".$datas."
AND glpi_tickets.entities_id = ".$id_ent."
GROUP BY day
ORDER BY day ";
}

$resultm = $DB->query($querym) or die('errol');

$contador = $DB->numrows($resultm);

$arr_grfm = array();
while ($row_result = $DB->fetch_assoc($resultm)){
	$v_row_result = $row_result['day_l'];
	$arr_grfm[$v_row_result] = $row_result['nb'];
}

$grfm = array_keys($arr_grfm) ;
$quantm = array_values($arr_grfm) ;

$grfm3 = json_encode($grfm);

//var_dump($grfm3);

$quantm2 = implode(',',$quantm);

$version = substr($CFG_GLPI["version"],0,5);

$status = "('5','6')"	;

if($interval >= "31") {

	$datas = "BETWEEN '".$data_ini." 00:00:00' AND '".$data_fin." 23:59:59'";

	// fechados mensais
	$queryf = "
	SELECT DISTINCT DATE_FORMAT(date, '%b-%Y') as day_l,  COUNT(id) as nb, DATE_FORMAT(date, '%y-%m') as day
	FROM glpi_tickets
	WHERE glpi_tickets.is_deleted = '0'
	AND glpi_tickets.date ".$datas."
	AND glpi_tickets.status IN ". $status ."
	AND glpi_tickets.entities_id = ".$id_ent."
	GROUP BY day
	ORDER BY day ";
 }

 else {

	$datas = "BETWEEN '".$data_ini." 00:00:00' AND '".$data_fin." 23:59:59'";

	// fechados mensais
	$queryf = "
	SELECT DISTINCT DATE_FORMAT(date, '%b-%d') as day_l,  COUNT(id) as nb, DATE_FORMAT(date, '%Y-%m-%d') as day
	FROM glpi_tickets
	WHERE glpi_tickets.is_deleted = '0'
	AND glpi_tickets.date ".$datas."
	AND glpi_tickets.status IN ". $status ."
	AND glpi_tickets.entities_id = ".$id_ent."
	GROUP BY day
	ORDER BY day ";

 }

$resultf = $DB->query($queryf) or die('erro');

$arr_grff = array();
while ($row_result = $DB->fetch_assoc($resultf)){
	$v_row_result = $row_result['day_l'];
	$arr_grff[$v_row_result] = $row_result['nb'];
}

$grff = array_keys($arr_grff) ;
$quantf = array_values($arr_grff) ;

$quantf2 = implode(',',$quantf);

echo "
<script type='text/javascript'>
$(function ()
{
        $('#graf_linhas').highcharts({
            chart: {
                type: 'column'
            },
            title: {
                text: '".__('Tickets','dashboard')."'
            },
            legend: {
                layout: 'horizontal',
                align: 'center',
                verticalAlign: 'bottom',
                x: 0,
                y: 0,
                //floating: true,
                borderWidth: 0,
                adjustChartSize: true
                //backgroundColor: '#FFFFFF'
            },
            xAxis: {
                categories: $grfm3,
						  labels: {
                    rotation: -45,
                    align: 'right',
                    style: {
                        fontSize: '11px',
                        fontFamily: 'Verdana, sans-serif'
                    }
                }

            },
            yAxis: {
                title: {
                    text: ''
                }
            },
            tooltip: {
                shared: true
            },
            credits: {
                enabled: false
            },
            plotOptions: {
                column: {
                    fillOpacity: 0.5,
                    borderWidth: 1,
                	  borderColor: 'white',
                	  shadow:true,
                    dataLabels: {
	                 	enabled: true
	                 },
                },
            },
          series: [{

                name: '".__('Opened','dashboard')."',
                data: [$quantm2] },

                {
                name: '".__('Closed','dashboard')."',
                data: [$quantf2]
            }]
        });
    });
  </script>
";
?>
			</div>

			 <div id="graf2" class="col-md-6" >
				<?php 
if($data_ini == $data_fin) {
$datas = "LIKE '".$data_ini."%'";	
}	

else {
$datas = "BETWEEN '".$data_ini." 00:00:00' AND '".$data_fin." 23:59:59'";	
}

$query2 = "
SELECT COUNT(glpi_tickets.id) as tick, glpi_tickets.status as stat
FROM glpi_tickets
WHERE glpi_tickets.date ".$datas."
AND glpi_tickets.is_deleted = 0     
AND glpi_tickets.entities_id = ".$id_ent."    
GROUP BY glpi_tickets.status
ORDER BY stat  ASC ";

		
$result2 = $DB->query($query2) or die('erro');

$arr_grf2 = array();
while ($row_result = $DB->fetch_assoc($result2))		
	{ 
		$v_row_result = $row_result['stat'];
		$arr_grf2[$v_row_result] = $row_result['tick'];			
	} 
	
$grf2 = array_keys($arr_grf2);
$quant2 = array_values($arr_grf2);

$conta = count($arr_grf2);
	

echo "
<script type='text/javascript'>

$(function () {		
    	   		
		// Build the chart
        $('#graf2').highcharts({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
            },
            title: {
                text: 'Répartition par Etat'
            },
            tooltip: {
        	    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    size: '85%',
 					dataLabels: {
								format: '{point.y} - ( {point.percentage:.1f}% )',
                   		style: {
                        	color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                        		},
                        connectorColor: 'black'
                    },
                showInLegend: true
                }
            },
            series: [{
                type: 'pie',
                name: '".__('Tickets','dashboard')."',
                data: [
                    {
                        name: '" . Ticket::getStatus($grf2[0]) . "',
                        y: $quant2[0],
                        sliced: true,
                        selected: true
                    },";
                    
for($i = 1; $i < $conta; $i++) {    
     echo '[ "' . Ticket::getStatus($grf2[$i]) . '", '.$quant2[$i].'],';
        }                    
                                                         
echo "                ]
            }]
        });
    });

		</script>
";
?>
			</div>
			
			<div id="graf_tipo" class="col-md-6" style="margin-left: 0%;">
				<script src="https://code.highcharts.com/highcharts.src.js"></script>
<?php
// Pour une période début/fin similaire
if ($data_ini == $data_fin) {
  $datas = "LIKE '".$data_ini."%'";
} // Pour une période début/fin différente
else {
	$datas = "BETWEEN '".$data_ini." 00:00:00' AND '".$data_fin." 23:59:59'";
}
// Requête SQL TYPE
$query = "
SELECT COUNT(glpi_tickets.id) as tick, glpi_tickets.type AS tipo, SUM(glpi_tickets.actiontime) as temps
FROM glpi_tickets
WHERE glpi_tickets.is_deleted = 0
AND glpi_tickets.date ".$datas."
AND glpi_tickets.entities_id = ".$id_ent."
AND glpi_tickets.status = '6'
GROUP BY glpi_tickets.type
ORDER BY type  ASC ";
// On execute la requête, renvoi une erreur
$result = $DB->query($query) or die ('erro');
// On stock le résultat dans des tableaux
$arr_grft2 = array();
while ($row_result = $DB->fetch_assoc($result))
{
  $v_row_result= $row_result['tipo'];
  $arr_grft2[$v_row_result] = $row_result['temps'];
}
// On stock l'ID dans grft2, la valeur dans quantt2
$test = array_values($arr_grft2);
$grft2 = array_keys($arr_grft2);
$quantt2 = array_values($arr_grft2);
$conta = count($arr_grft2);
// Si il n'y a qu'un type
if ($conta < 1){
  // Si l'ID = 1
  if ($grft2[0] == 1) {
    $grft2[0] = __('Incident');
  }
  if ($grft2[0] == 3){
    $grft2[0] = __('Suivi');
  }
  if ($grft2[0] == 4){
    $grft2[0] = __('Changement');
  }
  if ($grft2[0] == 5){
    $grft2[0] = __('Evenement');
  }
}
function convertirTemps($duree){
	$heures=intval(($duree / 3600));
	$minutes=intval(($duree % 3600) / 60);
	return($heures.":".$minutes." H");
}

// Si il y a + de 1 types
if($conta >= 1) {
	if ($test[0]  >= 86400)
  $grft2[0] = "Incident " . ' ' . convertirTemps($test[0]);
	else
	{
		$grft2[0] = "Incident " . ' ' . date('H:i', $test[0] - 3600) . " H";
	}
	if ($test[1]  >= 86400)
  $grft2[1] = "Suivi" . ' ' . convertirTemps($test[1]);
	else
	{
	 $grft2[1] = "Suivi" . ' ' . date('H:i', $test[1] - 3600) . " H";
	}
	if ($test[2]  >= 86400)
  $grft2[2] = "Changement" . ' ' . convertirTemps($test[2]);
	else{
		$grft2[2] = "Changement" . ' ' . date('H:i', $test[2] - 3600) . " H";
	}
	if ($test[3]  >= 86400)
  $grft2[3] = "Evenement" . ' ' . convertirTemps($test[3]);
	else{
	$grft2[3] = "Evenement" . ' ' . date('H:i', $test[3] - 3600) . " H";
	}
}

echo "
<script type='text/javascript'>

$(function () {

		// Build the chart
        $('#graf_tipo').highcharts({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
            },
            title: {
                text: 'Répartition par actions'
            },
            tooltip: {
        	    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    size: '85%',
 					dataLabels: {
								enabled: true,
								format: '({point.percentage:.1f}% )'
                    },
                showInLegend: true
                }
            },
            series: [{
                type: 'pie',
                name: '".__('Tickets','dashboard')."',
                data: [
                   {
                        name: '" . $grft2[0] .  "',
                        y: $quantt2[0],
                        sliced: true,
                        selected: true
                    },";
if($conta == 1) {
	for($i = 1; $i < $conta; $i++) {
	     echo '[ "' . $grft2[$i] . '", '.$quantt2[$i].'],';
	        }
        }

if($conta > 1) {
	for($i = 1; $i <= $conta; $i++) {
		
	     echo '[ "' . $grft2[$i] . '", '.$quantt2[$i].'],';
	        }
        }

echo "                ],
            }]
        });
    });

		</script><script>
		var chartOptions = {
			chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
				style: {
					font: '12px Dosis, sans-serif'
				}
            },
            title: {
                text: 'Répartition par actions',
				style: {
					textTransform: 'uppercase',
					fontWeight: 'bold'
				}
            },
            tooltip: {
        	    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    size: '85%',
 					dataLabels: {
								enabled: true,
								format: '({point.percentage:.1f}% )'
                    },
                showInLegend: true
                }
            },
            series: [{
                type: 'pie',
                name: '".__('Tickets','dashboard')."',
                data: [
                   {
                        name: '" . $grft2[0] .  "',
                        y: $quantt2[0],
                        sliced: true,
                        selected: true
                    },";
if($conta == 1) {
	for($i = 1; $i < $conta; $i++) {
	     echo '[ "' . $grft2[$i] . '", '.$quantt2[$i].'],';
	        }
        }

if($conta > 1) {
	for($i = 1; $i <= $conta; $i++) {
		
	     echo '[ "' . $grft2[$i] . '", '.$quantt2[$i].'],';
	        }
        }

echo "                ],
            }]
        };
	var data = {
    options: JSON.stringify(chartOptions),
    filename: 'image',
    type: 'image/png',
    async: true
};
	var exportUrl = 'http://export.highcharts.com/';
	$.post(exportUrl, data, function(data) {
		var url = exportUrl + data;
		var site = url;
		$.ajax({
			url : 'graph1.php', // on donne l'URL du fichier de traitement
			type : 'POST', // la requête est de type POST
			data : 'site=' + site + '&numero=' + 2
		});
	});
    </script>
";
?>

			</div>
			
			<div id="category" class="col-md-6" style="width:100%; height:400px;"> 
			<?php

if($data_ini == $data_fin) {
	$datas = "LIKE '".$data_ini."%'";	
}	

else {
	$datas = "BETWEEN '".$data_ini." 00:00:00' AND '".$data_fin." 23:59:59'";	
}
 

//tickets by type
$query2 = "
SELECT glpi_itilcategories.completename as cat_name, COUNT(glpi_tickets.id) as cat_tick, SUM(glpi_tickets.actiontime) as temps
FROM glpi_tickets
JOIN glpi_itilcategories
ON glpi_itilcategories.id = glpi_tickets.itilcategories_id
WHERE glpi_tickets.is_deleted = '0'
AND glpi_tickets.date ".$datas."
AND glpi_tickets.entities_id = ".$id_ent."
AND glpi_itilcategories.completename IS NOT NULL
AND glpi_tickets.type = '1'
AND glpi_tickets.status = '6'
GROUP BY glpi_itilcategories.completename
ORDER BY glpi_itilcategories.completename ASC";
		
$result2 = $DB->query($query2) or die('erro');
$test = 'H';
$arr_grft2 = array();
while ($row_result = $DB->fetch_assoc($result2))
{
		$v_row_result = $row_result['cat_name']." (".date('H:i',$row_result['temps'] - 3600). " H" . ")";
		$arr_grft2[$v_row_result] = $row_result['temps'];
	}
	
$grft2 = array_keys($arr_grft2);
$quantt2 = array_values($arr_grft2);
$conta = count($arr_grft2);
	
echo "
<script type='text/javascript'>

$(function () {		
    	   		
		// Build the chart
        $('#category').highcharts({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
				type: 'pie'
            },
            title: {
                text: 'Répartition par application'

            },
            tooltip: {
        	    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    size: '85%',
 					dataLabels: {
								enabled: true,
								format: '({point.percentage:.1f}% )'
                    },
                showInLegend: true
                }
            },
            series: [{
                type: 'pie',
                name: '".__('Tickets','dashboard')."',
                data: [
                   {

                        name: '" . $grft2[0] . "',
                        y: $quantt2[0],
                        sliced: true,
                        selected: true
                    },";
        
                                      
	for($i = 1; $i <= $conta; $i++) {    
	     echo '[ "' . $grft2[$i] . '", '.$quantt2[$i].'],';
	        }                          
                                                         
echo "                ],
            }]
        });
    });

		</script>  <script>
		var chartOptions = {
			chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
				type: 'pie',
				style: {
					font: '12px Dosis, sans-serif'
				}
            },
            title: {
                text: 'Répartition par application',
				style: {
					textTransform: 'uppercase',
					fontWeight: 'bold'
				}

            },
            tooltip: {
        	    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    size: '85%',
 					dataLabels: {
								enabled: true,
								format: '({point.percentage:.1f}% )'
                    },
                showInLegend: true
                }
            },
            series: [{
                type: 'pie',
                name: '".__('Tickets','dashboard')."',
                data: [
                   {

                        name: '" . $grft2[0] . "',
                        y: $quantt2[0],
                        sliced: true,
                        selected: true
                    },";
        
                                      
	for($i = 1; $i <= $conta; $i++) {    
	     echo '[ "' . $grft2[$i] . '", '.$quantt2[$i].'],';
	        }                          
                                                         
echo "                ],
            }]
        };
	var data = {
    options: JSON.stringify(chartOptions),
    filename: 'image',
    type: 'image/png',
    async: true
};
	var exportUrl = 'http://export.highcharts.com/';
	$.post(exportUrl, data, function(data) {
		var url = exportUrl + data;
		var site = url;
		$.ajax({
			url : 'graph1.php', // on donne l'URL du fichier de traitement
			type : 'POST', // la requête est de type POST
			data : 'site=' + site + '&numero=' + 3
		});
	});
    </script>
";
?>

			</div>
			
			<div id="category_critique_maj" class="col-md-6" style="width:100%; height:400px;">
			<?php

if($data_ini == $data_fin) {
	$datas = "LIKE '".$data_ini."%'";	
}	

else {
	$datas = "BETWEEN '".$data_ini." 00:00:00' AND '".$data_fin." 23:59:59'";	
}
 

//tickets by type
$query2 = "
SELECT glpi_itilcategories.completename as cat_name, COUNT(glpi_tickets.id) as cat_tick
FROM glpi_tickets
JOIN glpi_itilcategories
ON glpi_itilcategories.id = glpi_tickets.itilcategories_id
WHERE glpi_tickets.is_deleted = '0'
AND glpi_tickets.date ".$datas."
AND glpi_tickets.entities_id = ".$id_ent."
AND glpi_itilcategories.completename IS NOT NULL
AND glpi_tickets.type = '1'
AND glpi_tickets.priority = '3'
AND glpi_tickets.status = '6'
GROUP BY glpi_itilcategories.completename
ORDER BY glpi_itilcategories.completename ASC";
		
$result2 = $DB->query($query2) or die('erro');

$arr_grft2 = array();
while ($row_result = $DB->fetch_assoc($result2))
	{
		$v_row_result = $row_result['cat_name'];
		$arr_grft2[$v_row_result] = $row_result['cat_tick'];
	}
	
$grft2 = array_keys($arr_grft2);
$quantt2 = array_values($arr_grft2);
$conta = count($arr_grft2);

	
echo "
<script type='text/javascript'>

$(function () {		
    	   		
		// Build the chart
        $('#category_critique_maj').highcharts({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
            },
            title: {
                text: 'Incidents Critiques et Majeurs'
            },
            tooltip: {
        	    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    size: '85%',
 					dataLabels: {
								format: '{point.y} - ( {point.percentage:.1f}% )',
                   		style: {
                        	color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                        		},
                        connectorColor: 'black'
                    },
                showInLegend: true
                }
            },
            series: [{
                type: 'pie',
                name: '".__('Tickets','dashboard')."',
                data: [
                   {
                        name: '" . $grft2[0] . "',
                        y: $quantt2[0],
                        sliced: true,
                        selected: true
                    },";
        
                                      
	for($i = 1; $i <= $conta; $i++) {    
	     echo '[ "' . $grft2[$i] . '", '.$quantt2[$i].'],';
	        }                          
                                                         
echo "                ],
            }]
        });
    });

		</script><script>
		var chartOptions = {
			chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
				style: {
					font: '12px Dosis, sans-serif'
				}
            },
            title: {
                text: 'Incidents Critiques et Majeurs',
				style: {
					textTransform: 'uppercase',
					fontWeight: 'bold'
				}
            },
            tooltip: {
        	    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    size: '85%',
 					dataLabels: {
								format: '{point.y} - ( {point.percentage:.1f}% )',
                   		style: {
                        	color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                        		},
                        connectorColor: 'black'
                    },
                showInLegend: true
                }
            },
            series: [{
                type: 'pie',
                name: '".__('Tickets','dashboard')."',
                data: [
                   {
                        name: '" . $grft2[0] . "',
                        y: $quantt2[0],
                        sliced: true,
                        selected: true
                    },";
        
                                      
	for($i = 1; $i <= $conta; $i++) {    
	     echo '[ "' . $grft2[$i] . '", '.$quantt2[$i].'],';
	        }                          
                                                         
echo "                ],
            }]
        };
	var data = {
    options: JSON.stringify(chartOptions),
    filename: 'image',
    type: 'image/png',
    async: true
};
	var exportUrl = 'http://export.highcharts.com/';
	$.post(exportUrl, data, function(data) {
		var url = exportUrl + data;
		var site = url;
		$.ajax({
			url : 'graph1.php', // on donne l'URL du fichier de traitement
			type : 'POST', // la requête est de type POST
			data : 'site=' + site + '&numero=' + 4
		});
	});
    </script>
";
		
		?>

			</div>
			
			<div id="category_critique" class="col-md-6" style="width:100%; height:400px;">
			<?php

if($data_ini == $data_fin) {
	$datas = "LIKE '".$data_ini."%'";	
}	

else {
	$datas = "BETWEEN '".$data_ini." 00:00:00' AND '".$data_fin." 23:59:59'";	
}
 

//tickets by type
$query2 = "
SELECT glpi_itilcategories.completename as cat_name, COUNT(glpi_tickets.id) as cat_tick
FROM glpi_tickets
JOIN glpi_itilcategories
ON glpi_itilcategories.id = glpi_tickets.itilcategories_id
WHERE glpi_tickets.is_deleted = '0'
AND glpi_tickets.date ".$datas."
AND glpi_tickets.entities_id = ".$id_ent."
AND glpi_itilcategories.completename IS NOT NULL
AND glpi_tickets.type = '1'
AND glpi_tickets.priority = '2'
AND glpi_tickets.status = '6'
GROUP BY glpi_itilcategories.completename
ORDER BY glpi_itilcategories.completename ASC";
		
$result2 = $DB->query($query2) or die('erro');

$arr_grft2 = array();
while ($row_result = $DB->fetch_assoc($result2))
	{
		$v_row_result = $row_result['cat_name'];
		$arr_grft2[$v_row_result] = $row_result['cat_tick'];
	}
	
$grft2 = array_keys($arr_grft2);
$quantt2 = array_values($arr_grft2);
$conta = count($arr_grft2);

	
echo "
<script type='text/javascript'>

$(function () {		
    	   		
		// Build the chart
        $('#category_critique').highcharts({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
            },
            title: {
                text: 'Incidents Critiques'
            },
            tooltip: {
        	    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    size: '85%',
 					dataLabels: {
								format: '{point.y} - ( {point.percentage:.1f}% )',
                   		style: {
                        	color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                        		},
                        connectorColor: 'black'
                    },
                showInLegend: true
                }
            },
            series: [{
                type: 'pie',
                name: '".__('Tickets','dashboard')."',
                data: [
                   {
                        name: '" . $grft2[0] . "',
                        y: $quantt2[0],
                        sliced: true,
                        selected: true
                    },";
        
                                      
	for($i = 1; $i <= $conta; $i++) {    
	     echo '[ "' . $grft2[$i] . '", '.$quantt2[$i].'],';
	        }                          
                                                         
echo "                ],
            }]
        });
    });

		</script><script>
		var chartOptions = {
			chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
				style: {
					font: '12px Dosis, sans-serif'
				}
            },
            title: {
                text: 'Incidents Critiques',
				style: {
					textTransform: 'uppercase',
					fontWeight: 'bold'
				}
            },
            tooltip: {
        	    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    size: '85%',
 					dataLabels: {
								format: '{point.y} - ( {point.percentage:.1f}% )',
                   		style: {
                        	color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                        		},
                        connectorColor: 'black'
                    },
                showInLegend: true
                }
            },
            series: [{
                type: 'pie',
                name: '".__('Tickets','dashboard')."',
                data: [
                   {
                        name: '" . $grft2[0] . "',
                        y: $quantt2[0],
                        sliced: true,
                        selected: true
                    },";
        
                                      
	for($i = 1; $i <= $conta; $i++) {    
	     echo '[ "' . $grft2[$i] . '", '.$quantt2[$i].'],';
	        }                          
                                                         
echo "                ],
            }]
        };
	var data = {
    options: JSON.stringify(chartOptions),
    filename: 'image',
    type: 'image/png',
    async: true
};
	var exportUrl = 'http://export.highcharts.com/';
	$.post(exportUrl, data, function(data) {
		var url = exportUrl + data;
		var site = url;
		$.ajax({
			url : 'graph1.php', // on donne l'URL du fichier de traitement
			type : 'POST', // la requête est de type POST
			data : 'site=' + site + '&numero=' + 5
		});
	});
    </script>
";
		
		?>

			</div>
			
			<div id="category_mineur" class="col-md-6" style="width:100%; height:400px;">
			<?php

if($data_ini == $data_fin) {
	$datas = "LIKE '".$data_ini."%'";	
}	

else {
	$datas = "BETWEEN '".$data_ini." 00:00:00' AND '".$data_fin." 23:59:59'";	
}
 

//tickets by type
$query2 = "
SELECT glpi_itilcategories.completename as cat_name, COUNT(glpi_tickets.id) as cat_tick
FROM glpi_tickets
JOIN glpi_itilcategories
ON glpi_itilcategories.id = glpi_tickets.itilcategories_id
WHERE glpi_tickets.is_deleted = '0'
AND glpi_tickets.date ".$datas."
AND glpi_tickets.entities_id = ".$id_ent."
AND glpi_itilcategories.completename IS NOT NULL
AND glpi_tickets.priority = '1'
AND glpi_tickets.status = '6'
AND glpi_tickets.type = '1'
GROUP BY glpi_itilcategories.completename
ORDER BY glpi_itilcategories.completename ASC";
		
$result2 = $DB->query($query2) or die('erro');

$arr_grft2 = array();
while ($row_result = $DB->fetch_assoc($result2))
	{
		$v_row_result = $row_result['cat_name'];
		$arr_grft2[$v_row_result] = $row_result['cat_tick'];
	}
	
$grft2 = array_keys($arr_grft2);
$quantt2 = array_values($arr_grft2);
$conta = count($arr_grft2);

	
echo "
<script type='text/javascript'>

$(function () {		
    	   		
		// Build the chart
        $('#category_mineur').highcharts({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
            },
            title: {
                text: 'Incidents Mineurs'
            },
            tooltip: {
        	    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    size: '85%',
 					dataLabels: {
								format: '{point.y} - ( {point.percentage:.1f}% )',
                   		style: {
                        	color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                        		},
                        connectorColor: 'black'
                    },
                showInLegend: true
                }
            },
            series: [{
                type: 'pie',
                name: '".__('Tickets','dashboard')."',
                data: [
                   {
                        name: '" . $grft2[0] . "',
                        y: $quantt2[0],
                        sliced: true,
                        selected: true
                    },";
        
                                      
	for($i = 1; $i <= $conta; $i++) {    
	     echo '[ "' . $grft2[$i] . '", '.$quantt2[$i].'],';
	        }                          
                                                         
echo "                ],
            }]
        });
    });

		</script><script>
		var chartOptions = {
			chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
				style: {
					font: '12px Dosis, sans-serif'
				}
            },
            title: {
                text: 'Incidents Mineurs',
				style: {
					textTransform: 'uppercase',
					fontWeight: 'bold'
				}
            },
            tooltip: {
        	    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    size: '85%',
 					dataLabels: {
								format: '{point.y} - ( {point.percentage:.1f}% )',
                   		style: {
                        	color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                        		},
                        connectorColor: 'black'
                    },
                showInLegend: true
                }
            },
            series: [{
                type: 'pie',
                name: '".__('Tickets','dashboard')."',
                data: [
                   {
                        name: '" . $grft2[0] . "',
                        y: $quantt2[0],
                        sliced: true,
                        selected: true
                    },";
        
                                      
	for($i = 1; $i <= $conta; $i++) {    
	     echo '[ "' . $grft2[$i] . '", '.$quantt2[$i].'],';
	        }                          
                                                         
echo "                ],
            }]
        };
	var data = {
    options: JSON.stringify(chartOptions),
    filename: 'image',
    type: 'image/png',
    async: true
};
	var exportUrl = 'http://export.highcharts.com/';
	$.post(exportUrl, data, function(data) {
		var url = exportUrl + data;
		var site = url;
		$.ajax({
			url : 'graph1.php', // on donne l'URL du fichier de traitement
			type : 'POST', // la requête est de type POST
			data : 'site=' + site + '&numero=' + 6
		});
	});
    </script>
";
		
		?>

			</div>
		
			<div id="graf_time1" class="col-md-12" style="height: 450px; margin-top: 25px; margin-left: -5px;">
				<?php

if($data_ini == $data_fin) {
	$datas = "LIKE '".$data_ini."%'";	
}	

else {
	$datas = "BETWEEN '".$data_ini." 00:00:00' AND '".$data_fin." 23:59:59'";	
}

$query_grp = "
SELECT ggt.groups_id AS gid, count( ggt.tickets_id ) AS quant
FROM glpi_groups_tickets ggt, glpi_tickets gt
WHERE ggt.type = 1
AND gt.is_deleted = 0
AND gt.closedate IS NOT NULL
AND ggt.tickets_id = gt.id
AND gt.solvedate ".$datas."
AND gt.entities_id = ".$id_ent."
GROUP BY ggt.groups_id
ORDER BY quant DESC
LIMIT 0, 20 ";

$result_grp = $DB->query($query_grp);

$arr_grft2 = array();

while ($row = $DB->fetch_assoc($result_grp)) {
	
	//tickets by type
	$query2 = "
	SELECT gg.completename AS gname, sum( gt.solve_delay_stat) AS time
	FROM glpi_groups_tickets ggt, glpi_tickets gt, glpi_groups gg
	WHERE ggt.groups_id = ".$row['gid']."
	AND ggt.type = 1
	AND ggt.groups_id = gg.id
	AND gt.is_deleted = 0
	AND closedate IS NOT NULL
	AND gt.id = ggt.tickets_id ";
	
	$result2 = $DB->query($query2) or die('erro');
	
	$row_result = $DB->fetch_assoc($result2);		
		 			
		$v_row_result = $row_result['gname'];
		$arr_grft2[$v_row_result] =  round($row_result['time'], 3);		
		
	$grft2 = array_keys($arr_grft2);	
	$quantt2 = array_values($arr_grft2);
			 		
}
	$conta = count($arr_grft2);


echo "
<script type='text/javascript'>

$(function () {
    $('#graf_time1').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: '".__('Time spent by requester group','dashboard')."'
        },
        xAxis: {
            categories: ['" ._n('Group','Groups',2). "']
        },
        yAxis: {
            min: 0,
            title: {
                text: '" ._n('Hour','Hours',2)."'
            },
            stackLabels: {
                enabled: true,
                style: {
                    fontWeight: 'bold',
                    color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                }
            }
        },

        tooltip: {
            formatter: function () {
                return '<b>' + this.series.name + '</b><br/>' +                    
                    Highcharts.numberFormat(this.y, 2) + ' h<br>' +
                    'Total: ' + Highcharts.numberFormat(this.point.stackTotal, 2) + ' h';
            }
        },
        plotOptions: {
            column: {
                stacking: 'normal',
                dataLabels: {
						  type: 'datetime',
  		              dateTimeLabelFormats: {
               	  hour: '%H:%M'
            			}, 
            		formatter: function() 
            		{
                  return ''+ Highcharts.numberFormat(this.y, 2) + ' h';
            		},               	
                	  //format: '{point.y} h - ( {point.percentage:.1f}% )',                	  
                    enabled: true,
                    color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
                    style: {
                        textShadow: '0 0 3px black, 0 0 3px black'
                    }
                }
            }
        },
        series: [ ";
          for($i = 0; $i < $conta; $i++) {  
          	if(date('H:i',mktime(0,0,$quantt2[$i])) != 0) {  
						echo "{ name: '". $grft2[$i]."',"	;	
						echo "data: [".date('H',mktime(0,0,$quantt2[$i]))."] },";			
					}
			}				
        
        echo "]
    });
});
    
</script>
";

					}
				?>
			</div>



		</div>
	</div>
</div>
</div>


<!-- Highcharts export xls, csv -->
<script src="../js/export-csv.js"></script>

</body>
</html>