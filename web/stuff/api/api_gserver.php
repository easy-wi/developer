<?php

/**
 * File: api_gserver.php.
 * Author: Ulrich Block
 * Date: 05.08.12
 * Time: 18:27
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

$minimumArray = array('action', 'identify_server_by', 'server_local_id', 'server_external_id');
$editArray = array('active', 'private', 'slots', 'shorten', 'identify_user_by', 'user_localid', 'user_externalid', 'username');

foreach ($minimumArray as $key) {
    if (!array_key_exists($key, $data)) {
        $success['false'][] = 'Data key does not exist: ' . $key;
    }
}

if (array_key_exists('action', $data) and $data['action'] != 'gs') {
    foreach ($editArray as $key) {
        if (!array_key_exists($key, $data)) {
            $success['false'][] = 'Data key does not exist: ' . $key;
        }
    }
}

$active = '';
$private = '';
$shorten = '';
$slots = '';
$identifyUserBy = '';
$localUserID = '';
$externalUserID = '';
$username = '';
$identifyServerBy = '';
$localServerID = '';
$externalServerID = '';
$taskset = '';
$eacallowed = '';
$brandname = '';
$tvenable = '';
$pallowed = '';
$name = '';
$homeDirLabel = '';
$hdd = '';
$ip = '';
$port = '';
$port2 = '';
$port3 = '';
$port4 = '';
$port5 = '';
$minram = '';
$maxram = '';
$hostID = '';
$cores = '';
$coreCount = '';
$customID = 0;
$hostExternalID = '';
$initialpassword = '';
$installGames = 'A';
$autoRestart = '';
$ftpUser = '';

if (!isset($success['false']) and array_value_exists('action', 'add', $data) and 1 > $licenceDetails['lG']) {

    $success['false'][] = 'licence limit reached';

} else if (!isset($success['false']) and array_value_exists('action', 'add', $data) and $licenceDetails['lG'] > 0) {

    if (dataExist('identify_user_by', $data) and isid($data['slots'], 11)) {

        if (is_array($data['shorten']) or is_object($data['shorten'])) {
            $shorten = $data['shorten'];
        } else {
            $shorten = array($data['shorten']);
        }

        if (count($shorten) == 0) {
 
            $success['false'][] = 'No gameshorten(s) has been send';
 
        } else {

            $typeIDs = array();
            $typeIDList = array();
            $shortenToID = array();

            $from = array('user_localid' => 'id', 'username' => 'cname', 'user_externalid' => 'externalID', 'email' => 'mail');

            $active = active_check($data['active']);
            $private = active_check($data['private']);
            $slots = isid($data['slots'], 11);
            $identifyUserBy = $data['identify_user_by'];
            $localUserID = isid($data['user_localid'], 21);
            $externalUserID = isExternalID($data['user_externalid']);
            $username = $data['username'];
            $identifyServerBy = $data['identify_server_by'];
            $localServerID = isid($data['server_local_id'], 19);
            $externalServerID = isExternalID($data['server_external_id']);

            $query = $sql->prepare("SELECT `id`,`cname` FROM `userdata` WHERE `" . $from[$data['identify_user_by']] . "`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($data[$data['identify_user_by']], $resellerID));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

                $localUserLookupID = $row['id'];
                $ftpUser = $row['cname'];

                if ($username != $row['cname']) {
                    $username = $row['cname'];
                }
            }

            if (!isset($localUserLookupID) or !isid($localUserLookupID, 11)) {
                $success['false'][] = 'user does not exist';
            }

            $query = $sql->prepare("SELECT * FROM `servertypes` WHERE `shorten`=? AND `resellerid`=? LIMIT 1");

            foreach ($shorten as $singleShorten) {

                $query->execute(array($singleShorten, $resellerID));
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

                    if (!isset($portMax) or $row['portMax'] > $portMax or (isset($data['primary']) and gamestring($data['primary']) and $row['portMax'] <= $portMax and $singleShorten == $data['primary'])) {
                        $portStep = $row['portStep'];
                        $portMax = $row['portMax'];
                        $port = $row['portOne'];
                        $port2 = $row['portTwo'];
                        $port3 = $row['portThree'];
                        $port4 = $row['portFour'];
                        $port5 = $row['portFive'];
                    }

                    $typeIDList[] = $row['id'];
                    $shortenToID[$row['id']] = $singleShorten;
                    $typeIDs[$singleShorten] = array('id' => $row['id'], 'map' => $row['map'], 'mapGroup' => $row['mapGroup'], 'tic' => $row['tic'], 'fps' => $row['fps'], 'cmd' => $row['cmd'], 'gamemod' => $row['gamemod'], 'gamemod2' => $row['gamemod2'], 'modcmds' => $row['modcmds']);
                }

                if (!isset($typeIDs[$singleShorten])) {
                    $success['false'][] = 'image with the shorten ' . $singleShorten . ' does not exists';
                }
            }

            if (!isset($success['false']) and !in_array($externalServerID, $bad)) {
                $query = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `gsswitch` WHERE `externalID`=? LIMIT 1");
                $query->execute(array($externalServerID));

                if ($query->fetchColumn() > 0) {
                    $success['false'][] = 'server with external ID already exists';
                }
            }

            if (!isset($success['false'])) {

                $masterServerCount = count($typeIDList);

                if ($masterServerCount == 1) {
                    $implodedQuery = 'm.`servertypeid`=' . $typeIDList[0];
                } else {
                    $implodedQuery = '(m.`servertypeid`=' . implode(' OR m.`servertypeid`=', $typeIDList) . ')';
                }

                if (isset($data['master_server_id'])) {
                    $masterIDsArray = (isid($data['master_server_id'], 19)) ? array($data['master_server_id']) : (array) $data['master_server_id'];
                }

                if (isset($data['master_server_external_id'])) {
                    $externalMasterIDsArray = (isExternalID($data['master_server_external_id']) != '') ? array($data['master_server_external_id']) : (array) $data['master_server_external_id'];
                }

                $inSQLArray = '';

                if (isset($masterIDsArray) and count($masterIDsArray) > 0) {

                    $inSQLArray = 'r.`id` IN (' . implode(',', $masterIDsArray) . ') AND';

                } else if (isset($externalMasterIDsArray) and count($externalMasterIDsArray) > 0) {

                    $inSQLArray = 'r.`externalID` IN (' . implode(',', "'" . $externalMasterIDsArray . "'") . ') AND';
                }

                $query = $sql->prepare("SELECT r.`id`,r.`quota_active`,r.`install_paths`,r.`hyperthreading`,r.`cores`,r.`externalID`,r.`connect_ip_only`,r.`ip`,r.`altips`,r.`maxslots`,r.`maxserver`,r.`active` AS `hostactive`,r.`resellerid` AS `resellerid`,(r.`maxserver`-(SELECT COUNT(`id`) FROM `gsswitch` AS g WHERE g.`rootID`=r.`id` )) AS `freeserver`,(r.`maxslots`-(SELECT SUM(g.`slots`) FROM `gsswitch` AS g WHERE g.`rootID`=r.`id`)) AS `leftslots`,(SELECT COUNT(m.`id`) FROM `rservermasterg` AS m WHERE m.`serverid`=r.`id` AND $implodedQuery) `mastercount` FROM `rserverdata` AS r GROUP BY r.`id` HAVING ($inSQLArray `hostactive`='Y' AND r.`resellerid`=? AND (`freeserver`>0 OR `freeserver` IS NULL) AND (`leftslots`>? OR `leftslots` IS NULL) AND `mastercount`=?) ORDER BY `freeserver` DESC LIMIT 1");
                $query->execute(array($resellerID, $slots, $masterServerCount));

                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

                    $ips = array();

                    $defaultHomeDir = 'home';

                    $iniVars = parse_ini_string($row['install_paths'], true);

                    if ($iniVars) {
                        foreach ($iniVars as $key => $values) {
                            if (isset($values['default']) and $values['default'] == 1) {
                                $defaultHomeDir = $key;
                            }
                        }
                    }

                    $homeLabelGiven = (isset($data['home_label']) and strlen($data['home_label']) > 0) ? $data['home_label'] : $defaultHomeDir;
                    $homeDirLabel = ($iniVars and isset($iniVars[$homeLabelGiven]['path'])) ? $homeLabelGiven : $defaultHomeDir;
                    $quotaActive = $row['quota_active'];

                    $hostID = $row['id'];
                    $hostExternalID = $row['externalID'];

                    if ($row['connect_ip_only'] != 'Y') {
                        $ips[] = $row['ip'];
                    }

                    if (isset($data['coreCount']) and $data['coreCount'] > 0) {

                        $coreCount = ($row['hyperthreading'] == 'Y') ? 2 * $row['cores'] : $row['cores'];

                        $c = 0;
                        $cores = array();

                        while ($c < $coreCount) {
                            $cores[$c] = 0;
                            $c++;
                        }

                        $query2 = $sql->prepare("SELECT `taskset`,`cores` FROM `gsswitch` WHERE `rootID`=? AND `resellerid`=?");
                        $query2->execute(array($hostID, $resellerID));
                        foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {

                            $coreExploded = explode(',', $row2['cores']);
                            $coreCounted = count($coreExploded);

                            if ($row2['taskset'] == 'Y' and $coreCounted > 0) {
                                foreach ($coreExploded as $usedCore) {
                                    $cores[$usedCore] = $cores[$usedCore] + round(1 / $coreCounted, 2);
                                }
                            }
                        }

                        asort($cores);

                        $i = 0;
                        $calculatedCores = array();

                        foreach ($cores as $core => $usage) {

                            $calculatedCores[] = $core;

                            $i++;

                            if ($i == $coreCount or $i == $data['coreCount']) {
                                break;
                            }
                        }

                        $calculatedCores = implode(',', $calculatedCores);
                    }

                    foreach (preg_split('/\r\n/', $row['altips'], -1, PREG_SPLIT_NO_EMPTY) as $ip) {
                        $ips[] = $ip;
                    }

                    $used = usedPorts($ips);
                    $ip = $used['ip'];
                    $ports = $used['ports'];
                }
            }

            if (!isset($success['false']) and isip($ip, 'ip4')) {

                if ($portMax > 0) {

                    if (isset($data['port']) and checkPorts(array($data['port']), $ports) === true) {
                        $port = $data['port'];
                    }

                    while (in_array($port, $ports)) {
                        $port += $portStep;
                    }
                }

                if ($portMax > 1) {

                    if (isset($data['port2']) and checkPorts(array($data['port2']), $ports) === true) {
                        $port = $data['port'];
                    }

                    while (in_array($port2, $ports)) {
                        $port2 += $portStep;
                    }
                }

                if ($portMax > 2) {

                    if (isset($data['port3']) and checkPorts(array($data['port3']), $ports) === true) {
                        $port = $data['port'];
                    }

                    while (in_array($port3, $ports)) {
                        $port3 += $portStep;
                    }
                }

                if ($portMax > 3) {

                    if (isset($data['port4']) and checkPorts(array($data['port4']), $ports) === true) {
                        $port = $data['port'];
                    }

                    while (in_array($port4, $ports)) {
                        $port4 += $portStep;
                    }
                }

                if ($portMax > 4) {

                    if (isset($data['port5']) and checkPorts(array($data['port5']), $ports) === true) {
                        $port = $data['port'];
                    }

                    while (in_array($port5, $ports)) {
                        $port5 += $portStep;
                    }
                }

                $initialpassword = (isset($data['initialpassword']) and wpreg_check($data['initialpassword'], 50) and strlen($data['initialpassword']) > 1) ? $data['initialpassword'] : passwordgenerate(10);
                $taskset = (isset($data['taskset']) and active_check($data['taskset'])) ? $data['taskset'] : 'N';
                $eacallowed = (isset($data['eacallowed']) and active_check($data['eacallowed'])) ? $data['eacallowed'] : 'N';
                $brandname = (isset($data['brandname']) and active_check($data['brandname'])) ? $data['brandname'] : 'N';
                $tvenable = (isset($data['tvenable']) and active_check($data['tvenable'])) ? $data['tvenable'] : 'N';
                $pallowed = (isset($data['pallowed']) and active_check($data['pallowed'])) ? $data['pallowed'] : 'N';
                $autoRestart = (isset($data['autoRestart']) and active_check($data['autoRestart'])) ? $data['autoRestart'] : 'Y';
                $minram = (isset($data['minram']) and isid($data['minram'], 10)) ? $data['minram'] : '';
                $maxram = (isset($data['maxram']) and isid($data['maxram'], 10)) ? $data['maxram'] : '';
                $hdd = (isset($quotaActive) and $quotaActive == 'Y' and isset($data['hdd']) and isid($data['hdd'], 10)) ? $data['maxram'] : 0;

                if (isset($data['coreCount']) and $data['coreCount'] > 0 and isset($calculatedCores)) {
                    $cores = $calculatedCores;
                } else {
                    $cores = (isset($data['cores']) and cores($data['cores'])) ? $data['cores'] : '';
                }

                if (isset($data['installGames']) and wpreg_check($data['installGames'], 1)) {
                    $installGames = $data['installGames'];
                }

                $json = json_encode(array('installGames' => $installGames));

                $query = $sql->prepare("INSERT INTO `gsswitch` (`active`,`homeLabel`,`hdd`,`taskset`,`cores`,`userid`,`pallowed`,`eacallowed`,`serverip`,`rootID`,`tvenable`,`port`,`port2`,`port3`,`port4`,`port5`,`minram`,`maxram`,`slots`,`war`,`brandname`,`autoRestart`,`ftppassword`,`resellerid`,`externalID`,`serverid`,`stopped`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,AES_ENCRYPT(?,?),?,?,1,'Y')");
                $query->execute(array($active, $homeDirLabel, $hdd, $taskset, $cores, $localUserLookupID, $pallowed, $eacallowed, $ip, $hostID, $tvenable, $port, $port2, $port3, $port4, $port5, $minram, $maxram, $slots, $private, $brandname, $autoRestart, $initialpassword, $aeskey, $resellerID, $externalServerID));

                $localServerID = $sql->lastInsertId();
                $customID = $localServerID;
                $ftpUser .=  '-' . $localServerID;

                customColumns('G', $localServerID,'save', $data);

                if (isid($localServerID, 19)) {

                    $query = $sql->prepare("INSERT INTO `serverlist` (`servertype`,`switchID`,`map`,`mapGroup`,`cmd`,`modcmd`,`tic`,`fps`,`gamemod`,`gamemod2`,`resellerid`) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
                    foreach ($typeIDs as $shorten => $array) {

                        $modcmd = '';

                        foreach (explode("\r\n", $array['modcmds']) as $line) {

                            if (preg_match('/^(\[[\w\/\.\-\_\= ]{1,}\])$/', $line)) {

                                $name = trim($line, '[]');
                                $ex = preg_split("/\=/", $name, -1,PREG_SPLIT_NO_EMPTY);

                                if (isset($ex[1]) and trim($ex[1]) == 'default') {
                                    $modcmd = trim($ex[0]);
                                    break;
                                }

                            }
                        }

                        $query->execute(array($array['id'], $localServerID, $array['map'], $array['mapGroup'], $array['cmd'], $modcmd, $array['tic'], $array['fps'], $array['gamemod'], $array['gamemod2'], $resellerID));

                        if (!isset($lastServerID) or (isset($data['primary']) and gamestring($data['primary']) and $shorten == $data['primary'])) {
                            $lastServerID = $sql->lastInsertId();
                        }
                    }

                    if (!isset($lastServerID) or !isid($lastServerID, 19) ) {
                        $query = $sql->prepare("SELECT `id` FROM `serverlist` WHERE `switchID`=? AND `resellerid`=? ORDER BY `id` DESC LIMIT 1");
                        $query->execute(array($localServerID, $resellerID));
                        $lastServerID = $query->fetchColumn();
                    }

                    $name = $ip . ':' . $port;

                    $query = $sql->prepare("UPDATE `gsswitch` SET `serverid`=? WHERE `id`=? AND `resellerid`=? LIMIT 1");
                    $query->execute(array($lastServerID, $localServerID, $resellerID));

                    $query = $sql->prepare("UPDATE `jobs` SET `status`='2' WHERE `type`='gs' AND (`status` IS NULL OR `status`='1') AND `affectedID`=? and `resellerID`=?");
                    $query->execute(array($localServerID, $resellerID));

                    $query = $sql->prepare("INSERT INTO `jobs` (`api`,`type`,`hostID`,`invoicedByID`,`affectedID`,`userID`,`name`,`status`,`date`,`action`,`extraData`,`resellerid`) VALUES ('A','gs',?,?,?,?,?,NULL,NOW(),'ad',?,?)");
                    $query->execute(array($hostID, $resellerID, $localServerID, $localUserLookupID, $name, $json, $resellerID));

                } else {
                    $success['false'][] = 'Could not write game server to database';
                }
            } else {
                $success['false'][] = 'Cannot find free root server with given shorten';
            }
        }

    } else if (!isset($success['false'])) {

        $active = active_check($data['active']);
        $private = active_check($data['private']);
        $shorten = $data['shorten'];
        $slots = isid($data['slots'], 11);
        $identifyUserBy = $data['identify_user_by'];
        $localUserID = isid($data['user_localid'], 21);
        $externalUserID = isExternalID($data['user_externalid']);
        $username = $data['username'];
        $identifyServerBy = $data['identify_server_by'];
        $localServerID = isid($data['server_local_id'], 21);
        $externalServerID = isExternalID($data['server_external_id']);

        $success['false'][] = (!dataExist('identify_user_by', $data)) ? 'Can not identify user or bad email' : 'Slot amount needs to be specified';
    }

} else if (!isset($success['false']) and array_value_exists('action', 'mod', $data)) {

    $identifyUserBy = $data['identify_user_by'];
    $localUserID = isid($data['user_localid'], 21);
    $externalUserID = isExternalID($data['user_externalid']);
    $username = $data['username'];
    $identifyServerBy = $data['identify_server_by'];
    $localServerID = isid($data['server_local_id'], 21);
    $externalServerID = isExternalID($data['server_external_id']);
    $shorten = $data['shorten'];
    $from = array('server_local_id' => 'id', 'server_external_id' => 'externalID');
    $initialpassword = (isset($data['initialpassword']) and wpreg_check($data['initialpassword'], 50)) ? $data['initialpassword'] : '';

    if (is_array($data['shorten']) or is_object($data['shorten'])) {
        $shorten = $data['shorten'];
    } else if (isset($data['shorten'])) {
        $shorten = array($data['shorten']);
    }

    if (dataExist('identify_server_by', $data)) {

        $query = $sql->prepare("SELECT r.`install_paths`,r.`quota_active`,r.`externalID`,r.`hyperthreading`,r.`cores` AS `coresAvailable`,g.*,u.`cname` FROM `gsswitch` g INNER JOIN `rserverdata` r ON g.`rootID`=r.`id` INNER JOIN `userdata` u ON u.`id`=g.`userid` WHERE g.`".$from[$data['identify_server_by']]."`=? AND g.`resellerid`=? LIMIT 1");
        $query->execute(array($data[$data['identify_server_by']], $resellerID));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

            $localID = $row['id'];
            $userID = $row['userid'];
            $ftpUser = $row['cname'] . '-' . $row['id'];
            $hostID = $row['rootID'];
            $quotaActive = $row['quota_active'];

            if ($username != $row['cname']) {
                $username = $row['cname'];
            }

            if (isset($data['coreCount']) and $data['coreCount'] > 0 and $data['coreCount'] != count(preg_split('/,/', $row['cores'], -1, PREG_SPLIT_NO_EMPTY))) {

                $coreCount = ($row['hyperthreading'] == 'Y') ? 2 * $row['coresAvailable'] : $row['coresAvailable'];

                $c = 0;
                $cores = array();

                while ($c < $coreCount) {
                    $cores[$c] = 0;
                    $c++;
                }

                $query2 = $sql->prepare("SELECT `taskset`,`cores` FROM `gsswitch` WHERE `rootID`=? AND `resellerid`=?");
                $query2->execute(array($hostID, $resellerID));
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row2) {

                    $coreExploded = explode(',', $row2['cores']);
                    $coreCounted = count($coreExploded);

                    if ($row2['taskset'] == 'Y' and $coreCounted > 0) {
                        foreach ($coreExploded as $usedCore) {
                            $cores[$usedCore] = $cores[$usedCore] + round(1 / $coreCounted, 2);
                        }
                    }
                }

                asort($cores);

                $i = 0;
                $calculatedCores = array();

                foreach ($cores as $core => $usage) {

                    $calculatedCores[] = $core;

                    $i++;

                    if ($i == $coreCount or $i == $data['coreCount']) {
                        break;
                    }
                }

                $calculatedCores = implode(',', $calculatedCores);
            }

            $hostExternalID = $row['externalID'];
            $oldSlots = $row['slots'];
            $name = $row['serverip'] . ':' . $row['port'];
            $oldActive = $row['active'];
            $oldIP = $row['serverip'];
            $oldPort = $row['port'];
            $oldHomeDirLabel = $row['homeLabel'];
            $oldHdd = $row['hdd'];
            $oldProtected = $row['pallowed'];
            $usedPorts = usedPorts(array($row['serverip']));

            $active = $row['active'];
            $ip = $row['serverip'];
            $port = $row['port'];
            $port2 = $row['port2'];
            $port3 = $row['port3'];
            $port4 = $row['port4'];
            $port5 = $row['port5'];
            $active = $row['active'];
            $cores = $row['cores'];
            $minram = $row['minram'];
            $maxram = $row['maxram'];
            $pallowed = $row['pallowed'];
            $autoRestart = $row['autoRestart'];
            $homeDirLabel = $row['homeLabel'];
            $hdd = $row['hdd'];

            $query = $sql->prepare("SELECT COUNT(`jobID`) AS `amount` FROM `jobs` WHERE `affectedID`=? AND `resellerID`=? AND `action`='dl' AND (`status` IS NULL OR `status`='1') LIMIT 1");
            $query->execute(array($localID, $resellerID));
            if ($query->fetchColumn() > 0) {
                $success['false'][] = 'Server is marked for deletion';
            }

            $updateArray = array();
            $eventualUpdate = '';

            $iniVars = parse_ini_string($row['install_paths'], true);

            if (isset($data['private']) and active_check($data['private']) and $data['private'] != $row['war']) {
                $updateArray[] = $data['private'];
                $eventualUpdate .= ',`war`=?';
                $private = $data['private'];
            }

            if (isset($data['home_label']) and $data['home_label'] != $row['homeLabel'] and isset($iniVars[$data['home_label']]['path'])) {
                $updateArray[] = $data['home_label'];
                $eventualUpdate .= ',`homeLabel`=?';
                $homeDirLabel = $data['home_label'];
            }

            if ($quotaActive == 'Y' and isset($data['hdd']) and $data['hdd'] != $row['hdd']) {
                $updateArray[] = $data['hdd'];
                $eventualUpdate .= ',`hdd`=?';
                $hdd = $data['hdd'];
            }

            if (isset($data['slots']) and isid($data['slots'], 11) and $data['slots'] != $row['slots']) {
                $updateArray[] = $data['slots'];
                $eventualUpdate .= ',`slots`=?';
                $slots = $data['slots'];
            }

            if (isset($data['taskset']) and active_check($data['taskset']) and $data['taskset'] != $row['taskset']) {
                $updateArray[] = $data['taskset'];
                $eventualUpdate .= ',`taskset`=?';
                $taskset = $data['taskset'];
            }

            if (isset($data['eacallowed']) and active_check($data['eacallowed']) and $data['eacallowed'] != $row['eacallowed']) {
                $updateArray[] = $data['eacallowed'];
                $eventualUpdate .= ',`eacallowed`=?';
                $eacallowed = $data['eacallowed'];
            }

            if (isset($data['brandname']) and active_check($data['brandname']) and $data['brandname'] != $row['brandname']) {
                $updateArray[] = $data['brandname'];
                $eventualUpdate .= ',`brandname`=?';
                $brandname = $data['brandname'];
            }

            if (isset($data['tvenable']) and active_check($data['tvenable']) and $data['tvenable'] != $row['tvenable']) {
                $updateArray[] = $data['tvenable'];
                $eventualUpdate .= ',`tvenable`=?';
                $tvenable = $data['tvenable'];
            }

            if (isset($data['pallowed']) and active_check($data['pallowed']) and $data['pallowed'] != $row['pallowed']) {
                $updateArray[] = $data['pallowed'];
                $eventualUpdate .= ',`pallowed`=?';
                $pallowed = $data['pallowed'];
            }

            if (isset($data['autoRestart']) and active_check($data['autoRestart']) and $data['autoRestart'] != $row['autoRestart']) {
                $updateArray[] = $data['autoRestart'];
                $eventualUpdate .= ',`autoRestart`=?';
                $autoRestart = $data['autoRestart'];
            }

            if (isset($data['minram']) and isid($data['minram'], 10) and $data['minram'] != $row['minram']) {
                $updateArray[] = $data['minram'];
                $eventualUpdate .= ',`minram`=?';
                $minram = $data['minram'];
            }

            if (isset($data['maxram']) and isid($data['maxram'], 10) and $data['maxram'] != $row['maxram']) {
                $updateArray[] = $data['maxram'];
                $eventualUpdate .= ',`maxram`=?';
                $maxram = $data['maxram'];
            }

            if (isset($calculatedCores) or (isset($data['cores']) and cores($data['cores']) and $data['cores'] != $row['cores'])) {
                $updateArray[] = (isset($calculatedCores)) ? $calculatedCores : $data['cores'];
                $eventualUpdate .= ',`cores`=?';
                $cores = (isset($calculatedCores)) ? $calculatedCores : $data['cores'];
            }

            if (isset($data['active']) and active_check($data['active']) and $data['active'] != $row['active']) {
                $active = $data['active'];
            }

            if (isset($data['port']) and port($data['port']) and $data['port'] != $row['port'] and !in_array($data['port'], $usedPorts)) {
                $port = $data['port'];
            }

            if (isset($data['port2']) and port($data['port2']) and $data['port2'] != $row['port2'] and !in_array($data['port'], $usedPorts)) {
                $updateArray[] = $data['port2'];
                $eventualUpdate .= ',`port2`=?';
                $port2 = $data['port2'];
            }

            if (isset($data['port3']) and port($data['port3']) and $data['port3'] != $row['port3'] and !in_array($data['port'], $usedPorts)) {
                $updateArray[] = $data['port3'];
                $eventualUpdate .= ',`port3`=?';
                $port3 = $data['port3'];
            }

            if (isset($data['port4']) and port($data['port4']) and $data['port4'] != $row['port4'] and !in_array($data['port'], $usedPorts)) {
                $updateArray[] = $data['port4'];
                $eventualUpdate .= ',`port4`=?';
                $port4 = $data['port4'];
            }

            if (isset($data['port5']) and port($data['port5']) and $data['port5'] != $row['port5'] and !in_array($data['port'], $usedPorts)) {
                $updateArray[] = $data['port5'];
                $eventualUpdate .= ',`port5`=?';
                $port5 = $data['port5'];
            }

            if (isExternalID($data['server_external_id']) and $data['identify_server_by'] == 'server_local_id') {
                $updateArray[] = $data['server_external_id'];
                $eventualUpdate .= ',`externalID`=?';
            }

            if (count($updateArray)>0) {
                $eventualUpdate = trim($eventualUpdate,',');
                $eventualUpdate .= ',';
            }

            $updateArray[] = $localID;
            $updateArray[] = $resellerID;

            $query = $sql->prepare("UPDATE `gsswitch` SET $eventualUpdate`jobPending`='Y' WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute($updateArray);

            if (strlen($initialpassword) > 1) {
                $query = $sql->prepare("UPDATE `gsswitch` SET `ftppassword`=AES_ENCRYPT(?,?) WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($initialpassword, $aeskey, $localID, $resellerID));
            }

            customColumns('G', $localID,'save', $data);

            // Updating the gameswitch list. If the currently active game is removed we to update the server type at gsswitch.
            // In any remove case we need to add a job entry to remove the game from the app root
            $gamesToBeRemoved = array();

            if (isset($shorten)) {

                $installedGameList = array();

                // First get the current list
                $query = $sql->prepare("SELECT l.`id`,t.`shorten` FROM `serverlist` AS l LEFT JOIN `servertypes` AS t ON t.`id`=l.`servertype` WHERE l.`switchID`=? AND l.`resellerid`=?");
                $query->execute(array($localID, $resellerID));
                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $installedGameList[$row['id']] = $row['shorten'];
                }

                // This section will add missing games, if the according masterserver is installed
                $query = $sql->prepare("SELECT t.*,l.`id` AS `list_id` FROM `servertypes` AS t INNER JOIN `rservermasterg` AS m ON m.`servertypeid`=t.`id` LEFT JOIN `serverlist` AS l ON l.`servertype`=t.`id` AND l.`switchID`=? WHERE t.`shorten`=? AND t.`resellerid`=? LIMIT 1");
                $query2 = $sql->prepare("INSERT INTO `serverlist` (`servertype`,`switchID`,`map`,`mapGroup`,`cmd`,`modcmd`,`tic`,`fps`,`gamemod`,`gamemod2`,`resellerid`) VALUES (?,?,?,?,?,?,?,?,?,?,?)");

                foreach ($shorten as $singleShorten) {

                    $query->execute(array($localID, $singleShorten, $resellerID));
                    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

                        if ($row['list_id'] === null) {

                            $modcmd = '';

                            foreach (explode("\r\n", $row['modcmds']) as $line) {

                                if (preg_match('/^(\[[\w\/\.\-\_\= ]{1,}\])$/', $line)) {

                                    $cmdName = trim($line, '[]');
                                    $ex = preg_split("/\=/", $cmdName, -1,PREG_SPLIT_NO_EMPTY);

                                    if (isset($ex[1]) and trim($ex[1]) == 'default') {
                                        $modcmd = trim($ex[0]);
                                        break;
                                    }
                                }
                            }

                            $query2->execute(array($row['id'], $localID, $row['map'], $row['mapGroup'], $row['cmd'], $modcmd, $row['tic'], $row['fps'], $row['gamemod'], $row['gamemod2'], $resellerID));

                            if (!isset($lastServerID) or (isset($data['primary']) and gamestring($data['primary']) and $shorten == $data['primary'])) {
                                $lastServerID = $sql->lastInsertId();
                            }

                        } else {
                            unset($installedGameList[$row['list_id']]);
                        }
                    }
                }

                // Remove games that no longer exists
                $query = $sql->prepare("DELETE FROM `serverlist` WHERE `id`=? AND `switchID`=? AND `resellerid`=? LIMIT 1");

                foreach ($installedGameList as $removeID => $shorten) {

                    $query->execute(array($removeID, $localID, $resellerID));

                    $gamesToBeRemoved[] = $shorten;
                }
            }

            $gamesRemoveAmount = count($gamesToBeRemoved);
            $gamesRemoveString = ($gamesRemoveAmount > 0) ? $gamesRemoveAmount . '_' . implode('_', $gamesToBeRemoved) : '';

            $customID = $localID;

            if ($active != $oldActive or $port != $oldPort or $homeDirLabel != $oldHomeDirLabel or $hdd != $oldHdd or $pallowed != $oldProtected or $gamesRemoveAmount > 0) {

                $query = $sql->prepare("UPDATE `jobs` SET `status`='2' WHERE `type`='gs' AND (`status` IS NULL OR `status`='1') AND `action`!='ad' AND `affectedID`=? and `resellerID`=?");
                $query->execute(array($localID, $resellerID));

                $query = $sql->prepare("INSERT INTO `jobs` (`api`,`type`,`hostID`,`invoicedByID`,`affectedID`,`userID`,`name`,`status`,`date`,`action`,`extraData`,`resellerID`) VALUES ('A','gs',?,?,?,?,?,NULL,NOW(),'md',?,?)");
                $query->execute(array($hostID, $resellerID, $localID, $userID, $name, json_encode(array('newActive' => $active, 'newPort' => $port, 'oldProtected' => $oldProtected, 'homeDirChanged' => ($homeDirLabel != $oldHomeDirLabel) ? 1 : 0, 'installGames' => 'N', 'gamesRemoveString' => $gamesRemoveString)), $resellerID));
            }
        }

        if (!isset($oldSlots)) {
            $success['false'][] = 'No server can be found to edit';
        }

    } else {
        $success['false'][] = 'No data for this method: ' . $data['action'];
    }

} else if (!isset($success['false']) and array_value_exists('action', 'del', $data)) {

    $identifyServerBy = $data['identify_server_by'];
    $localServerID = isid($data['server_local_id'], 21);
    $externalServerID = isExternalID($data['server_external_id']);

    $from = array('server_local_id' => 'id', 'server_external_id' => 'externalID');

    if (dataExist('identify_server_by', $data)) {

        $query = $sql->prepare("SELECT r.`externalID`,g.`id`,g.`serverip`,g.`port`,g.`userid`,g.`rootID` FROM `gsswitch` g LEFT JOIN `rserverdata` r ON g.`rootID`=r.`id` WHERE g.`".$from[$data['identify_server_by']]."`=? AND g.`resellerid`=?");
        $query->execute(array($data[$data['identify_server_by']], $resellerID));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $localID = $row['id'];
            $userID = $row['userid'];
            $name = $row['serverip'] . ':' . $row['port'];
            $hostID = $row['rootID'];
            $hostExternalID = $row['rootID'];
        }

        if (isset($localID) and isset($name)) {

            $query = $sql->prepare("UPDATE `gsswitch` SET `jobPending`='Y' WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($localID, $resellerID));

            $query = $sql->prepare("UPDATE `jobs` SET `status`='2' WHERE `type`='gs' AND (`status` IS NULL OR `status`='1') AND `affectedID`=? and `resellerID`=?");
            $query->execute(array($localID, $resellerID));

            $query = $sql->prepare("INSERT INTO `jobs` (`api`,`type`,`hostID`,`invoicedByID`,`affectedID`,`userID`,`name`,`status`,`date`,`action`,`resellerid`) VALUES ('A','gs',?,?,?,?,?,NULL,NOW(),'dl',?)");
            $query->execute(array($hostID, $resellerID, $localID, $userID, $name, $resellerID));

        } else {
            $success['false'][] = 'No server can be found to delete';
        }

    } else {
        $success['false'][] = 'No data for this method: '.$data['action'];
    }

} else if (array_value_exists('action', 'ls', $data)) {

    $list = true;

    $query = $sql->prepare("SELECT r.`id`,r.`ip`,r.`description`,r.`altips`,r.`maxslots`,r.`maxserver`,r.`maxserver`-COUNT(g.`id`) AS `freeserver`,COUNT(g.`id`) AS `installedserver`,r.`active` AS `hostactive`,r.`resellerid` AS `resellerid`,(r.`maxslots`-SUM(g.`slots`)) AS `leftslots`,SUM(g.`slots`) AS `installedslots` FROM `rserverdata` r LEFT JOIN `gsswitch` g ON g.`rootID`=r.`id` GROUP BY r.`id` HAVING ((`freeserver` > 0 OR `freeserver` IS NULL) AND (`leftslots`>0 OR `leftslots` IS NULL) AND `hostactive`='Y' AND `resellerid`=?) ORDER BY `freeserver` DESC");
    $query2 = $sql->prepare("SELECT t.`shorten`,t.`description` FROM `rservermasterg` AS r INNER JOIN `servertypes` AS t ON r.`servertypeid` = t.`id` WHERE r.`serverid`=?");

    $query->execute(array($resellerID));

    if ($apiType == 'xml') {

        header("Content-type: text/xml; charset=UTF-8");

        $responsexml = new DOMDocument('1.0','utf-8');
        $element = $responsexml->createElement('gserver');

        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            $key = $responsexml->createElement('server');

            $listServerXML = $responsexml->createElement('id', $row['id']);
            $key->appendChild($listServerXML);

            $listServerXML = $responsexml->createElement('ip', $row['ip']);
            $key->appendChild($listServerXML);

            $listServerXML = $responsexml->createElement('description', $row['description']);
            $key->appendChild($listServerXML);

            $listServerXML = $responsexml->createElement('altips', $row['altips']);
            $key->appendChild($listServerXML);

            $listServerXML = $responsexml->createElement('maxslots', $row['maxslots']);
            $key->appendChild($listServerXML);

            $listServerXML = $responsexml->createElement('maxserver', $row['maxserver']);
            $key->appendChild($listServerXML);

            $listServerXML = $responsexml->createElement('freeserver', $row['freeserver']);
            $key->appendChild($listServerXML);

            $listServerXML = $responsexml->createElement('installedserver', $row['installedserver']);
            $key->appendChild($listServerXML);

            $listServerXML = $responsexml->createElement('leftslots', $row['leftslots']);
            $key->appendChild($listServerXML);

            $listServerXML = $responsexml->createElement('installedslots', $row['installedslots']);
            $key->appendChild($listServerXML);

            $listServerXML = $responsexml->createElement('gamesavailable');

            $query2->execute(array($row['id']));
            foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                $listShortenXML = $responsexml->createElement($row2['shorten'], $row2['description']);
                $listServerXML->appendChild($listShortenXML);
            }

            $key->appendChild($listServerXML);

            $element->appendChild($key);
        }

        $responsexml->appendChild($element);

        $responsexml->formatOutput = true;

        echo $responsexml->saveXML();

    } else if ($apiType == 'json') {

        header("Content-type: application/json; charset=UTF-8");

        echo json_encode($query->fetchAll(PDO::FETCH_ASSOC));

    } else {
        header('HTTP/1.1 403 Forbidden');
        die('403 Forbidden');
    }

} else if (!isset($success['false']) and array_value_exists('action', 'gs', $data)) {

    $identifyServerBy = $data['identify_server_by'];
    $localServerID = isid($data['server_local_id'], 10);
    $externalServerID = isExternalID($data['server_external_id']);

    if (isset($data['restart']) and ($data['restart'] == 're' or $data['restart'] == 'st')) {

        $from = array('server_local_id' => 'id', 'server_external_id' => 'externalID');
        $gsRestart = $data['restart'];

        if (dataExist('identify_server_by', $data)) {

            $query = $sql->prepare("SELECT `id`,`userid`,`rootID`,`serverip`,`port` FROM `gsswitch` WHERE `".$from[$data['identify_server_by']]."`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($data[$data['identify_server_by']], $resellerID));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $hostID = $row['rootID'];
                $userID = $row['userid'];
                $localID = $row['id'];
                $name = $row['serverip'] . ':' . $row['port'];
            }

            if (isset($localID) and isset($userID)) {

                $query = $sql->prepare("UPDATE `gsswitch` SET `jobPending`='Y' WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($localID, $resellerID));

                $query = $sql->prepare("UPDATE `jobs` SET `status`='2' WHERE `type`='gs' AND (`status` IS NULL OR `status`='1') AND (`action`='re' OR `action`='st') AND `affectedID`=? and `resellerID`=?");
                $query->execute(array($localID, $resellerID));

                $query = $sql->prepare("INSERT INTO `jobs` (`api`,`type`,`hostID`,`invoicedByID`,`affectedID`,`userID`,`name`,`status`,`date`,`action`,`resellerid`) VALUES ('A','gs',?,?,?,?,?,NULL,NOW(),?,?)");
                $query->execute(array($hostID, $resellerID, $localID, $userID, $name, $gsRestart, $resellerID));

            } else {
                $success['false'][] = 'No server can be found to edit';
            }

        } else {
            $success['false'][] = 'Server cannot be identified';
        }

    } else {
        $success['false'][] = '(Re)start or Stop not defined';
    }

} else {
    $success['false'][] = 'Not supported method or incomplete data';
}

if ($apiType == 'xml' and !isset($list)) {

    if (isset($success['false'])) {
        $errors = implode(', ', $success['false']);
        $action = 'fail';
    } else {
        $errors = '';
        $action = 'success';
    }

    header("Content-type: text/xml; charset=UTF-8");

    $responsexml = new DOMDocument('1.0','utf-8');
    $element = $responsexml->createElement('gserver');

    $key = $responsexml->createElement('action', $action);
    $element->appendChild($key);

    $key = $responsexml->createElement('actionSend', (isset($data['action']) ? $data['action'] : ''));
    $element->appendChild($key);

    $key = $responsexml->createElement('private', $private);
    $element->appendChild($key);

    $key = $responsexml->createElement('active', $active);
    $element->appendChild($key);

    $key = $responsexml->createElement('identify_server_by', $identifyServerBy);
    $element->appendChild($key);

    $key = $responsexml->createElement('slots', $slots);
    $element->appendChild($key);

    $key = $responsexml->createElement('server_external_id', $externalServerID);
    $element->appendChild($key);

    $key = $responsexml->createElement('server_local_id', $localServerID);
    $element->appendChild($key);

    $key = $responsexml->createElement('identify_user_by', $identifyUserBy);
    $element->appendChild($key);

    $key = $responsexml->createElement('user_localid', $localUserID);
    $element->appendChild($key);

    $key = $responsexml->createElement('user_externalid', $externalUserID);
    $element->appendChild($key);

    $key = $responsexml->createElement('username', $username);
    $element->appendChild($key);

    $key = $responsexml->createElement('home_label', $homeDirLabel);
    $element->appendChild($key);

    $key = $responsexml->createElement('hdd', $hdd);
    $element->appendChild($key);

    $key = $responsexml->createElement('taskset', $taskset);
    $element->appendChild($key);

    $key = $responsexml->createElement('cores', $cores);
    $element->appendChild($key);

    $key = $responsexml->createElement('eacallowed', $eacallowed);
    $element->appendChild($key);

    $key = $responsexml->createElement('brandname', $brandname);
    $element->appendChild($key);

    $key = $responsexml->createElement('tvenable', $tvenable);
    $element->appendChild($key);

    $key = $responsexml->createElement('pallowed', $pallowed);
    $element->appendChild($key);

    $key = $responsexml->createElement('serverName', $name);
    $element->appendChild($key);

    $key = $responsexml->createElement('ip', $ip);
    $element->appendChild($key);

    $key = $responsexml->createElement('port', $port);
    $element->appendChild($key);

    $key = $responsexml->createElement('port2', $port2);
    $element->appendChild($key);

    $key = $responsexml->createElement('port3', $port3);
    $element->appendChild($key);

    $key = $responsexml->createElement('port4', $port4);
    $element->appendChild($key);

    $key = $responsexml->createElement('port5', $port5);
    $element->appendChild($key);

    $key = $responsexml->createElement('minram', $minram);
    $element->appendChild($key);

    $key = $responsexml->createElement('maxram', $maxram);
    $element->appendChild($key);

    $key = $responsexml->createElement('master_server_id', $hostID);
    $element->appendChild($key);

    $key = $responsexml->createElement('master_server_external_id', $hostExternalID);
    $element->appendChild($key);

    $key = $responsexml->createElement('initialpassword', $initialpassword);
    $element->appendChild($key);

    $key = $responsexml->createElement('installGames', $installGames);
    $element->appendChild($key);

    $key = $responsexml->createElement('autoRestart', $autoRestart);
    $element->appendChild($key);

    $key = $responsexml->createElement('ftpUser', $ftpUser);
    $element->appendChild($key);

    $key = $responsexml->createElement('errors', $errors);
    $element->appendChild($key);

    if (isset ($shorten) and is_array($shorten)) {
        foreach ($shorten as $short) {
            $key = $responsexml->createElement('shorten', $short);
            $element->appendChild($key);
        }
    }

    foreach(customColumns('G', $customID) as $row) {
        $key = $responsexml->createElement($row['name'], $row['value']);
        $element->appendChild($key);
    }

    $responsexml->appendChild($element);

    $responsexml->formatOutput = true;

    echo $responsexml->saveXML();

} else if ($apiType == 'json' and !isset($list)) {

    header("Content-type: application/json; charset=UTF-8");

    echo json_encode(array('action' => $action,'private' => $private,'active' => $active,'identify_server_by' => $identifyServerBy,'shorten' => $shorten,'slots' => $slots,'server_external_id' => $externalServerID,'server_local_id' => $localServerID,'identify_user_by' => $identifyUserBy,'user_localid' => $localUserID,'user_externalid' => $externalUserID,'username' => $username,'errors' => $errors));

} else if (!isset($list)) {
    header('HTTP/1.1 403 Forbidden');
    die('403 Forbidden');
}