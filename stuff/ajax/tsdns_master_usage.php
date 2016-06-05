<?php

/**
 * File: tsdns_master_usage.php.
 * Author: Ulrich Block
 * Date: 21.02.15
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

if ($ui->id('id', 10, 'get')) {

    $dns = '';
    $installedServer = 0;
    $maxServer = 0;

    $query = $sql->prepare("SELECT COUNT(d.`dnsID`) AS `installed_server`,m.`max_dns`,m.`defaultdns` FROM `voice_tsdns` AS m LEFT JOIN `voice_dns` AS d ON d.`tsdnsID`=m.`id` WHERE m.`id`=? AND m.`resellerid`=? AND m.`active`='Y' GROUP BY m.`id` LIMIT 1");
    $query->execute(array($ui->id('id', 10, 'get'), $resellerLockupID));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $dns = $row['defaultdns'];
        $installedServer = (int) $row['installed_server'];
        $maxServer = (int) $row['max_dns'];
    }

    if ($ui->id('serverID', 10, 'get') and isset($masterServerData)) {
        $query = $sql->prepare("SELECT `dns` FROM `voice_dns` WHERE `dnsID`=? AND `resellerID`=? LIMIT 1");
        $query->execute(array($ui->id('serverID', 10, 'get'), $resellerLockupID));
        $dns = $query->fetchColumn();
    }
}

require_once IncludeTemplate($template_to_use, 'ajax_admin_tsdns_server_usage.tpl', 'ajax');