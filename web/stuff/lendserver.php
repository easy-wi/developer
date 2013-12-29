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
include(EASYWIDIR . '/stuff/ssh_exec.php');
include(EASYWIDIR . '/stuff/class_ts3.php');

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
        foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
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
    foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {

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

    if ($ui->id('id',19, 'post')) {

        $query = $sql->prepare("SELECT `serverid`,`servertype` FROM `lendedserver` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($ui->id('id',19, 'post'), $reseller_id));
        foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
            $id = $row['serverid'];
            $servertype = $row['servertype'];
        }

        if (isset($servertype) and $servertype == 'g') {

            $query = $sql->prepare("SELECT s.`switchID`,g.`rootID` FROM `serverlist` s INNER JOIN `gsswitch` g ON s.`switchID`=g.`id` WHERE s.`id`=? AND s.`resellerid`=? LIMIT 1");
            $query->execute(array($id, $reseller_id));
            foreach($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $cmds = gsrestart($row['switchID'], 'so', $aeskey, $reseller_id);
                ssh2_execute('gs', $row['rootID'], $cmds);
            }

        } else if (isset($servertype) and $servertype == 'v') {

            $query = $sql->prepare("SELECT v.`localserverid`,m.`ssh2ip`,m.`rootid`,m.`addedby`,m.`queryport`,AES_DECRYPT(m.`querypassword`,?) AS `decryptedquerypassword` FROM `voice_server` v LEFT JOIN `voice_masterserver` m ON v.`masterserver`=m.`id` WHERE v.`id`=? AND v.`resellerid`=? LIMIT 1");
            $query->execute(array($aeskey, $id, $reseller_id));
            foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {

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

        $gscount = 0;
        $gscounts = array();
        $gsused = array();
        $table = array();

        $query = $sql->prepare("SELECT `id` FROM `gsswitch` WHERE `active`='Y' AND `lendserver`='Y' AND `resellerid`=?");
        $query2 = $sql->prepare("SELECT t.`shorten` FROM `serverlist` s LEFT JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE s.`switchID`=? AND s.`resellerid`=?");
        $query->execute(array($reseller_id));
        foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {

            $query2->execute(array($row['id'], $reseller_id));
            foreach ($query2->fetchall(PDO::FETCH_ASSOC) as $row2) {
                if (isset($gscounts[$row2['shorten']])) {
                    $gscounts[$row2['shorten']]++;
                } else {
                    $gscounts[$row2['shorten']] = 1;
                    $gsused[$row2['shorten']] = 0;
                }
            }

            $gscount++;

        }

        $query = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `voice_server` WHERE `active`='Y' AND `lendserver`='Y' AND `active`='Y' AND `resellerid`=?");
        $query->execute(array($reseller_id));
        $vocount = $query->fetchColumn();
        $voTotalCount = $vocount;

        $query = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `lendedserver` WHERE `servertype`='v' AND `resellerid`=?");
        $query->execute(array($reseller_id));
        $voused = $query->fetchColumn();

        $query = $sql->prepare("SELECT * FROM `lendedserver` WHERE `resellerid`=? ORDER BY `servertype` DESC");
        $query2 = $sql->prepare("SELECT s.`switchID`,g.`rootID` FROM `serverlist` s INNER JOIN `gsswitch` g ON s.`switchID`=g.`id` WHERE s.`id`=? AND s.`resellerid`=? LIMIT 1");
        $query3 = $sql->prepare("SELECT v.`localserverid`,m.`ssh2ip`,m.`rootid`,m.`addedby`,m.`queryport`,AES_DECRYPT(m.`querypassword`,?) AS `decryptedquerypassword` FROM `voice_server` v LEFT JOIN `voice_masterserver` m ON v.`masterserver`=m.`id` WHERE v.`id`=? AND v.`resellerid`=? LIMIT 1");
        $query4 = $sql->prepare("SELECT `ip`,`altips` FROM `rserverdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query5 = $sql->prepare("DELETE FROM `lendedserver` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query6 = $sql->prepare("SELECT g.`id`,g.`serverip`,g.`port`,t.`shorten` FROM `gsswitch` g LEFT JOIN `serverlist` s ON g.`id`=s.`switchID` LEFT JOIN `servertypes` t ON s.`id`=? AND s.`servertype`=t.`id` WHERE s.`resellerid`=? AND t.`shorten` IS NOT NULL LIMIT 1");
        $query7 = $sql->prepare("SELECT t.`shorten` FROM `serverlist` s LEFT JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE s.`switchID`=? AND s.`resellerid`=?");
        $query8 = $sql->prepare("SELECT v.`ip`,v.`port`,v.`dns`,m.`type`,m.`usedns` FROM `voice_server` v LEFT JOIN `voice_masterserver` m ON v.`masterserver`=m.`id` WHERE v.`id`=? AND v.`resellerid`=? LIMIT 1");

        $query->execute(array($reseller_id));
        foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {

            $id = $row['id'];
            $servertype = $row['servertype'];
            $serverid = $row['serverid'];
            $lendtime = $row['lendtime'];
            $timeleft = round($lendtime - (strtotime('now') - strtotime($row['started'])) / 60);

            if ($servertype == 'g' and (!isset($nextfree) or $timeleft < $nextfree)) {
                $nextfree = $timeleft;
            } else if ($servertype == 'v' and (!isset($vonextfree) or $timeleft < $vonextfree)) {
                $vonextfree = $timeleft;
            }

            if ($timeleft <= 0) {

                if ($servertype == 'g') {
                    $query2->execute(array($serverid, $reseller_id));
                    foreach($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                        $cmds = gsrestart($row2['switchID'], 'so', $aeskey, $reseller_id);
                        ssh2_execute('gs', $row['rootID'], $cmds);
                    }

                } else if ($servertype == 'v') {

                    $query3->execute(array($aeskey, $serverid, $reseller_id));
                    foreach ($query3->fetchall(PDO::FETCH_ASSOC) as $row2) {

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
                    }
                }
                $query5->execute(array($id, $reseller_id));

            } else {

                $server = '';
                $shorten = '';
                $rcon = $row['rcon'];
                $password = $row['password'];
                $slots = $row['slots'];
                $lenderip = $row['lenderip'];

                if ($servertype == 'g') {

                    $query6->execute(array($serverid, $reseller_id));
                    foreach ($query6->fetchall(PDO::FETCH_ASSOC) as $row2) {

                        $server = $row2['serverip'] . ':' . $row2['port'];
                        $shorten = $row2['shorten'];

                        $query7->execute(array($row2['id'], $reseller_id));
                        foreach ($query7->fetchall(PDO::FETCH_ASSOC) as $row3) {
                            $shortenExists = $row3['shorten'];
                            $gsused[$shortenExists]++;
                        }
                    }

                    $gscount--;

                } else if ($servertype == 'v') {

                    $query8->execute(array($serverid, $reseller_id));
                    foreach ($query8->fetchall(PDO::FETCH_ASSOC) as $row2) {
                        $server = $row2['ip'] . ':' . $row2['port'];
                        $shorten = $row2['type'];
                    }

                    $vocount--;

                }
                $table[] = array('id' => $id,'servertype' => $servertype, 'server' => $server,'shorten' => $shorten,'password' => $password,'rcon' => $rcon,'slots' => $slots,'lenderip' => $lenderip,'lendtime' => $lendtime,'timeleft' => $timeleft);
            }
        }

        if (!isset($nextfree) or (isset($nextfree) and $gscount>0)) {
            $nextfree = 0;
        }
        if (!isset($vonextfree) or (isset($vonextfree) and $vocount>0)) {
            $vonextfree = 0;
        }

        $query = $sql->prepare("SELECT ((UNIX_TIMESTAMP(`lastcheck`)-UNIX_TIMESTAMP(`oldcheck`))/60)-((UNIX_TIMESTAMP()-UNIX_TIMESTAMP(`lastcheck`))/60) AS `nextRunInMinutes` FROM `lendsettings` WHERE `resellerid`=? LIMIT 1");
        $query->execute(array($reseller_id));
        $nextcheck = $query->fetchColumn();

        $nextcheck = ($nextcheck > 0) ? ceil($nextcheck) : ceil($nextcheck) * -1;
        $used[] = 'Teamspeak 3: '.$voused. '/' . $voTotalCount;

        foreach ($gscounts as $key=>$value) {
            $used[] = $key. ': ' .$gsused[$key]. '/' . $value;
        }

        $template_file = 'admin_lendserver_list.tpl';
    }
}