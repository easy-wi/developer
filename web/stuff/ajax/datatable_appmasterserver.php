<?php

/**
 * File: datatable_appmasterserver.php.
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

include(EASYWIDIR . '/stuff/keyphrasefile.php');
include(EASYWIDIR . '/stuff/methods/functions_ssh_exec.php');
include(EASYWIDIR . '/stuff/methods/functions_gs.php');

$query = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `rserverdata` WHERE `resellerid`=?");
$query->execute(array($resellerLockupID));
$array['iTotalRecords'] = $query->fetchColumn();

if ($sSearch) {
    $query = $sql->prepare("SELECT COUNT(r.`id`) AS `amount` FROM `rserverdata` AS r WHERE r.`active`='Y' AND r.`resellerid`=:resellerID AND (r.`id` LIKE :search OR r.`ip` LIKE :search OR r.`description` LIKE :search OR EXISTS(SELECT 1 FROM `rservermasterg` AS m INNER JOIN `servertypes` s ON s.`id`=m.`servertypeid` WHERE m.`serverid`=r.`id` AND s.`shorten` LIKE :search))");
    $query->execute(array(':resellerID' => $resellerLockupID, ':search' => '%' . $sSearch . '%'));
    $array['iTotalDisplayRecords'] = $query->fetchColumn();

} else {
    $array['iTotalDisplayRecords'] = $array['iTotalRecords'];
}

$orderFields = array(0 => '`ip`', 1 => '`id`', 2 => '`description`');

if (isset($orderFields[$iSortCol]) and is_array($orderFields[$iSortCol])) {
    $orderBy = implode(' ' . $sSortDir . ', ', $orderFields[$iSortCol]) . ' ' . $sSortDir;
} else if (isset($orderFields[$iSortCol]) and !is_array($orderFields[$iSortCol])) {
    $orderBy = $orderFields[$iSortCol] . ' ' . $sSortDir;
} else {
    $orderBy = '`id` ASC';
}

$query2 = $sql->prepare("SELECT s.`shorten`,r.`installing`,r.`updating`,r.`installstarted` FROM `rservermasterg` r INNER JOIN `servertypes` s ON r.`servertypeid`=s.`id` WHERE r.`serverid`=? AND r.`resellerid`=? GROUP BY s.`shorten`");
$query3 = $sql->prepare("SELECT r.`id`,s.`steamgame`,s.`updates`,d.`updates` AS `rupdates` FROM `rservermasterg` r INNER JOIN `rserverdata` d ON r.`serverid`=d.`id` INNER JOIN `servertypes` s ON r.`servertypeid`=s.`id` WHERE s.`shorten`=? AND r.`resellerid`=? AND d.`ip`=? LIMIT 1");
$query4 = $sql->prepare("UPDATE `rservermasterg` SET `installing`='N',`updating`='N' WHERE `id`=? LIMIT 1");

if ($sSearch) {
    $query = $sql->prepare("SELECT `id`,`ip`,`description` FROM `rserverdata` AS r WHERE `active`='Y' AND `resellerid`=:resellerID AND (`id` LIKE :search OR `ip` LIKE :search OR `description` LIKE :search OR EXISTS (SELECT 1 FROM `rservermasterg` AS m INNER JOIN `servertypes` s ON s.`id`=m.`servertypeid` WHERE m.`serverid`=r.`id` AND s.`shorten` LIKE :search)) ORDER BY $orderBy LIMIT {$iDisplayStart},{$iDisplayLength}");
    $query->execute(array(':resellerID' => $resellerLockupID, ':search' => '%' . $sSearch . '%'));
} else {
    $query = $sql->prepare("SELECT `id`,`ip`,`description` FROM `rserverdata` WHERE `active`='Y' AND `resellerid`=? ORDER BY $orderBy LIMIT {$iDisplayStart},{$iDisplayLength}");
    $query->execute(array($resellerLockupID));
}

while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

    $statusList = array();
    $sshcheck = array();

    $description = $row['description'];

    $query2->execute(array($row['id'], $resellerLockupID));
    foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {

        $shorten = $row2['shorten'];

        if ($row2['installing'] == 'N' and $row2['updating'] == 'N') {
            $statusList[$row2['shorten']] = true;
        } else {

            $toolong = date($row2['installstarted'], strtotime("+15 minutes"));

            if (strtotime($logdate) > strtotime($toolong) or $row2['updating'] == 'Y') {
                $sshcheck[] = $row2['shorten'];
            } else {
                $statusList[$row2['shorten']] = false;
            }
        }
    }

    if (count($sshcheck) > 0) {

        $serverdata = serverdata('root', $row['id'], $aeskey);
        $ip = $serverdata['ip'];

        $check = ssh2_execute('gs', $row['id'], './control.sh updatestatus "' . implode(' ', $sshcheck) . '"');

        if ($check === false) {

            $description = 'The login data does not work';

        } else if (preg_match('/^[\w\:\-\=]+$/', $check)) {

            $games = array();

            foreach (preg_split('/\:/',$check,-1,PREG_SPLIT_NO_EMPTY) as $status) {
                $ex = explode('=', $status);
                if (isset($ex[1])) {
                    $games[$ex[0]] = $ex[1];
                }
            }

            foreach ($games as $k => $v) {

                if (!in_array($k, array('steamcmd', 'sync'))) {

                    $query3->execute(array($k, $resellerLockupID, $ip));
                    foreach ($query3->fetchAll(PDO::FETCH_ASSOC) as $row2) {

                        if (($v == 0 and $row2['rupdates'] != 4 and $row2['updates'] != 4 and $row2['steamgame'] != 'S') or ($row2['steamgame'] == 'S' and (!isset($games['steamcmd']) or $games['steamcmd'] == 0)) or (($row2['rupdates'] == 4 or $row2['updates'] == 4) and (!isset($games['sync']) or $games['sync'] == 0))) {

                            $statusList[$k] = true;

                            $query4->execute(array($row2['id']));

                            unset($sshcheck[array_search($k, $sshcheck)]);
                        }
                    }
                }
            }
        }

        foreach ($sshcheck as $shorten) {
            $statusList[$shorten] = false;
        }
    }

    $array['aaData'][] = array($row['ip'], $row['id'], $description, returnButton($template_to_use, 'ajax_admin_master_list.tpl', $statusList, '', '', ''), returnButton($template_to_use, 'ajax_admin_buttons_dl.tpl', 'ma', 'dl', $row['id'], $gsprache->del) . ' ' . returnButton($template_to_use, 'ajax_admin_buttons_add.tpl', 'ma', 'ad', $row['id'], $gsprache->add));
}