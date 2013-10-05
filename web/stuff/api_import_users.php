<?php
/**
 * File: api_import_users.php.
 * Author: Ulrich Block
 * Date: 21.10.12
 * Time: 13:43
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
if ($main!=1 or !isset($admin_id) or (isset($admin_id) and !$pa['apiSettings'])) {
    header('Location: admin.php');
    die('No acces');
}
include(EASYWIDIR . '/stuff/keyphrasefile.php');
$sprache = getlanguagefile('api',$user_language,$reseller_id);
$loguserid = $admin_id;
$logusername = getusername($admin_id);
$logusertype = 'admin';
$loguserid = $admin_id;
$logusername = getusername($admin_id);
$logusertype = 'admin';
if ($reseller_id==0) {
    $logreseller = 0;
    $logsubuser = 0;
    $lookupID = 0;
} else {
    if (isset($_SESSION['oldid'])) {
        $logsubuser=$_SESSION['oldid'];
    } else {
        $logsubuser = 0;
    }
    $logreseller = 0;
    if ($admin_id != $reseller_id) {
        $lookupID=$reseller_id;
    } else {
        $lookupID=$admin_id;
    }
}
if ($ui->w('action', 4, 'post') and !token(true)) {
    $template_file = $spracheResponse->token;
} else if (in_array($ui->st('d','get'), array('md','ad'))){
    if (!in_array($ui->smallletters('action',2,'post'), array('md','ad')) and $ui->st('d','get') == 'md') {
        $id=$ui->id('id',19,'get');
        $groupIDS = array();
        $query = $sql->prepare("SELECT `id`,`name` FROM `usergroups` WHERE `active`='Y' AND `grouptype`='u' AND `resellerid`=?");
        $query->execute(array($reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $groupIDS[$row['id']] = $row['name'];
        }
        $query = $sql->prepare("SELECT * FROM `api_import` WHERE `importID`=? AND `resellerID`=? LIMIT 1");
        $query->execute(array($id,$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $active=$row['active'];
            $ssl=$row['ssl'];
            $token=$row['token'];
            $domain=$row['domain'];
            $file=$row['file'];
            $fetchUpdates=$row['fetchUpdates'];
            $groupID=$row['groupID'];
            $lastID=$row['lastID'];
            $chunkSize=$row['chunkSize'];
            $lastCheck=$row['lastCheck'];
        }
        if (isset($fetchUpdates)) {
            $template_file = 'admin_api_import_users_md.tpl';
        } else {
            $template_file = 'admin_404.tpl';
        }
    } else if (!in_array($ui->smallletters('action',2,'post'), array('md','ad')) and $ui->st('d','get') == 'ad') {
        $groupIDS = array();
        $query = $sql->prepare("SELECT `id`,`name` FROM `usergroups` WHERE `active`='Y' AND `grouptype`='u' AND `resellerid`=?");
        $query->execute(array($reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $groupIDS[$row['id']] = $row['name'];
        }
        $template_file = 'admin_api_import_users_add.tpl';
    } else if (in_array($ui->smallletters('action',2,'post'), array('md','ad'))) {
        $error = array();
        if (!$ui->active('active','post')) {
            $error[] = 'Active';
        }
        if (!$ui->active('fetchUpdates','post')) {
            $error[] = 'fetchUpdates';
        }
        if (!$ui->password('accessToken',255,'post')) {
            $error[] = 'Token';
        }
        if (!$ui->id('groupID',19,'post')) {
            $error[] = 'File';
        }
        if (!$ui->id('chunkSize',19,'post')) {
            $error[] = 'chunkSize';
        }
        if (!$ui->active('ssl','post')) {
            $error[] = 'SSL';
        }
        if (!$ui->domain('domain','post')) {
            $error[] = 'Domain';
        }
        if (!$ui->startparameter('file','post')) {
            $error[] = 'File';
        }
        if (count($error)>0) {
            $template_file = 'Error: '.implode('<br />',$error);
        } else {
            if ($ui->st('d','get') == 'md' and $ui->id('id',19,'get')) {
                $id=$ui->id('id',19,'get');
                $query = $sql->prepare("SELECT `importID` FROM `api_import` WHERE `importID`!=? AND `domain`=? AND `file`=? AND `resellerID`=? LIMIT 1");
                $query->execute(array($id,$ui->domain('domain','post'),$ui->startparameter('file','post'),$reseller_id));
                if ($query->rowCount()>0) {
                    $template_file = 'Error: Domain and file already existing';
                } else {
                    $query = $sql->prepare("UPDATE `api_import` SET `active`=?,`fetchUpdates`=?,`token`=?,`groupID`=?,`chunkSize`=?,`ssl`=?,`domain`=?,`file`=? WHERE `importID`=? AND `resellerID`=? LIMIT 1");
                    $query->execute(array($ui->active('active','post'),$ui->active('fetchUpdates','post'),$ui->password('accessToken',255,'post'),$ui->id('groupID',19,'post'),$ui->id('chunkSize',19,'post'),$ui->active('ssl','post'),$ui->domain('domain','post'),$ui->startparameter('file','post'),$id,$reseller_id));
                    $loguseraction='%mod% %apiimport% '.$ui->domain('domain','post'). '/' . $ui->startparameter('file','post');
                }
            } else if ($ui->st('d','get') == 'ad') {
                $query = $sql->prepare("SELECT `importID` FROM `api_import` WHERE `domain`=? AND `file`=? AND `resellerID`=? LIMIT 1");
                $query->execute(array($ui->domain('domain','post'),$ui->startparameter('file','post'),$reseller_id));
                if ($query->rowCount()>0) {
                    $template_file = 'Error: Domain and file already added';
                } else {
                    $query = $sql->prepare("INSERT INTO `api_import` (`active`,`fetchUpdates`,`token`,`groupID`,`chunkSize`,`ssl`,`domain`,`file`,`lastID`,`lastCheck`,`resellerID`) VALUES (?,?,?,?,?,?,?,?,0,'0000-00-00 00:00:00',?)");
                    $query->execute(array($ui->active('active','post'),$ui->active('fetchUpdates','post'),$ui->password('accessToken',255,'post'),$ui->id('groupID',19,'post'),$ui->id('chunkSize',19,'post'),$ui->active('ssl','post'),$ui->domain('domain','post'),$ui->startparameter('file','post'),$reseller_id));
                }
                $loguseraction='%add% %apiimport% '.$ui->domain('domain','post'). '/' . $ui->startparameter('file','post');
            } else {
                $template_file = 'admin_404.tpl';
            }
            if (!isset($template_file) and $query->rowCount()>0) {
                $insertlog->execute();
                $template_file = $spracheResponse->table_add;
            } else if (!isset($template_file)) {
                $template_file = $spracheResponse->error_table;
            }
        }
    }
} else if ($ui->st('d','get') == 'dl' and $ui->id('id','30','get')) {
    $id=$ui->id('id','30','get');
    if (!$ui->smallletters('action',2,'post')) {
        $query = $sql->prepare("SELECT `ssl`,`domain`,`file` FROM `api_import` WHERE `importID`=? AND `resellerID`=? LIMIT 1");
        $query->execute(array($id,$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if($row['ssl'] == 'Y') {
                $ssl='https://';
            } else {
                $ssl='http://';
            }
            $domain=$row['domain'];
            $file=$row['file'];
        }
        if (isset($ssl) and isset($domain) and isset($file)) {
            $template_file = 'admin_api_import_users_dl.tpl';
        } else {
            $template_file = 'admin_404.tpl'; 
        }
    } else if ($ui->smallletters('action',2,'post') == 'dl') {
        $query = $sql->prepare("SELECT `domain`,`file` FROM `api_import` WHERE `importID`=? AND `resellerID`=? LIMIT 1");
        $query->execute(array($id,$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $domain=$row['domain'];
            $file=$row['file'];
        }
        $query = $sql->prepare("DELETE FROM `api_import` WHERE `importID`=? AND `resellerID`=? LIMIT 1");
        $query->execute(array($id,$reseller_id));
        if ($query->rowCount()>0) {
            $loguseraction='%del% %apiimport% '.$domain. '/' . $file;
            $insertlog->execute();
            $template_file = $spracheResponse->table_del;
        } else {
            $template_file = $spracheResponse->error_table;
        }
    }
} else {
    $table = array();
    $query = $sql->prepare("SELECT COUNT(`importID`) AS `amount` FROM `api_import` WHERE `resellerID`=?");
    $query->execute(array($reseller_id));
    $colcount=$query->fetchColumn();
    if ($start>$colcount) {
        $start=$colcount-$amount;
        if ($start<0) {
            $start = 0;
        }
    }
    $next=$start+$amount;
    if ($colcount>$next) {
        $vor=$start+$amount;
    } else {
        $vor=$start;
    }
    $back=$start - $amount;
    if ($back>=0){
        $zur=$start - $amount;
    } else {
        $zur=$start;
    }
    $o = $ui->st('o','get');
    if ($ui->st('o','get') == 'da') {
        $orderby = '`active` DESC';
    } else if ($ui->st('o','get') == 'aa') {
        $orderby = '`active` ASC';
    } else if ($ui->st('o','get') == 'dd') {
        $orderby = '`domain` DESC';
    } else if ($ui->st('o','get') == 'ad') {
        $orderby = '`domain` ASC';
    } else if ($ui->st('o','get') == 'dl') {
        $orderby = '`lastID` DESC';
    } else if ($ui->st('o','get') == 'al') {
        $orderby = '`lastID` ASC';
    } else if ($ui->st('o','get') == 'dc') {
        $orderby = '`lastCheck` DESC';
    } else if ($ui->st('o','get') == 'ac') {
        $orderby = '`lastCheck` ASC';
    } else if ($ui->st('o','get') == 'di') {
        $orderby = '`importID` DESC';
    } else {
        $orderby = '`importID` ASC';
    }
    $query = $sql->prepare("SELECT * FROM `api_import` WHERE `resellerID`=? ORDER BY $orderby LIMIT $start,$amount");
    $query->execute(array($reseller_id));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if ($row['active'] == 'Y') {
            $imgName='16_ok';
            $imgAlt='Active';
        } else {
            $imgName='16_bad';
            $imgAlt='Inactive';
        }
        if($row['ssl'] == 'Y') {
            $ssl='https://';
        } else {
            $ssl='http://';
        }
        $table[]=array('id' => $row['importID'],'img' => $imgName,'alt' => $imgAlt,'domain' => $ssl.$row['domain']. '/' . $row['file'],'lastID' => $row['lastID'],'lastCheck' => $row['lastCheck'],'active' => $row['active']);
    }
    $pageamount = ceil($colcount / $amount);
    $link='<a href="admin.php?w=ui&amp;o='.$o.'&amp;a=';
    if(!isset($amount)) {
        $link .="20";
    } else {
        $link .=$amount;
    }
    if ($start==0) {
        $link .='&p=0" class="bold">1</a>';
    } else {
        $link .='&p=0">1</a>';
    }
    $pages[] = $link;
    $i = 2;
    while ($i<=$pageamount) {
        $selectpage = ($i - 1) * $amount;
        if ($start==$selectpage) {
            $pages[] = '<a href="admin.php?w=ui&amp;o='.$o.'&amp;a='.$amount.'&p='.$selectpage.'" class="bold">'.$i.'</a>';
        } else {
            $pages[] = '<a href="admin.php?w=ui&amp;o='.$o.'&amp;a='.$amount.'&p='.$selectpage.'">'.$i.'</a>';
        }
        $i++;
    }
    $pages=implode(', ',$pages);
    $template_file = 'admin_api_import_users_list.tpl';
}