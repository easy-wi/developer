<?php
/**
 * File: voice_master.php.
 * Author: Ulrich Block
 * Date: 23.09.12
 * Time: 11:16
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

if ((!isset($admin_id) or $main!=1) or (isset($admin_id) and !$pa['voicemasterserver'])) {
    redirect('admin.php');
}
$sprache=getlanguagefile('voice',$user_language,$reseller_id);
$loguserid=$admin_id;
$logusername=getusername($admin_id);
$logusertype="admin";
if ($reseller_id==0) {
    $logreseller=0;
    $logsubuser=0;
} else {
    $logsubuser=(isset($_SESSION['oldid'])) ? $_SESSION['oldid'] : 0;
    $logreseller=0;
}
if ($reseller_id!=0 and $admin_id!=$reseller_id) $reseller_id=$admin_id;
$aesfilecvar=getconfigcvars(EASYWIDIR."/stuff/keyphrasefile.php");
$aeskey=$aesfilecvar['aeskey'];
include(EASYWIDIR."/stuff/class_voice.php");
if ($ui->w('action',4,'post') and !token(true)) {
    $template_file=$spracheResponse->token;
} else if ($ui->st('d','get')=='ad' or (($ui->st('d','get')=='ri' or $ui->st('d','get')=='md')and $id=$ui->id('id',10,'get'))) {
    if (!$ui->w('action',3,'post') and $ui->st('d','get')=='ad') {
        $roots=array();
        $query=$sql->prepare("SELECT `id`,`ip` FROM `rserverdata` WHERE `active`='Y' AND `resellerid`=?");
        $query->execute(array($reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $query2=$sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `voice_masterserver` WHERE `rootid`=? AND `resellerid`=?");
            $query2->execute(array($row['id'],$reseller_id));
            $colcount=$query2->fetchColumn();
            if($colcount==0) $roots[]='<option value="'.$row['id'].'">'.$row['ip'].'</option>';
        }
        $brandname=$rSA['brandname'];
        $externalDNS=array();
        $query=$sql->prepare("SELECT `id`,`ssh2ip`,`description` FROM `voice_tsdns` WHERE `active`='Y' AND `resellerid`=?");
        $query->execute(array($reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) $externalDNS[$row['id']]=$row['ssh2ip'].': '.$row['description'];
        $template_file='admin_voicemasterserver_add.tpl';
    } else if ($ui->st('d','get')=='md' and !$ui->w('action',3,'post') and $ui->id('id',19,'get')) {
        $id=$ui->id('id',10,'get');
        $query=$sql->prepare("SELECT *,AES_DECRYPT(`querypassword`,:aeskey) AS `decryptedquerypassword`,AES_DECRYPT(`ssh2port`,:aeskey) AS `decryptedssh2port`,AES_DECRYPT(`ssh2user`,:aeskey) AS `decryptedssh2user`,AES_DECRYPT(`ssh2password`,:aeskey) AS `decryptedssh2password` FROM `voice_masterserver` WHERE `id`=:id AND `resellerid`=:reseller_id LIMIT 1");
        $query->execute(array(':aeskey'=>$aeskey,':id'=>$id,':reseller_id'=>$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $active=$row['active'];
            $defaultname=$row['defaultname'];
            $addedby=$row['addedby'];
            $autorestart=$row['autorestart'];
            $externalID=$row['externalID'];
            $tsdnsServerID=$row['tsdnsServerID'];
            $externalDefaultDNS=$row['externalDefaultDNS'];
            $defaultFlexSlotsFree=$row['defaultFlexSlotsFree'];
            $defaultFlexSlotsPercent=$row['defaultFlexSlotsPercent'];
            if ($row['type']=='ts3') {
                $type=$sprache->ts3;
                $usedns=$row['usedns'];
                $defaultdns=$row['defaultdns'];
                $defaultwelcome=$row['defaultwelcome'];
                $defaulthostbanner_url=$row['defaulthostbanner_url'];
                $defaulthostbanner_gfx_url=$row['defaulthostbanner_gfx_url'];
                $defaulthostbutton_tooltip=$row['defaulthostbutton_tooltip'];
                $defaulthostbutton_url=$row['defaulthostbutton_url'];
                $defaulthostbutton_gfx_url=$row['defaulthostbutton_gfx_url'];
                $queryport=$row['queryport'];
                $querypassword=$row['decryptedquerypassword'];
                $filetransferport=$row['filetransferport'];
                $maxserver=$row['maxserver'];
                $maxslots=$row['maxslots'];
            }
            if ($addedby==2) {
                $publickey=$row['publickey'];
                $ssh2ip=$row['ssh2ip'];
                $ips=$row['ips'];
                $ssh2port=$row['decryptedssh2port'];
                $ssh2user=$row['decryptedssh2user'];
                $ssh2password=$row['decryptedssh2password'];
                $serverdir=$row['serverdir'];
                $keyname=$row['keyname'];
                $bit=$row['bitversion'];
            } else if ($addedby==1) {
                $query=$sql->prepare("SELECT `ip` FROM `rserverdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($row['rootid'],$reseller_id));
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $root=$row['ip'];
                }
            }
        }
        $externalDNS=array();
        $query=$sql->prepare("SELECT `id`,`ssh2ip`,`description` FROM `voice_tsdns` WHERE `active`='Y' AND `resellerid`=?");
        $query->execute(array($reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $externalDNS[$row['id']]=$row['ssh2ip'].': '.$row['description'];
        }
        if (isset($defaultname)) {
            $template_file='admin_voicemasterserver_md.tpl';
        } else {
            $template_file='Error: ID';
        }
    } else if ($ui->w('action',3,'post')=='ad' or $ui->w('action',3,'post')=='md' or ($ui->st('d','get')=='ri' and $id=$ui->id('id',10,'get')) and $ui->w('action',3,'post')!='ad2') {
        $error=array();
        if ($ui->st('d','get')=='ad' or $ui->st('d','get')=='md') {
            if ($ui->active('active','post')) {
                $active=$ui->active('active','post');
            } else {
                $error[]='Active';
            }
            if ($ui->active('autorestart','post')) {
                $autorestart=$ui->active('autorestart','post');
            } else {
                $error[]='Autorestart';
            }
            if ($ui->startparameter('defaultname','post')) {
                $defaultname=$ui->startparameter('defaultname','post');
            } else {
                $error[]='Defaultname';
            }
            if ($ui->active('externalDefaultDNS','post')) {
                $externalDefaultDNS=$ui->active('externalDefaultDNS','post');
            } else {
                $error[]='externalDefaultDNS';
            }
            if (($ui->w('action',3,'post')=='ad' and $ui->id('addtype',1,'post')==2) or $ui->w('action',3,'post')=='md') {
                if ($ui->active('publickey','post')) {
                    $publickey=$ui->active('publickey','post');
                } else {
                    $error[]='Public Key';
                }
                if ($ui->ip('ip','post')) {
                    $ip=$ui->ip('ip','post');
                } else {
                    $error[]='IP';
                }
                if ($ui->port('port','post')) {
                    $port=$ui->port('port','post');
                } else {
                    $error[]='Port';
                }
                if ($ui->username('user',50,'post')) {
                    $user=$ui->username('user',50,'post');
                } else {
                    $error[]='Username';
                }
                if ($ui->id('bit',2,'post')) {
                    $bit=$ui->id('bit',2,'post');
                } else {
                    $error[]='Bit';
                }
                $externalID=$ui->escaped('externalID','post');
                $newuser=$ui->id('newuser',1,'post');
                $ips=$ui->ips('ips','post');
                $addtype=2;
                $rootid=0;
                $keyname=$ui->startparameter('keyname','post');
                $pass=$ui->startparameter('pass','post');
                $serverdir=$ui->folder('serverdir','post');
            } else {
                $addtype=1;
                $publickey=null;
                $ip=null;
                $ips=null;
                $port=null;
                $user=null;
                $bit=null;
                $pass=null;
                $keyname=null;
                $serverdir=null;
                if ($ui->id('rootid',2,'post')) {
                    $rootid=$ui->id('rootid',2,'post');
                } else {
                    $error[]='RootID';
                }
            }
            if (($ui->w('action',3,'post')=='ad' and $ui->w('type',3,'post')=='ts3') or $ui->w('action',3,'post')=='md') {
                if ($ui->active('usedns','post')) {
                    $usedns=$ui->active('usedns','post');
                } else {
                    $error[]='Use DNS';
                }
                if ($ui->password('querypassword',50,'post')) {
                    $querypassword=$ui->password('querypassword',50,'post');
                } else {
                    $error[]='Querypassword';
                }
                $defaultdns=strtolower($ui->domain('defaultdns','post'));
                $defaultwelcome=$ui->description('defaultwelcome','post');
                $defaulthostbanner_url=$ui->url('defaulthostbanner_url','post');
                $defaulthostbanner_gfx_url=$ui->url('defaulthostbanner_gfx_url','post');
                $defaulthostbutton_tooltip=$ui->description('defaulthostbutton_tooltip','post');
                $defaulthostbutton_url=$ui->url('defaulthostbutton_url','post');
                $defaulthostbutton_gfx_url=$ui->url('defaulthostbutton_gfx_url','post');
                $defaultFlexSlotsFree=$ui->id('defaultFlexSlotsFree',11,'post');
                $defaultFlexSlotsPercent=$ui->id('defaultFlexSlotsPercent',3,'post');
                $tsdnsServerID=$ui->id('tsdnsServerID',19,'post');
                if ($ui->port('queryport','post')) {
                    $queryport=$ui->port('queryport','post');
                } else {
                    $error[]='Queryport';
                }
                if ($ui->port('filetransferport','post')) {
                    $filetransferport=$ui->port('filetransferport','post');
                } else {
                    $error[]='Filetransferport';
                }
                if ($ui->id('maxserver',30,'post')) {
                    $maxserver=$ui->id('maxserver',30,'post');
                } else {
                    $error[]='Maxserver';
                }
                if ($ui->id('maxslots',30,'post')) {
                    $maxslots=$ui->id('maxslots',30,'post');
                } else {
                    $error[]='Maxslots';
                }
                $type='ts3';
            } else {
                $defaultdns=null;
                $defaultwelcome=null;
                $defaulthostbanner_url=null;
                $defaulthostbanner_gfx_url=null;
                $defaulthostbutton_tooltip=null;
                $defaulthostbutton_url=null;
                $defaulthostbutton_gfx_url=null;
                $queryport=null;
                $querypassword=null;
                $filetransferport=null;
                $maxserver=null;
                $maxslots=null;
                $usedns='N';
                $fail=1;
            }
        } else if ($ui->st('d','get')=='ri' and $id=$ui->id('id',10,'get')) {
            $masterid=$id=$ui->id('id',10,'get');
            $query=$sql->prepare("SELECT *,AES_DECRYPT(`querypassword`,:aeskey) AS `decryptedquerypassword`,AES_DECRYPT(`ssh2port`,:aeskey) AS `decryptedssh2port`,AES_DECRYPT(`ssh2user`,:aeskey) AS `decryptedssh2user`,AES_DECRYPT(`ssh2password`,:aeskey) AS `decryptedssh2password` FROM `voice_masterserver` WHERE `id`=:id AND `resellerid`=:reseller_id LIMIT 1");
            $query->execute(array(':aeskey'=>$aeskey,':id'=>$masterid,':reseller_id'=>$reseller_id));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $active=$row['active'];
                $defaultname=$row['defaultname'];
                $addtype=$row['addedby'];
                $rootid=$row['rootid'];
                $tsdnsServerID=$row['tsdnsServerID'];
                $externalDefaultDNS=$row['externalDefaultDNS'];
                $defaultFlexSlotsFree=$row['defaultFlexSlotsFree'];
                $defaultFlexSlotsPercent=$row['defaultFlexSlotsPercent'];
                if ($row['type']=='ts3') {
                    $type=$sprache->ts3;
                    $usedns=$row['usedns'];
                    $defaultdns=$row['defaultdns'];
                    $defaultwelcome=$row['defaultwelcome'];
                    $defaulthostbanner_url=$row['defaulthostbanner_url'];
                    $defaulthostbanner_gfx_url=$row['defaulthostbanner_gfx_url'];
                    $defaulthostbutton_tooltip=$row['defaulthostbutton_tooltip'];
                    $defaulthostbutton_url=$row['defaulthostbutton_url'];
                    $defaulthostbutton_gfx_url=$row['defaulthostbutton_gfx_url'];
                    $queryport=$row['queryport'];
                    $querypassword=$row['decryptedquerypassword'];
                    $filetransferport=$row['filetransferport'];
                    $maxserver=$row['maxserver'];
                    $maxslots=$row['maxslots'];
                }
                if ($addtype==2) {
                    $publickey=$row['publickey'];
                    $ip=$row['ssh2ip'];
                    $ips=$row['ips'];
                    $port=$row['decryptedssh2port'];
                    $user=$row['decryptedssh2user'];
                    $pass=$row['decryptedssh2password'];
                    $serverdir=$row['serverdir'];
                    $keyname=$row['keyname'];
                    $bit=$row['bitversion'];
                } else if ($addtype==1) {
                    $query=$sql->prepare("SELECT `ip` FROM `rserverdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                    $query->execute(array($rootid,$reseller_id));
                    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                        $ip=$row['ip'];
                    }
                }
            }
            $prefix1=$rSA['prefix1'];
            if ($prefix1=="Y") {
                $newuser=1;
            } else {
                $newuser=2;
            }
        } else {
            $error[]='Unknown Error 1';
        }
        if (count($error)>0) {
            $template_file='Error: '.implode('<br />',$error);
        } else {
            if ($ui->w('action',3,'post')=='ad' or ($ui->st('d','get')=='ri' and $id=$ui->id('id',10,'get') and $ui->w('action',3,'post')!='ad2')) {
                $usprache=getlanguagefile('user',$user_language,$reseller_id);
                $connected=false;
                if ($addtype==2) {
                    $table=array();
                    $query=$sql->prepare("SELECT `id`,`cname`,`name`,`vname` FROM `userdata` WHERE `resellerid`=? AND `accounttype`='u' ORDER BY `id` DESC");
                    $query->execute(array($reseller_id));
                    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                        $id=$row['id'];
                        if ($row['vname']!='' or $row['name']!='') {
                            $table["$id"]=$row['cname'].' ('.$row['vname'].' '.$row['name'].')';
                        } else {
                            $table["$id"]=$row['cname'];
                        }
                    }
                    if ($usedns=='Y') {
                        if (isset($tsdnsServerID) and isid($tsdnsServerID,19)) {
                            $query=$sql->prepare("SELECT *,AES_DECRYPT(`ssh2port`,:aeskey) AS `decryptedssh2port`,AES_DECRYPT(`ssh2user`,:aeskey) AS `decryptedssh2user`,AES_DECRYPT(`ssh2password`,:aeskey) AS `decryptedssh2password` FROM `voice_tsdns` WHERE `active`='Y' AND `id`=:id AND `resellerid`=:reseller_id LIMIT 1");
                            $query->execute(array(':aeskey'=>$aeskey,':id'=>$tsdnsServerID,':reseller_id'=>$reseller_id));
                            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                                $publickey=$row['publickey'];
                                $ip=$row['ssh2ip'];
                                $port=$row['decryptedssh2port'];
                                $user=$row['decryptedssh2user'];
                                $pass=$row['decryptedssh2password'];
                                $serverdir=$row['serverdir'];
                                $keyname=$row['keyname'];
                                $bit=$row['bitversion'];
                                if ($externalDefaultDNS=='Y') {
                                    $defaultdns=$row['defaultdns'];
                                }
                            }
                        }
                        $dnsarray=tsdns('li',$ip,$port,$user,$publickey,$keyname,$pass,'N',$serverdir,$bit,array(''),array(''),array(''),$reseller_id,$sql);
                        if (is_array($dnsarray)) {
                            $connected=true;
                        } else {
                            $error=$dnsarray;
                        }
                    } else {
                        $dnsarray=array();
                        $connected=true;
                    }
                    if ($connected==true) {
                        $connection=new TS3($ip,$queryport,'serveradmin',$querypassword);
                        $errorcode=$connection->errorcode;
                        if (strpos($errorcode,'error id=0') === false) {
                            $error=$errorcode;
                            $connected=false;
                        } else {
                            $i=1;
                            $servers=$connection->ImportData($dnsarray);
                            $query=$sql->prepare("SELECT `id` FROM `voice_server` WHERE `localserverid`=? AND `ip`=? AND `resellerid`=? LIMIT 1");
                            foreach ($servers as $virtualserver_id=>$values) {
                                $query->execute(array($virtualserver_id,$values['virtualserver_ip'],$reseller_id));
                                $colcount=$query->rowCount();
                                if ($colcount==1 or $i>25) {
                                    unset($servers["$virtualserver_id"]);
                                } else {
                                    $i++;
                                }
                            }
                        }
                        $connection->CloseConnection();
                    }
                }
                if ($connected==true) {
                    $query=$sql->prepare("SELECT `id` FROM `voice_masterserver` WHERE `rootid`=? AND `ssh2ip`=? AND `resellerid`=? LIMIT 1");
                    $query->execute(array($rootid,$ip,$reseller_id));
                    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                        $masterid=$row['id'];
                    }
                    if (!isset($masterid)) {
                        $query=$sql->prepare("INSERT INTO `voice_masterserver` (`active`,`type`,`defaultname`,`bitversion`,`queryport`,`querypassword`,`filetransferport`,`maxserver`,`maxslots`,`rootid`,`addedby`,`usedns`,`defaultdns`,`defaultwelcome`,`defaulthostbanner_url`,`defaulthostbanner_gfx_url`,`defaulthostbutton_tooltip`,`defaulthostbutton_url`,`defaulthostbutton_gfx_url`,`defaultFlexSlotsFree`,`defaultFlexSlotsPercent`,`publickey`,`ssh2ip`,`ssh2port`,`ssh2user`,`ssh2password`,`ips`,`serverdir`,`keyname`,`autorestart`,`externalID`,`tsdnsServerID`,`externalDefaultDNS`,`resellerid`) VALUES (:active,:type,:defaultname,:bit,:queryport,AES_ENCRYPT(:querypassword,:aeskey),:filetransferport,:maxserver,:maxslots,:rootid,:addedby,:usedns,:defaultdns,:defaultwelcome,:defaulthostbanner_url,:defaulthostbanner_gfx_url,:defaulthostbutton_tooltip,:defaulthostbutton_url,:defaulthostbutton_gfx_url,:defaultFlexSlotsFree,:defaultFlexSlotsPercent,:publickey,:ssh2ip,AES_ENCRYPT(:ssh2port,:aeskey),AES_ENCRYPT(:ssh2user,:aeskey),AES_ENCRYPT(:ssh2password,:aeskey),:ips,:serverdir,:keyname,:autorestart,:externalID,:tsdnsServerID,:externalDefaultDNS,:reseller_id)");
                        $query->execute(array(':aeskey'=>$aeskey,':active'=>$active,':type'=>$type,':defaultname'=>$defaultname,':bit'=>$bit,':queryport'=>$queryport,':querypassword'=>$querypassword,':filetransferport'=>$filetransferport,':maxserver'=>$maxserver,':maxslots'=>$maxslots,':rootid'=>$rootid,':addedby'=>$addtype,':usedns'=>$usedns,':defaultdns'=>$defaultdns,':defaultwelcome'=>$defaultwelcome,':defaulthostbanner_url'=>$defaulthostbanner_url,':defaulthostbanner_gfx_url'=>$defaulthostbanner_gfx_url,':defaulthostbutton_tooltip'=>$defaulthostbutton_tooltip,':defaulthostbutton_url'=>$defaulthostbutton_url,':defaulthostbutton_gfx_url'=>$defaulthostbutton_gfx_url,':defaultFlexSlotsFree'=>$defaultFlexSlotsFree,':defaultFlexSlotsPercent'=>$defaultFlexSlotsPercent,':publickey'=>$publickey,':ssh2ip'=>$ip,':ssh2port'=>$port,':ssh2user'=>$user,':ssh2password'=>$pass,':ips'=>$ips,':serverdir'=>$serverdir,':keyname'=>$keyname,':autorestart'=>$autorestart,':externalID'=>$externalID,':tsdnsServerID'=>$tsdnsServerID,':externalDefaultDNS'=>$externalDefaultDNS,':reseller_id'=>$reseller_id));
                        $loguseraction="%add% %voserver% %master% $ip";
                        $insertlog->execute();
                        $query=$sql->prepare("SELECT `id` FROM `voice_masterserver` WHERE `rootid`=? AND `ssh2ip`=? AND `resellerid`=? LIMIT 1");
                        $query->execute(array($rootid,$ip,$reseller_id));
                        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                            $masterid=$row['id'];
                        }
                    }
                    $template_file="admin_voicemasterserver_add2.tpl";
                } else {
                    $template_file=$error;
                }
            } else if ($ui->w('action',3,'post')=='md') {
                $tsstop='';
                $query=$sql->prepare("SELECT `active`,`type`,`rootid`,`addedby`,`ssh2ip`,`notified`,`usedns`,`publickey`,`keyname`,AES_DECRYPT(`ssh2port`,:aeskey) AS `decryptedssh2port`,AES_DECRYPT(`ssh2user`,:aeskey) AS `decryptedssh2user`,AES_DECRYPT(`ssh2password`,:aeskey) AS `decryptedssh2password`,`serverdir`,`bitversion` FROM `voice_masterserver` WHERE `id`=:id AND `resellerid`=:reseller_id");
                $query->execute(array(':aeskey'=>$aeskey,':id'=>$id,':reseller_id'=>$reseller_id));
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $oldactive=$row['active'];
                    $ts3masternotified=$row['notified'];
                    $addedby=$row['addedby'];
                    if ($addedby==2) {
                        $queryip=$row['ssh2ip'];
                    } else if ($addedby==1) {
                        $query2=$sql->prepare("SELECT `ip` FROM `rserverdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                        $query2->execute(array($row['rootid'],$reseller_id));
                        $queryip=$query->fetchColumn();
                    }
                    if (($oldactive=='Y' and $active=='N') or ($oldactive=='N' and $active=='Y')) {
                        if ($row['publickey']=="Y") {
                            $pubkey=EASYWIDIR."/keys/".$row['keyname'].".pub";
                            $key=EASYWIDIR."/keys/".$row['keyname'];
                            if (file_exists($pubkey) and file_exists($key)) {
                                $ssh2= @ssh2_connect($queryip,$row['decryptedssh2port'],array('hostkey'=>'ssh-rsa'));
                            } else {
                                $ssh2=false;
                            }
                        } else {
                            $ssh2= @ssh2_connect($queryip,$row['decryptedssh2port']);
                        }
                        if ($ssh2==true) {
                            if ($row['publickey']=="Y") {
                                $connect_ssh2= @ssh2_auth_pubkey_file($ssh2,$row['decryptedssh2user'],$pubkey,$key);
                            } else {
                                $connect_ssh2= @ssh2_auth_password($ssh2,$row['decryptedssh2user'],$row['decryptedssh2password']);
                            }
                            if ($connect_ssh2==true) {
                                $split_config=preg_split('/\//',$row['serverdir'], -1, PREG_SPLIT_NO_EMPTY);
                                $folderfilecount=count($split_config)-1;
                                $i=0;
                                while ($i<=$folderfilecount) {
                                    if (isset($folders)) {
                                        $folders .=$split_config["$i"]."/";
                                    } else {
                                        $folders='cd '.$split_config["$i"]."/";
                                    }
                                    $i++;
                                }
                                if (isset($folders)) {
                                    $folders .=$folders.' && ';
                                } else {
                                    $folders='';
                                }
                                if ($row['bitversion']=='32') {
                                    $tsbin='ts3server_linux_x86';
                                    $tsdnsbin='tsdnsserver_linux_x86';
                                } else {
                                    $tsbin='ts3server_linux_amd64';
                                    $tsdnsbin='tsdnsserver_linux_amd64';
                                }
                                if ($active=='N') {
                                    $ssh2cmd="ps fx | grep '$tsbin' | grep -v 'grep' | awk '{print $1}' | while read pid; do kill ".'$pid'."; done";
                                    $ssh2cmd2="ps fx | grep '$tsdnsbin' | grep -v 'grep' | awk '{print $1}' | while read pid; do kill ".'$pid'."; done";
                                } else if ($active=='Y') {
                                    $ssh2cmd=$folders.'function restart1 () { if [ "`ps fx | grep '.$tsbin.' | grep -v grep`" == "" ]; then ./ts3server_startscript.sh start > /dev/null & else ./ts3server_startscript.sh restart > /dev/null & fi }; restart1& ';
                                    $ssh2cmd2='cd tsdns && function restart2 () { if [ "`ps fx | grep '.$tsdnsbin.' | grep -v grep`" == "" ]; then ./'.$tsdnsbin.' > /dev/null & else ./'.$tsdnsbin.' --update > /dev/null & fi }; restart2& ';
                                }
                                ssh2_exec($ssh2,$ssh2cmd);
                                if ($row['usedns']=='Y') {
                                    ssh2_exec($ssh2,$ssh2cmd2);
                                }
                            } else {
                                $tsstop="Error Serverstop: Bad logindata<br />";
                            }
                        } else {
                            $tsstop="Error Serverstop: Can not connect via ssh2<br />";
                        }
                    }
                }
                $query=$sql->prepare("UPDATE `voice_masterserver` SET `active`=:active,`externalID`=:externalID,`defaultname`=:defaultname,`bitversion`=:bit,`queryport`=:queryport,`querypassword`=AES_ENCRYPT(:querypassword,:aeskey),`filetransferport`=:filetransferport,`maxserver`=:maxserver,`maxslots`=:maxslots,`usedns`=:usedns,`defaultdns`=:defaultdns,`defaultwelcome`=:defaultwelcome,`defaulthostbanner_url`=:defaulthostbanner_url,`defaulthostbanner_gfx_url`=:defaulthostbanner_gfx_url,`defaulthostbutton_tooltip`=:defaulthostbutton_tooltip,`defaulthostbutton_url`=:defaulthostbutton_url,`defaulthostbutton_gfx_url`=:defaulthostbutton_gfx_url,`defaultFlexSlotsFree`=:defaultFlexSlotsFree,`defaultFlexSlotsPercent`=:defaultFlexSlotsPercent,`publickey`=:publickey,`ssh2ip`=:ssh2ip,`ssh2port`=AES_ENCRYPT(:ssh2port,:aeskey),`ssh2user`=AES_ENCRYPT(:ssh2user,:aeskey),`ssh2password`=AES_ENCRYPT(:ssh2password,:aeskey),`ips`=:ips,`serverdir`=:serverdir,`keyname`=:keyname,`autorestart`=:autorestart,`tsdnsServerID`=:tsdnsServerID,`externalDefaultDNS`=:externalDefaultDNS WHERE `id`=:id AND `resellerid`=:reseller_id LIMIT 1");
                $query->execute(array(':aeskey'=>$aeskey,':active'=>$active,':externalID'=>$externalID,':defaultname'=>$defaultname,':bit'=>$bit,':queryport'=>$queryport,':querypassword'=>$querypassword,':filetransferport'=>$filetransferport,':maxserver'=>$maxserver,':maxslots'=>$maxslots,':usedns'=>$usedns,':defaultdns'=>$defaultdns,':defaultwelcome'=>$defaultwelcome,':defaulthostbanner_url'=>$defaulthostbanner_url,':defaulthostbanner_gfx_url'=>$defaulthostbanner_gfx_url,':defaulthostbutton_tooltip'=>$defaulthostbutton_tooltip,':defaulthostbutton_url'=>$defaulthostbutton_url,':defaulthostbutton_gfx_url'=>$defaulthostbutton_gfx_url,':defaultFlexSlotsFree'=>$defaultFlexSlotsFree,':defaultFlexSlotsPercent'=>$defaultFlexSlotsPercent,':publickey'=>$publickey,':ssh2ip'=>$ip,':ssh2port'=>$port,':ssh2user'=>$user,':ssh2password'=>$pass,':ips'=>$ips,':serverdir'=>$serverdir,':keyname'=>$keyname,':autorestart'=>$autorestart,':tsdnsServerID'=>$tsdnsServerID,':externalDefaultDNS'=>$externalDefaultDNS,':id'=>$id,':reseller_id'=>$reseller_id));
                $template_file=$tsstop.$spracheResponse->table_add;
                $loguseraction="%mod% %voserver% %master% $ip";
                $insertlog->execute();
            } else {
                $template_file='Unknown Error 2';
            }
        }
    } else if ($ui->w('action',3,'post')=='ad2') {
        if ($ui->id('id',10,'get')) {
            $masterid=$ui->id('id',10,'get');
            $query=$sql->prepare("SELECT *,AES_DECRYPT(`querypassword`,:aeskey) AS `decryptedquerypassword`,AES_DECRYPT(`ssh2port`,:aeskey) AS `decryptedssh2port`,AES_DECRYPT(`ssh2user`,:aeskey) AS `decryptedssh2user`,AES_DECRYPT(`ssh2password`,:aeskey) AS `decryptedssh2password` FROM `voice_masterserver` WHERE `id`=:id AND `resellerid`=:reseller_id LIMIT 1");
            $query->execute(array(':aeskey'=>$aeskey,':id'=>$masterid,':reseller_id'=>$reseller_id));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $defaultdns=$row['defaultdns'];
                $serverdir=$row['serverdir'];
                $addedby=$row['addedby'];
                $usedns=$row['usedns'];
                $defaultdns=$row['defaultdns'];
                $queryport=$row['queryport'];
                $querypassword=$row['decryptedquerypassword'];
                $mnotified=$row['notified'];
                $defaultwelcome=$row['defaultwelcome'];
                $defaulthostbanner_url=$row['defaulthostbanner_url'];
                $defaulthostbanner_gfx_url=$row['defaulthostbanner_gfx_url'];
                $defaulthostbutton_tooltip=$row['defaulthostbutton_tooltip'];
                $defaulthostbutton_url=$row['defaulthostbutton_url'];
                $defaulthostbutton_gfx_url=$row['defaulthostbutton_gfx_url'];
                $tsdnsServerID=$row['tsdnsServerID'];
                $externalDefaultDNS=$row['externalDefaultDNS'];
                if ($addedby==2) {
                    $publickey=$row['publickey'];
                    $queryip=$row['ssh2ip'];
                    $ssh2ip=$row['ssh2ip'];
                    $ssh2port=$row['decryptedssh2port'];
                    $ssh2user=$row['decryptedssh2user'];
                    $ssh2password=$row['decryptedssh2password'];
                    $keyname=$row['keyname'];
                    $bitversion=$row['bitversion'];
                } else if ($addedby==1) {
                    $query=$sql->prepare("SELECT `ip` FROM `rserverdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                    $query->execute(array($row['rootid'],$reseller_id));
                    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) $queryip=$row['ip'];
                }
            }
            if (isid($tsdnsServerID,19)) {
                $query=$sql->prepare("SELECT *,AES_DECRYPT(`ssh2port`,:aeskey) AS `decryptedssh2port`,AES_DECRYPT(`ssh2user`,:aeskey) AS `decryptedssh2user`,AES_DECRYPT(`ssh2password`,:aeskey) AS `decryptedssh2password` FROM `voice_tsdns` WHERE `active`='Y' AND `id`=:id AND `resellerid`=:reseller_id LIMIT 1");
                $query->execute(array(':aeskey'=>$aeskey,':id'=>$tsdnsServerID,':reseller_id'=>$reseller_id));
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $publickey=$row['publickey'];
                    $ssh2ip=$row['ssh2ip'];
                    $ssh2port=$row['decryptedssh2port'];
                    $ssh2user=$row['decryptedssh2user'];
                    $ssh2password=$row['decryptedssh2password'];
                    $serverdir=$row['serverdir'];
                    $keyname=$row['keyname'];
                    $bitversion=$row['bitversion'];
                    if ($externalDefaultDNS=='Y') $defaultdns=$row['defaultdns'];
                }
            }
            $connection=new TS3($queryip,$queryport,'serveradmin',$querypassword);
            $errorcode=$connection->errorcode;
            if (strpos($errorcode,'error id=0') === false) {
                $template_file=$errorcode;
                $connected=false;
            } else {
                $connected=true;
            }
        }
        if (isset($connected) and $connected==true) {
            $prefix=$rSA['prefix2'];
            $voiceleft=$licenceDetails['lVo'];
            $i=0;
            $toomuch=0;
            $added='<br />Added:';
            foreach ($ui->id('virtualserver_id',19,'post') as $virtualserver_id) {
                if ($ui->active("$virtualserver_id-import",'post')=='Y' and $voiceleft>0) {
                    $customer=$ui->id("$virtualserver_id-customer",19,'post');
                    if ($customer==0 or $customer==false or $customer==null) {
                        $usernew=true;
                        if ($ui->username("$virtualserver_id-username",50,'post') and $ui->ismail("$virtualserver_id-email",'post')) {
                            $query=$sql->prepare("SELECT `id` FROM `userdata` WHERE `cname`=? AND `mail`=? AND `resellerid`=? LIMIT 1");
                            $query->execute(array($ui->username("$virtualserver_id-username",50,'post'),$ui->ismail("$virtualserver_id-email",'post'),$reseller_id));
                            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                                $usernew=false;
                                $customer=$row['id'];
                                $cnamenew=$ui->username("$virtualserver_id-username",50,'post');
                            }
                            if ($usernew==true) {
                                $initialpassword=passwordgenerate(10);
                                $salt=md5(mt_rand().date('Y-m-d H:i:s:u'));
                                $security=createHash($ui->username("$virtualserver_id-username",50,'post'),$initialpassword,$salt,$aeskey);
                                $query=$sql->prepare("INSERT INTO `userdata` (`cname`,`security`,`mail`,`accounttype`,`salt`,`resellerid`) VALUES (?,?,?,'u',?,?)");
                                $query->execute(array($ui->username("$virtualserver_id-username",50,'post'),$security,$ui->ismail("$virtualserver_id-email",'post'),$salt,$reseller_id));
                                $query=$sql->prepare("SELECT `id` FROM `userdata` WHERE `cname`=? AND `mail`=? AND `resellerid`=? ORDER BY `id` DESC LIMIT 1");
                                $query->execute(array($ui->username("$virtualserver_id-username",50,'post'),$ui->ismail("$virtualserver_id-email",'post'),$reseller_id));
                                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                                    $customer=$row['id'];
                                    $cnamenew=$ui->username("$virtualserver_id-username",50,'post');
                                    sendmail('emailuseradd',$customer,$cnamenew,$initialpassword);
                                }
                            }
                        } else {
                            $userlist="";
                            $cldbid=rand(1,100).'.'.rand(1,100);
                            $adminList=$connection->AdminList($virtualserver_id);
                            if (is_array($adminList)) {
                                foreach ($adminList as $cldbid=>$client_unique_identifier) {
                                    $userlist .="$cldbid:$client_unique_identifier|";
                                }
                            }
                            $cnamenew=$prefix.$cldbid;
                            $query=$sql->prepare("INSERT INTO `userdata` (`cname`,`security`,`mail`,`accounttype`,`resellerid`) VALUES (?,?,?,'u',?)");
                            $query->execute(array($cnamenew,$userlist,'ts3@import.mail',$reseller_id));
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
                    $slots=$ui->id("$virtualserver_id-virtualserver_maxclients",30,'post');
                    $port=$ui->port("$virtualserver_id-virtualserver_port",'post');
                    $forcewelcome=$ui->active("$virtualserver_id-forcewelcome",'post');
                    $forcebanner=$ui->active("$virtualserver_id-forcebanner",'post');
                    $forcebutton=$ui->active("$virtualserver_id-forcebutton",'post');
                    $forceservertag=$ui->active("$virtualserver_id-forceservertag",'post');
                    $flexSlots=$ui->active("$virtualserver_id-flexSlots",'post');
                    $flexSlotsFree=$ui->id("$virtualserver_id-flexSlotsFree",11,'post');
                    $flexSlotsPercent=$ui->id("$virtualserver_id-flexSlotsPercent",3,'post');
                    if ($ui->id("$virtualserver_id-password",1,'post')==1) {
                        $password='Y';
                    } else {
                        $password='N';
                    }
                    $serverdns=($ui->domain("$virtualserver_id-virtualserver_dns",'post')=="") ? $cnamenew.'-'.$virtualserver_id.'.'.$defaultdns: $ui->domain("$virtualserver_id-virtualserver_dns",'post');
                    if($port!=null) {
                        $serverdns=strtolower($serverdns);
                        unset($addlist);
                        $addlist=array();
                        unset($removelist);
                        $removelist=array();
                        unset($settings);
                        $settings=array();
                        $settings['virtualserver_max_download_total_bandwidth']=65536;
                        $settings['virtualserver_max_upload_total_bandwidth']=65536;
                        if ($forcebanner=='Y') {
                            $removelist[]='b_virtualserver_modify_hostbanner';
                            $removelist[]='i_needed_modify_power_virtualserver_modify_hostbanner';
                            $settings['virtualserver_hostbanner_url']=$defaulthostbanner_url;
                            $settings['virtualserver_hostbanner_gfx_url']=$defaulthostbanner_gfx_url;
                        } else if ($forcebanner=='N') {
                            $addlist[]='b_virtualserver_modify_hostbanner';
                            $addlist[]='i_needed_modify_power_virtualserver_modify_hostbanner';
                        }
                        if ($forcebutton=='Y') {
                            $removelist[]='b_virtualserver_modify_hostbutton';
                            $removelist[]='i_needed_modify_power_virtualserver_modify_hostbutton';
                            $settings['virtualserver_hostbutton_url']=$defaulthostbutton_url;
                            $settings['virtualserver_hostbutton_gfx_url']=$defaulthostbutton_gfx_url;
                            $settings['virtualserver_hostbutton_tooltip']=$defaulthostbutton_tooltip;
                        } else if ($forcebutton=='N') {
                            $addlist[]='b_virtualserver_modify_hostbutton';
                            $addlist[]='i_needed_modify_power_virtualserver_modify_hostbutton';
                        }
                        if ($forcewelcome=='Y') {
                            $removelist[]='b_virtualserver_modify_welcomemessage';
                            $removelist[]='i_needed_modify_power_virtualserver_modify_welcomemessage';
                            $settings['virtualserver_welcomemessage']=$defaultwelcome;
                        } else if ($forcewelcome=='N') {
                            $addlist[]='b_virtualserver_modify_welcomemessage';
                            $addlist[]='i_needed_modify_power_virtualserver_modify_welcomemessage';
                        }
                        if (isset($addlist)) $connection->AdminPermissions($virtualserver_id,'add',$addlist);
                        if (isset($removelist)) $connection->AdminPermissions($virtualserver_id,'del',$removelist);
                        $connection->ImportModServer ($virtualserver_id,$slots,$ssh2ip,$port,$settings);
                        $added .='Server '.$ssh2ip.':'.$port.'<br />';
                        $query=$sql->prepare("INSERT INTO `voice_server` (`userid`,`masterserver`,`ip`,`port`,`slots`,`password`,`forcebanner`,`forcebutton`,`forceservertag`,`forcewelcome`,`dns`,`flexSlots`,`flexSlotsFree`,`flexSlotsPercent`,`localserverid`,`resellerid`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                        $query->execute(array($customer,$masterid,$ssh2ip,$port,$slots,$password,$forcebanner,$forcebutton,$forceservertag,$forcewelcome,$serverdns,$flexSlots,$flexSlotsFree,$flexSlotsPercent,$virtualserver_id,$reseller_id));
                    }
                    $i++;
                    $voiceleft--;
                }
                if ($voiceleft==0) {
                    $toomuch++;
                }
            }
            if ($toomuch>0) {
                $not='<br />'.$toomuch.' Server not importet because Easy-Wi Serverlimit has been reached';
            } else {
                $not='';
            }
            $connection->CloseConnection();
            if ($usedns=='Y') {
                $dns=array();
                if (isid($tsdnsServerID,19)) {
                    $query=$sql->prepare("SELECT `id` FROM `voice_masterserver` WHERE `tsdnsServerID`=? AND `resellerid`=?");
                    $query2=$sql->prepare("SELECT `ip`,`port`,`dns` FROM `voice_server` WHERE `masterserver`=? AND `resellerid`=?");
                    $query->execute(array($tsdnsServerID,$reseller_id));
                    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                        $query2->execute(array($row['id'],$reseller_id));
                        foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                            $dns[]=$row2['dns'].'='.$row2['ip'].':'.$row2['port'];
                        }
                    }
                } else {
                    $query=$sql->prepare("SELECT `ip`,`port`,`dns` FROM `voice_server` WHERE `masterserver`=? AND `resellerid`=?");
                    $query->execute(array($masterid,$reseller_id));
                    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                        $dns[]=$row['dns'].'='.$row['ip'].':'.$row['port'];
                    }
                }
                $dns=array_unique($dns);
                if ($i>0) {
                    $template_file=tsdns('mw',$ssh2ip,$ssh2port,$ssh2user,$publickey,$keyname,$ssh2password,$mnotified,$serverdir,$bitversion,$dns,'','',$reseller_id,$sql);
                }
            }
            $template_file=$spracheResponse->table_add.$not.$added;
        } else {
            $template_file='Unknown Error 3';
        }
    } else {
        $template_file='Unknown Error 4';
    }
} else if ($ui->st('d','get')=='dl' and $ui->id('id',19,'get')) {
    $id=$ui->id('id',10,'get');
    if (!$ui->w('action',2,'post')) {
        $query=$sql->prepare("SELECT `ssh2ip`,`rootid`,`type` FROM `voice_masterserver` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id,$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $ip=$row['ssh2ip'];
            if ($row['type']=='ts3') {
                $type=$sprache->ts3;
            }
            if ($ip==null) {
                $query=$sql->prepare("SELECT `ip` FROM `rserverdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($row['rootid'],$reseller_id));
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $ip=$row['ip'];
                }
            }
        }
        $template_file="admin_voicemasterserver_dl.tpl";
    } else if ($ui->w('action',2,'post')=='dl'){
        $query=$sql->prepare("SELECT `ssh2ip`,`rootid`,`type` FROM `voice_masterserver` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id,$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $ip=$row['ssh2ip'];
            if ($row['type']=='ts3') {
                $type=$sprache->ts3;
            }
            if ($ip==null) {
                $query=$sql->prepare("SELECT `ip` FROM `rserverdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($row['rootid'],$reseller_id));
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $ip=$row['ip'];
                }
            }
        }
        $query=$sql->prepare("DELETE FROM `voice_masterserver` WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id,$reseller_id));
        $query=$sql->prepare("DELETE FROM `voice_server` WHERE `masterserver`=? AND `resellerid`=?");
        $query->execute(array($id,$reseller_id));
        $loguseraction="%del% %voserver% %master% $ip $type";
        $insertlog->execute();
        $template_file=$spracheResponse->table_del;
    } else {
        $template_file='Unknown Error 4';
    }
} else {
    $o=$ui->st('o','get');
    if ($ui->st('o','get')=='da') {
        $orderby='m.`active` DESC';
    } else if ($ui->st('o','get')=='aa') {
        $orderby='m.`active` ASC';
    } else if ($ui->st('o','get')=='pn') {
        $orderby='m.`ssh2ip` DESC';
    } else if ($ui->st('o','get')=='pn') {
        $orderby='m.`ssh2ip` ASC';
    } else if ($ui->st('o','get')=='dt') {
        $orderby='m.`type` DESC';
    } else if ($ui->st('o','get')=='at') {
        $orderby='m.`type` ASC';
    } else if ($ui->st('o','get')=='ds') {
        $orderby='`installedserver` DESC';
    } else if ($ui->st('o','get')=='as') {
        $orderby='`installedserver` ASC';
    } else if ($ui->st('o','get')=='dl') {
        $orderby='`installedslots` DESC';
    } else if ($ui->st('o','get')=='al') {
        $orderby='`installedslots` ASC';
    } else if ($ui->st('o','get')=='dd') {
        $orderby='m.`defaultdns` DESC';
    } else if ($ui->st('o','get')=='ad') {
        $orderby='m.`defaultdns` ASC';
    } else if ($ui->st('o','get')=='di') {
        $orderby='m.`id` DESC';
    } else {
        $orderby='m.`id` ASC';
        $o='ai';
    }
    $query=$sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `voice_masterserver` WHERE `resellerid`=?");
    $query->execute(array($reseller_id));
    $colcount=$query->fetchColumn();
    if ($start>$colcount) {
        $start=$colcount-$amount;
        if ($start<0)$start=0;
    }
    $table=array();
    $query=$sql->prepare("SELECT m.`id`,m.`notified`,m.`rootid`,m.`active`,m.`type`,m.`ssh2ip`,m.`maxserver`,m.`maxslots`,m.`addedby`,m.`defaultdns`,m.`usedns`,COUNT(s.`id`) AS `installedserver`,SUM(s.`slots`) AS `installedslots`,SUM(s.`usedslots`) AS `uslots` FROM `voice_masterserver` m LEFT JOIN `voice_server` s ON m.`id`=s.`masterserver` WHERE m.`resellerid`=? GROUP BY m.`id` ORDER BY $orderby LIMIT $start,$amount");
    $query2=$sql->prepare("SELECT `ip` FROM `rserverdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
    $query3=$sql->prepare("SELECT `id`,`active`,`uptime`,CONCAT(`ip`,':',`port`) AS `address` FROM `voice_server` WHERE `masterserver`=? AND `resellerid`=?");
    $query->execute(array($reseller_id));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $id=$row['id'];
        if ($id!=null) {
            if ($row['type']=='ts3') $type=$sprache->ts3;
            if ($row['active']=='Y' and $downChecks>$row['notified']) {
                $imgName='16_ok';
                $imgAlt='ok';
            } else if ($row['active']=='Y' and $downChecks<=$row['notified']) {
                $imgName='16_error';
                $imgAlt='crashed';
            } else {
                $imgName='16_bad';
                $imgAlt='inactive';
            }
            if ($row['ssh2ip']==null) {
                $query2->execute(array($row['rootid'],$reseller_id));
                $ip=$query2->fetchColumn();
            } else {
                $ip=$row['ssh2ip'];
            }
            $defaultdns=($row['usedns']=='Y') ? $row['defaultdns'] : null;
            $installedslots=($row['installedslots']==null) ? 0 : $row['installedslots'];
            $uslots=($row['uslots']==null) ? 0 : $row['uslots'];
            $vs=array();
            $query3->execute(array($id,$reseller_id));
            foreach ($query3->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                if ($row2['active']=='N' or $row2['uptime']==1) $vsStatus=2;
                else if ($row2['active']=='Y' and $row2['uptime']<1) $vsStatus=3;
                else $vsStatus=1;
                $vs[]=array('id'=>$row2['id'],'address'=>$row2['address'],'name'=>$row2['queryName'],'status'=>$vsStatus);
            }
            $table[]=array('id'=>$id,'active'=>$row['active'],'img'=>$imgName,'alt'=>$imgAlt,'ip'=>$ip,'type'=>$type,'defaultdns'=>$defaultdns,'installedserver'=>$row['installedserver']."/".$row['maxserver'],'installedslots'=>$uslots."/".$installedslots."/".$row['maxslots'],'server'=>$vs);
        }
    }
    $next=$start+$amount;
    $vor=($colcount>$next) ? $start+$amount : $start;
    $back=$start-$amount;
    $zur=($back>=0) ? $start-$amount : $start;
    $pageamount=ceil($colcount/$amount);
    $pages[]='<a href="admin.php?w=vm&amp;d=md&amp;a=' . (!isset($amount)) ? 20 : $amount . ($start==0) ? '&p=0" class="bold">1</a>' : '&p=0">1</a>';
    $i=2;
    while ($i<=$pageamount) {
        $selectpage=($i-1)*$amount;
        $pages[]='<a href="admin.php?w=vm&amp;d=md&amp;a='.$amount.'&p='.$selectpage.'"' . ($start==$selectpage) ? 'class="bold"' : '' .' >'.$i.'</a>';
        $i++;
    }
    $pages=implode(', ',$pages);
    $template_file="admin_voicemasterserver_list.tpl";
}