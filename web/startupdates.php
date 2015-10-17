<?php

/**
 * File: startupdates.php.
 * Author: Ulrich Block
 * Date: 05.01.13
 * Time: 12:20
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

if (isset($_SERVER['REMOTE_ADDR'])) {
    $ip = $_SERVER['REMOTE_ADDR'];

    $timelimit = (isset($_GET['timeout']) and is_numeric($_GET['timeout'])) ? $_GET['timeout'] : ini_get('max_execution_time') - 10;

} else {
    $timelimit = 600;
}

$allRoots = (isset($_GET['all_root'])) ? true : false;
$forceUpdate = (isset($_GET['force_update'])) ? true : false;

if (isset($argv)) {

    $args = array();

    foreach ($argv as $a) {
        if ($a == 'all_root') {
            $allRoots = true;
        } else if ($a == 'force_update') {
            $forceUpdate = true;
        }
    }
}

set_time_limit($timelimit);

define('EASYWIDIR', dirname(__FILE__));

include(EASYWIDIR . '/stuff/methods/vorlage.php');
include(EASYWIDIR . '/stuff/methods/class_validator.php');
include(EASYWIDIR . '/stuff/methods/functions.php');
include(EASYWIDIR . '/stuff/settings.php');
include(EASYWIDIR . '/stuff/methods/functions_ssh_exec.php');
include(EASYWIDIR . '/stuff/methods/class_masterserver.php');
include(EASYWIDIR . '/stuff/keyphrasefile.php');

if (!isset($ip) or $ui->escaped('SERVER_ADDR', 'server') == $ip or in_array($ip, ipstoarray($rSA['cronjob_ips']))) {

    $currentHour = date('G');
    $currentMinute = (int) date('i');

    echo "Start Syncs and Updates. Hour is ${currentHour} and minute is ${currentMinute}\r\n";

    if ($allRoots) {

        echo "Checking for all available and active servers\r\n";

        $query = $sql->prepare("SELECT `id`,`updates` FROM `rserverdata` WHERE `active`!='N'");
        $query->execute();

    } else {

        $query = $sql->prepare("SELECT `lastUpdateRun` FROM `settings` WHERE `resellerid`=0 LIMIT 1");
        $query->execute();
        $lastUpdateRun = (int) $query->fetchColumn();

        $query = $sql->prepare("UPDATE `settings` SET `lastUpdateRun`=? WHERE `resellerid`=0 LIMIT 1");
        $query->execute(array($currentMinute));

        echo "Checking for servers to be updated and or synced at hour ${currentHour} and between minutes ${lastUpdateRun} and ${currentMinute}\r\n";

        // avoid less/more OR equal in SQL. We want only less/more to eliminate the OR comparison
        $currentMinute++;
        $lastUpdateRun--;

        echo "Altered minutes for running a more efficient query will be  updateMinute > ${lastUpdateRun} AND updateMinute < ${currentMinute}\r\n";

        $query = $sql->prepare("SELECT `id`,`updates` FROM `rserverdata` WHERE (`alreadyStartedAt` IS NULL OR `alreadyStartedAt`!=?) AND `updateMinute`>? AND `updateMinute`<? AND  `active`!='N'");
        $query->execute(array($currentHour, $lastUpdateRun, $currentMinute));
    }

    $query2 = $sql->prepare("UPDATE `rserverdata` SET `alreadyStartedAt`=? WHERE `id`=? LIMIT 1");

    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

        $rootServer = new masterServer($row['id'], $aeskey);

        if ($row['updates'] == 3) {

            echo "Updates deactivated for: " . $rootServer->sship . "\r\n";

        } else {

            if (4 == $currentHour or $forceUpdate) {
                $rootServer->collectData(true, true, false);
            } else {
                $rootServer->collectData();
            }

            $sshReturn = $rootServer->sshConnectAndExecute();

            if ($sshReturn === false and $rootServer->updateAmount > 0) {

                echo "Updating failed for: " . $rootServer->sship . "\r\n";

            } else {

                if ($rootServer->updateAmount > 0) {

                    echo "Updater started for " . $rootServer->sship . "\r\n";

                    if (isset($dbConnect['debug']) and $dbConnect['debug'] == 1) {
                        print_r($rootServer->getCommands());
                    }

                } else {
                    echo "No updates to be executed for " . $rootServer->sship . "\r\n";
                }
            }

            $query2->execute(array($currentHour, $row['id']));

            $rootServer = null;
        }
    }

    $query = $sql->prepare("UPDATE `settings` SET `lastCronUpdates`=UNIX_TIMESTAMP()");
    $query->execute();
}