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

include(EASYWIDIR . '/stuff/methods/functions_ssh_exec.php');
include(EASYWIDIR . '/stuff/keyphrasefile.php');

if ((!isset($user_id) or $main != 1) or (isset($user_id) and !$pa['useraddons'])) {
	header('Location: userpanel.php');
	die('No acces');
}

$sprache = getlanguagefile('images', $user_language, $reseller_id);
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

if (isset($admin_id) and $reseller_id != 0 and $admin_id != $reseller_id) {
	$reseller_id = $admin_id;
}

if (isset($admin_id)) {
	$logsubuser = $admin_id;
} else if (isset($subuser_id)) {
	$logsubuser = $subuser_id;
} else {
	$logsubuser = 0;
}

if ($ui->id('id', 10, 'get') and $ui->id('adid', 10, 'get') and in_array($ui->st('action', 'get'), array('ad','dl')) and (!isset($_SESSION['sID']) or in_array($ui->id('id', 10, 'get'), $substituteAccess['gs']))) {

    $gameserverid = (int) $ui->id('id',19, 'get');
    $addonid = $ui->id('adid',10, 'get');
    $action = $ui->smallletters('action',2, 'get');

    $query = $sql->prepare("SELECT r.`install_paths`,g.`rootID`,g.`newlayout`,g.`serverid`,g.`serverip`,g.`port`,g.`protected`,g.`homeLabel`,AES_DECRYPT(g.`ftppassword`,?) AS `dftpppassword`,AES_DECRYPT(g.`ppassword`,?) AS `decryptedppassword`, t.`modfolder`,t.`shorten`,s.`servertemplate`,u.`cname` FROM `gsswitch` g INNER JOIN `serverlist` s ON g.`serverid`=s.`id` INNER JOIN `servertypes` t ON s.`servertype`=t.`id` INNER JOIN `userdata` u ON g.`userid`=u.`id` INNER JOIN `rserverdata` r ON r.`id`=g.`rootID` WHERE g.`id`=? AND g.`userid`=? AND g.`resellerid`=? LIMIT 1");
    $query->execute(array($aeskey, $aeskey, $gameserverid, $user_id, $reseller_id));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $protected = $row['protected'];
        $rootID = $row['rootID'];
        $serverip = $row['serverip'];
        $serverid = $row['serverid'];
        $port = $row['port'];
        $modfolder = (strlen($row['modfolder'])> 0) ? $row['modfolder'] : 'none';
        $ppassword = $row['decryptedppassword'];
        $ftppass = $row['dftpppassword'];
        $servertemplate = $row['servertemplate'];
        $newlayout = $row['newlayout'];
        $customer = ($newlayout == 'Y') ? $row['cname'] . '-' . $gameserverid : $row['cname'];
        $shorten = ($servertemplate == 1) ? $row['shorten'] : $row['shorten'] . '-' . $servertemplate;

        $iniVars = parse_ini_string($row['install_paths'], true);
        $homeDir = ($iniVars and isset($iniVars[$row['homeLabel']]['path'])) ? $iniVars[$row['homeLabel']]['path'] : '/home';
    }

    if (isset($rootID)) {

        $query = $sql->prepare("SELECT `addon`,`paddon`,`type`,`folder` FROM `addons` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($addonid, $reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $addon = $row['addon'];
            $paddon = $row['paddon'];
            $type = $row['type'];
            $folder = (strlen($row['folder'])> 0) ? $row['folder'] : 'none';
        }

        if (isset($protected) and $protected == 'N') {
            $serverfolder = $customer . '/server/' . $serverip . '_' . $port . '/' . $shorten;
        } else {
            $serverfolder = $customer . '/pserver/' . $serverip . '_' . $port . '/' . $shorten;
            $ftppass = $ppassword;
            $customer .= '-p';
        }

        $serverfolder = str_replace('//', '/', $serverfolder);

        if ($ui->st('action', 'get') == 'ad' and isset($modfolder) and ($protected == 'N' or ($protected == 'Y' and $paddon == 'Y'))) {

            $cmd = "sudo -u {$customer} ./control.sh addaddon {$type} {$addon} \"{$serverfolder}\" \"{$modfolder}\" \"{$homeDir}\"";

            if (ssh2_execute('gs', $rootID, $cmd) !== false) {

                $query = $sql->prepare("INSERT INTO `addons_installed` (`userid`,`addonid`,`serverid`,`servertemplate`,`paddon`,`resellerid`) VALUES (?,?,?,?,?,?)");
                $query->execute(array($user_id, $addonid, $serverid, $servertemplate, $protected, $reseller_id));

                $template_file = $sprache->addon_inst;
                $actionstatus = 'ok';

            } else {
                $template_file = $sprache->failed;
                $actionstatus = 'fail';
            }

            if (isset($dbConnect['debug']) and $dbConnect['debug'] == 1) {
                $template_file .= '<pre>' . $cmd . '</pre>';
            }

        } else if ($ui->st('action', 'get') == 'dl' and $ui->id('rid', 10, 'get')) {

            $cmds = array();

            $installedid = $ui->id('rid', 10, 'get');
            $delids = $addonid;

            $serverfolder = str_replace('//', '/', $homeDir . '/' . $serverfolder);

            $cmds[] = "sudo -u $customer ./control.sh deladdon {$type} {$addon} \"{$serverfolder}\" \"{$modfolder}\" \"{$folder}\"";

            $query = $sql->prepare("SELECT a.`id`,a.`folder`,a.`addon` FROM `addons` AS a INNER JOIN `addons_installed` AS i ON i.`addonid`=a.`id` AND i.`serverid`=? AND i.`servertemplate`=? WHERE a.`depending`=? AND a.`resellerid`=? LIMIT 1");

            while (isset($delids) and isset($installedid)) {

                $query->execute(array($serverid, $servertemplate, $delids, $reseller_id));

                if (isset($installedid)) {

                    $query2 = $sql->prepare("DELETE FROM `addons_installed` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                    $query2->execute(array($installedid, $reseller_id));

                    unset($installedid);

                    if (isset($deladdon)) {
                        $cmds[] = "sudo -u {$customer} ./control.sh deladdon {$type} {$deladdon} \"{$serverfolder}\" \"{$modfolder}\" \"{$delfolder}\"";

                        unset($deladdon);
                        unset($delfolder);
                    }
                }

                unset($delids);

                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

                    $delids = $row['id'];
                    $delfolder = (strlen($row['folder'])> 0) ? $row['folder'] : 'none';
                    $deladdon = $row['addon'];

                    $query2 = $sql->prepare("SELECT `id` FROM `addons_installed` WHERE `addonid`=? AND `serverid`=? AND `servertemplate`=? AND `resellerid`=? LIMIT 1");
                    $query2->execute(array($delids, $serverid, $servertemplate, $reseller_id));
                    $installedid = $query2->fetchColumn();
                }
            }

            if (ssh2_execute('gs', $rootID, $cmds) !== false){
                $template_file = $sprache->addon_del;
                $actionstatus = 'ok';
            } else {
                $template_file = $sprache->failed;
                $actionstatus = 'fail';
            }

            if (isset($dbConnect['debug']) and $dbConnect['debug'] == 1) {
                $template_file .= '<pre>' . implode("\r\n", $cmds) . '</pre>';
            }
        }

        if (isset($actionstatus) and ($protected == 'N' or ($protected == 'Y' and $paddon == 'Y'))) {

            $loguseraction = "%{$action}% %addon% {$addon} {$serverip}:{$port} %{$actionstatus}%";
            $insertlog->execute();

        } else {
            $template_file = $sprache->failed;
        }

    } else {
        $template_file = $sprache->failed;
    }

} else if ($ui->id('id',19, 'get') and (!isset($_SESSION['sID']) or in_array($ui->id('id', 10, 'get'), $substituteAccess['gs']))) {

    $table = array();
    $i = 0;
	$username = getusername($user_id);
    $switchID = $ui->id('id', 10, 'get');

	$query = $sql->prepare("SELECT g.`serverid`,g.`serverip`,g.`port`,g.`protected`,g.`queryName`,s.`servertemplate`,t.`shorten`,t.`id` AS `servertype_id` FROM `gsswitch` g INNER JOIN `serverlist` s ON g.`serverid`=s.`id` INNER JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE g.`userid`=? AND g.`id`=? AND g.`resellerid`=? LIMIT 1");
    $query->execute(array($user_id, $switchID, $reseller_id));
	foreach($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
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

		$query2 = ($protected== 'Y') ? $sql->prepare("SELECT a.`addon_id`,t.`menudescription`,t.`depending`,t.`type` FROM `addons_allowed` AS a INNER JOIN `addons` t ON a.`addon_id`=t.`id` AND a.`reseller_id`=t.`resellerid` WHERE t.`active`='Y' AND t.`paddon`='Y' AND a.`servertype_id`=? AND a.`reseller_id`=? ORDER BY t.`depending`,t.`menudescription`") : $sql->prepare("SELECT a.`addon_id`,t.`menudescription`,t.`depending`,t.`type` FROM `addons_allowed` AS a INNER JOIN `addons` t ON a.`addon_id`=t.`id` AND a.`reseller_id`=t.`resellerid` WHERE t.`active`='Y' AND a.`servertype_id`=? AND a.`reseller_id`=? ORDER BY t.`depending`,t.`menudescription`");
        $query2->execute(array($row['servertype_id'], $reseller_id));
		foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {

            $imgAlt = '';
            $descriptionrow = '';
            $lang = '';
            $delete = '';

			$adid = $row2['addon_id'];
			$depending = $row2['depending'];
			$menudescription = $row2['menudescription'];

			$query3 = $sql->prepare("SELECT `text` FROM `translations` WHERE `type`='ad' AND `transID`=? AND `lang`=? AND `resellerID`=? LIMIT 1");
            $query3->execute(array($adid, $user_language, $reseller_id));
            $descriptionrow = $query3->fetchColumn();

			if (empty($descriptionrow)) {
                $query3 = $sql->prepare("SELECT `text` FROM `translations` WHERE `type`='ad' AND `transID`=? AND `lang`=? AND `resellerID`=? LIMIT 1");
                $query3->execute(array($adid, $rSA['language'], $reseller_id));
                $descriptionrow = $query->fetchColumn();
			}

            $addescription = nl2br($descriptionrow);

            $query3 =  ($protected == 'Y') ? $sql->prepare("SELECT `id` FROM `addons_installed` WHERE `userid`=? AND `serverid`=? AND `addonid`=? AND `servertemplate`=? AND `paddon`='Y' AND `resellerid`=? LIMIT 1") : $sql->prepare("SELECT `id` FROM `addons_installed` WHERE `userid`=? AND `serverid`=? AND `addonid`=? AND `servertemplate`=? AND `resellerid`=? LIMIT 1");
            $query3->execute(array($user_id, $serverid, $adid, $servertemplate, $reseller_id));
            $installedid = $query3->fetchColumn();

            if (isid($installedid, 19)){

                $action = 'dl';
                $delete = '&amp;rid=' . $installedid;

            } else {

                $query3 = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `addons_installed` WHERE `userid`=? AND `serverid`=? AND `servertemplate`=? AND `addonid`=? AND `resellerid`=? LIMIT 1");
                $query3->execute(array($user_id, $serverid, $servertemplate, $depending, $reseller_id));
                $colcount = $query3->fetchColumn();

                if ($row2['type'] == 'map' or $depending == 0 or ($depending > 0 and $colcount > 0)) {
                    $action = 'ad';
                } else {

                    $action = 'none';

                    $query3 = $sql->prepare("SELECT `menudescription` FROM `addons` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                    $query3->execute(array($depending, $reseller_id));
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