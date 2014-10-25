<?php
/**
 * File: voice.php.
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

if ((!isset($admin_id) or $main != 1) or (isset($admin_id) and !$pa['voiceserver'])) {
	header('Location: admin.php');
	die;
}

include(EASYWIDIR . '/stuff/keyphrasefile.php');
include(EASYWIDIR . '/stuff/methods/class_ts3.php');
include(EASYWIDIR . '/stuff/methods/functions_ts3.php');
include(EASYWIDIR . '/stuff/methods/functions_ssh_exec.php');

$sprache = getlanguagefile('voice',$user_language,$reseller_id);
$loguserid = $admin_id;
$logusername = getusername($admin_id);
$logusertype = 'admin';
if ($reseller_id == 0) {
	$logreseller = 0;
	$logsubuser = 0;
} else {
    $logsubuser=(isset($_SESSION['oldid'])) ? $_SESSION['oldid'] : 0;
	$logreseller = 0;
}
if ($reseller_id != 0 and $admin_id != $reseller_id) {
	$reseller_id = $admin_id;
}
if ($ui->st('d', 'get') == 'ad' and is_numeric($licenceDetails['lVo']) and $licenceDetails['lVo']>0 and $licenceDetails['left']>0 and !is_numeric($licenceDetails['left'])) {

    $template_file = $gsprache->licence;

} else if ($ui->w('action', 4, 'post') and !token(true)) {

    $template_file = $spracheResponse->token;

} else if ($ui->st('d', 'get') == 'ad' and (!is_numeric($licenceDetails['lVo']) or $licenceDetails['lVo']>0) and ($licenceDetails['left']>0 or !is_numeric($licenceDetails['left']))) {

    if (!$ui->w('action',3, 'post')) {

        $table = array();
        $table2 = array();

        $query = $sql->prepare("SELECT `id`,`cname`,`vname`,`name` FROM `userdata` WHERE `resellerid`=? AND `accounttype`='u' ORDER BY `id` DESC");
        $query->execute(array($reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $table[$row['id']] = trim($row['cname'] . ' ' . $row['vname'] . ' ' . $row['name']);
        }

        $query = $sql->prepare("SELECT m.`id`,m.`ssh2ip`,m.`ips`,m.`usedns`,m.`defaultdns`,m.`type`,m.`rootid`,m.`maxserver`,m.`maxslots`,m.`active`,m.`resellerid`,m.`managedForID`,COUNT(v.`id`)*(100/m.`maxserver`) AS `serverpercent`,SUM(v.`slots`)*(100/m.`maxslots`) AS `slotpercent`,COUNT(v.`id`) AS `installedserver`,SUM(v.`slots`) AS `installedslots`,SUM(v.`usedslots`) AS `uslots`,r.`ip`  FROM `voice_masterserver` m LEFT JOIN `rserverdata` r ON m.`rootid`=r.`id` LEFT JOIN `voice_server` v ON m.`id`=v.`masterserver` GROUP BY m.`id` HAVING (`installedserver`<`maxserver` AND (`installedslots`<`maxslots` OR `installedslots` IS NULL) AND `active`='Y' AND (`resellerid`=? OR m.`managedForID`=?)) ORDER BY `slotpercent`,`serverpercent` ASC");
        $query->execute(array($reseller_id,$admin_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if ($row['type'] == 'ts3') {
                $type = $sprache->ts3;
            }

            $installedserver=($row['installedserver'] == null) ? 0 : $row['installedserver'];
            $installedslots=($row['installedslots'] == null) ? 0 : $row['installedslots'];
            $uslots=($row['uslots'] == null) ? 0 : $row['uslots'];

            $table2[] = array('id' => $row['id'], 'server' => $row['ssh2ip'], 'type' => $type,'maxserver' => $row['maxserver'], 'maxslots' => $row['maxslots'], 'installedserver' => $installedserver,'uslots' => $uslots,'installedslots' => $installedslots);
        }

        $template_file = 'admin_voiceserver_add.tpl';

    } else if ($ui->w('action', 3, 'post') == 'ad' and $ui->id('masterserver', 19, 'post') and $ui->id('customer', 19, 'post')) {
        $masterserver = $ui->id('masterserver',19, 'post');
        $customer = $ui->id('customer',19, 'post');

        $query = $sql->prepare("SELECT `cname` FROM `userdata` WHERE `id`=? AND `resellerid`=? AND `accounttype`='u' LIMIT 1");
        $query->execute(array($customer,$reseller_id));
        $cname = $query->fetchColumn();

        $query2 = $sql->prepare("SELECT m.*,COUNT(v.`id`) AS `installedserver`,SUM(v.`slots`) AS `installedslots`  FROM `voice_masterserver` m LEFT JOIN `voice_server` v ON m.`id`=v.`masterserver` WHERE m.`id`=? AND (m.`resellerid`=? OR m.`managedForID`=?) LIMIT 1");
        $query2->execute(array($masterserver,$reseller_id,$admin_id));
        foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
            $installedserver=($row2['installedserver'] == null) ? 0 : $row2['installedserver'];
            $installedslots=($row2['installedslots'] == null) ? 0 : $row2['installedslots'];
            if ($row2['usedns'] == 'Y') {
                $dns=strtolower($cname . '.' . $row2['defaultdns']);
                if ($row2['externalDefaultDNS'] == 'Y' and isid($row2['tsdnsServerID'],19)) {
                    $query3 = $sql->prepare("SELECT `defaultdns` FROM `voice_tsdns` WHERE `active`='Y' AND `id`=? AND `resellerid`=? LIMIT 1");
                    $query3->execute(array($row2['tsdnsServerID'],$reseller_id));
                    foreach ($query3->fetchAll(PDO::FETCH_ASSOC) as $row3) {
                        $dns=strtolower($cname . '.' . $row3['defaultdns']);
                    }
                }
            } else {
                $dns = '';
            }
            $dns = strtolower($dns);
            $maxserver = $row2['maxserver'];
            $maxslots = $row2['maxslots'];
            $addedby = $row2['addedby'];
            $name = $row2['defaultname'];
            $welcome = $row2['defaultwelcome'];
            $hostbanner_url = $row2['defaulthostbanner_url'];
            $hostbanner_gfx_url = $row2['defaulthostbanner_gfx_url'];
            $hostbutton_tooltip = $row2['defaulthostbutton_tooltip'];
            $hostbutton_url = $row2['defaulthostbutton_url'];
            $hostbutton_gfx_url = $row2['defaulthostbutton_gfx_url'];
            $defaultFlexSlotsFree = $row2['defaultFlexSlotsFree'];
            $defaultFlexSlotsPercent = $row2['defaultFlexSlotsPercent'];
            if ($addedby == 2) {
                $ips[] = $row2['ssh2ip'];
                foreach (preg_split('/\r\n/', $row2['ips'],-1,PREG_SPLIT_NO_EMPTY) as $ip) {
                    $ips[] = $ip;
                }
            } else if ($addedby == 1) {
                $query3 = $sql->prepare("SELECT `ip`,`altips` FROM `rserverdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query3->execute(array($row2['rootid'],$reseller_id));
                foreach ($query3->fetchAll(PDO::FETCH_ASSOC) as $row3) {
                    $ips[] = $row3['ip'];
                    foreach (preg_split('/\r\n/', $row3['altips'],-1,PREG_SPLIT_NO_EMPTY) as $ip) {
                        $ips[] = $ip;
                    }
                }
            }
            $portsArray = array();
            foreach ($ips as $serverIP) {
                $ports = array();
                $query = $sql->prepare("SELECT `port`,`port2`,`port3`,`port4`,`port5` FROM `gsswitch` WHERE `serverip`=? ORDER BY `port`");
                $query->execute(array($serverIP));
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    if (is_numeric($row['port'])) $ports[] = $row['port'];
                    if (is_numeric($row['port2'])) $ports[] = $row['port2'];
                    if (is_numeric($row['port3'])) $ports[] = $row['port3'];
                    if (is_numeric($row['port4'])) $ports[] = $row['port4'];
                    if (is_numeric($row['port5'])) $ports[] = $row['port5'];
                }
                $query = $sql->prepare("SELECT `port` FROM `voice_server` WHERE `ip`=?");
                $query->execute(array($ips[0]));
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    if (is_numeric($row['port']))$ports[] = $row['port'];
                }
                $portsArray[count($ports)] = array('ip' => $serverIP,'ports' => $ports);
            }
            $bestIP=current($portsArray);
            $ip = $bestIP['ip'];
            $ports=array_unique($bestIP['ports']);
            natsort($ports);
            $port=9987;
            while(in_array($port,$ports) or $port==10011) {
                $port++;
            }
        }
        if (isset($port) and isset($ips)) {
            $template_file = 'admin_voiceserver_add2.tpl';
        } else {
            $template_file = 'Error: Could not find the masterserver';
        }
    } else if ($ui->w('action',3, 'post') == 'ad2' and $ui->id('masterserver',19, 'post') and $ui->id('customer',19, 'post')) {
        $errors = array();
        if ($ui->startparameter('name', 'post')) {
            $name = $ui->startparameter('name', 'post');
        } else {
            $errors[] = $sprache->name;
        }
        if ($ui->ip('ip', 'post')) {
            $ip = $ui->ip('ip', 'post');
        } else {
            $errors[] = $sprache->ip;
        }
        if ($ui->port('port', 'post')) {
            $port = $ui->port('port', 'post');
            if (isset($ip)) {
                $query = $sql->prepare("SELECT `id` FROM `gsswitch` WHERE (`port`=:port OR `port2`=:port OR `port3`=:port OR `port4`=:port OR `port5`=:port) AND `serverip`=:serverip AND `resellerid`=:reseller_id LIMIT 1");
                $query->execute(array(':port' => $port,':serverip' => $ip,':reseller_id' => $reseller_id));
                $query2 = $sql->prepare("SELECT `id` FROM `voice_server` WHERE `port`=? AND `ip`=? AND `resellerid`=? LIMIT 1");
                $query2->execute(array($port,$ip,$reseller_id));
                $num_check_game = $query->rowCount()+$query2->rowCount();
                if ($num_check_game>0) $errors[] = $sprache->port;
            }
        } else {
            $errors[] = $sprache->port;
        }
        $masterserver = $ui->id('masterserver',19, 'post');
        $customer = $ui->id('customer',19, 'post');
        $dns=strtolower($ui->domain('dns', 'post'));
        $welcome = $ui->description('welcome', 'post');
        $hostbanner_url = $ui->url('hostbanner_url', 'post');
        $hostbanner_gfx_url = $ui->url('hostbanner_gfx_url', 'post');
        $hostbutton_tooltip = $ui->description('hostbutton_tooltip', 'post');
        $hostbutton_url = $ui->url('hostbutton_url', 'post');
        $hostbutton_gfx_url = $ui->url('hostbutton_gfx_url', 'post');
        $max_download_total_bandwidth=($ui->id('max_download_total_bandwidth',255, 'post')) ? $ui->id('max_download_total_bandwidth',255, 'post') : 65536;
        $max_upload_total_bandwidth=($ui->id('max_upload_total_bandwidth',255, 'post')) ? $ui->id('max_upload_total_bandwidth',255, 'post') : 65536;
        $maxtraffic=($ui->escaped('maxtraffic', 'post')==0 or $ui->escaped('maxtraffic', 'post') == '-1' or $ui->id('maxtraffic',255, 'post')) ? $ui->escaped('maxtraffic', 'post') : 1024;
        $flexSlots=($ui->active('flexSlots', 'post')) ? $ui->active('flexSlots', 'post') : 'N';
        $autoRestart=($ui->active('autoRestart', 'post')) ? $ui->active('autoRestart', 'post') : 'N';
        $active=($ui->active('active', 'post')) ? $ui->active('active', 'post') : 'Y';
        $backup=($ui->active('backup', 'post')) ? $ui->active('backup', 'post') : 'Y';
        $password=($ui->active('password', 'post')) ? $ui->active('password', 'post') : 'Y';
        $lendserver=($ui->active('lendserver', 'post')) ? $ui->active('lendserver', 'post') : 'Y';
        $forcebanner=($ui->active('forcebanner', 'post')) ? $ui->active('forcebanner', 'post') : 'Y';
        $forcebutton=($ui->active('forcebutton', 'post')) ? $ui->active('forcebutton', 'post') : 'Y';
        $forceservertag=($ui->active('forceservertag', 'post')) ? $ui->active('forceservertag', 'post') : 'Y';
        $forcewelcome=($ui->active('forcewelcome', 'post')) ? $ui->active('forcewelcome', 'post') : 'Y';
        $flexSlotsPercent = $ui->id('flexSlotsPercent',3, 'post');
        $flexSlotsFree = $ui->id('flexSlotsFree',11, 'post');
        if ($ui->id('slots',30, 'post')) {
            $slots = $ui->id('slots',30, 'post');
            $query = $sql->prepare("SELECT *,AES_DECRYPT(`querypassword`,:aeskey) AS `decryptedquerypassword`,AES_DECRYPT(`ssh2port`,:aeskey) AS `decryptedssh2port`,AES_DECRYPT(`ssh2user`,:aeskey) AS `decryptedssh2user`,AES_DECRYPT(`ssh2password`,:aeskey) AS `decryptedssh2password` FROM `voice_masterserver` WHERE `id`=:id AND (`resellerid`=:reseller_id OR `managedForID`=:managedForID) LIMIT 1");
            $query->execute(array(':aeskey' => $aeskey,':id' => $masterserver,':reseller_id' => $reseller_id,':managedForID' => $admin_id));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $active = $row['active'];
                $defaultname = $row['defaultname'];
                $addedby = $row['addedby'];
                $usedns = $row['usedns'];
                $defaultdns = $row['defaultdns'];
                $queryport = $row['queryport'];
                $querypassword = $row['decryptedquerypassword'];
                $maxserver = $row['maxserver'];
                $maxslots = $row['maxslots'];
                $serverdir = $row['serverdir'];
                $mnotified = $row['notified'];
                if ($addedby == 2) {
                    $publickey = $row['publickey'];
                    $queryip = $row['ssh2ip'];
                    $ssh2port = $row['decryptedssh2port'];
                    $ssh2user = $row['decryptedssh2user'];
                    $ssh2password = $row['decryptedssh2password'];
                    $keyname = $row['keyname'];
                    $bitversion = $row['bitversion'];
                } else if ($addedby == 1) {
                    $pselect2 = $sql->prepare("SELECT `ip` FROM `rserverdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                    $pselect2->execute(array($row['rootid'],$reseller_id));
                    foreach ($pselect2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                        $queryip = $row2['ip'];
                    }
                }
                $tsdnsServerID = $row['tsdnsServerID'];
                $externalDefaultDNS = $row['externalDefaultDNS'];
                if ($externalDefaultDNS== 'Y' and isid($tsdnsServerID,19)) {
                    $query2 = $sql->prepare("SELECT `defaultdns` FROM `voice_tsdns` WHERE `active`='Y' AND `id`=? AND `resellerid`=? LIMIT 1");
                    $query2->execute(array($tsdnsServerID,$reseller_id));
                    foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                        $defaultdns = $row2['defaultdns'];
                    }
                }
            }
            if (isset($maxslots) and isset($maxserver)) {
                $query = $sql->prepare("SELECT COUNT(`id`) AS `installedserver`,SUM(`slots`) AS `installedslots`  FROM `voice_server` WHERE `masterserver`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($masterserver,$reseller_id));
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $installedserver=($row['installedserver'] == null) ? 0 : $row['installedserver'];
                    $installedslots=($row['installedslots'] == null) ? 0 : $row['installedslots'];
                    if (($installedslots+$slots)>$maxslots) $errors[] = $gsprache->licence.' ('.$sprache->slots.')';
                    if ($installedserver>=$maxserver) $errors[] = $gsprache->licence;
                }
            } else {
                $errors[] = $sprache->rootserver;
            }
        } else {
            $errors[] = $sprache->slots;
        }
        if (count($errors)==0) {
            $initialpassword = '';
            if ($password== 'Y') $initialpassword=passwordgenerate(10);
            $connection=new TS3($queryip,$queryport,'serveradmin',$querypassword);
            $errorcode = $connection->errorcode;
            if (strpos($errorcode,'error id=0') === false) {
                $template_file = $errorcode."<br />";
            } else {
                $virtualserver_id = $connection->AddServer($slots,$ip,$port,$initialpassword,$name, array($forcewelcome,$welcome),$max_download_total_bandwidth,$max_upload_total_bandwidth, array($forcebanner,$hostbanner_url),$hostbanner_gfx_url, array($forcebutton,$hostbutton_url),$hostbutton_gfx_url,$hostbutton_tooltip);
            }
            if (isset($virtualserver_id) and isid($virtualserver_id,'255')) {
                if ($active == 'N') $connection->StopServer($virtualserver_id);
                $username=strtolower(getusername($customer));
                $connection->CloseConnection();
                $pinsert = $sql->prepare("INSERT INTO `voice_server` (`active`,`backup`,`lendserver`,`userid`,`masterserver`,`ip`,`port`,`slots`,`initialpassword`,`password`,`forcebanner`,`forcebutton`,`forceservertag`,`forcewelcome`,`max_download_total_bandwidth`,`max_upload_total_bandwidth`,`localserverid`,`dns`,`maxtraffic`,`serverCreated`,`flexSlots`,`flexSlotsFree`,`flexSlotsPercent`,`autoRestart`,`externalID`,`resellerid`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW(),?,?,?,?,?,?)");
                $pinsert->execute(array($active,$backup,$lendserver,$customer,$masterserver,$ip,$port,$slots,$initialpassword,$password,$forcebanner,$forcebutton,$forceservertag,$forcewelcome,$max_download_total_bandwidth,$max_upload_total_bandwidth,$virtualserver_id,$dns,$maxtraffic,$flexSlots,$flexSlotsFree,$flexSlotsPercent,$autoRestart,$ui->externalID('externalID', 'post'),$reseller_id));
                $ts3LocalID = $sql->lastInsertId();
                customColumns('T',$ts3LocalID,'save');
                $template_file = $spracheResponse->table_add;
                if ($usedns == 'Y') {
                    if ($dns==strtolower($username . '.' . $defaultdns)) {
                        $dns=strtolower($ts3LocalID . '.' . $defaultdns);
                        $query = $sql->prepare("UPDATE `voice_server` SET `dns`=? WHERE `id`=? LIMIT 1");
                        $query->execute(array($dns,$ts3LocalID));
                    }
                    if (isid($tsdnsServerID,19)) {
                        $query = $sql->prepare("SELECT *,AES_DECRYPT(`ssh2port`,:aeskey) AS `decryptedssh2port`,AES_DECRYPT(`ssh2user`,:aeskey) AS `decryptedssh2user`,AES_DECRYPT(`ssh2password`,:aeskey) AS `decryptedssh2password` FROM `voice_tsdns` WHERE `active`='Y' AND `id`=:id AND `resellerid`=:reseller_id LIMIT 1");
                        $query->execute(array(':aeskey' => $aeskey,':id' => $tsdnsServerID,':reseller_id' => $reseller_id));
                        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                            $publickey = $row['publickey'];
                            $queryip = $row['ssh2ip'];
                            $ssh2port = $row['decryptedssh2port'];
                            $ssh2user = $row['decryptedssh2user'];
                            $ssh2password = $row['decryptedssh2password'];
                            $serverdir = $row['serverdir'];
                            $keyname = $row['keyname'];
                            $bitversion = $row['bitversion'];
                        }
                    }
                    $template_file = tsdns('md',$queryip,$ssh2port,$ssh2user,$publickey,$keyname,$ssh2password,0,$serverdir,$bitversion, array($ip), array($port), array($dns),$reseller_id);
                }
                $loguseraction="%add% %voserver% $ip:$port";
                $insertlog->execute();
            } else if (isset($virtualserver_id)) {
                $template_file = 'TS errorcode: '.$virtualserver_id;
            }
        } else {
            $template_file = 'Error: '.implode('<br>',$errors);
        }
    } else {
        $template_file = "Error: No User or Server selected";
    }
} else if ($ui->st('d', 'get') == 'dl' and $ui->id('id', 10, 'get')) {

    $id = $ui->id('id', 10, 'get');

    $query = $sql->prepare("SELECT `ip`,`port`,`dns`,`masterserver`,`localserverid` FROM `voice_server` WHERE `id`=? AND `resellerid`=? LIMIT 1");
    $query->execute(array($id,$reseller_id));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $server=($row['dns'] == null or $row['dns'] == '') ? $row['ip'] . ':' . $row['port'] : $row['dns'].' ('.$row['ip'] . ':' . $row['port'].')';
        $dns = $row['dns'];
        $ip = $row['ip'];
        $port = $row['port'];
        $masterserver = $row['masterserver'];
        $localserverid = $row['localserverid'];
    }

    if (!$ui->w('action',2, 'post') and isset($server)) {

        $template_file = 'admin_voiceserver_dl.tpl';

    } else if ($ui->w('action',2, 'post') == 'dl' and isset($server)) {

        $query = $sql->prepare("SELECT *,AES_DECRYPT(`querypassword`,:aeskey) AS `decryptedquerypassword`,AES_DECRYPT(`ssh2port`,:aeskey) AS `decryptedssh2port`,AES_DECRYPT(`ssh2user`,:aeskey) AS `decryptedssh2user`,AES_DECRYPT(`ssh2password`,:aeskey) AS `decryptedssh2password` FROM `voice_masterserver` WHERE `id`=:id AND (`resellerid`=:reseller_id OR `managedForID`=:managedForID) LIMIT 1");
        $query->execute(array(':aeskey' => $aeskey,':id' => $masterserver,':reseller_id' => $reseller_id,':managedForID' => $admin_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $defaultdns = $row['defaultdns'];
            $serverdir = $row['serverdir'];
            $addedby = $row['addedby'];
            $usedns = $row['usedns'];
            $queryport = $row['queryport'];
            $querypassword = $row['decryptedquerypassword'];
            $mnotified = $row['notified'];
            $tsdnsServerID = $row['tsdnsServerID'];
            $externalDefaultDNS = $row['externalDefaultDNS'];
            if ($addedby == 2) {
                $publickey = $row['publickey'];
                $queryip = $row['ssh2ip'];
                $ssh2port = $row['decryptedssh2port'];
                $ssh2user = $row['decryptedssh2user'];
                $ssh2password = $row['decryptedssh2password'];
                $keyname = $row['keyname'];
                $bitversion = $row['bitversion'];
            } else if ($addedby == 1) {
                $query = $sql->prepare("SELECT `ip` FROM `rserverdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($row['rootid'],$reseller_id));
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $queryip = $row['ip'];
                }
            }
        }

        if (isset($queryip) and $ui->w('safeDelete',1, 'post') != 'D') {
            $connection=new TS3($queryip,$queryport,'serveradmin',$querypassword);
            $errorcode = $connection->errorcode;
            if (isset($localserverid) and strpos($errorcode,'error id=0') !== false) {
                $connection->DelServer($localserverid);
                $errorcode = $connection->errorcode;
                $connection->CloseConnection();
            }
        }

        if (($ui->w('safeDelete',1, 'post') != 'S' or (isset($errorcode) and strpos($errorcode,'error id=0') !== false))) {
            $query = $sql->prepare("DELETE FROM `voice_server` WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($id,$reseller_id));
            $query = $sql->prepare("UPDATE `jobs` SET `status`='2' WHERE `affectedID`=? AND `type`='vo'");
            $query->execute(array($id));
            customColumns('T',$id,'del');
            $template_file = $spracheResponse->table_del;
            $loguseraction="%del% %voserver% $server";
            $insertlog->execute();
            if (isset($usedns) and $usedns == 'Y') {
                if (isset($tsdnsServerID) and isid($tsdnsServerID,19)) {
                    $query = $sql->prepare("SELECT *,AES_DECRYPT(`ssh2port`,:aeskey) AS `decryptedssh2port`,AES_DECRYPT(`ssh2user`,:aeskey) AS `decryptedssh2user`,AES_DECRYPT(`ssh2password`,:aeskey) AS `decryptedssh2password` FROM `voice_tsdns` WHERE `active`='Y' AND `id`=:id AND (`resellerid`=:reseller_id OR `managedForID`=:managedForID) LIMIT 1");
                    $query->execute(array(':aeskey' => $aeskey,':id' => $tsdnsServerID,':reseller_id' => $reseller_id,':managedForID' => $admin_id));
                    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                        $publickey = $row['publickey'];
                        $queryip = $row['ssh2ip'];
                        $ssh2port = $row['decryptedssh2port'];
                        $ssh2user = $row['decryptedssh2user'];
                        $ssh2password = $row['decryptedssh2password'];
                        $serverdir = $row['serverdir'];
                        $keyname = $row['keyname'];
                        $bitversion = $row['bitversion'];
                    }
                }

                tsdns('dl',$queryip,$ssh2port,$ssh2user,$publickey,$keyname,$ssh2password,$mnotified,$serverdir,$bitversion, array($ip), array($port), array($dns),$reseller_id);

				tsbackup('delete', $ssh2user, $serverdir, $masterserver, $localserverid, '*');
				
                $query = $sql->prepare("DELETE b.* FROM `voice_server_backup` b LEFT JOIN `userdata` u ON b.`uid`=u.`id` LEFT JOIN `voice_server` v ON b.`sid`=v.`id` WHERE u.`id` IS NULL OR  v.`id` IS NULL");
                $query->execute();
            }

        } else if ( $ui->w('safeDelete',1, 'post') == 'S' and (!isset($errorcode) or strpos($errorcode,'error id=0') === false)) {
            $template_file = (isset($errorcode)) ? 'Error: '.$errorcode : 'Error: Could not connect to TS3 masterserver';
        }

    } else {
        $template_file = 'admin_404.tpl';
    }

} else if ($ui->st('d', 'get') == 'md' and $ui->id('id', 10, 'get')) {

    $id = $ui->id('id', 10, 'get');

    if (!$ui->w('action',2, 'post')) {
        $ips = array();
        $query = $sql->prepare("SELECT v.*,u.`cname` FROM `voice_server` v INNER JOIN `userdata` u ON v.`userid`=u.`id` WHERE v.`id`=? AND v.`resellerid`=? LIMIT 1");
        $query->execute(array($id,$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $cname = $row['cname'];
            $externalID = $row['externalID'];
            $active = $row['active'];
            $backup = $row['backup'];
            $lendserver = $row['lendserver'];
            $userid = $row['userid'];
            $masterserver = $row['masterserver'];
            $ip = $row['ip'];
            $port = $row['port'];
            $slots = $row['slots'];
            $initialpassword = $row['initialpassword'];
            $password = $row['password'];
            $localserverid = $row['localserverid'];
            $forcebanner = $row['forcebanner'];
            $forcebutton = $row['forcebutton'];
            $forceservertag = $row['forceservertag'];
            $forcewelcome = $row['forcewelcome'];
            $flexSlots = $row['flexSlots'];
            $flexSlotsPercent = $row['flexSlotsPercent'];
            $flexSlotsFree = $row['flexSlotsFree'];
            $autoRestart = $row['autoRestart'];
            $maxtraffic=($row['maxtraffic']>=0) ? round($row['maxtraffic']) : $row['maxtraffic'];
            $filetraffic=round(($row['filetraffic']/1024),2);
            $max_download_total_bandwidth = $row['max_download_total_bandwidth'];
            $max_upload_total_bandwidth = $row['max_upload_total_bandwidth'];
            $dns = $row['dns'];
            $query2 = $sql->prepare("SELECT m.`ssh2ip`,m.`ips`,m.`rootid`,m.`addedby`,m.`queryport`,AES_DECRYPT(m.`querypassword`,?) AS `decryptedquerypassword`,m.`maxserver`,m.`maxslots`,COUNT(v.`id`) AS `installedserver`,SUM(v.`slots`) AS `installedslots`  FROM `voice_masterserver` m LEFT JOIN `voice_server` v ON m.`id`=v.`masterserver` WHERE m.`id`=? AND (m.`resellerid`=? OR m.`managedForID`=?) LIMIT 1");
            $query2->execute(array($aeskey, $row['masterserver'],$reseller_id,$admin_id));
            foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                $installedserver=($row2['installedserver'] == null) ? 0 : $row2['installedserver'];
                $installedslots=($row2['installedslots'] == null) ? 0 : $row2['installedslots'];
                $queryport = $row2['queryport'];
                $querypassword = $row2['decryptedquerypassword'];
                $maxserver = $row2['maxserver'];
                $maxslots = $row2['maxslots'];
                $addedby = $row2['addedby'];
                if ($addedby == 2) {
                    $queryip = $row2['ssh2ip'];
                    $ips[] = $row2['ssh2ip'];
                    foreach (preg_split('/\r\n/', $row2['ips'],-1,PREG_SPLIT_NO_EMPTY) as $ip) $ips[] = $ip;
                } else if ($addedby == 1) {
                    $query3 = $sql->prepare("SELECT `ip`,`altips` FROM `rserverdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                    $query3->execute(array($row2['rootid'],$reseller_id));
                    foreach ($query3->fetchAll(PDO::FETCH_ASSOC) as $row3) {
                        $queryip = $row3['ip'];
                        $ips[] = $row3['ip'];
                        foreach (preg_split('/\r\n/', $row3['altips'],-1,PREG_SPLIT_NO_EMPTY) as $ip) $ips[] = $ip;
                    }
                }
                $ports = array();
                $query3 = $sql->prepare("SELECT `port`,`port2`,`port3`,`port4`,`port5` FROM `gsswitch` WHERE `serverip`=? ORDER BY `port`");
                $query3->execute(array($ips[0]));
                foreach ($query3->fetchAll(PDO::FETCH_ASSOC) as $row3) {
                    if (is_numeric($row3['port'])) $ports[] = $row3['port'];
                    if (is_numeric($row3['port2'])) $ports[] = $row3['port2'];
                    if (is_numeric($row3['port3'])) $ports[] = $row3['port3'];
                    if (is_numeric($row3['port4'])) $ports[] = $row3['port4'];
                    if (is_numeric($row3['port5'])) $ports[] = $row3['port5'];
                }
                $query3 = $sql->prepare("SELECT `port` FROM `voice_server` WHERE `ip`=?");
                $query3->execute(array($ips[0]));
                foreach ($query3->fetchAll(PDO::FETCH_ASSOC) as $row3) {
                    if (is_numeric($row3['port'])) $ports[] = $row3['port'];
                }
                asort($ports);
                $ports=implode(", ", $ports);
            }
        }
        if (isset($queryip) and isset($queryport)) {
            $connection=new TS3($queryip,$queryport,'serveradmin',$querypassword);
            $errorcode = $connection->errorcode;
            if (strpos($errorcode,'error id=0') === false) {
                $template_file = $errorcode."<br />";
            } else {
                $ips=array_unique($ips);
                $serverdetails = $connection->ServerDetails($localserverid);
                $name = $serverdetails['virtualserver_name'];
                $welcome = $serverdetails['virtualserver_welcomemessage'];
                $hostbanner_url = $serverdetails['virtualserver_hostbanner_url'];
                $hostbanner_gfx_url = $serverdetails['virtualserver_hostbanner_gfx_url'];
                $hostbutton_tooltip = $serverdetails['virtualserver_hostbutton_tooltip'];
                $hostbutton_url = $serverdetails['virtualserver_hostbutton_url'];
                $hostbutton_gfx_url = $serverdetails['virtualserver_hostbutton_gfx_url'];
                $template_file = "admin_voiceserver_md.tpl";
            }
            $connection->CloseConnection();
        }
        if (!isset($template_file)) $template_file = 'admin_404.tpl';
    } else if ($ui->w('action',2, 'post') == 'md'){
        $errors = array();
        $masterserver = 0;
        $slots = $ui->id('slots',30, 'post');
        $ip = $ui->ip('ip', 'post');
        if (!$ui->id('slots',30, 'post')) $errors[] = $sprache->slots;
        if (!$ui->ip('ip', 'post')) $errors[] = $sprache->ip;
        if ($ui->password('initialpassword',50, 'post') or (isset($ui->post['initialpassword']) and ($ui->post['initialpassword'] == '' or $ui->post['initialpassword'] == null))) $initialpassword = $ui->post['initialpassword'];
        else $errors[] = $sprache->password;

        $query = $sql->prepare("SELECT * FROM `voice_server` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id,$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $oldactive = $row['active'];
            $oldip = $row['ip'];
            $oldport = $row['port'];
            $olddns = $row['dns'];
            $oldslots = $row['slots'];
            $oldforcebanner = $row['forcebanner'];
            $oldforcebutton = $row['forcebutton'];
            $oldforcewelcome = $row['forcewelcome'];
            $masterserver = $row['masterserver'];
            $localserverid = $row['localserverid'];
            $query2 = $sql->prepare("SELECT SUM(`slots`) AS `installedslots`  FROM `voice_server` WHERE `masterserver`=? AND `resellerid`=? LIMIT 1");
            $query2->execute(array($masterserver,$reseller_id));
            $futureSlots= (int) $query2->fetchColumn()-$oldslots+$slots;
        }
        if (!isset($oldslots)) $errors[] = $gsprache->voiceserver.' ID';
        $query = $sql->prepare("SELECT *,AES_DECRYPT(`querypassword`,:aeskey) AS `decryptedquerypassword`,AES_DECRYPT(`ssh2port`,:aeskey) AS `decryptedssh2port`,AES_DECRYPT(`ssh2user`,:aeskey) AS `decryptedssh2user`,AES_DECRYPT(`ssh2password`,:aeskey) AS `decryptedssh2password` FROM `voice_masterserver` WHERE `id`=:id AND (`resellerid`=:reseller_id OR `managedForID`=:managedForID) LIMIT 1");
        $query->execute(array(':aeskey' => $aeskey,':id' => $masterserver,':reseller_id' => $reseller_id,':managedForID' => $admin_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if ($futureSlots>$row['maxslots']) $errors[] = $gsprache->licence.' ('.$sprache->slots.')';
            $serverdir = $row['serverdir'];
            $addedby = $row['addedby'];
            $usedns = $row['usedns'];
            $queryport = $row['queryport'];
            $querypassword = $row['decryptedquerypassword'];
            $mnotified = $row['notified'];
            $tsdnsServerID = $row['tsdnsServerID'];
            $externalDefaultDNS = $row['externalDefaultDNS'];
            if ($addedby == 2) {
                $publickey = $row['publickey'];
                $queryip = $row['ssh2ip'];
                $ssh2port = $row['decryptedssh2port'];
                $ssh2user = $row['decryptedssh2user'];
                $ssh2password = $row['decryptedssh2password'];
                $keyname = $row['keyname'];
                $bitversion = $row['bitversion'];
            } else if ($addedby == 1) {
                $query = $sql->prepare("SELECT `ip`,`bitversion` FROM `rserverdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($row['rootid'],$reseller_id));
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $queryip = $row['ip'];
                    $bitversion = $row['bitversion'];
                }
            }
        }
        if (!isset($queryport)) $errors[] = $sprache->rootserver;
        if ($ui->port('port', 'post')) {
            $port = $ui->port('port', 'post');
            if (isset($ip) and ($port != $oldport or $ip != $oldip)) {
                $query = $sql->prepare("SELECT `id` FROM `gsswitch` WHERE (`port`=:port OR `port2`=:port OR `port3`=:port OR `port4`=:port OR `port5`=:port) AND `serverip`=:serverip LIMIT 1");
                $query->execute(array(':port' => $port,':serverip' => $ip));
                $query2 = $sql->prepare("SELECT `id` FROM `voice_server` WHERE `port`=? AND `ip`=? LIMIT 1");
                $query2->execute(array($port,$ip));
                $num_check_game = $query->rowcount()+$query2->rowcount();
                if ($num_check_game>0) $errors[] = $sprache->port;
            }
        } else {
            $errors[] = $sprache->port;
        }
        $max_download_total_bandwidth=($ui->id('max_download_total_bandwidth',255, 'post')) ? $ui->id('max_download_total_bandwidth',255, 'post') : 65536;
        $max_upload_total_bandwidth=($ui->id('max_upload_total_bandwidth',255, 'post')) ? $ui->id('max_upload_total_bandwidth',255, 'post') : 65536;
        $maxtraffic=($ui->escaped('maxtraffic', 'post')==0 or $ui->escaped('maxtraffic', 'post') == '-1' or $ui->id('maxtraffic',255, 'post')) ? $ui->escaped('maxtraffic', 'post') : 1024;
        $flexSlots=($ui->active('flexSlots', 'post')) ? $ui->active('flexSlots', 'post') : 'N';
        $autoRestart=($ui->active('autoRestart', 'post')) ? $ui->active('autoRestart', 'post') : 'N';
        $active=($ui->active('active', 'post')) ? $ui->active('active', 'post') : 'Y';
        $backup=($ui->active('backup', 'post')) ? $ui->active('backup', 'post') : 'Y';
        $password=($ui->active('password', 'post')) ? $ui->active('password', 'post') : 'Y';
        $lendserver=($ui->active('lendserver', 'post')) ? $ui->active('lendserver', 'post') : 'Y';
        $forcebanner=($ui->active('forcebanner', 'post')) ? $ui->active('forcebanner', 'post') : 'Y';
        $forcebutton=($ui->active('forcebutton', 'post')) ? $ui->active('forcebutton', 'post') : 'Y';
        $forceservertag=($ui->active('forceservertag', 'post')) ? $ui->active('forceservertag', 'post') : 'Y';
        $forcewelcome=($ui->active('forcewelcome', 'post')) ? $ui->active('forcewelcome', 'post') : 'Y';
        $dns=strtolower($ui->domain('dns', 'post'));

        if (isset($oldport) and ($dns != $olddns or $port != $oldport or $ip != $oldip)) {
            $query = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `voice_server` WHERE `id`!=? AND `dns`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($id,$dns,$reseller_id));
            $query2 = $sql->prepare("SELECT COUNT(`tsdnsID`) AS `amount` FROM `voice_dns` WHERE `dnsID`!=? AND `dns`=? AND `resellerID`=? LIMIT 1");
            $query2->execute(array($tsdnsServerID,$dns,$reseller_id));

            if ($query->fetchColumn() > 0 or $query2->fetchColumn() > 0) {
                $errors[] = 'DNS already in use';

            } else if (count($errors) == 0) {

                if ($usedns == 'Y') {

                    if (isid($tsdnsServerID, 19)) {
                        $query = $sql->prepare("SELECT *,AES_DECRYPT(`ssh2port`,:aeskey) AS `decryptedssh2port`,AES_DECRYPT(`ssh2user`,:aeskey) AS `decryptedssh2user`,AES_DECRYPT(`ssh2password`,:aeskey) AS `decryptedssh2password` FROM `voice_tsdns` WHERE `active`='Y' AND `id`=:id AND (`resellerid`=:reseller_id OR `managedForID`=:managedForID) LIMIT 1");
                        $query->execute(array(':aeskey' => $aeskey,':id' => $tsdnsServerID,':reseller_id' => $reseller_id,':managedForID' => $admin_id));
                        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                            $publickey = $row['publickey'];
                            $queryip = $row['ssh2ip'];
                            $ssh2port = $row['decryptedssh2port'];
                            $ssh2user = $row['decryptedssh2user'];
                            $ssh2password = $row['decryptedssh2password'];
                            $serverdir = $row['serverdir'];
                            $keyname = $row['keyname'];
                            $bitversion = $row['bitversion'];
                        }
                    }

                    $template_file = tsdns('md',$queryip,$ssh2port,$ssh2user,$publickey,$keyname,$ssh2password,$mnotified,$serverdir,$bitversion, array($ip,$oldip), array($port,$oldport), array($dns,$olddns),$reseller_id);
                }
            }
        }

        $welcome = $ui->description('welcome', 'post');
        $tooltip = $ui->description('hostbutton_tooltip', 'post');
        $banner_url = $ui->url('hostbanner_url', 'post');
        $banner_gfx = $ui->url('hostbanner_gfx_url', 'post');
        $button_url = $ui->url('hostbutton_url', 'post');
        $button_gfx = $ui->url('hostbutton_gfx_url', 'post');
        $flexSlots = $ui->active('flexSlots', 'post');
        $flexSlotsPercent = $ui->id('flexSlotsPercent',3, 'post');
        $flexSlotsFree = $ui->id('flexSlotsFree',11, 'post');

        if (count($errors)==0) {
            $name = $ui->startparameter('name', 'post');
            $connection=new TS3($queryip,$queryport,'serveradmin',$querypassword);
            $errorcode = $connection->errorcode;
            if (strpos($errorcode,'error id=0') === false) {
                $template_file = $errorcode;
            } else {
                $connection->ModServer($localserverid,$slots,$ip,$port,$initialpassword,$name,$welcome,$max_download_total_bandwidth,$max_upload_total_bandwidth,$banner_url,$banner_gfx,$button_url,$button_gfx,$tooltip);
                if ($forcebanner != $oldforcebanner and $forcebanner== 'Y') {
                    $removelist[] = 'b_virtualserver_modify_hostbanner';
                    $removelist[] = 'i_needed_modify_power_virtualserver_modify_hostbanner';
                } else if ($forcebanner != $oldforcebanner and $forcebanner== 'N') {
                    $addlist[] = 'b_virtualserver_modify_hostbanner';
                    $addlist[] = 'i_needed_modify_power_virtualserver_modify_hostbanner';
                }
                if ($forcebutton != $oldforcebutton and $forcebutton == 'Y') {
                    $removelist[] = 'b_virtualserver_modify_hostbutton';
                    $removelist[] = 'i_needed_modify_power_virtualserver_modify_hostbutton';
                } else if ($forcebutton != $oldforcebutton and $forcebutton == 'N') {
                    $addlist[] = 'b_virtualserver_modify_hostbutton';
                    $addlist[] = 'i_needed_modify_power_virtualserver_modify_hostbutton';
                }
                if ($forcewelcome != $oldforcewelcome and $forcewelcome == 'Y') {
                    $removelist[] = 'b_virtualserver_modify_welcomemessage';
                    $removelist[] = 'i_needed_modify_power_virtualserver_modify_welcomemessage';
                } else if ($forcewelcome != $oldforcewelcome and $forcewelcome == 'N') {
                    $addlist[] = 'b_virtualserver_modify_welcomemessage';
                    $addlist[] = 'i_needed_modify_power_virtualserver_modify_welcomemessage';
                }
                if (isset($addlist)) $connection->AdminPermissions ($localserverid,'add',$addlist);
                if (isset($removelist)) $connection->AdminPermissions ($localserverid,'del',$removelist);
                if (isset($oldactive) and $oldactive != $active and $active == 'N') {
                    $connection->StopServer($localserverid);
                } else if (isset($oldactive) and $oldactive != $active and $active == 'Y') {
                    $connection->StartServer($localserverid);
                }
                $connection->CloseConnection();
            }
            $query = $sql->prepare("UPDATE `voice_server` SET `active`=?,`backup`=?,`lendserver`=?,`ip`=?,`port`=?,`slots`=?,`password`=?,`forcebanner`=?,`forcebutton`=?,`forceservertag`=?,`forcewelcome`=?,`max_download_total_bandwidth`=?,`max_upload_total_bandwidth`=?,`dns`=?,`flexSlots`=?,`flexSlotsFree`=?,`flexSlotsPercent`=?,`maxtraffic`=?,`autoRestart`=?,`externalID`=? WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($active,$backup,$lendserver,$ip,$port,$slots,$password,$forcebanner,$forcebutton,$forceservertag,$forcewelcome,$max_download_total_bandwidth,$max_upload_total_bandwidth,$dns,$flexSlots,$flexSlotsFree,$flexSlotsPercent,$maxtraffic,$autoRestart,$ui->externalID('externalID', 'post'),$id,$reseller_id));
            customColumns('T',$id,'save');
            $template_file = $spracheResponse->table_add;
            $loguseraction="%mod% %voserver% $ip:$port";
            $insertlog->execute();
        } else {
            $template_file = 'Error: '.implode('<br/>',$errors);
        }
    } else {
        $template_file = 'admin_404.tpl';
    }
} else {
    $o = $ui->st('o', 'get');
    if ($ui->st('o', 'get') == 'da') {
        $orderby = 'u.`cname` DESC';
    } else if ($ui->st('o', 'get') == 'aa') {
        $orderby = 'u.`cname` ASC';
    } else if ($ui->st('o', 'get') == 'dn') {
        $orderby = 'u.`name` DESC,u.`vname` DESC';
    } else if ($ui->st('o', 'get') == 'an') {
        $orderby = 'u.`name` ASC,u.`vname` ASC';
    } else if ($ui->st('o', 'get') == 'du') {
        $orderby = 'v.`uptime` DESC';
    } else if ($ui->st('o', 'get') == 'au') {
        $orderby = 'v.`uptime` ASC';
    } else if ($ui->st('o', 'get') == 'di') {
        $orderby = 'v.`id` DESC';
    } else if ($ui->st('o', 'get') == 'ai') {
        $orderby = 'v.`id` ASC';
    } else if ($ui->st('o', 'get') == 'dv') {
        $orderby = 'v.`localserverid` DESC';
    } else if ($ui->st('o', 'get') == 'av') {
        $orderby = 'v.`localserverid` ASC';
    } else if ($ui->st('o', 'get') == 'dt') {
        $orderby = 'v.`active` DESC';
    } else if ($ui->st('o', 'get') == 'at') {
        $orderby = 'v.`active` ASC';
    } else if ($ui->st('o', 'get') == 'dp') {
        $orderby = 'v.`jobPending` DESC';
    } else if ($ui->st('o', 'get') == 'ap') {
        $orderby = 'v.`jobPending` ASC';
    } else if ($ui->st('o', 'get') == 'dl') {
        $orderby = 'v.`lendserver` DESC';
    } else if ($ui->st('o', 'get') == 'al') {
        $orderby = 'v.`lendserver` ASC';
    } else if ($ui->st('o', 'get') == 'dm') {
        $orderby = 'v.`ip`,v.`port` DESC';
    } else {
        $orderby = 'v.`ip`,v.`port` ASC';
        $o = 'am';
    }
    $table = array();
    $query = $sql->prepare("SELECT v.*,m.`type`,m.`usedns`,u.`cname`,u.`name`,u.`vname` FROM `voice_server` v LEFT JOIN `voice_masterserver` m ON v.`masterserver`=m.`id` LEFT JOIN `userdata` u ON v.`userid`=u.`id` WHERE v.`resellerid`=? ORDER BY $orderby LIMIT $start,$amount");
    $query->execute(array($reseller_id));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $dns = $row['dns'];
        if ($row['active'] == 'Y') {
            if ($row['uptime']>1) {
                $imgName = '16_ok';
                $imgAlt='online';
            } else {
                $imgName = '16_error';
                $imgAlt='offline';
            }
        } else {
            $imgName = '16_bad';
            $imgAlt='inactive';
        }
        $lendserver=($row['lendserver'] == 'Y') ? $gsprache->yes : $gsprache->no;
        $jobPending=($row['jobPending'] == 'Y') ? $gsprache->yes : $jobPending = $gsprache->no;
        if ($row['type'] == 'ts3') {
            $password=($row['initialpassword'] != null and $row['initialpassword'] != '') ? '?password='.$row['initialpassword'] : '';
            $type = $sprache->ts3;
            $server=($row['usedns'] == 'Y' and $dns != null or $dns != '') ? '<a href="ts3server://'.$row['dns'].$password.'">'.$row['ip'] . ':' . $row['port'].'</a><br />( '.$row['dns'].' )' : '<a href="ts3server://'.$row['ip'] . ':' . $row['port'].$password.'">'.$row['ip'] . ':' . $row['port'].'</a>';
        }
        $usedSlots = $row['usedslots'];
        if ($row['usedslots'] == null) $usedSlots = 0;
        $flexSlots = '';
        if ($row['flexSlots'] == 'Y' and $row['flexSlotsCurrent'] == null) $flexSlots = $row['slots'] . '/';
        else if ($row['flexSlots'] == 'Y') $flexSlots = $row['flexSlotsCurrent'] . '/';
        $usage = $usedSlots. '/' . $flexSlots.$row['slots'];
        $days=floor($row['uptime']/86400);
        $hours=floor(($row['uptime']-($days*86400))/3600);
        $minutes=floor(($row['uptime']-($days*86400)-($hours*3600))/60);
        $uptime = $days.'D '.$hours.'H '.$minutes.'M';
        $userid = $row['userid'];
        $table[] = array('id' => $row['id'], 'active' => $row['active'], 'virtualID' => $row['localserverid'], 'img' => $imgName,'alt' => $imgAlt,'usage' => $usage,'uptime' => $uptime, 'server' => $server,'cname' => $row['cname'], 'names' => trim($row['name'] . ' ' . $row['vname']),'userid' => $userid,'lendserver' => $lendserver,'type' => $type,'jobPending' => $jobPending);
    }
    $query = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `voice_server` WHERE `resellerid`=?");
    $query->execute(array($reseller_id));
    $colcount = $query->fetchColumn();
    $next = $start+$amount;
    $vor=($colcount>$next) ? $start+$amount : $start;
    $back = $start - $amount;
    $zur = ($back >= 0) ? $start - $amount : $start;
    $pageamount = ceil($colcount / $amount);
    $pages[] = '<a href="admin.php?w=vo&amp;d=md&amp;o='.$o.'&amp;a=' . (!isset($amount)) ? 20 : $amount . ($start==0) ? '&p=0" class="bold">1</a>' : '&p=0">1</a>';
    $i = 2;
    while ($i<=$pageamount) {
        $selectpage = ($i - 1) * $amount;
        $pages[]=($start==$selectpage) ? '<a href="admin.php?w=vo&amp;d=md&amp;o='.$o.'&amp;a=' . $amount . '&p=' . $selectpage . '" class="bold">' . $i . '</a>' : '<a href="admin.php?w=vo&amp;d=md&amp;o='.$o.'&amp;a=' . $amount . '&p=' . $selectpage . '">' . $i . '</a>';
        $i++;
    }
    $pages=implode(', ',$pages);
    $template_file = "admin_voiceserver_list.tpl";
}