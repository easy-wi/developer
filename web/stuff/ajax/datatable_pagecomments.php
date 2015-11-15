<?php

/**
 * File: datatable_pagecommentphp.
 * Author: Ulrich Block
 * Date: 15.05.15
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
 * GNU General Public License for more detail
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
 * Siehe die GNU General Public License fuer weitere Detail
 *
 * Sie sollten eine Kopie der GNU General Public License zusammen mit diesem
 * Programm erhalten haben. Wenn nicht, siehe <http://www.gnu.org/licenses/>.
 */

if (!defined('AJAXINCLUDED')) {
    die('Do not access directly!');
}

$query = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `page_comments` WHERE `resellerID`=?");
$query->execute(array($resellerLockupID));

$array['iTotalRecords'] = $query->fetchColumn();

if ($sSearch) {

    $toLower = strtolower($sSearch);

    $statusQuery = array();

    if (strpos(strtolower($gsprache->yes), $toLower) !== false) {
        $statusQuery[] = "OR c.`markedSpam`='Y'";
        $statusQuery[] = "OR c.`moderateAccepted`='Y'";
    }

    if (strpos(strtolower($gsprache->no), $toLower) !== false) {
        $statusQuery[] = "OR c.`markedSpam`='N'";
        $statusQuery[] = "OR c.`moderateAccepted`='N'";
    }

    $statusQuery = (count($statusQuery) > 0) ? implode(' ', $statusQuery) : '';

    $query = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `page_comments` c LEFT JOIN `page_pages_text` t ON c.`pageTextID`=t.`id` WHERE c.`resellerID`=:reseller_id AND (t.`title` LIKE :search OR c.`commentID` LIKE :search OR c.`authorname` LIKE :search OR c.`date` LIKE :search {$statusQuery})");
    $query->execute(array(':search' => '%' . $sSearch . '%',':reseller_id' => $resellerLockupID));
    $array['iTotalDisplayRecords'] = $query->fetchColumn();
} else {
    $array['iTotalDisplayRecords'] = $array['iTotalRecords'];
}

$orderFields = array(0 => 't.`title`', 1 => 'c.`commentID`', 2 => 'c.`authorname`', 3 => 'c.`date`', 4 => 'c.`moderateAccepted`', 5 => 'c.`markedSpam`');

if (isset($orderFields[$iSortCol]) and is_array($orderFields[$iSortCol])) {
    $orderBy = implode(' ' . $sSortDir . ', ', $orderFields[$iSortCol]) . ' ' . $sSortDir;
} else if (isset($orderFields[$iSortCol]) and !is_array($orderFields[$iSortCol])) {
    $orderBy = $orderFields[$iSortCol] . ' ' . $sSortDir;
} else {
    $orderBy = 'c.`commentID` DESC';
}

$query = $sql->prepare("SELECT `seo` FROM `page_settings` WHERE `resellerid`=? LIMIT 1");
$query->execute(array($resellerLockupID));
$seoActive = $query->fetchColumn();

if ($sSearch) {
    $query = $sql->prepare("SELECT t.`pageid`,t.`language`,t.`title`,c.`commentID`,c.`date`,c.`authorname`,c.`markedSpam`,c.`moderateAccepted` FROM `page_comments` c LEFT JOIN `page_pages_text` t ON c.`pageTextID`=t.`id` WHERE c.`resellerID`=:reseller_id AND (t.`title` LIKE :search OR c.`commentID` LIKE :search OR c.`authorname` LIKE :search OR c.`date` LIKE :search {$statusQuery}) ORDER BY $orderBy LIMIT {$iDisplayStart},{$iDisplayLength}");
    $query->execute(array(':search' => '%' . $sSearch . '%', ':reseller_id' => $resellerLockupID));
} else {
    $query = $sql->prepare("SELECT t.`pageid`,t.`language`,t.`title`,c.`commentID`,c.`date`,c.`authorname`,c.`markedSpam`,c.`moderateAccepted` FROM `page_comments` c LEFT JOIN `page_pages_text` t ON c.`pageTextID`=t.`id` WHERE c.`resellerID`=? ORDER BY $orderBy LIMIT {$iDisplayStart},{$iDisplayLength}");
    $query->execute(array($resellerLockupID));
}

while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

    if (!isset($titleLanguages[$row['language']])) {
        $titleLanguages[$row['language']] = array(
            'page' => getlanguagefile('page', $row['language'], 0),
            'general' => getlanguagefile('general', $row['language'], 0)
        );
    }

    $moderated = ($row['moderateAccepted'] == 'N') ? $gsprache->yes : $gsprache->no;
    $spam = ($row['markedSpam'] == 'Y') ? $gsprache->yes : $gsprache->no;
    $link = ($seoActive == 'N') ? $page_url . '/index.php?site=news&amp;id=' . $row['pageid'] : $page_url . '/' . $row['language'] . '/' . szrp($titleLanguages[$row['language']]['general']->news) . '/' . szrp($row['title']) . '/';

    $array['aaData'][] = array('<a href="' .$link . '" target="_blank">' . $row['title'] . '</a>', $row['commentID'], $row['authorname'], $row['date'], (string) $moderated, (string) $spam, returnButton($template_to_use, 'ajax_admin_buttons_dl.tpl', 'pc', 'dl', $row['commentID'], $gsprache->del) . ' ' . returnButton($template_to_use, 'ajax_admin_buttons_md.tpl', 'pc', 'md', $row['commentID'], $gsprache->mod));
}