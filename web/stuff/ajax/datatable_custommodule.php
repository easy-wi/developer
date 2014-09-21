<?php

/**
 * File: datatable_custommodule.php.
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

$sprache = getlanguagefile('modules', $user_language, $resellerLockupID);

$types = array('A' => $sprache->type_admin, 'P' => $sprache->type_cms, 'U' => $sprache->type_user, 'C' => $sprache->type_core);

$query = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `modules`");
$query->execute();

$array['iTotalRecords'] = $query->fetchColumn();

if ($sSearch) {

    $translationIDs = array();

    $query = $sql->prepare("SELECT `transID` FROM `translations` WHERE `type`='mo' AND `lang`=:lang AND `text` LIKE :search");
    $query->execute(array(':lang' => $user_language, ':search' => '%' . $sSearch . '%'));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $translationIDs[] = $row['transID'];
    }

    $translationInQuery = (count($translationIDs) > 0) ? ' OR m.`id` IN (' . implode(',', $translationIDs) . ')' : '';

    $translationTypeKeys = array();

    foreach ($types as $key => $value) {
        if (strpos(strtolower($value), strtolower($sSearch)) !== false) {
            $translationTypeKeys[] = $key;
        }
    }

    $translationInQuery .= (count($translationTypeKeys) > 0) ? ' OR m.`type` IN (\'' . implode("','", $translationTypeKeys) . '\')' : '';

    $query = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `modules` AS m WHERE `id` LIKE :search $translationInQuery");
    $query->execute(array(':search' => '%' . $sSearch . '%'));

    $array['iTotalDisplayRecords'] = $query->fetchColumn();
} else {
    $array['iTotalDisplayRecords'] = $array['iTotalRecords'];
}

$orderFields = array(0 => '`name`', 1 => 'm.`id`', 2 => 'm.`active`', 3 => 'm.`type`');

if (isset($orderFields[$iSortCol]) and is_array($orderFields[$iSortCol])) {
    $orderBy = implode(' ' . $sSortDir . ', ', $orderFields[$iSortCol]) . ' ' . $sSortDir;
} else if (isset($orderFields[$iSortCol]) and !is_array($orderFields[$iSortCol])) {
    $orderBy = $orderFields[$iSortCol] . ' ' . $sSortDir;
} else {
    $orderBy = 'm.`id` ASC';
}

if ($sSearch) {
    $query = $sql->prepare("SELECT m.*,(SELECT `text` FROM `translations` WHERE `type`='mo' AND `transID`=m.`id` AND `lang`=:lang LIMIT 1) AS `name` FROM `modules` AS m WHERE m.`id` LIKE :search $translationInQuery ORDER BY $orderBy LIMIT {$iDisplayStart},{$iDisplayLength}");
    $query->execute(array(':lang' => $user_language, ':search' => '%' . $sSearch . '%'));
} else {
    $query = $sql->prepare("SELECT m.*,(SELECT `text` FROM `translations` WHERE `type`='mo' AND `transID`=m.`id` AND `lang`=? LIMIT 1) AS `name` FROM `modules` AS m ORDER BY $orderBy LIMIT {$iDisplayStart},{$iDisplayLength}");
    $query->execute(array($user_language));
}

while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $array['aaData'][] = array($row['name'], $row['id'], returnButton($template_to_use, 'ajax_admin_show_status.tpl', '', '', ($row['active'] == 'N') ? 2 : 4, ''), (string) $types[$row['type']], returnButton($template_to_use, 'ajax_admin_buttons_dl.tpl', 'mo', 'dl', $row['id'], $gsprache->del) . ' ' . returnButton($template_to_use, 'ajax_admin_buttons_md.tpl', 'mo', 'md', $row['id'], $gsprache->mod));
}