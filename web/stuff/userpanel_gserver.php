<?php
/**
 * File: userpanel_gserver.php.
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
if ((!isset($main) or $main!=1) or (!isset($user_id) or (isset($user_id) and !$pa['restart']))) {
    header('Location: userpanel.php');
    die('No acces');
}
$sprache=getlanguagefile('gserver',$user_language,$reseller_id,$sql);
$loguserid=$user_id;
$logusername=getusername($user_id,$sql);
$logusertype="user";
$logreseller=0;
if (isset($admin_id)) {
	$logsubuser=$admin_id;
} else if (isset($subuser_id)) {
	$logsubuser=$subuser_id;
} else {
	$logsubuser=0;
}

if (isset($admin_id) and $reseller_id!=0) {
    $reseller_id=$admin_id;
}
$aesfilecvar=getconfigcvars(EASYWIDIR."/stuff/keyphrasefile.php");
$aeskey=$aesfilecvar['aeskey'];
include(EASYWIDIR.'/stuff/ssh_exec.php');
if ($ui->w('action',4,'post') and !token(true)) {
    $template_file=$spracheResponse->token;
} else if ($ui->st('d','get')=='ri' and !$ui->id('id',10,'get')) {
    $template_file=$sprache->error_id;
} else if ($ui->st('d','get')=='ri' and $ui->id('id',10,'get') and (!isset($_SESSION['sID']) or in_array($ui->id('id',10,'get'),$substituteAccess['gs']))) {
    $id=$ui->id('id',10,'get');
    if ($ui->st('action','post')=='ri') {
        $i=0;
        $gamestring=array();
        $query=$sql->prepare("SELECT AES_DECRYPT(g.`ftppassword`,?) AS `cftppass`,AES_DECRYPT(g.`ppassword`,?) AS `pftppass`,g.`id`,g.`newlayout`,g.`rootID`,g.`serverip`,g.`port`,g.`pallowed`,g.`protected`,u.`cname` FROM `gsswitch` g INNER JOIN `userdata` u ON g.`userid`=u.`id` WHERE g.`id`=? AND g.`userid`=? AND g.`resellerid`=? LIMIT 1");
        $query->execute(array($aeskey,$aeskey,$ui->id('id',10,'get'),$user_id,$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $customer=$row['cname'];
            $ftppass=($row['pallowed']=='Y' and $row['protected']=='Y') ? $row['pftppass'] : $row['cftppass'];
            $rootID=$row['rootID'];
            $serverip=$row['serverip'];
            $port=$row['port'];
            $gsfolder=$serverip.'_'.$port;
            if ($row['newlayout']=='Y') $customer=$customer.'-'.$row['id'];
        }
        $template=array();
        foreach($ui->id('template',10,'post') as $id => $tpl) {
            if ($tpl>0) {
                $template[]=$tpl;
                if ($ui->active('type','post')=='Y') {
                    $query=$sql->prepare("DELETE FROM `addons_installed` WHERE `serverid`=? AND `resellerid`=? AND `userid`=?");
                    $query->execute(array($id,$reseller_id,$user_id));
                }
                $query=$sql->prepare("SELECT s.`gamemod`,s.`gamemod2`,t.`shorten` FROM `serverlist` s INNER JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE s.`id`=? AND s.`resellerid`=? LIMIT 1");
                $query->execute(array($id,$reseller_id));
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $shorten=$row['shorten'];
                    $gamemod2=$row['gamemod2'];
                    $gamestring[]=($row['gamemod']=='Y') ? $shorten.$gamemod2 : $shorten;
                }
            }
        }
        if (isset($gsfolder) and count($gamestring)>0 and $ui->active('type','post')) {
            $gamestring=count($gamestring).'_'.implode('_',$gamestring);
            $rdata=serverdata('root',$rootID,$aeskey,$sql);
            $sship=$rdata['ip'];
            $sshport=$rdata['port'];
            $sshuser=$rdata['user'];
            $sshpass=$rdata['pass'];
            if ($ui->active('type','post')=='Y') {
                ssh2exec($rootID,'root',$aeskey,"./control.sh add ${customer} ${ftppass} ${sshuser} ".passwordgenerate(10),$sql);
                $sshcmd="./control.sh reinstserver ${customer} ${gamestring} ${gsfolder} \"".implode(' ',$template).'"';
                $loguseraction="%reinstall% %gserver% ${serverip}:${port}";
            } else {
                $sshcmd="./control.sh addserver ${customer} ${gamestring} ${gsfolder} \"".implode(' ',$template).'"';
                $loguseraction="%resync% %gserver% ${serverip}:${port}";
            }
            shell_server($sship,$sshport,$sshuser,$sshpass,$customer,$ftppass,$sshcmd,$sql);
            $template_file=$sprache->server_installed;
            $insertlog->execute();
        } else {
            $template_file='userpanel_404.tpl';
        }
    } else {
        $query=$sql->prepare("SELECT `serverid` FROM `gsswitch` WHERE `id`=? AND `resellerid`=?");
        $query->execute(array($id,$reseller_id));
        $currentID=$query->fetchColumn();
        $query=$sql->prepare("SELECT s.*,t.`description`,t.`shorten` FROM `serverlist` s INNER JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE s.`switchID`=? AND s.`resellerid`=?");
        $query->execute(array($id,$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $servertemplate=($currentID==$row['id']) ? $row['servertemplate'] : '';
            $table[]=array('id'=>$row['id'],'description'=>$row['description'],'shorten'=>$row['shorten'],'servertemplate'=>$servertemplate);
        }
        if (count($table)>0) {
            $template_file="userpanel_gserver_reinstall.tpl";
        } else {
            $template_file='userpanel_404.tpl';
        }
    }
} else if (($ui->st('d','get')=='rs' or $ui->st('d','get')=='st' or $ui->st('d','get')=='du') and $ui->id('id',10,'get') and (!isset($_SESSION['sID']) or in_array($ui->id('id',10,'get'),$substituteAccess['gs']))) {
    $id=$ui->id('id',10,'get');
    $query=$sql->prepare("SELECT `serverip`,`port`,`rootID` FROM `gsswitch` WHERE `id`=? AND `resellerid`=? AND `active`='Y' LIMIT 1");
    $query->execute(array($id,$reseller_id));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $gsip=$row['serverip'];
        $port=$row['port'];
        if ($ui->st('d','get')=='rs') {
            $template_file='Restart done';
            $cmds=gsrestart($id,'re',$aeskey,$sprache,$reseller_id,$sql);
            $loguseraction="%start% %gserver% $gsip:$port";
        } else if ($ui->st('d','get')=='st') {
            $template_file='Stop done';
            $cmds=gsrestart($id,'so',$aeskey,$sprache,$reseller_id,$sql);
            $loguseraction="%stop% %gserver% $gsip:$port";
        } else if ($ui->st('d','get')=='du') {
            $template_file='SourceTV upload started';
            $cmds=gsrestart($id,'du',$aeskey,$sprache,$reseller_id,$sql);
            $loguseraction="%movie% %gserver% $gsip:$port";
        }
        if (isset($cmds)) ssh2_execute('gs',$row['rootID'],$cmds);
        $insertlog->execute();
    }
    if (!isset($gsip)) {
        $template_file='userpanel_404.tpl';
    }
} else if ($ui->st('d','get')=='md' and $ui->id('id',10,'get') and (!isset($_SESSION['sID']) or in_array($ui->id('id',10,'get'),$substituteAccess['gs']))) {
    $id=$ui->id('id',10,'get');
    if (!$ui->smallletters('action',2,'post')) {
        $table = array();
        $query=$sql->prepare("SELECT `id`,`normal_3`,`normal_4`,`hlds_3`,`hlds_4`,`hlds_5`,`hlds_6` FROM `eac` WHERE active='Y' AND `resellerid`=? LIMIT 1");
        $query->execute(array($reseller_id));
        $rowcount=$query->rowCount();
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $normal_3=$row['normal_3'];
            $normal_4=$row['normal_4'];
            $hlds_3=$row['hlds_3'];
            $hlds_4=$row['hlds_4'];
            $hlds_5=$row['hlds_5'];
            $hlds_6=$row['hlds_6'];
        }
        $query=$sql->prepare("SELECT `id`,AES_DECRYPT(`ftppassword`,?) AS `cftppass`,CONCAT(`serverip`,':',`port`) AS `server`,`eacallowed`,`serverid` FROM `gsswitch` WHERE `id`=? AND `userid`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($aeskey,$id,$user_id,$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $address=$row['server'];
            $ftppass=$row['cftppass'];
            $eacallowed=$row['eacallowed'];
            $serverID=$row['serverid'];
            $eacallowed=$row['eacallowed'];
        }
        $query=$sql->prepare("SELECT s.*,AES_DECRYPT(s.`uploaddir`,?) AS `decypteduploaddir`,AES_DECRYPT(s.`webapiAuthkey`,?) AS `dwebapiAuthkey`,t.`description`,t.`qstat`,t.`shorten`,t.`modcmds`,t.`mapGroup` AS `defaultMapGroup`,t.`appID`,t.`map` AS `defaultmap` FROM `serverlist` s INNER JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE s.`switchID`=? AND s.`resellerid`=?");
        $query->execute(array($aeskey,$aeskey,$id,$reseller_id));
        $i=0;
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $gshorten=$row['shorten'];
            $qstat=$row['qstat'];
            $anticheat=$row['anticheat'];
            if ($qstat=='a2s' and $row['user_uploaddir']=='Y' and $row['upload']>1 and $row['upload']<4) {
                $uploaddir=$row['decypteduploaddir'];
                $upload=true;
            } else {
                $upload=false;
                $uploaddir='';
            }
            if ($qstat=="a2s" or $qstat=="hla2s") {
                $anticheatsoft="Valve Anti Cheat";
            } else if ($qstat=="cods") {
                $anticheatsoft="Punkbuster";
            } else {
                $anticheatsoft='';
            }
            if ($row['id']==$serverID) {
                $currentTemplate=$gshorten;
                $displayNone='';
                $displayNoneBoot='in';
                $option='<option value="'.$row['id'].'" selected="selected">'.$gshorten.'</option>';
            } else {
                $displayNone='display_none';
                $displayNoneBoot='';
                $option='<option value="'.$row['id'].'">'.$gshorten.'</option>';
            }
            $eac=array();
            if ($rowcount>0 and $eacallowed=='Y' and ($gshorten=="css" or $gshorten=="cod4" or $gshorten=="cstrike" or $gshorten=="czero" or $gshorten=="tf")) {
                if ($gshorten=="cstrike" or $gshorten=="czero") {
                    if($anticheat==3 and $hlds_3=='Y') {
                        $eac[]='<option value="3" selected="selected">Easy Anti Cheat</option>';
                    } else if($hlds_3=='Y') {
                        $eac[]='<option value="3">Easy Anti Cheat</option>';
                    }
                    if($anticheat==4 and $hlds_4=='Y') {
                        $eac[]='<option value="4" selected="selected">Easy Anti Cheat Public</option>';
                    } else if($hlds_4=='Y') {
                        $eac[]='<option value="4">Easy Anti Cheat Public</option>';
                    }
                    if($anticheat==5 and $hlds_5=='Y') {
                        $eac[]='<option value="5" selected="selected">Easy Anti Cheat 32Bit</option>';
                    } else if($hlds_5=='Y') {
                        $eac[]='<option value="5">Easy Anti Cheat 32Bit</option>';
                    }
                    if($anticheat==6 and $hlds_6=='Y') {
                        $eac[]='<option value="6" selected="selected">Easy Anti Cheat Public 32Bit</option>';
                    } else if($hlds_6=='Y') {
                        $eac[]='<option value="6">Easy Anti Cheat Public 32Bit</option>';
                    }
                } else {
                    if($anticheat==3 and $normal_3=='Y') {
                        $eac[]='<option value="3" selected="selected">Easy Anti Cheat</option>';
                    } else if($normal_3=='Y') {
                        $eac[]='<option value="3">Easy Anti Cheat</option>';
                    }
                    if($anticheat==4 and $normal_4=='Y') {
                        $eac[]='<option value="4" selected="selected">Easy Anti Cheat Public</option>';
                    } else if($normal_4=='Y') {
                        $eac[]='<option value="4">Easy Anti Cheat Public</option>';
                    }
                }
            }
            $mods=array();
            $mod=$row['modcmd'];
            foreach (explode("\r\n",$row['modcmds']) as $line) {
                if (preg_match('/^(\[[\w\/\.\-\_\= ]{1,}\])$/',$line)) {
                    $name=trim($line,'[]');
                    $ex=preg_split("/\=/",$name,-1,PREG_SPLIT_NO_EMPTY);
                    $mods[]=trim($ex[0]);
                }
            }
            $workshopCollection=false;
            if (in_array($row['appID'],array(560,730,740))) {
                $workshopCollection=$row['workshopCollection'];
            }
            $map=(!in_array($row['defaultmap'],array('',null))) ? $row['map'] : null;
            $table[]=array('id'=>$row['id'],'cmd'=>$row['cmd'],'fps' =>$row['fps'],'tic'=>$row['tic'],'map'=>$map,'workshopCollection'=>$workshopCollection,'webapiAuthkey'=>$row['dwebapiAuthkey'],'mapGroup'=>$row['mapGroup'],'defaultMapGroup'=>$row['defaultMapGroup'],'servertemplate'=>$row['servertemplate'],'userfps'=>$row['userfps'],'usertick'=>$row['usertick'],'usermap'=>$row['usermap'],'description'=>$row['description'],'option'=>$option,'qstat'=>$qstat,'upload'=>$upload,'uploaddir'=>$uploaddir,'anticheat'=>$anticheat,'anticheatsoft'=>$anticheatsoft,'eac'=>$eac,'shorten'=>$gshorten,'mod'=>$mod,'mods'=>$mods,'displayNone'=>$displayNone,'displayNoneBoot'=>$displayNoneBoot);
            $i++;
        }
        if ($i>0) {
            $template_file="userpanel_gserver_md.tpl";
        } else {
            $template_file='userpanel_404.tpl';
        }
    } else if ($ui->smallletters('action',2,'post')=='md' and $ui->id('shorten',19,'post')) {
        $switchID=$ui->id('shorten',19,'post');
        $query=$sql->prepare("SELECT `active`,`normal_3`,`normal_4`,`hlds_3`,`hlds_4`,`hlds_5`,`hlds_6` FROM `eac` WHERE `resellerid`=? LIMIT 1");
        $query->execute(array($reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $active=$row['active'];
            $normal_3=$row['normal_3'];
            $normal_4=$row['normal_4'];
            $hlds_3=$row['hlds_3'];
            $hlds_4=$row['hlds_4'];
            $hlds_5=$row['hlds_5'];
            $hlds_6=$row['hlds_6'];
        }
        $query=$sql->prepare("SELECT g.*,AES_ENCRYPT(g.`ftppassword`,?) AS `encrypted`,AES_ENCRYPT(g.`ppassword`,?) AS `pencrypted`,u.`cname` FROM `gsswitch` g INNER JOIN `userdata` u ON g.`userid`=u.`id` WHERE g.`id`=? AND g.`resellerid`=? LIMIT 1");
        $query->execute(array($aeskey,$aeskey,$id,$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $oldID=$row['serverid'];
            $serverip=$row['serverip'];
            $port=$row['port'];
            $oldPass=$row['encrypted'];
            $poldPass=$row['pencrypted'];
            $oldProtected=$row['protected'];
            $rootID=$row['rootID'];
            $servercname=$row['cname'];
            $newlayout=$row['newlayout'];
            $server=$serverip.":".$port;
        }
        $query=$sql->prepare("SELECT s.*,AES_DECRYPT(s.`uploaddir`,?) AS `decypteduploaddir`,t.`shorten` FROM `serverlist` s INNER JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE s.`id`=? AND s.`resellerid`=? LIMIT 1");
        $query->execute(array($aeskey,$switchID,$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $fps=($row['userfps']=='Y' and $ui->id("fps_${switchID}",4,'post')) ? $ui->id("fps_${switchID}",4,'post') : $row['fps'];
            $tic=($row['usertick']=='Y' and $ui->id("tic_${switchID}",4,'post')) ? $ui->id("tic_${switchID}",4,'post') : $row['tic'];
            $map=($row['usermap']=='Y' and $ui->mapname("map_${switchID}",'post')) ? $ui->mapname("map_${switchID}",'post') : $row['map'];
            $mapGroup=($row['usermap']=='Y' and $ui->mapname("mapGroup_${switchID}",'post')) ? $ui->mapname("mapGroup_${switchID}",'post') : $row['mapGroup'];
            $uploaddir=($row['user_uploaddir']=='Y' and $row['upload']>1 and $row['upload']<4) ? $ui->url("uploaddir_${switchID}",'post') : $row['decypteduploaddir'];
            $servertemplate=($ui->id("servertemplate_${switchID}",1,'post')) ? $ui->id("servertemplate_${switchID}",1,'post') : 1;
            $modcmd=$ui->escaped("mod_${switchID}",'post');
            $workshopCollection=$ui->id("workshopCollection_${switchID}",10,'post');
            $webapiAuthkey=$ui->w("webapiAuthkey_${switchID}",32,'post');
            if ($ui->id("anticheat_${switchID}",1,'post')) {
                $anticheat=($ui->id("anticheat_${switchID}",1,'post')>0) ? $ui->id("anticheat_${switchID}",1,'post') : 1;
                if ($row['shorten']=="cstrike" or $row['shorten']=="czero") {
                    if($anticheat==3 and $hlds_3=='N' and $hlds_5=='Y' and $active=='Y') $anticheat=5;
                    else if($anticheat==3 and $hlds_3=='N' and $hlds_5=='N' and $active=='Y') $anticheat=1;
                    else if ($anticheat>1 and $active=='N') $anticheat=1;
                    if($anticheat==4 and $hlds_4=='N' and $hlds_6=='Y' and $active=='Y') $anticheat=6;
                    else if($anticheat==4 and $hlds_4=='N' and $hlds_6=='N' and $active=='Y') $anticheat=1;
                    else if ($anticheat>1 and $active=='N') $anticheat=1;
                    if($anticheat==5 and $hlds_5=='N' and $active=='Y') $anticheat=1;
                    if($anticheat==6 and $hlds_6=='N' and $active=='Y') $anticheat=1;
                    if(($anticheat>6 and $active=='Y') or $anticheat>2 and $active=='N') $anticheat=1;
                } else {
                    if($anticheat==3 and $normal_3=='N' and $active=='Y') $anticheat=1;
                    if($anticheat==4 and $normal_4=='N' and $active=='Y') $anticheat=1;
                    if(($anticheat>4 and $active=='Y') or $anticheat>2 and $active=='N') $anticheat=1;
                }
            } else {
                $anticheat=1;
            }
        }
        if (isset($anticheat)) {
            $query=$sql->prepare("UPDATE `serverlist` SET `anticheat`=?,`fps`=?,`tic`=?,`map`=?,`workshopCollection`=?,`mapGroup`=?,`modcmd`=?,`servertemplate`=?,`uploaddir`=AES_ENCRYPT(?,?),`webapiAuthkey`=AES_ENCRYPT(?,?) WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($anticheat,$fps,$tic,$map,$workshopCollection,$mapGroup,$modcmd,$servertemplate,$uploaddir,$aeskey,$webapiAuthkey,$aeskey,$switchID,$reseller_id));
            $template_file=$spracheResponse->table_add;
        } else {
            $template_file=$spracheResponse->error_table;
        }
        $ftppass=$ui->password('ftppass',100,'post');
        $cmds=array();
        if (isset($oldID) and $oldID!=$switchID) {
            $tmp=gsrestart($id,'so',$aeskey,$sprache,$reseller_id,$sql);
            if (is_array($tmp)) foreach($tmp as $t) $cmds[]=$t;
        }
        $query=$sql->prepare("UPDATE `gsswitch` SET `serverid`=?,`ftppassword`=AES_ENCRYPT(?,?) WHERE `id`=? AND `resellerid`=? LIMIT 1");
        $query->execute(array($switchID,$ftppass,$aeskey,$id,$reseller_id));
        if (isset($oldID) and $oldID!=$switchID or $ftppass!=$oldPass) {
            if ($oldID!=$switchID) {
                if (isset($oldProtected) and $oldProtected=='Y') {
                    $query=$sql->prepare("UPDATE `gsswitch` SET `protected`='N' WHERE `id`=? AND `resellerid`=? LIMIT 1");
                    $query->execute(array($id,$reseller_id));
                    $tmp=gsrestart($id,'re',$aeskey,$sprache,$reseller_id,$sql);
                    if (is_array($tmp)) foreach($tmp as $t) $cmds[]=$t;
                } else {
                    $tmp=gsrestart($id,'re',$aeskey,$sprache,$reseller_id,$sql);
                    if (is_array($tmp)) foreach($tmp as $t) $cmds[]=$t;
                }
            }
            if ($ftppass!=$oldPass) {
                $rdata=serverdata('root',$rootID,$aeskey,$sql);
                $sship=$rdata['ip'];
                $sshport=$rdata['port'];
                $sshuser=$rdata['user'];
                $sshpass=$rdata['pass'];
                if ($newlayout=='Y') $servercname=$servercname.'-'.$id;
                $cmds[]='./control.sh mod '.$servercname.' '.$ftppass.' '.$poldPass;
            }
        }
        if (isset($rootID) and count($cmds)>0)ssh2_execute('gs',$rootID,$cmds);
        $loguseraction="%mod% %gserver% $server";
        $insertlog->execute();
    } else {
        $template_file='Error: No such game!';
    }
} else if ($ui->st('d','get')=='cf' and $ui->id('id',10,'get') and (!isset($_SESSION['sID']) or in_array($ui->id('id',10,'get'),$substituteAccess['gs']))) {
    $id=$ui->id('id',10,'get');
    $serverID=0;
    $query=$sql->prepare("SELECT g.*,AES_DECRYPT(g.`ftppassword`,?) AS `dftppass`,AES_DECRYPT(g.`ppassword`,?) AS `dpftppass`,s.`anticheat`,s.`servertemplate`,t.`shorten`,t.`gamebinary`,t.`modfolder`,t.`binarydir`,t.`qstat`,u.`cname` FROM `gsswitch` g INNER JOIN `serverlist` s ON g.`serverid`=s.`id` INNER JOIN `servertypes` t ON s.`servertype`=t.`id` INNER JOIN `userdata` u ON g.`userid`=u.`id` WHERE g.`id`=? AND g.`userid`=? AND g.`resellerid`=? LIMIT 1");
    $query->execute(array($aeskey,$aeskey,$id,$user_id,$reseller_id));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $username=$row['cname'];
        if ($row['newlayout']=='Y') $username=$username.'-'.$row['id'];
        $anticheat=$row['anticheat'];
        $qstat=$row['qstat'];
        $eacallowed=$row['eacallowed'];
        $serverip=$row['serverip'];
        $port=$row['port'];
        $rootID=$row['rootID'];
        $shorten=$row['shorten'];
        $binarydir=$row['binarydir'];
        $gamebinary=$row['gamebinary'];
        $modfolder=$row['modfolder'];
        $protected=$row['protected'];
        $servertemplate=$row['servertemplate'];
        $ftppass=$row['dftppass'];
        $pallowed=$row['pallowed'];
        if ($protected=='N' and $servertemplate>1) {
            $ftpshorten=$row['shorten']."-".$servertemplate;
            $pserver="server/";
        } else if ($protected=='Y' and $pallowed=='Y') {
            $ftpshorten=$row['shorten'];
            $username=$username."-p";
            $ftppass=$row['dpftppass'];
            $pserver='';
        } else {
            $ftpshorten=$row['shorten'];
            $pserver="server/";
        }
    }
    $configs=array();
    $configCheck=array();
    $query=$sql->prepare("SELECT g.`protected`,t.`configs`,s.`id` FROM `gsswitch` g INNER JOIN `serverlist` s ON g.`serverid`=s.`id` INNER JOIN `servertypes` t ON s.`servertype`=t.`id` WHERE g.`id`=? AND g.`userid`=? AND g.`resellerid`=? LIMIT 1");
    $query->execute(array($id,$user_id,$reseller_id));
    $customer=getusername($user_id,$sql);
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $serverID=$row['id'];
        $protected=$row['protected'];
        $config_rows=explode("\r\n", $row['configs']);
        foreach ($config_rows as $configline) {
            $data_explode=explode(" ", $configline);
            $permission=(isset($data_explode[1])) ? $data_explode[1] : 'full';
            if ($data_explode[0]!='') {
                $configs[]=array('permission'=>$permission,'line'=>$data_explode[0]);
                $configCheck[]=$data_explode[0];
            }
        }
    }
    $query=$sql->prepare("SELECT a.`configs`,a.`paddon` FROM `addons_installed` i INNER JOIN `addons` a ON i.`addonid`=a.`id` WHERE i.`serverid`=? AND i.`userid`=? AND i.`resellerid`=?");
    $query->execute(array($serverID,$user_id,$reseller_id));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if (isset($protected) and ($protected=='N' or $row['paddon']=='Y')) {
            $config_rows=explode("\r\n", $row['configs']);
            foreach ($config_rows as $configline) {
                $data_explode=explode(" ", $configline);
                $permission=(isset($data_explode[1])) ? $data_explode[1] : 'full';
                if ($data_explode[0]!='') {
                    $configs[]=array('permission'=>$permission,'line'=>$data_explode[0]);
                    $configCheck[]=$data_explode[0];
                }
            }
        }
    }
    if ($ui->smallletters('type',4,'get')) {
        if ($ui->config('config','get')) {
            $postconfig=$ui->config('config','get');
        } else if ($ui->config('config','post')) {
            $postconfig=$ui->config('config','post');
        } else {
            $postconfig=null;
        }
        if (in_array($postconfig,$configCheck) and $ui->smallletters('type',4,'get') and ($ui->smallletters('type',4,'get')=='easy' or $ui->smallletters('type',4,'get')=='full')) {
            $explodeconfig=explode("/", $postconfig);
            $configname=$explodeconfig[(count($explodeconfig)-1)];
            $query=$sql->prepare("SELECT `ip`,`ftpport` FROM `rserverdata` WHERE `id`=? AND `resellerid`=? LIMIT 1");
            $query->execute(array($rootID,$reseller_id));
            foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $ftpport=$row['ftpport'];
                $ip=$row['ip'];
            }
            if($gamebinary=="srcds_run"){
                $config=$binarydir."/".$modfolder."/".$postconfig;
                if ($configname=="server.cfg" and $qstat=="a2s") {
                    $general_cvar=array('hostname','sv_password','sv_contact','sv_tags','motdfile','mapcyclefile','sv_downloadurl','net_maxfilesize','rcon_password','sv_rcon_minfailures','sv_rcon_maxfailures','sv_rcon_banpenalty','sv_rcon_minfailuretime','sv_pure','sv_pure_kick_clients','sv_timeout','sv_voiceenable','sv_allowdownload','sv_allowupload','sv_region','sv_friction','sv_stopspeed','sv_gravity','sv_accelerate','sv_airaccelerate','sv_wateraccelerate','sv_allow_color_correction','sv_allow_wait_command','mp_flashlight','mp_footsteps','mp_falldamage','mp_limitteams','mp_limitteams','mp_friendlyfire','mp_autokick','mp_forcecamera','mp_fadetoblack','mp_allowspectators','mp_chattime','log','sv_log_onefile','sv_logfile','sv_logbans','sv_logecho','mp_logdetail','mp_timelimit','mp_winlimit','sv_minrate','sv_maxrate','sv_minupdaterate','sv_maxupdaterate','sv_mincmdrate','sv_maxcmdrate','sv_client_cmdrate_difference','sv_client_min_interp_ratio','sv_client_max_interp_ratio','mp_fraglimit','mp_maxrounds');
                } else {
                    $general_cvar=array();
                }
            } else if($gamebinary=="hlds_run"){
                $config=$modfolder."/".$postconfig;
                $general_cvar=array();
            } else {
                $general_cvar=array();
                $config=$postconfig;
            }
            if ($shorten=="css" and $configname=="server.cfg") {
                $game_cvars=array('motdfile_text','sv_disablefreezecam','sv_nonemesis','sv_nomvp','sv_nostats','sv_allowminmodels','sv_hudhint_sound','sv_competitive_minspec','sv_legacy_grenade_damage','sv_enableboost','sv_enablebunnyhopping','mp_forceautoteam','mp_enableroundwaittime','mp_startmoney','mp_roundtime','mp_buytime','mp_c4timer','mp_freezetime','mp_spawnprotectiontime','mp_hostagepenalty','mp_tkpunish');
            } else if ($shorten=="dods" and $configname=="server.cfg") {
                $game_cvars=array('mp_limit_allies_rocket','mp_limit_axis_rocket','mp_limit_axis_mg','mp_limit_axis_sniper','mp_limit_axis_assault','mp_limit_axis_support','mp_limit_axis_rifleman','mp_limit_allies_mg','mp_limit_allies_sniper','mp_limit_allies_assault','mp_limit_allies_support','mp_limit_allies_rifleman','dod_freezecam','dod_enableroundwaittime','dod_bonusroundtime','dod_bonusround');
            } else {
                $game_cvars=array();
            }
            if ($ui->smallletters('type',4,'get')=='full' and isset($ui->post['update']) and $ui->post['update']==1) {
                $configfile=stripslashes($ui->post['cleanedconfig']);
                $fp=true;
            } else {
                $fp=@fopen("ftp://$username:$ftppass@$ip:$ftpport/$pserver".$serverip."_"."$port/$ftpshorten/$config",'r');
                $configfile='';
            }
            $noConfig=($fp==false) ? true: false;
            $cleanedconfig='';
            if ($noConfig===false and ($ui->smallletters('type',4,'get')=="easy" or ($ui->smallletters('type',4,'get')=="full" and !isset($ui->post['update'])))) {
                stream_set_timeout($fp,5);
                while (!feof($fp)) $configfile .=fread($fp,1024);
                $info=stream_get_meta_data($fp);
                fclose($fp);
            }
            if ($noConfig===false and isset($info['timed_out']) and $info['timed_out']!="") {
                $template_file="Connection timed out!";
            } else {
                $configfile=str_replace(array("\0" , "\b" , "\r", "\Z"),"",$configfile);
                $lines=explode("\n", $configfile);
            }
            $lines=preg_replace('/\s+/',' ', $lines);
            if (isset($ui->post['update']) and $ui->post['update']==1) {
                $newconfig='';
                $setarray=array();
                foreach ($lines as $singeline) {
                    if (preg_match("/\w/", substr($singeline,0,1))) {
                        if (preg_match("/\"/", $singeline)) {
                            $split=explode('"', $singeline);
                            $cvar=str_replace(" ", "", $split[0]);
                            $value=$split[1];
                            if ($cvar!="exec") {
                                if (isset($ui->post["$cvar"])) {
                                    if (isset($ui->post['oldrcon']) and $cvar=="rcon_password" and $ui->post["$cvar"]!=$ui->post['oldrcon'] and $configname=="server.cfg" and (($anticheat==2 or $anticheat==3 or $anticheat==4 or $anticheat==5)) and ($qstat=="a2s" or $qstat=="hla2s") and $eacallowed=='Y') {
                                        eacchange('change',$id,$ui->post["$cvar"],$aeskey,$reseller_id,$sql);
                                    }
                                    $newconfig .=$cvar." \"".$ui->post[$cvar]."\""."\r\n";
                                } else if (isset($ui->post['oldrcon']) and $cvar=="rcon_password" and $value!=$ui->post['oldrcon'] and $configname=="server.cfg" and (($anticheat==2 or $anticheat==3 or $anticheat==4 or $anticheat==5)) and ($qstat=="a2s" or $qstat=="hla2s") and $eacallowed=='Y') {
                                    eacchange('change',$id,$value,$aeskey,$reseller_id,$sql);
                                } else {
                                    $newconfig .=$singeline."\r\n";
                                }
                                array_push($setarray, $cvar);
                            } else {
                                $newconfig .=$singeline."\r\n";
                            }
                        } else {
                            $split=explode(' ', $singeline);
                            if (isset($split[0])) {
                                $cvar=$split[0];
                                if (isset($split[1])) {
                                    $value=$split[1];
                                } else {
                                    $value='';
                                }
                                if ($cvar!="exec") {
                                    if (isset($ui->post["$cvar"])) {
                                        if (isset($ui->post['oldrcon']) and $cvar=="rcon_password" and $ui->post["$cvar"]!=$ui->post['oldrcon'] and $configname=="server.cfg" and (($anticheat==2 or $anticheat==3 or $anticheat==4 or $anticheat==5)) and ($qstat=="a2s" or $qstat=="hla2s") and $eacallowed=='Y') {
                                            eacchange('change',$id,$ui->post["$cvar"],$aeskey,$reseller_id,$sql);
                                        }
                                        $newconfig .=$cvar." \"".$ui->post[$cvar]."\""."\r\n";
                                    } else if (isset($ui->post['oldrcon']) and $cvar=="rcon_password" and $value!=$ui->post['oldrcon'] and $configname=="server.cfg" and (($anticheat==2 or $anticheat==3 or $anticheat==4 or $anticheat==5)) and ($qstat=="a2s" or $qstat=="hla2s") and $eacallowed=='Y') {
                                        eacchange('change',$id,$value,$aeskey,$reseller_id,$sql);
                                    } else {
                                        $newconfig .=$singeline."\r\n";
                                    }
                                    array_push($setarray, $cvar);
                                } else {
                                    $newconfig .=$singeline."\r\n";
                                }
                            }
                        }
                    } else {
                        $newconfig .=$singeline."\r\n";
                    }
                }
                if ($ui->smallletters('type',4,'get')=="easy") {
                    foreach ($general_cvar as $check_cvar) {
                        if (!in_array($check_cvar, $setarray)) {
                            $newconfig .=$check_cvar." \"".$ui->post[$check_cvar]."\""."\r\n";
                        }
                    }
                    foreach ($game_cvars as $check_cvar) {
                        if (!in_array($check_cvar, $setarray)) {
                            $newconfig .=$check_cvar." \"".$ui->post[$check_cvar]."\""."\r\n";
                        }
                    }
                }
                $temp = tmpfile();
                if ($ui->smallletters('type',4,'get')=="easy") {
                    fwrite($temp, $newconfig);
                } else if ($ui->smallletters('type',4,'get')=="full") {
                    if (mb_strlen($ui->post['cleanedconfig'],'UTF-8')<=16384) {
                        fwrite($temp, stripslashes($ui->post['cleanedconfig']),16384);
                    } else {
                        $post_lines=explode("<br />",nl2br(stripslashes($ui->post['cleanedconfig'])));
                        $post_lines[]="\r\n";
                        $post_lines[]="\r\n";
                        $post_lines[]="\r\n";
                        foreach ($post_lines as $line) {
                            if ($line=='\r\n') {
                                fwrite($temp, $line,16384);
                            } else {
                                fwrite($temp, $line."\r\n",16384);
                            }
                        }
                    }
                }
                fseek($temp,0);
                fseek($temp,0);
                if ($ftpport==21 or $ftpport=="" or $ftpport==null) {
                    $ftp_connect= @ftp_connect($ip);
                } else {
                    $ftp_connect= @ftp_connect($ip,$ftpport);
                }
                if ($ftp_connect) {
                    $ftp_login= @ftp_login($ftp_connect,$username,$ftppass);
                    if ($ftp_login) {
                        $split_config=preg_split('/\//', $config, -1, PREG_SPLIT_NO_EMPTY);
                        $folderfilecount=count($split_config)-1;
                        $i=0;
                        $folders=$pserver.$serverip.'_'.$port.'/'.$ftpshorten;
                        while ($i<$folderfilecount) {
                            $folders .="/".$split_config["$i"];
                            $i++;
                        }
                        $uploadfile=$split_config["$i"];
                        ftp_chdir($ftp_connect,$folders);
                        $checkupload=@ftp_fput($ftp_connect,$uploadfile,$temp,FTP_ASCII);
                        $template_file=($checkupload) ? $sprache->updated." ".$uploadfile : $sprache->failed." ".$folders.'/'.$uploadfile;
                    } else {
                        $template_file='Error: Logindata';
                    }
                    ftp_close($ftp_connect);
                } else {
                    $template_file='Error: FTP Connect';
                }
                fclose($temp);
                $loguseraction="%cfg% $configname";
                $insertlog->execute();
            } else {
                $linearray=array();
                $unknownarray=array();
                $cleanedconfig='';
                foreach ($lines as $singeline) {
                    $cleanedconfig .=$singeline."\r\n";
                    if (preg_match("/\w/", substr($singeline,0,1))) {
                        if (preg_match("/\"/", $singeline)) {
                            $split=explode('"', $singeline);
                            $cvar=str_replace(" ", "", $split[0]);
                            $value=$split[1];
                            if ($cvar!="exec") {
                                if (in_array($cvar, $general_cvar) or in_array($cvar, $game_cvars)) {
                                    $linearray["$cvar"]=$value;
                                } else {
                                    $unknownarray["$cvar"]=$value;
                                }
                            }
                        } else {
                            $split=explode(' ', $singeline);
                            if (isset($split[0])) {
                                $cvar=$split[0];
                                $value=(isset($split[1])) ? $split[1] : '';
                                if ($cvar!="exec") {
                                    if (in_array($cvar, $general_cvar) or in_array($cvar, $game_cvars)) {
                                        $linearray["$cvar"]=$value;
                                    } else {
                                        $unknownarray["$cvar"]=$value;
                                    }
                                }
                            }
                        }
                    }
                }
                $array_keys=array_keys($unknownarray);
                if ($configname=="server.cfg" and (($anticheat==2 or $anticheat==3 or $anticheat==4 or $anticheat==5)) and ($qstat=="a2s" or $qstat=="hla2s") and $eacallowed=='Y') $oldrcon=(array_key_exists('rcon_password', $linearray)) ? $linearray['rcon_password'] : 'unset';
                if ($ui->smallletters('type',4,'get')=="easy") {
                    $template_file="userpanel_gserver_config_edit_easy.tpl";
                } else if ($ui->smallletters('type',4,'get')=="full") {
                    $template_file="userpanel_gserver_config_edit_full.tpl";
                }
            }
        } else {
            $template_file='userpanel_404.tpl';
        }
    } else {
        $template_file="userpanel_gserver_config_edit.tpl";
    }
} else {
    $table=array();
    if (isset($admin_id) and $reseller_id!=0 and $admin_id!=$reseller_id) $reseller_id=$admin_id;
    $query=$sql->prepare("SELECT AES_DECRYPT(`ftppassword`,?) AS `cftppass`,g.*,s.`servertemplate`,s.`upload`,t.`shorten`,t.`qstatpassparam`,t.`protected` AS `tp`,u.`cname` FROM `gsswitch` g INNER JOIN `serverlist` s ON g.`serverid`=s.`id` INNER JOIN `servertypes` t ON s.`servertype`=t.`id` INNER JOIN `userdata` u ON g.`userid`=u.`id` WHERE g.`active`='Y' AND g.`userid`=? AND g.`resellerid`=? ORDER BY g.`serverip`,g.`port`");
    $query2=$sql->prepare("SELECT `ftpport` FROM `rserverdata` WHERE `id`=? LIMIT 1");
    $query->execute(array($aeskey,$user_id,$reseller_id));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if (!isset($_SESSION['sID']) or in_array($row['id'],$substituteAccess['gs'])) {
            $colspan=10;
            if (!$pa['useraddons']) $colspan--;
            $rootid=$row['rootID'];
            $war=$row['war'];
            $brandname=$row['brandname'];
            $protected=$row['protected'];
            $tprotected=$row['tp'];
            $pallowed=$row['pallowed'];
            $cname=$row['cname'];
            $shorten=$row['shorten'];
            $qstatpassparam=$row['qstatpassparam'];
            $passparams=explode(":", $qstatpassparam);
            $gameserverid=$row['id'];
            $name=$row['queryName'];
            $ip=$row['serverip'];
            $port=$row['port'];
            $address=$ip.':'.$port;
            $numplayers=$row['queryNumplayers'];
            $maxplayers=$row['queryMaxplayers'];
            $map=(in_array($row['queryMap'],array(false,null,''))) ? 'Unknown' : $row['queryMap'];
            $ce=explode(',',$row['cores']);
            $coreCount=count($ce);
            $cores=array();
            if ($row['taskset']=='Y' and $coreCount>0) foreach ($ce as $uc) $cores[]=$uc;
            $cores=implode(', ',$cores);
            $password=$row['queryPassword'];
            $stopped=$row['stopped'];
            $notified=$row['notified'];
            $cftppass=$row['cftppass'];
            if ($stopped=='Y') $name="OFFLINE";
            $updatetime=($user_language=="de") ? ($row['queryUpdatetime']!='') ? date('d.m.Y H:m:s',strtotime($row['queryUpdatetime'])) : $sprache->never : $row['queryUpdatetime'];
            $servertemplate=$row['servertemplate'];
            if ($row['upload']>1 and $row['upload']<4) {
                $upload=true;
            } else {
                $colspan--;
                $upload=false;
            }
            $currentTemplate=(($protected=='N' or $tprotected=='N') and $servertemplate>1) ? $row['shorten']."-".$servertemplate : $row['shorten'];
            $imgNameP='';
            $imgAltP='';
            $pro='';
            $pserver="/server/";
            if ($protected=='N' and ($pallowed=='Y' and $tprotected=='Y')) {
                $imgNameP='16_unprotected';
                $imgAltP=$sprache->off2;
                $pro=$sprache->off2;
                $pserver="/server/";
            } else if ($protected=='Y' and $tprotected=='Y' and $pallowed=='Y') {
                $imgNameP='16_protected';
                $imgAltP=$sprache->on;
                $pserver="/pserver/";
                $pro=$sprache->on;
            }
            if($pa['ftpaccess'] or $pa['miniroot']) {
                if ($row['newlayout']=='Y') $cname=$cname.'-'.$row['id'];
                $query2->execute(array($rootid));
                $ftpport=$query2->fetchColumn();
                $ftpdata="ftp://".$cname.":".$cftppass."@".$ip.":".$ftpport.$pserver.$ip.'_'.$port."/".$currentTemplate;
            } else {
                $cftppass='';
                $ftpport='';
                $ftpdata='';
            }
            $nameremoved='';
            $premoved='';
            $imgName='16_ok';
            $imgAlt='Online';
            if ($stopped=='Y') {
                $numplayers=0;
                $maxplayers=0;
                $imgName='16_bad';
                $imgAlt='Stopped';
            } else if (($name=='OFFLINE' or $name=='') and $notified>=$rSA['down_checks'] and $stopped=='N') {
                $numplayers=0;
                $maxplayers=0;
                $imgName='16_error';
                $imgAlt='Crashed';
            } else {
                if ($war=='Y' and $password=='N') {
                    $imgName='16_error';
                    $imgAlt='No Password';
                    $premoved=$sprache->premoved;
                }
                if ($brandname=='Y' and $rSA['brandname']!=null and $rSA['brandname']!='' and strpos(strtolower($name), strtolower($rSA['brandname'])) === false) {
                    $imgName='16_error';
                    $imgAlt='No Servertag';
                    $nameremoved=$sprache->nameremoved;
                }
            }
            $initalize[]=$gameserverid.'-start';
            $initalize[]=$gameserverid.'-stop';
            $initalize[]=$gameserverid.'-settings';
            $initalize[]=$gameserverid.'-config';
            $initalize[]=$gameserverid.'-reinstall';
            $initalize[]=$gameserverid.'-planer';
            $initalize[]=$gameserverid.'-logs';
            $initalize[]=$gameserverid.'-backup';
            $initalize[]=$gameserverid.'-addons';
            $initalize[]=$gameserverid.'-protect';
            $initalize[]=$gameserverid.'-sourcetv';
            $table[]=array('id'=>$gameserverid,'premoved'=>$premoved,'nameremoved'=>$nameremoved,'server'=>$address,'name'=>$name,'img'=>$imgName,'alt'=>$imgAlt,'imgp'=>$imgNameP,'altp'=>$imgAltP,'numplayers'=>$numplayers,'maxplayers'=>$maxplayers,'map'=>$map,'cname'=>$cname,'cftppass'=>$cftppass,'ip'=>$ip,'ftpport'=>$ftpport,'port'=>$port,'shorten'=>$currentTemplate,'gameShorten'=>$shorten,'ftpdata'=>$ftpdata,'updatetime'=>$updatetime,'stopped'=>$stopped,'pro'=>$pro,'upload'=>$upload,'minram'=>$row['minram'],'maxram'=>$row['maxram'],'taskset'=>$row['taskset'],'coreCount'=>$coreCount,'cores'=>$cores);
        }
    }
    $template_file="userpanel_gserver_list.tpl";
}