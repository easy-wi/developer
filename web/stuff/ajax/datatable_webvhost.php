<?php

/**
 * File: datatable_webvhost.php.
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

$query = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `webVhost` WHERE `resellerID`=?");
$query->execute(array($resellerLockupID));

$array['iTotalRecords'] = $query->fetchColumn();

if ($sSearch) {

    $userInQuery = (count($userIDs) > 0) ? ' OR `userID` IN (' . implode(',', $userIDs) . ')' : '';

    $toLower = strtolower($sSearch);

    $statusQuery = array();

    if (strpos(strtolower($gsprache->status_inactive), $toLower) !== false or strpos(strtolower($gsprache->status_stop), $toLower) !== false) {
        $statusQuery[] = "OR v.`active`='N'";
    }
    if (strpos(strtolower($gsprache->status_ok), $toLower) !== false) {
        $statusQuery[] = "OR v.`active`='Y'";
    }

    $statusQuery = (count($statusQuery) > 0) ? implode(' ', $statusQuery) : '';

    $query = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `webVhost` AS v LEFT JOIN `userdata` AS u ON v.`userID`=u.`id` WHERE v.`resellerID`=:reseller_id AND (v.`webVhostID` LIKE :search OR v.`dns` LIKE :search {$userInQuery} {$statusQuery})");
    $query->execute(array(':search' => '%' . $sSearch . '%', ':reseller_id' => $resellerLockupID));
    $array['iTotalDisplayRecords'] = $query->fetchColumn();

} else {
    $array['iTotalDisplayRecords'] = $array['iTotalRecords'];
}

$orderFields = array(0 => 'v.`dns`', 1 => 'v.`webVhostID`', 2 => 'v.`active`', 3 => 'd.`description`', 4 => 'u.`cname`', 5 => 'v.`jobPending`');

if (isset($orderFields[$iSortCol]) and is_array($orderFields[$iSortCol])) {
    $orderBy = implode(' ' . $sSortDir . ', ', $orderFields[$iSortCol]) . ' ' . $sSortDir;
} else if (isset($orderFields[$iSortCol]) and !is_array($orderFields[$iSortCol])) {
    $orderBy = $orderFields[$iSortCol] . ' ' . $sSortDir;
} else {
    $orderBy = 'v.`webVhostID` DESC';
}


if ($sSearch) {
    $query = $sql->prepare("SELECT v.`dns`,v.`webVhostID`,v.`active`,v.`jobPending`,v.`hdd`,v.`hddUsage`,v.`userID`,u.`cname`,CONCAT(u.`vname`,' ',u.`name`) AS `full_name` FROM `webVhost` AS v LEFT JOIN `userdata` AS u ON v.`userID`=u.`id` WHERE v.`resellerID`=:reseller_id AND (v.`webVhostID` LIKE :search OR v.`dns` LIKE :search {$userInQuery} {$statusQuery}) ORDER BY $orderBy LIMIT {$iDisplayStart},{$iDisplayLength}");
    $query->execute(array(':search' => '%' . $sSearch . '%', ':reseller_id' => $resellerLockupID));
} else {
    $query = $sql->prepare("SELECT v.`dns`,v.`webVhostID`,v.`active`,v.`jobPending`,v.`hdd`,v.`hddUsage`,v.`userID`,u.`cname`,CONCAT(u.`vname`,' ',u.`name`) AS `full_name` FROM `webVhost` AS v LEFT JOIN `userdata` AS u ON v.`userID`=u.`id` WHERE v.`resellerID`=? ORDER BY $orderBy LIMIT {$iDisplayStart},{$iDisplayLength}");
    $query->execute(array($resellerLockupID));
}

$query2 = $sql->prepare("SELECT `action`,`extraData` FROM `jobs` WHERE `affectedID`=? AND `resellerID`=? AND `type`='wv' AND (`status` IS NULL OR `status`=1) ORDER BY `jobID` DESC LIMIT 1");
$query3 = $sql->prepare("UPDATE `webVhost` SET `jobPending`='N' WHERE `webVhostID`=? AND `resellerID`=? LIMIT 1");

while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

    $jobPending = $gsprache->no;
    $status = 4;
    $statusMessage = $gsprache->status_ok;

    if ($row['jobPending'] == 'Y') {

        $query2->execute(array($row['id'], $resellerLockupID));
        while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {

            if ($row2['action'] == 'ad') {
                $jobPending = $gsprache->add;
            } else if ($row2['action'] == 'dl') {
                $jobPending = $gsprache->del;
            } else {
                $jobPending = $gsprache->mod;
            }

            $json = @json_decode($row2['extraData']);
            $row['status'] = ((is_object($json) and isset($json->newActive) and $json->newActive == 'N')) ? 2 : 0;
        }

        if ($query2->rowCount() == 0) {
            $query3->execute(array($row['id'], $resellerLockupID));
        }
    }

    if ($row['active'] == 'N') {
        $status = 3;
        $statusMessage = $gsprache->status_inactive;
    }

    $array['aaData'][] = array($row['dns'], $row['webVhostID'], returnButton($template_to_use, 'ajax_admin_show_status.tpl', '', '', $status, (string) $statusMessage), returnButton($template_to_use, 'ajax_admin_user_switch.tpl', $row['cname'], $row['full_name'], $row['userID'], ''), (int) $row['hddUsage'] . '/' . (int) $row['hdd'], (string) $jobPending, returnButton($template_to_use, 'ajax_admin_buttons_ri.tpl', 'wv', 'ri', $row['webVhostID'], $gsprache->reinstall) . ' ' . returnButton($template_to_use, 'ajax_admin_buttons_dl.tpl', 'wv', 'dl', $row['webVhostID'], $gsprache->del) . ' ' . returnButton($template_to_use, 'ajax_admin_buttons_md.tpl', 'wv', 'md', $row['webVhostID'], $gsprache->mod));
}