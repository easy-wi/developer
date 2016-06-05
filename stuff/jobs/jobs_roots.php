<?php

/**
 * File: jobs_roots.php.
 * Author: Ulrich Block
 * Date: 01.04.13
 * Time: 12:14
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

$rootObject = new rootServer($aeskey);
$removeIDs = array('de' => array(), 'vs' => array());

print 'Collecting Dedicated and Vmware server changes'."\r\n";

$query = $sql->prepare("SELECT * FROM `jobs` WHERE (`status` IS NULL OR `status`=1) AND `type` IN ('de','vs')");
$query2 = $sql->prepare("UPDATE `jobs` SET `status`='3' WHERE `jobID`=? AND `type` IN ('de','vs') LIMIT 1");
$query3 = $sql->prepare("UPDATE `jobs` SET `status`='2' WHERE `jobID`=? AND `type` IN ('de','vs') LIMIT 1");
$query4 = $sql->prepare("UPDATE `jobs` SET `status`='1' WHERE `jobID`=? AND `type` IN ('de','vs') LIMIT 1");
$query5 = $sql->prepare("SELECT `ip`,`ips` FROM `rootsDedicated` WHERE `dedicatedID`=? LIMIT 1");
$query6 = $sql->prepare("SELECT `ip`,`ips` FROM `virtualcontainer` WHERE `id`=? LIMIT 1");
$query7 = $sql->prepare("UPDATE `rootsIP4` SET `ownerID`=0 WHERE `ip`=? LIMIT 1");

$query->execute();
while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

    $extraData = @json_decode($row['extraData']);
    $extraData = (array) $extraData;

    $type = ($row['type'] == 'de') ? 'dedicated' : 'vmware';

    if (!isset($extraData['runAt']) or strtotime('now') > $extraData['runAt']) {

        $return = $rootObject->rootServer($row['affectedID'], $row['action'], $type, $extraData);

        // bei add und mod restart Auftrag schreiben mit extra Data = timestamp
        if ($row['action'] == 'dl') {

            if ($return === true) {

                $query2->execute(array($row['jobID']));

                $command = $gsprache->del . ' ' . $type . ' server: ' . $row['affectedID'] . ' name:' . $row['name'];

                $removeIDs[$row['type']][] = $row['affectedID'];

                $ips = array();

                if ($type == 'dedicated') {

                    $query5->execute(array($row['affectedID']));
                    foreach ($query5->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                        $ips[] = $row2['ip'];
                        foreach (ipstoarray($row2['ips']) as $ip) {
                            $ips[] = $ip;
                        }
                    }

                } else {
                    $query6->execute(array($row['affectedID']));
                    foreach ($query6->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                        $ips[] = $row2['ip'];
                        foreach (ipstoarray($row2['ips']) as $ip) {
                            $ips[] = $ip;
                        }
                    }
                }

                foreach ($ips as $ip) {
                    $query7->execute(array($ip));
                }

            } else {

                $query4->execute(array($row['jobID']));

                $command = 'Error: ' . $gsprache->del . ' ' . $type . ' server: ' . $row['affectedID'] . ' name:' . $row['name'] . ' ' . $return;

            }

        } else if ($row['action'] == 'ad') {

            $query2->execute(array($row['jobID']));

            $command = $gsprache->add . ' ' . $type . ' server: ' . $row['affectedID'] . ' name:' . $row['name'];

        } else if (in_array($row['action'], array('md', 'ri', 'st', 'rc', 'rp'))) {

            if ($return === true) {

                $query2->execute(array($row['jobID']));

                $command = $gsprache->mod . ' ' . $type . ' server: ' . $row['affectedID'] . ' name: ' . $row['name'];

            } else {

                $query4->execute(array($row['jobID']));

                $command = 'Error modding ' . $type . ' server: ' . $row['affectedID'] . ' name: '.$row['name'] . ' ' . $return;

            }

        } else if ($row['action'] == 're') {

            if ($return === true and !isset($extraData->reboot) and strtotime('now')<$extraData->reboot) {

                $query2->execute(array($row['jobID']));

                $command = 'Skipped (Re)Start ' . $type . ' server: ' . $row['affectedID'] . ' name: ' . $row['name'] . ' will try later';

            } else if ($return === true and (!isset($extraData->reboot) or strtotime('now')>$extraData->reboot)) {

                $query2->execute(array($row['jobID']));

                $command = '(Re)Start ' . $type . ' server: ' . $row['affectedID'] . ' name: '.$row['name'];

            } else {

                $query4->execute(array($row['jobID']));

                $command = 'Error (Re)starting ' . $type . ' server: ' . $row['affectedID'] . ' name: ' . $row['name'] . ' ' . $return;
            }

        } else if (!isset($customer)) {

            $command = 'Error: can not find ' . $type . ' server';

            $query3->execute(array($row['jobID']));

        } else {
            $command = 'Error: unknown command';
        }

    } else {
        $command = 'Notice: Not the time to run command "Remove PXE boot from DHCP" (' . $row['name'] . ')';
    }

    $theOutput->printGraph($command);

}

print "\r\nApplyingPXE changes\r\n";
$res = (array) $rootObject->PXEFiles();

$query = $sql->prepare("INSERT INTO `jobs` (`action`,`date`,`status`,`api`,`type`,`affectedID`,`name`,`hostID`,`userID`,`extraData`,`resellerID`) VALUES ('rp',NOW(),NULL,'J',?,?,?,?,?,?,?)");
$query2 = $sql->prepare("UPDATE `virtualcontainer` SET `imageid`=? WHERE `id`=? LIMIT 1");
$query3 = $sql->prepare("UPDATE `rootsDedicated` SET `imageID`=? WHERE `dedicatedID`=? LIMIT 1");
foreach ($res as $row) {
    if (isset($row['type'])) {

        $query->execute(array($row['type'], $row['affectedID'], $row['name'], $row['hostID'], $row['userID'], json_encode($row['extraData']), $row['resellerID']));

        if ($row['type'] == 'de') {
            $query2->execute(array($row['imageID'], $row['affectedID']));
        } else {
            $query3->execute(array($row['imageID'], $row['affectedID']));
        }
    }
}

print "\r\nApplying DHCPchanges\r\n";
$rootObject->dhcpFiles();

print "\r\n(Re)starting/Stopping dedicated server\r\n";
#$rootObject->startStop();

print "\r\nAdding, Altering, Removing, (Re)starting and Stopping VMWare Virtual Container\r\n";
#$rootObject->VMWare();

print "\r\nRemoving VMWare Virtual Container/Dedicated Server from DB \r\n";

$query = $sql->prepare("DELETE FROM `rootsDedicated` WHERE `dedicatedID`=? LIMIT 1");
foreach ($removeIDs['de'] as $id) {
    customColumns('G', $id, 'del');
    $query->execute(array($id));
}

$query = $sql->prepare("DELETE FROM `virtualcontainer` WHERE `id`=? LIMIT 1");
foreach ($removeIDs['vs'] as $id) {
    customColumns('V', $id, 'del');
    $query->execute(array($id));
}

$rootObject = null;