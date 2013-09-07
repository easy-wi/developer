<?php
/**
 * File: userpanel_voice.php.
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

if ((!isset($user_id) or !$main == "1") or (isset($user_id) and !$pa['voiceserver'])) {
	header('Location: userpanel.php');
	die('No acces');
}
$sprache=getlanguagefile('voice',$user_language,$reseller_id);
$loguserid=$user_id;
$logusername=getusername($user_id);
$logusertype='user';
$logreseller=0;
if (isset($admin_id)) {
	$logsubuser=$admin_id;
} else if (isset($subuser_id)) {
	$logsubuser=$subuser_id;
} else {
	$logsubuser=0;
}
$aesfilecvar=getconfigcvars(EASYWIDIR."/stuff/keyphrasefile.php");
$aeskey=$aesfilecvar['aeskey'];
include(EASYWIDIR."/stuff/class_voice.php");
if ($ui->st('d','get')=='bu' and $ui->id('id',10,'get') and (!isset($_SESSION['sID']) or in_array($ui->id('id',10,'get'),$substituteAccess['vo']))) {
    $id=$ui->id('id',10,'get');
    $query=$sql->prepare("SELECT v.`id`,v.`ip`,v.`port`,v.`dns`,v.`localserverid`,m.`type`,m.`queryport`,AES_DECRYPT(m.`querypassword`,:aeskey) AS `decryptedquerypassword`,m.`rootid`,m.`addedby`,m.`ssh2ip`,m.`type`,m.`usedns`,m.`publickey`,m.`ssh2ip`,AES_DECRYPT(m.`ssh2port`,:aeskey) AS `decryptedssh2port`,AES_DECRYPT(m.`ssh2user`,:aeskey) AS `decryptedssh2user`,AES_DECRYPT(m.`ssh2password`,:aeskey) AS `decryptedssh2password`,m.`serverdir`,m.`keyname`,m.`notified` FROM `voice_server` v LEFT JOIN `voice_masterserver` m ON v.`masterserver`=m.`id` WHERE v.`active`='Y' AND m.`active`='Y' AND v.`backup`='Y' AND v.`id`=:server_id AND v.`userid`=:user_id AND v.`resellerid`=:reseller_id LIMIT 1");
    $query->execute(array(':aeskey'=>$aeskey,':server_id'=>$id,':user_id'=>$user_id,':reseller_id'=>$reseller_id));
    foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
        $dns=$row['dns'];
        if ($row['type']=='ts3') {
            $type=$sprache->ts3;
            $server=($row['usedns']=='N' or $dns==null or $dns=='') ? $row['ip'].':'.$row['port'] : $row['dns'].' ('.$row['ip'].':'.$row['port'].')';
        }
        $serverdir=$row['serverdir'];
        $addedby=$row['addedby'];
        $queryport=$row['queryport'];
        $querypassword=$row['decryptedquerypassword'];
        $volocalserverid=$row['localserverid'];
        $notified=$row['notified'];
        if ($addedby==2) {
            $queryip=$row['ssh2ip'];
            $publickey=$row['publickey'];
            $queryip=$row['ssh2ip'];
            $ssh2port=$row['decryptedssh2port'];
            $ssh2user=$row['decryptedssh2user'];
            $ssh2password=$row['decryptedssh2password'];
            $keyname=$row['keyname'];
        } else if ($addedby==1) {
            $query=$sql->prepare("SELECT `ip` FROM `rserverdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($row['rootid'],$reseller_id));
            $queryip=$query->fetchColumn();
        }
    }
    $query=$sql->prepare("SELECT `voice_maxbackup` FROM `settings` WHERE `resellerid`=? LIMIT 1");
    $query->execute(array($reseller_id));
    $voice_maxbackup=$query->fetchColumn();
    $query=$sql->prepare("SELECT COUNT(`id`) AS `amount` FROM `voice_server_backup` WHERE `sid`=? AND `uid`=? AND `resellerid`=?");
    $query->execute(array($id,$user_id,$reseller_id));
    $backupcount=$query->fetchColumn();
    if (isset($server) and !$ui->smallletters('action',2,'post') and !isset($server_port)) {
        $table=array();
        $query2=$sql->prepare("SELECT `id`,`name`,`date` FROM `voice_server_backup` WHERE `sid`=? AND `uid`=? AND `resellerid`=? ORDER BY `date` DESC");
        $query2->execute(array($id,$user_id,$reseller_id));
        foreach ($query2->fetchall(PDO::FETCH_ASSOC) as $row2) {
            $table[]=array('id'=>$row2['id'],'date'=>$row2['date'],'name'=>$row2['name']);
        }
        $template_file="userpanel_voiceserver_backup_list.tpl";
    } else if (isset($server) and !$ui->smallletters('action',2,'post') and isset($server_port) and $server_port=='1') {
        $template_file="userpanel_voiceserver_backup_new.tpl";
    } else if (isset($server) and $ui->smallletters('action',2,'post')=='nb') {
        $name=($ui->names('name',50,'post')) ? $ui->names('name',50,'post') : 'New Backup';
        $toomuch=$backupcount+1-$voice_maxbackup;
        if ($toomuch>0) {
            $query=$sql->prepare("SELECT `id` FROM `voice_server_backup` WHERE `sid`=? AND `uid`=? AND `resellerid`=? ORDER BY `id` ASC LIMIT $toomuch");
            $query->execute(array($id,$user_id,$reseller_id));
            foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
                $delete=$sql->prepare("DELETE FROM `voice_server_backup` WHERE `id`=? AND `uid`=? AND `resellerid`=? LIMIT 1");
                $delete->execute(array($row['id'],$user_id,$reseller_id));
                tsbackup('delete',$queryip,$ssh2port,$ssh2user,$publickey,$keyname,$ssh2password,$notified,$serverdir,$volocalserverid,$row['id'],$reseller_id,$sql);
            }
        }
        $connection=new TS3($queryip,$queryport,'serveradmin',$querypassword);
        $errorcode=$connection->errorcode;
        if (strpos($errorcode,'error id=0') === false) {
            $template_file=$spracheResponse->error_ts_query_connect.$errorcode;
        } else {
            $connection->StartServer($volocalserverid);
            $rawsnapshot=$connection->Snapshotcreate($volocalserverid);
            $channelSnapshot=$connection->channelList($volocalserverid);
            if (is_array($rawsnapshot) and isset($rawsnapshot['0']['msg'])) {
                $template_file=$spracheResponse->error_ts_query.$rawsnapshot['0']['msg'];
            } else if (is_array($rawsnapshot) and !isset($rawsnapshot['0']['msg'])) {
                $template_file='Unknown error';
            } else {
                $snapshot=gzcompress($rawsnapshot,9);
                $query=$sql->prepare("INSERT INTO `voice_server_backup` (`sid`,`uid`,`name`,`snapshot`,`channels`,`date`,`resellerid`) VALUES(?,?,?,?,?,NOW(),?)");
                $query->execute(array($id,$user_id,$name,$snapshot,$channelSnapshot,$reseller_id));
                $query=$sql->prepare("SELECT `id` FROM `voice_server_backup` WHERE `sid`=? AND `uid`=? AND `resellerid`=? ORDER BY `id` DESC LIMIT 1");
                $query->execute(array($id,$user_id,$reseller_id));
                $return=tsbackup('create',$queryip,$ssh2port,$ssh2user,$publickey,$keyname,$ssh2password,$notified,$serverdir,$volocalserverid,$sql->lastInsertId(),$reseller_id,$sql);
                $template_file=($return=='ok') ? $spracheResponse->ts_query_success.$return : $spracheResponse->error_ts_query.$return;
                foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row);
                $query=$sql->prepare("SELECT CONCAT(`ip`,':',`port`) AS `address` FROM `voice_server` WHERE `id`=? AND `userid`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($id,$user_id,$reseller_id));
                $address=$query->fetchColumn();
                $loguseraction="%add% %voserver% %backup% ${name} ${address}";
                $insertlog->execute();
            }
        }
        $connection->CloseConnection();
    } else if (isset($server) and $ui->smallletters('action',2,'post')=='md' and isset($ui->post['delete']) and $ui->id('id',10,'post')!=false) {
        $query=$sql->prepare("SELECT `name`,`sid` FROM `voice_server_backup` WHERE `id`=? AND `uid`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($ui->id('id',10,'post'),$user_id,$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $name=$row['name'];
            $sid=$row['sid'];
        }
        $query=$sql->prepare("DELETE FROM `voice_server_backup` WHERE `id`=? AND `uid`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($ui->id('id',10,'post'),$user_id,$reseller_id));
        tsbackup('delete',$queryip,$ssh2port,$ssh2user,$publickey,$keyname,$ssh2password,$notified,$serverdir,$volocalserverid,$ui->id('id',10,'post'),$reseller_id,$sql);
        $template_file=$spracheResponse->table_del;
        $query=$sql->prepare("SELECT CONCAT(`ip`,':',`port`) AS `address` FROM `voice_server` WHERE `id`=? AND `userid`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($sid,$user_id,$reseller_id));
        $address=$query->fetchColumn();
        $loguseraction="%del% %voserver% %backup% ${name} ${address}";
        $insertlog->execute();
    } else if (isset($server) and $ui->smallletters('action',2,'post')=='md' and isset($ui->post['use']) and $ui->id('id',10,'post')!=false) {
        $query=$sql->prepare("SELECT `snapshot`,`name`,`sid`,`channels` FROM `voice_server_backup` WHERE `id`=? AND `uid`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($ui->id('id',10,'post'),$user_id,$reseller_id));
        foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
            $snapshot= @gzuncompress($row['snapshot']);
            $connection=new TS3($queryip,$queryport,'serveradmin',$querypassword);
            $errorcode=$connection->errorcode;
            if (strpos($errorcode,'error id=0') === false) {
                $template_file=$spracheResponse->error_ts_query_connect.$errorcode;
            } else {
                $connection->StartServer($volocalserverid);
                $reply=$connection->Snapshotdeploy($volocalserverid,$snapshot);
                if (isset($reply['0']['id']) and $reply['0']['id']=='0') {
                    $move=array();
                    $channelListOld=@json_decode($row['channels']);
                    $channelListDeployed=@json_decode($connection->channelList($volocalserverid));
                    if (is_object($channelListDeployed) and is_object($channelListOld)) foreach ($channelListOld as $k=>$v) if (isset($channelListDeployed->$k)) $move[$v]=$channelListDeployed->$k;
                    tsbackup('deploy',$queryip,$ssh2port,$ssh2user,$publickey,$keyname,$ssh2password,$notified,$serverdir,$volocalserverid,$ui->id('id',10,'post'),$reseller_id,$sql,$move);
                    $query=$sql->prepare("SELECT CONCAT(`ip`,':',`port`) AS `address` FROM `voice_server` WHERE `id`=? AND `userid`=? AND `resellerid`=? LIMIT 1");
                    $query->execute(array($row['sid'],$user_id,$reseller_id));
                    $address=$query->fetchColumn();
                    $loguseraction="%use% %voserver% %backup% ${row['name']} ${address}";
                    $insertlog->execute();
                }
                $template_file=$spracheResponse->ts_query_success.$reply['0']['msg'];
            }
            $connection->CloseConnection();
        }
    } else {
        $template_file=$spracheResponse->token;
    }
} else if ($ui->st('d','get')=='pk' and $ui->id('id',10,'get') and (!isset($_SESSION['sID']) or in_array($ui->id('id',10,'get'),$substituteAccess['vo']))) {
    $id=$ui->id('id',10,'get');
    $query=$sql->prepare("SELECT `masterserver`,`localserverid`,CONCAT(`ip`,':',`port`) AS `address` FROM `voice_server` WHERE `id`=? AND `userid`=? AND `resellerid`=? LIMIT 1");
    $query->execute(array($id,$user_id,$reseller_id));
    foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
        $masterserver=$row['masterserver'];
        $localserverid=$row['localserverid'];
        $address=$row['address'];
    }
    if (isset($masterserver)) {
        $query=$sql->prepare("SELECT *,AES_DECRYPT(`querypassword`,:aeskey) AS `decryptedquerypassword`,AES_DECRYPT(`ssh2port`,:aeskey) AS `decryptedssh2port`,AES_DECRYPT(`ssh2user`,:aeskey) AS `decryptedssh2user`,AES_DECRYPT(`ssh2password`,:aeskey) AS `decryptedssh2password` FROM `voice_masterserver` WHERE `id`=:id AND (`resellerid`=:reseller_id OR (`managedServer`='Y' AND `managedForID`=:reseller_id)) LIMIT 1");
        $query->execute(array(':aeskey'=>$aeskey,':id'=>$masterserver,':reseller_id'=>$reseller_id));
        foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
            $masteractive=$row['active'];
            $addedby=$row['addedby'];
            $queryport=$row['queryport'];
            $querypassword=$row['decryptedquerypassword'];
            if ($addedby=='2') {
                $queryip=$row['ssh2ip'];
            } else if ($addedby=='1') {
                $pselect3=$sql->prepare("SELECT `ip`,`bitversion` FROM `rserverdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $pselect3->execute(array($row['rootid'],$reseller_id));
                foreach ($pselect3->fetchall(PDO::FETCH_ASSOC) as $row3) {
                    $queryip=$row3['ip'];
                }
            }
        }
        if (isset($masteractive) and $masteractive=='Y') {
            $connection=new TS3($queryip,$queryport,'serveradmin',$querypassword);
            $errorcode=$connection->errorcode;
            if (strpos($errorcode,'error id=0') === false) {
                $template_file=$spracheResponse->error_ts_query_connect.$errorcode;
            } else {
                if (isset($server_port) and $server_port=='1') {
                    $servergroups=array();
                    foreach($connection->ServerGroups($localserverid) as $servergroup) {
                        if ($servergroup['type']=='1') {
                            $servergroups[$servergroup['id']]=$servergroup['name'];
                        }
                    }
                    $template_file="userpanel_voiceserver_key_add.tpl";
                } else if (!$ui->smallletters('action',2,'post') and !isset($server_port)) {
                    $pklist=$connection->KeyList($localserverid);
                    $template_file=(is_array($pklist)) ? "userpanel_voiceserver_key_list.tpl" : $spracheResponse->ts_query_success.$pklist;
                } else if ($ui->smallletters('action',2,'post')=='ad') {
                    if ($ui->id('group',255,'post')) {
                        $newkey=$connection->AddKey($localserverid,$ui->id('group',255,'post'));
                        $loguseraction="%add% %voserver% Token ${address}";
                        $insertlog->execute();
                        $template_file=$spracheResponse->ts_query_success.$newkey['0']['token'];
                    }
                } else if ($ui->smallletters('action',2,'post')=='dl') {
                    $loguseraction="%del% %voserver% Token ${address}";
                    $insertlog->execute();
                    $del=$connection->DelKey($localserverid,$ui->post['token']);
                    $template_file=$spracheResponse->ts_query_success.$del['0']['msg'];
                }
            }
            $connection->CloseConnection();
        } else {
            $template_file='userpanel_404.tpl';
        }
    } else {
        $template_file='userpanel_404.tpl';
    }
} else if ($ui->st('d','get')=='rs' and $ui->id('id',10,'get') and $ui->smallletters('action',2,'post')=='rs' and (!isset($_SESSION['sID']) or in_array($ui->id('id',10,'get'),$substituteAccess['vo']))) {
    $id=$ui->id('id',10,'get');
    $query=$sql->prepare("SELECT v.*,m.`type`,m.`queryport`,AES_DECRYPT(m.`querypassword`,?) AS `decryptedquerypassword`,m.`rootid`,m.`addedby`,m.`ssh2ip`,m.`defaultname`,m.`defaultwelcome`,m.`defaulthostbanner_url`,m.`defaulthostbanner_gfx_url`,m.`defaulthostbutton_tooltip`,m.`defaulthostbutton_url`,m.`defaulthostbutton_gfx_url`,m.`usedns` FROM `voice_server` v LEFT JOIN `voice_masterserver` m ON v.`masterserver`=m.`id` WHERE v.`active`='Y' AND m.`active`='Y' AND v.`id`=? AND v.`userid`=? AND v.`resellerid`=? LIMIT 1");
    $query->execute(array($aeskey,$id,$user_id,$reseller_id));
    foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
        $addedby=$row['addedby'];
        $queryport=$row['queryport'];
        $querypassword=$row['decryptedquerypassword'];
        $usedns=$row['usedns'];
        $name=$row['defaultname'];
        $welcome=$row['defaultwelcome'];
        $banner_url=$row['defaulthostbanner_url'];
        $banner_gfx=$row['defaulthostbanner_gfx_url'];
        $tooltip=$row['defaulthostbutton_tooltip'];
        $button_url=$row['defaulthostbutton_url'];
        $button_gfx=$row['defaulthostbutton_gfx_url'];
        $slots=$row['slots'];
        $voip=$row['ip'];
        $voport=$row['port'];
        $password=$row['initialpassword'];
        $max_download_total_bandwidth=$row['max_download_total_bandwidth'];
        $max_upload_total_bandwidth=$row['max_upload_total_bandwidth'];
        $volocalserverid=$row['localserverid'];
        $forcebanner=$row['forcebanner'];
        $forcebutton=$row['forcebutton'];
        $forcewelcome=$row['forcewelcome'];
        if ($addedby=='2') {
            $queryip=$row['ssh2ip'];
        } else if ($addedby=='1') {
            $query=$sql->prepare("SELECT `ip` FROM `rserverdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($vrow['rootid'],$reseller_id));
            foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
                $queryip=$row['ip'];
            }
        }
    }
    if (isset($queryip,$queryport,$querypassword)) {
        $connection=new TS3($queryip,$queryport,'serveradmin',$querypassword);
        $errorcode=$connection->errorcode;
        if (strpos($errorcode,'error id=0') === false) {
            $template_file=$spracheResponse->error_ts_query_connect.$errorcode;
        } else {
            $connection->StartServer($volocalserverid);
            $connection->ModServer($volocalserverid,$slots,$voip,$voport,$password,$name,$welcome,$max_download_total_bandwidth,$max_upload_total_bandwidth,$banner_url,$banner_gfx,$button_url,$button_gfx,$tooltip);
            $reply=$connection->PermReset($volocalserverid);
            $loguseraction="%reinstall% %voserver% ${voip}:${voport}";
            $insertlog->execute();
            if (isset($reply['0']['token'])) {
                $template_file=$spracheResponse->ts_query_success.$reply['0']['token'];
                if ($forcebanner=='Y') {
                    $removelist[]='b_virtualserver_modify_hostbanner';
                    $removelist[]='i_needed_modify_power_virtualserver_modify_hostbanner';
                } else if ($forcebanner=='N') {
                    $addlist[]='b_virtualserver_modify_hostbanner';
                    $addlist[]='i_needed_modify_power_virtualserver_modify_hostbanner';
                }
                if ($forcebutton=='Y') {
                    $removelist[]='b_virtualserver_modify_hostbutton';
                    $removelist[]='i_needed_modify_power_virtualserver_modify_hostbutton';
                } else if ($forcebutton=='N') {
                    $addlist[]='b_virtualserver_modify_hostbutton';
                    $addlist[]='i_needed_modify_power_virtualserver_modify_hostbutton';
                }
                if ($forcewelcome=='Y') {
                    $removelist[]='b_virtualserver_modify_welcomemessage';
                    $removelist[]='i_needed_modify_power_virtualserver_modify_welcomemessage';
                } else if ($forcewelcome=='N') {
                    $addlist[]='b_virtualserver_modify_welcomemessage';
                    $addlist[]='i_needed_modify_power_virtualserver_modify_welcomemessage';
                }
                if (isset($addlist)) {
                    $connection->AdminPermissions ($volocalserverid,'add',$addlist);
                }
                if (isset($removelist)) {
                    $connection->AdminPermissions ($volocalserverid,'del',$removelist);
                }
            } else {
                $template_file=$spracheResponse->error_ts_query.$connection->errorcode;
            }
            $connection->CloseConnection();
        }
    } else {
        $template_file='userpanel_404.tpl';
    }
} else if ($ui->st('d','get')=='md' and $ui->id('id',10,'get') and (!isset($_SESSION['sID']) or in_array($ui->id('id',10,'get'),$substituteAccess['vo']))) {
    $id=$ui->id('id',10,'get');
    if (!$ui->smallletters('action',2,'post')) {
        $query=$sql->prepare("SELECT * FROM `voice_server` WHERE `id`=? AND `userid`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id,$user_id,$reseller_id));
        foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
            $masterserver=$row['masterserver'];
            $ip=$row['ip'];
            $port=$row['port'];
            $initialpassword=$row['initialpassword'];
            $localserverid=$row['localserverid'];
            $forcebanner=$row['forcebanner'];
            $forcebutton=$row['forcebutton'];
            $forceservertag=$row['forceservertag'];
            $forcewelcome=$row['forcewelcome'];
            $dns=$row['dns'];
            $active=$row['active'];
            $password=$row['password'];
            if ($active=='Y') {
                $query2=$sql->prepare("SELECT *,AES_DECRYPT(`querypassword`,?) AS `decryptedquerypassword`  FROM `voice_masterserver` WHERE `id`=? AND (`resellerid`=? OR (`managedServer`='Y' AND `managedForID`=?)) LIMIT 1");
                $query2->execute(array($aeskey,$row['masterserver'],$reseller_id,$reseller_id));
                foreach ($query2->fetchall(PDO::FETCH_ASSOC) as $row2) {
                    $resellerToUse=$row2['resellerid'];
                    $masteractive=$row2['active'];
                    $usedns=$row2['usedns'];
                    $defaultdns=strtolower($id.'.'.$row2['defaultdns']);
                    $queryport=$row2['queryport'];
                    $querypassword=$row2['decryptedquerypassword'];
                    $addedby=$row2['addedby'];
                    $externalDefaultDNS=$row2['externalDefaultDNS'];
                    $tsdnsServerID=$row2['tsdnsServerID'];
                    if ($addedby=='2') {
                        $queryip=$row2['ssh2ip'];
                    } else if ($addedby=='1') {
                        $query3=$sql->prepare("SELECT `ip`,`altips` FROM `rserverdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                        $query3->execute(array($row2['rootid'],$resellerToUse));
                        foreach ($query3->fetchall(PDO::FETCH_ASSOC) as $row3) {
                            $queryip=$row3['ip'];
                        }
                    }
                }
                if (isset($tsdnsServerID) and isid($tsdnsServerID,10)) {
                    $query2=$sql->prepare("SELECT `defaultdns` FROM `voice_tsdns` WHERE `active`='Y' AND `id`=? AND `resellerid`=? LIMIT 1");
                    $query2->execute(array($tsdnsServerID,$resellerToUse));
                    foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                        if ($externalDefaultDNS=='Y') {
                            $defaultdns=strtolower($id.'.'.$row2['defaultdns']);
                        }
                    }
                }
            }
        }
        if ($masteractive=='Y' and $active=='Y') {
            $connection=new TS3($queryip,$queryport,'serveradmin',$querypassword);
            $errorcode=$connection->errorcode;
            if (strpos($errorcode,'error id=0') === false) {
                $template_file=$spracheResponse->error_ts_query_connect.$errorcode."<br />";
            } else {
                $serverdetails=$connection->ServerDetails($localserverid);
                $name=$serverdetails['virtualserver_name'];
                $welcome=$serverdetails['virtualserver_welcomemessage'];
                $hostbanner_url=$serverdetails['virtualserver_hostbanner_url'];
                $hostbanner_gfx_url=$serverdetails['virtualserver_hostbanner_gfx_url'];
                $hostbutton_tooltip=$serverdetails['virtualserver_hostbutton_tooltip'];
                $hostbutton_url=$serverdetails['virtualserver_hostbutton_url'];
                $hostbutton_gfx_url=$serverdetails['virtualserver_hostbutton_gfx_url'];

                # Ticket https://github.com/easy-wi/developer/issues/13 "Bearbeiten von TS3 Servern im Usermodul erweitern"
                $virtualserver_antiflood_points_needed_command_block=$serverdetails['virtualserver_antiflood_points_needed_command_block'];
                $virtualserver_antiflood_points_needed_ip_block=$serverdetails['virtualserver_antiflood_points_needed_ip_block'];
                $virtualserver_antiflood_points_tick_reduce=$serverdetails['virtualserver_antiflood_points_tick_reduce'];
                $virtualserver_hostbanner_gfx_interval=$serverdetails['virtualserver_hostbanner_gfx_interval'];
                $virtualserver_hostmessage_mode=$serverdetails['virtualserver_hostmessage_mode'];
                $virtualserver_needed_identity_security_level=$serverdetails['virtualserver_needed_identity_security_level'];
                $virtualserver_reserved_slots=$serverdetails['virtualserver_reserved_slots'];
            }
            $connection->CloseConnection();
            $template_file="userpanel_voiceserver_md.tpl";
        } else {
            $template_file='userpanel_404.tpl';
        }
    } else if ($ui->smallletters('action',2,'post')=='md' and token(true)){
        $query=$sql->prepare("SELECT `active`,`ip`,`port`,`slots`,`dns`,`masterserver`,`localserverid`,`password`,`forceservertag`,`forcebanner`,`forcebutton`,`forcewelcome`,`max_download_total_bandwidth`,`max_upload_total_bandwidth` FROM `voice_server` WHERE `id`=? AND `userid`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($id,$user_id,$reseller_id));
        foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
            $active=$row['active'];
            $ip=$row['ip'];
            $port=$row['port'];
            $oldip=$ip;
            $oldport=$port;
            $slots=$row['slots'];
            $olddns=$row['dns'];
            $forceservertag=$row['forceservertag'];
            $forcebanner=$row['forcebanner'];
            $forcebutton=$row['forcebutton'];
            $forcewelcome=$row['forcewelcome'];
            $masterserver=$row['masterserver'];
            $localserverid=$row['localserverid'];
            $max_download_total_bandwidth=$row['max_download_total_bandwidth'];
            $max_upload_total_bandwidth=$row['max_upload_total_bandwidth'];
        }
        if ($active=='Y') {
            $query=$sql->prepare("SELECT *,AES_DECRYPT(`querypassword`,:aeskey) AS `decryptedquerypassword`,AES_DECRYPT(`ssh2port`,:aeskey) AS `decryptedssh2port`,AES_DECRYPT(`ssh2user`,:aeskey) AS `decryptedssh2user`,AES_DECRYPT(`ssh2password`,:aeskey) AS `decryptedssh2password` FROM `voice_masterserver` WHERE `id`=:id AND (`resellerid`=:reseller_id OR (`managedServer`='Y' AND `managedForID`=:reseller_id)) LIMIT 1");
            $query->execute(array(':aeskey'=>$aeskey,':id'=>$masterserver,':reseller_id'=>$reseller_id));
            foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
                $resellerToUse=$row['resellerid'];
                $masteractive=$row['active'];
                $serverdir=$row['serverdir'];
                $addedby=$row['addedby'];
                $usedns=$row['usedns'];
                $masterDNS=$row['defaultdns'];
                $queryport=$row['queryport'];
                $querypassword=$row['decryptedquerypassword'];
                $mnotified=$row['notified'];
                $tsdnsServerID=$row['tsdnsServerID'];
                $externalDefaultDNS=$row['externalDefaultDNS'];
                if ($addedby=='2') {
                    $publickey=$row['publickey'];
                    $queryip=$row['ssh2ip'];
                    $ssh2port=$row['decryptedssh2port'];
                    $ssh2user=$row['decryptedssh2user'];
                    $ssh2password=$row['decryptedssh2password'];
                    $keyname=$row['keyname'];
                    $bitversion=$row['bitversion'];
                } else if ($addedby=='1') {
                    $query2=$sql->prepare("SELECT `ip`,`bitversion` FROM `rserverdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
                    $query2->execute(array($row['rootid'],$resellerToUse));
                    foreach ($query2->fetchall(PDO::FETCH_ASSOC) as $row2) {
                        $queryip=$row2['ip'];
                        $bitversion=$row2['bitversion'];
                    }
                }
            }
            $fail=0;
            $error="Error:<br />";
            if ($masteractive=='N') $fail=1;
            $dns=strtolower($ui->domain('dns','post'));
            $dnsCheck=checkDNS($dns,$id,$user_id,$type='server');
            if ($fail!=1 and $usedns=='Y' and $dns!=$olddns and $dns!='' and $dnsCheck!==false) {
                if (isset($tsdnsServerID) and isid($tsdnsServerID,10) and isset($resellerToUse)) {
                    $query=$sql->prepare("SELECT *,AES_DECRYPT(`ssh2port`,:aeskey) AS `decryptedssh2port`,AES_DECRYPT(`ssh2user`,:aeskey) AS `decryptedssh2user`,AES_DECRYPT(`ssh2password`,:aeskey) AS `decryptedssh2password` FROM `voice_tsdns` WHERE `active`='Y' AND `id`=:id AND `resellerid`=:reseller_id LIMIT 1");
                    $query->execute(array(':aeskey'=>$aeskey,':id'=>$tsdnsServerID,':reseller_id'=>$resellerToUse));
                    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                        $publickey=$row['publickey'];
                        $ip=$row['ssh2ip'];
                        $port=$row['decryptedssh2port'];
                        $user=$row['decryptedssh2user'];
                        $pass=$row['decryptedssh2password'];
                        $serverdir=$row['serverdir'];
                        $keyname=$row['keyname'];
                        $bit=$row['bitversion'];
                        $slots=$row['slots'];
                    }
                }
                $return=tsdns('md',$queryip,$ssh2port,$ssh2user,$publickey,$keyname,$ssh2password,$mnotified,$serverdir,$bitversion,array($ip,$oldip),array($port,$oldport),array($dns,$olddns),$reseller_id,$sql);
                $template_file=($return=='ok') ? $spracheResponse->ts_query_success.$return : $spracheResponse->error_ts_query.$return;
            } else if ($usedns=='Y' and $dns!=$olddns and $dns!='' and $dnsCheck===false) {
                $error .=" DNS is not allowed<br />";
                $fail=1;
            }
            if ($fail!=1) {
                $initialpassword=$ui->password('initialpassword',50,'post');
                $name=$ui->post['name'];
                $connection=new TS3($queryip,$queryport,'serveradmin',$querypassword);
                $errorcode=$connection->errorcode;
                if (strpos($errorcode,'error id=0') === false) {
                    $template_file=$spracheResponse->error_ts_query_connect.$errorcode;
                } else {
                    $serverdetails=$connection->ServerDetails($localserverid);
                    if ($forceservertag=="Y" and isset($brandname) and $brandname!="" and strpos(strtolower($name), strtolower($brandname)) === false) $name=$serverdetails['virtualserver_name'];
                    if ($forcebanner=='Y') {
                        $banner_url=$serverdetails['virtualserver_hostbanner_url'];
                        $banner_gfx=$serverdetails['virtualserver_hostbanner_gfx_url'];
                    } else {
                        $banner_url=$ui->url('hostbanner_url','post');
                        $banner_gfx=$ui->url('hostbanner_gfx_url','post');
                    }
                    if ($forcebutton=='Y') {
                        $tooltip=$serverdetails['virtualserver_hostbutton_tooltip'];
                        $button_url=$serverdetails['virtualserver_hostbutton_url'];
                        $button_gfx=$serverdetails['virtualserver_hostbutton_gfx_url'];
                    } else {
                        $tooltip=$ui->description('hostbutton_tooltip','post');
                        $button_url=$ui->url('hostbutton_url','post');
                        $button_gfx=$ui->url('hostbutton_gfx_url','post');
                    }
                    $welcome=(isset($forcewelcome) and $forcewelcome=='Y') ? $serverdetails['virtualserver_welcomemessage'] : $ui->description('welcome','post');

                    # Ticket https://github.com/easy-wi/developer/issues/13 "Bearbeiten von TS3 Servern im Usermodul erweitern"
                    $virtualserver_antiflood_points_needed_command_block=$ui->id('virtualserver_antiflood_points_needed_command_block',255,'post');
                    $virtualserver_antiflood_points_needed_ip_block=$ui->id('virtualserver_antiflood_points_needed_ip_block',255,'post');
                    $virtualserver_antiflood_points_tick_reduce=$ui->id('virtualserver_antiflood_points_tick_reduce',255,'post');
                    $virtualserver_hostbanner_gfx_interval=$ui->id('virtualserver_hostbanner_gfx_interval',255,'post');
                    $virtualserver_hostmessage_mode=$ui->id('virtualserver_hostmessage_mode',1,'post');
                    $virtualserver_needed_identity_security_level=$ui->id('virtualserver_needed_identity_security_level',255,'post');
                    $virtualserver_reserved_slots=($ui->id('virtualserver_reserved_slots',4,'post') and $ui->id('virtualserver_reserved_slots',4,'post')<$slots) ? $ui->id('virtualserver_reserved_slots',4,'post') : 0;

                    $mod=$connection->ModServer($localserverid,$slots,$ip,$port,$initialpassword,$name,$welcome,$max_download_total_bandwidth,$max_upload_total_bandwidth,$banner_url,$banner_gfx,$button_url,$button_gfx,$tooltip,$virtualserver_reserved_slots,$virtualserver_needed_identity_security_level,$virtualserver_hostmessage_mode,$virtualserver_hostbanner_gfx_interval,$virtualserver_antiflood_points_tick_reduce,$virtualserver_antiflood_points_needed_command_block,$virtualserver_antiflood_points_needed_ip_block);
                    $template_file=$spracheResponse->table_add.'<br />'.$spracheResponse->ts_query_success.$mod['0']['msg'];
                }
                $connection->CloseConnection();
                $query=$sql->prepare("UPDATE `voice_server` SET `dns`=?,`initialpassword`=? WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($dns,$initialpassword,$id,$reseller_id));
                $loguseraction="%mod% %voserver% ${ip}:${port}";
                $insertlog->execute();
            } else {
                $template_file=$error;
            }
        } else {
            $template_file='userpanel_404.tpl';
        }
    } else {
        $template_file=$spracheResponse->token;
    }
} else if ($ui->st('d','get')=='st' and $ui->id('id',10,'get') and $ui->smallletters('action',2,'post') and (!isset($_SESSION['sID']) or in_array($ui->id('id',10,'get'),$substituteAccess['vo']))) {
    $id=$ui->id('id',10,'get');
    $query=$sql->prepare("SELECT v.`ip`,v.`port`,v.`localserverid`,m.`type`,m.`queryport`,AES_DECRYPT(m.`querypassword`,?) AS `decryptedquerypassword`,m.`rootid`,m.`addedby`,m.`ssh2ip` FROM `voice_server` v LEFT JOIN `voice_masterserver` m ON v.`masterserver`=m.`id` WHERE v.`active`='Y' AND m.`active`='Y' AND v.`id`=? AND v.`userid`=? AND v.`resellerid`=? LIMIT 1");
    $query->execute(array($aeskey,$id,$user_id,$reseller_id));
    foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
        $addedby=$row['addedby'];
        $queryport=$row['queryport'];
        $querypassword=$row['decryptedquerypassword'];
        $volocalserverid=$row['localserverid'];
        if ($addedby=='2') {
            $queryip=$row['ssh2ip'];
        } else if ($addedby=='1') {
            $query=$sql->prepare("SELECT `ip` FROM `rserverdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($row['rootid'],$reseller_id));
            $queryip=$query->fetchColumn();
        }
    }
    if (isset($queryip)) {
        $connection=new TS3($queryip,$queryport,'serveradmin',$querypassword);
        $errorcode=$connection->errorcode;
        if (strpos($errorcode,'error id=0') === false) {
            $template_file=$spracheResponse->error_ts_query_connect.$errorcode;
        } else {
            if ($ui->smallletters('action',2,'post')=='re') {
                $uptime=2;
                $reply=$connection->StopServer($volocalserverid);
                $reply=$connection->StartServer($volocalserverid);
                $loguseraction="%start% %voserver% ${row['ip']}:${row['port']}";
                $insertlog->execute();
            } else if ($ui->smallletters('action',2,'post')=='so') {
                $uptime=1;
                $reply=$connection->StopServer($volocalserverid);
                $loguseraction="%stop% %voserver% ${row['ip']}:${row['port']}";
                $insertlog->execute();
            }
            if (isset($reply)) {
                $query=$sql->prepare("UPDATE `voice_server` SET `uptime`=? WHERE `id`=? AND `resellerid`=? LIMIT 1");
                $query->execute(array($uptime,$id,$reseller_id));
                $template_file=$spracheResponse->ts_query_success.$reply['0']['msg'];
            } else {
                $template_file='Unknown Error';
            }
        }
    } else {
        $template_file='userpanel_404.tpl';
    }
} else {
    $o=$ui->st('o','get');
    if ($ui->st('o','get')=='aa') {
        $orderby='v.`ip` ASC, v.`port` ASC';
    } else if ($ui->st('o','get')=='da') {
        $orderby='v.`ip` DESC, v.`port` DESC';
    } else if ($ui->st('o','get')=='du') {
        $orderby='v.`usedslots` DESC';
    } else if ($ui->st('o','get')=='au') {
        $orderby='v.`usedslots` ASC';
    } else if ($ui->st('o','get')=='dt') {
        $orderby='v.`filetraffic` DESC';
    } else if ($ui->st('o','get')=='at') {
        $orderby='v.`filetraffic` ASC';
    } else if ($ui->st('o','get')=='dr') {
        $orderby='v.`uptime` DESC';
    } else if ($ui->st('o','get')=='ar') {
        $orderby='v.`uptime` ASC';
    } else if ($ui->st('o','get')=='ds') {
        $orderby='v.`uptime` DESC';
    } else if ($ui->st('o','get')=='as') {
        $orderby='v.`uptime` ASC';
    } else if ($ui->st('o','get')=='di') {
        $orderby='v.`id` DESC';
    } else{
        $o='ai';
        $orderby='v.`id` ASC';
    }
    $table=array();
    $query=$sql->prepare("SELECT v.*,m.`type`,m.`usedns` FROM `voice_server` v INNER JOIN `voice_masterserver` m ON v.`masterserver`=m.`id` WHERE v.`active`='Y' AND m.`active`='Y' AND v.`userid`=? AND v.`resellerid`=? ORDER BY $orderby");
    $query->execute(array($user_id,$reseller_id));
    foreach ($query->fetchall(PDO::FETCH_ASSOC) as $row) {
        if (!isset($_SESSION['sID']) or in_array($row['id'],$substituteAccess['vo'])) {
            $dns=$row['dns'];
            if ($row['type']=='ts3') {
                $type=$sprache->ts3;
                if ($row['initialpassword']!='' and $row['initialpassword']!=null) {
                    $password='?password='.$row['initialpassword'];
                } else {
                    $password='';
                }
                $usedSlots=$row['usedslots'];
                if ($row['usedslots']==null) {
                    $usedSlots=0;
                }
                $flexSlots='';
                if ($row['flexSlots']=='Y' and $row['flexSlotsCurrent']==null) {
                    $flexSlots=$row['slots'].'/';
                } else if ($row['flexSlots']=='Y') {
                    $flexSlots=$row['flexSlotsCurrent'].'/';
                }
                $usage=$usedSlots.'/'.$flexSlots.$row['slots'];
                if ($row['uptime']==0) {
                    $imgName='16_error';
                    $imgAlt='error';
                    $stopped='C';
                } else if ($row['uptime']>1) {
                    $imgName='16_ok';
                    $imgAlt='online';
                    $stopped='N';
                } else {
                    $imgName='16_bad';
                    $imgAlt='offline';
                    $stopped='Y';
                }
                $days=floor($row['uptime']/86400);
                $hours=floor(($row['uptime']-($days*86400))/3600);
                $minutes=floor(($row['uptime']-($days*86400)-($hours*3600))/60);
                $uptime=$days.'D/'.$hours.'H/'.$minutes.'M';
                if ($row['usedns']=='N' or $dns==null or $dns=='') {
                    $server='<a href="ts3server://'.$row['ip'].':'.$row['port'].$password.'">'.$row['ip'].':'.$row['port'].'</a>';
                } else {
                    $server='<a href="ts3server://'.$row['dns'].$password.'">'.$row['dns'].' ('.$row['ip'].':'.$row['port'].')</a>';
                }
                $address=$row['ip'].':'.$row['port'];
                $filetraffic=round(($row['filetraffic']/1024),2);
                if ($row['maxtraffic']>=0) {
                    $maxtraffic=round($row['maxtraffic']);
                } else {
                    $maxtraffic=$row['maxtraffic'];
                }
                $initalize[]=$row['id'].'-start';
                $initalize[]=$row['id'].'-stop';
                $initalize[]=$row['id'].'-reinstall';
                $initalize[]=$row['id'].'-backup';
                $initalize[]=$row['id'].'-token';
                $initalize[]=$row['id'].'-settings';
            }
            $table[]=array('id'=>$row['id'],'virtual_id'=>$row['localserverid'],'backup'=>$row['backup'],'filetraffic'=>$filetraffic,'maxtraffic'=>$maxtraffic,'server'=>$server,'address'=>$address,'usage'=>$usage,'uptime'=>$uptime,'stopped'=>$stopped,'img'=>$imgName,'alt'=>$imgAlt,'type'=>$type);
        }
    }
    $template_file="userpanel_voiceserver_list.tpl";
}