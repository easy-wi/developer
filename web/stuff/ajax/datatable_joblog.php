<?php

/**
 * File: datatable_joblog.php.
 * Author: Ulrich Block
 * Date: 20.09.14
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

$sprache = getlanguagefile('api', $user_language, $reseller_id);

$placeholders = array('de', 'ds', 'gs', 'wv', 'my', 'us', 'vo', 'vs');
$replace = array($gsprache->dedicated, 'TS3 DNS', $gsprache->gameserver, $gsprache->webspace, 'MySQL', $gsprache->user, $gsprache->voiceserver, $gsprache->virtual);

$placeholders2 = array('ad', 'dl', 'md', 'st', 're', 'rp', 'ri', 'rc');
$replace2 = array($gsprache->add, $gsprache->del, $gsprache->mod, 'Stop', '(Re)Start', 'Remove PXE from DHCP', $gsprache->reinstall, 'Recovery Mode');

if ($sSearch) {
    $sSearch = str_replace($replace, $placeholders, str_replace($replace2, $placeholders2, $sSearch));
}

if ($reseller_id == 0) {
    $query = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `jobs`");
    $query->execute();
} else {
    $query = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `jobs` WHERE `resellerID`=?");
    $query->execute(array($resellerLockupID));
}
$array['iTotalRecords'] = $query->fetchColumn();

if ($sSearch) {

    if ($reseller_id == 0) {
        $query = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `jobs` WHERE `date` LIKE :search OR `action` LIKE :search OR `name` LIKE :search OR `type` LIKE :search");
        $query->execute(array(':search' => '%' . $sSearch . '%'));
    } else {
        $query = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `jobs` WHERE `resellerID`=:resellerid AND (`date` LIKE :search OR `action` LIKE :search OR `name` LIKE :search OR `type` LIKE :search)");
        $query->execute(array(':search' => '%' . $sSearch . '%', ':resellerid' => $resellerLockupID));
    }

    $array['iTotalDisplayRecords'] = $query->fetchColumn();
} else {
    $array['iTotalDisplayRecords'] = $array['iTotalRecords'];
}

$orderFields = array(0 => '`date`', 1 => '`action`', 2 => '`status`', 3 => '`name`', 4 => '`type`');

if (isset($orderFields[$iSortCol]) and is_array($orderFields[$iSortCol])) {
    $orderBy = implode(' ' . $sSortDir . ', ', $orderFields[$iSortCol]) . ' ' . $sSortDir;
} else if (isset($orderFields[$iSortCol]) and !is_array($orderFields[$iSortCol])) {
    $orderBy = $orderFields[$iSortCol] . ' ' . $sSortDir;
} else {
    $orderBy = '`date` DESC';
}

if ($sSearch) {
    if ($reseller_id == 0) {
        $query = $sql->prepare("SELECT *,CASE WHEN `status`=3 THEN 4 ELSE `status` END AS `status` FROM `jobs` WHERE `date` LIKE :search OR `action` LIKE :search OR `name` LIKE :search OR `type` LIKE :search ORDER BY {$orderBy} LIMIT {$iDisplayStart},{$iDisplayLength}");
        $query->execute(array(':search' => '%' . $sSearch . '%'));
    } else {
        $query = $sql->prepare("SELECT *,CASE WHEN `status`=3 THEN 4 ELSE `status` END AS `status` FROM `jobs` WHERE `resellerID`=:resellerid AND (`date` LIKE :search OR `action` LIKE :search OR `name` LIKE :search OR `type` LIKE :search) ORDER BY {$orderBy} LIMIT {$iDisplayStart},{$iDisplayLength}");
        $query->execute(array(':search' => '%' . $sSearch . '%', ':resellerid' => $resellerLockupID));
    }
} else {
    if ($reseller_id == 0) {
        $query = $sql->prepare("SELECT *,CASE WHEN `status`=3 THEN 4 ELSE `status` END AS `status` FROM `jobs` ORDER BY {$orderBy} LIMIT {$iDisplayStart},{$iDisplayLength}");
        $query->execute();
    } else {
        $query = $sql->prepare("SELECT *,CASE WHEN `status`=3 THEN 4 ELSE `status` END AS `status` FROM `jobs` WHERE `resellerID`=? ORDER BY {$orderBy} LIMIT {$iDisplayStart},{$iDisplayLength}");
        $query->execute(array($resellerLockupID));
    }
}
while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $array['aaData'][] = array($row['date'], str_replace($placeholders2, $replace2, $row['action']), returnButton($template_to_use, 'ajax_admin_show_status.tpl', '', '', $row['status'], ''), $row['name'], str_replace($placeholders, $replace, $row['type']), returnButton($template_to_use, 'ajax_admin_job_checkbox.tpl', '', '', $row['jobID'], ''));
}