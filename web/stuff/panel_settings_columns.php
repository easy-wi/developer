<?php
/**
 * File: panel_settings_columns.php.
 * Author: Ulrich Block
 * Date: 16.03.13
 * Time: 20:56
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

if ((!isset($admin_id) or !isset($reseller_id) or $main!=1) or (isset($admin_id) and !$pa['settings']) or $reseller_id!=0) {
    redirect('admin.php');
}
$loguserid=$admin_id;
$logusername=getusername($admin_id);
$logusertype='admin';
$logreseller=0;
$logsubuser=0;
$sprache=getlanguagefile('columns',$user_language,$reseller_id,$sql);
if ($ui->w('action',4,'post') and !token(true)) {
    $template_file=$spracheResponse->token;
} else if(in_array($ui->st('d','get'),array('ad','md'))) {
    $id=$ui->id('id',10,'get');
    if (in_array($ui->st('action','post'),array('ad','md'))) {
        $error=array();
        if (!$ui->active('active','post')) {
            $error[]='Active';
        }
        if (!$ui->id('length',10,'post')) {
            $error[]='Length';
        }
        if (!$ui->w('item',1,'post')) {
            $error[]='Item';
        }
        if (!$ui->w('type',1,'post')) {
            $error[]='Type';
        }
        if (!$ui->w('name',255,'post')) {
            $error[]='Name';
        }
        if (count($error)>0) {
            $template_file='Error: '.implode('<br />',$error);
        } else {
            $name=$ui->w('name',255,'post');
            if($ui->st('d','get')=='ad') {
                $query=$sql->prepare("INSERT INTO `custom_columns_settings` (`active`,`item`,`type`,`length`,`name`) VALUES (?,?,?,?,?)");
                $query->execute(array($ui->active('active','post'),$ui->w('item',1,'post'),$ui->w('type',1,'post'),$ui->id('length',10,'post'),$name));
                $id=$sql->lastInsertId();
                $loguseraction="%add% Custom Column ${name}";
            } else if ($ui->id('id',10,'get') and $ui->st('d','get')=='md') {
                $query=$sql->prepare("UPDATE `custom_columns_settings` SET `active`=?,`item`=?,`type`=?,`length`=?,`name`=? WHERE `customID`=? LIMIT 1");
                $query->execute(array($ui->active('active','post'),$ui->w('item',1,'post'),$ui->w('type',1,'post'),$ui->id('length',10,'post'),$name,$id));
                $loguseraction="%mod% Custom Column ${name}";
            } else {
                $template_file='admin_404.tpl';
            }
            if (!isset($template_file) and $query->rowCount()>0) {
                $array=array();
                $query=$sql->prepare("INSERT INTO `translations` (`type`,`transID`,`lang`,`text`,`resellerID`) VALUES ('cc',?,?,?,0) ON DUPLICATE KEY UPDATE `text`=VALUES(`text`)");
                if ($ui->smallletters('language',2,'post')) {
                    $array=(array)$ui->smallletters('language',2,'post');
                    foreach($array as $language) {
                        $query->execute(array($id,$language,$ui->description('menu','post',$language)));
                    }
                }
                $query=$sql->prepare("SELECT `lang` FROM `translations` WHERE `type`='cc' AND `transID`=?");
                $query->execute(array($id));
                $query2=$sql->prepare("DELETE FROM `translations` WHERE `type`='cc' AND `transID`=? AND `lang`=? LIMIT 1");
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                    if (!in_array($row2['lang'],$array)) {
                        $query2->execute(array($id,$row2['lang']));
                    }
                }
                $insertlog->execute();
                $template_file=$spracheResponse->table_add;
            } else if (!isset($template_file)) {
                $template_file=$spracheResponse->error_table;
            }
        }
    } else if ($ui->st('d','get')=='ad') {
        foreach ($languages as $row) {
            if (small_letters_check($row,2)) {
                if ($row==$default_language) {
                    $style='';
                    $class='';
                    $checkbox='<input id="inputCheckbox'.$row.'" type="checkbox" name="language[]" value="'.$row.'" onclick="textdrop('."'".$row."'".');" checked /> ';
                } else {
                    $class='display_none';
                    $style='class="display_none"';
                    $checkbox='<input id="inputCheckbox'.$row.'" type="checkbox" name="language[]" value="'.$row.'" onclick="textdrop('."'".$row."'".');" /> ';
                }
                $foundlanguages[]=array('style'=>$style,'class'=>$class,'lang'=>$row,'checkbox'=>$checkbox);
            }
        }
        $template_file='admin_settings_columns_add.tpl';
    } else if ($ui->id('id',10,'get') and $ui->st('d','get')=='md') {
        $query=$sql->prepare("SELECT * FROM `custom_columns_settings` WHERE `customID`=? LIMIT 1");
        $query->execute(array($id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $active=$row['active'];
            $item=$row['item'];
            $type=$row['type'];
            $length=$row['length'];
            $name=$row['name'];
        }
        $query2=$sql->prepare("SELECT `lang`,`text` FROM `translations` WHERE `type`='cc' AND `transID`=? AND `lang`=? LIMIT 1");
        foreach ($languages as $ln) {
            $lang='';
            $text='';
            $query2->execute(array($id,$ln));
            foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                $lang=$row2['lang'];
                $text=$row2['text'];
            }
            if (empty($lang)) {
                $style='class="display_none"';
                $class='display_none';
                $checkbox="<input id=\"inputCheckbox$ln\" type=\"checkbox\" name=\"language[]\" value=\"$ln\" onclick=\"textdrop('$ln');\" /> ";
            } else {
                $style='';
                $class='';
                $checkbox="<input id=\"inputCheckbox$ln\" type=\"checkbox\" name=\"language[]\" value=\"$ln\" onclick=\"textdrop('$ln');\" checked /> ";
            }
            $foundlanguages[]=array('style'=>$style,'class'=>$class,'lang'=>$ln,'checkbox'=>$checkbox,'text'=>$text);
        }
        $template_file=(isset($active)) ? 'admin_settings_columns_md.tpl' : 'admin_404.tpl';
    }
} else if ($ui->id('id',10,'get') and $ui->st('d','get')=='dl') {
    $id=$ui->id('id',10,'get');
    $query=$sql->prepare("SELECT `name` FROM `custom_columns_settings` WHERE `customID`=? LIMIT 1");
    $query->execute(array($id));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $name=$row['name'];
    }
    if (!$ui->st('action','post') and isset($name)) {
        $template_file='admin_settings_columns_dl.tpl';
    } else if ($ui->st('action','post')=='dl' and isset($name)) {
        $query=$sql->prepare("DELETE FROM `custom_columns_settings` WHERE `customID`=? LIMIT 1");
        $query->execute(array($id));
        if ($query->rowCount()>0) {
            $query=$sql->prepare("DELETE FROM `custom_columns` WHERE `customID`=?");
            $query->execute(array($id));
            $query=$sql->prepare("DELETE FROM `translations` WHERE `type`='cc' AND `transID`=?");
            $query->execute(array($id));
            $loguseraction="%del% Custom Column ${name}";
            $insertlog->execute();
            $template_file=$spracheResponse->table_del;
        } else {
            $template_file=$spracheResponse->error_table;
        }
    } else {
        $template_file='admin_404.tpl';
    }
} else {
    $table=array();
    $query=$sql->prepare("SELECT COUNT(`customID`) AS `amount` FROM `custom_columns_settings`");
    $query->execute();
    $colcount=$query->fetchColumn();
    if ($start>$colcount) {
        $start=$colcount-$amount;
        if ($start<0) {
            $start=0;
        }
    }
    $next=$start+$amount;
    if ($colcount>$next) {
        $vor=$start+$amount;
    } else {
        $vor=$start;
    }
    $back=$start-$amount;
    if ($back>=0){
        $zur=$start-$amount;
    } else {
        $zur=$start;
    }
    $o=$ui->st('o','get');
    if ($ui->st('o','get')=='da') {
        $orderby='`active` DESC';
    } else if ($ui->st('o','get')=='aa') {
        $orderby='`active` ASC';
    } else if ($ui->st('o','get')=='dn') {
        $orderby='`name` DESC';
    } else if ($ui->st('o','get')=='an') {
        $orderby='`name`';
    } else if ($ui->st('o','get')=='dt') {
        $orderby='`type` DESC';
    } else if ($ui->st('o','get')=='at') {
        $orderby='`type` ASC';
    } else if ($ui->st('o','get')=='di') {
        $orderby='`customID` DESC';
    } else {
        $orderby='`customID` ASC';
        $o='ai';
    }
    $query=$sql->prepare("SELECT * FROM `custom_columns_settings` ORDER BY $orderby LIMIT $start,$amount");
    $query->execute();
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if ($row['active']=='Y') {
            $imgName='16_ok';
            $imgAlt='Active';
        } else {
            $imgName='16_bad';
            $imgAlt='Inactive';
        }
        $type=($row['type']=='I') ? $sprache->int : $sprache->var;
        $table[]=array('id'=>$row['customID'],'img'=>$imgName,'alt'=>$imgAlt,'name'=>$row['name'],'type'=>$type,'active'=>$row['active']);
    }
    $pageamount=ceil($colcount/$amount);
    $link='<a href="admin.php?w=cc&amp;o='.$o.'&amp;a=';
    if(!isset($amount)) {
        $link .=20;
    } else {
        $link .=$amount;
    }
    if ($start==0) {
        $link .='&p=0" class="bold">1</a>';
    } else {
        $link .='&p=0">1</a>';
    }
    $pages[]=$link;
    $i=2;
    while ($i<=$pageamount) {
        $selectpage=($i-1)*$amount;
        if ($start==$selectpage) {
            $pages[]='<a href="admin.php?w=cc&amp;o='.$o.'&amp;a='.$amount.'&p='.$selectpage.'" class="bold">'.$i.'</a>';
        } else {
            $pages[]='<a href="admin.php?w=cc&amp;o='.$o.'&amp;a='.$amount.'&p='.$selectpage.'">'.$i.'</a>';
        }
        $i++;
    }
    $pages=implode(', ',$pages);
    $template_file='admin_settings_columns_list.tpl';
}