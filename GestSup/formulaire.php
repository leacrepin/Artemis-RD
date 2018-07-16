<?php
ob_start();

/**
 * Récupérer la véritable adresse IP d'un visiteur
 */
function get_ip() {
	// IP si internet partagé
	if (isset($_SERVER['HTTP_CLIENT_IP'])) {
		return $_SERVER['HTTP_CLIENT_IP'];
	}
	// IP derrière un proxy
	elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		return $_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	// Sinon : IP normale
	else {
		return (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '');
	}
}

if(!isset($_GET['send'])) $_GET['send'] = '';
if(!isset($_GET['place'])) $_GET['place'] = '';
if(!isset($_GET['u_group'])) $_GET['u_group']= '';
if(!isset($_GET['lastname'])) $_GET['lastname'] = '';
if(!isset($_GET['usermail'])) $_GET['usermail'] = '';
if(!isset($_GET['phone'])) $_GET['phone'] = '';
if(!isset($_GET['text'])) $_GET['text'] = '';

if(!isset($_POST['send'])) $_POST['send']= $_GET['send'];
if(!isset($_POST['place'])) $_POST['place']= $_GET['place'];
if(!isset($_POST['u_group'])) $_POST['u_group']= $_GET['u_group'];
if(!isset($_POST['lastname'])) $_POST['lastname']= $_GET['lastname'];
if(!isset($_POST['usermail'])) $_POST['usermail']= $_GET['usermail'];
if(!isset($_POST['phone'])) $_POST['phone']= $_GET['phone'];
if(!isset($_POST['text'])) $_POST['text']= $_GET['text'];

require "connect.php";

mysql_query("SET NAMES 'utf8'"); 

$query=$db->query("SELECT IP_WAN FROM glpi_users WHERE glpi=1");
while($ip=$query->fetch()){
	if($ip["IP_WAN"]===get_ip()){
		header('Location: http://185.50.52.133/artemis/glpi&output=embed');
	}
}
$query->closeCursor(); 


$query=$db->query("SELECT * FROM tparameters");
$rparameters=$query->fetch();
$query->closeCursor(); 

$date = date('o')."-".date('m')."-".date('d')." ".date('H').":".date('i').":".date('s');

$phoneReg = '^(\+\d{1,2}\s)?\(?\d{3}\)?[\s.-]?\d{3}[\s.-]?\d{4}$';
$mailReg = '^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$';
?>


<!DOCTYPE html>
<html lang="fr">
	<head>
	    <?php header('x-ua-compatible: ie=edge'); //disable ie compatibility mode ?>
		<meta charset="UTF-8" />
		<!-- <?php if (($rparameters['auto_refresh']!=0)&&($_GET['page']=='dashboard')&&($_GET['searchengine']==0)) echo '<meta http-equiv="Refresh" content="'.$rparameters['auto_refresh'].';">'; ?> -->
		<link rel="shortcut icon" type="image/ico" href="./images/favicon.ico" />
		<meta name="description" content="gestsup" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<!-- basic styles -->
		<link href="./template/assets/css/bootstrap.min.css" rel="stylesheet" />
		<link rel="stylesheet" href="./template/assets/css/font-awesome.min.css" />
		<!-- timepicker styles -->
		<link rel="stylesheet" href="template/assets/css/bootstrap-timepicker.css" />


		<!--[if IE 7]>
		  <link rel="stylesheet" href="./template/assets/css/font-awesome-ie7.min.css" />
		<![endif]-->
		<!-- page specific plugin styles -->
		<!-- fonts -->
		<link rel="stylesheet" href="./template/assets/css/ace-fonts.css" />
		<link rel="stylesheet" href="./template/assets/css/jquery-ui-1.10.3.full.min.css" />
		<!-- ace styles -->
		<link rel="stylesheet" href="./template/assets/css/ace.min.css" />
		<link rel="stylesheet" href="./template/assets/css/ace-rtl.min.css" />
		<link rel="stylesheet" href="./template/assets/css/ace-skins.min.css" />
		
		
		<!--[if lte IE 8]>
		  <link rel="stylesheet" href="./template/assets/css/ace-ie.min.css" />
		<![endif]-->
		<!-- inline styles related to this page -->
		<!-- ace settings handler -->
		<script src="./template/assets/js/ace-extra.min.js"></script>
		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
		<script src="./template/assets/js/html5shiv.js"></script>
		<script src="./template/assets/js/respond.min.js"></script>
		<![endif]-->
		<style>
		input[type=text]
		{
		  background-color: #B2D2CB;
		  border: none;
		  border-color: red;
		  width: 100%;
		  length: 100%;
		}
		input[type=text]:focus
		{
		  background-color: #B2D2CB;
		  border: none;
		  border-color: red;
		}
		select
		{
		  background-color: #B2D2CB;
		  border: none;
		  border-color: red;
		  width: 100%;
		  length: 100%;
		}
		select:focus
		{
		  background-color: #B2D2CB;
		  border: none;
		  border-color: red;
		}
		textarea
		{
		  background-color: #B2D2CB;
		  border: none;
		  border-color: red;
		  width: 100%;
		  length: 100%;
		}
		textarea:focus
		{
		  background-color: #B2D2CB;
		  border: none;
		  border-color: red;
		}
		.btn-send
		{
			background-color: #009AB2 !important;
			border-color: #009AB2 !improtant;
		}
		</style>
	</head>
	<body>
		<div id="row">
			<div class="widget-box">
				<div class="widget-header">
					<h4>
						<i class="icon-ticket"></i>
							Envoi d'un ticket support
						</i>
					</h4>
				</div>
				<div class="widget-body">
					<div class="widget-main">
						<form name="form" method="get">
							<table>
								<tr>
									<td><input name="u_group" type="text" size="26" id="u_group" placeholder="Etablissement"></td>
									<td><input name="place" type="text" size="26" id="place" placeholder="Site"></td>
								<tr>
									<td><input  name="phone" type="text" size="26" required pattern = <?php echo $phoneReg; ?> placeholder="Téléphone: XXXXXXXXXX" title="Numéro de téléphone sans espace"></td>
									<td rowspan = 3><textarea  name="text" type="text" rows="4" cols="50" required style="resize:none;" placeholder="Incident"></textarea></td>
								</tr>
								<tr>
									<td><input  name="usermail" type="text" value="" size="26" required pattern = <?php echo $mailReg; ?> placeholder="Email" title="Adresse email en minuscules"></td>
								</tr>
								<tr>
									<td><input  name="lastname" type="text" size="26" required placeholder="Nom"></td>
								</tr>
							</table></br></br>
							
							<input name="send" type="submit" id="send" value="Envoyer" class="btn btn-sm btn-send">
							<?php
								if($_POST['send'] == 'Envoyer')
								{				

									$incident = mysql_real_escape_string($_POST['text']) . "      " . $_POST['lastname'] . "        " . $_POST['phone'];
									
									
									
								
										
									$query = $db->prepare("INSERT INTO `tincidents` (`type`, `technician`, `t_group`, `title`, `description`, `user`, `u_service`, `date_create`, `date_hope`, `date_res`, `date_modif`, `state`, `priority`, `criticality`, `img1`, `img2`, `img3`, `img4`, `img5`, `time`, `time_hope`, `creator`, `category`, `subcat`, `techread`, `template`, `disable`, `notify`, `place`, `start_availability`, `end_availability`, `availability_planned`, `telephone`) VALUES (1, 1, 16, '".$incident."', '" . $incident . "' , '', 7, '$date', '0000-00-00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 6, 3, '', '', '', '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '" .$telephone."')");
									$query->execute();
									
									$id = $db->lastInsertId();


									$subjectToClient = "Prise en charge de votre demande : Ticket n°$id";
									$subject = "Mail automatique pour la création du ticket n°$id";
									$message = "
												<html>
													<body>
														<font face=\"Arial\">
															<table  border=\"1\" bordercolor=\"#FFFFFF\" cellspacing=\"0\"  cellpadding=\"5\">
																<tr>
																	<td colspan=\"2\"><font color=\"#000000\">Ticket n°$id.</font></td>
																</tr>
																<tr>
																	<td width=\"400px\"><font color=\"#000000\"><b>Nom:</b> $_POST[lastname]</font></td>
																	<td width=\"400px\"><font color=\"#000000\"><b>Mail:</b> $_POST[usermail]</font></td>
																</tr>
																<tr>
																	<td><font color=\"#000000\"><b>Lieu:</b> $_POST[place]</font></td>
																	<td><font color=\"#000000\"><b>Groupe:</b> $_POST[u_group]</font></td>	
																</tr> 
																<tr>
																	<td><font color=\"#000000\"><b>Tel:</b> $_POST[phone]</font></td>
																</tr> 
																<tr>
																	<td colspan=\"2\"><font color=\"#000000\"><b>Message:</b><br /> $_POST[text]</font></td>
																</tr>
															</table>
														</font>
													</body>
												</html>"."\r\n";

												
									//mail($to, $subject, $message, $headers);
									
									include("components/PHPMailer-5.2.13/PHPMailerAutoload.php"); 
									$mail = new PHPmailer();
									$mail->SMTPOptions = array(
										'ssl' => array(
											'verify_peer' => false,
											'verify_peer_name' => false,
											'allow_self_signed' => true
										)
									);
									$mail->CharSet = 'UTF-8'; //ISO-8859-1 possible if characters problems
									$mail->IsSMTP(); //$mail->isSendMail(); works for 1&1
									if($rparameters['mail_secure']=='SSL') 
									{$mail->Host = "ssl://$rparameters[mail_smtp]";} 
									elseif($rparameters['mail_secure']=='TLS') 
									{$mail->Host = "tls://$rparameters[mail_smtp]";} 
									else 
									{$mail->Host = "$rparameters[mail_smtp]";}
									$mail->SMTPAuth = $rparameters['mail_auth'];
									if ($rparameters['debug']==1) $mail->SMTPDebug = 2;
									if ($rparameters['mail_secure']!=0) $mail->SMTPSecure = $rparameters['mail_secure'];
									if ($rparameters['mail_port']!=25) $mail->Port = $rparameters['mail_port'];
									$mail->Username = "$rparameters[mail_username]";
									$mail->Password = "$rparameters[mail_password]";
									$mail->IsHTML(true); 
									$mail->From = "$rparameters[mail_from_adr]";
									$mail->FromName = "$rparameters[mail_from_name]";
									$mail->AddAddress('support@artemis-rd.fr');
									$mail->Subject = "$subject";
									$mail->Body = "$message";
									
									if(!$mail->send()) {
										echo 'ERREUR: ' . $mail->ErrorInfo;
									} else {
										header('location:http://www.artemis-rd.com/#!support/icmx5');
									}
									
									$mail->SmtpClose();
								}
							?>
							
						</form>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>