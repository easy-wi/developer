<?php
/**
 * File: root_dedicated.php.
 * Author: Ulrich Block
 * Date: 11.10.12
 * Time: 10:31
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

if (!isset($admin_id) or $main!=1 or !$pa['dedicatedServer']) {
    header('Location: admin.php');
    die;
}
$aesfilecvar=getconfigcvars(EASYWIDIR."/stuff/keyphrasefile.php");
$aeskey=$aesfilecvar['aeskey'];
$sprache=getlanguagefile('reseller',$user_language,$reseller_id,$sql);
$loguserid=$admin_id;
$logusername=getusername($admin_id,$sql);
$logusertype='admin';
if ($reseller_id==0) {
    $logreseller=0;
    $logsubuser=0;
} else {
    $logsubuser=(isset($_SESSION['oldid'])) ? $_SESSION['oldid'] : 0;
    $logreseller=0;
}
if ($ui->st('d','get')=='ad' and is_numeric($licenceDetails['lDs']) and $licenceDetails['lDs']>0 and $licenceDetails['left']>0 and !is_numeric($licenceDetails['left'])) {
    $template_file=$gsprache->licence;
} else if ($ui->w('action',4,'post') and !token(true)) {
    $template_file=$spracheResponse->token;
} else if (in_array($ui->st('d','get'),array('md','ad'))){
    $query=$sql->prepare("SELECT COUNT(`id`) AS `a` FROM `rootsDHCP` WHERE `active`='Y' LIMIT 1");
    $query->execute();
    $dhcp=($query->fetchColumn()>0) ? 'Y' : 'N';
    $query=$sql->prepare("SELECT COUNT(`id`) AS `a` FROM `rootsPXE` WHERE `active`='Y' LIMIT 1");
    $query->execute();
    $pxe=($query->fetchColumn()>0) ? 'Y' : 'N';
    if (!in_array($ui->smallletters('action',2,'post'),array('md','ad')) and $ui->st('d','get')=='md') {
        $table=array();
        $query=($reseller_id==0) ? $sql->prepare("SELECT `id`,`cname`,`vname`,`name`,`accounttype` FROM `userdata` WHERE (`id`=`resellerid` OR `resellerid`=?) AND `accounttype` IN ('r','u') ORDER BY `id` DESC") : $sql->prepare("SELECT `id`,`cname`,`vname`,`name`,`accounttype` FROM `userdata` WHERE `resellerid`=? AND `accounttype` IN ('r','u') ORDER BY `id` DESC");
        $query->execute(array($reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $type=($row['accounttype']=='u') ? $gsprache->user : $gsprache->reseller;
            $table[$row['id']]=$type.' '.trim($row['cname'].' '.$row['vname'].' '.$row['name']);
        }
        $id=$ui->id('id',10,'get');
        $query=$sql->prepare("SELECT * FROM `rootsDedicated` WHERE `dedicatedID`=? AND `resellerID`=? LIMIT 1");
        $query->execute(array($id,$reseller_id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $active=$row['active'];
            $ip=$row['ip'];
            $ips=$row['ips'];
            $description=$row['description'];
            $externalID=$row['externalID'];
            $mac=$row['mac'];
            $restart=$row['restart'];
            $apiRequestType=$row['apiRequestType'];
            $https=$row['https'];
            $apiRequestRestart=$row['apiRequestRestart'];
            $apiRequestStop=$row['apiRequestStop'];
            $apiURL=$row['apiURL'];
            $userID=$row['userID'];
            $useDHCP=$row['useDHCP'];
            $usePXE=$row['usePXE'];
            if ($row['status']==1) {
                $status=$sprache->stopped;
            } else if ($row['status']==2) {
                $status=$sprache->installing;
            } else if ($row['status']==3) {
                $status=$sprache->rescue;
            } else {
                $status=$sprache->ok;
            }
        }
        $template_file=(isset($active)) ? 'admin_root_dedicated_md.tpl' : 'admin_404.tpl';
    } else if (!in_array($ui->smallletters('action',2,'post'),array('md','ad')) and $ui->st('d','get')=='ad' and $reseller_id==0) {
        $table=array();
        $query=$sql->prepare("SELECT `id`,`cname`,`vname`,`name` FROM `userdata` WHERE `id`=`resellerid` AND `accounttype`='r' ORDER BY `id` DESC");
        $query->execute();
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $table[$row['id']]=trim($row['cname'].' '.$row['vname'].' '.$row['name']);
        }
        $template_file='admin_root_dedicated_ad.tpl';
    } else if (in_array($ui->smallletters('action',2,'post'),array('md','ad'))) {
        $error=array();
        if (!$ui->active('active','post')) {
            $error[]='Active';
        }
        if (!$ui->ip('ip','post')) {
            $error[]='IP';
        }
        if (!$ui->w('restart',1,'post')) {
            $error[]='Restart';
        }
        if (count($error)>0) {
            $template_file='Error: '.implode('<br />',$error);
        } else {
            $id=$ui->id('id',10,'get');
            $active=yesNo('active');
            $https=yesNo('https');
            $useDHCP=yesNo('useDHCP');
            $usePXE=yesNo('usePXE');
            $ip=$ui->ip('ip','post');
            $ips=$ui->ips('ips','post');
            $description=$ui->escaped('description','post');
            $mac=$ui->mac('mac','post');
            $externalID=$ui->w('externalID',255,'post');
            $restart=$ui->w('restart',1,'post');
            $apiURL=$ui->domainPath('apiURL','post');
            $apiRequestType=$ui->w('apiRequestType',1,'post');
            $apiRequestRestart=$ui->escaped('apiRequestRestart','post');
            $apiRequestStop=$ui->escaped('apiRequestStop','post');
            $userID=$ui->id('userID',19,'post');
            $query=$sql->prepare("UPDATE `jobs` SET `status`='2' WHERE `type`='de' AND (`status` IS NULL OR `status`='1') AND `affectedID`=? and `resellerID`=?");
            $query->execute(array($id,$reseller_id));
            if ($ui->st('d','get')=='md' and $ui->id('id',10,'get')) {
                $query=$sql->prepare("SELECT `active`,`ip`,`mac`,`useDHCP`,`usePXE` FROM `rootsDedicated` WHERE `dedicatedID`=? AND `resellerID`=? LIMIT 1");
                $query->execute(array($id,$reseller_id));
                foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    if ($row['active']!=$active or $row['ip']!=$ip or $row['mac']!=$mac or $row['useDHCP']!=$useDHCP or $row['usePXE']!=$usePXE) {
                        $query=$sql->prepare("INSERT INTO `jobs` (`api`,`type`,`hostID`,`invoicedByID`,`affectedID`,`userID`,`name`,`status`,`date`,`action`,`extraData`,`resellerid`) VALUES ('D','de',NULL,?,?,?,?,NULL,NOW(),'md',?,?)");
                        $query->execute(array($admin_id,$id,$userID,$ip,json_encode(array('oldactive'=>$row['active'],'oldip'=>$row['ip'],'oldmac'=>$row['mac'])),$reseller_id));
                    }
                }
                $query=$sql->prepare("UPDATE `rootsDedicated` SET `active`=?,`userID`=?,`description`=?,`ip`=?,`ips`=?,`restart`=?,`apiRequestType`=?,`apiRequestRestart`=?,`apiRequestStop`=?,`apiURL`=?,`https`=?,`mac`=?,`useDHCP`=?,`usePXE`=?,`externalID`=?,`jobPending`='Y' WHERE `dedicatedID`=? AND `resellerID`=?");
                $query->execute(array($active,$userID,$description,$ip,$ips,$restart,$apiRequestType,$apiRequestRestart,$apiRequestStop,$apiURL,$https,$mac,$useDHCP,$usePXE,$externalID,$id,$reseller_id));
                $loguseraction="%mod% ".$gsprache->dedicated;
            } else if ($ui->st('d','get')=='ad' and $reseller_id==0) {
                $query=$sql->prepare("INSERT INTO `rootsDedicated` (`active`,`userID`,`description`,`ip`,`ips`,`restart`,`apiRequestType`,`apiRequestRestart`,`apiRequestStop`,`apiURL`,`https`,`mac`,`useDHCP`,`usePXE`,`externalID`,`resellerID`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                $query->execute(array($active,$userID,$description,$ip,$ips,$restart,$apiRequestType,$apiRequestRestart,$apiRequestStop,$apiURL,$https,$mac,$useDHCP,$usePXE,$externalID,$reseller_id));
                $loguseraction="%add% ".$gsprache->dedicated;
            } else {
                $template_file='admin_404.tpl';
            }
            customColumns('S',$id,'save');
            if (!isset($template_file) and isset($id) and $query->rowCount()>0) {
                $insertlog->execute();
                $template_file=$spracheResponse->table_add;
            } else if (!isset($template_file)) {
                $template_file=$spracheResponse->error_table;
            }
        }
    }
} else if ($ui->st('d','get')=='dl' and $ui->id('id',10,'get') and $reseller_id==0) {
    $id=$ui->id('id',10,'get');
    $query=$sql->prepare("SELECT `ip`,`description`,`restart`,`useDHCP`,`usePXE` FROM `rootsDedicated` WHERE `dedicatedID`=? AND `resellerID`=? LIMIT 1");
    $query->execute(array($id,$reseller_id));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $ip=$row['ip'];
        $restart=$row['restart'];
        $description=$row['description'];
        $useDHCP=$row['useDHCP'];
        $usePXE=$row['usePXE'];
    }
    if (!$ui->smallletters('action',2,'post')) {
        $template_file=(isset($ip)) ? 'admin_root_dedicated_dl.tpl' : 'admin_404.tpl';
    } else if (isset($restart) and $ui->smallletters('action',2,'post')=='dl') {
        customColumns('S',$id,'del');
        $query=$sql->prepare("SELECT COUNT(`id`) AS `a` FROM `rootsDHCP` WHERE `active`='Y' LIMIT 1");
        $query->execute();
        $dhcp=($query->fetchColumn()>0) ? 'Y' : 'N';
        $query=$sql->prepare("SELECT COUNT(`id`) AS `a` FROM `rootsPXE` WHERE `active`='Y' LIMIT 1");
        $query->execute();
        $pxe=($query->fetchColumn()>0) ? 'Y' : 'N';
        if (($dhcp=='Y' and $useDHCP=='Y') or ($pxe=='Y' and $usePXE=='Y') or $restart=='A') {
            $query=$sql->prepare("UPDATE `jobs` SET `status`='2' WHERE `type`='de' AND (`status` IS NULL OR `status`='1') AND `affectedID`=? and `resellerID`=?");
            $query->execute(array($id,$reseller_id));
            $query=$sql->prepare("INSERT INTO `jobs` (`api`,`type`,`hostID`,`invoicedByID`,`affectedID`,`userID`,`name`,`status`,`date`,`action`,`resellerid`) VALUES ('D','de',NULL,?,?,NULL,?,NULL,NOW(),'dl',?)");
            $query->execute(array($admin_id,$id,$ip,$reseller_id));
            if ($query->rowCount()>0) {
                $loguseraction="%del% ".$gsprache->dedicated;
                $insertlog->execute();
                $template_file=$spracheResponse->table_add;
            } else {
                $template_file=$spracheResponse->error_table;
            }
            $query=$sql->prepare("UPDATE `rootsDedicated` SET `jobPending`='Y' WHERE `dedicatedID`=? AND `resellerID`=?");
            $query->execute(array($id,$reseller_id));
        } else {
            $query=$sql->prepare("DELETE FROM `rootsDedicated` WHERE `dedicatedID`=? AND `resellerID`=? LIMIT 1");
            $query->execute(array($id,$reseller_id));
            if ($query->rowCount()>0) {
                $loguseraction="%del% ".$gsprache->dedicated;
                $insertlog->execute();
                $template_file=$spracheResponse->table_del;
            } else {
                $template_file=$spracheResponse->error_table;
            }
        }
    } else {
        $template_file='admin_404.tpl';
    }
} else if ($ui->st('d','get')=='ri' and $ui->id('id',10,'get')) {
    $id=$ui->id('id',10,'get');
    if (!$ui->st('action','post')) {
        $option=array();
        $query=$sql->prepare("SELECT COUNT(`id`) AS `a` FROM `rootsDHCP` WHERE `active`='Y' LIMIT 1");
        $query->execute();
        $dhcp=($query->fetchColumn()>0) ? 'Y' : 'N';
        $query=$sql->prepare("SELECT COUNT(`id`) AS `a` FROM `rootsPXE` WHERE `active`='Y' LIMIT 1");
        $query->execute();
        $pxe=($query->fetchColumn()>0) ? 'Y' : 'N';
        $query=$sql->prepare("SELECT r.*,d.*,AES_DECRYPT(d.`initialPass`,?) AS `decryptedpass` FROM `rootsDedicated` d LEFT JOIN `resellerimages` r ON d.`imageID`=r.`id` WHERE d.`dedicatedID`=? LIMIT 1");
        $query->execute(array($aeskey,$id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $ip=$row['ip'];
            $showImages=false;
            if ($row['userID']==null) {
                $error=$sprache->userAdd;
            } else if ($dhcp=='N' or $pxe=='N') {
                    $option[]='<option value="rs">'.$sprache->restart.'</option>';
                    $option[]='<option value="st">'.$sprache->stop.'</option>';
            } else {
                $showImages=true;
                if ($row['status']==null or $row['status']==2) {
                    $option[]='<option value="rc">'.$sprache->rescue_start.'</option>';
                    $option[]='<option value="ri">'.$sprache->reinstall.'</option>';
                } else if ($row['status']==0) {
                    $option[]='<option value="rs">'.$sprache->restart.'</option>';
                    $option[]='<option value="st">'.$sprache->stop.'</option>';
                    $option[]='<option value="rc">'.$sprache->rescue_start.'</option>';
                    $option[]='<option value="ri">'.$sprache->reinstall.'</option>';
                } else if ($row['status']==1) {
                    $option[]='<option value="rs">'.$sprache->restart.'</option>';
                    $option[]='<option value="rc">'.$sprache->rescue_start.'</option>';
                    $option[]='<option value="ri">'.$sprache->reinstall.'</option>';
                } else if ($row['status']==3) {
                    $option[]='<option value="rt">'.$sprache->rescue_stop.'</option>';
                    $option[]='<option value="ri">'.$sprache->reinstall.'</option>';
                }
            }
            if ($row['status'] == 1) {
                $status=$sprache->stopped;
            } else if ($row['status'] == 2) {
                $status=$sprache->installing;
            } else if ($row['status'] == 3) {
                $status=$sprache->rescue;
            } else {
                $status=$sprache->ok;
            }
            $description=$row['description'];
            $bitversion=$row['bitversion'];
            $pass=$row['decryptedpass'];
        }
        $templates=array();
        $query=$sql->prepare("SELECT `id`,`description`,`bitversion` FROM `resellerimages` WHERE `description` NOT IN ('Rescue 32bit','Rescue 64bit') ORDER BY `distro`,`bitversion`,`description`");
        $query->execute();
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if ($row['description']!='Rescue 32bit' and $row['description']!='Rescue 64bit') $templates[]=array('id'=>$row['id'],'description'=>$row['description']);
        }
        $template_file=(isset($ip)) ? 'admin_root_dedicated_ri.tpl' : 'admin_404.tpl';
    } else if (in_array($ui->st('action','post'),array('ri','rc','rs','st'))) {
        $query=$sql->prepare("SELECT d.`ip`,i.`bitversion` FROM `rootsDedicated` d LEFT JOIN `resellerimages` i ON d.`resellerImageID`=i.`id` WHERE d.`dedicatedID`=? LIMIT 1");
        $query->execute(array($id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $ip=$row['ip'];
            $bitversion=$row['bitversion'];
        }
        if (!isset($bitversion)) $bitversion=64;
        if (isset($ip)) {
            $extraData=array();
            if ($ui->st('action','post')=='ri') {
                $extraData['imageID']=$ui->id('imageid',10,'post');
            } else if ($ui->st('action','post')=='rc') {
                $query=$sql->prepare("SELECT `id` FROM `resellerimages` WHERE `bitversion`=? AND `active`='Y' AND `distro`='other' AND `description` LIKE 'Rescue %' LIMIT 1");
                $query->execute(array($bitversion));
                $extraData['imageID']=$query->fetchColumn();
            }
            $query=$sql->prepare("INSERT INTO `jobs` (`api`,`type`,`hostID`,`invoicedByID`,`affectedID`,`userID`,`name`,`status`,`date`,`action`,`extraData`,`resellerid`) VALUES ('D','de',NULL,?,?,NULL,?,NULL,NOW(),?,?,?)");
            $query->execute(array($admin_id,$id,$ip,$ui->st('action','post'),json_encode($extraData),$reseller_id));
            $query=$sql->prepare("UPDATE `rootsDedicated` SET `jobPending`='Y' WHERE `dedicatedID`=? AND `resellerID`=?");
            $query->execute(array($id,$reseller_id));
            $template_file=$spracheResponse->table_add;
        } else {
            $template_file='admin_404.tpl';
        }
    } else {
        $template_file='admin_404.tpl';
    }
} else {
    $table=array();
    $query=$sql->prepare("SELECT COUNT(`dedicatedID`) AS `amount` FROM `rootsDedicated` WHERE `resellerID`=?");
    $query->execute(array($reseller_id));
    $colcount=$query->fetchColumn();
    if ($start>$colcount) $start=$colcount-$amount;
    if ($start<0) $start=0;
    $next=$start+$amount;
    $vor=($colcount>$next) ? $start+$amount : $start;
    $back=$start-$amount;
    $zur=($back>=0) ? $start-$amount : $start;
    $o=$ui->st('o','get');
    if ($ui->st('o','get')=='dp') {
        $orderby='d.`ip` DESC';
    } else if ($ui->st('o','get')=='ap') {
        $orderby='d.`ip` ASC';
    } else if ($ui->st('o','get')=='ds') {
        $orderby='d.`active` DESC,`notified` DESC';
    } else if ($ui->st('o','get')=='as') {
        $orderby='d.`active` ASC,`notified` ASC';
    } else if ($ui->st('o','get')=='dc') {
        $orderby='u.`cname` DESC';
    } else if ($ui->st('o','get')=='ac') {
        $orderby='u.`cname` ASC';
    } else if ($ui->st('o','get')=='dn') {
        $orderby='u.`name` DESC,u.`vname` DESC';
    } else if ($ui->st('o','get')=='an') {
        $orderby='u.`name` ASC,u.`vname` ASC';
    } else if ($ui->st('o','get')=='as') {
        $orderby='d.`active` ASC,`notified` ASC';
    } else if ($ui->st('o','get')=='di') {
        $orderby='d.`dedicatedID` DESC';
    } else {
        $orderby='d.`dedicatedID` ASC';
        $o='ai';
    }
    $query=($reseller_id==0) ? $sql->prepare("SELECT d.*,u.`cname`,u.`name`,u.`vname` FROM `rootsDedicated` d LEFT JOIN `userdata` u ON d.`userID`=u.`id` WHERE d.`resellerID`=? OR u.`id`=u.`resellerid` ORDER BY $orderby LIMIT $start,$amount") : $sql->prepare("SELECT d.*,u.`cname`,u.`name`,u.`vname` FROM `rootsDedicated` d LEFT JOIN `userdata` u ON d.`userID`=u.`id` WHERE d.`resellerID`=? ORDER BY $orderby LIMIT $start,$amount");
    $query2=$sql->prepare("SELECT `action`,`extraData` FROM `jobs` WHERE `affectedID`=? AND `type`='de' AND (`status` IS NULL OR `status`=1 OR `status`=4) ORDER BY `jobID` DESC LIMIT 1");
    $query->execute(array($reseller_id));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $jobPending=$gsprache->no;
        if ($row['jobPending']=='Y') {
            $query2->execute(array($row['dedicatedID']));
            foreach ($query2->fetchAll(PDO::FETCH_ASSOC) as $row2) {
                if ($row2['action']=='ad') $jobPending=$gsprache->add;
                else if ($row2['action']=='dl') $jobPending=$gsprache->del;
                else if ($row2['action']=='ri') $jobPending=$sprache->reinstall;
                else if ($row2['action']=='rc') $jobPending=$sprache->rescue_start;
                else if ($row2['action']=='rs') $jobPending=$sprache->restart;
                else if ($row2['action']=='st') $jobPending=$sprache->stop;
                else $jobPending=$gsprache->mod;
                $json=@json_decode($row2['extraData']);
                $tobeActive=(is_object($json) and isset($json->newActive)) ? $json->newActive : 'N';
            }
        }
        $imgName='16_ok';
        $imgAlt='Active';
        $active='Y';
        if (($row['active']=='Y' and $row['jobPending']=='N' and $row['notified']<=$rSA['down_checks']) or ($row['jobPending']=='Y') and isset($tobeActive) and $tobeActive=='Y') {
            $imgName='16_ok';
            $imgAlt='Active';
        } else if (($row['active']=='Y' and $row['jobPending']=='N' and $row['notified']>$rSA['down_checks']) or ($row['jobPending']=='Y') and isset($tobeActive) and $tobeActive=='Y') {
            $imgName='16_error';
            $imgAlt='Crashed';
            $active='C';
        } else if ($row['active']=='N') {
            $imgName='16_bad';
            $imgAlt='Inactive';
            $active='N';
        }
        if ($row['status'] == 1) {
            $status=$sprache->stopped;
        } else if ($row['status'] == 2) {
            $status=$sprache->installing;
        } else if ($row['status'] == 3) {
            $status=$sprache->rescue;
        } else {
            $status=$sprache->ok;
        }
        $table[]=array('id'=>$row['dedicatedID'],'ip'=>$row['ip'],'description'=>$row['description'],'status'=>$status,'img'=>$imgName,'alt'=>$imgAlt,'userID'=>$row['userID'],'cname'=>$row['cname'],'names'=>trim($row['name'].' '.$row['vname']),'active'=>$active,'jobPending'=>$jobPending);
    }
    $pageamount=ceil($colcount/$amount);
    $link='<a href="admin.php?w=rp&amp;o='.$o.'&amp;a=';
    if(!isset($amount)) $link .="20";
    else $link .=$amount;
    if ($start==0) $link .='&p=0" class="bold">1</a>';
    else $link .='&p=0">1</a>';
    $pages[]=$link;
    $i=2;
    while ($i<=$pageamount) {
        $selectpage=($i-1)*$amount;
        $pages[]=($start==$selectpage) ? '<a href="admin.php?w=rp&amp;o='.$o.'&amp;a='.$amount.'&p='.$selectpage.'" class="bold">'.$i.'</a>' : '<a href="admin.php?w=rp&amp;o='.$o.'&amp;a='.$amount.'&p='.$selectpage.'">'.$i.'</a>';
        $i++;
    }
    $pages=implode(', ',$pages);
    $template_file='admin_root_dedicated_list.tpl';
}