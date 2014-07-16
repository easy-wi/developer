<?php

/**
 * File: api_web.php.
 * Author: Ulrich Block
 * Date: 22.03.14
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
$editArray = array('active', 'identify_user_by', 'user_localid', 'user_externalid', 'username');

foreach ($minimumArray as $key) {
    if (!array_key_exists($key, $data)) {
        $success['false'][] = 'Data key does not exist: ' . $key;
    }
}

if (array_key_exists('action', $data)) {
    foreach ($editArray as $key) {
        if (!array_key_exists($key, $data)) {
            $success['false'][] = 'Data key does not exist: ' . $key;
        }
    }
}

$active = '';
$identifyUserBy = '';
$localUserID = '';
$externalUserID = '';
$email = '';
$username = '';
$identifyServerBy = '';
$localServerID = '';
$externalServerID = '';
$hostExternalID = '';
$webMasterID = '';
$password = '';
$dns = '';
$hdd = '';
$ownVhost = 'N';

if (!isset($success['false']) and array_value_exists('action', 'add', $data)) {

    if (dataExist('identify_user_by', $data)) {

        $from = array('user_localid' => 'id', 'username' => 'cname', 'user_externalid' => 'externalID', 'email' => 'mail');

        $active = active_check($data['active']);
        $identifyUserBy = $data['identify_user_by'];
        $localUserID = isid($data['user_localid'], 10);
        $externalUserID = isExternalID($data['user_externalid']);
        $username = $data['username'];
        $identifyServerBy = $data['identify_server_by'];
        $localServerID = isid($data['server_local_id'], 10);
        $hdd = isid($data['hdd'], 10);
        $externalServerID = isExternalID($data['server_external_id']);
        $ownVhost = (isset($data['ownVhost']) and active_check($data['ownVhost'])) ? $data['ownVhost'] : 'N';
        $dns = (isset($data['dns']) and isdomain($data['dns'])) ? $data['dns'] : '';

        $query = $sql->prepare("SELECT `id`,`cname` FROM `userdata` WHERE `" . $from[$data['identify_user_by']] . "`=? AND `resellerid`=?");
        $query->execute(array($data[$data['identify_user_by']], $resellerID));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $localUserLookupID = $row['id'];
            $localUserCname = $row['cname'];

            if ($username != $row['cname']) {
                $username = $row['cname'];
            }
        }

        if (!isset($localUserLookupID) or !isset($localUserCname)) {
            $success['false'][] = 'user does not exist';
        }

        if (!isset($success['false']) and !in_array($externalServerID, $bad)) {

            $query = $sql->prepare("SELECT COUNT(`webVhostID`) AS `amount` FROM `vhostTemplate` WHERE `externalID`=? LIMIT 1");
            $query->execute(array($externalServerID));

            if ($query->fetchColumn() > 0) {
                $success['false'][] = 'server with external ID already exists';
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

                $inSQLArray = 'm.`webMasterID` IN (' . implode(',', $masterIDsArray) . ') AND';

            } else if (isset($externalMasterIDsArray) and count($externalMasterIDsArray) > 0) {

                $inSQLArray = 'm.`externalID` IN (' . implode(',', "'" . $externalMasterIDsArray . "'") . ') AND';
            }

            $query = $sql->prepare("SELECT m.`webMasterID`,m.`defaultdns`,(SELECT COUNT(v.`webVhostID`) AS `a` FROM `webVhost` AS v WHERE v.`webMasterID`=m.`webMasterID`)/(m.`maxVhost`/100) AS `percentVhostUsage`,(SELECT SUM(v.`hdd`) AS `a` FROM `webVhost` AS v WHERE v.`webMasterID`=m.`webMasterID`)/(IF(m.`hddOverbook`='Y',(m.`maxHDD`/100) * (100+m.`overbookPercent`),`maxHDD`)/100) AS `percentHDDUsage` FROM `webMaster` AS m WHERE m.`active`='Y' AND m.`resellerID`=? GROUP BY m.`webMasterID` HAVING $inSQLArray (`percentVhostUsage`<100 OR `percentVhostUsage`IS NULL) AND (`percentHDDUsage`<100 OR `percentHDDUsage`IS NULL) ORDER BY `percentHDDUsage` ASC,`percentVhostUsage` ASC LIMIT 1");
            $query->execute(array($resellerID));

            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $webMasterID = $row['webMasterID'];
                $hostExternalID = $row['externalID'];
                $defaultdns = $row['defaultdns'];
            }

            if (!isid($webMasterID, 10)) {
                $success['false'][] = 'No free host';
            }
        }

        if (!isset($success['false'])) {

            $password = (isset($data['password']) and strlen($data['password']) > 0) ? $data['password'] : passwordgenerate(10);

            $query = $sql->prepare("INSERT INTO `webVhost` (`externalID`,`webMasterID`,`userID`,`active`,`hdd`,`ftpPassword`,`ownVhost`,`vhostTemplate`,`jobPending`,`resellerID`) VALUES (?,?,?,?,?,AES_ENCRYPT(?,?),?,?,'Y',?)");
            $query->execute(array($externalServerID, $webMasterID, $localUserLookupID, $active, $hdd, $password, $aeskey, $ownVhost, $vhostTemplate, $resellerID));

            $localServerID = (int) $sql->lastInsertId();

            $query = $sql->prepare("SELECT `defaultdns` FROM `webMaster` WHERE `webMasterID`=? AND `resellerID`=? LIMIT 1");
            $query->execute(array($webMasterID, $resellerID));
            $defaultDns = (string) $query->fetchColumn();

            $localUserCname .= '-' . $localServerID;
            $ftpUser = 'web-' . $localServerID;

            if ($defaultDns == $dns or $dns == '') {
                $dns = str_replace('..', '.', $localUserCname . '.' .$defaultDns);
            }

            $query = $sql->prepare("UPDATE `webVhost` SET `dns`=?,`ftpUser`=? WHERE `webVhostID`=? AND `resellerID`=? LIMIT 1");
            $query->execute(array($dns, $ftpUser, $localServerID, $resellerID));

            $query = $sql->prepare("INSERT INTO `jobs` (`api`,`type`,`affectedID`,`invoicedByID`,`hostID`,`userID`,`name`,`status`,`date`,`action`,`extraData`,`resellerid`) VALUES ('A','wv',?,?,?,?,?,NULL,NOW(),'ad','',?)");
            $query->execute(array($localServerID, $resellerID, $webMasterID, $localUserLookupID, $dns, $resellerID));
        }
    }

} else if (!isset($success['false']) and array_value_exists('action', 'mod', $data)) {

    $identifyServerBy = $data['identify_server_by'];
    $localServerID = isid($data['server_local_id'], 10);
    $externalServerID = isExternalID($data['server_external_id']);

    $from = array('server_local_id' => 'webVhostID', 'server_external_id' => 'externalID');

    if (dataExist('identify_server_by', $data)) {

        $changedCount = 0;

        $query = $sql->prepare("SELECT w.*,c.`cname` FROM `webVhost` AS w INNER JOIN `userdata` AS u ON u.`id`=w.`userID` WHERE w.`" . $from[$data['identify_server_by']] . "`=? AND w.`resellerID`=?");
        $query->execute(array($data[$data['identify_server_by']], $resellerID));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

            $changedCount = 1;

            if ($username != $row['cname']) {
                $username = $row['cname'];
            }

            $localServerID = $row['webVhostID'];
            $localUserLookupID = $row['userID'];
            $webMasterID = $row['webMasterID'];
            $externalServerID = $row['externalID'];
            $dns = $row['dns'];
            $userID = $row['userID'];
            $oldHDD = $row['hdd'];

            $query = $sql->prepare("SELECT COUNT(`jobID`) AS `amount` FROM `jobs` WHERE `affectedID`=? AND `type`='wv' AND `action`='dl' AND (`status` IS NULL OR `status`='1') LIMIT 1");
            $query->execute(array($localServerID));
            if ($query->fetchColumn() > 0) {
                $success['false'][] = 'Server is marked for deletion';
            }

            $updateArray = array();
            $eventualUpdate = '';

            if (isset($data['active']) and active_check($data['active'])) {
                $updateArray[] = $data['active'];
                $eventualUpdate .= ',`active`=?';
                $active = $data['active'];
            }

            if (isset($data['dns']) and isdomain($data['dns'])) {
                $updateArray[] = $data['dns'];
                $eventualUpdate .= ',`dns`=?';
                $dns = $data['dns'];
            }

            if (isset($data['password']) and is_password($data['password'], 255)) {
                $updateArray[] = $data['private'];
                $updateArray[] = $aeskey;
                $eventualUpdate .= ',`ftpPassword`=AES_ENCRYPT(?,?)';
                $private = $data['password'];
            }

            if (isset($data['hdd']) and isid($data['hdd'], 10)) {
                $updateArray[] = $data['hdd'];
                $eventualUpdate .= ',`hdd`=?';
                $hdd = $data['hdd'];

                $query = $sql->prepare("SELECT IF(`hddOverbook`='Y',(`maxHDD`/100) * (100+`overbookPercent`),`maxHDD`) AS `maxHDD` FROM `webMaster` WHERE `webMasterID`=? LIMIT 1");
                $query->execute(array($webMasterID));
                $maxHDD = (int) $query->fetchColumn();

                $query = $sql->prepare("SELECT SUM(v.`hdd`) AS `a` FROM `webVhost` WHERE `webMasterID`=?");
                $query->execute(array($localServerID));

                if (($maxHDD + $oldHDD - $query->fetchColumn() - $hdd) < 0) {
                    $success['false'][] = 'Not enough space left';
                }
            }

            if (isset($data['ownVhost']) and active_check($data['ownVhost'])) {
                $updateArray[] = $data['ownVhost'];
                $eventualUpdate .= ',`ownVhost`=?';
                $ownVhost = $data['ownVhost'];
            }

            if (count($updateArray) > 0 and count($success['false']) == 0) {

                $eventualUpdate = trim($eventualUpdate,',');
                $eventualUpdate .= ',';

                $updateArray[] = $localServerID;

                $query = $sql->prepare("UPDATE `webVhost` SET $eventualUpdate `jobPending`='Y' WHERE `webVhostID`=? LIMIT 1");
                $query->execute($updateArray);

                $query = $sql->prepare("UPDATE `jobs` SET `status`='2' WHERE `type`='wv' AND (`status` IS NULL OR `status`='1') AND `affectedID`=?");
                $query->execute(array($localServerID));

                $query = $sql->prepare("INSERT INTO `jobs` (`api`,`type`,`hostID`,`invoicedByID`,`affectedID`,`userID`,`name`,`status`,`date`,`action`,`resellerID`) VALUES ('A','wv',?,?,?,?,?,NULL,NOW(),'md',?)");
                $query->execute(array($webMasterID, $resellerID, $localServerID, $userID, $dns, $resellerID));

            }
        }

        if ($changedCount == 0) {
            $success['false'][] = 'No webspace can be found with the given ID';
        }

    } else {
        $success['false'][] = 'No data for this method';
    }

} else if (!isset($success['false']) and array_value_exists('action', 'del', $data)) {

    $identifyServerBy = $data['identify_server_by'];
    $localServerID = isid($data['server_local_id'], 10);
    $externalServerID = isExternalID($data['server_external_id']);

    $from = array('server_local_id' => 'webVhostID', 'server_external_id' => 'externalID');

    if (dataExist('identify_server_by', $data)) {

        $affectedCount = 0;

        $query = $sql->prepare("SELECT `webVhostID`,`userID`,`webMasterID`,`dns` FROM `webVhost` WHERE `" . $from[$data['identify_server_by']] . "`=? AND `resellerID`=?");
        $query->execute(array($data[$data['identify_server_by']], $resellerID));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

            $localServerID = $row['webVhostID'];
            $localUserLookupID = $row['userID'];
            $dns = ($row['dns'] !== null) ? $row['dns'] : '';
            $webMasterID = $row['webMasterID'];

            $query = $sql->prepare("UPDATE `webVhost` SET `jobPending`='Y' WHERE `webVhostID`=? LIMIT 1");
            $query->execute(array($localServerID));
            $affectedCount += $query->rowCount();

            $query = $sql->prepare("UPDATE `jobs` SET `status`='2' WHERE `type`='wv' AND (`status` IS NULL OR `status`='1') AND `affectedID`=?");
            $query->execute(array($localServerID));
            $affectedCount += $query->rowCount();

            $query = $sql->prepare("INSERT INTO `jobs` (`api`,`type`,`affectedID`,`invoicedByID`,`hostID`,`userID`,`name`,`status`,`date`,`action`,`extraData`,`resellerid`) VALUES ('A','wv',?,?,?,?,?,NULL,NOW(),'dl','',?)");
            $query->execute(array($localServerID, $resellerID, $webMasterID, $localUserLookupID, $dns, $resellerID));
            $affectedCount += $query->rowCount();
        }

        if ($affectedCount == 0) {
            $success['false'][] = 'No webspace can be found with the given ID';
        }

    } else {
        $success['false'][] = 'No data for this method';
    }

} else if (!isset($success['false']) and array_value_exists('action', 'read', $data)) {

    $identifyServerBy = $data['identify_server_by'];
    $localServerID = isid($data['server_local_id'], 10);
    $externalServerID = isExternalID($data['server_external_id']);

    $from = array('server_local_id' => 'webVhostID', 'server_external_id' => 'externalID');

    if (dataExist('identify_server_by', $data)) {

        $query = $sql->prepare("SELECT v.*,u.`cname`,u.`mail`,u.`externalID` AS `userExternalID`,m.`externalID` AS `masterExternalID` FROM `webVhost` AS v INNER JOIN `webMaster` AS m ON m.`webMasterID`=v.`webMasterID` INNER JOIN `userdata` AS u ON u.`id`=v.`userID` WHERE v.`" . $from[$data['identify_server_by']] . "`=? AND v.`resellerID`=?");
        $query->execute(array($data[$data['identify_server_by']], $resellerID));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {

            $localServerID = $row['webVhostID'];
            $localUserLookupID = $row['userID'];
            $webMasterID = $row['webMasterID'];
            $externalServerID = $row['externalID'];
            $active = $row['active'];
            $dns = $row['dns'];
            $hdd = $row['hdd'];
            $ownVhost = $row['ownVhost'];

            $localUserID = $row['userID'];
            $externalUserID = $row['userExternalID'];
            $email = $row['mail'];

            if ($username != $row['cname']) {
                $username = $row['cname'];
            }

            $hostExternalID = $row['masterExternalID'];
        }

        if ($query->rowCount() == 0) {
            $success['false'][] = 'No webspace can be found with the given ID';
        }

    } else {
        $success['false'][] = 'No data for this method';
    }

} else if (array_value_exists('action', 'ls', $data)) {

    unset($success);

    $query = $sql->prepare("SELECT m.`webMasterID`,m.`externalID`,m.`description`,m.`ip`,m.`defaultdns`,m.`maxVhost`,(SELECT COUNT(v.`webVhostID`) AS `a` FROM `webVhost` AS v WHERE v.`webMasterID` = m.`webMasterID`) AS `installedVhosts`,m.`maxHDD`,m.`hddOverbook`,(SELECT SUM( v.`hdd` ) AS `a` FROM `webVhost` AS v WHERE v.`webMasterID` = m.`webMasterID`) AS `hddUsage` FROM `webMaster` AS m WHERE m.`active`='Y' AND m.`resellerID`=?");
    $query->execute(array($resellerID));

    if ($apiType == 'xml') {

        $responsexml = new DOMDocument('1.0','utf-8');
        $element = $responsexml->createElement('webspace');

        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            $listRootServerXML = $responsexml->createElement('webspaceServer');

            $listServerXML = $responsexml->createElement('id', $row['webMasterID']);
            $listRootServerXML->appendChild($listServerXML);

            $listServerXML = $responsexml->createElement('externalID', $row['externalID']);
            $listRootServerXML->appendChild($listServerXML);

            $listServerXML = $responsexml->createElement('ssh2ip', $row['ip']);
            $listRootServerXML->appendChild($listServerXML);

            $listServerXML = $responsexml->createElement('description', $row['description']);
            $listRootServerXML->appendChild($listServerXML);

            $listServerXML = $responsexml->createElement('defaultdns', $row['defaultdns']);
            $listRootServerXML->appendChild($listServerXML);

            $listServerXML = $responsexml->createElement('maxVhost', $row['maxVhost']);
            $listRootServerXML->appendChild($listServerXML);

            $listServerXML = $responsexml->createElement('installedVhosts', $row['installedVhosts']);
            $listRootServerXML->appendChild($listServerXML);

            $listServerXML = $responsexml->createElement('maxHDD', $row['maxHDD']);
            $listRootServerXML->appendChild($listServerXML);

            $listServerXML = $responsexml->createElement('hddOverbook', $row['hddOverbook']);
            $listRootServerXML->appendChild($listServerXML);

            $listServerXML = $responsexml->createElement('hddUsage', $row['hddUsage']);
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
    $success['false'][] = 'Unknown method';
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
    $element = $responsexml->createElement('web');

    $server = $responsexml->createElement('action', $action);
    $element->appendChild($server);

    $key = $responsexml->createElement('actionSend', (isset($data['action']) ? $data['action'] : ''));
    $element->appendChild($key);

    $server = $responsexml->createElement('master_server_id', $webMasterID);
    $element->appendChild($server);

    $server = $responsexml->createElement('master_server_external_id', $hostExternalID);
    $element->appendChild($server);

    $server = $responsexml->createElement('identify_user_by', $identifyUserBy);
    $element->appendChild($server);

    $server = $responsexml->createElement('username', $username);
    $element->appendChild($server);

    $server = $responsexml->createElement('email', $email);
    $element->appendChild($server);

    $server = $responsexml->createElement('user_localid', $localUserID);
    $element->appendChild($server);

    $server = $responsexml->createElement('user_externalid', $externalUserID);
    $element->appendChild($server);

    $server = $responsexml->createElement('password', $password);
    $element->appendChild($server);

    $server = $responsexml->createElement('identify_server_by', $identifyServerBy);
    $element->appendChild($server);

    $server = $responsexml->createElement('server_external_id', $externalServerID);
    $element->appendChild($server);

    $server = $responsexml->createElement('server_local_id', $localServerID);
    $element->appendChild($server);

    $server = $responsexml->createElement('active', $active);
    $element->appendChild($server);

    $server = $responsexml->createElement('dns', $dns);
    $element->appendChild($server);

    $server = $responsexml->createElement('hdd', $hdd);
    $element->appendChild($server);

    $server = $responsexml->createElement('ownVhost', $ownVhost);
    $element->appendChild($server);

    $server = $responsexml->createElement('errors', $errors);
    $element->appendChild($server);

    $responsexml->appendChild($element);

    $responsexml->formatOutput = true;

    echo $responsexml->saveXML();

} else if ($apiType == 'json') {

} else {

    header('HTTP/1.1 403 Forbidden');
    die('403 Forbidden');

}