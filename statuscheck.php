<?php

/**
 * File: statuscheck.php.
 * Author: Ulrich Block
 * Date: 03.10.12
 * Time: 17:09
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

if (isset($_SERVER['REMOTE_ADDR'])) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $timelimit = (isset($_GET['timeout']) and is_numeric($_GET['timeout'])) ? $_GET['timeout'] : ini_get('max_execution_time') - 10;
} else {
    $timelimit = 600;
}

$args = array();
$checkTypesOfServer = array('gs', 'vs', 'vh', 'my', 'st');

if (isset($argv)) {

    foreach ($argv as $a) {
        if (in_array($a, $checkTypesOfServer)) {
            $checkTypeOfServer = $a;
        } else if (is_numeric($a)) {
            $sleep = $a;
        } else {

            $e = explode(':', $a);

            if (isset($e[1])) {
                $args[$e[0]] = $e[1];
            }
        }
    }
}

if (isset($_GET['checkTypeOfServer']) and in_array($_GET['checkTypeOfServer'], $checkTypesOfServer)) {
    $checkTypeOfServer = $_GET['checkTypeOfServer'];
}

if (isset($_GET['sleep']) and is_numeric($_GET['sleep'])) {
    $sleep = intval($_GET['sleep']);
}

if (isset($_GET['tsDebug']) and ($_GET['tsDebug'] == 1 or $_GET['tsDebug'] == 0)) {
    $args['tsDebug'] = intval($_GET['tsDebug']);
}

if (isset($_GET['coolDown']) and is_numeric($_GET['coolDown'])) {
    $args['coolDown'] = intval($_GET['coolDown']);
}

define('EASYWIDIR', dirname(__FILE__));

include(EASYWIDIR . '/stuff/methods/vorlage.php');
include(EASYWIDIR . '/stuff/methods/functions.php');
include(EASYWIDIR . '/stuff/methods/class_validator.php');
include(EASYWIDIR . '/stuff/settings.php');
include(EASYWIDIR . '/stuff/methods/functions_gs.php');
include(EASYWIDIR . '/stuff/methods/functions_ssh_exec.php');
include(EASYWIDIR . '/stuff/methods/class_app.php');
include(EASYWIDIR . '/stuff/methods/class_ts3.php');
include(EASYWIDIR . '/third_party/gameq/GameQ/Autoloader.php');
include(EASYWIDIR . '/third_party/gameq_v2/GameQ.php');
include(EASYWIDIR . '/stuff/methods/class_mysql.php');
include(EASYWIDIR . '/stuff/methods/class_httpd.php');
include(EASYWIDIR . '/stuff/keyphrasefile.php');

set_time_limit($timelimit);

$logreseller = 0;
$logsubuser = 0;
$loguserip = '127.0.0.1';
$userHostname = 'localhost';
$logusername = 'Cronjob';
$logusertype = 'cron';

$query = $sql->prepare("UPDATE `settings` SET `lastCronStatus`=UNIX_TIMESTAMP()");
$query->execute();

if (!isset($ip) or $ui->escaped('SERVER_ADDR', 'server') == $ip or in_array($ip, ipstoarray($rSA['cronjob_ips']))) {

    if (isset($ip)) {
        echo '<pre>';
    }

    if (isset($checkTypeOfServer)) {
        if ($checkTypeOfServer == 'gs') {
            print 'Checking Gameserver' . "\r\n";
        } else if ($checkTypeOfServer == 'vh') {
            print 'Checking Web Quotas' . "\r\n";
        } else if ($checkTypeOfServer == 'vs') {
            print 'Checking Voiceserver' . "\r\n";
        } else if ($checkTypeOfServer == 'st') {
            print 'Checking Usage Statistics' . "\r\n";
        } else {
            print 'Getting MySQL DB sizes' . "\r\n";
        }
    } else {
        $checkTypeOfServer = 'all';
        print 'Checking Gameserver, Voiceserver MySQL DB sizes and Web Quotas' . "\r\n";
    }

    $ssprache = getlanguagefile('settings','uk',0);
    $vosprache = getlanguagefile('voice','uk',0);
    $sprache = getlanguagefile('gserver','uk',0);

    // lendmodul active ?
    $query = $sql->prepare("SELECT `active` FROM `modules` WHERE `id`=5 LIMIT 1");
    $query->execute();
    $lendActive = $query->fetchColumn();
    $lendActive = (active_check($lendActive)) ? $lendActive : 'Y';

    # Pick up Reseller and Lend Settings
    $resellersettings = array();
    $query = $sql->prepare("SELECT `brandname`,`noservertag`,`nopassword`,`tohighslots`,`down_checks`,`resellerid` FROM `settings`");
    $query2 = $sql->prepare("SELECT `shutdownempty`,`shutdownemptytime`,`lastcheck`,`oldcheck` FROM `lendsettings` WHERE `resellerid`=? LIMIT 1");
    $query->execute();
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

        unset($shutdownempty);

        $resellerid = $row['resellerid'];

        $query2->execute(array($resellerid));
        while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {
            $shutdownempty = $row2['shutdownempty'];
            $shutdownemptytime = $row2['shutdownemptytime'];
            $firstcheck = '00-00-' . round(2 * (strtotime($row2['lastcheck']) - strtotime($row2['oldcheck'])) / 60);
            $firstchecktime = date('d-G-i');
        }

        if (isset($shutdownempty)) {
            $resellersettings[$resellerid] = array('shutdownempty' => $shutdownempty,'shutdownemptytime' => $shutdownemptytime,'firstchecktime' => $firstchecktime,'firstcheck' => $firstcheck,'brandname' => $row['brandname'], 'noservertag' => $row['noservertag'], 'nopassword' => $row['nopassword'], 'tohighslots' => $row['tohighslots'], 'down_checks' => $row['down_checks']);
        }
    }

    $query = $sql->prepare("UPDATE `lendsettings` SET `oldcheck`=`lastcheck`,`lastcheck`=NOW()");
    $query->execute();

    # Game Server
    if ($checkTypeOfServer == 'all' or $checkTypeOfServer == 'gs') {

        $startStopList = array();

        // Lend server stopping.
        // We want only one socket per root server. Collect the to be stopped lendservers in an array and sort by root ID
        $rtmp = array();

        $query = $sql->prepare("SELECT `id`,`serverid`,`started`,`lendtime`,`resellerid` FROM `lendedserver` WHERE `servertype`='g'");
        $query2 = $sql->prepare("SELECT g.`rootID`,g.`id`,g.`userid`,g.`serverip`,g.`port` FROM `serverlist` s INNER JOIN `gsswitch` g ON s.`switchID`=g.`id` WHERE s.`id`=? LIMIT 1");
        $query3 = $sql->prepare("DELETE FROM `lendedserver` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute();
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];
            $lendtime = $row['lendtime'];
            $serverid = $row['serverid'];
            $resellerid = $row['resellerid'];
            $timeleft = round($row['lendtime'] - (strtotime('now') - strtotime($row['started'])) / 60);

            if ($timeleft <= 0) {
                $query3->execute(array($id, $resellerid));

                $query2->execute(array($row['serverid']));
                while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {

                    $loguserid = $row2['userid'];
                    $reseller_id = $row['resellerid'];
                    $loguseraction = "%stop% %gserver% {$row2['serverip']}:{$row2['port']} (Lend stop)";
                    $insertlog->execute();

                    $startStopList[$row['resellerid']][$row2['rootID']]['stop'][] = $id;
                }

                print "Time is up, stopping lendserver: $id\r\n";

            } else {
                print "Lendserver $serverid has $timeleft minutes left\r\n";
            }
        }

        // Define basic variables for GS status checks
        $gameQv3Protocols = getGameQ3List();

        $other = array();
        $rootID = 0;
        $iV2 = 1;
        $totalV2Count = 0;
        $serverBatchV2Array = array();
        $allServersV2Array = array();
        $iV3 = 1;
        $totalV3Count = 0;
        $serverBatchV3Array = array();
        $allServersV3Array = array();

        $query2 = $sql->prepare("SELECT g.`id`,g.`serverid`,g.`serverip`,g.`port`,g.`port2`,g.`port3`,g.`port4`,g.`port5`,t.`gameq`,t.`shorten`,t.`useQueryPort` FROM `gsswitch` g INNER JOIN `serverlist` s ON g.`serverid`=s.`id` INNER JOIN `servertypes` t ON s.`servertype`=t.`id` INNER JOIN `userdata` u ON u.`id`=g.`userid` WHERE g.`rootID`=? AND g.`stopped`='N' AND g.`active`='Y' AND u.`active`='Y'");
        $query = $sql->prepare("SELECT DISTINCT(`rootID`) AS `root_id` FROM `gsswitch` WHERE `active`='Y'");
        $query->execute();
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            // Avoid that servers belonging to different roots are checked together
            // If combined more false positives are possible
            if ($rootID != 0) {
                $allServersV3Array[] = $serverBatchV3Array;
                $serverBatchV3Array = array();
                $allServersV2Array[] = $serverBatchV2Array;
                $serverBatchV2Array = array();
                $iV2 = 1;
                $iV3 = 1;
            }

            // Get the list of servers which are active and are not stopped. The array to be created will support batch mode.
            $query2->execute(array($row['root_id']));
            while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {

                // without the gameq value we cannot query. So this results need to be sorted out.
                if (!in_array($row2['gameq'], array('', null, false))) {

                    if ($row2['useQueryPort'] == 5) {
                        $queryPort = $row2['port5'];
                    } else if ($row2['useQueryPort'] == 4) {
                        $queryPort = $row2['port4'];
                    } else if ($row2['useQueryPort'] == 3) {
                        $queryPort = $row2['port3'];
                    } else if ($row2['useQueryPort'] == 2) {
                        $queryPort = $row2['port2'];
                    } else {
                        $queryPort = $row2['port'];
                    }

                    if (isset($gameQv3Protocols[$row2['gameq']])) {

                        print "GameQ v3 support for {$row2['shorten']} {$row2['serverip']}:{$row2['port']}\r\n";

                        $serverBatchV3Array[] = array('id' => $row2['id'], 'type' => $row2['gameq'], 'host' => $row2['serverip'] . ':' . $row2['port'], 'options' => array('query_port' => $queryPort));
                        $iV3++;

                        if ($iV3 == 5) {
                            $allServersV3Array[] = $serverBatchV3Array;
                            $serverBatchV3Array = array();
                            $iV3 = 1;
                        }

                        $totalV3Count++;

                    } else {

                        print "GameQ v2 support for {$row2['shorten']} {$row2['serverip']}:{$row2['port']}\r\n";

                        $checkAtIPPort = $row2['serverip'] . ':';

                        if ($row2['useQueryPort'] == 5) {
                            $checkAtIPPort .= $row2['port5'];
                        } else if ($row2['useQueryPort'] == 4) {
                            $checkAtIPPort .= $row2['port4'];
                        } else if ($row2['useQueryPort'] == 3) {
                            $checkAtIPPort .= $row2['port3'];
                        } else if ($row2['useQueryPort'] == 2) {
                            $checkAtIPPort .= $row2['port2'];
                        } else {
                            $checkAtIPPort .= $row2['port'];
                        }

                        $serverBatchV2Array[] = array('id' => $row2['id'], 'type' => $row2['gameq'], 'host' => $checkAtIPPort);

                        $iV2++;

                        if ($iV2 == 5) {
                            $allServersV2Array[] = $serverBatchV2Array;
                            $serverBatchV2Array = array();
                            $iV2 = 1;
                        }

                        $totalV2Count++;
                    }

                } else {
                    print "No GameQ found for {$row2['shorten']}\r\n";
                }
            }

            // Set and used in order to prevent that GS from different roots are checked together
            $rootID = $row['root_id'];
        }

        $allServersV2Array[] = $serverBatchV2Array;
        $allServersV3Array[] = $serverBatchV3Array;

        $serverReplies = array();

        print "Checking $totalV3Count server(s) with GameQ v3 query\r\n";
        foreach ($allServersV3Array as $servers) {
            if (count($servers) > 0) {

                $gq = new \GameQ\GameQ();
                $gq->addServers($servers);
                $gq->setOption('timeout', 60);

                if (isset($dbConnect['debug']) and $dbConnect['debug'] == 1) {
                    $gq->setOption('debug', true);
                }

                $gq->addFilter('normalise');
                $gq->addFilter('stripcolor');

                foreach($gq->process() as $switchID => $v) {
                    $serverReplies[$switchID] = $v;
                }
            }
        }

        print "Checking $totalV2Count server(s) with GameQ v2 query\r\n";
        foreach ($allServersV2Array as $servers) {
            if (count($servers) > 0) {

                $gq = new GameQ();
                $gq->setOption('timeout', 60);

                if (isset($dbConnect['debug']) and $dbConnect['debug'] == 1) {
                    $gq->setOption('debug', true);
                }

                $gq->setFilter('normalise');
                $gq->addServers($servers);

                foreach($gq->requestData() as $switchID => $v) {
                    $serverReplies[$switchID] = $v;
                }
            }
        }
        unset($gq);

        foreach($serverReplies as $switchID => $v) {

            unset($userid, $resellerid, $lendserver, $stopserver, $doNotRestart);

            $lid = 0;
            $elapsed = 0;
            $shutdownemptytime = 0;
            $notified = 0;

            $query = $sql->prepare("SELECT s.`id` AS `serverID`,t.`id` AS `templateID`,t.`description` AS `templateDescription`,t.`gamebinary`,l.`id` AS `lend_id`,l.`started` AS `lend_started`,g.* FROM `gsswitch` g INNER JOIN `serverlist` s ON g.`serverid`=s.`id` INNER JOIN `servertypes` t ON s.`servertype`=t.`id` LEFT JOIN `lendedserver` AS l ON l.`serverid`=s.`id` WHERE g.`id`=? LIMIT 1");
            $query->execute(array($switchID));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

                $serverip = $row['serverip'];
                $autoRestart = $row['autoRestart'];
                $port = $row['port'];
                $address = $row['serverip'] . ':' . $row['port'];
                $gametype = $row['templateDescription'];
                $notified = $row['notified'];
                $secnotified = $row['secnotified'];
                $lendserver = $row['lendserver'];
                $userid = $row['userid'];
                $resellerid = $row['resellerid'];
                $brandname = $row['brandname'];
                $rootID = $row['rootID'];
                $war = $row['war'];
                $slots = $row['slots'];

                if (($row['gamebinary'] == 'hlds_run' or $row['gamebinary'] == 'srcds_run') and $row['tvenable'] == 'Y') {
                    $slots++;
                }

                if ($lendserver == 'Y' and $lendActive == 'Y') {
                    $lid = $row['lend_id'];
                    $shutdownemptytime = $resellersettings[$resellerid]['shutdownemptytime'];
                    $elapsed = round((strtotime('now') - strtotime($row['lend_started'])) / 60);
                }
            }

            if ($v['gq_online'] == 1) {
                $name = normalizeName($v['gq_hostname']);
                $numplayers = $v['gq_numplayers'];
                $maxplayers = $v['gq_maxplayers'];
                $map = $v['gq_mapname'];
                $password = ($v['gq_password'] == 1) ? 'Y' : 'N';
            } else {
                $name = 'OFFLINE';
                $numplayers = 0;
                $maxplayers = 0;
                $map = '';
                $password = 'Y';
            }

            $lendStop = array();

            // Check lendserver specific settings
            if (isset($userid) and isset($lendserver) and $lendserver == 'Y') {

                // Running but no lend information in temp table
                if ($v['gq_online'] == 1) {
                    $query = $sql->prepare("SELECT 1 FROM `lendedserver` WHERE `id`=? LIMIT 1");
                    $query->execute(array($lid));

                    if ($query->rowCount() == 0) {

                        print "Will stop lendserver $address because not lendet\r\n";

                        $lendStop[] = 'not lended';

                        $stopserver = true;
                    }

                    if (!isset($stopserver) and $lendserver == 'Y' and $lendActive == 'Y' and $resellersettings[$resellerid]['shutdownempty'] == 'Y' and $elapsed > $shutdownemptytime and $numplayers == 0 and $maxplayers != 0 and $slots != 0) {

                        print "Will stop server $address after $elapsed minutes, because it is empty and threshold is $shutdownemptytime minutes \r\n";

                        $lendStop[] = 'stop empty lended';

                        $stopserver = true;
                    }
                }

                // Expected to be running but is not, so remove from temp table
                if (isset($stopserver) or $v['gq_online'] != 1) {

                    if (!isset($stopserver)) {
                        print "Will remove lendserver $address with lendID $lid because it is lendet but stopped \r\n";
                    }

                    $doNotRestart = true;

                    $query = $sql->prepare("DELETE FROM `lendedserver` WHERE `id`=? LIMIT 1");
                    $query->execute(array($lid));
                }
            }

            if (isset($userid) and $v['gq_online'] == 1) {

                $rulebreak = array();

                if ($war == 'Y' and $password == 'N') {

                    $rulebreak[] = $ssprache->nopassword;

                    if ($resellersettings[$resellerid]['nopassword'] == 1) {
                        $stopserver = true;
                        print "Will stop server $address because running without password. The name converted to ISO-8859-1 is ".iconv('UTF-8','ISO-8859-1//TRANSLIT', $name).".\r\n";

                    } else {
                        print "Server with address $address is running as $gametype and illegal without password. The name converted to ISO-8859-1 is ".iconv('UTF-8','ISO-8859-1//TRANSLIT', $name).".\r\n";
                    }
                }

                if ($maxplayers > $slots) {

                    $rulebreak[] = $ssprache->tohighslots;

                    if ($resellersettings[$resellerid]['tohighslots'] == 1) {
                        $stopserver = true;
                        print "Will stop server $address because running with to much slots. The name converted to ISO-8859-1 is ".iconv('UTF-8','ISO-8859-1//TRANSLIT', $name).".\r\n";
                    } else {
                        print "Server $address is running as $gametype and with illegal slotamount. The name converted to ISO-8859-1 is ".iconv('UTF-8','ISO-8859-1//TRANSLIT', $name).".\r\n";
                    }
                }

                if ($brandname == 'Y' and $resellersettings[$resellerid]['brandname'] != '' and strpos(strtolower($name),strtolower($resellersettings[$resellerid]['brandname'])) === false) {

                    $rulebreak[] = $ssprache->noservertag;

                    if ($resellersettings[$resellerid]['noservertag'] == 1) {
                        $stopserver = true;
                        print "Will stop server $address because running without servertag. The name converted to ISO-8859-1 is ".iconv('UTF-8','ISO-8859-1//TRANSLIT', $name).".\r\n";
                    } else {
                        print "Server $address is running as $gametype and illegal without servertag. The name converted to ISO-8859-1 is ".iconv('UTF-8','ISO-8859-1//TRANSLIT', $name).".\r\n";
                    }
                }

                if (count($rulebreak) == 0 and !isset($stopserver)) {
                    print "Server $address is running as $gametype. The name converted to ISO-8859-1 is ".iconv('UTF-8','ISO-8859-1//TRANSLIT', $name).".\r\n";
                }

                if ($secnotified == 'N' and count($rulebreak) > 0) {

                    if ($resellerid == 0) {
                        $query = $sql->prepare("SELECT `id`,`mail_securitybreach` FROM `userdata` WHERE `id`=? OR (`resellerid`=0 AND `accounttype`='a')");
                        $query->execute(array($userid));

                    } else {
                        $query = $sql->prepare("SELECT `id`,`mail_securitybreach` FROM `userdata` WHERE `id`=? OR (`id`=? AND `accounttype`='r')");
                        $query->execute(array($userid, $resellerid));
                    }

                    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                        if ($row['mail_securitybreach'] == 'Y') {
                            sendmail('emailsecuritybreach', $row['id'], $address, implode('<br>', $rulebreak));
                        }
                    }

                    $query = $sql->prepare("UPDATE `gsswitch` SET `secnotified`='Y' WHERE `id`=? LIMIT 1");
                    $query->execute(array($switchID));

                }

                if ($secnotified == 'Y' and count($rulebreak) == 0) {
                    $query = $sql->prepare("UPDATE `gsswitch` SET `secnotified`='N' WHERE `id`=? LIMIT 1");
                    $query->execute(array($switchID));
                }

                if (isset($stopserver) and $stopserver === true) {

                    $numplayers = 0;
                    $map = '';

                    $loguserid = $userid;
                    $reseller_id = $resellerid;
                    $loguseraction = "%stop% %gserver% {$address}";
                    $loguseraction .= (count($rulebreak) > 0) ? " " . implode(', ', $rulebreak) : "";
                    $loguseraction .= (count($lendStop) > 0 && count($rulebreak) == 0) ? " " . implode(', ', $lendStop) : "";
                    $insertlog->execute();

                    $startStopList[$resellerid][$rootID]['stop'][] = $switchID;

                    $query = $sql->prepare("DELETE FROM `lendedserver` WHERE `serverid`=? AND `resellerid`=? AND `servertype`='g' LIMIT 1");
                    $query->execute(array($switchID, $resellerid));
                }

                if ($notified > 0) {

                    $notified = 0;

                    $query = $sql->prepare("UPDATE `gsswitch` SET `notified`=0 WHERE `id`=? LIMIT 1");
                    $query->execute(array($switchID));
                }

            } else if (isset($userid) and isset($autoRestart)) {

                $name = 'OFFLINE';
                $numplayers = 0;
                $maxplayers = 0;
                $map = '';
                $password = 'Y';

                if (!isset($doNotRestart)) {

                    $notified++;

                    if (isset($dbConnect['debug']) and $dbConnect['debug'] == 1) {
                        print_r($v);
                    }

                    if ($autoRestart == 'Y' and $notified >= $resellersettings[$resellerid]['down_checks']) {

                        print "Restarting: $address\r\n";

                        $loguserid = $userid;
                        $reseller_id = $resellerid;
                        $loguseraction = "%start% %gserver% {$address} (Found offline since {$notified} checks)";
                        $insertlog->execute();

                        $startStopList[$resellerid][$rootID]['start'][] = $switchID;

                    } else {
                        print "Not Restarting: $address\r\n";
                    }

                    if ($notified == $resellersettings[$resellerid]['down_checks']) {
                        $query = $sql->prepare("SELECT `mail_serverdown` FROM `userdata` WHERE `id`=? LIMIT 1");
                        $query->execute(array($userid));
                        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                            if ($row['mail_serverdown'] == 'Y') {
                                sendmail('emaildownrestart', $userid, $address,'');
                            }
                        }
                    }
                } else {
                    print "Not Stopping as database leftover: $address\r\n";
                }
            }

            $query = $sql->prepare("UPDATE `gsswitch` SET `queryName`=?,`queryNumplayers`=?,`queryMaxplayers`=?,`queryMap`=?,`queryPassword`=?,`queryUpdatetime`=?,`notified`=? WHERE `id`=? LIMIT 1");
            $query->execute(array($name, $numplayers, $maxplayers, $map, $password, $logdate, $notified, $switchID));
        }

        foreach ($startStopList as $resellerLockupID => $rootServer) {

            foreach ($rootServer as $rootID => $actionList) {

                $appServer = new AppServer($rootID);

                foreach ($actionList as $action => $switchIDs) {

                     foreach ($switchIDs as $switchID) {

                         $appServer->getAppServerDetails($switchID);

                        if ($action == 'start') {
                            $appServer->startApp();
                        } else {
                            $appServer->stopApp();
                        }
                    }
                }

                $appServer->execute();

                if (isset($dbConnect['debug']) and $dbConnect['debug'] == 1) {
                    print implode("\r\n", $appServer->debug()) . "\r\n";
                }
            }
        }
    }

    # Voice Server
    if ($checkTypeOfServer == 'all' or $checkTypeOfServer == 'vs') {

        # voice_tsdns

        print 'Checking TSDNS' . "\r\n";
        $query = $sql->prepare("SELECT *,AES_DECRYPT(`ssh2port`,:aeskey) AS `decryptedssh2port`,AES_DECRYPT(`ssh2user`,:aeskey) AS `decryptedssh2user`,AES_DECRYPT(`ssh2password`,:aeskey) AS `decryptedssh2password` FROM `voice_tsdns` WHERE `active`='Y'");
        $query->execute(array(':aeskey' => $aeskey));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            $resellerid = $row['resellerid'];

            $tsdnscheck = @fsockopen ($row['ssh2ip'], 41144, $errno, $errstr,5);

            if (!is_resource($tsdnscheck) and $row['autorestart'] == 'Y') {
                sleep(1);
                $tsdnscheck = @fsockopen ($row['ssh2ip'], 41144, $errno, $errstr,5);
            }

            if (!is_resource($tsdnscheck) and $row['autorestart'] == 'Y') {

                print "TSDNS Error: ".$row['ssh2ip'] . ' ' . $errno.' ('.$errstr.")\r\n";

                $query3 = $sql->prepare("UPDATE `voice_tsdns` SET `notified`=`notified`+1 WHERE `id`=? LIMIT 1");
                $query3->execute(array($row['id']));

                $tsdnsDownCheck = $row['notified']+1;

                if ($tsdnsDownCheck == $resellersettings[$resellerid]['down_checks']) {

                    if ($resellerid == 0) {
                        $query3 = $sql->prepare("SELECT `id`,`mail_serverdown` FROM `userdata` WHERE `accounttype`='a' AND `resellerid`=0");
                        $query3->execute();
                    } else {
                        $query3 = $sql->prepare("SELECT `id`,`mail_serverdown` FROM `userdata` WHERE `id`=? LIMIT 1");
                        $query3->execute(array($resellerid));
                    }

                    while ($row3 = $query3->fetch(PDO::FETCH_ASSOC)) {
                        if ($row3['mail_serverdown'] == 'Y') {
                            sendmail('emaildownrestart', $row3['id'], $row['ssh2ip'].' (External TSDNS)','');
                        }
                    }
                }

                unset($folders);

                $i = 0;
                $lastFolder = '';

                $tsdnsbin= ($row['bitversion'] == '32') ? 'tsdnsserver_linux_x86' : 'tsdnsserver_linux_amd64';
                $split_config = preg_split('/\//', $row['serverdir'], -1, PREG_SPLIT_NO_EMPTY);
                $folderfilecount = count($split_config) - 1;
                $folders = (substr($row['serverdir'], 0, 1) == '/') ? 'cd  /' : 'cd ';

                while ($i <= $folderfilecount) {
                    $folders = $folders . $split_config[$i] . '/';
                    $lastFolder = $split_config[$i];
                    $i++;
                }

                if ($folders == 'cd ') {
                    $folders = '';
                } else if ($lastFolder != 'tsdns' or substr($row['serverdir'], 0, 1) != '/') {
                    $folders = $folders . 'tsdns/ && ';
                } else {
                    $folders = $folders  . ' && ';
                }

                $ssh2cmd = $folders . ' if [ -f "tsdnsserver" ]; then TSDNSBIN="tsdnsserver"; else TSDNSBIN="' . $tsdnsbin . '"; fi; function r () { if [ "`ps fx | grep $TSDNSBIN | grep -v grep`" == "" ]; then ./$TSDNSBIN > /dev/null & else ./$TSDNSBIN --update > /dev/null & fi }; r& ';

                if ((isset($args['tsDebug']) and $args['tsDebug'] == 1)) {
                    print "$ssh2cmd\r\n";
                }

                if (ssh2_execute('vd', $row['id'], $ssh2cmd)) {
                    print "Restarting TSDNS: {$row['ssh2ip']}\r\n";
                } else {
                    print "Failed restarting TSDNS: {$row['ssh2ip']}\r\n";
                }

                if (isset($dbConnect['debug']) and $dbConnect['debug'] == 1) {
                    echo $ssh2cmd . "\r\n";
                }

            } else {

                print "TSDNS ${row['ssh2ip']} is up and running\r\n";

                $query3 = $sql->prepare("UPDATE `voice_tsdns` SET `notified`=0 WHERE `id`=? LIMIT 1");
                $query3->execute(array($row['id']));
            }
        }

        /* Voice Server */
        if ((isset($args['tsDebug']) and $args['tsDebug'] == 1)) {
            print "Checking voice server with debug on\r\n";
        }

        $query = $sql->prepare("SELECT *,AES_DECRYPT(`querypassword`,:aeskey) AS `decryptedquerypassword`,AES_DECRYPT(`ssh2port`,:aeskey) AS `decryptedssh2port`,AES_DECRYPT(`ssh2user`,:aeskey) AS `decryptedssh2user`,AES_DECRYPT(`ssh2password`,:aeskey) AS `decryptedssh2password` FROM `voice_masterserver` WHERE `active`='Y'");
        $query->execute(array(':aeskey' => $aeskey));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $vrow) {

            unset($connect_ssh2, $ssh2, $badLogin);

            $ts3masterid = $vrow['id'];
            $ts3masternotified = $vrow['notified'];
            $addedby = $vrow['addedby'];
            $queryport = $vrow['queryport'];
            $querypassword = $vrow['decryptedquerypassword'];
            $resellerid = $vrow['resellerid'];
            $autorestart = $vrow['autorestart'];
            $latestVersion = $vrow['latest_version'];

            if ($addedby == 1) {
                $vselect2 = $sql->prepare("SELECT `ip` FROM `rserverdata` WHERE `id`=? LIMIT 1");
                $vselect2->execute(array($vrow['rootid']));
                $queryip = $vselect2->fetchColumn();
            } else {
                $queryip = $vrow['ssh2ip'];
            }

            if ($vrow['type'] == 'ts3') {

                print "Connecting to TS3 server $queryip\r\n";

                $tsdown = false;
                $tsdnsdown = false;
                $defaultwelcome = $vrow['defaultwelcome'];

                $default = array('virtualserver_hostbanner_url' => $vrow['defaulthostbanner_url'], 'virtualserver_hostbanner_gfx_url' => $vrow['defaulthostbanner_gfx_url'], 'virtualserver_hostbutton_tooltip' => $vrow['defaulthostbutton_tooltip'], 'virtualserver_hostbutton_url' => $vrow['defaulthostbutton_url'], 'virtualserver_hostbutton_gfx_url' => $vrow['defaulthostbutton_gfx_url'], 'defaultwelcome' => $vrow['defaultwelcome']);

                $connection = new TS3($queryip, $queryport,'serveradmin', $querypassword, (isset($args['tsDebug']) and $args['tsDebug'] == 1) ? true : false);
                $errorcode = $connection->errorcode;

                if (strpos($errorcode,'error id=0') === false) {

                    $connection->CloseConnection();
                    unset($connection);

                    sleep(1);

                    $connection = new TS3($queryip, $queryport,'serveradmin', $querypassword);
                    $errorcode = $connection->errorcode;
                }

                if (strpos($errorcode, 'error id=0') === false) {
                    $connection->CloseConnection();
                    unset($connection);
                }

                if (strpos($errorcode, 'error id=0') === false) {
                    print "TS3 Query Error: " . $errorcode . "\r\n";
                    $tsdown = true;
                    $restartreturn = "TS3";
                }

                if ($vrow['usedns'] == 'Y') {

                    $tsdnscheck = @fsockopen ($queryip,41144, $errno, $errstr,5);

                    if (!is_resource($tsdnscheck)) {
                        sleep(1);
                        $tsdnscheck = @fsockopen ($queryip,41144, $errno, $errstr,5);
                    }
                    if (!is_resource($tsdnscheck)) {

                        print "TSDNS Error: ".$errno.' ('.$errstr.")\r\n";

                        $tsdnsdown = true;

                        if (isset($restartreturn)) {
                            $restartreturn .= " and TSDNS";
                        } else {
                            $restartreturn = "TSDNS";
                        }
                    }
                }

                if ($tsdown == true or $tsdnsdown == true) {

                    $ts3masternotified++;

                    if ($ts3masternotified == $resellersettings[$resellerid]['down_checks']) {

                        if ($resellerid == 0) {
                            $query2 = $sql->prepare("SELECT `id`,`mail_serverdown` FROM `userdata` WHERE `accounttype`='a' AND `resellerid`=0");
                            $query2->execute();
                        } else {
                            $query2 = $sql->prepare("SELECT `id`,`mail_serverdown` FROM `userdata` WHERE `id`=? LIMIT 1");
                            $query2->execute(array($resellerid));
                        }

                        while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {
                            if ($row2['mail_serverdown'] == 'Y') {
                                sendmail('emaildownrestart', $row2['id'], $queryip . ' (' . $restartreturn . ')', '');
                            }
                        }
                    }

                    $query2 = $sql->prepare("UPDATE `voice_server` SET `uptime`=0 WHERE `masterserver`=?");
                    $query2->execute(array($ts3masterid));

                    $query2 = $sql->prepare("UPDATE `voice_masterserver` SET `notified`=? WHERE `id`=? LIMIT 1");
                    $query2->execute(array($ts3masternotified, $ts3masterid));

                    if (($autorestart == 'Y' and $ts3masternotified >= $resellersettings[$resellerid]['down_checks'])) {

                        $cmds = array();

                        $i = 0;
                        $split_config = preg_split('/\//', $vrow['serverdir'], -1, PREG_SPLIT_NO_EMPTY);
                        $folderfilecount = count($split_config) - 1;
                        $folders = (substr($vrow['serverdir'], 0, 1) == '/') ? 'cd  /' : 'cd ';

                        while ($i <= $folderfilecount) {
                            $folders = $folders . $split_config[$i] . '/';
                            $i++;
                        }

                        if ($folders == 'cd ') {
                            $folders = '';
                            $tsdnsFolders = 'cd tsdns && ';
                        } else {
                            $tsdnsFolders = $folders.'tsdns && ';
                            $folders = $folders . ' && ';
                        }

                        if ($vrow['bitversion'] == '32') {
                            $tsbin = 'ts3server_linux_x86';
                            $tsdnsbin = 'tsdnsserver_linux_x86';
                        } else {
                            $tsbin = 'ts3server_linux_amd64';
                            $tsdnsbin = 'tsdnsserver_linux_amd64';
                        }

                        if ($tsdown == true) {
                            $cmds[] = $folders . 'if [ -f "ts3server" ]; then TSBIN="ts3server"; else TSBIN="' . $tsdnsbin . '"; fi; function r () { if [ "`ps fx | grep $TSBIN | grep -v grep`" == "" ]; then ./ts3server_startscript.sh start inifile=ts3server.ini > /dev/null & else ./ts3server_startscript.sh restart inifile=ts3server.ini > /dev/null & fi }; r& ';
                        }

                        if ($vrow['usedns'] == 'Y' and $tsdnsdown == true) {
                            $cmds[] = $tsdnsFolders . 'if [ -f "tsdnsserver" ]; then TSDNSBIN="tsdnsserver"; else TSDNSBIN="' . $tsdnsbin . '"; fi; function r () { if [ "`ps fx | grep ' . $tsdnsbin . ' | grep -v grep`" == "" ]; then ./' . $tsdnsbin . ' > /dev/null & else ./' . $tsdnsbin . ' --update > /dev/null & fi }; r& ';
                        }

                        if (count($cmds) > 0) {

                            if (ssh2_execute('vm', $ts3masterid, $cmds)) {
                                print "Restarting: $restartreturn $queryip\r\n";
                            } else {
                                print "Failed restarting: $restartreturn $queryip\r\n";
                            }

                            if ((isset($dbConnect['debug']) and $dbConnect['debug'] == 1) or (isset($args['tsDebug']) and $args['tsDebug'] == 1)) {
                                print_r($cmds);
                            }
                        }
                    } else {
                        print "$restartreturn $queryip down but no Restart triggert\r\n";
                    }
                }

                if ($tsdown != true) {

                    if ($ts3masternotified > 0) {
                        $pupdate = $sql->prepare("UPDATE `voice_masterserver` SET `notified`=0 WHERE `id`=? LIMIT 1");
                        $pupdate->execute(array($ts3masterid));
                    }

                    $serverVersion = $connection->getServerVersion();

                    if ($serverVersion and preg_match('/^([\d]{1,2}.)*[\d]{1,2}$/', $serverVersion)) {

                        if ($serverVersion == $latestVersion) {
                            echo "TS3 server version is running up to date version $serverVersion\r\n";
                        } else {
                            echo "TS3 server version is running outdated version $serverVersion. Latest is $latestVersion\r\n";
                        }

                        $pupdate = $sql->prepare("UPDATE `voice_masterserver` SET `local_version`=? WHERE `id`=? LIMIT 1");
                        $pupdate->execute(array($serverVersion, $ts3masterid));
                    }

                    $serverlist = $connection->ServerList();

                    if (!isset($serverlist[0]['id']) or $serverlist[0]['id'] == 0) {

                        foreach ($serverlist as $server) {

                            unset($newtrafficdata, $newtraffic, $ts3id);

                            $modbadserver = array();
                            $newnotified = 0;

                            $virtualserver_id = $server['virtualserver_id'];
                            $vs = $server['virtualserver_status'];
                            $uptime = (isset($server['virtualserver_uptime'])) ? $server['virtualserver_uptime'] : 0;

                            $vselect2 = $sql->prepare("SELECT v.*,u.`active` AS `user_active` FROM `voice_server` AS v INNER JOIN `userdata` u ON u.`id`=v.`userid` WHERE v.`localserverid`=? AND v.`masterserver`=? LIMIT 1");
                            $vselect2->execute(array($virtualserver_id, $vrow['id']));
                            foreach ($vselect2->fetchall(PDO::FETCH_ASSOC) as $vrow2) {
                                $autoRestart = $vrow2['autoRestart'];
                                $queryName = $vrow2['queryName'];
                                $lendserver = $vrow2['lendserver'];
                                $ts3id = $vrow2['id'];
                                $userid = $vrow2['userid'];
                                $slots = $vrow2['slots'];
                                $password = $vrow2['password'];
                                $forcebanner = $vrow2['forcebanner'];
                                $forcebutton = $vrow2['forcebutton'];
                                $forceservertag = $vrow2['forceservertag'];
                                $forcewelcome = $vrow2['forcewelcome'];
                                $flexSlots = $vrow2['flexSlots'];
                                $flexSlotsFree = $vrow2['flexSlotsFree'];
                                $flexSlotsPercent = $vrow2['flexSlotsPercent'];
                                $flexSlotsCurrent = $vrow2['flexSlotsCurrent'];
                                $max_download_total_bandwidth = $vrow2['max_download_total_bandwidth'];
                                $max_upload_total_bandwidth = $vrow2['max_upload_total_bandwidth'];
                                $address = $vrow2['ip'] . ':' . $vrow2['port'];
                                $active = $vrow2['active'];
                                $userActive = $vrow2['user_active'];
                                $notified = $vrow2['notified'];
                                $olduptime = $vrow2['uptime'];
                                $initialpassword = $vrow2['initialpassword'];
                                $maxtrafficmb = $vrow2['maxtraffic'];
                                $maxtraffic = $maxtrafficmb * 1024;
                                $filetraffic = ($vrow2['filetraffic'] == null) ? 0 : $vrow2['filetraffic'];
                                $lastfiletraffic = ($vrow2['lastfiletraffic'] == null) ? 0 : $vrow2['lastfiletraffic'];
                                $newtrafficdata = $lastfiletraffic;
                                $newtraffic = $filetraffic;
                            }

                            if (isset($ts3id) and $vs == 'online' and ($active == 'N' or $userActive == 'N')) {

                                print "Inactive TS3 server $address running. Stopping it.\r\n";
                                $connection->StopServer($virtualserver_id);

                            } else if (isset($ts3id) and $vs == 'online' and $active == 'Y') {

                                unset($rulebreak, $changeSlots);

                                $queryName = $server['virtualserver_name'];
                                $usedslots = (isset($server['virtualserver_clientsonline'])) ? $server['virtualserver_clientsonline'] : 0;

                                if ($lendserver == 'Y') {

                                    $vselect2 = $sql->prepare("SELECT `slots` FROM `lendedserver` WHERE `servertype`='v' AND `serverid`=? LIMIT 1");
                                    $vselect2->execute(array($ts3id));
                                    $lendslots = $vselect2->fetchColumn();
                                } else {
                                    $lendslots = 0;
                                }

                                $sd = $connection->ServerDetails($virtualserver_id);
                                $newtrafficdata = round(($sd['connection_filetransfer_bytes_sent_total'] + $sd['connection_filetransfer_bytes_received_total']) / 1024);

                                if (isset($resellersettings[$resellerid]['firstchecktime']) and isset($resellersettings[$resellerid]['firstcheck']) and $resellersettings[$resellerid]['firstchecktime'] < $resellersettings[$resellerid]['firstcheck']) {
                                    $filetraffic = 0;
                                }

                                $newtraffic = 0;
                                $addedtraffic = 0;

                                if ($newtrafficdata > $lastfiletraffic) {
                                    $addedtraffic = $newtrafficdata - $lastfiletraffic;
                                    $newtraffic = $filetraffic + $addedtraffic;
                                } else if ($newtrafficdata == $lastfiletraffic) {
                                    $newtraffic = $filetraffic;
                                } else if ($newtrafficdata < $lastfiletraffic) {
                                    $addedtraffic = $newtrafficdata;
                                    $newtraffic = $filetraffic + $addedtraffic;
                                }

                                $newtrafficmb = round($newtraffic / 1024);
                                $traffictext = '';
                                $virtualserver_max_download_total_bandwidth = $max_download_total_bandwidth;
                                $virtualserver_max_upload_total_bandwidth = $max_upload_total_bandwidth;

                                if (isset($ts3id) and $flexSlots == 'Y' and $usedslots==0 and ($usedslots+$flexSlotsFree) != $flexSlotsCurrent) {
                                    $changeSlots = $flexSlotsFree;
                                } else if (isset($ts3id) and $flexSlots == 'Y' and ($usedslots + $flexSlotsFree) != $flexSlotsCurrent and ($usedslots + $flexSlotsFree) <= $slots and (abs(($usedslots + $flexSlotsFree) - $flexSlotsCurrent) / ($flexSlotsFree / 100)) >= $flexSlotsPercent) {
                                    $changeSlots = $usedslots + $flexSlotsFree;
                                } else if (isset($ts3id) and $flexSlots == 'Y' and $flexSlotsCurrent != $slots and ($usedslots + $flexSlotsFree) > $slots and (abs(($usedslots + $flexSlotsFree) - $flexSlotsCurrent) / ($flexSlotsFree / 100)) >= $flexSlotsPercent) {
                                    $changeSlots = $slots;
                                }

                                if (isset($changeSlots) and $flexSlotsCurrent != 2 and $changeSlots < 2) {
                                    $changeSlots = 2;
                                } else if (isset($changeSlots) and $flexSlotsCurrent == 2 and $changeSlots < 2) {
                                    unset($changeSlots);
                                }

                                if ($maxtraffic > 0 and $newtraffic > $maxtraffic and $sd['virtualserver_max_download_total_bandwidth'] > 1 and $sd['virtualserver_max_upload_total_bandwidth'] > 1) {

                                    $virtualserver_max_download_total_bandwidth = 1;
                                    $virtualserver_max_upload_total_bandwidth = 1;

                                    $traffictext = 'and has now reached the traffic limit ' . $newtrafficmb . '/' . $maxtrafficmb." MB";

                                    if (isset($rulebreak)) {
                                        $rulebreak .= '<br />Traffic Limit' . $newtrafficmb . '/' . $maxtrafficmb." MB";
                                    } else {
                                        $rulebreak = '<br />Traffic Limit' . $newtrafficmb . '/' . $maxtrafficmb." MB";
                                    }

                                } else if ($maxtraffic > 0 and $newtraffic > $maxtraffic and $sd['virtualserver_max_download_total_bandwidth'] < 2 and $sd['virtualserver_max_upload_total_bandwidth'] < 2) {

                                    $virtualserver_max_download_total_bandwidth = 1;
                                    $virtualserver_max_upload_total_bandwidth = 1;
                                    $traffictext = 'and has still reached the traffic limit ' . $newtrafficmb . '/' . $maxtrafficmb." MB";

                                } else if ($maxtraffic > 0) {
                                    $traffictext = 'and has not reached traffic limit ' . $newtrafficmb . '/' . $maxtrafficmb." MB";
                                } else {
                                    $traffictext = 'and has traffic limit ' . $newtrafficmb . '/' . $maxtrafficmb." MB";
                                }

                                if ($sd['virtualserver_max_download_total_bandwidth'] != $virtualserver_max_download_total_bandwidth or $sd['virtualserver_max_upload_total_bandwidth'] != $virtualserver_max_download_total_bandwidth) {
                                    $modbadserver['virtualserver_max_download_total_bandwidth'] = $virtualserver_max_download_total_bandwidth;
                                    $modbadserver['virtualserver_max_upload_total_bandwidth'] = $virtualserver_max_download_total_bandwidth;
                                }

                                if ($forceservertag == 'Y' and $resellersettings[$resellerid]['brandname'] != '' and strpos(strtolower($server['virtualserver_name']), strtolower($resellersettings[$resellerid]['brandname'])) === false) {

                                    print $vrow['type'] . ' server $address illegal without servertag. The name converted to ISO-8859-1 is ' . iconv('UTF-8','ISO-8859-1//TRANSLIT', $server['virtualserver_name']) . "\r\n";

                                    if (isset($rulebreak)) {
                                        $rulebreak .= '<br />' . $ssprache->noservertag;
                                    } else {
                                        $rulebreak = $ssprache->noservertag;
                                    }

                                    $modbadserver['virtualserver_name'] = $server['virtualserver_name'] . ' ' . $resellersettings[$resellerid]['brandname'];
                                }

                                if (isset($ts3id) and $forcebanner == 'Y') {
                                    foreach (array('virtualserver_hostbanner_url', 'virtualserver_hostbanner_gfx_url') as $param) {
                                        if ($default[$param] != '' and $sd[$param] != $default[$param]) {

                                            $modbadserver[$param] = $default[$param];

                                            print $vrow['type']." server $address $param != ".$default[$param].". The name converted to ISO-8859-1 is ".iconv('UTF-8','ISO-8859-1//TRANSLIT', $server['virtualserver_name']) . "\r\n";

                                            if (isset($rulebreak)) {
                                                $rulebreak .= '<br />' . $param . '  ' . $vosprache->isnot . '  ' . $default[$param];
                                            } else {
                                                $rulebreak = $param . '  ' . $vosprache->isnot . '  ' . $default[$param];
                                            }
                                        }
                                    }
                                }

                                if (isset($ts3id) and $forcebutton == 'Y') {
                                    foreach (array('virtualserver_hostbutton_tooltip', 'virtualserver_hostbutton_url', 'virtualserver_hostbutton_gfx_url') as $param) {
                                        if ($default[$param] != '' and $sd[$param] != $default[$param]) {
                                            $modbadserver[$param] = $default[$param];
                                            print $vrow['type'] . " server $address $param != " . $default[$param] . ". The name converted to ISO-8859-1 is " . iconv('UTF-8','ISO-8859-1//TRANSLIT', $server['virtualserver_name']) . "\r\n";

                                            if (isset($rulebreak)) {
                                                $rulebreak .='<br />' . $param . '  ' . $vosprache->isnot . '  ' . $default[$param];
                                            } else {
                                                $rulebreak = $param . '  ' . $vosprache->isnot . '  ' . $default[$param];
                                            }
                                        }
                                    }
                                }

                                if (isset($ts3id) and $forcewelcome == 'Y' and $default['defaultwelcome'] != '' and $sd['virtualserver_welcomemessage'] != $default['defaultwelcome']) {
                                    $modbadserver['virtualserver_welcomemessage'] = $default['defaultwelcome'];
                                    print $vrow['type'] . " server $address $param != ".$default['defaultwelcome'] . '. The name converted to ISO-8859-1 is ' . iconv('UTF-8','ISO-8859-1//TRANSLIT', $server['virtualserver_name']) . "\r\n";
                                    if (isset($rulebreak)) {
                                        $rulebreak .= '<br />virtualserver_welcomemessage ' . $vosprache->isnot . '  ' . $default['defaultwelcome'];
                                    } else {
                                        $rulebreak = 'virtualserver_welcomemessage ' . $vosprache->isnot . '  ' . $default['defaultwelcome'];
                                    }
                                }

                                if (isset($ts3id, $lendserver) and (($lendserver == 'N' and $slots < $server['virtualserver_maxclients']) or ($lendserver == 'Y' and $lendslots < $server['virtualserver_maxclients']))) {

                                    $showSlots = ($lendserver == 'Y') ? $lendslots : $slots;
                                    print $vrow['type'] . " server $address virtualserver_maxclients ${sd['virtualserver_maxclients']}!= " . $showSlots . ". The name converted to ISO-8859-1 is " . iconv('UTF-8','ISO-8859-1//TRANSLIT', $server['virtualserver_name']) . "\r\n";

                                    if (isset($rulebreak)) {
                                        $rulebreak .= '<br />virtualserver_maxclients ' . $vosprache->isnot . '  ' . $showSlots;
                                    } else {
                                        $rulebreak = 'virtualserver_maxclients ' . $vosprache->isnot . '  ' . $showSlots;
                                    }
                                }

                                if (isset($ts3id) and $password == 'Y' and $sd['virtualserver_flag_password'] != '1') {
                                    $modbadserver['virtualserver_password'] = $initialpassword;
                                    print $vrow['type'] . " server $address virtualserver_flag_password != 1. The name converted to ISO-8859-1 is " . iconv('UTF-8','ISO-8859-1//TRANSLIT', $server['virtualserver_name']) . "\r\n";
                                    if (isset($rulebreak)) {
                                        $rulebreak .="<br />virtualserver_flag_password ".$vosprache->isnot." 1";
                                    } else {
                                        $rulebreak="virtualserver_flag_password ".$vosprache->isnot." 1";
                                    }
                                }

                                if (isset($ts3id) and $lendserver == 'N' and !isset($rulebreak)) {

                                    if (isset($changeSlots)) {

                                        print $vrow['type'] . " server $address Changing Flex Slots to ${changeSlots}. The name converted to ISO-8859-1 is " . iconv('UTF-8','ISO-8859-1//TRANSLIT', $server['virtualserver_name']) . "\r\n";

                                        $connection->ImportModServer($virtualserver_id, $changeSlots, $vrow2['ip'], $vrow2['port'], array());

                                        $pupdate2 = $sql->prepare("UPDATE `voice_server` SET `notified`=0,`flexSlotsCurrent`=? WHERE `id`=? LIMIT 1");
                                        $pupdate2->execute(array($changeSlots, $ts3id));

                                    } else if ($notified > 0) {
                                        $pupdate2 = $sql->prepare("UPDATE `voice_server` SET `notified`=0 WHERE `id`=? LIMIT 1");
                                        $pupdate2->execute(array($ts3id));
                                    }

                                    print $vrow['type']." server $address is running $traffictext. The name converted to ISO-8859-1 is ".iconv('UTF-8','ISO-8859-1//TRANSLIT', $server['virtualserver_name']) . "\r\n";

                                } else if (isset($ts3id) and $notified == 0 and isset($rulebreak)) {

                                    $connection->ImportModServer($virtualserver_id,($lendserver == 'Y') ? $lendslots : $slots, $vrow2['ip'], $vrow2['port'], $modbadserver);

                                    if ($resellerid==0) {
                                        $query2 = $sql->prepare("SELECT `id`,`mail_securitybreach` FROM `userdata` WHERE `id`=? OR (`resellerid`=0 AND `accounttype`='a')");
                                        $query2->execute(array($userid));
                                    } else {
                                        $query2 = $sql->prepare("SELECT `id`,`mail_securitybreach` FROM `userdata` WHERE `id`=? OR (`id`=? AND `accounttype`='r')");
                                        $query2->execute(array($userid, $resellerid));
                                    }

                                    while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {
                                        if ($row2['mail_securitybreach'] == 'Y' or $row2['id'] == $userid) {
                                            sendmail('emailsecuritybreach', $row2['id'], $address, $rulebreak);
                                        }
                                    }
                                    $pupdate2 = $sql->prepare("UPDATE `voice_server` SET `notified`='1' WHERE `id`=? LIMIT 1");
                                    $pupdate2->execute(array($ts3id));
                                }

                                if (isset($ts3id) and $lendserver == 'Y' and $resellersettings[$resellerid]['shutdownempty'] == 'Y') {
                                    $stop = false;
                                    $dataloss = true;
                                    $shutdownemptytime = $resellersettings[$resellerid]['shutdownemptytime'];
                                    $elapsedtime = $sql->prepare("SELECT `id`,`started`,`lendtime` FROM `lendedserver` WHERE `serverid`=? LIMIT 1");
                                    $elapsedtime->execute(array($ts3id));
                                    foreach ($elapsedtime->fetchall(PDO::FETCH_ASSOC) as $erow) {
                                        $dataloss = false;
                                        $lid = $erow['id'];
                                        $runtime = $erow['lendtime'];
                                        $elapsed = round((strtotime('now') - strtotime($erow['started'])) / 60);

                                        if ($elapsed > $shutdownemptytime and $usedslots == 0) {
                                            print "Will stop server $address before time is up, because it is empty\r\n";
                                            $stop = true;
                                        } else if ($elapsed >= $runtime) {
                                            print "Will stop server $address because time is up\r\n";
                                            $stop = true;
                                        }

                                        if ($stop == true) {
                                            $rmvoicelend = $sql->prepare("DELETE FROM `lendedserver` WHERE `id`=? LIMIT 1");
                                            $rmvoicelend->execute(array($lid));
                                        }
                                    }
                                    if ($dataloss == true) {
                                        print "Will stop server $address because it is a lendserver and should not be running\r\n";
                                        $stop = true;
                                    }
                                    if ($stop == true) {
                                        $connection->StopServer($virtualserver_id);
                                    }
                                }

                                $query = $sql->prepare("INSERT INTO `voice_server_stats` (`sid`,`mid`,`installed`,`used`,`traffic`,`date`,`uid`,`resellerid`) VALUES (?,?,?,?,?,CURRENT_DATE(),?,?) ON DUPLICATE KEY UPDATE `traffic`=`traffic`+VALUES(`traffic`),`used`=(`used`*(`count`/(`count`+1))+(VALUES(`used`)*(1/(`count`+1)))),`installed`=(`installed`*(`count`/(`count`+1))+(VALUES(`installed`)*(1/(`count`+1)))),`count`=`count`+1");
                                $query->execute(array($ts3id, $ts3masterid, $server['virtualserver_maxclients'], $usedslots, $addedtraffic, $userid, $resellerid));

                            } else if (isset($ts3id)) {

                                $uptime = 1;
                                $usedslots = 0;

                                if ($lendserver == 'Y' and $lendActive == 'Y') {

                                    $removedeadvoiceserver = $sql->prepare("DELETE FROM `lendedserver` WHERE `serverid`=? LIMIT 1");
                                    $removedeadvoiceserver->execute(array($ts3id));

                                } else if ($active == 'Y' and $vs != 'online' and $olduptime > 1 and $olduptime != null and $autoRestart == 'Y') {

                                    $notified++;

                                    if ($notified >= $ts3masternotified == $resellersettings[$resellerid]['down_checks']){
                                        print "TS3 server $address not running. Starting it.\r\n";
                                        $connection->StartServer($virtualserver_id);
                                    }

                                    if ($notified == $resellersettings[$resellerid]['down_checks']) {

                                        $query2 = $sql->prepare("SELECT `mail_serverdown` FROM `userdata` WHERE `id`=? LIMIT 1");
                                        $query2->execute(array($userid));
                                        while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {
                                            if ($row2['mail_serverdown'] == 'Y') {
                                                sendmail('emaildownrestart', $userid, $address,'');
                                            }
                                        }

                                        $newnotified = $notified;
                                    }
                                }
                            }

                            if (isset($ts3id)) {

                                $flagPassword = 'N';

                                if (isset($sd['virtualserver_flag_password']) and $sd['virtualserver_flag_password'] == 1) {
                                    $flagPassword = 'Y';
                                }

                                $query2 = $sql->prepare("UPDATE `voice_server` SET `usedslots`=?,`uptime`=?,`notified`=?,`filetraffic`=?,`lastfiletraffic`=?,`queryName`=?,`queryNumplayers`=?,`queryMaxplayers`=?,`queryPassword`=?,`queryUpdatetime`=NOW() WHERE `id`=? LIMIT 1");
                                $query2->execute(array($usedslots, $uptime, $newnotified, $newtraffic, $newtrafficdata, $queryName,((isset($server['virtualserver_clientsonline'])) ? $server['virtualserver_clientsonline'] : 0 - 1),(isset($server['virtualserver_maxclients'])) ? $server['virtualserver_maxclients'] : 0, $flagPassword, $ts3id));
                            }

                            if (isset($args['coolDown'])) {

                                $nano = time_nanosleep(0, $args['coolDown']);

                                if ($nano === true) {
                                    echo 'Slept for ' . $args['coolDown'] . ' microseconds' . "\r\n";
                                } elseif ($nano === false) {
                                    echo 'Sleeping failed' . "\r\n";
                                } elseif (is_array($nano)) {
                                    echo 'Interrupted by a signal' . "\r\n";
                                    echo 'Time remaining: ' . $nano['seconds'] . ' seconds, ' . $nano['nanoseconds'] . ' nanoseconds' . "\r\n";
                                }
                            }
                        }
                    } else {
                        print "Error: " . $serverlist[0]['msg'] . "\r\n";
                    }
                }

                if (isset($connection)) {
                    $connection->CloseConnection();
                    sleep(1);
                }
            }
        }
    }

    flush();

    # MySQL table sizes
    if ($checkTypeOfServer == 'all' or $checkTypeOfServer == 'my') {

        $query = $sql->prepare("SELECT `id`,`ip`,`port`,`user`,AES_DECRYPT(`password`,?) AS `decryptedpassword` FROM `mysql_external_servers` WHERE `active`='Y'");
        $query2 = $sql->prepare("SELECT `id` FROM `mysql_external_dbs` WHERE `sid`=? AND `dbname`=? LIMIT 1");
        $query3 = $sql->prepare("UPDATE `mysql_external_dbs` SET `dbSize`=? WHERE `id`=? LIMIT 1");

        $query->execute(array($aeskey));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            $remotesql = new ExternalSQL ($row['ip'], $row['port'], $row['user'], $row['decryptedpassword']);

            if ($remotesql->error == 'ok') {

                $list = $remotesql->getDBSizeList();

                if (is_array($list)) {

                    foreach ($list as $db) {

                        $query2->execute(array($row['id'], $db['dbName']));
                        $dbID = $query2->fetchColumn();

                        if (isid($dbID, 10)) {

                            echo 'Found DB ' . $db['dbName'] . ' with size ' . $db['dbSize'] . "\r\n";

                            $query3->execute(array(round($db['dbSize']), $dbID));

                        } else {
                            echo 'Cannot find DB ' . $db['dbName'] . ' with size ' . $db['dbSize'] . "\r\n";
                        }
                    }
                } else {
                    echo 'Error getting DB list for DB Server ' . $row['ip'] . ':' . $row['port'] . ': ' . $list . "\r\n";
                }

            } else {

                echo 'Error connecting to DB Server ' . $row['ip'] . ':' . $row['port'] . ': ' . $remotesql->error . "\r\n";

            }
        }
    }

    flush();

    # Web Quotas
    if ($checkTypeOfServer == 'all' or $checkTypeOfServer == 'vh') {

        echo "Checking Quota usage\r\n";

        $query = $sql->prepare("SELECT `webMasterID`,`ip`,`resellerID` FROM `webMaster` WHERE `active`='Y'");
        $query->execute();
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            echo 'Checking webMaster ' . $row['ip'] . ' with webMasterID ' . $row['webMasterID'] . "\r\n";

            $httpd = new HttpdManagement($row['webMasterID'], $row['resellerID']);
            $httpd->ssh2Connect();
            $httpd->checkQuotaUsage();
        }
    }

    flush();

    # Gather statistics
    if ($checkTypeOfServer == 'all' or $checkTypeOfServer == 'st') {

        $query = $sql->prepare("SELECT u.`id`,u.`cname`,u.`resellerid`,u.`accounttype`,s.`brandname` FROM `userdata` AS u LEFT JOIN `settings` AS s ON u.`resellerid`=s.`resellerid` WHERE u.`active`='Y'");
        $query->execute();
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            if (($row['accounttype'] == 'a' and !isset($adminStatsCollected)) or $row['accounttype'] != 'a') {

                echo "Gathering statistics for user " . $row['cname'] . " with ID " . $row['id'] . " \r\n";

                $statsArray = array(
                    'gameMasterInstalled' => 0,
                    'gameMasterActive' => 0,
                    'gameMasterSlotsAvailable' => 0,
                    'gameMasterServerAvailable' => 0,
                    'gameMasterCrashed' => 0,
                    'gameserverInstalled' => 0,
                    'gameserverActive' => 0,
                    'gameserverSlotsInstalled' => 0,
                    'gameserverSlotsActive' => 0,
                    'gameserverSlotsUsed' => 0,
                    'gameserverNoPassword' => 0,
                    'gameserverNoTag' => 0,
                    'gameserverNotRunning' => 0,
                    'mysqlMasterInstalled' => 0,
                    'mysqlMasterActive' => 0,
                    'mysqlMasterDBAvailable' => 0,
                    'mysqlMasterCrashed' => 0,
                    'mysqlDBInstalled' => 0,
                    'mysqlDBActive' => 0,
                    'mysqlDBSpaceUsed' => 0,
                    'ticketsCompleted' => 0,
                    'ticketsInProcess' => 0,
                    'ticketsNew' => 0,
                    'userAmount' => 0,
                    'userAmountActive' => 0,
                    'virtualMasterInstalled' => 0,
                    'virtualMasterActive' => 0,
                    'virtualMasterVserverAvailable' => 0,
                    'virtualInstalled' => 0,
                    'virtualActive' => 0,
                    'voiceMasterInstalled' => 0,
                    'voiceMasterActive' => 0,
                    'voiceMasterServerAvailable' => 0,
                    'voiceMasterSlotsAvailable' => 0,
                    'voiceMasterCrashed' => 0,
                    'voiceserverInstalled' => 0,
                    'voiceserverActive' => 0,
                    'voiceserverSlotsInstalled' => 0,
                    'voiceserverSlotsActive' => 0,
                    'voiceserverSlotsUsed' => 0,
                    'voiceserverCrashed' => 0,
                    'voiceserverTrafficAllowed' => 0,
                    'voiceserverTrafficUsed' => 0,
                    'webMasterInstalled' => 0,
                    'webMasterActive' => 0,
                    'webMasterSpaceAvailable' => 0,
                    'webMasterVhostAvailable' => 0,
                    'webspaceInstalled' => 0,
                    'webspaceActive' => 0,
                    'webspaceSpaceGiven' => 0,
                    'webspaceSpaceGivenActive' => 0,
                    'webspaceSpaceUsed' => 0,
                );

                if ($row['accounttype'] == 'a') {
                    $insertID = 0;
                    $adminStatsCollected = true;
                } else {
                    $insertID = $row['id'];
                }

                if ($row['accounttype'] == 'a') {
                    $query2 = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `userdata` WHERE `resellerid`=? OR `id`=`resellerid`");
                } else if ($row['accounttype'] == 'r') {
                    $query2 = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `userdata` WHERE `resellerid`=?");
                }
                if ($row['accounttype'] != 'u') {
                    $query2->execute(array($insertID));
                    $statsArray['userAmount'] = (int) $query2->fetchColumn();
                }

                if ($row['accounttype'] == 'a') {
                    $query2 = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `userdata` WHERE (`resellerid`=? OR `id`=`resellerid`) AND `active`='Y'");
                } else if ($row['accounttype'] == 'r') {
                    $query2 = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `userdata` WHERE `resellerid`=? AND `active`='Y'");
                }
                if ($row['accounttype'] != 'u') {
                    $query2->execute(array($insertID));
                    $statsArray['userAmountActive'] = (int) $query2->fetchColumn();
                }

                if ($row['accounttype'] != 'u') {

                    $query2 = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `rserverdata` WHERE `userID`=? OR `resellerid`=?");
                    $query2->execute(array($insertID, $insertID));
                    $statsArray['gameMasterInstalled'] = (int) $query2->fetchColumn();

                    $query2 = $sql->prepare("SELECT COUNT(1) AS `amount`,SUM(`maxslots`) AS `maxSlotsTotal`,SUM(`maxserver`) AS `maxServerTotal` FROM `rserverdata` WHERE (`userID`=? OR `resellerid`=?) AND `active`='Y'");
                    $query2->execute(array($insertID, $insertID));
                    while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {
                        $statsArray['gameMasterActive'] = (int) $row2['amount'];
                        $statsArray['gameMasterSlotsAvailable'] = (int) $row2['maxSlotsTotal'];
                        $statsArray['gameMasterServerAvailable'] = (int) $row2['maxServerTotal'];
                    }

                    if ($row['accounttype'] == 'a') {
                        $query2 = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `voice_masterserver` WHERE `managedForID` IS NULL OR `resellerid`=?");
                        $query2->execute(array($insertID));
                    } else {
                        $query2 = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `voice_masterserver` WHERE `managedForID`=? OR `resellerid`=?");
                        $query2->execute(array($insertID, $insertID));
                    }
                    $statsArray['voiceMasterInstalled'] = (int) $query2->fetchColumn();

                    if ($row['accounttype'] == 'a') {
                        $query2 = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `voice_masterserver` WHERE `notified`>0 AND `active`='Y' AND (`managedForID` IS NULL OR `resellerid`=?)");
                        $query2->execute(array($insertID));
                    } else {
                        $query2 = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `voice_masterserver` WHERE `notified`>0 AND `active`='Y' AND (`managedForID`=? OR `resellerid`=?)");
                        $query2->execute(array($insertID, $insertID));
                    }
                    $statsArray['voiceMasterCrashed'] = (int) $query2->fetchColumn();

                    if ($row['accounttype'] == 'a') {
                        $query2 = $sql->prepare("SELECT COUNT(1) AS `amount`,SUM(`maxslots`) AS `maxSlotsTotal`,SUM(`maxserver`) AS `maxServerTotal` FROM `voice_masterserver` WHERE  (`managedForID` IS NULL OR `resellerid`=?) AND `active`='Y'");
                        $query2->execute(array($insertID));
                    } else {
                        $query2 = $sql->prepare("SELECT COUNT(1) AS `amount`,SUM(`maxslots`) AS `maxSlotsTotal`,SUM(`maxserver`) AS `maxServerTotal` FROM `voice_masterserver` WHERE (`managedForID`=? OR `resellerid`=?) AND `active`='Y'");
                        $query2->execute(array($insertID, $insertID));
                    }
                    while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {
                        $statsArray['voiceMasterActive'] = (int) $row2['amount'];
                        $statsArray['voiceMasterSlotsAvailable'] = (int) $row2['maxSlotsTotal'];
                        $statsArray['voiceMasterServerAvailable'] = (int) $row2['maxServerTotal'];
                    }

                    $query2 = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `webMaster` WHERE `resellerID`=?");
                    $query2->execute(array($insertID));
                    $statsArray['webMasterInstalled'] = (int) $query2->fetchColumn();

                    $query2 = $sql->prepare("SELECT COUNT(1) AS `amount`,SUM(`maxHDD`) AS `maxHDDTotal`,SUM(`maxVhost`) AS `maxVhostTotal` FROM `webMaster` WHERE `resellerID`=? AND `active`='Y'");
                    $query2->execute(array($insertID));
                    while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {
                        $statsArray['webMasterActive'] = (int) $row2['amount'];
                        $statsArray['webMasterVhostAvailable'] = (int) $row2['maxVhostTotal'];
                        $statsArray['webMasterSpaceAvailable'] = (int) $row2['maxHDDTotal'];
                    }

                    $query2 = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `mysql_external_servers` WHERE `resellerid`=?");
                    $query2->execute(array($insertID));
                    $statsArray['mysqlMasterInstalled'] = (int) $query2->fetchColumn();

                    $query2 = $sql->prepare("SELECT COUNT(1) AS `amount`,SUM(`max_databases`) AS `maxDBsTotal` FROM `mysql_external_servers` WHERE `resellerid`=? AND `active`='Y'");
                    $query2->execute(array($insertID));
                    while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {
                        $statsArray['mysqlMasterActive'] = (int) $row2['amount'];
                        $statsArray['mysqlMasterDBAvailable'] = (int) $row2['maxDBsTotal'];
                    }
                }

                if ($row['accounttype'] == 'u') {
                    $query2 = $sql->prepare("SELECT `active`,`slots`,`stopped`,`war`,`brandname`,`queryName`,`queryPassword`,`queryNumplayers` FROM `gsswitch` AS g WHERE `userid`=?");
                } else {
                    $query2 = $sql->prepare("SELECT `active`,`slots`,`stopped`,`war`,`brandname`,`queryName`,`queryPassword`,`queryNumplayers` FROM `gsswitch` AS g WHERE `resellerid`=?");
                }
                $query2->execute(array($insertID));
                while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {

                    $statsArray['gameserverInstalled']++;
                    $statsArray['gameserverSlotsInstalled'] += (int) $row2['slots'];

                    if ($row2['active'] == 'Y') {
                        $statsArray['gameserverActive']++;
                        $statsArray['gameserverSlotsActive'] += (int) $row2['slots'];
                        $statsArray['gameserverSlotsUsed'] += (int) $row2['queryNumplayers'];
                    }

                    if ($row2['queryName'] != 'OFFLINE' and $row2['stopped'] != 'Y' and $row2['war'] == 'Y' and $row2['queryPassword'] == 'N') {
                        $statsArray['gameserverNoPassword']++;
                    } else if ($row2['queryName'] == 'OFFLINE' and $row2['stopped'] != 'Y') {
                        $statsArray['gameserverNotRunning']++;
                    }

                    if ($row2['queryName'] != '' and $row2['stopped'] == 'N' and $row2['queryName'] != 'OFFLINE' and $row2['brandname'] == 'Y' and $row['brandname'] != '' and strpos(strtolower($row2['queryName']), strtolower($row['brandname'])) === false) {
                        $statsArray['gameserverNoTag']++;
                    }
                }

                if ($row['accounttype'] == 'u') {
                    $query2 = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `tickets` WHERE `userid`=? AND `state` = 'C'");
                } else {
                    $query2 = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `tickets` WHERE `resellerid`=? AND `state` = 'C'");
                }
                $query2->execute(array($insertID));
                $statsArray['ticketsCompleted'] = (int) $query2->fetchColumn();

                if ($row['accounttype'] == 'u') {
                    $query2 = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `tickets` WHERE `userid`=? AND `state` NOT IN ('C','D')");
                } else {
                    $query2 = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `tickets` WHERE `resellerid`=? AND `state` NOT IN ('C','D')");
                }
                $query2->execute(array($insertID));
                $statsArray['ticketsInProcess'] = (int) $query2->fetchColumn();

                if ($row['accounttype'] == 'u') {
                    $query2 = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `tickets` WHERE `userid`=? AND `state` = 'N'");
                } else {
                    $query2 = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `tickets` WHERE `resellerid`=? AND `state` = 'N'");
                }
                $query2->execute(array($insertID));
                $statsArray['ticketsNew'] = (int) $query2->fetchColumn();

                if ($row['accounttype'] == 'u') {
                    $query2 = $sql->prepare("SELECT COUNT(1) AS `amount`,SUM(`slots`) AS `slotsInstalled` FROM `voice_server` WHERE `userid`=? GROUP BY `userid`");
                } else {
                    $query2 = $sql->prepare("SELECT COUNT(1) AS `amount`,SUM(`slots`) AS `slotsInstalled` FROM `voice_server` WHERE `resellerid`=? GROUP BY `resellerid`");
                }
                $query2->execute(array($insertID));
                while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {
                    $statsArray['voiceserverInstalled'] = (int) $row2['amount'];
                    $statsArray['voiceserverSlotsInstalled'] = (int) $row2['slotsInstalled'];
                }

                if ($row['accounttype'] == 'u') {
                    $query2 = $sql->prepare("SELECT COUNT(1) AS `amount`,SUM(`slots`) AS `slotsInstalled`,SUM(`queryNumplayers`) AS `slotsUsed`,SUM(`maxtraffic`) AS `trafficAllowed`,SUM(`filetraffic`) AS `trafficUsed` FROM `voice_server` WHERE `active`='Y' AND `userid`=? GROUP BY `userid`");
                } else {
                    $query2 = $sql->prepare("SELECT COUNT(1) AS `amount`,SUM(`slots`) AS `slotsInstalled`,SUM(`queryNumplayers`) AS `slotsUsed`,SUM(`maxtraffic`) AS `trafficAllowed`,SUM(`filetraffic`) AS `trafficUsed` FROM `voice_server` WHERE `active`='Y' AND `resellerid`=? GROUP BY `resellerid`");
                }
                $query2->execute(array($insertID));
                while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {
                    $statsArray['voiceserverActive'] = (int) $row2['amount'];
                    $statsArray['voiceserverSlotsActive'] = (int) $row2['slotsInstalled'];
                    $statsArray['voiceserverSlotsUsed'] = (int) $row2['slotsUsed'];
                    $statsArray['voiceserverTrafficAllowed'] = (int) $row2['trafficAllowed'];
                    $statsArray['voiceserverTrafficUsed'] = round((int) $row2['trafficUsed'] / 1024);
                }

                if ($row['accounttype'] == 'u') {
                    $query2 = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `voice_server` WHERE `active`='Y' AND `uptime`='0' AND `userid`=? GROUP BY `userid`");
                } else {
                    $query2 = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `voice_server` WHERE `active`='Y' AND `uptime`='0' AND `resellerid`=? GROUP BY `resellerid`");
                }
                $query2->execute(array($insertID));
                $statsArray['voiceserverCrashed'] = (int) $query2->fetchColumn();

                if ($row['accounttype'] == 'u') {
                    $query2 = $sql->prepare("SELECT COUNT(1) AS `amount`,SUM(`hdd`) AS `spaceInstalled` FROM `webVhost` WHERE `userID`=? GROUP BY `userID`");
                } else {
                    $query2 = $sql->prepare("SELECT COUNT(1) AS `amount`,SUM(`hdd`) AS `spaceInstalled` FROM `webVhost` WHERE `resellerID`=? GROUP BY `resellerID`");
                }
                $query2->execute(array($insertID));
                while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {
                    $statsArray['webspaceInstalled'] = (int) $row2['amount'];
                    $statsArray['webspaceSpaceGivenActive'] = (int) $row2['spaceInstalled'];
                }

                if ($row['accounttype'] == 'u') {
                    $query2 = $sql->prepare("SELECT COUNT(1) AS `amount`,SUM(`hdd`) AS `spaceInstalled`,SUM(`hddUsage`) AS `spaceUsed` FROM `webVhost` WHERE `active`='Y' AND `userID`=? GROUP BY `userID`");
                } else {
                    $query2 = $sql->prepare("SELECT COUNT(1) AS `amount`,SUM(`hdd`) AS `spaceInstalled`,SUM(`hddUsage`) AS `spaceUsed` FROM `webVhost` WHERE `active`='Y' AND `resellerID`=? GROUP BY `resellerID`");
                }
                $query2->execute(array($insertID));
                while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {
                    $statsArray['webspaceActive'] = (int) $row2['amount'];
                    $statsArray['webspaceSpaceGiven'] = (int) $row2['spaceInstalled'];
                    $statsArray['webspaceSpaceUsed'] = (int) $row2['spaceUsed'];
                }

                if ($row['accounttype'] == 'u') {
                    $query2 = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `mysql_external_dbs` WHERE `uid`=? GROUP BY `uid`");
                } else {
                    $query2 = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `mysql_external_dbs` WHERE `resellerid`=? GROUP BY `resellerid`");
                }
                $query2->execute(array($insertID));
                $statsArray['mysqlDBInstalled'] = (int) $query2->fetchColumn();

                if ($row['accounttype'] == 'u') {
                    $query2 = $sql->prepare("SELECT COUNT(1) AS `amount`,SUM(`dbSize`) AS `spaceUsed` FROM `mysql_external_dbs` WHERE `active`='Y' AND `uid`=? GROUP BY `uid`");
                } else {
                    $query2 = $sql->prepare("SELECT COUNT(1) AS `amount`,SUM(`dbSize`) AS `spaceUsed` FROM `mysql_external_dbs` WHERE `active`='Y' AND `resellerid`=? GROUP BY `resellerid`");
                }
                $query2->execute(array($insertID));
                while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {
                    $statsArray['mysqlDBActive'] = (int) $row2['amount'];
                    $statsArray['mysqlDBSpaceUsed'] = (int) $row2['spaceUsed'];
                }

                unset($updateString, $insertString, $duplicateString);

                foreach ($statsArray as $k => $v) {

                    if (isset($updateString)) {
                        $updateString .= ',`' . $k . '`=' . $v;
                        $insertColumns .= ',`' . $k . '`';
                        $duplicateString .= ',`' . $k . '`=(`' . $k . '`*(`countUpdates`/(`countUpdates`+1))+(VALUES(`' . $k . '`)*(1/(`countUpdates`+1))))';
                    } else {
                        $updateString = '`' . $k . '`=' . $v;
                        $insertColumns = '`' . $k . '`';
                        $duplicateString = '`' . $k . '`=(`' . $k . '`*(`countUpdates`/(`countUpdates`+1))+(VALUES(`' . $k . '`)*(1/(`countUpdates`+1))))';
                    }
                }

                if (isset($updateString)) {
                    $query2 = $sql->prepare("INSERT INTO  `easywi_statistics_current` (`userID`) VALUES (?) ON DUPLICATE KEY UPDATE `userID`=`userID`");
                    $query2->execute(array($insertID));

                    $query2 = $sql->prepare("UPDATE `easywi_statistics_current` SET " . $updateString . " WHERE `userID`= " . $insertID . " LIMIT 1");
                    $query2->execute();

                    $query2 = $sql->prepare("INSERT INTO `easywi_statistics` (" . $insertColumns . ",`userID`,`statDate`,`countUpdates`) VALUES (" . implode(',', $statsArray) . "," . $insertID . ",CURDATE(),1) ON DUPLICATE KEY UPDATE " . $duplicateString . ",`countUpdates`=`countUpdates`+1");
                    $query2->execute();
                }
            }
        }
    }

    $query = $sql->prepare("UPDATE `settings` SET `lastCronStatus`=UNIX_TIMESTAMP()");
    $query->execute();



} else {
	header('Location: login.php');
	die('Statuscheck can only be run via console or a cronjob');
}
