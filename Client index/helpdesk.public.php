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

include ('../inc/includes.php');

// Change profile system
if (isset($_POST['newprofile'])) {
   if (isset($_SESSION["glpiprofiles"][$_POST['newprofile']])) {
      Session::changeProfile($_POST['newprofile']);

      if ($_SESSION["glpiactiveprofile"]["interface"] == "central") {
         Html::redirect($CFG_GLPI['root_doc']."/front/central.php");
      } else {
         Html::redirect($_SERVER['PHP_SELF']);
      }

   } else {
      Html::redirect(preg_replace("/entities_id=.*/", "", $_SERVER['HTTP_REFERER']));
   }
}

// Manage entity change
if (isset($_GET["active_entity"])) {
   $_GET["active_entity"] = rtrim($_GET["active_entity"], 'r');
   if (!isset($_GET["is_recursive"])) {
      $_GET["is_recursive"] = 0;
   }
   if (Session::changeActiveEntities($_GET["active_entity"], $_GET["is_recursive"])) {
      if ($_GET["active_entity"] == $_SESSION["glpiactive_entity"]) {
         Html::redirect(preg_replace("/(\?|&|".urlencode('?')."|".urlencode('&').")?(entities_id|active_entity).*/", "", $_SERVER['HTTP_REFERER']));
      }
   }
}

// Redirect management
if (isset($_GET["redirect"])) {
   Toolbox::manageRedirect($_GET["redirect"]);
}

// redirect if no create ticket right
if (!Session::haveRight('ticket', CREATE)
    && !Session::haveRight('reminder_public', READ)
    && !Session::haveRight("rssfeed_public", READ)) {

   if (Session::haveRight('followup', TicketFollowup::SEEPUBLIC)
       || Session::haveRight('task', TicketTask::SEEPUBLIC)
       || Session::haveRightsOr('ticketvalidation', [TicketValidation::VALIDATEREQUEST,
                                                          TicketValidation::VALIDATEINCIDENT])) {
      Html::redirect($CFG_GLPI['root_doc']."/front/ticket.php");

   } else if (Session::haveRight('reservation', ReservationItem::RESERVEANITEM)) {
      Html::redirect($CFG_GLPI['root_doc']."/front/reservationitem.php");

   } else if (Session::haveRight('knowbase', KnowbaseItem::READFAQ)) {
      Html::redirect($CFG_GLPI['root_doc']."/front/helpdesk.faq.php");
   }
}

Session::checkHelpdeskAccess();


if (isset($_GET['create_ticket'])) {
   Html::helpHeader(__('New ticket'), $_SERVER['PHP_SELF'], $_SESSION["glpiname"]);
   $ticket = new Ticket();
   $ticket->showFormHelpdesk(Session::getLoginUserID());

} else {
   Html::helpHeader(__('Home'), $_SERVER['PHP_SELF'], $_SESSION["glpiname"]);
   echo "<table class='tab_cadre_postonly'><tr class='noHover'>";
   echo "<td class='top' width='50%'><br>";
   echo "<table class='central'>";



      echo "<tr class='noHover' ><td class='top' width='50%'>";
      echo '<br><table class="central"><tbody>
      <table class="tab_cadrehov homepage_forms_container" id="homepage_forms_container"><tbody>
      <tr class="noHover"></tr><tr class="noHover"></tr><tr class="line1 tab_bg_2"><td><img src="/glpi/pics/plus.png" alt="+" title="" onclick="showDescription(1, this)" align="absmiddle" style="cursor: pointer">&nbsp;<a href="/artemis/glpi/plugins/formcreator/front/formdisplay.php?id=1" title="Incident">Déclarer un incident</a></td></tr><tr id="desc1" class="line1 form_description"><td><div>Incident&nbsp;</div></td></tr></tbody></table><br><script type="text/javascript">
      function showDescription(id, img){
         if(img.alt == "+") {
           img.alt = "-";
           img.src = "/glpi/pics/moins.png";
           document.getElementById("desc" + id).style.display = "table-row";
         } else {
           img.alt = "+";
           img.src = "/glpi/pics/plus.png";
           document.getElementById("desc" + id).style.display = "none";
         }
      }
   </script><tr class="noHover"><td class="top"></td></tr></tbody></table>';
      echo "</td></tr>";


   echo "</table></td>";

   echo "<td class='top' width='50%'><br>";
   echo "</td>";
   echo "</tr></table>";

}

Html::helpFooter();

