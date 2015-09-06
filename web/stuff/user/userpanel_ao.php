<?php

/**
 * File: userpanel_ao.php.
 * Author: Ulrich Block
 * Contact: <ulrich.block@easy-wi.com>
 *
 * This file is part of Easy-WI.
 *
 * Easy-WI is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Easy-WI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Easy-WI.  If not, see <http://www.gnu.org/licenses/>.
 *
 * Diese Datei ist Teil von Easy-WI.
 *
 * Easy-WI ist Freie Software: Sie koennen es unter den Bedingungen
 * der GNU General Public License, wie von der Free Software Foundation,
 * Version 3 der Lizenz oder (nach Ihrer Wahl) jeder spaeteren
 * veroeffentlichten Version, weiterverbreiten und/oder modifizieren.
 *
 * Easy-WI wird in der Hoffnung, dass es nuetzlich sein wird, aber
 * OHNE JEDE GEWAEHELEISTUNG, bereitgestellt; sogar ohne die implizite
 * Gewaehrleistung der MARKTFAEHIGKEIT oder EIGNUNG FUER EINEN BESTIMMTEN ZWECK.
 * Siehe die GNU General Public License fuer weitere Details.
 *
 * Sie sollten eine Kopie der GNU General Public License zusammen mit diesem
 * Programm erhalten haben. Wenn nicht, siehe <http://www.gnu.org/licenses/>.
 */

include(EASYWIDIR . '/stuff/keyphrasefile.php');
include(EASYWIDIR . '/stuff/methods/class_ftp.php');
include(EASYWIDIR . '/stuff/methods/functions_ssh_exec.php');
include(EASYWIDIR . '/stuff/methods/class_app.php');

if ((!isset($user_id) or $main != 1) or (isset($user_id) and !$pa['useraddons'])) {
	header('Location: userpanel.php');
	die('No access');
}

$sprache = getlanguagefile('images', $user_language, $resellerLockupID);
$loguserid = $user_id;
$logusername = getusername($user_id);
$logusertype = 'user';
$logreseller = 0;

if (isset($admin_id)) {
	$logsubuser = $admin_id;
} else if (isset($subuser_id)) {
	$logsubuser = $subuser_id;
} else {
	$logsubuser = 0;
}

if (isset($admin_id)) {
	$logsubuser = $admin_id;
} else if (isset($subuser_id)) {
	$logsubuser = $subuser_id;
} else {
	$logsubuser = 0;
}

if ($ui->id('id', 10, 'get') and $ui->id('adid', 10, 'get') and in_array($ui->st('action', 'get'), array('ad','dl')) and (!isset($_SESSION['sID']) or in_array($ui->id('id', 10, 'get'), $substituteAccess['gs']))) {

    $id = (int) $ui->id('id', 10, 'get');
    $addonID = (int) $ui->id('adid', 10, 'get');

    $query = $sql->prepare("SELECT `rootID` FROM `gsswitch` WHERE `id`=? AND `userid`=? AND `resellerid`=? LIMIT 1");
    $query->execute(array($id, $user_id, $resellerLockupID));

    $appServer = new AppServer($query->fetchColumn());

    $appServer->getAppServerDetails($id);

    // Will be false in case no fitting data could be fetched before
    if ($appServer->appServerDetails) {


        $query2 = $sql->prepare("INSERT INTO `addons_installed` (`userid`,`addonid`,`serverid`,`servertemplate`,`paddon`,`resellerid`) VALUES (?,?,?,?,?,?)");

        $query = $sql->prepare("SELECT `addon`,`paddon` FROM `addons` WHERE `id`=? AND `resellerid`=? AND `active`='Y' LIMIT 1");
        $query->execute(array($addonID, $resellerLockupID));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            $protectedAllowed = $row['paddon'];
            $addonName = $row['addon'];

            if ($ui->st('action', 'get') == 'ad') {

                // Check if the addon is allowed for installing

                if ($appServer->appServerDetails['protectionModeStarted'] == 'N' or ($appServer->appServerDetails['protectionModeStarted'] == 'Y' and $protectedAllowed =='Y')) {

                    $query2->execute(array($user_id, $addonID, $appServer->appServerDetails['app']['id'], $appServer->appServerDetails['app']['servertemplate'], $appServer->appServerDetails['protectionModeStarted'], $resellerLockupID));

                    $template_file = $sprache->addon_inst;
                    $actionstatus = 'ok';

                    // Reload addon details in order to have the newly inserted available as well
                    $appServer->getAddonDetails();
                    $appServer->addAddon($addonID);

                    // This case becomes true in case the user tries to manipulate the URL
                } else {
                    $template_file = $sprache->failed;
                    $actionstatus = 'fail';
                }


            } else {

                // This will load all details for all installed addons into the object
                $appServer->getAddonDetails();

                // Remove the selected addon
                $appServer->removeAddon($addonID);

                $template_file = $sprache->addon_del;
                $actionstatus = 'ok';

            }
        }

        if (isset($protectedAllowed, $addonName)) {

            $appServer->execute();

            $loguseraction = "%{$ui->st('action', 'get')}% %addon% {$addonName} {$appServer->appServerDetails['serverIP']}:{$appServer->appServerDetails['port']} %{$actionstatus}%";
            $insertlog->execute();

            if (isset($dbConnect['debug']) and $dbConnect['debug'] == 1) {
                $template_file .= '<br><pre>' . implode("\r\n", $appServer->debug()) . '</pre>';
            }

        } else {
            $template_file = $sprache->failed;
        }

    } else {
        $template_file = $sprache->failed;
    }

} else if ($ui->id('id', 10, 'get') and (!isset($_SESSION['sID']) or in_array($ui->id('id', 10, 'get'), $substituteAccess['gs']))) {

    $table = array();
    $i = 0;
	$username = getusername($user_id);
    $switchID = $ui->id('id', 10, 'get');

	$query = $sql->prepare("SELECT g.`serverid`,g.`serverip`,g.`port`,g.`protected`,g.`queryName`,s.`servertemplate`,t.`shorten`,t.`id` AS `servertype_id` FROM `gsswitch` g INNER JOIN `serverlist` s ON g.`serverid`=s.`id` INNER JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE g.`userid`=? AND g.`id`=? AND g.`resellerid`=? LIMIT 1");
    $query->execute(array($user_id, $switchID, $resellerLockupID));
	while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

        $table2 = array();
        $table3 = array();

		$description = '';

        $serverip = $row['serverip'];
		$serverport = $row['port'];
		$serverid = $row['serverid'];
        $servershorten = $row['shorten'];
		$servertemplate = $row['servertemplate'];
		$protected = $row['protected'];
        $description = $row['queryName'];

        $currentTemplate = ($servertemplate > 1) ? $servershorten . '-' . $servertemplate : $servershorten;

		$query2 = ($protected == 'Y') ? $sql->prepare("SELECT a.`addon_id`,t.`menudescription`,t.`depending`,t.`type` FROM `addons_allowed` AS a INNER JOIN `addons` t ON a.`addon_id`=t.`id` AND a.`reseller_id`=t.`resellerid` WHERE t.`active`='Y' AND t.`paddon`='Y' AND a.`servertype_id`=? AND a.`reseller_id`=? ORDER BY t.`depending`,t.`menudescription`") : $sql->prepare("SELECT a.`addon_id`,t.`menudescription`,t.`depending`,t.`type` FROM `addons_allowed` AS a INNER JOIN `addons` t ON a.`addon_id`=t.`id` AND a.`reseller_id`=t.`resellerid` WHERE t.`active`='Y' AND a.`servertype_id`=? AND a.`reseller_id`=? ORDER BY t.`depending`,t.`menudescription`");
        $query2->execute(array($row['servertype_id'], $resellerLockupID));
		while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {

            $imgAlt = '';
            $descriptionrow = '';
            $lang = '';
            $delete = '';

			$adid = $row2['addon_id'];
			$depending = $row2['depending'];
			$menudescription = $row2['menudescription'];

			$query3 = $sql->prepare("SELECT `text` FROM `translations` WHERE `type`='ad' AND `transID`=? AND `lang`=? AND `resellerID`=? LIMIT 1");
            $query3->execute(array($adid, $user_language, $resellerLockupID));
            $descriptionrow = $query3->fetchColumn();

			if (empty($descriptionrow)) {
                $query3 = $sql->prepare("SELECT `text` FROM `translations` WHERE `type`='ad' AND `transID`=? AND `lang`=? AND `resellerID`=? LIMIT 1");
                $query3->execute(array($adid, $rSA['language'], $resellerLockupID));
                $descriptionrow = $query->fetchColumn();
			}

            $addescription = nl2br($descriptionrow);

            $query3 =  ($protected == 'Y') ? $sql->prepare("SELECT `id` FROM `addons_installed` WHERE `userid`=? AND `serverid`=? AND `addonid`=? AND `servertemplate`=? AND `paddon`='Y' AND `resellerid`=? LIMIT 1") : $sql->prepare("SELECT `id` FROM `addons_installed` WHERE `userid`=? AND `serverid`=? AND `addonid`=? AND `servertemplate`=? AND `resellerid`=? LIMIT 1");
            $query3->execute(array($user_id, $serverid, $adid, $servertemplate, $resellerLockupID));
            $installedid = $query3->fetchColumn();

            if (isid($installedid, 10)){

                $action = 'dl';
                $delete = '&amp;rid=' . $installedid;

            } else {

                $query3 = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `addons_installed` WHERE `userid`=? AND `serverid`=? AND `servertemplate`=? AND `addonid`=? AND `resellerid`=? LIMIT 1");
                $query3->execute(array($user_id, $serverid, $servertemplate, $depending, $resellerLockupID));
                $colcount = $query3->fetchColumn();

                if ($row2['type'] == 'map' or $depending == 0 or ($depending > 0 and $colcount > 0)) {
                    $action = 'ad';
                } else {

                    $action = 'none';

                    $query3 = $sql->prepare("SELECT `menudescription` FROM `addons` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                    $query3->execute(array($depending, $resellerLockupID));
                    $imgAlt = $sprache->requires. ': ' .$query3->fetchColumn();
                }

            }

            $link = ($action != 'none') ? 'userpanel.php?w=ao&amp;id=' . $switchID . '&amp;adid=' . $adid . '&amp;action=' . $action . $delete . '&amp;r=gs' : '#';

            if ($row2['type'] == 'tool') {
                $table2[] = array('adid' => $adid, 'menudescription' => $menudescription, 'addescription' => $addescription, 'installedid' => $installedid, 'alt' => $imgAlt, 'link' => $link, 'action' => $action);
            } else if ($row2['type'] == 'map') {
                $table3[] = array('adid' => $adid, 'menudescription' => $menudescription, 'addescription' => $addescription, 'installedid' => $installedid, 'alt' => $imgAlt, 'link' => $link, 'action' => $action);
            }

		}

		$table = array('id' => $switchID, 'serverip' => $serverip, 'port' => $serverport, 'tools' => $table2, 'maps' => $table3, 'name' => $description);

        unset($table2, $table3);
	}

	$template_file = 'userpanel_gserver_addon.tpl';

} else {
    $template_file = 'userpanel_404.tpl';
}
