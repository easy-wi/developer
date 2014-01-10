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

if (isset($argv)) {

    $args = array();

    foreach ($argv as $a) {
        if ($a == 'gs' or $a == 'vs') {
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

define('EASYWIDIR', dirname(__FILE__));
include(EASYWIDIR . '/stuff/vorlage.php');
include(EASYWIDIR . '/stuff/functions.php');
include(EASYWIDIR . '/stuff/class_validator.php');
include(EASYWIDIR . '/stuff/settings.php');
include(EASYWIDIR . '/stuff/functions_ssh_exec.php');
include(EASYWIDIR . '/stuff/class_ts3.php');
include(EASYWIDIR . '/third_party/gameq/GameQ.php');
include(EASYWIDIR . '/stuff/keyphrasefile.php');

set_time_limit($timelimit);

if (!isset($ip) or $ui->escaped('SERVER_ADDR', 'server') == $ip or in_array($ip, ipstoarray($rSA['cronjob_ips']))) {

    if (isset($checkTypeOfServer)) {
        print ($checkTypeOfServer == 'gs') ? 'Checking Gameserver' . "\r\n" : 'Checking Voiceserver' . "\r\n";
    } else {
        $checkTypeOfServer='all';
        print 'Checking Gameserver and Voiceserver' . "\r\n";
    }

    $dayAndHour=date('Y-m-d H:').'00:00';
    $dayAndZeroHour=date('Y-m-d').' 00:00:00';
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
    foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
        unset($shutdownempty);
        $resellerid = $row['resellerid'];
        $query2->execute(array($resellerid));
        foreach ($query2->fetchall(PDO::FETCH_ASSOC) as $row2) {
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

        // Lend server stopping.
        // We want only one socket per root server. Collect the to be stopped lendservers in an array and sort by root ID
        $rtmp = array();

        $query = $sql->prepare("SELECT `id`,`serverid`,`started`,`lendtime`,`resellerid` FROM `lendedserver` WHERE `servertype`='g'");
        $query2 = $sql->prepare("SELECT g.`rootID`,g.`id` FROM `serverlist` s INNER JOIN `gsswitch` g ON s.`switchID`=g.`id` WHERE s.`id`=? LIMIT 1");
        $query3 = $sql->prepare("DELETE FROM `lendedserver` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute();
        foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
            $id = $row['id'];
            $lendtime = $row['lendtime'];
            $serverid = $row['serverid'];
            $resellerid = $row['resellerid'];
            $timeleft = round($row['lendtime'] - (strtotime('now') - strtotime($row['started'])) / 60);

            if ($timeleft <= 0) {
                $query3->execute(array($id, $resellerid));

                $query2->execute(array($row['serverid']));
                foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {

                    $tmp = gsrestart($row2['id'], 'so', $aeskey, $resellerid);

                    if (is_array($tmp)) {
                        foreach($tmp as $t) {
                            $rtmp[$row2['rootID']][] = $t;
                        }
                    }

                }


                print "Time is up, stopping lendserver: $id\r\n";

            } else {
                print "Lendserver $serverid has $timeleft minutes left\r\n";
            }
        }

        // Send stop commands to rootserver
        foreach ($rtmp as $k => $v) {
            if (count($v) > 0) {
                ssh2_execute('gs', $k, $v);
            }
        }


        // Define basic variables for GS status checks
        $other = array();
        $i = 1;
        $totalCount = 0;
        $serverBatchArray = array();
        $allServersArray = array();
        $shellCmds = array();

        // Get the list of servers which are active and are not stopped. The array to be created will support batch mode.
        $query = $sql->prepare("SELECT g.`id`,g.`rootID`,g.`serverid`,g.`serverip`,g.`port`,g.`port2`,t.`gameq` FROM `gsswitch` g INNER JOIN `serverlist` s ON g.`serverid`=s.`id` INNER JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE g.`stopped`='N' AND g.`active`='Y'");
        $query->execute();
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

            // without the gameq value we cannot query. So this results need to be sorted out.
            if (!in_array($row['gameq'], array('', null, false))) {

                $serverBatchArray[] = array('id' => $row['id'], 'type' => $row['gameq'], 'host' => $row['serverip'] . ':' . $row['port']);
                $i++;

                if ($i == 50) {
                    $allServersArray[] = $serverBatchArray;
                    $serverBatchArray = array();
                    $i = 1;
                }

                $totalCount++;
            }
        }

        $allServersArray[] = $serverBatchArray;


        print "Checking $totalCount server(s) with GameQ query\r\n";

        foreach ($allServersArray as $servers) {
            $gq = new GameQ();
            $gq->setOption('timeout', 3);

            if (isset($dbConnect['debug']) and $dbConnect['debug'] == 1) {
                $gq->setOption('debug', true);
            }

            $gq->setFilter('normalise');
            $gq->addServers($servers);

            foreach($gq->requestData() as $switchID => $v) {

                unset($userid, $stopserver, $doNotRestart);
                $lid = 0;
                $elapsed = 0;
                $shutdownemptytime = 0;
                $notified = 0;

                $query = $sql->prepare("SELECT s.`id` AS `serverID`,t.`description`,t.`gamebinary`,g.* FROM `gsswitch` g INNER JOIN `serverlist` s ON g.`serverid`=s.`id` INNER JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE g.`id`=? LIMIT 1");
                $query2 = $sql->prepare("SELECT `id`,`started` FROM `lendedserver` WHERE `serverid`=? LIMIT 1");
                $query->execute(array($switchID));
                foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
                    $serverip = $row['serverip'];
                    $autoRestart = $row['autoRestart'];
                    $port = $row['port'];
                    $address = $row['serverip'] . ':' . $row['port'];
                    $gametype = $row['description'];
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

                    if ($lendserver == 'Y' and $lendActive == 'Y' and $resellersettings[$resellerid]['shutdownempty'] == 'Y') {
                        $shutdownemptytime = $resellersettings[$resellerid]['shutdownemptytime'];
                        $query2->execute(array($row['serverID']));
                        foreach ($query2->fetchall(PDO::FETCH_ASSOC) as $row2) {
                            $lid = $row2['id'];
                            $elapsed = round((strtotime('now') - strtotime($row2['started'])) / 60);
                        }
                    }
                }

                if ($v['gq_online'] == 1) {
                    $name = $v['gq_hostname'];
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

                $returnCmd = array();

                // Check lendserver specific settings
                if (isset($userid) and $lendserver == 'Y') {

                    // Running but no lend information in temp table
                    if ($v['gq_online'] == 1) {
                        $query = $sql->prepare("SELECT 1 FROM `lendedserver` WHERE `id`=? LIMIT 1");
                        $query->execute(array($lid));

                        if ($query->rowCount() == 0) {
                            print "Will stop lendserver $address because not lendet\r\n";
                            $stopserver = true;
                        }

                        if (!isset($stopserver) and $lendserver == 'Y' and $lendActive == 'Y' and $resellersettings[$resellerid]['shutdownempty'] == 'Y' and $elapsed > $shutdownemptytime and $numplayers == 0 and $maxplayers != 0 and $slots != 0) {
                            print "Will stop server $address after $elapsed minutes, because it is empty and threshold is $shutdownemptytime minutes \r\n";
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

                        if ($resellerid==0) {
                            $query = $sql->prepare("SELECT `id`,`mail_securitybreach` FROM `userdata` WHERE `id`=? OR (`resellerid`=0 AND `accounttype`='a')");
                            $query->execute(array($userid));

                        } else {
                            $query = $sql->prepare("SELECT `id`,`mail_securitybreach` FROM `userdata` WHERE `id`=? OR (`id`=? AND `accounttype`='r')");
                            $query->execute(array($userid, $resellerid));
                        }

                        foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
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

                        $tmp = gsrestart($switchID, 'so', $aeskey, $resellerid);
                        if (is_array($tmp)) {
                            foreach($tmp as $t) {
                                $returnCmd[] = $t;
                            }
                        }

                        $query = $sql->prepare("DELETE FROM `lendedserver` WHERE `serverid`=? AND `resellerid`=? AND `servertype`='g' LIMIT 1");
                        $query->execute(array($switchID, $resellerid));
                    }

                    if ($notified > 0) {
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
                            $tmp = gsrestart($switchID, 're', $aeskey, $resellerid);
                            if (is_array($tmp)) {
                                foreach($tmp as $t) {
                                    $returnCmd[] = $t;
                                }
                            }

                        } else {
                            print "Not Restarting: $address\r\n";
                        }

                        if ($notified == $resellersettings[$resellerid]['down_checks']) {
                            $query = $sql->prepare("SELECT `mail_serverdown` FROM `userdata` WHERE `id`=? LIMIT 1");
                            $query->execute(array($userid));
                            foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
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

                foreach($returnCmd as $t) {
                    $shellCmds[$rootID][] = $t;
                }
            }
        }

        unset($gq);

        if (isset($dbConnect['debug']) and $dbConnect['debug'] == 1) {
            print_r($shellCmds);
        }

        foreach($shellCmds as $k => $v) {
            ssh2_execute('gs', $k, $v);
        }
    }

    # Voice Server
    if ($checkTypeOfServer == 'all' or $checkTypeOfServer == 'vs') {
        #voice_tsdns

        print 'Checking TSDNS' . "\r\n";
        $query = $sql->prepare("SELECT *,AES_DECRYPT(`ssh2port`,:aeskey) AS `decryptedssh2port`,AES_DECRYPT(`ssh2user`,:aeskey) AS `decryptedssh2user`,AES_DECRYPT(`ssh2password`,:aeskey) AS `decryptedssh2password` FROM `voice_tsdns` WHERE `active`='Y'");
        $query->execute(array(':aeskey' => $aeskey));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $resellerid = $row['resellerid'];
            $tsdnscheck=@fsockopen ($row['ssh2ip'],41144, $errno, $errstr,5);
            if (!is_resource($tsdnscheck) and $row['autorestart'] == 'Y') {
                sleep(1);
                $tsdnscheck=@fsockopen ($row['ssh2ip'],41144, $errno, $errstr,5);
            }
            if (!is_resource($tsdnscheck) and $row['autorestart'] == 'Y') {
                print "TSDNS Error: ".$row['ssh2ip'] . ' ' . $errno.' ('.$errstr.")\r\n";
                $query3 = $sql->prepare("UPDATE `voice_tsdns` SET `notified`=`notified`+1 WHERE `id`=? LIMIT 1");
                $query3->execute(array($row['id']));
                $tsdnsDownCheck = $row['notified']+1;
                if ($tsdnsDownCheck == $resellersettings[$resellerid]['down_checks']) {
                    if ($resellerid==0) {
                        $query3 = $sql->prepare("SELECT `id`,`mail_serverdown` FROM `userdata` WHERE `accounttype`='a' AND `resellerid`=0");
                        $query3->execute();
                    } else {
                        $query3 = $sql->prepare("SELECT `id`,`mail_serverdown` FROM `userdata` WHERE `id`=? LIMIT 1");
                        $query3->execute(array($resellerid));
                    }
                    foreach ($query3->fetchAll(PDO::FETCH_ASSOC) as $row3) {
                        if ($row3['mail_serverdown'] == 'Y') {
                            sendmail('emaildownrestart', $row3['id'], $row['ssh2ip'].' (External TSDNS)','');
                        }
                    }
                }
                if ($row['bitversion'] == '32') {
                    $tsdnsbin='tsdnsserver_linux_x86';
                } else {
                    $tsdnsbin='tsdnsserver_linux_amd64';
                }

                $split_config=preg_split('/\//', $row['serverdir'], -1, PREG_SPLIT_NO_EMPTY);
                $folderfilecount=count($split_config)-1;
                $i = 0;
                unset($folders);
                $folders=(substr($row['serverdir'],0,1) == '/') ? 'cd  /' : 'cd ';
                $lastFolder = '';
                while ($i <= $folderfilecount) {
                    $folders = $folders.$split_config[$i] . '/';
                    $lastFolder = $split_config[$i];
                    $i++;
                }
                if ($folders == 'cd ') {
                    $folders = '';
                } else if ($lastFolder!='tsdns' or substr($row['serverdir'],0,1) != '/') {
                    $folders = $folders .'tsdns/ && ';
                } else {
                    $folders = $folders  . ' && ';
                }
                $ssh2cmd = $folders.'function r () { if [ "`ps fx | grep '.$tsdnsbin.' | grep -v grep`" == "" ]; then ./'.$tsdnsbin.' > /dev/null & else ./'.$tsdnsbin.' --update > /dev/null & fi }; r& ';

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

        $vselect = $sql->prepare("SELECT *,AES_DECRYPT(`querypassword`,:aeskey) AS `decryptedquerypassword`,AES_DECRYPT(`ssh2port`,:aeskey) AS `decryptedssh2port`,AES_DECRYPT(`ssh2user`,:aeskey) AS `decryptedssh2user`,AES_DECRYPT(`ssh2password`,:aeskey) AS `decryptedssh2password` FROM `voice_masterserver` WHERE `active`='Y'");
        $vselect->execute(array(':aeskey' => $aeskey));
        foreach ($vselect->fetchall(PDO::FETCH_ASSOC) as $vrow) {
            unset($connect_ssh2, $ssh2, $badLogin);
            $ts3masterid = $vrow['id'];
            $ts3masternotified = $vrow['notified'];
            $addedby = $vrow['addedby'];
            $queryport = $vrow['queryport'];
            $querypassword = $vrow['decryptedquerypassword'];
            $resellerid = $vrow['resellerid'];
            $autorestart = $vrow['autorestart'];

            if ($addedby == 2) {
                $queryip = $vrow['ssh2ip'];
            } else if ($addedby == 1) {
                $vselect2 = $sql->prepare("SELECT `ip` FROM `rserverdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $vselect2->execute(array($vrow['rootid'], $resellerid));
                $queryip = $vselect2->fetchColumn();
            }

            if ($vrow['type'] == 'ts3') {
                $tsdown = false;
                $tsdnsdown = false;
                $defaultwelcome = $vrow['defaultwelcome'];

                $default = array('virtualserver_hostbanner_url' => $vrow['defaulthostbanner_url'], 'virtualserver_hostbanner_gfx_url' => $vrow['defaulthostbanner_gfx_url'], 'virtualserver_hostbutton_tooltip' => $vrow['defaulthostbutton_tooltip'], 'virtualserver_hostbutton_url' => $vrow['defaulthostbutton_url'], 'virtualserver_hostbutton_gfx_url' => $vrow['defaulthostbutton_gfx_url'], 'defaultwelcome' => $vrow['defaultwelcome']);
                print "Connecting to TS3 server $queryip\r\n";
                $connection=new TS3($queryip, $queryport,'serveradmin', $querypassword,(isset($args['tsDebug']) and $args['tsDebug'] == 1) ? true : false);
                $errorcode = $connection->errorcode;
                if (strpos($errorcode,'error id=0') === false) {
                    $connection->CloseConnection();
                    unset($connection);
                    sleep(1);
                    $connection=new TS3($queryip, $queryport,'serveradmin', $querypassword);
                    $errorcode = $connection->errorcode;
                }
                if (strpos($errorcode,'error id=0') === false) {
                    $connection->CloseConnection();
                    unset($connection);
                }
                if (strpos($errorcode,'error id=0') === false) {
                    print "TS3 Query Error: ".$errorcode . "\r\n";
                    $tsdown = true;
                    $restartreturn="TS3";
                }
                if ($vrow['usedns'] == 'Y') {
                    $tsdnscheck=@fsockopen ($queryip,41144, $errno, $errstr,5);
                    if (!is_resource($tsdnscheck)) {
                        sleep(1);
                        $tsdnscheck=@fsockopen ($queryip,41144, $errno, $errstr,5);
                    }
                    if (!is_resource($tsdnscheck)) {
                        print "TSDNS Error: ".$errno.' ('.$errstr.")\r\n";
                        $tsdnsdown = true;
                        if (isset($restartreturn)) {
                            $restartreturn .=" and TSDNS";
                        } else {
                            $restartreturn="TSDNS";
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
                        foreach ($query2->fetchall(PDO::FETCH_ASSOC) as $row2) {
                            if ($row2['mail_serverdown'] == 'Y') {
                                sendmail('emaildownrestart', $row2['id'], $queryip.' ('.$restartreturn.')','');
                            }
                        }
                    }
                    $query2 = $sql->prepare("UPDATE `voice_server` SET `uptime`=0 WHERE `masterserver`=?");
                    $query2->execute(array($ts3masterid));
                    $query2 = $sql->prepare("UPDATE `voice_masterserver` SET `notified`=? WHERE `id`=? LIMIT 1");
                    $query2->execute(array($ts3masternotified, $ts3masterid));

                    if (($autorestart == 'Y' and $ts3masternotified >= $resellersettings[$resellerid]['down_checks'])) {

                        $cmds = array();

                        $split_config=preg_split('/\//', $vrow['serverdir'], -1, PREG_SPLIT_NO_EMPTY);
                        $folderfilecount=count($split_config)-1;
                        $i = 0;
                        $folders = (substr($vrow['serverdir'], 0, 1) == '/') ? 'cd  /' : 'cd ';
                        while ($i <= $folderfilecount) {
                            $folders = $folders.$split_config[$i] . '/';
                            $i++;
                        }

                        if ($folders == 'cd ') {
                            $folders = '';
                            $tsdnsFolders='cd tsdns && ';
                        } else {
                            $tsdnsFolders = $folders.'tsdns && ';
                            $folders = $folders . ' && ';
                        }

                        if ($vrow['bitversion'] == '32') {
                            $tsbin='ts3server_linux_x86';
                            $tsdnsbin='tsdnsserver_linux_x86';
                        } else {
                            $tsbin='ts3server_linux_amd64';
                            $tsdnsbin='tsdnsserver_linux_amd64';
                        }

                        if ($tsdown == true) {
                            $cmds[] = $folders.'function r () { if [ "`ps fx | grep '.$tsbin.' | grep -v grep`" == "" ]; then ./ts3server_startscript.sh start > /dev/null & else ./ts3server_startscript.sh restart > /dev/null & fi }; r& ';
                        }

                        if ($vrow['usedns'] == 'Y' and $tsdnsdown == true) {
                            $cmds[] = $tsdnsFolders.'function r () { if [ "`ps fx | grep '.$tsdnsbin.' | grep -v grep`" == "" ]; then ./'.$tsdnsbin.' > /dev/null & else ./'.$tsdnsbin.' --update > /dev/null & fi }; r& ';
                        }

                        if (count($cmds) > 0) {

                            if (ssh2_execute('vm', $ts3masterid, $cmds)) {
                                print "Restarting: $restartreturn $queryip\r\n";
                            } else {
                                print "Failed restarting: $restartreturn $queryip\r\n";
                            }

                            if (isset($dbConnect['debug']) and $dbConnect['debug'] == 1) {
                                print_r($cmds);
                            }
                        }
                    } else {
                        print "$restartreturn $queryip down but no Restart triggert\r\n";
                    }
                }

                if ($tsdown != true) {
                    if ($ts3masternotified>0) {
                        $pupdate = $sql->prepare("UPDATE `voice_masterserver` SET `notified`=0 WHERE `id`=? LIMIT 1");
                        $pupdate->execute(array($ts3masterid));
                    }

                    $serverlist = $connection->ServerList();

                    if (!isset($serverlist[0]['id']) or $serverlist[0]['id'] == 0) {

                        foreach ($serverlist as $server) {
                            unset($modbadserver);
                            $modbadserver = array();
                            $virtualserver_id = $server['virtualserver_id'];
                            $vs = $server['virtualserver_status'];
                            $uptime=(isset($server['virtualserver_uptime'])) ? $server['virtualserver_uptime'] : 0;
                            $newnotified = 'N';
                            unset($newtrafficdata, $newtraffic, $ts3id);
                            $vselect2 = $sql->prepare("SELECT * FROM `voice_server` WHERE `localserverid`=? AND `masterserver`=? AND `resellerid`=? AND `autoRestart`='Y' LIMIT 1");
                            $vselect2->execute(array($virtualserver_id, $vrow['id'], $resellerid));
                            foreach ($vselect2->fetchall(PDO::FETCH_ASSOC) as $vrow2) {
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
                                $notified = $vrow2['notified'];
                                $olduptime = $vrow2['uptime'];
                                $initialpassword = $vrow2['initialpassword'];
                                $maxtrafficmb = $vrow2['maxtraffic'];
                                $maxtraffic = $maxtrafficmb*1024;
                                $filetraffic=($vrow2['filetraffic'] == null) ? 0 : $vrow2['filetraffic'];
                                $lastfiletraffic=($vrow2['lastfiletraffic'] == null) ? 0 : $vrow2['lastfiletraffic'];
                                $newtrafficdata = $lastfiletraffic;
                                $newtraffic = $filetraffic;
                            }

                            if (isset($ts3id) and $vs == 'online' and $active == 'N') {
                                print "Inactive TS3 server $address running. Stopping it.\r\n";
                                $connection->StopServer($virtualserver_id);
                            } else if (isset($ts3id) and $vs == 'online' and $active == 'Y') {
                                unset($rulebreak, $changeSlots);
                                $queryName = $server['virtualserver_name'];
                                $usedslots = (isset($server['virtualserver_clientsonline'])) ? $server['virtualserver_clientsonline'] : 0;

                                $sd = $connection->ServerDetails($virtualserver_id);
                                $newtrafficdata=round(($sd['connection_filetransfer_bytes_sent_total']+$sd['connection_filetransfer_bytes_received_total']) / 1024);

                                if (isset($resellersettings[$resellerid]['firstchecktime']) and isset($resellersettings[$resellerid]['firstcheck']) and $resellersettings[$resellerid]['firstchecktime'] < $resellersettings[$resellerid]['firstcheck']) {
                                    $filetraffic = 0;
                                }

                                $newtraffic = 0;

                                if ($newtrafficdata > $lastfiletraffic) {
                                    $addedtraffic = $newtrafficdata - $lastfiletraffic;
                                    $newtraffic = $filetraffic + $addedtraffic;
                                } else if ($newtrafficdata == $lastfiletraffic) {
                                    $newtraffic = $filetraffic;
                                } else if ($newtrafficdata < $lastfiletraffic) {
                                    $addedtraffic = $newtrafficdata;
                                    $newtraffic = $filetraffic+$addedtraffic;
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
                                    $traffictext="and has now reached the traffic limit ".$newtrafficmb. '/' . $maxtrafficmb." MB";
                                    if (isset($rulebreak)) {
                                        $rulebreak .="<br />Traffic Limit".$newtrafficmb. '/' . $maxtrafficmb." MB";
                                    } else {
                                        $rulebreak = "<br />Traffic Limit".$newtrafficmb. '/' . $maxtrafficmb." MB";
                                    }
                                } else if ($maxtraffic>0 and $newtraffic>$maxtraffic and $sd['virtualserver_max_download_total_bandwidth']<2 and $sd['virtualserver_max_upload_total_bandwidth']<2) {
                                    $virtualserver_max_download_total_bandwidth = 1;
                                    $virtualserver_max_upload_total_bandwidth = 1;
                                    $traffictext="and has still reached the traffic limit ".$newtrafficmb. '/' . $maxtrafficmb." MB";
                                } else if ($maxtraffic>0) {
                                    $traffictext="and has not reached traffic limit ".$newtrafficmb. '/' . $maxtrafficmb." MB";
                                } else {
                                    $traffictext="and has traffic limit ".$newtrafficmb. '/' . $maxtrafficmb." MB";
                                }
                                if ($sd['virtualserver_max_download_total_bandwidth'] != $virtualserver_max_download_total_bandwidth or $sd['virtualserver_max_upload_total_bandwidth'] != $virtualserver_max_download_total_bandwidth) {
                                    $modbadserver['virtualserver_max_download_total_bandwidth'] = $virtualserver_max_download_total_bandwidth;
                                    $modbadserver['virtualserver_max_upload_total_bandwidth'] = $virtualserver_max_download_total_bandwidth;
                                }
                                if ($forceservertag == 'Y' and $resellersettings[$resellerid]['brandname'] != '' and strpos(strtolower($server['virtualserver_name']), strtolower($resellersettings[$resellerid]['brandname'])) === false) {
                                    print $vrow['type']." server $address illegal without servertag. The name converted to ISO-8859-1 is ".iconv('UTF-8','ISO-8859-1//TRANSLIT', $server['virtualserver_name']) . "\r\n";
                                    if (isset($rulebreak)) {
                                        $rulebreak .="<br />".$ssprache->noservertag;
                                    } else {
                                        $rulebreak = $ssprache->noservertag;
                                    }
                                    $modbadserver['virtualserver_name'] = $server['virtualserver_name'] . ' ' . $resellersettings[$resellerid]['brandname'];
                                }
                                if (isset($ts3id) and $forcebanner == 'Y') {
                                    foreach (array('virtualserver_hostbanner_url','virtualserver_hostbanner_gfx_url') as $param) {
                                        if ($default[$param] != '' and $sd[$param] != $default[$param]) {
                                            $modbadserver[$param] = $default[$param];
                                            print $vrow['type']." server $address $param != ".$default[$param].". The name converted to ISO-8859-1 is ".iconv('UTF-8','ISO-8859-1//TRANSLIT', $server['virtualserver_name']) . "\r\n";
                                            if (isset($rulebreak)) {
                                                $rulebreak .="<br />".$param . '  ' . $vosprache->isnot . '  ' . $default[$param];
                                            } else {
                                                $rulebreak = $param . '  ' . $vosprache->isnot . '  ' . $default[$param];
                                            }
                                        }
                                    }
                                }
                                if (isset($ts3id) and $forcebutton == 'Y') {
                                    foreach (array('virtualserver_hostbutton_tooltip','virtualserver_hostbutton_url','virtualserver_hostbutton_gfx_url') as $param) {
                                        if ($default[$param] != '' and $sd[$param] != $default[$param]) {
                                            $modbadserver[$param] = $default[$param];
                                            print $vrow['type']." server $address $param != ".$default[$param].". The name converted to ISO-8859-1 is ".iconv('UTF-8','ISO-8859-1//TRANSLIT', $server['virtualserver_name']) . "\r\n";
                                            if (isset($rulebreak)) {
                                                $rulebreak .="<br />".$param . '  ' . $vosprache->isnot . '  ' . $default[$param];
                                            } else {
                                                $rulebreak = $param . '  ' . $vosprache->isnot . '  ' . $default[$param];
                                            }
                                        }
                                    }
                                }
                                if (isset($ts3id) and $forcewelcome == 'Y' and $default['defaultwelcome'] != '' and $sd['virtualserver_welcomemessage'] != $default['defaultwelcome']) {
                                    $modbadserver['virtualserver_welcomemessage'] = $default['defaultwelcome'];
                                    print $vrow['type']." server $address $param != ".$default['defaultwelcome'].". The name converted to ISO-8859-1 is ".iconv('UTF-8','ISO-8859-1//TRANSLIT', $server['virtualserver_name']) . "\r\n";
                                    if (isset($rulebreak)) {
                                        $rulebreak .="<br />virtualserver_welcomemessage ".$vosprache->isnot . '  ' . $default['defaultwelcome'];
                                    } else {
                                        $rulebreak="virtualserver_welcomemessage ".$vosprache->isnot . '  ' . $default['defaultwelcome'];
                                    }
                                }
                                if (isset($ts3id, $lendserver) and $lendserver == 'N' and $slots<$server['virtualserver_maxclients']) {
                                    print $vrow['type']." server $address virtualserver_maxclients ${sd['virtualserver_maxclients']}!= ".$slots.". The name converted to ISO-8859-1 is ".iconv('UTF-8','ISO-8859-1//TRANSLIT', $server['virtualserver_name']) . "\r\n";
                                    if (isset($rulebreak)) {
                                        $rulebreak .="<br />virtualserver_maxclients ".$vosprache->isnot . '  ' . $slots;
                                    } else {
                                        $rulebreak="virtualserver_maxclients ".$vosprache->isnot . '  ' . $slots;
                                    }
                                }
                                if (isset($ts3id) and $password == 'Y' and $sd['virtualserver_flag_password'] != '1') {
                                    $modbadserver['virtualserver_password'] = $initialpassword;
                                    print $vrow['type']." server $address virtualserver_flag_password != 1. The name converted to ISO-8859-1 is ".iconv('UTF-8','ISO-8859-1//TRANSLIT', $server['virtualserver_name']) . "\r\n";
                                    if (isset($rulebreak)) {
                                        $rulebreak .="<br />virtualserver_flag_password ".$vosprache->isnot." 1";
                                    } else {
                                        $rulebreak="virtualserver_flag_password ".$vosprache->isnot." 1";
                                    }
                                }
                                if (isset($ts3id) and $lendserver == 'N' and !isset($rulebreak)) {
                                    if (isset($changeSlots)) {
                                        print $vrow['type']." server $address Changing Flex Slots to ${changeSlots}. The name converted to ISO-8859-1 is ".iconv('UTF-8','ISO-8859-1//TRANSLIT', $server['virtualserver_name']) . "\r\n";
                                        $connection->ImportModServer($virtualserver_id, $changeSlots, $vrow2['ip'], $vrow2['port'], array());
                                        $pupdate2 = $sql->prepare("UPDATE `voice_server` SET `notified`=0,`flexSlotsCurrent`=? WHERE `id`=? LIMIT 1");
                                        $pupdate2->execute(array($changeSlots, $ts3id));
                                    } else if ($notified>0) {
                                        $pupdate2 = $sql->prepare("UPDATE `voice_server` SET `notified`=0 WHERE `id`=? LIMIT 1");
                                        $pupdate2->execute(array($ts3id));
                                    }
                                    print $vrow['type']." server $address is running $traffictext. The name converted to ISO-8859-1 is ".iconv('UTF-8','ISO-8859-1//TRANSLIT', $server['virtualserver_name']) . "\r\n";
                                } else if (isset($ts3id) and $notified == 0 and isset($rulebreak)) {
                                    $connection->ImportModServer($virtualserver_id, $slots, $vrow2['ip'], $vrow2['port'], $modbadserver);
                                    if ($resellerid==0) {
                                        $query2 = $sql->prepare("SELECT `id`,`mail_securitybreach` FROM `userdata` WHERE `id`=? OR (`resellerid`=0 AND `accounttype`='a')");
                                        $query2->execute(array($userid));
                                    } else {
                                        $query2 = $sql->prepare("SELECT `id`,`mail_securitybreach` FROM `userdata` WHERE `id`=? OR (`id`=? AND `accounttype`='r')");
                                        $query2->execute(array($userid, $resellerid));
                                    }
                                    foreach ($query2->fetchall(PDO::FETCH_ASSOC) as $row2) {
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
                                $query = $sql->prepare("INSERT INTO `voice_server_stats` (`sid`,`mid`,`installed`,`used`,`date`,`uid`,`resellerid`) VALUES (?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `used`=(`used`*(`count`/(`count`+1))+(VALUES(`used`)*(1/(`count`+1)))),`installed`=(`installed`*(`count`/(`count`+1))+(VALUES(`installed`)*(1/(`count`+1)))),`count`=`count`+1");
                                $query->execute(array($ts3id, $ts3masterid, $server['virtualserver_maxclients'], $usedslots, $dayAndZeroHour, $userid, $resellerid));
                            } else if (isset($ts3id)) {
                                $uptime = 1;
                                $usedslots = 0;
                                if ($lendserver == 'Y' and $lendActive == 'Y') {
                                    $removedeadvoiceserver = $sql->prepare("DELETE FROM `lendedserver` WHERE `serverid`=? LIMIT 1");
                                    $removedeadvoiceserver->execute(array($ts3id));
                                } else if ($active == 'Y' and $vs != 'online' and $olduptime>1 and $olduptime != null) {
                                    $notified++;
                                    if ($notified>=$ts3masternotified == $resellersettings[$resellerid]['down_checks']){
                                        print "TS3 server $address not running. Starting it.\r\n";
                                        $connection->StartServer($virtualserver_id);
                                    }
                                    if ($notified == $resellersettings[$resellerid]['down_checks']) {
                                        $query2 = $sql->prepare("SELECT `mail_serverdown` FROM `userdata` WHERE `id`=? LIMIT 1");
                                        $query2->execute(array($userid));
                                        foreach ($query2->fetchall(PDO::FETCH_ASSOC) as $row2) {
                                            if ($row2['mail_serverdown'] == 'Y') sendmail('emaildownrestart', $userid, $address,'');
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
                                $query2 = $sql->prepare("UPDATE `voice_server` SET `usedslots`=?,`uptime`=?,`notified`=?,`filetraffic`=?,`lastfiletraffic`=?,`queryName`=?,`queryNumplayers`=?,`queryMaxplayers`=?,`queryPassword`=?,`queryUpdatetime`=NOW() WHERE `id`=? AND `resellerid`=? LIMIT 1");
                                $query2->execute(array($usedslots, $uptime, $newnotified, $newtraffic, $newtrafficdata, $queryName,((isset($server['virtualserver_clientsonline'])) ? $server['virtualserver_clientsonline'] : 0 - 1),(isset($server['virtualserver_maxclients'])) ? $server['virtualserver_maxclients'] : 0, $flagPassword, $ts3id, $resellerid));
                            }
                            if (isset($args['coolDown'])) {
                                $nano = time_nanosleep(0, $args['coolDown']);
                                if ($nano === true) {
                                    echo 'Slept for '.$args['coolDown'].' microseconds' . "\r\n";
                                } elseif ($nano === false) {
                                    echo 'Sleeping failed' . "\r\n";
                                } elseif (is_array($nano)) {
                                    echo 'Interrupted by a signal' . "\r\n";
                                    echo 'Time remaining: '.$nano['seconds'].' seconds, '.$nano['nanoseconds'].' nanoseconds' . "\r\n";
                                }
                            }
                        }
                    } else print "Error: ".$serverlist[0]['msg'] . "\r\n";
                }
                if (isset($connection)) {
                    $connection->CloseConnection();
                    sleep(1);
                }
            }
        }
    }
    flush();
    $query = $sql->prepare("UPDATE `settings` SET `lastCronStatus`=UNIX_TIMESTAMP()");
    $query->execute();
} else {
	header('Location: login.php');
	die('Statuscheck can only be run via console or a cronjob');
}