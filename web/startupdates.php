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
    if (isset($_GET['timeout']) and is_numeric($_GET['timeout'])) {
        $timelimit = $_GET['timeout'];
    } else {
        $timelimit=ini_get('max_execution_time')-10;
    }
} else {
    $timelimit=600;
}
set_time_limit($timelimit);
if (!isset($ip) or $_SERVER['SERVER_ADDR'] == $ip) {
    define('EASYWIDIR', dirname(__FILE__));
    echo "Start Syncs and Updates loading...\r\n";
    include(EASYWIDIR . '/stuff/vorlage.php');
    include(EASYWIDIR . '/stuff/class_validator.php');
    include(EASYWIDIR . '/stuff/functions.php');
    include(EASYWIDIR . '/stuff/settings.php');
    include(EASYWIDIR . '/stuff/ssh_exec.php');
    include(EASYWIDIR . '/stuff/class_masterserver.php');
    include(EASYWIDIR . '/stuff/keyphrasefile.php');
    $currentHour=date('G');
    $currentMinute=(int)date('i');
    $query = $sql->prepare("SELECT `lastUpdateRun` FROM `settings` WHERE `resellerid`=0 LIMIT 1");
    $query->execute();
    $lastUpdateRun= (int) $query->fetchColumn();
    $query = $sql->prepare("UPDATE `settings` SET `lastUpdateRun`=? WHERE `resellerid`=0 LIMIT 1");
    $query->execute(array($currentMinute));
    echo "Checking for servers to be updated and or synced at hour ${currentHour} and between minutes ${lastUpdateRun} and ${currentMinute}\r\n";
    $currentMinute++;
    if ($lastUpdateRun != null and $lastUpdateRun != 0) $lastUpdateRun--;
    $query = $sql->prepare("SELECT `id` FROM `rserverdata` WHERE `updates`!=3 AND (`alreadyStartedAt` IS NULL OR `alreadyStartedAt`!=?) AND `updateMinute`>? AND `updateMinute`<?");
    $query2 = $sql->prepare("UPDATE `rserverdata` SET `alreadyStartedAt`=? WHERE `id`=? LIMIT 1");
    $query->execute(array($currentHour,$lastUpdateRun,($currentMinute+1)));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $rootServer=new masterServer($row['id'],$aeskey);
        $rootServer->collectData();
        echo "Starting updates for ".$rootServer->sship."\r\n";
        $sshcmd = $rootServer->returnCmds();
        if ($rootServer->sshcmd!==null) {
            $update=ssh2_execute('gs', $row['id'],$rootServer->sshcmd);
            if ($update !== false) {
                $rootServer->setUpdating();
                echo "Updater started for ".$rootServer->sship."\r\n";
            } else {
                echo "Updating failed for: ".$rootServer->sship."\r\n";
            }
        }
        $query2->execute(array($currentHour, $row['id']));
        unset($rootServer);
    }
    $query = $sql->prepare("UPDATE `settings` SET `lastCronUpdates`=UNIX_TIMESTAMP() WHERE `resellerid`=0 LIMIT 1");
    $query->execute();
}