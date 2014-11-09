<?php

/**
 * File: jobs_gserver.php.
 * Author: Ulrich Block
 * Date: 05.08.12
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

$query = $sql->prepare("SELECT `hostID`,`resellerID` FROM `jobs` WHERE (`status` IS NULL OR `status`='1') AND `type`='gs' GROUP BY `hostID`");
$query2 = $sql->prepare("SELECT * FROM `jobs` WHERE (`status` IS NULL OR `status`='1') AND `type`='gs' AND `hostID`=?");
$query3 = $sql->prepare("SELECT `serverip`,`port`,`pallowed` FROM `gsswitch` WHERE `id`=? LIMIT 1");
$query4 = $sql->prepare("DELETE FROM `gsswitch` WHERE `id`=? LIMIT 1");
$query5 = $sql->prepare("UPDATE `jobs` SET `status`='3' WHERE `jobID`=? LIMIT 1");
$query6 = $sql->prepare("SELECT t.`shorten` FROM `serverlist` AS s INNER JOIN `servertypes` AS t ON t.`id`=s.`servertype` WHERE s.`switchID`=?");
$query7 = $sql->prepare("UPDATE `gsswitch` SET `serverip`=?,`port`=? WHERE `id`=? LIMIT 1");
$query8 = $sql->prepare("UPDATE `gsswitch` SET `jobPending`='N' WHERE `id`=? LIMIT 1");
$query9 = $sql->prepare("UPDATE `jobs` SET `status`='2' WHERE `jobID`=? LIMIT 1");

$query->execute();
while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

    // at web part set within settings.php; here in the job needs to be set on a root by root case
    $resellerLockupID = $row['resellerID'];

    $appServer = new AppServer($row['hostID']);

    $query2->execute(array($row['hostID']));

    while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {

        $appServer->getAppServerDetails($row2['affectedID']);

        $extraData = @json_decode($row2['extraData']);

        $query3->execute(array($row2['affectedID']));
        while ($row3 = $query3->fetch(PDO::FETCH_ASSOC)) {
            $gsIP = $row3['serverip'];
            $port = $row3['port'];
            $protectedAllowed = $row3['pallowed'];
        }

        if ($query3->rowCount() > 0) {

            if ($row2['action'] == 'dl') {

                $appServer->stopAppHard();
                $appServer->userCud('del', 'both');

                $query4->execute(array($row2['affectedID']));

                customColumns('G', $row2['affectedID'], 'del');

                $query5->execute(array($row2['jobID']));

                $command = $gsprache->del . ' gsswitchID: ' . $row2['affectedID'] . ' name:' . $row2['name'] . ' gsswitchID:' . $row2['affectedID'];

            } else if ($row2['action'] == 'ad' or $row2['action'] == 'md' and is_object($extraData)) {

                $installGames = (is_object($extraData) and property_exists($extraData, 'installGames') and preg_match('/[APN]/', $extraData->installGames)) ? $extraData->installGames : 'N';

                // userCrud will set the user to inactive
                $appServer->userCud('add');

                $appServer->setQuota();

                if ($row2['action'] == 'ad') {

                    if ($installGames == 'P') {

                        $appServer->addApp();

                    } else if ($installGames == 'A') {

                        $templates = array();

                        $query6->execute(array($row2['affectedID']));
                        while ($row = $query6->fetch(PDO::FETCH_ASSOC)) {
                            $templates[] = $row['shorten'];
                            $templates[] = $row['shorten'] . '-2';
                            $templates[] = $row['shorten'] . '-3';
                        }

                        $appServer->addApp($templates);
                    }

                    $command = $gsprache->add . ' gsswitchID: ' . $row2['affectedID'] . ' name:' . $row2['name'] . ' gsswitchID:' . $row2['affectedID'];

                } else {

                    $removeGames = (array) $extraData->gamesRemoveArray;

                    if (count($removeGames) > 0) {

                        $removeTemplates = array();

                        foreach ($removeGames as $game) {
                            $removeTemplates[] = $game;
                            $removeTemplates[] = $game . '-2';
                            $removeTemplates[] = $game . '-3';
                        }

                        $appServer->removeApp($removeTemplates);
                    }

                    // Send delete request for protected user in case it has been removed from server
                    if ($protectedAllowed == 'N' and isset($extraData->oldProtected) and $extraData->oldProtected == 'Y') {
                        $appServer->userCud('del');
                    }

                    $newPort = (isset($extraData->newPort) and strlen($extraData->newPort) > 0) ? $extraData->newPort : $port;
                    $newIP = (isset($extraData->newIP) and strlen($extraData->newIP) > 0) ? $extraData->newIP : $gsIP;

                    if ($port != $newPort or $gsIP != $newIP) {

                        $query7->execute(array($newIP, $newPort, $row2['affectedID']));

                        $appServer->moveServerLocal($row3['serverip'], $row3['port']);
                    }

                    $command = $gsprache->mod . ' gsswitchID: ' . $row2['affectedID'] . ' name: ' . $row2['name'] . ' gsswitchID:' . $row2['affectedID'];
                }

                $query8->execute(array($row2['affectedID']));

                $query5->execute(array($row2['jobID']));

            } else if ($row2['action'] == 're') {

                $appServer->startApp();

                $query5->execute(array($row2['jobID']));

                $command = '(Re)Start gsswitchID: ' . $row2['affectedID'] . ' name: ' . $row2['name'];

            } else if ($row2['action'] == 'st') {

                $appServer->stopApp();

                $query5->execute(array($row2['jobID']));

                $command = 'Stop gsswitchID: ' . $row2['affectedID'] . ' name: ' . $row2['name'];

            } else {

                $query9->execute(array($row2['jobID']));

                $command = 'Error: unknown command';
            }

        } else {

            $query9->execute(array($row2['jobID']));

            $command = 'Error: can not find gsswitchID';
        }

        $theOutput->printGraph($command);
    }

    $appServer->execute();

    if (isset($dbConnect['debug']) and $dbConnect['debug'] == 1) {
        print_r($appServer->debug());
    }
}