<?php

/**
 * File: stats_voicestats.php.
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

$usageArray = array();

$dateRange = ($ui->escaped('dateRange', 'get')) ? $ui->escaped('dateRange', 'get') : date('m/d/Y', strtotime("-6 days")) . ' - ' . date('m/d/Y');
$accuracy = (in_array($ui->st('accuracy', 'get'), array('da', 'mo'))) ? $ui->st('accuracy', 'get') : 'da';

@list($startDate, $endDate) = explode('-', str_replace(' ', '', $dateRange));
@list($startMonth, $startDay, $startYear) = explode('/', $startDate);
@list($endMonth, $endDay, $endYear) = explode('/', $endDate);

if ($endYear > 2000 and $startYear > 2000) {

    $menuStart = round((strtotime("{$endYear}-{$endMonth}-{$endDay}") - strtotime("{$startYear}-{$startMonth}-{$startDay}")) / 86400);

    $extractOrNormal = ($accuracy == 'mo') ? "CONCAT(EXTRACT(YEAR FROM `date`),'-',EXTRACT(MONTH FROM `date`))" : '`date`';

    $startDateFormatted = date('Y-m-d', strtotime($startYear . '-' . $startMonth . '-' . $startDay));
    $endDateFormatted = date('Y-m-d', strtotime($endYear . '-' . $endMonth . '-' . $endDay));

    if ($ui->w('d', 15, 'get') == 'voiceAdminStats') {
        if ($ui->id('masterID', 10, 'get')) {
            $query = $sql->prepare("SELECT $extractOrNormal AS `groupedDate`,SUM(`used`)/COUNT(`sid`) AS `averageused`,SUM(`traffic`)/1024 as `fileTrafficMB` FROM `voice_server_stats` WHERE `mid`=? AND `resellerid`=? AND `date` BETWEEN ? AND ? GROUP BY `groupedDate` ORDER BY `groupedDate`");
            $query->execute(array($ui->id('masterID', 10, 'get'), $resellerLockupID, $startDateFormatted, $endDateFormatted));
        } else if ($ui->id('userID', 10, 'get')) {
            $query = $sql->prepare("SELECT $extractOrNormal AS `groupedDate`,SUM(`used`)/COUNT(`sid`) AS `averageused`,SUM(`traffic`)/1024 as `fileTrafficMB` FROM `voice_server_stats` WHERE `uid`=? AND `resellerid`=? AND `date` BETWEEN ? AND ? GROUP BY `groupedDate` ORDER BY `groupedDate`");
            $query->execute(array($ui->id('userID', 10, 'get'), $resellerLockupID, $startDateFormatted, $endDateFormatted));
        } else if ($ui->id('serverID', 10, 'post')) {
            $query = $sql->prepare("SELECT $extractOrNormal AS `groupedDate`,SUM(`used`)/COUNT(`sid`) AS `averageused`,SUM(`traffic`)/1024 as `fileTrafficMB` FROM `voice_server_stats` WHERE `sid`=? AND `resellerid`=? AND `date` BETWEEN ? AND ? GROUP BY `groupedDate` ORDER BY `groupedDate`");
            $query->execute(array($ui->id('serverID', 10, 'get'), $resellerLockupID, $startDateFormatted, $endDateFormatted));
        } else {
            $query = $sql->prepare("SELECT $extractOrNormal AS `groupedDate`,SUM(`used`)/COUNT(`sid`) AS `averageused`,SUM(`traffic`)/1024 as `fileTrafficMB` FROM `voice_server_stats` WHERE `resellerid`=? AND `date` BETWEEN ? AND ? GROUP BY `groupedDate` ORDER BY `groupedDate`");
            $query->execute(array($resellerLockupID, $startDateFormatted, $endDateFormatted));
        }
    } else {
        if ($ui->id('serverID', 10, 'get')) {
            $query = $sql->prepare("SELECT $extractOrNormal AS `groupedDate`,SUM(`used`)/COUNT(`sid`) AS `averageused`,SUM(`traffic`)/1024 as `fileTrafficMB` FROM `voice_server_stats` WHERE `sid`=? AND `uid`=? AND `resellerid`=? AND `date` BETWEEN ? AND ? GROUP BY `groupedDate` ORDER BY `groupedDate`");
            $query->execute(array($ui->id('serverID', 10, 'get'), $user_id, $resellerLockupID, $startDateFormatted, $endDateFormatted));
        } else {
            $query = $sql->prepare("SELECT $extractOrNormal AS `groupedDate`,SUM(`used`)/COUNT(`sid`) AS `averageused`,SUM(`traffic`)/1024 as `fileTrafficMB` FROM `voice_server_stats` WHERE `uid`=? AND `resellerid`=? AND `date` BETWEEN ? AND ? GROUP BY `groupedDate` ORDER BY `groupedDate`");
            $query->execute(array($user_id, $resellerLockupID, $startDateFormatted, $endDateFormatted));
        }
    }

    while($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $usageArray[] = array('y' => $row['groupedDate'], 'slots' => ceil($row['averageused']), 'traffic' => ceil($row['fileTrafficMB']));
    }
}

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 1 Jan 1900 00:00:00 GMT');
header('Content-type: application/json');

die(json_encode($usageArray));