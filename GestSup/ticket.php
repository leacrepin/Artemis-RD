<?php
################################################################################
# @Name : ticket.php 
# @Description : page to display: create and edit ticket
# @call : /dashboard.php
# @Author : Flox
# @Version : 3.1.14
# @Create : 07/01/2007
# @Update : 19/12/2016
################################################################################

//initialize variables 
if(!isset($userreg)) $userreg = ''; 
if(!isset($category)) $category = ''; 
if(!isset($subcat)) $subcat = ''; 
if(!isset($title)) $title = ''; 
if(!isset($date_hope)) $date_hope = ''; 
if(!isset($date_create)) $date_create = ''; 
if(!isset($state)) $state = ''; 
if(!isset($description)) $description = ''; 
if(!isset($resolution)) $resolution = ''; 
if(!isset($priority)) $priority = '';
if(!isset($percentage)) $percentage = '';
if(!isset($id)) $id = '';
if(!isset($id_in)) $id_in = '';
if(!isset($save)) $save = '';
if(!isset($techread)) $techread = '';
if(!isset($next)) $next = '';
if(!isset($previous)) $previous = '';
if(!isset($user)) $user = '';
if(!isset($down)) $down = '';
if(!isset($u_group)) $u_group = '';
if(!isset($t_group)) $t_group = '';
if(!isset($userid)) $userid = '';
if(!isset($u_service)) $u_service = '';
if(!isset($date_hope_error)) $date_hope_error = '';
if(!isset($selected_time)) $selected_time = '';

if(!isset($_POST['mail'])) $_POST['mail'] = '';
if(!isset($_POST['upload'])) $_POST['upload'] = '';
if(!isset($_POST['title'])) $_POST['title'] = '';
if(!isset($_POST['description'])) $_POST['description'] = '';
if(!isset($_POST['resolution'])) $_POST['resolution'] = '';
if(!isset($_POST['Submit'])) $_POST['Submit'] = '';
if(!isset($_POST['subcat'])) $_POST['subcat'] = '';
if(!isset($_POST['user'])) $_POST['user'] = '';
if(!isset($_POST['type'])) $_POST['type'] = '';
if(!isset($_POST['modify'])) $_POST['modify'] = '';
if(!isset($_POST['quit'])) $_POST['quit'] = '';
if(!isset($_POST['date_create'])) $_POST['date_create'] = '';
if(!isset($_POST['date_hope'])) $_POST['date_hope'] = '';
if(!isset($_POST['date_res'])) $_POST['date_res'] = '';
if(!isset($_POST['priority'])) $_POST['priority'] = '';
if(!isset($_POST['criticality'])) $_POST['criticality'] = '';
if(!isset($_POST['category'])) $_POST['category'] = '';
if(!isset($_POST['time'])) $_POST['time'] = '';
if(!isset($_POST['time_hope'])) $_POST['time_hope'] = '';
if(!isset($_POST['state'])) $_POST['state'] = '';
if(!isset($_POST['cancel'])) $_POST['cancel'] = '';
if(!isset($_POST['technician'])) $_POST['technician'] = '';
if(!isset($_POST['ticket_places'])) $_POST['ticket_places'] = '';
if(!isset($_POST['text2'])) $_POST['text2'] = '';
if(!isset($_POST['start_availability_d'])) $_POST['start_availability_d'] = '';
if(!isset($_POST['end_availability_d'])) $_POST['end_availability_d'] = '';

if(!isset($_GET['id'])) $_GET['id'] = '';
if(!isset($_GET['action'])) $_GET['action'] = '';
if(!isset($_GET['threadedit'])) $_GET['threadedit'] = '';

if(!isset($globalrow['technician'])) $globalrow['technician'] = '';
if(!isset($globalrow['time'])) $globalrow['time'] = '';

//core ticket actions
include('./core/ticket.php');

//defaults values for new tickets
if(!isset($globalrow['creator'])) $globalrow['creator'] = '';
if(!isset($globalrow['t_group'])) $globalrow['t_group'] = '';
if(!isset($globalrow['u_group'])) $globalrow['u_group'] = '';
if(!isset($globalrow['category'])) $globalrow['category'] = '';
if(!isset($globalrow['subcat'])) $globalrow['subcat'] = '';
if(!isset($globalrow['title'])) $globalrow['title'] = '';
if(!isset($globalrow['description'])) $globalrow['description'] = '';
if(!isset($globalrow['date_create'])) $globalrow['date_create'] = date("Y-m-d").' '.date("H:i:s");
if(!isset($globalrow['date_hope'])) $globalrow['date_hope'] = '';
if(!isset($globalrow['date_res'])) $globalrow['date_res'] = '';
if(!isset($globalrow['time_hope'])) $globalrow['time_hope'] = '5';
if(!isset($globalrow['time'])) $globalrow['time'] = '';
if(!isset($globalrow['priority'])) $globalrow['priority'] = '6'; 
if(!isset($globalrow['criticality'])) $globalrow['criticality'] = '4';
if(!isset($globalrow['state'])) $globalrow['state'] = '1';
if(!isset($globalrow['type'])) $globalrow['type'] = '1';
if(!isset($globalrow['start_availability'])) $globalrow['start_availability'] = date("Y-m-d").' '.date("H:i:s");
if(!isset($globalrow['end_availability'])) $globalrow['end_availability'] = date("Y-m-d").' '.date("H:i:s");
if(!isset($globalrow['availability_planned'])) $globalrow['availability_planned'] = 0;
if(!isset($globalrow['place'])) $globalrow['place'] = '0';

//default values for tech and admin
if($_SESSION['profile_id']==4 || $_SESSION['profile_id']==0)
{
	if(!isset($globalrow['technician'])) $globalrow['technician']=$_SESSION['user_id'];
	if(!isset($globalrow['user'])) $globalrow['user']=0;
} else {
	if(!isset($globalrow['technician'])) $globalrow['technician']='';
	if(!isset($globalrow['user'])) $globalrow['user']=$_SESSION['user_id'];
}

?>
<div id="row">
	<div class="col-xs-12">
		<div class="widget-box">
			<form class="form-horizontal" name="myform" id="myform" enctype="multipart/form-data" method="post" action="" onsubmit="loadVal();" >
				<div class="widget-header">
					<h4>
						<i class="icon-ticket"></i>
						<?php
    						//display widget title
    						if($_GET['action']=='new') echo T_('Ouverture du ticket').' n° '.$_GET['id'].''; else echo T_('Édition du ticket').' '.$_GET['id'].' '.$percentage.':  '.$title.'';
    						//display clock if alarm 
							$query=$db->query('SELECT * FROM tevents WHERE incident='.$_GET['id'].' and disable=0');
							$alarm=$query->fetch();
    						if($alarm) echo ' <i class="icon-bell-alt green" title="'.T_('Alarme activée le').' '.$alarm['date_start'].'" /></i>';
						?>
					</h4>
					<span class="widget-toolbar">
						<?php 
							if ($rright['ticket_next']!=0)
							{
								if($previous[0]!='') echo'<a href="./index.php?page=ticket&amp;id='.$previous[0].'&amp;state='.$state.'&amp;userid='.$userid.'"><i title="'.T_('Ticket précédent de cet état').'" class="icon-circle-arrow-left bigger-130"></i>&nbsp;'; 
								if($next[0]!='') echo'<a href="./index.php?page=ticket&amp;id='.$next[0].'&amp;state='.$state.'&amp;userid='.$userid.' "><i title="'.T_('Ticket suivant de cet état').'" class="icon-circle-arrow-right bigger-130"></i></a>';
							}
							if ($rright['ticket_print']!=0)
							{
								//truncate token table
								$db->exec("TRUNCATE ttoken");
								$token_ticket_print =uniqid(); //secure ticket access page
								$db->exec("INSERT INTO ttoken (token) VALUES ('$token_ticket_print')");
								echo "&nbsp;";
								echo '<a target="_blank" href="./ticket_print.php?id='.$_GET['id'].'&token='.$token_ticket_print.'"><i title="'.T_('Imprimer ce ticket').'" class="icon-print green bigger-130"></i></a>';
							}
							if ($rright['ticket_template']!=0 && $_GET['action']=='new')
							{
								echo "&nbsp;";
								echo '<a href="./index.php?page=ticket&id='.$_GET['id'].'&userid='.$_GET['userid'].'&state='.$_GET['state'].'&action=template"><i title="'.T_('Modèle de tickets').'" class="icon-tags pink bigger-130"></i></a>';
							}
							if ($rright['ticket_event']!=0)
							{
								echo "&nbsp;&nbsp;";
								echo'<i onclick="parent.location=\'./index.php?page=ticket&id='.$_GET['id'].'&userid='.$_GET['userid'].'&state='.$_GET['state'].'&action=addevent&technician='.$_SESSION['user_id'].'\'" title="'.T_('Créer un rappel pour ce ticket').'" class="icon-bell-alt bigger-130 orange"></i>';
							}
							if (($rright['planning']!=0) && ($rparameters['planning']==1) && ($rright['ticket_calendar']!=0)) 
							{
								echo "&nbsp;&nbsp;";
								echo'<i onclick="parent.location=\'./index.php?page=ticket&id='.$_GET['id'].'&userid='.$_GET['userid'].'&state='.$_GET['state'].'&action=addcalendar&technician='.$_SESSION['user_id'].'\'" title="'.T_('Planifier une intervention dans le calendrier').'" class="icon-calendar bigger-130 purple"></i>';
							}
							if ($rright['ticket_delete']!=0 && $_GET['action']!='new')
							{
								echo "&nbsp;&nbsp;";
								echo '<a href="./index.php?page=ticket&id='.$_GET['id'].'&userid='.$_GET['userid'].'&state='.$_GET['state'].'&action=delete"><i title="'.T_('Supprimer ce ticket').'" class="icon-trash red bigger-130"></i></a>';
							}
							if ($rright['ticket_save']!=0)
							{
								echo "&nbsp;&nbsp;";
								echo '<button class="btn btn-minier btn-success" title="'.T_('Sauvegarder').'" name="modify" value="submit" type="submit" id="modify"><i class="icon-save bigger-140"></i></button>';
                                echo "&nbsp;&nbsp;";
                                echo '<button class="btn btn-minier btn-purple" title="'.T_('Sauvegarder et quitter').'" name="quit" value="quit" type="submit" id="quit"><i class="icon-save bigger-140"></i></button>';
							}
							?>
					</span>
				</div>

				<div class="widget-body">
					<div class="widget-main">
						<!-- START sender part -->	
						<div class="form-group <?php if(($rright['ticket_user_disp']==0 && $_GET['action']!='new') || ($rright['ticket_new_user_disp']==0 && $_GET['action']=='new')) echo 'hide';?>" >
							<label class="col-sm-2 control-label no-padding-right" for="user">
								<?php if (($_POST['user']==0) && ($globalrow['user']==0) && ($u_group=='')) echo '<i title="'.T_('Sélectionner un demandeur').'" class="icon-warning-sign red bigger-130"></i>&nbsp;'; ?>
								<?php echo T_('Demandeur').':'; ?>
							</label>
							<div class="col-sm-9">
								<!-- START sender list part -->
								<select id="user" name="user" onchange="loadVal(); submit();" <?php if(($rright['ticket_user']==0 && $_GET['action']!='new') || ($rright['ticket_new_user']==0 && $_GET['action']=='new')) echo ' disabled="disabled" ';?> >
									<?php
									//display user list and keep selected an disable user
									$query = $db->query("SELECT * FROM `tusers` ORDER BY lastname ASC, firstname ASC");
									while ($row = $query->fetch()) 
									{
										if ($_POST['user']==$row['id']) {$selected='selected';} elseif (($_POST['user']=='') && ($globalrow['user']==$row['id'])) {$selected='selected';} else {$selected='';} 
										if ($row['id']==0) {echo '<option '.$selected.' value="'.$row['id'].'">'.T_(" $row[lastname]").' '.$row['firstname'].'</option>';} //case no user
										if ($row['disable']==0) {echo '<option '.$selected.' value="'.$row['id'].'">'.$row['lastname'].' '.$row['firstname'].'</option>';} //all enable users and technician
										if (($row['disable']==1) && ($selected=='selected') && $_POST['user']!=0) {echo '<option '.$selected.' value="'.$row['id'].'">'.$row['lastname'].' '.$row['firstname'].'</option>';} //case disable user always attached to this ticket
									}

									$query->closeCursor(); 
									//display group list and keep selected an disable group
									$query = $db->query("SELECT distinct tgroups.id,tgroups.name,tgroups.type,tgroups.disable FROM `tgroups` LEFT OUTER JOIN glpi_users ON tgroups.id=glpi_users.id_group WHERE tgroups.type=0 AND glpi_users.glpi is NULL OR glpi_users.glpi=0 ORDER BY tgroups.name");
									while ($row = $query->fetch()) 
									{
										if ($row['id']==$u_group) {$selected='selected';} else {$selected='';}
										if ($row['disable']==0) {echo '<option '.$selected.' value="G_'.$row['id'].'">[G] '.T_(" $row[name]").'</option>';}
										if (($row['disable']==1) && ($selected=='selected')) {echo '<option '.$selected.' value="G_'.$row['id'].'">[G] '.$row['name'].'</option>';}

									}
									$query->closeCursor(); 

									?>
								</select>
								
								<?php if(($rright['ticket_user']==0 && $_GET['action']!='new') || ($rright['ticket_new_user']==0 && $_GET['action']=='new')) echo ' <input type="hidden" name="user" value='.$globalrow['user'].' /> '; //send data in disabled case?>
								<!-- END sender list part -->
								<!-- START sender actions part -->
								<?php
								if ($rright['ticket_user_actions']!=0)
								{
								    echo'<input type="hidden" name="action" value="">';
								    echo'<input type="hidden" name="edituser" value="">';
									echo '&nbsp;&nbsp;<i class="icon-plus-sign green bigger-130" title="'.T_('Ajouter un utilisateur').'" onclick="loadVal(); document.forms[\'myform\'].action.value=\'adduser\';document.forms[\'myform\'].submit();"></i>&nbsp;&nbsp;';
									if ($u_group!=0)
									{
									    echo '<i class="icon-pencil orange bigger-130" title="'.T_('Modifier le groupe').'" value="useredit" onClick="parent.location=\'./index.php?page=ticket&id='.$_GET['id'].'&userid='.$_GET['userid'].'&action=edituser&edituser='.$u_group.'\'"  /></i>&nbsp;&nbsp;';
									}
									else
									{
										if ($_POST['user']) $selecteduser=$_POST['user']; else $selecteduser=$globalrow['user'];
						                echo '<i class="icon-pencil orange bigger-130" title="'.T_('Modifier un utilisateur').'" onclick="loadVal(); document.forms[\'myform\'].action.value=\'edituser\';document.forms[\'myform\'].edituser.value=\''.$selecteduser.'\';document.forms[\'myform\'].submit();"></i>&nbsp;&nbsp;';
									}
								}	

								?>
								<!-- END sender actions part -->
								<!-- START user info part -->
									<?php
									//Display asset tel fax department if exist
									if ($u_group=='')
									{
										if ($_POST['user']) 
										{
											$query = $db->query("SELECT * FROM `tusers` WHERE id LIKE '$_POST[user]'"); 
										}
										else
										{
											$query = $db->query("SELECT * FROM `tusers` WHERE id LIKE '$globalrow[user]'"); 
										}
										$row=$query->fetch();
										$query->closeCursor(); 
										echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
										if ($row['phone']!="") echo '&nbsp;&nbsp;&nbsp;<i title="'.T_('Téléphone').'" class="icon-phone-sign blue bigger-130"></i> <b>'.$row['phone'].'</b>';
										if ($row['mail']!="") echo '&nbsp;&nbsp;&nbsp;<a href="mailto:'.$row['mail'].'"><i title="'.$row['mail'].'" class="icon-envelope blue bigger-130"></i></a>';
										if ($row['function']!="") echo '&nbsp;&nbsp;&nbsp;<i title="'.T_('Fonction').'" class="icon-user blue bigger-130"></i> '.$row['function'];
										if ($row['service']!=0) 
										{
											$query=$db->query("SELECT name FROM tservices WHERE id='$row[service]'"); 
											$g_service_name=$query->fetch();
											$query->closeCursor();
											echo '&nbsp;&nbsp;&nbsp;<i title="'.T_('Service').'" class="icon-group blue bigger-120"></i> '.$g_service_name[0];
										}
										if ($row['company']!=0) 
										{
											$query=$db->query("SELECT * FROM tcompany WHERE id='$row[company]'"); 
											$g_company_name=$query->fetch();
											$query->closeCursor();
											echo '&nbsp;&nbsp;&nbsp;<i title="'.T_('Société').': '.$g_company_name['name'].' '.$g_company_name['address'].' '.$g_company_name['zip'].' '.$g_company_name['city'].'" class="icon-building blue bigger-130"></i> '.$g_company_name['name'];
										}
										//find associated asset
										if ($_POST['user']) {$query = $db->query("SELECT * FROM `tassets` WHERE user LIKE '$_POST[user]' AND state='2' AND ip!='' AND user!='0' ORDER BY id DESC");} else {$query = $db->query("SELECT * FROM `tassets` WHERE user LIKE '$globalrow[user]' AND state='2'  AND ip!='' AND user!='0' ORDER BY id DESC");}
										$row=$query->fetch();
										if ($row['ip']!='') echo '&nbsp;&nbsp;&nbsp;<a target="about_blank" href="./index.php?page=asset&id='.$row['id'].'"><i title="'.T_('Matériel associé').'" class="icon-desktop blue bigger-120"></i></a> '.$row['netbios'];
										$query->closeCursor(); 
									}
									//other demands for this user or group
									if ($u_group)
									{
										$umodif=$u_group;
										$usergroup="u_group";
									} else {
										if($_POST['user']) $umodif=$_POST['user']; else $umodif=$globalrow['user'];
										$usergroup="user";
									}
									if ($umodif!='') //case for new ticket without sender
									{
										$qn = $db->query("SELECT count(*) FROM `tincidents` WHERE $usergroup LIKE '$umodif' and (state='1' OR state='2' OR state='6' OR state='5') and id NOT LIKE $_GET[id] and disable=0");
										$rn=$qn->fetch();
										$qn->closeCursor();
										$rnn=$rn[0];
										if ($rnn!=0) echo '&nbsp;&nbsp; <i title="'.T_('Autres tickets de cet utilisateur').'" class="icon-ticket blue bigger-130"></i> ';
										$c=0;
										$q = $db->query("SELECT id,title FROM `tincidents` WHERE $usergroup LIKE '$umodif' and (state='1' OR state='2' OR state='6' OR state='5') and id NOT LIKE $_GET[id] and disable=0 ORDER BY id DESC"); 
										while (($r=$q->fetch()) && ($c<2)) {	
											$c=$c+1;
											echo "<a title=\"$r[title]\" href=\"./index.php?page=ticket&amp;id=$r[id]\">#$r[id]</a>";
											if ($c<$rnn) echo ", ";
											if ($c==2) echo "...";
										}  
										$query->closeCursor();
										if ($rnn!=0) echo "";
									}
									?>
								<!-- START user info part -->
							</div>
						</div>
						<!-- END sender part -->
				        <!-- START type part -->
				        <?php
				            if($rparameters['ticket_type']==1)
				            {
				                echo'
				                <div class="form-group '; if(($rright['ticket_type_disp']==0 && $_GET['action']!='new') || ($rright['ticket_new_type_disp']==0 && $_GET['action']=='new')) {echo 'hide';} echo'">
        							<label class="col-sm-2 control-label no-padding-right" for="type">
        							    ';
											if (($_POST['type']==0) && ($globalrow['type']==0)) {echo '<i title="'.T_('Sélectionner un type').'" class="icon-warning-sign red bigger-130"></i>&nbsp;';} 
										echo'
        							    '.T_('Type').':
        							</label>
        							<div class="col-sm-8">
        							    <select  id="type" name="type"'; if(($rright['ticket_type']==0 && $_GET['action']!='new') || ($rright['ticket_new_type']==0 && $_GET['action']=='new')) {echo 'disabled="disabled"';} echo'>';
        									if ($_POST['type'])
        									{
												$query2=$db->query("SELECT * FROM `ttypes` WHERE id='$_POST[type]'");
												$row2=$query2->fetch();
												$query2->closeCursor(); 
        										echo '<option value="'.$_POST['type'].'" selected >'.T_($row2['name']).'</option>';
        										$query2 = $db->query("SELECT * FROM `ttypes` WHERE id!='$_POST[type]'");
        							    		while ($row2 = $query2->fetch()) echo '<option value="'.$row2['id'].'">'.T_($row2['name']).'</option>';
												$query2->closeCursor(); 
        									}
        									else
        									{
        										$query2=$db->query("SELECT * FROM `ttypes` WHERE id='$globalrow[type]' ORDER BY id");
        										$row2=$query2->fetch();
												$query2->closeCursor();
        										echo '<option value="'.$globalrow['type'].'" selected >'.T_($row2['name']).'</option>';
        										$query2 = $db->query("SELECT * FROM `ttypes` WHERE id!='$globalrow[type]'");
        								    	while ($row2 = $query2->fetch()) echo '<option value="'.$row2['id'].'">'.T_($row2['name']).'</option>';
												$query2->closeCursor(); 
        									}
        									echo'			
        								</select>
										';
										//send data in disabled case
										if($rright['ticket_type']==0 && $_GET['action']!='new') echo '<input type="hidden" name="type" value="'.$globalrow['type'].'" />'; 
										echo '
        							</div>
    					    	</div>
    					    	';
				            }
				        ?>
					    <!-- END type part -->	
						<!-- START technician part -->
						<div class="form-group <?php if(($rright['ticket_tech_disp']==0 && $_GET['action']!='new') || ($rright['ticket_new_tech_disp']==0 && $_GET['action']=='new')) echo 'hide';?>">
							<label class="col-sm-2 control-label no-padding-right" for="technician">
							<?php if($globalrow['technician']==0 && $globalrow['t_group']==0 && $_POST['technician']==0 ) echo '<i title="'.T_('Aucun technicien associé à ce ticket').'" class="icon-warning-sign red bigger-130"></i>&nbsp;'; ?>
							<?php echo T_('Technicien').':'; ?>
							</label>
							<div class="col-sm-8 ">
								<select id="technician" name="technician" onchange="loadVal(); submit();" <?php if($rright['ticket_tech']==0) echo ' disabled="disabled" ';?> >
									<?php
									//display technician list
									$query = $db->query("SELECT * FROM `tusers` WHERE (profile='0' || profile='4') OR id=0 ORDER BY lastname ASC, firstname ASC") ;
									while ($row = $query->fetch()) 
									{
										if ($_POST['technician']==$row['id']) {$selected='selected';} elseif (($_POST['technician']=='') && ($globalrow['technician']==$row['id'])) {$selected='selected';} else {$selected='';} 
										if ($row['id']==0) {echo '<option '.$selected.' value="'.$row['id'].'">'.T_($row['lastname']).' '.$row['firstname'].'</option>';} //case no technician
										if ($row['disable']==0) {echo '<option '.$selected.' value="'.$row['id'].'">'.$row['lastname'].' '.$row['firstname'].'</option>';} //all enable technician
										if (($row['disable']==1) && ($selected=='selected') && $_POST['technician']!=0) {echo '<option '.$selected.' value="'.$row['id'].'">'.$row['lastname'].' '.$row['firstname'].'</option>';} //case disable technician always attached to this ticket
									} 
									//display technician group list
									$query = $db->query("SELECT * FROM `tgroups` WHERE type='1' ORDER BY name");
									while ($row = $query->fetch()) {
										//echo "<option value=\"G_$row[id]\">[G] $row[name]</option>";
										if ($row['id']==$t_group) {$selected='selected';} else {$selected='';}
										if ($row['disable']==0) {echo '<option '.$selected.' value="G_'.$row['id'].'">[G] '.T_($row['name']).'</option>';}
										if (($row['disable']==1) && ($selected=='selected')) {echo '<option '.$selected.' value="G_'.$row['id'].'">[G] '.$row['name'].'</option>';}
									}
									?>
								</select>
								<?php 
								//send data in disabled case
								if($rright['ticket_tech']==0) echo '<input type="hidden" name="technician" value="'.$globalrow['technician'].'" />'; 
								//display open user
								if (($globalrow['creator']!=$globalrow['technician']) && ($globalrow['creator']!="0") && $_GET['action']!='new')
								{
									//select creator name
									$query = $db->query("SELECT * FROM `tusers` WHERE id LIKE '$globalrow[creator]'");
									$row=$query->fetch();
									$query->closeCursor(); 
									echo '&nbsp;<i class="icon-user blue bigger-130"></i>&nbsp;'.T_('Ouvert par').' '.$row['firstname'].' '.$row['lastname'];
								}
								?>
							</div>
						</div>
						<!-- END technician part -->
						<!-- START category part -->
						<div class="form-group <?php if(($rright['ticket_cat_disp']==0 && $_GET['action']!='new') || ($rright['ticket_new_cat_disp']==0 && $_GET['action']=='new')) echo 'hide';?>">
							<label class="col-sm-2 control-label no-padding-right" for="category">
								<?php if(($globalrow['category']==0) && ($_POST['category']==0)) echo '<i title="'.T_('Aucune catégorie associée').'." class="icon-warning-sign red bigger-130"></i>&nbsp;'; ?>
								<?php echo T_('Catégorie').':'; ?>
							</label>
							<div class="col-sm-8">
								<select title="<?php echo T_('Catégorie'); ?>" id="category" name="category" onchange="loadVal(); submit();" <?php if(($rright['ticket_cat']==0 && $_GET['action']!='new') || ($rright['ticket_new_cat']==0 && $_GET['action']=='new')) echo ' disabled="disabled" ';?>>
								<?php
									$query= $db->query("SELECT distinct tcategory.id,tcategory.name FROM `tcategory` LEFT OUTER JOIN glpi_users ON tcategory.id=glpi_users.id_cat WHERE glpi_users.glpi is NULL OR glpi_users.glpi=0 ORDER BY tcategory.id!=0, tcategory.name"); //order to display none in first
									while ($row = $query->fetch()) 
									{
										if ($row['id']==0) {$row['name']=T_($row['name']);} //translate only none
										if ($_POST['category']!=''){if ($_POST['category']==$row['id']) echo '<option value="'.$row['id'].'" selected>'.$row['name'].'</option>'; else echo '<option value="'.$row['id'].'">'.T_($row['name']).'</option>';}
										else
										{if ($globalrow['category']==$row['id']) echo '<option value="'.$row['id'].'" selected>'.T_($row['name']).'</option>'; else echo '<option value="'.$row['id'].'">'.T_($row['name']).'</option>';}
									}
									$query->closeCursor();
								?>
								</select>
								<?php if(($rright['ticket_cat']==0 && $_GET['action']!='new') || ($rright['ticket_new_cat']==0 && $_GET['action']=='new'))  echo '<input type="hidden" name="category" value="'.$globalrow['category'].'" />'; //send data in disabled case ?>
								<select  title="<?php echo T_('Sous-catégorie'); ?>" id="subcat" name="subcat" onchange="loadVal(); submit();" <?php if(($rright['ticket_cat']==0 && $_GET['action']!='new') || ($rright['ticket_new_cat']==0 && $_GET['action']=='new')) echo ' disabled="disabled" ';?> >
								<?php
									if ($_POST['category'])
									{$query= $db->query("SELECT * FROM `tsubcat` WHERE cat LIKE '$_POST[category]' ORDER BY name ASC");}
									else
									{$query= $db->query("SELECT * FROM `tsubcat` WHERE cat LIKE '$globalrow[category]' ORDER BY name ASC");}
									
									while ($row = $query->fetch()) 
									{
										if ($row['id']==0) {$row['name']=T_($row['name']);}
										if ($_POST['subcat'])
										{
											if ($_POST['subcat']==$row['id']) echo '<option value="'.$row['id'].'" selected>'.$row['name'].'</option>'; else echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
										}
										else
										{
											if ($globalrow['subcat']==$row['id']) echo '<option value="'.$row['id'].'" selected>'.$row['name'].'</option>'; else echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
										}
									} 
									$query->closeCursor();
									if ($globalrow['subcat']==0 && $_POST['subcat']==0) echo "<option value=\"\" selected></option>";
								?>
								</select>
								<?php if(($rright['ticket_cat']==0 && $_GET['action']!='new') || ($rright['ticket_new_cat']==0 && $_GET['action']=='new'))  echo '<input type="hidden" name="subcat" value="'.$globalrow['subcat'].'" />'; //send data in disabled case?>
								<?php
								if ($rright['ticket_cat_actions']!=0)
								{
									echo '
									&nbsp;&nbsp;<i class="icon-plus-sign green bigger-130" title="'.T_('Ajouter une catégorie').'" onclick="loadVal(); document.forms[\'myform\'].action.value=\'addcat\';document.forms[\'myform\'].submit();"></i>
									&nbsp;&nbsp;<i class="icon-pencil orange bigger-130" title="'.T_('Modifier une catégorie').'" onclick="loadVal(); document.forms[\'myform\'].action.value=\'editcat\';document.forms[\'myform\'].submit();"></i>&nbsp;&nbsp;
									';
								}
								?>
							</div>
						</div>
						<!-- END category part -->
						<!-- START place part if parameter is on -->
						<?php
						
						
						if($rparameters['ticket_places']==1)
						{
							
							echo '
							<div class="form-group">
								<label class="col-sm-2 control-label no-padding-right" for="ticket_places">'.T_('Lieu').':</label>
								<div class="col-sm-8">
									<select class="textfield" id="ticket_places" name="ticket_places" '; if($rright['ticket_place']==0 && $_GET['action']!='new') {echo 'disabled="disabled"';} echo' > 
										';
										
										if($_POST['ticket_places'])
										{
											
										    $query = $db->query("SELECT tplaces.id,tplaces.name FROM `tplaces` LEFT OUTER JOIN glpi_users ON tplaces.id=glpi_users.id_place WHERE glpi_users.glpi is NULL OR glpi_users.glpi=0 ORDER BY tplaces.name ASC");
    										while ($row = $query->fetch()) 
    										{
    											if ($_POST['ticket_places']==$row['id']) echo '<option selected value="'.$row['id'].'">'.T_($row['name']).'</option>'; else echo '<option value="'.$row['id'].'">'.T_($row['name']).'</option>';
    										}
										
											$query->closeCursor();
											
										} else {
    										$query = $db->query("SELECT tplaces.id,tplaces.name FROM `tplaces` LEFT OUTER JOIN glpi_users ON tplaces.id=glpi_users.id_place WHERE glpi_users.glpi is NULL OR glpi_users.glpi=0 ORDER BY tplaces.name ASC");
    										while ($row = $query->fetch()) 
    										{
    											if ($globalrow['place']==$row['id']) echo '<option selected value="'.$row['id'].'">'.T_($row['name']).'</option>'; else echo '<option value="'.$row['id'].'">'.T_($row['name']).'</option>';
    										}
			$query->closeCursor();
										}
									echo '
									</select>
									';
									
								
									
									
										
									
									if($rright['ticket_place']==0 && $_GET['action']!='new')  echo '<input type="hidden" name="ticket_places" value="'.$globalrow['place'].'" />'; //send data in disabled case
									echo '
								</div>
							</div>
							';
						}
						?>
						<!-- END place part -->
						<!-- START title part -->
						<div class="form-group <?php if($rright['ticket_title_disp']==0) echo 'hide';?>">
							<label class="col-sm-2 control-label no-padding-right" for="title"><?php echo T_('Titre'); ?>:</label>
							<div class="col-sm-8">
								<input  name="title" id="title" type="text" size="50"  value="<?php if ($_POST['title']!='' && $_POST['title']!='\'\'') echo $_POST['title']; else echo htmlspecialchars($globalrow['title']); ?>" <?php if($rright['ticket_title']==0  && $_GET['action']!='new') echo 'readonly="readonly"';?> />
							</div>
						</div>
						<!-- END title part -->
						<!-- START description part -->
						<div class="form-group <?php if($rright['ticket_description_disp']==0) echo 'hide';?>">
							<label class="col-sm-2 control-label no-padding-right" for="text"><?php echo T_('Description'); ?>:</label>
							<div class="col-sm-8">
								<table border="1" width="732" style="border: 1px solid #D8D8D8;" <?php if ($rright['ticket_description']==0) echo 'cellpadding="10"'; ?> >
									<tr>
										<td>
											<?php
											if ($rright['ticket_description']!=0 || $_GET['action']=='new')
											{	
												//display editor
												echo '
												<div id="editor" class="wysiwyg-editor" style="min-height:80px; ">';
											    	if ($_POST['text'] && $_POST['text']!='') echo "$_POST[text]"; else echo $globalrow['title'];
										            if ($_GET['action']=='new' && !$_POST['user']) {echo '';}	 echo'
												</div>
												<input type="hidden" id="text" name="text" />
												';
											} else {
												echo $globalrow['title'];
												echo '<input type="hidden" name="text" value="'.htmlentities($globalrow['title']).'" />';
											}
											?>
										</td>
									</tr>
								</table>
							</div>
						</div>		
						<!-- END description part -->
						<!-- START resolution part -->
						<div class="form-group <?php if(($rright['ticket_resolution_disp']==0 && $_GET['action']!='new') || ($rright['ticket_new_resolution_disp']==0 && $_GET['action']=='new')) echo 'hide';?>" >
							<label class="col-sm-2 control-label no-padding-right" for="resolution"><?php echo T_('Résolution'); ?>:</label>
							<div class="col-sm-8">	
							<?php include "./thread.php";?>	
							</div>
						</div>
						<a id="down"></a>
						<!-- END resolution part -->
						<!-- START attachement part -->
						<?php
						if ($rright['ticket_attachment']!=0)
						{
							echo '
							<div class="form-group">
								<label class="col-sm-2 control-label no-padding-right" for="attachment">'.T_('Fichier joint').':</label>
									<div class="col-sm-8">
										<table border="1" style="border: 1px solid #D8D8D8;" cellpadding="10" >
										<tr>
											<td>';
										include "./attachement.php";
										echo '
										</td>
										</tr>
									</table>
									</div>
							</div>';
						}
						?>
						<!-- END attachement part -->
						<!-- START create date part -->
						<?php
						//datetime convert SQL format to display
						if ($globalrow['date_create'])
						{
							$globalrow['date_create'] = DateTime::createFromFormat('Y-m-d H:i:s', $globalrow['date_create']);
							$globalrow['date_create']=$globalrow['date_create']->format('d/m/Y H:i:s');
						}
						?>
						<div class="form-group  <?php if($rright['ticket_date_create_disp']==0) echo 'hide';?>">
							<label class="col-sm-2 control-label no-padding-right" for="date_create"><?php echo T_('Date de la demande'); ?>:</label>
							<div class="col-sm-8">
								<input type="hidden" name="hide" id="hide" value="1"/>
								<input type="text" name="date_create" id="date_create" value="<?php if ($_POST['date_create']) echo $_POST['date_create']; else echo $globalrow['date_create']; ?>" <?php if($rright['ticket_date_create']==0) echo 'readonly="readonly"';?> >
							</div> 
						</div>
						<!-- END create date part -->
						
						<!-- END hope date part -->
						<!-- START resolution date part -->
						<?php
						//datetime convert SQL format to display
						if ($globalrow['date_res']=='0000-00-00 00:00:00')
						{
							$globalrow['date_res']='';
						} elseif ($globalrow['date_res'])
						{
							$globalrow['date_res'] = DateTime::createFromFormat('Y-m-d H:i:s', $globalrow['date_res']);
							$globalrow['date_res']=$globalrow['date_res']->format('d/m/Y H:i:s');
						}
						?>
						<div class="form-group <?php if($rright['ticket_date_res_disp']==0) echo 'hide';?>">
							<label class="col-sm-2 control-label no-padding-right" for=""><?php echo T_('Date de résolution'); ?>:</label>
							<div class="col-sm-8">
								<input  type="text" id="date_res" name="date_res"  value="<?php  if ($_POST['date_res']) echo $_POST['date_res']; else echo $globalrow['date_res']; ?>" <?php if($rright['ticket_date_res']==0) echo 'readonly="readonly"';?>>
							</div>
						</div>
						<!-- END resolution date part -->

						<!-- START time part -->
						<div class="form-group <?php if($rright['ticket_time_disp']==0) echo 'hide';?>">
							<label class="col-sm-2 control-label no-padding-right" for="time"><?php echo T_('Temps passé'); ?>:</label>
							<div class="col-sm-8">
								<select  id="time" name="time" <?php if($rright['ticket_time']==0) echo 'disabled';?> >
								<?php
									$query = $db->query("SELECT * FROM `ttime` ORDER BY min ASC");
									while ($row = $query->fetch()) 
									{
										if (($_POST['time']==$row['min'])||($globalrow['time']==$row['min']))
										{
											echo '<option selected value="'.$row['min'].'">'.$row['name'].'</option>';
											$selected_time=$row['min'];
										} else {
											echo '<option value="'.$row['min'].'">'.$row['name'].'</option>';
										}
									}
								?>
								</select>
								<?php
								//send value in lock select case 
								if($rright['ticket_time']==0) {echo '<input type="hidden" name="time" value="'.$selected_time.'" />';}
								?>
							</div>
						</div>
						
									
						<!-- END time part -->
						
						<!-- START priority part -->
						
						<!-- END priority part -->
						<!-- START criticality part -->
						<?php if($rright['ticket_criticality_mandatory']!=0) {if(($_POST['criticality']=="" && $_GET['action']=='new') || ($globalrow['criticality']=="" && $_GET['action']!='new') || ($globalrow['criticality']=="0")) {$criticality_error="has-error";} else {$criticality_error="";}}  else {$criticality_error="";} ?>
						<div class="form-group <?php echo $criticality_error; if($rright['ticket_criticality_disp']==0) echo 'hide';?>">
							<label  class="col-sm-2 control-label no-padding-right" for="criticality" >
							    <?php if($rright['ticket_criticality_mandatory']!=0) { if (($_POST['criticality']==0) && ($globalrow['criticality']==0)) {echo '<i title="La sélection d\'une criticité est obligatoire." class="icon-warning-sign red bigger-130"></i>&nbsp;';}} ?>
						    	<?php echo T_('Criticité'); ?>:
							</label>
							<div class="col-sm-8">
								<select  id="criticality" name="criticality" <?php if($rparameters['availability']==1) {echo 'onchange="loadVal(); submit();"';}  if($rright['ticket_criticality']==0) {echo 'disabled';}?>>
									<?php
									if ($_POST['criticality'])
									{
										//find row to select
										$query = $db->query("SELECT * FROM `tcriticality` WHERE id='$_POST[criticality]' ORDER BY number");
										$row=$query->fetch();
										$query->closeCursor(); 
										echo '<option value="'.$_POST['criticality'].'" selected >'.T_($row['name']).'</option>';
										//display all entries whitout selected
										$selected_criticality=$_POST['criticality'];
										$query = $db->query("SELECT * FROM `tcriticality` WHERE id!='$_POST[criticality]' ORDER BY number");
										while ($row = $query->fetch()) echo '<option value="'.$row['id'].'">'.T_($row['name']).'</option>'; 
										$query->closeCursor(); 
									}
									else
									{
										//find row to select
										$query = $db->query("SELECT * FROM `tcriticality` WHERE id='$globalrow[criticality]' ORDER BY number");
										$row=$query->fetch();
										$query->closeCursor(); 
										echo '<option value="'.$globalrow['criticality'].'" selected >'.T_($row['name']).'</option>';
										$selected_criticality=$globalrow['criticality'];
										//display all entries whitout selected
										$query = $db->query("SELECT * FROM `tcriticality` WHERE id!='$globalrow[criticality]' ORDER BY number");
										while ($row = $query->fetch()) echo '<option value="'.$row['id'].'">'.T_($row['name']).'</option>'; 
										$query->closeCursor(); 
									}			
									?>			
								</select>
								<?php
								//send value in lock select case 
								if($rright['ticket_criticality']==0) {echo '<input type="hidden" name="criticality" value="'.$selected_criticality.'" />';}
								
								//display criticality icon
								if($_POST['criticality']) {$check_id=$_POST['criticality'];} else { $check_id=$globalrow['criticality'];}
								$query = $db->query("SELECT * FROM `tcriticality` WHERE id='$check_id'");
								$row=$query->fetch();
								$query->closeCursor(); 
								if ($row['name']) {echo '<i title="'.T_($row['name']).'" class="icon-bullhorn bigger-130" style="color:'.$row['color'].'" ></i>';}
								?>
							</div>
						</div>
						<!-- START criticality part -->

						<!-- START state part -->
						<div class="form-group <?php if($rright['ticket_state_disp']==0) echo 'hide';?>">
							<label class="col-sm-2 control-label no-padding-right" for="state"><?php echo T_('État'); ?>:</label>
							<div class="col-sm-8">
								<select  id="state"  name="state" <?php if($rright['ticket_state']==0) echo 'disabled';?> >	
									<?php
									//selected value
									if ($_POST['state'])
									{
										$query = $db->query("SELECT * FROM `tstates` WHERE id='$_POST[state]'");
										$row=$query->fetch();
										$query->closeCursor(); 
										echo '<option value="'.$_POST['state'].'" selected >'.T_($row['name']).'</option>';
										$selected_state=$_POST['state'];
									}
									else
									{
										$query = $db->query("SELECT * FROM `tstates` WHERE id='$globalrow[state]'");
										$row=$query->fetch();
										$query->closeCursor(); 
										echo '<option value="'.$globalrow['state'].'" selected >'.T_($row['name']).'</option>';
										$selected_state=$globalrow['state'];
									}			
							    	$query = $db->query("SELECT * FROM `tstates` WHERE id!='$_POST[state]' AND id!='$globalrow[state]' ORDER BY number");
								    while ($row = $query->fetch()) echo '<option value="'.$row['id'].'">'.T_($row['name']).'</option>'; 
									$query->closeCursor(); 
									?>
								</select>
								<?php
								//send value in lock select case 
								if($rright['ticket_state']==0) {echo '<input type="hidden" name="state" value="'.$selected_state.'" />';}
								
								//display state icon
								$query = $db->query("SELECT * FROM `tstates` WHERE id LIKE '$globalrow[state]'");
								$row=$query->fetch();
								$query->closeCursor(); 
								echo '<span class="'.$row['display'].'" title="'.T_($row['description']).'">&nbsp;</span>';
								?>
							</div>
						</div>
						<!-- END state part -->
						<!-- START availability part --> 
						<?php
						//check if the availability parameter is on and condition parameter
						if($rparameters['availability']==1)
						{
						        if($rparameters['availability_condition_type']=='criticality' && ($globalrow['criticality']==$rparameters['availability_condition_value'] || $_POST['criticality']==$rparameters['availability_condition_value']))
						        {    
						        	//calc time
        					    	if ($globalrow['start_availability']!='0000-00-00 00:00:00' && $globalrow['end_availability']!='0000-00-00 00:00:00')
        					    	{
        					    	    $t1 =strtotime($globalrow['start_availability']) ;
                                        $t2 =strtotime($globalrow['end_availability']) ;
                                       	$time=(($t2-$t1)/60)/60;
                                       	$time="soit $time h";
        					    	} else $time='';
        					    	//explode selected date and hour
        					    	if ($_POST['start_availability_d'])
        					    	{
        					    	    $start_availability_d=$_POST['start_availability_d'];
        					    	    $start_availability_h=$_POST['start_availability_h'];
        					    	} elseif ($globalrow['start_availability']!='0000-00-00 00:00:00') 
        					    	{
        					    	    $start_availability_d=date("d/m/Y",strtotime($globalrow['start_availability']));
        					    	    $start_availability_h=date("G:i:s",strtotime($globalrow['start_availability']));
        					    	} else {
        					    	    $start_availability_d=date("d/m/Y");
        					    	    $start_availability_h=date("H:i:s");
        					    	}
        					    	
        					    	if ($_POST['end_availability_d'])
        					    	{
        					    	    $end_availability_d=$_POST['end_availability_d'];
        					    	    $end_availability_h=$_POST['end_availability_h'];
        					    	} else
        					    	if ($globalrow['start_availability']!='0000-00-00 00:00:00') {
        					    	    $end_availability_d=date("d/m/Y",strtotime($globalrow['end_availability']));
        					    	    $end_availability_h=date("G:i:s",strtotime($globalrow['end_availability']));
        					    	} else {
        					    	    $end_availability_d=date("d/m/Y");
        					    	    $end_availability_h=date("H:i:s");
        					    	}
        						    echo'
        						   	<div class="form-group '; if($rright['ticket_availability_disp']==0) echo 'hide'; echo '">
        						    	<label class="col-sm-2 control-label no-padding-right" for="start_availability_d">Début de l\'indisponibilité:</label>
        						    	<div class="col-sm-8">
            						    	<input  type="text" id="start_availability_d" name="start_availability_d"  value="'.$start_availability_d.'"';                							    	    echo '"';
                							    	    if($rright['ticket_availability']==0) echo ' readonly="readonly" ';
                							echo '
                							>
        						    	    <div class="bootstrap-timepicker">
									        	<input id="start_availability_h" name="start_availability_h" value="'.$start_availability_h.'" type="text"  />
							    	        </div>	
        						    	</div>
        					    	</div>
        					    	<div class="form-group '; if($rright['ticket_availability_disp']==0) echo 'hide'; echo '">
        						    	<label class="col-sm-2 control-label no-padding-right" for="end_availability_d">Fin de l\'indisponibilité:</label>
        						    	<div class="col-sm-8">
        							    	<input  type="text" id="end_availability_d" name="end_availability_d"  value="'.$end_availability_d.'"';
        							    	    if($rright['ticket_availability']==0) echo ' readonly="readonly" ';
        							    	echo '
        							    	>
        							        <div class="bootstrap-timepicker">
									        	<input id="end_availability_h" name="end_availability_h" value="'.$end_availability_h.'" type="text"  />
							                </div>
							                '.$time.'
							             </div>
        						    </div>
        					    	<div class="form-group '; if($rright['ticket_availability_disp']==0) echo 'hide'; echo '">
        					    		<label class="col-sm-2 control-label no-padding-right" for="availability_planned">Indisponibilité planifiée:</label>
        					    		<div class="col-sm-8">
        					    			<input type="checkbox"'; if ($globalrow['availability_planned']==1) {echo "checked";} echo ' name="availability_planned" value="1" />
        					    		</div>
        					    	</div>
        					    	';
						        }
						}
						?>
						<!-- END availability part -->
						<div class="form-actions center">
							<?php
							if (($rright['ticket_save']!=0 && $_GET['action']!='new') || ($rright['ticket_new_save']!=0 && $_GET['action']=='new'))
							{
								echo '
								<button name="modify" id="modify" value="modify" type="submit" class="btn btn-sm btn-success">
									<i class="icon-save icon-on-right bigger-110"></i> 
									&nbsp;'.T_('Enregistrer').'
								</button>
								&nbsp;
								';
							}
							if ($rright['ticket_save_close']!=0)
							{
								echo '
								<button name="quit" id="quit" value="quit" type="submit" class="btn btn-sm btn-purple">
									<i class="icon-save icon-on-right bigger-110"></i> 
									&nbsp;'.T_('Enregistrer et Fermer').'
								</button>
								&nbsp;
								';
							}
							if ($rright['ticket_new_send']!=0 && $_GET['action']=='new')
							{
								echo '
								<button name="send" id="send" value="send" type="submit" class="btn btn-sm btn-success">
									'.T_('Envoyer').'
									&nbsp;<i class="icon-arrow-right icon-on-right bigger-110"></i> 
								</button>
								&nbsp;
								';
							}
							if ($rright['ticket_close']!=0 && $_POST['state']!='3' && $globalrow['state']!='3' && $_GET['action']!='new')
							{
								echo '
								<button name="close" id="close" value="close" type="submit" class="btn btn-sm btn-purple">
									<i class="icon-ok icon-on-right bigger-110"></i> 
									&nbsp;'.T_('Clôturer le ticket').'
								</button>
								&nbsp;
								';
							}
							if ($rright['ticket_send_mail']!=0)
							{
								echo '
								<button name="mail" id="mail" value="mail" type="submit" class="btn btn-sm btn-primary">
									<i class="icon-envelope icon-on-right bigger-110"></i> 
									&nbsp;'.T_('Envoyer un mail').'
								</button>
								&nbsp;
								';
							}
							if ($rright['ticket_cancel']!=0)
							{
								echo '
								<button name="cancel" id="cancel" value="cancel" type="submit" class="btn btn-sm btn-danger">
									<i class="icon-remove icon-on-right bigger-110"></i> 
									&nbsp;'.T_('Annuler').'
								</button>
								';
							}
							?>
						</div>
					</div>
				</div> <!-- div widget body -->
			</form>
		</div> <!-- div end sm -->
	</div> <!-- div end x12 -->
</div> <!-- div end row -->

<?php include ('./wysiwyg.php'); ?>

<!-- date picker script -->
<script type="text/javascript">
	window.jQuery || document.write("<script src='assets/js/jquery-2.0.3.min.js'>"+"<"+"/script>");
</script>
<script src="template/assets/js/date-time/bootstrap-timepicker.min.js" charset="UTF-8"></script>
<script type="text/javascript">
jQuery(function($) {
    
    	$('#start_availability_h').timepicker({
    	        minuteStep: 1,
				showSeconds: true,
				showMeridian: false
			});
		$('#end_availability_h').timepicker({
	        minuteStep: 1,
			showSeconds: true,
			showMeridian: false
		});
		<?php
		echo '
			$.datepicker.setDefaults( $.datepicker.regional["fr"] );
			jQuery(function($){
			   $.datepicker.regional["fr"] = {
				  closeText: "Fermer",
				  prevText: "'.T_('<Préc').'",
				  nextText: "'.T_('Suiv>').'",
				  currentText: "Courant",
				  monthNames: ["'.T_('Janvier').'","'.T_('Février').'","'.T_('Mars').'","'.T_('Avril').'","'.T_('Mai').'","'.T_('Juin').'","'.T_('Juillet').'","'.T_('Août').'","'.T_('Septembre').'","'.T_('Octobre').'","'.T_('Novembre').'","'.T_('Décembre').'"],
				  monthNamesShort: ["Jan","Fév","Mar","Avr","Mai","Jun",
				  "Jul","Aoû","Sep","Oct","Nov","Déc"],
				  dayNames: ["Dimanche","Lundi","Mardi","Mercredi","Jeudi","Vendredi","Samedi"],
				  dayNamesShort: ["Dim","Lun","Mar","Mer","Jeu","Ven","Sam"],
				  dayNamesMin: ["'.T_('Di').'","'.T_('Lu').'","'.T_('Ma').'","'.T_('Me').'","'.T_('Je').'","'.T_('Ve').'","'.T_('Sa').'"],
				  weekHeader: "Sm",
				  dateFormat: "dd/mm/yy",
				  timeFormat:  "hh:mm:ss",
				  firstDay: 1,
				  isRTL: false,
				  showMonthAfterYear: false,
				  yearSuffix: ""};
			   $.datepicker.setDefaults($.datepicker.regional["fr"]);
				});
		';

			if($rright['ticket_date_create']!=0)
			{
				echo '
				$( "#date_create" ).datepicker({ 
				dateFormat: \'dd/mm/yy\',
					onSelect: function(datetext){
					var d = new Date(); // for now
					datetext=datetext+" "+"00:00:00";
					$(\'#date_create\').val(datetext);
					},
				});
				';
			}
			if($rright['ticket_date_res']!=0)
			{
				echo '
				$( "#date_res" ).datepicker({ 
					dateFormat: \'dd/mm/yy\',
					onSelect: function(datetext){
					var d = new Date(); // for now
					datetext=datetext+" "+(\'0\'+d.getHours()).slice(-2)+":"+(\'0\'+d.getMinutes()).slice(-2)+":"+(\'0\'+d.getSeconds()).slice(-2);  
					$(\'#date_res\').val(datetext);
					},
				}); 
				';
			}
			if($rright['ticket_date_hope']!=0)
			{
				echo '
				$( "#date_hope" ).datepicker({ 
					dateFormat: \'dd/mm/yy\'
				}); 
				';
			}
		?>
		$( "#start_availability_d" ).datepicker({ 
			dateFormat: 'dd/mm/yy'
		});
		$( "#end_availability_d" ).datepicker({ 
			dateFormat: 'dd/mm/yy'
		});
	});		
</script>		