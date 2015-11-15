<?php

/**
 * File: page_comments.php.
 * Author: Ulrich Block
 * Date: 11.11.12
 * Time: 13:27
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

if ((!isset($admin_id) or $main != 1) or (isset($admin_id) and !$pa['cms_pages']) or $reseller_id != 0) {
    redirect('admin.php');
}
$sprache = getlanguagefile('page',$user_language,$reseller_id);
$loguserid = $admin_id;
$logusername = getusername($admin_id);
$logusertype = 'admin';
$logreseller = 0;
$logsubuser = 0;
$logsubuser = 0;

if ($ui->w('action', 4, 'post') and !token(true)) {
    $template_file = $spracheResponse->token;
} else if ($ui->st('d', 'get') == 'md' and $ui->id('id',19, 'get') and $ui->smallletters('action',2, 'post') == 'md'){
    $id = $ui->id('id',19, 'get');
    $url = '';
    if ($ui->url('url', 'post')) {
        $url = $ui->url('url', 'post');
    }
    if ($ui->domain('url', 'post')) {
        $url='http://'.$ui->domain('url', 'post');
    }
    $query = $sql->prepare("UPDATE `page_comments` SET `homepage`=?,`markedSpam`=?,`moderateAccepted`=?,`comment`=? WHERE `commentID`=? AND `resellerID`=? LIMIT 1");
    $query->execute(array($url,$ui->active('markedSpam', 'post'),$ui->active('moderateAccepted', 'post'),$ui->post['comment'],$id,$reseller_id));
    if ($query->rowCount() > 0) {
        $loguseraction='%mod% %comment% '.$ui->id('id',19, 'get');
        $insertlog->execute();
        $template_file = $spracheResponse->table_add;
    } else {
        $template_file = $spracheResponse->error_table;
    }
} else if ($ui->st('d', 'get') == 'md' and $ui->id('id',19, 'get') and !$ui->smallletters('action',2, 'post')){
    $id = $ui->id('id',19, 'get');
    $query = $sql->prepare("SELECT t.`pageid`,t.`title`,c.* FROM `page_comments` c LEFT JOIN `page_pages_text` t ON c.`pageTextID`=t.`id` WHERE c.`commentID`=? AND c.`resellerID`=? LIMIT 1");
    $query->execute(array($id,$reseller_id));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $commentDate='m.d.Y H:i';
        if ($user_language == 'de') $commentDate='d.m.Y H:i';
        $date=date($commentDate,strtotime($row['date']));
        $comment=htmlentities($row['comment']);
        $authorname=htmlentities($row['authorname']);
        $email = $row['email'];
        $homepage = $row['homepage'];
        $ip = $row['ip'];
        $dns = $row['dns'];
        $markedSpam = $row['markedSpam'];
        $spamReason = $row['spamReason'];
        $moderateAccepted = $row['moderateAccepted'];
    }
    if (isset($comment)) {
        $template_file = 'admin_page_comments_md.tpl';
    } else {
        $template_file = 'admin_404.tpl';
    }
} else if ($ui->st('d', 'get') == 'dl' and $ui->id('id',19, 'get')){
    $query = $sql->prepare("DELETE FROM `page_comments` WHERE `commentID`=? AND `resellerID`=? LIMIT 1");
    $query->execute(array($ui->id('id',19, 'get'),$reseller_id));
    if ($query->rowCount() > 0) {
        $loguseraction='%del% %comment% '.$ui->id('id',19, 'get');
        $insertlog->execute();
        $template_file = $spracheResponse->table_del;
    } else {
        $template_file = $spracheResponse->error_table;
    }
} else {

    configureDateTables('-1', '1, "desc"', 'ajax.php?w=datatable&d=pagecomments');

    $template_file = 'admin_page_comments_list.tpl';
}