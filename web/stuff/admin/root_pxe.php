<?php
/**
 * File: root_pxe.php.
 * Author: Ulrich Block
 * Date: 03.10.12
 * Time: 11:36
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

if (!isset($admin_id) or $main != 1 or  $reseller_id != 0 or !$pa['vserversettings']){
    header('Location: admin.php');
    die;
}

include(EASYWIDIR . '/stuff/keyphrasefile.php');

$sprache = getlanguagefile('reseller',$user_language,$reseller_id);
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
if ($ui->w('action', 4, 'post') and !token(true)) {
    $template_file = $spracheResponse->token;
} else if (in_array($ui->st('d', 'get'), array('md','ad'))){
    if (!in_array($ui->smallletters('action',2, 'post'), array('md','ad')) and $ui->st('d', 'get') == 'md') {
        $id = $ui->id('id',19, 'get');
        $query = $sql->prepare("SELECT *,AES_DECRYPT(`port`,:aeskey) AS `decryptedport`,AES_DECRYPT(`user`,:aeskey) AS `decrypteduser`,AES_DECRYPT(`pass`,:aeskey) AS `decryptedpass` FROM `rootsPXE` WHERE `resellerid`=:reseller_id LIMIT 1");
        $query->execute(array(':aeskey' => $aeskey,':reseller_id' => $reseller_id));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $active = $row['active'];
            $ip = $row['ip'];
            $port = $row['decryptedport'];
            $user = $row['decrypteduser'];
            $pass = $row['decryptedpass'];
            $publickey = $row['publickey'];
            $keyname = $row['keyname'];
            $PXEFolder = $row['PXEFolder'];
            $description = $row['description'];
        }
        $template_file = (isset($PXEFolder)) ? 'admin_root_pxe_md.tpl' : 'admin_404.tpl';
    } else if (!in_array($ui->smallletters('action',2, 'post'), array('md','ad')) and $ui->st('d', 'get') == 'ad') {
        $template_file = 'admin_root_pxe_ad.tpl';
    } else if (in_array($ui->smallletters('action',2, 'post'), array('md','ad'))) {
        $error = array();
        if (!$ui->w('publickey', 1, 'post')) {
            $error[] = 'Publickey';
        }
        if (!$ui->active('active', 'post')) {
            $error[] = 'Active';
        }
        if (!$ui->ip('ip', 'post')) {
            $error[] = 'IP';
        }
        if (!$ui->port('port', 'post')) {
            $error[] = 'Port';
        }
        if (!$ui->password('pass',255, 'post')) {
            $error[] = 'Password';
        }
        if (!$ui->username('user',255, 'post')) {
            $error[] = 'Username';
        }
        if (count($error)>0) {
            $template_file = 'Error: '.implode('<br />',$error);
        } else {
            $publickey = $ui->w('publickey', 1, 'post');
            $keyname = $ui->startparameter('keyname', 'post');
            $active = $ui->active('active', 'post');
            $ip = $ui->ip('ip', 'post');
            $ips = $ui->ips('ips', 'post');
            $netmask = $ui->ips('netmask', 'post');
            $port = $ui->port('port', 'post');
            $user = $ui->username('user',255, 'post');
            $pass = $ui->password('pass',255, 'post');
            $PXEFolder = $ui->startparameter('PXEFolder', 'post');
            $description = $ui->escaped('description', 'post');
            if ($ui->st('d', 'get') == 'md' and $ui->id('id',19, 'get')) {
                $id = $ui->id('id',19, 'get');
                $query = $sql->prepare("UPDATE `rootsPXE` SET `active`=:active,`ip`=:ip,`port`=AES_ENCRYPT(:port,:aeskey),`user`=AES_ENCRYPT(:user,:aeskey),`pass`=AES_ENCRYPT(:pass,:aeskey),`publickey`=:publickey,`keyname`=:keyname,`PXEFolder`=:PXEFolder,`description`=:description WHERE `id`=:id AND `resellerid`=:reseller_id");
                $query->execute(array(':active' => $active,':ip' => $ip,':port' => $port,':aeskey' => $aeskey,':user' => $user,':pass' => $pass,':publickey' => $publickey,':keyname' => $keyname,':PXEFolder' => $PXEFolder,':description' => $description,':id' => $id,':reseller_id' => $reseller_id));
                $loguseraction="%mod% PXE";
            } else if ($ui->st('d', 'get') == 'ad') {
                $query = $sql->prepare("INSERT INTO `rootsPXE` (`active`,`ip`,`port`,`user`,`pass`,`publickey`,`keyname`,`PXEFolder`,`description`,`resellerid`) VALUES (:active,:ip,AES_ENCRYPT(:port,:aeskey),AES_ENCRYPT(:user,:aeskey),AES_ENCRYPT(:pass,:aeskey),:publickey,:keyname,:PXEFolder,:description,:reseller_id)");
                $query->execute(array(':active' => $active,':ip' => $ip,':port' => $port,':aeskey' => $aeskey,':user' => $user,':pass' => $pass,':publickey' => $publickey,':keyname' => $keyname,':PXEFolder' => $PXEFolder,':description' => $description,':reseller_id' => $reseller_id));
                $loguseraction="%add% PXE";
            } else {
                $template_file = 'admin_404.tpl';
            }
            if (!isset($template_file) and $query->rowCount() > 0) {
                $insertlog->execute();
                $template_file = $spracheResponse->table_add;
            } else if (!isset($template_file)) {
                $template_file = $spracheResponse->error_table;
            }
        }
    }
} else if ($ui->st('d', 'get') == 'dl' and $ui->id('id',19, 'get')) {
    $id = $ui->id('id',19, 'get');
    if (!$ui->smallletters('action',2, 'post')) {
        $id = $ui->id('id',19, 'get');
        $query = $sql->prepare("SELECT `ip`,`description` FROM `rootsPXE` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id,$reseller_id));
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $ip = $row['ip'];
            $description = $row['description'];
        }
        $template_file = (isset($ip)) ? 'admin_root_pxe_dl.tpl' : 'admin_404.tpl';
    } else if ($ui->smallletters('action',2, 'post') == 'dl') {
        $query = $sql->prepare("DELETE FROM `rootsPXE` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id,$reseller_id));
        if ($query->rowCount() > 0) {
            $loguseraction="%del% PXE";
            $insertlog->execute();
            $template_file = $spracheResponse->table_del;
        } else {
            $template_file = $spracheResponse->error_table;
        }
    } else {
        $template_file = 'admin_404.tpl';
    }
} else {
    $o = $ui->st('o', 'get');
    if ($ui->st('o', 'get') == 'dd') {
        $orderby = '`description` DESC';
    } else if ($ui->st('o', 'get') == 'ad') {
        $orderby = '`description` ASC';
    } else if ($ui->st('o', 'get') == 'dp') {
        $orderby = '`ip` DESC';
    } else if ($ui->st('o', 'get') == 'ap') {
        $orderby = '`ip` ASC';
    } else if ($ui->st('o', 'get') == 'ds') {
        $orderby = '`active` DESC,`notified` DESC';
    } else if ($ui->st('o', 'get') == 'as') {
        $orderby = '`active` ASC,`notified` ASC';
    } else if ($ui->st('o', 'get') == 'di') {
        $orderby = '`id` DESC';
    } else {
        $orderby = '`id` ASC';
        $o = 'ai';
    }
    $table = array();
    $query = $sql->prepare("SELECT `active`,`id`,`ip`,`description`,`notified` FROM `rootsPXE` WHERE `resellerid`=? ORDER BY $orderby");
    $query->execute(array($reseller_id));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        if ($row['active'] == 'Y' and $row['notified']>0) {
            $imgName = '16_error';
            $imgAlt = 'Crashed';
        } else if ($row['active'] == 'Y') {
            $imgName = '16_ok';
            $imgAlt = 'Active';
        } else {
            $imgName = '16_bad';
            $imgAlt = 'Inactive';
        }
        $table[] = array('id' => $row['id'], 'ip' => $row['ip'], 'description' => $row['description'], 'img' => $imgName,'alt' => $imgAlt,'active' => $row['active']);
    }
    $template_file = 'admin_root_pxe_list.tpl';
}