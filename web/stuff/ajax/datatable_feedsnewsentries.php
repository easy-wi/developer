<?php

/**
 * File: datatable_feedsnewsentries.php.
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

$query = $sql->prepare("SELECT COUNT(`newsID`) AS `amount` FROM `feeds_news` WHERE `resellerID`=?");
$query->execute(array($resellerLockupID));

$array['iTotalRecords'] = $query->fetchColumn();

if ($sSearch) {
    $query = $sql->prepare("SELECT COUNT(`newsID`) AS `amount` FROM `feeds_news` WHERE `resellerID`=:reseller_id AND (`newsID` LIKE :search OR `pubDate` LIKE :search OR ((LENGTH(`title`)<2 AND `link` LIKE :search) OR (LENGTH(`title`)>1 AND `title` LIKE :search)))");
    $query->execute(array(':search' => '%' . $sSearch . '%', ':reseller_id' => $resellerLockupID));

    $array['iTotalDisplayRecords'] = $query->fetchColumn();
} else {
    $array['iTotalDisplayRecords'] = $array['iTotalRecords'];
}

$orderFields = array(0 => 'n.`title`', 1 => 'n.`newsID`', 2 => 'n.`pubDate`');

if (isset($orderFields[$iSortCol]) and is_array($orderFields[$iSortCol])) {
    $orderBy = implode(' ' . $sSortDir . ', ', $orderFields[$iSortCol]) . ' ' . $sSortDir;
} else if (isset($orderFields[$iSortCol]) and !is_array($orderFields[$iSortCol])) {
    $orderBy = $orderFields[$iSortCol] . ' ' . $sSortDir;
} else {
    $orderBy = 'n.`newsID` ASC';
}

if ($sSearch) {
    $query = $sql->prepare("SELECT n.`newsID`,n.`title`,n.`link`,n.`pubDate`,n.`active` FROM `feeds_news` n LEFT JOIN `feeds_url` u ON n.`feedID`=u.`feedID` WHERE n.`resellerID`=:reseller_id AND (n.`newsID` LIKE :search OR n.`pubDate` LIKE :search OR ((LENGTH(n.`title`)<2 AND n.`link` LIKE :search) OR (LENGTH(n.`title`)>1 AND n.`title` LIKE :search))) ORDER BY $orderBy LIMIT {$iDisplayStart},{$iDisplayLength}");
    $query->execute(array(':search' => '%' . $sSearch . '%', ':reseller_id' => $resellerLockupID));
} else {
    $query = $sql->prepare("SELECT n.`newsID`,n.`title`,n.`link`,n.`pubDate`,n.`active` FROM `feeds_news` n LEFT JOIN `feeds_url` u ON n.`feedID`=u.`feedID` WHERE n.`resellerID`=? ORDER BY $orderBy LIMIT {$iDisplayStart},{$iDisplayLength}");
    $query->execute(array($resellerLockupID));
}

while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $array['aaData'][] = array((strlen($row['title']) < 2) ? $row['link'] : $row['title'], $row['newsID'], $row['pubDate'], returnButton($template_to_use, 'ajax_admin_feed_active.tpl', $row['active'], '', $row['newsID'], ''), returnButton($template_to_use, 'ajax_admin_feed_del.tpl', '', '', $row['newsID'], '') . returnButton($template_to_use, 'ajax_admin_feed_hidden.tpl', '', '', $row['newsID'], ''));
}