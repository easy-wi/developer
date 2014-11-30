<?php

/**
 * File: lendserver.php.
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

if ((!isset($admin_id) or $main != 1) or (isset($admin_id) and (!isset($pa) or (!$pa['lendserver'] and !$pa['lendserverSettings'])))) {
    redirect('admin.php');
    die;
}
include(EASYWIDIR . '/stuff/keyphrasefile.php');
include(EASYWIDIR . '/stuff/methods/functions_gs.php');
include(EASYWIDIR . '/stuff/methods/functions_ssh_exec.php');
include(EASYWIDIR . '/stuff/methods/class_ts3.php');
include(EASYWIDIR . '/stuff/methods/class_app.php');

$sprache = getlanguagefile('lendserver', $user_language, $reseller_id);
$gssprache = getlanguagefile('gserver', $user_language, $reseller_id);
$loguserid = $admin_id;
$logusername = getusername($admin_id);
$logusertype = 'admin';

if ($reseller_id == 0) {
    $logreseller = 0;
    $logsubuser = 0;
} else {
    $logsubuser = (isset($_SESSION['oldid'])) ? $_SESSION['oldid'] : 0;
    $logreseller = 0;
}

if ($ui->w('action', 4, 'post') and !token(true)) {
    $template_file = $spracheResponse->token;
} else if ($ui->st('d', 'get')=="se" and $pa['lendserverSettings']) {

    if ($ui->w('activeGS', 1, 'post') or $ui->w('activeVS', 1, 'post')) {

        $query = $sql->prepare("UPDATE `lendsettings` SET `activeGS`=?,`activeVS`=?,`mintime`=?,`maxtime`=?,`timesteps`=?,`minplayer`=?,`maxplayer`=?,`playersteps`=?,`mintimeRegistered`=?,`maxtimeRegistered`=?,`timestepsRegistered`=?,`minplayerRegistered`=?,`maxplayerRegistered`=?,`playerstepsRegistered`=?,`vomintime`=?,`vomaxtime`=?,`votimesteps`=?,`vominplayer`=?,`vomaxplayer`=?,`voplayersteps`=?,`vomintimeRegistered`=?,`vomaxtimeRegistered`=?,`votimestepsRegistered`=?,`vominplayerRegistered`=?,`vomaxplayerRegistered`=?,`voplayerstepsRegistered`=?,`shutdownempty`=?,`shutdownemptytime`=?,`ftpupload`=?,`ftpuploadpath`=AES_ENCRYPT(?,?),`lendaccess`=? WHERE `resellerid`=? LIMIT 1");
        $query->execute(array($ui->w('activeGS', 1, 'post'), $ui->w('activeVS', 1, 'post'), $ui->id('mintime',3, 'post'), $ui->id('maxtime',4, 'post'), $ui->id('timesteps',3, 'post'), $ui->id('minplayer',3, 'post'), $maxplayer = $ui->id('maxplayer',3, 'post'), $ui->id('playersteps',3, 'post'), $ui->id('mintimeRegistered',3, 'post'), $ui->id('maxtimeRegistered',4, 'post'), $ui->id('timestepsRegistered',3, 'post'), $ui->id('minplayerRegistered',3, 'post'), $ui->id('maxplayerRegistered',3, 'post'), $ui->id('playerstepsRegistered',3, 'post'), $vomintime = $ui->id('vomintime',3, 'post'), $ui->id('vomaxtime',4, 'post'), $ui->id('votimesteps',3, 'post'), $ui->id('vominplayer',3, 'post'), $ui->id('vomaxplayer',3, 'post'), $ui->id('voplayersteps',3, 'post'), $ui->id('vomintimeRegistered',3, 'post'), $ui->id('vomaxtimeRegistered',4, 'post'), $ui->id('votimestepsRegistered',3, 'post'), $ui->id('vominplayerRegistered',3, 'post'), $ui->id('vomaxplayerRegistered',3, 'post'), $ui->id('voplayerstepsRegistered',3, 'post'), $ui->active('shutdownempty', 'post'), $ui->id('shutdownemptytime',4, 'post'), $ui->w('ftpupload',1, 'post'), $ui->url('ftpuploadpath', 'post'), $aeskey, $ui->id('lendaccess',1, 'post'), $reseller_id));

        $template_file = ($query->rowCount() > 0) ? $spracheResponse->table_add : $spracheResponse->error_table;;

    } else {
        $query = $sql->prepare("SELECT *,AES_DECRYPT(`ftpuploadpath`,?) AS `decyptedftpuploadpath` FROM `lendsettings` WHERE `resellerid`=? LIMIT 1");
        $query->execute(array($aeskey, $reseller_id));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $activeGS = $row['activeGS'];
            $activeVS = $row['activeVS'];
            $mintime = (int) $row['mintime'];
            $maxtime = (int) $row['maxtime'];
            $timesteps = (int) $row['timesteps'];
            $minplayer = (int) $row['minplayer'];
            $maxplayer = (int) $row['maxplayer'];
            $playersteps = (int) $row['playersteps'];
            $mintimeRegistered = (int) $row['mintimeRegistered'];
            $maxtimeRegistered = (int) $row['maxtimeRegistered'];
            $timestepsRegistered = (int) $row['timestepsRegistered'];
            $minplayerRegistered = (int) $row['minplayerRegistered'];
            $maxplayerRegistered = (int) $row['maxplayerRegistered'];
            $playerstepsRegistered = (int) $row['playerstepsRegistered'];
            $vomintime = (int) $row['vomintime'];
            $vomaxtime = (int) $row['vomaxtime'];
            $votimesteps = (int) $row['votimesteps'];
            $vominplayer = (int) $row['vominplayer'];
            $vomaxplayer = (int) $row['vomaxplayer'];
            $voplayersteps = (int) $row['voplayersteps'];
            $vomintimeRegistered = (int) $row['vomintimeRegistered'];
            $vomaxtimeRegistered = (int) $row['vomaxtimeRegistered'];
            $votimestepsRegistered = (int) $row['votimestepsRegistered'];
            $vominplayerRegistered = (int) $row['vominplayerRegistered'];
            $vomaxplayerRegistered = (int) $row['vomaxplayerRegistered'];
            $voplayerstepsRegistered = (int) $row['voplayerstepsRegistered'];
            $shutdownempty = $row['shutdownempty'];
            $shutdownemptytime = $row['shutdownemptytime'];
            $ftpupload = $row['ftpupload'];
            $ftpuploadpath = $row['decyptedftpuploadpath'];
            $lendaccess = $row['lendaccess'];
        }

        $template_file = 'admin_lendserver_settings.tpl';
    }

} else if ($ui->st('d', 'get') == 'st' and $pa['lendserver']) {

    $statistic = array();
    $stats = '';

    $query = $sql->prepare("SELECT * FROM `lendstats` WHERE `resellerID`=?");
    $query->execute(array($reseller_id));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

        $servertype = $row['servertype'];
        $lendtime = $row['lendtime'];
        $shorten = $row['shorten'];
        $slots = $row['slots'];

        if (isset($statistic[$row['lenddate']][$shorten])) {
            $statistic[$row['lenddate']][$shorten]++;
        } else {
            $statistic[$row['lenddate']][$shorten] = 1;
        }
    }
    foreach ($statistic as $key=>$value) {
        foreach ($value as $key2=>$value2) {
            $stats .="$key: $value2 ($key2)</br>";
        }
    }

    $template_file = $stats;

} else if ($pa['lendserver']) {

    if ($ui->id('id', 19, 'post')) {

        $query = $sql->prepare("SELECT `serverid`,`servertype` FROM `lendedserver` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($ui->id('id',19, 'post'), $reseller_id));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['serverid'];
            $servertype = $row['servertype'];
        }

        if (isset($servertype) and $servertype == 'g') {

            $query = $sql->prepare("SELECT s.`switchID`,g.`rootID` FROM `serverlist` s INNER JOIN `gsswitch` g ON s.`switchID`=g.`id` WHERE s.`id`=? AND s.`resellerid`=? LIMIT 1");
            $query->execute(array($id, $reseller_id));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $appServer = new AppServer($row['rootID']);
                $appServer->getAppServerDetails($row['switchID']);
                $appServer->stopApp();
                $appServer->execute();
            }

        } else if (isset($servertype) and $servertype == 'v') {

            $query = $sql->prepare("SELECT v.`localserverid`,m.`ssh2ip`,m.`rootid`,m.`addedby`,m.`queryport`,AES_DECRYPT(m.`querypassword`,?) AS `decryptedquerypassword` FROM `voice_server` v LEFT JOIN `voice_masterserver` m ON v.`masterserver`=m.`id` WHERE v.`id`=? AND v.`resellerid`=? LIMIT 1");
            $query->execute(array($aeskey, $id, $reseller_id));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

                $queryport = $row['queryport'];
                $querypassword = $row['decryptedquerypassword'];
                $addedby = $row['addedby'];
                $localserverid = $row['localserverid'];

                if ($addedby == 2) {
                    $queryip = $row['ssh2ip'];
                } else if ($addedby == 1) {
                    $query2 = $sql->prepare("SELECT `ip`,`altips` FROM `rserverdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                    $query2->execute(array($row['rootid'], $reseller_id));
                    $queryip = $query2->fetchColumn();
                }

                $connection = new TS3($queryip, $queryport,'serveradmin', $querypassword);
                $errorcode = $connection->errorcode;
                if (strpos($errorcode,'error id=0') !== false) {
                    $connection->StopServer($localserverid);
                }

                $connection->CloseConnection();
            }
        }
        $query = $sql->prepare("DELETE FROM `lendedserver` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($ui->id('id',19, 'post'), $reseller_id));

        $template_file = ($query->rowCount() > 0) ? $spracheResponse->table_del : $spracheResponse->error_table;

    } else {

        $htmlExtraInformation['css'][] = '<link href="css/adminlte/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css">';
        $htmlExtraInformation['js'][] = '<script src="js/adminlte/plugins/datatables/jquery.datatables.js" type="text/javascript"></script>';
        $htmlExtraInformation['js'][] = '<script src="js/adminlte/plugins/datatables/datatables.bootstrap.js" type="text/javascript"></script>';

        $lendGameServers = array();
        $lendVoiceServers = array();
        $shutDownEmpty = 'Y';
        $shutDownEmptyTime = 5;

        $query = $sql->prepare("SELECT `shutdownempty`,`shutdownemptytime` FROM `lendsettings` WHERE `resellerid`=? LIMIT 1");
        $query->execute(array($reseller_id));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $shutDownEmpty = $row['shutdownempty'];
            $shutDownEmptyTime = $row['shutdownemptytime'];
        }

        $deleteQuery = $sql->prepare("DELETE FROM `lendedserver` WHERE `id`=? LIMIT 1");

        $query = $sql->prepare("SELECT `id`,`queryMap`,`queryNumplayers`,`queryName`,`serverip`,`port`,`slots`,`serverid`,`rootID` FROM `gsswitch` WHERE `lendserver`='Y' AND `active`='Y' AND `resellerid`=0");
        $query2 = $sql->prepare("SELECT s.`id`,t.`shorten`,t.`description` FROM `serverlist` s INNER JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE s.`switchID`=? AND s.`resellerid`=0");
        $query3 = $sql->prepare("SELECT `id`,`slots`,`started`,`lendtime`,`password`,`rcon`,CURRENT_TIMESTAMP AS `now` FROM `lendedserver` WHERE `serverid`=? AND `servertype`='g' LIMIT 1");

        $query->execute(array($reseller_id));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            $installedShorten = array();
            $time = 0;
            $runningGame = '';
            $slots = $row['slots'];
            $lendID = null;
            $password = null;
            $rcon = null;

            $query2->execute(array($row['id']));
            while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {
                $installedShorten[$row2['shorten']] = $row2['description'];

                if ($row2['id'] == $row['serverid']) {
                    $runningGame = $row2['shorten'];
                }
            }

            $query3->execute(array($row['serverid']));
            while ($row3 = $query3->fetch(PDO::FETCH_ASSOC)) {

                $lendID = $row3['id'];
                $password = $row3['password'];
                $rcon = $row3['rcon'];

                $slots = $row3['slots'];
                $timeleft = round($row3['lendtime'] - (strtotime($row3['now']) - strtotime($row3['started'])) / 60);
                $time = ($timeleft <= 0) ? 0 : $timeleft . '/'. $row3['lendtime'];

                if ($time == 0 or ($shutDownEmpty == 'Y' and ($row3['lendtime'] - $timeleft) > $shutDownEmptyTime and $row['queryNumplayers'] < 1)) {

                    $appServer = new AppServer($row['rootID']);
                    $appServer->getAppServerDetails($row['id']);
                    $appServer->stopApp();
                    $appServer->execute();

                    $deleteQuery->execute(array($row3['id']));

                    $lendID = null;
                    $time = 0;
                }

                if (!isset($nextfree) or $nextfree > $timeleft) {
                    $nextfree = $timeleft;
                }
            }

            $lendGameServers[] = array('id' => $lendID, 'password' => $password, 'rcon' => $rcon, 'ip' => $row['serverip'], 'port' => (int) $row['port'], 'queryName' => htmlentities($row['queryName'], ENT_QUOTES, 'UTF-8'), 'queryMap' => htmlentities($row['queryMap'], ENT_QUOTES, 'UTF-8'), 'runningGame' => $runningGame, 'games' => $installedShorten, 'slots' => (int) $slots,'usedslots' => (int) $row['queryNumplayers'], 'timeleft' => $time);
        }

        if (!isset($nextfree)) {
            $nextfree = 0;
        }

        $query = $sql->prepare("SELECT v.`id`,v.`ip`,v.`port`,v.`queryName`,v.`dns`,v.`usedslots`,v.`slots` AS `availableSlots`,l.`password`,l.`slots`,l.`started`,l.`lendtime`,CURRENT_TIMESTAMP AS `now`,l.`id` AS `lend_id` FROM `voice_server` v LEFT JOIN `lendedserver` l ON v.`id`=l.`serverid` AND l.`servertype`='v' WHERE v.`lendserver`='Y' AND v.`active`='Y' AND v.`resellerid`=0");
        $query2 = $sql->prepare("SELECT v.`localserverid`,m.`ssh2ip`,m.`rootid`,m.`addedby`,m.`queryport`,AES_DECRYPT(m.`querypassword`,?) AS `decryptedquerypassword` FROM `voice_server` v LEFT JOIN `voice_masterserver` m ON v.`masterserver`=m.`id` WHERE v.`id`=? AND v.`resellerid`=? LIMIT 1");
        $query->execute(array($reseller_id));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            $time = 0;
            $lendID = null;
            $slots = $row['availableSlots'];

            if ($row['slots'] != null) {

                $lendID = $row['lend_id'];

                $timeleft = round($row['lendtime'] - (strtotime($row['now']) - strtotime($row['started'])) / 60);
                $slots = $row['slots'];

                $time = ($timeleft <= 0) ? 0 : $timeleft . '/'. $row['lendtime'];

                if ($time == 0 or ($shutDownEmpty == 'Y' and ($row['lendtime'] - $timeleft) > $shutDownEmptyTime and $row['usedslots'] < 1)) {

                    $query2->execute(array($aeskey, $row['id'], $reseller_id));
                    while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {

                        $queryport = $row2['queryport'];
                        $querypassword = $row2['decryptedquerypassword'];
                        $addedby = $row2['addedby'];
                        $localserverid = $row2['localserverid'];

                        if ($addedby == 2) {
                            $queryip = $row2['ssh2ip'];
                        } else if ($addedby == 1) {
                            $query4->execute(array($row2['rootid'], $reseller_id));
                            $queryip = $query4->fetchColumn();
                        }

                        $connection = new TS3($queryip, $queryport,'serveradmin', $querypassword);
                        $errorcode = $connection->errorcode;

                        if (strpos($errorcode,'error id=0') !== false) {
                            $connection->StopServer($localserverid);
                        }

                        $connection->CloseConnection();

                        $deleteQuery->execute(array($row['lend_id']));

                        $time = 0;
                        $lendID = null;
                    }

                    if (!isset($vonextfree) or $vonextfree > $timeleft) {
                        $vonextfree = $timeleft;
                    }
                }
            }

            $lendVoiceServers[] = array('id' => $lendID, 'password' => $row['password'], 'ip' => $row['ip'], 'port' => (int) $row['port'], 'queryName' => htmlentities($row['queryName'], ENT_QUOTES, 'UTF-8'), 'connect' => $row['dns'], 'slots' => (int) $slots, 'usedslots' => (int) $row['usedslots'], 'timeleft' => $time);
        }

        if (!isset($vonextfree)) {
            $vonextfree = 0;
        }

        $query = $sql->prepare("SELECT ((UNIX_TIMESTAMP(`lastcheck`)-UNIX_TIMESTAMP(`oldcheck`))/60)-((UNIX_TIMESTAMP()-UNIX_TIMESTAMP(`lastcheck`))/60) AS `nextRunInMinutes` FROM `lendsettings` WHERE `resellerid`=? LIMIT 1");
        $query->execute(array($reseller_id));
        $nextcheck = $query->fetchColumn();

        $nextcheck = ($nextcheck > 0) ? ceil($nextcheck) : ceil($nextcheck) * -1;

        $template_file = 'admin_lendserver_list.tpl';
    }
}