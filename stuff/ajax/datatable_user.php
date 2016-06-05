<?php

/**
 * File: datatable_user.php.
 * Author: Ulrich Block
 * Date: 10.01.15
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

$sprache = getlanguagefile('user', $user_language, $reseller_id);

if ($reseller_id == 0) {
    $query = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `userdata` WHERE (`resellerid`=0 OR `id`=`resellerid`)");
    $query->execute();
} else {
    $query = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `userdata` WHERE `id`=:id AND `resellerid`=:id");
    $query->execute(array(':id' => $resellerLockupID));
}

$array['iTotalRecords'] = $query->fetchColumn();

if ($sSearch) {

    $toLower = strtolower($sSearch);

    $accountTypeQuery = array();

    if (strpos(strtolower($sprache->accounttype_admin), $toLower) !== false) {
        $accountTypeQuery[] = 'OR `accounttype`=\'a\'';
    }

    if (strpos(strtolower($sprache->accounttype_reseller), $toLower) !== false) {
        $accountTypeQuery[] = 'OR `accounttype`=\'r\'';
    }

    if (strpos(strtolower($sprache->accounttype_user), $toLower) !== false) {
        $accountTypeQuery[] = 'OR `accounttype`=\'u\'';
    }

    $accountTypeQuery = (count($accountTypeQuery) > 0) ? implode(' ', $accountTypeQuery) : '';

    $activeQuery = array();

    if (strpos(strtolower($gsprache->status_inactive), $toLower) !== false) {
        $activeQuery[] = 'OR `active`=\'N\'';
    }

    if (strpos(strtolower($gsprache->status_ok), $toLower) !== false) {
        $activeQuery[] = 'OR `active`=\'Y\'';
    }

    $activeQuery = (count($activeQuery) > 0) ? implode(' ', $activeQuery) : '';

    if ($reseller_id == 0) {
        $query = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `userdata` WHERE (`resellerid`=0 OR `id`=`resellerid`) AND (`cname` LIKE :search OR `id` LIKE :search OR CONCAT(`vname`,' ',`name`) LIKE :search {$accountTypeQuery} {$activeQuery})");
        $query->execute(array(':search' => '%' . $sSearch . '%'));
    } else {
        $query = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `userdata` WHERE `id`=:id AND `resellerid`=:id AND (`cname` LIKE :search OR `id` LIKE :search OR CONCAT(`vname`,' ',`name`) LIKE :search {$accountTypeQuery} {$activeQuery})");
        $query->execute(array(':search' => '%' . $sSearch . '%', ':id' => $resellerLockupID));
    }

    $array['iTotalDisplayRecords'] = $query->fetchColumn();

} else {
    $array['iTotalDisplayRecords'] = $array['iTotalRecords'];
}

$orderFields = array(0 => '`cname`', 1 => '`id`', 2 => '`full_name`', 3 => '`active`', 4 => '`accounttype`');

if (isset($orderFields[$iSortCol]) and is_array($orderFields[$iSortCol])) {
    $orderBy = implode(' ' . $sSortDir . ', ', $orderFields[$iSortCol]) . ' ' . $sSortDir;
} else if (isset($orderFields[$iSortCol]) and !is_array($orderFields[$iSortCol])) {
    $orderBy = $orderFields[$iSortCol] . ' ' . $sSortDir;
} else {
    $orderBy = '`id` DESC';
}

if ($sSearch) {

    if ($reseller_id == 0) {
        $query = $sql->prepare("SELECT `cname`,`id`,CONCAT(`vname`,' ',`name`) AS `full_name`,`active`,`accounttype`,`jobPending`,`resellerid` FROM `userdata` WHERE (`resellerid`=0 OR `id`=`resellerid`) AND (`cname` LIKE :search OR `id` LIKE :search OR CONCAT(`vname`,' ',`name`) LIKE :search {$accountTypeQuery} {$activeQuery}) ORDER BY $orderBy LIMIT {$iDisplayStart},{$iDisplayLength}");
        $query->execute(array(':search' => '%' . $sSearch . '%'));
    } else {
        $query = $sql->prepare("SELECT `cname`,`id`,CONCAT(`vname`,' ',`name`) AS `full_name`,`active`,`accounttype`,`jobPending`,`resellerid` FROM `userdata` WHERE `id`!=:id AND `resellerid`=:id AND (`cname` LIKE :search OR `id` LIKE :search OR CONCAT(`vname`,' ',`name`) LIKE :search {$accountTypeQuery} {$activeQuery}) ORDER BY $orderBy LIMIT {$iDisplayStart},{$iDisplayLength}");
        $query->execute(array(':search' => '%' . $sSearch . '%', ':id' => $resellerLockupID));
    }

} else {

    if ($reseller_id == 0) {
        $query = $sql->prepare("SELECT `cname`,`id`,CONCAT(`vname`,' ',`name`) AS `full_name`,`active`,`accounttype`,`jobPending`,`resellerid` FROM `userdata` WHERE (`resellerid`=0 OR `id`=`resellerid`) ORDER BY $orderBy LIMIT {$iDisplayStart},{$iDisplayLength}");
        $query->execute();
    } else {
        $query = $sql->prepare("SELECT `cname`,`id`,CONCAT(`vname`,' ',`name`) AS `full_name`,`active`,`accounttype`,`jobPending`,`resellerid` FROM `userdata` WHERE `id`!=:id AND `resellerid`=:id ORDER BY $orderBy LIMIT {$iDisplayStart},{$iDisplayLength}");
        $query->execute(array(':id' => $resellerLockupID));
    }
}

$query2 = $sql->prepare("SELECT `action`,`extraData` FROM `jobs` WHERE `affectedID`=? AND `resellerID`=? AND `type`='us' AND (`status` IS NULL OR `status`=1) ORDER BY `jobID` DESC LIMIT 1");
$query3 = $sql->prepare("UPDATE `userdata` SET `jobPending`='N' WHERE `id`=? AND `resellerid`=? LIMIT 1");

while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

    $jobPending = $gsprache->no;
    $statusMessage = $gsprache->status_ok;

    if ($row['jobPending'] == 'Y') {

        $query2->execute(array($row['id'], $row['resellerid']));
        while ($row2 = $query2->fetch(PDO::FETCH_ASSOC)) {

            if ($row2['action'] == 'ad') {
                $jobPending = $gsprache->add;
            } else if ($row2['action'] == 'dl') {
                $jobPending = $gsprache->del;
            } else {
                $jobPending = $gsprache->mod;
            }

            $json = @json_decode($row2['extraData']);
            $row['active'] = (is_object($json) and isset($json->newActive)) ? $json->newActive : 'N';
        }

        if ($query2->rowCount() == 0) {
            $query3->execute(array($row['id'], $row['resellerid']));
        }
    }

    $statusMessage = ($row['active'] == 'N') ? $gsprache->status_inactive : $gsprache->status_ok;

    if ($row['accounttype'] == 'a') {
        $accounttype = $sprache->accounttype_admin;
    } else if ($row['accounttype'] == 'r') {
        $accounttype = $sprache->accounttype_reseller;
    } else {
        $accounttype = $sprache->accounttype_user;
    }

    $actionString = '';

    if ($pa['userPassword'] and (($row['accounttype'] == 'a' and $pa['user']) or $row['accounttype'] != 'a')) {
        $actionString .= ' ' . returnButton($template_to_use, 'ajax_admin_buttons_pw.tpl', 'us', 'pw', $row['id'], $gsprache->password);
    }

    if ($row['id'] != $admin_id and (($row['accounttype'] == 'a' and $pa['user']) or ($row['accounttype'] != 'a' and ($pa['user_users'] or $pa['user'])))) {
        $actionString .= ' ' . returnButton($template_to_use, 'ajax_admin_buttons_dl.tpl', 'us', 'dl', $row['id'], $gsprache->del);
    }
    if (($row['accounttype'] == 'a' and $pa['user']) or ($row['accounttype'] != 'a' and ($pa['user_users'] or $pa['user']))) {
        $actionString .= ' ' . returnButton($template_to_use, 'ajax_admin_buttons_md.tpl', 'us', 'md', $row['id'], $gsprache->mod);
    }

    $array['aaData'][] = array(returnButton($template_to_use, 'ajax_admin_user_switch.tpl', $row['cname'], '', $row['id'], ''),  $row['id'], $row['full_name'], returnButton($template_to_use, 'ajax_admin_show_status.tpl', '', '', ($row['active'] == 'N') ? 3 : 4, (string) $statusMessage), (string) $accounttype, (string) $jobPending, $actionString);
}