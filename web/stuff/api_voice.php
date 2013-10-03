<?php
/**
 * File: api_voice.php.
 * Author: Ulrich Block
 * Date: 30.05.12
 * Time: 20:29
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


foreach (array('active','action','private','slots','shorten','identify_server_by','server_local_id','server_external_id','identify_user_by','user_localid','user_externalid','username') as $key) {
    if (!array_key_exists($key,$data)) {
        $success['false'][] = 'Data key does not exist: '.$key;
    }
}
$action='fail';
$active = '';
$name = '';
$private = '';
$shorten = '';
$slots = '';
$port = '';
$identifyUserBy = '';
$localUserID = '';
$externalUserID = '';
$username = '';
$identifyServerBy = '';
$localServerID = '';
$externalServerID = '';
$max_download_total_bandwidth = '';
$max_upload_total_bandwidth = '';
$maxtraffic = '';
$forcebanner = '';
$forcebutton = '';
$forceservertag = '';
$forcewelcome = '';
$lendserver = '';
$backup = '';
$masterServerID = '';
$masterServerExternalID = '';
$flexSlots = '';
$flexSlotsFree = '';
$flexSlotsPercent = '';
$tsdns = '';
$dns = '';
$autoRestart = '';
if (!isset($success['false']) and array_value_exists('action','add',$data) and $data['shorten'] == 'ts3' and 1>$licenceDetails['lVo']) {
    $success['false'][] = 'licence limit reached';
} else if (!isset($success['false']) and array_value_exists('action','add',$data) and $data['shorten'] == 'ts3' and $licenceDetails['lVo']>0) {
    if (dataExist('identify_user_by',$data) and isid($data['slots'],11)) {
        $active=active_check($data['active']);
        $private=active_check($data['private']);
        $shorten=$data['shorten'];
        $slots=isid($data['slots'],11);
        $identifyUserBy=$data['identify_user_by'];
        $localUserID=isid($data['user_localid'],21);
        $externalUserID=$data['user_externalid'];
        $username=$data['username'];
        $identifyServerBy=$data['identify_server_by'];
        $localServerID=isid($data['server_local_id'],21);
        $externalServerID=$data['server_external_id'];
        $from=array('user_localid'=>'id','username'=>'cname','user_externalid'=>'externalID','email'=>'mail');
        $query = $sql->prepare("SELECT `id`,`cname` FROM `userdata` WHERE `".$from[$data['identify_user_by']]."`=? AND `resellerid`=?");
        $query->execute(array($data[$data['identify_user_by']],$resellerID));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $localUserLookupID=$row['id'];
            $username=$row['cname'];
        }
        if (!isset($localUserLookupID)) {
            $success['false'][] = 'user does not exist';
        }
        if (!isset($success['false']) and !in_array($externalServerID,$bad)) {
            $query = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `voice_server` WHERE `externalID`=? LIMIT 1");
            $query->execute(array($externalServerID));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                if ($row['amount']>0) {
                    $success['false'][] = 'server with external ID already exists';
                }
            }
        }
        if (!isset($success['false'])) {
            if (isset($data['master_server_id']) and isid($data['master_server_id'],19)) {
                $query = $sql->prepare("SELECT m.`id` AS `hostID`,m.*,COUNT(v.`id`)*(100/m.`maxserver`) AS `serverpercent`,SUM(v.`slots`)*(100/m.`maxslots`) AS `slotpercent`,COUNT(v.`id`) AS `installedserver`,SUM(v.`slots`) AS `installedslots`,SUM(v.`usedslots`) AS `uslots`,r.`ip`  FROM `voice_masterserver` m LEFT JOIN `rserverdata` r ON m.`rootid`=r.`id` LEFT JOIN `voice_server` v ON m.`id`=v.`masterserver` WHERE m.`id`=? AND m.`resellerid`=? LIMIT 1");
                $query->execute(array($data['master_server_id'],$resellerID));
            } else if (isset($data['master_server_external_id']) and wpreg_check($data['master_server_external_id'],255)) {
                $query = $sql->prepare("SELECT m.`id` AS `hostID`,m.*,COUNT(v.`id`)*(100/m.`maxserver`) AS `serverpercent`,SUM(v.`slots`)*(100/m.`maxslots`) AS `slotpercent`,COUNT(v.`id`) AS `installedserver`,SUM(v.`slots`) AS `installedslots`,SUM(v.`usedslots`) AS `uslots`,r.`ip`  FROM `voice_masterserver` m LEFT JOIN `rserverdata` r ON m.`rootid`=r.`id` LEFT JOIN `voice_server` v ON m.`id`=v.`masterserver` WHERE m.`externalID`=? AND m.`resellerid`=? LIMIT 1");
                $query->execute(array($data['master_server_external_id'],$resellerID));
                $masterServerExternalID=$data['master_server_external_id'];
            } else {
                $query = $sql->prepare("SELECT m.`id` AS `hostID`,m.*,COUNT(v.`id`)*(100/m.`maxserver`) AS `serverpercent`,SUM(v.`slots`)*(100/m.`maxslots`) AS `slotpercent`,COUNT(v.`id`) AS `installedserver`,SUM(v.`slots`) AS `installedslots`,SUM(v.`usedslots`) AS `uslots`,r.`ip`  FROM `voice_masterserver` m LEFT JOIN `rserverdata` r ON m.`rootid`=r.`id` LEFT JOIN `voice_server` v ON m.`id`=v.`masterserver` GROUP BY m.`id` HAVING (`installedserver`<`maxserver` AND (`installedslots`<`maxslots` OR `installedslots` IS NULL) AND ((`maxslots`-`installedslots`)>? OR `installedslots` IS NULL) AND `active`='Y' AND `resellerid`=?) ORDER BY `slotpercent`,`serverpercent` ASC LIMIT 1");
                $query->execute(array($slots,$resellerID));
            }
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $masterServerID=$row['hostID'];
                if ($row['active'] == 'Y') {
                    $hostID=$row['hostID'];
                    $name=$row['defaultname'];
                    $welcome=$row['defaultwelcome'];
                    $hostbanner_url=$row['defaulthostbanner_url'];
                    $hostbanner_gfx_url=$row['defaulthostbanner_gfx_url'];
                    $hostbutton_tooltip=$row['defaulthostbutton_tooltip'];
                    $hostbutton_url=$row['defaulthostbutton_url'];
                    $hostbutton_gfx_url=$row['defaulthostbutton_gfx_url'];
                    $defaultFlexSlotsFree=$row['defaultFlexSlotsFree'];
                    $defaultFlexSlotsPercent=$row['defaultFlexSlotsPercent'];
                    $usedns=$row['usedns'];
                    $defaultdns=$row['defaultdns'];
                    if ($row['externalDefaultDNS'] == 'Y' and isid($row['tsdnsServerID'],19)) {
                        $query2 = $sql->prepare("SELECT `defaultdns` FROM `voice_tsdns` WHERE `active`='Y' AND `id`=? AND `resellerid`=? LIMIT 1");
                        $query2->execute(array($tsdnsServerID,$resellerID));
                        $defaultdns=$query2->fetchColumn();
                    }
                    if ($row['addedby'] == '2') {
                        $ips[]=$row['ssh2ip'];
                        foreach (preg_split('/\r\n/', $row['ips'],-1,PREG_SPLIT_NO_EMPTY) as $ip) {
                            $ips[]=$ip;
                        }
                    } else if ($row['addedby'] == '1') {
                        $query2 = $sql->prepare("SELECT `ip`,`altips` FROM `rserverdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                        $query2->execute(array($row['rootid'],$resellerID));
                        foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                            $ips[]=$row2['ip'];
                            foreach (preg_split('/\r\n/', $row2['altips'],-1,PREG_SPLIT_NO_EMPTY) as $ip) {
                                $ips[]=$ip;
                            }
                        }
                    }
                } else {
                    $success['false'][] = 'Host is inactive. Internal ID is: '.$row['hostID'];
                }
            }
            if (!isset($success) and !isset($hostID)) {
                $success['false'][] = 'No free host';
            }
        }
        if (!isset($success['false']) and isset($ips)) {
            $usedPorts=usedPorts($ips);
            $ip=$usedPorts['ip'];
            $ports=$usedPorts['ports'];
            $ports[]=10011;
            if (isset($data['port']) and port($data['port']) and !in_array($data['port'],$ports)) {
                $port=$data['port'];
            } else {
                $port=9987;
            }
            if (is_array($ports)) {
                while(in_array($port,$ports)) {
                    $port++;
                }
                $max_download_total_bandwidth=(isset($data['max_download_total_bandwidth']) and isid($data['max_download_total_bandwidth'],255)) ? $data['max_download_total_bandwidth'] : 65536;
                $max_upload_total_bandwidth=(isset($data['max_upload_total_bandwidth']) and isid($data['max_upload_total_bandwidth'],255)) ? $data['max_upload_total_bandwidth'] : 65536;
                $maxtraffic=(isset($data['maxtraffic']) and ($data['maxtraffic']==0 or $data['maxtraffic'] == '-1') or isid($data['maxtraffic'],255)) ? $data['maxtraffic'] : 1024;
                $forcebanner=(isset($data['forcebanner']) and active_check($data['forcebanner'])) ? $data['forcebanner'] : 'Y';
                $forcebutton=(isset($data['forcebutton']) and active_check($data['forcebutton'])) ? $data['forcebutton'] : 'Y';
                $forceservertag=(isset($data['forceservertag']) and active_check($data['forceservertag'])) ? $data['forceservertag'] : 'Y';
                $forcewelcome=(isset($data['forcewelcome']) and active_check($data['forcewelcome'])) ? $data['forcewelcome'] : 'Y';
                $lendserver=(isset($data['lendserver']) and active_check($data['lendserver'])) ? $data['lendserver'] : 'N';
                $backup=(isset($data['backup']) and active_check($data['backup'])) ? $data['backup'] : 'Y';
                $flexSlots=(isset($data['flexSlots']) and active_check($data['flexSlots'])) ? $data['flexSlots'] : 'N';
                $flexSlotsFree=(isset($data['flexSlotsFree']) and isid($data['flexSlotsFree'],11)) ? $data['flexSlotsFree'] : $defaultFlexSlotsFree;
                $flexSlotsPercent=(isset($data['flexSlotsPercent']) and isid($data['flexSlotsPercent'],3)) ? $data['flexSlotsPercent'] : $defaultFlexSlotsPercent;
                $autoRestart=(isset($data['autoRestart']) and active_check($data['autoRestart'])) ? $data['autoRestart'] : 'Y';
                $initialpassword=passwordgenerate(10);
                $query = $sql->prepare("INSERT INTO `voice_server` (`active`,`lendserver`,`backup`,`userid`,`masterserver`,`ip`,`port`,`slots`,`initialpassword`,`password`,`max_download_total_bandwidth`,`max_upload_total_bandwidth`,`localserverid`,`maxtraffic`,`forcebanner`,`forcebutton`,`forceservertag`,`forcewelcome`,`externalID`,`jobPending`,`serverCreated`,`flexSlots`,`flexSlotsFree`,`flexSlotsPercent`,`autoRestart`,`resellerid`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,0,?,?,?,?,?,?,'Y',NOW(),?,?,?,?,?)");
                $query->execute(array($active,$lendserver,$backup,$localUserLookupID,$hostID,$ip,$port,$slots,$initialpassword,$private,$max_download_total_bandwidth,$max_upload_total_bandwidth,$maxtraffic,$forcebanner,$forcebutton,$forceservertag,$forcewelcome,$externalServerID,$flexSlots,$flexSlotsFree,$flexSlotsPercent,$autoRestart,$resellerID));
                $localID=$sql->lastInsertId();
                $localServerID=$localID;
                if (isid($localID,10)) {
                    if($usedns == 'Y' and isset($data['tsdns']) and active_check($data['tsdns'])) {
                        $tsdns=$data['tsdns'];
                    } else if($usedns == 'Y' and (!isset($data['tsdns']) or !active_check($data['tsdns']))) {
                        $tsdns = 'Y';
                    } else {
                        $tsdns = 'N';
                    }
                    if ($tsdns == 'Y') {
                        if (isdomain($data['dns'])) {
                            $dns=$data['dns'];
                        } else {
                            $dns=strtolower($localID.'.'.$defaultdns);
                        }
                        $query = $sql->prepare("UPDATE `voice_server` SET `dns`=? WHERE `id`=? LIMIT 1");
                        $query->execute(array($dns,$localID));
                    }
                    $query = $sql->prepare("UPDATE `jobs` SET `status`='2' WHERE `type`='vo' AND (`status` IS NULL OR `status`='1') AND `affectedID`=? and `resellerID`=?");
                    $query->execute(array($localID,$resellerID));
                    $query = $sql->prepare("INSERT INTO `jobs` (`api`,`type`,`hostID`,`invoicedByID`,`affectedID`,`userID`,`name`,`status`,`date`,`action`,`resellerid`) VALUES ('A','vo',?,?,?,?,?,NULL,NOW(),'ad',?)");
                    $query->execute(array($hostID,$resellerID,$localID,$localUserLookupID,$ip . ':' . $port,$resellerID));
                } else {
                    $success['false'][] = 'Could not write voice server to database';
                }
            } else {
                $success['false'][] = 'Error: could not determine IP and Port or given data was already in use';
            }
        }
    } else if (!isset($success['false'])) {
        $active=active_check($data['active']);
        $private=active_check($data['private']);
        $shorten=$data['shorten'];
        $slots=isid($data['slots'],11);
        $identifyUserBy=$data['identify_user_by'];
        $localUserID=isid($data['user_localid'],21);
        $externalUserID=$data['user_externalid'];
        $username=$data['username'];
        $identifyServerBy=$data['identify_server_by'];
        $localServerID=isid($data['server_local_id'],21);
        $externalServerID=$data['server_external_id'];
        if (!dataExist('identify_user_by',$data)) {
            $success['false'][] = 'Can not identify user or bad email';
        } else {
            $success['false'][] = 'Slot amount needs to be specified';
        }
    }
} else if (!isset($success['false']) and array_value_exists('action','mod',$data) and $data['shorten'] == 'ts3') {
    $shorten=$data['shorten'];
    $identifyUserBy=$data['identify_user_by'];
    $localUserID=isid($data['user_localid'],21);
    $externalUserID=$data['user_externalid'];
    $username=$data['username'];
    $identifyServerBy=$data['identify_server_by'];
    $localServerID=isid($data['server_local_id'],21);
    $externalServerID=$data['server_external_id'];
    $from=array('server_local_id'=>'id','server_external_id'=>'externalID');
    if (dataExist('identify_server_by',$data)) {
        $query = $sql->prepare("SELECT * FROM `voice_server` WHERE `".$from[$data['identify_server_by']]."`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($data[$data['identify_server_by']],$resellerID));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $localID=$row['id'];
            $userID=$row['userid'];
            $hostID=$row['masterserver'];
            $masterServerID=$row['masterserver'];
            $oldSlots=$row['slots'];
            $name=$row['ip'] . ':' . $row['port'];
            $usedPorts=usedPorts(array($row['ip']));
            $oldActive=$row['active'];
            $query = $sql->prepare("SELECT COUNT(`jobID`) AS `amount` FROM `jobs` WHERE `affectedID`=? AND `resellerID`=? AND `action`='dl' AND (`status` IS NULL OR `status`='1') LIMIT 1");
            $query->execute(array($localID,$resellerID));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                if($row['amount']>0) {
                    $success['false'][] = 'Server is marked for deletion';
                }
            }
            $updateArray = array();
            $eventualUpdate = '';
            if (isset($data['private']) and active_check($data['private'])) {
                $updateArray[]=$data['private'];
                $eventualUpdate.=',`password`=?';
                $private=$data['private'];
            }
            if (isset($data['port']) and port($data['port']) and !in_array($data['port'],$usedPorts)) {
                $updateArray[]=$data['port'];
                $eventualUpdate.=',`port`=?';
                $port=$data['port'];
            }
            if (isset($data['active']) and active_check($data['active'])) {
                $updateArray[]=$data['active'];
                $eventualUpdate.=',`active`=?';
                $active=$data['active'];
            }
            if (isset($data['slots']) and isid($data['slots'],11)) {
                $updateArray[]=$data['slots'];
                $eventualUpdate.=',`slots`=?';
                $slots=$data['slots'];
            }
            if (isset($data['max_download_total_bandwidth']) and isid($data['max_download_total_bandwidth'],255)) {
                $updateArray[]=$data['max_download_total_bandwidth'];
                $eventualUpdate .=',`max_download_total_bandwidth`=?';
                $max_download_total_bandwidth=$data['max_download_total_bandwidth'];
            }
            if (isset($data['max_upload_total_bandwidth']) and isid($data['max_upload_total_bandwidth'],255)) {
                $updateArray[]=$data['max_upload_total_bandwidth'];
                $eventualUpdate .=',`max_upload_total_bandwidth`=?';
                $max_upload_total_bandwidth=$data['max_upload_total_bandwidth'];
            }
            if (isset($data['maxtraffic']) and ($data['maxtraffic']==0 or $data['maxtraffic'] == '-1' or isid($data['maxtraffic'],255))) {
                $updateArray[]=$data['maxtraffic'];
                $eventualUpdate .=',`maxtraffic`=?';
                $maxtraffic=$data['maxtraffic'];
            }
            if(isset($data['forcebanner']) and active_check($data['forcebanner'])) {
                $updateArray[]=$data['forcebanner'];
                $eventualUpdate .=',`forcebanner`=?';
                $forcebanner=$data['forcebanner'];
            }
            if(isset($data['forcebutton']) and active_check($data['forcebutton'])) {
                $updateArray[]=$data['forcebutton'];
                $eventualUpdate .=',`forcebutton`=?';
                $forcebutton=$data['forcebutton'];
            }
            if(isset($data['forceservertag']) and active_check($data['forceservertag'])) {
                $updateArray[]=$data['forceservertag'];
                $eventualUpdate .=',`forceservertag`=?';
                $forceservertag=$data['forceservertag'];
            }
            if(isset($data['forcewelcome']) and active_check($data['forcewelcome'])) {
                $updateArray[]=$data['forcewelcome'];
                $eventualUpdate .=',`forcewelcome`=?';
                $forcewelcome=$data['forcewelcome'];
            }
            if(isset($data['lendserver']) and active_check($data['lendserver'])) {
                $updateArray[]=$data['lendserver'];
                $eventualUpdate .=',`lendserver`=?';
                $lendserver=$data['lendserver'];
            }
            if(isset($data['backup']) and active_check($data['backup'])) {
                $updateArray[]=$data['backup'];
                $eventualUpdate .=',`backup`=?';
                $backup=$data['backup'];
            }
            if(isset($data['flexSlots']) and active_check($data['flexSlots'])) {
                $updateArray[]=$data['flexSlots'];
                $eventualUpdate .=',`flexSlots`=?';
                $flexSlots=$data['flexSlots'];
            }
            if(isset($data['flexSlotsFree']) and isid($data['flexSlotsFree'],11)) {
                $updateArray[]=$data['flexSlotsFree'];
                $eventualUpdate .=',`flexSlotsFree`=?';
                $flexSlotsFree=$data['flexSlotsFree'];
            }
            if(isset($data['flexSlotsPercent']) and isid($data['flexSlotsPercent'],3)) {
                $updateArray[]=$data['flexSlotsPercent'];
                $eventualUpdate .=',`flexSlotsPercent`=?';
                $flexSlotsPercent=$data['flexSlotsPercent'];
            }
            if(isset($data['tsdns']) and active_check($data['tsdns'])) {
                $tsdns=$data['tsdns'];
            }
            if(isset($data['dns']) and $tsdns == 'Y' and isdomain($data['dns'])) {
                $updateArray[]=$data['dns'];
                $eventualUpdate .=',`dns`=?';
                $dns=$data['dns'];
            }
            if(isset($data['autoRestart']) and active_check($data['autoRestart'])) {
                $updateArray[]=$data['autoRestart'];
                $eventualUpdate .=',`autoRestart`=?';
                $flexSlots=$data['autoRestart'];
            }
            if (count($updateArray)>0) {
                $eventualUpdate = trim($eventualUpdate,',');
                $eventualUpdate .=',';
            }
            $updateArray[]=$localID;
            $updateArray[]=$resellerID;
            $query = $sql->prepare("UPDATE `voice_server` SET $eventualUpdate `jobPending`='Y' WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute($updateArray);
            if (!in_array($active,$bad) and !in_array($slots,$bad) and ($active != $oldActive or $slots != $oldSlots)) {
                $update=$sql->prepare("UPDATE `jobs` SET `status`='2' WHERE `type`='vo' AND (`status` IS NULL OR `status`='1') AND `affectedID`=? and `resellerID`=?");
                $update->execute(array($localID,$resellerID));
                $insert=$sql->prepare("INSERT INTO `jobs` (`api`,`type`,`hostID`,`invoicedByID`,`affectedID`,`userID`,`name`,`status`,`date`,`action`,`resellerID`) VALUES ('A','vo',?,?,?,?,?,NULL,NOW(),'md',?)");
                $insert->execute(array($hostID,$resellerID,$localID,$userID,$name,$resellerID));
            }
        }
        if(!isset($oldSlots)) {
            $success['false'][] = 'No server can be found to edit';
        }
    } else {
        $success['false'][] = 'No data for this method';
    }
} else if (!isset($success['false']) and array_value_exists('action','del',$data) and $data['shorten'] == 'ts3') {
    $identifyServerBy=$data['identify_server_by'];
    $localServerID=isid($data['server_local_id'],21);
    $externalServerID=$data['server_external_id'];
    $from=array('server_local_id'=>'id','server_external_id'=>'externalID');
    if (dataExist('identify_server_by',$data)) {
        $query = $sql->prepare("SELECT `id`,`ip`,`port`,`userid`,`masterserver` AS `hostID` FROM `voice_server` WHERE `".$from[$data['identify_server_by']]."`=? AND `resellerid`=?");
        $query->execute(array($data[$data['identify_server_by']],$resellerID));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $localID=$row['id'];
            $userID=$row['userid'];
            $name=$row['ip'] . ':' . $row['port'];
            $hostID=$row['hostID'];
        }
        if(isset($localID) and isset($name)) {
            $query = $sql->prepare("UPDATE `voice_server` SET `jobPending`='Y' WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($localID,$resellerID));
            $query = $sql->prepare("UPDATE `jobs` SET `status`='2' WHERE `type`='vo' AND (`status` IS NULL OR `status`='1') AND `affectedID`=? and `resellerID`=?");
            $query->execute(array($localID,$resellerID));
            $query = $sql->prepare("INSERT INTO `jobs` (`api`,`type`,`hostID`,`invoicedByID`,`affectedID`,`userID`,`name`,`status`,`date`,`action`,`resellerid`) VALUES ('A','vo',?,?,?,?,?,NULL,NOW(),'dl',?)");
            $query->execute(array($hostID,$resellerID,$localID,$userID,$name,$resellerID));
        } else {
            $success['false'][] = 'No server can be found to delete';
        }
    } else {
        $success['false'][] = 'No data for this method';
    }
} else if (!isset($success['false']) and array_value_exists('action','ls',$data) and isset($data['identify_server_by'])) {
    $identifyServerBy=$data['identify_server_by'];
    $localServerID=isid($data['server_local_id'],21);
    $externalServerID=$data['server_external_id'];
    $from=array('server_local_id'=>'id','server_external_id'=>'externalID');
    if (dataExist('identify_server_by',$data)) {
        $list = true;
        $query = $sql->prepare("SELECT * FROM `voice_server` WHERE `".$from[$data['identify_server_by']]."`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($data[$data['identify_server_by']],$resellerID));
        if ($apiType == 'xml') {
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                header("Content-type: text/xml; charset=UTF-8");
                $reply="<?xml version='1.0' encoding='UTF-8'?>
<!DOCTYPE voice>
<voice>";
                foreach ($row as $k=>$v) {
                    $reply.='<'.$k.'>'.$v.'</'.$k.'>';
                }
                $reply.="</voice>";
                echo $reply;
            }
        } else if ($apiType == 'json') {
            header("Content-type: application/json; charset=UTF-8");
            echo json_encode($query->fetchAll(PDO::FETCH_ASSOC));
        } else {
            header('HTTP/1.1 403 Forbidden');
            die('403 Forbidden');
        }
    }
} else if (array_value_exists('action','ls',$data)) {
    $query = $sql->prepare("SELECT m.`id`,m.`usedns`,m.`ssh2ip`,m.`defaultname`,m.`defaultwelcome`,m.`defaulthostbanner_url`,m.`defaulthostbanner_gfx_url`,m.`defaulthostbutton_tooltip`,m.`defaulthostbutton_url`,m.`defaulthostbutton_gfx_url`,m.`maxserver`,m.`maxslots`,COUNT(v.`id`)*(100/m.`maxserver`) AS `serverpercent`,SUM(v.`slots`)*(100/m.`maxslots`) AS `slotpercent`,COUNT(v.`id`) AS `installedserver`,SUM(v.`slots`) AS `installedslots`,SUM(v.`usedslots`) AS `uslots` FROM `voice_masterserver` m LEFT JOIN `rserverdata` r ON m.`rootid`=r.`id` LEFT JOIN `voice_server` v ON m.`id`=v.`masterserver` WHERE m.`active`='Y' AND m.`resellerid`=? GROUP BY m.`id`");
    $query->execute(array($resellerID));
    $list = true;
    if ($apiType == 'xml') {
        $reply="<?xml version='1.0' encoding='UTF-8'?>
<!DOCTYPE voice>
<voice>";
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $reply .=' <server>
                <id>'.$row['id'].'</id>
                <usedns>'.$row['usedns'].'</usedns>
                <ssh2ip>'.$row['ssh2ip'].'</ssh2ip>
                <usedns>'.$row['usedns'].'</usedns>
                <serverpercent>'.$row['serverpercent'].'</serverpercent>
                <maxserver>'.$row['maxserver'].'</maxserver>
                <installedserver>'.$row['installedserver'].'</installedserver>
                <maxslots>'.$row['maxslots'].'</maxslots>
                <installedslots>'.$row['installedslots'].'</installedslots>
                <uslots>'.$row['uslots'].'</uslots>
                <defaultname>'.$row['defaultname'].'</defaultname>
                <defaultwelcome>'.$row['defaultwelcome'].'</defaultwelcome>
                <defaulthostbanner_url>'.$row['defaulthostbanner_url'].'</defaulthostbanner_url>
                <defaulthostbanner_gfx_url>'.$row['defaulthostbanner_gfx_url'].'</defaulthostbanner_gfx_url>
                <defaulthostbutton_tooltip>'.$row['defaulthostbutton_tooltip'].'</defaulthostbutton_tooltip>
                <defaulthostbutton_url>'.$row['defaulthostbutton_url'].'</defaulthostbutton_url>
                <defaulthostbutton_gfx_url>'.$row['defaulthostbutton_gfx_url'].'</defaulthostbutton_gfx_url>
                </server>';
        }
        $reply .='</voice>';
        header("Content-type: text/xml; charset=UTF-8");
        echo $reply;
    } else if ($apiType == 'json') {
        header("Content-type: application/json; charset=UTF-8");
        echo json_encode($query->fetchAll(PDO::FETCH_ASSOC));
    } else {
        header('HTTP/1.1 403 Forbidden');
        die('403 Forbidden');
    }
} else {
    $success['false'][] = 'Not supported method or incomplete data';
}

if ($apiType == 'xml' and !isset($list)) {
    header("Content-type: text/xml; charset=UTF-8");
    if (isset($success['false'])) {
        $errors=implode(', ',$success['false']);
    } else {
        $errors = '';
        $action='success';
    }
    $reply=<<<XML
<?xml version='1.0' encoding='UTF-8'?>
<!DOCTYPE voice>
<voice>
	<action>$action</action>
	<private>$private</private>
	<port>$port</port>
	<active>$active</active>
	<address>$name</address>
	<max_download_total_bandwidth>$max_download_total_bandwidth</max_download_total_bandwidth>
    <max_upload_total_bandwidth>$max_upload_total_bandwidth</max_upload_total_bandwidth>
    <maxtraffic>$maxtraffic</maxtraffic>
    <forcebanner>$forcebanner</forcebanner>
    <forcebutton>$forcebutton</forcebutton>
    <forceservertag>$forceservertag</forceservertag>
    <forcewelcome>$forcewelcome</forcewelcome>
    <lendserver>$lendserver</lendserver>
    <backup>$backup</backup>
	<identify_server_by>$identifyServerBy</identify_server_by>
	<shorten>$shorten</shorten>
	<slots>$slots</slots>
	<server_external_id>$externalServerID</server_external_id>
	<server_local_id>$localServerID</server_local_id>
	<master_server_id>$masterServerID</master_server_id>
	<master_server_external_id>$masterServerExternalID</master_server_external_id>
	<identify_user_by>$identifyUserBy</identify_user_by>
	<user_localid>$localUserID</user_localid>
	<user_externalid>$externalUserID</user_externalid>
	<username>$username</username>
    <flexSlots>$flexSlots</flexSlots>
    <flexSlotsFree>$flexSlotsFree</flexSlotsFree>
    <flexSlotsPercent>$flexSlotsPercent</flexSlotsPercent>
    <tsdns>$tsdns</tsdns>
    <dns>$dns</dns>
    <autoRestart>$autoRestart</autoRestart>
	<errors>$errors</errors>
</voice>
XML;
    print $reply;
} else if ($apiType == 'json' and !isset($list)) {
    header("Content-type: application/json; charset=UTF-8");
    echo json_encode(array('action'=>$action,'private'=>$private,'active'=>$active,'identify_server_by'=>$identifyServerBy,'shorten'=>$shorten,'slots'=>$slots,'server_external_id'=>$externalServerID,'server_local_id'=>$localServerID,'identify_user_by'=>$identifyUserBy,'user_localid'=>$localUserID,'user_externalid'=>$externalUserID,'username'=>$username,'errors'=>$errors));
} else if (!isset($list)) {
    header('HTTP/1.1 403 Forbidden');
    die('403 Forbidden');
}