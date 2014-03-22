<?php

/**
 * File: userpanel_web_vhost.php.
 * Author: Ulrich Block
 * Time: 08:04
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

if ((!isset($user_id) or $main != 1) or (isset($user_id) and !$pa['webvhost'])) {
    header('Location: userpanel.php');
    die;
}

include(EASYWIDIR . '/stuff/keyphrasefile.php');
include(EASYWIDIR . '/stuff/methods/class_httpd.php');

$sprache = getlanguagefile('web', $user_language, $reseller_id);
$gsSprache = getlanguagefile('gserver', $user_language, $reseller_id);
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

if ($ui->id('id', 10, 'get') and in_array($ui->st('d', 'get'), array('if', 'pw'))) {
    $query = $sql->prepare("SELECT `dns` FROM `webVhost` WHERE `webVhostID`=? AND `userID`=? AND `resellerID`=? AND `active`='Y'");
    $query->execute(array($ui->id('id', 10, 'get'), $user_id, $reseller_id));
    $dns = $query->fetchColumn();
}

if ($ui->st('d', 'get') == 'pw' and $ui->id('id', 10, 'get') and (!isset($_SESSION['sID']) or in_array($ui->id('id', 10, 'get'), $substituteAccess['ws']))) {

    $id = $ui->id('id', 10, 'get');

    $errors = array();

    if ($ui->st('action', 'post') == 'pw') {

        if ($ui->w('action', 4, 'post') and !token(true)) {
            $errors[] = $spracheResponse->token;
        }

        if (!$ui->password('password1', 40, 'post') or !$ui->password('password2', 40, 'post')) {
            $errors[] = $sprache->error_password_not_set;
        }

        if ($ui->password('password1', 40, 'post') != $ui->password('password2', 40, 'post')) {
            $errors[] = $sprache->error_password_not_match;
        }

        if (count($errors) == 0) {
            $query = $sql->prepare("UPDATE `webVhost` SET `ftpPassword`=AES_ENCRYPT(?,?) WHERE `webVhostID`=? AND `userID`=? AND `resellerID`=? AND `active`='Y' LIMIT 1");
            $query->execute(array($ui->password('password1', 40, 'post'), $aeskey, $id, $user_id, $reseller_id));

            if ($query->rowCount() == 0) {
                $errors[] = $spracheResponse->error_table;
            }
        }

        if (count($errors) > 0) {

            unset($header, $text);

            $template_file = 'userpanel_web_vhost_pw.tpl';

        } else {

            $query = $sql->prepare("SELECT `webMasterID` FROM `webVhost` WHERE `webVhostID`=? AND `userID`=? AND `resellerID`=? AND `active`='Y' LIMIT 1");
            $query->execute(array($id, $user_id, $reseller_id));
            $webMasterID = $query->fetchColumn();

            $vhostObject = new HttpdManagement($webMasterID, $reseller_id);

            if ($vhostObject != false and $vhostObject->ssh2Connect()) {

                $vhostObject->changePassword($id);
                $template_file = $sprache->ftpPasswordChanged;
            }

        }

    } else {
        $template_file = 'userpanel_web_vhost_pw.tpl';
    }

} else if ($ui->st('d', 'get') == 'if' and $ui->id('id', 10, 'get') and (!isset($_SESSION['sID']) or in_array($ui->id('id', 10, 'get'), $substituteAccess['ws']))) {

    if ( ($query->rowCount() > 0)) {

        $hlCfg = 'sv_downloadurl "http://' . $dns . '/"
sv_allowdownload "1"
sv_allowupload "1"';

        $codCfg = 'set sv_allowDownload "1"
set sv_wwwBaseURL "http://' . $dns . '/"
set sv_wwwDlDisconnected "0"
set sv_wwwDownload "1"';

        $template_file = 'userpanel_web_vhost_info.tpl';

    } else {
        $template_file = 'userpanel_404.tpl';
    }

} else {

    $table = array();

    $query = $sql->prepare("SELECT v.`webVhostID`,v.`dns`,v.`hdd`,v.`ftpUser`,AES_DECRYPT(v.`ftpPassword`,?) AS `decryptedFTPPass`,m.`ip`,m.`ftpIP`,m.`ftpPort`,m.`quotaActive` FROM `webVhost` AS v INNER JOIN `webMaster` AS m ON m.`webMasterID`=v.`webMasterID` WHERE v.`userID`=? AND v.`resellerID`=? AND v.`active`='Y'");
    $query->execute(array($aeskey, $user_id, $reseller_id));
    foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
        if (!isset($_SESSION['sID']) or in_array($row['webVhostID'], $substituteAccess['ws'])) {
            $table[] = array('id' => $row['webVhostID'], 'dns' => $row['dns'], 'hdd' => $row['hdd'], 'quotaActive' => $row['quotaActive'], 'ftpIP' => (isip($row['ftpIP'], 'ip4')) ? $row['ftpIP'] : $row['ip'], 'ftpPort' => $row['ftpPort'], 'ftpUser' => $row['ftpUser'], 'ftpPass' => $row['decryptedFTPPass']);
        }
    }

    $template_file = 'userpanel_web_vhost_list.tpl';
}