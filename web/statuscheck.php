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

ini_set('display_errors',1);
error_reporting(E_ALL|E_STRICT);
if (isset($_SERVER['REMOTE_ADDR'])) {
	$ip = $_SERVER['REMOTE_ADDR'];
    $timelimit=(isset($_GET['timeout']) and is_numeric($_GET['timeout'])) ? (int) $_GET['timeout'] : ini_get('max_execution_time')-10;
} else {
	$timelimit=600;
}
if (isset($argv)) {
    $args = array();
    foreach ($argv as $a) {
        if ($a == 'gs' or $a == 'vs') $checkTypeOfServer = $a;
        else if (is_numeric($a)) $sleep = $a;
        else {
            $e = explode(':', $a);
            if (isset($e[1])) $args[$e[0]] = $e[1];
        }
    }
}
if (isset($checkTypeOfServer)) {
    print ($checkTypeOfServer == 'gs') ? 'Checking Gameserver' . "\r\n" : 'Checking Voiceserver' . "\r\n";
} else {
    $checkTypeOfServer='all';
    print 'Checking Gameserver and Voiceserver' . "\r\n";
}
set_time_limit($timelimit);
if (!isset($ip) or $_SERVER['SERVER_ADDR'] == $ip) {
    define('EASYWIDIR', dirname(__FILE__));
	include(EASYWIDIR . '/stuff/vorlage.php');
	include(EASYWIDIR . '/stuff/functions.php');
	include(EASYWIDIR . '/stuff/class_validator.php');
	include(EASYWIDIR . '/stuff/settings.php');
	include(EASYWIDIR . '/stuff/ssh_exec.php');
	include(EASYWIDIR . '/stuff/class_voice.php');
	include(EASYWIDIR . '/stuff/queries.php');
    include(EASYWIDIR . '/stuff/keyphrasefile.php');
    $dayAndHour=date('Y-m-d H:').'00:00';
    $dayAndZeroHour=date('Y-m-d').' 00:00:00';
    $ssprache = getlanguagefile('settings','uk',0);
    $vosprache = getlanguagefile('voice','uk',0);
    $sprache = getlanguagefile('gserver','uk',0);

    # Pick up Reseller and Lend Settings
    $resellersettings = array();
    $query = $sql->prepare("SELECT `brandname`,`noservertag`,`nopassword`,`tohighslots`,`down_checks`,`resellerid` FROM `settings`");
    $query2 = $sql->prepare("SELECT `active`,`shutdownempty`,`shutdownemptytime`,`lastcheck`,`oldcheck` FROM `lendsettings` WHERE `resellerid`=? LIMIT 1");
    $query->execute();
    foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
        unset($active);
        $resellerid = $row['resellerid'];
        $query2->execute(array($resellerid));
        foreach ($query2->fetchall(PDO::FETCH_ASSOC) as $row2) {
            $active = $row2['active'];
            $shutdownempty = $row2['shutdownempty'];
            $shutdownemptytime = $row2['shutdownemptytime'];
            $firstcheck='00-00-'.round(2*(strtotime($row2['lastcheck'])-strtotime($row2['oldcheck']))/60);
            $firstchecktime=date('d-G-i');
        }
        if (isset($active)) $resellersettings[$resellerid] = array('active' => $active,'shutdownempty' => $shutdownempty,'shutdownemptytime' => $shutdownemptytime,'firstchecktime' => $firstchecktime,'firstcheck' => $firstcheck,'brandname' => $row['brandname'], 'noservertag' => $row['noservertag'], 'nopassword' => $row['nopassword'], 'tohighslots' => $row['tohighslots'], 'down_checks' => $row['down_checks']);
    }
    $query=$sql->prepare("UPDATE `lendsettings` SET `oldcheck`=`lastcheck`,`lastcheck`=NOW()");
    $query->execute();

    # Game Server
    if ($checkTypeOfServer == 'all' or $checkTypeOfServer == 'gs') {
        function statushandle() {
            global $userid, $resellersettings, $resellerid, $serverid, $logdate, $aeskey, $address, $gametype, $war, $status, $password, $lendserver, $elapsed, $shutdownemptytime, $numplayers, $maxplayers, $slots, $brandname, $name, $map, $secnotified, $notified, $sql, $ssprache, $lid;
            list($serverip, $port) = explode(':', $address);
            $returnCmd = array();
            if ($status == 'UP') {
                if ($lendserver == 'Y' and $resellersettings[$resellerid]['active'] == 'Y' and $resellersettings[$resellerid]['shutdownempty'] == 'Y' and $elapsed>$shutdownemptytime and $numplayers == '0' and $maxplayers != '0' and $slots != '0') {
                    print "Will stop server $address before time is up, because it is empty\r\n";
                    $stopserver = true;
                } else if ($war == 'Y' and $password == 'N') {
                    if ($resellersettings[$resellerid]['nopassword'] == '1') {
                        $stopserver = true;
                        print "Will stop server $address because running without password. The name converted to ISO-8859-1 is ".iconv('UTF-8','ISO-8859-1//TRANSLIT', $name).".\r\n";
                    } else {
                        print "Server with address $address is running as $gametype and illegal without password. The name converted to ISO-8859-1 is ".iconv('UTF-8','ISO-8859-1//TRANSLIT', $name).".\r\n";
                    }
                    $rulebreak=$ssprache->nopassword;
                }
                if ($maxplayers > $slots) {
                    if ($resellersettings[$resellerid]['tohighslots'] == '1') {
                        $stopserver = true;
                        print "Will stop server $address because running with to much slots. The name converted to ISO-8859-1 is ".iconv('UTF-8','ISO-8859-1//TRANSLIT', $name).".\r\n";
                    } else {
                        print "Server $address is running as $gametype and with illegal slotamount. The name converted to ISO-8859-1 is ".iconv('UTF-8','ISO-8859-1//TRANSLIT', $name).".\r\n";
                    }
                    if (isset($rulebreak)) {
                        $rulebreak .="<br />".$ssprache->tohighslots;
                    } else {
                        $rulebreak=$ssprache->tohighslots;
                    }
                }
                if ($brandname == 'Y' and $resellersettings[$resellerid]['brandname'] != '' and strpos(strtolower($name),strtolower($resellersettings[$resellerid]['brandname'])) === false) {
                    if ($resellersettings[$resellerid]['noservertag'] == '1') {
                        $stopserver = true;
                        print "Will stop server $address because running without servertag. The name converted to ISO-8859-1 is ".iconv('UTF-8','ISO-8859-1//TRANSLIT', $name).".\r\n";
                    } else {
                        print "Server $address is running as $gametype and illegal without servertag. The name converted to ISO-8859-1 is ".iconv('UTF-8','ISO-8859-1//TRANSLIT', $name).".\r\n";
                    }
                    if (isset($rulebreak)) {
                        $rulebreak .="<br />".$ssprache->noservertag;
                    } else {
                        $rulebreak=$ssprache->noservertag;
                    }
                }
                if (!isset($rulebreak)) {
                    print "Server $address is running as $gametype. The name converted to ISO-8859-1 is ".iconv('UTF-8','ISO-8859-1//TRANSLIT', $name).".\r\n";
                }
                if ($secnotified == 'N' and isset($rulebreak)) {
                    if ($resellerid==0) {
                        $query = $sql->prepare("SELECT `id`,`mail_securitybreach` FROM `userdata` WHERE `id`=? OR (`resellerid`='0' AND `accounttype`='a')");
                        $query->execute(array($userid));
                    } else {
                        $query = $sql->prepare("SELECT `id`,`mail_securitybreach` FROM `userdata` WHERE `id`=? OR (`id`=? AND `accounttype`='r')");
                        $query->execute(array($userid, $resellerid));
                    }
                    foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
                        if ($row['mail_securitybreach'] == 'Y') {
                            sendmail('emailsecuritybreach', $row['id'], $address, $rulebreak);
                        }
                    }
                    $query = $sql->prepare("UPDATE `gsswitch` SET `secnotified`='Y' WHERE `serverip`=? AND `port`=? LIMIT 1");
                    $query->execute(array($serverip, $port));
                }
                if ($secnotified == 'Y' and !isset($rulebreak)) {
                    $query = $sql->prepare("UPDATE `gsswitch` SET `secnotified`='N' WHERE `serverip`=? AND `port`=? LIMIT 1");
                    $query->execute(array($serverip, $port));
                }
                if (isset($stopserver)) {
                    $tmp = gsrestart($serverid,'so', $aeskey, $resellerid);
                    if (is_array($tmp)) {
                        foreach($tmp as $t) {
                            $returnCmd[] = $t;
                        }
                    }
                    $numplayers = 0;
                    $map = '';
                    $query = $sql->prepare("DELETE FROM `lendedserver` WHERE `serverid`=? AND `resellerid`=? AND `servertype`='g' LIMIT 1");
                    $query->execute(array($serverid, $resellerid));
                }
                if ($notified>0) {
                    $query = $sql->prepare("UPDATE `gsswitch` SET `notified`='0' WHERE `serverip`=? AND `port`=? LIMIT 1");
                    $query->execute(array($serverip, $port));
                }
            } else {
                $name = 'OFFLINE';
                $numplayers = 0;
                $maxplayers = 0;
                $map = '';
                $password = 'Y';
                unset($donotrestart);
                if ($lendserver == 'Y' and $resellersettings[$resellerid]['active'] == 'Y' and $resellersettings[$resellerid]['shutdownempty'] == 'Y') {
                    $query = $sql->prepare("SELECT `started`,`lendtime` FROM `lendedserver` WHERE `id`=? LIMIT 1");
                    $query->execute(array($lid));
                    foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
                        $timeleft=round($row['lendtime']-(strtotime('now')-strtotime($row['started']))/60);
                        if ($timeleft>=$shutdownemptytime) {
                            $query = $sql->prepare("DELETE FROM `lendedserver` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                            $query->execute(array($lid, $resellerid));
                            $query = $sql->prepare("SELECT `switchID` FROM `serverlist` WHERE `id`=? LIMIT 1");
                            $query->execute(array($serverid));
                            $restartID = $query->fetchColumn();
                            $tmp = gsrestart($restartID, 'so', $aeskey, $resellerid);
                            if (is_array($tmp)) {
                                foreach($tmp as $t) {
                                    $returnCmd[] = $t;
                                }
                            }
                            print "Stopping server $address before time is up, because it is crashed\r\n";
                            $donotrestart = true;
                        }
                    }
                }
                if (!isset($donotrestart)) {
                    $notified++;
                    $query = $sql->prepare("SELECT `autoRestart` FROM `gsswitch` WHERE `serverip`=? and `port`=? LIMIT 1");
                    $query->execute(array($serverip, $port));
                    if ($query->fetchColumn() == 'Y' and $notified>=$resellersettings[$resellerid]['down_checks']) {
                        print "Restarting: $address\r\n";
                        $tmp = gsrestart($serverid,'re', $aeskey, $resellerid);
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
                            if ($row['mail_serverdown'] == 'Y') sendmail('emaildownrestart', $userid, $address,'');
                        }
                    }
                }
            }
            $query = $sql->prepare("UPDATE `gsswitch` SET `queryName`=?,`queryNumplayers`=?,`queryMaxplayers`=?,`queryMap`=?,`queryPassword`=?,`queryUpdatetime`=?,`notified`=? WHERE `serverip`=? and `port`=? LIMIT 1");
            $query->execute(array($name, $numplayers, $maxplayers, $map, $password, $logdate, $notified, $serverip, $port));
            return $returnCmd;
        }
        $query = $sql->prepare("SELECT `id`,`serverid`,`started`,`lendtime`,`resellerid` FROM `lendedserver` WHERE `servertype`='g'");
        $query2 = $sql->prepare("SELECT g.`rootID` FROM `serverlist` s INNER JOIN `gsswitch` g ON s.`switchID`=g.`id` WHERE s.`id`=? LIMIT 1");
        $query->execute();
        $rtmp = array();
        foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
            $id = $row['id'];
            $serverid = $row['serverid'];
            $lendtime = $row['lendtime'];
            $resellerid = $row['resellerid'];
            $timeleft = round($row['lendtime'] - (strtotime('now') - strtotime($row['started'])) / 60);
            if ($timeleft <= 0) {
                $query2->execute(array($serverid));
                $rootID= (int) $query2->fetchColumn();
                $tmp = gsrestart($serverid,'so', $aeskey, $resellerid);
                if (is_array($tmp)) foreach($tmp as $t) $rtmp[$rootID][] = $t;
                $query2 = $sql->prepare("DELETE FROM `lendedserver` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query2->execute(array($id, $resellerid));
                print "Time is up, stopping lendserver: $id\r\n";
            } else {
                print "Lendserver $serverid has $timeleft minutes left\r\n";
            }
        }
        foreach ($rtmp as $k=>$v) if (count($v)>0) ssh2_execute('gs', $k, $v);
        $other = array();
        $i = 1;
        $totalcount = 0;
        $queries = array();
        $query = $sql->prepare("SELECT g.`id`,g.`serverid`,g.`serverip`,g.`port`,t.`qstat` FROM `gsswitch` g INNER JOIN `serverlist` s ON g.`serverid`=s.`id` INNER JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE g.`stopped`='N' AND g.`active`='Y'");
        $query->execute();
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $qstat = $row['qstat'];
            $serverip = $row['serverip'];
            $port = $row['port'];
            $server = $serverip . ':' . $port;
            if (!in_array($qstat, array('', null, false))) {
                if (in_array($qstat, array('minecraft', 'tm', 'gtasamp', 'teeworlds', 'mtasa'))) {
                    $other[] = array('qstat' => $qstat, 'switchID' => $row['id']);
                } else {
                    $queries[] = '-' . $qstat . ' ' . $server;
                    $i++;
                }
                if ($i == 50) {
                    $querry_array[] = implode(' ', $queries);
                    $queries = array();
                    $i = 1;
                }
                $totalcount++;
            }
        }
        $querry_array[] = implode(' ', $queries);
        print "Checking $totalcount server\r\n";
        $shellCmds = array();
        foreach ($querry_array as $querystring) {
            print "The Quakestat Querystring is: ".$querystring . "\r\n";
            unset($xmlquakestring);
            $xml = array();
            ob_start();
            if ($querystring != '') {
                passthru(escapeshellcmd("/usr/bin/quakestat -xml -R -utf8 $querystring -sort i"));
                $xmlquakestring=ob_get_contents();
            }
            ob_end_clean();
            if (isset($xmlquakestring)) {
                $xml=@simplexml_load_string($xmlquakestring);
            }
            if (!is_array($xml) and !is_object($xml)) {
                $xml = array();
            }
            unset($badstatus);
            unset($badquery);
            unset($badxml);
            unset($badquerystring);
            foreach ($xml as $xml2) {
                $address = $xml2['address'];
                list($ip, $port) = explode(':', $address);
                $query = $sql->prepare("SELECT t.`qstat` FROM `gsswitch` g INNER JOIN `serverlist` s ON g.`serverid`=s.`id` INNER JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE g.`serverip`=? AND g.`port`=?  AND g.`active`='Y' LIMIT 1");
                $query->execute(array($ip, $port));
                $qstat = $query->fetchColumn();
                if ($xml2['status'] == 'DOWN' or $xml2['status'] == 'TIMEOUT') {
                    if (isset($badquery)) {
                        $badquery .= ' -' . $qstat . ' ' . $address;
                    } else {
                        $badquery = ' -' . $qstat . ' ' . $address;
                    }
                }
            }
            $badstatus = array();
            if (isset($badquery) and $badquery != '') {
                print "The recheck Querystring is: $badquery\r\n";
                ob_start();
                passthru(escapeshellcmd("/usr/bin/quakestat -xml -R -utf8 $badquery -sort i"));
                $badquerystring=ob_get_contents();
                ob_end_clean();
                $badxml=simplexml_load_string($badquerystring);
                foreach ($badxml as $badxml2) {
                    if (isset($badxml2['address']) and isip($badxml2['address'], 'ipx')) {
                        $address = $badxml2['address'];
                        $status = $badxml2['status'];
                        $badstatus[$address] = array('status' => $status);
                        if ($badxml2['status'] == 'UP') {
                            if ($badxml2['type'] != 'A2S') {
                                $gametype = $badxml2->gametype;
                            }
                            foreach ($badxml2->rules->rule as $rule) {
                                switch((string) $rule['name']) {
                                    case 'gamename':
                                        $gametype = $rule;
                                        break;
                                }
                            }
                            $name = $badxml2->name;
                            $numplayers = $badxml2->numplayers;
                            $maxplayers = $badxml2->maxplayers;
                            $map = $badxml2->map;
                            $badstatus[$address] = array('gametype' => $gametype,'name' => $name,'numplayers' => $numplayers,'maxplayers' => $maxplayers,'map' => $map,'rules' => $badxml2->rules->rule);
                        }
                    }
                }
            }
            if (!is_array($xml) and !is_object($xml)) $xml = array();
            foreach ($xml as $xml2) {
                $lid = 0;
                unset($war);
                $address = $xml2['address'];
                $password = '';
                $addressarray = explode(':', $address);
                $ip = $addressarray[0];
                $port = $addressarray[1];
                $query = $sql->prepare("SELECT g.*,t.`shorten`,t.`qstat` FROM `gsswitch` g INNER JOIN `serverlist` s ON g.`serverid`=s.`id` INNER JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE g.`serverip`=? AND g.`port`=? LIMIT 1");
                $query->execute(array($ip, $port));
                foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
                    $serverid = $row['id'];
                    $rootID = $row['rootID'];
                    $resellerid = $row['resellerid'];
                    $lendserver = $row['lendserver'];
                    $notified = $row['notified'];
                    $secnotified = $row['secnotified'];
                    $userid = $row['userid'];
                    $qstat = $row['qstat'];
                    $shorten = $row['shorten'];
                    $brandname = $row['brandname'];
                    $slots = $row['slots'];
                    $war = $row['war'];
                    if ($row['tvenable'] == 'Y') {
                        $slots++;
                    }
                    if ($lendserver == 'Y' and $resellersettings[$resellerid]['active'] == 'Y' and $resellersettings[$resellerid]['shutdownempty'] == 'Y') {
                        $shutdownemptytime = $resellersettings[$resellerid]['shutdownemptytime'];
                        $query2 = $sql->prepare("SELECT `id`,`started` FROM `lendedserver` WHERE `serverid`=? LIMIT 1");
                        $query2->execute(array($serverid));
                        foreach ($query2->fetchall(PDO::FETCH_ASSOC) as $row2) {
                            $lid = $row2['id'];
                            $elapsed=round((strtotime('now')-strtotime($row2['started']))/60);
                        }
                    }
                }
                $query = $sql->prepare("SELECT `qstatpassparam` FROM `servertypes` WHERE `shorten`=? LIMIT 1");
                $query->execute(array($shorten));
                foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
                    $qstatpassparam = $row['qstatpassparam'];
                    $passparams = explode(':', $qstatpassparam);
                }
                unset($password, $rulebreak, $maxplayers, $name);
                if ((!isset($xml2['status']) or $xml2['status'] == 'DOWN' or $xml2['status'] == 'TIMEOUT') and (!isset($badstatus[$address]['status']) or $badstatus[$address]['status'] == 'DOWN' or $badstatus[$address]['status'] == 'TIMEOUT')) {
                    $status='DOWN';
                    $name = '';
                    $numplayers = 0;
                    $maxplayers = 0;
                    $map = '';
                    $gametype = '';
                    print "recheck status for $address is still: $status\r\n";
                } else if ((!$xml2['status'] or $xml2['status'] == 'DOWN' or $xml2['status'] == 'TIMEOUT') and isset($badstatus[$address]['status']) and $badstatus[$address]['status'] == 'UP') {
                    $status = 'UP';
                    foreach ($badstatus[$address]['rules'] as $rule) {
                        switch((string) $rule['name']) {
                            case $passparams[0]:
                                if ($rule == $passparams[1]) $password = 'Y';
                                else $password = 'N';
                                break;
                        }
                    }
                    $name = $badstatus[$address]['name'];
                    $numplayers = $badstatus[$address]['numplayers'];
                    $maxplayers = $badstatus[$address]['maxplayers'];
                    $map = $badstatus[$address]['map'];
                    $gametype = $badstatus[$address]['gametype'];
                } else {
                    $status = 'UP';
                    $name = $xml2->name;
                    $numplayers = $xml2->numplayers;
                    $maxplayers = $xml2->maxplayers;
                    $map = $xml2->map;
                    $type = $xml2['type'];
                    if ($type != 'A2S') {
                        $gametype = $xml2->gametype;
                    }
                    foreach ($xml2->rules->rule as $rule) {
                        switch((string) $rule['name']) {
                            case 'gamename':
                                $gametype = $rule;
                                break;
                            case $passparams[0]:
                                if ($rule == $passparams[1]) $password = 'Y';
                                else $password = 'N';
                                break;
                        }
                    }
                }
                if (!isset($password) or $password == '') {
                    $password = 'N';
                }
                if (!isset($elapsed)) {
                    $elapsed = 0;
                }
                if (!isset($shutdownemptytime)) {
                    $shutdownemptytime = 0;
                }
                if (isset($war)) {
                    $tmp = statushandle();
                    if (is_array($tmp)) {
                        foreach($tmp as $t) {
                            $shellCmds[$rootID][] = $t;
                        }
                    }
                }
            }
        }
        print "Checking Gameserver with Easy-Wi query:\r\n";
        foreach ($other as $array) {
            unset($userid, $serverid);
            $lid = 0;
            $qstat = $array['qstat'];
            $serverid = $array['switchID'];
            $query = $sql->prepare("SELECT s.`id`,t.`description`,g.`serverip`,g.`port`,g.`port2`,g.`slots`,g.`war`,g.`brandname`,g.`secnotified`,g.`notified`,g.`lendserver`,g.`userid`,g.`resellerid`,g.`rootID` FROM `gsswitch` g INNER JOIN `serverlist` s ON g.`serverid`=s.`id` INNER JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE g.`id`=? LIMIT 1");
            $query2=$sql->prepare("SELECT `id`,`started` FROM `lendedserver` WHERE `serverid`=? LIMIT 1");
            $query->execute(array($serverid));
            foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
                $serverip = $row['serverip'];
                $port = $row['port'];
                $address = $row['serverip'] . ':' . $row['port'];
                $gametype = $row['description'];
                $notified = $row['notified'];
                $secnotified = $row['secnotified'];
                $lendserver = $row['lendserver'];
                $slots = $row['slots'];
                $userid = $row['userid'];
                $resellerid = $row['resellerid'];
                $brandname = $row['brandname'];
                $rootID = $row['rootID'];
                $war = $row['war'];
                if ($lendserver == 'Y' and $resellersettings[$resellerid]['active'] == 'Y' and $resellersettings[$resellerid]['shutdownempty'] == 'Y') {
                    $shutdownemptytime = $resellersettings[$resellerid]['shutdownemptytime'];
                    $query2->execute(array($row['id']));
                    foreach ($query2->fetchall(PDO::FETCH_ASSOC) as $row2) {
                        $lid = $row2['id'];
                        $elapsed = round((strtotime('now') - strtotime($row2['started'])) / 60);
                    }
                }
            }
            if (isset($userid) and isset($serverid) and isset($port) and isset($qstat) and isset($serverip)) {
                $status = 'UP';
                if (in_array($qstat, array('gtasamp', 'minecraft', 'teeworlds', 'mtasa'))) {
                    echo "$qstat\r\n";
                    $query = ($qstat == 'mtasa') ? serverQuery($serverip, ($port + 123), $qstat) : serverQuery($serverip, $port, $qstat);
                    if (is_array($query)) {
                        $name = $query['hostname'];
                        $password = ($query['password'] == 1) ? 'Y' : 'N';
                        $numplayers = $query['players'];
                        $maxplayers = $query['slots'];
                        $map = $query['map'];
                    } else {
                        $status = 'DOWN';
                        $name = $gametype . 'OFFLINE';
                        $numplayers = 0;
                        $maxplayers = 0;
                        $map = '';
                        $password = 'Y';
                    }
                } else {
                    $brandname = 'N';
                    $name = $gametype . 'ONLINE';
                    $numplayers = 0;
                    $maxplayers = 0;
                    $map = '';
                    $password = 'Y';
                }
                if (!isset($elapsed)) {
                    $elapsed = 0;
                }
                if (!isset($shutdownemptytime)) {
                    $shutdownemptytime = 0;
                }
                $tmp = statushandle();
                if (is_array($tmp)) {
                    foreach($tmp as $t) {
                        $shellCmds[$rootID][] = $t;
                    }
                }
            }
        }
        foreach($shellCmds as $k=>$v) {
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
                $ssh2_2 = false;
                if ($row['publickey'] == 'Y') {

                    # https://github.com/easy-wi/developer/issues/70
                    $sshkey=removePub($row['keyname']);
                    $pubkey='keys/'.$sshkey.'.pub';
                    $key='keys/'.$sshkey;

                    if (file_exists($pubkey) and file_exists($key)) {
                        $ssh2_2= ssh2_connect($row['ssh2ip'], $row['decryptedssh2port'], array('hostkey' => 'ssh-rsa'));
                    }
                } else {
                    $ssh2_2=ssh2_connect($row['ssh2ip'], $row['decryptedssh2port']);
                }
                if ($ssh2_2==true) {
                    $connect_ssh2_2=($row['publickey'] == 'Y') ? @ssh2_auth_pubkey_file($ssh2_2, $row['decryptedssh2user'], $pubkey, $key) : @ssh2_auth_password($ssh2_2, $row['decryptedssh2user'], $row['decryptedssh2password']);
                    if ($connect_ssh2_2==true) {
                        $split_config=preg_split('/\//', $row['serverdir'], -1, PREG_SPLIT_NO_EMPTY);
                        $folderfilecount=count($split_config)-1;
                        $i = 0;
                        unset($folders);
                        $folders=(substr($row['serverdir'],0,1) == '/') ? 'cd  /' : 'cd ';
                        $lastFolder = '';
                        while ($i <= $folderfilecount) {
                            $folders = $folders.$split_config[$i]."/";
                            $lastFolder = $split_config[$i];
                            $i++;
                        }
                        if ($folders == 'cd ') {
                            $folders = '';
                        } else if ($lastFolder!='tsdns' or substr($row['serverdir'],0,1) != '/') {
                            $folders = $folders .'tsdns/ && ';
                        } else {
                            $folders = $folders .' && ';
                        }
                        $ssh2cmd = $folders.'function r () { if [ "`ps fx | grep '.$tsdnsbin.' | grep -v grep`" == "" ]; then ./'.$tsdnsbin.' > /dev/null & else ./'.$tsdnsbin.' --update > /dev/null & fi }; r& ';
                        echo $ssh2cmd . "\r\n";
                        echo ssh2_exec($ssh2_2, $ssh2cmd) . "\r\n";
                        $ssh2_2=null;
                    } else {
                        print "Error: Bad logindata for external tsdns ".$row['ssh2ip'] . "\r\n";
                    }
                } else {
                    print "Error: Can not connect to external tsdns server ".$row['ssh2ip']." via ssh2\r\n";
                }
            } else {
                print "TSDNS ${row['ssh2ip']} is up and running\r\n";
                $query3 = $sql->prepare("UPDATE `voice_tsdns` SET `notified`=0 WHERE `id`=? LIMIT 1");
                $query3->execute(array($row['id']));
            }
        }

        /* Voice Server */
        if ((isset($args['tsDebug']) and $args['tsDebug'] == 1)) print "Checking voice server with debug on\r\n";
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
            if ($addedby == '2') {
                $queryip = $vrow['ssh2ip'];
            } else if ($addedby == '1') {
                $vselect2=$sql->prepare("SELECT `ip` FROM `rserverdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $vselect2->execute(array($vrow['rootid'], $resellerid));
                foreach ($vselect2->fetchall(PDO::FETCH_ASSOC) as $vrow2) $queryip = $vrow2['ip'];
            }
            if ($vrow['type'] == 'ts3') {
                $tsdown = false;
                $tsdnsdown = false;
                $defaultwelcome = $vrow['defaultwelcome'];
                $default=array('virtualserver_hostbanner_url' => $vrow['defaulthostbanner_url'], 'virtualserver_hostbanner_gfx_url' => $vrow['defaulthostbanner_gfx_url'], 'virtualserver_hostbutton_tooltip' => $vrow['defaulthostbutton_tooltip'], 'virtualserver_hostbutton_url' => $vrow['defaulthostbutton_url'], 'virtualserver_hostbutton_gfx_url' => $vrow['defaulthostbutton_gfx_url'], 'defaultwelcome' => $vrow['defaultwelcome']);
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
                if ($tsdown==true or $tsdnsdown==true) {
                    $ts3masternotified++;
                    if ($ts3masternotified == $resellersettings[$resellerid]['down_checks']) {
                        if ($resellerid == '0') {
                            $query2 = $sql->prepare("SELECT `id`,`mail_serverdown` FROM `userdata` WHERE `accounttype`='a' AND `resellerid`='0'");
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
                    $query2=$sql->prepare("UPDATE `voice_server` SET `uptime`='0' WHERE `masterserver`=?");
                    $query2->execute(array($ts3masterid));
                    $query2=$sql->prepare("UPDATE `voice_masterserver` SET `notified`=? WHERE `id`=? LIMIT 1");
                    $query2->execute(array($ts3masternotified, $ts3masterid));
                    if (($autorestart == 'Y' and $ts3masternotified>=$resellersettings[$resellerid]['down_checks']) or ($tsdown!=true and $tsdnsdown==true)) {
                        if ($vrow['publickey'] == 'Y') {

                            # https://github.com/easy-wi/developer/issues/70
                            $sshkey=removePub($vrow['keyname']);
                            $pubkey=EASYWIDIR . '/keys/'.$sshkey.'.pub';
                            $key=EASYWIDIR . '/keys/'.$sshkey;

                            $ssh2=(file_exists($pubkey) and file_exists($key)) ? @ssh2_connect($queryip, $vrow['decryptedssh2port'], array('hostkey' => 'ssh-rsa')) : false;
                        } else {
                            $ssh2= @ssh2_connect($queryip, $vrow['decryptedssh2port']);
                        }
                        if ($ssh2) {
                            if ($vrow['publickey'] == 'Y') {
                                $connect_ssh2= @ssh2_auth_pubkey_file($ssh2, $vrow['decryptedssh2user'], $pubkey, $key);
                            } else {
                                $connect_ssh2= @ssh2_auth_password($ssh2, $vrow['decryptedssh2user'], $vrow['decryptedssh2password']);
                            }
                            if ($connect_ssh2) {
                                $split_config=preg_split('/\//', $vrow['serverdir'], -1, PREG_SPLIT_NO_EMPTY);
                                $folderfilecount=count($split_config)-1;
                                $i = 0;
                                $folders = (substr($vrow['serverdir'], 0, 1) == '/') ? 'cd  /' : 'cd ';
                                while ($i <= $folderfilecount) {
                                    $folders = $folders.$split_config[$i]."/";
                                    $i++;
                                }
                                if ($folders == 'cd ') {
                                    $folders = '';
                                    $tsdnsFolders='cd tsdns && ';
                                } else {
                                    $tsdnsFolders = $folders.'tsdns && ';
                                    $folders = $folders.' && ';
                                }
                                if ($vrow['bitversion'] == '32') {
                                    $tsbin='ts3server_linux_x86';
                                    $tsdnsbin='tsdnsserver_linux_x86';
                                } else {
                                    $tsbin='ts3server_linux_amd64';
                                    $tsdnsbin='tsdnsserver_linux_amd64';
                                }
                                $ssh2cmd = $folders.'function r () { if [ "`ps fx | grep '.$tsbin.' | grep -v grep`" == "" ]; then ./ts3server_startscript.sh start > /dev/null & else ./ts3server_startscript.sh restart > /dev/null & fi }; r& ';
                                if ($vrow['usedns'] == 'Y') {
                                    $tsndsserver=" and TSDNS";
                                    $ssh2cmd2=$tsdnsFolders.'function r () { if [ "`ps fx | grep '.$tsdnsbin.' | grep -v grep`" == "" ]; then ./'.$tsdnsbin.' > /dev/null & else ./'.$tsdnsbin.' --update > /dev/null & fi }; r& ';
                                }
                                if ($tsdown==true) {
                                    echo $ssh2cmd . "\r\n";
                                    echo ssh2_exec($ssh2, $ssh2cmd);
                                }
                                if ($tsdnsdown==true) {
                                    echo $ssh2cmd2 . "\r\n";
                                    echo ssh2_exec($ssh2, $ssh2cmd2);
                                }
                                print 'Restarting: '.$restartreturn . "\r\n";
                            } else {
                                print "Error: Bad logindata\r\n";
                            }
                        } else {
                            print "Error: Can not connect via ssh2\r\n";
                        }
                    } else {
                        print "Down but no Restart triggert\r\n";
                    }
                }
                if ($tsdown!=true) {
                    if ($ts3masternotified>0) {
                        $pupdate = $sql->prepare("UPDATE `voice_masterserver` SET `notified`='0' WHERE `id`=? LIMIT 1");
                        $pupdate->execute(array($ts3masterid));
                    }
                    $serverlist = $connection->ServerList();
                    if (!isset($serverlist[0]['id']) or $serverlist[0]['id'] == '0') {
                        foreach ($serverlist as $server) {
                            unset($modbadserver);
                            $modbadserver = array();
                            $virtualserver_id = $server['virtualserver_id'];
                            $vs = $server['virtualserver_status'];
                            $uptime=(isset($server['virtualserver_uptime'])) ? $server['virtualserver_uptime'] : 0;
                            $newnotified = 'N';
                            unset($newtrafficdata, $newtraffic, $ts3id);
                            $vselect2=$sql->prepare("SELECT * FROM `voice_server` WHERE `localserverid`=? AND `masterserver`=? AND `resellerid`=? AND `autoRestart`='Y' LIMIT 1");
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
                                $queryName = $server['virtualserver_name'];
                                $usedslots=(isset($server['virtualserver_clientsonline'])) ? $server['virtualserver_clientsonline'] : 0 - (isset($server['virtualserver_queryclientsonline'])) ? $server['virtualserver_queryclientsonline'] : 0;
                                $sd = $connection->ServerDetails($virtualserver_id);
                                unset($rulebreak, $changeSlots);
                                $newtrafficdata=round(($sd['connection_filetransfer_bytes_sent_total']+$sd['connection_filetransfer_bytes_received_total'])/1024);
                                if ($resellersettings[$resellerid]['firstchecktime']<$resellersettings[$resellerid]['firstcheck']) $filetraffic = 0;
                                $newtraffic = 0;
                                if ($newtrafficdata>$lastfiletraffic) {
                                    $addedtraffic = $newtrafficdata-$lastfiletraffic;
                                    $newtraffic = $filetraffic+$addedtraffic;
                                } else if ($newtrafficdata == $lastfiletraffic) {
                                    $newtraffic = $filetraffic;
                                } else if ($newtrafficdata<$lastfiletraffic) {
                                    $addedtraffic = $newtrafficdata;
                                    $newtraffic = $filetraffic+$addedtraffic;
                                }
                                $newtrafficmb=round($newtraffic/1024);
                                $traffictext = '';
                                $virtualserver_max_download_total_bandwidth=$max_download_total_bandwidth;
                                $virtualserver_max_upload_total_bandwidth=$max_upload_total_bandwidth;
                                if (isset($ts3id) and $flexSlots == 'Y' and $usedslots==0 and ($usedslots+$flexSlotsFree) != $flexSlotsCurrent) $changeSlots = $flexSlotsFree;
                                else if (isset($ts3id) and $flexSlots == 'Y' and ($usedslots+$flexSlotsFree) != $flexSlotsCurrent and ($usedslots+$flexSlotsFree) <= $slots and (abs(($usedslots+$flexSlotsFree)-$flexSlotsCurrent)/($flexSlotsFree/100))>=$flexSlotsPercent) $changeSlots = $usedslots+$flexSlotsFree;
                                else if (isset($ts3id) and $flexSlots == 'Y' and $flexSlotsCurrent != $slots and ($usedslots+$flexSlotsFree)>$slots and (abs(($usedslots+$flexSlotsFree)-$flexSlotsCurrent)/($flexSlotsFree/100))>=$flexSlotsPercent) $changeSlots = $slots;
                                if (isset($changeSlots) and $flexSlotsCurrent!=2 and $changeSlots<2) $changeSlots = 2;
                                else if (isset($changeSlots) and $flexSlotsCurrent==2 and $changeSlots<2) unset($changeSlots);
                                if ($maxtraffic>0 and $newtraffic>$maxtraffic and $sd['virtualserver_max_download_total_bandwidth']>1 and $sd['virtualserver_max_upload_total_bandwidth']>1) {
                                    $virtualserver_max_download_total_bandwidth = 1;
                                    $virtualserver_max_upload_total_bandwidth = 1;
                                    $traffictext="and has now reached the traffic limit ".$newtrafficmb. '/' . $maxtrafficmb." MB";
                                    if (isset($rulebreak)) {
                                        $rulebreak .="<br />Traffic Limit".$newtrafficmb. '/' . $maxtrafficmb." MB";
                                    } else {
                                        $rulebreak="<br />Traffic Limit".$newtrafficmb. '/' . $maxtrafficmb." MB";
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
                                        $rulebreak=$ssprache->noservertag;
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
                                                $rulebreak=$param . '  ' . $vosprache->isnot . '  ' . $default[$param];
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
                                                $rulebreak=$param . '  ' . $vosprache->isnot . '  ' . $default[$param];
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
                                        $pupdate2=$sql->prepare("UPDATE `voice_server` SET `notified`='0',`flexSlotsCurrent`=? WHERE `id`=? LIMIT 1");
                                        $pupdate2->execute(array($changeSlots, $ts3id));
                                    } else if ($notified>0) {
                                        $pupdate2=$sql->prepare("UPDATE `voice_server` SET `notified`='0' WHERE `id`=? LIMIT 1");
                                        $pupdate2->execute(array($ts3id));
                                    }
                                    print $vrow['type']." server $address is running $traffictext. The name converted to ISO-8859-1 is ".iconv('UTF-8','ISO-8859-1//TRANSLIT', $server['virtualserver_name']) . "\r\n";
                                } else if (isset($ts3id) and $notified == '0' and isset($rulebreak)) {
                                    $connection->ImportModServer($virtualserver_id, $slots, $vrow2['ip'], $vrow2['port'], $modbadserver);
                                    if ($resellerid==0) {
                                        $query2 = $sql->prepare("SELECT `id`,`mail_securitybreach` FROM `userdata` WHERE `id`=? OR (`resellerid`='0' AND `accounttype`='a')");
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
                                    $pupdate2=$sql->prepare("UPDATE `voice_server` SET `notified`='1' WHERE `id`=? LIMIT 1");
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
                                        $elapsed=round((strtotime('now')-strtotime($erow['started']))/60);
                                        if ($elapsed>$shutdownemptytime and $usedslots == '0') {
                                            print "Will stop server $address before time is up, because it is empty\r\n";
                                            $stop = true;
                                        } else if ($elapsed>=$runtime) {
                                            print "Will stop server $address because time is up\r\n";
                                            $stop = true;
                                        }
                                        if ($stop==true) {
                                            $rmvoicelend = $sql->prepare("DELETE FROM `lendedserver` WHERE `id`=? LIMIT 1");
                                            $rmvoicelend->execute(array($lid));
                                        }
                                    }
                                    if ($dataloss==true) {
                                        print "Will stop server $address because it is a lendserver and should not be running\r\n";
                                        $stop = true;
                                    }
                                    if ($stop==true) {
                                        $connection->StopServer($virtualserver_id);
                                    }
                                }
                                $query = $sql->prepare("INSERT INTO `voice_server_stats` (`sid`,`mid`,`installed`,`used`,`date`,`uid`,`resellerid`) VALUES (?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `used`=(`used`*(`count`/(`count`+1))+(VALUES(`used`)*(1/(`count`+1)))),`installed`=(`installed`*(`count`/(`count`+1))+(VALUES(`installed`)*(1/(`count`+1)))),`count`=`count`+1");
                                $query->execute(array($ts3id, $ts3masterid, $server['virtualserver_maxclients'], $usedslots, $dayAndZeroHour, $userid, $resellerid));
                            } else if (isset($ts3id)) {
                                $uptime = 1;
                                $usedslots = 0;
                                if ($lendserver == 'Y' and $resellersettings[$resellerid]['active'] == 'Y') {
                                    $removedeadvoiceserver = $sql->prepare("DELETE FROM `lendedserver` WHERE `serverid`=? LIMIT 1");
                                    $removedeadvoiceserver->execute(array($ts3id));
                                } else if ($active == 'Y' and $vs != 'online' and $olduptime>1 and $olduptime != null) {
                                    $notified++;
                                    if($notified>=$ts3masternotified == $resellersettings[$resellerid]['down_checks']){
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
                                if(isset($sd['virtualserver_flag_password']) and $sd['virtualserver_flag_password'] == 1) {
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
    $query = $sql->prepare("UPDATE `settings` SET `lastCronStatus`=UNIX_TIMESTAMP() WHERE `resellerid`=0 LIMIT 1");
    $query->execute();
} else {
	header('Location: login.php');
	die('Statuscheck can only be run via console or a cronjob');
}