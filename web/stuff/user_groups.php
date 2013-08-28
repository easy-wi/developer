<?php
/**
 * File: user_groups.php.
 * Author: Ulrich Block
 * Date: 10.06.12
 * Time: 09:31
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

if ((!isset($admin_id) or $main!=1) or (isset($admin_id) and !$pa['userGroups'])) {
    header('Location: admin.php');
    die('No acces');
}
$sprache=getlanguagefile('user',$user_language,$reseller_id,$sql);
$rsprache=getlanguagefile('reseller',$user_language,$reseller_id,$sql);
$loguserid=$admin_id;
$logusername=getusername($admin_id,$sql);
$logusertype='admin';
if ($reseller_id==0) {
    $logreseller=0;
    $logsubuser=0;
} else {
    if (isset($_SESSION['oldid'])) {
        $logsubuser=$_SESSION['oldid'];
    } else {
        $logsubuser=0;
    }
    $logreseller=0;
}
$lookIpID=$reseller_id;
if ($reseller_id!=0 and $admin_id!=$reseller_id) {
    $lookIpID=$admin_id;
}
if ($ui->w('action',4,'post') and !token(true)) {
    $template_file=$spracheResponse->token;
} else if (in_array($ui->st('d','get'),array('md','ad'))){
    if (!in_array($ui->smallletters('action',2,'post'),array('md','ad')) and $ui->st('d','get')=='md') {
        $id=$ui->id('id',19,'get');
        $query=$sql->prepare("SELECT * FROM `usergroups` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id,$lookIpID));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $active=$row['active'];
            $grouptype=$row['grouptype'];
            $defaultgroup=$row['defaultgroup'];
            $name=$row['name'];
            $root=$row['root'];
            $user=$row['user'];
            $user_users=$row['user_users'];
            $log=$row['log'];
            $settings=$row['settings'];
            $cms_comments=$row['cms_comments'];
            $cms_settings=$row['cms_settings'];
            $cms_pages=$row['cms_pages'];
            $cms_news=$row['cms_news'];
            $gserver=$row['gserver'];
            $addons=$row['addons'];
            $gimages=$row['gimages'];
            $roots=$row['roots'];
            $restart=$row['restart'];
            $miniroot=$row['miniroot'];
            $fastdl=$row['fastdl'];
            $modfastdl=$row['modfastdl'];
            $useraddons=$row['useraddons'];
            $usersettings=$row['usersettings'];
            $ftpaccess=$row['ftpaccess'];
            $addvserver=$row['addvserver'];
            $modvserver=$row['modvserver'];
            $delvserver=$row['delvserver'];
            $usevserver=$row['usevserver'];
            $vserversettings=$row['vserversettings'];
            $pxeServer=$row['pxeServer'];
            $dhcpServer=$row['dhcpServer'];
            $dedicatedServer=$row['dedicatedServer'];
            $vserverhost=$row['vserverhost'];
            $voicemasterserver=$row['voicemasterserver'];
            $voiceserver=$row['voiceserver'];
            $resellertemplates=$row['resellertemplates'];
            $ftpbackup=$row['ftpbackup'];
            $traffic=$row['traffic'];
            $mysql_settings=$row['mysql_settings'];
            $mysql=$row['mysql'];
            $trafficsettings=$row['trafficsettings'];
            $lendserver=$row['lendserver'];
            $tickets=$row['tickets'];
            $usertickets=$row['usertickets'];
            $voiceserverSettings=$row['voiceserverSettings'];
            $voiceserverStats=$row['voiceserverStats'];
            $lendserverSettings=$row['lendserverSettings'];
            $reset=$row['gsResetting'];
            $eac=$row['eac'];
            $masterServer=$row['masterServer'];
            $userGroups=$row['userGroups'];
            $userPassword=$row['userPassword'];
            $apiSettings=$row['apiSettings'];
            $jobs=$row['jobs'];
            $updateEW=$row['updateEW'];
            $ipBans=$row['ipBans'];
        }
        if (isset($grouptype)) {
            $template_file='admin_user_groups_md.tpl';
        } else {
            $template_file='admin_404.tpl';
        }
    } else if (!in_array($ui->smallletters('action',2,'post'),array('md','ad')) and $ui->st('d','get')=='ad') {
        $template_file='admin_user_groups_add.tpl';
    } else if (in_array($ui->smallletters('action',2,'post'),array('md','ad'))) {
        $error=array();
        if (!$ui->active('active','post')) {
            $error[]='Active';
        }
        if (!$ui->smallletters('grouptype',1,'post')) {
            $error[]='Grouptype';
        }
        if (!$ui->names('groupname',255,'post')) {
            $error[]='Groupname';
        }
        if (count($error)>0) {
            $template_file='Error: '.implode('<br />',$error);
        } else {
            $active='N';
            $root='N';
            $user='N';
            $user_users='N';
            $log='N';
            $settings='N';
            $cms_comments='N';
            $cms_settings='N';
            $cms_pages='N';
            $cms_news='N';
            $gserver='N';
            $addons='N';
            $gimages='N';
            $roots='N';
            $restart='N';
            $miniroot='N';
            $fastdl='N';
            $modfastdl='N';
            $useraddons='N';
            $usersettings='N';
            $ftpaccess='N';
            $addvserver='N';
            $modvserver='N';
            $delvserver='N';
            $usevserver='N';
            $vserversettings='N';
            $pxeServer='N';
            $dhcpServer='N';
            $dedicatedServer='N';
            $vserverhost='N';
            $voicemasterserver='N';
            $voiceserver='N';
            $resellertemplates='N';
            $ftpbackup='N';
            $traffic='N';
            $mysql_settings='N';
            $mysql='N';
            $trafficsettings='N';
            $lendserver='N';
            $tickets='N';
            $usertickets='N';
            $voiceserverSettings='N';
            $voiceserverStats='N';
            $lendserverSettings='N';
            $reset='N';
            $eac='N';
            $masterServer='N';
            $userGroups='N';
            $userPassword='N';
            $apiSettings='N';
            $jobs='N';
            $updateEW='N';
            $ipBans='N';
            if ($ui->smallletters('grouptype',1,'post')=='a') {
                $root=yesNo('root');
                $user=yesNo('user');
                $user_users=yesNo('user_users');
                $settings=yesNo('settings');
                $gserver=yesNo('gserver');
                $lendserver=yesNo('lendserver');
                $addons=yesNo('addons');
                $gimages=yesNo('gimages');
                $roots=yesNo('roots');
                $tickets=yesNo('tickets');
                $addvserver=yesNo('addvserver');
                $modvserver=yesNo('modvserver');
                $delvserver=yesNo('delvserver');
                $usevserver=yesNo('usevserver');
                $vserversettings=yesNo('vserversettings');
                $vserverhost=yesNo('vserverhost');
                $voiceserver=yesNo('voiceserver');
                $voicemasterserver=yesNo('voicemasterserver');
                $traffic=yesNo('traffic');
                $trafficsettings=yesNo('trafficsettings');
                $resellertemplates=yesNo('resellertemplates');
                $log=yesNo('log');
                $cms_comments=yesNo('cms_comments');
                $cms_settings=yesNo('cms_settings');
                $cms_pages=yesNo('cms_pages');
                $cms_news=yesNo('cms_news');
                $voiceserverSettings=yesNo('voiceserverSettings');
                $voiceserverStats=yesNo('voiceserverStats');
                $lendserverSettings=yesNo('lendserverSettings');
                $dhcpServer=yesNo('dhcpServer');
                $pxeServer=yesNo('pxeServer');
                $dedicatedServer=yesNo('dedicatedServer');
                $eac=yesNo('eac');
                $masterServer=yesNo('masterServer');
                $userGroups=yesNo('userGroups');
                $userPassword=yesNo('userPassword');
                $apiSettings=yesNo('apiSettings');
                $jobs=yesNo('jobs');
                $updateEW=yesNo('updateEW');
                $ipBans=yesNo('ipBans');
                $mysql=yesNo('mysql');
                $mysql_settings=yesNo('mysql_settings');
            } else if ($ui->smallletters('grouptype',1,'post')=='u') {
                $log=yesNo('ulog');
                $restart=yesNo('restart');
                $reset=yesNo('reset');
                $miniroot=yesNo('miniroot');
                $fastdl=yesNo('fastdl');
                $modfastdl=yesNo('modfastdl');
                $useraddons=yesNo('useraddons');
                $usersettings=yesNo('usersettings');
                $ftpaccess=yesNo('ftpaccess');
                $usertickets=yesNo('usertickets');
                $ftpbackup=yesNo('ftpbackup');
                $voiceserver=yesNo('uvoiceserver');
                $apiSettings=yesNo('uapiSettings');
                $jobs=yesNo('ujobs');
                $mysql=yesNo('umysql');
                $roots=yesNo('uroots');
            } else if ($ui->smallletters('grouptype',1,'post')=="r") {
                $user=yesNo('ruser');
                $user_users=yesNo('ruser_users');
                $root=yesNo('rroot');
                $settings=yesNo('rsettings');
                $gserver=yesNo('rgserver');
                $gimages=yesNo('rgimages');
                $lendserver=yesNo('rlendserver');
                $addons=yesNo('raddons');
                $roots=yesNo('rroots');
                $tickets=yesNo('rtickets');
                $usertickets=yesNo('rusertickets');
                $addvserver=yesNo('raddvserver');
                $modvserver=yesNo('rmodvserver');
                $delvserver=yesNo('rdelvserver');
                $usevserver=yesNo('rusevserver');
                $dedicatedServer=yesNo('rdedicatedServer');
                $traffic=yesNo('rtraffic');
                $log=yesNo('rlog');
                $voiceserver=yesNo('rvoiceserver');
                $voicemasterserver=yesNo('rvoicemasterserver');
                $voiceserverSettings=yesNo('rvoiceserverSettings');
                $voiceserverStats=yesNo('rvoiceserverStats');
                $lendserverSettings=yesNo('rlendserverSettings');
                $eac=yesNo('reac');
                $masterServer=yesNo('rmasterServer');
                $userGroups=yesNo('ruserGroups');
                $userPassword=yesNo('ruserPassword');
                $apiSettings=yesNo('rapiSettings');
                $jobs=yesNo('rjobs');
                $mysql=yesNo('rmysql');
                $mysql_settings=yesNo('rmysql_settings');
            }
            if ($ui->st('d','get')=='md' and $ui->id('id',19,'get')) {
                $id=$ui->id('id',19,'get');
                $defaultgroup=$ui->active('defaultgroup','post');
                if ($defaultgroup=='Y') {
                    $query=$sql->prepare("UPDATE `usergroups` SET `defaultgroup`='N' WHERE `grouptype`=? AND `id`!=? AND `resellerid`=? LIMIT 1");
                    $query->execute(array($ui->smallletters('grouptype',1,'post'),$id,$lookIpID));
                }
                $query=$sql->prepare("UPDATE `usergroups` SET `active`=?,`defaultgroup`=?,`name`=?,`root`=?,`user`=?,`user_users`=?,`log`=?,`settings`=?,`cms_comments`=?,`cms_settings`=?,`cms_pages`=?,`cms_news`=?,`gserver`=?,`addons`=?,`gimages`=?,`roots`=?,`restart`=?,`gsResetting`=?,`miniroot`=?,`fastdl`=?,`modfastdl`=?,`useraddons`=?,`usersettings`=?,`ftpaccess`=?,`addvserver`=?,`modvserver`=?,`delvserver`=?,`usevserver`=?,`vserversettings`=?,`vserverhost`=?,`voicemasterserver`=?,`voiceserver`=?,`resellertemplates`=?,`ftpbackup`=?,`traffic`=?,`trafficsettings`=?,`lendserver`=?,`voiceserverSettings`=?,`voiceserverStats`=?,`lendserverSettings`=?,`pxeServer`=?,`dhcpServer`=?,`dedicatedServer`=?,`eac`=?,`masterServer`=?,`userGroups`=?,`userPassword`=?,`apiSettings`=?,`jobs`=?,`updateEW`=?,`ipBans`=?,`mysql`=?,`mysql_settings`=?,`tickets`=?,`usertickets`=? WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($ui->active('active','post'),$defaultgroup,$ui->names('groupname',255,'post'),$root,$user,$user_users,$log,$settings,$cms_comments,$cms_settings,$cms_pages,$cms_news,$gserver,$addons,$gimages,$roots,$restart,$reset,$miniroot,$fastdl,$modfastdl,$useraddons,$usersettings,$ftpaccess,$addvserver,$modvserver,$delvserver,$usevserver,$vserversettings,$vserverhost,$voicemasterserver,$voiceserver,$resellertemplates,$ftpbackup,$traffic,$trafficsettings,$lendserver,$voiceserverSettings,$voiceserverStats,$lendserverSettings,$pxeServer,$dhcpServer,$dedicatedServer,$eac,$masterServer,$userGroups,$userPassword,$apiSettings,$jobs,$updateEW,$ipBans,$mysql,$mysql_settings,$tickets,$usertickets,$id,$lookIpID));
                $loguseraction='%mod% %group% '.$ui->names('groupname',255,'post');
            } else if ($ui->st('d','get')=='ad') {
                $defaultgroup=$ui->active('defaultgroup','post');
                if ($defaultgroup=='Y') {
                    $query=$sql->prepare("UPDATE `usergroups` SET `defaultgroup`='N' WHERE `grouptype`=? AND `resellerid`=? LIMIT 1");
                    $query->execute(array($ui->smallletters('grouptype',1,'post'),$lookIpID));
                }
                $query=$sql->prepare("SELECT `id` FROM `usergroups` WHERE `name`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($ui->names('groupname',255,'post'),$lookIpID));
                if ($query->rowcount()>0) {
                    $template_file='Error: Group already exists';
                } else {
                    $query=$sql->prepare("INSERT INTO `usergroups` (`active`,`defaultgroup`,`grouptype`,`name`,`root`,`user`,`user_users`,`log`,`settings`,`cms_comments`,`cms_settings`,`cms_pages`,`cms_news`,`gserver`,`addons`,`gimages`,`roots`,`restart`,`gsResetting`,`miniroot`,`fastdl`,`modfastdl`,`useraddons`,`usersettings`,`ftpaccess`,`addvserver`,`modvserver`,`delvserver`,`usevserver`,`vserversettings`,`vserverhost`,`voicemasterserver`,`voiceserver`,`resellertemplates`,`ftpbackup`,`traffic`,`trafficsettings`,`lendserver`,`tickets`,`usertickets`,`voiceserverSettings`,`voiceserverStats`,`lendserverSettings`,`pxeServer`,`dhcpServer`,`dedicatedServer`,`eac`,`masterServer`,`userGroups`,`userPassword`,`apiSettings`,`jobs`,`updateEW`,`ipBans`,`mysql`,`mysql_settings`,`resellerid`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                    $query->execute(array($ui->active('active','post'),$defaultgroup,$ui->smallletters('grouptype',1,'post'),$ui->names('groupname',255,'post'),$root,$user,$user_users,$log,$settings,$cms_comments,$cms_settings,$cms_pages,$cms_news,$gserver,$addons,$gimages,$roots,$restart,$reset,$miniroot,$fastdl,$modfastdl,$useraddons,$usersettings,$ftpaccess,$addvserver,$modvserver,$delvserver,$usevserver,$vserversettings,$vserverhost,$voicemasterserver,$voiceserver,$resellertemplates,$ftpbackup,$traffic,$trafficsettings,$lendserver,$tickets,$usertickets,$voiceserverSettings,$voiceserverStats,$lendserverSettings,$pxeServer,$dhcpServer,$dedicatedServer,$eac,$masterServer,$userGroups,$userPassword,$apiSettings,$jobs,$updateEW,$ipBans,$mysql,$mysql_settings,$lookIpID));
                }
                $loguseraction='%add% %group% '.$ui->names('groupname',255,'post');
            } else {
                $template_file='admin_404.tpl';
            }
            if (!isset($template_file) and $query->rowCount()>0) {
                $insertlog->execute();
                $template_file=$spracheResponse->table_add;
            } else if (!isset($template_file)) {
                $template_file=$spracheResponse->error_table;
            }
        }
    }
} else if ($ui->st('d','get')=='dl' and $ui->id('id','30','get')) {
    $id=$ui->id('id','30','get');
    if (!$ui->smallletters('action',2,'post')) {
        $query=$sql->prepare("SELECT `active`,`grouptype`,`name` FROM `usergroups` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id,$lookIpID));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if($row['active']=='Y') {
                $imgName='16_ok';
                $imgAlt='ok';
            } else {
                $imgName='16_bad';
                $imgAlt='bad';
            }
            if($row['grouptype']=='r') {
                $grouptype=$sprache->accounttype_reseller;
            } else if($row['grouptype']=='a') {
                $grouptype=$sprache->accounttype_admin;
            } else {
                $grouptype=$sprache->accounttype_user;
            }
            $name=$row['name'];
        }
        $template_file='admin_user_groups_dl.tpl';
    } else if ($ui->smallletters('action',2,'post')=='dl' and $ui->id('id','30','get')) {
        $query=$sql->prepare("SELECT c.`id`,g.`name` FROM `usergroups` g LEFT JOIN `usergroups` c ON g.`grouptype`=c.`grouptype` AND c.`defaultgroup`='Y' WHERE g.`id`=? AND g.`resellerid`=? LIMIT 1");
        $query->execute(array($id,$lookIpID));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $default_id=$row['id'];
            $name=$row['name'];
        }
        if (isset($default_id) and $default_id!=$id and $default_id!=null and $default_id!=0) {
            $query=$sql->prepare("DELETE FROM `usergroups` WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($id,$lookIpID));
            if ($query->rowCount()>0) {
                $query=$sql->prepare("DELETE FROM `userdata_groups` WHERE `groupID`=? AND `resellerID`=?");
                $query->execute(array($id,$lookIpID));
                $loguseraction='%del% %group% '.$name;
                $insertlog->execute();
                $template_file=$spracheResponse->table_del;
            } else {
                $template_file=$spracheResponse->error_table;
            }
        } else if (isset($default_id) and $default_id==$id) {
            $template_file='Error: Can not remove default group';
        } else {
            $template_file='Error: No mastergroup to default users belonging to the to be removed group';
        }
    }
} else {
    $table=array();
    $o=$ui->st('o','get');
    if ($ui->st('o','get')=='da') {
        $orderby='`active` DESC';
    } else if ($ui->st('o','get')=='aa') {
        $orderby='`active` ASC';
    } else if ($ui->st('o','get')=='dt') {
        $orderby='`grouptype` DESC';
    } else if ($ui->st('o','get')=='at') {
        $orderby='`grouptype` ASC';
    } else if ($ui->st('o','get')=='dd') {
        $orderby='`defaultgroup` DESC';
    } else if ($ui->st('o','get')=='ad') {
        $orderby='`defaultgroup` ASC';
    } else if ($ui->st('o','get')=='dn') {
        $orderby='`name` DESC';
    } else if ($ui->st('o','get')=='at') {
        $orderby='`name` ASC';
    } else if ($ui->st('o','get')=='di') {
        $orderby='`id` DESC';
    } else {
        $orderby='`id` ASC';
    }
    $query=$sql->prepare("SELECT * FROM `usergroups` WHERE `resellerid`=? ORDER BY $orderby");
    $query->execute(array($lookIpID));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if($row['active']=='Y') {
            $imgName='16_ok';
            $imgAlt='ok';
        } else {
            $imgName='16_bad';
            $imgAlt='inactive';
        }
        if($row['grouptype']=='r') {
            $grouptype=$sprache->accounttype_reseller;
        } else if($row['grouptype']=='a') {
            $grouptype=$sprache->accounttype_admin;
        } else {
            $grouptype=$sprache->accounttype_user;
        }
        if($row['defaultgroup']=='Y') {
            $defaultgroup=$gsprache->yes;
        } else {
            $defaultgroup=$gsprache->no;
        }
        $table[]=array('id'=>$row['id'],'img'=>$imgName,'alt'=>$imgAlt,'grouptype'=>$grouptype,'defaultgroup'=>$defaultgroup,'name'=>$row['name'],'active'=>$row['active']);
    }
    $template_file='admin_user_groups_list.tpl';
}