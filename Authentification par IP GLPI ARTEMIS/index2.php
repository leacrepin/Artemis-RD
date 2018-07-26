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
*/

// Modified by Stevenes Donato
// stevenesdonato@gmail.com

// Check PHP version not to have trouble
// Need to be the very fist step before any include
if (version_compare(PHP_VERSION, '5.6') < 0) {
   die('PHP >= 5.6 required');
}

use Glpi\Event;

//Load GLPI constants
define('GLPI_ROOT', __DIR__);
include (GLPI_ROOT . "/inc/based_config.php");
include_once (GLPI_ROOT . "/inc/define.php");

// Check PHP version not to have trouble
if (version_compare(PHP_VERSION, GLPI_MIN_PHP) < 0) {
   die(sprintf("PHP >= %s required", GLPI_MIN_PHP));
}

define('DO_NOT_CHECK_HTTP_REFERER', 1);

// If config_db doesn't exist -> start installation
if (!file_exists(GLPI_CONFIG_DIR . "/config_db.php")) {
   include_once (GLPI_ROOT . "/inc/autoload.function.php");
   Html::redirect("install/install.php");
   die();

} else {
   $TRY_OLD_CONFIG_FIRST = true;
   include (GLPI_ROOT . "/inc/includes.php");
   $_SESSION["glpicookietest"] = 'testcookie';

   // For compatibility reason
   if (isset($_GET["noCAS"])) {
      $_GET["noAUTO"] = $_GET["noCAS"];
   }

   if (!isset($_GET["noAUTO"])) {
      Auth::redirectIfAuthenticated();
   }

   Auth::checkAlternateAuthSystems(true, isset($_GET["redirect"])?$_GET["redirect"]:"");
   
   $_SESSION['namfield'] = $namfield = uniqid('fielda');
   $_SESSION['pwdfield'] = $pwdfield = uniqid('fieldb');
   $_SESSION['rmbfield'] = $rmbfield = uniqid('fieldc');
   
  
}

header('Location: front/loginIP.php');
exit();
