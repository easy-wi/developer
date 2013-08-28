<?php
/**
 * File: voice_tsdns.php.
 * Author: Ulrich Block
 * Date: 22.09.12
 * Time: 21:53
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

if ((!isset($admin_id) or $main!=1) or (isset($admin_id) and !$pa['voiceserver'])) redirect('admin.php');
$sprache=getlanguagefile('voice',$user_language,$reseller_id,$sql);
$loguserid=$admin_id;
$logusername=getusername($admin_id,$sql);
$logusertype="admin";
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
if ($reseller_id!=0 and $admin_id!=$reseller_id) {
    $reseller_id=$admin_id;
}
$aesfilecvar=getconfigcvars(EASYWIDIR."/stuff/keyphrasefile.php");
$aeskey=$aesfilecvar['aeskey'];
include(EASYWIDIR."/stuff/class_voice.php");
if ($ui->w('action',4,'post') and !token(true)) {
    $template_file=$spracheResponse->token;
} else if ($ui->st('d','get')=='ad' or $ui->st('d','get')=='md') {
    if ($ui->st('d','get')=='ad' and !$ui->smallletters('action',2,'post')) {
        $template_file='admin_voice_tsdns_add.tpl';
    } else if ($ui->st('d','get')=='md' and !$ui->smallletters('action',2,'post') and $ui->id('id',19,'get')) {
        $id=$ui->id('id',19,'get');
        $query=$sql->prepare("SELECT *,AES_DECRYPT(`ssh2port`,:aeskey) AS `decryptedssh2port`,AES_DECRYPT(`ssh2user`,:aeskey) AS `decryptedssh2user`,AES_DECRYPT(`ssh2password`,:aeskey) AS `decryptedssh2password` FROM `voice_tsdns` WHERE `id`=:id AND `resellerid`=:reseller_id LIMIT 1");
        $query->execute(array(':aeskey'=>$aeskey,':id'=>$id,':reseller_id'=>$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $active=$row['active'];
            $description=$row['description'];
            $autorestart=$row['autorestart'];
            $defaultdns=$row['defaultdns'];
            $publickey=$row['publickey'];
            $ssh2ip=$row['ssh2ip'];
            $ssh2port=$row['decryptedssh2port'];
            $ssh2user=$row['decryptedssh2user'];
            $ssh2password=$row['decryptedssh2password'];
            $serverdir=$row['serverdir'];
            $keyname=$row['keyname'];
            $bit=$row['bitversion'];
        }
        if (isset($ssh2ip)) {
            $template_file='admin_voice_tsdns_md.tpl';
        } else {
            $template_file='Error: unknown ID';
        }
    } else if ($ui->smallletters('action',2,'post')=='ad' or $ui->smallletters('action',2,'post')=='md') {
        $error=array();
        if ($ui->active('active','post')) {
            $active=$ui->active('active','post');
        } else {
            $error[]='Active';
        }
        if ($ui->active('autorestart','post')) {
            $autorestart=$ui->active('autorestart','post');
        } else {
            $error[]='autorestart';
        }
        if ($ui->active('publickey','post')) {
            $publickey=$ui->active('publickey','post');
        } else {
            $error[]="Public key";
        }
        if ($ui->ip('ip','post')) {
            $ip=$ui->ip('ip','post');
        } else {
            $error[]="IP";
        }
        if ($ui->port('port','post')) {
            $port=$ui->port('port','post');
        } else {
            $error[]="Port";
        }
        if ($ui->username('user',50,'post')) {
            $user=$ui->username('user',50,'post');
        } else {
            $error[]="Username";
        }
        if ($ui->id('bit',2,'post')) {
            $bit=$ui->id('bit',2,'post');
        } else {
            $error[]="Bit";
        }
        $defaultdns=strtolower($ui->domain('defaultdns','post'));
        $keyname=$ui->startparameter('keyname','post');
        $pass=$ui->startparameter('pass','post');
        $serverdir=$ui->folder('serverdir','post');
        $description=$ui->escaped('description','post');
        if (count($error)>0) {
            $template_file='Error: '.implode('<br />',$error);
        } else {
            if ($ui->smallletters('action',2,'post')=='ad') {
                $log='add';
                $query=$sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `voice_tsdns` WHERE `ssh2ip`=? LIMIT 1");
                $query->execute(array($ip));
                if ($query->fetchColumn()==0) {
                    $query=$sql->prepare("INSERT INTO `voice_tsdns` (`active`,`bitversion`,`defaultdns`,`publickey`,`ssh2ip`,`ssh2port`,`ssh2user`,`ssh2password`,`serverdir`,`keyname`,`autorestart`,`description`,`resellerid`) VALUES (:active,:bit,:defaultdns,:publickey,:ssh2ip,AES_ENCRYPT(:ssh2port,:aeskey),AES_ENCRYPT(:ssh2user,:aeskey),AES_ENCRYPT(:ssh2password,:aeskey),:serverdir,:keyname,:autorestart,:description,:reseller_id)");
                    $query->execute(array(':aeskey'=>$aeskey,':active'=>$active,':bit'=>$bit,':defaultdns'=>$defaultdns,':publickey'=>$publickey,':ssh2ip'=>$ip,':ssh2port'=>$port,':ssh2user'=>$user,':ssh2password'=>$pass,':serverdir'=>$serverdir,':keyname'=>$keyname,':autorestart'=>$autorestart,':description'=>$description,':reseller_id'=>$reseller_id));
                } else {
                    $insterfail=true;
                }
            } else if ($ui->smallletters('action',2,'post')=='md') {
                $log='mod';
                $id=$ui->id('id',19,'get');
                $query=$sql->prepare("UPDATE `voice_tsdns` SET `active`=:active,`bitversion`=:bit,`defaultdns`=:defaultdns,`publickey`=:publickey,`ssh2ip`=:ssh2ip,`ssh2port`=AES_ENCRYPT(:ssh2port,:aeskey),`ssh2user`=AES_ENCRYPT(:ssh2user,:aeskey),`ssh2password`=AES_ENCRYPT(:ssh2password,:aeskey),`serverdir`=:serverdir,`keyname`=:keyname,`autorestart`=:autorestart,`description`=:description WHERE `id`=:id AND `resellerid`=:reseller_id LIMIT 1");
                $query->execute(array(':aeskey'=>$aeskey,':active'=>$active,':bit'=>$bit,':defaultdns'=>$defaultdns,':publickey'=>$publickey,':ssh2ip'=>$ip,':ssh2port'=>$port,':ssh2user'=>$user,':ssh2password'=>$pass,':serverdir'=>$serverdir,':keyname'=>$keyname,':autorestart'=>$autorestart,':description'=>$description,':id'=>$id,':reseller_id'=>$reseller_id));
            }
            if (!isset($insterfail) and $query->rowCount()>0) {
                $loguseraction="%$log% %voserver% %tsdns% $ip";
                $insertlog->execute();
                if ($ui->smallletters('action',2,'post')=='md') {
                    $template_file=$spracheResponse->table_add;
                } else {
                    $query=$sql->prepare("SELECT `id` FROM `voice_tsdns` WHERE `ssh2ip`=? ORDER BY `id` DESC LIMIT 1");
                    $query->execute(array($ip));
                    $id=$query->fetchColumn();
                    $dnsarray=tsdns('li',$ip,$port,$user,$publickey,$keyname,$pass,'N',$serverdir,$bit,array(''),array(''),array(''),$reseller_id,$sql);
                    $newArray=array();
                    if(is_array($dnsarray)) {
                        $table=array();
                        $query=$sql->prepare("SELECT `id`,`cname`,`vname`,`name` FROM `userdata` WHERE `resellerid`=? AND `accounttype`='u' ORDER BY `id` DESC");
                        $query->execute(array($reseller_id));
                        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                            $table[$row['id']]=trim($row['cname'].' '.$row['vname'].' '.$row['name']);
                        }
                        $query=$sql->prepare("SELECT `prefix1`,`prefix2` FROM `settings` WHERE `resellerid`=? LIMIT 1");
                        $query->execute(array($reseller_id));
                        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                            if ($row['prefix1']=='Y') {
                                $newuser=1;
                            } else {
                                $newuser=2;
                            }
                        }
                        $usprache=getlanguagefile('user',$user_language,$reseller_id,$sql);
                        $newArray=array();
                        $maxPost=@ini_get('suhosin.post.max_vars');
                        $maxRequests=@ini_get('suhosin.request.max_vars');
                        if($maxRequests and $maxPost and $maxPost<$maxRequests) {
                            $max=$maxPost;
                        } else {
                            $max=$maxRequests;
                        }
                        if (isset($max)) {
                            $max=($max-10)/6;
                        } else {
                            $max=count($dnsarray);
                        }
                        $i=0;
                        $query=$sql->prepare("SELECT `tsdnsID` FROM `voice_dns` WHERE `dns`=? AND `resellerID`=? LIMIT 1");
                        $query2=$sql->prepare("SELECT `id` FROM `voice_server` WHERE `dns`=? AND `resellerid`=? LIMIT 1");
                        foreach ($dnsarray as $k=>$v) {
                            $query->execute(array($v,$reseller_id));
                            $query2->execute(array($v,$reseller_id));
                            $ex=explode(':',$k);
                            if ($query->rowCount()==0 and $query2->rowCount()==0 and $i<=$max and isset($ex[1]) and port($ex[1])) {
                                $newArray[$k]=$v;
                                $i++;
                            }
                        }
                    }
                    $template_file='admin_voice_tsdns_import.tpl';
                }
            } else {
                $template_file=$spracheResponse->error_table;
            }
        }
    } else {
        $template_file='admin_404.tpl';
    }
} else if ($ui->st('d','get')=='ip' and $ui->id('id',19,'get')) {
    $id=$ui->id('id',19,'get');
    if (!$ui->smallletters('action',2,'post')) {
        $query=$sql->prepare("SELECT *,AES_DECRYPT(`ssh2port`,:aeskey) AS `decryptedssh2port`,AES_DECRYPT(`ssh2user`,:aeskey) AS `decryptedssh2user`,AES_DECRYPT(`ssh2password`,:aeskey) AS `decryptedssh2password` FROM `voice_tsdns` WHERE `id`=:id AND `resellerid`=:reseller_id LIMIT 1");
        $query->execute(array(':aeskey'=>$aeskey,':id'=>$id,':reseller_id'=>$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $publickey=$row['publickey'];
            $ssh2ip=$row['ssh2ip'];
            $ssh2port=$row['decryptedssh2port'];
            $ssh2user=$row['decryptedssh2user'];
            $ssh2password=$row['decryptedssh2password'];
            $serverdir=$row['serverdir'];
            $keyname=$row['keyname'];
            $bit=$row['bitversion'];
        }
        $dnsarray=tsdns('li',$ssh2ip,$ssh2port,$ssh2user,$publickey,$keyname,$ssh2password,'N',$serverdir,$bit,array(''),array(''),array(''),$reseller_id,$sql);
        if(is_array($dnsarray)) {
            $table=array();
            $query=$sql->prepare("SELECT `id`,`cname`,`vname`,`name` FROM `userdata` WHERE `resellerid`=? AND `accounttype`='u' ORDER BY `id` DESC");
            $query->execute(array($reseller_id));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $table[$row['id']]=trim($row['cname'].' '.$row['vname'].' '.$row['name']);
            }
            $query=$sql->prepare("SELECT `prefix1`,`prefix2` FROM `settings` WHERE `resellerid`=? LIMIT 1");
            $query->execute(array($reseller_id));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                if ($row['prefix1']=='Y') {
                    $newuser=1;
                } else {
                    $newuser=2;
                }
            }
            $usprache=getlanguagefile('user',$user_language,$reseller_id,$sql);
            $newArray=array();
            $maxPost=@ini_get('suhosin.post.max_vars');
            $maxRequests=@ini_get('suhosin.request.max_vars');
            if($maxRequests and $maxPost and $maxPost<$maxRequests) {
                $max=$maxPost;
            } else {
                $max=$maxRequests;
            }
            if (isset($max)) {
                $max=($max-10)/7;
            } else {
                $max=count($dnsarray);
            }
            $i=0;
            $query=$sql->prepare("SELECT `tsdnsID` FROM `voice_dns` WHERE `dns`=? AND `resellerID`=? LIMIT 1");
            $query2=$sql->prepare("SELECT `id` FROM `voice_server` WHERE `dns`=? AND `resellerid`=? LIMIT 1");
            foreach ($dnsarray as $k=>$v) {
                $query->execute(array($v,$reseller_id));
                $query2->execute(array($v,$reseller_id));
                $ex=explode(':',$k);
                if ($query->rowCount()==0 and $query2->rowCount()==0 and $i<=$max and isset($ex[1]) and port($ex[1])) {
                    $newArray[$k]=$v;
                    $i++;
                }
            }
        }
        $template_file='admin_voice_tsdns_import.tpl';
    } else if ($ui->smallletters('action',2,'post')=='ip') {
        $query=$sql->prepare("SELECT `prefix2` FROM `settings` WHERE `resellerid`=? LIMIT 1");
        $query->execute(array($reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $prefix=$row['prefix2'];
        }
        $added='';
        foreach ($ui->domain('dns','post') as $dns) {
            $lookUp=str_replace('.','_',$dns);
            $ex=explode(':',$ui->ipport("${lookUp}-address",'post'));
            if ($ui->active("${lookUp}-import",'post')=='Y'  and isset($ex[1]) and port($ex[1])) {
                $ip=$ex[0];
                $port=$ex[1];
                $customer=$ui->id("${lookUp}-customer",19,'post');
                if ($customer==0 or $customer==false or $customer==null) {
                    $usernew=true;
                    if ($ui->username("${lookUp}-username",50,'post') and $ui->ismail("${lookUp}-email",'post')) {
                        $query=$sql->prepare("SELECT `id` FROM `userdata` WHERE `cname`=? AND `mail`=? AND `resellerid`=? LIMIT 1");
                        $query->execute(array($ui->username("${lookUp}-username",50,'post'),$ui->ismail("${lookUp}-email",'post'),$reseller_id));
                        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                            $usernew=false;
                            $customer=$row['id'];
                            $cnamenew=$ui->username("${lookUp}-username",50,'post');
                        }
                        if ($usernew==true) {
                            $initialpassword=passwordgenerate(10);
                            $salt=md5(mt_rand().date('Y-m-d H:i:s:u'));
                            $security=createHash($ui->username("${lookUp}-username",50,'post'),$initialpassword,$salt,$aeskey);
                            $query=$sql->prepare("INSERT INTO `userdata` (`cname`,`security`,`mail`,`accounttype`,`resellerid`) VALUES (?,?,?,'u',?)");
                            $query->execute(array($ui->username("${lookUp}-username",50,'post'),$security,$ui->ismail("${lookUp}-email",'post'),$reseller_id));
                            $query=$sql->prepare("SELECT `id` FROM `userdata` WHERE `cname`=? AND `mail`=? AND `resellerid`=? ORDER BY `id` DESC LIMIT 1");
                            $query->execute(array($ui->username("${lookUp}-username",50,'post'),$ui->ismail("${lookUp}-email",'post'),$reseller_id));
                            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                                $customer=$row['id'];
                                $cnamenew=$ui->username("${lookUp}-username",50,'post');
                                sendmail('emailuseradd',$customer,$cnamenew,$initialpassword,$sql);
                            }
                        }
                    } else {
                        $cldbid=rand(1,100).'.'.rand(1,100);
                        $cnamenew=$prefix.$cldbid;
                        $query=$sql->prepare("INSERT INTO `userdata` (`cname`,`security`,`mail`,`accounttype`,`resellerid`) VALUES (?,?,?,'u',?)");
                        $query->execute(array($cnamenew,passwordgenerate(10),'ts3@import.mail',$reseller_id));
                        $query=$sql->prepare("SELECT `id` FROM `userdata` WHERE `cname`=? AND `mail`='ts3@import.mail' ORDER BY `id` DESC LIMIT 1");
                        $query->execute(array($cnamenew));
                        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                            $customer=$row['id'];
                            $cnamenew=$prefix.$customer;
                        }
                        $query=$sql->prepare("UPDATE `userdata` SET `cname`=? WHERE `id`=? AND `resellerid`=? LIMIT 1");
                        $query->execute(array($cnamenew,$customer,$reseller_id));
                    }
                    if ($usernew==true) {
                        $query=$sql->prepare("SELECT `id` FROM `usergroups` WHERE `active`='Y' AND `defaultgroup`='Y' AND `grouptype`='u' AND `resellerid`=? LIMIT 1");
                        $query->execute(array($reseller_id));
                        $groupID=$query->fetchColumn();
                        $query=$sql->prepare("UPDATE `userdata` SET `usergroup`=? WHERE id=? AND `resellerid`=? LIMIT 1");
                        $query->execute(array($groupID,$customer,$reseller_id));
                    }
                    $added .='User '.$cnamenew.' ';
                } else {
                    $query=$sql->prepare("SELECT `cname` FROM `userdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                    $query->execute(array($customer,$reseller_id));
                    $cnamenew=$query->fetchColumn();
                }
                $added .='Server '.$ip.':'.$port.':'.$dns.'<br />';
                $query=$sql->prepare("INSERT INTO `voice_dns` (`active`,`dns`,`ip`,`port`,`tsdnsID`,`userID`,`externalID`,`resellerID`) VALUES (?,?,?,?,?,?,?,?)");
                $query->execute(array('Y',$dns,$ip,$port,$id,$customer,'',$reseller_id));
            }
        }
        $template_file=$added;
    }
} else if ($ui->st('d','get')=='dl' and $ui->id('id',19,'get')) {
    $id=$ui->id('id',19,'get');
    if (!$ui->smallletters('action',2,'post')) {
        $query=$sql->prepare("SELECT `ssh2ip`,`description` FROM `voice_tsdns` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id,$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $ip=$row['ssh2ip'];
            $description=$row['description'];
        }
        if (isset($ip)) {
            $template_file='admin_voice_tsdns_dl.tpl';
        } else {
            $template_file='Error: unknown ID';
        }
    } else if ($ui->smallletters('action',2,'post')=='dl'){
        $query=$sql->prepare("SELECT `ssh2ip` FROM `voice_tsdns` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id,$reseller_id));
        $ip=$query->fetchColumn();
        if ($query->rowCount()>0) {
            $query=$sql->prepare("UPDATE `voice_masterserver` SET `tsdnsServerID`=NULL WHERE `tsdnsServerID`=? AND `resellerid`=?");
            $query->execute(array($id,$reseller_id));
            $query=$sql->prepare("DELETE FROM `voice_tsdns` WHERE `id`=? AND `resellerid`=?");
            $query->execute(array($id,$reseller_id));
            $loguseraction="%del% %voserver% %tsdns% $ip";
            $insertlog->execute();
            $template_file=$spracheResponse->table_del;
        } else {
            $template_file='Error: unknown ID';
        }
    } else {
        $template_file='admin_404.tpl';
    }
} else {
    $o=$ui->st('o','get');
    if ($ui->st('o','get')=='da') {
        $orderby='`active` DESC';
    } else if ($ui->st('o','get')=='aa') {
        $orderby='`active` ASC';
    } else if ($ui->st('o','get')=='dp') {
        $orderby='`ssh2ip` DESC';
    } else if ($ui->st('o','get')=='ap') {
        $orderby='`ssh2ip` ASC';
    } else if ($ui->st('o','get')=='dd') {
        $orderby='`defaultdns` DESC';
    } else if ($ui->st('o','get')=='ad') {
        $orderby='`defaultdns` ASC';
    } else if ($ui->st('o','get')=='db') {
        $orderby='`description` DESC';
    } else if ($ui->st('o','get')=='ab') {
        $orderby='`description` ASC';
    } else if ($ui->st('o','get')=='di') {
        $orderby='`id` DESC';
    } else {
        $orderby='`id` ASC';
        $o='ai';
    }
    $query=$sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `voice_tsdns` WHERE `resellerid`=?");
    $query->execute(array($reseller_id));
    $colcount=$query->fetchColumn();
    if ($start>$colcount) {
        $start=$colcount-$amount;
        if ($start<0)$start=0;
    }
    $table=array();
    $query=$sql->prepare("SELECT * FROM `voice_tsdns` WHERE `resellerid`=? ORDER BY $orderby LIMIT $start,$amount");
    $query2=$sql->prepare("SELECT `dnsID`,`active`,`dns` FROM `voice_dns` WHERE `tsdnsID`=? AND `resellerID`=?");
    $query->execute(array($reseller_id));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if ($row['active']=='Y') {
            if ($row['notified']>2) {
                $imgName='16_error';
                $imgAlt='16_error';
            } else {
                $imgName='16_ok';
                $imgAlt='online';
            }
        } else {
            $imgName='16_bad';
            $imgAlt='inactive';
        }
        $ds=array();
        $query2->execute(array($row['id'],$reseller_id));
        foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) $ds[]=array('id'=>$row2['dnsID'],'address'=>$row2['dns'],'status'=>($row2['active']=='N') ? 2 : 1);
        $table[]=array('id'=>$row['id'],'active'=>$row['active'],'img'=>$imgName,'alt'=>$imgAlt,'ip'=>$row['ssh2ip'],'defaultdns'=>$row['defaultdns'],'description'=>$row['description'],'server'=>$ds);
    }
    $next=$start+$amount;
    $vor=($colcount>$next) ? $start+$amount : $start;
    $back=$start-$amount;
    $zur=($back>=0) ? $start-$amount : $start;
    $pageamount=ceil($colcount/$amount);
    $pages[]='<a href="admin.php?w=vd&amp&amp;o='.$o.'&amp;a=' . (!isset($amount)) ? 20 : $amount . ($start==0) ? '&p=0" class="bold">1</a>' : '&p=0">1</a>';
    $i=2;
    while ($i<=$pageamount) {
        $selectpage=($i-1)*$amount;
        $pages[]='<a href="admin.php?w=vd&amp&amp;o='.$o.'&amp;a='.$amount.'&p='.$selectpage.'"' . ($start==$selectpage) ? 'class="bold"' : '' . ' >'.$i.'</a>';
        $i++;
    }
    $pages=implode(', ',$pages);
    $template_file='admin_voice_tsdns_list.tpl';
}