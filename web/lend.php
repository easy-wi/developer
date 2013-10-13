<?php

/**
 * File: lend.php.
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


if (isset($page_include)) {
    $reseller_id = 0;

} else {

    define('EASYWIDIR', dirname(__FILE__));

    if (is_dir(EASYWIDIR . '/install')) {
        die('Please remove the "install" folder');
    }

    $logininclude = 1;

    include(EASYWIDIR . '/stuff/vorlage.php');
    include(EASYWIDIR . '/stuff/class_validator.php');
    include(EASYWIDIR . '/stuff/functions.php');
    include(EASYWIDIR . '/stuff/settings.php');

    $query = $sql->prepare("SELECT `language` FROM `settings` WHERE `resellerid`=0 LIMIT 1");
    $user_language = $query->fetchColumn();
    if (!small_letters_check($user_language,2)) {
        $user_language = 'en';
    }
}

include(EASYWIDIR . '/stuff/keyphrasefile.php');
include(EASYWIDIR . '/stuff/ssh_exec.php');
include(EASYWIDIR . '/stuff/class_voice.php');

$validacces = false;

if ($ui->ip4('REMOTE_ADDR', 'server') and $ui->names('user', 255, 'post') and !isset($page_include)) {

    $query = $sql->prepare("SELECT `active`,`pwd`,`salt`,`user`,i.`resellerID` FROM `api_ips` i LEFT JOIN `api_settings` s ON i.`resellerID`=s.`resellerID` WHERE `ip`=?");
    $query->execute(array($ui->ip4('REMOTE_ADDR', 'server')));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $pwd = $row['pwd'];
        $salt = $row['salt'];

        if ($row['active'] == 'Y' and passwordhash($ui->password('pwd', 255, 'post'), $salt) == $pwd and $ui->names('user', 255, 'post') == $row['user']) {
            $resellerIDs[] = $row['resellerID'];
        }

        if (isset($resellerIDs) and count($resellerIDs) == 1 and passwordhash($ui->password('pwd', 255, 'post'), $salt) == $pwd) {
            $reseller_id = $resellerIDs[0];
            $validacces = true;
        }
    }

} else {
    $reseller_id = 0;
    $validacces = true;
}

if ($validacces == false) {
    header('HTTP/1.1 403 Forbidden');
    die('403 Forbidden: Access data not valid');
}

if ($ui->escaped('email', 'post') != '') {
    $fullday=date('Y-m-d H:i:s', strtotime("+1 day"));
    $query = $sql->prepare("SELECT `id` FROM `badips` WHERE `badip`=? LIMIT 1");
    $query->execute(array($loguserip));
    $query=($query->rowCount()==0) ? $sql->prepare("INSERT INTO `badips` (`bantime`,`failcount`,`reason`,`badip`) VALUES (?,'1','bot',?)") : $sql->prepare("UPDATE `badips` SET `bantime`=?, `failcount`=failcount+1, `reason`='bot' WHERE `badip`=? LIMIT 1");
    $query->execute(array($fullday, $loguserip));
}

$sprache = getlanguagefile('lendserver', $user_language, $reseller_id);
$gssprache = getlanguagefile('gserver', $user_language, $reseller_id);
$vosprache = getlanguagefile('voice', $user_language, $reseller_id);
$licenceDetails=serverAmount($reseller_id);

if (is_numeric($licenceDetails['left']) and (0>$licenceDetails['left'] or 0>$licenceDetails['lG'] or 0>$licenceDetails['lVo'] or $licenceDetails['t'] == 'l')) {
    header('HTTP/1.1 403 Forbidden');
    die('403 Forbidden: ' . $gsprache->licence);
}

$timeselect = array();
$slotselect = array();
$votimeselect = array();
$voslotselect = array();

$query = $sql->prepare("SELECT *,AES_DECRYPT(`ftpuploadpath`,?) AS `decyptedftpuploadpath` FROM `lendsettings` WHERE `resellerid`=? LIMIT 1");
$query->execute(array($aeskey, $reseller_id));
foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
    $active = $row['active'];
    $activeGS = ($row['activeGS'] == 'B' or ($row['activeGS'] != 'N' and (isset($admin_id) or ($row['activeGS'] != 'N' and $ui->username('shorten', 50, 'get') == 'api'))) or ($row['activeGS'] == 'R' and isset($user_id)) or ($row['activeGS'] == 'A' and !isset($user_id))) ? 'Y' : 'N';
    $activeVS = ($row['activeVS'] == 'B' or ($row['activeVS'] != 'N' and (isset($admin_id) or ($row['activeVS'] != 'N' and $ui->username('shorten', 50, 'get') == 'api'))) or ($row['activeVS'] == 'R' and isset($user_id)) or ($row['activeVS'] == 'A' and !isset($user_id))) ? 'Y' : 'N';
    $ftpupload = ($row['ftpupload'] == 'Y' or ($row['ftpupload'] != 'N' and (isset($admin_id) or ($row['ftpupload'] != 'N' and $ui->username('shorten', 50, 'get') == 'api'))) or ($row['ftpupload'] == 'R' and isset($user_id)) or ($row['ftpupload'] == 'A' and !isset($user_id))) ? 'Y' : 'N';
    $ftpuploadpath = $row['decyptedftpuploadpath'];
    if (($ui->username('shorten', 50, 'get') == 'api') or (in_array($row['activeGS'], array('B', 'R')) and (isset($user_id) or isset($admin_id)))) {
        $mintime = (int) $row['mintimeRegistered'];
        $time = (int) $row['mintimeRegistered'];
        $maxtime = (int) $row['maxtimeRegistered'];
        $timesteps = (int) $row['timestepsRegistered'];
        $minplayer = (int) $row['minplayerRegistered'];
        $maxplayer = (int) $row['maxplayerRegistered'];
        $player = (int) $row['maxplayerRegistered'];
        $playersteps = (int) $row['playerstepsRegistered'];
    } else {
        $mintime = (int) $row['mintime'];
        $time = (int) $row['mintime'];
        $maxtime = (int) $row['maxtime'];
        $timesteps = (int) $row['timesteps'];
        $minplayer = (int) $row['minplayer'];
        $maxplayer = (int) $row['maxplayer'];
        $player = (int) $row['maxplayer'];
        $playersteps = (int) $row['playersteps'];
    }
    if (($ui->username('shorten', 50, 'get') == 'api') or (in_array($row['activeVS'], array('B', 'R')) and (isset($user_id) or isset($admin_id)))) {
        $vomintime = (int) $row['vomintimeRegistered'];
        $votime = (int) $row['vomintimeRegistered'];
        $vomaxtime = (int) $row['vomaxtimeRegistered'];
        $votimesteps = (int) $row['votimestepsRegistered'];
        $vominplayer = (int) $row['vominplayerRegistered'];
        $vomaxplayer = (int) $row['vomaxplayerRegistered'];
        $voplayer = (int) $row['vomaxplayerRegistered'];
        $voplayersteps = (int) $row['voplayerstepsRegistered'];
    } else {
        $vomintime = (int) $row['vomintime'];
        $votime = (int) $row['vomintime'];
        $vomaxtime = (int) $row['vomaxtime'];
        $votimesteps = (int) $row['votimesteps'];
        $vominplayer = (int) $row['vominplayer'];
        $vomaxplayer = (int) $row['vomaxplayer'];
        $voplayer = (int) $row['vomaxplayer'];
        $voplayersteps = (int) $row['voplayersteps'];
    }
    $lendaccess = $row['lendaccess'];
    $lastcheck = $row['lastcheck'];
    $timebetweenchecks = (strtotime($lastcheck) - strtotime($row['oldcheck'])) / 60;
    $timebetweenlastandnow = (strtotime('now')-strtotime($lastcheck))/60;
    $nextcheck = ceil($timebetweenchecks-$timebetweenlastandnow);

    if ($nextcheck < 0) {
        $nextcheck = $nextcheck * (-1);
    }
    if ($time>0 and $maxtime>0) {
        while ($time <= $maxtime) {
            $timeselect[] = $time;
            $time = $time + $timesteps;
        }
    }
    $gsstart = $minplayer;
    if ($player>0 and $gsstart>0) {
        while ($gsstart <= $player) {
            $slotselect[] = $gsstart;
            $gsstart = $gsstart + $playersteps;
        }
    }
    if ($votime>0 and $vomaxtime>0) {
        while ($votime <= $vomaxtime) {
            $votimeselect[] = $votime;
            $votime = $votime + $votimesteps;
        }
    }
    $vstart = $vominplayer;
    if ($voplayer>0 and $vstart>0) {
        while ($vstart <= $voplayer) {
            $voslotselect[] = $vstart;
            $vstart = $vstart + $voplayersteps;
        }
    }
}

if ($ui->username('shorten', 50, 'get') == 'api' and isset($lendaccess) and ($lendaccess == 1 or $lendaccess == 3)) {
    $loguserip = '';
}

$gsstillrunning = false;
$vostillrunning = false;

if (!isset($page_include) and $ui->id('xml', 1, 'post') == 1) {

    if ($ui->escaped('game', 'post'))	{
        $xml = @simplexml_load_string(base64_decode($ui->escaped('game', 'post')));

    } else if ($ui->escaped('ipblocked', 'post')) {
        $xml = @simplexml_load_string(base64_decode($ui->escaped('ipblocked', 'post')));
    }

    if (isset($xml) and $xml == false) {
        header('HTTP/1.1 403 Forbidden');
        die('403 Forbidden: XML not valid');

    } else if (isset($xml)) {

        if (isip($xml->userip,'all')) {
            $loguserip = $xml->userip;

        } else {
            $error = 'no userip</br>';
            $fail = 1;
        }
    }
}

$query = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `voice_server` WHERE `lendserver`='Y' AND `active`='Y' AND `resellerid`=?");
$query->execute(array($reseller_id));
$vocount = $query->fetchColumn();

$query = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `gsswitch` WHERE `lendserver`='Y' AND `resellerid`=?");
$query->execute(array($reseller_id));
$gscount = $query->fetchColumn();

if ($activeGS == 'Y' and ($w == 'gs' or $d == 'gs' or $ui->st('w', 'post') == 'gs' or (isset($page_name) and $page_name == strtolower(str_replace(' ', '-', $gsprache->gameserver))))) {
    $servertype = 'g';
} else if ($activeVS == 'Y' and ($w == 'vo' or $d == 'vo' or $ui->st('w', 'post') == 'vo' or (isset($page_name) and $page_name == strtolower(str_replace(' ', '-', $gsprache->voiceserver))))) {
    $servertype = 'v';
}

$volallowed = ($vocount>0) ? true : false;
$gslallowed = ($gscount>0) ? true : false;

if (!isset($servertype) and !isset($page_include) and (!$ui->username('shorten', 50, 'get') or ($ui->username('shorten', 50, 'get') == 'api') and !$ui->st('w', 'post'))) {
    $servertype = ($vocount > $gscount) ? 'v' : 'g';
}

if (isset($servertype)) {

    $query = $sql->prepare("SELECT `id`,`serverid`,`rcon`,`password`,`slots`,`started`,`lendtime` FROM `lendedserver` WHERE `lenderip`=? AND `servertype`=? AND `resellerid`=? LIMIT 1");
    $query1 = $sql->prepare("SELECT s.`switchID`,g.`rootID` FROM `serverlist` s INNER JOIN `gsswitch` g ON s.`switchID`=g.`id` WHERE s.`id`=? AND s.`resellerid`=? LIMIT 1");
    $query2 = $sql->prepare("DELETE FROM `lendedserver` WHERE `id`=? AND `resellerid`=? LIMIT 1");
    $query3 = $sql->prepare("SELECT v.`localserverid`,m.`ssh2ip`,m.`rootid`,m.`addedby`,m.`queryport`,AES_DECRYPT(m.`querypassword`,?) AS `decryptedquerypassword` FROM `voice_server` v LEFT JOIN `voice_masterserver` m ON v.`masterserver`=m.`id` WHERE v.`id`=? AND v.`resellerid`=? LIMIT 1");
    $query4 = $sql->prepare("SELECT `ip`,`altips` FROM `rserverdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");

    $query->execute(array($loguserip, $servertype, $reseller_id));
    foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
        $serverid = $row['serverid'];
        $lendtime = $row['lendtime'];
        $timeleft = round($lendtime - (strtotime('now') - strtotime($row['started'])) / 60);

        if ($timeleft <= 0) {
            $query2->execute(array($row['id'], $reseller_id));

            if ($servertype == 'g') {

                unset($_SESSION['lend']['gs']);

                $query1->execute(array($serverid, $reseller_id));
                foreach($query1->fetchAll(PDO::FETCH_ASSOC) as $row1) {
                    $cmds = gsrestart($row1['switchID'], 'so', $aeskey, $reseller_id);
                    ssh2_execute('gs', $row1['rootID'], $cmds);
                }

            } else if ($servertype == 'v') {

                unset($_SESSION['lend']['vs']);

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
                        foreach ($query4->fetchall(PDO::FETCH_ASSOC) as $row3) {
                            $queryip = $row3['ip'];
                        }
                    }
                }

                $connection = new TS3($queryip, $queryport, 'serveradmin', $querypassword);
                $errorcode = $connection->errorcode;

                if (strpos($errorcode, 'error id=0') !== false) {
                    $connection->StopServer($localserverid);
                }

                $connection->CloseConnection();
            }
        } else {
            $rcon = $row['rcon'];
            $password = $row['password'];
            $slots = $row['slots'];
            if ($servertype == 'g') {

                if (!$ui->id('xml', 1, 'post') and (!isset($_SESSION['lend']['gs']) or $_SESSION['lend']['gs'] != $serverid)) {
                    $lendIPBlock = true;
                }

                $gsstillrunning = true;
                $description = '';
                $serverip = '';
                $port = '';
                $query2 = $sql->prepare("SELECT g.`serverip`,g.`port`,t.`description` FROM `gsswitch` g LEFT JOIN `serverlist` s ON g.`serverid`=s.`id` LEFT JOIN `servertypes` t ON s.`id`=? AND s.`servertype`=t.`id` WHERE s.`resellerid`=? AND t.`description` IS NOT NULL LIMIT 1");
                $query2->execute(array($serverid, $reseller_id));
                foreach ($query2->fetchall(PDO::FETCH_ASSOC) as $row2) {
                    $description = $row2['description'];
                    $serverip = $row2['serverip'];
                    $port = $row2['port'];
                }

                $responsexml = new DOMDocument('1.0','utf-8');
                $element = $responsexml->createElement('startserver');

                $key = $responsexml->createElement('status', 'stillrunning');
                $element->appendChild($key);
                $key = $responsexml->createElement('ip', $serverip);
                $element->appendChild($key);
                $key = $responsexml->createElement('port', $port);
                $element->appendChild($key);
                $key = $responsexml->createElement('slots', $slots);
                $element->appendChild($key);
                $key = $responsexml->createElement('lendtime', $lendtime);
                $element->appendChild($key);
                $key = $responsexml->createElement('rcon', $rcon);
                $element->appendChild($key);
                $key = $responsexml->createElement('password', $password);
                $element->appendChild($key);
                $key = $responsexml->createElement('timeleft', $timeleft);
                $element->appendChild($key);

            } else if ($servertype == 'v') {

                if (!$ui->id('xml', 1, 'post') and (!isset($_SESSION['lend']['vs']) or $_SESSION['lend']['vs'] != $serverid)) {
                    $lendIPBlock = true;
                }

                $vostillrunning = true;
                $query2 = $sql->prepare("SELECT v.`ip`,v.`port`,v.`dns`,m.`type`,m.`usedns` FROM `voice_server` v LEFT JOIN `voice_masterserver` m ON v.`masterserver`=m.`id` WHERE v.`id`=? AND v.`resellerid`=? LIMIT 1");
                $query2->execute(array($serverid, $reseller_id));
                foreach ($query2->fetchall(PDO::FETCH_ASSOC) as $row2) {
                    $server = ($row2['usedns'] == 'N' or $row2['dns'] == null or $row2['dns'] == '') ? $row2['ip'] . ':' . $row2['port'] : $row2['dns'];
                    $serverip = $row2['ip'];
                    $port = $row2['port'];
                }

                $responsexml = new DOMDocument('1.0','utf-8');
                $element = $responsexml->createElement('startserver');

                $key = $responsexml->createElement('status', 'started');
                $element->appendChild($key);
                $key = $responsexml->createElement('ip', $serverip);
                $element->appendChild($key);
                $key = $responsexml->createElement('port', $port);
                $element->appendChild($key);
                $key = $responsexml->createElement('dns', $server);
                $element->appendChild($key);
                $key = $responsexml->createElement('slots', $slots);
                $element->appendChild($key);
                $key = $responsexml->createElement('lendtime', $lendtime);
                $element->appendChild($key);
                $key = $responsexml->createElement('token', $rcon);
                $element->appendChild($key);
                $key = $responsexml->createElement('password', $password);
                $element->appendChild($key);
                $key = $responsexml->createElement('timeleft', $timeleft);
                $element->appendChild($key);

            }

            if (!isset($nextfree)) {
                $nextfree = 0;
            }

            if (!isset($page_include) and $ui->id('xml', 1, 'post') == 1 and isset($element)) {

                $responsexml->appendChild($element);

                $responsexml->formatOutput = true;

                header("Content-Type: text/xml; charset=UTF-8");

                echo $responsexml->saveXML();

            } else if (isset($page_include)) {

                $page_data->setCanonicalUrl($s);
                $template_file = (isset($lendIPBlock)) ? 'page_lenddata_ipblock.tpl' : 'page_lenddata.tpl';

            } else {

                $tFile = (isset($lendIPBlock)) ? 'lenddata_ipblock.tpl' : 'lenddata.tpl';

                if (isset($template_to_use) and is_file(EASYWIDIR . '/template/' . $template_to_use . '/' . $tFile)) {
                    include(EASYWIDIR . '/template/' . $template_to_use . '/' . $tFile);
                } else if (is_file(EASYWIDIR . '/template/default/' . $tFile)) {
                    include(EASYWIDIR . '/template/default/' . $tFile);
                } else {
                    include(EASYWIDIR . '/template/' . $tFile);
                }
            }
        }
    }
}

if (!isset($template_file) and $ui->escaped('ipblocked', 'post') and $ui->id('xml', 1, 'post') == 1 and !isset($responsexml)) {
    die('notblocked');
}

if (!isset($template_file) and ((!isset($servertype) and isset($page_include) and $ui->id('xml', 1, 'post')!=1) or ($ui->id('xml', 1, 'post') == 1 and !$ui->st('w', 'post')))) {

    $lendGameServers = array();
    $lendVoiceServers = array();

    $query = $sql->prepare("SELECT `id`,`queryMap`,`queryNumplayers`,`queryName`,`serverip`,`port`,`slots`,`serverid` FROM `gsswitch` WHERE `lendserver`='Y' AND `active`='Y' AND `resellerid`=0");
    $query2 = $sql->prepare("SELECT s.`id`,t.`shorten`,t.`description` FROM `serverlist` s LEFT JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE s.`switchID`=? AND s.`resellerid`=0");
    $query3 = $sql->prepare("SELECT `slots`,`started`,`lendtime` FROM `lendedserver` WHERE `serverid`=? AND `servertype`='g' LIMIT 1");
    $query->execute(array($reseller_id));
    foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {

        $installedShorten = array();
        $timeleft = 0;
        $runningGame = '';
        $slots = $row['slots'];
        $free = '16_ok.png';

        $query2->execute(array($row['id']));
        foreach ($query2->fetchall(PDO::FETCH_ASSOC) as $row2) {
            $installedShorten[$row2['shorten']] = $row2['description'];

            if ($row2['id'] == $row['serverid']) {
                $runningGame = $row2['shorten'];
            }
        }

        $query3->execute(array($row['serverid']));
        foreach ($query3->fetchall(PDO::FETCH_ASSOC) as $row3) {
            $slots = $row3['slots'];
            $timeleft = round($row3['lendtime'] - (strtotime('now') - strtotime($row3['started'])) / 60);
            $free = '16_bad.png';

            if ($timeleft < 0) {
                $timeleft = 0;
            }
        }

        $lendGameServers[] = array('ip' => $row['serverip'], 'port' => (int) $row['port'], 'queryName' => htmlentities($row['queryName'], ENT_QUOTES, 'UTF-8'), 'queryMap' => htmlentities($row['queryMap'], ENT_QUOTES, 'UTF-8'), 'runningGame' => $runningGame, 'games' => $installedShorten, 'slots' => (int) $slots,'usedslots' => (int) $row['queryNumplayers'], 'timeleft' => (int) $timeleft, 'free' => $free);
    }

    $query = $sql->prepare("SELECT v.`ip`,v.`port`,v.`queryName`,v.`dns`,v.`usedslots`,v.`slots` AS `availableSlots`,l.`slots`,l.`started`,l.`lendtime` FROM `voice_server` v LEFT JOIN `lendedserver` l ON v.`id`=l.`serverid` AND l.`servertype`='v' WHERE v.`lendserver`='Y' AND v.`active`='Y' AND v.`resellerid`=0");
    $query->execute(array($reseller_id));
    foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
        $timeleft = 0;
        $slots = $row['availableSlots'];
        $free = '16_ok.png';
        if ($row['slots'] != null) {
            $timeleft = round($row['lendtime'] - (strtotime('now') - strtotime($row['started'])) / 60);
            $free = '16_bad.png';
            $slots = $row['slots'];

            if ($timeleft < 0) {
                $timeleft = 0;
            }

        }
        $lendVoiceServers[] = array('ip' => $row['ip'], 'port' => (int) $row['port'], 'queryName' => htmlentities($row['queryName'], ENT_QUOTES, 'UTF-8'), 'connect' => $row['dns'], 'slots' => (int) $slots, 'usedslots' => (int) $row['usedslots'], 'timeleft' => (int) $timeleft, 'free' => $free);
    }

    if ($ui->id('xml', 1, 'post') == 1) {

        $xml = new DOMDocument('1.0','utf-8');
        $element = $xml->createElement('serverlist');

        $key = $xml->createElement('tes','value');
        $element->appendChild($key);

        $voiceServersXML = $xml->createElement('voiceserver');
        foreach ($lendVoiceServers as $row) {

            $voiceServerXML = $xml->createElement('server');

            $key = $xml->createElement('ip', $row['ip']);
            $voiceServerXML->appendChild($key);

            $key = $xml->createElement('port', $row['port']);
            $voiceServerXML->appendChild($key);

            $key = $xml->createElement('slots', $row['slots']);
            $voiceServerXML->appendChild($key);

            $key = $xml->createElement('usedslots', $row['usedslots']);
            $voiceServerXML->appendChild($key);

            $key = $xml->createElement('timeleft', $row['timeleft']);
            $voiceServerXML->appendChild($key);

            $key = $xml->createElement('queryName', $row['queryName']);
            $voiceServerXML->appendChild($key);

            $key = $xml->createElement('connect', $row['connect']);
            $voiceServerXML->appendChild($key);

            $voiceServersXML->appendChild($voiceServerXML);
        }

        $element->appendChild($voiceServersXML);

        $gameServersXML = $xml->createElement('gameserver');

        foreach ($lendGameServers as $row) {

            $gameServerXML = $xml->createElement('server');

            $key = $xml->createElement('ip', $row['ip']);
            $gameServerXML->appendChild($key);

            $key = $xml->createElement('port', $row['port']);
            $gameServerXML->appendChild($key);

            $key = $xml->createElement('slots', $row['slots']);
            $gameServerXML->appendChild($key);

            $key = $xml->createElement('usedslots', $row['usedslots']);
            $gameServerXML->appendChild($key);

            $key = $xml->createElement('timeleft', $row['timeleft']);
            $gameServerXML->appendChild($key);

            $key = $xml->createElement('queryName', $row['queryName']);
            $gameServerXML->appendChild($key);

            $key = $xml->createElement('queryMap', $row['queryMap']);
            $gameServerXML->appendChild($key);

            $key = $xml->createElement('runningGame', $row['runningGame']);
            $gameServerXML->appendChild($key);

            $gamesXML = $xml->createElement('games');
            foreach ($row['games'] as $game) {
                $key = $xml->createElement('game', $game);
                $gamesXML->appendChild($key);
            }
            $gameServerXML->appendChild($gamesXML);

            $gameServersXML->appendChild($gameServerXML);
        }

        $element->appendChild($gameServersXML);

        $xml->appendChild($element);

        $xml->formatOutput = true;

        header("Content-Type: text/xml; charset=UTF-8");

        echo $xml->saveXML();

    } else {
        $template_file = 'page_lend_list.tpl';
    }

} else if (!isset($template_file) and $gsstillrunning == false and isset($active) and $active == 'Y' and $servertype == 'g' and !$ui->escaped('ipblocked', 'post')) {

    $switchcount = array();

    $query = $sql->prepare("SELECT `id` FROM `gsswitch` WHERE `lendserver`='Y' AND `resellerid`=?");
    $query2 = $sql->prepare("SELECT s.`id`,t.`shorten` FROM `serverlist` s LEFT JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE s.`switchID`=? AND s.`resellerid`=? ORDER BY t.`shorten`");
    $query->execute(array($reseller_id));
    $gscounts = array();
    $gsused = array();

    foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
        $shortens = array();
        $serverids = array();

        $query2->execute(array($row['id'], $reseller_id));
        foreach ($query2->fetchall(PDO::FETCH_ASSOC) as $row2) {
            $shorten = $row2['shorten'];
            $serverids[$shorten][] = $row2['id'];

            if (isset($gscounts[$shorten])) {
                $gscounts[$shorten]++;

            } else {
                $gscounts[$shorten] = 1;
                $gsused[$shorten] = 0;
            }

            $shortens[] = $shorten;
        }

        $shortenlist = implode('|', $shortens);

        foreach ($shortens as $shorten) {
            if (isset($switchcount[$shortenlist][$shorten]['exist'])) {
                $switchcount[$shortenlist][$shorten]['exist']++;

            } else {
                $switchcount[$shortenlist][$shorten]['exist'] = 1;
                $switchcount[$shortenlist][$shorten]['used'] = 0;
            }

            foreach ($serverids[$shorten] as $id) {
                $switchcount[$shortenlist][$shorten]['freeids'][] = $id;
            }
        }
    }

    $query = $sql->prepare("SELECT `serverid`,`rcon`,`password`,`slots`,`started`,`lendtime`,`lenderip` FROM `lendedserver` WHERE `servertype`='g' AND `resellerid`=?");
    $query2 = $sql->prepare("SELECT `switchID` FROM `serverlist` WHERE `id`=? AND `resellerid`=? LIMIT 1");
    $query3 = $sql->prepare("SELECT s.`id`,t.`shorten` FROM `serverlist` s INNER JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE s.`switchID`=? AND s.`resellerid`=? ORDER BY t.`shorten`");
    $query->execute(array($reseller_id));

    foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
        $lendtime = $row['lendtime'];
        $timeleft = round($lendtime-(strtotime('now')-strtotime($row['started']))/60);

        if (!isset($nextfree) or $timeleft < $nextfree) {
            $nextfree = $timeleft;
        }

        $gscount--;
        $serverids = array();
        $shortens = array();
        $rcon = $row['rcon'];
        $password = $row['password'];
        $slots = $row['slots'];
        $lenderip = $row['lenderip'];

        $query2->execute(array($row['serverid'], $reseller_id));
        $switchID = $query2->fetchColumn();

        if (isid($switchID, 10)) {

            $query3->execute(array($switchID, $reseller_id));
            foreach ($query3->fetchall(PDO::FETCH_ASSOC) as $row3) {
                $shorten = $row3['shorten'];
                $shortens[] = $shorten;
                $serverids[$shorten][] = $row3['id'];
                $gsused[$shorten]++;
            }

            $shortenlist=implode('|', $shortens);

            foreach ($shortens as $shorten) {
                $switchcount[$shortenlist][$shorten]['used']++;
                foreach ($serverids[$shorten] as $id) {
                    $key = array_search($id, $switchcount[$shortenlist][$shorten]['freeids']);
                    if ($key == 0 or isinteger($key)) {
                        unset($switchcount[$shortenlist][$shorten]['freeids'][$key]);
                    }
                }
            }
        }
    }

    $status = array();
    $serveravailable = false;
    $gameselect = array();
    foreach ($gscounts as $key => $value) {
        $query = $sql->prepare("SELECT `description` FROM `servertypes` WHERE `shorten`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($key, $reseller_id));
        $description = $query->fetchColumn();

        $amount = $value-$gsused[$key];
        $switchcount[$shortenlist][$shorten];

        if ($amount > 0) {
            $serveravailable = true;
            $gameselect[$key] = $description;
            $text = $sprache->available;

        } else {
            $gscount = 0;
            $text = $sprache->used;
        }

        $status[$description] = array('text' => $text, 'amount' => $amount, 'total' => $value);
    }

    if ((!isset($nextfree) and $gscount > 0) or (isset($nextfree) and $gscount > 0)){
        $nextfree = 0;
    }

    if ($serveravailable == true and ($lendaccess == 1 or $lendaccess == 2) and $ui->w('game', 20, 'post')) {
        $fail = 0;
        $error = "Error:";

        if ($ui->id('xml', 1, 'post') == 1) {
            $game = $xml->game;
            $rcon = $xml->rcon;
            $password = $xml->password;
            $slots = (int) $xml->slots;
            $lendtime = (int) $xml->lendtime;
            $postedftpuploadpath =isurl($xml->ftpuploadpath);

        } else {
            $game = $ui->w('game', 20, 'post');
            $rcon = $ui->w('rcon', 20, 'post');
            $password = $ui->w('password', 20, 'post');
            $slots = $ui->id('slots', 3, 'post');
            $lendtime = $ui->id('time', 4, 'post');
            $postedftpuploadpath = $ui->url('ftpuploadpath', 'post');
        }

        if (!wpreg_check($game,20)) {
            $fail = 1;
            $error .= "Game</br>";
        }

        if (!isid($slots, 3) or $slots > $maxplayer or $slots < $minplayer) {
            $fail = 1;
            $error .= "Slots</br>";
        }

        if (!isid($lendtime,4) or $lendtime > $maxtime or $lendtime < $mintime) {
            $fail = 1;
            $error .= "Time</br>";
        }

        if (!wpreg_check($rcon,20)) {
            $error .= "Rcon</br>";
            $fail = 1;
        }

        if (!wpreg_check($password,20)) {
            $error .= "Password</br>";
            $fail = 1;
        }

        if ($fail == 0) {

            if ($ftpupload == 'Y' and isurl($postedftpuploadpath) and $postedftpuploadpath != 'ftp://username:password@1.1.1.1/demos') {

                $split = preg_split('/\//', $postedftpuploadpath, -1, PREG_SPLIT_NO_EMPTY);
                $split2 = preg_split('/@/', $split[1], -1, PREG_SPLIT_NO_EMPTY);

                if (isset($split2[1])) {
                    $ftpipport = $split2[1];
                    $userpass = explode(':', $split2[0]);
                    $ftpuser = $userpass[0];
                    $ftppass = (isset($userpass[1])) ? $userpass[1] : '';

                } else {
                    $ftpipport = $split2[0];
                    $ftpuser = 'anonymous';
                    $ftppass = '';
                }

                $ftpipport = preg_split('/:/', $ftpipport, -1, PREG_SPLIT_NO_EMPTY);

                $ftp_connect = (isset($ftpipport[1])) ? @ftp_connect($ftpipport[0], $ftpipport[1], 5) : @ftp_connect($ftpipport[0], 21, 5);
                
                if ($ftp_connect) {
                    $ftp_login= @ftp_login($ftp_connect, $ftpuser, $ftppass);

                    if ($ftp_login) {
                        $ftpuploadpath = $postedftpuploadpath;
                    }
                }

                ftp_close($ftp_connect);
            }

            $free = $gscounts[$game] - $gsused[$game];

            if ($free > 0) {

                if (isset($switchcount[$game][$game]['freeids']) and count($switchcount[$game][$game]['freeids']) > 0) {
                    $random = array_rand($switchcount[$game][$game]['freeids'], 1);
                    $serverid = $switchcount[$game][$game]['freeids'][$random];

                } else {

                    $mostleft = array();
                    $leftservers = array();
                    foreach ($switchcount as $key=>$arrays) {

                        if (isset($switchcount[$key][$game]['freeids']) and count($switchcount[$key][$game]['freeids']) > 0) {

                            foreach ($switchcount as $leftkey => $leftarrays) {
                                if ($leftkey != $key) {
                                    foreach ($leftarrays as $gametype => $values) {
                                        $leftservers[$key][$gametype] = (isset($leftservers[$gametype])) ? $leftservers[$gametype] + ($values['exist'] - $values['used']) : $values['exist'] - $values['used'];
                                    }
                                }
                            }

                            foreach ($arrays as $gametype => $values) {
                                $leftservers[$key][$gametype] =  (isset($leftservers[$gametype])) ? $leftservers[$gametype]+($values['exist'] - $values['used'] - 1) : $values['exist'] - $values['used'] - 1;
                            }
                        }
                    }

                    foreach ($leftservers as $keys => $arrays) {
                        $percent = 0;
                        foreach ($arrays as $key => $count) {
                            $percent = $percent + ((100 / $switchcount[$keys][$key]['exist']) * ($count));
                        }
                        $mostleft[$keys] = $percent;
                    }

                    arsort($mostleft);
                    $bestmultigame = key($mostleft);
                    $random = array_rand($switchcount[$bestmultigame][$game]['freeids'], 1);
                    $serverid = $switchcount[$bestmultigame][$game]['freeids'][$random];
                }

                $timeleft = $lendtime;

                if (!$ui->id('xml', 1, 'post') == 1) {
                    $_SESSION['lend']['gs'] = $serverid;
                }

                $query = $sql->prepare("INSERT INTO `lendedserver` (`serverid`,`servertype`,`rcon`,`password`,`slots`,`started`,`lendtime`,`lenderip`,`ftpuploadpath`,`resellerid`) VALUES (?,?,?,?,?,NOW(),?,?,AES_ENCRYPT(?,?),?)");
                $query->execute(array($serverid,'g', $rcon, $password, $slots, $lendtime, $loguserip, $ftpuploadpath, $aeskey, $reseller_id));
                $query = $sql->prepare("INSERT INTO `lendstats` (`lendDate`,`serverID`,`serverType`,`lendtime`,`slots`,`resellerID`) VALUES (NOW(),?,?,?,?,?) ON DUPLICATE KEY UPDATE `resellerID`=`resellerID`");
                $query->execute(array($serverid,'g', $lendtime, $slots, $reseller_id));
                $query = $sql->prepare("SELECT g.`id`,g.`serverip`,g.`port`,g.`rootID`,t.`description` FROM `gsswitch` g  LEFT JOIN `serverlist` s ON s.`switchID`=g.`id` LEFT JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE s.`id`=? AND s.`resellerid`=? LIMIT 1");
                $query->execute(array($serverid, $reseller_id));
                foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
                    $serverip = $row['serverip'];
                    $port = $row['port'];
                    $description = $row['description'];
                    $updateID = $row['id'];
                    $rootID = $row['rootID'];
                }

                $query = $insert = $sql->prepare("UPDATE `gsswitch` SET `serverid`=? WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($serverid, $updateID, $reseller_id));

                $cmds = gsrestart($updateID, 're', $aeskey, $reseller_id);
                ssh2_execute('gs', $rootID, $cmds);

                if (!isset($page_include) and $ui->id('xml', 1, 'post') == 1) {
                    
                    $xml = new DOMDocument('1.0','utf-8');
                    $element = $xml->createElement('startserver');

                    $key = $xml->createElement('status', 'started');
                    $element->appendChild($key);
                    
                    $key = $xml->createElement('ip', $serverip);
                    $element->appendChild($key);

                    $key = $xml->createElement('port', $port);
                    $element->appendChild($key);

                    $key = $xml->createElement('slots', $slots);
                    $element->appendChild($key);

                    $key = $xml->createElement('lendtime', $lendtime);
                    $element->appendChild($key);

                    $key = $xml->createElement('rcon', $rcon);
                    $element->appendChild($key);

                    $key = $xml->createElement('password', $password);
                    $element->appendChild($key);

                    $key = $xml->createElement('timeleft', $timeleft);
                    $element->appendChild($key);

                    $xml->appendChild($element);

                    $xml->formatOutput = true;
                    header("Content-Type: text/xml; charset=UTF-8");
                    echo $xml->saveXML();

                } else {

                    if (!isset($nextfree)) {
                        $nextfree = 0;
                    }

                    if (isset($page_include)) {
                        $page_data->setCanonicalUrl($s);
                        $template_file = 'page_lenddata.tpl';

                    } else {

                        if (is_file(EASYWIDIR . '/template/' . $template_to_use . '/lenddata.tpl')) {
                            include(EASYWIDIR . '/template/' . $template_to_use . '/lenddata.tpl');

                        } else if (is_file(EASYWIDIR . '/template/default/lenddata.tpl')) {
                            include(EASYWIDIR . '/template/default/lenddata.tpl');

                        } else {
                            include(EASYWIDIR . '/template/lenddata.tpl');
                        }
                    }
                }

            } else if (isset($page_include)) {
                $template_file = 'too slow';

            } else {
                echo 'tooslow';
            }

        } else {
            echo $error;
        }

    } else if (isset($page_include) and $serveravailable == false and isset($lendaccess) and ($lendaccess == 1 or $lendaccess == 2)) {
        $template_file = 'Module deaktivated';

    } else if (!isset($page_include) and $serveravailable == false and isset($lendaccess) and ($lendaccess == 1 or $lendaccess == 2) and (($ui->id('xml', 1, 'post') and $ui->w('game', 20, 'post')) or $ui->w('password', 20, 'post'))) {
        echo 'too slow';

    } else if (isset($lendaccess) and ($lendaccess == 1 or $lendaccess == 2 or $lendaccess == 3)) {

        if (!isset($nextfree)) {
            $nextfree = 0;
        }

        $rcon = passwordgenerate(10);
        $password = passwordgenerate(10);

        if (($lendaccess == 1 or $lendaccess == 2) and !$ui->id('xml', 1, 'post')) {

            if (isset($page_include)) {
                $page_data->setCanonicalUrl($s);
                $template_file = 'page_lend.tpl';

            } else {
                if (is_file(EASYWIDIR . '/template/' . $template_to_use . '/lend.tpl')) {
                    include(EASYWIDIR . '/template/' . $template_to_use . '/lend.tpl');

                } else if (is_file(EASYWIDIR . '/template/default/lend.tpl')) {
                    include(EASYWIDIR . '/template/default/lend.tpl');

                } else {
                    include(EASYWIDIR . '/template/lend.tpl');
                }
            }

        } else if (!isset($page_include) and ($lendaccess == 1 or $lendaccess == 3) and $ui->id('xml', 1, 'post') == 1) {

            $xml = new DOMDocument('1.0','utf-8');
            $element = $xml->createElement('status');

            $key = $xml->createElement('demoupload', $ftpupload);
            $element->appendChild($key);

            $key = $xml->createElement('nextfree', $nextfree);
            $element->appendChild($key);

            $key = $xml->createElement('nextcheck', $nextcheck);
            $element->appendChild($key);

            $key = $xml->createElement('mintime', $mintime);
            $element->appendChild($key);

            $key = $xml->createElement('maxtime', $maxtime);
            $element->appendChild($key);

            $key = $xml->createElement('timesteps', $timesteps);
            $element->appendChild($key);

            $key = $xml->createElement('minplayer', $minplayer);
            $element->appendChild($key);

            $key = $xml->createElement('maxplayer', $maxplayer);
            $element->appendChild($key);

            $key = $xml->createElement('playersteps', $playersteps);
            $element->appendChild($key);

            $key = $xml->createElement('rcon', $rcon);
            $element->appendChild($key);

            $key = $xml->createElement('password', $password);
            $element->appendChild($key);

            $gamesXML = $xml->createElement('games');

            foreach ($gscounts as $key => $value){
                $amount = $value - $gsused[$key];
                $keyGame = $xml->createElement($key);

                $key = $xml->createElement('free', $amount);
                $keyGame->appendChild($key);

                $key = $xml->createElement('total', $value);
                $keyGame->appendChild($key);

                $gamesXML->appendChild($keyGame);
            }

            $element->appendChild($gamesXML);

            $xml->appendChild($element);

            $xml->formatOutput = true;
            header("Content-Type: text/xml; charset=UTF-8");
            echo $xml->saveXML();

        } else {
            die('Module deactivated');
        }
    }

# Voiceserver
} else if (!isset($template_file) and $vostillrunning == false and isset($active) and $active == 'Y' and $servertype == 'v' and !$ui->escaped('ipblocked', 'post')) {

    $serveravailable = false;
    $freevoice = $vocount;

    $password = passwordgenerate(10);

    if ($vocount > 0) {
        $masterservers = array();
        $mastervoiceids = array();
        $query = $sql->prepare("SELECT `id`,`maxserver`,`maxslots` FROM `voice_masterserver` WHERE `active`='Y' AND `resellerid`=?");
        $query2 = $sql->prepare("SELECT `id`,`slots` FROM `voice_server` WHERE `lendserver`='Y' AND `active`='Y' AND `masterserver`=? AND `resellerid`=?");
        $query3 = $sql->prepare("SELECT `id`,`started`,`lendtime` FROM `lendedserver` WHERE `serverid`=? AND `servertype`='v' AND `resellerid`=? LIMIT 1");
        $query->execute(array($reseller_id));

        foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
            $masterid = $row['id'];
            $query2->execute(array($masterid, $reseller_id));
            $vomacount = 0;
            $slots = 0;
            $usedvoice = 0;

            foreach ($query2->fetchall(PDO::FETCH_ASSOC) as $row2) {
                $lendable = true;

                $query3->execute(array($row2['id'], $reseller_id));

                foreach ($query3->fetchall(PDO::FETCH_ASSOC) as $row3) {
                    $lendtime = $row3['lendtime'];
                    $timeleft = round($lendtime - (strtotime('now') - strtotime($row3['started'])) / 60);

                    if (!isset($nextfree) or $timeleft < $nextfree) {
                        $nextfree = $timeleft;
                    }

                    $usedvoice++;
                    $freevoice--;
                    $lendable = false;
                }

                if ($lendable == true) {
                    $mastervoiceids[$masterid][] = $row2['id'];
                }

                $slots = $slots + $row2['slots'];
                $vomacount++;

            }

            if ($freevoice < $vocount) {
                $nextfree = 0;
            }

            if ($vomacount > 0) {
                $masterservers[$masterid] = (100 / $vomacount) * $usedvoice;
            }
        }

        asort($masterservers);
        $bestmaster = key($masterservers);

        if ($masterservers[$bestmaster] != 100) {
            $serveravailable = true;
            $counmaster=count($mastervoiceids[$bestmaster]);
            $arrayid=mt_rand(0, $counmaster-1);
            $tousevoiceid = $mastervoiceids[$bestmaster][$arrayid];
        }

        if ($serveravailable == true and isset($lendaccess) and ($lendaccess == 1 or $lendaccess == 2) and (($ui->id('xml', 1, 'post') and $ui->w('game', 20, 'post') or $ui->w('password', 20, 'post')))) {

            $fail = 0;

            $error = 'Error:';

            if ($ui->id('xml', 1, 'post') == 1) {
                $password = $xml->password;
                $slots= (int) $xml->slots;
                $lendtime= (int) $xml->lendtime;

            } else {
                $password = $ui->w('password', 20, 'post');
                $slots = $ui->id('slots', 3, 'post');
                $lendtime = $ui->id('time', 4, 'post');
            }

            if (!isid($slots, 3) or $slots > $vomaxplayer or $slots < $vominplayer) {
                $fail = 1;
                $error .= 'Slots</br>';
            }

            if (!isid($lendtime, 4) or $lendtime > $vomaxtime or $lendtime < $vomintime) {
                $fail = 1;
                $error .= 'Time</br>';
            }

            if (!wpreg_check($password, 20)) {
                $error .= 'Password</br>';
                $fail = 1;
            }

            if ($fail==0 and $freevoice>0) {
                $timeleft = $lendtime;
                $query = $sql->prepare("SELECT `bitversion`,`type`,`queryport`,AES_DECRYPT(`querypassword`,:aeskey) AS `decryptedquerypassword`,`rootid`,`addedby`,`publickey`,`ssh2ip`,AES_DECRYPT(`ssh2port`,:aeskey) AS `decryptedssh2port`,AES_DECRYPT(`ssh2user`,:aeskey) AS `decryptedssh2user`,AES_DECRYPT(`ssh2password`,:aeskey) AS `decryptedssh2password`,`serverdir`,`keyname`,`notified`,`defaultname`,`defaultwelcome`,`defaulthostbanner_url`,`defaulthostbanner_gfx_url`,`defaulthostbutton_tooltip`,`defaulthostbutton_url`,`defaulthostbutton_gfx_url`,`usedns` FROM `voice_masterserver` WHERE `active`='Y' AND `id`=:id AND `resellerid`=:reseller_id LIMIT 1");
                $query->execute(array(':aeskey' => $aeskey,':id' => $bestmaster,':reseller_id' => $reseller_id));
                foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
                    $addedby = $row['addedby'];
                    $queryport = $row['queryport'];
                    $querypassword = $row['decryptedquerypassword'];
                    $usedns = $row['usedns'];
                    $name = $row['defaultname'];
                    $welcome = $row['defaultwelcome'];
                    $banner_url = $row['defaulthostbanner_url'];
                    $banner_gfx = $row['defaulthostbanner_gfx_url'];
                    $tooltip = $row['defaulthostbutton_tooltip'];
                    $button_url = $row['defaulthostbutton_url'];
                    $button_gfx = $row['defaulthostbutton_gfx_url'];
                    $mnotified = $row['notified'];

                    if ($addedby == 2) {
                        $serverdir = $row['serverdir'];
                        $publickey = $row['publickey'];
                        $queryip = $row['ssh2ip'];
                        $ssh2port = $row['decryptedssh2port'];
                        $ssh2user = $row['decryptedssh2user'];
                        $ssh2password = $row['decryptedssh2password'];
                        $keyname = $row['keyname'];
                        $bitversion = $row['bitversion'];

                    } else if ($addedby == 1) {
                        $query2 = $sql->prepare("SELECT `ip` FROM `rserverdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                        $query2->execute(array($row['rootid'], $reseller_id));
                        $queryip = $query2->fetchColumn();
                    }

                    $connection = new TS3($queryip, $queryport,'serveradmin', $querypassword);
                    $errorcode = $connection->errorcode;

                    if (strpos($errorcode, 'error id=0') === false) {
                        $connecterror = $errorcode;

                    } else {
                        $query2 = $sql->prepare("SELECT `ip`,`port`,`dns`,`max_download_total_bandwidth`,`max_upload_total_bandwidth`,`localserverid` FROM `voice_server` WHERE `lendserver`='Y' AND `active`='Y' AND `id`=? AND `resellerid`=? LIMIT 1");
                        $query2->execute(array($tousevoiceid, $reseller_id));
                        foreach ($query2->fetchall(PDO::FETCH_ASSOC) as $row2) {
                            $voip = $row2['ip'];
                            $voport = $row2['port'];
                            $vodns = $row2['dns'];
                            $max_download_total_bandwidth = $row2['max_download_total_bandwidth'];
                            $max_upload_total_bandwidth = $row2['max_upload_total_bandwidth'];
                            $volocalserverid = $row2['localserverid'];
                            $server = ($usedns == 'N' or $vodns == null or $vodns == '') ? $voip . ':' . $voport : $vodns;
                        }

                        $connection->StartServer($volocalserverid);
                        $connection->ModServer($volocalserverid, $slots, $voip, $voport, $password, $name, $welcome, $max_download_total_bandwidth, $max_upload_total_bandwidth, $banner_url, $banner_gfx, $button_url, $button_gfx, $tooltip);
                        $reply = $connection->PermReset($volocalserverid);
                        $connection->CloseConnection();

                        $rcon = $reply[0]['token'];

                        if (!$ui->id('xml', 1, 'post')) {
                            $_SESSION['lend']['vs'] = $tousevoiceid;
                        }

                        $query = $sql->prepare("INSERT INTO `lendedserver` (`serverid`,`servertype`,`rcon`,`password`,`slots`,`started`,`lendtime`,`lenderip`,`resellerid`) VALUES (?,?,?,?,?,NOW(),?,?,?)");
                        $query->execute(array($tousevoiceid, 'v', $rcon, $password, $slots, $lendtime, $loguserip, $reseller_id));

                        $query = $sql->prepare("INSERT INTO `lendstats` (`lendDate`,`serverID`,`serverType`,`lendtime`,`slots`,`resellerID`) VALUES (NOW(),?,?,?,?,?) ON DUPLICATE KEY UPDATE `resellerID`=`resellerID`");
                        $query->execute(array($tousevoiceid, 'v', $lendtime, $slots, $reseller_id));
                    }
                }

                if (!isset($page_include) and !isset($connecterror) and $ui->id('xml', 1, 'post') == 1) {

                    $xml = new DOMDocument('1.0','utf-8');
                    $element = $xml->createElement('startserver');

                    $key = $xml->createElement('started', 'started');
                    $element->appendChild($key);

                    $key = $xml->createElement('ip', $voip);
                    $element->appendChild($key);

                    $key = $xml->createElement('port', $voport);
                    $element->appendChild($key);

                    $key = $xml->createElement('dns', $vodns);
                    $element->appendChild($key);

                    $key = $xml->createElement('slots', $slots);
                    $element->appendChild($key);

                    $key = $xml->createElement('lendtime', $lendtime);
                    $element->appendChild($key);

                    $key = $xml->createElement('token', $rcon);
                    $element->appendChild($key);

                    $key = $xml->createElement('password', $password);
                    $element->appendChild($key);

                    $key = $xml->createElement('timeleft', $timeleft);
                    $element->appendChild($key);

                    $xml->appendChild($element);

                    $xml->formatOutput = true;

                    header("Content-Type: text/xml; charset=UTF-8");

                    echo $xml->saveXML();

                } else if (!isset($page_include) and isset($connecterror)) {

                    echo $connecterror;

                } else {

                    if (!isset($nextfree)) {
                        $nextfree = 0;
                    }

                    if (isset($page_include)) {
                        $page_data->setCanonicalUrl($s);
                        $template_file = 'page_lenddata.tpl';

                    } else {

                        if (is_file(EASYWIDIR . '/template/' . $template_to_use . '/lenddata.tpl')) {
                            include(EASYWIDIR . '/template/' . $template_to_use . '/lenddata.tpl');

                        } else if (is_file(EASYWIDIR . '/template/default/lenddata.tpl')) {
                            include(EASYWIDIR . '/template/default/lenddata.tpl');

                        } else {
                            include(EASYWIDIR . '/template/lenddata.tpl');
                        }
                    }
                }

            } else if (isset($page_include)) {
                $template_file = 'Too slow';

            } else {
                echo 'tooslow';
            }

        } else if (isset($page_include) and $serveravailable == false and isset($lendaccess) and ($lendaccess == 1 or $lendaccess == 2)) {
            $template_file = 'Module deaktivated';

        } else if (!isset($page_include) and $serveravailable == false and isset($lendaccess) and ($lendaccess == 1 or $lendaccess == 2) and (($ui->id('xml', 1, 'post') and $ui->w('game', 20, 'post')) or $ui->w('password', 20, 'post'))) {
            echo 'too slow';

        } else if (isset($lendaccess) and ($lendaccess == 1 or $lendaccess == 2 or $lendaccess == 3)) {

            if (!isset($nextfree)) {
                $nextfree = 0;
            }

            if (($lendaccess == 1 or $lendaccess == 2) and !$ui->id('xml', 1, 'post')) {

                if (isset($page_include)) {
                    $page_data->setCanonicalUrl($s);
                    $template_file = 'page_lend.tpl';

                } else {

                    if (is_file(EASYWIDIR . '/template/' . $template_to_use . '/lend.tpl')) {
                        include(EASYWIDIR . '/template/' . $template_to_use . '/lend.tpl');

                    } else if (is_file(EASYWIDIR . '/template/default/lend.tpl')) {
                        include(EASYWIDIR . '/template/default/lend.tpl');

                    } else {
                        include(EASYWIDIR . '/template/lend.tpl');
                    }
                }

            } else if (!isset($page_include) and ($lendaccess == 1 or $lendaccess == 3) and $ui->id('xml', 1, 'post') == 1) {
                if (!isset($rcon)) {
                    $rcon = '';
                }

                if (!isset($nextcheck)) {
                    $nextcheck = '';
                }

                $xml = new DOMDocument('1.0','utf-8');
                $element = $xml->createElement('status');

                $key = $xml->createElement('demoupload');
                $element->appendChild($key);

                $key = $xml->createElement('nextfree', $nextfree);
                $element->appendChild($key);

                $key = $xml->createElement('nextcheck', $nextcheck);
                $element->appendChild($key);

                $key = $xml->createElement('mintime', $vomintime);
                $element->appendChild($key);

                $key = $xml->createElement('maxtime', $vomaxtime);
                $element->appendChild($key);

                $key = $xml->createElement('timesteps', $votimesteps);
                $element->appendChild($key);

                $key = $xml->createElement('minplayer', $vominplayer);
                $element->appendChild($key);

                $key = $xml->createElement('maxplayer', $vomaxplayer);
                $element->appendChild($key);

                $key = $xml->createElement('playersteps', $voplayersteps);
                $element->appendChild($key);

                $key = $xml->createElement('rcon', $rcon);
                $element->appendChild($key);

                $key = $xml->createElement('password', $password);
                $element->appendChild($key);

                $keyTS3 = $xml->createElement('ts3');

                $key = $xml->createElement('free', $freevoice);
                $keyTS3->appendChild($key);

                $key = $xml->createElement('total', $vocount);
                $keyTS3->appendChild($key);

                $element->appendChild($keyTS3);

                $xml->appendChild($element);

                $xml->formatOutput = true;
                header("Content-Type: text/xml; charset=UTF-8");
                echo $xml->saveXML();

            } else if (isset($page_include)) {
                $template_file = 'Module deactivated';

            } else {
                $template_file = 'Module deactivated';
            }

        } else {
            $template_file = 'Module deactivated';
        }
    }

} else if (!isset($template_file)){
   $template_file = 'Module deactivated';
}