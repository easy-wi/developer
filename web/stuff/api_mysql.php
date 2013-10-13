<?php
/**
 * File: api_mysql.php.
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

include(EASYWIDIR . '/stuff/keyphrasefile.php');
foreach (array('active','action','identify_server_by','server_local_id','server_external_id','identify_user_by','user_localid','user_externalid','username') as $key) {
    if (!array_key_exists($key,$data)) {
        $success['false'][] = 'Data key does not exist: '.$key;
    }
}
if (!isset($success['false']) and array_value_exists('action','add',$data)) {
    if (dataExist('identify_user_by',$data)) {
        $active=active_check($data['active']);
        $identifyUserBy = $data['identify_user_by'];
        $localUserID=isid($data['user_localid'],21);
        $externalUserID = $data['user_externalid'];
        $username = $data['username'];
        $identifyServerBy = $data['identify_server_by'];
        $localServerID=isid($data['server_local_id'],21);
        $externalServerID = $data['server_external_id'];
        $from=array('user_localid' => 'id','username' => 'cname','user_externalid' => 'externalID','email' => 'mail');
        $query = $sql->prepare("SELECT `id`,`cname` FROM `userdata` WHERE `".$from[$data['identify_user_by']]."`=? AND `resellerid`=?");
        $query->execute(array($data[$data['identify_user_by']],$resellerID));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $localUserLookupID = $row['id'];
            $localUserCname = $row['cname'];
        }
        if (!isset($localUserLookupID)) {
            $success['false'][] = 'user does not exist';
        }
        if (!isset($success['false']) and !in_array($externalServerID,$bad)) {
            $query = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `mysql_external_dbs` WHERE `externalID`=? LIMIT 1");
            $query->execute(array($externalServerID));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                if ($row['amount']>0) {
                    $success['false'][] = 'database with external ID already exists';
                }
            }
        }
        if (!isset($success['false'])) {
            $query = $sql->prepare("SELECT s.`id`,s.`ip`,s.`max_databases`, COUNT(d.`id`) AS `installed`,(s.`max_databases`/100)*COUNT(d.`id`) AS `usedpercent`,s.`max_queries_per_hour`,s.`max_updates_per_hour`,s.`max_connections_per_hour`,s.`max_userconnections_per_hour` FROM `mysql_external_servers` s LEFT JOIN `mysql_external_dbs` d ON s.`id`=d.`sid` WHERE s.`active`='Y' AND s.`resellerid`=? GROUP BY s.`ip` HAVING `usedpercent`<100 ORDER BY `usedpercent` ASC LIMIT 1");
            $query->execute(array($resellerID));
            foreach ($query->fetchall() as $row) {
                $max_databases = $row['max_databases'];
                $max_queries_per_hour = $row['max_queries_per_hour'];
                $max_updates_per_hour = $row['max_updates_per_hour'];
                $max_connections_per_hour = $row['max_connections_per_hour'];
                $max_userconnections_per_hour = $row['max_userconnections_per_hour'];
                $hostID = $row['id'];
            }
            if (!isset($hostID)) {
                $success['false'][] = 'No free host';
            }
        }
        if (!isset($success['false'])) {
            $password=passwordgenerate(10);
            $dbname = $localUserCname . '_' . $password;
            $insert = $sql->prepare("INSERT INTO `mysql_external_dbs` (`active`,`sid`,`uid`,`dbname`,`password`,`ips`,`max_queries_per_hour`,`max_updates_per_hour`,`max_connections_per_hour`,`max_userconnections_per_hour`,`externalID`,`resellerid`) VALUES (?,?,?,?,AES_ENCRYPT(?,?),?,?,?,?,?,?,?)");
            $insert->execute(array($active,$hostID,$localUserLookupID,$dbname,$password,$aeskey,'',$max_queries_per_hour,$max_updates_per_hour,$max_connections_per_hour,$max_userconnections_per_hour,$externalServerID,$resellerID));
            $query = $sql->prepare("SELECT `id` FROM `mysql_external_dbs` WHERE `dbname`=? AND `resellerid`=? ORDER BY `id` DESC LIMIT 1");
            $query->execute(array($dbname,$resellerID));
            foreach ($query->fetchall() as $row) {
                $localID = $row['id'];
            }
            $dbname = $localUserCname . '-' . $localID;
            $nameLength=strlen($dbname);
            if ($nameLength>16) {
                $strStart = $nameLength-16;
                $dbname=substr($dbname,$strStart,$nameLength);
            }
            if (isset($localID)) {
                if ($active == 'N') {
                    $password=passwordgenerate(20);
                }
                $pupdate = $sql->prepare("UPDATE `mysql_external_dbs` SET `dbname`=?,`password`=AES_ENCRYPT(?,?) WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $pupdate->execute(array($dbname,$password,$aeskey,$localID,$resellerID));
                $update = $sql->prepare("UPDATE `jobs` SET `status`='2' WHERE `type`='my' AND (`status` IS NULL OR `status`='1') AND `affectedID`=? and `resellerID`=?");
                $update->execute(array($localID,$resellerID));
                $insert = $sql->prepare("INSERT INTO `jobs` (`api`,`type`,`hostID`,`invoicedByID`,`affectedID`,`userID`,`name`,`status`,`date`,`action`,`resellerid`) VALUES ('A','my',?,?,?,?,?,NULL,NOW(),'ad',?)");
                $insert->execute(array($hostID,$resellerID,$localID,$localUserLookupID,$dbname,$resellerID));
            } else {
                $success['false'][] = 'Could not write database to database';
            }
        }
    } else if (!isset($success['false'])) {
        $active=active_check($data['active']);
        $identifyUserBy = $data['identify_user_by'];
        $localUserID=isid($data['user_localid'],21);
        $externalUserID = $data['user_externalid'];
        $username = $data['username'];
        $identifyServerBy = $data['identify_server_by'];
        $localServerID=isid($data['server_local_id'],21);
        $externalServerID = $data['server_external_id'];
        $success['false'][] = 'Can not identify user or bad email';
    }
} else if (!isset($success['false']) and array_value_exists('action','mod',$data)) {
    $active=active_check($data['active']);
    $identifyUserBy = $data['identify_user_by'];
    $localUserID=isid($data['user_localid'],21);
    $externalUserID = $data['user_externalid'];
    $username = $data['username'];
    $identifyServerBy = $data['identify_server_by'];
    $localServerID=isid($data['server_local_id'],21);
    $externalServerID = $data['server_external_id'];
    $from=array('server_local_id' => 'id','server_external_id' => 'externalID');
    if (dataExist('identify_server_by',$data)) {
        $query = $sql->prepare("SELECT `id`,`uid`,`active`,`sid`,`dbname` FROM `mysql_external_dbs` WHERE `".$from[$data['identify_server_by']]."`=? AND `resellerid`=?");
        $query->execute(array($data[$data['identify_server_by']],$resellerID));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $localID = $row['id'];
            $userID = $row['uid'];
            $hostID = $row['sid'];
            $name = $row['dbname'];
            $oldActive = $row['active'];
            $query = $sql->prepare("SELECT COUNT(`jobID`) AS `amount` FROM `jobs` WHERE `affectedID`=? AND `resellerID`=? AND `action`='dl' AND (`status` IS NULL OR `status`='1') LIMIT 1");
            $query->execute(array($localID,$resellerID));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                if($row['amount']>0) {
                    $success['false'][] = 'Database is already marked for deletion';
                }
            }
            if (!in_array($active,$bad) and $active != $oldActive) {
                $query = $sql->prepare("UPDATE `mysql_external_dbs` SET `active`=?,`jobPending`='Y' WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($active,$localID,$resellerID));
                $update = $sql->prepare("UPDATE `jobs` SET `status`='2' WHERE `type`='my' AND (`status` IS NULL OR `status`='1') AND `affectedID`=? and `resellerID`=?");
                $update->execute(array($localID,$resellerID));
                $insert = $sql->prepare("INSERT INTO `jobs` (`api`,`type`,`hostID`,`invoicedByID`,`affectedID`,`userID`,`name`,`status`,`date`,`action`,`resellerID`) VALUES ('A','my',?,?,?,?,?,NULL,NOW(),'md',?)");
                $insert->execute(array($hostID,$resellerID,$localID,$userID,$name,$resellerID));
            }
        }
        if(!isset($localID)) {
            $success['false'][] = 'No database can be found to edit';
        }
    } else {
        $success['false'][] = 'No data for this method';
    }
} else if (!isset($success['false']) and array_value_exists('action','del',$data)) {
    $active = '';
    $identifyUserBy = '';
    $localUserID = '';
    $externalUserID = '';
    $username = '';
    $identifyServerBy = $data['identify_server_by'];
    $localServerID=isid($data['server_local_id'],21);
    $externalServerID = $data['server_external_id'];
    $from=array('server_local_id' => 'id','server_external_id' => 'externalID');
    if (dataExist('identify_server_by',$data)) {
        $query = $sql->prepare("SELECT `id`,`uid`,`sid`,`dbname` FROM `mysql_external_dbs` WHERE `".$from[$data['identify_server_by']]."`=? AND `resellerid`=?");
        $query->execute(array($data[$data['identify_server_by']],$resellerID));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $localID = $row['id'];
            $userID = $row['uid'];
            $name = $row['dbname'];
            $hostID = $row['sid'];
        }
        if(isset($localID) and isset($name)) {
            $query = $sql->prepare("UPDATE `mysql_external_dbs` SET `jobPending`='Y' WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($localID,$resellerID));
            $update = $sql->prepare("UPDATE `jobs` SET `status`='2' WHERE (`status` IS NULL OR `status`='1') AND `affectedID`=? and `resellerID`=?");
            $update->execute(array($localID,$resellerID));
            $insert = $sql->prepare("INSERT INTO `jobs` (`api`,`type`,`hostID`,`invoicedByID`,`affectedID`,`userID`,`name`,`status`,`date`,`action`,`resellerid`) VALUES ('A','my',?,?,?,?,?,NULL,NOW(),'dl',?)");
            $insert->execute(array($hostID,$resellerID,$localID,$userID,$name,$resellerID));
        } else {
            $success['false'][] = 'No database can be found to delete';
        }
    } else {
        $success['false'][] = 'No data for this method';
    }
} else {
    $active = '';
    $identifyUserBy = '';
    $localUserID = '';
    $externalUserID = '';
    $username = '';
    $identifyServerBy = '';
    $localServerID = '';
    $externalServerID = '';
    $success['false'][] = 'Method not allowed';
}

if ($apiType == 'xml') {
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
<!DOCTYPE mysql>
<mysql>
	<action>$action</action>
	<active>$active</active>
	<identify_server_by>$identifyServerBy</identify_server_by>
	<server_external_id>$externalServerID</server_external_id>
	<server_local_id>$localServerID</server_local_id>
	<identify_user_by>$identifyUserBy</identify_user_by>
	<user_localid>$localUserID</user_localid>
	<user_externalid>$externalUserID</user_externalid>
	<username>$username</username>
	<errors>$errors</errors>
</mysql>
XML;
    print $reply;
} else if ($apiType == 'json') {
    header("Content-type: application/json; charset=UTF-8");
    echo json_encode(array('action' => $action,'active' => $active,'identify_server_by' => $identifyServerBy,'server_external_id' => $externalServerID,'server_local_id' => $localServerID,'identify_user_by' => $identifyUserBy,'user_localid' => $localUserID,'user_externalid' => $externalUserID,'username' => $username,'errors' => $errors));
} else {
    header('HTTP/1.1 403 Forbidden');
    die('403 Forbidden');
}