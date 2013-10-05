<?php
/**
 * File: api_settings.php.
 * Author: Ulrich Block
 * Date: 20.05.12
 * Time: 10:13
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
$sprache = getlanguagefile('api',$user_language,$reseller_id);
$loguserid = $admin_id;
$logusername = getusername($admin_id);
$logusertype = 'admin';
if ($reseller_id==0) {
    $logreseller = 0;
    $logsubuser = 0;
    $lookupID = 0;
} else {
    $logsubuser=(isset($_SESSION['oldid'])) ? $_SESSION['oldid'] : 0;
    $lookupID=($admin_id != $reseller_id) ? $reseller_id : $admin_id;
    $logreseller = 0;
}
if ($ui->w('action', 4, 'post') and !token(true)) {
    $template_file = $spracheResponse->token;
} else if ($ui->smallletters('action',2,'post') == 'md'){
    $query = $sql->prepare("SELECT COUNT(`active`) AS `amount` FROM `api_settings` WHERE `resellerID`=? LIMIT 1");
    $query->execute(array($lookupID));
    $amount=$query->fetchColumn();
    $salt=md5(date('Y-d-m H:m:s'));
    $user=$ui->names('user',255,'post');
    if ($amount>0) {
        $query = $sql->prepare("UPDATE `api_settings` SET `active`=?,`user`=? WHERE `resellerID`=? LIMIT 1");
        $query->execute(array($ui->active('active','post'),$user,$lookupID));
        if ($ui->password('pwd',255,'post') != 'encrypted') {
            $query = $sql->prepare("UPDATE `api_settings` SET `pwd`=?,`salt`=? WHERE `resellerID`=? LIMIT 1");
            $query->execute(array(passwordhash($ui->password('pwd',255,'post'),$salt),$salt,$lookupID));
        }
    } else {
        $query = $sql->prepare("INSERT INTO `api_settings` (`active`,`user`,`salt`,`pwd`,`resellerID`) VALUES (?,?,?,?,?)");
        $query->execute(array($ui->active('active','post'),$user,passwordhash($ui->password('pwd',255,'post'),$salt),$salt,$lookupID));
    }
    $ips = array();
    $postIPs=(array)$ui->ip4('ip','post');
    $query = $sql->prepare("SELECT `ip` FROM `api_ips` WHERE `resellerID`=?");
    $query->execute(array($lookupID));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if (!in_array($row['ip'],$postIPs)) {
            $delete=$sql->prepare("DELETE FROM `api_ips` WHERE `ip`=? AND `resellerID`=?");
            $delete->execute(array($row['ip'],$lookupID));
        } else {
            $ips[] = $row['ip'];
        }
    }
    foreach ($postIPs as $ip) {
        if (!in_array($ip,$ips)) {
            $query = $sql->prepare("INSERT INTO `api_ips` (`ip`,`resellerID`) VALUES (?,?)");
            $query->execute(array($ip,$lookupID));
        }
    }
    $template_file = $spracheResponse->table_add;
    $loguseraction="%mod% API";
    $insertlog->execute();
} else {
    $ips = array();
    $active = '';
    $user = '';
    $pwd = '';
    $query = $sql->prepare("SELECT `ip` FROM `api_ips` WHERE `resellerID`=?");
    $query->execute(array($lookupID));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $ips[] = $row['ip'];
    }
    $query = $sql->prepare("SELECT `active`,`user` FROM `api_settings` WHERE `resellerID`=? LIMIT 1");
    $query->execute(array($lookupID));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $active=$row['active'];
        $user=$row['user'];
        $pwd='encrypted';
    }
    $template_file = "admin_api_settings.tpl";
}