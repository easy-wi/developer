<?php

/**
 * File: datatable_maillog.php.
 * Author: Ulrich Block
 * Date: 21.09.14
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

$sprache = getlanguagefile('logs', $user_language, $reseller_id);
$gssprache = getlanguagefile('gserver', $user_language, $reseller_id);

$query = $sql->prepare("SELECT COUNT(1) AS `mail_log` FROM `mail_log` AS l WHERE `resellerid`=?");
$query->execute(array($resellerLockupID));
$array['iTotalRecords'] = $query->fetchColumn();

if ($sSearch) {

    $query = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `mail_log` AS l LEFT JOIN `userdata` AS s ON s.`id`=l.`uid` WHERE l.`resellerid`=:resellerid AND (l.`date` LIKE :search OR l.`topic` LIKE :search OR s.`cname` LIKE :search OR s.`mail` LIKE :search)");
    $query->execute(array(':search' => '%' . $sSearch . '%', ':resellerid' => $resellerLockupID));

    $array['iTotalDisplayRecords'] = $query->fetchColumn();

} else {
    $array['iTotalDisplayRecords'] = $array['iTotalRecords'];
}

$orderFields = array(0 => 'l.`date`', 1 => 's.`cname`', 2 => 'l.`topic`');

if (isset($orderFields[$iSortCol]) and is_array($orderFields[$iSortCol])) {
    $orderBy = implode(' ' . $sSortDir . ', ', $orderFields[$iSortCol]) . ' ' . $sSortDir;
} else if (isset($orderFields[$iSortCol]) and !is_array($orderFields[$iSortCol])) {
    $orderBy = $orderFields[$iSortCol] . ' ' . $sSortDir;
} else {
    $orderBy = '`logdate` DESC';
}

if ($sSearch) {
    $query = $sql->prepare("SELECT l.`date`,l.`topic`,s.`cname`,s.`mail` FROM `mail_log` AS l LEFT JOIN `userdata` AS s ON s.`id`=l.`uid` WHERE l.`resellerid`=:resellerid AND (l.`date` LIKE :search OR l.`topic` LIKE :search OR s.`cname` LIKE :search OR s.`mail` LIKE :search) ORDER BY {$orderBy} LIMIT {$iDisplayStart},{$iDisplayLength}");
    $query->execute(array(':search' => '%' . $sSearch . '%', ':resellerid' => $resellerLockupID));
} else {
    $query = $sql->prepare("SELECT l.`date`,l.`topic`,s.`cname`,s.`mail` FROM `mail_log` AS l LEFT JOIN `userdata` AS s ON s.`id`=l.`uid` WHERE l.`resellerid`=? ORDER BY {$orderBy} LIMIT {$iDisplayStart},{$iDisplayLength}");
    $query->execute(array($resellerLockupID));
}

foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $array['aaData'][] = array($row['date'], $row['cname'] . '(' . $row['mail'] . ')', $row['topic']);
}
