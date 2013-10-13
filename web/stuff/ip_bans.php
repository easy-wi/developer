<?php
/**
 * File: ip_bans.php.
 * Author: Ulrich Block
 * Date: 07.04.12
 * Time: 19:12
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

if ((!isset($admin_id) or $main != 1) or (isset($admin_id) and !$pa['ipBans'] and $reseller_id != 0)) {
    header('Location: admin.php');
    die('No acces');
}
$sprache = getlanguagefile('logs',$user_language,$reseller_id);
$gssprache = getlanguagefile('gserver',$user_language,$reseller_id);
if (isset($action) and $action == 'dl') {
    $i = 0;
    if ($ui->id('id',30, 'post')) {
        if (token(true)) {
            $query = $sql->prepare("DELETE FROM `badips` WHERE `id`=? LIMIT 1");
            foreach ($ui->id('id',30, 'post') as $id) {
                $query->execute(array($id));
                $i++;
            }
        } else $template_file = $spracheResponse->token;
    }
    if(!isset($template_file)) $template_file = $i." entries deleted";
} else {
    $table = array();
    $o = $ui->st('o', 'get');
    if ($ui->st('o', 'get') == 'dr') {
        $orderby = '`reason` DESC';
    } else if ($ui->st('o', 'get') == 'ar') {
        $orderby = '`reason` ASC';
    } else if ($ui->st('o', 'get') == 'df') {
        $orderby = '`failcount` DESC';
    } else if ($ui->st('o', 'get') == 'af') {
        $orderby = '`failcount` ASC';
    } else if ($ui->st('o', 'get') == 'dt') {
        $orderby = '`bantime` DESC';
    } else if ($ui->st('o', 'get') == 'at') {
        $orderby = '`bantime` ASC';
    } else if ($ui->st('o', 'get') == 'db') {
        $orderby = '`badip` DESC';
    } else if ($ui->st('o', 'get') == 'ab') {
        $orderby = '`badip` ASC';
    } else if ($ui->st('o', 'get') == 'ai') {
        $orderby = '`id` ASC';
    } else {
        $o = 'ai';
        $orderby = '`id` DESC';
    }
    $pselect = $sql->prepare("SELECT * FROM `badips` ORDER BY $orderby LIMIT $start,$amount");
    $pselect->execute();
    foreach ($pselect->fetchall() as $row) {
        $logdate=explode(' ', $row['bantime']);
        if (isset($row['id']) and isid($row['id'], '30') and isset($logdate[1])) {
            $table[] = array('id' => $row['id'], 'logday' => $logdate[0], 'loghour' => $logdate[0], 'badip' => $row['badip'], 'failcount' => $row['failcount'], 'reason' => $row['reason']);
        }
    }
    $pselect = $sql->prepare("SELECT `faillogins` FROM `settings` WHERE `resellerid`='0' LIMIT 1");
    $pselect->execute();
    foreach ($pselect->fetchall() as $row) {
        $faillogins = $row['faillogins'];
    }
    $next = $start+$amount;
    $countp = $sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `badips`");
    $countp->execute();
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
    $link='<a href="admin.php?w=ib&amp;d='.$d.'&amp;a=';
    if(!isset($amount)) {
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
            $pages[] = '<a href="admin.php?w=ib&amp;d='.$d.'&amp;a=' . $amount . '&amp;p=' . $selectpage . '" class="bold">' . $i . '</a>';
        } else {
            $pages[] = '<a href="admin.php?w=ib&amp;d='.$d.'&amp;a=' . $amount . '&amp;p=' . $selectpage . '">' . $i . '</a>';
        }
        $i++;
    }
    $pages=implode(', ',$pages);
    $template_file = "admin_ip_bans.tpl";
}
?>