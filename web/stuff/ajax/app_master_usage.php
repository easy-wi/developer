<?php

/**
 * File: app_master_usage.php.
 * Author: Ulrich Block
 * Date: 27.09.14
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

if (!defined('AJAXINCLUDED')) {
    die('Do not access directly!');
}

$sprache = getlanguagefile('gserver', $user_language, $resellerLockupID);

$maxSlots = 0;
$maxServer = 0;
$maxRam = 0;
$installedServer = 0;
$installedSlots = 0;
$installedRam = 0;
$c = 0;
$cores = array();
$ips = array();
$table = array();
$table2 = array();
$usedPorts = array();

$query = $sql->prepare("SELECT r.`connect_ip_only`,r.`ip`,r.`altips`,r.`maxslots`,r.`maxserver`,r.`ram`,r.`cores`,r.`hyperthreading`,r.`install_paths`,COUNT(g.`id`) AS `installedServer`,SUM(g.`slots`) AS `installedSlots`,SUM(g.`maxram`) AS `installedRam` FROM `rserverdata` AS r LEFT JOIN `gsswitch` AS g ON g.`rootID`=r.`id` WHERE r.`id`=? AND r.`resellerid`=? LIMIT 1");
$query->execute(array($ui->id('id', 10, 'get'), $resellerLockupID));
while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

    $maxSlots = (int) $row['maxslots'];
    $maxServer = (int) $row['maxserver'];
    $maxRam = (int) $row['ram'];
    $installedServer = (int) $row['installedServer'];
    $installedSlots = (int) $row['installedSlots'];
    $installedRam = (int) $row['installedRam'];

    if ($row['connect_ip_only'] != 'Y' and isip($row['ip'], 'ip4')) {
        $ips[] = $row['ip'];
    }

    foreach (preg_split('/\r\n/', $row['altips'], -1, PREG_SPLIT_NO_EMPTY) as $ip) {
        if (isip($ip, 'ip4')) {
            $ips[] = $ip;
        }
    }

    $coreCount = ($row['hyperthreading'] == 'Y') ? $row['cores'] * 2 : $row['cores'];

    for ($c = 0; $c < $coreCount; $c++) {
        $cores[$c] = 0;
    }

    $iniVars = parse_ini_string($row['install_paths'], true);

    if ($iniVars) {
        foreach ($iniVars as $key => $values) {

            $table2[] = $key;

            if (isset($values['default']) and $values['default'] == 1) {
                $homeDir = $key;
            }
        }
    }

    if (count($table2) == 0) {
        $table2[] = 'home';
    }
}

$query = $sql->prepare("SELECT t.`id`,t.`description`,t.`shorten` FROM `servertypes` t WHERE t.`resellerid`=? AND EXISTS (SELECT m.`id` FROM `rservermasterg` m WHERE m.`serverid`=? AND m.`servertypeid`=t.`id` LIMIT 1) ORDER BY t.`description` ASC");
$query->execute(array($resellerLockupID, $ui->id('id', 10, 'get')));
while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $table[$row['id']] = array('shorten' => $row['shorten'], 'description' => $row['description']);
}

$used = usedPorts(($ui->id('currentRootID', 10, 'get') == $ui->id('id', 10, 'get')) ? array($ui->ip4('currentIP', 'get')) : $ips);
$ports = (count($used['ports']) > 0) ? implode(', ', $used['ports']) : 'None';
$ip = $used['ip'];

$query = $sql->prepare("SELECT `cores`,`taskset` FROM `gsswitch` WHERE `rootID`=? AND `resellerid`=?");
$query->execute(array($ui->id('id', 10, 'get'), $resellerLockupID));
while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

    $ce = explode(',', $row['cores']);
    $cc = count($ce);

    if ($row['taskset'] == 'Y' and $cc > 0) {
        foreach ($ce as $uc) {
            $cores[$uc] = $cores[$uc] + round(1 / $cc, 2);
        }
    }
}

// During edit we need additional data
// To avoid PHP notice during add, we need to define up front
$currentIP = '';
$port = '';
$port2 = '';
$port3 = '';
$port4 = '';
$port5 = '';
$taskset = '';
$homeDir = (isset($homeDir)) ? $homeDir : '';
$usedCores = array();
$installedGames = array();

if ($ui->id('gameServerID', 10, 'get')) {

    $query = $sql->prepare("SELECT `serverip`,`port`,`port2`,`port3`,`port4`,`port5`,`taskset`,`cores`,`homeLabel` FROM `gsswitch` WHERE `id`=? AND `resellerid`=? LIMIT 1");
    $query->execute(array($ui->id('gameServerID', 10, 'get'), $resellerLockupID));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $currentIP = $row['serverip'];
        $port = $row['port'];
        $port2 = ($row['port2'] == 0) ? '' : $row['port2'];
        $port3 = ($row['port3'] == 0) ? '' : $row['port3'];
        $port4 = ($row['port4'] == 0) ? '' : $row['port4'];
        $port5 = ($row['port5'] == 0) ? '' : $row['port5'];
        $taskset = $row['taskset'];
        $homeDir = $row['homeLabel'];

        foreach (preg_split('/\,/', $row['cores'], -1, PREG_SPLIT_NO_EMPTY) as $core) {
            $usedCores[] = $core;
        }
    }

    $query = $sql->prepare("SELECT `servertype` FROM `serverlist` WHERE `switchID`=? AND `resellerid`=?");
    $query->execute(array($ui->id('gameServerID', 10, 'get'), $resellerLockupID));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $installedGames[] = $row['servertype'];
    }
}

require_once IncludeTemplate($template_to_use, 'ajax_admin_appserver_usage.tpl', 'ajax');