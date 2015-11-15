<?php

/**
 * File: datatable_userlog.php.
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

$sprache = getlanguagefile('logs', $user_language, $reseller_id);
$gssprache = getlanguagefile('gserver', $user_language, $reseller_id);

$placeholders = array('%%', '%ad%', '%add%', '%dl%', '%del%', '%md%', '%mod%', '%ri%', '%start%', '%restart%', '%stop%', '%upd%', '%fail%', '%ok%', '%psw%', '%cfg%', '%import%', '%reinstall%', '%backup%', '%use%');
$replace = array('', $gsprache->add, $gsprache->add, $gsprache->del, $gsprache->del, $gsprache->mod, $gsprache->mod, $gsprache->reinstall, $gsprache->start, $gsprache->start, $gsprache->stop, $gsprache->update,'','', $gssprache->password, $gssprache->config, $gsprache->import, $gssprache->reinstall, $gsprache->backup, $gsprache->use);
$placeholders2 = array('%modules%', '%voserver%', '%gserver%', '%user%', '%fastdl%', '%master%', '%user%', '%root%', '%addon%', '%settings%', '%vserver%', '%ticket_subject%', '%reseller%', '%virtual%', '%eac%', '%resync%', '%virtualimage%', '%template%', '%voserver%', '%emailsettings%', '%dns%', '%tsdns%', '%pmode%', '%file%', '%webmaster%', '%webvhost%');
$replace2 = array($gsprache->modules, $gsprache->voiceserver, $gsprache->gameserver, $gsprache->user, $gsprache->fastdownload, $gsprache->master, $gsprache->user, $gsprache->root, $gsprache->addon2, $gsprache->settings, $gsprache->virtual, $gsprache->support, $gsprache->reseller, $gsprache->hostsystem,'Easy Anti Cheat', $gssprache->resync, $gsprache->virtual . ' ' . $gsprache->template, $gsprache->template, $gsprache->voiceserver,'E-Mail '.$gsprache->settings, 'TSDNS', 'TSDNS', $gssprache->protect, $gsprache->file, $gsprache->webspace . ' ' . $gsprache->master, $gsprache->webspace);

if ($sSearch) {
    $sSearch = str_replace($replace, $placeholders, str_replace($replace2, $placeholders2, $sSearch));
}

if ($adminLookup) {
    $query = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `userlog` WHERE `resellerid`=?");
    $query->execute(array($resellerLockupID));
} else {
    $query = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `userlog` WHERE `usertype` IN ('user','cron') AND `userid`=? AND `resellerid`=?");
    $query->execute(array($user_id, $reseller_id));
}
$array['iTotalRecords'] = $query->fetchColumn();

if ($sSearch) {

    if ($adminLookup) {
        $query = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `userlog` AS l LEFT JOIN `userdata` AS s ON s.`id`=l.`subuser` AND l.`subuser`!=0 WHERE l.`resellerid`=:resellerid AND (`username` LIKE :search OR `cname` LIKE :search OR `ip` LIKE :search OR `logdate` LIKE :search OR `useraction` LIKE :search)");
        $query->execute(array(':search' => '%' . $sSearch . '%', ':resellerid' => $resellerLockupID));
    } else {
        $query = $sql->prepare("SELECT COUNT(1) AS `amount` FROM `userlog` AS l LEFT JOIN `userdata` AS s ON s.`id`=l.`subuser` AND l.`subuser`!=0 WHERE l.`usertype` IN ('user','cron') AND l.`userid`=:userid AND l.`resellerid`=:resellerid AND (`username` LIKE :search OR `cname` LIKE :search OR `ip` LIKE :search OR `logdate` LIKE :search OR `useraction` LIKE :search)");
        $query->execute(array(':search' => '%' . $sSearch . '%', ':userid' => $user_id, ':resellerid' => $reseller_id));
    }

    $array['iTotalDisplayRecords'] = $query->fetchColumn();

} else {
    $array['iTotalDisplayRecords'] = $array['iTotalRecords'];
}

$orderFields = array(0 => '`logdate`', 1 => array('`username`', '`cname`'), 2 => '`useraction`', 3 => '`ip`');

if (isset($orderFields[$iSortCol]) and is_array($orderFields[$iSortCol])) {
    $orderBy = implode(' ' . $sSortDir . ', ', $orderFields[$iSortCol]) . ' ' . $sSortDir;
} else if (isset($orderFields[$iSortCol]) and !is_array($orderFields[$iSortCol])) {
    $orderBy = $orderFields[$iSortCol] . ' ' . $sSortDir;
} else {
    $orderBy = '`logdate` DESC';
}

if ($sSearch) {

    if ($adminLookup) {
        $query = $sql->prepare("SELECT `subuser`,`username`,`useraction`,`ip`,`logdate`,`cname` FROM `userlog` AS l LEFT JOIN `userdata` AS s ON s.`id`=l.`subuser` AND l.`subuser`!=0 WHERE l.`resellerid`=:resellerid AND (`username` LIKE :search OR `cname` LIKE :search OR `ip` LIKE :search OR `logdate` LIKE :search OR `useraction` LIKE :search) ORDER BY {$orderBy} LIMIT {$iDisplayStart},{$iDisplayLength}");
        $query->execute(array(':search' => '%' . $sSearch . '%', ':resellerid' => $resellerLockupID));
    } else {
        $query = $sql->prepare("SELECT `subuser`,`username`,`useraction`,`ip`,`logdate`,`cname` FROM `userlog` AS l LEFT JOIN `userdata` AS s ON s.`id`=l.`subuser` AND l.`subuser`!=0 WHERE l.`usertype` IN ('user','cron') AND l.`userid`=:userid AND l.`resellerid`=:resellerid AND (`username` LIKE :search OR `cname` LIKE :search OR `ip` LIKE :search OR `logdate` LIKE :search OR `useraction` LIKE :search) ORDER BY {$orderBy} LIMIT {$iDisplayStart},{$iDisplayLength}");
        $query->execute(array(':search' => '%' . $sSearch . '%', ':userid' => $user_id, ':resellerid' => $reseller_id));
    }

} else {

    if ($adminLookup) {
        $query = $sql->prepare("SELECT `subuser`,`username`,`useraction`,`ip`,`logdate`,`cname` FROM `userlog` AS l LEFT JOIN `userdata` AS s ON s.`id`=l.`subuser` AND l.`subuser`!=0 WHERE l.`resellerid`=? ORDER BY {$orderBy} LIMIT {$iDisplayStart},{$iDisplayLength}");
        $query->execute(array($resellerLockupID));
    } else {
        $query = $sql->prepare("SELECT `subuser`,`username`,`useraction`,`ip`,`logdate`,`cname` FROM `userlog` AS l LEFT JOIN `userdata` AS s ON s.`id`=l.`subuser` AND l.`subuser`!=0 WHERE l.`usertype` IN ('user','cron') AND l.`userid`=? AND l.`resellerid`=? ORDER BY {$orderBy} LIMIT {$iDisplayStart},{$iDisplayLength}");
        $query->execute(array($user_id, $reseller_id));
    }
}

while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

    if ($row['subuser'] == 0 or $adminLookup) {
        $username = $row['username'];
        $ip = $row['ip'];
    } else {
        $username = $row['cname'];
        $ip = (isanyadmin($row['subuser'])) ? 'admin' : $row['ip'];
    }

    $array['aaData'][] = array($row['logdate'], $username, str_replace($placeholders2, $replace2, str_replace($placeholders, $replace, $row['useraction'])), $ip);
}