<?php
/**
 * File: api_external_auth.php.
 * Author: Ulrich Block
 * Date: 03.06.12
 * Time: 17:00
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
    if ($admin_id!=$reseller_id) {
        $lookupID=$reseller_id;
    } else {
        $lookupID=$admin_id;
    }
}
if ($ui->w('action',4,'post') and !token(true)) {
    $template_file = $spracheResponse->token;
} else if ($ui->smallletters('action',2,'post')=='md'){
    $query = $sql->prepare("SELECT COUNT(`active`) AS `amount` FROM `api_external_auth` LIMIT 1");
    $query->execute();
    $amount=$query->fetchColumn();
    $pwd = '';
    $domain = '';
    if ($amount>0) {
        $query = $sql->prepare("UPDATE `api_external_auth` SET `active`=?,`ssl`=?,`user`=?,`domain`=?,`pwd`=AES_ENCRYPT(?,?),`file`=? WHERE `resellerID`=? LIMIT 1");
        $query->execute(array($ui->active('active','post'),$ui->active('ssl','post'),$ui->names('user',255,'post'),$ui->domain('domain','post'),$ui->password('pwd',50,'post'),$aeskey,$ui->startparameter('file','post'),$lookupID));
    } else {
        $query = $sql->prepare("INSERT INTO `api_external_auth` (`active`,`ssl`,`user`,`domain`,`pwd`,`file`,`resellerID`) VALUES (?,?,?,?,AES_ENCRYPT(?,?),?,?)");
        $query->execute(array($ui->active('active','post'),$ui->active('ssl','post'),$ui->names('user',255,'post'),$ui->domain('domain','post'),$ui->password('pwd',50,'post'),$aeskey,$ui->startparameter('file','post'),$lookupID));
    }
    $loguseraction="%mod% API external auth";
    $insertlog->execute();
    $template_file = $spracheResponse->table_add;
} else {
    $active = '';
    $user = '';
    $pwd = '';
    $ssl = '';
    $domain = '';
    $file='auth.php';
    $query = $sql->prepare("SELECT `active`,`ssl`,`user`,`domain`,AES_DECRYPT(`pwd`,?) AS `decryptedPWD`,`file` FROM `api_external_auth` WHERE `resellerID`=? LIMIT 1");
    $query->execute(array($aeskey,$lookupID));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $active=$row['active'];
        $ssl=$row['ssl'];
        $user=$row['user'];
        $pwd=$row['decryptedPWD'];
        $domain=$row['domain'];
        $file=$row['file'];
    }
    $template_file = 'admin_api_external_auth_settings.tpl';
}