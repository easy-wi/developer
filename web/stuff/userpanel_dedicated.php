<?php
/**
 * File: userpanel_dedicated.php.
 * Author: Ulrich Block
 * Date: 07.07.13
 * Time: 10:52
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

include(EASYWIDIR . '/stuff/keyphrasefile.php');

$sprache=getlanguagefile('reseller',$user_language,$reseller_id);
$loguserid=$user_id;
$logusername=getusername($user_id);
$logusertype="user";
$logreseller=0;
$logsubuser=0;
if (isset($admin_id)) $logsubuser=$admin_id;
else if (isset($subuser_id)) $logsubuser=$subuser_id;
if (isset($admin_id) and $reseller_id!=0) $reseller_id=$admin_id;
if ($ui->w('action',4,'post') and !token(true)) {
    $template_file=$spracheResponse->token;
} else if ($ui->st('d','get')=='ri'and $ui->id('id',10,'get') and (!isset($_SESSION['sID']) or in_array($ui->id('id',10,'get'),$substituteAccess['ro']))) {
    $id=$ui->id('id',10,'get');
    if (!$ui->st('action','post')) {
        $option=array();
        $query=$sql->prepare("SELECT COUNT(`id`) AS `a` FROM `rootsDHCP` WHERE `active`='Y' LIMIT 1");
        $query->execute();
        $dhcp=($query->fetchColumn()>0) ? 'Y' : 'N';
        $query=$sql->prepare("SELECT COUNT(`id`) AS `a` FROM `rootsPXE` WHERE `active`='Y' LIMIT 1");
        $query->execute();
        $pxe=($query->fetchColumn()>0) ? 'Y' : 'N';
        $query=$sql->prepare("SELECT r.*,d.*,AES_DECRYPT(d.`initialPass`,?) AS `decryptedpass` FROM `rootsDedicated` d LEFT JOIN `resellerimages` r ON d.`imageID`=r.`id` WHERE d.`userID`=? AND d.`dedicatedID`=? LIMIT 1");
        $query->execute(array($aeskey,$user_id,$id));
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $ip=$row['ip'];
            $showImages=false;
            if ($row['status'] == 1) {
                $status=$sprache->stopped;
            } else if ($row['status'] == 2) {
                $status=$sprache->installing;
            } else if ($row['status'] == 3) {
                $status=$sprache->rescue;
            } else {
                $status=$sprache->ok;
            }
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
            $description=$row['description'];
            $bitversion=$row['bitversion'];
            $pass=$row['decryptedpass'];
        }
        $templates=array();
        $query=$sql->prepare("SELECT `id`,`description`,`bitversion` FROM `resellerimages` WHERE `description` NOT IN ('Rescue 32bit','Rescue 64bit') ORDER BY `distro`,`bitversion`,`description`");
        $query->execute();
        foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $templates[]=array('id'=>$row['id'],'description'=>$row['description']);
        }
        $template_file=(isset($ip)) ? 'userpanel_root_dedicated_ri.tpl' : 'admin_404.tpl';
    } else if (in_array($ui->st('action','post'),array('ri','rc','rs','st'))) {
        $query=$sql->prepare("SELECT d.`ip`,i.`bitversion` FROM `rootsDedicated` d LEFT JOIN `resellerimages` i ON d.`resellerImageID`=i.`id` WHERE d.`userID`=? AND d.`dedicatedID`=? LIMIT 1");
        $query->execute(array($user_id,$id));
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
            $query->execute(array($user_id,$id,$ip,$ui->st('action','post'),json_encode($extraData),$reseller_id));
            $query=$sql->prepare("UPDATE `rootsDedicated` SET `jobPending`='Y' WHERE `dedicatedID`=? AND `userID`=? AND `resellerID`=?");
            $query->execute(array($user_id,$id,$reseller_id));
            $template_file=$spracheResponse->table_add;
        } else {
            $template_file='admin_404.tpl';
        }
    } else {
        $template_file='admin_404.tpl';
    }
} else {
    $table=array();
    $query=$sql->prepare("SELECT * FROM `rootsDedicated` WHERE `active`='Y' AND `userID`=? AND `resellerID`=?");
    $query2=$sql->prepare("SELECT `action`,`extraData` FROM `jobs` WHERE `affectedID`=? AND `type`='de' AND (`status` IS NULL OR `status`=1 OR `status`=4) ORDER BY `jobID` DESC LIMIT 1");
    $query->execute(array($user_id,$reseller_id));
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if (!isset($_SESSION['sID']) or in_array($row['dedicatedID'],$substituteAccess['ro'])) {
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
            $table[]=array('id'=>$row['dedicatedID'],'ip'=>$row['ip'],'description'=>$row['description'],'img'=>$imgName,'alt'=>$imgAlt,'active'=>$active,'jobPending'=>$jobPending);
        }
    }
    $template_file='userpanel_root_dedicated_list.tpl';
}