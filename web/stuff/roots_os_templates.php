<?php
/**
 * File: roots_os_templates.php.
 * Author: Ulrich Block
 * Date: 29.04.12
 * Time: 11:56
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

if (!isset($admin_id) or $main!=1 or $reseller_id != 0 or !$pa['resellertemplates']) {
    header('Location: admin.php');
    die;
}

include(EASYWIDIR . '/stuff/keyphrasefile.php');

$sprache = getlanguagefile('reseller',$user_language,$reseller_id);
$loguserid = $admin_id;
$logusername = getusername($admin_id);
$logusertype = 'admin';
if ($reseller_id==0) {
    $logreseller = 0;
    $logsubuser = 0;
} else {
    if (isset($_SESSION['oldid'])) {
        $logsubuser=$_SESSION['oldid'];
    } else {
        $logsubuser = 0;
    }
    $logreseller = 0;
}
if ($ui->w('action', 4, 'post') and !token(true)) {
    $template_file = $spracheResponse->token;
} else if (in_array($ui->st('d','get'), array('md','ad'))){
    if (!in_array($ui->smallletters('action',2,'post'), array('md','ad')) and $ui->st('d','get') == 'md') {
        $id=$ui->id('id', 10, 'get');
        $query = $sql->prepare("SELECT * FROM `resellerimages` WHERE `id`=? LIMIT 1");
        $query->execute(array($id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $active=$row['active'];
            $distro=$row['distro'];
            $description=$row['description'];
            $bitversion=$row['bitversion'];
            $pxelinux=$row['pxelinux'];
        }
        if (isset($bitversion)) {
            $template_file = 'admin_root_templates_md.tpl';
        } else {
            $template_file = 'admin_404.tpl';
        }
    } else if (!in_array($ui->smallletters('action',2,'post'), array('md','ad')) and $ui->st('d','get') == 'ad') {
        $template_file = 'admin_root_templates_add.tpl';
    } else if (in_array($ui->smallletters('action',2,'post'), array('md','ad'))) {
        $bitversion=($ui->id('bitversion',2,'post')) ? $ui->id('bitversion',2,'post') : 64;
        $active=($ui->active('active','post')) ? $ui->active('active','post') : 'Y';
        $distro=$ui->names('distro',255,'post');
        $description=$ui->description('description','post');
        $pxelinux=$ui->escaped('pxelinux','post');
        if ($ui->st('d','get') == 'md' and $ui->id('id', 10, 'get')) {
            $query = $sql->prepare("UPDATE `resellerimages` SET `active`=?,`description`=?,`distro`=?,`bitversion`=?,`pxelinux`=? WHERE `id`=? LIMIT 1");
            $query->execute(array($active,$description,$distro,$bitversion,$pxelinux,$ui->id('id', 10, 'get')));
            $loguseraction="%mod% %virtualimage% $description";
        } else if ($ui->st('d','get') == 'ad') {
            $query = $sql->prepare("INSERT INTO `resellerimages` (`active`,`description`,`distro`,`bitversion`,`pxelinux`) VALUES (?,?,?,?,?)");
            $query->execute(array($active,$description,$distro,$bitversion,$pxelinux));
            $loguseraction="%add% %virtualimage% $description";
        } else {
            $template_file = 'admin_404.tpl';
        }
        if (!isset($template_file) and isset($query) and $query->rowCount()>0) {
            $insertlog->execute();
            $template_file = $spracheResponse->table_add;
        } else if (!isset($template_file)) {
            $template_file = $spracheResponse->error_table;
        }
    }
} else if ($ui->st('d','get') == 'dl' and $ui->id('id', 10, 'get')) {
    $id=$ui->id('id', 10, 'get');
    if (!$ui->smallletters('action',2,'post')) {
        $query = $sql->prepare("SELECT `description` FROM `resellerimages` WHERE `id`=? LIMIT 1");
        $query->execute(array($id));
        $description=$query->fetchColumn();
        if ($query->rowCount()>0) {
            $template_file = "admin_root_templates_dl.tpl";
        } else {
            $template_file = 'Error: No such ID';
        }
    } else if ($ui->smallletters('action',2,'post') == 'dl'){
        $query = $sql->prepare("SELECT `description` FROM `resellerimages` WHERE `id`=? LIMIT 1");
        $query->execute(array($id));
        $description=$query->fetchColumn();
        $query = $sql->prepare("DELETE FROM `resellerimages` WHERE `id`=? LIMIT 1");
        $query->execute(array($id));
        if ($query->rowCount()>0) {
            $loguseraction="%del% %virtualimage% $description";
            $insertlog->execute();
            $template_file = $spracheResponse->table_del;
        } else {
            $template_file = $spracheResponse->error_table;
        }
    } else {
        $template_file = 'admin_404.tpl';
    }
} else {
    $table = array();
    $o = $ui->st('o','get');
    if ($ui->st('o','get') == 'dd') {
        $orderby = '`distro` DESC';
    } else if ($ui->st('o','get') == 'ad') {
        $orderby = '`distro` ASC';
    } else if ($ui->st('o','get') == 'de') {
        $orderby = '`description` DESC';
    } else if ($ui->st('o','get') == 'ae') {
        $orderby = '`description` ASC';
    } else if ($ui->st('o','get') == 'db') {
        $orderby = '`bitversion` DESC';
    } else if ($ui->st('o','get') == 'ab') {
        $orderby = '`bitversion` ASC';
    } else if ($ui->st('o','get') == 'di') {
        $orderby = '`id` DESC';
    } else {
        $orderby = '`id` ASC';
        $o = 'ai';
    }
    $query = $sql->prepare("SELECT * FROM `resellerimages` ORDER BY $orderby");
    $query->execute();
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $table[]=array('id' => $row['id'],'distro' => $row['distro'],'description' => $row['description'],'bitversion' => $row['bitversion']);
    }
    $template_file = 'admin_root_templates_list.tpl';
}