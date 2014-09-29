<?php

/**
 * File: datatable_appserver.php.
 * Author: Ulrich Block
 * Date: 14.09.14
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

if ($reseller_id == 0) {
    $query = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `rserverdata`");
    $query->execute();
} else {
    $query = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `rserverdata` AS r WHERE `resellerid`=:reseller_id OR EXISTS (SELECT 1 FROM `userdata` WHERE `resellerid`=:reseller_id AND `id`=r.`resellerid`)");
    $query->execute(array(':reseller_id' => $resellerLockupID));
}

$array['iTotalRecords'] = $query->fetchColumn();

if ($sSearch) {
    if ($reseller_id == 0) {
        $query = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `rserverdata` WHERE `id` LIKE :search OR `ip` LIKE :search OR `description` LIKE :search");
        $query->execute(array(':search' => '%' . $sSearch . '%'));
    } else {
        $query = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `rserverdata` AS r WHERE (`resellerid`=:reseller_id OR EXISTS (SELECT 1 FROM `userdata` WHERE `resellerid`=:reseller_id AND `id`=r.`resellerid`)) AND (`id` LIKE :search OR `ip` LIKE :search OR `description` LIKE :search)");
        $query->execute(array(':search' => '%' . $sSearch . '%',':reseller_id' => $resellerLockupID));
    }
    $array['iTotalDisplayRecords'] = $query->fetchColumn();
} else {
    $array['iTotalDisplayRecords'] = $array['iTotalRecords'];
}

$orderFields = array(0 => 'r.`ip`', 1 => 'r.`id`', 2 => 'r.`active`', 3 => 'r.`os`', 4 => '`gameserver_amount`', 5 => '`gameserver_ram`', 6 => 'r.`description`');

if (isset($orderFields[$iSortCol]) and is_array($orderFields[$iSortCol])) {
    $orderBy = implode(' ' . $sSortDir . ', ', $orderFields[$iSortCol]) . ' ' . $sSortDir;
} else if (isset($orderFields[$iSortCol]) and !is_array($orderFields[$iSortCol])) {
    $orderBy = $orderFields[$iSortCol] . ' ' . $sSortDir;
} else {
    $orderBy = 'r.`id` ASC';
}

if ($sSearch) {
    if ($reseller_id == 0) {
        $query = $sql->prepare("SELECT r.`id`,r.`ip`,r.`active`,r.`description`,r.`maxserver`,r.`ram`,r.`os`,(SELECT COUNT(1) AS `amount` FROM `gsswitch` g WHERE g.`rootID`=r.`id`) AS `gameserver_amount`,(SELECT SUM(`maxram`) AS `amount` FROM `gsswitch` g WHERE g.`rootID`=r.`id`) AS `gameserver_ram` FROM `rserverdata` r WHERE r.`id` LIKE :search OR r.`ip` LIKE :search OR r.`description` LIKE :search ORDER BY $orderBy LIMIT {$iDisplayStart},{$iDisplayLength}");
        $query->execute(array(':search' => '%' . $sSearch . '%'));
    } else {
        $query = $sql->prepare("SELECT r.`id`,r.`ip`,r.`active`,r.`description`,r.`maxserver`,r.`ram`,r.`os`,(SELECT COUNT(1) AS `amount` FROM `gsswitch` g WHERE g.`rootID`=r.`id`) AS `gameserver_amount`,(SELECT SUM(`maxram`) AS `amount` FROM `gsswitch` g WHERE g.`rootID`=r.`id`) AS `gameserver_ram` FROM `rserverdata` r WHERE (r.`resellerid`=:reseller_id OR EXISTS (SELECT 1 FROM `userdata` WHERE `resellerid`=:reseller_id AND `id`=r.`resellerid`)) AND (r.`id` LIKE :search OR r.`ip` LIKE :search OR r.`description` LIKE :search) ORDER BY $orderBy LIMIT {$iDisplayStart},{$iDisplayLength}");
        $query->execute(array(':search' => '%' . $sSearch . '%', ':reseller_id' => $resellerLockupID));
    }
} else {
    if ($reseller_id == 0) {
        $query = $sql->prepare("SELECT r.`id`,r.`ip`,r.`active`,r.`description`,r.`maxserver`,r.`ram`,r.`os`,(SELECT COUNT(1) AS `amount` FROM `gsswitch` g WHERE g.`rootID`=r.`id`) AS `gameserver_amount`,(SELECT SUM(`maxram`) AS `amount` FROM `gsswitch` g WHERE g.`rootID`=r.`id`) AS `gameserver_ram` FROM `rserverdata` r ORDER BY $orderBy LIMIT {$iDisplayStart},{$iDisplayLength}");
        $query->execute();
    } else {
        $query = $sql->prepare("SELECT r.`id`,r.`ip`,r.`active`,r.`description`,r.`maxserver`,r.`ram`,r.`os`,(SELECT COUNT(1) AS `amount` FROM `gsswitch` g WHERE g.`rootID`=r.`id`) AS `gameserver_amount`,(SELECT SUM(`maxram`) AS `amount` FROM `gsswitch` g WHERE g.`rootID`=r.`id`) AS `gameserver_ram` FROM `rserverdata` r WHERE r.`resellerid`=:reseller_id OR EXISTS (SELECT 1 FROM `userdata` WHERE `resellerid`=:reseller_id AND `id`=r.`resellerid`) ORDER BY $orderBy LIMIT {$iDisplayStart},{$iDisplayLength}");
        $query->execute(array(':reseller_id' => $resellerLockupID));
    }
}

while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $array['aaData'][] = array($row['ip'], $row['id'], ($row['active'] == 'Y') ? (string) $gsprache->yes : (string) $gsprache->no, ($row['os'] == 'L') ? 'Linux' : 'Windows', (int) $row['gameserver_amount'] . '/' . (int) $row['maxserver'], (int) $row['gameserver_ram'] . '/' . (int) $row['ram'], $row['description'], returnButton($template_to_use, 'ajax_admin_buttons_ri.tpl', 'ro', 'ri', $row['id'], $gsprache->reinstall) . ' ' . returnButton($template_to_use, 'ajax_admin_buttons_dl.tpl', 'ro', 'dl', $row['id'], $gsprache->del) . ' ' . returnButton($template_to_use, 'ajax_admin_buttons_md.tpl', 'ro', 'md', $row['id'], $gsprache->mod));
}