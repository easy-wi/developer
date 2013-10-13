<?php
/**
 * File: page_downloads.php.
 * Author: Ulrich Block
 * Date: 25.08.13
 * Time: 19:48
 * Contact: <ulrich.block@easy-wi.com>
 * Ticket: https://github.com/easy-wi/developer/issues/11
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
if ((!isset($admin_id) or $main!=1) or (isset($admin_id) and !$pa['cms_pages']) or $reseller_id != 0) {
    header('Location: admin.php');
    die;
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
} else if ($ui->id('id', 10, 'get') or $ui->st('d', 'get') == 'ad') {
    $id = $ui->id('id', 10, 'get');
    $template_file = 'uadmin_404.tpl';
    $foundLanguages = array();
    $query = $sql->prepare("SELECT `lang`,`text` FROM `translations` WHERE `type`='pd' AND `transID`=? AND `lang`=? AND `resellerID`=? LIMIT 1");
    foreach ($languages as $row) {
        if (small_letters_check($row,2)) {
            $description = '';
            unset($lang);
            if($ui->id('id', 10, 'get')) {
                $query->execute(array($id, $row,$reseller_id));
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                    $lang = $row2['lang'];
                    $description = $row2['text'];
                }
            }
            if ((!$ui->id('id', 10, 'get') and $row==$rSA['language']) or isset($lang)) {
                $style = '';
                $displayNone = '';
                $checkbox='<input type="checkbox" name="language[]" value="'.$row.'" onclick="textdrop('."'".$row."'".');" checked /> ';
            } else {
                $style='style="display: none;"';
                $displayNone='display_none';
                $checkbox='<input type="checkbox" name="language[]" value="'.$row.'" onclick="textdrop('."'".$row."'".');" /> ';
            }
            $foundLanguages[] = array('style' => $style,'lang' => $row,'checkbox' => $checkbox,'description' => $description,'display' => $displayNone);
        }
    }
    if (!$ui->st('action', 'post') and $ui->st('d', 'get') == 'ad') {
        $template_file = 'admin_page_downloads_add.tpl';
    } else if (!$ui->st('action', 'post') and $ui->id('id', 10, 'get') and ($ui->st('d', 'get') == 'md' or  $ui->st('d', 'get') == 'dl')) {
        $query = $sql->prepare("SELECT * FROM `page_downloads` WHERE `fileID`=? AND `resellerID`=? LIMIT 1");
        $query->execute(array($id,$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $show = $row['show'];
            $order = $row['order'];
            $count = $row['count'];
            $description = $row['description'];
            $fileExtension = $row['fileExtension'];
            $fileName = $row['fileName'];
            $date = $row['date'];
            $template_file = ($ui->st('d', 'get') == 'md') ? 'admin_page_downloads_mod.tpl' : 'admin_page_downloads_del.tpl';
        }
    } else if ($ui->st('action', 'post') == 'ad' or ($ui->st('action', 'post') == 'md' and $ui->id('id', 10, 'get'))) {
        $id = $ui->id('id', 10, 'get');
        if ($ui->st('action', 'post') == 'ad') {
            $query = $sql->prepare("SELECT 1 FROM `page_downloads` WHERE `fileName`=? LIMIT 1");
            $query->execute(array($ui->names('fileName',255, 'post')));
            if ($query->rowCount()>0) {
                $template_file = $spracheResponse->error_name;
            } else {
                $query = $sql->prepare("INSERT INTO `page_downloads` (`show`,`description`,`fileName`,`date`,`resellerID`) VALUES (?,?,?,NOW(),?)");
                $query->execute(array($ui->w('show',1, 'post'),$ui->names('description',255, 'post'),$ui->names('fileName',255, 'post'),$reseller_id));
                if($query->rowCount()>0) $changed = true;
                $template_file = $spracheResponse->table_add;
                $id = $sql->lastInsertId();
            }
        } else if ($ui->st('action', 'post') == 'md' and $ui->id('id', 10, 'get')) {
            $query = $sql->prepare("UPDATE `page_downloads` SET `show`=?,`description`=?,`fileName`=? WHERE `fileID`=? AND `resellerID`=? LIMIT 1");
            $query->execute(array($ui->w('show',1, 'post'),$ui->names('description',255, 'post'),$ui->names('fileName',255, 'post'),$id,$reseller_id));
            if ($query->rowCount()>0) $changed = true;
        }
        if ($id) {
            if ($_FILES["upload"]["error"]===0) {
                $allowedTypes=array('pdf' => array('application/pdf','application/x-download'),'xls' => array('application/excel','application/vnd.ms-excel'),
                    'ppt' => 'application/powerpoint',
                    'gz' => 'application/x-gzip',
                    'tar' => 'application/x-tar',
                    'tgz' => 'application/x-tar',
                    'zip' => array('application/x-zip','application/zip','application/x-zip-compressed','application/octet-stream'),
                    'xml' => 'text/xml',
                    'xsl' => 'text/xml',
                    'mpeg' => 'video/mpeg',
                    'mpg' => 'video/mpeg',
                    'mpe' => 'video/mpeg',
                    'qt' => 'video/quicktime',
                    'mov' => 'video/quicktime',
                    'avi' => 'video/x-msvideo',
                    'movie' => 'video/x-sgi-movie',
                    'doc' => 'application/msword',
                    'word' => array('application/msword','application/octet-stream'),
                    'xl' => 'application/excel',
                    'rar' => 'application/x-rar-compressed');
                $exploded=explode('.',$_FILES["upload"]["name"]);
                $extension = $exploded[count($exploded)-1];
                if(isset($allowedTypes[$extension]) and ((is_array($allowedTypes[$extension]) and in_array($_FILES["upload"]["type"],$allowedTypes[$extension])) or (!is_array($allowedTypes[$extension]) and $_FILES["upload"]["type"] == $allowedTypes[$extension])) ) {
                    if (move_uploaded_file($_FILES["upload"]["tmp_name"],EASYWIDIR . '/downloads/'.$id . '.' . $extension)) {
                        $changed = true;
                        $query = $sql->prepare("UPDATE `page_downloads` SET `fileExtension`=? WHERE `fileID`=? AND `resellerID`=?");
                        $query->execute(array($extension,$id,$reseller_id));
                    }
                }
            }
            if ($ui->smallletters('language',2, 'post')) {
                $array=(array)$ui->smallletters('language',2, 'post');
                $query = $sql->prepare("INSERT INTO `translations` (`type`,`transID`,`lang`,`text`,`resellerID`) VALUES ('pd',?,?,?,?) ON DUPLICATE KEY UPDATE `text`=VALUES(`text`)");
                foreach($array as $language) {
                    if (small_letters_check($language,2)) {
                        $query->execute(array($id,$language,$ui->description('text', 'post',$language),$reseller_id));
                        if($query->rowCount()>0) $changed = true;
                    }
                }
                $query = $sql->prepare("SELECT `lang` FROM `translations` WHERE `type`='pd' AND `transID`=? AND `resellerID`=?");
                $query->execute(array($id,$reseller_id));
                $query2 = $sql->prepare("DELETE FROM `translations` WHERE `type`='pd' AND `transID`=? AND `lang`=? AND `resellerID`=? LIMIT 1");
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    if (!in_array($row['lang'],$array)) {
                        $query2->execute(array($addonid, $row['lang'],$reseller_id));
                        if($query2->rowCount()>0) $changed = true;
                    }
                }
            } else {
                $query = $sql->prepare("DELETE FROM `translations` WHERE `type`='pd' AND `transID`=? AND `resellerID`=?");
                $query->execute(array($id,$reseller_id));
                if($query->rowCount()>0) $changed = true;
            }
            $template_file = (isset($changed)) ? $spracheResponse->table_add : $spracheResponse->error_table;
        } else {
            $template_file = $spracheResponse->error_table;
        }
    } else if ($ui->st('action', 'post') == 'dl' and $ui->id('id', 10, 'get')) {
        $query = $sql->prepare("DELETE FROM `page_downloads` WHERE `fileID`=? AND `resellerID`=? LIMIT 1");
        $query->execute(array($id,$reseller_id));
        $template_file = ($query->rowCount()>0) ? $spracheResponse->table_del : 'admin_404.tpl';
        $query = $sql->prepare("DELETE FROM `translations` WHERE `type`='pd' AND `transID`=? AND `resellerID`=?");
        $query->execute(array($id,$reseller_id));
        unlink(EASYWIDIR . "/downloads/${id}/${fileExtension}");
        $template_file = $spracheResponse->table_del;
    }
} else {
    if ($ui->w('downloadOrder',4, 'post') == 'true') {
        $query = $sql->prepare("UPDATE `page_downloads` SET `order`=? WHERE `fileID`=? LIMIT 1");
        foreach ($ui->id('downloadID',10, 'post') as $id => $order) $query->execute(array($order,$id));
    }
    $o = $ui->st('o', 'get');
    if ($ui->st('o', 'get') == 'dt') $orderby = '`description` DESC';
    else if ($ui->st('o', 'get') == 'at') $orderby = '`description` ASC';
    else if ($ui->st('o', 'get') == 'dc') $orderby = '`count` DESC';
    else if ($ui->st('o', 'get') == 'ac') $orderby = '`count` ASC';
    else if ($ui->st('o', 'get') == 'ds') $orderby = '`order` DESC';
    else if ($ui->st('o', 'get') == 'as') $orderby = '`order` ASC';
    else if ($ui->st('o', 'get') == 'di') $orderby = '`fileID` DESC';
    else{
        $o = 'ai';
        $orderby = '`fileID` ASC';
    }
    $query = $sql->prepare("SELECT COUNT(`fileID`) AS `amount` FROM `page_downloads` WHERE `resellerID`=?");
    $query->execute(array($reseller_id));
    $colcount = $query->fetchColumn();
    $start=($ui->isinteger('p', 'get')>0) ? $ui->isinteger('p', 'get') : 0;
    $a=($ui->isinteger('a', 'get')>$colcount) ? $colcount : $ui->isinteger('a', 'get');
    $next = $start+$a;
    $vor=($colcount>$next) ? $start+$a : $start;
    $back = $start-$a;
    $zur = ($back >= 0) ? $start-$a : $start;
    $pageamount=($a>0) ? ceil($colcount/$a) : 1;
    $pages[] = '<a href="admin.php?w=vo&amp;d=md&amp;o='.$o.'&amp;a=' . (!isset($a)) ? 20 : $a . ($start==0) ? '&p=0" class="bold">1</a>' : '&p=0">1</a>';
    $i = 2;
    while ($i<=$pageamount) {
        $selectpage=($i-1)*$a;
        $pages[]=($start==$selectpage) ? '<a href="admin.php?w=vo&amp;d=md&amp;o='.$o.'&amp;a='.$a.'&p=' . $selectpage . '" class="bold">' . $i . '</a>' : '<a href="admin.php?w=vo&amp;d=md&amp;o='.$o.'&amp;a='.$a.'&p=' . $selectpage . '">' . $i . '</a>';
        $i++;
    }
    $pages=implode(', ',$pages);
    $table = array();
    $query = $sql->prepare("SELECT `fileID`,`description`,`order`,`count` FROM `page_downloads` WHERE `resellerID`=? ORDER BY ${orderby}");
    $query->execute(array($reseller_id));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $table[] = array('id' => $row['fileID'], 'description' => $row['description'], 'order' => $row['order'], 'count' => $row['count']);
    }
    $template_file = 'admin_page_downloads_list.tpl';
}