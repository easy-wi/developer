<?php

/**
 * File: jobs_webspace.php.
 * Author: Ulrich Block
 * Date: 09.03.14
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

$query = $sql->prepare("SELECT `hostID`,MAX(`resellerID`) AS `resellerID` FROM `jobs` WHERE (`status` IS NULL OR `status`='1') AND `type`='wv' GROUP BY `hostID`");
$query2 = $sql->prepare("SELECT * FROM `jobs` WHERE (`status` IS NULL OR `status`='1') AND `type`='wv' AND `hostID`=?");
$query3 = $sql->prepare("DELETE FROM `webVhost` WHERE `webVhostID`=? LIMIT 1");
$query4 = $sql->prepare("SELECT `active` FROM `webVhost` WHERE `webVhostID`=? LIMIT 1");
$query5 = $sql->prepare("UPDATE `jobs` SET `status`='3' WHERE `jobID`=? LIMIT 1");
$query6 = $sql->prepare("UPDATE `webVhost` SET `jobPending`='N' WHERE `webVhostID`=? LIMIT 1");
$query7 = $sql->prepare("UPDATE `jobs` SET `status`='1' WHERE (`status` IS NULL OR `status`='1') AND `type`='wv' AND `hostID`=?");
$query8 = $sql->prepare("UPDATE `jobs` SET `action`='dl' WHERE `hostID`=? AND `type`='wv'");

$query->execute();
while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

    $vhostObject = new HttpdManagement($row['hostID'], $row['resellerID']);

    if (($vhostObject != false and $vhostObject->ssh2Connect() and $vhostObject->sftpConnect()) or $vhostObject->masterNotfound) {

        if ($vhostObject->masterNotfound) {
            $query8->execute(array($row['hostID']));
        }

        $query2->execute(array($row['hostID']));
        while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {

            $extraData = @json_decode($row2['extraData']);

            if ($row2['action'] == 'dl') {

                if (!$vhostObject->masterNotfound) {
                    $vhostObject->vhostDelete($row2['affectedID']);
                }

                $query3->execute(array($row2['affectedID']));

            } else if ($row2['action'] == 'ad') {

                $vhostObject->vhostCreate($row2['affectedID']);

            } else if ($row2['action'] == 'md') {

                $query4->execute(array($row2['affectedID']));
                $active = $query4->fetchColumn();

                if ($active == 'N' or (property_exists($extraData, 'newActive') and $extraData->newActive == 'N')) {
                    $vhostObject->setInactive($row2['affectedID']);
                } else {
                    $vhostObject->vhostMod($row2['affectedID']);
                }

            } else if ($row2['action'] == 'ri') {

                $vhostObject->vhostReinstall($row2['affectedID']);

            }

            $query5->execute(array($row2['jobID']));
            $query6->execute(array($row2['affectedID']));
        }

        $vhostObject->restartHttpdServer();

    } else {
        $theOutput->printGraph('cannot connect to web host with ID: ' . $row['hostID']);
        $query7->execute(array($row['hostID']));
    }
}