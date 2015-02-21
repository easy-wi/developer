<?php

/**
 * File: datatable_voiceserver.php.
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

$query = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `voice_server` WHERE `resellerid`=?");
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

    $query = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `voice_server` AS v JOIN (SELECT s.`id`,s.`userid`,CASE WHEN s.`active` = 'N' OR s.`uptime` < 2 THEN 3 WHEN s.`password` = 'Y' AND s.`queryPassword` = 'N' THEN 1 ELSE 0 END AS `status`,CASE WHEN m.`usedns`='Y' AND `dns` IS NOT NULL AND `dns`!='' THEN CONCAT(`ip`,':',`port`,' (',`dns`,')') ELSE CONCAT(`ip`,':',`port`) END AS `server` FROM `voice_server` AS s LEFT JOIN `voice_masterserver` m ON s.`masterserver`=m.`id` LEFT JOIN `userdata` AS u ON s.`userid`=u.`id` WHERE s.`resellerid`=:reseller_id HAVING (s.`id` LIKE :search OR `server` LIKE :search {$userInQuery} {$statusQuery})) AS v2 ON v2.`id`=v.`id`");
    $query->execute(array(':search' => '%' . $sSearch . '%', ':reseller_id' => $resellerLockupID));
    $array['iTotalDisplayRecords'] = $query->fetchColumn();

} else {
    $array['iTotalDisplayRecords'] = $array['iTotalRecords'];
}

$orderFields = array(0 => '`server`', 1 => 'v.`id`', 2 => '`status`', 3 => 'v.`localserverid`', 4 => 'u.`cname`');

if (isset($orderFields[$iSortCol]) and is_array($orderFields[$iSortCol])) {
    $orderBy = implode(' ' . $sSortDir . ', ', $orderFields[$iSortCol]) . ' ' . $sSortDir;
} else if (isset($orderFields[$iSortCol]) and !is_array($orderFields[$iSortCol])) {
    $orderBy = $orderFields[$iSortCol] . ' ' . $sSortDir;
} else {
    $orderBy = 'v.`id` DESC';
}

if ($sSearch) {
    $query = $sql->prepare("SELECT v.*,m.`usedns`,u.`cname`,CONCAT(u.`name`,' ',u.`vname`) AS `full_name`,CASE WHEN v.`active` = 'N' OR v.`uptime` < 2 THEN 3 WHEN v.`password` = 'Y' AND v.`queryPassword` = 'N' THEN 1 ELSE 0 END AS `status`,CASE WHEN m.`usedns`='Y' AND `dns` IS NOT NULL AND `dns`!='' THEN CONCAT(`ip`,':',`port`,' (',`dns`,')') ELSE CONCAT(`ip`,':',`port`) END AS `server` FROM `voice_server` v LEFT JOIN `voice_masterserver` m ON v.`masterserver`=m.`id` LEFT JOIN `userdata` u ON v.`userid`=u.`id` WHERE v.`resellerid`=:reseller_id HAVING (v.`id` LIKE :search OR `server` LIKE :search {$userInQuery} {$statusQuery}) ORDER BY $orderBy LIMIT {$iDisplayStart},{$iDisplayLength}");
    $query->execute(array(':search' => '%' . $sSearch . '%', ':reseller_id' => $resellerLockupID));
} else {
    $query = $sql->prepare("SELECT v.*,m.`usedns`,u.`cname`,CONCAT(u.`name`,' ',u.`vname`) AS `full_name`,CASE WHEN v.`active` = 'N' OR v.`uptime` < 2 THEN 3 WHEN v.`password` = 'Y' AND v.`queryPassword` = 'N' THEN 1 ELSE 0 END AS `status`,CASE WHEN m.`usedns`='Y' AND `dns` IS NOT NULL AND `dns`!='' THEN CONCAT(`ip`,':',`port`,' (',`dns`,')') ELSE CONCAT(`ip`,':',`port`) END AS `server` FROM `voice_server` v LEFT JOIN `voice_masterserver` m ON v.`masterserver`=m.`id` LEFT JOIN `userdata` u ON v.`userid`=u.`id` WHERE v.`resellerid`=? ORDER BY $orderBy LIMIT {$iDisplayStart},{$iDisplayLength}");
    $query->execute(array($resellerLockupID));
}

$query2 = $sql->prepare("SELECT `action`,`extraData` FROM `jobs` WHERE `affectedID`=? AND `resellerID`=? AND `type`='vo' AND (`status` IS NULL OR `status`=1) ORDER BY `jobID` DESC LIMIT 1");
$query3 = $sql->prepare("UPDATE `voice_server` SET `jobPending`='N' WHERE `id`=? AND `resellerid`=? LIMIT 1");

while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

    $jobPending = $gsprache->no;
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

        $statusMessage = $gsprache->status_inactive;

    } else if ($row['uptime'] == 1) {

        $statusMessage = $gsprache->status_stop;

    } else if ($row['uptime'] < 1) {

        $statusMessage = $gsprache->status_crashed;

    } else if ($row['password'] == 'Y' and $row['queryPassword'] == 'N') {

        $statusMessage = $gsprache->status_password;
    }

    $password = ($row['initialpassword'] != null and $row['initialpassword'] != '') ? '?password=' . $row['initialpassword'] : '';
    $server = ($row['usedns'] == 'Y' and $row['dns'] != null or $row['dns'] != '') ? '<a href="ts3server://' . $row['dns'] . $password . '">' . $row['ip'] . ':' . $row['port'] . '</a> ( ' . $row['dns'] . ' )' : '<a href="ts3server://' . $row['ip'] . ':' . $row['port'] . $password . '">' . $row['ip'] . ':' . $row['port'] . '</a>';

    $array['aaData'][] = array($server, $row['id'], returnButton($template_to_use, 'ajax_admin_show_status.tpl', '', '', $row['status'], (string) $statusMessage), $row['localserverid'], returnButton($template_to_use, 'ajax_admin_user_switch.tpl', $row['cname'], $row['full_name'], $row['userid'], ''), ((int) $row['usedslots']). '/' . ((int) $row['slots']), (string) $jobPending, returnButton($template_to_use, 'ajax_admin_buttons_dl.tpl', 'vo', 'dl', $row['id'], $gsprache->del) . ' ' . returnButton($template_to_use, 'ajax_admin_buttons_md.tpl', 'vo', 'md', $row['id'], $gsprache->mod));
}