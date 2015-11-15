<?php

/**
 * File: datatable_mysqlserver.php.
 * Author: Ulrich Block
 * Date: 22.02.15
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

$query = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `mysql_external_servers` WHERE `resellerid`=?");
$query->execute(array($resellerLockupID));

$array['iTotalRecords'] = $query->fetchColumn();

if ($sSearch) {

    $toLower = strtolower($sSearch);

    $statusQuery = array();

    if (strpos(strtolower($gsprache->status_inactive), $toLower) !== false or strpos(strtolower($gsprache->status_stop), $toLower) !== false) {
        $statusQuery[] = "OR s.`active`='N'";
    }
    if (strpos(strtolower($gsprache->status_ok), $toLower) !== false) {
        $statusQuery[] = "OR s.`active`='Y'";
    }

    $statusQuery = (count($statusQuery) > 0) ? implode(' ', $statusQuery) : '';

    $query = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `mysql_external_servers` AS s WHERE s.`resellerid`=:reseller_id AND (s.`id` LIKE :search OR s.`ip` LIKE :search OR s.`interface` LIKE :search {$statusQuery})");
    $query->execute(array(':search' => '%' . $sSearch . '%',':reseller_id' => $resellerLockupID));
    $array['iTotalDisplayRecords'] = $query->fetchColumn();
} else {
    $array['iTotalDisplayRecords'] = $array['iTotalRecords'];
}

$orderFields = array(0 => 's.`ip`', 1 => 's.`id`', 2 => 's.`interface`', 3 => array('`installed_databases`', 's.`max_databases`'));

if (isset($orderFields[$iSortCol]) and is_array($orderFields[$iSortCol])) {
    $orderBy = implode(' ' . $sSortDir . ', ', $orderFields[$iSortCol]) . ' ' . $sSortDir;
} else if (isset($orderFields[$iSortCol]) and !is_array($orderFields[$iSortCol])) {
    $orderBy = $orderFields[$iSortCol] . ' ' . $sSortDir;
} else {
    $orderBy = 's.`id` ASC';
}

if ($sSearch) {
    $query = $sql->prepare("SELECT s.`id`,s.`active`,s.`ip`,s.`interface`,s.`max_databases`,(SELECT COUNT(1) AS `amount` FROM `mysql_external_dbs` AS d WHERE d.`sid`=s.`id`) AS `installed_databases` FROM `mysql_external_servers` AS s WHERE s.`resellerid`=:reseller_id AND (s.`id` LIKE :search OR s.`ip` LIKE :search OR s.`interface` LIKE :search {$statusQuery}) ORDER BY $orderBy LIMIT {$iDisplayStart},{$iDisplayLength}");
    $query->execute(array(':search' => '%' . $sSearch . '%', ':reseller_id' => $resellerLockupID));
} else {
    $query = $sql->prepare("SELECT s.`id`,s.`active`,s.`ip`,s.`interface`,s.`max_databases`,(SELECT COUNT(1) AS `amount` FROM `mysql_external_dbs` AS d WHERE d.`sid`=s.`id`) AS `installed_databases` FROM `mysql_external_servers` AS s WHERE s.`resellerid`=? ORDER BY $orderBy LIMIT {$iDisplayStart},{$iDisplayLength}");
    $query->execute(array($resellerLockupID));
}

while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

    $status = 4;
    $statusMessage = $gsprache->status_ok;

    if ($row['active'] == 'N') {
        $status = 3;
        $statusMessage = $gsprache->status_inactive;
    }

    $array['aaData'][] = array($row['ip'], $row['id'], returnButton($template_to_use, 'ajax_admin_show_status.tpl', '', '', $status, (string) $statusMessage), '<a href="' . $row['interface'] . '" target="_blank">' . $row['interface'] . '</a>', (int) $row['installed_databases'] . '/' . (int) $row['max_databases'], returnButton($template_to_use, 'ajax_admin_buttons_ri.tpl', 'my', 'ri', $row['id'], $gsprache->reinstall) . ' ' . returnButton($template_to_use, 'ajax_admin_buttons_dl.tpl', 'my', 'dl', $row['id'], $gsprache->del) . ' ' . returnButton($template_to_use, 'ajax_admin_buttons_md.tpl', 'my', 'md', $row['id'], $gsprache->mod));
}