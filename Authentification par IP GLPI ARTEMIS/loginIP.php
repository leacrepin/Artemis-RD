<?php
/**
 * ---------------------------------------------------------------------
 * GLPI - Gestionnaire Libre de Parc Informatique
 * Copyright (C) 2015-2017 Teclib' and contributors.
 *
 * http://glpi-project.org
 *
 * based on GLPI - Gestionnaire Libre de Parc Informatique
 * Copyright (C) 2003-2014 by the INDEPNET Development Team.
 *
 * ---------------------------------------------------------------------
 *
 * LICENSE
 *
 * This file is part of GLPI.
 *
 * GLPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GLPI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GLPI. If not, see <http://www.gnu.org/licenses/>.
 * ---------------------------------------------------------------------
 */

/** @file
* @brief
* @since version 0.85 in front
*/
include ('../inc/includes.php');


if (!isset($_SESSION["glpicookietest"]) || ($_SESSION["glpicookietest"] != 'testcookie')) {
   if (!is_writable(GLPI_SESSION_DIR)) {
      Html::redirect($CFG_GLPI['root_doc'] . "/index.php?error=2");
   } else {
      Html::redirect($CFG_GLPI['root_doc'] . "/index.php?error=1");
   }
}

//Authentication par IP

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
global $DB;
$query=$DB->query("SELECT user_name,IP_WAN FROM glpi_ip WHERE IP_WAN='".get_ip()."'") or die('erro');
$ip=$query->fetch_assoc();

if($ip!=false){
	$_SESSION["loginautoclient"]=true;
	$login = $ip["user_name"];
	$password = 'null';	
}else{
	$_SESSION["loginautoclient"]=false;
	$login = '';
	$password = '';	
}




$remember = isset($_SESSION['rmbfield']) && isset($_POST[$_SESSION['rmbfield']]) && $CFG_GLPI["login_remember_time"];

// Redirect management
$REDIRECT = "";
if (isset($_POST['redirect']) && (strlen($_POST['redirect']) > 0)) {
   $REDIRECT = "?redirect=" .rawurlencode($_POST['redirect']);

} else if (isset($_GET['redirect']) && strlen($_GET['redirect'])>0) {
   $REDIRECT = "?redirect=" .rawurlencode($_GET['redirect']);
}

$auth = new Auth();


// now we can continue with the process...
if ($auth->login($login, $password, (isset($_REQUEST["noAUTO"])?$_REQUEST["noAUTO"]:false), $remember)) {
   Auth::redirectIfAuthenticated();
} else {
   // we have done at least a good login? No, we exit.
   Html::nullHeader("Login", $CFG_GLPI["root_doc"] . '/index.php');
   echo '<div class="center b"><span style="font-size:15px;">' . $auth->getErr() . '</span>';
   // Logout whit noAUto to manage auto_login with errors
   echo '</div><br> <div class="center"><a style="font-size:16px;" class="vsubmit" href="' . $CFG_GLPI["root_doc"] . '/front/logout.php?noAUTO=1'.
         str_replace("?", "&", $REDIRECT).'">' .__('Log in again') . '</a></div>';
   Html::nullFooter();
   exit();
}
