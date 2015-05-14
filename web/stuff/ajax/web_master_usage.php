<?php

/**
 * File: web_master_usage.php.
 * Author: Ulrich Block
 * Date: 14.03.15
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

$sprache = getlanguagefile('web', $user_language, $resellerLockupID);

$maxVhost = 0;
$maxHDD = 0;
$webVhosts = 0;
$leftHDD = 0;
$totalHDD = 0;
$totalVhosts = 0;
$maxHDD = 1000;
$quotaActive = 'N';
$ownVhost = 'N';
$usageType = 'F';
$dns = array();
$phpConfigurationMaster = array();
$phpConfigurationVhost = new stdClass();

$query = $sql->prepare("SELECT m.`vhostTemplate`,m.`maxVhost`,m.`maxHDD`,m.`quotaActive`,m.`defaultdns`,m.`usageType`,m.`phpConfiguration`,(SELECT COUNT(v.`webVhostID`) AS `a` FROM `webVhost` AS v WHERE v.`webMasterID`=m.`webMasterID`) AS `totalVhosts`,(SELECT SUM(v.`hdd`) AS `a` FROM `webVhost` AS v WHERE v.`webMasterID`=m.`webMasterID`) AS `totalHDD` FROM `webMaster` AS m WHERE m.`webMasterID`=? AND m.`resellerID`=? LIMIT 1");
$query->execute(array($ui->id('id', 10, 'get'), $resellerLockupID));
while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $vhostTemplate = $row['vhostTemplate'];
    $maxVhost = (int) $row['maxVhost'];
    $maxHDD = (int) $row['maxHDD'];
    $totalVhosts = (int) $row['totalVhosts'];
    $leftHDD = (int) $row['maxHDD'] - $row['totalHDD'];
    $quotaActive = $row['quotaActive'];
    $dns[] = array('domain' => $row['defaultdns'], 'path' => '', 'ownVhost' => 'N', 'vhostTemplate' => $row['vhostTemplate']);
    $usageType = $row['usageType'];
    $phpConfigurationMaster = @parse_ini_string($row['phpConfiguration'], true, INI_SCANNER_RAW);
}

// Edit mode will provide the webhost ID
if ($ui->id('serverID', 10, 'get')) {

    $query = $sql->prepare("SELECT `hdd`,`phpConfiguration` FROM `webVhost` WHERE `webVhostID`=? AND `resellerID`=? LIMIT 1");
    $query->execute(array($ui->id('serverID', 10, 'get'), $resellerLockupID));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $maxHDD = $row['hdd'];
        $phpConfigurationVhost = @json_decode($row['phpConfiguration']);
    }

    $query = $sql->prepare("SELECT `domain`,`path`,`ownVhost`,`vhostTemplate` FROM `webVhostDomain` WHERE `webVhostID`=? AND `resellerID`=? ORDER BY `domain`");
    $query->execute(array($ui->id('serverID', 10, 'get'), $resellerLockupID));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $dns[] = array('domain' => $row['domain'], 'path' => $row['path'], 'ownVhost' => $row['ownVhost'], 'vhostTemplate' => $row['vhostTemplate']);
    }

    // Remove the first default entry, if others are given
    if (count($dns) > 1) {
        unset($dns[0]);
    } else {
        $dns[0]['domain'] = 'web-' . $ui->id('serverID', 10, 'get') . '.' . $dns[0]['domain'];
    }
}

require_once IncludeTemplate($template_to_use, 'ajax_admin_web_master.tpl', 'ajax');