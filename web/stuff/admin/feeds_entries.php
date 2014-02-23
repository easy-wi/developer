<?php

/**
 * File: feeds_entries.php.
 * Author: Ulrich Block
 * Date: 30.06.12
 * Time: 12:24
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

include(EASYWIDIR . '/stuff/keyphrasefile.php');

$sprache = getlanguagefile('feeds',$user_language,$reseller_id);
$loguserid = $admin_id;
$logusername = getusername($admin_id);
$logusertype = 'admin';
if ($reseller_id == 0) {
    $logreseller = 0;
    $logsubuser = 0;
} else {
    $logsubuser=(isset($_SESSION['oldid'])) ? $_SESSION['oldid'] : 0;
    $logreseller = 0;
}
$lookUpID=($reseller_id != 0 and $admin_id != $reseller_id) ? $admin_id: $reseller_id;
if ($ui->st('d', 'get') == 'ud') {
    $newsInclude = true;
    include(EASYWIDIR . '/stuff/methods/feeds_function.php');
} else if ($ui->st('d', 'get') == 'md') {
    $ids=(array)$ui->active('ids', 'post');
    $delete = $sql->prepare("DELETE FROM `feeds_news` WHERE `newsID`=? AND `resellerID`=? LIMIT 1");
    $update = $sql->prepare("UPDATE `feeds_news` SET `active`=? WHERE `newsID`=? AND `resellerID`=?");
    foreach($ids as $id=>$values) {
        if (isset($values->dl) and $values->dl== 'Y') {
            $delete->execute(array($id,$lookUpID));
        } else {
            if (isset($values->active) and $values->active == 'Y') {
                $update->execute(array('Y',$id,$lookUpID));
            } else {
                $update->execute(array('N',$id,$lookUpID));
            }
        }
    }
    $template_file = $spracheResponse->table_add;
} else {
    $table = array();
    $o = $ui->st('o', 'get');
    if ($ui->st('o', 'get') == 'au') {
        $orderby = 'u.`feedUrl` ASC';
    } else if ($ui->st('o', 'get') == 'du') {
        $orderby = 'u.`feedUrl` DESC';
    } else if ($ui->st('o', 'get') == 'ah') {
        $orderby = 'n.`title` ASC,n.`description` ASC';
    } else if ($ui->st('o', 'get') == 'dh') {
        $orderby = 'n.`title` DESC,n.`description` DESC';
    } else if ($ui->st('o', 'get') == 'as') {
        $orderby = 'n.`active` ASC';
    } else if ($ui->st('o', 'get') == 'ds') {
        $orderby = 'n.`active` DESC';
    } else if ($ui->st('o', 'get') == 'at') {
        $orderby = 'u.`twitter` ASC';
    } else if ($ui->st('o', 'get') == 'dt') {
        $orderby = 'u.`twitter` DESC';
    } else if ($ui->st('o', 'get') == 'ai') {
        $orderby = 'n.`newsID` ASC';
    } else if ($ui->st('o', 'get') == 'di') {
        $orderby = 'n.`newsID` DESC';
    } else if ($ui->st('o', 'get') == 'ad') {
        $orderby = 'n.`pubDate` ASC';
    } else {
        $orderby = 'n.`pubDate` DESC';
        $o = 'dd';
    }
    $query = $sql->prepare("SELECT n.`newsID`,n.`active`,n.`title`,n.`link`,n.`pubDate`,n.`description`,u.`twitter`,u.`feedUrl` FROM `feeds_news` n LEFT JOIN `feeds_url` u ON n.`feedID`=u.`feedID` WHERE n.`resellerID`=? ORDER BY $orderby LIMIT $start,$amount");
    $query->execute(array($lookUpID));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if ($row['active'] == 'Y') {
            $imgName = '16_ok';
            $imgAlt = 'Active';
        } else {
            $imgName = '16_bad';
            $imgAlt = 'Inactive';
        }
        $twitter=($row['twitter'] == 'Y') ? $gsprache->yes : $gsprache->no;
        $title = $row['title'];
        if (strlen($row['title'])<=1) $title = $row['link'];
        $table[] = array('id' => $row['newsID'], 'active' => $row['active'], 'img' => $imgName,'alt' => $imgAlt,'pubDate' => $row['pubDate'], 'twitter' => $twitter,'title' => $title,'link' => $row['link'], 'feedUrl' => $row['feedUrl']);
    }
    $next = $start+$amount;
    $query = $sql->prepare("SELECT COUNT(`newsID`) AS `amount` FROM `feeds_news` WHERE `resellerID`=?");
    $query->execute(array($lookUpID));
    $colcount = $query->fetchColumn();
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
    $link='<a href="admin.php?w=fn&amp;a=';
    if (!isset($amount)) {
        $link .="20";
    } else {
        $link .= $amount;
    }
    if ($start==0) {
        $link .= '&p=0" class="bold">1</a>';
    } else {
        $link .= '&p=0">1</a>';
    }
    $pages[] = $link;
    $i = 2;
    while ($i<=$pageamount) {
        $selectpage = ($i - 1) * $amount;
        if ($start==$selectpage) {
            $pages[] = '<a href="admin.php?w=fn&amp;a=' . $amount . '&p=' . $selectpage . '" class="bold">' . $i . '</a>';
        } else {
            $pages[] = '<a href="admin.php?w=fn&amp;a=' . $amount . '&p=' . $selectpage . '">' . $i . '</a>';
        }
        $i++;
    }
    $pages=implode(', ',$pages);
    $template_file = 'admin_feeds_entries_list.tpl';
}