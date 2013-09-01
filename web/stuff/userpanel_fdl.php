<?php
/**
 * File: userpanel_fdl.php.
 * Author: Ulrich Block
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
if ((!isset($user_id) or !$main == "1") or (isset($user_id) and !$pa['fastdl'])) {
	header('Location: userpanel.php');
	die('No acces');
}
$sprache=getlanguagefile('fastdl',$user_language,$reseller_id,$sql);
$loguserid=$user_id;
$logusername=getusername($user_id);
$logusertype="user";
$logreseller=0;
if (isset($admin_id) and $reseller_id!=0 and $admin_id!=$reseller_id) {
	$reseller_id=$admin_id;
}
if (isset($admin_id)) {
	$logsubuser=$admin_id;
} else if (isset($subuser_id)) {
	$logsubuser=$subuser_id;
} else {
	$logsubuser=0;
}

if ($ui->st('d','get')=='ud' and $ui->id('id',19,'get') and (!isset($_SESSION['sID']) or in_array($ui->id('id',10,'get'),$substituteAccess['gs']))) {
    $serverid=$ui->id('id',19,'get');
    $aesfilecvar=getconfigcvars(EASYWIDIR."/stuff/keyphrasefile.php");
    $aeskey=$aesfilecvar['aeskey'];
    $query=$sql->prepare("SELECT g.`rootID`,g.`protected`,g.`masterfdl`,g.`mfdldata`,g.`serverip`,g.`port`,g.`newlayout`,AES_DECRYPT(g.`ftppassword`,?) AS `dftppassword`,AES_DECRYPT(g.`ppassword`,?) AS `dppassword`,s.`servertemplate`,t.`modfolder`,t.`shorten`,u.`fdlpath`,u.`cname` FROM `gsswitch` g LEFT JOIN `serverlist` s ON g.`serverid`=s.`id` LEFT JOIN `servertypes` t ON s.`servertype`=t.`id` LEFT JOIN `userdata` u ON g.`userid`=u.`id` WHERE g.`active`='Y' AND g.`id`=? AND g.`resellerid`=? LIMIT 1");
    $query->execute(array($aeskey,$aeskey,$serverid,$reseller_id));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $rootid=$row['rootID'];
        $serverip=$row['serverip'];
        $port=$row['port'];
        $masterfdl=$row['masterfdl'];
        $mfdldata=$row['mfdldata'];
        $shorten=$row['shorten'];
        $modfolder=$row['modfolder'];
        $servertemplate=$row['servertemplate'];
        $customer=$row['cname'];
        $ftppass=$row['dftppassword'];
        if ($row['protected']=='Y') {
            $customer=$customer.'-p';
            $ftppass=$row['dppassword'];
        }
        if ($servertemplate==1) {
            $servertemplate="";
        } else {
            $servertemplate="-".$servertemplate;
        }
        if ($masterfdl=='Y') {
            $fdlpath=$row['fdlpath'];
            $ftpupload=$fdlpath;
        } else {
            $ftpupload=$mfdldata;
        }
        if ($ftpupload!="") {
            include(EASYWIDIR."/stuff/ssh_exec.php");
            $rdata=serverdata('root',$rootid,$aeskey);
            $sship=$rdata['ip'];
            $sshport=$rdata['port'];
            $sshuser=$rdata['user'];
            $sshpass=$rdata['pass'];
            if ($row['newlayout']=='Y') $customer=$customer.'-'.$serverid;
            $serverfolder=$serverip."_"."$port/$shorten$servertemplate";
            if(ssh2_execute('gs',$rootid,"sudo -u ${customer} ./control.sh fastdl ${customer} ${serverfolder} \"${ftpupload}\" ${modfolder}")===false) {
                $template_file=$spracheResponse->error_server;
                $actionstatus="fail";
            } else {
                $template_file=$sprache->fdlstarted;
                $actionstatus="ok";
            }
            $loguseraction="%start% %fastdl% $serverip:$port %$actionstatus%";
            $insertlog->execute();
        } else {
            $template_file=$sprache->fdlfailed;
            $actionstatus="fail";
        }
    }
} else if ($ui->st('d','get')=='es' and $ui->id('id',19,'get') and (!isset($_SESSION['sID']) or in_array($ui->id('id',10,'get'),$substituteAccess['gs']))) {
    $id=$ui->id('id',19,'get');
    if (!$ui->smallletters('action',2,'post')) {
        $query=$sql->prepare("SELECT `serverip`,`port`,`mfdldata`,`masterfdl` FROM `gsswitch` WHERE `active`='Y' AND `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id,$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $serverip=$row['serverip'];
            $port=$row['port'];
            $masterfdl=$row['masterfdl'];
            $mfdldata=$row['mfdldata'];
        }
        if (!isset($mfdldata)){
            $mfdldata="";
        }
        if (isset($serverip)) {
            $template_file="userpanel_gserver_fdl_es.tpl";
        } else {
            $template_file='userpanel_404.tpl';
        }
    } else if ($ui->smallletters('action',2,'post')=='md'){
        if ($ui->active('masterfdl','post')) {
            $query=$sql->prepare("SELECT `serverip`,`port` FROM `gsswitch` WHERE `active`='Y' AND `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($id,$reseller_id));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $gsip=$row['serverip'];
                $port=$row['port'];
            }
            if (isset($gsip)) {
                $mfdldata=$ui->url('mfdldata','post');
                $masterfdl=$ui->active('masterfdl','post');
                $query=$sql->prepare("UPDATE `gsswitch` SET `mfdldata`=?, `masterfdl`=? WHERE `active`='Y' AND `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($mfdldata,$masterfdl,$id,$reseller_id));
                $template_file=$sprache->udsuc;
                $loguseraction="%mod% %fastdl% $gsip:$port";
                $insertlog->execute();
            } else {
                $template_file='userpanel_404.tpl';
            }
        }
    } else {
        $template_file='userpanel_404.tpl';
    }
} else if ($ui->st('d','get')=='eu' and $pa['modfastdl']==true) {
    if (!$ui->smallletters('action',2,'post')) {
        $query=$sql->prepare("SELECT `fdlpath` FROM `userdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($user_id,$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $fdlpath=$row['fdlpath'];
        }
        if ($query->rowCount()>0) {
            $template_file='userpanel_gserver_fdl_eu.tpl';
        } else {
            $template_file='userpanel_404.tpl';
        }
    } else if ($ui->smallletters('action',2,'post')=='md'){
        if ($ui->url('fdlpath','post')) {
            $query=$sql->prepare("UPDATE `userdata` SET `fdlpath`=? WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($ui->url('fdlpath','post'),$user_id,$reseller_id));
            $template_file=$sprache->udsuc;
            $loguseraction="%mod% %fastdl% %master%";
            $insertlog->execute();
        }
    } else {
        $template_file='userpanel_404.tpl';
    }
} else {
    $query=$sql->prepare("SELECT `cname`,`fdlpath` FROM `userdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
    $query->execute(array($user_id,$reseller_id));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $fdlpath=explode('@',$row['fdlpath']);
        $username=$row['cname'];
    }
    if (!isset($fdlpath['1'])) $fdlpath['1']=$sprache->noset;
    $table=array();
    $query=$sql->prepare("SELECT `id`,`serverip`,`port` FROM `gsswitch` WHERE `active`='Y' AND `userid`=? AND `resellerid`=?");
    $query->execute(array($user_id,$reseller_id));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if (!isset($_SESSION['sID']) or in_array($row['id'],$substituteAccess['gs'])) $table[]=array('id'=>$row['id'],'serverip'=>$row['serverip'],'port'=>$row['port']);
    }
    $template_file="userpanel_gserver_fdl_list.tpl";
}