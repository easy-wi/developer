<?php

/**
 * File: api_tsdns.php.
 * Author: Ulrich Block
 * Date: 07.06.14
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
$tsdnsMasterID = '';
$dns = '';
$ip = '';
$port = '';

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
        $externalServerID = isExternalID($data['server_external_id']);
        $ip = isip($data['ip'], 'ip4');
        $port = port($data['port']);

        $query = $sql->prepare("SELECT `id`,`cname` FROM `userdata` WHERE `" . $from[$data['identify_user_by']] . "`=? AND `resellerid`=?");
        $query->execute(array($data[$data['identify_user_by']], $resellerID));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
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

            $query = $sql->prepare("SELECT COUNT(`dnsID`) AS `amount` FROM `voice_dns` WHERE `externalID`=? LIMIT 1");
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

                $inSQLArray = 's.`id` IN (' . implode(',', $masterIDsArray) . ') AND';

            } else if (isset($externalMasterIDsArray) and count($externalMasterIDsArray) > 0) {

                $inSQLArray = 's.`externalID` IN (' . implode(',', "'" . $externalMasterIDsArray . "'") . ') AND';
            }

            $query = $sql->prepare("SELECT m.`id`,m.`externalID`,m.`defaultdns`, COUNT(d.`dnsID`)/(m.`max_dns`/100) AS `usedpercent` FROM `voice_tsdns` AS m LEFT JOIN `voice_dns` AS d ON d.`tsdnsID`=m.`id` WHERE m.`resellerid`=? AND m.`active`='Y' GROUP BY m.`id` HAVING $inSQLArray `usedpercent`<100 ORDER BY `usedpercent` ASC LIMIT 1");
            $query->execute(array($resellerID));
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $tsdnsMasterID = $row['id'];
                $hostExternalID = $row['externalID'];
                $defaultdns = $row['defaultdns'];
            }

            if (!isid($tsdnsMasterID, 10)) {
                $success['false'][] = 'No free host';
            }
        }

        if (!isset($success['false'])) {

            $password = (isset($data['password']) and strlen($data['password']) > 0) ? $data['password'] : passwordgenerate(10);

            $query = $sql->prepare("INSERT INTO `voice_dns` (`active`,`ip`,`port`,`tsdnsID`,`userID`,`jobPending`,`externalID`,`resellerID`) VALUES (?,?,?,?,?,'Y',?,?)");
            $query->execute(array($active, $ip, $port, $tsdnsMasterID, $localUserLookupID, $externalServerID, $resellerID));

            $localServerID = (int) $sql->lastInsertId();

            $localUserCname .= '-' . $localServerID;

            $dns = (isset($data['dns']) and isdomain($data['dns'])) ? $data['dns'] : trim($localServerID . '-' . $username . '.' . $defaultdns);

            $query = $sql->prepare("UPDATE `voice_dns` SET `dns`=? WHERE `dnsID`=? AND `resellerID`=? LIMIT 1");
            $query->execute(array($dns, $localServerID, $resellerID));

            $query = $sql->prepare("UPDATE `jobs` SET `status`='2' WHERE `type`='us' AND (`status` IS NULL OR `status`='1') AND `affectedID`=? AND `action`='dl'");
            $query->execute(array($localUserLookupID));

            $query = $sql->prepare("INSERT INTO `jobs` (`api`,`type`,`affectedID`,`invoicedByID`,`hostID`,`userID`,`name`,`status`,`date`,`action`,`extraData`,`resellerid`) VALUES ('A','ds',?,?,?,?,?,NULL,NOW(),'ad','',?)");
            $query->execute(array($localServerID, $resellerID, $tsdnsMasterID, $localUserLookupID, $dns, $resellerID));
        }
    }

} else if (!isset($success['false']) and array_value_exists('action', 'mod', $data)) {

    $identifyServerBy = $data['identify_server_by'];
    $localServerID = isid($data['server_local_id'], 10);
    $externalServerID = isExternalID($data['server_external_id']);

    $from = array('server_local_id' => 'dnsID', 'server_external_id' => 'externalID');

    if (dataExist('identify_server_by', $data)) {

        $changedCount = 0;

        $query = $sql->prepare("SELECT d.*,u.`cname` FROM `voice_dns` AS d INNER JOIN `userdata` AS u ON u.`id`=d.`userID` WHERE d.`" . $from[$data['identify_server_by']] . "`=? AND d.`resellerID`=?");
        $query->execute(array($data[$data['identify_server_by']], $resellerID));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            $changedCount = 1;

            if ($username != $row['cname']) {
                $username = $row['cname'];
            }

            $localServerID = $row['dnsID'];
            $localUserLookupID = $row['userID'];
            $tsdnsMasterID = $row['tsdnsID'];
            $externalServerID = $row['externalID'];
            $ip = $row['ip'];
            $port = $row['port'];
            $dns = $row['dns'];
            $userID = $row['userID'];
            $active = $row['active'];

            $query = $sql->prepare("SELECT COUNT(`jobID`) AS `amount` FROM `jobs` WHERE `affectedID`=? AND `type`='ds' AND `action`='dl' AND (`status` IS NULL OR `status`='1') LIMIT 1");
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

            if (isset($data['ip']) and isip($data['ip'], 'ip4')) {
                $updateArray[] = $data['ip'];
                $eventualUpdate .= ',`ip`=?';
                $ip = $data['ip'];
            }

            if (isset($data['port']) and port($data['port'])) {
                $updateArray[] = $data['port'];
                $eventualUpdate .= ',`port`=?';
                $port = $data['port'];
            }

            if (isExternalID($data['server_external_id']) and $data['identify_server_by'] == 'server_local_id') {
                $updateArray[] = $data['server_external_id'];
                $eventualUpdate .= ',`externalID`=?';
            }

            if (count($updateArray) > 0 and !isset($success['false'])) {

                $eventualUpdate = trim($eventualUpdate,',');
                $eventualUpdate .= ',';

                $updateArray[] = $localServerID;

                $query = $sql->prepare("UPDATE `voice_dns` SET $eventualUpdate `jobPending`='Y' WHERE `dnsID`=? LIMIT 1");
                $query->execute($updateArray);

                $query = $sql->prepare("UPDATE `jobs` SET `status`='2' WHERE `type`='ds' AND (`status` IS NULL OR `status`='1') AND `affectedID`=?");
                $query->execute(array($localServerID));

                $query = $sql->prepare("INSERT INTO `jobs` (`api`,`type`,`hostID`,`invoicedByID`,`affectedID`,`userID`,`name`,`status`,`date`,`action`,`resellerID`) VALUES ('A','ds',?,?,?,?,?,NULL,NOW(),'md',?)");
                $query->execute(array($tsdnsMasterID, $resellerID, $localServerID, $userID, $dns, $resellerID));

                $query = $sql->prepare("UPDATE `jobs` SET `status`='2' WHERE `type`='us' AND (`status` IS NULL OR `status`='1') AND `affectedID`=? AND `action`='dl'");
                $query->execute(array($localUserLookupID));
            }
        }

        if ($changedCount == 0) {
            $success['false'][] = 'No TSDNS can be found with the given ID';
        }

    } else {
        $success['false'][] = 'No data for this method';
    }

} else if (!isset($success['false']) and array_value_exists('action', 'del', $data)) {

    $identifyServerBy = $data['identify_server_by'];
    $localServerID = isid($data['server_local_id'], 10);
    $externalServerID = isExternalID($data['server_external_id']);

    $from = array('server_local_id' => 'dnsID', 'server_external_id' => 'externalID');

    if (dataExist('identify_server_by', $data)) {

        $affectedCount = 0;

        $query = $sql->prepare("SELECT `dnsID`,`userID`,`tsdnsID`,`dns` FROM `voice_dns` WHERE `" . $from[$data['identify_server_by']] . "`=? AND `resellerID`=?");
        $query->execute(array($data[$data['identify_server_by']], $resellerID));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            $localServerID = $row['dnsID'];
            $localUserLookupID = $row['userID'];
            $dns = ($row['dns'] !== null) ? $row['dns'] : '';
            $tsdnsMasterID = $row['tsdnsID'];

            $query = $sql->prepare("UPDATE `voice_dns` SET `jobPending`='Y' WHERE `dnsID`=? LIMIT 1");
            $query->execute(array($localServerID));
            $affectedCount += $query->rowCount();

            $query = $sql->prepare("UPDATE `jobs` SET `status`='2' WHERE `type`='ds' AND (`status` IS NULL OR `status`='1') AND `affectedID`=?");
            $query->execute(array($localServerID));
            $affectedCount += $query->rowCount();

            $query = $sql->prepare("INSERT INTO `jobs` (`api`,`type`,`affectedID`,`invoicedByID`,`hostID`,`userID`,`name`,`status`,`date`,`action`,`extraData`,`resellerid`) VALUES ('A','ds',?,?,?,?,?,NULL,NOW(),'dl','',?)");
            $query->execute(array($localServerID, $resellerID, $tsdnsMasterID, $localUserLookupID, $dns, $resellerID));
            $affectedCount += $query->rowCount();
        }

        if ($affectedCount == 0) {
            $success['false'][] = 'No TSDNS can be found with the given ID';
        }

    } else {
        $success['false'][] = 'No data for this method';
    }

} else if (!isset($success['false']) and array_value_exists('action', 'read', $data)) {

    $identifyServerBy = $data['identify_server_by'];
    $localServerID = isid($data['server_local_id'], 10);
    $externalServerID = isExternalID($data['server_external_id']);

    $from = array('server_local_id' => 'dnsID', 'server_external_id' => 'externalID');

    if (dataExist('identify_server_by', $data)) {

        $query = $sql->prepare("SELECT d.*,u.`cname`,u.`mail`,u.`externalID` AS `userExternalID`,m.`externalID` AS `masterExternalID` FROM `voice_dns` AS d INNER JOIN `voice_tsdns` AS m ON m.`id`=d.`tsdnsID` INNER JOIN `userdata` AS u ON u.`id`=d.`userID` WHERE d.`" . $from[$data['identify_server_by']] . "`=? AND d.`resellerID`=?");
        $query->execute(array($data[$data['identify_server_by']], $resellerID));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            $localServerID = $row['dnsID'];
            $tsdnsMasterID = $row['tsdnsID'];
            $externalServerID = $row['externalID'];
            $active = $row['active'];
            $dns = $row['dns'];
            $ip = $row['ip'];
            $port = $row['port'];

            $localUserID = $row['userID'];
            $externalUserID = $row['userExternalID'];
            $email = $row['mail'];

            if ($username != $row['cname']) {
                $username = $row['cname'];
            }

            $hostExternalID = $row['masterExternalID'];
        }

        if ($query->rowCount() == 0) {
            $success['false'][] = 'No TSDNS can be found with the given ID';
        }

    } else {
        $success['false'][] = 'No data for this method';
    }

} else if (array_value_exists('action', 'ls', $data)) {

    unset($success);

    $query = $sql->prepare("SELECT m.`id`,m.`externalID`,m.`ssh2ip`,m.`description`,m.`defaultdns`,m.`max_dns`,COUNT(d.`dnsID`) AS `installedDNS` FROM `voice_tsdns` AS m LEFT JOIN `voice_dns` AS d ON d.`tsdnsID`=m.`id` WHERE m.`resellerid`=? AND m.`active`='Y' GROUP BY m.`id`");
    $query->execute(array($resellerID));

    if ($apiType == 'xml') {

        $responsexml = new DOMDocument('1.0','utf-8');
        $element = $responsexml->createElement('tsdns');

        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

            $listRootServerXML = $responsexml->createElement('tsdnsServer');

            $listServerXML = $responsexml->createElement('id', $row['id']);
            $listRootServerXML->appendChild($listServerXML);

            $listServerXML = $responsexml->createElement('externalID', $row['externalID']);
            $listRootServerXML->appendChild($listServerXML);

            $listServerXML = $responsexml->createElement('ssh2ip', $row['ssh2ip']);
            $listRootServerXML->appendChild($listServerXML);

            $listServerXML = $responsexml->createElement('description', $row['description']);
            $listRootServerXML->appendChild($listServerXML);

            $listServerXML = $responsexml->createElement('maxDNS', $row['max_dns']);
            $listRootServerXML->appendChild($listServerXML);

            $listServerXML = $responsexml->createElement('installedDNS', $row['installedDNS']);
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
    $element = $responsexml->createElement('tsdns');

    $server = $responsexml->createElement('action', $action);
    $element->appendChild($server);

    $key = $responsexml->createElement('actionSend', (isset($data['action']) ? $data['action'] : ''));
    $element->appendChild($key);

    $server = $responsexml->createElement('master_server_id', $tsdnsMasterID);
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

    $server = $responsexml->createElement('ip', $ip);
    $element->appendChild($server);

    $server = $responsexml->createElement('port', $port);
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