<?php
/**
 * File: feeds.php.
 * Author: Ulrich Block
 * Date: 17.06.12
 * Time: 19:11
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
if ($reseller_id==0) {
    $logreseller = 0;
    $logsubuser = 0;
} else {
    if (isset($_SESSION['oldid'])) {
        $logsubuser = $_SESSION['oldid'];
    } else {
        $logsubuser = 0;
    }
    $logreseller = 0;
}
if ($reseller_id != 0 and $admin_id != $reseller_id) {
    $lookUpID = $admin_id;
} else {
    $lookUpID = $reseller_id;
}
if ($ui->w('action', 4, 'post') and !token(true)) {
    $template_file = $spracheResponse->token;
} else if ($ui->st('d', 'get') == 'se') {
    if ($ui->smallletters('action',2, 'post') == 'md'){
        if ($ui->active('active', 'post')) {
            $active = $ui->active('active', 'post');
        } else {
            $active = 'N';
        }
        if ($ui->active('displayContent', 'post')) {
            $displayContent = $ui->active('displayContent', 'post');
        } else {
            $displayContent = 'N';
        }
        if ($ui->active('limitDisplay', 'post')) {
            $limitDisplay = $ui->active('limitDisplay', 'post');
        } else {
            $limitDisplay = 'N';
        }
        if ($ui->active('useLocal', 'post')) {
            $useLocal = $ui->active('useLocal', 'post');
        } else {
            $useLocal = 'N';
        }
        if ($ui->active('merge', 'post')) {
            $merge = $ui->active('merge', 'post');
        } else {
            $merge = 'N';
        }
        if ($ui->active('steamFeeds', 'post')) {
            $steamFeeds = $ui->active('steamFeeds', 'post');
        } else {
            $steamFeeds = 'N';
        }
        if ($ui->id('maxChars',6, 'post')) {
            $maxChars = $ui->id('maxChars',6, 'post');
        } else {
            $maxChars=200;
        }
        if ($ui->id('newsAmount',3, 'post')) {
            $newsAmount = $ui->id('newsAmount',3, 'post');
        } else {
            $newsAmount=20;
        }
        if ($ui->w('orderBy',1, 'post')) {
            $orderBy = $ui->w('orderBy',1, 'post');
        } else {
            $orderBy='I';
        }
        if ($ui->id('updateMinutes',10, 'post')) {
            $updateMinutes = $ui->id('updateMinutes',10, 'post');
        } else {
            $updateMinutes=30;
        }
        if ($ui->id('maxKeep',11, 'post')) {
            $maxKeep = $ui->id('maxKeep',11, 'post');
        } else {
            $maxKeep=200;
        }
        $query = $sql->prepare("SELECT COUNT(`settingsID`) AS `amount` FROM `feeds_settings` WHERE `resellerID`=? LIMIT 1");
        $query->execute(array($lookUpID));
        if ($query->fetchColumn()>0) {
            $query = $sql->prepare("UPDATE `feeds_settings` SET `active`=?,`displayContent`=?,`limitDisplay`=?,`maxChars`=?,`merge`=?,`newsAmount`=?,`orderBy`=?,`updateMinutes`=?,`useLocal`=?,`maxKeep`=?,`steamFeeds`=? WHERE `resellerID`=? LIMIT 1");
        } else {
            $query = $sql->prepare("INSERT INTO `feeds_settings` (`active`,`displayContent`,`limitDisplay`,`maxChars`,`merge`,`newsAmount`,`orderBy`,`updateMinutes`,`useLocal`,`maxKeep`,`steamFeeds`,`resellerID`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
        }
        $query->execute(array($active,$displayContent,$limitDisplay,$maxChars,$merge,$newsAmount,$orderBy,$updateMinutes,$useLocal,$maxKeep,$steamFeeds,$lookUpID));
        $loguseraction="%mod% Feed Settings";
        $insertlog->execute();
    } else {
        $active = '';
        $displayContent = '';
        $limitDisplay = '';
        $maxChars = '';
        $merge = '';
        $newsAmount = '';
        $orderBy = '';
        $updateMinutes = '';
        $useLocal = '';
        $maxKeep = '';
        $query = $sql->prepare("SELECT * FROM `feeds_settings` WHERE `resellerID`=? LIMIT 1");
        $query->execute(array($lookUpID));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $active = $row['active'];
            $displayContent = $row['displayContent'];
            $limitDisplay = $row['limitDisplay'];
            $maxChars = $row['maxChars'];
            $merge = $row['merge'];
            $newsAmount = $row['newsAmount'];
            $orderBy = $row['orderBy'];
            $updateMinutes = $row['updateMinutes'];
            $useLocal = $row['useLocal'];
            $maxKeep = $row['maxKeep'];
            $steamFeeds = $row['steamFeeds'];
        }
    }
    $template_file = 'admin_feeds_settings.tpl';
} else if ($ui->st('d', 'get') == 'ad') {
    if ($ui->smallletters('action',2, 'post') == 'ad'){
        $active = $ui->active('active', 'post');
        $feedUrl = $ui->url('feedUrl', 'post');
        $loginName = $ui->w('loginName',255, 'post');
        $twitter = $ui->active('twitter', 'post');
        if ($twitter== 'Y') {
            $feedUrl='https://twitter.com/'.$loginName;
            $query = $sql->prepare("SELECT COUNT(`feedID`) AS `amount` FROM `feeds_url` WHERE `loginName`=? AND `resellerID`=? LIMIT 1");
            $query->execute(array($loginName,$lookUpID));
        } else {
            $query = $sql->prepare("SELECT COUNT(`feedID`) AS `amount` FROM `feeds_url` WHERE `feedUrl`=? AND `resellerID`=? LIMIT 1");
            $query->execute(array($feedUrl,$lookUpID));
        }
        if ($query->fetchColumn()>0) {
            $template_file = 'Error: Feed already exists';
        } else {
            $query = $sql->prepare("INSERT INTO `feeds_url` (`active`,`twitter`,`feedUrl`,`loginName`,`resellerID`) VALUES (?,?,?,?,?)");
            $query->execute(array($active,$twitter,$feedUrl,$loginName,$lookUpID));
            $loguseraction="%add% Feed $feedUrl";
            $insertlog->execute();
            $template_file = $spracheResponse->table_add;
        }
    } else {
        $template_file = 'admin_feeds_add.tpl';
    }
} else if ($ui->st('d', 'get') == 'dl' and $ui->id('id','19', 'get')) {
    $id = $ui->id('id','19', 'get');
    $query = $sql->prepare("SELECT `feedUrl` FROM `feeds_url` WHERE `feedID`=? AND `resellerID`=? LIMIT 1");
    $query->execute(array($id,$lookUpID));
    $feedUrl = $query->fetchColumn();
    if ($ui->smallletters('action',2, 'post') == 'dl'){
        $query = $sql->prepare("DELETE FROM `feeds_url` WHERE `feedID`=? AND `resellerID`=? LIMIT 1");
        $query->execute(array($id,$lookUpID));
        if ($query->rowCount()>0) {
            $query = $sql->prepare("DELETE FROM `feeds_news` WHERE `feedID`=? AND `resellerID`=?");
            $query->execute(array($id,$lookUpID));
            $loguseraction="%del% Feed $feedUrl";
            $insertlog->execute();
            $template_file = $spracheResponse->table_del;
        } else {
            $template_file = 'Error: Could not remove the Feed';
        }
    } else {
        $template_file = 'admin_feeds_dl.tpl';
    }
} else if ($ui->st('d', 'get') == 'md' and $ui->id('id','19', 'get')) {
    $id = $ui->id('id','19', 'get');
    if ($ui->smallletters('action',2, 'post') == 'md'){
        $feedUrl = $ui->url('feedUrl', 'post');
        $loginName = $ui->w('loginName',255, 'post');
        $twitter = $ui->active('twitter', 'post');
        if ($twitter== 'Y') {
            $feedUrl='https://twitter.com/'.$loginName;
            $query = $sql->prepare("SELECT COUNT(`feedID`) AS `amount` FROM `feeds_url` WHERE `loginName`=? AND `feedID`!=? AND `resellerID`=? LIMIT 1");
            $query->execute(array($loginName,$id,$lookUpID));
        } else {
            $query = $sql->prepare("SELECT COUNT(`feedID`) AS `amount` FROM `feeds_url` WHERE `feedUrl`=? AND `feedID`!=? AND `resellerID`=? LIMIT 1");
            $query->execute(array($feedUrl,$id,$lookUpID));
        }
        if ($query->fetchColumn()>0) {
            $template_file = 'Error: Feed already exists';
        } else {
            $active = $ui->active('active', 'post');
            $query = $sql->prepare("UPDATE `feeds_url` SET `active`=?,`twitter`=?,`feedUrl`=?,`loginName`=? WHERE `feedID`=? AND `resellerID`=? LIMIT 1");
            $query->execute(array($active,$twitter,$feedUrl,$loginName,$id,$lookUpID));
            $loguseraction="%mod% Feed $feedUrl";
            $insertlog->execute();
            $template_file = $spracheResponse->table_add;
        }
    } else {
        $query = $sql->prepare("SELECT `active`,`twitter`,`feedUrl`,`loginName` FROM `feeds_url` WHERE `feedID`=? AND `resellerID`=? LIMIT 1");
        $query->execute(array($id,$lookUpID));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $active = $row['active'];
            $twitter = $row['twitter'];
            $feedUrl = $row['feedUrl'];
            $loginName = $row['loginName'];
        }
        $template_file = 'admin_feeds_md.tpl';
    }
} else {
    $table = array();
    $o = $ui->st('o', 'get');
    if ($ui->st('o', 'get') == 'au') {
        $orderby = '`feedUrl` ASC';
    } else if ($ui->st('o', 'get') == 'du') {
        $orderby = '`feedUrl` DESC';
    } else if ($ui->st('o', 'get') == 'as') {
        $orderby = '`active` ASC';
    } else if ($ui->st('o', 'get') == 'ds') {
        $orderby = '`active` DESC';
    } else if ($ui->st('o', 'get') == 'at') {
        $orderby = '`twitter` ASC';
    } else if ($ui->st('o', 'get') == 'dt') {
        $orderby = '`twitter` DESC';
    } else if ($ui->st('o', 'get') == 'ai') {
        $orderby = '`feedID` ASC';
    } else {
        $orderby = '`feedID` DESC';
        $o = 'di';
    }
    $query = $sql->prepare("SELECT `feedID`,`active`,`twitter`,`feedUrl` FROM `feeds_url` WHERE `resellerID`=? ORDER BY $orderby LIMIT $start,$amount");
    $query->execute(array($lookUpID));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if ($row['active'] == 'Y') {
            $imgName='16_ok';
            $imgAlt='Active';
        } else {
            $imgName='16_bad';
            $imgAlt='Inactive';
        }
        $twitter=($row['twitter'] == 'Y') ? $gsprache->yes : $gsprache->no;
        $table[] = array('id' => $row['feedID'], 'img' => $imgName,'alt' => $imgAlt,'twitter' => $twitter,'feedUrl' => $row['feedUrl'], 'active' => $row['active']);
    }
    $next = $start+$amount;
    $countp = $sql->prepare("SELECT COUNT(`feedID`) AS `amount` FROM `feeds_url` WHERE `resellerID`=?");
    $countp->execute(array($lookUpID));
    $colcount = $countp->fetchColumn();
    if ($colcount>$next) {
        $vor = $start+$amount;
    } else {
        $vor = $start;
    }
    $back = $start - $amount;
    if ($back>="0"){
        $zur = $start - $amount;
    } else {
        $zur = $start;
    }
    $pageamount = ceil($colcount / $amount);
    $link='<a href="admin.php?w=fe&amp;d=md&amp;a=';
    if(!isset($amount)) {
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
            $pages[] = '<a href="admin.php?w=fe&amp;d=md&amp;a=' . $amount . '&p=' . $selectpage . '" class="bold">' . $i . '</a>';
        } else {
            $pages[] = '<a href="admin.php?w=fe&amp;d=md&amp;a=' . $amount . '&p=' . $selectpage . '">' . $i . '</a>';
        }
        $i++;
    }
    $pages=implode(', ',$pages);
    $template_file = 'admin_feeds_list.tpl';
}