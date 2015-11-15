<?php

/**
 * File: api_users.php.
 * Author: Ulrich Block
 * Date: 20.05.12
 * Time: 16:37
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
include(EASYWIDIR . '/third_party/password_compat/password.php');

$username = '';
$externalID = '';
$email = '';
$password = '';
$active = '';
$errors = '';
$localID = '';
$name = '';
$vname = '';
$phone = '';
$handy = '';
$fax = '';
$city = '';
$cityn = '';
$street = '';
$streetn = '';
$salutation = '';
$birthday = '';
$country = '';
$fdlpath = '';
$mail_backup = '';
$mail_gsupdate = '';
$mail_securitybreach = '';
$mail_serverdown = '';
$mail_ticket = '';
$mail_vserver = '';
$passwordSet = '';

if (array_value_exists('action', 'add', $data)) {

    if (ismail($data['email'])) {

        $localID = '';
        $userGroupIDs = array();
        $salt = md5(mt_rand() . date('Y-m-d H:i:s:u'));

        $email = $data['email'];
        $identifyBy = $data['identify_by'];
        $username = (isset($data['username'])) ? $data['username'] : '';
        $externalID = isExternalID($data['external_id']);
        $active = active_check($data['active']);

        $query = $sql->prepare("SELECT `mail`,`cname` FROM `userdata` WHERE `mail`=? OR `cname`=? LIMIT 1");
        $query->execute(array($email, $username));
        $amount = $query->rowCount();

        if ($amount > 0) {

            foreach($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $username = $row['cname'];
                $email = $row['mail'];
            }

            $success['false'][] = 'user with this e-mail already exists: ' . $username;
        }

        if (!isset($success['false']) and !in_array($externalID, $bad)) {

            $query = $sql->prepare("SELECT COUNT(`id`) AS `amount`,`mail`,`cname` FROM `userdata` WHERE `externalID`=? LIMIT 1");
            $query->execute(array($externalID));
            $amount2 = (int) $query->fetchColumn();

            $amount += $amount2;

            if ($amount2 > 0) {
                $username = $row['cname'];
                $email = $row['mail'];
                $success['false'][] = 'user with external ID exists: ' . $username;
            }
        }

        if (!in_array($username, $bad)) {

            $tmpName = $username;

        } else {

            $query = $sql->prepare("SELECT `prefix2` FROM `settings` WHERE `resellerid`=? LIMIT 1");
            $query->execute(array($resellerID));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $prefix = $row['prefix2'];
                $tmpName = $row['prefix2'].$salt;
            }

        }

        $query = $sql->prepare("SELECT `id` FROM `usergroups` WHERE `active`='Y' AND `id`=? AND `resellerid`=? LIMIT 1");

        if (isset($data['groupID']) and isid($data['groupID'], 19)) {

            $query->execute(array($data['groupID'], $resellerID));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $userGroupIDs[] = $row['id'];
            }

        } else if (isset($data['groupID'])) {

            if (!is_array($data['groupID']) and !is_object($data['groupID'])) {
                $data['groupID'] = array($data['groupID']);
            }

            foreach ($data['groupID'] as $groupID) {
                if (isid($groupID, 19)) {
                    $query->execute(array($groupID, $resellerID));
                    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                        $userGroupIDs[] = $row['id'];
                    }
                }
            }
        }

        if (count($userGroupIDs) == 0 and $amount == 0) {

            $query = $sql->prepare("SELECT `id` FROM `usergroups` WHERE `grouptype`='u' AND `active`='Y' AND `defaultgroup`='Y' AND `resellerid`=? LIMIT 1");
            $query->execute(array($resellerID));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $userGroupIDs[] = $row['id'];
            }

            if (count($userGroupIDs) == 0) {
                $query = $sql->prepare("SELECT `id` FROM `usergroups` WHERE `grouptype`='u' AND `active`='Y' AND `resellerid`=? LIMIT 1");
                $query->execute(array($resellerID));
                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $userGroupIDs[] = $row['id'];
                }
            }
        }

        if (isset($data['name']) and names($data['name'], 255)) {
            $name = names($data['name'], 255);
        }

        if (isset($data['vname']) and names($data['vname'], 255)) {
            $vname = names($data['vname'], 255);
        }

        if (isset($data['phone']) and phone($data['phone'])) {
            $phone = phone($data['phone']);
        }

        if (isset($data['handy']) and phone($data['handy'])) {
            $handy = phone($data['handy']);
        }

        if (isset($data['fax']) and phone($data['fax'])) {
            $fax = phone($data['fax']);
        }

        if (isset($data['city']) and names($data['city'], 50)) {
            $city = names($data['city'], 50);
        }

        if (isset($data['cityn']) and is_number($data['cityn'], 6)) {
            $cityn = is_number($data['cityn'], 6);
        }

        if (isset($data['street']) and names($data['street'], 50)) {
            $street = names($data['street'], 50);
        }

        if (isset($data['streetn']) and wpreg_check($data['streetn'], 6)) {
            $streetn = wpreg_check($data['streetn'], 6);
        }

        if (isset($data['salutation']) and is_number($data['salutation'], 1)) {
            $salutation = is_number($data['salutation'], 1);
        }

        if (isset($data['birthday']) and isDate($data['birthday'])) {
            $birthday = date('Y-m-d', strtotime(isDate($data['birthday'])));
        }

        if (isset($data['country']) and wpreg_check(strtolower($data['country']), 2)) {
            $country = wpreg_check(strtolower($data['country']), 2);
        }

        if (isset($data['fdlpath']) and isurl($data['fdlpath'])) {
            $fdlpath = $data['fdlpath'];
        }

        $mail_backup = (isset($data['mail_backup']) and active_check($data['mail_backup'])) ? $data['mail_backup'] : 'Y';
        $mail_gsupdate = (isset($data['mail_gsupdate']) and active_check($data['mail_gsupdate'])) ? $data['mail_gsupdate'] : 'Y';
        $mail_securitybreach = (isset($data['mail_securitybreach']) and active_check($data['mail_securitybreach'])) ? $data['mail_securitybreach'] : 'Y';
        $mail_serverdown = (isset($data['mail_serverdown']) and active_check($data['mail_serverdown'])) ? $data['mail_serverdown'] : 'Y';
        $mail_ticket = (isset($data['mail_ticket']) and active_check($data['mail_ticket'])) ? $data['mail_ticket'] : 'Y';
        $mail_vserver = (isset($data['mail_vserver']) and active_check($data['mail_vserver'])) ? $data['mail_vserver'] : 'Y';

        if (!isset($success['false']) and count($userGroupIDs) > 0) {

            $query = $sql->prepare("INSERT INTO `userdata` (`creationTime`,`updateTime`,`accounttype`,`active`,`cname`,`vname`,`name`,`mail`,`salt`,`phone`,`handy`,`fax`,`city`,`cityn`,`street`,`streetn`,`salutation`,`birthday`,`country`,`fdlpath`,`mail_backup`,`mail_gsupdate`,`mail_securitybreach`,`mail_serverdown`,`mail_ticket`,`mail_vserver`,`externalID`,`sourceSystemID`,`resellerid`) VALUES (NOW(),NOW(),'u',?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $query->execute(array($active, $tmpName, $vname, $name, $email, $salt, $phone, $handy, $fax, $city, $cityn, $street, $streetn, $salutation, $birthday, $country, $fdlpath, $mail_backup, $mail_gsupdate, $mail_securitybreach, $mail_serverdown, $mail_ticket, $mail_vserver, $externalID,json_encode(array('A' => $ui->ip4('REMOTE_ADDR', 'server'))), $resellerID));

            $localID = $sql->lastInsertId();

            if (isid($localID, 10)) {
                $insert = true;
            }

            if (isset($prefix)) {
                $username = $prefix . $localID;
            }

        } else if (!isset($success['false'])) {
            $success['false'][] = 'No usergroup available';
        }

        if (!isset($success) and isset($insert) and $insert == true) {

            $password = (!isset($data['password']) or in_array($data['password'], $bad)) ? passwordgenerate(10) : $data['password'];

            $newHash = passwordCreate($name, $password);

            if (is_array($newHash)) {

                $query = $sql->prepare("UPDATE `userdata` SET `cname`=?,`security`=?,`salt`=? WHERE `id`=? LIMIT 1");
                $query->execute(array($username, $newHash['hash'], $newHash['salt'], $localID));

            } else {

                $query = $sql->prepare("UPDATE `userdata` SET `cname`=?,`security`=? WHERE `id`=? LIMIT 1");
                $query->execute(array($username, $newHash, $localID));

            }


            $query = $sql->prepare("INSERT INTO `userdata_groups` (`userID`,`groupID`,`resellerID`) VALUES (?,?,?)");
            foreach ($userGroupIDs as $groupID) {
				$query->execute(array($localID, $groupID, $resellerID));
			}

        } else if (!isset($success)) {
            $success['false'][] = 'Could not write user to database';
        }

    } else if (!isset($success['false'])) {
        $success['false'][] = 'Can not identify user or bad email';
    }

} else if (array_value_exists('action', 'clean', $data)) {

    $externalID = (isset($data['external_id']) and isExternalID($data['external_id']) != '') ? $data['external_id'] : false;

    if ($externalID != false) {
        $query = $sql->prepare("UPDATE `userdata` SET `externalID`='' WHERE `externalID`=? and `resellerid`=?");
        $query->execute(array($externalID, $resellerID));
    }

} else if (array_value_exists('action', 'mod', $data)) {

    $from = array('localid' => 'id','username' => 'cname','external_id' => 'externalID','email' => 'mail');
    $identifyBy = $data['identify_by'];
    $username = (isset($data['username'])) ? $data['username'] : '';
    $externalID = (isset($data['external_id']) and isExternalID($data['external_id']) != '') ? $data['external_id'] : '';
    $active = (isset($data['active'])) ? active_check($data['active']) : 'Y';
    $localID = (isset($data['localid'])) ? $data['localid'] : '';

    if (dataExist('identify_by', $data)) {

        $query = $sql->prepare("SELECT `id`,`cname`,`active` FROM `userdata` WHERE `".$from[$data['identify_by']]."`=? AND `resellerid`=?");
        $query->execute(array($data[$data['identify_by']], $resellerID));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $localID = $row['id'];
            $name = $row['cname'];
            $oldactive = $row['active'];
        }

        if (isid($localID, 10)) {

            $what = array();
            $foundGroupIDs = array();
            $userGroupIDs = array();

            if (isset($data['password']) and !in_array($data['password'], $bad)) {

                $password = $data['password'];
                $newHash = passwordCreate($name, $data['password']);

                if (is_array($newHash)) {

                    $what['security'] = $newHash['hash'];
                    $what['salt'] = $newHash['salt'];

                } else {
                    $what['security'] = $newHash;
                }
            }

            if (isset($data['email']) and ismail($data['email'])) {
                $what['mail'] = $data['email'];
                $mail = $what['mail'];
            }

            if (isset($data['name']) and names($data['name'], 255)) {
                $what['name'] = names($data['name'], 255);
                $name = $what['name'];
            }

            if (isset($data['vname']) and names($data['vname'], 255)) {
                $what['vname'] = names($data['vname'], 255);
                $vname = $what['vname'];
            }

            if (isset($data['phone']) and phone($data['phone'])) {
                $what['phone'] = phone($data['phone']);
                $phone = $what['phone'];
            }

            if (isset($data['handy']) and phone($data['handy'])) {
                $what['handy'] = phone($data['handy']);
                $handy = $what['handy'];
            }

            if (isset($data['fax']) and phone($data['fax'])) {
                $what['fax'] = phone($data['fax']);
                $fax = $what['fax'];
            }

            if (isset($data['city']) and names($data['city'], 50)) {
                $what['city'] = names($data['city'], 50);
                $city = $what['city'];
            }

            if (isset($data['cityn']) and is_number($data['cityn'], 6)) {
                $what['cityn'] = is_number($data['cityn'], 6);
                $cityn = $what['cityn'];
            }

            if (isset($data['street']) and names($data['street'], 50)) {
                $what['street'] = names($data['street'], 50);
                $street = $what['street'];
            }

            if (isset($data['streetn']) and wpreg_check($data['streetn'], 6)) {
                $what['streetn'] = wpreg_check($data['streetn'], 6);
                $streetn = $what['streetn'];
            }

            if (isset($data['salutation']) and is_number($data['salutation'], 1)) {
                $what['salutation'] = is_number($data['salutation'], 1);
                $salutation = $what['salutation'];
            }

            if (isset($data['birthday']) and isDate($data['birthday'])) {
                $what['birthday'] = date('Y-m-d',strtotime(isDate($data['birthday'])));
                $birthday = $what['birthday'];
            }

            if (isset($data['country']) and wpreg_check($data['country'], 2)) {
                $what['country'] = wpreg_check(strtolower($data['country']), 2);
                $country = $what['country'];
            }

            if (isset($data['mail_backup']) and active_check($data['mail_backup'])) {
                $what['mail_backup'] = $data['mail_backup'];
                $mail_backup = $what['mail_backup'];
            }

            if (isset($data['mail_gsupdate']) and active_check($data['mail_gsupdate'])) {
                $what['mail_gsupdate'] = $data['mail_gsupdate'];
                $mail_gsupdate = $what['mail_gsupdate'];
            }

            if (isset($data['mail_securitybreach']) and active_check($data['mail_securitybreach'])) {
                $what['mail_securitybreach'] = $data['mail_securitybreach'];
                $mail_securitybreach = $what['mail_securitybreach'];
            }

            if (isset($data['mail_serverdown']) and active_check($data['mail_serverdown'])) {
                $what['mail_serverdown'] = $data['mail_serverdown'];
                $mail_serverdown = $what['mail_serverdown'];
            }

            if (isset($data['mail_ticket']) and active_check($data['mail_ticket'])) {
                $what['mail_ticket'] = $data['mail_ticket'];
                $mail_ticket = $what['mail_ticket'];
            }

            if (isset($data['mail_vserver']) and active_check($data['mail_vserver'])) {
                $what['mail_vserver'] = $data['mail_vserver'];
                $mail_vserver = $what['mail_vserver'];
            }

            if (isset($data['fdlpath']) and isurl($data['fdlpath'])) {
                $what['fdlpath'] = $data['fdlpath'];
                $fdlpath = $what['fdlpath'];
            }

            if (isset($data['external_id']) and isExternalID($data['external_id']) != '') {
                $what['externalID'] = $data['external_id'];
            }

            $query = $sql->prepare("SELECT `groupID` FROM `userdata_groups` WHERE `userID`=? AND `resellerID`=?");
            $query->execute(array($localID, $resellerID));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $foundGroupIDs[] = $row['groupID'];
            }

            $query = $sql->prepare("SELECT `id` FROM `usergroups` WHERE `active`='Y' AND `id`=? AND `resellerid`=? LIMIT 1");

            if (isset($data['groupID']) and isid($data['groupID'], 19)) {

                $query->execute(array($data['groupID'], $resellerID));

                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $userGroupIDs[] = $row['id'];
                }

            } else if (isset($data['groupID'])) {

                if (!is_array($data['groupID']) and !is_object($data['groupID'])) {
                    $data['groupID'] = array($data['groupID']);
                }

                foreach ($data['groupID'] as $groupID) {
                    if (isid($groupID, 19)) {
                        $query->execute(array($groupID, $resellerID));
                        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                            $userGroupIDs[] = $row['id'];
                        }
                    }
                }

            }

            if (count($userGroupIDs) > 0) {
                $query = $sql->prepare("DELETE FROM `userdata_groups` WHERE `userID`=? AND `groupID`=? AND `resellerID`=? LIMIT 1");
                foreach ($foundGroupIDs as $groupID) {
                    if (!in_array($groupID, $userGroupIDs)) {
                        $query->execute(array($localID, $groupID, $resellerID));
                    }
                }
            }

            $extraUpdate = '';

            foreach($what as $key => $value) {
                $extraUpdate .= ",`" . $key . "`='" . $value . "'";
            }

            $query = $sql->prepare("UPDATE `userdata` SET `updateTime`=NOW() " . $extraUpdate. " WHERE `id`=? AND `resellerid`=?");
            $query->execute(array($localID, $resellerID));

            $query = $sql->prepare("UPDATE `jobs` SET `status`='2' WHERE `type`='us' AND (`status` IS NULL OR `status`='1') AND `userID`=? and `resellerID`=?");
            $query->execute(array($localID, $resellerID));

            if (!in_array($active, $bad) and $active != $oldactive) {

                $query = $sql->prepare("UPDATE `userdata` SET `jobPending`='Y' WHERE `id`=? and `resellerid`=?");
                $query->execute(array($localID, $resellerID));

                $query = $sql->prepare("INSERT INTO `jobs` (`api`,`type`,`invoicedByID`,`affectedID`,`userID`,`name`,`status`,`date`,`action`,`extraData`,`resellerid`) VALUES ('A','us',?,?,?,?,NULL,NOW(),'md',?,?)");
                $query->execute(array($resellerID, $localID, $localID, $name, json_encode(array('newActive' => $active)), $resellerID));

                updateJobs($localID, $resellerID);
            }

        } else {
            $success['false'][] = 'No user can be found to edit';
        }

    } else {
        $success['false'][] = 'No data for this method';
    }

} else if (array_value_exists('action', 'del', $data)) {

    $from = array('localid' => 'id', 'username' => 'cname', 'external_id' => 'externalID', 'email' => 'mail');

    $email = $data['email'];
    $identifyBy = $data['identify_by'];
    $username = (isset($data['username'])) ? $data['username'] : '';
    $externalID = (isset($data['external_id']) and isExternalID($data['external_id']) != '') ? $data['external_id'] : '';
    $active = (isset($data['active'])) ? active_check($data['active']) : '';
    $localID = (isset($data['localid'])) ? $data['localid'] : '';

    if (dataExist('identify_by', $data)) {

        $query = $sql->prepare("SELECT `id`,`cname` FROM `userdata` WHERE `" . $from[$data['identify_by']] . "`=? AND `resellerid`=?");
        $query->execute(array($data[$data['identify_by']], $resellerID));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $localID = $row['id'];
            $name = $row['cname'];
        }

        if (isset($localID) and isset($name)) {

            $query = $sql->prepare("UPDATE `userdata` SET `jobPending`='Y' WHERE `id`=? and `resellerid`=?");
            $query->execute(array($localID, $resellerID));

            $query = $sql->prepare("UPDATE `jobs` SET `status`='2' WHERE (`status` IS NULL OR `status`='1') AND `userID`=? and `resellerID`=?");
            $query->execute(array($localID, $resellerID));

            $query = $sql->prepare("INSERT INTO `jobs` (`api`,`type`,`invoicedByID`,`affectedID`,`userID`,`name`,`status`,`date`,`action`,`resellerid`) VALUES ('A','us',?,?,?,?,NULL,NOW(),'dl',?)");
            $query->execute(array($resellerID, $localID, $localID, $name, $resellerID));

            updateJobs($localID, $resellerID);

        } else {
            $success['false'][] = 'No user can be found to delete';
        }
    } else {
        $success['false'][] = 'No data for this method';
    }

} else if (array_value_exists('action', 'ls', $data) and isset($data['identify_by']) and isset($data[$data['identify_by']]) and !in_array($data[$data['identify_by']], $bad)) {

    $userArray = array('userdetails' => array(),'gserver' => array(), 'voice' => array(), 'mysql' => array(), 'tsdns' => array(), 'webspace' => array());
    $from = array('localid' => 'id', 'username' => 'cname', 'external_id' => 'externalID', 'email' => 'mail', 'mysql' => array());

    $email = (isset($data['email'])) ? $data['email'] : '';
    $identifyBy = $data['identify_by'];
    $username = (isset($data['username'])) ? $data['username'] : '';
    $externalID = (isset($data['external_id']) and isExternalID($data['external_id']) != '') ? $data['external_id'] : '';
    $localID = (isset($data['localid'])) ? $data['localid'] : '';
    $showUserDataOnly = (isset($data['show_user_data_only']) and $data['show_user_data_only'] == "1") ? 1 : 0;

    if (dataExist('identify_by', $data)) {

        $query = $sql->prepare("SELECT `id`,`active`,`cname`,`name`,`vname`,`mail`,`phone`,`handy`,`city`,`cityn`,`street`,`streetn`,`externalID`,`jobPending` FROM `userdata` WHERE `" . $from[$data['identify_by']] . "`=? AND `resellerid`=? AND `accounttype`='u' LIMIT 1");
        $query->execute(array($data[$data['identify_by']], $resellerID));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $userArray['userdetails'] = $row;
            $userArray['userdetails']['show_user_data_only'] = $showUserDataOnly;
        }

        if ($query->rowCount() > 0) {

            $list = true;

            if ($showUserDataOnly == 0) {

                $tempArray = array();

                $query = $sql->prepare("SELECT `id`,`active`,`queryUpdatetime`,`queryPassword`,`queryMap`,`queryMaxplayers`,`queryNumplayers`,`queryName`,`port5`,`serverid`,`pallowed`,`eacallowed`,`protected`,`brandname`,`tvenable`,`war`,`psince`,`serverip`,`port`,`port2`,`port3`,`port4`,`minram`,`maxram`,`slots`,`taskset`,`cores`,`lendserver`,`externalID`,`jobPending` FROM `gsswitch` WHERE `userid`=? AND `resellerid`=? ORDER BY `serverip`,`port`");
                $query2 = $sql->prepare("SELECT t.`shorten` FROM `serverlist` s INNER JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE s.`switchID`=? AND s.`resellerid`=?");
                $query->execute(array($userArray['userdetails']['id'], $resellerID));
                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

                    $shorten = array();

                    $query2->execute(array($row['id'], $resellerID));
                    while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {
                        $shorten[] = $row2['shorten'];
                    }

                    $row['shorten'] = implode(',', $shorten);
                    $tempArray[] = $row;
                }

                $userArray['gserver'] = $tempArray;

                $tempArray = array();

                $query = $sql->prepare("SELECT `id`,`active`,`autoRestart`,`backup`,`lendserver`,`ip`,`port`,`slots`,`password`,`forcebanner`,`forcebutton`,`forceservertag`,`forcewelcome`,`flexSlots`,`max_download_total_bandwidth`,`max_upload_total_bandwidth`,`localserverid`,`dns`,`usedslots`,`uptime`,`maxtraffic`,`maxtraffic`,`filetraffic`,`queryName`,`queryNumplayers`,`queryMaxplayers`,`queryPassword`,`queryUpdatetime`,`externalID`  FROM `voice_server` WHERE `userid`=? AND `resellerid`=?");
                $query->execute(array($userArray['userdetails']['id'], $resellerID));
                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $tempArray[] = $row;
                }

                $userArray['voice'] = $tempArray;

                $tempArray = array();

                $query = $sql->prepare("SELECT `active`,`id`,`dbname`,`ips`,`max_databases`,`max_queries_per_hour`,`max_updates_per_hour`,`max_connections_per_hour`,`max_userconnections_per_hour`,`externalID`,`jobPending` FROM `mysql_external_dbs` WHERE `uid`=? AND `resellerid`=?");
                $query->execute(array($userArray['userdetails']['id'], $resellerID));
                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $tempArray[] = $row;
                }

                $userArray['mysql'] = $tempArray;

                $tempArray = array();

                $query = $sql->prepare("SELECT `dnsID`,`active`,`dns`,`ip`,`port`,`externalID` FROM `voice_dns` WHERE `userID`=? AND `resellerID`=?");
                $query->execute(array($userArray['userdetails']['id'], $resellerID));
                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $tempArray[] = $row;
                }

                $userArray['tsdns'] = $tempArray;

                $tempArray = array();

                $query = $sql->prepare("SELECT `webVhostID`,`active`,`hdd`,`hddUsage`,`dns`,`externalID` FROM `webVhost` WHERE `userID`=? AND `resellerID`=?");
                $query->execute(array($userArray['userdetails']['id'], $resellerID));
                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $tempArray[] = $row;
                }

                $userArray['webspace'] = $tempArray;
            }

            if ($apiType == 'xml') {

                header("Content-type: text/xml; charset=UTF-8");
                echo array2xml($userArray, new SimpleXMLElement('<user/>'));

            } else if ($apiType == 'json') {
                header("Content-type: application/json; charset=UTF-8");
                echo json_encode($userArray);
            }

        } else {
            $success['false'][] = 'Can not find a user with this data!';
        }
    } else {
        $success['false'][] = 'No data for this method';
    }

} else if (array_value_exists('action', 'ls', $data) and !isset($data['identify_by'])) {

    $limit = (isset($data['start']) and is_numeric($data['start']) and isset($data['amount']) and is_numeric($data['amount'])) ? 'LIMIT ' . $data['start'] . ',' . $data['amount'] : '';

    $columns = array('`id`', '`active`', '`salutation`', '`cname`', '`name`', '`vname`', '`mail`', '`city`', '`cityn`', '`street`', '`streetn`', '`language`', '`phone`', '`externalID`');

    if (isset($data['notLike']) and wpreg_check($data['notLike'], 255)) {

        $query = $sql->prepare("SELECT " . implode(',', $columns) . " FROM `userdata` WHERE `resellerid`=? AND `accounttype`='u' AND (`externalID` IS NULL OR `externalID` NOT LIKE ?) " . $limit);
        $query->execute(array($resellerID, $data['notLike'] . '%'));

    } else if (isset($data['like']) and wpreg_check($data['like'], 255)) {

        $query = $sql->prepare("SELECT " . implode(',', $columns) . " FROM `userdata` WHERE `resellerid`=? AND `accounttype`='u' AND `externalID` LIKE ? " . $limit);
        $query->execute(array($resellerID, $data['like'] . '%'));

    } else  {
        $query = $sql->prepare("SELECT " . implode(',', $columns) . " FROM `userdata` WHERE `resellerid`=? AND `accounttype`='u' ". $limit);
        $query->execute(array($resellerID));
    }


    if ($apiType == 'xml') {

        header("Content-type: text/xml; charset=UTF-8");

        $responsexml = new DOMDocument('1.0','utf-8');
        $element = $responsexml->createElement('users');

        $query2 = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `userdata` WHERE `resellerid`=? AND `accounttype`='u'");
        $query2->execute(array($resellerID));

        $key = $responsexml->createElement('totalAmount', (int) $query2->fetchColumn());
        $element->appendChild($key);

        $key = $responsexml->createElement('start', (isset($data['start'])) ? (int) $data['start'] : '');
        $element->appendChild($key);

        $key = $responsexml->createElement('amount', (isset($data['amount'])) ? (int) $data['amount'] : '');
        $element->appendChild($key);

        $key = $responsexml->createElement('like', (isset($data['like']) and wpreg_check($data['like'], 255)) ? $data['like'] : '');
        $element->appendChild($key);

        $key = $responsexml->createElement('notLike', (isset($data['notLike']) and wpreg_check($data['notLike'], 255)) ? $data['notLike'] : '');
        $element->appendChild($key);

        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            $listRootServerXML = $responsexml->createElement('user');

            $listServerXML = $responsexml->createElement('id', $row['id']);
            $listRootServerXML->appendChild($listServerXML);

            $listServerXML = $responsexml->createElement('active', $row['active']);
            $listRootServerXML->appendChild($listServerXML);

            $listServerXML = $responsexml->createElement('salutation', $row['salutation']);
            $listRootServerXML->appendChild($listServerXML);

            $listServerXML = $responsexml->createElement('cname', $row['cname']);
            $listRootServerXML->appendChild($listServerXML);

            $listServerXML = $responsexml->createElement('vname', $row['vname']);
            $listRootServerXML->appendChild($listServerXML);

            $listServerXML = $responsexml->createElement('name', $row['name']);
            $listRootServerXML->appendChild($listServerXML);

            $listServerXML = $responsexml->createElement('mail', $row['mail']);
            $listRootServerXML->appendChild($listServerXML);

            $listServerXML = $responsexml->createElement('city', $row['city']);
            $listRootServerXML->appendChild($listServerXML);

            $listServerXML = $responsexml->createElement('cityn', $row['cityn']);
            $listRootServerXML->appendChild($listServerXML);

            $listServerXML = $responsexml->createElement('street', $row['street']);
            $listRootServerXML->appendChild($listServerXML);

            $listServerXML = $responsexml->createElement('streetn', $row['streetn']);
            $listRootServerXML->appendChild($listServerXML);

            $listServerXML = $responsexml->createElement('language', $row['language']);
            $listRootServerXML->appendChild($listServerXML);

            $listServerXML = $responsexml->createElement('phone', $row['phone']);
            $listRootServerXML->appendChild($listServerXML);

            $listServerXML = $responsexml->createElement('externalID', $row['externalID']);
            $listRootServerXML->appendChild($listServerXML);

            $element->appendChild($listRootServerXML);
        }

        $responsexml->appendChild($element);

        $responsexml->formatOutput = true;

        die($responsexml->saveXML());

    } else if ($apiType == 'json') {
        header("Content-type: application/json; charset=UTF-8");
        echo json_encode($query->fetchAll(PDO::FETCH_ASSOC));
    }

} else {
    $success['false'][] = 'No action defined';
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
    $element = $responsexml->createElement('voice');

    $server = $responsexml->createElement('action', $action);
    $element->appendChild($server);

    $key = $responsexml->createElement('actionSend', (isset($data['action']) ? $data['action'] : ''));
    $element->appendChild($key);

    $server = $responsexml->createElement('username', $username);
    $element->appendChild($server);

    $server = $responsexml->createElement('external_id', $externalID);
    $element->appendChild($server);

    $server = $responsexml->createElement('email', $email);
    $element->appendChild($server);

    $server = $responsexml->createElement('errors', $errors);
    $element->appendChild($server);

    $server = $responsexml->createElement('password', $password);
    $element->appendChild($server);

    $server = $responsexml->createElement('active', $active);
    $element->appendChild($server);

    $server = $responsexml->createElement('localid', $localID);
    $element->appendChild($server);

    $server = $responsexml->createElement('vname', $vname);
    $element->appendChild($server);

    $server = $responsexml->createElement('name', $name);
    $element->appendChild($server);

    $server = $responsexml->createElement('phone', $phone);
    $element->appendChild($server);

    $server = $responsexml->createElement('handy', $handy);
    $element->appendChild($server);

    $server = $responsexml->createElement('fax', $fax);
    $element->appendChild($server);

    $server = $responsexml->createElement('city', $city);
    $element->appendChild($server);

    $server = $responsexml->createElement('cityn', $cityn);
    $element->appendChild($server);

    $server = $responsexml->createElement('street', $street);
    $element->appendChild($server);

    $server = $responsexml->createElement('streetn', $streetn);
    $element->appendChild($server);

    $server = $responsexml->createElement('salutation', $salutation);
    $element->appendChild($server);

    $server = $responsexml->createElement('birthday', $birthday);
    $element->appendChild($server);

    $server = $responsexml->createElement('country', $country);
    $element->appendChild($server);

    $server = $responsexml->createElement('fdlpath', $fdlpath);
    $element->appendChild($server);

    $server = $responsexml->createElement('mail_backup', $mail_backup);
    $element->appendChild($server);

    $server = $responsexml->createElement('mail_gsupdate', $mail_gsupdate);
    $element->appendChild($server);

    $server = $responsexml->createElement('mail_securitybreach', $mail_securitybreach);
    $element->appendChild($server);

    $server = $responsexml->createElement('mail_serverdown', $mail_serverdown);
    $element->appendChild($server);

    $server = $responsexml->createElement('mail_ticket', $mail_ticket);
    $element->appendChild($server);

    $server = $responsexml->createElement('mail_vserver', $mail_vserver);
    $element->appendChild($server);

    $responsexml->appendChild($element);

    $responsexml->formatOutput = true;

    echo $responsexml->saveXML();

} else if ($apiType == 'json' and !isset($list)) {

    header("Content-type: application/json; charset=UTF-8");

    echo json_encode(array('action' => $action, 'username' => $username, 'external_id' => $externalID, 'email' => $email, 'errors' => $errors, 'password' => $password, 'active' => $active, 'localid' => $localID));

} else if (!isset($list)) {

    header('HTTP/1.1 403 Forbidden');
    die('403 Forbidden');

}