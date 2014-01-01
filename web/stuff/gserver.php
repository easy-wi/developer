<?php

/**
 * File: gserver.php.
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

if ((!isset($admin_id) or $main != 1) or (isset($admin_id) and !$pa['gserver'])) {
    header('Location: admin.php');
    die;
}

include(EASYWIDIR . '/stuff/keyphrasefile.php');
$sprache = getlanguagefile('gserver',$user_language,$reseller_id);
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

if ($reseller_id != 0 and $admin_id != $reseller_id) {
	$reseller_id = $admin_id;
}
if ($ui->st('d', 'get') == 'ad' and is_numeric($licenceDetails['lG']) and $licenceDetails['lG']>0 and $licenceDetails['left']>0 and !is_numeric($licenceDetails['left'])) {
    $template_file = $gsprache->licence;
} else if ($ui->w('action', 4, 'post') and !token(true)) {
    $template_file = $spracheResponse->token;
} else if ($ui->st('d', 'get') == 'ad' and (!is_numeric($licenceDetails['lG']) or $licenceDetails['lG']>0) and ($licenceDetails['left']>0 or !is_numeric($licenceDetails['left']))) {

    if (!$ui->w('action',3, 'post')) {

        $table = array();
        $query = $sql->prepare("SELECT `id`,`cname`,`vname`,`name` FROM `userdata` WHERE `resellerid`=? AND `accounttype`='u' ORDER BY `id` DESC");
        $query->execute(array($reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $table[$row['id']] = trim($row['cname'] . ' ' . $row['vname'] . ' ' . $row['name']);
        }

        $table2 = array();
        $i = 0;
        $available = 0;

        $query = $sql->prepare("SELECT r.`description`,r.`id`,r.`ip`,r.`altips`,r.`maxslots`,r.`maxserver`,r.`maxserver`-COUNT(DISTINCT s.`id`) AS `freeserver`,r.`active` AS `hostactive`,r.`resellerid` AS `resellerid` FROM `rserverdata` r LEFT JOIN `gsswitch` s ON s.`rootID`=r.`id` GROUP BY r.`id` HAVING ((`freeserver` > 0 OR `freeserver` IS NULL) AND `hostactive`='Y' AND `resellerid`=?) ORDER BY `freeserver` DESC");
        $query->execute(array($reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

            $used = 0;
            $available = 0;
            $maxslots = $row['maxslots'];
            $maxserver = $row['maxserver'];
            $rootid = $row['id'];

            $i = 0;
            $query2 = $sql->prepare("SELECT `slots`,`queryNumplayers` FROM `gsswitch` WHERE `rootID`=? AND `resellerid`=? AND `active`='Y'");
            $query2->execute(array($rootid,$reseller_id));
            foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                $used += $row2['queryNumplayers'];
                $available += $row2['slots'];
                $i++;
            }

            $percentslots =  ($maxslots != null) ? $available/($maxslots/100) : 0;
            $percenserver = ($maxserver != null) ? $i/($maxserver/100) : 0;

            $serverusage[$rootid] = array('slots' => $percentslots, 'server' => $percenserver);

            $table2[] = array('id' => $rootid, 'ip' => ($row['description'] != null and $row['description'] != '') ? $row['description'] : $row['ip']);
        }

        $query = $sql->prepare("SELECT s.`description`,s.`shorten` FROM `servertypes` s WHERE s.`resellerid`=? AND EXISTS (SELECT m.`id` FROM `rservermasterg` m WHERE m.`servertypeid`=s.`id` LIMIT 1) ORDER BY s.`description` ASC");
        $query->execute(array($reseller_id));
        $table3 = array();
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $table3[] = array('shorten' => $row['shorten'], 'description' => $row['description']);
        }

        $installedserver = 0;
        $maxserver = 0;
        $max = 0;
        $maxslots = 0;
        $used = 0;

        if (isset($serverusage)){

            asort($serverusage);
            $bestserver = key($serverusage);

            $query = $sql->prepare("SELECT `maxslots`,`maxserver` FROM `rserverdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($bestserver,$reseller_id));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $maxslots = $row['maxslots'];
                $maxserver = $row['maxserver'];
            }

            $query = $sql->prepare("SELECT `slots`,`queryNumplayers` FROM `gsswitch` WHERE `rootID`=? AND `resellerid`=? AND `active`='Y'");
            $query->execute(array($bestserver,$reseller_id));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $used += $row['queryNumplayers'];
                $max += $row['slots'];
                $installedserver++;
            }
        }

        $template_file = 'admin_gserver_add.tpl';

    } else if ($ui->w('action',3, 'post') == 'ad' and (!is_numeric($licenceDetails['lG']) or $licenceDetails['lG']>0) and ($licenceDetails['left']>0 or !is_numeric($licenceDetails['left']))) {
        if ($ui->escaped('shorten', 'post') and $ui->id('customer',19, 'post')) {
            $customer = $ui->id('customer',19, 'post');
            $count = 0;
            foreach ($ui->escaped('shorten', 'post') as $i) $count++;
            $i = 0;
            if ($ui->id('rserver',19, 'post')) {
                $id = $ui->id('rserver',19, 'post');
                $query = $sql->prepare("SELECT `ip`,`altips` FROM `rserverdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($id,$reseller_id));
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $ip = $row['ip'];
                    $altips = preg_split('/\r\n/', $row['altips'],-1,PREG_SPLIT_NO_EMPTY);
                }
                $table = array();
                $gamestring = $count;
                foreach($ui->escaped('shorten', 'post') as $shortencase => $shorten) {
                    if (gamestring($shorten)) {
                        $query = $sql->prepare("SELECT t.*,r.`installing` FROM `servertypes` t LEFT JOIN `rservermasterg` r ON t.`id`=r.`servertypeid` WHERE t.`shorten`=? AND t.`resellerid`=? AND r.`serverid`=? LIMIT 1");
                        $query->execute(array($shorten,$reseller_id,$id));
                        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                            $steamgame = $row['steamgame'];
                            $gamemod = $row['gamemod'];
                            if (!isset($portMax) or $portMax < $row['portMax']) {
                                $portStep = $row['portStep'];
                                $portMax = $row['portMax'];
                                $port = $row['portOne'];
                                $port2 = $row['portTwo'];
                                $port3 = $row['portThree'];
                                $port4 = $row['portFour'];
                                $port5 = $row['portFive'];
                            }
                            $gamestring .= '_';
                            $gamestring .= ($gamemod == 'Y' and $row['gamemod2'] != '') ? $shorten . '.' . $row['gamemod2']: $shorten;
                            $cmd = stripslashes($row['cmd']);
                            if ($row['installing'] == 'N') {
                                $installing = false;
                            } else {
                                $installing = true;
                            }

                            if ($row['gamebinary'] == 'srcds_run') {
                                $upload = 1;
                            } else {
                                $upload = 0;
                            }
                            $table[] = array('description' => $row['description'], 'id' => $row['id'], 'steamgame' => $row['steamgame'], 'shorten' => $shorten,'gamebinary' => $row['gamebinary'], 'binarydir' => $row['binarydir'], 'modfolder' => $row['modfolder'], 'fps' => $row['fps'], 'slots' => $row['slots'], 'map' => $row['map'], 'mapGroup' => $row['mapGroup'], 'cmd' => $cmd,'tic' => $row['tic'], 'upload' => $upload,'installing' => $installing);
                            $i++;
                        }
                        if ($query->rowcount()==0) {
                            $table[] = array('installing' => true);
                        }
                    }
                }
                $used = 0;
                $max = 0;
                $unbound = 0;
                $c = 0;
                $installedserver = 0;
                $numplayers = 0;
                $query = $sql->prepare("SELECT `maxslots`,`maxserver`,`cores`,`hyperthreading` FROM `rserverdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($id,$reseller_id));
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $maxslots = $row['maxslots'];
                    $maxserver = $row['maxserver'];
                    $corecount=($row['hyperthreading'] == 'Y') ? $row['cores']*2 : $row['cores'];
                    while ($c<$corecount) {
                        $cores[$c] = 0;
                        $c++;
                    }
                    $c--;
                }
                $query = $sql->prepare("SELECT `slots`,`cores`,`taskset`,`queryNumplayers` FROM `gsswitch` WHERE `rootID`=? AND `resellerid`=?");
                $query->execute(array($id,$reseller_id));
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $ce=explode(',', $row['cores']);
                    $cc=count($ce);
                    if ($row['taskset'] == 'Y' and $cc>0) foreach ($ce as $uc) $cores[$uc] = $cores[$uc]+round(1/$cc,2);
                    else $unbound++;
                    $used += $row['queryNumplayers'];
                    $max += $row['slots'];
                    $installedserver++;
                }

                $query = $sql->prepare("SELECT `port`,`port2`,`port3`,`port4`,`port5` FROM `gsswitch` WHERE `serverip`=? ORDER BY `port`");
                $query->execute(array($ip));
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    if (port($row['port'])) {
                        $ports[] = $row['port'];
                    }
                    if (port($row['port2'])) {
                        $ports[] = $row['port2'];
                    }
                    if (port($row['port3'])) {
                        $ports[] = $row['port3'];
                    }
                    if (port($row['port4'])) {
                        $ports[] = $row['port4'];
                    }
                    if (port($row['port5'])) {
                        $ports[] = $row['port5'];
                    }
                }

                $query = $sql->prepare("SELECT `port` FROM `voice_server` WHERE `ip`=?");
                $query->execute(array($ip));
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    if (port($row['port'])) {
                        $ports[] = $row['port'];
                    }
                }

                if (isset($ports) and isset($portMax)) {
                    $ports=array_unique($ports);
                    asort($ports);
                    if ($portMax==1) {
                        while (in_array($port,$ports)) $port += $portStep;
                        $port2 = '';
                        $port3 = '';
                        $port4 = '';
                        $port5 = '';
                    } else if ($portMax==2) {
                        while (in_array($port,$ports) or in_array($port2,$ports)) {
                            $port += $portStep;
                            $port2 += $portStep;
                        }
                        $port3 = '';
                        $port4 = '';
                        $port5 = '';
                    } else if ($portMax==3) {
                        while (in_array($port,$ports) or in_array($port2,$ports) or in_array($port3,$ports)) {
                            $port += $portStep;
                            $port2 += $portStep;
                            $port3 += $portStep;
                        }
                        $port4 = '';
                        $port5 = '';
                    } else if ($portMax==4) {
                        while (in_array($port,$ports) or in_array($port2,$ports) or in_array($port3,$ports) or in_array($port4,$ports)) {
                            $port += $portStep;
                            $port2 += $portStep;
                            $port3 += $portStep;
                            $port4 += $portStep;
                        }
                        $port5 = '';
                    } else {
                        while (in_array($port,$ports) or in_array($port2,$ports) or in_array($port3,$ports) or in_array($port4,$ports) or in_array($port5,$ports)) {
                            $port += $portStep;
                            $port2 += $portStep;
                            $port3 += $portStep;
                            $port4 += $portStep;
                            $port5 += $portStep;
                        }
                    }
                    $ports=implode(", ",$ports);
                } else {
                    if (!isset($portMax)) {
                        $port = '';
                        $port2 = '';
                        $port3 = '';
                        $port4 = '';
                        $port5 = '';
                    } else if ($portMax==1) {
                        $port2 = '';
                        $port3 = '';
                        $port4 = '';
                        $port5 = '';
                    } else if ($portMax==2) {
                        $port3 = '';
                        $port4 = '';
                        $port5 = '';
                    } else if ($portMax==3) {
                        $port4 = '';
                        $port5 = '';
                    } else if ($portMax==4) {
                        $port5 = '';
                    }
                    $ports = '';
                }
                $password=passwordgenerate(10);
                $template_file = "admin_gserver_add2.tpl";
            } else {
                $template_file = 'admin_404.tpl';
            }
        } else {
            $template_file = $sprache->no_game;
        }
    } else if ($ui->w('action',3, 'post')=="ad2" and (!is_numeric($licenceDetails['lG']) or $licenceDetails['lG']>0) and ($licenceDetails['left']>0 or !is_numeric($licenceDetails['left']))) {
        $error = array();
        if (!$ui->gamestring('gamestring', 'post')) $error[] = 'Gamestring';
        if (!$ui->id('id',19, 'post')) $error[] = 'rootID';
        if (!$ui->id('customer',19, 'post')) $error[] = 'userID';
        if (!$ui->id('slots',3, 'post')) $error[] = 'Slots';
        if (!$ui->ip('ip', 'post')) $error[] = 'IP';
        if (!$ui->port('port', 'post')) $error[] = 'Port';
        if (count($error)==0) {
            $gamestringPost = $ui->gamestring('gamestring', 'post');
            $serverid = $ui->id('id',19, 'post');
            $slots = $ui->id('slots',3, 'post');
            $serverip = $ui->ip('ip', 'post');
            $autoRestart = ($ui->active('autoRestart', 'post')) ? $ui->active('autoRestart', 'post') : 'N';
            $active = ($ui->active('active', 'post')) ? $ui->active('active', 'post') : 'Y';
            $taskset = ($ui->active('taskset', 'post')) ? $ui->active('taskset', 'post') : 'N';
            $eacallowed = ($ui->active('eacallowed', 'post')) ? $ui->active('eacallowed', 'post') : 'N';
            $brandname = ($ui->active('brandname', 'post')) ? $ui->active('brandname', 'post') : 'Y';
            $war = ($ui->active('war', 'post')) ? $ui->active('war', 'post') : 'N';
            $tvenable = ($ui->active('tvenable', 'post')) ? $ui->active('tvenable', 'post') : 'N';
            $lendserver = ($ui->active('lendserver', 'post')) ? $ui->active('lendserver', 'post') : 'N';
            $pallowed = ($ui->active('pallowed', 'post')) ? $ui->active('pallowed', 'post') : 'N';
            $customer = $ui->id('customer',19, 'post');
            $port = $ui->port('port', 'post');
            $gsfolder = $serverip . '_' . $port;
            $server = $serverip . ':' . $port;
            $port2 = $ui->port('port2', 'post');
            $port3 = $ui->port('port3', 'post');
            $port4 = $ui->port('port4', 'post');
            $port5 = $ui->port('port5', 'post');
            $minram = $ui->id('minram',10, 'post');
            $maxram = $ui->id('maxram',10, 'post');
            $ftppass = $ui->password('password',50, 'post');
            $query = $sql->prepare("SELECT `id` FROM `gsswitch` WHERE `rootID`=? AND `serverip`=? AND `port`=? AND `userid`!=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($serverid,$serverip,$port,$customer,$reseller_id));
            if ($query->rowCount() == 0) {
                include(EASYWIDIR . '/stuff/ssh_exec.php');
                $gamestring = array();
                $template_file = '';
                $rdata=serverdata('root',$serverid,$aeskey);
                $sship = $rdata['ip'];
                $sshport = $rdata['port'];
                $sshuser = $rdata['user'];
                $sshpass = $rdata['pass'];
                $hyperthreading = $rdata['hyperthreading'];
                $rootcores = $rdata['cores'];
                $usedcores = array();
                $c = 0;
                $corecount=($hyperthreading== 'Y') ? $rootcores*2 : $rootcores;
                $postCores=(isset($ui->post['cores'])) ? (array)$ui->post['cores'] : array();
                while ($c<$corecount) {
                    if (in_array($c,$postCores)) $usedcores[] = $c;
                    $c++;
                }
                $usedcores=implode(',',$usedcores);
                $query = $sql->prepare("SELECT `id` FROM `gsswitch` WHERE `rootID`=? AND `serverip`=? AND `port`=? AND `userid`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($serverid,$serverip,$port,$customer,$reseller_id));
                $switchID = $query->fetchColumn();
                if (!isid($switchID,19)) {
                    $query = $sql->prepare("INSERT INTO `gsswitch` (`taskset`,`cores`,`userid`,`pallowed`,`eacallowed`,`lendserver`,`serverip`,`rootID`,`tvenable`,`port`,`port2`,`port3`,`port4`,`port5`,`minram`,`maxram`,`slots`,`war`,`brandname`,`autoRestart`,`ftppassword`,`resellerid`,`serverid`,`stopped`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,AES_ENCRYPT(?,?),?,1,'Y')");
                    $query->execute(array($taskset,$usedcores,$customer,$pallowed,$eacallowed,$lendserver,$serverip,$serverid,$tvenable,$port,$port2,$port3,$port4,$port5,$minram,$maxram,$slots,$war,$brandname,$autoRestart,$ftppass,$aeskey,$reseller_id));
                    $switchID = $sql->lastInsertId();
                    customColumns('G',$switchID,'save');
                }
                $gamestring_awk=explode('_', $gamestringPost);
                $gamecount = $gamestring_awk[0];
                $i = 1;
                if (!isid($switchID,19)) {
                    $i = $gamecount+1;
                }
                while ($i <= $gamecount) {
                    $shorten = $gamestring_awk[$i];
                    $modcmd = '';
                    $query = $sql->prepare("SELECT `id`,`modcmds` FROM `servertypes` WHERE `shorten`=? AND `resellerid`=? LIMIT 1");
                    $query->execute(array($shorten,$reseller_id));
                    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                        $servertype = $row['id'];
                        foreach (explode("\r\n", $row['modcmds']) as $line) {
                            if (preg_match('/^(\[[\w\/\.\-\_\= ]{1,}\])$/',$line)) {
                                $name = trim($line,'[]');
                                $ex=preg_split("/\=/",$name,-1,PREG_SPLIT_NO_EMPTY);
                                if (isset($ex[1]) and trim($ex[1]) == 'default') {
                                    $modcmd = trim($ex[0]);
                                    $doNot = true;
                                }
                                if (!isset($doNot)) {
                                    $modcmd = trim($ex[0]);
                                }
                            }
                        }
                    }
                    $installedserver = 0;
                    $mod_awk=explode('.', $shorten);
                    if (isset($mod_awk[1])) {
                        $shorten = $mod_awk[0];
                        $gamemod = 'Y';
                        $gamemod2 = $mod_awk[1];
                    } else {
                        $gamemod = 'N';
                        $gamemod2 = '';
                    }
                    $fps = $ui->id("fps_$shorten",6, 'post');
                    $map = $ui->mapname("map_$shorten", 'post');
                    $mapGroup = $ui->mapname("mapGroup_$shorten", 'post');
                    $cmd = $ui->startparameter("cmd_$shorten", 'post');
                    $owncmd = $ui->active("owncmd_$shorten", 'post');
                    $tic = $ui->id("tic_$shorten",5, 'post');
                    $userfps = ($ui->active("user_fps_$shorten", 'post')) ? $ui->active("user_fps_$shorten", 'post') : 'N';
                    $usertick = ($ui->active("user_tick_$shorten", 'post')) ? $ui->active("user_tick_$shorten", 'post') : 'N';
                    $usermap = ($ui->active("user_map_$shorten", 'post')) ? $ui->active("user_map_$shorten", 'post') : 'N';
                    $user_uploaddir = ($ui->active("user_uploaddir_$shorten", 'post')) ? $ui->active("user_uploaddir_$shorten", 'post') : 'N';
                    if ($ui->id("upload_$shorten",1, 'post')) {
                        $upload = $ui->id("upload_$shorten",1, 'post');
                        if ($upload > 1) {
                            $uploaddir = $ui->url("uploaddir_$shorten", 'post');
                        } else {
                            $uploaddir = '';
                        }
                    } else {
                        $upload = 0;
                        $uploaddir = '';
                    }
                    $num_check3 = 0;
                    $query = $sql->prepare("SELECT `id` FROM `gsswitch` WHERE `rootID`=? AND `port`=? AND `userid`=? AND `resellerid`=? LIMIT 1");
                    $query->execute(array($serverid,$port,$customer,$reseller_id));
                    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                        $query = $sql->prepare("SELECT s.`id` FROM `serverlist` s LEFT JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE s.`switchID`=? AND t.`shorten`=? AND s.`resellerid`=? LIMIT 1");
                        $query->execute(array($row['id'],$shorten,$reseller_id));
                        $num_check3 = $query->rowCount();
                    }
                    if ($num_check3==0) {
                        if ($eacallowed== 'Y') {
                            if ($shorten=="cstrike" or $shorten=="czero") {
                                if ($war== 'Y') {
                                    $anticheat = 5;
                                } else {
                                    $anticheat = 6;
                                }
                            } else if ($shorten=="css" or $shorten=="tf" or $shorten=="cod4") {
                                $anticheat = 3;
                            } else {
                                $anticheat = 1;
                            }
                        } else {
                            $anticheat = 1;
                        }
                        $gamestring[] = $shorten;
                        $query = $sql->prepare("INSERT INTO `serverlist` (`servertype`,`anticheat`,`switchID`,`fps`,`map`,`mapGroup`,`cmd`,`modcmd`,`owncmd`,`tic`,`gamemod`,`gamemod2`,`userfps`,`usertick`,`usermap`,`user_uploaddir`,`upload`,`uploaddir`,`resellerid`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,AES_ENCRYPT(?,?),?)");
                        $query->execute(array($servertype,$anticheat,$switchID,$fps,$map,$mapGroup,$cmd,$modcmd,$owncmd,$tic,$gamemod,$gamemod2,$userfps,$usertick,$usermap,$user_uploaddir,$upload,$uploaddir,$aeskey,$reseller_id));
                        if ($shorten == $ui->escaped('primary', 'post')) {
                            $query = $sql->prepare("SELECT `id` FROM `serverlist` WHERE `switchID`=? AND `resellerid`=? ORDER BY `id` DESC LIMIT 1");
                            $query->execute(array($switchID,$reseller_id));
                            $lastServerID = $query->fetchColumn();
                        }
                        $template_file .= $shorten.": ".$sprache->server_installed.'<br />';
                    } else {
                        $template_file .= $shorten.": ".$sprache->error_folder.'<br />';
                    }
                    $i++;
                }

                $template_file = $spracheResponse->table_add;

                if (isid($switchID,19)) {

                    if (!isset($lastServerID)) {
                        $query = $sql->prepare("SELECT `id` FROM `serverlist` WHERE `switchID`=? AND `resellerid`=? ORDER BY `id` DESC LIMIT 1");
                        $query->execute(array($switchID,$reseller_id));
                        $lastServerID = $query->fetchColumn();
                    }

                    $query = $sql->prepare("SELECT `cname` FROM `userdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                    $query->execute(array($customer,$reseller_id));
                    $cname = $query->fetchColumn();

                    $query = $sql->prepare("UPDATE `gsswitch` SET `serverid`=? WHERE `id`=? AND `resellerid`=? LIMIT 1");
                    $query->execute(array($lastServerID,$switchID,$reseller_id));

                    $webhostdomain = webhostdomain($reseller_id);
                    $gsuser = $cname . '-' . $switchID;

                    $cmds = array("./control.sh add ${gsuser} ${ftppass} ${sshuser} " . passwordgenerate(10));

                    if ($ui->id('installGames',1, 'post') == 2) {
                        $gamestring = array($ui->escaped('primary', 'post'));
                    }

                    $gamestring = count($gamestring) . '_' . implode('_', $gamestring);

                    if ($ui->id('installGames',1, 'post') != 3) {
                        $cmds[]="sudo -u ${gsuser} ./control.sh addserver ${gsuser} ${gamestring} ${gsfolder} 1";
                    }

                    $reply = ssh2_execute('gs', $serverid, $cmds);

                } else {
                    $reply = "Could not insert data into database";
                }
                if ($reply === false) {
                    $template_file = $sprache->cant_install.'<br />';
                } else {
                    $loguseraction="%add% %gserver% $serverip:$port";
                    $insertlog->execute();
                }
            } else {
                $template_file = $sprache->error_port;
            }
        } else {
            $template_file = 'Error: '.implode('<br />',$error);
        }
    } else {
        $template_file = 'admin_404.tpl';
    }
} else if ($ui->st('d', 'get') == 'dl' and $ui->id('id', 10, 'get')) {
    $server_id = $ui->id('id', 10, 'get');
    if (!isset($action)) {
        $table = array();
        $query = $sql->prepare("SELECT `serverip`,`port`,`serverid` FROM `gsswitch` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($server_id,$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $serverip = $row['serverip'];
            $port = $row['port'];
        }
        $query = $sql->prepare("SELECT s.`id`,t.`description`,t.`shorten` FROM `serverlist` s LEFT JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE s.`switchID`=? AND s.`resellerid`=?");
        $query->execute(array($server_id,$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $table[] = array('id' => $row['id'], 'description' => $row['description'], 'shorten' => $row['shorten']);
        }
        if (isset($serverip) and isset($port)) {
            $template_file = 'admin_gserver_dl.tpl';
        } else {
            $template_file = 'admin_404.tpl';
        }
    } else if ($action == 'dl') {
        if ($ui->w('safeDelete',1, 'post') != 'D') include(EASYWIDIR . "/stuff/ssh_exec.php");
        $query = $sql->prepare("SELECT `newlayout`,`serverip`,`port`,`userid`,`rootID`,AES_DECRYPT(`ppassword`,?) AS `protectedpw`,AES_DECRYPT(`ftppassword`,?) AS `ftpPWD` FROM `gsswitch` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($aeskey,$aeskey,$server_id,$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $newlayout = $row['newlayout'];
            $rootID = $row['rootID'];
            $serverip = $row['serverip'];
            $port = $row['port'];
            $userID = $row['userid'];
            $ftppass = $row['ftpPWD'];
            $ftppass2 = $row['protectedpw'];
            $gsfolder = $serverip . '_' . $port;
            if ($ui->w('safeDelete',1, 'post') != 'D') {
                $cmds=gsrestart($server_id,'so',$aeskey,$reseller_id);
                if (is_array($cmds) and count($cmds)>0) ssh2_execute('gs', $row['rootID'],$cmds);
            }
        }
        $query = $sql->prepare("SELECT `cname` FROM `userdata` WHERE `id`=? LIMIT 1");
        $query->execute(array($userID));
        $customer2 = $query->fetchColumn();
        $rdata=serverdata('root',$rootID,$aeskey);
        $sship = $rdata['ip'];
        $sshport = $rdata['port'];
        $sshuser = $rdata['user'];
        $sshpass = $rdata['pass'];
        if (isset($ui->post['id'])) {
            $count=count($ui->post['id']);
        } else {
            $count = 0;
        }
        $gamestring = $count;
        $description = '';
        if ($count>0 and $ui->id('id',19, 'post') and (is_array($ui->id('id',19, 'post')) or is_object($ui->id('id',19, 'post')))) {
            $query = $sql->prepare("SELECT t.`shorten`,t.`description` FROM `serverlist` s LEFT JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE s.`id`=? AND s.`resellerid`=? LIMIT 1");
            $query2 = $sql->prepare("DELETE FROM `serverlist` WHERE id=? AND `resellerid`=? LIMIT 1");
            $query3 = $sql->prepare("DELETE FROM `addons_installed` WHERE `serverid`=? AND `resellerid`=?");
            foreach($ui->id('id',19, 'post') as $id) {
                $query->execute(array($id,$reseller_id));
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $shorten = $row['shorten'];
                    $description .= $row['description'].'<br />';
                    $gamestring .= '_'.$shorten;
                    $query2->execute(array($id,$reseller_id));
                    $query3->execute(array($id,$reseller_id));
                }
            }
        }
        $query = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `gsswitch` WHERE `rootID`=? AND `userid`=? AND `resellerid`=?");
        $query->execute(array($rootID,$userID,$reseller_id));
        $num3 = $query->fetchColumn();
        $query = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `serverlist` WHERE `switchID`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($server_id,$reseller_id));
        $num3_2 = $query->fetchColumn();
        $server_customer = $customer2;
        if ($newlayout == 'Y') {
            $num3 = $num3_2;
            $server_customer = $customer2 . '-' . $server_id;
        }
        if ($num3_2==0) {
            $query = $sql->prepare("DELETE FROM `gsswitch` WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($server_id,$reseller_id));
            customColumns('G',$server_id,'del');
            $query = $sql->prepare("DELETE s.* FROM `serverlist` s LEFT JOIN `gsswitch` g ON s.`switchID`=g.`id` WHERE g.`id` IS NULL");
            $query->execute();
            $query = $sql->prepare("DELETE a.* FROM `addons_installed` a LEFT JOIN `serverlist` s ON a.`serverid`=s.`id` WHERE s.`id` IS NULL");
            $query->execute();
            $query = $sql->prepare("DELETE a.* FROM `addons_installed` a LEFT JOIN `userdata` u ON a.`userid`=u.`id` WHERE u.`id` IS NULL");
            $query->execute();
            $query = $sql->prepare("DELETE FROM `gserver_restarts` WHERE `switchID`=? AND `resellerid`=?");
            $query->execute(array($server_id,$reseller_id));
        }
        $cmds = array();
        if (($num3>0 and $newlayout == 'N') or ($newlayout == 'Y' and $num3_2>0)) {
            if ($ui->w('safeDelete',1, 'post') != 'D') $cmds[]="sudo -u $server_customer ./control.sh delserver $server_customer $gamestring $gsfolder";;
            if ($ui->w('safeDelete',1, 'post') != 'D') $cmds[]="sudo -u $server_customer-p ./control.sh delserver $server_customer-p $gamestring $gsfolder";
            $template_file = $sprache->delete_server.": ";
            $template_file .= $description."<br />";
            $query = $sql->prepare("SELECT `id` FROM `serverlist` WHERE `switchID`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($server_id,$reseller_id));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $query = $sql->prepare("UPDATE `gsswitch` SET `serverid`=? WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($row['id'],$server_id,$reseller_id));
            }
        } else {
            if ($ui->w('safeDelete',1, 'post') != 'D') $cmds[]="sudo -u $server_customer ./control.sh delscreen $server_customer" ;
            if ($ui->w('safeDelete',1, 'post') != 'D') $cmds[]="sudo -u $server_customer-p ./control.sh delscreen $server_customer-p";
            if ($ui->w('safeDelete',1, 'post') != 'D') $cmds[]="./control.sh delCustomer $server_customer";
            $template_file = $sprache->no_server_left;
        }
        if (isset($rootID)) {
            include (EASYWIDIR . '/stuff/ssh_exec.php');
            ssh2_execute('gs', $rootID, $cmds);
        }
        $loguseraction="%del% %gserver% $serverip:$port";
        $insertlog->execute();
        $template_file = $spracheResponse->table_del;
    } else {
        $template_file = 'admin_404.tpl';
    }
} else if ($ui->st('d', 'get') == 'md' and $ui->id('id', 10, 'get')) {
    $server_id = $ui->id('id', 10, 'get');
    if (!isset($action)) {
        $table = array();
        $query = $sql->prepare("SELECT *,AES_DECRYPT(`ftppassword`,?) AS `ftp` FROM `gsswitch` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($aeskey,$server_id,$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $active = $row['active'];
            $password = $row['ftp'];
            $lendserver = $row['lendserver'];
            $serverip = $row['serverip'];
            $rootID = $row['rootID'];
            $war = $row['war'];
            $tvenable = $row['tvenable'];
            $port = $row['port'];
            $port2 = $row['port2'];
            $port3 = $row['port3'];
            $port4 = $row['port4'];
            $port5 = $row['port5'];
            $minram = $row['minram'];
            $maxram = $row['maxram'];
            $slots = $row['slots'];
            $pallowed = $row['pallowed'];
            $eacallowed = $row['eacallowed'];
            $brandname = $row['brandname'];
            $taskset = $row['taskset'];
            $autoRestart = $row['autoRestart'];
            $usedcores = array();
            foreach (preg_split('/\,/', $row['cores'],-1,PREG_SPLIT_NO_EMPTY) as $uc) {
                $usedcores[] = $uc;
            }
            $query2 = $sql->prepare("SELECT s.*,AES_DECRYPT(s.`uploaddir`,?) AS `decypteduploaddir`,t.`shorten`,t.`description`,t.`gamebinary`,t.`gamebinary`,t.`binarydir`,t.`modfolder` FROM `serverlist` s LEFT JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE s.`switchID`=? AND s.`resellerid`=?");
            $query2->execute(array($aeskey,$server_id,$reseller_id));
            $i = 0;
            $gamestringtemp = '';
            foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                $shorten = $row2['shorten'];
                $owncmd = $row2['owncmd'];
                if ($owncmd== 'Y') {
                    $style = '';
                } else {
                    $style="style=\"display: none; border-spacing: 0px;\"";
                }
                if ($row2['gamebinary'] == 'srcds_run') {
                    if ($row2['upload']>0) {
                        $upload = $row2['upload'];
                    } else {
                        $upload = 1;
                    }
                    $uploaddir = $row2['decypteduploaddir'];
                } else {
                    $upload = 0;
                    $uploaddir = '';
                }
                $gamestringtemp .= "_$shorten";
                $cmd=stripslashes($row2['cmd']);
                $table[] = array('id' => $row2['id'], 'shorten' => $row2['shorten'], 'description' => $row2['description'], 'gamebinary' => $row2['gamebinary'], 'binarydir' => $row2['binarydir'], 'modfolder' => $row2['modfolder'], 'fps' => $row2['fps'], 'map' => $row2['map'], 'mapGroup' => $row2['mapGroup'], 'cmd' => $cmd,'tic' => $row2['tic'],'upload' => $upload,'uploaddir' => $uploaddir,'userfps' => $row2['userfps'], 'usertick' => $row2['usertick'], 'usermap' => $row2['usermap'], 'user_uploaddir' => $row2['user_uploaddir'], 'owncmd' => $row2['owncmd'], 'style' => $style);
                $i++;
            }
            $gamestring = $i.$gamestringtemp;
        }
        if (isset($rootID)) {
            $ports = array();
            $cores = array();
            $query = $sql->prepare("SELECT `ip`,`altips`,`hyperthreading`,`cores` FROM `rserverdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($rootID,$reseller_id));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $ip = $row['ip'];
                $altips=explode("\r\n", $row['altips']);
                $hyperthreading = $row['hyperthreading'];
                $rootcores = $row['cores'];
                $c = 0;
                if ($hyperthreading== 'Y') {
                    $corecount = $rootcores*2;
                } else {
                    $corecount = $rootcores;
                }
                while ($c<$corecount) {
                    $cores[$c] = 0;
                    $c++;
                }
                $c--;

            }
            $unbound = 0;
            $query = $sql->prepare("SELECT `port`,`port2`,`port3`,`port4`,`port5`,`taskset`,`cores` FROM `gsswitch` WHERE `rootID`=? AND `resellerid`=?");
            $query->execute(array($rootID,$reseller_id));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                if (port($row['port'])){
                    $ports[] = $row['port'];
                }
                if (port($row['port2'])){
                    $ports[] = $row['port2'];
                }
                if (port($row['port3'])){
                    $ports[] = $row['port3'];
                }
                if (port($row['port4'])){
                    $ports[] = $row['port4'];
                }
                if (port($row['port5'])){
                    $ports[] = $row['port5'];
                }
                $ce=explode(',', $row['cores']);
                $cc=count($ce);
                if ($row['taskset'] == 'Y' and $cc>0) {
                    foreach ($ce as $uc) {
                        $cores[$uc] = $cores[$uc]+round(1/$cc,2);
                    }
                } else {
                    $unbound++;
                }
            }
            $query = $sql->prepare("SELECT `port` FROM `voice_server` WHERE `ip`=? ORDER BY `port`");
            $query->execute(array($ip));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                if (port($row['port'])){
                    $ports[] = $row['port'];
                }
            }
            $ports=array_unique($ports);
            asort($ports);
            $ports=implode(", ", $ports);
            $template_file = "admin_gserver_md.tpl";
        } else {
            $template_file = 'admin_404.tpl';
        }
    } else if ($action == 'md'){
        $error = array();
        if (!$ui->gamestring('gamestring', 'post')) {
            $error[] = 'Gamestring';
        }
        if (!$ui->id('slots',3, 'post')) {
            $error[] = 'Slots';
        }
        if (!$ui->ip('ip', 'post')) {
            $error[] = 'IP';
        }
        if (!$ui->port('port', 'post')) {
            $error[] = 'Port';
        }
        if (count($error)==0) {
            $serverip_new = $ui->ip('ip', 'post');
            $gamestring = $ui->gamestring('gamestring', 'post');
            $ftppassword_new = $ui->password('password',50, 'post');
            $slots = $ui->id('slots',3, 'post');
            $customer = $ui->id('customer',19, 'post');
            $port_new = $ui->port('port', 'post');
            $port2 = $ui->port('port2', 'post');
            $port3 = $ui->port('port3', 'post');
            $port4 = $ui->port('port4', 'post');
            $port5 = $ui->port('port5', 'post');
            $minram = $ui->id('minram',10, 'post');
            $maxram = $ui->id('maxram',10, 'post');
            $tvenable = ($ui->active('tvenable', 'post')) ? $ui->active('tvenable', 'post') : 'N';
            $autoRestart = ($ui->active('autoRestart', 'post')) ? $ui->active('autoRestart', 'post') : 'N';
            $active = ($ui->active('active', 'post')) ? $ui->active('active', 'post') : 'Y';
            $taskset = ($ui->active('taskset', 'post')) ? $ui->active('taskset', 'post') : 'N';
            $eacallowed = ($ui->active('eacallowed', 'post')) ? $ui->active('eacallowed', 'post') : 'N';
            $brandname = ($ui->active('brandname', 'post')) ? $ui->active('brandname', 'post') : 'Y';
            $war = ($ui->active('war', 'post')) ? $ui->active('war', 'post') : 'N';
            $lendserver = ($ui->active('lendserver', 'post')) ? $ui->active('lendserver', 'post') : 'N';
            $pallowed = ($ui->active('pallowed', 'post')) ? $ui->active('pallowed', 'post') : 'N';
            $ftppass = $ui->password('password',50, 'post');
            $pallowed = $ui->active('pallowed', 'post');
            include(EASYWIDIR . '/stuff/ssh_exec.php');
            $query = $sql->prepare("SELECT `newlayout`,`userid`,AES_DECRYPT(`ftppassword`,?) AS `ftp`,AES_DECRYPT(`ppassword`,?) AS `ppass`,`active`,`rootID`,`serverip`,`port`,`port2`,`port3`,`port4`,`port5`,`userid`,`slots` FROM `gsswitch` WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($aeskey,$aeskey,$server_id,$reseller_id));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $rootID = $row['rootID'];
                $userID = $row['userid'];
                $newlayout = $row['newlayout'];
                $serverip_old = $row['serverip'];
                $ftppass_old = $row['ftp'];
                $protectedpw = $row['ppass'];
                $port_old = $row['port'];
                $port2_old = $row['port2'];
                $port3_old = $row['port3'];
                $port4_old = $row['port4'];
                $port5_old = $row['port5'];
                $slots_old = $row['slots'];
                $userID = $row['userid'];
                $active_old = $row['active'];
                $query2 = $sql->prepare("SELECT `cname` FROM `userdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query2->execute(array($userID,$reseller_id));
                $server_customer = $query2->fetchColumn();
                if ($row['newlayout'] == 'Y') {
                    $server_customer = $server_customer . '-' . $server_id;
                }
            }
            $rdata=serverdata('root',$rootID,$aeskey);
            $sship = $rdata['ip'];
            $sshport = $rdata['port'];
            $sshuser = $rdata['user'];
            $sshpass = $rdata['pass'];
            $hyperthreading = $rdata['hyperthreading'];
            $rootcores = $rdata['cores'];
            $c = 0;
            $corecount=($hyperthreading== 'Y') ? $rootcores*2 : $rootcores;
            $postCores = (isset($ui->post['cores'])) ? (array) $ui->post['cores'] : array();
            $usedcores = array();
            while ($c<$corecount) {
                if (in_array($c,$postCores)) $usedcores[] = $c;
                $c++;
            }
            $usedcores=implode(',',$usedcores);
            $template_file = '';
            $query = $sql->prepare("SELECT `id` FROM `gsswitch` WHERE (`port`=:port OR `port2`=:port OR `port3`=:port OR `port4`=:port OR `port5`=:port) AND `id`!=:switchID AND `serverip`=:serverip AND `resellerid`=:reseller_id LIMIT 1");
            $query->execute(array(':port' => $port_new,':switchID' => $server_id,':serverip' => $serverip_new,':reseller_id' => $reseller_id));
            $num_check_game = $query->rowcount();
            $query = $sql->prepare("SELECT `id` FROM `voice_server` WHERE `port`=? AND `ip`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($port_new,$serverip_new,$reseller_id));
            $num_check_game += $query->rowCount();
            if ($port2_old != $port2) {
                $query = $sql->prepare("SELECT `id` FROM `gsswitch` WHERE (`port`=:port OR `port2`=:port OR `port3`=:port OR `port4`=:port OR `port5`=:port) AND `id`!=:switchID AND `serverip`=:serverip AND `resellerid`=:reseller_id LIMIT 1");
                $query->execute(array(':port' => $port2,':switchID' => $server_id,':serverip' => $serverip_new,':reseller_id' => $reseller_id));
                $num_check_port2 = $query->rowCount();
                $query = $sql->prepare("SELECT `id` FROM `voice_server` WHERE `port`=? AND `ip`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($port2,$serverip_new,$reseller_id));
                $num_check_port2 += $query->rowCount();
            } else {
                $num_check_port2 = 0;
            }
            if ($port3_old != $port3) {
                $query = $sql->prepare("SELECT `id` FROM `gsswitch` WHERE (`port`=:port OR `port2`=:port OR `port3`=:port OR `port4`=:port OR `port5`=:port) AND `id`!=:switchID AND `serverip`=:serverip AND `resellerid`=:reseller_id LIMIT 1");
                $query->execute(array(':port' => $port3,':switchID' => $server_id,':serverip' => $serverip_new,':reseller_id' => $reseller_id));
                $num_check_port3 = $query->rowCount();
                $query = $sql->prepare("SELECT `id` FROM `voice_server` WHERE `port`=? AND `ip`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($port3,$serverip_new,$reseller_id));
                $num_check_port3 += $query->rowCount();
            } else {
                $num_check_port3 = 0;
            }
            if ($port4_old != $port4) {
                $query = $sql->prepare("SELECT `id` FROM `gsswitch` WHERE (`port`=:port OR `port2`=:port OR `port3`=:port OR `port4`=:port OR `port5`=:port) AND `id`!=:switchID AND `serverip`=:serverip AND `resellerid`=:reseller_id LIMIT 1");
                $query->execute(array(':port' => $port4,':switchID' => $server_id,':serverip' => $serverip_new,':reseller_id' => $reseller_id));
                $num_check_port4 = $query->rowCount();
                $check_select_port4_2 = $sql->prepare("SELECT `id` FROM `voice_server` WHERE `port`=? AND `ip`=? AND `resellerid`=? LIMIT 1");
                $check_select_port4_2->execute(array($port4,$serverip_new,$reseller_id));
                $num_check_port4 += $query->rowCount();
            } else {
                $num_check_port4 = 0;
            }
            if ($port5_old != $port5) {
                $query = $sql->prepare("SELECT `id` FROM `gsswitch` WHERE (`port`=:port OR `port2`=:port OR `port3`=:port OR `port4`=:port OR `port5`=:port) AND `id`!=:switchID AND `serverip`=:serverip AND `resellerid`=:reseller_id LIMIT 1");
                $query->execute(array(':port' => $port5,':switchID' => $server_id,':serverip' => $serverip_new,':reseller_id' => $reseller_id));
                $num_check_port5 = $query->rowCount();
                $check_select_port4_2 = $sql->prepare("SELECT `id` FROM `voice_server` WHERE `port`=? AND `ip`=? AND `resellerid`=? LIMIT 1");
                $check_select_port4_2->execute(array($port5,$serverip_new,$reseller_id));
                $num_check_port5 += $query->rowCount();
            } else {
                $num_check_port5 = 0;
            }
            if ($num_check_game==0 and $num_check_port2==0 and $num_check_port3==0 and $num_check_port4==0 and $num_check_port5==0) {
                $updateGo = true;
            }
            $cmds = array();
            if (($serverip_old != $serverip_new or $port_old != $port_new) and isset($updateGo)){
                $tmp=gsrestart($server_id,'so',$aeskey,$reseller_id);
                if (is_array($tmp)) foreach($tmp as $t) $cmds[] = $t;
                $address_old = $serverip_old . ':' . $port_old;
                $gsfolder_old = $serverip_old . '_' . $port_old;
                $gsfolder_new = $serverip_new . '_' . $port_new;
                $address_new = $serverip_new . ':' . $port_new;
                $alreadystopped = true;
                $cmds[]="sudo -u ${server_customer} ./control.sh move $server_customer $gsfolder_old $gsfolder_new";
            }
            if (isset($updateGo)) {
                if ($active_old== 'Y' and $active == 'N' and !isset($alreadystopped)) {
                    $tmp=gsrestart($server_id,'so',$aeskey,$reseller_id);
                    if (is_array($tmp)) foreach($tmp as $t) $cmds[] = $t;
                    $alreadystopped = true;
                }
                $query = $sql->prepare("UPDATE `gsswitch` SET `active`=?,`taskset`=?,`cores`=?,`pallowed`=?,`eacallowed`=?,`brandname`=?,`lendserver`=?,`serverip`=?,`tvenable`=?,`port`=?,`port2`=?,`port3`=?,`port4`=?,`port5`=?,`minram`=?,`maxram`=?,`slots`=?,`war`=?,`autoRestart`=?,`ftppassword`=AES_ENCRYPT(?,?) WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($active,$taskset,$usedcores,$pallowed,$eacallowed,$brandname,$lendserver,$serverip_new,$tvenable,$port_new,$port2,$port3,$port4,$port5,$minram,$maxram,$slots,$war,$autoRestart,$ftppassword_new,$aeskey,$server_id,$reseller_id));
                customColumns('G',$server_id,'save');
                if ($ftppassword_new != $ftppass_old or ($active_old== 'Y' and $active == 'N')){
                    $cmds[] = './control.sh mod '.$server_customer . ' ' . $ftppass . ' ' . $protectedpw;
                }
                if ($slots_old != $slots and !isset($alreadystopped)) {
                    $tmp=gsrestart($server_id,'re',$aeskey,$reseller_id);
                    if (is_array($tmp)) foreach($tmp as $t) $cmds[] = $t;
                }
                $gamestring_awk=explode('_', $gamestring);
                $gamecount = $gamestring_awk[0];
            } else {
                $gamecount = 0;
            }
            if (is_array($cmds) and count($cmds)>0) ssh2_execute('gs',$rootID,$cmds);
            $i = 1;
            $num_check = 0;
            while ($i <= $gamecount) {
                $shorten = $gamestring_awk[$i];
                $id = $ui->id("id_$shorten",19, 'post');
                $fps = $ui->id("fps_$shorten",6, 'post');
                $map = $ui->mapname("map_$shorten", 'post');
                $mapGroup = $ui->mapname("mapGroup_$shorten", 'post');
                $cmd = $ui->startparameter("cmd_$shorten", 'post');
                $owncmd = $ui->active("owncmd_$shorten", 'post');
                $tic = $ui->id("tic_$shorten",5, 'post');
                $userfps = ($ui->active("user_fps_$shorten", 'post')) ? $ui->active("user_fps_$shorten", 'post') : 'N';
                $usertick = ($ui->active("user_tick_$shorten", 'post')) ? $ui->active("user_tick_$shorten", 'post') : 'N';
                $usermap = ($ui->active("user_map_$shorten", 'post')) ? $ui->active("user_map_$shorten", 'post') : 'N';
                $user_uploaddir = ($ui->active("user_uploaddir_$shorten", 'post')) ? $ui->active("user_uploaddir_$shorten", 'post') : 'N';
                if ($ui->id("upload_$shorten",1, 'post')) {
                    $upload = $ui->id("upload_$shorten",1, 'post');
                    if ($upload>1) {
                        $uploaddir = $ui->url("uploaddir_$shorten", 'post');
                    } else {
                        $uploaddir = '';
                    }
                } else {
                    $upload = 0;
                    $uploaddir = '';
                }
                $query = $sql->prepare("UPDATE `serverlist` SET `fps`=?,`map`=?,`mapGroup`=?,`cmd`=?,`owncmd`=?,`tic`=?,`userfps`=?,`usertick`=?,`usermap`=?,`user_uploaddir`=?,`upload`=?,`uploaddir`=AES_ENCRYPT(?,?) WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($fps,$map,$mapGroup,$cmd,$owncmd,$tic,$userfps,$usertick,$usermap,$user_uploaddir,$upload,$uploaddir,$aeskey,$id,$reseller_id));
                $template_file .= $shorten . '  ' . $serverip_new . ':' . $port_new.": ".$sprache->server_ud."<br />";
                $i++;
            }
            if (isset($updateGo)) {
                $loguseraction="%mod% %gserver% $serverip_new:$port_new";
                $insertlog->execute();
            } else {
                $template_file = $sprache->error_port;
            }
        } else {
            $template_file = 'Error: '.implode('<br />',$error);
        }
    } else {
        $template_file = 'admin_404.tpl';
    }
} else if ($ui->st('d', 'get') == 'ri' and $ui->id('id', 10, 'get')) {
    $server_id = $ui->id('id', 10, 'get');
    if (!isset($action)) {
        $table = array();
        $query = $sql->prepare("SELECT `serverip`,`port`,`serverid` FROM `gsswitch` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query2 = $sql->prepare("SELECT s.`id`,s.`servertemplate`,t.`shorten`,t.`description` FROM `serverlist` s LEFT JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE s.`switchID`=? AND s.`resellerid`=?");
        $query->execute(array($server_id,$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $serverip = $row['serverip'];
            $port = $row['port'];
            $query2->execute(array($server_id,$reseller_id));
            foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                $servertemplate=($row['serverid'] == $row2['id']) ? $row2['servertemplate'] : '';
                $table[] = array('id' => $row2['id'], 'description' => $row2['description'], 'shorten' => $row2['shorten'], 'servertemplate' => $servertemplate);
            }
        }
        if (isset($serverip) and isset($port)) {
            $template_file = 'admin_gserver_ri.tpl';
        } else {
            $template_file = 'admin_404.tpl';
        }
    } else if ($action == 'ri') {
        $gamestring = array();
        $template = array();
        $i = 0;
        $query = $sql->prepare("SELECT g.`userid`,g.`id`,g.`serverip`,g.`port`,g.`rootID`,u.`cname` FROM `gsswitch` g LEFT JOIN `userdata` u ON g.`userid`=u.`id` WHERE g.`id`=? AND g.`resellerid`=? LIMIT 1");
        $query->execute(array($server_id,$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $customer = $row['cname'];
            $serverid = $row['rootID'];
            $serverip = $row['serverip'];
            $port = $row['port'];
            $gsfolder = $serverip . '_' . $port;
        }

        # https://github.com/easy-wi/developer/issues/69
        $templates = (array) $ui->id('template',10, 'post');
        foreach($templates as $id => $tpl) {
            if ($tpl>0) {
                $template[] = $tpl;
                if ($ui->active('type', 'post') == 'Y') {
                    $query = $sql->prepare("DELETE FROM `addons_installed` WHERE `serverid`=? AND `resellerid`=?");
                    $query->execute(array($id,$reseller_id));
                    $query = $sql->prepare("DELETE a.* FROM `addons_installed` a LEFT JOIN `serverlist` s ON a.`serverid`=s.`id` WHERE s.`id` IS NULL");
                    $query->execute();
                    $query = $sql->prepare("DELETE a.* FROM `addons_installed` a LEFT JOIN `userdata` u ON a.`userid`=u.`id` WHERE u.`id` IS NULL");
                    $query->execute();
                }
                $query = $sql->prepare("SELECT s.`gamemod`,s.`gamemod2`,t.`shorten` FROM `serverlist` s LEFT JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE s.`id`=? AND s.`resellerid`=? LIMIT 1");
                $query->execute(array($id,$reseller_id));
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $shorten = $row['shorten'];
                    $gamemod2 = $row['gamemod2'];
                    $gamestring[] = ($row['gamemod'] == 'Y') ? $shorten.$gamemod2 : $shorten;
                }
            }
        }
        if (count($gamestring)>0 and $ui->active('type', 'post')) {
            include(EASYWIDIR . '/stuff/ssh_exec.php');
            $gamestring=count($gamestring) . '_' . implode('_',$gamestring);
            $rdata=serverdata('root',$serverid,$aeskey);
            $sship = $rdata['ip'];
            $sshport = $rdata['port'];
            $sshuser = $rdata['user'];
            $sshpass = $rdata['pass'];
            $query = $sql->prepare("SELECT `id`,`serverid`,`newlayout`,`rootID`,AES_DECRYPT(`ftppassword`,?) AS `cftppass` FROM `gsswitch` WHERE `serverip`=? AND `port`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($aeskey,$serverip,$port,$reseller_id));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $ftppass = $row['cftppass'];
                if ($row['newlayout'] == 'Y') $customer = $customer . '-' . $row['id'];
                if ($ui->active('type', 'post') == 'Y') {
                    $cmds = gsrestart($row['id'], 'so',$aeskey,$reseller_id);
                    $cmds[]="./control.sh add ${customer} ${ftppass} ${sshuser} ".passwordgenerate(10);
                    $cmds[]="sudo -u ${customer} ./control.sh reinstserver ${customer} ${gamestring} ${gsfolder} \"".implode(' ',$template).'"';
                    $loguseraction="%reinstall% %gserver% ${serverip}:${port}";
                } else {
                    $cmds[]="sudo -u ${customer} ./control.sh addserver ${customer} ${gamestring} ${gsfolder} \"".implode(' ',$template).'"';
                    $loguseraction="%resync% %gserver% ${serverip}:${port}";
                }
                if (count($cmds)>0) {
                    ssh2_execute('gs', $serverid, $cmds);
                }
                $template_file = $sprache->server_installed;
                $insertlog->execute();
            }
        } else {
            $template_file = 'admin_404.tpl';
        }
    } else {
        $template_file = 'admin_404.tpl';
    }
} else if (in_array($ui->st('d', 'get'), array('rs','st','du')) and $ui->id('id', 10, 'get')) {
    $id = $ui->id('id', 10, 'get');
    $query = $sql->prepare("SELECT `serverip`,`port`,`rootID` FROM `gsswitch` WHERE `id`=? AND `resellerid`=? LIMIT 1");
    $query->execute(array($id,$reseller_id));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $gsip = $row['serverip'];
        $port = $row['port'];
        $port = $row['port'];
        $rootID = $row['rootID'];
    }
    if (isset($gsip) and isset($port)) {
        include(EASYWIDIR . '/stuff/ssh_exec.php');
        if ($ui->st('d', 'get') == 'rs') {
            $template_file = 'Restart done';
            $cmds=gsrestart($id,'re',$aeskey,$reseller_id);
            $loguseraction="%start% %gserver% $gsip:$port";
        } else if ($ui->st('d', 'get') == 'st') {
            $template_file = 'Stop done';
            $cmds=gsrestart($id,'so',$aeskey,$reseller_id);
            $loguseraction="%stop% %gserver% $gsip:$port";
        } else if ($ui->st('d', 'get') == 'du') {
            $template_file = 'SourceTV upload started';
            $cmds=gsrestart($id,'du',$aeskey,$reseller_id);
            $loguseraction="%movie% %gserver% $gsip:$port";
        }
        if (isset($cmds) and is_array($cmds) and count($cmds)>0) ssh2_execute('gs',$rootID,$cmds);
        $insertlog->execute();
    } else {
        $template_file = 'admin_404.tpl';
    }
} else {
    $o = $ui->st('o', 'get');
    if ($ui->st('o', 'get') == 'di') {
        $orderby = 'g.`id` DESC';
    } else if ($ui->st('o', 'get') == 'ai') {
        $orderby = 'g.`id` ASC';
    } else if ($ui->st('o', 'get') == 'dt') {
        $orderby = 'g.`active` ASC';
    } else if ($ui->st('o', 'get') == 'at') {
        $orderby = 'g.`active` ASC';
    } else if ($ui->st('o', 'get') == 'da') {
        $orderby = 'u.`cname` DESC,g.`serverip` ASC,g.`port` ASC';
    } else if ($ui->st('o', 'get') == 'aa') {
        $orderby = 'u.`cname` ASC,g.`serverip` ASC,g.`port` ASC';
    } else if ($ui->st('o', 'get') == 'dn') {
        $orderby = 'u.`name` DESC,u.`vname` DESC,g.`serverip` ASC,g.`port` ASC';
    } else if ($ui->st('o', 'get') == 'an') {
        $orderby = 'u.`name` ASC,u.`vname` ASC,g.`serverip` ASC,g.`port` ASC';
    } else if ($ui->st('o', 'get') == 'dl') {
        $orderby = 'g.`lendserver` DESC';
    } else if ($ui->st('o', 'get') == 'al') {
        $orderby = 'g.`lendserver` ASC';
    } else if ($ui->st('o', 'get') == 'ds') {
        $orderby = 'g.`serverip` DESC,g.`port` DESC';
    } else {
        $orderby = 'g.`serverip` ASC,g.`port` ASC';
        $o = 'as';
    }
    $query = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `gsswitch` WHERE `resellerid`=?");
    $query->execute(array($reseller_id));
    $colcount = $query->fetchColumn();
    if ($start>$colcount) {
        $start = $colcount-$amount;
        if ($start<0)$start = 0;
    }
    $query = $sql->prepare("SELECT g.*,CONCAT(g.`serverip`,':',g.`port`) AS `server`,t.`shorten`,u.`cname`,u.`name`,u.`vname`,u.`active` AS `useractive` FROM `gsswitch` g INNER JOIN `serverlist` s ON g.`serverid`=s.`id` INNER JOIN `servertypes` t ON s.`servertype`=t.`id` INNER JOIN `userdata` u ON g.`userid`=u.`id` WHERE g.`resellerid`=? ORDER BY $orderby LIMIT $start,$amount");
    $query->execute(array($reseller_id));
    $table = array();
    $query2 = $sql->prepare("SELECT `extraData` FROM `jobs` WHERE `affectedID`=? AND `resellerID`=? AND `type`='gs' AND (`status` IS NULL OR `status`=1) ORDER BY `jobID` DESC LIMIT 1");
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        unset($tobeActive);
        $jobPending = '';
        $server = $row['server'];
        $userid = $row['userid'];
        $serverid = $row['id'];
        $notified = $row['notified'];
        $stopped = $row['stopped'];
        $serveractive = $row['active'];
        $war = $row['war'];
        $brandname = $row['brandname'];
        $name = $row['queryName'];
        $map = $row['queryMap'];
        $numplayers = $row['queryNumplayers'];
        $maxplayers = $row['queryMaxplayers'];
        $password = $row['queryPassword'];
        $lendserver=($row['lendserver'] == 'Y') ? $gsprache->yes : $gsprache->no;
        if (!isset($name)) $name = '';
        if (!isset($type)) $type = '';
        if (!isset($map)) $map = '';
        if (!isset($numplayers)) $numplayers = '';
        if (!isset($maxplayers)) $maxplayers = '';
        $premoved = '';
        $nameremoved = '';
        $imgName = '16_ok';
        $imgAlt = 'Online';
        if (isset($row['jobPending']) and $row['jobPending'] == 'Y') {
            $query2->execute(array($row['id'], $row['resellerid']));
            foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                if ($row2['action'] == 'ad') $jobPending = $gsprache->add;
                else if ($row2['action'] == 'dl') $jobPending = $gsprache->del;
                else $jobPending = $gsprache->mod;
                $json=@json_decode($row2['extraData']);
                $tobeActive=(is_object($json) and isset($json->newActive)) ? $json->newActive : 'N';
            }
        } else {
            $jobPending = $gsprache->no;
        }

        if ($row['active'] == 'N' and $row['jobPending'] == 'Y' and isset($tobeActive) and $tobeActive == 'Y') {
            $imgName = '16_ok';
            $imgAlt = 'Active';

        } else if ($row['active'] == 'N') {
            $imgName = '16_bad';
            $imgAlt = 'Inactive';

        } else if ($stopped == 'Y') {
            $imgName = '16_bad';
            $imgAlt = 'Stopped';

        } else if (($name == 'OFFLINE' or $name == '') and $notified >= $rSA['down_checks'] and $stopped== 'N') {
            $imgName = '16_error';
            $imgAlt = 'Crashed';

        } else {

            if ($war == 'Y' and $password == 'N') {
                $imgName = '16_error';
                $imgAlt = 'No Password';
            }

            if ($brandname == 'Y' and $rSA['brandname'] != null and $rSA['brandname'] != '' and strpos(strtolower($name),strtolower($rSA['brandname'])) === false) {
                $imgName = '16_error';
                $imgAlt = 'No Servertag';
            }

        }

        $table[] = array('serveractive' => $serveractive,'shorten' => $row['shorten'], 'useractive' => $row['useractive'], 'cname' => $row['cname'], 'names' => trim($row['name'] . ' ' . $row['vname']),'img' => $imgName,'alt' => $imgAlt,'premoved' => $premoved,'nameremoved' => $nameremoved, 'server' => $server,'serverid' => $serverid,'name' => $name,'type' => $type,'map' => $map,'numplayers' => $numplayers,'maxplayers' => $maxplayers,'id' => $userid,'lendserver' => $lendserver,'active' => $row['active'], 'jobPending' => $jobPending);
    }
    $next = $start+$amount;
    $vor=($colcount>$next) ? $start+$amount : $start;
    $back = $start - $amount;
    $zur = ($back >= 0) ? $start - $amount : $start;
    $pageamount = ceil($colcount / $amount);
    $pages[] = '<a href="admin.php?w=gs&amp;d=md&amp;a=' . (!isset($amount)) ? 20 : $amount . ($start==0) ? '&p=0" class="bold">1</a>' : '&p=0">1</a>';
    $i = 2;
    while ($i <= $pageamount) {
        $selectpage = ($i - 1) * $amount;
        $pages[]=($start == $selectpage) ? '<a href="admin.php?w=gs&amp;d=md&amp;a=' . $amount . '&p=' . $selectpage . '" class="bold">' . $i . '</a>' : '<a href="admin.php?w=ro&amp;d=md&amp;a=' . $amount . '&p=' . $selectpage . '">' . $i . '</a>';
        $i++;
    }
    $pages=implode(', ',$pages);
    $template_file = "admin_gserver_list.tpl";
}