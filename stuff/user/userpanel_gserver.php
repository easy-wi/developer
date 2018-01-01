<?php

/**
 * File: userpanel_gserver.php.
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
if ((!isset($main) or $main != 1) or (!isset($user_id) or (isset($user_id) and !$pa['restart']))) {
    header('Location: userpanel.php');
    die('No Access');
}

include(EASYWIDIR . '/stuff/keyphrasefile.php');
include(EASYWIDIR . '/stuff/methods/class_ftp.php');
include(EASYWIDIR . '/stuff/methods/functions_gs.php');
include(EASYWIDIR . '/stuff/methods/class_app.php');

if (isset($resellerLockupID)) {
    $reseller_id = $resellerLockupID;
}

$sprache = getlanguagefile('gserver', $user_language, $reseller_id);
$imageSprache = getlanguagefile('images', $user_language, $reseller_id);
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

if ($ui->w('action', 4, 'post') and !token(true)) {
    $template_file = $spracheResponse->token;

} else if (in_array($ui->st('d', 'get'), array('ri', 'wf')) and !$ui->id('id', 10, 'get')) {
    $template_file = $sprache->error_id;

} else if ($ui->st('d', 'get') == 'wf' and $ui->id('id', 10, 'get') and ($pa['ftpaccess'] or $pa['miniroot'])) {

    $query = $sql->prepare("SELECT g.*,AES_DECRYPT(g.`ftppassword`,?) AS `cftppass`,u.`cname`,r.`ftpport`,s.`servertemplate`,t.`shorten` FROM `gsswitch` g INNER JOIN `userdata` u ON g.`userid`=u.`id` INNER JOIN `rserverdata` r ON g.`rootID`=r.`id` INNER JOIN `serverlist` s ON g.`serverid`=s.`id` INNER JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE g.`id`=? AND g.`userid`=? AND g.`resellerid`=? AND t.`ftpAccess`='Y' LIMIT 1");
    $query->execute(array($aeskey, $ui->id('id', 10, 'get'), $user_id, $resellerLockupID));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $ftpIP = $row['serverip'];
        $ftpPort = $row['ftpport'];
        $ftpPass = $row['cftppass'];
        $ftpUser = ($row['newlayout'] == 'Y') ? $row['cname'] . '-' . $row['id'] : $row['cname'];
        $gsFolder = '/server/';
        $gsFolder .= ($row['servertemplate'] == 1) ? $row['shorten'] : $row['shorten'] . '-' . $row['servertemplate'];
        $address = $row['serverip'] . ':' . $row['port'];
        $shorten = $row['shorten'];
    }

    if ($query->rowCount() > 0) {

        $userPanelInclude = true;

        include(EASYWIDIR . '/third_party/monstaftp/class_monstaftp.php');
        include(EASYWIDIR . '/third_party/monstaftp/monstaftp.php');

    } else {
        $template_file = 'userpanel_404.tpl';
    }

} else if ($ui->st('d', 'get') == 'sl' and $ui->id('id', 10, 'get') and (!isset($_SESSION['sID']) or in_array($ui->id('id', 10, 'get'), $substituteAccess['gs']))) {

    $id = $ui->id('id', 10, 'get');

    $query = $sql->prepare("SELECT g.`serverip`,g.`port`,g.`id`,g.`stopped`,t.`liveConsole` FROM `gsswitch` AS g INNER JOIN `serverlist` AS s ON g.`serverid`=s.`id` INNER JOIN `servertypes` AS t ON s.`servertype`=t.`id` WHERE g.`id`=? AND g.`userid`=? AND g.`resellerid`=? LIMIT 1");
    $query->execute(array($id, $user_id, $resellerLockupID));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $serverIp = $row['serverip'];
        $port = $row['port'];
        $liveConsole = $row['liveConsole'];
        $id = $row['id'];
        $stopped = $row['stopped'];
    }

    if ($query->rowCount() > 0) {
        $template_file = 'userpanel_gserver_log.tpl';
    } else {
        $template_file = 'userpanel_404.tpl';
    }

} else if ($ui->st('d', 'get') == 'ri' and $ui->id('id', 10, 'get') and (!isset($_SESSION['sID']) or in_array($ui->id('id', 10, 'get'), $substituteAccess['gs']))) {

    $id = (int) $ui->id('id', 10, 'get');

    if ($ui->st('action', 'post') == 'ri') {

        $query = $sql->prepare("SELECT `rootID` FROM `gsswitch` WHERE `id`=? AND `userid`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id, $user_id, $resellerLockupID));
        $rootID = $query->fetchColumn();

        $appServer = new AppServer($rootID);

        $appServer->getAppServerDetails($id);
        $appServer->userCud('add');

        $game = $ui->id('game', 10, 'post');

        if ($ui->active('type', 'post') == 'Y') {
            $query = $sql->prepare("DELETE FROM `addons_installed` WHERE `serverid`=? AND `resellerid`=?");
            $query->execute(array($game, $resellerLockupID));
        }

        $query = $sql->prepare("SELECT t.`shorten` FROM `serverlist` s INNER JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE s.`id`=? AND s.`resellerid`=? LIMIT 1");
        $query->execute(array($game, $resellerLockupID));
        $shorten = $query->fetchColumn();

        $template = (in_array($ui->id('template', 10, 'post'), array(1, 2, 3, 4))) ? (int) $ui->id('template', 10, 'post') : 1;

        if ($template == 4) {
            $templates = array($shorten, $shorten . '-2', $shorten . '-3');
        } else if ($template == 1) {
            $templates = array($shorten);
        } else {
            $templates = array($shorten . '-' . $template);
        }

        if ($ui->active('type', 'post') == 'Y') {

            $appServer->stopAppHard();
            $appServer->removeApp($templates);

            $loguseraction = "%reinstall% %gserver% {$appServer->appServerDetails['serverIP']}:{$appServer->appServerDetails['port']}";

        } else {

            $appServer->stopApp();

            $loguseraction = "%resync% %gserver% {$appServer->appServerDetails['serverIP']}:{$appServer->appServerDetails['port']}";
        }

        $insertlog->execute();

        $appServer->addApp($templates);

        $appServer->execute();

        $template_file = $sprache->server_installed;

        if (isset($dbConnect['debug']) and $dbConnect['debug'] == 1) {
            $template_file .= '<br><pre>' . implode("\r\n", $appServer->debug()) . '</pre>';
        }

    } else {

        $shorten = '';
        $selected2 = '';
        $selected3 = '';

        $query = $sql->prepare("SELECT `serverid` FROM `gsswitch` WHERE `id`=? AND `resellerid`=?");
        $query->execute(array($id, $resellerLockupID));
        $currentID = $query->fetchColumn();

        $query = $sql->prepare("SELECT s.*,t.`description`,t.`shorten` FROM `serverlist` s INNER JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE s.`switchID`=? AND s.`resellerid`=?");
        $query->execute(array($id, $resellerLockupID));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            if ($currentID == $row['id']) {

                $shorten = $row['shorten'];

                if ($row['servertemplate'] == 2) {
                    $selected2 = 'selected="selected"';
                } else if ($row['servertemplate'] == 3) {
                    $selected3 = 'selected="selected"';
                }
            }

            $table[] = array(
                'id' => $row['id'],
                'description' => $row['description'],
                'shorten' => $row['shorten']
            );
        }

        $template_file = (count($table) > 0) ? 'userpanel_gserver_reinstall.tpl' : 'userpanel_404.tpl';
    }

} else if (($ui->st('d', 'get') == 'rs' or $ui->st('d', 'get') == 'st' or $ui->st('d', 'get') == 'du') and $ui->id('id', 10, 'get') and (!isset($_SESSION['sID']) or in_array($ui->id('id', 10, 'get'),$substituteAccess['gs']))) {

    $id = $ui->id('id', 10, 'get');

    $query = $sql->prepare("SELECT `serverip`,`port`,`rootID` FROM `gsswitch` WHERE `id`=? AND `resellerid`=? AND `active`='Y' LIMIT 1");
    $query->execute(array($id,$resellerLockupID));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

        $gsip = $row['serverip'];
        $port = $row['port'];

        $appServer = new AppServer($row['rootID']);
        $appServer->getAppServerDetails($id);

        if ($ui->st('d', 'get') == 'rs') {

            $appServer->startApp();

            $template_file = 'Restart done';
            $loguseraction = "%start% %gserver% $gsip:$port";

        } else if ($ui->st('d', 'get') == 'st') {

            $appServer->stopApp();

            $template_file = 'Stop done';
            $loguseraction = "%stop% %gserver% $gsip:$port";

        } else if ($ui->st('d', 'get') == 'du') {

            $appServer->demoUpload();

            $template_file = 'SourceTV upload started';
            $loguseraction =" %movie% %gserver% $gsip:$port";

        }

        $appServer->execute();

        if (isset($dbConnect['debug']) and $dbConnect['debug'] == 1) {
            $template_file .= '<br><pre>' . implode("\r\n", $appServer->debug()) . '</pre>';
        }

        $insertlog->execute();
    }

    if (!isset($gsip)) {
        $template_file = 'userpanel_404.tpl';
    }

} else if ($ui->st('d', 'get') == 'md' and $ui->id('id', 10, 'get') and (!isset($_SESSION['sID']) or in_array($ui->id('id', 10, 'get'),$substituteAccess['gs']))) {

    $id = $ui->id('id', 10, 'get');
    $ftpAccess = 'Y';

    if (!$ui->smallletters('action',2, 'post')) {

        $table = array();
        $i = 0;

        $query = $sql->prepare("SELECT `id`,`normal_3`,`normal_4`,`hlds_3`,`hlds_4`,`hlds_5`,`hlds_6` FROM `eac` WHERE active='Y' AND `resellerid`=? LIMIT 1");
        $query->execute(array($resellerLockupID));
        $rowcount = $query->rowCount();
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $normal_3 = $row['normal_3'];
            $normal_4 = $row['normal_4'];
            $hlds_3 = $row['hlds_3'];
            $hlds_4 = $row['hlds_4'];
            $hlds_5 = $row['hlds_5'];
            $hlds_6 = $row['hlds_6'];
        }

        $query = $sql->prepare("SELECT g.`description`,g.`autoRestart`,g.`updateRestart`,g.`id`,g.`serverip`,g.`port`,g.`eacallowed`,g.`serverid`,g.`newlayout`,g.`protected`,AES_DECRYPT(g.`ftppassword`,?) AS `cftppass`,AES_DECRYPT(g.`ppassword`,?) AS `pftppass`,u.`cname`,r.`ftpport` FROM `gsswitch` g INNER JOIN `userdata` u ON g.`userid`=u.`id` INNER JOIN `rserverdata` r ON g.`rootID`=r.`id` WHERE g.`id`=? AND g.`userid`=? AND g.`resellerid`=? LIMIT 1");
        $query->execute(array($aeskey, $aeskey, $id, $user_id, $resellerLockupID));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            $description = $row['description'];
            $autoRestart = $row['autoRestart'];
            $updateRestart = $row['updateRestart'];
            $gsIP = $row['serverip'];
            $gsPort = $row['port'];
            $ftppass = $row['cftppass'];
            $eacallowed = $row['eacallowed'];
            $serverID = $row['serverid'];
            $eacallowed = $row['eacallowed'];
            $ftpPort = $row['ftpport'];
            $protected = $row['protected'];

            $address = $row['serverip'] . ':' .$row['port'];
            $ftpUser = ($row['newlayout'] == 'Y') ? $row['cname'] . '-' . $row['id'] : $row['cname'];

            if ($row['protected'] == 'Y') {
                $ftpUser .= '-p';
                $pserverFolder = '';
                $ftpPWD = $row['pftppass'];
            } else {
                $pserverFolder = 'server/';
                $ftpPWD = $row['cftppass'];
            }
        }

        if (isset($gsIP)) {

            $query = $sql->prepare("SELECT 1 FROM `serverlist` s INNER JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE s.`switchID`=? AND s.`resellerid`=? AND (t.`mapGroup` IS NOT NULL OR t.`mapGroup`!='') LIMIT 1");
            $query->execute(array($id, $resellerLockupID));

            if ($query->rowCount() > 0) {
                $ftp = new EasyWiFTP($gsIP, $ftpPort, $ftpUser, $ftpPWD);
            }

            $query = $sql->prepare("SELECT s.*,AES_DECRYPT(s.`uploaddir`,?) AS `decypteduploaddir`,AES_DECRYPT(s.`webapiAuthkey`,?) AS `dwebapiAuthkey`,AES_DECRYPT(s.`steamServerToken`,?) AS `dsteamServerToken`,t.`modfolder`,t.`description`,t.`gamebinary`,t.`shorten`,t.`modcmds`,t.`ftpAccess`,t.`appID`,t.`workShop` AS `workShopAllowed`,t.`map` AS `defaultmap`,t.`mapGroup` AS `defaultMapGroup`,t.`steamGameserverToken` FROM `serverlist` s INNER JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE s.`switchID`=? AND s.`resellerid`=?");
            $query->execute(array($aeskey, $aeskey, $aeskey, $id, $resellerLockupID));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $eac = array();
                $mods = array();

                $gshorten = $row['shorten'];
                $anticheat = $row['anticheat'];

                if ($row['ftpAccess'] == 'N') {
                    $ftpAccess = 'N';
                }

                if ($row['gamebinary'] == 'srcds_run' and $row['user_uploaddir'] == 'Y' and $row['upload']>1 and $row['upload']<4) {
                    $uploaddir = $row['decypteduploaddir'];
                    $upload = true;
                } else {
                    $upload = false;
                    $uploaddir = '';
                }

                if ($row['gamebinary'] == 'srcds_run' or $row['gamebinary'] == 'hlds_run') {
                    $anticheatsoft="Valve Anti Cheat";
                } else if ($row['gamebinary'] == 'cod4_lnxded') {
                    $anticheatsoft = 'Punkbuster';
                } else {
                    $anticheatsoft = '';
                }

                if ($row['id'] == $serverID) {
                    $currentTemplate = $gshorten;
                    $displayNone = '';
                    $displayNoneBoot = 'in';
                    $option = '<option value="'.$row['id'].'" selected="selected">'.$gshorten.'</option>';
                } else {
                    $displayNone = 'display_none';
                    $displayNoneBoot = '';
                    $option = '<option value="'.$row['id'].'">'.$gshorten.'</option>';
                }


                if ($rowcount > 0 and $eacallowed == 'Y' and in_array($gshorten, array('css', 'csgo', 'cstrike', 'czero', 'tf'))) {

                    if ($gshorten == 'cstrike' or $gshorten == 'czero') {
                        if ($anticheat == 3 and $hlds_3 == 'Y') {
                            $eac[] = '<option value="3" selected="selected">Easy Anti Cheat</option>';
                        } else if ($hlds_3 == 'Y') {
                            $eac[] = '<option value="3">Easy Anti Cheat</option>';
                        }
                        if ($anticheat == 4 and $hlds_4 == 'Y') {
                            $eac[] = '<option value="4" selected="selected">Easy Anti Cheat Public</option>';
                        } else if ($hlds_4 == 'Y') {
                            $eac[] = '<option value="4">Easy Anti Cheat Public</option>';
                        }
                        if ($anticheat == 5 and $hlds_5 == 'Y') {
                            $eac[] = '<option value="5" selected="selected">Easy Anti Cheat 32Bit</option>';
                        } else if ($hlds_5 == 'Y') {
                            $eac[] = '<option value="5">Easy Anti Cheat 32Bit</option>';
                        }
                        if ($anticheat == 6 and $hlds_6 == 'Y') {
                            $eac[] = '<option value="6" selected="selected">Easy Anti Cheat Public 32Bit</option>';
                        } else if ($hlds_6 == 'Y') {
                            $eac[] = '<option value="6">Easy Anti Cheat Public 32Bit</option>';
                        }
                    } else {
                        if ($anticheat == 3 and $normal_3 == 'Y') {
                            $eac[] = '<option value="3" selected="selected">Easy Anti Cheat</option>';
                        } else if ($normal_3 == 'Y') {
                            $eac[] = '<option value="3">Easy Anti Cheat</option>';
                        }
                        if ($anticheat == 4 and $normal_4 == 'Y') {
                            $eac[] = '<option value="4" selected="selected">Easy Anti Cheat Public</option>';
                        } else if ($normal_4 == 'Y') {
                            $eac[] = '<option value="4">Easy Anti Cheat Public</option>';
                        }
                    }
                }

                $mod = $row['modcmd'];
                foreach (explode("\r\n", $row['modcmds']) as $line) {
                    if (preg_match('/^(\[[\w\/\.\-\_\= ]{1,}\])$/',$line)) {
                        $name = trim($line,'[]');
                        $ex = preg_split("/\=/",$name,-1,PREG_SPLIT_NO_EMPTY);
                        $mods[] = trim($ex[0]);
                    }
                }

                $workshopCollection = false;
                if ($row['workShopAllowed'] == 'Y') {
                    $workshopCollection = $row['workshopCollection'];
                }

                $mapGroupsAvailable = array();

                if (!in_array($row['defaultMapGroup'],array('',null))) {

                    if ($ftp->loggedIn) {

                        $serverTemplate = ($row['servertemplate'] == 1 or $protected == 'Y') ? $row['shorten'] : $row['shorten'] . '-' . $row['servertemplate'];

                        $ftp->downloadToTemp($pserverFolder . $gsIP . '_' . $gsPort . '/' . $serverTemplate . '/' . $row['modfolder'] . '/', 0, array('gamemodes.txt','gamemodes_server.txt'));

                        $mapGroupsAvailable = $ftp->getMapGroups();
                        $ftp->removeTempFiles();
                    }

                }

                $map = (!in_array($row['defaultmap'], array('', null))) ? $row['map'] : null;

                $table[] = array('id' => $row['id'], 'cmd' => $row['cmd'], 'fps' =>$row['fps'], 'tic' => $row['tic'], 'map' => $map, 'workShop' => $row['workShop'], 'workshopCollection' => $workshopCollection, 'webapiAuthkey' => $row['dwebapiAuthkey'], 'steamGameserverToken' => $row['steamGameserverToken'], 'steamServerToken' => $row['dsteamServerToken'], 'mapGroup' => $row['mapGroup'], 'defaultMapGroup' => $row['defaultMapGroup'], 'mapGroupsAvailable' => $mapGroupsAvailable, 'servertemplate' => $row['servertemplate'], 'userfps' => $row['userfps'], 'usertick' => $row['usertick'], 'usermap' => $row['usermap'], 'description' => $row['description'], 'option' => $option, 'gamebinary' => $row['gamebinary'], 'upload' => $upload,'uploaddir' => $uploaddir, 'anticheat' => $anticheat, 'anticheatsoft' => $anticheatsoft, 'eac' => $eac, 'shorten' => $gshorten, 'mod' => $mod, 'mods' => $mods, 'displayNone' => $displayNone, 'displayNoneBoot' => $displayNoneBoot);
                $i++;
            }

            $ftp = null;
        }

        $template_file = ($i > 0) ? 'userpanel_gserver_md.tpl' : 'userpanel_404.tpl';

    } else if ($ui->smallletters('action',2, 'post') == 'md' and $ui->id('shorten', 19, 'post')) {

        $switchID = $ui->id('shorten', 19, 'post');
        $rootID = 0;

        $query = $sql->prepare("SELECT `active`,`normal_3`,`normal_4`,`hlds_3`,`hlds_4`,`hlds_5`,`hlds_6` FROM `eac` WHERE `resellerid`=? LIMIT 1");
        $query->execute(array($resellerLockupID));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $active = $row['active'];
            $normal_3 = $row['normal_3'];
            $normal_4 = $row['normal_4'];
            $hlds_3 = $row['hlds_3'];
            $hlds_4 = $row['hlds_4'];
            $hlds_5 = $row['hlds_5'];
            $hlds_6 = $row['hlds_6'];
        }

        $query = $sql->prepare("SELECT g.*,AES_DECRYPT(g.`ftppassword`,?) AS `encrypted`,AES_DECRYPT(g.`ppassword`,?) AS `pencrypted`,u.`cname` FROM `gsswitch` g INNER JOIN `userdata` u ON g.`userid`=u.`id` WHERE g.`id`=? AND g.`resellerid`=? LIMIT 1");
        $query->execute(array($aeskey, $aeskey, $id, $resellerLockupID));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $oldID = $row['serverid'];
            $serverip = $row['serverip'];
            $port = $row['port'];
            $oldPass = $row['encrypted'];
            $poldPass = $row['pencrypted'];
            $oldProtected = $row['protected'];
            $rootID = $row['rootID'];
            $servercname = $row['cname'];
            $newlayout = $row['newlayout'];
            $server = $serverip . ':' . $port;
        }

        $query = $sql->prepare("SELECT s.*,AES_DECRYPT(s.`uploaddir`,?) AS `decypteduploaddir`,t.`shorten`,t.`ftpAccess` FROM `serverlist` s INNER JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE s.`id`=? AND s.`resellerid`=? LIMIT 1");
        $query->execute(array($aeskey, $switchID, $resellerLockupID));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            $oldServerTemplate = $row['servertemplate'];

            $fps = ($row['userfps'] == 'Y' and $ui->id("fps_${switchID}", 4, 'post')) ? $ui->id("fps_${switchID}", 4, 'post') : $row['fps'];
            $tic = ($row['usertick'] == 'Y' and $ui->id("tic_${switchID}", 4, 'post')) ? $ui->id("tic_${switchID}", 4, 'post') : $row['tic'];
            $map = ($row['usermap'] == 'Y' and $ui->mapname("map_${switchID}", 'post')) ? $ui->mapname("map_${switchID}", 'post') : $row['map'];
            $mapGroup = ($row['usermap'] == 'Y' and $ui->mapname("mapGroup_${switchID}", 'post')) ? $ui->mapname("mapGroup_${switchID}", 'post') : $row['mapGroup'];
            $uploaddir = ($row['user_uploaddir'] == 'Y' and $row['upload'] > 1 and $row['upload'] < 4) ? $ui->url("uploaddir_${switchID}", 'post') : $row['decypteduploaddir'];
            $serverTemplate = ($ui->id("servertemplate_${switchID}", 1, 'post')) ? $ui->id("servertemplate_${switchID}", 1, 'post') : 1;
            $modcmd = $ui->escaped("mod_${switchID}", 'post');
            $workShop = ($ui->active("workShop_${switchID}", 'post')) ? $ui->active("workShop_${switchID}", 'post') : 'Y';
            $workshopCollection = $ui->id("workshopCollection_${switchID}", 10, 'post') ? $ui->id("workshopCollection_${switchID}", 10, 'post') : null;
            $webapiAuthkey = $ui->w("webapiAuthkey_${switchID}", 32, 'post');
            $steamServerToken = $ui->w("steamServerToken_${switchID}", 32, 'post');

            if ($ui->id("anticheat_${switchID}", 1, 'post')) {

                $anticheat=($ui->id("anticheat_${switchID}", 1, 'post')>0) ? $ui->id("anticheat_${switchID}", 1, 'post') : 1;

                if ($row['shorten'] == 'cstrike' or $row['shorten'] == 'czero') {

                    if ($anticheat == 3 and $hlds_3 == 'N' and $hlds_5 == 'Y' and $active == 'Y') {
                        $anticheat = 5;
                    } else if ($anticheat == 3 and $hlds_3 == 'N' and $hlds_5 == 'N' and $active == 'Y') {
                        $anticheat = 1;
                    } else if ($anticheat>1 and $active == 'N') {
                        $anticheat = 1;
                    }

                    if ($anticheat == 4 and $hlds_4 == 'N' and $hlds_6 == 'Y' and $active == 'Y') {
                        $anticheat = 6;
                    } else if ($anticheat == 4 and $hlds_4 == 'N' and $hlds_6 == 'N' and $active == 'Y') {
                        $anticheat = 1;
                    } else if ($anticheat > 1 and $active == 'N') {
                        $anticheat = 1;
                    }

                    if ($anticheat == 5 and $hlds_5 == 'N' and $active == 'Y') {
                        $anticheat = 1;
                    }

                    if ($anticheat == 6 and $hlds_6 == 'N' and $active == 'Y') {
                        $anticheat = 1;
                    }

                    if (($anticheat > 6 and $active == 'Y') or $anticheat > 2 and $active == 'N') {
                        $anticheat = 1;
                    }
                } else {
                    if ($anticheat == 3 and $normal_3 == 'N' and $active == 'Y') {
                        $anticheat = 1;
                    }

                    if ($anticheat == 4 and $normal_4 == 'N' and $active == 'Y') {
                        $anticheat = 1;
                    }

                    if (($anticheat > 4 and $active == 'Y') or $anticheat>2 and $active == 'N') {
                        $anticheat = 1;
                    }
                }
            } else {
                $anticheat = 1;
            }

            if ($row['ftpAccess'] == 'N') {
                $ftpAccess = 'N';
            }
        }

        if (isset($anticheat)) {

            $query = $sql->prepare("UPDATE `serverlist` SET `anticheat`=?,`fps`=?,`tic`=?,`map`=?,`workShop`=?,`workshopCollection`=?,`mapGroup`=?,`modcmd`=?,`servertemplate`=?,`uploaddir`=AES_ENCRYPT(?,?),`webapiAuthkey`=AES_ENCRYPT(?,?),`steamServerToken`=AES_ENCRYPT(?,?) WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($anticheat, $fps, $tic, $map, $workShop, $workshopCollection, $mapGroup, $modcmd, $serverTemplate, $uploaddir, $aeskey, $webapiAuthkey, $aeskey, $steamServerToken, $aeskey, $switchID, $resellerLockupID));

            $updated = ($query->rowCount() > 0) ? true : false;

        } else {
            $updated = false;
        }

        $query = $sql->prepare("UPDATE `gsswitch` SET `description`=?,`autoRestart`=?,`updateRestart`=? WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($ui->names('description', 255, 'post'), $ui->active('autoRestart', 'post'), $ui->active('updateRestart', 'post'), $id, $resellerLockupID));

        $updated = ($query->rowCount() > 0) ? true : $updated;

        $ftppass = $ui->password('ftppass', 100, 'post');

        if ($ftpAccess == 'Y') {

            $query = $sql->prepare("UPDATE `gsswitch` SET `serverid`=?,`ftppassword`=AES_ENCRYPT(?,?) WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($switchID, $ftppass, $aeskey, $id, $resellerLockupID));

            $updated = ($query->rowCount() > 0) ? true : $updated;
        }

        if (isset($oldID, $switchID, $oldProtected) and $oldID != $switchID and $oldProtected == 'Y') {

            $query = $sql->prepare("UPDATE `gsswitch` SET `protected`='N' WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($id, $resellerLockupID));

            $updated = ($query->rowCount() > 0) ? true : $updated;
        }

        if ($updated) {

            $appServer = new AppServer($rootID);

            $appServer->getAppServerDetails($id);

            if (isset($oldPass, $ftppass) and $ftppass != $oldPass) {
                $appServer->userCud('add');
            }

            if (isset($oldID, $switchID, $oldServerTemplate, $serverTemplate) and ($oldID != $switchID or $oldServerTemplate != $serverTemplate)) {
                $appServer->startApp();
            }

            $loguseraction = '%mod% %gserver% ' . $server;
            $insertlog->execute();

            $template_file = $spracheResponse->table_add;

            $appServer->execute();

            if (isset($dbConnect['debug']) and $dbConnect['debug'] == 1) {
                $template_file .= '<br><pre>' . implode("\r\n", $appServer->debug()) . '</pre>';
            }

        } else {
            $template_file = $spracheResponse->error_table;
        }
    } else {
        $template_file = 'Error: No such game!';
    }

} else if ($ui->st('d', 'get') == 'cf' and $ui->id('id', 10, 'get') and (!isset($_SESSION['sID']) or in_array($ui->id('id', 10, 'get'),$substituteAccess['gs']))) {

    $id = $ui->id('id', 10, 'get');

    $serverID = 0;
    $configs = array();
    $configCheck = array();

    $query = $sql->prepare("SELECT g.*,AES_DECRYPT(g.`ftppassword`,?) AS `dftppass`,AES_DECRYPT(g.`ppassword`,?) AS `dpftppass`,s.`anticheat`,s.`servertemplate`,t.`shorten`,t.`gamebinary`,t.`modfolder`,t.`binarydir`,u.`cname` FROM `gsswitch` g INNER JOIN `serverlist` s ON g.`serverid`=s.`id` INNER JOIN `servertypes` t ON s.`servertype`=t.`id` INNER JOIN `userdata` u ON g.`userid`=u.`id` WHERE g.`id`=? AND g.`userid`=? AND g.`resellerid`=? LIMIT 1");
    $query->execute(array($aeskey, $aeskey,$id, $user_id, $resellerLockupID));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

        $anticheat = $row['anticheat'];
        $eacallowed = $row['eacallowed'];
        $serverip = $row['serverip'];
        $port = $row['port'];
        $rootID = $row['rootID'];
        $shorten = $row['shorten'];
        $binarydir = $row['binarydir'];
        $gamebinary = $row['gamebinary'];
        $modfolder = $row['modfolder'];
        $protected = $row['protected'];
        $servertemplate = $row['servertemplate'];
        $ftppass = $row['dftppass'];
        $pallowed = $row['pallowed'];

        $username = ($row['newlayout'] == 'Y') ? $row['cname'] . '-' . $row['id'] : $row['cname'];

        if ($protected == 'N' and $servertemplate > 1) {
            $ftpshorten = $row['shorten'] . '-' . $servertemplate;
            $pserver = 'server/';
        } else if ($protected == 'Y' and $pallowed == 'Y') {
            $ftpshorten = $row['shorten'];
            $username = $username . '-p';
            $ftppass = $row['dpftppass'];
            $pserver = '';
        } else {
            $ftpshorten = $row['shorten'];
            $pserver = 'server/';
        }
    }

    $query = $sql->prepare("SELECT g.`protected`,g.`homeLabel`,t.`configs`,s.`id` FROM `gsswitch` g INNER JOIN `serverlist` s ON g.`serverid`=s.`id` INNER JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE g.`id`=? AND g.`userid`=? AND g.`resellerid`=? LIMIT 1");
    $query->execute(array($id,$user_id,$resellerLockupID));
    $customer = getusername($user_id);
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

        $serverID = $row['id'];
        $protected = $row['protected'];
        $homeLabel = $row['homeLabel'];
        $config_rows = explode("\r\n", $row['configs']);

        foreach ($config_rows as $configline) {

            $data_explode = explode(" ", $configline);
            $permission = (isset($data_explode[1])) ? $data_explode[1] : 'full';

            if ($data_explode[0] != '') {
                $configs[] = array('permission' => $permission, 'line' => $data_explode[0]);
                $configCheck[] = $data_explode[0];
            }
        }
    }

    $query = $sql->prepare("SELECT a.`configs`,a.`paddon` FROM `addons_installed` i INNER JOIN `addons` a ON i.`addonid`=a.`id` WHERE i.`serverid`=? AND i.`userid`=? AND i.`resellerid`=?");
    $query->execute(array($serverID,$user_id,$resellerLockupID));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        if (isset($protected) and ($protected == 'N' or $row['paddon'] == 'Y')) {

            $config_rows = explode("\r\n", $row['configs']);

            foreach ($config_rows as $configline) {

                $data_explode = explode(" ", $configline);
                $permission = (isset($data_explode[1])) ? $data_explode[1] : 'full';

                if ($data_explode[0] != '') {
                    $configs[] = array('permission' => $permission, 'line' => $data_explode[0]);
                    $configCheck[] = $data_explode[0];
                }
            }
        }
    }

    if ($ui->smallletters('type', 4, 'get')) {

        if ($ui->config('config', 'get')) {
            $postconfig = $ui->config('config', 'get');
        } else if ($ui->config('config', 'post')) {
            $postconfig = $ui->config('config', 'post');
        } else {
            $postconfig = null;
        }

        if (in_array($postconfig, $configCheck) and $ui->smallletters('type', 4, 'get') and ($ui->smallletters('type', 4, 'get') == 'easy' or $ui->smallletters('type', 4, 'get') == 'full')) {

            $explodeconfig = explode('/', $postconfig);
            $configname = $explodeconfig[(count($explodeconfig) - 1)];

            $query = $sql->prepare("SELECT `ip`,`ftpport`,`install_paths` FROM `rserverdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($rootID,$resellerLockupID));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

                $ftpport = $row['ftpport'];
                $ip = $row['ip'];

                $iniVars = @parse_ini_string($row['install_paths'], true);
                $homeDir = ($iniVars and isset($iniVars[$homeLabel]['path'])) ? (string) $iniVars[$homeLabel]['path'] : '/home';

                if (substr($homeDir, -1, 1) != '/') {
                    $homeDir .= '/';
                }

                $homeDir .= $username;
            }

            if ($gamebinary == 'srcds_run'){

                $config = $binarydir. '/' . $modfolder. '/' . $postconfig;

                if ($configname == 'server.cfg' and $gamebinary == 'srcds_run') {
                    $general_cvar = array('hostname','sv_password','sv_contact','sv_tags','motdfile','mapcyclefile','sv_downloadurl','net_maxfilesize','rcon_password','sv_rcon_minfailures','sv_rcon_maxfailures','sv_rcon_banpenalty','sv_rcon_minfailuretime','sv_pure','sv_pure_kick_clients','sv_timeout','sv_voiceenable','sv_allowdownload','sv_allowupload','sv_region','sv_friction','sv_stopspeed','sv_gravity','sv_accelerate','sv_airaccelerate','sv_wateraccelerate','sv_allow_color_correction','sv_allow_wait_command','mp_flashlight','mp_footsteps','mp_falldamage','mp_limitteams','mp_limitteams','mp_friendlyfire','mp_autokick','mp_forcecamera','mp_fadetoblack','mp_allowspectators','mp_chattime','log','sv_log_onefile','sv_logfile','sv_logbans','sv_logecho','mp_logdetail','mp_timelimit','mp_winlimit','sv_minrate','sv_maxrate','sv_minupdaterate','sv_maxupdaterate','sv_mincmdrate','sv_maxcmdrate','sv_client_cmdrate_difference','sv_client_min_interp_ratio','sv_client_max_interp_ratio','mp_fraglimit','mp_maxrounds');
                } else {
                    $general_cvar = array();
                }

            } else if ($gamebinary == 'hlds_run'){
                $config = $modfolder. '/' . $postconfig;
                $general_cvar = array();
            } else {
                $general_cvar = array();
                $config = $postconfig;
            }

            if ($shorten == 'css' and $configname == 'server.cfg') {
                $game_cvars = array('motdfile_text','sv_disablefreezecam','sv_nonemesis','sv_nomvp','sv_nostats','sv_allowminmodels','sv_hudhint_sound','sv_competitive_minspec','sv_legacy_grenade_damage','sv_enableboost','sv_enablebunnyhopping','mp_forceautoteam','mp_enableroundwaittime','mp_startmoney','mp_roundtime','mp_buytime','mp_c4timer','mp_freezetime','mp_spawnprotectiontime','mp_hostagepenalty','mp_tkpunish');
            } else if ($shorten=="dods" and $configname == 'server.cfg') {
                $game_cvars = array('mp_limit_allies_rocket','mp_limit_axis_rocket','mp_limit_axis_mg','mp_limit_axis_sniper','mp_limit_axis_assault','mp_limit_axis_support','mp_limit_axis_rifleman','mp_limit_allies_mg','mp_limit_allies_sniper','mp_limit_allies_assault','mp_limit_allies_support','mp_limit_allies_rifleman','dod_freezecam','dod_enableroundwaittime','dod_bonusroundtime','dod_bonusround');
            } else {
                $game_cvars = array();
            }

            $configfile = '';
            $cleanedconfig = '';
            $newconfig = '';
            $setarray = array();

            $ftp = new EasyWiFTP($ip, $ftpport, $username, $ftppass);

            if ($ftp->loggedIn === true) {

                if ($ui->smallletters('type', 4, 'get') == 'full' and isset($ui->post['update']) and $ui->post['update'] == 1) {

                    $configfile = stripslashes($ui->post['cleanedconfig']);

                } else if ($ui->smallletters('type', 4, 'get') == 'easy' or ($ui->smallletters('type', 4, 'get') == 'full' and !isset($ui->post['update']))) {

                    $ftp->downloadToTemp($pserver . '/' . $ftpshorten . '/' . $config);
                    $configfile = $ftp->getTempFileContent();

                }

                $lines = array();

                if (strlen($configfile) > 0) {
                    $configfile = str_replace(array("\0" , "\b" , "\r", "\Z"), '', $configfile);
                    $lines = explode("\r\n", $configfile);
                }

                if (isset($ui->post['update']) and $ui->post['update'] == 1 and isset($lines)) {

                    foreach ($lines as $singeline) {

                        $singeline = preg_replace('/\s+/',' ', $singeline);

                        if (preg_match("/\w/", substr($singeline, 0, 1))) {

                            if (preg_match("/\"/", $singeline)) {

                                $split = explode('"', $singeline);
                                $cvar = str_replace(' ', '', $split[0]);
                                $value = $split[1];

                                if ($cvar != 'exec') {

                                    if (isset($ui->post[$cvar])) {

                                        if (isset($ui->post['oldrcon']) and $cvar == 'rcon_password' and $ui->post[$cvar] != $ui->post['oldrcon'] and $configname == 'server.cfg' and in_array($anticheat, array(2, 3, 4, 5)) and ($gamebinary == 'srcds_run' or $gamebinary == 'hlds_run') and $eacallowed == 'Y') {
                                            eacchange('change',$id,$ui->post[$cvar],$resellerLockupID);
                                        }

                                        $newconfig .= $cvar . ' "' . $ui->post[$cvar] . '"' . "\r\n";

                                    } else if (isset($ui->post['oldrcon']) and $cvar == 'rcon_password' and $value != $ui->post['oldrcon'] and $configname == 'server.cfg' and in_array($anticheat, array(2, 3, 4, 5)) and ($gamebinary == 'srcds_run' or $gamebinary == 'hlds_run') and $eacallowed == 'Y') {
                                        eacchange('change', $id, $value, $resellerLockupID);
                                    } else {
                                        $newconfig .= $singeline . "\r\n";
                                    }

                                    array_push($setarray, $cvar);

                                } else {
                                    $newconfig .= $singeline . "\r\n";
                                }

                            } else {

                                $split = explode(' ', $singeline);

                                if (isset($split[0])) {

                                    $cvar = $split[0];
                                    $value = (isset($split[1])) ? $split[1] : '';

                                    if ($cvar != 'exec') {

                                        if (isset($ui->post[$cvar])) {

                                            if (isset($ui->post['oldrcon']) and $cvar == 'rcon_password' and $ui->post[$cvar] != $ui->post['oldrcon'] and $configname == 'server.cfg' and in_array($anticheat, array(2, 3, 4, 5)) and ($gamebinary == 'srcds_run' or $gamebinary == 'hlds_run') and $eacallowed == 'Y') {
                                                eacchange('change', $id, $ui->post[$cvar], $resellerLockupID);
                                            }

                                            $newconfig .= $cvar . ' "' . $ui->post[$cvar] . '"' . "\r\n";

                                        } else if (isset($ui->post['oldrcon']) and $cvar == 'rcon_password' and $value != $ui->post['oldrcon'] and $configname == 'server.cfg' and in_array($anticheat, array(2, 3, 4, 5)) and ($gamebinary == 'srcds_run' or $gamebinary == 'hlds_run') and $eacallowed == 'Y') {
                                            eacchange('change',$id,$value,$resellerLockupID);
                                        } else {
                                            $newconfig .= $singeline . "\r\n";
                                        }

                                        array_push($setarray, $cvar);

                                    } else {
                                        $newconfig .= $singeline . "\r\n";
                                    }
                                }
                            }

                        } else {
                            $newconfig .= $singeline . "\r\n";
                        }
                    }

                    if ($ui->smallletters('type', 4, 'get') == 'easy') {

                        foreach ($general_cvar as $check_cvar) {
                            if (!in_array($check_cvar, $setarray)) {
                                $newconfig .= $check_cvar . ' "' . $ui->post[$check_cvar] . '"' . "\r\n";
                            }
                        }

                        foreach ($game_cvars as $check_cvar) {
                            if (!in_array($check_cvar, $setarray)) {
                                $newconfig .= $check_cvar . ' "' . $ui->post[$check_cvar] . '"' . "\r\n";
                            }
                        }
                    }

                    $ftp->tempHandle = null;

                    if ($ui->smallletters('type', 4, 'get') == 'easy') {
                        $ftp->writeContentToTemp($newconfig);
                    } else if ($ui->smallletters('type', 4, 'get') == 'full') {
                        $ftp->writeContentToTemp($ui->post['cleanedconfig']);
                    }

                    $uploaded = false;

                    if ($ftp->uploadFileFromTemp($ftp->removeSlashes($pserver . '/' . $ftpshorten . '/'), $config, false)) {
                        $uploaded = true;
                    }

                    if ($uploaded == false and $ftp->uploadFileFromTemp($ftp->removeSlashes($homeDir . '/' . $pserver . '/' . $ftpshorten . '/'), $config, false)) {
                        $uploaded = true;
                    }

                    if ($uploaded) {

                        $template_file = 'Success: ' . $config;

                        $loguseraction = '%cfg% ' . $configname;
                        $insertlog->execute();

                    } else {
                        $template_file = 'Error writing config: ' . $config;
                    }

                } else if (isset($lines)) {

                    $linearray = array();
                    $unknownarray = array();
                    $cleanedconfig = '';

                    $lineCount = count($lines);
                    $i = 0;

                    foreach ($lines as $singeline) {

                        if (preg_match("/\w/", substr($singeline, 0, 1))) {

                            if (preg_match("/\"/", $singeline)) {

                                $split = explode('"', $singeline);
                                $cvar = str_replace(' ', '', $split[0]);
                                $value = $split[1];

                                if ($cvar != 'exec') {
                                    if (in_array($cvar, $general_cvar) or in_array($cvar, $game_cvars)) {
                                        $linearray[$cvar] = $value;
                                    } else {
                                        $unknownarray[$cvar] = $value;
                                    }
                                }

                            } else {

                                $split = explode(' ', $singeline);

                                if (isset($split[0])) {

                                    $cvar = $split[0];
                                    $value=(isset($split[1])) ? $split[1] : '';

                                    if ($cvar != 'exec') {
                                        if (in_array($cvar, $general_cvar) or in_array($cvar, $game_cvars)) {
                                            $linearray[$cvar] = $value;
                                        } else {
                                            $unknownarray[$cvar] = $value;
                                        }
                                    }

                                }
                            }
                        }

                        $i++;

                        $cleanedconfig .= ($i == $lineCount) ? $singeline : $singeline . "\r\n";
                    }

                    $array_keys = array_keys($unknownarray);

                    if ($configname == 'server.cfg' and in_array($anticheat, array(2, 3, 4, 5)) and ($gamebinary == 'srcds_run' or $gamebinary == 'hlds_run') and $eacallowed == 'Y') {
                        $oldrcon = (array_key_exists('rcon_password', $linearray)) ? $linearray['rcon_password'] : 'unset';
                    }

                    if ($ui->smallletters('type', 4, 'get') == 'easy') {
                        $template_file = 'userpanel_gserver_config_edit_easy.tpl';
                    } else if ($ui->smallletters('type', 4, 'get') == 'full') {
                        $template_file = 'userpanel_gserver_config_edit_full.tpl';
                    }
                }

            } else {
                $template_file = 'Error: FTP Access';
            }
        } else {
            $template_file = 'userpanel_404.tpl';
        }
    } else {
        $template_file = 'userpanel_gserver_config_edit.tpl';
    }

} else {

    $table = array();

    $query = $sql->prepare("SELECT AES_DECRYPT(`ftppassword`,?) AS `cftppass`,g.*,s.`servertemplate`,s.`upload`,t.`id` AS `tid`,t.`ramLimited`,t.`shorten`,t.`protected` AS `tp`,u.`cname` FROM `gsswitch` g INNER JOIN `serverlist` s ON g.`serverid`=s.`id` INNER JOIN `servertypes` t ON s.`servertype`=t.`id` INNER JOIN `userdata` u ON g.`userid`=u.`id` WHERE g.`active`='Y' AND g.`userid`=? AND g.`resellerid`=? ORDER BY g.`serverip`,g.`port`");
    $query2 = $sql->prepare("SELECT `ftpport` FROM `rserverdata` WHERE `id`=? LIMIT 1");
    $query3 = $sql->prepare("SELECT 1 FROM `servertypes` WHERE `id`=? AND `ftpAccess`='N' LIMIT 1");
    $query->execute(array($aeskey, $user_id,$resellerLockupID));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        if (!isset($_SESSION['sID']) or in_array($row['id'],$substituteAccess['gs'])) {
            $description = $row['description'];
            $rootid = $row['rootID'];
            $war = $row['war'];
            $brandname = $row['brandname'];
            $protected = $row['protected'];
            $tprotected = $row['tp'];
            $pallowed = $row['pallowed'];
            $cname = $row['cname'];
            $shorten = $row['shorten'];
            $gameserverid = $row['id'];
            $name = $row['queryName'];
            $ip = $row['serverip'];
            $port = $row['port'];
            $numplayers = $row['queryNumplayers'];
            $maxplayers = $row['queryMaxplayers'];
            $password = $row['queryPassword'];
            $stopped = $row['stopped'];
            $notified = $row['notified'];
            $cftppass = $row['cftppass'];
            $servertemplate = $row['servertemplate'];

            $address = $ip . ':' . $port;
            $map = (in_array($row['queryMap'], array(false, null, ''))) ? 'Unknown' : $row['queryMap'];
            $updatetime = ($user_language == 'de') ? ($row['queryUpdatetime'] != '') ? date('d.m.Y H:m:s',strtotime($row['queryUpdatetime'])) : $sprache->never : $row['queryUpdatetime'];
            $upload = ($row['upload'] > 1 and $row['upload'] < 4) ? true : false;
            $currentTemplate = (($protected == 'N' or $tprotected == 'N') and $servertemplate > 1) ? $row['shorten'] . '-' . $servertemplate : $row['shorten'];
            $ce = explode(',', $row['cores']);
            $coreCount = count($ce);
            $cores = array();

            if ($row['taskset'] == 'Y' and $coreCount>0) {
                foreach ($ce as $uc) {
                    $cores[] = $uc;
                }
            }

            $cores = implode(', ', $cores);
            if ($stopped == 'Y') {
                $name = 'OFFLINE';
            }

            $imgNameP = '';
            $imgAltP = '';
            $pro = '';
            $pserver = '/server/';

            if ($protected == 'N' and ($pallowed == 'Y' and $tprotected == 'Y')) {
                $imgNameP = '16_unprotected';
                $imgAltP = $sprache->off2;
                $pro = $sprache->off2;
            } else if ($protected == 'Y' and $tprotected == 'Y' and $pallowed == 'Y') {
                $imgNameP = '16_protected';
                $imgAltP = $sprache->on;
                $pserver = '/pserver/';
                $pro = $sprache->on;
            }

            if ($pa['ftpaccess'] or $pa['miniroot']) {

                if ($row['newlayout'] == 'Y') {
                    $cname = $cname . '-' . $row['id'];
                }

                $query2->execute(array($rootid));
                $ftpport = $query2->fetchColumn();
                $ftpdata = 'ftp://' . $cname . ':' . $cftppass . '@' . $ip . ':' . $ftpport . $pserver . $currentTemplate;
            } else {
                $cftppass = '';
                $ftpport = '';
                $ftpdata = '';
            }

            $nameremoved = '';
            $premoved = '';
            $imgName = '16_ok';
            $imgAlt = 'Online';

            if ($stopped == 'Y') {
                $numplayers = 0;
                $maxplayers = 0;
                $imgName = '16_bad';
                $imgAlt = 'Stopped';
            } else if ($name == 'OFFLINE' and $stopped == 'N') {
                $numplayers = 0;
                $maxplayers = 0;
                $imgName = '16_error';
                $imgAlt = 'Crashed';
            } else {
                if ($war == 'Y' and $password == 'N') {
                    $imgName = '16_error';
                    $imgAlt = 'No Password';
                    $premoved = $sprache->premoved;
                }
                if ($brandname == 'Y' and $rSA['brandname'] != null and $rSA['brandname'] != '' and strpos(strtolower($name), strtolower($rSA['brandname'])) === false) {
                    $imgName = '16_error';
                    $imgAlt = 'No Servertag';
                    $nameremoved = $sprache->nameremoved;
                }
            }

            $query3->execute(array($row['tid']));
            $ftpAllowed = ($query3->rowCount() == 0) ? true : false;

            $table[] = array(
                'id' => $gameserverid,
                'premoved' => $premoved,
                'nameremoved' => $nameremoved,
                'server' => $address,
                'name' => (strlen($description) == 0) ? $name : $description . ' ' . $name,
                'img' => $imgName,
                'alt' => $imgAlt,
                'imgp' => $imgNameP,
                'altp' => $imgAltP,
                'numplayers' => $numplayers,
                'maxplayers' => $maxplayers,
                'map' => $map,
                'cname' => $cname,
                'cftppass' => $cftppass,
                'ip' => $ip,
                'ftpport' => $ftpport,
                'port' => $port,
                'shorten' => $currentTemplate,
                'gameShorten' => $shorten,
                'ftpdata' => $ftpdata,
                'updatetime' => $updatetime,
                'stopped' => $stopped,
                'pro' => $pro,
                'upload' => $upload,
                'minram' => $row['minram'],
                'maxram' => $row['maxram'],
                'taskset' => $row['taskset'],
                'ramLimited' => $row['ramLimited'],
                'coreCount' => $coreCount,
                'cores' => $cores,
                'ftpAllowed' => $ftpAllowed
            );
        }
    }

    $template_file = 'userpanel_gserver_list.tpl';
}
