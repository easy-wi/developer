<?php

/**
 * File: datatable_gameaddons.php.
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

$sprache = getlanguagefile('images', $user_language, $resellerLockupID);

$query = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `addons` WHERE `resellerid`=?");
$query->execute(array($resellerLockupID));

$array['iTotalRecords'] = $query->fetchColumn();

if ($sSearch) {

    $addonShortenIDs = array();

    $query = $sql->prepare("SELECT al.`addon_id`, GROUP_CONCAT(DISTINCT s.`shorten` ORDER BY s.`shorten` ASC SEPARATOR ', ') AS `list` FROM `addons_allowed` AS al INNER JOIN `servertypes` AS s ON al.`servertype_id`=s.`id` WHERE al.`reseller_id`=:reseller_id GROUP BY al.`addon_id` HAVING (`list` LIKE :search)");
    $query->execute(array(':search' => '%' . $sSearch . '%',':reseller_id' => $resellerLockupID));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $addonShortenIDs[] = $row['addon_id'];
    }

    $addonInQuery = (count($addonShortenIDs) > 0) ? ' OR `id` IN (' . implode(',', $addonShortenIDs) . ')' : '';
    $query = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `addons` WHERE `resellerid`=:reseller_id AND (`id` LIKE :search OR `type` LIKE :search OR `menudescription` LIKE :search $addonInQuery)");
    $query->execute(array(':search' => '%' . $sSearch . '%',':reseller_id' => $resellerLockupID));

    $array['iTotalDisplayRecords'] = $query->fetchColumn();
} else {
    $array['iTotalDisplayRecords'] = $array['iTotalRecords'];
}

$orderFields = array(0 => 'a.`menudescription`', 1 => 'a.`id`', 2 => 'a.`type`', 3 => '`list`');

if (isset($orderFields[$iSortCol]) and is_array($orderFields[$iSortCol])) {
    $orderBy = implode(' ' . $sSortDir . ', ', $orderFields[$iSortCol]) . ' ' . $sSortDir;
} else if (isset($orderFields[$iSortCol]) and !is_array($orderFields[$iSortCol])) {
    $orderBy = $orderFields[$iSortCol] . ' ' . $sSortDir;
} else {
    $orderBy = 'a.`id` ASC';
}

if ($sSearch) {

    $query = $sql->prepare("SELECT a.`id`,a.`menudescription`,a.`type`,(SELECT GROUP_CONCAT(DISTINCT s.`shorten` ORDER BY s.`shorten` ASC SEPARATOR ', ') AS `list` FROM `addons_allowed` AS al INNER JOIN `servertypes` AS s ON al.`servertype_id`=s.`id` WHERE al.`addon_id`=a.`id`) AS `list` FROM `addons` AS a WHERE a.`resellerid`=:reseller_id AND (`id` LIKE :search OR `type` LIKE :search OR `menudescription` LIKE :search $addonInQuery) ORDER BY $orderBy LIMIT {$iDisplayStart},{$iDisplayLength}");
    $query->execute(array(':search' => '%' . $sSearch . '%', ':reseller_id' => $resellerLockupID));

} else {
    $query = $sql->prepare("SELECT a.`id`,a.`menudescription`,a.`type`,(SELECT GROUP_CONCAT(DISTINCT s.`shorten` ORDER BY s.`shorten` ASC SEPARATOR ', ') AS `list` FROM `addons_allowed` AS al INNER JOIN `servertypes` AS s ON al.`servertype_id`=s.`id` WHERE al.`addon_id`=a.`id`) AS `list` FROM `addons` AS a WHERE a.`resellerid`=? ORDER BY $orderBy LIMIT {$iDisplayStart},{$iDisplayLength}");
    $query->execute(array($resellerLockupID));
}

while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

    $array['aaData'][] = array($row['menudescription'], $row['id'], ($row['type'] == 'map') ? (string) $sprache->map : (string) $sprache->tool, $row['list'], returnButton($template_to_use, 'ajax_admin_buttons_ex.tpl', 'ad', 'ex', $row['id'], $gsprache->export) . ' ' . returnButton($template_to_use, 'ajax_admin_buttons_dl.tpl', 'ad', 'dl', $row['id'], $gsprache->del) . ' ' . returnButton($template_to_use, 'ajax_admin_buttons_md.tpl', 'ad', 'md', $row['id'], $gsprache->mod));
}