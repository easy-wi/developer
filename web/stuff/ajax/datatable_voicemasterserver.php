<?php

/**
 * File: datatable_voicemasterserver.php.
 * Author: Ulrich Block
 * Date: 18.01.15
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

$sprache = getlanguagefile('voice', $user_language, $resellerLockupID);

$query = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `voice_masterserver` WHERE `resellerid`=? OR `managedForID`=?");
$query->execute(array($resellerLockupID, $resellerLockupID));

$array['iTotalRecords'] = $query->fetchColumn();

if ($sSearch) {

    $query = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `voice_masterserver`  WHERE (`resellerid`=:reseller_id OR `managedForID`=:reseller_id) AND (`ssh2ip` LIKE :search OR `id` LIKE :search OR `description` LIKE :search)");
    $query->execute(array(':search' => '%' . $sSearch . '%', ':reseller_id' => $resellerLockupID));
    $array['iTotalDisplayRecords'] = $query->fetchColumn();

} else {
    $array['iTotalDisplayRecords'] = $array['iTotalRecords'];
}

$orderFields = array(0 => '`ssh2ip`', 1 => 'm.`id`', 2 => 'm.`active`', 3 => '`description`', 4 => '`installedserver`', 5 => '`installedslots`');

if (isset($orderFields[$iSortCol]) and is_array($orderFields[$iSortCol])) {
    $orderBy = implode(' ' . $sSortDir . ', ', $orderFields[$iSortCol]) . ' ' . $sSortDir;
} else if (isset($orderFields[$iSortCol]) and !is_array($orderFields[$iSortCol])) {
    $orderBy = $orderFields[$iSortCol] . ' ' . $sSortDir;
} else {
    $orderBy = 'm.`id` DESC';
}

if ($sSearch) {
    $query = $sql->prepare("SELECT m.*,COUNT(s.`id`) AS `installedserver`,SUM(s.`slots`) AS `installedslots`,SUM(s.`usedslots`) AS `uslots` FROM `voice_masterserver` AS m LEFT JOIN `voice_server` s ON m.`id`=s.`masterserver` WHERE (m.`resellerid`=:reseller_id OR m.`managedForID`=:reseller_id) AND (m.`ssh2ip` LIKE :search OR m.`id` LIKE :search OR m.`description` LIKE :search) GROUP BY m.`id` ORDER BY $orderBy LIMIT {$iDisplayStart},{$iDisplayLength}");
    $query->execute(array(':search' => '%' . $sSearch . '%', ':reseller_id' => $resellerLockupID));
} else {
    $query = $sql->prepare("SELECT m.*,COUNT(s.`id`) AS `installedserver`,SUM(s.`slots`) AS `installedslots`,SUM(s.`usedslots`) AS `uslots` FROM `voice_masterserver` AS m LEFT JOIN `voice_server` s ON m.`id`=s.`masterserver` WHERE (m.`resellerid`=? OR m.`managedForID`=?) GROUP BY m.`id` ORDER BY $orderBy LIMIT {$iDisplayStart},{$iDisplayLength}");
    $query->execute(array($resellerLockupID, $resellerLockupID));
}

while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

    $status = 4;
    $statusMessage = $gsprache->status_ok;

    if (preg_match('/^([\d]{1,2}.)*[\d]{1,2}$/', $row['local_version']) and preg_match('/^([\d]{1,2}.)*[\d]{1,2}$/', $row['latest_version']) and $row['local_version'] != $row['latest_version']) {
        $status = 2;
        $statusMessage = $sprache->old_version . ' ' . $row['local_version'];
    }

    if ($row['active'] == 'N') {
        $status = 3;
        $statusMessage = $gsprache->status_inactive;
    }

    $installedslots = ($row['installedslots'] == null) ? 0 : $row['installedslots'];

    $aaDataArray = array($row['ssh2ip'], $row['id'], returnButton($template_to_use, 'ajax_admin_show_status.tpl', '', '', $status, (string) $statusMessage), $row['description'], $row['installedserver'] . '/' . $row['maxserver'], $installedslots . '/' . $row['maxslots']);
    $aaDataArray[] = ($row['managedServer'] == 'N' or $reseller_id == 0) ? returnButton($template_to_use, 'ajax_admin_buttons_ri.tpl', 'vm', 'ri', $row['id'], $sprache->import) . ' ' . returnButton($template_to_use, 'ajax_admin_buttons_dl.tpl', 'vm', 'dl', $row['id'], $gsprache->del) . ' ' . returnButton($template_to_use, 'ajax_admin_buttons_md.tpl', 'vm', 'md', $row['id'], $gsprache->mod) : '';

    $array['aaData'][] = $aaDataArray;
}