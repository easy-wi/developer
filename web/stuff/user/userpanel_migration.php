<?php
/**
 * File: userpanel_migration.php.
 * Author: Ulrich Block
 * Date: 01.02.13
 * Time: 17:13
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

if ((!isset($main) or $main != 1) or (!isset($user_id) or (isset($user_id) and !$pa['restart']))) {
    header('Location: userpanel.php');
    die;
}

include(EASYWIDIR . '/stuff/keyphrasefile.php');
include(EASYWIDIR . '/stuff/methods/class_ftp.php');
include(EASYWIDIR . '/stuff/methods/functions_gs.php');
include(EASYWIDIR . '/stuff/methods/class_app.php');

$sprache = getlanguagefile('gserver',$user_language,$reseller_id);
$loguserid = $user_id;
$logusername = getusername($user_id);
$logusertype = 'user';
$logreseller = 0;
$logsubuser = 0;
if (isset($admin_id)) {
    $logsubuser = $admin_id;
} else if (isset($subuser_id)) {
    $logsubuser = $subuser_id;
}
if (isset($admin_id) and $reseller_id != 0) {
    $reseller_id = $admin_id;
}
$ftpAddress = '';
$ftpPort=21;
$ftpUser = '';
$ftpPassword = '';
$ftpPath = '';
$thisID = 0;
$thisTemplate = '';
$ssl=($ui->active('ssl', 'post')) ? $ui->active('ssl', 'post') : 'N';
$error = array();
$table = array();

$query = $sql->prepare("SELECT AES_DECRYPT(g.`ftppassword`,?) AS `cftppass`,g.`id`,g.`newlayout`,g.`rootID`,g.`serverip`,g.`port`,g.`pallowed`,g.`protected`,u.`cname` FROM `gsswitch` g INNER JOIN `userdata` u ON g.`userid`=u.`id` WHERE g.`userid`=? AND g.`resellerid`=? AND g.`active`='Y'");
$query2 = $sql->prepare("SELECT s.`id`,t.`description`,t.`shorten`,t.`gamebinary`,t.`binarydir`,t.`modfolder`,t.`appID` FROM `serverlist` s INNER JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE s.`switchID`=? GROUP BY t.`shorten` ORDER BY t.`shorten`");
$query->execute(array($aeskey, $user_id, $reseller_id));

while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    if (!isset($_SESSION['sID']) or in_array($row['id'], $substituteAccess['gs'])) {

        $temp = array();
        $search = '';
        $customer = $row['cname'];

        if ($row['newlayout'] == 'Y') {
            $customer = $row['cname'] . '-' . $row['id'];
        }

        $query2->execute(array($row['id']));
        while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {

            if ($row2['gamebinary'] == 'hlds_run' or ($row2['gamebinary'] == 'srcds_run' and ($row2['appID'] == 740 or $row2['appID'] == 730))) {
                $search = '/' . $row2['modfolder'];
            } else if ($row2['gamebinary'] == 'srcds_run' or $row2['gamebinary'] == 'hlds_run') {
                $search = '/' . $row2['binarydir']. '/' . $row2['modfolder'];
                $search = str_replace(array('//', '///'), '', $search);
            }

            $temp[$row2['shorten']] = array('shorten' => $row2['shorten'], 'description' => $row2['description'], 'searchFor' => $search, 'modfolder' => $row2['modfolder']);
        }

        $table[$row['id']] = array('id' => $row['id'], 'address' => $row['serverip'] . ':' . $row['port'], 'games' => $temp,'rootID' => $row['rootID'], 'gsfolder' => $row['serverip'] . '_' . $row['port'], 'customer' => $customer,'cftppass' => $row['cftppass']);
    }
}

if ($ui->w('action', 4, 'post') and !token(true)) {

    $template_file = $spracheResponse->token;

} else if ($ui->smallletters('action',2, 'post') == 'ms') {

    if (!$ui->domain('ftpAddress', 'post') and !$ui->ip('ftpAddress', 'post')) {
        $error[] = $sprache->ftp_adresse;
    } else {
        $ftpAddress = $ui->post['ftpAddress'];
    }

    if (!$ui->port('ftpPort', 'post')) {
        $error[] = $sprache->ftp_port;
    } else {
        $ftpPort = $ui->port('ftpPort', 'post');
    }

    if (!$ui->config('ftpUser', 'post')) {
        $error[] = $sprache->ftp_user;
    } else {
        $ftpUser = $ui->config('ftpUser', 'post');
    }

    if (!$ui->config('ftpPassword', 'post')) {
        $error[] = $sprache->ftp_password;
    } else {
        $ftpPassword = $ui->config('ftpPassword', 'post');
    }

    if (!$ui->id('switchID', 10, 'post') or !isset($table[$ui->id('switchID', 10, 'post')])) {

        $error[] = $sprache->server;

    } else {
        $thisID = $ui->id('switchID', 10, 'post');
        $address = $table[$ui->id('switchID', 10, 'post')]['address'];
        $rootID = $table[$ui->id('switchID', 10, 'post')]['rootID'];
        $gsfolder = $table[$ui->id('switchID', 10, 'post')]['gsfolder'];
        $customer = $table[$ui->id('switchID', 10, 'post')]['customer'];
        $cftppass = $table[$ui->id('switchID', 10, 'post')]['cftppass'];
    }

    if (!$ui->config('template', 'post',$thisID) or !isset($table[$ui->id('switchID', 10, 'post')]['games'])) {

        $error[] = $gsprache->template;

    } else if (isset($table[$ui->id('switchID', 10, 'post')]['games'])) {

        foreach($table[$ui->id('switchID', 10, 'post')]['games'] as $game) {
            unset($temp);

            if ($ui->config('template', 'post',$thisID) == $game['shorten']) {
                $temp = 1;
            } else if ($ui->config('template', 'post',$thisID) == $game['shorten'] . '-2') {
                $temp = 2;
            } else if ($ui->config('template', 'post',$thisID) == $game['shorten'] . '-3') {
                $temp = 3;
            }

            if (isset($temp)) {
                $gameSwitchTemplate = ($temp == 1) ? $game['shorten'] : $game['shorten'] . '-' . $temp;
                $searchFor = str_replace('/', '', $game['searchFor']);
                $modFolder = $game['modfolder'];
            }
        }

        if (isset($gameSwitchTemplate)) {
            $thisTemplate = $ui->config('template', 'post', $thisID);
        } else if (!in_array($gsprache->template, $error)) {
            $error[] = $gsprache->template;
        }
    }

    if ($ui->anyPath('ftpPath', 'post')) {
        $ftpPath = $ui->anyPath('ftpPath', 'post');
    }

    $ftp = new EasyWiFTP($ftpAddress, $ftpPort, $ftpUser, $ftpPassword, $ssl);

    if ($ftp->ftpConnection and $ftp->loggedIn) {

        if (isset($searchFor)) {

            $ftpPath = $ftp->checkPath($ftpPath, $searchFor);

            if (!$ftpPath) {

                $foundPath = $ftp->checkFolders($ui->anyPath('ftpPath', 'post'), $searchFor, 5);

                $ftpPath = (is_array($foundPath)) ? '' : str_replace('//', '/', $foundPath);

                if (strlen($searchFor) > 0 or strlen($ftpPath) == 0) {
                    $error[] = $sprache->ftp_path . '. ' . $sprache->import_corrected;
                }
            }
        }

    } else if ($ftp->ftpConnection and !$ftp->loggedIn) {

        $error[] = $sprache->ftp_user;
        $error[] = $sprache->ftp_password;

    } else {
        if (!in_array($sprache->ftp_adresse,$error)) {
            $error[] = $sprache->ftp_adresse;
        }
        if (!in_array($sprache->ftp_port,$error)) {
            $error[] = $sprache->ftp_port;
        }
    }

    if (count($error) == 0 and isset($rootID)) {

        $ftpConnectString = ($ssl == 'N') ? 'ftp://' : 'ftps://';
        $ftpConnectString .= $ftpAddress . ':' . $ftpPort . str_replace(array('//', '///'), '/', '/' . $ftpPath);

        $appServer = new AppServer($rootID);
        $appServer->getAppServerDetails($thisID);
        $appServer->migrateToEasyWi(array('user' => $ftpUser, 'password' => $ftpPassword, 'path' => $ftpPath, 'connectString' => $ftpConnectString), $gameSwitchTemplate, $modFolder);
        $appServer->execute();

        $template_file = $sprache->import_start;

        if (isset($dbConnect['debug']) and $dbConnect['debug'] == 1) {
            $template_file .= '<br><pre>' . implode("\r\n", $appServer->debug()) . '</pre>';
        }

        $loguseraction = '%import% %gserver% ' . $address;
        $insertlog->execute();
    }
}

if (!isset($template_file) and isset($customer)) {
    $template_file = 'userpanel_gserver_migration.tpl';
}

if (!isset($template_file)) {
    $template_file = 'userpanel_404.tpl';
}