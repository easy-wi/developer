<?php

/**
 * File: jobs_list.php.
 * Author: Ulrich Block
 * Date: 20.05.12
 * Time: 19:44
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

if ((!isset($admin_id) or $main != 1) or (isset($admin_id) and !$pa['jobs'])) {
    header('Location: admin.php');
    die('No acces');
}
$sprache = getlanguagefile('api', $user_language, $reseller_id);
if ($ui->w('action', 4, 'post') and !token(true)) {
    $template_file = $spracheResponse->token;
} else if ($ui->w('action', 4, 'post') == 'dl' and !$ui->id('id', 19, 'get')) {
    $i = 0;
    if ($ui->id('id',30, 'post')) {
        foreach ($ui->id('id',30, 'post') as $id) {
            if ($reseller_id == 0) {
                $delete = $sql->prepare("DELETE FROM `jobs` WHERE `jobID`=? LIMIT 1");
                $delete->execute(array($id));
            } else {
                $delete = $sql->prepare("DELETE FROM `jobs` WHERE `jobID`=? AND `resellerID`=? LIMIT 1");
                $delete->execute(array($id, $reseller_id));
            }
            $i++;
        }
    }
    $template_file = $i . ' ' . $gsprache->jobs.' deleted';
} else if ($ui->id('id', 19, 'get')) {
    if ($reseller_id == 0) {
        $query = $sql->prepare("SELECT `text` FROM `mail_log` WHERE `id`=? LIMIT 1");
        $query->execute(array($ui->id('id', 19, 'get')));
    } else if ($reseller_id != 0 and $admin_id != $reseller_id) {
        $query = $sql->prepare("SELECT `text` FROM `mail_log` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($ui->id('id', 19, 'get'), $admin_id));
    } else {
        $query = $sql->prepare("SELECT `text` FROM `mail_log` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($ui->id('id', 19, 'get'), $reseller_id));
    }
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $text= @gzuncompress($row['text']);
    }
    $template_file = $text;
} else {
    $table = array();
    $o = $ui->st('o', 'get');
    if ($ui->st('o', 'get') == 'dn') {
        $orderby = '`name` DESC';
    } else if ($ui->st('o', 'get') == 'an') {
        $orderby = '`name` ASC';
    } else if ($ui->st('o', 'get') == 'ds') {
        $orderby = '`status` DESC';
    } else if ($ui->st('o', 'get') == 'as') {
        $orderby = '`status` ASC';
    } else if ($ui->st('o', 'get') == 'dt') {
        $orderby = '`type` DESC';
    } else if ($ui->st('o', 'get') == 'at') {
        $orderby = '`type` ASC';
    } else if ($ui->st('o', 'get') == 'da') {
        $orderby = '`api` DESC';
    } else if ($ui->st('o', 'get') == 'aa') {
        $orderby = '`api` ASC';
    } else if ($ui->st('o', 'get') == 'dc') {
        $orderby = '`action` DESC';
    } else if ($ui->st('o', 'get') == 'ac') {
        $orderby = '`action` ASC';
    } else if ($ui->st('o', 'get') == 'dd') {
        $orderby = '`date` DESC';
    } else if ($ui->st('o', 'get') == 'ad') {
        $orderby = '`date` ASC';
    } else if ($ui->st('o', 'get') == 'dn') {
        $orderby = '`name` DESC';
    } else if ($ui->st('o', 'get') == 'an') {
        $orderby = '`name` ASC';
    } else if ($ui->st('o', 'get') == 'du') {
        $orderby = '`userID` DESC';
    } else if ($ui->st('o', 'get') == 'au') {
        $orderby = '`userID` ASC';
    } else if ($ui->st('o', 'get') == 'ai') {
        $orderby = '`jobID` ASC';
    } else {
        $o = 'di';
        $orderby = '`jobID` DESC';
    }
    if ($reseller_id == 0) {
        $where = '';
    } else {
        $where='WHERE `resellerID`=?';
    }
    if ($reseller_id == 0) {
        $query = $sql->prepare("SELECT * FROM `jobs` $where ORDER BY $orderby LIMIT $start,$amount");
        $query->execute();
    } else {
        $query = $sql->prepare("SELECT * FROM `jobs` $where ORDER BY $orderby LIMIT $start,$amount");
        $query->execute(array($reseller_id));
    }
    $type=array('de' => $gsprache->dedicated,'ds' => 'TS3 DNS','gs' => $gsprache->gameserver,'my' => 'MySQL','us' => $gsprache->user,'vo' => $gsprache->voiceserver,'vs' => $gsprache->virtual);
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if ($user_language == 'de') {
            $date=date('Y-d-m H:m:s',strtotime($row['date']));
        } else {
            $date = $row['date'];
        }
        if ($row['api'] == 'A'){
            $api = $gsprache->yes;
        } else {
            $api = $gsprache->no;
        }
        if ($row['status'] == null or $row['status']==4) {
            $imgName = '16_ok';
            $imgAlt='Running';
        } else if ($row['status']==1) {
            $imgName = '16_bad';
            $imgAlt='Error';
        } else if ($row['status']==2) {
            $imgName='16_notice';
            $imgAlt='Canceled';
        } else if ($row['status']==3) {
            $imgName='16_check';
            $imgAlt='Done';
        }
        if ($row['action'] == 'ad') $action = $gsprache->add;
        else if ($row['action'] == 'dl') $action = $gsprache->del;
        else if ($row['action'] == 'md') $action = $gsprache->mod;
        else if ($row['action'] == 'st') $action='Stop';
        else if ($row['action'] == 're') $action='(Re)Start';
        else if ($row['action'] == 'rp') $action='Remove PXE from DHCP';
        else if ($row['action'] == 'ri') $action='(Re)Install';
        else if ($row['action'] == 'rc') $action='Recovery Mode';
        else $action = '';
        $table[] = array('jobID' => $row['jobID'], 'date' => $date,'name' => $row['name'], 'api' => $api,'status' => $row['status'], 'img' => $imgName,'alt' => $imgAlt,'userID' => $row['userID'], 'type' => $type[$row['type']], 'action' => $action);
    }
    $next = $start+$amount;
    if ($reseller_id == 0) {
        $countp = $sql->prepare("SELECT COUNT(`jobID`) AS `amount` FROM `jobs`");
        $countp->execute();
    } else {
        $countp = $sql->prepare("SELECT COUNT(`jobID`) AS `amount` FROM `jobs` WHERE `resellerID`=?");
        $countp->execute(array($reseller_id));
    }
    foreach ($countp->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $colcount = $row['amount'];
    }
    if ($colcount>$next) {
        $vor = $start+$amount;
    } else {
        $vor = $start;
    }
    $back = $start - $amount;
    if ($back>=0){
        $zur = $start - $amount;
    } else {
        $zur = $start;
    }
    $pageamount = ceil($colcount / $amount);
    $link='<a href="admin.php?w=jb&amp;o='.$o.'&amp;a=';
    if (!isset($amount)) {
        $link .="20";
    } else {
        $link .= $amount;
    }
    if ($start==0) {
        $link .= '&amp;p=0" class="bold">1</a>';
    } else {
        $link .= '&amp;p=0">1</a>';
    }
    $pages[] = $link;
    $i = 2;
    while ($i<=$pageamount) {
        $selectpage = ($i - 1) * $amount;
        if ($start==$selectpage) {
            $pages[] = '<a href="admin.php?w=jb&amp;o='.$o.'&amp;a=' . $amount . '&amp;p=' . $selectpage . '" class="bold">' . $i . '</a>';
        } else {
            $pages[] = '<a href="admin.php?w=jb&amp;o='.$o.'&amp;a=' . $amount . '&amp;p=' . $selectpage . '">' . $i . '</a>';
        }
        $i++;
    }
    $pages=implode(', ', $pages);
    $template_file = "admin_jobs_list.tpl";
}