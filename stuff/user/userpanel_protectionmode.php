<?php

/**
 * File: userpanel_protectionmode.php.
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
if ((!isset($user_id) or $main != 1) or (isset($user_id) and !$pa['restart']) or !$ui->id('id', 10, 'get')) {
	header('Location: userpanel.php');
	die;
}

include(EASYWIDIR . '/stuff/keyphrasefile.php');
include(EASYWIDIR . '/stuff/methods/class_ftp.php');
include(EASYWIDIR . '/stuff/methods/class_app.php');
include(EASYWIDIR . '/stuff/methods/functions_gs.php');
include(EASYWIDIR . '/stuff/methods/functions_ssh_exec.php');

$sprache = getlanguagefile('gserver', $user_language, $reseller_id);
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

$id = $ui->id('id', 10, 'get');

$query = $sql->prepare("SELECT `rootID` FROM `gsswitch` WHERE `id`=? AND `userid`=? AND `resellerid`=? LIMIT 1");
$query->execute(array($id, $user_id, $resellerLockupID));
$rootID = $query->fetchColumn();

$appServer = new AppServer($rootID);
$appServer->getAppServerDetails($id);

if ($query->rowCount() == 0 or !$appServer->appServerDetails or $appServer->appServerDetails['protectionModeAllowed'] == 'N' or (isset($_SESSION['sID']) and !in_array($id, $substituteAccess['gs']))) {

    redirect('userpanel.php');

} else if ($rootID > 0) {

    $files = array();

    foreach (explode("\r\n", $appServer->appServerDetails['template']['protectedSaveCFGs']) as $cfg) {
        if ($cfg != '') {
            $files[] = $cfg;
        }
    }

    if ($appServer->appServerDetails['protectionModeStarted'] == 'Y') {
        $protected = 'N';
        $template_file = $sprache->protect . ' ' . $sprache->off2;
        $loguseraction = '%stop% %pmode% ' . $appServer->appServerDetails['serverIP'] . ':' . $appServer->appServerDetails['port'];
    } else {
        $protected = 'Y';
        $template_file = $sprache->protect . ' ' . $sprache->on;
        $loguseraction = '%restart% %pmode% ' . $appServer->appServerDetails['serverIP'] . ':' . $appServer->appServerDetails['port'];
    }


    $query = $sql->prepare("UPDATE `serverlist` SET `anticheat`='1' WHERE `id`=? AND `resellerid`=? LIMIT 1");
    $query->execute(array($id, $resellerLockupID));

    $ftp = new EasyWiFTP($appServer->appMasterServerDetails['ssh2IP'], $appServer->appMasterServerDetails['ftpPort'], $appServer->appServerDetails['userNameExecute'], $appServer->appServerDetails['ftpPasswordExecute']);

    if ($ftp->loggedIn) {
        $ftp->downloadToTemp($appServer->appServerDetails['absoluteFTPPath'], 0, $files);
    }

    $query = $sql->prepare("UPDATE `gsswitch` SET `protected`=? WHERE `id`=? LIMIT 1");
    $query->execute(array($protected, $id));

    $appServer->getAppServerDetails($id);

    if ($ftp->loggedIn) {

        $ftp->createSecondFTPConnect($appServer->appMasterServerDetails['ssh2IP'], $appServer->appMasterServerDetails['ftpPort'], $appServer->appServerDetails['userNameExecute'], $appServer->appServerDetails['ftpPasswordExecute']);

        if ($ftp->secondLoggedIn) {
            $ftp->uploadFileFromTemp($appServer->appServerDetails['absoluteFTPPath']);
        }
    }

    if ($appServer->appServerDetails['protectionModeStarted'] == 'Y') {

        $query = $sql->prepare("UPDATE `gsswitch` SET `ppassword`=AES_ENCRYPT(?,?),`psince`=NOW() WHERE `id`=? LIMIT 1");
        $query->execute(array(passwordgenerate(10), $aeskey, $id));

        $appServer->getAppServerDetails($id);
        $appServer->userCud('add');
        $appServer->removeApp(array($appServer->appServerDetails['app']['templateChoosen']));
        $appServer->addApp();
    }

    $ftp = null;

    $appServer->startApp();
    $appServer->execute();

    $insertlog->execute();

    if (isset($dbConnect['debug']) and $dbConnect['debug'] == 1) {
        $template_file .= '<br><pre>' . implode("\r\n", $appServer->debug()) . '</pre>';
    }
}