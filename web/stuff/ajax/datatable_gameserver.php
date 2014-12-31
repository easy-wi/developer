<?php

/**
 * File: datatable_gameserver.php.
 * Author: Ulrich Block
 * Date: 26.09.14
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

$query = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `gsswitch` WHERE `resellerid`=?");
$query->execute(array($resellerLockupID));

$array['iTotalRecords'] = $query->fetchColumn();

if ($sSearch) {

    $userInQuery = (count($userIDs) > 0) ? ' OR `userid` IN (' . implode(',', $userIDs) . ')' : '';

    $toLower = strtolower($sSearch);

    $statusQuery = array();

    if (strpos(strtolower($gsprache->status_crashed), $toLower) !== false) {
        $statusQuery[] = 'OR `status`=2';
    }
    if (strpos(strtolower($gsprache->status_inactive), $toLower) !== false or strpos(strtolower($gsprache->status_stop), $toLower) !== false) {
        $statusQuery[] = 'OR `status`=3';
    }
    if (strpos(strtolower($gsprache->status_ok), $toLower) !== false) {
        $statusQuery[] = 'OR `status`=0';
    }
    if (strpos(strtolower($gsprache->status_password), $toLower) !== false or strpos(strtolower($gsprache->status_server_tag), $toLower) !== false) {
        $statusQuery[] = 'OR `status`=1';
    }

    $statusQuery = (count($statusQuery) > 0) ? implode(' ', $statusQuery) : '';

    $query = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `gsswitch` AS g JOIN (SELECT g.`id`,g.`userid`,t.`shorten`,CONCAT(`serverip`,'',`port`) AS `address`,CASE WHEN g.`active` = 'N' OR g.`stopped` = 'Y' THEN 3 WHEN g.`queryName` = 'OFFLINE' AND g.`stopped` = 'N' AND g.`notified`>=:downchecks THEN 2 WHEN (g.`war` = 'Y' AND g.`queryPassword` = 'N') OR (g.`brandname` = 'Y' AND LOWER(g.`queryName`) NOT LIKE :brandname) THEN 1 ELSE 0 END AS `status` FROM `gsswitch` AS g LEFT JOIN `serverlist` AS s ON g.`serverid`=s.`id` LEFT JOIN `servertypes` AS t ON s.`servertype`=t.`id` LEFT JOIN `userdata` AS u ON g.`userid`=u.`id` WHERE g.`resellerid`=:reseller_id HAVING (g.`id` LIKE :search OR t.`shorten` LIKE :search OR `address` LIKE :search {$userInQuery} {$statusQuery})) AS g2 ON g2.`id`=g.`id`");
    $query->execute(array(':downchecks' => $rSA['down_checks'], ':brandname' => '%'. strtolower($rSA['brandname']) . '%', ':search' => '%' . $sSearch . '%', ':reseller_id' => $resellerLockupID));
    $array['iTotalDisplayRecords'] = $query->fetchColumn();

} else {
    $array['iTotalDisplayRecords'] = $array['iTotalRecords'];
}

$orderFields = array(0 => '`address`', 1 => 'g.`id`', 2 => '`status`', 3 => '`full_name`');

if (isset($orderFields[$iSortCol]) and is_array($orderFields[$iSortCol])) {
    $orderBy = implode(' ' . $sSortDir . ', ', $orderFields[$iSortCol]) . ' ' . $sSortDir;
} else if (isset($orderFields[$iSortCol]) and !is_array($orderFields[$iSortCol])) {
    $orderBy = $orderFields[$iSortCol] . ' ' . $sSortDir;
} else {
    $orderBy = 'g.`id` DESC';
}

if ($sSearch) {
    $query = $sql->prepare("SELECT g.`id`,g.`notified`,CONCAT(g.`serverip`,':',g.`port`) AS `address`,g.`active`,g.`stopped`,g.`queryName`,g.`queryPassword`,g.`war`,g.`brandname`,g.`userid`,g.`jobPending`,t.`shorten`,u.`cname`,CONCAT(u.`name`,' ',u.`vname`) AS `full_name`,CASE WHEN g.`active` = 'N' OR g.`stopped` = 'Y' THEN 3 WHEN g.`queryName` = 'OFFLINE' AND g.`stopped` = 'N' AND g.`notified`>=:downchecks THEN 2 WHEN (g.`war` = 'Y' AND g.`queryPassword` = 'N') OR (g.`brandname` = 'Y' AND LOWER(g.`queryName`) NOT LIKE :brandname) THEN 1 ELSE 0 END AS `status` FROM `gsswitch` AS g LEFT JOIN `serverlist` AS s ON g.`serverid`=s.`id` LEFT JOIN `servertypes` AS t ON s.`servertype`=t.`id` LEFT JOIN `userdata` AS u ON g.`userid`=u.`id` WHERE g.`resellerid`=:reseller_id HAVING (g.`id` LIKE :search OR t.`shorten` LIKE :search OR `address` LIKE :search {$userInQuery} {$statusQuery}) ORDER BY $orderBy LIMIT {$iDisplayStart},{$iDisplayLength}");
    $query->execute(array(':downchecks' => $rSA['down_checks'], ':brandname' => '%'. strtolower($rSA['brandname']) . '%', ':search' => '%' . $sSearch . '%', ':reseller_id' => $resellerLockupID));
} else {
    $query = $sql->prepare("SELECT g.`id`,g.`notified`,CONCAT(g.`serverip`,':',g.`port`) AS `address`,g.`active`,g.`stopped`,g.`queryName`,g.`queryPassword`,g.`war`,g.`brandname`,g.`userid`,g.`jobPending`,t.`shorten`,u.`cname`,CONCAT(u.`name`,' ',u.`vname`) AS `full_name`,CASE WHEN g.`active` = 'N' OR g.`stopped` = 'Y' THEN 3 WHEN g.`queryName` = 'OFFLINE' AND g.`stopped` = 'N' AND g.`notified`>=? THEN 2 WHEN (g.`war` = 'Y' AND g.`queryPassword` = 'N') OR (g.`brandname` = 'Y' AND LOWER(g.`queryName`) NOT LIKE ?) THEN 1 ELSE 0 END AS `status` FROM `gsswitch` AS g LEFT JOIN `serverlist` AS s ON g.`serverid`=s.`id` LEFT JOIN `servertypes` AS t ON s.`servertype`=t.`id` LEFT JOIN `userdata` AS u ON g.`userid`=u.`id` WHERE g.`resellerid`=? ORDER BY $orderBy LIMIT {$iDisplayStart},{$iDisplayLength}");
    $query->execute(array($rSA['down_checks'], '%'. strtolower($rSA['brandname']) . '%', $resellerLockupID));
}

$query2 = $sql->prepare("SELECT `action`,`extraData` FROM `jobs` WHERE `affectedID`=? AND `resellerID`=? AND `type`='gs' AND (`status` IS NULL OR `status`=1) ORDER BY `jobID` DESC LIMIT 1");

while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

    $tobeActive = false;
    $jobPending = $gsprache->no;
    $statusMessage = $gsprache->status_ok;

    if (isset($row['jobPending']) and $row['jobPending'] == 'Y') {

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
            $tobeActive = (is_object($json) and isset($json->newActive)) ? $json->newActive : 'N';
        }
    }

    if ($row['active'] == 'N') {

        $statusMessage = $gsprache->status_inactive;

    } else if ($row['stopped'] == 'Y') {

        $statusMessage = $gsprache->status_stop;

    } else if ($row['queryName'] == 'OFFLINE' and $row['notified'] >= $rSA['down_checks'] and $row['stopped'] == 'N') {

        $statusMessage = $gsprache->status_crashed;

    } else if ($row['war'] == 'Y' and $row['queryPassword'] == 'N') {

        $statusMessage = $gsprache->status_password;

    } else if ($row['brandname'] == 'Y' and $rSA['brandname'] != null and $rSA['brandname'] != '' and strpos(strtolower($row['queryName']), strtolower($rSA['brandname'])) === false) {

        $statusMessage = $gsprache->status_server_tag;
    }

    $array['aaData'][] = array(returnButton($template_to_use, 'ajax_admin_gameserver_icon.tpl', $row['shorten'], '', '', '') . ' ' . $row['address'], $row['id'], returnButton($template_to_use, 'ajax_admin_show_status.tpl', '', '', $row['status'], (string) $statusMessage), returnButton($template_to_use, 'ajax_admin_user_switch.tpl', $row['cname'], $row['full_name'], $row['userid'], ''), (string) $jobPending, returnButton($template_to_use, 'ajax_admin_buttons_rs.tpl', 'gs', 'rs', $row['id'], $gsprache->start) . ' ' . returnButton($template_to_use, 'ajax_admin_buttons_st.tpl', 'gs', 'st', $row['id'], $gsprache->stop), returnButton($template_to_use, 'ajax_admin_buttons_ri.tpl', 'gs', 'ri', $row['id'], $gsprache->reinstall) . ' ' . returnButton($template_to_use, 'ajax_admin_buttons_dl.tpl', 'gs', 'dl', $row['id'], $gsprache->del) . ' ' . returnButton($template_to_use, 'ajax_admin_buttons_md.tpl', 'gs', 'md', $row['id'], $gsprache->mod));
}