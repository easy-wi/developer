<?php

/**
 * File: userpanel_voice_usage.php.
 * Author: Ulrich Block
 * Date: 30.03.14
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

if (!isset($user_id) or $main != 1 or (isset($user_id) and !$pa['voiceserverStats'])) {
    header('Location: userpanel.php');
    die;
}

$sprache = getlanguagefile('traffic', $user_language, $reseller_id);

if ($ui->w('action', 4, 'post') and !token(true)) {

    $template_file = $spracheResponse->token;

} else {

    $data = array();

    $display = $sprache->total;

    if (!$ui->st('kind', 'post') or $ui->st('kind', 'post') == 'al') {

        $kind = 'al';
        $whichdata = '';

    } else if ($ui->id('what', 30, 'post') and $ui->st('kind', 'post') == 'se') {

        $kind = 'se';
        $whichdata = '&amp;shorten=' . $ui->id('what', 30, 'post');

        $query = $sql->prepare("SELECT v.`id`,v.`ip`,v.`port`,v.`dns`,m.`usedns` FROM `voice_server` v INNER JOIN `voice_masterserver` m ON v.`masterserver`=m.`id` WHERE v.`id`=? AND v.`userid`=? AND v.`resellerid`=? AND v.`resellerid`=? LIMIT 1");
        $query->execute(array($ui->id('what', 30, 'post'), $user_id, $reseller_id));
        foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
            $display = $sprache->server . '  ' . $row['ip'] . ':' . $row['port'];
        }

        $query = $sql->prepare("SELECT v.`id`,v.`ip`,v.`port`,v.`dns`,m.`usedns` FROM `voice_server` v INNER JOIN `voice_masterserver` m ON v.`masterserver`=m.`id` WHERE v.`userid`=? AND v.`resellerid`=? ORDER BY v.`ip`,v.`port`");
        $query->execute(array($user_id, $reseller_id));
        foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
            $server = $row['ip'] . ':' . $row['port'];
            if ($ui->id('what', 30, 'post') == $row['id']) {
                $data[] = '<option value='. $row['id'] .' selected="selected">' . $server . '</option>';
            } else {
                $data[] = '<option value=' . $row['id'] . '>' . $server . '</option>';
            }
        }
    }

    if (!isset($ui->post['dmy'])) {

        $dmy = 'da';
        $year = date('Y',strtotime('-6 days'));
        $month = date('m',strtotime('-6 days'));
        $day = date('d',strtotime('-6 days'));
        $yearstop = date('Y');
        $monthstop = date('m');
        $daystop = date('d');
        $amount = 7;

    } else if ($ui->post['dmy'] == 'da') {

        $dmy = 'da';

        $year = ($ui->isinteger('yearstart', 'post') and $ui->isinteger('yearstart', 'post') <= date('Y')) ? $ui->isinteger('yearstart', 'post') : date('Y', strtotime('-6 days'));
        $yearstop = ($ui->isinteger('yearstop', 'post') and $ui->isinteger('yearstop', 'post') <= date('Y')) ? $ui->isinteger('yearstop', 'post') : date('Y');

        $month = ($ui->isinteger('monthstart', 'post') and $ui->isinteger('monthstart', 'post') <= 12) ? $ui->isinteger('monthstart', 'post') : date('m', strtotime('-6 days'));
        $monthstop = ($ui->isinteger('monthstop', 'post') and $ui->isinteger('monthstop', 'post') <= 12) ? $ui->isinteger('monthstop', 'post') : date('m');

        $day = ($ui->isinteger('daystart', 'post') and $ui->isinteger('daystart', 'post') <= 31) ? $ui->isinteger('daystart', 'post') : date('d', strtotime('-6 days'));
        $daystop = ($ui->isinteger('daystop', 'post') and $ui->isinteger('daystop', 'post') <= 31) ? $ui->isinteger('daystop', 'post') : date('d');

        $now = date('Y-m-d');
        $date1 = strtotime("$year-$month-$day");
        $date2 = strtotime("$yearstop-$monthstop-$daystop");
        $amount = intval(($date2 - $date1) / 86400) + 1;

        if ($amount < 0 and "$yearstop-$monthstop-$daystop" > $now){
            $yearstop = date('Y');
            $monthstop = date('m');
            $daystop = date('d');
            $day = date('d',strtotime('-6 days'));
            $month = date('m',strtotime('-6 days'));
            $year = date('Y',strtotime('-6 days'));
            $amount = 7;
        }

    } else if ($ui->post['dmy'] == 'mo') {

        $dmy = 'mo';
        $day = 1;

        $year = ($ui->isinteger('yearstart', 'post') and $ui->isinteger('yearstart', 'post') <= date('Y')) ? $ui->isinteger('yearstart', 'post') : date('Y', strtotime('-6 days'));
        $yearstop = ($ui->isinteger('yearstop', 'post') and $ui->isinteger('yearstop', 'post') <= date('Y')) ? $ui->isinteger('yearstop', 'post') : date('Y');

        $month = ($ui->isinteger('monthstart', 'post') and $ui->isinteger('monthstart', 'post') <= 12) ? $ui->isinteger('monthstart', 'post') : date('m', strtotime('-6 days'));
        $monthstop = ($ui->isinteger('monthstop', 'post') and $ui->isinteger('monthstop', 'post') <= 12) ? $ui->isinteger('monthstop', 'post') : date('m');

        $daystop = date('t', strtotime("$yearstop-$monthstop"));
        $now = date('Y-m');
        $date1 = strtotime("$year-$month-$day");
        $add = $date1;
        $date2 = strtotime("$yearstop-$monthstop-$daystop");
        $i = 0;

        while ($add <= $date2) {
            $add = strtotime("+1 months", $add);
            $i++;
        }

        $amount = $i;

        if ($amount < 0 or "$yearstop-$monthstop" > $now){
            $yearstop = date('Y');
            $monthstop = date('m');
            $daystop = date('t', strtotime("$yearstop-$monthstop"));
            $day = 1;
            $month = date('m', strtotime('-6 months'));
            $year = date('Y', strtotime('-6 months'));
            $amount = 7;
        }

    } else if ($ui->post['dmy'] == 'ye') {

        $dmy = 'ye';
        $day = 1;

        $year = ($ui->isinteger('yearstart', 'post') and $ui->isinteger('yearstart', 'post') <= date('Y')) ? $ui->isinteger('yearstart', 'post') : date('Y', strtotime('-6 days'));
        $yearstop = ($ui->isinteger('yearstop', 'post') and $ui->isinteger('yearstop', 'post') <= date('Y')) ? $ui->isinteger('yearstop', 'post') : date('Y');

        $month = 1;
        $monthstop = 12;
        $daystop = 31;

        $now = date('Y');
        $date1 = strtotime($year . '-' . $month . '-' . $day);
        $date2 = strtotime($yearstop . '-' . $monthstop . '-' . $daystop);
        $add = $date1;
        $i = 0;

        while ($add <= $date2) {
            $add = strtotime('+1 year', $add);
            $i++;
        }

        $amount = $i;

        if ($amount < 0 or "$yearstop" > $now){
            $yearstop = date('Y');
            $monthstop = 12;
            $daystop = 31;
            $day = 1;
            $month = 1;
            $year = date('Y', strtotime('-1 year'));
            $amount = 2;
        }
    }

    if ($user_language == 'de') {
        $startdate = $day . '.' . $month . '.' . $year;
        $stopdate = $daystop . '.' . $monthstop . '.' . $yearstop;
    } else {
        $startdate = $year . '-' . $month . '-' . $day;
        $stopdate = $yearstop . '-' . $monthstop . '-' . $daystop;
    }

    $getlink = "images.php?img=vo&amp;from=admin&amp;d={$dmy}&amp;p={$year}&amp;id={$day}&amp;po={$month}&amp;m={$amount}{$whichdata}";
    $template_file = 'userpanel_voice_stats.tpl';
}