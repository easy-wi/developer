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
 
include(EASYWIDIR . '/stuff/keyphrasefile.php');
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
if (array_value_exists('action', 'add', $data)) {
    if (ismail($data['email'])) {
        $email = $data['email'];
        $salt=md5(mt_rand().date('Y-m-d H:i:s:u'));
        $identifyBy = $data['identify_by'];
        $username = $data['username'];
        $externalID = $data['external_id'];
        $active=active_check($data['active']);
        $password = $data['password'];
        $localID = '';
        $userGroupIDs = array();
        $query = $sql->prepare("SELECT COUNT(`id`) AS `amount`,`mail`,`cname` FROM `userdata` WHERE `mail`=? OR `cname`=? LIMIT 1");
        $query->execute(array($email,$username));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $amount = $row['amount'];
            if ($row['amount']>0) {
                $username = $row['cname'];
                $email = $row['mail'];
                $success['false'][] = 'user with this e-mail already exists: '.$username;
            }
        }
        if (!isset($success['false']) and !in_array($externalID,$bad)) {
            $query = $sql->prepare("SELECT COUNT(`id`) AS `amount`,`mail`,`cname` FROM `userdata` WHERE `externalID`=? LIMIT 1");
            $query->execute(array($externalID));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $amount = $amount+$row['amount'];
                if ($row['amount']>0) {
                    $username = $row['cname'];
                    $email = $row['mail'];
                    $success['false'][] = 'user with external ID exists: '.$username;
                }
            }
        }
        if (!in_array($username,$bad)) {
            $tmpName = $username;
        } else {
            $query = $sql->prepare("SELECT `prefix2` FROM `settings` WHERE `resellerid`=? LIMIT 1");
            $query->execute(array($resellerID));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $prefix = $row['prefix2'];
                $tmpName = $row['prefix2'].$salt;
            }
        }
        $query = $sql->prepare("SELECT `id` FROM `usergroups` WHERE `active`='Y' AND `id`=? AND `resellerid`=? LIMIT 1");
        if (isset($data['groupID']) and isid($data['groupID'],19)) {
            $query->execute(array($data['groupID'],$resellerID));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) $userGroupIDs[] = $row['id'];
        } else if (isset($data['groupID']) and (is_array($data['groupID'])) or is_object($data['groupID'])) {
            foreach ($data['groupID'] as $groupID) {
                if (isid($groupID,19)) {
                    $query->execute(array($groupID,$resellerID));
                    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) $userGroupIDs[] = $row['id'];
                }
            }
        }
        if (count($userGroupIDs)==0 and (!isset($amount) or $amount==0)) {
            $query = $sql->prepare("SELECT `id` FROM `usergroups` WHERE `grouptype`='u' AND `active`='Y' AND `defaultgroup`='Y' AND `resellerid`=? LIMIT 1");
            $query->execute(array($resellerID));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $userGroupIDs[] = $row['id'];
            }
            if (count($userGroupIDs)==0) {
                $query = $sql->prepare("SELECT `id` FROM `usergroups` WHERE `grouptype`='u' AND `active`='Y' AND `resellerid`=? LIMIT 1");
                $query->execute(array($resellerID));
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $userGroupIDs[] = $row['id'];
                }
            }
        }
        if (isset($data['name']) and names($data['name'],255)) $name=names($data['name'],255);
        if (isset($data['vname']) and names($data['vname'],255)) $vname=names($data['vname'],255);
        if (isset($data['phone']) and phone($data['phone'])) $phone=phone($data['phone']);
        if (isset($data['handy']) and phone($data['handy'])) $handy=phone($data['handy']);
        if (isset($data['fax']) and phone($data['fax'])) $fax=phone($data['fax']);
        if (isset($data['city']) and names($data['city'],50)) $city=names($data['city'],50);
        if (isset($data['cityn']) and is_number($data['cityn'],6)) $cityn=is_number($data['cityn'],6);
        if (isset($data['street']) and names($data['street'],50)) $street=names($data['street'],50);
        if (isset($data['streetn']) and wpreg_check($data['streetn'],6)) $streetn=wpreg_check($data['streetn'],6);
        if (isset($data['salutation']) and is_number($data['salutation'],1)) $salutation=is_number($data['salutation'],1);
        if (isset($data['birthday']) and isDate($data['birthday'])) $birthday=date('Y-m-d',strtotime(isDate($data['birthday'])));
        if (isset($data['country']) and st(strtolower($data['country']))) $country=st(strtolower($data['country']));
        if (isset($data['fdlpath']) and isurl($data['fdlpath'])) $fdlpath = $data['fdlpath'];
        $mail_backup=(isset($data['mail_backup']) and active_check($data['mail_backup'])) ? $data['mail_backup'] : 'Y';
        $mail_gsupdate=(isset($data['mail_gsupdate']) and active_check($data['mail_gsupdate'])) ? $data['mail_gsupdate'] : 'Y';
        $mail_securitybreach=(isset($data['mail_securitybreach']) and active_check($data['mail_securitybreach'])) ? $data['mail_securitybreach'] : 'Y';
        $mail_serverdown=(isset($data['mail_serverdown']) and active_check($data['mail_serverdown'])) ? $data['mail_serverdown'] : 'Y';
        $mail_ticket=(isset($data['mail_ticket']) and active_check($data['mail_ticket'])) ? $data['mail_ticket'] : 'Y';
        $mail_vserver=(isset($data['mail_vserver']) and active_check($data['mail_vserver'])) ? $data['mail_vserver'] : 'Y';
        if (!isset($success['false']) and count($userGroupIDs)>0) {
            $query = $sql->prepare("INSERT INTO `userdata` (`creationTime`,`updateTime`,`accounttype`,`active`,`cname`,`vname`,`name`,`mail`,`salt`,`phone`,`handy`,`fax`,`city`,`cityn`,`street`,`streetn`,`salutation`,`birthday`,`country`,`fdlpath`,`mail_backup`,`mail_gsupdate`,`mail_securitybreach`,`mail_serverdown`,`mail_ticket`,`mail_vserver`,`externalID`,`sourceSystemID`,`resellerid`) VALUES (NOW(),NOW(),'u',?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $query->execute(array($active,$tmpName,$vname,$name,$email,$salt,$phone,$handy,$fax,$city,$cityn,$street,$streetn,$salutation,$birthday,$country,$fdlpath,$mail_backup,$mail_gsupdate,$mail_securitybreach,$mail_serverdown,$mail_ticket,$mail_vserver,$externalID,json_encode(array('A' => $apiIP)),$resellerID));
            $insert = true;
        } else if (!isset($success['false'])) {
            $success['false'][] = 'No usergroup available';
        }
        $query = $sql->prepare("SELECT `id` FROM `userdata` WHERE `cname`=? AND `resellerid`=? ORDER BY `id` DESC LIMIT 1");
        $query->execute(array($tmpName,$resellerID));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $localID = $row['id'];
            if (isset($prefix)) {
                $username = $prefix.$localID;
            }
        }
        if (!isset($success) and isset($localID) and isset($insert) and $insert == true) {

            $password = (!isset($data['password']) or in_array($data['password'],$bad)) ? passwordgenerate(10) : $data['password'];

            $newHash = passwordCreate($name, $data['password']);

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

} else if (array_value_exists('action','mod',$data)) {

    $identifyBy = $data['identify_by'];
    $username = $data['username'];
    $externalID = $data['external_id'];
    $active = active_check($data['active']);
    $localID = $data['localid'];
    $from = array('localid' => 'id','username' => 'cname','external_id' => 'externalID','email' => 'mail');

    if (dataExist('identify_by',$data)) {

        $query = $sql->prepare("SELECT `id`,`cname`,`active` FROM `userdata` WHERE `".$from[$data['identify_by']]."`=? AND `resellerid`=?");
        $query->execute(array($data[$data['identify_by']], $resellerID));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $localID = $row['id'];
            $name = $row['cname'];
            $oldactive = $row['active'];
        }

        if (isset($localID)) {

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

            if (isset($data['name']) and names($data['name'],255)) {
                $what['name']=names($data['name'],255);
                $name = $what['name'];
            }

            if (isset($data['vname']) and names($data['vname'],255)) {
                $what['vname']=names($data['vname'],255);
                $vname = $what['vname'];
            }

            if (isset($data['phone']) and phone($data['phone'])) {
                $what['phone']=phone($data['phone']);
                $phone = $what['phone'];
            }

            if (isset($data['handy']) and phone($data['handy'])) {
                $what['handy']=phone($data['handy']);
                $handy = $what['handy'];
            }

            if (isset($data['fax']) and phone($data['fax'])) {
                $what['fax']=phone($data['fax']);
                $fax = $what['fax'];
            }

            if (isset($data['city']) and names($data['city'],50)) {
                $what['city']=names($data['city'],50);
                $city = $what['city'];
            }

            if (isset($data['cityn']) and is_number($data['cityn'],6)) {
                $what['cityn']=is_number($data['cityn'],6);
                $cityn = $what['cityn'];
            }

            if (isset($data['street']) and names($data['street'],50)) {
                $what['street']=names($data['street'],50);
                $street = $what['street'];
            }

            if (isset($data['streetn']) and wpreg_check($data['streetn'],6)) {
                $what['streetn']=wpreg_check($data['streetn'],6);
                $streetn = $what['streetn'];
            }

            if (isset($data['salutation']) and is_number($data['salutation'],1)) {
                $what['salutation']=is_number($data['salutation'],1);
                $salutation = $what['salutation'];
            }

            if (isset($data['birthday']) and isDate($data['birthday'])) {
                $what['birthday']=date('Y-m-d',strtotime(isDate($data['birthday'])));
                $birthday = $what['birthday'];
            }

            if (isset($data['country']) and wpreg_check($data['country'], 2)) {
                $what['country'] = wpreg_check(strtolower($data['country']),2);
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

            $query = $sql->prepare("SELECT `groupID` FROM `userdata_groups` WHERE `userID`=? AND `resellerID`=?");
            $query->execute(array($localID,$resellerID));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $foundGroupIDs[] = $row['groupID'];
            }

            $query = $sql->prepare("SELECT `id` FROM `usergroups` WHERE `active`='Y' AND `id`=? AND `resellerid`=? LIMIT 1");

            if (isset($data['groupID']) and isid($data['groupID'],19)) {

                $query->execute(array($data['groupID'],$resellerID));

                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $userGroupIDs[] = $row['id'];
                }

            } else if (isset($data['groupID']) and (is_array($data['groupID'])) or is_object($data['groupID'])) {

                foreach ($data['groupID'] as $groupID) {
                    if (isid($groupID,19)) {
                        $query->execute(array($groupID,$resellerID));
                        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                            $userGroupIDs[] = $row['id'];
                        }
                    }
                }

            }

            $query = $sql->prepare("DELETE FROM `userdata_groups` WHERE `userID`=? AND `groupID`=? AND `resellerID`=? LIMIT 1");
            foreach ($foundGroupIDs as $groupID) {
                if (!in_array($groupID,$userGroupIDs)) {
                    $query->execute(array($localID,$groupID,$resellerID));
                }
            }

            $extraUpdate = '';
            foreach($what as $key=>$value) {
                $extraUpdate .=",`".$key."`='".$value."'";
            }

            $query = $sql->prepare("UPDATE `userdata` SET `updateTime`=NOW() $extraUpdate WHERE `id`=? AND `resellerid`=?");
            $query->execute(array($localID, $resellerID));

            if (!in_array($active,$bad) and $active != $oldactive) {

                $query = $sql->prepare("UPDATE `jobs` SET `status`='2' WHERE `type`='us' AND (`status` IS NULL OR `status`='1') AND `userID`=? and `resellerID`=?");
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

} else if (array_value_exists('action','del', $data)) {

    $email = $data['email'];
    $identifyBy = $data['identify_by'];
    $username = $data['username'];
    $password = $data['password'];
    $externalID = $data['external_id'];
    $active = active_check($data['active']);
    $localID = $data['localid'];
    $from = array('localid' => 'id','username' => 'cname','external_id' => 'externalID','email' => 'mail');

    if (dataExist('identify_by',$data)) {

        $query = $sql->prepare("SELECT `id`,`cname` FROM `userdata` WHERE `".$from[$data['identify_by']]."`=? AND `resellerid`=?");
        $query->execute(array($data[$data['identify_by']],$resellerID));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $localID = $row['id'];
            $name = $row['cname'];
        }

        if (isset($localID) and isset($name)) {
            $query = $sql->prepare("UPDATE `jobs` SET `status`='2' WHERE (`status` IS NULL OR `status`='1') AND `userID`=? and `resellerID`=?");
            $query->execute(array($localID, $resellerID));

            $query = $sql->prepare("INSERT INTO `jobs` (`api`,`type`,`invoicedByID`,`affectedID`,`userID`,`name`,`status`,`date`,`action`,`resellerid`) VALUES ('A','us',?,?,?,?,NULL,NOW(),'dl',?)");
            $query->execute(array($resellerID, $localID, $localID, $name, $resellerID));

            updateJobs($localID, $resellerID);

            #$query = $sql->prepare("DELETE FROM `userdata` WHERE `".$from[$data['identify_by']]."`=? AND `resellerid`=?");
            #$query->execute(array($data[$data['identify_by']],$resellerID));

        } else {
            $success['false'][] = 'No user can be found to delete';
        }
    } else {
        $success['false'][] = 'No data for this method';
    }

} else if (array_value_exists('action', 'ls', $data) and isset($data['identify_by']) and isset($data[$data['identify_by']]) and !in_array($data[$data['identify_by']],$bad)) {

    $userArray = array('userdetails' => array(),'gserver' => array(),'voice' => array());
    $email = $data['email'];
    $identifyBy = $data['identify_by'];
    $username = $data['username'];
    $password = $data['password'];
    $externalID = $data['external_id'];
    $active = active_check($data['active']);
    $localID = $data['localid'];
    $from = array('localid' => 'id', 'username' => 'cname', 'external_id' => 'externalID', 'email' => 'mail', 'mysql' => array());

    if (dataExist('identify_by', $data)) {
        $query = $sql->prepare("SELECT `id`,`active`,`cname`,`name`,`vname`,`mail`,`phone`,`handy`,`city`,`cityn`,`street`,`streetn`,`externalID`,`jobPending` FROM `userdata` WHERE `".$from[$data['identify_by']]."`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($data[$data['identify_by']],$resellerID));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $userArray['userdetails'] = $row;
        }
        if ($query->rowCount() > 0) {
            $list = true;
            $tempArray = array();
            $query = $sql->prepare("SELECT `id`,`active`,`queryUpdatetime`,`queryPassword`,`queryMap`,`queryMaxplayers`,`queryNumplayers`,`queryName`,`port5`,`serverid`,`pallowed`,`eacallowed`,`protected`,`brandname`,`tvenable`,`war`,`psince`,`serverip`,`port`,`port2`,`port3`,`port4`,`minram`,`maxram`,`slots`,`taskset`,`cores`,`lendserver`,`externalID`,`jobPending` FROM `gsswitch` WHERE `userid`=? AND `resellerid`=? ORDER BY `serverip`,`port`");
            $query2 = $sql->prepare("SELECT t.`shorten` FROM `serverlist` s LEFT JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE s.`switchID`=? AND s.`resellerid`=?");
            $query->execute(array($userArray['userdetails']['id'],$resellerID));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $query2->execute(array($row['id'],$resellerID));
                 $shorten = array();
                foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                    $shorten[] = $row2['shorten'];
                }
                $row['shorten'] = implode(',',$shorten);
                $tempArray[] = $row;
            }
            $userArray['gserver'] = $tempArray;
            $tempArray = array();
            $query = $sql->prepare("SELECT * FROM `voice_server` WHERE `userid`=? AND `resellerid`=?");
            $query->execute(array($userArray['userdetails']['id'],$resellerID));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $tempArray[] = $row;
            }
            $userArray['voice'] = $tempArray;
            $tempArray = array();
            $query = $sql->prepare("SELECT `active`,`sid`,`gsid`,`dbname`,`ips`,`max_databases`,`max_queries_per_hour`,`max_updates_per_hour`,`max_connections_per_hour`,`max_userconnections_per_hour`,`externalID`,`jobPending` FROM `mysql_external_dbs` WHERE `uid`=? AND `resellerid`=?");
            $query->execute(array($userArray['userdetails']['id'],$resellerID));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $tempArray[] = $row;
            }
            $userArray['mysql'] = $tempArray;
            if ($apiType == 'xml') {
                header("Content-type: text/xml; charset=UTF-8");
                echo array2xml($userArray,new SimpleXMLElement('<user/>'));
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
} else {
    $success['false'][] = 'No action defined';
}

if ($apiType == 'xml' and !isset($list)) {
    header("Content-type: text/xml; charset=UTF-8");
    if (isset($success['false'])) {
        $errors=implode(', ',$success['false']);
        $action='fail';
    } else {
        $errors = '';
        $action='success';
    }
    $reply=<<<XML
<?xml version='1.0' encoding='UTF-8'?>
<!DOCTYPE users>
<users>
	<action>$action</action>
	<username>$username</username>
	<external_id>$externalID</external_id>
	<email>$email</email>
	<errors>$errors</errors>
	<password>$password</password>
	<active>$active</active>
	<localid>$localID</localid>
	<vname>$vname</vname>
	<name>$name</name>
	<phone>$phone</phone>
	<handy>$handy</handy>
	<fax>$fax</fax>
	<city>$city</city>
	<cityn>$cityn</cityn>
	<street>$street</street>
	<streetn>$streetn</streetn>
	<salutation>$salutation</salutation>
	<birthday>$birthday</birthday>
	<country>$country</country>
	<fdlpath>$fdlpath</fdlpath>
    <mail_backup>$mail_backup</mail_backup>
    <mail_gsupdate>$mail_gsupdate</mail_gsupdate>
    <mail_securitybreach>$mail_securitybreach</mail_securitybreach>
    <mail_serverdown>$mail_serverdown</mail_serverdown>
    <mail_ticket>$mail_ticket</mail_ticket>
    <mail_vserver>$mail_vserver</mail_vserver>
</users>
XML;
    print $reply;
} else if ($apiType == 'json' and !isset($list)) {
    header("Content-type: application/json; charset=UTF-8");
    echo json_encode(array('action' => $action,'username' => $username,'external_id' => $externalID,'email' => $email,'errors' => $errors,'password' => $password,'active' => $active,'localid' => $localID));
} else if (!isset($list)) {
    header('HTTP/1.1 403 Forbidden');
    die('403 Forbidden');
}