<?php

/**
 * File: datatable_tsdnsmasterserver.php.
 * Author: Ulrich Block
 * Date: 25.01.15
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

$query = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `voice_tsdns` WHERE `resellerid`=?");
$query->execute(array($resellerLockupID));

$array['iTotalRecords'] = $query->fetchColumn();

if ($sSearch) {

    $query = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `voice_tsdns`  WHERE `resellerid`=:reseller_id AND (`ssh2ip` LIKE :search OR `id` LIKE :search OR `description` LIKE :search OR `defaultdns` LIKE :search)");
    $query->execute(array(':search' => '%' . $sSearch . '%', ':reseller_id' => $resellerLockupID));
    $array['iTotalDisplayRecords'] = $query->fetchColumn();

} else {
    $array['iTotalDisplayRecords'] = $array['iTotalRecords'];
}

$orderFields = array(0 => '`ssh2ip`', 1 => 'm.`id`', 2 => 'm.`active`', 3 => '`description`', 4 => '`defaultdns`', 5 => '`installed_dns`');

if (isset($orderFields[$iSortCol]) and is_array($orderFields[$iSortCol])) {
    $orderBy = implode(' ' . $sSortDir . ', ', $orderFields[$iSortCol]) . ' ' . $sSortDir;
} else if (isset($orderFields[$iSortCol]) and !is_array($orderFields[$iSortCol])) {
    $orderBy = $orderFields[$iSortCol] . ' ' . $sSortDir;
} else {
    $orderBy = 'm.`id` DESC';
}

if ($sSearch) {
    $query = $sql->prepare("SELECT m.*,COUNT(d.`dnsID`) AS `installed_dns` FROM `voice_tsdns` AS m LEFT JOIN `voice_dns` d ON d.`tsdnsID`=m.`id` WHERE m.`resellerid`=:reseller_id AND (m.`ssh2ip` LIKE :search OR m.`id` LIKE :search OR m.`description` LIKE :search OR `defaultdns` LIKE :search) GROUP BY m.`id` ORDER BY $orderBy LIMIT {$iDisplayStart},{$iDisplayLength}");
    $query->execute(array(':search' => '%' . $sSearch . '%', ':reseller_id' => $resellerLockupID));
} else {
    $query = $sql->prepare("SELECT m.*,COUNT(d.`dnsID`) AS `installed_dns` FROM `voice_tsdns` AS m LEFT JOIN `voice_dns` d ON d.`tsdnsID`=m.`id` WHERE m.`resellerid`=? GROUP BY m.`id` ORDER BY $orderBy LIMIT {$iDisplayStart},{$iDisplayLength}");
    $query->execute(array($resellerLockupID));
}

while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

    $status = 4;
    $statusMessage = $gsprache->status_ok;

    if ($row['active'] == 'N') {
        $status = 3;
        $statusMessage = $gsprache->status_inactive;
    }

    $array['aaData'][] = array($row['ssh2ip'], $row['id'], returnButton($template_to_use, 'ajax_admin_show_status.tpl', '', '', $status, (string) $statusMessage), $row['description'], $row['defaultdns'], $row['installed_dns'] . '/' . $row['max_dns'], returnButton($template_to_use, 'ajax_admin_buttons_ri.tpl', 'vd', 'ip', $row['id'], $sprache->import) . ' ' . returnButton($template_to_use, 'ajax_admin_buttons_dl.tpl', 'vd', 'dl', $row['id'], $gsprache->del) . ' ' . returnButton($template_to_use, 'ajax_admin_buttons_md.tpl', 'vd', 'md', $row['id'], $gsprache->mod));
}