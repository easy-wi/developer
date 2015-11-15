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

foreach (array('active', 'action', 'identify_server_by', 'server_local_id', 'server_external_id', 'identify_user_by', 'user_localid', 'user_externalid', 'username') as $key) {
    if (!array_key_exists($key,$data)) {
        $success['false'][] = 'Data key does not exist: '.$key;
    }
}

$manage_host_table = (isset($data['manage_host_table']) and active_check($data['manage_host_table'])) ? $data['manage_host_table'] : 'N';

if (!isset($success['false']) and array_value_exists('action', 'add', $data)) {

    if (dataExist('identify_user_by', $data)) {

        $active = active_check($data['active']);
        $identifyUserBy = $data['identify_user_by'];
        $localUserID = isid($data['user_localid'], 10);
        $externalUserID = isExternalID($data['user_externalid']);
        $username = $data['username'];
        $identifyServerBy = $data['identify_server_by'];
        $localServerID = isid($data['server_local_id'], 10);
        $externalServerID = isExternalID($data['server_external_id']);
        $from = array('user_localid' => 'id', 'username' => 'cname', 'user_externalid' => 'externalID', 'email' => 'mail');

        $query = $sql->prepare("SELECT `id`,`cname` FROM `userdata` WHERE `" . $from[$data['identify_user_by']] . "`=? AND `resellerid`=?");
        $query->execute(array($data[$data['identify_user_by']], $resellerID));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $localUserLookupID = $row['id'];
            $localUserCname = $row['cname'];

            if ($username != $row['cname']) {
                $username = $row['cname'];
            }
        }

        if (!isset($localUserLookupID)) {
            $success['false'][] = 'user does not exist';
        }

        if (!isset($success['false']) and !in_array($externalServerID, $bad)) {

            $query = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `mysql_external_dbs` WHERE `externalID`=? LIMIT 1");
            $query->execute(array($externalServerID));
            if ($query->fetchColumn() > 0) {
                $success['false'][] = 'database with external ID already exists';
            }
        }

        if (!isset($success['false'])) {

            if (isset($data['master_server_id'])) {
                $masterIDsArray = (isid($data['master_server_id'], 19)) ? array($data['master_server_id']) : (array) $data['master_server_id'];
            }

            if (isset($data['master_server_external_id'])) {
                $externalMasterIDsArray = (isExternalID($data['master_server_external_id']) != '') ? array($data['master_server_external_id']) : (array) $data['master_server_external_id'];
            }

            $inSQLArray = '';

            if (isset($masterIDsArray) and count($masterIDsArray) > 0) {

                $inSQLArray = 's.`id` IN (' . implode(',', $masterIDsArray) . ') AND';

            } else if (isset($externalMasterIDsArray) and count($externalMasterIDsArray) > 0) {

                $inSQLArray = 's.`externalID` IN (' . implode(',', "'" . $externalMasterIDsArray . "'") . ') AND';
            }

            $query = $sql->prepare("SELECT s.`id`,s.`ip`,s.`max_databases`, COUNT(d.`id`) AS `installed`,COUNT(d.`id`)/(s.`max_databases`/100) AS `usedpercent`,s.`max_queries_per_hour`,s.`max_updates_per_hour`,s.`max_connections_per_hour`,s.`max_userconnections_per_hour` FROM `mysql_external_servers` s LEFT JOIN `mysql_external_dbs` d ON s.`id`=d.`sid` WHERE s.`active`='Y' AND s.`resellerid`=? GROUP BY s.`ip` HAVING $inSQLArray `usedpercent`<100 ORDER BY `usedpercent` ASC LIMIT 1");
            $query->execute(array($resellerID));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $hostID = $row['id'];
                $max_databases = $row['max_databases'];
                $max_queries_per_hour = $row['max_queries_per_hour'];
                $max_updates_per_hour = $row['max_updates_per_hour'];
                $max_connections_per_hour = $row['max_connections_per_hour'];
                $max_userconnections_per_hour = $row['max_userconnections_per_hour'];
            }

            if (!isset($hostID)) {
                $success['false'][] = 'No free host';
            }
        }

        if (!isset($success['false'])) {

            $password = passwordgenerate(10);

            $query = $sql->prepare("INSERT INTO `mysql_external_dbs` (`active`,`sid`,`uid`,`password`,`ips`,`manage_host_table`,`max_queries_per_hour`,`max_updates_per_hour`,`max_connections_per_hour`,`max_userconnections_per_hour`,`externalID`,`resellerid`) VALUES (?,?,?,AES_ENCRYPT(?,?),?,?,?,?,?,?,?,?)");
            $query->execute(array($active, $hostID, $localUserLookupID, $password, $aeskey, '', $manage_host_table, $max_queries_per_hour, $max_updates_per_hour, $max_connections_per_hour, $max_userconnections_per_hour, $externalServerID, $resellerID));

            $localID = $sql->lastInsertId();

            $dbname = 'sql' . $localID;

            if ($query->rowCount() > 0) {

                if ($active == 'N') {
                    $password = passwordgenerate(20);
                }

                $query = $sql->prepare("UPDATE `mysql_external_dbs` SET `dbname`=?,`password`=AES_ENCRYPT(?,?) WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($dbname, $password, $aeskey, $localID, $resellerID));

                $query = $sql->prepare("UPDATE `jobs` SET `status`='2' WHERE `type`='my' AND (`status` IS NULL OR `status`='1') AND `affectedID`=? and `resellerID`=?");
                $query->execute(array($localID, $resellerID));

                $query = $sql->prepare("INSERT INTO `jobs` (`api`,`type`,`hostID`,`invoicedByID`,`affectedID`,`userID`,`name`,`status`,`date`,`action`,`resellerid`) VALUES ('A','my',?,?,?,?,?,NULL,NOW(),'ad',?)");
                $query->execute(array($hostID, $resellerID, $localID, $localUserLookupID, $dbname, $resellerID));

            } else {
                $success['false'][] = 'Could not write database to database';
            }
        }

    } else if (!isset($success['false'])) {

        $active = active_check($data['active']);
        $identifyUserBy = $data['identify_user_by'];
        $localUserID = isid($data['user_localid'], 10);
        $externalUserID = isExternalID($data['user_externalid']);
        $username = $data['username'];
        $identifyServerBy = $data['identify_server_by'];
        $localServerID = isid($data['server_local_id'], 10);
        $externalServerID = isExternalID($data['server_external_id']);
        $success['false'][] = 'Can not identify user or bad email';
    }

} else if (!isset($success['false']) and array_value_exists('action', 'mod', $data)) {

    $active = active_check($data['active']);
    $identifyUserBy = $data['identify_user_by'];
    $localUserID = isid($data['user_localid'], 10);
    $externalUserID = isExternalID($data['user_externalid']);
    $username = $data['username'];
    $identifyServerBy = $data['identify_server_by'];
    $localServerID = isid($data['server_local_id'], 10);
    $externalServerID = isExternalID($data['server_external_id']);
    $from = array('server_local_id' => 'id', 'server_external_id' => 'externalID');

    if (dataExist('identify_server_by', $data)) {

        $query = $sql->prepare("SELECT m.`id`,m.`uid`,m.`active`,m.`sid`,m.`dbname`,u.`cname` FROM `mysql_external_dbs` AS m INNER JOIN `userdata` AS u ON u.`id`=m.`uid` WHERE m.`" . $from[$data['identify_server_by']] . "`=? AND m.`resellerid`=?");
        $query->execute(array($data[$data['identify_server_by']], $resellerID));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            $localID = $row['id'];
            $userID = $row['uid'];
            $hostID = $row['sid'];
            $dbname = $row['dbname'];
            $oldActive = $row['active'];

            if ($username != $row['cname']) {
                $username = $row['cname'];
            }

            $query = $sql->prepare("SELECT COUNT(`jobID`) AS `amount` FROM `jobs` WHERE `affectedID`=? AND `type`='my' AND `action`='dl' AND (`status` IS NULL OR `status`='1') LIMIT 1");
            $query->execute(array($localID));
            if ($query->fetchColumn() > 0) {
                $success['false'][] = 'Database is already marked for deletion';
            }

            if (!in_array($active, $bad) and $active != $oldActive) {

                $query = $sql->prepare("UPDATE `mysql_external_dbs` SET `active`=?,`manage_host_table`=?,`jobPending`='Y',`externalID`=? WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($active, $manage_host_table, $externalServerID, $localID, $resellerID));

                $query = $sql->prepare("UPDATE `jobs` SET `status`='2' WHERE `type`='my' AND (`status` IS NULL OR `status`='1') AND `affectedID`=? and `resellerID`=?");
                $query->execute(array($localID, $resellerID));

                $query = $sql->prepare("INSERT INTO `jobs` (`api`,`type`,`hostID`,`invoicedByID`,`affectedID`,`userID`,`name`,`status`,`date`,`action`,`resellerID`) VALUES ('A','my',?,?,?,?,?,NULL,NOW(),'md',?)");
                $query->execute(array($hostID, $resellerID, $localID, $userID, $dbname, $resellerID));

            } else {
                $query = $sql->prepare("UPDATE `mysql_external_dbs` SET `manage_host_table`=?,`externalID`=? WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($manage_host_table, $externalServerID, $localID, $resellerID));
            }
        }

        if (!isset($localID)) {
            $success['false'][] = 'No database can be found to edit';
        }

    } else {
        $success['false'][] = 'No data for this method';
    }

} else if (!isset($success['false']) and array_value_exists('action', 'del', $data)) {

    $active = '';
    $identifyUserBy = '';
    $localUserID = '';
    $externalUserID = '';
    $username = '';
    $identifyServerBy = $data['identify_server_by'];
    $localServerID=isid($data['server_local_id'], 10);
    $externalServerID = isExternalID($data['server_external_id']);
    $from = array('server_local_id' => 'id', 'server_external_id' => 'externalID');

    if (dataExist('identify_server_by', $data)) {

        $query = $sql->prepare("SELECT `id`,`uid`,`sid`,`dbname` FROM `mysql_external_dbs` WHERE `" . $from[$data['identify_server_by']] . "`=? AND `resellerid`=?");
        $query->execute(array($data[$data['identify_server_by']], $resellerID));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $localID = $row['id'];
            $userID = $row['uid'];
            $dbname = $row['dbname'];
            $hostID = $row['sid'];
        }

        if (isset($localID) and isset($dbname)) {

            $query = $sql->prepare("UPDATE `mysql_external_dbs` SET `jobPending`='Y' WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($localID, $resellerID));

            $query = $sql->prepare("UPDATE `jobs` SET `status`='2' WHERE `type`='my' AND (`status` IS NULL OR `status`='1') AND `affectedID`=? and `resellerID`=?");
            $query->execute(array($localID, $resellerID));

            $query = $sql->prepare("INSERT INTO `jobs` (`api`,`type`,`hostID`,`invoicedByID`,`affectedID`,`userID`,`name`,`status`,`date`,`action`,`resellerid`) VALUES ('A','my',?,?,?,?,?,NULL,NOW(),'dl',?)");
            $query->execute(array($hostID, $resellerID, $localID, $userID, $dbname, $resellerID));

        } else {
            $success['false'][] = 'No database can be found to delete';
        }

    } else {
        $success['false'][] = 'No data for this method';
    }

} else if (!isset($success['false']) and array_value_exists('action', 'read', $data)) {

} else if (array_value_exists('action', 'ls', $data)) {

    unset($success['false']);

    $query = $sql->prepare("SELECT s.`id`,s.`externalID`,s.`ip`,s.`interface` AS `description`,s.`max_databases`, COUNT(d.`id`) AS `installed` FROM `mysql_external_servers` s LEFT JOIN `mysql_external_dbs` d ON s.`id`=d.`sid` WHERE s.`active`='Y' AND s.`resellerid`=? GROUP BY s.`ip`");
    $query->execute(array($resellerID));

    if ($apiType == 'xml') {

        $responsexml = new DOMDocument('1.0','utf-8');
        $element = $responsexml->createElement('webspace');

        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            $listRootServerXML = $responsexml->createElement('mysqlServer');

            $listServerXML = $responsexml->createElement('id', $row['id']);
            $listRootServerXML->appendChild($listServerXML);

            $listServerXML = $responsexml->createElement('externalID', $row['externalID']);
            $listRootServerXML->appendChild($listServerXML);

            $listServerXML = $responsexml->createElement('ssh2ip', $row['ip']);
            $listRootServerXML->appendChild($listServerXML);

            $listServerXML = $responsexml->createElement('description', $row['description']);
            $listRootServerXML->appendChild($listServerXML);

            $listServerXML = $responsexml->createElement('maxDBs', $row['max_databases']);
            $listRootServerXML->appendChild($listServerXML);

            $listServerXML = $responsexml->createElement('dbsInstalled', $row['installed']);
            $listRootServerXML->appendChild($listServerXML);

            $element->appendChild($listRootServerXML);
        }

        $responsexml->appendChild($element);

        $responsexml->formatOutput = true;

        die($responsexml->saveXML());

    } else if ($apiType == 'json') {

        header("Content-type: application/json; charset=UTF-8");

        echo json_encode($query->fetchAll(PDO::FETCH_ASSOC));

        die;

    } else {

        header('HTTP/1.1 403 Forbidden');

        die('403 Forbidden');

    }


} else {
    $dbname = '';
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

    if (isset($success['false'])) {
        $errors = implode(', ', $success['false']);
        $action = 'fail';
    } else {
        $errors = '';
        $action = 'success';
    }

    header("Content-type: text/xml; charset=UTF-8");

    $responsexml = new DOMDocument('1.0', 'utf-8');
    $element = $responsexml->createElement('mysql');

    $server = $responsexml->createElement('action', $action);
    $element->appendChild($server);

    $key = $responsexml->createElement('actionSend', (isset($data['action']) ? $data['action'] : ''));
    $element->appendChild($key);

    $server = $responsexml->createElement('errors', $errors);
    $element->appendChild($server);

    $server = $responsexml->createElement('active', $active);
    $element->appendChild($server);

    $server = $responsexml->createElement('dbname', $dbname);
    $element->appendChild($server);

    $server = $responsexml->createElement('manage_host_table', $manage_host_table);
    $element->appendChild($server);

    $server = $responsexml->createElement('identify_server_by', $identifyServerBy);
    $element->appendChild($server);

    $server = $responsexml->createElement('server_external_id', $externalServerID);
    $element->appendChild($server);

    $server = $responsexml->createElement('server_local_id', $localServerID);
    $element->appendChild($server);

    $server = $responsexml->createElement('identify_user_by', $identifyUserBy);
    $element->appendChild($server);

    $server = $responsexml->createElement('user_localid', $localUserID);
    $element->appendChild($server);

    $server = $responsexml->createElement('user_externalid', $externalUserID);
    $element->appendChild($server);

    $server = $responsexml->createElement('username', $username);
    $element->appendChild($server);

    $responsexml->appendChild($element);

    $responsexml->formatOutput = true;

    echo $responsexml->saveXML();

} else if ($apiType == 'json') {

    header("Content-type: application/json; charset=UTF-8");
    echo json_encode(array('action' => $action, 'active' => $active, 'identify_server_by' => $identifyServerBy, 'server_external_id' => $externalServerID, 'server_local_id' => $localServerID, 'identify_user_by' => $identifyUserBy, 'user_localid' => $localUserID, 'user_externalid' => $externalUserID, 'username' => $username, 'errors' => $errors));

} else {

    header('HTTP/1.1 403 Forbidden');
    die('403 Forbidden');
}