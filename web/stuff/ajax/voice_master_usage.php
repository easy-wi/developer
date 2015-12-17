<?php

/**
 * File: voice_master_usage.php.
 * Author: Ulrich Block
 * Date: 01.02.15
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

$sprache = getlanguagefile('voice', $user_language, $reseller_id);

$currentIP = '';
$dns = '';
$name = '';
$welcome = '';
$hostbanner_url = '';
$hostbanner_gfx_url = '';
$hostbutton_tooltip = '';
$hostbutton_url = '';
$hostbutton_gfx_url = '';
$flexSlots = '';
$flexSlotsFree = '';
$flexSlotsPercent = '';
$iniConfigurationMaster = array();
$iniConfigurationServer = new stdClass();

if ($ui->id('id', 10, 'get')) {

    $query = $sql->prepare("SELECT m.*,AES_DECRYPT(m.`querypassword`,?) AS `decryptedquerypassword`,COUNT(v.`id`)*(100/m.`maxserver`) AS `serverpercent`,SUM(v.`slots`)*(100/m.`maxslots`) AS `slotpercent`,COUNT(v.`id`) AS `installedserver`,SUM(v.`slots`) AS `installedslots`,SUM(v.`usedslots`) AS `uslots`,r.`ip`  FROM `voice_masterserver` m LEFT JOIN `rserverdata` r ON m.`rootid`=r.`id` LEFT JOIN `voice_server` v ON m.`id`=v.`masterserver` WHERE m.`id`=? AND m.`active`='Y' AND (m.`resellerid`=? OR m.`managedForID`=?) GROUP BY m.`id` HAVING (`installedserver`<`maxserver` AND (`installedslots`<`maxslots` OR `installedslots` IS NULL)) LIMIT 1");
    $query->execute(array($aeskey, $ui->id('id', 10, 'get'), $resellerLockupID, $admin_id));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

        $installedServer = (int) $row['installedserver'];
        $maxServer = (int) $row['maxserver'];
        $installedSlots = (int) $row['installedslots'];
        $maxSlots = (int) $row['maxslots'];

        $masterServerData['ssh2ip'] = $row['ssh2ip'];
        $masterServerData['queryport'] = $row['queryport'];
        $masterServerData['decryptedquerypassword'] = $row['decryptedquerypassword'];

        $ips = ($row['connect_ip_only'] == 'Y') ? array() : array($row['ssh2ip']);

        foreach (preg_split('/\r\n/', $row['ips'], -1, PREG_SPLIT_NO_EMPTY) as $ip) {
            $ips[] = $ip;
        }

        $dns = $row['defaultdns'];
        $name = $row['defaultname'];
        $welcome = $row['defaultwelcome'];
        $hostbanner_url = $row['defaulthostbanner_url'];
        $hostbanner_gfx_url = $row['defaulthostbanner_gfx_url'];
        $hostbutton_tooltip = $row['defaulthostbutton_tooltip'];
        $hostbutton_url = $row['defaulthostbutton_url'];
        $hostbutton_gfx_url = $row['defaulthostbutton_gfx_url'];
        $flexSlotsFree = $row['defaultFlexSlotsFree'];
        $flexSlotsPercent = $row['defaultFlexSlotsPercent'];

        $iniConfigurationMaster = @parse_ini_string($row['iniConfiguration'], true, INI_SCANNER_RAW);
   }

    if ($ui->id('serverID', 10, 'get') and isset($masterServerData)) {

        $query = $sql->prepare("SELECT `localserverid`,`ip`,`dns`,`flexSlots`,`flexSlotsPercent`,`flexSlotsFree`,`iniConfiguration` FROM `voice_server` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($ui->id('serverID', 10, 'get'), $resellerLockupID));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $localID = $row['localserverid'];
            $currentIP = $row['ip'];
            $dns = $row['dns'];
            $flexSlots = $row['flexSlots'];
            $flexSlotsPercent = $row['flexSlotsPercent'];
            $flexSlotsFree = $row['flexSlotsFree'];
            $iniConfigurationServer = @json_decode($row['iniConfiguration']);
        }

        $connection = new TS3($masterServerData['ssh2ip'], $masterServerData['queryport'], 'serveradmin', $masterServerData['decryptedquerypassword']);

        if (strpos($connection->errorcode,'error id=0') !== false and isset($localID)) {

            $serverDetails = $connection->ServerDetails($localID);

            $name = $serverDetails['virtualserver_name'];
            $welcome = $serverDetails['virtualserver_welcomemessage'];
            $hostbanner_url = $serverDetails['virtualserver_hostbanner_url'];
            $hostbanner_gfx_url = $serverDetails['virtualserver_hostbanner_gfx_url'];
            $hostbutton_tooltip = $serverDetails['virtualserver_hostbutton_tooltip'];
            $hostbutton_url = $serverDetails['virtualserver_hostbutton_url'];
            $hostbutton_gfx_url = $serverDetails['virtualserver_hostbutton_gfx_url'];
        }

        $connection->CloseConnection();
    }
}

require_once IncludeTemplate($template_to_use, 'ajax_admin_voice_server_usage.tpl', 'ajax');